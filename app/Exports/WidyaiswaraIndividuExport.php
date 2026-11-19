<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class WidyaiswaraIndividuExport implements FromView, ShouldAutoSize
{
    protected array $rekap;
    protected int $bulan;
    protected int $tahun;

    public function __construct(array $rekap, int $bulan, int $tahun)
    {
        $this->rekap = $rekap;
        $this->bulan = $bulan;
        $this->tahun = $tahun;
    }

    public function view(): View
    {
        return view('exports.widyaiswara_individu_excel', [
            'rekap' => $this->rekap,
            'bulan' => $this->bulan,
            'tahun' => $this->tahun,
        ]);
    }
}
