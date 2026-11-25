<?php

namespace App\Http\Controllers;

use App\Services\AttendanceService;
use App\Models\AttendanceSession;
use App\Models\Jadwal;
use App\Models\KelasMapel;
use App\Models\Pelatihan;
use App\Models\Angkatan;
use App\Models\LogAktivitas;
use App\Models\Presensi;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AttendanceSessionController extends Controller
{
    protected AttendanceService $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    public function index(Request $request)
    {
        $this->attendanceService->autoCloseExpiredSessions();

        $status = $request->get('status');
        $pelatihanId = $request->get('pelatihan_id');
        $tanggal = $request->get('tanggal');
        $search = $request->get('search');

        $query = AttendanceSession::with([
            'jadwal.mataPelatihan.angkatan.pelatihan',
            'jadwal.penugasanPengajar.widyaiswara',
            'jadwal.penugasanPengajar.narasumber',
            'kelasMapel.ruangan',
            'presensis',
            'creator',
        ]);

        // Filter otomatis untuk Pengajar: hanya tampilkan sesi yang diampu atau dibuat oleh pengajar tersebut
        $user = auth()->user();
        if ($user && $user->isPengajar() && !$user->isAdministrator() && !$user->isPenyelenggara()) {
            $this->applyPengajarScope($query, $user);
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($tanggal) {
            $query->where('tanggal', $tanggal);
        }

        if ($pelatihanId) {
            $query->whereHas('kelasMapel', fn($q) => $q->where('pelatihan_id', $pelatihanId));
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('judul_sesi', 'like', "%{$search}%")
                  ->orWhereHas('jadwal.mataPelatihan', fn($m) => $m->where('nama_mata_pelatihan', 'like', "%{$search}%"))
                  ->orWhereHas('kelasMapel', fn($k) => $k->where('nama_kelas', 'like', "%{$search}%"));
            });
        }

        $sessions = $query->orderBy('tanggal', 'desc')->orderBy('jam_mulai', 'desc')->paginate(12)->withQueryString();
        $pelatihans = Pelatihan::orderBy('nama_pelatihan')->get();
        $globalStats = $this->attendanceService->getDashboardStatistics($pelatihanId);

        return view('presensi.sessions.index', compact('sessions', 'pelatihans', 'status', 'tanggal', 'pelatihanId', 'search', 'globalStats'));
    }

    public function create(Request $request)
    {
        $jadwalId = $request->get('jadwal_id');
        $selectedJadwal = $jadwalId ? Jadwal::with(['mataPelatihan.angkatan.pelatihan', 'penugasanPengajar.widyaiswara', 'penugasanPengajar.narasumber'])->find($jadwalId) : null;

        $query = Jadwal::with(['mataPelatihan.angkatan.pelatihan', 'penugasanPengajar.widyaiswara', 'penugasanPengajar.narasumber'])
            ->where('status', 'Terjadwal');

        $user = auth()->user();
        if ($user && $user->isPengajar() && !$user->isAdministrator() && !$user->isPenyelenggara()) {
            $query->whereHas('penugasanPengajar', function($p) use ($user) {
                $p->whereHas('widyaiswara', function ($w) use ($user) {
                    $w->where(function($sub) use ($user) {
                        if (!empty($user->nik)) {
                            $sub->orWhere('nik', $user->nik)->orWhere('nip', $user->nik);
                        }
                        if (!empty($user->email)) {
                            $sub->orWhere('email', $user->email);
                        }
                        if (!empty($user->name)) {
                            $sub->orWhere('nama_lengkap', 'like', "%{$user->name}%");
                        }
                    });
                })->orWhereHas('narasumber', function ($n) use ($user) {
                    $n->where(function($sub) use ($user) {
                        if (!empty($user->nik)) {
                            $sub->orWhere('nik', $user->nik);
                        }
                        if (!empty($user->email)) {
                            $sub->orWhere('email', $user->email);
                        }
                        if (!empty($user->name)) {
                            $sub->orWhere('nama_lengkap', 'like', "%{$user->name}%");
                        }
                    });
                });
            });
            $jadwals = $query->orderBy('tanggal', 'desc')->get();
        } else {
            $jadwals = $query->orderBy('tanggal', 'desc')->take(30)->get();
        }

        return view('presensi.sessions.create', compact('jadwals', 'selectedJadwal'));
    }

    /**
     * Terapkan scope query agar pengajar hanya melihat sesi presensi yang diampu / dibuat oleh dirinya
     */
    protected function applyPengajarScope($query, User $user)
    {
        $query->where(function ($q) use ($user) {
            $q->where('created_by', $user->id)
              ->orWhereHas('jadwal.penugasanPengajar', function ($p) use ($user) {
                  $p->whereHas('widyaiswara', function ($w) use ($user) {
                      $w->where(function($sub) use ($user) {
                          if (!empty($user->nik)) {
                              $sub->orWhere('nik', $user->nik)->orWhere('nip', $user->nik);
                          }
                          if (!empty($user->email)) {
                              $sub->orWhere('email', $user->email);
                          }
                          if (!empty($user->name)) {
                              $sub->orWhere('nama_lengkap', 'like', "%{$user->name}%");
                          }
                      });
                  })->orWhereHas('narasumber', function ($n) use ($user) {
                      $n->where(function($sub) use ($user) {
                          if (!empty($user->nik)) {
                              $sub->orWhere('nik', $user->nik);
                          }
                          if (!empty($user->email)) {
                              $sub->orWhere('email', $user->email);
                          }
                          if (!empty($user->name)) {
                              $sub->orWhere('nama_lengkap', 'like', "%{$user->name}%");
                          }
                      });
                  });
              });
        });
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'jadwal_id' => 'required|exists:jadwals,id',
            'kelas_mapel_id' => 'nullable|exists:kelas_mapels,id',
            'judul_sesi' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
            'link_meeting' => 'nullable|string|max:500',
            'toleransi_terlambat_menit' => 'required|integer|min:0|max:120',
            'status' => 'required|in:Draft,Aktif',
        ], [
            'jadwal_id.required' => 'Pilih jadwal pembelajaran.',
            'judul_sesi.required' => 'Judul sesi presensi wajib diisi.',
            'tanggal.required' => 'Tanggal sesi wajib ditentukan.',
            'jam_mulai.required' => 'Jam mulai wajib diisi.',
            'jam_selesai.required' => 'Jam selesai wajib diisi.',
        ]);

        $jadwal = Jadwal::with('mataPelatihan')->findOrFail($validated['jadwal_id']);

        $kelasMapelId = $request->input('kelas_mapel_id') ?: $jadwal->kelas_mapel_id;
        if (!$kelasMapelId && $jadwal->mataPelatihan) {
            $kelasMapels = KelasMapel::where('mata_pelatihan_id', $jadwal->mata_pelatihan_id)
                ->where('angkatan_id', $jadwal->mataPelatihan->angkatan_id)
                ->limit(2)
                ->get();

            if ($kelasMapels->count() === 1) {
                $kelasMapelId = $kelasMapels->first()->id;
            }
        }

        $session = AttendanceSession::create([
            'jadwal_id' => $jadwal->id,
            'kelas_mapel_id' => $kelasMapelId,
            'judul_sesi' => $validated['judul_sesi'],
            'link_meeting' => $request->input('link_meeting'),
            'tanggal' => $validated['tanggal'],
            'jam_mulai' => $validated['jam_mulai'],
            'jam_selesai' => $validated['jam_selesai'],
            'toleransi_terlambat_menit' => $validated['toleransi_terlambat_menit'],
            'status' => $validated['status'],
            'qr_secret_key' => Str::random(32),
            'waktu_buka_presensi' => $validated['status'] === 'Aktif' ? now() : null,
            'created_by' => auth()->id(),
        ]);

        if ($session->status === 'Aktif') {
            $this->attendanceService->generateQrToken($session);
        }

        LogAktivitas::catat('Presensi', 'Buat Sesi Presensi', 'Membuat sesi presensi: ' . $session->judul_sesi);

        return redirect()->route('attendance-sessions.show', $session->id)
            ->with('success', 'Sesi presensi pembelajaran berhasil dibuat!');
    }

    public function show(AttendanceSession $attendanceSession)
    {
        $this->attendanceService->autoCloseExpiredSessions();
        $attendanceSession->refresh();

        $attendanceSession->load([
            'jadwal.mataPelatihan.angkatan.pelatihan',
            'jadwal.penugasanPengajar.widyaiswara',
            'jadwal.penugasanPengajar.narasumber',
            'jadwal.realisasi',
            'kelasMapel.ruangan',
            'documentations',
            'requests.peserta',
            'creator',
        ]);

        $stats = $this->attendanceService->getSessionStatistics($attendanceSession);

        return view('presensi.sessions.show', compact('attendanceSession', 'stats'));
    }

    public function activate(AttendanceSession $attendanceSession)
    {
        $attendanceSession->update([
            'status' => 'Aktif',
            'waktu_buka_presensi' => now(),
            'waktu_tutup_presensi' => null,
        ]);

        $this->attendanceService->generateQrToken($attendanceSession);

        LogAktivitas::catat('Presensi', 'Aktivasi Sesi Presensi', 'Mengaktifkan sesi presensi ID #' . $attendanceSession->id);

        return back()->with('success', 'Sesi presensi sekarang AKTIF! Peserta dapat melakukan presensi.');
    }

    public function close(AttendanceSession $attendanceSession)
    {
        $result = $this->attendanceService->closeSession($attendanceSession, auth()->id(), true);

        LogAktivitas::catat(
            'Presensi',
            'Tutup Sesi Presensi',
            "Menutup sesi presensi ID #{$attendanceSession->id} (Auto-Alpa: {$result['alpa_count']}, Sync Realisasi JP: " . ($result['realisasi_synced'] ? 'Ya' : 'Tidak/Sudah Ada') . ')'
        );

        $msgParts = ['Sesi presensi telah DITUTUP.'];
        if ($result['alpa_count'] > 0) {
            $msgParts[] = "{$result['alpa_count']} peserta yang belum presensi otomatis ditandai Alpa.";
        }
        if ($result['realisasi_synced']) {
            $msgParts[] = 'Realisasi jadwal & JP pengajar otomatis disinkronkan ke Rekapitulasi JP.';
        }

        return back()->with('success', implode(' ', $msgParts));
    }

    public function verifyPresensi(Request $request, AttendanceSession $attendanceSession, Presensi $presensi)
    {
        if ((int) $presensi->attendance_session_id !== (int) $attendanceSession->id) {
            abort(404);
        }

        $validated = $request->validate([
            'keputusan' => 'required|in:Disetujui,Ditolak',
            'catatan_verifikasi' => 'nullable|string|max:500',
        ]);

        $this->attendanceService->verifyDirectPresensi(
            $presensi,
            $validated['keputusan'],
            $validated['catatan_verifikasi'] ?? null,
            auth()->id()
        );

        $namaPeserta = $presensi->peserta->nama ?? 'Peserta';
        LogAktivitas::catat(
            'Presensi',
            'Verifikasi Bukti Presensi',
            "Verifikasi presensi {$namaPeserta} pada sesi #{$attendanceSession->id}: {$validated['keputusan']}"
        );

        return back()->with('success', "Status bukti presensi {$namaPeserta} berhasil diperbarui menjadi {$validated['keputusan']}.");
    }

    public function exportBapPdf(AttendanceSession $attendanceSession)
    {
        $attendanceSession->load([
            'jadwal.mataPelatihan.angkatan.pelatihan',
            'jadwal.penugasanPengajar.widyaiswara',
            'jadwal.penugasanPengajar.narasumber',
            'jadwal.realisasi',
            'kelasMapel.ruangan',
            'documentations',
            'creator',
        ]);

        $stats = $this->attendanceService->getSessionStatistics($attendanceSession);

        // Siapkan foto dokumentasi berbasis base64 agar DomPDF merender aman tanpa timeout HTTP
        $docPhotos = [];
        foreach ($attendanceSession->documentations->take(4) as $doc) {
            if (Storage::disk('local')->exists($doc->path_file)) {
                $raw = Storage::disk('local')->get($doc->path_file);
                $docPhotos[] = [
                    'src' => 'data:' . ($doc->mime_type ?: 'image/jpeg') . ';base64,' . base64_encode($raw),
                    'kategori' => $doc->kategori,
                    'keterangan' => $doc->keterangan ?: $doc->nama_file_asli,
                ];
            }
        }

        LogAktivitas::catat('Laporan', 'Cetak BAP Sesi PDF', "Mencetak Berita Acara Pembelajaran sesi #{$attendanceSession->id}");

        $pdf = Pdf::loadView('presensi.sessions.bap_pdf', compact('attendanceSession', 'stats', 'docPhotos'))
            ->setPaper('a4', 'portrait');

        $safeTitle = Str::slug($attendanceSession->judul_sesi ?: 'sesi-' . $attendanceSession->id);
        return $pdf->stream("BAP_{$safeTitle}.pdf");
    }

    public function qrDisplay(AttendanceSession $attendanceSession)
    {
        if ($attendanceSession->status !== 'Aktif') {
            return redirect()->route('attendance-sessions.show', $attendanceSession->id)
                ->with('warning', 'Sesi presensi belum aktif. Aktifkan sesi terlebih dahulu.');
        }

        $tokenData = $this->attendanceService->generateQrToken($attendanceSession, 45);

        return view('presensi.sessions.qr_display', compact('attendanceSession', 'tokenData'));
    }

    public function getLatestQrToken(AttendanceSession $attendanceSession)
    {
        if ($attendanceSession->status !== 'Aktif') {
            return response()->json([
                'active' => false,
                'message' => 'Sesi presensi tidak aktif.',
            ], 400);
        }

        $tokenData = $this->attendanceService->generateQrToken($attendanceSession, 45);

        return response()->json([
            'active' => true,
            'payload' => $tokenData['payload'],
            'ttl' => $tokenData['ttl'],
            'expired_at' => $tokenData['expired_at']->toIso8601String(),
        ]);
    }

    public function updateMeetingLink(Request $request, AttendanceSession $attendanceSession)
    {
        $validated = $request->validate([
            'link_meeting' => 'nullable|string|max:500',
        ]);

        $attendanceSession->update([
            'link_meeting' => $validated['link_meeting'],
        ]);

        LogAktivitas::catat('Presensi', 'Update Link Zoom Sesi', 'Memperbarui tautan virtual meeting sesi: ' . $attendanceSession->judul_sesi);

        return back()->with('success', 'Tautan Zoom / Virtual Meeting berhasil diperbarui dan disiarkan ke portal peserta!');
    }

    public function destroy(AttendanceSession $attendanceSession)
    {
        $judul = $attendanceSession->judul_sesi;
        $attendanceSession->delete();

        LogAktivitas::catat('Presensi', 'Hapus Sesi Presensi', 'Menghapus sesi presensi: ' . $judul);

        return redirect()->route('attendance-sessions.index')->with('success', 'Sesi presensi berhasil dihapus.');
    }
}
