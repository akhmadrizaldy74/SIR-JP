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

    public function test_pengajar_has_absensi_in_sidebar_and_can_mark_attendance_without_qr()
    {
        $unique = time() . rand(100, 999);
        $pengajar = User::create([
            'name' => 'Widyaiswara Test ' . $unique,
            'username' => 'pengajar' . $unique,
            'email' => 'pengajar' . $unique . '@kemnaker.go.id',
            'password' => bcrypt('password'),
            'role' => 'pengajar',
            'roles' => ['Pengajar'],
            'status' => 'Aktif',
        ]);

        // 1. Pengajar melihat menu Absensi Peserta di sidebar
        $resp = $this->actingAs($pengajar)->get(route('dashboard'));
        $resp->assertOk();
        $resp->assertSee('Absensi Peserta');
        $resp->assertSee('Rekapitulasi Absensi');
        $resp->assertSee('Absen Pengajar (Klaim JP)');

        // 2. Pengajar membuka halaman sesi absensi
        $respSession = $this->actingAs($pengajar)->get(route('attendance-sessions.show', $this->session->id));
        $respSession->assertOk();
        $respSession->assertSee('Tandai Semua Hadir');
        $respSession->assertSee('Ubah Status');

        // 3. Pengajar menandai peserta hadir langsung tanpa QR
        $respMark = $this->actingAs($pengajar)
            ->post(route('attendance-manual.update', $this->session->id), [
                'peserta_id' => $this->peserta->id,
                'status' => 'Hadir',
                'alasan_perubahan' => 'Presensi langsung oleh pengajar di kelas online',
            ]);

        $respMark->assertRedirect();
        $this->assertDatabaseHas('presensis', [
            'attendance_session_id' => $this->session->id,
            'peserta_id' => $this->peserta->id,
            'status_kehadiran' => 'Hadir',
        ]);
    }

    public function test_pengajar_only_sees_their_own_sessions_and_peserta_only_sees_their_own_class()
    {
        // Buat Pengajar A dan Pengajar B
        $unique = rand(1000, 9999);
        $pengajarA = User::create([
            'name' => 'Pengajar Alfa ' . $unique,
            'email' => 'pengajar.alfa.' . $unique . '@kemnaker.go.id',
            'username' => 'pengajar_alfa_' . $unique,
            'password' => bcrypt('password'),
            'role' => 'pengajar',
            'roles' => ['Pengajar'],
        ]);

        $pengajarB = User::create([
            'name' => 'Pengajar Beta ' . $unique,
            'email' => 'pengajar.beta.' . $unique . '@kemnaker.go.id',
            'username' => 'pengajar_beta_' . $unique,
            'password' => bcrypt('password'),
            'role' => 'pengajar',
            'roles' => ['Pengajar'],
        ]);

        // Buat sesi milik Pengajar A
        $sessionA = AttendanceSession::create([
            'jadwal_id' => $this->session->jadwal_id,
            'kelas_mapel_id' => $this->session->kelas_mapel_id,
            'judul_sesi' => 'Sesi Khusus Alfa ' . $unique,
            'tanggal' => now()->toDateString(),
            'jam_mulai' => '08:00',
            'jam_selesai' => '10:00',
            'status' => 'Aktif',
            'waktu_buka_presensi' => now(),
            'created_by' => $pengajarA->id,
        ]);

        // Buat sesi milik Pengajar B
        $sessionB = AttendanceSession::create([
            'jadwal_id' => $this->session->jadwal_id,
            'kelas_mapel_id' => $this->session->kelas_mapel_id,
            'judul_sesi' => 'Sesi Khusus Beta ' . $unique,
            'tanggal' => now()->toDateString(),
            'jam_mulai' => '10:00',
            'jam_selesai' => '12:00',
            'status' => 'Aktif',
            'waktu_buka_presensi' => now(),
            'created_by' => $pengajarB->id,
        ]);

        // 1. Pengajar A HANYA melihat sesi miliknya di daftar sesi presensi
        $respA = $this->actingAs($pengajarA)->get(route('attendance-sessions.index'));
        $respA->assertOk();
        $respA->assertSee('Sesi Khusus Alfa ' . $unique);
        $respA->assertDontSee('Sesi Khusus Beta ' . $unique);

        // 2. Pengajar B HANYA melihat sesi miliknya di daftar sesi presensi
        $respB = $this->actingAs($pengajarB)->get(route('attendance-sessions.index'));
        $respB->assertOk();
        $respB->assertSee('Sesi Khusus Beta ' . $unique);
        $respB->assertDontSee('Sesi Khusus Alfa ' . $unique);

        // 3. Peserta di Kelas Lain tidak dapat presensi di sesi khusus kelas ini
        $angkatan = $this->session->kelasMapel->angkatan;
        $kelasB = \App\Models\KelasMapel::create([
            'pelatihan_id' => $angkatan->pelatihan_id,
            'angkatan_id' => $angkatan->id,
            'mata_pelatihan_id' => $this->session->jadwal->mata_pelatihan_id,
            'nama_kelas' => 'Kelas Beta ' . $unique,
        ]);

        $pesertaKelasB = Peserta::create([
            'nip' => '199' . time() . rand(100, 999),
            'nama' => 'Siswa Kelas Beta ' . $unique,
            'email' => 'beta.' . $unique . '@kemnaker.go.id',
            'jenis_kelamin' => 'Perempuan',
            'angkatan_id' => $angkatan->id,
            'kelas_mapel_id' => $kelasB->id,
        ]);

        // Peserta Kelas B mencoba absen di sesi kelas A
        $service = app(\App\Services\AttendanceService::class);
        $res = $service->submitDirectAttendance($pesertaKelasB, $sessionA, 'Hadir');
        $this->assertFalse($res['success']);
        $this->assertStringContainsString('dikhususkan untuk kelas lain', $res['message']);
    }

    public function test_closing_session_triggers_auto_alpa_and_syncs_realisasi_jp()
    {
        $unique = rand(1000, 9999);
        $wi = \App\Models\Widyaiswara::create([
            'nik' => '317201' . rand(1000000000, 9999999999),
            'nip' => '19800101' . $unique . '1001',
            'nama_lengkap' => 'Widyaiswara Sync ' . $unique,
            'jabatan' => 'Widyaiswara Ahli Madya',
            'instansi' => 'PPSDMK',
            'status' => 'Aktif',
        ]);

        $penugasan = \App\Models\PenugasanPengajar::create([
            'mata_pelatihan_id' => $this->session->jadwal->mata_pelatihan_id,
            'jenis_pengajar' => 'Widyaiswara',
            'widyaiswara_id' => $wi->id,
            'peran' => 'Pengajar Utama',
        ]);

        $this->session->jadwal->update(['penugasan_pengajar_id' => $penugasan->id]);

        // Tutup sesi tanpa peserta melakukan presensi -> harus otomatis Alpa & RealisasiJadwal tercipta
        $response = $this->actingAs($this->admin)
            ->post(route('attendance-sessions.close', $this->session->id));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->session->refresh();
        $this->assertEquals('Selesai', $this->session->status);

        $this->assertDatabaseHas('presensis', [
            'attendance_session_id' => $this->session->id,
            'peserta_id' => $this->peserta->id,
            'status_kehadiran' => 'Alpa',
            'metode_presensi' => 'Sistem',
        ]);

        $this->assertDatabaseHas('realisasi_jadwals', [
            'jadwal_id' => $this->session->jadwal_id,
            'status' => 'Terlaksana',
        ]);

        $realisasi = \App\Models\RealisasiJadwal::where('jadwal_id', $this->session->jadwal_id)->first();
        $this->assertDatabaseHas('realisasi_pengajars', [
            'realisasi_jadwal_id' => $realisasi->id,
            'widyaiswara_id' => $wi->id,
            'status_kehadiran' => 'Hadir',
        ]);
    }

    public function test_expired_past_session_is_auto_closed_by_command()
    {
        $pastJadwal = Jadwal::create([
            'mata_pelatihan_id' => $this->session->jadwal->mata_pelatihan_id,
            'tanggal' => now()->subDays(2)->toDateString(),
            'jam_mulai' => '08:00:00',
            'jam_selesai' => '10:00:00',
            'jp_rencana' => 2,
            'status' => 'Terjadwal',
        ]);

        $pastSession = AttendanceSession::create([
            'jadwal_id' => $pastJadwal->id,
            'kelas_mapel_id' => $this->session->kelas_mapel_id,
            'judul_sesi' => 'Sesi Lupa Tutup Kemarin',
            'tanggal' => now()->subDays(2)->toDateString(),
            'jam_mulai' => '08:00:00',
            'jam_selesai' => '10:00:00',
            'status' => 'Aktif',
            'waktu_buka_presensi' => now()->subDays(2),
            'created_by' => $this->admin->id,
        ]);

        $this->artisan('attendance:auto-close')->assertExitCode(0);

        $pastSession->refresh();
        $this->assertEquals('Selesai', $pastSession->status);
        $this->assertDatabaseHas('presensis', [
            'attendance_session_id' => $pastSession->id,
            'peserta_id' => $this->peserta->id,
            'status_kehadiran' => 'Alpa',
        ]);
    }

    public function test_pengajar_can_approve_and_reject_direct_sakit_izin_proof()
    {
        $fakePdf = UploadedFile::fake()->create('surat_sakit.pdf', 200, 'application/pdf');
        $this->withSession(['participant_id' => $this->peserta->id])
            ->post(route('portal.presensi.direct.post', $this->session->id), [
                'status_kehadiran' => 'Sakit',
                'keterangan' => 'Demam tinggi.',
                'bukti_file' => $fakePdf,
            ]);

        $presensi = Presensi::where('attendance_session_id', $this->session->id)
            ->where('peserta_id', $this->peserta->id)
            ->first();

        $this->assertEquals('Menunggu Verifikasi', $presensi->status_verifikasi);

        // 1. Admin/Pengajar menolak bukti -> status kehadiran berubah menjadi Alpa
        $respReject = $this->actingAs($this->admin)
            ->post(route('attendance-sessions.verify_presensi', [$this->session->id, $presensi->id]), [
                'keputusan' => 'Ditolak',
                'catatan_verifikasi' => 'Dokumen buram tidak terbaca',
            ]);
        $respReject->assertRedirect();

        $presensi->refresh();
        $this->assertEquals('Ditolak', $presensi->status_verifikasi);
        $this->assertEquals('Alpa', $presensi->status_kehadiran);
    }

    public function test_bap_pdf_export_returns_valid_pdf_response()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('attendance-sessions.bap_pdf', $this->session->id));

        $response->assertOk();
        $this->assertStringContainsString('application/pdf', $response->headers->get('content-type'));
    }
}
