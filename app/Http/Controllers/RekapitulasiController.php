<?php

namespace App\Http\Controllers;

use App\Services\JPRecapitulationService;
use App\Models\Widyaiswara;
use App\Models\Narasumber;
use App\Models\Pengaturan;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RekapitulasiController extends Controller
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
        $jabatan = $request->get('jabatan');
        $search = $request->get('search');
        $jenisPengajar = $request->get('jenis_pengajar', 'Widyaiswara');

        $rekapWidyaiswara = $this->jpService->rekapSemuaWidyaiswara($bulan, $tahun, $jabatan, $search);
        $rekapNarasumber = $this->jpService->rekapSemuaNarasumber($bulan, $tahun, $search);

        $jabatanOptions = [
            'Widyaiswara Ahli Pertama',
            'Widyaiswara Ahli Muda',
            'Widyaiswara Ahli Madya',
            'Widyaiswara Ahli Utama',
        ];

        $namaBulan = $this->jpService->getNamaBulan($bulan);
        $minimumJp = $this->jpService->getMinimumJp();

        return view('rekapitulasi.index', compact(
            'rekapWidyaiswara',
            'rekapNarasumber',
            'bulan',
            'tahun',
            'namaBulan',
            'jabatan',
            'search',
            'jenisPengajar',
            'jabatanOptions',
            'minimumJp'
        ));
    }

    public function detail(Request $request, $id)
    {
        $bulan = (int) $request->get('bulan', Carbon::now()->month);
        $tahun = (int) $request->get('tahun', Carbon::now()->year);

        $rekap = $this->jpService->hitungWidyaiswara((int) $id, $bulan, $tahun);

        $allWidyaiswaras = Widyaiswara::where('status', 'Aktif')->orderBy('nama_lengkap', 'asc')->get();

        return view('rekapitulasi.detail', compact('rekap', 'bulan', 'tahun', 'allWidyaiswaras'));
    }

    public function detailNarasumber(Request $request, $id)
    {
        $bulan = (int) $request->get('bulan', Carbon::now()->month);
        $tahun = (int) $request->get('tahun', Carbon::now()->year);

        $rekap = $this->jpService->hitungNarasumber((int) $id, $bulan, $tahun);

        return view('rekapitulasi.detail_narasumber', compact('rekap', 'bulan', 'tahun'));
    }

    public function tahunan(Request $request)
    {
        $tahun = (int) $request->get('tahun', Carbon::now()->year);
        $jabatan = $request->get('jabatan');

        $rekapTahunan = $this->jpService->rekapTahunanWidyaiswara($tahun, $jabatan);

        $jabatanOptions = [
            'Widyaiswara Ahli Pertama',
            'Widyaiswara Ahli Muda',
            'Widyaiswara Ahli Madya',
            'Widyaiswara Ahli Utama',
        ];

        return view('rekapitulasi.tahunan', compact('rekapTahunan', 'tahun', 'jabatan', 'jabatanOptions'));
    }
}
