<?php

namespace Tests\Feature;

use App\Models\Angkatan;
use App\Models\AttendanceSession;
use App\Models\Jadwal;
use App\Models\KelasMapel;
use App\Models\MataPelatihan;
use App\Models\Pelatihan;
use App\Models\Peserta;
use App\Models\Presensi;
use App\Models\User;
use App\Services\AttendanceService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DirectAttendanceAndModuleAccessTest extends TestCase
{
    protected User $admin;
    protected Peserta $peserta;
    protected AttendanceSession $session;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        $this->admin = User::first() ?? User::factory()->create();

        $unique = rand(10000, 99999);

        $pelatihan = Pelatihan::create([
            'kode_pelatihan' => 'PEL-DIR-' . $unique,
            'nama_pelatihan' => 'Pelatihan Direct ' . $unique,
            'kategori' => 'Teknis',
            'penyelenggara' => 'PPSDMK',
            'tanggal_mulai' => '2026-11-23',
            'tanggal_selesai' => '2026-12-04',
            'status' => 'Berjalan',
        ]);

        $angkatan = Angkatan::create([
            'pelatihan_id' => $pelatihan->id,
            'nomor_angkatan' => 'Angkatan DIR ' . $unique,
            'tanggal_mulai' => '2026-11-23',
            'tanggal_selesai' => '2026-12-04',
            'jumlah_peserta' => 30,
            'status' => 'Berjalan',
        ]);

        $mapel = MataPelatihan::create([
            'angkatan_id' => $angkatan->id,
            'nama_mata_pelatihan' => 'Mapel DIR ' . $unique,
            'rencana_jp' => 4,
        ]);

        $kelas = KelasMapel::create([
            'pelatihan_id' => $pelatihan->id,
            'angkatan_id' => $angkatan->id,
            'mata_pelatihan_id' => $mapel->id,
            'nama_kelas' => 'Kelas DIR ' . $unique,
            'kapasitas' => 30,
            'kurikulum' => '2026',
            'jp' => 4,
            'ada_kelompok' => 'tidak',
            'metode_pembelajaran' => 'Synchronous',
        ]);

        $jadwal = Jadwal::create([
            'mata_pelatihan_id' => $mapel->id,
            'tanggal' => now()->toDateString(),
            'jam_mulai' => '08:00:00',
            'jam_selesai' => '10:00:00',
            'sesi_ke' => 1,
            'jp_rencana' => 2,
            'ruangan' => 'Lab Komputer',
            'status' => 'Terjadwal',
        ]);

        $this->peserta = Peserta::create([
            'nip' => '198' . time() . rand(1000, 9999),
            'nama' => 'Peserta Direct ' . $unique,
            'email' => 'peserta_dir' . $unique . '@kemnaker.go.id',
            'jenis_kelamin' => 'Laki-laki',
            'instansi' => 'PPSDM Kemnaker',
            'angkatan_id' => $angkatan->id,
            'kelas_mapel_id' => $kelas->id,
        ]);

        app(AttendanceService::class)->getOrGenerateAccessCode($this->peserta, '123456');

        $this->session = AttendanceSession::create([
            'jadwal_id' => $jadwal->id,
            'kelas_mapel_id' => $kelas->id,
            'judul_sesi' => 'Pertemuan 1 - Pembelajaran Mandiri',
            'tanggal' => now()->toDateString(),
            'jam_mulai' => '08:00:00',
            'jam_selesai' => '10:00:00',
            'toleransi_terlambat_menit' => 30,
            'status' => 'Aktif',
            'waktu_buka_presensi' => now()->subMinutes(10),
            'created_by' => $this->admin->id,
        ]);
    }

    public function test_peserta_can_submit_direct_attendance_without_qr()
    {
        $fakePhoto = UploadedFile::fake()->image('selfie_kehadiran.jpg', 600, 600);

        $response = $this->withSession(['participant_id' => $this->peserta->id])
            ->post(route('portal.presensi.direct.post', $this->session->id), [
                'status_kehadiran' => 'Hadir',
                'keterangan' => 'Hadir tepat waktu mengikuti sesi kelas daring.',
                'bukti_file' => $fakePhoto,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('presensis', [
            'attendance_session_id' => $this->session->id,
            'peserta_id' => $this->peserta->id,
            'status_kehadiran' => 'Hadir',
            'metode_presensi' => 'Mandiri',
            'keterangan' => 'Hadir tepat waktu mengikuti sesi kelas daring.',
        ]);

        $presensi = Presensi::where('attendance_session_id', $this->session->id)
            ->where('peserta_id', $this->peserta->id)
            ->first();

        $this->assertNotNull($presensi->bukti_file);
        Storage::disk('public')->assertExists($presensi->bukti_file);
    }

    public function test_peserta_can_submit_sakit_with_pdf_proof()
    {
        $unique = rand(1000, 9999);
        $peserta2 = Peserta::create([
            'nip' => '197' . time() . $unique,
            'nama' => 'Peserta Sakit ' . $unique,
            'email' => 'peserta_sakit' . $unique . '@kemnaker.go.id',
            'jenis_kelamin' => 'Perempuan',
            'instansi' => 'PPSDM Kemnaker',
            'angkatan_id' => $this->peserta->angkatan_id,
            'kelas_mapel_id' => $this->peserta->kelas_mapel_id,
        ]);

        $fakePdf = UploadedFile::fake()->create('surat_dokter.pdf', 300, 'application/pdf');

        $response = $this->withSession(['participant_id' => $peserta2->id])
            ->post(route('portal.presensi.direct.post', $this->session->id), [
                'status_kehadiran' => 'Sakit',
                'keterangan' => 'Izin tidak hadir karena demam.',
                'bukti_file' => $fakePdf,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('presensis', [
            'attendance_session_id' => $this->session->id,
            'peserta_id' => $peserta2->id,
            'status_kehadiran' => 'Sakit',
            'metode_presensi' => 'Mandiri',
        ]);
    }

    public function test_admin_can_update_participant_module_access()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('participant-access.modules'), [
                'modules' => ['presensi', 'tugas', 'materi', 'kuis'],
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Pastikan tersimpan di Pengaturan
        $stored = json_decode(\App\Models\Pengaturan::get('participant_modules'), true);
        $this->assertContains('presensi', $stored);
        $this->assertContains('tugas', $stored);
        $this->assertContains('materi', $stored);
        $this->assertContains('kuis', $stored);
        $this->assertNotContains('ujian', $stored);
    }

    public function test_peserta_and_admin_can_view_uploaded_attendance_proof_without_403()
    {
        $fakePdf = UploadedFile::fake()->create('surat_dokter.pdf', 300, 'application/pdf');

        $this->withSession(['participant_id' => $this->peserta->id])
            ->post(route('portal.presensi.direct.post', $this->session->id), [
                'status_kehadiran' => 'Sakit',
                'keterangan' => 'Izin sakit.',
                'bukti_file' => $fakePdf,
            ]);

        $presensi = Presensi::where('attendance_session_id', $this->session->id)
            ->where('peserta_id', $this->peserta->id)
            ->first();

        $this->assertNotNull($presensi->bukti_file);

        // 1. Peserta pemilik bukti bisa melihat bukti tanpa 403
        $respPeserta = $this->withSession(['participant_id' => $this->peserta->id])
            ->get(route('portal.presensi.bukti', $presensi->id));
        $respPeserta->assertOk();

        // 2. Admin/Pengajar bisa melihat bukti
        $respAdmin = $this->actingAs($this->admin)
            ->get(route('portal.presensi.bukti', $presensi->id));
        $respAdmin->assertOk();

        // 3. Peserta lain dicegah (403)
        $unique = rand(1000, 9999);
        $otherPeserta = Peserta::create([
            'nip' => '198' . time() . $unique,
            'nama' => 'Peserta Lain ' . $unique,
            'email' => 'other' . $unique . '@kemnaker.go.id',
            'jenis_kelamin' => 'Laki-laki',
        ]);
        $respOther = $this->withSession(['participant_id' => $otherPeserta->id])
            ->get(route('portal.presensi.bukti', $presensi->id));
        $respOther->assertStatus(403);
    }

    public function test_pengajar_can_update_meeting_link_and_it_shows_on_participant_dashboard()
    {
        $zoomUrl = 'https://us04web.zoom.us/j/9876543210';

        // Admin / Pengajar update link meeting
        $response = $this->actingAs($this->admin)
            ->patch(route('attendance-sessions.update_meeting_link', $this->session->id), [
                'link_meeting' => $zoomUrl,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->session->refresh();
        $this->assertEquals($zoomUrl, $this->session->link_meeting);

        // Peserta melihat link meeting di dashboard portal
        $portalResponse = $this->withSession(['participant_id' => $this->peserta->id])
            ->get(route('portal.dashboard'));

        $portalResponse->assertOk();
        $portalResponse->assertSee($zoomUrl);
        $portalResponse->assertSee('Gabung Zoom / Kelas Daring');
    }
}
