<?php

namespace App\Http\Controllers;

use App\Services\AttendanceService;
use App\Models\Peserta;
use App\Models\AttendanceSession;
use App\Models\Presensi;
use App\Models\AttendanceRequest;
use Illuminate\Http\Request;

class ParticipantPortalController extends Controller
{
    protected AttendanceService $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    protected function getAuthenticatedPeserta(): Peserta
    {
        $id = session('participant_id');
        return Peserta::with(['angkatan.pelatihan', 'kelasMapel.ruangan'])->findOrFail($id);
    }

    public function dashboard()
    {
        $peserta = $this->getAuthenticatedPeserta();

        // Cari sesi pembelajaran hari ini yang relevan untuk peserta
        $today = now()->toDateString();
        $sessionsToday = AttendanceSession::with(['jadwal.mataPelatihan', 'kelasMapel.ruangan'])
            ->where('tanggal', $today)
            ->where(function ($q) use ($peserta) {
                if ($peserta->kelas_mapel_id) {
                    $q->where('kelas_mapel_id', $peserta->kelas_mapel_id);
                }
                if ($peserta->angkatan_id) {
                    $q->orWhereHas('kelasMapel', fn($k) => $k->where('angkatan_id', $peserta->angkatan_id))
                      ->orWhereHas('jadwal.mataPelatihan', fn($m) => $m->where('angkatan_id', $peserta->angkatan_id));
                }
            })
            ->orderBy('jam_mulai', 'asc')
            ->get();

        // Ambil presensi peserta untuk sesi-sesi hari ini
        $sessionIds = $sessionsToday->pluck('id');
        $presensisToday = Presensi::whereIn('attendance_session_id', $sessionIds)
            ->where('peserta_id', $peserta->id)
            ->get()
            ->keyBy('attendance_session_id');

        // Ringkasan riwayat keseluruhan peserta
        $totalHadir = Presensi::where('peserta_id', $peserta->id)->where('status_kehadiran', 'Hadir')->count();
        $totalTerlambat = Presensi::where('peserta_id', $peserta->id)->where('status_kehadiran', 'Terlambat')->count();
        $totalIzin = Presensi::where('peserta_id', $peserta->id)->where('status_kehadiran', 'Izin')->count();
        $totalSakit = Presensi::where('peserta_id', $peserta->id)->where('status_kehadiran', 'Sakit')->count();
        $totalAlpa = Presensi::where('peserta_id', $peserta->id)->where('status_kehadiran', 'Alpa')->count();
        $totalDispensasi = Presensi::where('peserta_id', $peserta->id)->where('status_kehadiran', 'Dispensasi')->count();

        // Pengajuan izin yang sedang diproses
        $pendingRequests = AttendanceRequest::with('session.jadwal.mataPelatihan')
            ->where('peserta_id', $peserta->id)
            ->latest()
            ->take(5)
            ->get();

        // 5 riwayat kehadiran terakhir
        $recentPresensis = Presensi::with('session.jadwal.mataPelatihan')
            ->where('peserta_id', $peserta->id)
            ->latest('tanggal')
            ->latest('id')
            ->take(5)
            ->get();

        return view('portal.dashboard', compact(
            'peserta',
            'sessionsToday',
            'presensisToday',
            'totalHadir',
            'totalTerlambat',
            'totalIzin',
            'totalSakit',
            'totalAlpa',
            'totalDispensasi',
            'pendingRequests',
            'recentPresensis'
        ));
    }

    public function scan(Request $request)
    {
        $peserta = $this->getAuthenticatedPeserta();

        // Jika peserta membuka link scan langsung (misal scan lewat kamera bawaan HP)
        if ($request->filled('token')) {
            $result = $this->attendanceService->checkInViaQr($peserta, $request->fullUrl());
            if ($result['success']) {
                return redirect()->route('portal.dashboard')->with('success', $result['message']);
            }
            return redirect()->route('portal.dashboard')->with('error', $result['message']);
        }

        $sessionId = $request->get('session_id');
        $selectedSession = $sessionId ? AttendanceSession::find($sessionId) : null;

        return view('portal.scan', compact('peserta', 'selectedSession'));
    }

    public function submitScan(Request $request)
    {
        $request->validate([
            'qr_payload' => 'required|string',
        ]);

        $peserta = $this->getAuthenticatedPeserta();
        $result = $this->attendanceService->checkInViaQr($peserta, $request->qr_payload);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 422);
        }

        return response()->json([
            'success' => true,
            'status' => $result['status'],
            'message' => $result['message'],
            'redirect' => route('portal.dashboard'),
        ]);
    }

    public function riwayat(Request $request)
    {
        $peserta = $this->getAuthenticatedPeserta();
        $statusFilter = $request->get('status');

        $query = Presensi::with(['session.jadwal.mataPelatihan', 'session.kelasMapel'])
            ->where('peserta_id', $peserta->id);

        if ($statusFilter) {
            $query->where('status_kehadiran', $statusFilter);
        }

        $presensis = $query->latest('tanggal')->latest('id')->paginate(15)->withQueryString();

        return view('portal.riwayat', compact('peserta', 'presensis', 'statusFilter'));
    }

    public function pengajuan(Request $request)
    {
        $peserta = $this->getAuthenticatedPeserta();

        // Ambil sesi pembelajaran yang tersedia untuk pengajuan (mendatang atau hari ini)
        $availableSessions = AttendanceSession::with(['jadwal.mataPelatihan', 'kelasMapel'])
            ->where(function ($q) use ($peserta) {
                if ($peserta->kelas_mapel_id) {
                    $q->where('kelas_mapel_id', $peserta->kelas_mapel_id);
                }
                if ($peserta->angkatan_id) {
                    $q->orWhereHas('kelasMapel', fn($k) => $k->where('angkatan_id', $peserta->angkatan_id));
                }
            })
            ->where('tanggal', '>=', now()->subDays(3)->toDateString())
            ->orderBy('tanggal', 'desc')
            ->orderBy('jam_mulai', 'asc')
            ->get();

        $myRequests = AttendanceRequest::with(['session.jadwal.mataPelatihan', 'evidences'])
            ->where('peserta_id', $peserta->id)
            ->latest()
            ->paginate(10);

        return view('portal.pengajuan', compact('peserta', 'availableSessions', 'myRequests'));
    }

    public function submitPengajuan(Request $request)
    {
        $request->validate([
            'attendance_session_id' => 'required|exists:attendance_sessions,id',
            'jenis_pengajuan' => 'required|in:Izin,Sakit,Dispensasi',
            'alasan' => 'required|string|min:5|max:1000',
            'bukti_file.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ], [
            'attendance_session_id.required' => 'Pilih sesi pembelajaran.',
            'jenis_pengajuan.required' => 'Pilih jenis pengajuan (Izin / Sakit / Dispensasi).',
            'alasan.required' => 'Alasan wajib diisi.',
            'alasan.min' => 'Alasan minimal 5 karakter.',
            'bukti_file.*.mimes' => 'Format file bukti harus berupa JPG, PNG, atau PDF.',
            'bukti_file.*.max' => 'Ukuran file bukti maksimal 5 MB.',
        ]);

        $peserta = $this->getAuthenticatedPeserta();
        $files = $request->file('bukti_file') ?: [];

        $this->attendanceService->submitAttendanceRequest(
            $peserta,
            (int) $request->attendance_session_id,
            $request->jenis_pengajuan,
            $request->alasan,
            $files
        );

        return redirect()->route('portal.pengajuan')->with('success', 'Pengajuan ' . $request->jenis_pengajuan . ' berhasil dikirim! Menunggu verifikasi admin.');
    }
}
