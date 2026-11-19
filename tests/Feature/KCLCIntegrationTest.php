<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Ruangan;
use App\Models\Pelatihan;
use App\Models\Angkatan;
use App\Models\MataPelatihan;
use App\Models\KelasMapel;
use App\Models\Peserta;
use App\Models\AspekPenilaianSikap;
use App\Models\FormEvaluasi;
use Tests\TestCase;

class KCLCIntegrationTest extends TestCase
{
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::first() ?? User::factory()->create();
    }

    public function test_ruangan_crud(): void
    {
        $response = $this->actingAs($this->user)->get(route('ruangan.index'));
        $response->assertStatus(200);
        $response->assertSee('RUANGAN');

        // Store
        $storeResponse = $this->actingAs($this->user)->post(route('ruangan.store'), [
            'nama_ruangan' => 'Ruang Kelas Simulasi Baru',
            'kapasitas' => '40 Peserta',
            'lokasi' => 'Gedung A Lt. 1',
            'is_aktif' => '1',
        ]);
        $storeResponse->assertRedirect(route('ruangan.index'));
        $this->assertDatabaseHas('ruangans', ['nama_ruangan' => 'Ruang Kelas Simulasi Baru']);

        $ruangan = Ruangan::where('nama_ruangan', 'Ruang Kelas Simulasi Baru')->first();

        // Update
        $updateResponse = $this->actingAs($this->user)->put(route('ruangan.update', $ruangan->id), [
            'nama_ruangan' => 'Ruang Kelas Simulasi Updated',
            'kapasitas' => '50 Peserta',
            'lokasi' => 'Gedung A Lt. 2',
            'is_aktif' => '1',
        ]);
        $updateResponse->assertRedirect(route('ruangan.index'));
        $this->assertDatabaseHas('ruangans', ['nama_ruangan' => 'Ruang Kelas Simulasi Updated']);

        // Destroy
        $destroyResponse = $this->actingAs($this->user)->delete(route('ruangan.destroy', $ruangan->id));
        $destroyResponse->assertRedirect(route('ruangan.index'));
        $this->assertDatabaseMissing('ruangans', ['id' => $ruangan->id]);
    }

    public function test_kelas_mapel_four_tabs_flow(): void
    {
        $pelatihan = Pelatihan::first() ?? Pelatihan::create([
            'kode_pelatihan' => 'PEL-TEST-01',
            'nama_pelatihan' => 'Pelatihan Test KCLC',
            'kategori' => 'Teknis',
            'tanggal_mulai' => '2026-11-01',
            'tanggal_selesai' => '2026-11-30',
        ]);

        $angkatan = Angkatan::first() ?? Angkatan::create([
            'pelatihan_id' => $pelatihan->id,
            'nomor_angkatan' => 'Angkatan Test KCLC',
            'tanggal_mulai' => '2026-11-01',
            'tanggal_selesai' => '2026-11-30',
        ]);

        $kelas = KelasMapel::first() ?? KelasMapel::create([
            'pelatihan_id' => $pelatihan->id,
            'angkatan_id' => $angkatan->id,
            'nama_kelas' => 'PK PPPK Test',
            'kapasitas' => 350,
            'kurikulum' => '2026',
            'jp' => 4,
            'ada_kelompok' => 'tidak',
            'hari' => 'Rabu',
            'jam_mulai' => '08:00',
            'jam_selesai' => '12:00',
            'metode_pembelajaran' => 'Synchronous',
        ]);

        // Tab 1: Detail
        $resDetail = $this->actingAs($this->user)->get(route('kelas-mapel.show', ['kelas_mapel' => $kelas->id, 'tab' => 'detail']));
        $resDetail->assertStatus(200);
        $resDetail->assertSee('Detail Kelas');
        $resDetail->assertSee('Buat Jadwal Mingguan');

        // Tab 2: Pengajar
        $resPengajar = $this->actingAs($this->user)->get(route('kelas-mapel.show', ['kelas_mapel' => $kelas->id, 'tab' => 'pengajar']));
        $resPengajar->assertStatus(200);
        $resPengajar->assertSee('Informasi Daftar Pengajar');

        // Tab 3: Jadwal
        $resJadwal = $this->actingAs($this->user)->get(route('kelas-mapel.show', ['kelas_mapel' => $kelas->id, 'tab' => 'jadwal']));
        $resJadwal->assertStatus(200);
        $resJadwal->assertSee('Jadwal Pelaksanaan Sesi');

        // Tab 4: Peserta
        $resPeserta = $this->actingAs($this->user)->get(route('kelas-mapel.show', ['kelas_mapel' => $kelas->id, 'tab' => 'peserta']));
        $resPeserta->assertStatus(200);
        $resPeserta->assertSee('Daftar Peserta Pelatihan');
    }

    public function test_aspek_penilaian_sikap_and_evaluasi_form(): void
    {
        $pelatihan = Pelatihan::first();

        // Sikap Form
        $resSikap = $this->actingAs($this->user)->get(route('sikap-form.index'));
        $resSikap->assertStatus(200);
        $resSikap->assertSee('FORM ASPEK PENILAIAN SIKAP');

        // Evaluasi Form
        $resEval = $this->actingAs($this->user)->get(route('evaluasi-form.index'));
        $resEval->assertStatus(200);
        $resEval->assertSee('FORM EVALUASI');
    }

    public function test_dashboard_displays_kclc_and_sir_jp_sections(): void
    {
        $response = $this->actingAs($this->user)->get(route('dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Dashboard KCLC Learning Center');
        $response->assertSee('Statistik KCLC Learning Center');
        $response->assertSee('Statistik Pelatihan');
        $response->assertSee('Rekapitulasi Jam Pelajaran');
    }
}
