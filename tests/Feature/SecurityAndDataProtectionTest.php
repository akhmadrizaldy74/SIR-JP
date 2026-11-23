<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Peserta;
use App\Models\Widyaiswara;
use App\Models\Narasumber;
use App\Models\Tugas;
use App\Models\PengumpulanTugas;
use App\Models\AttendanceEvidence;
use App\Services\SecurityMaskingService;
use Illuminate\Support\Facades\Storage;

class SecurityAndDataProtectionTest extends TestCase
{
    public function test_security_headers_are_present_on_responses(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);

        // Verifikasi HTTP Security Headers
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-XSS-Protection', '1; mode=block');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    public function test_authenticated_routes_have_anti_cache_headers(): void
    {
        $user = User::where('role', 'admin')->first() ?? User::factory()->create([
            'role' => 'admin',
            'status' => 'Aktif',
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));
        $response->assertStatus(200);
        $response->assertHeader('Cache-Control');
        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
    }

    public function test_sensitive_pii_masking_service_and_model_accessors(): void
    {
        // 1. NIK Masking
        $this->assertEquals('317101******0001', SecurityMaskingService::maskNik('3171011234560001'));
        $this->assertEquals('-', SecurityMaskingService::maskNik(null));

        // 2. NIP Masking (18 digit -> 6 depan + 8 bintang + 4 belakang)
        $this->assertEquals('198501********0001', SecurityMaskingService::maskNip('198501012010120001'));
        $this->assertEquals('-', SecurityMaskingService::maskNip(null));

        // 3. Phone Masking
        $this->assertEquals('0812****789', SecurityMaskingService::maskPhone('08123456789'));

        // 4. Email Masking
        $this->assertEquals('ad***@domain.com', SecurityMaskingService::maskEmail('admin@domain.com'));

        // 5. Model Accessor Tests
        $peserta = new Peserta([
            'nip' => '199001012015011001',
            'email' => 'peserta@kclc.go.id',
        ]);
        $this->assertEquals('199001********1001', $peserta->masked_nip);
        $this->assertEquals('pe*****@kclc.go.id', $peserta->masked_email);

        $wi = new Widyaiswara([
            'nik' => '3201011234560002',
            'nip' => '197501012000031001',
            'nomor_telepon' => '081298765432',
        ]);
        $this->assertEquals('320101******0002', $wi->masked_nik);
        $this->assertEquals('197501********1001', $wi->masked_nip);
        $this->assertEquals('0812*****432', $wi->masked_phone);
    }

    public function test_unauthenticated_user_cannot_download_attendance_evidence(): void
    {
        // Mencoba akses berkas bukti tanpa login
        $response = $this->get('/attendance-evidences/9999');
        $response->assertRedirect(route('login'));
    }

    public function test_participant_cannot_access_or_download_other_participants_submission(): void
    {
        $peserta1 = Peserta::first() ?? Peserta::create([
            'nama' => 'Peserta A',
            'nip' => '199901012022011001',
        ]);
        $peserta2 = Peserta::where('id', '!=', $peserta1->id)->first() ?? Peserta::create([
            'nama' => 'Peserta B',
            'nip' => '199901012022011002',
        ]);

        $tugas = Tugas::first() ?? Tugas::create([
            'judul' => 'Tugas Uji IDOR',
            'kategori' => 'individu',
            'tanggal_mulai' => now(),
            'tanggal_selesai' => now()->addDays(2),
        ]);

        Storage::fake('local');
        $fakePath = 'tugas_submissions/' . $tugas->id . '/test_jawaban.pdf';
        Storage::put($fakePath, 'dummy content');

        // Pengumpulan milik Peserta 1
        PengumpulanTugas::updateOrCreate([
            'tugas_id' => $tugas->id,
            'peserta_id' => $peserta1->id,
        ], [
            'file_jawaban' => $fakePath,
            'nama_file_asli' => 'jawaban_peserta_1.pdf',
            'waktu_upload' => now(),
            'status' => 'dikumpulkan',
        ]);

        // Peserta 2 mencoba mengunduh tugas Peserta 1 -> harus 404/Denied (anti-IDOR)
        $response = $this->withSession(['participant_id' => $peserta2->id])
            ->get(route('portal.tugas.download', $tugas->id));

        $response->assertStatus(404);
    }
}
