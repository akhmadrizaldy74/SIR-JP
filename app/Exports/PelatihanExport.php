<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PelatihanExport implements FromView, ShouldAutoSize
{
    protected Collection $pelatihans;

    public function __construct(Collection $pelatihans)
    {
        $this->pelatihans = $pelatihans;
    }

    public function view(): View
    {
        return view('exports.pelatihan_excel', [
            'pelatihans' => $this->pelatihans,
        ]);
    }
}
