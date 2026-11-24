<?php

namespace App\Http\Controllers;

use App\Services\AttendanceService;
use App\Models\AttendanceSession;
use App\Models\Jadwal;
use App\Models\KelasMapel;
use App\Models\Pelatihan;
use App\Models\Angkatan;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
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
        ]);

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
        $selectedJadwal = $jadwalId ? Jadwal::with(['mataPelatihan.angkatan.pelatihan', 'penugasanPengajar.widyaiswara'])->find($jadwalId) : null;

        $jadwals = Jadwal::with(['mataPelatihan.angkatan.pelatihan', 'penugasanPengajar.widyaiswara'])
            ->where('status', 'Terjadwal')
            ->orderBy('tanggal', 'desc')
            ->get();

        $kelasMapels = KelasMapel::with(['pelatihan', 'angkatan'])->orderBy('nama_kelas')->get();

        return view('presensi.sessions.create', compact('jadwals', 'selectedJadwal', 'kelasMapels'));
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

        // Jika kelas_mapel_id tidak dipilih, cari kelas mapel terkait
        $kelasMapelId = $validated['kelas_mapel_id'];
        if (!$kelasMapelId && $jadwal->mataPelatihan) {
            $km = KelasMapel::where('mata_pelatihan_id', $jadwal->mata_pelatihan_id)->first();
            if ($km) {
                $kelasMapelId = $km->id;
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
        $attendanceSession->load([
            'jadwal.mataPelatihan.angkatan.pelatihan',
            'jadwal.penugasanPengajar.widyaiswara',
            'jadwal.penugasanPengajar.narasumber',
            'kelasMapel.ruangan',
            'documentations',
            'requests.peserta',
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

        return back()->with('success', 'Sesi presensi sekarang AKTIF! QR Code dinamis siap ditampilkan.');
    }

    public function close(AttendanceSession $attendanceSession)
    {
        $attendanceSession->update([
            'status' => 'Selesai',
            'waktu_tutup_presensi' => now(),
        ]);

        // Nonaktifkan semua token QR sesi ini
        $attendanceSession->qrTokens()->update(['is_active' => false]);

        LogAktivitas::catat('Presensi', 'Tutup Sesi Presensi', 'Menutup sesi presensi ID #' . $attendanceSession->id);

        return back()->with('success', 'Sesi presensi telah DITUTUP. Presensi QR tidak lagi diterima.');
    }

    public function qrDisplay(AttendanceSession $attendanceSession)
    {
        if ($attendanceSession->status !== 'Aktif') {
            return redirect()->route('attendance-sessions.show', $attendanceSession->id)
                ->with('warning', 'Sesi presensi belum aktif. Aktifkan sesi terlebih dahulu untuk memproyeksikan QR Code.');
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
