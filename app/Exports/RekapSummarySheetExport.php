<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class RekapSummarySheetExport implements FromView, WithTitle, ShouldAutoSize
{
    protected Collection $rekap;
    protected int $bulan;
    protected int $tahun;
    protected string $namaBulan;
    protected float $minimumJp;

    public function __construct(Collection $rekap, int $bulan, int $tahun, string $namaBulan, float $minimumJp)
    {
        $this->rekap = $rekap;
        $this->bulan = $bulan;
        $this->tahun = $tahun;
        $this->namaBulan = $namaBulan;
        $this->minimumJp = $minimumJp;
    }

    public function view(): View
    {
        return view('exports.rekap_semua_widyaiswara_excel', [
            'rekap' => $this->rekap,
            'bulan' => $this->bulan,
            'tahun' => $this->tahun,
            'namaBulan' => $this->namaBulan,
            'minimumJp' => $this->minimumJp,
        ]);
    }

    public function title(): string
    {
        return 'REKAPITULASI';
    }
}
