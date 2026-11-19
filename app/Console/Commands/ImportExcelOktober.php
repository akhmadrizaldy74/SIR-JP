<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use App\Models\Widyaiswara;
use App\Models\Pelatihan;
use App\Models\Angkatan;
use App\Models\MataPelatihan;
use App\Models\PenugasanPengajar;
use App\Models\Jadwal;
use App\Models\RealisasiJadwal;
use App\Models\RealisasiPengajar;
use App\Models\Pengaturan;
use Illuminate\Support\Str;

class ImportExcelOktober extends Command
{
    protected $signature = 'sirjp:import-excel-oktober {path?}';
    protected $description = 'Import data rekapitulasi JP resmi bulan Oktober 2026 dari file Excel Kemnaker';

    public function handle(): int
    {
        $filePath = $this->argument('path') ?? 'c:/Users/Akhmad Rizaldy/Downloads/JP WI  Bulan Oktober.xlsx';

        if (!file_exists($filePath)) {
            $this->error("File tidak ditemukan pada path: {$filePath}");
            return Command::FAILURE;
        }

        $this->info("Membaca file Excel: {$filePath} ...");
        $spreadsheet = IOFactory::load($filePath);
        $sheetNames = $spreadsheet->getSheetNames();
        $this->info("Ditemukan " . count($sheetNames) . " sheet dalam file Excel.");

        // 1. Update Pengaturan Instansi Kemnaker
        Pengaturan::set('instansi_nama', 'Pusat Pengembangan SDM Ketenagakerjaan');
        Pengaturan::set('instansi_sub', 'Ketenagakerjaan');
        Pengaturan::set('pimpinan_nama', 'Dr. Narsih, S.Pd., M.M.');
        Pengaturan::set('pimpinan_nip', '197202111998032002');
        Pengaturan::set('pimpinan_jabatan', 'Kepala Pusat Pengembangan SDM Ketenagakerjaan');
        Pengaturan::set('pimpinan_kota', 'Jakarta');
        Pengaturan::set('target_jp_widyaiswara', 32);

        $totalWi = 0;
        $totalActivities = 0;

        foreach ($sheetNames as $idx => $sheetName) {
            $sheet = $spreadsheet->getSheetByName($sheetName);
            $namaRaw = trim((string)$sheet->getCell('B4')->getValue());
            $jabatanRaw = trim((string)$sheet->getCell('B5')->getValue());

            $namaClean = trim(str_replace([':', '  '], ['', ' '], $namaRaw));
            $jabatanClean = trim(str_replace([':', '  '], ['', ' '], $jabatanRaw));

            if (!$namaClean) {
                continue;
            }

            // Normalisasi jabatan
            if (stripos($jabatanClean, 'UTAMA') !== false) {
                $jabatanStd = 'Widyaiswara Ahli Utama';
            } elseif (stripos($jabatanClean, 'MADYA') !== false) {
                $jabatanStd = 'Widyaiswara Ahli Madya';
            } elseif (stripos($jabatanClean, 'MUDA') !== false) {
                $jabatanStd = 'Widyaiswara Ahli Muda';
            } else {
                $jabatanStd = 'Widyaiswara Ahli Pertama';
            }

            // Pisahkan gelar
            $parts = explode(',', $namaClean, 2);
            $namaTanpaGelar = trim($parts[0]);
            $gelar = isset($parts[1]) ? trim($parts[1]) : null;

            // Generate NIK & NIP realistis
            $nikDummy = '317101' . sprintf('%02d', $idx + 1) . '85000' . sprintf('%02d', $idx + 1);
            $nipDummy = '1975' . sprintf('%02d', ($idx % 12) + 1) . '152005011' . sprintf('%03d', $idx + 1);

            $wi = Widyaiswara::firstOrCreate(
                ['nama_lengkap' => $namaTanpaGelar],
                [
                    'nik' => $nikDummy,
                    'nip' => $nipDummy,
                    'gelar' => $gelar,
                    'jabatan' => $jabatanStd,
                    'pangkat_golongan' => 'Pembina (IV/a)',
                    'instansi' => 'Pusat Pengembangan SDM Ketenagakerjaan',
                    'email' => Str::slug($namaTanpaGelar) . '@kemnaker.go.id',
                    'nomor_telepon' => '0812' . rand(10000000, 99999999),
                    'status' => 'Aktif',
                ]
            );

            // Pastikan gelar & jabatan terupdate
            $wi->update([
                'gelar' => $gelar,
                'jabatan' => $jabatanStd,
                'instansi' => 'Pusat Pengembangan SDM Ketenagakerjaan',
            ]);

            $totalWi++;

            // Loop activities
            $highestRow = $sheet->getHighestRow();
            $currentPelatihan = '';

            for ($r = 9; $r <= $highestRow; $r++) {
                $valB = trim((string)$sheet->getCell("B$r")->getValue());
                if (strtoupper($valB) === 'TOTAL A') {
                    break;
                }

                $valA = $sheet->getCell("A$r")->getValue();
                if ($valB !== '') {
                    $currentPelatihan = $valB;
                }

                $mataPelatihanText = trim((string)$sheet->getCell("C$r")->getValue());
                $jpVal = (float)$sheet->getCell("D$r")->getCalculatedValue();
                $jpAkumulasiVal = (float)$sheet->getCell("E$r")->getCalculatedValue();
                $jpDibayarRaw = $sheet->getCell("F$r")->getCalculatedValue();
                $ket1 = trim((string)$sheet->getCell("G$r")->getValue());
                $ket2 = trim((string)$sheet->getCell("H$r")->getValue());

                if ($mataPelatihanText !== '' || $jpVal > 0) {
                    $tanggal = null;
                    if (is_numeric($valA)) {
                        $tanggal = Date::excelToDateTimeObject($valA)->format('Y-m-d');
                    } elseif ($valA) {
                        $tanggal = date('Y-m-d', strtotime(str_replace('/', '-', $valA)));
                    } else {
                        $tanggal = '2026-10-15';
                    }

                    // Parse Pelatihan & Angkatan
                    $pelatihanRaw = $currentPelatihan ?: 'Pelatihan Teknis Ketenagakerjaan';
                    [$namaPelatihan, $nomorAngkatan] = $this->parsePelatihanAngkatan($pelatihanRaw);

                    $kodePelatihan = 'PEL-' . strtoupper(Str::slug(substr($namaPelatihan, 0, 15)));
                    $pelatihan = Pelatihan::firstOrCreate(
                        ['kode_pelatihan' => $kodePelatihan],
                        [
                            'nama_pelatihan' => $namaPelatihan,
                            'kategori' => 'Teknis',
                            'penyelenggara' => 'Pusat Pengembangan SDM Ketenagakerjaan',
                            'tanggal_mulai' => '2026-10-01',
                            'tanggal_selesai' => '2026-10-31',
                            'status' => 'Berjalan',
                            'keterangan' => 'Pelatihan Anggaran 2026',
                        ]
                    );

                    $angkatan = Angkatan::firstOrCreate(
                        [
                            'pelatihan_id' => $pelatihan->id,
                            'nomor_angkatan' => $nomorAngkatan,
                        ],
                        [
                            'tanggal_mulai' => '2026-10-01',
                            'tanggal_selesai' => '2026-10-31',
                            'jumlah_peserta' => 30,
                            'status' => 'Berjalan',
                            'keterangan' => 'Pelatihan ' . $pelatihanRaw,
                        ]
                    );

                    $mataPelatihan = MataPelatihan::firstOrCreate(
                        [
                            'angkatan_id' => $angkatan->id,
                            'nama_mata_pelatihan' => $mataPelatihanText ?: 'Pembelajaran Tatap Muka',
                        ],
                        [
                            'rencana_jp' => max(1, $jpVal),
                            'keterangan' => 'Mata pelatihan ' . $pelatihanRaw,
                        ]
                    );

                    // Penugasan
                    $penugasan = PenugasanPengajar::firstOrCreate(
                        [
                            'mata_pelatihan_id' => $mataPelatihan->id,
                            'jenis_pengajar' => 'Widyaiswara',
                            'widyaiswara_id' => $wi->id,
                        ],
                        [
                            'peran' => 'Pengajar Utama',
                        ]
                    );

                    // Jadwal
                    $jadwal = Jadwal::firstOrCreate(
                        [
                            'mata_pelatihan_id' => $mataPelatihan->id,
                            'penugasan_pengajar_id' => $penugasan->id,
                            'tanggal' => $tanggal,
                            'sesi_ke' => $r,
                        ],
                        [
                            'jp_rencana' => $jpVal,
                            'jam_mulai' => '08:00',
                            'jam_selesai' => '11:15',
                            'status' => 'Terlaksana',
                            'keterangan' => $ket1 ?: ($ket2 ?: null),
                        ]
                    );

                    // Realisasi
                    $jpDibayar = is_numeric($jpDibayarRaw) ? (float)$jpDibayarRaw : 0;
                    $realisasiJadwal = RealisasiJadwal::updateOrCreate(
                        ['jadwal_id' => $jadwal->id],
                        [
                            'status' => 'Terlaksana',
                            'tanggal_realisasi' => $tanggal,
                            'jam_mulai_aktual' => '08:00',
                            'jam_selesai_aktual' => '11:15',
                            'jp_aktual' => $jpVal,
                            'jp_dibayarkan' => $jpDibayar,
                            'catatan' => $ket1 ?: ($ket2 ?: 'Terlaksana sesuai jadwal Excel'),
                        ]
                    );

                    RealisasiPengajar::updateOrCreate(
                        [
                            'realisasi_jadwal_id' => $realisasiJadwal->id,
                            'widyaiswara_id' => $wi->id,
                        ],
                        [
                            'penugasan_pengajar_id' => $penugasan->id,
                            'jenis_pengajar' => 'Widyaiswara',
                            'jp_aktual' => $jpVal,
                            'jp_akumulasi' => $jpAkumulasiVal,
                            'jp_dibayarkan' => $jpDibayar,
                            'status_kehadiran' => 'Hadir',
                            'keterangan' => $ket1 ?: ($ket2 ?: null),
                        ]
                    );

                    $totalActivities++;
                }
            }
        }

        $this->info("BERHASIL: Diimpor {$totalWi} Widyaiswara dan {$totalActivities} kegiatan tatap muka bulan Oktober 2026.");
        return Command::SUCCESS;
    }

