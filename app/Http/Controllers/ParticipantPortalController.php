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

    public function dashboard(Request $request)
    {
        $peserta = $this->getAuthenticatedPeserta();
        $pelatihan = $peserta->angkatan->pelatihan ?? null;
        $angkatan = $peserta->angkatan ?? null;

        // Ambil sesi pembelajaran yang relevan untuk peserta
        $query = AttendanceSession::with([
            'jadwal.mataPelatihan', 
            'jadwal.penugasanPengajar.widyaiswara', 
            'jadwal.penugasanPengajar.narasumber',
            'kelasMapel.ruangan'
        ])
        ->where(function ($q) use ($peserta) {
            if ($peserta->kelas_mapel_id) {
                $q->where('kelas_mapel_id', $peserta->kelas_mapel_id);
            }
            if ($peserta->angkatan_id) {
                $q->orWhereHas('kelasMapel', fn($k) => $k->where('angkatan_id', $peserta->angkatan_id))
                  ->orWhereHas('jadwal.mataPelatihan', fn($m) => $m->where('angkatan_id', $peserta->angkatan_id));
            }
        });

        // Filter jadwal jika ada
        if ($request->filled('filter_tanggal')) {
            $query->whereDate('tanggal', $request->filter_tanggal);
        }
        if ($request->filled('filter_mapel')) {
            $query->whereHas('jadwal', fn($j) => $j->where('mata_pelatihan_id', $request->filter_mapel));
        }

        $sessionsList = $query->orderBy('tanggal', 'asc')->orderBy('jam_mulai', 'asc')->get();

        // Fallback jika belum ada sesi terdaftar untuk kelas peserta
        if ($sessionsList->isEmpty()) {
            $sessionsList = AttendanceSession::with([
                'jadwal.mataPelatihan', 
                'jadwal.penugasanPengajar.widyaiswara', 
                'jadwal.penugasanPengajar.narasumber',
                'kelasMapel.ruangan'
            ])
            ->where('status', 'Aktif')
            ->orderBy('tanggal', 'asc')
            ->orderBy('jam_mulai', 'asc')
            ->take(5)
            ->get();
        }

        // Ambil presensi peserta untuk sesi-sesi ini
        $sessionIds = $sessionsList->pluck('id');
        $presensisToday = Presensi::whereIn('attendance_session_id', $sessionIds)
            ->where('peserta_id', $peserta->id)
            ->get()
            ->keyBy('attendance_session_id');

        // Sesi hari ini untuk backward compatibility
        $today = now()->toDateString();
        $sessionsToday = $sessionsList->where('tanggal', $today);

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

        // Daftar mata pelatihan untuk dropdown filter
        $mataPelatihans = $angkatan ? $angkatan->mataPelatihans : collect();

        return view('portal.dashboard', compact(
            'peserta',
            'pelatihan',
            'angkatan',
            'sessionsList',
            'sessionsToday',
            'presensisToday',
            'totalHadir',
            'totalTerlambat',
            'totalIzin',
            'totalSakit',
            'totalAlpa',
            'totalDispensasi',
            'pendingRequests',
            'recentPresensis',
            'mataPelatihans'
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

    /**
     * Submit Presensi Langsung (Tanpa QR Code) sesuai form template pilihan Hadir, Sakit, Izin, Dispensasi,
     * Keterangan, dan Bukti Foto / PDF (maks. 2MB).
     */
    public function submitDirectPresensi(Request $request, int $sessionId)
    {
        $peserta = $this->getAuthenticatedPeserta();
        $session = AttendanceSession::findOrFail($sessionId);

        $request->validate([
            'status_kehadiran' => 'required|in:Hadir,Sakit,Izin,Dispensasi',
            'keterangan'       => 'nullable|string|max:500',
            'bukti_file'       => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048', // maks 2MB
        ], [
            'status_kehadiran.required' => 'Pilih salah satu status kehadiran.',
            'bukti_file.max'            => 'Ukuran file bukti maksimal 2 MB.',
            'bukti_file.mimes'          => 'Format bukti harus berupa Foto (JPG, PNG) atau dokumen PDF.',
        ]);

        $result = $this->attendanceService->submitDirectAttendance(
            $peserta,
            $session,
            $request->input('status_kehadiran'),
            $request->input('keterangan'),
            $request->file('bukti_file')
        );

        if (!$result['success']) {
            return redirect()->back()->with('error', $result['message']);
        }

        return redirect()->back()->with('success', $result['message']);
    }
}
