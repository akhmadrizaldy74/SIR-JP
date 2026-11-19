<?php

namespace App\Exports;

use App\Models\Pengaturan;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class RekapSummarySheetExport implements WithEvents, WithTitle
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

    public function title(): string
    {
        return 'REKAPITULASI';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->setShowGridLines(true);

                // Default Font
                $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);

                // Column Widths
                $widths = [
                    'A' => 6,   // No.
                    'B' => 32,  // Nama Widyaiswara
                    'C' => 22,  // NIP
                    'D' => 28,  // Jabatan
                    'E' => 14,  // Tatap Muka (A)
                    'F' => 14,  // Konversi (B)
                    'G' => 15,  // Total (A + B)
                    'H' => 15,  // JP Dibayarkan
                    'I' => 15,  // Kekurangan JP
                    'J' => 18,  // Status Pencapaian
                ];
                foreach ($widths as $col => $w) {
                    $sheet->getColumnDimension($col)->setWidth($w);
                }

                // Row 1: Instansi
                $instansi = Pengaturan::get('instansi_nama', 'Pusat Pengembangan Sumber Daya Manusia');
                $instansiSub = Pengaturan::get('instansi_sub', 'Ketenagakerjaan');
                $fullInstansi = strtoupper($instansi . ' ' . $instansiSub);
                $sheet->setCellValue('A1', $fullInstansi);
                $sheet->mergeCells('A1:J1');
                $sheet->getStyle('A1')->getFont()->setSize(14)->setBold(true);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getRowDimension(1)->setRowHeight(22);

                // Row 2: Title
                $sheet->setCellValue('A2', 'REKAPITULASI JAM PELAJARAN (JP) SELURUH WIDYAISWARA');
                $sheet->mergeCells('A2:J2');
                $sheet->getStyle('A2')->getFont()->setSize(12)->setBold(true);
                $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getRowDimension(2)->setRowHeight(20);

                // Row 3: Periode & Target
                $sheet->setCellValue('A3', 'Periode: ' . strtoupper($this->namaBulan) . ' ' . $this->tahun . ' — Batas Minimum Wajib: ' . (int) $this->minimumJp . ' JP');
                $sheet->mergeCells('A3:J3');
                $sheet->getStyle('A3')->getFont()->setSize(10)->setItalic(true);
                $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getRowDimension(3)->setRowHeight(18);

                // Row 5: Table Headers
                $headers = [
                    'A' => 'No.',
                    'B' => 'Nama Widyaiswara',
                    'C' => 'NIP',
                    'D' => 'Jabatan',
                    'E' => 'Tatap Muka (A)',
                    'F' => 'Konversi (B)',
                    'G' => 'Total (A + B)',
                    'H' => 'JP Dibayarkan',
                    'I' => 'Kekurangan JP',
                    'J' => 'Status Pencapaian',
                ];
                $sheet->getRowDimension(5)->setRowHeight(28);
                foreach ($headers as $col => $title) {
                    $sheet->setCellValue($col . '5', $title);
                }

                $headerStyle = [
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '183B65'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                    ],
                ];
                $sheet->getStyle('A5:J5')->applyFromArray($headerStyle);

                $thinBorders = [
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                    ],
                ];

                $currentRow = 6;
                foreach ($this->rekap as $idx => $r) {
                    $sheet->getRowDimension($currentRow)->setRowHeight(20);

                    $sheet->setCellValue("A$currentRow", $idx + 1);
                    $sheet->setCellValue("B$currentRow", $r['nama_gelar'] ?? $r['nama_lengkap']);
                    $sheet->setCellValue("C$currentRow", $r['nip'] ?? '-');
                    $sheet->setCellValue("D$currentRow", $r['jabatan'] ?? '-');
                    $sheet->setCellValue("E$currentRow", $r['total_a'] ?? 0);
                    $sheet->setCellValue("F$currentRow", $r['total_b'] ?? 0);
                    $sheet->setCellValue("G$currentRow", $r['total_ab'] ?? 0);
                    $sheet->setCellValue("H$currentRow", $r['total_dibayarkan'] ?? 0);
                    $sheet->setCellValue("I$currentRow", ($r['kekurangan_jp'] ?? 0) > 0 ? $r['kekurangan_jp'] : 0);
                    $sheet->setCellValue("J$currentRow", $r['status_pencapaian'] ?? '-');

                    $sheet->getStyle("A$currentRow:J$currentRow")->applyFromArray($thinBorders);

                    // Alignments
                    $sheet->getStyle("A$currentRow")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("C$currentRow")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("E$currentRow:I$currentRow")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("J$currentRow")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                    // Bold on Total AB
                    $sheet->getStyle("G$currentRow")->getFont()->setBold(true);

                    // Color on Kekurangan JP
                    if (($r['kekurangan_jp'] ?? 0) > 0) {
                        $sheet->getStyle("I$currentRow")->getFont()->getColor()->setRGB('DC2626');
                    } else {
                        $sheet->getStyle("I$currentRow")->getFont()->getColor()->setRGB('16A34A');
                    }

                    $currentRow++;
                }

                // Summary Row
                $rowSummary = $currentRow;
                $sheet->getRowDimension($rowSummary)->setRowHeight(22);
                $sheet->mergeCells("A$rowSummary:D$rowSummary");
                $sheet->setCellValue("A$rowSummary", 'TOTAL KESELURUHAN');
                $sheet->getStyle("A$rowSummary")->getFont()->setBold(true);
                $sheet->getStyle("A$rowSummary")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                $lastDataRow = $currentRow - 1;
                $sheet->setCellValue("E$rowSummary", "=SUM(E6:E$lastDataRow)");
                $sheet->setCellValue("F$rowSummary", "=SUM(F6:F$lastDataRow)");
                $sheet->setCellValue("G$rowSummary", "=SUM(G6:G$lastDataRow)");
                $sheet->setCellValue("H$rowSummary", "=SUM(H6:H$lastDataRow)");
                $sheet->setCellValue("I$rowSummary", "=SUM(I6:I$lastDataRow)");

                $sheet->getStyle("A$rowSummary:J$rowSummary")->applyFromArray($thinBorders);
                $sheet->getStyle("E$rowSummary:I$rowSummary")->getFont()->setBold(true);
                $sheet->getStyle("E$rowSummary:I$rowSummary")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $currentRow++;

                // Signature
                $sigRow = $currentRow + 2;
                $kota = Pengaturan::get('pimpinan_kota', 'Jakarta');
                $sheet->mergeCells("G$sigRow:J$sigRow");
                $sheet->setCellValue("G$sigRow", "$kota, " . date('j') . " " . $this->namaBulan . " " . $this->tahun);
                $sheet->getStyle("G$sigRow")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sigRow2 = $sigRow + 1;
                $sheet->mergeCells("G$sigRow2:J$sigRow2");
                $sheet->setCellValue("G$sigRow2", "Kepala " . $instansi);
                $sheet->getStyle("G$sigRow2")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sigRow3 = $sigRow + 2;
                $sheet->mergeCells("G$sigRow3:J$sigRow3");
                $sheet->setCellValue("G$sigRow3", $instansiSub);
                $sheet->getStyle("G$sigRow3")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sigRowName = $sigRow + 7;
                $sheet->mergeCells("G$sigRowName:J$sigRowName");
                $pimpinanNama = Pengaturan::get('pimpinan_nama', 'Dr. Narsih, S.Pd., M.M.');
                $sheet->setCellValue("G$sigRowName", $pimpinanNama);
                $sheet->getStyle("G$sigRowName")->getFont()->setBold(true)->setUnderline(true);
                $sheet->getStyle("G$sigRowName")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sigRowNip = $sigRow + 8;
                $sheet->mergeCells("G$sigRowNip:J$sigRowNip");
                $pimpinanNip = Pengaturan::get('pimpinan_nip', '197202111998032002');
                $sheet->setCellValue("G$sigRowNip", "NIP. " . $pimpinanNip);
                $sheet->getStyle("G$sigRowNip")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            },
        ];
    }
}
