<?php

namespace App\Exports;

use App\Models\Pengaturan;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class WidyaiswaraIndividuExport implements WithEvents, WithTitle
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

    public function title(): string
    {
        if ($this->sheetTitle) {
            return strtoupper(substr($this->sheetTitle, 0, 25));
        }

        $nama = $this->rekap['widyaiswara']->nama_lengkap ?? 'Widyaiswara';
        $cleanName = preg_replace('/^(Dr\.|Drs\.|Ir\.|Prof\.)\s*/i', '', trim($nama));
        $firstName = explode(' ', trim($cleanName))[0];
        $clean = preg_replace('/[\\\\\/\?\*\:\[\]]/', '', $firstName);
        return strtoupper(substr($clean, 0, 20)) ?: 'REKAP';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Gridlines visible
                $sheet->setShowGridLines(true);

                // Column Widths matching original JP WI Excel
                $sheet->getColumnDimension('A')->setWidth(17.7);
                $sheet->getColumnDimension('B')->setWidth(26.0);
                $sheet->getColumnDimension('C')->setWidth(36.0);
                $sheet->getColumnDimension('D')->setWidth(8.0);
                $sheet->getColumnDimension('E')->setWidth(8.0);
                $sheet->getColumnDimension('F')->setWidth(15.0);
                // Column G is collapsed / hidden in the original template
                $sheet->getColumnDimension('G')->setWidth(0.5);
                $sheet->getColumnDimension('G')->setVisible(false);
                $sheet->getColumnDimension('H')->setWidth(26.0);

                // Set default font to Calibri 11
                $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);

                $wi = $this->rekap['widyaiswara'] ?? null;
                $namaGelar = $wi ? $wi->nama_gelar : 'WIDYAISWARA';
                $jabatan = $wi ? $wi->jabatan : '-';
                $namaBulan = $this->rekap['nama_bulan'] ?? $this->getNamaBulan($this->bulan);

                // Row 1: Title
                $sheet->setCellValue('A1', 'REKAPITULASI JP WAJIB WIDYAISWARA');
                $sheet->mergeCells('A1:H1');
                $sheet->getStyle('A1')->getFont()->setSize(14)->setBold(true);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getRowDimension(1)->setRowHeight(22);

                // Row 2: Bulan & Tahun
                $sheet->setCellValue('A2', 'BULAN: ' . strtoupper($namaBulan) . ' ' . $this->tahun);
                $sheet->mergeCells('A2:H2');
                $sheet->getStyle('A2')->getFont()->setSize(12)->setBold(true);
                $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getRowDimension(2)->setRowHeight(20);

                // Row 4: Nama
                $sheet->setCellValue('A4', 'NAMA');
                $sheet->setCellValue('B4', ': ' . strtoupper($namaGelar));
                $sheet->getStyle('A4')->getFont()->setSize(12)->setBold(true);
                $sheet->getStyle('B4')->getFont()->setSize(12)->setBold(true);

                // Row 5: Jabatan
                $sheet->setCellValue('A5', 'JABATAN');
                $sheet->setCellValue('B5', ': ' . strtoupper($jabatan));
                $sheet->getStyle('A5')->getFont()->setSize(12)->setBold(true);
                $sheet->getStyle('B5')->getFont()->setSize(12)->setBold(true);

                // Row 7: Table Headers
                $sheet->getRowDimension(7)->setRowHeight(30);
                $headers = [
                    'A' => 'TANGGAL',
                    'B' => 'NAMA PELATIHAN',
                    'C' => 'MATA PELATIHAN',
                    'D' => 'JP',
                    'E' => 'JP AKUMULASI',
                    'F' => 'JP DIBAYARKAN',
                    'G' => 'KETERANGAN',
                    'H' => 'KETERANGAN',
                ];
                foreach ($headers as $col => $title) {
                    $sheet->setCellValue($col . '7', $title);
                }

                $thinBorders = [
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                    ],
                ];

                $headerStyle = [
                    'font' => ['bold' => true, 'size' => 11],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                    ],
                ];
                $sheet->getStyle('A7:H7')->applyFromArray($headerStyle);

                // Row 8: Section A. TATAP MUKA
                $sheet->setCellValue('B8', 'A. TATAP MUKA');
                $sheet->getStyle('B8')->getFont()->setBold(true);
                $sheet->getStyle('A8:H8')->applyFromArray($thinBorders);
                $sheet->getRowDimension(8)->setRowHeight(20);

                $currentRow = 9;
                $tatapMukaStartRow = $currentRow;
                $tatapMukaItems = $this->rekap['tatap_muka'] ?? [];

                if (!empty($tatapMukaItems)) {
                    $currentPelatihan = null;
                    $pelatihanStartRow = null;

                    foreach ($tatapMukaItems as $tm) {
                        $sheet->getRowDimension($currentRow)->setRowHeight(22);

                        // Col A: Tanggal
                        $sheet->setCellValue("A$currentRow", $tm['tanggal']);
                        $sheet->getStyle("A$currentRow")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);

                        // Col B: Nama Pelatihan
                        $sheet->setCellValue("B$currentRow", $tm['nama_pelatihan']);
                        $sheet->getStyle("B$currentRow")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setWrapText(true);

                        // Col C: Mata Pelatihan
                        $sheet->setCellValue("C$currentRow", $tm['mata_pelatihan']);
                        $sheet->getStyle("C$currentRow")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setWrapText(true);

                        // Col D: JP
                        $sheet->setCellValue("D$currentRow", $tm['jp'] > 0 ? $tm['jp'] : '');
                        $sheet->getStyle("D$currentRow")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);

                        // Col E: JP Akumulasi
                        $sheet->setCellValue("E$currentRow", $tm['jp_akumulasi'] > 0 ? $tm['jp_akumulasi'] : '');
                        $sheet->getStyle("E$currentRow")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);

                        // Col F: JP Dibayarkan
                        $sheet->setCellValue("F$currentRow", $tm['jp_dibayarkan'] > 0 ? $tm['jp_dibayarkan'] : '');
                        $sheet->getStyle("F$currentRow")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);

                        // Col H: Keterangan (Col G is hidden)
                        $ket = ($tm['keterangan'] && $tm['keterangan'] !== '-') ? $tm['keterangan'] : '';
                        $sheet->setCellValue("H$currentRow", $ket);
                        $sheet->getStyle("H$currentRow")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setWrapText(true);

                        $sheet->getStyle("A$currentRow:H$currentRow")->applyFromArray($thinBorders);

                        // Track consecutive identical pelatihan for vertical merging
                        if ($currentPelatihan === null) {
                            $currentPelatihan = $tm['nama_pelatihan'];
                            $pelatihanStartRow = $currentRow;
                        } elseif ($currentPelatihan !== $tm['nama_pelatihan']) {
                            if ($pelatihanStartRow < $currentRow - 1) {
                                $sheet->mergeCells("B$pelatihanStartRow:B" . ($currentRow - 1));
                                $sheet->getStyle("B$pelatihanStartRow")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
                            }
                            $currentPelatihan = $tm['nama_pelatihan'];
                            $pelatihanStartRow = $currentRow;
                        }

                        $currentRow++;
                    }

                    // Merge trailing identical pelatihan
                    if ($pelatihanStartRow && $pelatihanStartRow < $currentRow - 1) {
                        $sheet->mergeCells("B$pelatihanStartRow:B" . ($currentRow - 1));
                        $sheet->getStyle("B$pelatihanStartRow")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
                    }
                } else {
                    // Empty tatap muka row
                    $sheet->mergeCells("A$currentRow:H$currentRow");
                    $sheet->setCellValue("A$currentRow", "Tidak ada kegiatan tatap muka pada bulan ini.");
                    $sheet->getStyle("A$currentRow")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
                    $sheet->getStyle("A$currentRow")->getFont()->setItalic(true);
                    $sheet->getStyle("A$currentRow:H$currentRow")->applyFromArray($thinBorders);
                    $currentRow++;
                }

                // Row TOTAL A
                $rowTotalA = $currentRow;
                $sheet->getRowDimension($rowTotalA)->setRowHeight(22);
                $sheet->setCellValue("B$rowTotalA", 'TOTAL A');
                if (!empty($tatapMukaItems)) {
                    $lastTMRow = $currentRow - 1;
                    $sheet->setCellValue("D$rowTotalA", "=SUM(D$tatapMukaStartRow:D$lastTMRow)");
                    $sheet->setCellValue("F$rowTotalA", "=SUM(F$tatapMukaStartRow:F$lastTMRow)");
                } else {
                    $sheet->setCellValue("D$rowTotalA", 0);
                    $sheet->setCellValue("F$rowTotalA", 0);
                }
                $minJp = (int) ($this->rekap['minimum_jp'] ?? 32);
                $sheet->setCellValue("H$rowTotalA", 'Jam Wajib ' . $minJp . ' JP');

                $sheet->getStyle("A$rowTotalA:H$rowTotalA")->applyFromArray($thinBorders);
                $sheet->getStyle("B$rowTotalA")->getFont()->setBold(true);
                $sheet->getStyle("D$rowTotalA")->getFont()->setBold(true);
                $sheet->getStyle("D$rowTotalA")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("F$rowTotalA")->getFont()->setBold(true);
                $sheet->getStyle("F$rowTotalA")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("H$rowTotalA")->getFont()->setBold(true);
                $currentRow++;

                // Row B. KONVERSI
                $rowKonversiHeader = $currentRow;
                $sheet->getRowDimension($rowKonversiHeader)->setRowHeight(20);
                $sheet->setCellValue("B$rowKonversiHeader", 'B. KONVERSI');
                $sheet->setCellValue("D$rowKonversiHeader", 0);
                $sheet->getStyle("B$rowKonversiHeader")->getFont()->setBold(true);
                $sheet->getStyle("D$rowKonversiHeader")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("A$rowKonversiHeader:H$rowKonversiHeader")->applyFromArray($thinBorders);
                $currentRow++;

                $konversiStartRow = $currentRow;
                $konversiItems = $this->rekap['konversi'] ?? [];

                if (!empty($konversiItems)) {
                    foreach ($konversiItems as $kv) {
                        $sheet->getRowDimension($currentRow)->setRowHeight(24);

                        // Col A: Tanggal
                        $sheet->setCellValue("A$currentRow", $kv['tanggal']);
                        $sheet->getStyle("A$currentRow")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);

                        // Col B & C: Nama Kegiatan (Merge B:C for ample width)
                        $sheet->mergeCells("B$currentRow:C$currentRow");
                        $sheet->setCellValue("B$currentRow", $kv['nama_kegiatan']);
                        $sheet->getStyle("B$currentRow")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setWrapText(true);

                        // Col D: JP
                        $sheet->setCellValue("D$currentRow", $kv['jp']);
                        $sheet->getStyle("D$currentRow")->getFont()->setBold(true);
                        $sheet->getStyle("D$currentRow")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);

                        // Col F: JP Dibayarkan
                        if (($kv['jp_dibayarkan'] ?? 0) > 0) {
                            $sheet->setCellValue("F$currentRow", $kv['jp_dibayarkan']);
                            $sheet->getStyle("F$currentRow")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
                        }

                        // Col H: Keterangan
                        $sheet->setCellValue("H$currentRow", $kv['keterangan'] ?? '');
                        $sheet->getStyle("H$currentRow")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setWrapText(true);

                        $sheet->getStyle("A$currentRow:H$currentRow")->applyFromArray($thinBorders);
                        $currentRow++;
                    }
                }

                // Row TOTAL B
                $rowTotalB = $currentRow;
                $sheet->getRowDimension($rowTotalB)->setRowHeight(22);
                $sheet->setCellValue("B$rowTotalB", 'TOTAL  B');
                if (!empty($konversiItems)) {
                    $lastKVRow = $currentRow - 1;
                    $sheet->setCellValue("D$rowTotalB", "=SUM(D$konversiStartRow:D$lastKVRow)");
                    $sheet->setCellValue("F$rowTotalB", "=SUM(F$konversiStartRow:F$lastKVRow)");
                } else {
                    $sheet->setCellValue("D$rowTotalB", 0);
                    $sheet->setCellValue("F$rowTotalB", 0);
                }
                $sheet->getStyle("A$rowTotalB:H$rowTotalB")->applyFromArray($thinBorders);
                $sheet->getStyle("B$rowTotalB")->getFont()->setBold(true);
                $sheet->getStyle("D$rowTotalB")->getFont()->setBold(true);
                $sheet->getStyle("D$rowTotalB")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("F$rowTotalB")->getFont()->setBold(true);
                $sheet->getStyle("F$rowTotalB")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $currentRow++;

                // Row TOTAL A DAN B
                $rowTotalAB = $currentRow;
                $sheet->getRowDimension($rowTotalAB)->setRowHeight(22);
                $sheet->setCellValue("B$rowTotalAB", 'TOTAL A DAN B');
                $sheet->setCellValue("D$rowTotalAB", "=SUM(D$rowTotalA+D$rowTotalB)");
                $sheet->setCellValue("F$rowTotalAB", "=SUM(F$rowTotalA+F$rowTotalB)");
                $sheet->getStyle("A$rowTotalAB:H$rowTotalAB")->applyFromArray($thinBorders);
                $sheet->getStyle("B$rowTotalAB")->getFont()->setBold(true);
                $sheet->getStyle("D$rowTotalAB")->getFont()->setBold(true);
                $sheet->getStyle("D$rowTotalAB")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("F$rowTotalAB")->getFont()->setBold(true);
                $sheet->getStyle("F$rowTotalAB")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $currentRow++;

                // Signature Block (D to H merged, exactly as in original Excel)
                $sigStart = $currentRow + 1; // 1 blank row spacing

                // Tanggal penandatanganan
                $sheet->mergeCells("D$sigStart:H$sigStart");
                $sheet->setCellValue("D$sigStart", "Jakarta, " . date('j') . " " . $namaBulan . " " . $this->tahun);
                $sheet->getStyle("D$sigStart")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Kepala Instansi
                $rowSig2 = $sigStart + 1;
                $sheet->mergeCells("D$rowSig2:H$rowSig2");
                $instansiNama = Pengaturan::get('instansi_nama', 'Pusat Pengembangan SDM');
                $sheet->setCellValue("D$rowSig2", "Kepala " . $instansiNama);
                $sheet->getStyle("D$rowSig2")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Sub Instansi
                $rowSig3 = $sigStart + 2;
                $sheet->mergeCells("D$rowSig3:H$rowSig3");
                $instansiSub = Pengaturan::get('instansi_sub', 'Ketenagakerjaan');
                $sheet->setCellValue("D$rowSig3", $instansiSub);
                $sheet->getStyle("D$rowSig3")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Nama Pimpinan (after blank rows for signature)
                $rowSigName = $sigStart + 7;
                $sheet->mergeCells("D$rowSigName:H$rowSigName");
                $pimpinanNama = Pengaturan::get('pimpinan_nama', 'Dr. Narsih, S.Pd., M.M.');
                $sheet->setCellValue("D$rowSigName", $pimpinanNama);
                $sheet->getStyle("D$rowSigName")->getFont()->setBold(true);
                $sheet->getStyle("D$rowSigName")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // NIP Pimpinan
                $rowSigNip = $sigStart + 8;
                $sheet->mergeCells("D$rowSigNip:H$rowSigNip");
                $pimpinanNip = Pengaturan::get('pimpinan_nip', '197202111998032002');
                $sheet->setCellValue("D$rowSigNip", "NIP. " . $pimpinanNip);
                $sheet->getStyle("D$rowSigNip")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            },
        ];
    }

    protected function getNamaBulan(int $bulan): string
    {
        $nama = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        return $nama[$bulan] ?? 'Bulan';
    }
}
