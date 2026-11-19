<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
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
use App\Services\JPRecapitulationService;
use Carbon\Carbon;

class SIRJPBusinessLogicTest extends TestCase
{
    use \Illuminate\Foundation\Testing\DatabaseTransactions;

    protected JPRecapitulationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new JPRecapitulationService();
    }

    /**
     * Test 1: Jadwal belum terealisasi TIDAK boleh dihitung.
     */
    public function test_jadwal_belum_terealisasi_tidak_dihitung(): void
    {
        $bulan = 5;
        $tahun = 2026;

        $wi = Widyaiswara::create([
            'nik' => '99990001',
            'nama_lengkap' => 'Widyaiswara Test 1',
            'jabatan' => 'Widyaiswara Ahli Muda',
            'instansi' => 'PPSDM',
            'status' => 'Aktif',
        ]);

        $pelatihan = Pelatihan::create([
            'kode_pelatihan' => 'TEST-01',
            'nama_pelatihan' => 'Pelatihan Test 1',
            'kategori' => 'Teknis',
            'penyelenggara' => 'PPSDM',
            'tanggal_mulai' => '2026-05-01',
            'tanggal_selesai' => '2026-05-30',
        ]);

        $angkatan = Angkatan::create([
            'pelatihan_id' => $pelatihan->id,
            'nomor_angkatan' => 'Angkatan I',
            'tanggal_mulai' => '2026-05-01',
            'tanggal_selesai' => '2026-05-30',
            'jumlah_peserta' => 30,
        ]);

        $mp = MataPelatihan::create([
            'angkatan_id' => $angkatan->id,
            'nama_mata_pelatihan' => 'Materi Uji',
            'rencana_jp' => 10.00,
        ]);

        $tugas = PenugasanPengajar::create([
            'mata_pelatihan_id' => $mp->id,
            'jenis_pengajar' => 'Widyaiswara',
            'widyaiswara_id' => $wi->id,
            'peran' => 'Pengampu Utama',
        ]);

        // Buat jadwal berstatus 'Terjadwal' (Belum ada realisasi)
        Jadwal::create([
            'mata_pelatihan_id' => $mp->id,
            'penugasan_pengajar_id' => $tugas->id,
            'tanggal' => '2026-05-10',
            'jam_mulai' => '08:00:00',
            'jam_selesai' => '12:00:00',
            'jp_rencana' => 5.00,
            'status' => 'Terjadwal',
        ]);

        $result = $this->service->hitungWidyaiswara($wi->id, $bulan, $tahun);

        $this->assertEquals(0, $result['total_a'], 'Jadwal belum terealisasi tidak boleh dihitung ke Total A.');
        $this->assertEquals(0, $result['total_ab']);
    }

    /**
     * Test 2: Jadwal dibatalkan TIDAK boleh dihitung.
     */
    public function test_jadwal_dibatalkan_tidak_dihitung(): void
    {
        $bulan = 5;
        $tahun = 2026;

        $wi = Widyaiswara::create([
            'nik' => '99990002',
            'nama_lengkap' => 'Widyaiswara Test 2',
            'jabatan' => 'Widyaiswara Ahli Madya',
            'instansi' => 'PPSDM',
            'status' => 'Aktif',
        ]);

        $pelatihan = Pelatihan::create([
            'kode_pelatihan' => 'TEST-02',
            'nama_pelatihan' => 'Pelatihan Test 2',
            'kategori' => 'Teknis',
            'penyelenggara' => 'PPSDM',
            'tanggal_mulai' => '2026-05-01',
            'tanggal_selesai' => '2026-05-30',
        ]);

        $angkatan = Angkatan::create([
            'pelatihan_id' => $pelatihan->id,
            'nomor_angkatan' => 'Angkatan I',
            'tanggal_mulai' => '2026-05-01',
            'tanggal_selesai' => '2026-05-30',
            'jumlah_peserta' => 30,
        ]);

        $mp = MataPelatihan::create([
            'angkatan_id' => $angkatan->id,
            'nama_mata_pelatihan' => 'Materi Uji Batal',
            'rencana_jp' => 10.00,
        ]);

        $tugas = PenugasanPengajar::create([
            'mata_pelatihan_id' => $mp->id,
            'jenis_pengajar' => 'Widyaiswara',
            'widyaiswara_id' => $wi->id,
            'peran' => 'Pengampu Utama',
        ]);

        $jdw = Jadwal::create([
            'mata_pelatihan_id' => $mp->id,
            'penugasan_pengajar_id' => $tugas->id,
            'tanggal' => '2026-05-12',
            'jam_mulai' => '08:00:00',
            'jam_selesai' => '12:00:00',
            'jp_rencana' => 5.00,
            'status' => 'Dibatalkan',
        ]);

        $realisasi = RealisasiJadwal::create([
            'jadwal_id' => $jdw->id,
            'tanggal_realisasi' => '2026-05-12',
            'jam_mulai_aktual' => '08:00:00',
            'jam_selesai_aktual' => '12:00:00',
            'jp_aktual' => 5.00,
            'jp_dibayarkan' => 0.00,
            'status' => 'Dibatalkan',
            'alasan_batal' => 'Narasumber sakit / bencana alam',
        ]);

        $result = $this->service->hitungWidyaiswara($wi->id, $bulan, $tahun);

        $this->assertEquals(0, $result['total_a'], 'Jadwal berstatus Dibatalkan tidak boleh dihitung ke Total A.');
    }

    /**
     * Test 3: Jadwal terealisasi dihitung TEPAT SATU KALI.
     */
    public function test_jadwal_terealisasi_dihitung_tepat_satu_kali(): void
    {
        $bulan = 5;
        $tahun = 2026;

        $wi = Widyaiswara::create([
            'nik' => '99990003',
            'nama_lengkap' => 'Widyaiswara Test 3',
            'jabatan' => 'Widyaiswara Ahli Utama',
            'instansi' => 'PPSDM',
            'status' => 'Aktif',
        ]);

        $pelatihan = Pelatihan::create([
            'kode_pelatihan' => 'TEST-03',
            'nama_pelatihan' => 'Pelatihan Test 3',
            'kategori' => 'Teknis',
            'penyelenggara' => 'PPSDM',
            'tanggal_mulai' => '2026-05-01',
            'tanggal_selesai' => '2026-05-30',
        ]);

        $angkatan = Angkatan::create([
            'pelatihan_id' => $pelatihan->id,
            'nomor_angkatan' => 'Angkatan I',
            'tanggal_mulai' => '2026-05-01',
            'tanggal_selesai' => '2026-05-30',
            'jumlah_peserta' => 30,
        ]);

        $mp = MataPelatihan::create([
            'angkatan_id' => $angkatan->id,
            'nama_mata_pelatihan' => 'Materi Uji Realisasi',
            'rencana_jp' => 10.00,
        ]);

        $tugas = PenugasanPengajar::create([
            'mata_pelatihan_id' => $mp->id,
            'jenis_pengajar' => 'Widyaiswara',
            'widyaiswara_id' => $wi->id,
            'peran' => 'Pengampu Utama',
        ]);

        $jdw = Jadwal::create([
            'mata_pelatihan_id' => $mp->id,
            'penugasan_pengajar_id' => $tugas->id,
            'tanggal' => '2026-05-15',
            'jam_mulai' => '08:00:00',
            'jam_selesai' => '12:00:00',
            'jp_rencana' => 5.00,
            'status' => 'Terlaksana',
        ]);

        $realisasi = RealisasiJadwal::create([
            'jadwal_id' => $jdw->id,
            'tanggal_realisasi' => '2026-05-15',
            'jam_mulai_aktual' => '08:00:00',
            'jam_selesai_aktual' => '12:00:00',
            'jp_aktual' => 5.00,
            'jp_dibayarkan' => 5.00,
            'status' => 'Terlaksana',
        ]);

        RealisasiPengajar::create([
            'realisasi_jadwal_id' => $realisasi->id,
            'penugasan_pengajar_id' => $tugas->id,
            'jenis_pengajar' => 'Widyaiswara',
            'widyaiswara_id' => $wi->id,
            'jp_aktual' => 5.00,
            'jp_dibayarkan' => 5.00,
            'status_kehadiran' => 'Hadir',
        ]);

        $result = $this->service->hitungWidyaiswara($wi->id, $bulan, $tahun);

        $this->assertEquals(5.00, $result['total_a']);
        $this->assertEquals(1, $result['tatap_muka']->count(), 'Jumlah sesi harus tepat satu kali.');
    }

    /**
     * Test 4: JP Konversi dihitung terpisah dari JP Tatap Muka.
     */
    public function test_jp_konversi_dihitung_terpisah(): void
    {
        $bulan = 5;
        $tahun = 2026;

        $wi = Widyaiswara::create([
            'nik' => '99990004',
            'nama_lengkap' => 'Widyaiswara Test 4',
            'jabatan' => 'Widyaiswara Ahli Madya',
            'instansi' => 'PPSDM',
            'status' => 'Aktif',
        ]);

        // Buat Konversi 10 JP
        KonversiJp::create([
            'widyaiswara_id' => $wi->id,
            'tanggal_kegiatan' => '2026-05-10',
            'nama_kegiatan' => 'Penyusunan Modul Diklat',
            'kategori_konversi' => 'Penyusunan Modul',
            'jumlah_jp' => 10.00,
            'jp_dibayarkan' => 10.00,
            'status_validasi' => 'Disetujui',
        ]);

        $result = $this->service->hitungWidyaiswara($wi->id, $bulan, $tahun);

        $this->assertEquals(0, $result['total_a'], 'Total A (Tatap Muka) harus 0.');
        $this->assertEquals(10.00, $result['total_b'], 'Total B (Konversi) harus 10.');
        $this->assertEquals(10.00, $result['total_ab'], 'Total A + B harus 10.');
    }

    /**
     * Test 5: JP Dibayarkan TIDAK otomatis disamakan dengan Total JP.
     */
    public function test_jp_dibayarkan_tidak_otomatis_sama_dengan_total_jp(): void
    {
        $bulan = 5;
        $tahun = 2026;

        $wi = Widyaiswara::create([
            'nik' => '99990005',
            'nama_lengkap' => 'Widyaiswara Test 5',
            'jabatan' => 'Widyaiswara Ahli Pertama',
            'instansi' => 'PPSDM',
            'status' => 'Aktif',
        ]);

        // Konversi 15 JP tapi JP Dibayarkan = 0 (kredit non-budgeter)
        KonversiJp::create([
            'widyaiswara_id' => $wi->id,
            'tanggal_kegiatan' => '2026-05-20',
            'nama_kegiatan' => 'Karya Tulis Ilmiah',
            'kategori_konversi' => 'Karya Tulis Ilmiah',
            'jumlah_jp' => 15.00,
            'jp_dibayarkan' => 0.00,
            'informasi_pembayaran' => 'Non-Budgeter',
            'status_validasi' => 'Disetujui',
        ]);

        $result = $this->service->hitungWidyaiswara($wi->id, $bulan, $tahun);

        $this->assertEquals(15.00, $result['total_ab'], 'Total JP harus 15.');
        $this->assertEquals(0.00, $result['total_dibayarkan'], 'Total JP Dibayarkan harus 0 (tidak otomatis disamakan).');
    }

    /**
     * Test 6: Narasumber TIDAK dikenakan batas minimum 32 JP.
     */
    public function test_narasumber_tidak_dikenakan_minimum_32_jp(): void
    {
        $bulan = 5;
        $tahun = 2026;

        $ns = Narasumber::create([
            'nik' => '88880001',
            'nama_lengkap' => 'Narasumber Test',
            'instansi' => 'Kementerian Keuangan',
            'status' => 'Aktif',
        ]);

        $result = $this->service->hitungNarasumber($ns->id, $bulan, $tahun);

        $this->assertArrayNotHasKey('minimum_jp', $result, 'Hasil rekap narasumber tidak boleh memiliki field minimum_jp.');
        $this->assertArrayNotHasKey('kekurangan_jp', $result, 'Narasumber tidak boleh memiliki kekurangan JP minimum.');
    }

    /**
     * Test 7: Data antarbulan TIDAK saling mencampur.
     */
    public function test_data_antarbulan_tidak_tercampur(): void
    {
        $wi = Widyaiswara::create([
            'nik' => '99990007',
            'nama_lengkap' => 'Widyaiswara Test 7',
            'jabatan' => 'Widyaiswara Ahli Muda',
            'instansi' => 'PPSDM',
            'status' => 'Aktif',
        ]);

        // Kegiatan di Bulan 5 (Mei)
        KonversiJp::create([
            'widyaiswara_id' => $wi->id,
            'tanggal_kegiatan' => '2026-05-10',
            'nama_kegiatan' => 'Kegiatan Mei',
            'kategori_konversi' => 'Penyusunan Modul',
            'jumlah_jp' => 10.00,
            'status_validasi' => 'Disetujui',
        ]);

        // Kegiatan di Bulan 6 (Juni)
        KonversiJp::create([
            'widyaiswara_id' => $wi->id,
            'tanggal_kegiatan' => '2026-06-15',
            'nama_kegiatan' => 'Kegiatan Juni',
            'kategori_konversi' => 'Penyusunan Modul',
            'jumlah_jp' => 20.00,
            'status_validasi' => 'Disetujui',
        ]);

        // Periksa perhitungan bulan Mei
        $resMei = $this->service->hitungWidyaiswara($wi->id, 5, 2026);
        $this->assertEquals(10.00, $resMei['total_ab'], 'Mei harus hanya 10 JP.');

        // Periksa perhitungan bulan Juni
        $resJuni = $this->service->hitungWidyaiswara($wi->id, 6, 2026);
        $this->assertEquals(20.00, $resJuni['total_ab'], 'Juni harus hanya 20 JP.');
    }

    /**
     * Test 8: Status pencapaian 32 JP (Belum Memenuhi, Memenuhi, Melebihi).
     */
    public function test_status_pencapaian_32_jp(): void
    {
        $wi = Widyaiswara::create([
            'nik' => '99990008',
            'nama_lengkap' => 'Widyaiswara Test 8',
            'jabatan' => 'Widyaiswara Ahli Madya',
            'instansi' => 'PPSDM',
            'status' => 'Aktif',
        ]);

        // Kasus 1: Belum memenuhi (< 32 JP) -> e.g. 20 JP
        $k1 = KonversiJp::create([
            'widyaiswara_id' => $wi->id,
            'tanggal_kegiatan' => '2026-07-01',
            'nama_kegiatan' => 'Kegiatan 1',
            'kategori_konversi' => 'Penyusunan Modul',
            'jumlah_jp' => 20.00,
            'status_validasi' => 'Disetujui',
        ]);

        $res1 = $this->service->hitungWidyaiswara($wi->id, 7, 2026);
        $this->assertEquals('Belum Memenuhi', $res1['status_pencapaian']);
        $this->assertEquals(12.00, $res1['kekurangan_jp']);

        // Kasus 2: Pas Memenuhi (= 32 JP) -> tambah 12 JP
        $k2 = KonversiJp::create([
            'widyaiswara_id' => $wi->id,
            'tanggal_kegiatan' => '2026-07-02',
            'nama_kegiatan' => 'Kegiatan 2',
            'kategori_konversi' => 'Penyusunan Modul',
            'jumlah_jp' => 12.00,
            'status_validasi' => 'Disetujui',
        ]);

        $res2 = $this->service->hitungWidyaiswara($wi->id, 7, 2026);
        $this->assertEquals('Memenuhi', $res2['status_pencapaian']);
        $this->assertEquals(0.00, $res2['kekurangan_jp']);

        // Kasus 3: Melebihi (> 32 JP) -> tambah lagi 5 JP
        KonversiJp::create([
            'widyaiswara_id' => $wi->id,
            'tanggal_kegiatan' => '2026-07-03',
            'nama_kegiatan' => 'Kegiatan 3',
            'kategori_konversi' => 'Penyusunan Modul',
            'jumlah_jp' => 5.00,
            'status_validasi' => 'Disetujui',
        ]);

        $res3 = $this->service->hitungWidyaiswara($wi->id, 7, 2026);
        $this->assertEquals('Melebihi', $res3['status_pencapaian']);
        $this->assertEquals(5.00, $res3['kelebihan_jp']);
    }
}
