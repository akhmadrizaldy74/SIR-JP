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
use App\Models\AttendanceSession;
use App\Models\FormEvaluasi;
use App\Models\Jadwal;
use App\Models\KelasMapelPengajar;
use App\Models\PenugasanPengajar;
use App\Models\Widyaiswara;
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
        $response->assertSee('Ringkasan aktivitas setiap pelatihan');
        $response->assertSee('Pengantar Kerja PPPK');
        $response->assertSee('472');
        $response->assertSee('Pengawas Ketenagakerjaan A.112');
        $response->assertSee('1,613');
        $response->assertSee('Instruktur A.7');
        $response->assertSee('1,041');
        $response->assertSee('Rekapitulasi Jam Pelajaran');
    }

    public function test_schedule_sync_is_idempotent_and_links_schedule_to_class(): void
    {
        $kelasMapel = $this->createKelasMapelFixture(uniqid('sync-'));

        $this->actingAs($this->user)->post(route('kelas-mapel.sinkron_jadwal', $kelasMapel->id));
        $this->actingAs($this->user)->post(route('kelas-mapel.sinkron_jadwal', $kelasMapel->id));

        $this->assertSame(1, Jadwal::where('kelas_mapel_id', $kelasMapel->id)->count());
        $this->assertDatabaseHas('jadwals', [
            'kelas_mapel_id' => $kelasMapel->id,
            'mata_pelatihan_id' => $kelasMapel->mata_pelatihan_id,
        ]);
        $this->assertSame(1, $kelasMapel->jadwals()->count());
    }

    public function test_schedule_sync_links_a_matching_legacy_schedule_instead_of_duplicating_it(): void
    {
        $kelasMapel = $this->createKelasMapelFixture(uniqid('legacy-'));
        $keterangan = 'Sesi KCLC ' . $kelasMapel->nama_kelas . ' (' . $kelasMapel->hari . ')';

        Jadwal::create([
            'mata_pelatihan_id' => $kelasMapel->mata_pelatihan_id,
            'tanggal' => $kelasMapel->tanggal_mulai->format('Y-m-d'),
            'jam_mulai' => $kelasMapel->jam_mulai,
            'jam_selesai' => $kelasMapel->jam_selesai,
            'jp_rencana' => $kelasMapel->jp,
            'status' => 'Terjadwal',
            'keterangan' => $keterangan,
        ]);

        $this->actingAs($this->user)->post(route('kelas-mapel.sinkron_jadwal', $kelasMapel->id));

        $this->assertSame(1, Jadwal::where('mata_pelatihan_id', $kelasMapel->mata_pelatihan_id)
            ->where('keterangan', $keterangan)
            ->count());
        $this->assertDatabaseHas('jadwals', [
            'kelas_mapel_id' => $kelasMapel->id,
            'keterangan' => $keterangan,
        ]);
    }

    public function test_attendance_session_inherits_class_from_its_schedule(): void
    {
        $kelasMapel = $this->createKelasMapelFixture(uniqid('session-'));
        $jadwal = Jadwal::create([
            'mata_pelatihan_id' => $kelasMapel->mata_pelatihan_id,
            'kelas_mapel_id' => $kelasMapel->id,
            'tanggal' => $kelasMapel->tanggal_mulai->format('Y-m-d'),
            'jam_mulai' => $kelasMapel->jam_mulai,
            'jam_selesai' => $kelasMapel->jam_selesai,
            'jp_rencana' => $kelasMapel->jp,
            'status' => 'Terjadwal',
        ]);

        $this->actingAs($this->user)->post(route('attendance-sessions.store'), [
            'jadwal_id' => $jadwal->id,
            'judul_sesi' => 'Sesi uji keterkaitan kelas',
            'tanggal' => $kelasMapel->tanggal_mulai->format('Y-m-d'),
            'jam_mulai' => $kelasMapel->jam_mulai,
            'jam_selesai' => $kelasMapel->jam_selesai,
            'toleransi_terlambat_menit' => 15,
            'status' => 'Draft',
        ]);

        $this->assertDatabaseHas('attendance_sessions', [
            'jadwal_id' => $jadwal->id,
            'kelas_mapel_id' => $kelasMapel->id,
        ]);
    }

    public function test_removing_last_kclc_teacher_soft_deletes_unreferenced_integrated_assignment(): void
    {
        $kelasMapel = $this->createKelasMapelFixture(uniqid('teacher-'));
        $widyaiswara = $this->createWidyaiswaraFixture(uniqid('teacher-'));

        $this->actingAs($this->user)->post(route('kelas-mapel.pengajar.store', $kelasMapel->id), [
            'pengajar_combo' => 'Widyaiswara:' . $widyaiswara->id,
            'jp' => 4,
            'peran' => 'widyaiswara',
        ]);

        $pengajar = KelasMapelPengajar::where('kelas_mapel_id', $kelasMapel->id)->firstOrFail();
        $penugasan = PenugasanPengajar::where('mata_pelatihan_id', $kelasMapel->mata_pelatihan_id)
            ->where('widyaiswara_id', $widyaiswara->id)
            ->firstOrFail();

        $this->actingAs($this->user)->delete(route('kelas-mapel.pengajar.destroy', [
            'kelas_mapel' => $kelasMapel->id,
            'pengajar' => $pengajar->id,
        ]));

        $this->assertSoftDeleted('penugasan_pengajars', ['id' => $penugasan->id]);
    }

    public function test_removing_teacher_keeps_integrated_assignment_still_used_by_another_class(): void
    {
        $kelasMapel = $this->createKelasMapelFixture(uniqid('shared-'));
        $kelasMapelLain = $this->createKelasMapelFixture(uniqid('shared-'), $kelasMapel->mataPelatihan);
        $widyaiswara = $this->createWidyaiswaraFixture(uniqid('shared-'));

        foreach ([$kelasMapel, $kelasMapelLain] as $kelas) {
            $this->actingAs($this->user)->post(route('kelas-mapel.pengajar.store', $kelas->id), [
                'pengajar_combo' => 'Widyaiswara:' . $widyaiswara->id,
                'jp' => 4,
                'peran' => 'widyaiswara',
            ]);
        }

        $penugasan = PenugasanPengajar::where('mata_pelatihan_id', $kelasMapel->mata_pelatihan_id)
            ->where('widyaiswara_id', $widyaiswara->id)
            ->firstOrFail();
        $pengajar = KelasMapelPengajar::where('kelas_mapel_id', $kelasMapel->id)->firstOrFail();

        $this->actingAs($this->user)->delete(route('kelas-mapel.pengajar.destroy', [
            'kelas_mapel' => $kelasMapel->id,
            'pengajar' => $pengajar->id,
        ]));

        $this->assertDatabaseHas('penugasan_pengajars', ['id' => $penugasan->id, 'deleted_at' => null]);
    }

    public function test_removing_teacher_keeps_integrated_assignment_referenced_by_schedule_history(): void
    {
        $kelasMapel = $this->createKelasMapelFixture(uniqid('history-'));
        $widyaiswara = $this->createWidyaiswaraFixture(uniqid('history-'));

        $this->actingAs($this->user)->post(route('kelas-mapel.pengajar.store', $kelasMapel->id), [
            'pengajar_combo' => 'Widyaiswara:' . $widyaiswara->id,
            'jp' => 4,
            'peran' => 'widyaiswara',
        ]);
        $this->actingAs($this->user)->post(route('kelas-mapel.sinkron_jadwal', $kelasMapel->id));

        $pengajar = KelasMapelPengajar::where('kelas_mapel_id', $kelasMapel->id)->firstOrFail();
        $penugasan = PenugasanPengajar::where('mata_pelatihan_id', $kelasMapel->mata_pelatihan_id)
            ->where('widyaiswara_id', $widyaiswara->id)
            ->firstOrFail();

        $this->actingAs($this->user)->delete(route('kelas-mapel.pengajar.destroy', [
            'kelas_mapel' => $kelasMapel->id,
            'pengajar' => $pengajar->id,
        ]));

        $this->assertDatabaseHas('penugasan_pengajars', ['id' => $penugasan->id, 'deleted_at' => null]);
    }

    private function createKelasMapelFixture(string $suffix, ?MataPelatihan $mataPelatihan = null): KelasMapel
    {
        if (!$mataPelatihan) {
            $pelatihan = Pelatihan::create([
                'kode_pelatihan' => 'SYNC-' . $suffix,
                'nama_pelatihan' => 'Pelatihan Sinkron ' . $suffix,
                'kategori' => 'Teknis',
                'tanggal_mulai' => '2026-11-01',
                'tanggal_selesai' => '2026-11-30',
            ]);
            $angkatan = Angkatan::create([
                'pelatihan_id' => $pelatihan->id,
                'nomor_angkatan' => 'Angkatan ' . $suffix,
                'tanggal_mulai' => '2026-11-01',
                'tanggal_selesai' => '2026-11-30',
            ]);
            $mataPelatihan = MataPelatihan::create([
                'angkatan_id' => $angkatan->id,
                'nama_mata_pelatihan' => 'Materi Sinkron ' . $suffix,
                'rencana_jp' => 4,
            ]);
        } else {
            $pelatihan = $mataPelatihan->angkatan->pelatihan;
            $angkatan = $mataPelatihan->angkatan;
        }

        return KelasMapel::create([
            'pelatihan_id' => $pelatihan->id,
            'angkatan_id' => $angkatan->id,
            'mata_pelatihan_id' => $mataPelatihan->id,
            'nama_kelas' => 'Kelas Sinkron ' . $suffix,
            'kapasitas' => 30,
            'kurikulum' => '2026',
            'tanggal_mulai' => '2026-11-15',
            'tanggal_selesai' => '2026-11-15',
            'jp' => 4,
            'ada_kelompok' => 'tidak',
            'hari' => 'Minggu',
            'jam_mulai' => '08:00:00',
            'jam_selesai' => '11:30:00',
            'metode_pembelajaran' => 'Klasikal',
        ]);
    }

    private function createWidyaiswaraFixture(string $suffix): Widyaiswara
    {
        return Widyaiswara::create([
            'nik' => '1234567890' . substr(md5($suffix), 0, 6),
            'nip' => '1980' . substr(md5($suffix), 0, 14),
            'nama_lengkap' => 'Widyaiswara Uji ' . $suffix,
            'jabatan' => 'Widyaiswara Ahli Muda',
            'instansi' => 'PPSDM',
            'status' => 'Aktif',
        ]);
    }
}
