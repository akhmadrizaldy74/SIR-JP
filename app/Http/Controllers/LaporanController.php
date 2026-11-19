<?php

namespace App\Http\Controllers;

use App\Services\JPRecapitulationService;
use App\Models\Widyaiswara;
use App\Models\Narasumber;
use App\Models\Pelatihan;
use App\Models\LogAktivitas;
use App\Exports\WidyaiswaraIndividuExport;
use App\Exports\RekapSemuaWidyaiswaraExport;
use App\Exports\RekapTahunanExport;
use App\Exports\PelatihanExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class LaporanController extends Controller
{
    protected JPRecapitulationService $jpService;

    public function __construct(JPRecapitulationService $jpService)
    {
        $this->jpService = $jpService;
    }

    public function index(Request $request)
    {
        $bulan = (int) $request->get('bulan', Carbon::now()->month);
        $tahun = (int) $request->get('tahun', Carbon::now()->year);

        $widyaiswaras = Widyaiswara::where('status', 'Aktif')->orderBy('nama_lengkap', 'asc')->get();
        $narasumbers = Narasumber::where('status', 'Aktif')->orderBy('nama_lengkap', 'asc')->get();
        $pelatihans = Pelatihan::orderBy('nama_pelatihan', 'asc')->get();

        return view('laporan.index', compact('widyaiswaras', 'narasumbers', 'pelatihans', 'bulan', 'tahun'));
    }

    public function exportExcelIndividu($id, Request $request)
    {
        $bulan = (int) $request->get('bulan', Carbon::now()->month);
        $tahun = (int) $request->get('tahun', Carbon::now()->year);

        $rekap = $this->jpService->hitungWidyaiswara((int) $id, $bulan, $tahun);
        $filename = 'Rekap_JP_' . str_replace(' ', '_', $rekap['widyaiswara']->nama_lengkap) . '_' . $bulan . '_' . $tahun . '.xlsx';

        LogAktivitas::catat('Laporan', 'Ekspor Excel Individu', 'Mengunduh rekap individu ' . $rekap['widyaiswara']->nama_lengkap);

        return Excel::download(new WidyaiswaraIndividuExport($rekap, $bulan, $tahun), $filename);
    }

    public function exportPdfIndividu($id, Request $request)
    {
        $bulan = (int) $request->get('bulan', Carbon::now()->month);
        $tahun = (int) $request->get('tahun', Carbon::now()->year);

        $rekap = $this->jpService->hitungWidyaiswara((int) $id, $bulan, $tahun);
        $filename = 'Rekap_JP_' . str_replace(' ', '_', $rekap['widyaiswara']->nama_lengkap) . '_' . $bulan . '_' . $tahun . '.pdf';

        LogAktivitas::catat('Laporan', 'Ekspor PDF Individu', 'Mencetak rekap individu ' . $rekap['widyaiswara']->nama_lengkap);

        $pdf = Pdf::loadView('pdf.individu_widyaiswara', [
            'rekap' => $rekap,
            'bulan' => $bulan,
            'tahun' => $tahun,
        ])->setPaper('a4', 'portrait');

        return $pdf->stream($filename);
    }

    public function exportExcelSemua(Request $request)
    {
        $bulan = (int) $request->get('bulan', Carbon::now()->month);
        $tahun = (int) $request->get('tahun', Carbon::now()->year);

        $rekap = $this->jpService->rekapSemuaWidyaiswara($bulan, $tahun);
        $namaBulan = $this->jpService->getNamaBulan($bulan);
        $minimumJp = $this->jpService->getMinimumJp();

        $filename = 'Rekap_Semua_Widyaiswara_' . $bulan . '_' . $tahun . '.xlsx';

        LogAktivitas::catat('Laporan', 'Ekspor Excel Seluruh Widyaiswara', 'Mengunduh rekap seluruh WI periode ' . $namaBulan . ' ' . $tahun);

        return Excel::download(new RekapSemuaWidyaiswaraExport($rekap, $bulan, $tahun, $namaBulan, $minimumJp), $filename);
    }

    public function exportPdfSemua(Request $request)
    {
        $bulan = (int) $request->get('bulan', Carbon::now()->month);
        $tahun = (int) $request->get('tahun', Carbon::now()->year);

        $rekap = $this->jpService->rekapSemuaWidyaiswara($bulan, $tahun);
        $namaBulan = $this->jpService->getNamaBulan($bulan);
        $minimumJp = $this->jpService->getMinimumJp();

        $filename = 'Rekap_Semua_Widyaiswara_' . $bulan . '_' . $tahun . '.pdf';

        LogAktivitas::catat('Laporan', 'Ekspor PDF Seluruh Widyaiswara', 'Mencetak PDF seluruh WI periode ' . $namaBulan . ' ' . $tahun);

        $pdf = Pdf::loadView('pdf.rekap_semua_widyaiswara', [
            'rekap' => $rekap,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'namaBulan' => $namaBulan,
            'minimumJp' => $minimumJp,
        ])->setPaper('a4', 'landscape');

        return $pdf->stream($filename);
    }

    public function exportExcelTahunan(Request $request)
    {
        $tahun = (int) $request->get('tahun', Carbon::now()->year);
        $rekapTahunan = $this->jpService->rekapTahunanWidyaiswara($tahun);

        $filename = 'Rekap_Tahunan_Widyaiswara_' . $tahun . '.xlsx';

        LogAktivitas::catat('Laporan', 'Ekspor Excel Tahunan', 'Mengunduh rekap tahunan ' . $tahun);

        return Excel::download(new RekapTahunanExport($rekapTahunan, $tahun), $filename);
    }

    public function exportPdfTahunan(Request $request)
    {
        $tahun = (int) $request->get('tahun', Carbon::now()->year);
        $rekapTahunan = $this->jpService->rekapTahunanWidyaiswara($tahun);

        $filename = 'Rekap_Tahunan_Widyaiswara_' . $tahun . '.pdf';

        LogAktivitas::catat('Laporan', 'Ekspor PDF Tahunan', 'Mencetak PDF tahunan ' . $tahun);

        $pdf = Pdf::loadView('pdf.tahunan', [
            'rekapTahunan' => $rekapTahunan,
            'tahun' => $tahun,
        ])->setPaper('a4', 'landscape');

        return $pdf->stream($filename);
    }

    public function exportPdfNarasumber($id, Request $request)
    {
        $bulan = (int) $request->get('bulan', Carbon::now()->month);
        $tahun = (int) $request->get('tahun', Carbon::now()->year);

        $rekap = $this->jpService->hitungNarasumber((int) $id, $bulan, $tahun);
        $filename = 'Aktivitas_Narasumber_' . str_replace(' ', '_', $rekap['narasumber']->nama_lengkap) . '_' . $bulan . '_' . $tahun . '.pdf';

        LogAktivitas::catat('Laporan', 'Ekspor PDF Narasumber', 'Mencetak PDF narasumber ' . $rekap['narasumber']->nama_lengkap);

        $pdf = Pdf::loadView('pdf.aktivitas_narasumber', [
            'rekap' => $rekap,
            'bulan' => $bulan,
            'tahun' => $tahun,
        ])->setPaper('a4', 'portrait');

        return $pdf->stream($filename);
    }

    public function exportExcelPelatihan()
    {
        $pelatihans = Pelatihan::orderBy('nama_pelatihan', 'asc')->get();
        $filename = 'Daftar_Program_Pelatihan_' . date('Ymd') . '.xlsx';

        LogAktivitas::catat('Laporan', 'Ekspor Excel Pelatihan', 'Mengunduh master data program pelatihan');

        return Excel::download(new PelatihanExport($pelatihans), $filename);
    }
}
