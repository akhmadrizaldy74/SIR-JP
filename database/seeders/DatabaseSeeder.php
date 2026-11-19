<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\AturanJp;
use App\Models\PengaturanSesi;
use App\Models\Pengaturan;
use App\Models\Widyaiswara;
use App\Models\Narasumber;
use App\Models\Pelatihan;
use App\Models\Angkatan;
use App\Models\MataPelatihan;
use App\Models\PenugasanPengajar;
use App\Models\Jadwal;
use App\Models\RealisasiJadwal;
use App\Models\RealisasiPengajar;
use App\Models\KonversiJp;
use App\Models\LogAktivitas;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Penyelenggara (Sesuai KCLC)
        $penyelenggara = User::firstOrCreate(
            ['email' => 'penyelenggara@sir-jp.go.id'],
            [
                'name' => 'AKHMAD RIZALDY',
                'username' => 'admin_penyelenggara',
                'phone' => '081234567890',
                'role' => 'penyelenggara',
                'status' => 'Aktif',
                'password' => Hash::make('password123'),
            ]
        );
        $penyelenggara->update([
            'name' => 'AKHMAD RIZALDY',
            'username' => 'admin_penyelenggara',
        ]);

        // 2. Aturan JP (Default 32 JP per bulan)
        AturanJp::firstOrCreate(
            ['nama_aturan' => 'Standar Minimum JP Widyaiswara PPSDM'],
            [
                'minimum_jp_bulanan' => 32.00,
                'durasi_per_jp_menit' => 45,
                'is_active' => true,
                'keterangan' => 'Ketentuan batas beban mengajar minimal Widyaiswara PPSDM 32 JP per bulan.',
            ]
        );

        // 3. Pengaturan Sistem & Instansi
        $settings = [
            ['key' => 'instansi_nama', 'value' => 'Pusat Pengembangan SDM Ketenagakerjaan', 'label' => 'Nama Instansi', 'tipe' => 'text'],
            ['key' => 'instansi_sub', 'value' => 'Ketenagakerjaan', 'label' => 'Sub Nama Instansi', 'tipe' => 'text'],
            ['key' => 'instansi_alamat', 'value' => 'Jl. Pengesahan No. 1, Jakarta Timur', 'label' => 'Alamat Instansi', 'tipe' => 'textarea'],
            ['key' => 'instansi_telepon', 'value' => '(021) 87654321', 'label' => 'Nomor Telepon', 'tipe' => 'text'],
            ['key' => 'instansi_email', 'value' => 'info@kemnaker.go.id', 'label' => 'Email Instansi', 'tipe' => 'text'],
            ['key' => 'pimpinan_nama', 'value' => 'Dr. Narsih, S.Pd., M.M.', 'label' => 'Nama Pimpinan', 'tipe' => 'text'],
            ['key' => 'pimpinan_nip', 'value' => '197202111998032002', 'label' => 'NIP Pimpinan', 'tipe' => 'text'],
            ['key' => 'pimpinan_jabatan', 'value' => 'Kepala Pusat Pengembangan SDM Ketenagakerjaan', 'label' => 'Jabatan Pimpinan', 'tipe' => 'text'],
            ['key' => 'pimpinan_kota', 'value' => 'Jakarta', 'label' => 'Kota Penandatanganan', 'tipe' => 'text'],
            ['key' => 'minimum_jp_bulanan', 'value' => '32', 'label' => 'Minimum JP Widyaiswara / Bulan', 'tipe' => 'number'],
        ];

        foreach ($settings as $st) {
            Pengaturan::firstOrCreate(['key' => $st['key']], $st);
        }

        // 4. Template Sesi Standar
        $sesiList = [
            ['nama_sesi' => 'Sesi 1', 'tipe' => 'Pembelajaran', 'nomor_sesi' => 1, 'jam_mulai' => '08:00:00', 'jam_selesai' => '08:45:00', 'durasi_menit' => 45, 'jumlah_jp' => 1.00, 'urutan' => 1],
            ['nama_sesi' => 'Sesi 2', 'tipe' => 'Pembelajaran', 'nomor_sesi' => 2, 'jam_mulai' => '08:45:00', 'jam_selesai' => '09:30:00', 'durasi_menit' => 45, 'jumlah_jp' => 1.00, 'urutan' => 2],
            ['nama_sesi' => 'Sesi 3', 'tipe' => 'Pembelajaran', 'nomor_sesi' => 3, 'jam_mulai' => '09:30:00', 'jam_selesai' => '10:15:00', 'durasi_menit' => 45, 'jumlah_jp' => 1.00, 'urutan' => 3],
            ['nama_sesi' => 'Istirahat Pagi', 'tipe' => 'Istirahat', 'nomor_sesi' => null, 'jam_mulai' => '10:15:00', 'jam_selesai' => '10:30:00', 'durasi_menit' => 15, 'jumlah_jp' => 0.00, 'urutan' => 4],
            ['nama_sesi' => 'Sesi 4', 'tipe' => 'Pembelajaran', 'nomor_sesi' => 4, 'jam_mulai' => '10:30:00', 'jam_selesai' => '11:15:00', 'durasi_menit' => 45, 'jumlah_jp' => 1.00, 'urutan' => 5],
            ['nama_sesi' => 'Sesi 5', 'tipe' => 'Pembelajaran', 'nomor_sesi' => 5, 'jam_mulai' => '11:15:00', 'jam_selesai' => '12:00:00', 'durasi_menit' => 45, 'jumlah_jp' => 1.00, 'urutan' => 6],
            ['nama_sesi' => 'Ishoma Siang', 'tipe' => 'Ishoma', 'nomor_sesi' => null, 'jam_mulai' => '12:00:00', 'jam_selesai' => '13:00:00', 'durasi_menit' => 60, 'jumlah_jp' => 0.00, 'urutan' => 7],
            ['nama_sesi' => 'Sesi 6', 'tipe' => 'Pembelajaran', 'nomor_sesi' => 6, 'jam_mulai' => '13:00:00', 'jam_selesai' => '13:45:00', 'durasi_menit' => 45, 'jumlah_jp' => 1.00, 'urutan' => 8],
            ['nama_sesi' => 'Sesi 7', 'tipe' => 'Pembelajaran', 'nomor_sesi' => 7, 'jam_mulai' => '13:45:00', 'jam_selesai' => '14:30:00', 'durasi_menit' => 45, 'jumlah_jp' => 1.00, 'urutan' => 9],
            ['nama_sesi' => 'Sesi 8', 'tipe' => 'Pembelajaran', 'nomor_sesi' => 8, 'jam_mulai' => '14:30:00', 'jam_selesai' => '15:15:00', 'durasi_menit' => 45, 'jumlah_jp' => 1.00, 'urutan' => 10],
            ['nama_sesi' => 'Istirahat Sore', 'tipe' => 'Istirahat', 'nomor_sesi' => null, 'jam_mulai' => '15:15:00', 'jam_selesai' => '15:45:00', 'durasi_menit' => 30, 'jumlah_jp' => 0.00, 'urutan' => 11],
            ['nama_sesi' => 'Sesi 9', 'tipe' => 'Pembelajaran', 'nomor_sesi' => 9, 'jam_mulai' => '15:45:00', 'jam_selesai' => '16:30:00', 'durasi_menit' => 45, 'jumlah_jp' => 1.00, 'urutan' => 12],
            ['nama_sesi' => 'Sesi 10', 'tipe' => 'Pembelajaran', 'nomor_sesi' => 10, 'jam_mulai' => '16:30:00', 'jam_selesai' => '17:15:00', 'durasi_menit' => 45, 'jumlah_jp' => 1.00, 'urutan' => 13],
        ];

        foreach ($sesiList as $s) {
            PengaturanSesi::firstOrCreate(['nama_sesi' => $s['nama_sesi']], $s);
        }

        // 5. Master Data Widyaiswara (Tabel Terpisah)
        $wi1 = Widyaiswara::firstOrCreate(
            ['nik' => '3171011504800001'],
            [
                'nip' => '19800415 200501 1 004',
                'nama_lengkap' => 'Bambang Sudarsono',
                'gelar_depan' => 'Drs.',
                'gelar_belakang' => 'M.Si.',
                'jabatan' => 'Widyaiswara Ahli Utama',
                'instansi' => 'PPSDM Kemendagri',
                'nomor_telepon' => '081389012345',
                'email' => 'bambang.sudarsono@ppsdm.go.id',
                'status' => 'Aktif',
                'keterangan' => 'Spesialisasi Kepemimpinan dan Manajemen Strategis',
            ]
        );

        $wi2 = Widyaiswara::firstOrCreate(
            ['nik' => '3273022508850002'],
            [
                'nip' => '19850825 200902 2 003',
                'nama_lengkap' => 'Sri Wahyuni',
                'gelar_depan' => 'Dr.',
                'gelar_belakang' => 'S.IP., M.PA.',
                'jabatan' => 'Widyaiswara Ahli Madya',
                'instansi' => 'PPSDM Kemendagri',
                'nomor_telepon' => '081298765432',
                'email' => 'sri.wahyuni@ppsdm.go.id',
                'status' => 'Aktif',
                'keterangan' => 'Spesialisasi Analisis Kebijakan Publik',
            ]
        );

        $wi3 = Widyaiswara::firstOrCreate(
            ['nik' => '3175031011900003'],
            [
                'nip' => '19901110 201403 1 002',
                'nama_lengkap' => 'Agus Priyono',
                'gelar_depan' => '',
                'gelar_belakang' => 'S.Kom., M.TI.',
                'jabatan' => 'Widyaiswara Ahli Muda',
                'instansi' => 'PPSDM Kemendagri',
                'nomor_telepon' => '085612345678',
                'email' => 'agus.priyono@ppsdm.go.id',
                'status' => 'Aktif',
                'keterangan' => 'Spesialisasi Transformasi Digital dan SPBE',
            ]
        );

        $wi4 = Widyaiswara::firstOrCreate(
            ['nik' => '3201041206950004'],
            [
                'nip' => '19950612 201902 2 005',
                'nama_lengkap' => 'Dian Kusuma Wardani',
                'gelar_depan' => '',
                'gelar_belakang' => 'S.STP., M.Tr.IP.',
                'jabatan' => 'Widyaiswara Ahli Pertama',
                'instansi' => 'PPSDM Kemendagri',
                'nomor_telepon' => '087812348765',
                'email' => 'dian.kusuma@ppsdm.go.id',
                'status' => 'Aktif',
                'keterangan' => 'Spesialisasi Pelayanan Publik dan Tata Kelola',
            ]
        );

        // 6. Master Data Narasumber (Tabel Terpisah dari Widyaiswara)
        $ns1 = Narasumber::firstOrCreate(
            ['nik' => '3174052003780005'],
            [
                'nama_lengkap' => 'Ferry Indrawan',
                'gelar_depan' => 'Prof. Dr.',
                'gelar_belakang' => 'S.E., M.Ec.',
                'instansi' => 'Fakultas Ekonomi dan Bisnis Universitas Indonesia',
                'nomor_telepon' => '08118889990',
                'email' => 'ferry.indrawan@ui.ac.id',
                'status' => 'Aktif',
                'keterangan' => 'Pakar Perencanaan Anggaran dan Keuangan Negara',
            ]
        );

        $ns2 = Narasumber::firstOrCreate(
            ['nik' => '3271061407820006'],
            [
                'nama_lengkap' => 'Rina Kartika Dewi',
                'gelar_depan' => '',
                'gelar_belakang' => 'S.H., LL.M.',
                'instansi' => 'Lembaga Kebijakan Pengadaan Barang/Jasa Pemerintah (LKPP)',
                'nomor_telepon' => '081233445566',
                'email' => 'rina.kartika@lkpp.go.id',
                'status' => 'Aktif',
                'keterangan' => 'Auditor Hukum dan Pengadaan Barang Jasa',
            ]
        );

        // 7. Master Pelatihan
        $pel1 = Pelatihan::firstOrCreate(
            ['kode_pelatihan' => 'PKP-2026-01'],
            [
                'nama_pelatihan' => 'Pelatihan Kepemimpinan Pengawas (PKP)',
                'kategori' => 'Manajerial',
                'penyelenggara' => 'PPSDM Kemendagri',
                'tanggal_mulai' => Carbon::now()->startOfMonth()->toDateString(),
                'tanggal_selesai' => Carbon::now()->endOfMonth()->toDateString(),
                'status' => 'Berjalan',
                'keterangan' => 'Pelatihan kepemimpinan tingkat pengawas pola blended learning.',
            ]
        );

        $pel2 = Pelatihan::firstOrCreate(
            ['kode_pelatihan' => 'PBJ-2026-02'],
            [
                'nama_pelatihan' => 'Pelatihan Teknis Pengadaan Barang dan Jasa Pemerintah',
                'kategori' => 'Teknis',
                'penyelenggara' => 'PPSDM Kemendagri',
                'tanggal_mulai' => Carbon::now()->startOfMonth()->addDays(5)->toDateString(),
                'tanggal_selesai' => Carbon::now()->startOfMonth()->addDays(20)->toDateString(),
                'status' => 'Berjalan',
                'keterangan' => 'Sertifikasi kompetensi pengadaan barang dan jasa level 1.',
            ]
        );

        // 8. Angkatan
        $ang1 = Angkatan::firstOrCreate(
            ['pelatihan_id' => $pel1->id, 'nomor_angkatan' => 'Angkatan I'],
            [
                'tanggal_mulai' => Carbon::now()->startOfMonth()->toDateString(),
                'tanggal_selesai' => Carbon::now()->endOfMonth()->toDateString(),
                'jumlah_peserta' => 40,
                'lokasi' => 'Kampus PPSDM Kalibata - Gedung A',
                'status' => 'Berjalan',
                'keterangan' => 'Peserta pejabat pengawas dari berbagai provinsi.',
            ]
        );

        $ang2 = Angkatan::firstOrCreate(
            ['pelatihan_id' => $pel2->id, 'nomor_angkatan' => 'Angkatan II'],
            [
                'tanggal_mulai' => Carbon::now()->startOfMonth()->addDays(5)->toDateString(),
                'tanggal_selesai' => Carbon::now()->startOfMonth()->addDays(20)->toDateString(),
                'jumlah_peserta' => 35,
                'lokasi' => 'Lab Komputer PPSDM Gedung B',
                'status' => 'Berjalan',
                'keterangan' => 'Peserta PPK dan Pejabat Pengadaan.',
            ]
        );

        // 9. Mata Pelatihan
        $mp1 = MataPelatihan::firstOrCreate(
            ['angkatan_id' => $ang1->id, 'nama_mata_pelatihan' => 'Manajemen Mutu Pelayanan Publik'],
            [
                'rencana_jp' => 12.00,
                'deskripsi' => 'Penguatan standar pelayanan publik berbasis kepuasan pengguna.',
                'keterangan' => 'Tatap Muka & Diskusi Kasus',
            ]
        );

        $mp2 = MataPelatihan::firstOrCreate(
            ['angkatan_id' => $ang1->id, 'nama_mata_pelatihan' => 'Kepemimpinan Transformatif dan Digital'],
            [
                'rencana_jp' => 10.00,
                'deskripsi' => 'Pengembangan kepemimpinan adaptif di era digital birokrasi.',
                'keterangan' => 'Klasikal dan Simulasi',
            ]
        );

        $mp3 = MataPelatihan::firstOrCreate(
            ['angkatan_id' => $ang2->id, 'nama_mata_pelatihan' => 'Penyusunan Spesifikasi Teknis dan HPS'],
            [
                'rencana_jp' => 9.00,
                'deskripsi' => 'Teknik penyusunan HPS dan kerangka acuan kerja PBJ.',
                'keterangan' => 'Praktikum & Analisis Kasus',
            ]
        );

        // 10. Penugasan Pengajar
        $tugas1 = PenugasanPengajar::firstOrCreate(
            ['mata_pelatihan_id' => $mp1->id, 'jenis_pengajar' => 'Widyaiswara', 'widyaiswara_id' => $wi1->id],
            ['peran' => 'Pengampu Utama', 'keterangan' => 'Mengampu 8 JP Tatap Muka']
        );

        $tugas2 = PenugasanPengajar::firstOrCreate(
            ['mata_pelatihan_id' => $mp1->id, 'jenis_pengajar' => 'Widyaiswara', 'widyaiswara_id' => $wi2->id],
            ['peran' => 'Pendamping Fasilitator', 'keterangan' => 'Mengampu 4 JP Pendampingan']
        );

        $tugas3 = PenugasanPengajar::firstOrCreate(
            ['mata_pelatihan_id' => $mp2->id, 'jenis_pengajar' => 'Widyaiswara', 'widyaiswara_id' => $wi3->id],
            ['peran' => 'Pengampu Utama', 'keterangan' => 'Mengampu 10 JP']
        );

        $tugas4 = PenugasanPengajar::firstOrCreate(
            ['mata_pelatihan_id' => $mp3->id, 'jenis_pengajar' => 'Narasumber', 'narasumber_id' => $ns2->id],
            ['peran' => 'Narasumber Ahli', 'keterangan' => 'Mengampu 9 JP Praktik PBJ']
        );

        // 11. Jadwal & Realisasi Aktual Bulan Ini
        $today = Carbon::now();
        $date1 = $today->copy()->subDays(6)->toDateString();
        $date2 = $today->copy()->subDays(5)->toDateString();
        $date3 = $today->copy()->subDays(3)->toDateString();
        $date4 = $today->copy()->subDays(1)->toDateString();
        $date5 = $today->copy()->addDays(2)->toDateString(); // Terjadwal mendatang

        // Jadwal 1: Wi1 (3 JP) - Terlaksana
        $jdw1 = Jadwal::firstOrCreate(
            ['mata_pelatihan_id' => $mp1->id, 'tanggal' => $date1, 'jam_mulai' => '08:00:00'],
            [
                'penugasan_pengajar_id' => $tugas1->id,
                'jam_selesai' => '10:15:00',
                'sesi_ke' => 1,
                'jp_rencana' => 3.00,
                'ruangan' => 'Ruang Teori 101',
                'metode' => 'Tatap Muka / Klasikal',
                'status' => 'Terlaksana',
                'keterangan' => 'Sesi pembuka konsep manajemen mutu',
            ]
        );

        $real1 = RealisasiJadwal::firstOrCreate(
            ['jadwal_id' => $jdw1->id],
            [
                'tanggal_realisasi' => $date1,
                'jam_mulai_aktual' => '08:00:00',
                'jam_selesai_aktual' => '10:15:00',
                'jp_aktual' => 3.00,
                'jp_dibayarkan' => 3.00,
                'status' => 'Terlaksana',
                'catatan' => 'Kegiatan berlangsung lancar dan tepat waktu.',
                'diverifikasi_oleh' => $penyelenggara->id,
                'diverifikasi_pada' => Carbon::now()->subDays(6),
            ]
        );

        RealisasiPengajar::firstOrCreate(
            ['realisasi_jadwal_id' => $real1->id, 'widyaiswara_id' => $wi1->id],
            [
                'penugasan_pengajar_id' => $tugas1->id,
                'jenis_pengajar' => 'Widyaiswara',
                'jp_aktual' => 3.00,
                'jp_dibayarkan' => 3.00,
                'status_kehadiran' => 'Hadir',
                'keterangan' => 'Hadir tepat waktu dan mengisi presensi.',
            ]
        );

        // Jadwal 2: Wi1 (4 JP) - Terlaksana
        $jdw2 = Jadwal::firstOrCreate(
            ['mata_pelatihan_id' => $mp1->id, 'tanggal' => $date2, 'jam_mulai' => '10:30:00'],
            [
                'penugasan_pengajar_id' => $tugas1->id,
                'jam_selesai' => '14:30:00',
                'sesi_ke' => 4,
                'jp_rencana' => 4.00,
                'ruangan' => 'Ruang Teori 101',
                'metode' => 'Tatap Muka / Klasikal',
                'status' => 'Terlaksana',
                'keterangan' => 'Studi kasus implementasi pelayanan prima',
            ]
        );

        $real2 = RealisasiJadwal::firstOrCreate(
            ['jadwal_id' => $jdw2->id],
            [
                'tanggal_realisasi' => $date2,
                'jam_mulai_aktual' => '10:30:00',
                'jam_selesai_aktual' => '14:30:00',
                'jp_aktual' => 4.00,
                'jp_dibayarkan' => 4.00,
                'status' => 'Terlaksana',
                'catatan' => 'Selesai sesuai target pembelajaran.',
                'diverifikasi_oleh' => $penyelenggara->id,
                'diverifikasi_pada' => Carbon::now()->subDays(5),
            ]
        );

        RealisasiPengajar::firstOrCreate(
            ['realisasi_jadwal_id' => $real2->id, 'widyaiswara_id' => $wi1->id],
            [
                'penugasan_pengajar_id' => $tugas1->id,
                'jenis_pengajar' => 'Widyaiswara',
                'jp_aktual' => 4.00,
                'jp_dibayarkan' => 4.00,
                'status_kehadiran' => 'Hadir',
                'keterangan' => 'Mengajar penuh.',
            ]
        );

        // Jadwal 3: Wi2 (4 JP) - Terlaksana
        $jdw3 = Jadwal::firstOrCreate(
            ['mata_pelatihan_id' => $mp1->id, 'tanggal' => $date3, 'jam_mulai' => '08:00:00'],
            [
                'penugasan_pengajar_id' => $tugas2->id,
                'jam_selesai' => '11:15:00',
                'sesi_ke' => 1,
                'jp_rencana' => 4.00,
                'ruangan' => 'Ruang Teori 102',
                'metode' => 'Tatap Muka / Klasikal',
                'status' => 'Terlaksana',
                'keterangan' => 'Pendampingan rancangan proyek perubahan',
            ]
        );

        $real3 = RealisasiJadwal::firstOrCreate(
            ['jadwal_id' => $jdw3->id],
            [
                'tanggal_realisasi' => $date3,
                'jam_mulai_aktual' => '08:00:00',
                'jam_selesai_aktual' => '11:15:00',
                'jp_aktual' => 4.00,
                'jp_dibayarkan' => 4.00,
                'status' => 'Terlaksana',
                'catatan' => 'Semua peserta mendapatkan coaching.',
                'diverifikasi_oleh' => $penyelenggara->id,
                'diverifikasi_pada' => Carbon::now()->subDays(3),
            ]
        );

        RealisasiPengajar::firstOrCreate(
            ['realisasi_jadwal_id' => $real3->id, 'widyaiswara_id' => $wi2->id],
            [
                'penugasan_pengajar_id' => $tugas2->id,
                'jenis_pengajar' => 'Widyaiswara',
                'jp_aktual' => 4.00,
                'jp_dibayarkan' => 4.00,
                'status_kehadiran' => 'Hadir',
                'keterangan' => 'Coaching kelompok.',
            ]
        );

        // Jadwal 4: Wi3 (5 JP) - Terlaksana
        $jdw4 = Jadwal::firstOrCreate(
            ['mata_pelatihan_id' => $mp2->id, 'tanggal' => $date4, 'jam_mulai' => '08:00:00'],
            [
                'penugasan_pengajar_id' => $tugas3->id,
                'jam_selesai' => '12:00:00',
                'sesi_ke' => 1,
                'jp_rencana' => 5.00,
                'ruangan' => 'Lab Komputer 201',
                'metode' => 'Tatap Muka / Klasikal',
                'status' => 'Terlaksana',
                'keterangan' => 'Praktik pemanfaatan dashboard SPBE',
            ]
        );

        $real4 = RealisasiJadwal::firstOrCreate(
            ['jadwal_id' => $jdw4->id],
            [
                'tanggal_realisasi' => $date4,
                'jam_mulai_aktual' => '08:00:00',
                'jam_selesai_aktual' => '12:00:00',
                'jp_aktual' => 5.00,
                'jp_dibayarkan' => 5.00,
                'status' => 'Terlaksana',
                'catatan' => 'Praktik selesai dengan baik.',
                'diverifikasi_oleh' => $penyelenggara->id,
                'diverifikasi_pada' => Carbon::now()->subDays(1),
            ]
        );

        RealisasiPengajar::firstOrCreate(
            ['realisasi_jadwal_id' => $real4->id, 'widyaiswara_id' => $wi3->id],
            [
                'penugasan_pengajar_id' => $tugas3->id,
                'jenis_pengajar' => 'Widyaiswara',
                'jp_aktual' => 5.00,
                'jp_dibayarkan' => 5.00,
                'status_kehadiran' => 'Hadir',
                'keterangan' => 'Mengajar materi SPBE.',
            ]
        );

        // Jadwal 5: Ns2 (6 JP) - Terlaksana
        $jdw5 = Jadwal::firstOrCreate(
            ['mata_pelatihan_id' => $mp3->id, 'tanggal' => $date4, 'jam_mulai' => '08:00:00'],
            [
                'penugasan_pengajar_id' => $tugas4->id,
                'jam_selesai' => '13:45:00',
                'sesi_ke' => 1,
                'jp_rencana' => 6.00,
                'ruangan' => 'Lab PBJ Gedung C',
                'metode' => 'Tatap Muka / Klasikal',
                'status' => 'Terlaksana',
                'keterangan' => 'Penyusunan KAK dan HPS PBJ',
            ]
        );

        $real5 = RealisasiJadwal::firstOrCreate(
            ['jadwal_id' => $jdw5->id],
            [
                'tanggal_realisasi' => $date4,
                'jam_mulai_aktual' => '08:00:00',
                'jam_selesai_aktual' => '13:45:00',
                'jp_aktual' => 6.00,
                'jp_dibayarkan' => 6.00,
                'status' => 'Terlaksana',
                'catatan' => 'Narasumber eksternal hadir tepat waktu.',
                'diverifikasi_oleh' => $penyelenggara->id,
                'diverifikasi_pada' => Carbon::now()->subDays(1),
            ]
        );

        RealisasiPengajar::firstOrCreate(
            ['realisasi_jadwal_id' => $real5->id, 'narasumber_id' => $ns2->id],
            [
                'penugasan_pengajar_id' => $tugas4->id,
                'jenis_pengajar' => 'Narasumber',
                'jp_aktual' => 6.00,
                'jp_dibayarkan' => 6.00,
                'status_kehadiran' => 'Hadir',
                'keterangan' => 'Narasumber eksternal LKPP.',
            ]
        );

        // Jadwal 6: Terjadwal Mendatang (Belum terealisasi - TIDAK boleh masuk hitungan!)
        Jadwal::firstOrCreate(
            ['mata_pelatihan_id' => $mp2->id, 'tanggal' => $date5, 'jam_mulai' => '08:00:00'],
            [
                'penugasan_pengajar_id' => $tugas3->id,
                'jam_selesai' => '12:00:00',
                'sesi_ke' => 1,
                'jp_rencana' => 5.00,
                'ruangan' => 'Ruang Teori 101',
                'metode' => 'Tatap Muka / Klasikal',
                'status' => 'Terjadwal',
                'keterangan' => 'Jadwal sesi lanjutan minggu depan.',
            ]
        );

        // 12. Konversi JP Widyaiswara (Khusus WI)
        KonversiJp::firstOrCreate(
            ['widyaiswara_id' => $wi1->id, 'nama_kegiatan' => 'Penyusunan Modul Pelatihan Kepemimpinan Pengawas'],
            [
                'tanggal_kegiatan' => $today->copy()->subDays(10)->toDateString(),
                'kategori_konversi' => 'Penyusunan Modul',
                'jumlah_jp' => 15.00,
                'jp_dibayarkan' => 15.00,
                'informasi_pembayaran' => 'DIPA PPSDM TA 2026',
                'nomor_sk' => 'SK.800/124/PPSDM/2026',
                'status_validasi' => 'Disetujui',
                'keterangan' => 'Penyusunan modul mata pelatihan manajemen mutu.',
            ]
        );

        KonversiJp::firstOrCreate(
            ['widyaiswara_id' => $wi1->id, 'nama_kegiatan' => 'Karya Tulis Ilmiah Tata Kelola Pemerintahan Daerah'],
            [
                'tanggal_kegiatan' => $today->copy()->subDays(4)->toDateString(),
                'kategori_konversi' => 'Karya Tulis Ilmiah',
                'jumlah_jp' => 12.00,
                'jp_dibayarkan' => 0.00, // Menunjukkan JP Dibayarkan tidak otomatis sama dengan JP Aktual
                'informasi_pembayaran' => 'Non-Budgeter (Kredit Angka Kumulatif)',
                'nomor_sk' => 'ST.421/089/PPSDM/2026',
                'status_validasi' => 'Disetujui',
                'keterangan' => 'Karya ilmiah dipublikasikan pada Jurnal Kebijakan Publik.',
            ]
        );

        KonversiJp::firstOrCreate(
            ['widyaiswara_id' => $wi2->id, 'nama_kegiatan' => 'Bimbingan Rancangan Proyek Aksi Perubahan'],
            [
                'tanggal_kegiatan' => $today->copy()->subDays(7)->toDateString(),
                'kategori_konversi' => 'Bimbingan / Mentoring',
                'jumlah_jp' => 8.00,
                'jp_dibayarkan' => 8.00,
                'informasi_pembayaran' => 'DIPA PPSDM TA 2026',
                'nomor_sk' => 'SK.800/130/PPSDM/2026',
                'status_validasi' => 'Disetujui',
                'keterangan' => 'Bimbingan 4 peserta PKP Angkatan I.',
            ]
        );

        // 13. Log Aktivitas
        LogAktivitas::create([
            'user_id' => $penyelenggara->id,
            'modul' => 'Sistem',
            'aktivitas' => 'Inisialisasi Database Sistem Informasi Rekapitulasi Jam Pelajaran (SIR-JP)',
            'detail' => 'Database migrations dan seed data awal berhasil diproses.',
            'ip_address' => '127.0.0.1',
        ]);

        // 14. Integrasi KCLC Learning Center (Screenshot Seeder)
        $this->call([
            KCLCScreenshotSeeder::class,
        ]);
    }
}
