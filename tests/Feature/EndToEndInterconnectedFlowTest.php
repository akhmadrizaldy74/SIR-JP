<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Ruangan;
use App\Models\Pelatihan;
use App\Models\Angkatan;
use App\Models\MataPelatihan;
use App\Models\Widyaiswara;
use App\Models\Narasumber;
use App\Models\KelasMapel;
use App\Models\KelasMapelPengajar;
use App\Models\Peserta;
use App\Models\Presensi;
use App\Models\AspekPenilaianSikap;
use App\Models\FormEvaluasi;
use App\Models\Jadwal;
use App\Models\RealisasiJadwal;
use App\Models\RealisasiPengajar;
use Tests\TestCase;

class EndToEndInterconnectedFlowTest extends TestCase
{
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::first() ?? User::factory()->create();
    }

    public function test_complete_interconnected_workflow_from_kclc_to_sir_jp_rekap(): void
    {
        $unique = rand(1000, 9999);

        // -------------------------------------------------------------
        // STEP 1: Buat Ruangan di KCLC
        // -------------------------------------------------------------
        $ruangan = Ruangan::create([
            'nama_ruangan' => 'Ruang Kelas E2E ' . $unique,
            'kapasitas' => '40 Peserta',
            'lokasi' => 'Gedung KCLC Lt. 3',
            'is_aktif' => true,
        ]);
        $this->assertDatabaseHas('ruangans', ['id' => $ruangan->id]);

        // -------------------------------------------------------------
        // STEP 2: Buat Pelatihan & Angkatan
        // -------------------------------------------------------------
        $pelatihan = Pelatihan::create([
            'kode_pelatihan' => 'PEL-E2E-' . $unique,
            'nama_pelatihan' => 'Pelatihan Terintegrasi E2E ' . $unique,
            'kategori' => 'Teknis',
            'penyelenggara' => 'PPSDM Kemnaker (KCLC)',
            'tanggal_mulai' => '2026-11-01',
            'tanggal_selesai' => '2026-11-30',
            'status' => 'Berjalan',
        ]);

        $angkatan = Angkatan::create([
            'pelatihan_id' => $pelatihan->id,
            'nomor_angkatan' => 'Angkatan E2E ' . $unique,
            'tanggal_mulai' => '2026-11-01',
            'tanggal_selesai' => '2026-11-30',
            'jumlah_peserta' => 30,
            'status' => 'Berjalan',
        ]);

        // -------------------------------------------------------------
        // STEP 3: Buat Mata Pelatihan
        // -------------------------------------------------------------
        $mataPelatihan = MataPelatihan::create([
            'angkatan_id' => $angkatan->id,
            'nama_mata_pelatihan' => 'Kepemimpinan dan Integritas ASN ' . $unique,
            'rencana_jp' => 6,
            'deskripsi' => 'Penguatan integritas dan tata kelola',
        ]);

        // -------------------------------------------------------------
        // STEP 4: Buat Widyaiswara (Pengajar Internal)
        // -------------------------------------------------------------
        $wi = Widyaiswara::create([
            'nik' => '317101' . rand(1000000000, 9999999999),
            'nip' => '1975' . time() . rand(1000, 9999),
            'nama_lengkap' => 'Dr. H. Bambang E2E ' . $unique,
            'gelar_depan' => 'Dr.',
            'gelar_belakang' => 'M.Si.',
            'jabatan' => 'Widyaiswara Ahli Utama',
            'instansi' => 'Pusat Pengembangan SDM Kemnaker',
            'status' => 'Aktif',
        ]);

        // -------------------------------------------------------------
        // STEP 5: Buat Kelas Mapel di KCLC
        // -------------------------------------------------------------
        $kelasMapel = KelasMapel::create([
            'pelatihan_id' => $pelatihan->id,
            'angkatan_id' => $angkatan->id,
            'mata_pelatihan_id' => $mataPelatihan->id,
            'ruangan_id' => $ruangan->id,
            'nama_kelas' => 'Kelas E2E ' . $unique,
            'kapasitas' => 40,
            'kurikulum' => '2026',
            'tanggal_mulai' => '2026-11-15',
            'tanggal_selesai' => '2026-11-15',
            'jp' => 6,
            'ada_kelompok' => 'tidak',
            'hari' => 'Rabu',
            'jam_mulai' => '08:00:00',
            'jam_selesai' => '13:30:00',
            'metode_pembelajaran' => 'Synchronous',
        ]);

        // -------------------------------------------------------------
        // STEP 6: Input Pengajar di KCLC Tab 2 -> Auto-sync ke SIR-JP Penugasan
        // -------------------------------------------------------------
        $assignResponse = $this->actingAs($this->user)->post(
            route('kelas-mapel.pengajar.store', $kelasMapel->id),
            [
                'pengajar_combo' => 'Widyaiswara:' . $wi->id,
                'jp' => 6,
                'peran' => 'widyaiswara',
                'jadwal_keterangan' => 'Rabu 08:00-13:30',
            ]
        );
        $assignResponse->assertRedirect(route('kelas-mapel.show', ['kelas_mapel' => $kelasMapel->id, 'tab' => 'pengajar']));

        // Verifikasi bahwa PenugasanPengajar di SIR-JP otomatis terbuat!
        $this->assertDatabaseHas('penugasan_pengajars', [
            'mata_pelatihan_id' => $mataPelatihan->id,
            'jenis_pengajar' => 'Widyaiswara',
            'widyaiswara_id' => $wi->id,
        ]);

        // -------------------------------------------------------------
        // STEP 7: Sinkronkan / Buat Sesi Jadwal ke SIR-JP
        // -------------------------------------------------------------
        $syncResponse = $this->actingAs($this->user)->post(
            route('kelas-mapel.sinkron_jadwal', $kelasMapel->id)
        );
        $syncResponse->assertRedirect(route('kelas-mapel.show', ['kelas_mapel' => $kelasMapel->id, 'tab' => 'jadwal']));

        // Verifikasi bahwa Jadwal di SIR-JP berhasil terbentuk dan terhubung
        $this->assertDatabaseHas('jadwals', [
            'mata_pelatihan_id' => $mataPelatihan->id,
            'tanggal' => '2026-11-15',
            'jp_rencana' => 6,
            'ruangan' => $ruangan->nama_ruangan,
            'status' => 'Terjadwal',
        ]);

        $jadwal = Jadwal::where('mata_pelatihan_id', $mataPelatihan->id)->first();

        // -------------------------------------------------------------
        // STEP 8: Daftarkan Peserta Diklat & Presensi Hadir di KCLC
        // -------------------------------------------------------------
        $peserta = Peserta::create([
            'nip' => '199510102022031' . rand(100, 999),
            'nama' => 'Peserta Test E2E ' . $unique,
            'email' => 'peserta' . $unique . '@kemnaker.go.id',
            'jenis_kelamin' => 'Laki-laki',
            'instansi' => 'Ditjen Binapenta & PKK',
            'jabatan' => 'Pengantar Kerja',
            'golongan' => 'III/a',
            'angkatan_id' => $angkatan->id,
            'kelas_mapel_id' => $kelasMapel->id,
        ]);

        Presensi::create([
            'peserta_id' => $peserta->id,
            'jadwal_id' => $jadwal->id,
            'kelas_mapel_id' => $kelasMapel->id,
            'tanggal' => '2026-11-15',
            'status_kehadiran' => 'Hadir',
            'waktu_presensi' => '07:50:00',
        ]);

        $this->assertDatabaseHas('presensis', [
            'peserta_id' => $peserta->id,
            'status_kehadiran' => 'Hadir',
        ]);

        // -------------------------------------------------------------
        // STEP 9: Form Sikap & Form Evaluasi di KCLC
        // -------------------------------------------------------------
        $sikap = AspekPenilaianSikap::create([
            'pelatihan_id' => $pelatihan->id,
            'judul' => 'Penilaian Sikap E2E ' . $unique,
            'kategori' => 'Nilai Sikap Widyaiswara',
            'aspek_penilaian' => ['Kedisiplinan', 'Kerjasama'],
            'is_active' => true,
        ]);
        $this->assertDatabaseHas('aspek_penilaian_sikaps', ['id' => $sikap->id]);

        $evaluasi = FormEvaluasi::create([
            'pelatihan_id' => $pelatihan->id,
            'judul_form' => 'Evaluasi Pengajar E2E ' . $unique,
            'deskripsi' => 'Kuesioner evaluasi pembelajaran',
            'is_active' => true,
        ]);
        $this->assertDatabaseHas('form_evaluasis', ['id' => $evaluasi->id]);

        // -------------------------------------------------------------
        // STEP 10: Konfirmasi Realisasi Sesi di SIR-JP
        // -------------------------------------------------------------
        $penugasan = \App\Models\PenugasanPengajar::where('mata_pelatihan_id', $mataPelatihan->id)->first();

        $realisasiResponse = $this->actingAs($this->user)->post(route('realisasi.store'), [
            'jadwal_id' => $jadwal->id,
            'tanggal_realisasi' => '2026-11-15',
            'jam_mulai_aktual' => '08:00',
            'jam_selesai_aktual' => '13:30',
            'status' => 'Terlaksana',
            'total_jp_aktual' => 6,
            'total_jp_dibayarkan' => 6,
            'catatan' => 'Sesi E2E terlaksana penuh dan tertib.',
            'pengajar' => [
                [
                    'selected' => 1,
                    'penugasan_pengajar_id' => $penugasan->id,
                    'jenis_pengajar' => 'Widyaiswara',
                    'pengajar_id' => $wi->id,
                    'jp_aktual' => 6,
                    'jp_dibayarkan' => 6,
                    'status_kehadiran' => 'Hadir',
                    'keterangan' => 'Mengajar penuh tepat waktu.',
                ]
            ],
        ]);
        $realisasiResponse->assertRedirect(route('realisasi.index'));

        // Pastikan status jadwal berubah menjadi Terlaksana
        $jadwal->refresh();
        $this->assertEquals('Terlaksana', $jadwal->status);

        // -------------------------------------------------------------
        // STEP 11: Verifikasi Aliran Otomatis ke Rekapitulasi SIR-JP
        // -------------------------------------------------------------
        $rekapDetailResponse = $this->actingAs($this->user)->get(
            route('rekapitulasi.detail', ['id' => $wi->id, 'bulan' => 11, 'tahun' => 2026])
        );
        $rekapDetailResponse->assertStatus(200);
        $rekapDetailResponse->assertSee($wi->nama_lengkap);
        $rekapDetailResponse->assertSee('Kepemimpinan dan Integritas ASN ' . $unique);

        // -------------------------------------------------------------
        // STEP 12: Ekspor Excel & PDF Widyaiswara Tersebut
        // -------------------------------------------------------------
        $excelResponse = $this->actingAs($this->user)->get(
            route('laporan.export.excel_individu', ['id' => $wi->id, 'bulan' => 11, 'tahun' => 2026])
        );
        $excelResponse->assertStatus(200);

        $pdfResponse = $this->actingAs($this->user)->get(
            route('laporan.export.pdf_individu', ['id' => $wi->id, 'bulan' => 11, 'tahun' => 2026])
        );
        $pdfResponse->assertStatus(200);
    }
}
