<?php

namespace App\Http\Controllers;

use App\Services\AttendanceService;
use App\Models\AttendanceSession;
use App\Models\Presensi;
use App\Models\Peserta;
use App\Models\Pelatihan;
use App\Models\Angkatan;
use App\Models\KelasMapel;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PresensiExport;

class AttendanceReportController extends Controller
{
    protected AttendanceService $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    public function index(Request $request)
    {
        $pelatihanId = $request->get('pelatihan_id');
        $angkatanId = $request->get('angkatan_id');
        $kelasMapelId = $request->get('kelas_mapel_id');

        $stats = $this->attendanceService->getDashboardStatistics($pelatihanId, $angkatanId, $kelasMapelId);

        $pelatihans = Pelatihan::orderBy('nama_pelatihan')->get();
        $angkatans = Angkatan::with('pelatihan')->orderBy('nomor_angkatan')->get();
        $kelasMapels = KelasMapel::orderBy('nama_kelas')->get();

        // 5 sesi terkini
        $recentSessions = AttendanceSession::with(['jadwal.mataPelatihan', 'kelasMapel.ruangan', 'presensis'])
            ->latest('tanggal')
            ->latest('jam_mulai')
            ->take(5)
            ->get();

        // 10 aktivitas presensi terakhir
        $recentLogs = Presensi::with(['peserta', 'session.jadwal.mataPelatihan'])
            ->latest('waktu_check_in')
            ->take(10)
            ->get();

        return view('presensi.index', compact('stats', 'pelatihans', 'angkatans', 'kelasMapels', 'pelatihanId', 'angkatanId', 'kelasMapelId', 'recentSessions', 'recentLogs'));
    }

    public function rekap(Request $request)
    {
        $kelasMapelId = $request->get('kelas_mapel_id');
        $angkatanId = $request->get('angkatan_id');
        $pelatihanId = $request->get('pelatihan_id');

        $kelasMapels = KelasMapel::with(['pelatihan', 'angkatan'])->orderBy('nama_kelas')->get();
        $selectedKelas = $kelasMapelId ? KelasMapel::with(['pelatihan', 'angkatan'])->find($kelasMapelId) : $kelasMapels->first();

        $sessions = collect();
        $pesertas = collect();
        $matrix = [];

        if ($selectedKelas) {
            $sessions = AttendanceSession::where('kelas_mapel_id', $selectedKelas->id)
                ->orderBy('tanggal')
                ->orderBy('jam_mulai')
                ->get();

            $pesertas = Peserta::where(function ($q) use ($selectedKelas) {
                $q->where('kelas_mapel_id', $selectedKelas->id)
                  ->orWhere('angkatan_id', $selectedKelas->angkatan_id);
            })->orderBy('nama')->get();

            $allPresensis = Presensi::whereIn('attendance_session_id', $sessions->pluck('id'))
                ->get()
                ->groupBy('peserta_id');

            foreach ($pesertas as $pst) {
                $userPresensis = $allPresensis->get($pst->id, collect())->keyBy('attendance_session_id');

                $hadir = 0;
                $terlambat = 0;
                $izin = 0;
                $sakit = 0;
                $alpa = 0;
                $dispensasi = 0;

                $sessionDetails = [];
                foreach ($sessions as $sess) {
                    $record = $userPresensis->get($sess->id);
                    $st = $record ? $record->status_kehadiran : '-';
                    $sessionDetails[$sess->id] = $record;

                    match ($st) {
                        'Hadir' => $hadir++,
                        'Terlambat' => $terlambat++,
                        'Izin' => $izin++,
                        'Sakit' => $sakit++,
                        'Alpa' => $alpa++,
                        'Dispensasi' => $dispensasi++,
                        default => null,
                    };
                }

                $totalSesi = $sessions->count();
                $persen = $totalSesi > 0 ? round((($hadir + $terlambat) / $totalSesi) * 100, 1) : 0;

                $matrix[$pst->id] = [
                    'peserta' => $pst,
                    'hadir' => $hadir,
                    'terlambat' => $terlambat,
                    'izin' => $izin,
                    'sakit' => $sakit,
                    'alpa' => $alpa,
                    'dispensasi' => $dispensasi,
                    'persen' => $persen,
                    'sessions' => $sessionDetails,
                ];
            }
        }

        return view('presensi.reports.rekap', compact('kelasMapels', 'selectedKelas', 'sessions', 'pesertas', 'matrix'));
    }

