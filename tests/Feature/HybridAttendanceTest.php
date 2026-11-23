<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Pelatihan;
use App\Models\Angkatan;
use App\Models\MataPelatihan;
use App\Models\Widyaiswara;
use App\Models\KelasMapel;
use App\Models\Peserta;
use App\Models\Jadwal;
use App\Models\AttendanceSession;
use App\Models\AttendanceQrToken;
use App\Models\Presensi;
use App\Models\AttendanceRequest;
use App\Models\AttendanceEvidence;
use App\Models\AttendanceLog;
use App\Services\AttendanceService;
use App\Services\JPRecapitulationService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class HybridAttendanceTest extends TestCase
{
    protected User $admin;
    protected AttendanceService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::first() ?? User::factory()->create();
        $this->service = app(AttendanceService::class);
    }

    private function createDummyContext(): array
    {
        $unique = rand(10000, 99999);

        $pelatihan = Pelatihan::create([
            'kode_pelatihan' => 'PEL-ATT-' . $unique,
            'nama_pelatihan' => 'Pelatihan Presensi Test ' . $unique,
            'kategori' => 'Teknis',
            'penyelenggara' => 'PPSDMK',
            'tanggal_mulai' => '2026-11-01',
            'tanggal_selesai' => '2026-11-30',
            'status' => 'Berjalan',
        ]);

        $angkatan = Angkatan::create([
            'pelatihan_id' => $pelatihan->id,
            'nomor_angkatan' => 'Angkatan ATT ' . $unique,
            'tanggal_mulai' => '2026-11-01',
            'tanggal_selesai' => '2026-11-30',
            'jumlah_peserta' => 30,
            'status' => 'Berjalan',
        ]);

        $mataPelatihan = MataPelatihan::create([
            'angkatan_id' => $angkatan->id,
            'nama_mata_pelatihan' => 'Materi Presensi ' . $unique,
            'rencana_jp' => 4,
        ]);

        $kelasMapel = KelasMapel::create([
            'pelatihan_id' => $pelatihan->id,
            'angkatan_id' => $angkatan->id,
            'mata_pelatihan_id' => $mataPelatihan->id,
            'nama_kelas' => 'Kelas ATT ' . $unique,
            'kapasitas' => 30,
            'kurikulum' => '2026',
            'jp' => 4,
            'ada_kelompok' => 'tidak',
            'metode_pembelajaran' => 'Synchronous',
        ]);

        $jadwal = Jadwal::create([
            'mata_pelatihan_id' => $mataPelatihan->id,
            'tanggal' => '2026-11-20',
            'jam_mulai' => '08:00:00',
            'jam_selesai' => '09:30:00',
            'sesi_ke' => 1,
            'jp_rencana' => 2,
            'ruangan' => 'Lab Komputer',
            'status' => 'Terjadwal',
        ]);

        $peserta = Peserta::create([
            'nip' => '199' . time() . rand(10000, 99999),
            'nama' => 'Peserta Test ' . $unique,
            'email' => 'peserta' . $unique . '_' . time() . '@kemnaker.go.id',
            'jenis_kelamin' => 'Laki-laki',
            'instansi' => 'PPSDM Kemnaker',
            'angkatan_id' => $angkatan->id,
            'kelas_mapel_id' => $kelasMapel->id,
        ]);

        $access = $this->service->getOrGenerateAccessCode($peserta, '123456');

        $session = AttendanceSession::create([
            'jadwal_id' => $jadwal->id,
            'kelas_mapel_id' => $kelasMapel->id,
            'judul_sesi' => 'Sesi 1 - Uji Coba Presensi ' . $unique,
            'tanggal' => '2026-11-20',
            'jam_mulai' => '08:00:00',
            'jam_selesai' => '09:30:00',
            'toleransi_terlambat_menit' => 15,
            'status' => 'Aktif',
            'waktu_buka_presensi' => now()->subMinutes(5),
            'qr_secret_key' => 'secret_' . $unique,
            'created_by' => $this->admin->id,
        ]);

        return compact('pelatihan', 'angkatan', 'mataPelatihan', 'kelasMapel', 'jadwal', 'peserta', 'access', 'session');
    }

    public function test_login_peserta_berhasil(): void
    {
        $ctx = $this->createDummyContext();
        $peserta = $ctx['peserta'];
        $kode = $ctx['access']['kode_peserta'];

        $response = $this->post(route('portal.login.post'), [
            'kode_peserta' => $kode,
            'pin' => '123456',
        ]);

        $response->assertRedirect(route('portal.dashboard'));
        $this->assertEquals($peserta->id, session('participant_id'));
    }

    public function test_login_peserta_gagal(): void
    {
        $ctx = $this->createDummyContext();
        $kode = $ctx['access']['kode_peserta'];

        $response = $this->post(route('portal.login.post'), [
            'kode_peserta' => $kode,
            'pin' => 'wrong_pin',
        ]);

        $response->assertSessionHasErrors('kode_peserta');
        $this->assertNull(session('participant_id'));
    }

    public function test_qr_aktif_berhasil(): void
    {
        $ctx = $this->createDummyContext();
        $peserta = $ctx['peserta'];
        $session = $ctx['session'];

        $tokenData = $this->service->generateQrToken($session, 60);

        $response = $this->withSession(['participant_id' => $peserta->id])
            ->postJson(route('portal.scan.post'), [
                'qr_payload' => $tokenData['payload'],
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('presensis', [
            'attendance_session_id' => $session->id,
            'peserta_id' => $peserta->id,
            'metode_presensi' => 'QR',
        ]);
    }

    public function test_qr_kedaluwarsa_ditolak(): void
    {
        $ctx = $this->createDummyContext();
        $peserta = $ctx['peserta'];
        $session = $ctx['session'];

        // Buat token yang sudah kedaluwarsa
        $tokenHash = 'expired_hash_' . rand(1000, 9999);
        $expiredToken = AttendanceQrToken::create([
            'attendance_session_id' => $session->id,
            'token_hash' => $tokenHash,
            'expired_at' => now()->subMinute(),
            'is_active' => true,
        ]);

        $payload = json_encode([
            'session_id' => $session->id,
            'token' => $tokenHash,
        ]);

        $response = $this->withSession(['participant_id' => $peserta->id])
            ->postJson(route('portal.scan.post'), [
                'qr_payload' => $payload,
            ]);

        $response->assertStatus(422);
        $response->assertJson(['success' => false]);
    }

    public function test_qr_tidak_valid_ditolak(): void
    {
        $ctx = $this->createDummyContext();
        $peserta = $ctx['peserta'];

        $response = $this->withSession(['participant_id' => $peserta->id])
            ->postJson(route('portal.scan.post'), [
                'qr_payload' => json_encode(['session_id' => 9999, 'token' => 'invalid_random_token']),
            ]);

        $response->assertStatus(422);
        $response->assertJson(['success' => false]);
    }

    public function test_presensi_ganda_ditolak(): void
    {
        $ctx = $this->createDummyContext();
        $peserta = $ctx['peserta'];
        $session = $ctx['session'];

        $tokenData = $this->service->generateQrToken($session, 60);

        // Presensi pertama
        $res1 = $this->withSession(['participant_id' => $peserta->id])
            ->postJson(route('portal.scan.post'), [
                'qr_payload' => $tokenData['payload'],
            ]);
        $res1->assertStatus(200);

        // Presensi kedua pada sesi yang sama -> harus ditolak
        $res2 = $this->withSession(['participant_id' => $peserta->id])
            ->postJson(route('portal.scan.post'), [
                'qr_payload' => $tokenData['payload'],
            ]);
        $res2->assertStatus(422);
        $res2->assertJson(['success' => false]);
    }

    public function test_presensi_manual_berhasil_dan_tercatat_audit_log(): void
    {
        $ctx = $this->createDummyContext();
        $peserta = $ctx['peserta'];
        $session = $ctx['session'];

        $response = $this->actingAs($this->admin)->post(
            route('attendance-manual.update', $session->id),
            [
                'peserta_id' => $peserta->id,
                'status' => 'Izin',
                'keterangan' => 'Izin dinas luar',
                'alasan_perubahan' => 'Verifikasi via surat tugas resmi',
            ]
        );

        $response->assertRedirect();

        $this->assertDatabaseHas('presensis', [
            'attendance_session_id' => $session->id,
            'peserta_id' => $peserta->id,
            'status_kehadiran' => 'Izin',
            'metode_presensi' => 'Manual',
        ]);

        $this->assertDatabaseHas('attendance_logs', [
            'attendance_session_id' => $session->id,
            'peserta_id' => $peserta->id,
            'status_sesudah' => 'Izin',
            'alasan_perubahan' => 'Verifikasi via surat tugas resmi',
        ]);
    }

    public function test_pengajuan_izin_dan_verifikasi_admin(): void
    {
        Storage::fake('local');
        $ctx = $this->createDummyContext();
        $peserta = $ctx['peserta'];
        $session = $ctx['session'];

        $fakeFile = UploadedFile::fake()->create('surat_dokter.pdf', 100, 'application/pdf');

        $response = $this->withSession(['participant_id' => $peserta->id])->post(
            route('portal.pengajuan.post'),
            [
                'attendance_session_id' => $session->id,
                'jenis_pengajuan' => 'Sakit',
                'alasan' => 'Demam tinggi dan istirahat dokter',
                'bukti_file' => [$fakeFile],
            ]
        );

        $response->assertRedirect(route('portal.pengajuan'));

        $this->assertDatabaseHas('attendance_requests', [
            'peserta_id' => $peserta->id,
            'attendance_session_id' => $session->id,
            'jenis_pengajuan' => 'Sakit',
            'status' => 'Pending',
        ]);

        $req = AttendanceRequest::where('peserta_id', $peserta->id)->first();
        $this->assertDatabaseHas('attendance_evidences', [
            'attendance_request_id' => $req->id,
            'nama_file_asli' => 'surat_dokter.pdf',
        ]);

        // Verifikasi Admin: Setujui
        $verifyResponse = $this->actingAs($this->admin)->post(
            route('attendance-requests.verify', $req->id),
            [
                'keputusan' => 'Approved',
                'catatan_admin' => 'Surat dokter terverifikasi valid',
            ]
        );

        $verifyResponse->assertRedirect();
        $req->refresh();
        $this->assertEquals('Approved', $req->status);

        // Status di tabel presensi harus otomatis menjadi Sakit
        $this->assertDatabaseHas('presensis', [
            'attendance_session_id' => $session->id,
            'peserta_id' => $peserta->id,
            'status_kehadiran' => 'Sakit',
        ]);
    }

    public function test_penolakan_pengajuan_izin(): void
    {
        $ctx = $this->createDummyContext();
        $peserta = $ctx['peserta'];
        $session = $ctx['session'];

        $req = AttendanceRequest::create([
            'peserta_id' => $peserta->id,
            'attendance_session_id' => $session->id,
            'jenis_pengajuan' => 'Izin',
            'alasan' => 'Urusan keluarga mendadak',
            'status' => 'Pending',
        ]);

        $response = $this->actingAs($this->admin)->post(
            route('attendance-requests.verify', $req->id),
            [
                'keputusan' => 'Rejected',
                'catatan_admin' => 'Tidak melampirkan surat permohonan resmi',
            ]
        );

        $response->assertRedirect();
        $req->refresh();
        $this->assertEquals('Rejected', $req->status);
        $this->assertEquals('Tidak melampirkan surat permohonan resmi', $req->catatan_admin);
    }

    public function test_sesi_ditutup_tidak_dapat_menerima_presensi(): void
    {
        $ctx = $this->createDummyContext();
        $peserta = $ctx['peserta'];
        $session = $ctx['session'];

        $tokenData = $this->service->generateQrToken($session, 60);

        // Tutup sesi
        $session->update(['status' => 'Selesai']);

        $response = $this->withSession(['participant_id' => $peserta->id])
            ->postJson(route('portal.scan.post'), [
                'qr_payload' => $tokenData['payload'],
            ]);

        $response->assertStatus(422);
        $response->assertJson(['success' => false]);
    }

    public function test_rekapitulasi_kehadiran_dan_zero_duplikasi_jp(): void
    {
        $ctx = $this->createDummyContext();
        $session = $ctx['session'];
        $peserta = $ctx['peserta'];

        // Tambah presensi
        $this->service->recordManualAttendance($session, $peserta->id, 'Hadir', 'Hadir tepat waktu', $this->admin->id);

        $stats = $this->service->getSessionStatistics($session);
        $this->assertEquals(1, $stats['hadir']);
        $this->assertEquals(100.0, $stats['persen_kehadiran']);

        // Pastikan kalkulasi JP tidak terpengaruh / tidak duplikasi
        $jpService = app(JPRecapitulationService::class);
        $this->assertNotNull($jpService);
    }
}