    private function parsePelatihanAngkatan(string $raw): array
    {
        $raw = trim($raw);

        if (stripos($raw, 'PPPK') !== false) {
            $nama = 'Pelatihan Orientasi PPPK';
        } elseif (stripos($raw, 'PK') !== false && (stripos($raw, 'PK.') !== false || stripos($raw, 'PK ') !== false || stripos($raw, 'PK 69') !== false)) {
            $nama = 'Pelatihan Kepemimpinan Pengawas (PKP)';
        } elseif (stripos($raw, 'Instruktur') !== false) {
            $nama = 'Pelatihan Instruktur';
        } elseif (stripos($raw, 'Wasnaker') !== false) {
            $nama = 'Pelatihan Pengawas Ketenagakerjaan (Wasnaker)';
        } elseif (stripos($raw, 'MHI') !== false) {
            $nama = 'Pelatihan Mediator Hubungan Industrial (MHI)';
        } elseif (stripos($raw, 'Penguji K3') !== false) {
            $nama = 'Pelatihan Penguji K3';
        } else {
            $nama = $raw;
        }

        // Cari angkatan
        if (preg_match('/A\.?\s*([0-9\s&dan]+)/i', $raw, $matches)) {
            $angkatan = 'Angkatan ' . trim($matches[1]);
        } elseif (preg_match('/([0-9]+)/', $raw, $matches)) {
            $angkatan = 'Angkatan ' . trim($matches[1]);
        } else {
            $angkatan = 'Angkatan 1';
        }

        return [$nama, $angkatan];
    }
}
