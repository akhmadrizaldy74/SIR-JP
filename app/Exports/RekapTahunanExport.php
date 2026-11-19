<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class RekapTahunanExport implements FromView, ShouldAutoSize
{
    protected Collection $rekapTahunan;
    protected int $tahun;

    public function __construct(Collection $rekapTahunan, int $tahun)
    {
        $this->rekapTahunan = $rekapTahunan;
        $this->tahun = $tahun;
    }

    public function view(): View
    {
        return view('exports.rekap_tahunan_excel', [
            'rekapTahunan' => $this->rekapTahunan,
            'tahun' => $this->tahun,
        ]);
    }
}