    public function exportExcel(Request $request)
    {
        $kelasMapelId = $request->get('kelas_mapel_id');
        $kelas = KelasMapel::findOrFail($kelasMapelId);

        $rekapData = $this->getRekapData($kelas);

        LogAktivitas::catat('Laporan', 'Ekspor Excel Presensi', "Mengunduh rekap presensi kelas {$kelas->nama_kelas}");

        return Excel::download(new PresensiExport($rekapData), 'Rekap_Presensi_' . str_replace(' ', '_', $kelas->nama_kelas) . '.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $kelasMapelId = $request->get('kelas_mapel_id');
        $kelas = KelasMapel::findOrFail($kelasMapelId);

        $rekapData = $this->getRekapData($kelas);

        LogAktivitas::catat('Laporan', 'Ekspor PDF Presensi', "Mencetak PDF rekap presensi kelas {$kelas->nama_kelas}");

        $pdf = Pdf::loadView('exports.presensi_pdf', $rekapData)->setPaper('a4', 'landscape');
        return $pdf->stream('Rekap_Presensi_' . str_replace(' ', '_', $kelas->nama_kelas) . '.pdf');
    }

    protected function getRekapData(KelasMapel $kelas): array
    {
        $sessions = AttendanceSession::where('kelas_mapel_id', $kelas->id)
            ->orderBy('tanggal')
            ->orderBy('jam_mulai')
            ->get();

        $pesertas = Peserta::where(function ($q) use ($kelas) {
            $q->where('kelas_mapel_id', $kelas->id)
              ->orWhere('angkatan_id', $kelas->angkatan_id);
        })->orderBy('nama')->get();

        $allPresensis = Presensi::whereIn('attendance_session_id', $sessions->pluck('id'))
            ->get()
            ->groupBy('peserta_id');

        $matrix = [];
        foreach ($pesertas as $pst) {
            $userPresensis = $allPresensis->get($pst->id, collect())->keyBy('attendance_session_id');

            $hadir = 0;
            $terlambat = 0;
            $izin = 0;
            $sakit = 0;
            $alpa = 0;
            $dispensasi = 0;

            $sessionDetails = [];
            foreach ($sessions as $sess) {
                $record = $userPresensis->get($sess->id);
                $st = $record ? $record->status_kehadiran : '-';
                $sessionDetails[$sess->id] = $record;

                match ($st) {
                    'Hadir' => $hadir++,
                    'Terlambat' => $terlambat++,
                    'Izin' => $izin++,
                    'Sakit' => $sakit++,
                    'Alpa' => $alpa++,
                    'Dispensasi' => $dispensasi++,
                    default => null,
                };
            }

            $totalSesi = $sessions->count();
            $persen = $totalSesi > 0 ? round((($hadir + $terlambat) / $totalSesi) * 100, 1) : 0;

            $matrix[$pst->id] = [
                'peserta' => $pst,
                'hadir' => $hadir,
                'terlambat' => $terlambat,
                'izin' => $izin,
                'sakit' => $sakit,
                'alpa' => $alpa,
                'dispensasi' => $dispensasi,
                'persen' => $persen,
                'sessions' => $sessionDetails,
            ];
        }

        return [
            'kelas' => $kelas,
            'sessions' => $sessions,
            'pesertas' => $pesertas,
            'matrix' => $matrix,
        ];
    }
}
