<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Illuminate\Support\Collection;
use App\Models\Widyaiswara;
use App\Services\JPRecapitulationService;

class RekapSemuaWidyaiswaraExport implements WithMultipleSheets
{
    protected Collection $rekapSummary;
    protected int $bulan;
    protected int $tahun;
    protected string $namaBulan;
    protected float $minimumJp;

    public function __construct(Collection $rekapSummary, int $bulan, int $tahun, string $namaBulan, float $minimumJp)
    {
        $this->rekapSummary = $rekapSummary;
        $this->bulan = $bulan;
        $this->tahun = $tahun;
        $this->namaBulan = $namaBulan;
        $this->minimumJp = $minimumJp;
    }

    public function sheets(): array
    {
        $sheets = [];

        // Sheet 1: Master Summary of all Widyaiswaras
        $sheets[] = new RekapSummarySheetExport(
            $this->rekapSummary,
            $this->bulan,
            $this->tahun,
            $this->namaBulan,
            $this->minimumJp
        );

        // Sheet 2 to N: Individual sheet for each Widyaiswara
        $service = app(JPRecapitulationService::class);
        $widyaiswaras = Widyaiswara::where('status', 'Aktif')->orderBy('id', 'asc')->get();

        $usedTitles = ['REKAPITULASI' => true];

        foreach ($widyaiswaras as $wi) {
            $individualRekap = $service->hitungWidyaiswara($wi->id, $this->bulan, $this->tahun);

            // Clean title: first name or short name
            $cleanName = preg_replace('/^(Dr\.|Drs\.|Ir\.|Prof\.)\s*/i', '', trim($wi->nama_lengkap));
            $firstName = explode(' ', trim($cleanName))[0];
            $cleanTitle = preg_replace('/[\\\\\/\?\*\:\[\]]/', '', $firstName);
            $sheetTitle = strtoupper(substr($cleanTitle, 0, 20)) ?: 'WI-' . $wi->id;

            // Ensure unique title
            $baseTitle = $sheetTitle;
            $counter = 1;
            while (isset($usedTitles[$sheetTitle])) {
                $sheetTitle = substr($baseTitle, 0, 18) . '-' . $counter;
                $counter++;
            }
            $usedTitles[$sheetTitle] = true;

            $sheets[] = new WidyaiswaraIndividuExport($individualRekap, $this->bulan, $this->tahun, $sheetTitle);
        }

        return $sheets;
    }
}
