<?php

namespace App\Http\Controllers;

use App\Services\AttendanceService;
use App\Models\Peserta;
use App\Models\Angkatan;
use App\Models\KelasMapel;
use App\Models\ParticipantAccessCode;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;

class ParticipantAccessController extends Controller
{
    protected AttendanceService $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    public function index(Request $request)
    {
        $angkatanId = $request->get('angkatan_id');
        $kelasMapelId = $request->get('kelas_mapel_id');
        $search = $request->get('search');

        $query = Peserta::with(['accessCode', 'angkatan.pelatihan', 'kelasMapel']);

        if ($angkatanId) {
            $query->where('angkatan_id', $angkatanId);
        }

        if ($kelasMapelId) {
            $query->where('kelas_mapel_id', $kelasMapelId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhereHas('accessCode', fn($a) => $a->where('kode_peserta', 'like', "%{$search}%"));
            });
        }

        $pesertas = $query->latest()->paginate(15)->withQueryString();
        $angkatans = Angkatan::with('pelatihan')->orderBy('nomor_angkatan')->get();
        $kelasMapels = KelasMapel::orderBy('nama_kelas')->get();

        return view('presensi.access_codes.index', compact('pesertas', 'angkatans', 'kelasMapels', 'angkatanId', 'kelasMapelId', 'search'));
    }

    public function generateMassal(Request $request)
    {
        $angkatanId = $request->get('angkatan_id');
        $kelasMapelId = $request->get('kelas_mapel_id');

        $query = Peserta::query();
        if ($angkatanId) $query->where('angkatan_id', $angkatanId);
        if ($kelasMapelId) $query->where('kelas_mapel_id', $kelasMapelId);

        $pesertas = $query->get();
        $count = 0;

        foreach ($pesertas as $p) {
            // Jika belum punya kode akses, generate
            if (!$p->accessCode) {
                $this->attendanceService->getOrGenerateAccessCode($p);
                $count++;
            }
        }

        LogAktivitas::catat('Presensi', 'Generate Kode Akses Massal', "Menerbitkan kode akses untuk {$count} peserta diklat.");

        return back()->with('success', "Berhasil menerbitkan kode akses & PIN untuk {$count} peserta diklat!");
    }

    public function resetPin(Request $request, Peserta $peserta)
    {
        $customPin = $request->get('custom_pin');
        $result = $this->attendanceService->resetPin($peserta, $customPin);

        LogAktivitas::catat('Presensi', 'Reset PIN Peserta', "Reset PIN akses untuk peserta {$peserta->nama} ({$result['kode_peserta']})");

        return back()->with('success', "PIN baru untuk {$peserta->nama} ({$result['kode_peserta']}) berhasil diterbitkan: {$result['raw_pin']}");
    }
}
