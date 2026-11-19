<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class WidyaiswaraIndividuExport implements FromView, WithTitle, WithColumnWidths
{
    protected array $rekap;
    protected int $bulan;
    protected int $tahun;
    protected ?string $sheetTitle;

    public function __construct(array $rekap, int $bulan, int $tahun, ?string $sheetTitle = null)
    {
        $this->rekap = $rekap;
        $this->bulan = $bulan;
        $this->tahun = $tahun;
        $this->sheetTitle = $sheetTitle;
    }

    public function view(): View
    {
        return view('exports.widyaiswara_individu_excel', [
            'rekap' => $this->rekap,
            'bulan' => $this->bulan,
            'tahun' => $this->tahun,
        ]);
    }

    public function title(): string
    {
        if ($this->sheetTitle) {
            return strtoupper(substr($this->sheetTitle, 0, 30));
        }

        $nama = $this->rekap['widyaiswara']->nama_lengkap ?? 'Widyaiswara';
        $firstName = explode(' ', trim($nama))[0];
        // Clean special characters invalid in sheet names: \ / ? * : [ ]
        $clean = preg_replace('/[\\\\\/\?\*\:\[\]]/', '', $firstName);
        return strtoupper(substr($clean, 0, 25)) ?: 'REKAP';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 16,
            'B' => 26,
            'C' => 38,
            'D' => 10,
            'E' => 12,
            'F' => 15,
            'G' => 18,
            'H' => 18,
        ];
    }
}
