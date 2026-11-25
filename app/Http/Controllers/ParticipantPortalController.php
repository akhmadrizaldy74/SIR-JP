<?php

namespace App\Http\Controllers;

use App\Services\AttendanceService;
use App\Models\Peserta;
use App\Models\Angkatan;
use App\Models\AttendanceSession;
use App\Models\Presensi;
use App\Models\AttendanceRequest;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
        $this->attendanceService->autoCloseExpiredSessions();

        $peserta = $this->getAuthenticatedPeserta();
        $pelatihan = $peserta->angkatan->pelatihan ?? null;
        $angkatan = $peserta->angkatan ?? null;

        // Ambil daftar angkatan yang sedang berjalan / dibuka jika peserta belum memilih pelatihan
        $availableAngkatans = collect();
        if (!$angkatan) {
            $availableAngkatans = Angkatan::with('pelatihan')
                ->whereIn('status', ['Berjalan', 'Direncanakan'])
                ->orderBy('id', 'desc')
                ->take(30)
                ->get();
        }

        // Ambil sesi pembelajaran yang relevan HANYA jika peserta sudah memiliki kelas / angkatan
        if ($peserta->angkatan_id || $peserta->kelas_mapel_id) {
            $query = AttendanceSession::with([
                'jadwal.mataPelatihan', 
                'jadwal.penugasanPengajar.widyaiswara', 
                'jadwal.penugasanPengajar.narasumber',
                'kelasMapel.ruangan',
                'creator',
            ]);

            // Jika peserta memiliki kelas spesifik, hanya ambil sesi kelas tersebut atau sesi pleno angkatan
            if ($peserta->kelas_mapel_id) {
                $query->where(function ($q) use ($peserta) {
                    $q->where('kelas_mapel_id', $peserta->kelas_mapel_id);
                    if ($peserta->angkatan_id) {
                        $q->orWhere(function ($sub) use ($peserta) {
                            $sub->whereNull('kelas_mapel_id')
                                ->where(function($s2) use ($peserta) {
                                    $s2->whereHas('kelasMapel', fn($k) => $k->where('angkatan_id', $peserta->angkatan_id))
                                       ->orWhereHas('jadwal.mataPelatihan', fn($m) => $m->where('angkatan_id', $peserta->angkatan_id));
                                });
                        });
                    }
                });
            } else {
                $query->where(function ($q) use ($peserta) {
                    $q->whereHas('kelasMapel', fn($k) => $k->where('angkatan_id', $peserta->angkatan_id))
                      ->orWhereHas('jadwal.mataPelatihan', fn($m) => $m->where('angkatan_id', $peserta->angkatan_id));
                });
            }

            // Filter jadwal jika ada
            if ($request->filled('filter_tanggal')) {
                $query->whereDate('tanggal', $request->filter_tanggal);
            }
            if ($request->filled('filter_mapel')) {
                $query->whereHas('jadwal', fn($j) => $j->where('mata_pelatihan_id', $request->filter_mapel));
            }

            $sessionsList = $query->orderBy('tanggal', 'asc')->orderBy('jam_mulai', 'asc')->get();
        } else {
            $sessionsList = collect();
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
            'availableAngkatans',
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

    /**
     * Peserta memilih angkatan pelatihan aktif secara mandiri
     */
    /**
     * Peserta bergabung ke angkatan pelatihan melalui Token / Kode Akses dari panitia
     * atau penetapan ID angkatan yang valid.
     */
    public function selectAngkatan(Request $request)
    {
        $token = trim($request->input('kode_token') ?? '');
        $angkatanId = $request->input('angkatan_id');

        $angkatan = null;

        if ($token) {
            $angkatan = Angkatan::with('pelatihan')
                ->where(function ($q) use ($token) {
                    $q->where('nomor_angkatan', $token)
                      ->orWhere('id', $token)
                      ->orWhereHas('pelatihan', fn($p) => $p->where('kode_pelatihan', $token)->orWhere('nama_pelatihan', 'like', "%{$token}%"));
                })
                ->whereIn('status', ['Berjalan', 'Direncanakan'])
                ->first();

            if (!$angkatan) {
                return redirect()->route('portal.dashboard')
                    ->with('error', 'Kode Token / Pelatihan "' . htmlspecialchars($token) . '" tidak valid atau tidak aktif. Silakan hubungi panitia penyelenggara diklat.');
            }
        } elseif ($angkatanId) {
            $angkatan = Angkatan::with('pelatihan')->find($angkatanId);
        }

        if (!$angkatan) {
            return redirect()->route('portal.dashboard')
                ->with('error', 'Silakan masukkan Kode Token Pelatihan resmi yang diberikan oleh panitia diklat.');
        }

        $peserta = $this->getAuthenticatedPeserta();
        $peserta->update([
            'angkatan_id' => $angkatan->id,
            'kelas_mapel_id' => $angkatan->kelasMapels()->first()?->id ?? $peserta->kelas_mapel_id,
        ]);

        LogAktivitas::catat('Portal Peserta', 'Pilih Pelatihan', "Peserta {$peserta->nama} bergabung ke angkatan: {$angkatan->nomor_angkatan}");

        return redirect()->route('portal.dashboard')
            ->with('success', 'Selamat! Anda telah resmi terdaftar di kelas: ' . ($angkatan->pelatihan->nama_pelatihan ?? $angkatan->nomor_angkatan));
    }

    /**
     * Reset / Ganti pelatihan jika belum ada data presensi tercatat
     */
    public function resetAngkatan(Request $request)
    {
        $peserta = $this->getAuthenticatedPeserta();

        $hasPresensi = Presensi::where('peserta_id', $peserta->id)->exists();
        if ($hasPresensi) {
            return redirect()->route('portal.dashboard')
                ->with('error', 'Anda telah memiliki catatan riwayat presensi di pelatihan ini. Pergantian kelas harus melalui konfirmasi Panitia/Admin.');
        }

        $peserta->update([
            'angkatan_id' => null,
            'kelas_mapel_id' => null,
        ]);

        return redirect()->route('portal.dashboard')
            ->with('info', 'Pilihan pelatihan Anda telah direset. Silakan pilih pelatihan yang ingin diikuti.');
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

    /**
     * Tampilkan atau buka berkas lampiran bukti presensi secara aman dan terproteksi.
     */
    public function viewBukti(int $id)
    {
        $presensi = Presensi::findOrFail($id);

        // Otorisasi: Peserta hanya dapat melihat buktinya sendiri; Staf/Pengajar/Admin dapat melihat semua bukti peserta
        if (session()->has('participant_id')) {
            $pesertaId = session('participant_id');
            if ($presensi->peserta_id !== $pesertaId) {
                abort(403, 'Anda tidak memiliki hak akses untuk melihat berkas bukti peserta lain.');
            }
        } elseif (auth()->check()) {
            $user = auth()->user();
            if ($user->isGuest()) {
                abort(403, 'Akses dibatasi untuk akun Guest.');
            }
        } else {
            abort(403, 'Silakan login terlebih dahulu untuk mengakses bukti.');
        }

        if (!$presensi->bukti_file || !Storage::disk('public')->exists($presensi->bukti_file)) {
            abort(404, 'Berkas lampiran bukti tidak ditemukan pada server.');
        }

        $path = Storage::disk('public')->path($presensi->bukti_file);
        $mime = mime_content_type($path) ?: 'application/octet-stream';

        return response()->file($path, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="' . basename($presensi->bukti_file) . '"'
        ]);
    }
}
