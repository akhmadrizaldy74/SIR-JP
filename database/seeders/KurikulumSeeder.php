<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JenisPelatihan;
use App\Models\TahunKurikulum;
use App\Models\Pelatihan;
use App\Models\MataPelatihan;

class KurikulumSeeder extends Seeder
{
    public function run(): void
    {
        JenisPelatihan::firstOrCreate(['nama_jenis' => 'Pelatihan Teknis'], ['status' => true]);
        JenisPelatihan::firstOrCreate(['nama_jenis' => 'Pelatihan Fungsional'], ['status' => true]);
        JenisPelatihan::firstOrCreate(['nama_jenis' => 'Pelatihan Manajemen Aparatur'], ['status' => true]);

        TahunKurikulum::firstOrCreate(['tahun' => '2027'], ['status' => true]);
        TahunKurikulum::firstOrCreate(['tahun' => '2026'], ['status' => true]);
        TahunKurikulum::firstOrCreate(['tahun' => '2025'], ['status' => true]);

        $p1 = Pelatihan::firstOrCreate(
            ['nama_pelatihan' => 'Pelatihan Teknis Antar Kerja PPPK Pengantar Kerja'],
            ['kategori' => 'Pelatihan Teknis', 'kode_pelatihan' => 'TEK-001', 'status' => 'Berjalan', 'tanggal_mulai' => '2026-01-01', 'tanggal_selesai' => '2026-12-31']
        );
        $p2 = Pelatihan::firstOrCreate(
            ['nama_pelatihan' => 'Pelatihan Penjenjangan Jabatan Fungsional Pengantar Kerja Ahli Muda'],
            ['kategori' => 'Pelatihan Fungsional', 'kode_pelatihan' => 'FUNG-001', 'status' => 'Berjalan', 'tanggal_mulai' => '2026-01-01', 'tanggal_selesai' => '2026-12-31']
        );
        $p3 = Pelatihan::firstOrCreate(
            ['nama_pelatihan' => 'Pelatihan Pembentukan Jabatan Fungsional Penguji K3'],
            ['kategori' => 'Pelatihan Fungsional', 'kode_pelatihan' => 'FUNG-002', 'status' => 'Berjalan', 'tanggal_mulai' => '2026-01-01', 'tanggal_selesai' => '2026-12-31']
        );
        $p4 = Pelatihan::firstOrCreate(
            ['nama_pelatihan' => 'Pelatihan Pembentukan Jabatan Fungsional Pengawas Ketenagakerjaan'],
            ['kategori' => 'Pelatihan Fungsional', 'kode_pelatihan' => 'FUNG-003', 'status' => 'Berjalan', 'tanggal_mulai' => '2026-01-01', 'tanggal_selesai' => '2026-12-31']
        );
        $p5 = Pelatihan::firstOrCreate(
            ['nama_pelatihan' => 'Pelatihan Pembentukan Jabatan Fungsional Pengantar Kerja'],
            ['kategori' => 'Pelatihan Fungsional', 'kode_pelatihan' => 'FUNG-004', 'status' => 'Berjalan', 'tanggal_mulai' => '2026-01-01', 'tanggal_selesai' => '2026-12-31']
        );
        $p6 = Pelatihan::firstOrCreate(
            ['nama_pelatihan' => 'Pelatihan Pembentukan Jabatan Fungsional Mediator Hubungan Industrial'],
            ['kategori' => 'Pelatihan Fungsional', 'kode_pelatihan' => 'FUNG-005', 'status' => 'Berjalan', 'tanggal_mulai' => '2026-01-01', 'tanggal_selesai' => '2026-12-31']
        );

        MataPelatihan::firstOrCreate(
            ['nama_mata_pelatihan' => 'Coaching', 'pelatihan_id' => $p1->id],
            ['rencana_jp' => 4, 'tahun_kurikulum' => '2026']
        );
        MataPelatihan::firstOrCreate(
            ['nama_mata_pelatihan' => 'Character Building', 'pelatihan_id' => $p1->id],
            ['rencana_jp' => 6, 'tahun_kurikulum' => '2026']
        );
        MataPelatihan::firstOrCreate(
            ['nama_mata_pelatihan' => 'Teknis Antar Kerja (Job Counselling, Job Canvassing, Job Matching)', 'pelatihan_id' => $p1->id],
            ['rencana_jp' => 6, 'tahun_kurikulum' => '2026']
        );
        MataPelatihan::firstOrCreate(
            ['nama_mata_pelatihan' => 'Pembekalan Uji Kompetensi Pengantar Kerja Ahli Muda', 'pelatihan_id' => $p2->id],
            ['rencana_jp' => 3, 'tahun_kurikulum' => '2026']
        );
    }
}
