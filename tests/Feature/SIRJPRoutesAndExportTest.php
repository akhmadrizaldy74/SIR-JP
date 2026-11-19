<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\User;
use App\Models\Widyaiswara;
use App\Models\Narasumber;
use App\Models\Pelatihan;
use App\Models\Angkatan;
use App\Models\MataPelatihan;
use App\Models\PenugasanPengajar;
use App\Models\Jadwal;
use App\Models\RealisasiJadwal;
use App\Models\KonversiJp;

class SIRJPRoutesAndExportTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::where('email', 'penyelenggara@sir-jp.go.id')->first() ?? User::factory()->create([
            'role' => 'penyelenggara',
            'status' => 'Aktif',
        ]);
    }

    /**
     * Test autentikasi & proteksi middleware
     */
    public function test_unauthenticated_user_redirected_to_login(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_user_can_login_with_correct_credentials(): void
    {
        $response = $this->post('/login', [
            'login' => 'penyelenggara@sir-jp.go.id',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
    }

    public function test_user_cannot_login_with_invalid_password(): void
    {
        $response = $this->post('/login', [
            'login' => 'penyelenggara@sir-jp.go.id',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors('login');
        $this->assertGuest();
    }

    /**
     * Test Dashboard page
     */
    public function test_authenticated_user_can_access_dashboard(): void
    {
        $response = $this->actingAs($this->user)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('SIR-JP');
        $response->assertSee('Total JP Terealisasi');
    }

    /**
     * Test Master Widyaiswara CRUD
     */
    public function test_widyaiswara_crud(): void
    {
        // 1. Index
        $response = $this->actingAs($this->user)->get(route('widyaiswara.index'));
        $response->assertStatus(200);

        // 2. Create
        $response = $this->actingAs($this->user)->post(route('widyaiswara.store'), [
            'nik' => '1122334455667788',
            'nip' => '19880101 201201 1 009',
            'nama_lengkap' => 'WI Baru Test',
            'gelar_depan' => 'Dr.',
            'gelar_belakang' => 'M.Si.',
            'jabatan' => 'Widyaiswara Ahli Madya',
            'instansi' => 'PPSDM Test',
            'status' => 'Aktif',
        ]);
        $response->assertRedirect(route('widyaiswara.index'));
        $this->assertDatabaseHas('widyaiswaras', ['nik' => '1122334455667788']);

        $wi = Widyaiswara::where('nik', '1122334455667788')->first();

        // 3. Show
        $response = $this->actingAs($this->user)->get(route('widyaiswara.show', $wi));
        $response->assertStatus(200);

        // 4. Update
        $response = $this->actingAs($this->user)->put(route('widyaiswara.update', $wi), [
            'nik' => '1122334455667788',
            'nama_lengkap' => 'WI Baru Test Updated',
            'jabatan' => 'Widyaiswara Ahli Utama',
            'instansi' => 'PPSDM Updated',
            'status' => 'Aktif',
        ]);
        $response->assertRedirect(route('widyaiswara.index'));
        $this->assertDatabaseHas('widyaiswaras', ['nama_lengkap' => 'WI Baru Test Updated']);

        // 5. Delete (Soft delete)
        $response = $this->actingAs($this->user)->delete(route('widyaiswara.destroy', $wi));
        $response->assertRedirect(route('widyaiswara.index'));
        $this->assertSoftDeleted('widyaiswaras', ['id' => $wi->id]);
    }

    /**
     * Test Master Narasumber CRUD (Tabel terpisah)
     */
    public function test_narasumber_crud(): void
    {
        $response = $this->actingAs($this->user)->post(route('narasumber.store'), [
            'nik' => '7766554433221100',
            'nama_lengkap' => 'Narasumber Baru Test',
            'gelar_depan' => 'Prof.',
            'gelar_belakang' => 'Ph.D.',
            'instansi' => 'Institut Teknologi Bandung',
            'status' => 'Aktif',
        ]);
        $response->assertRedirect(route('narasumber.index'));
        $this->assertDatabaseHas('narasumbers', ['nik' => '7766554433221100']);

        $ns = Narasumber::where('nik', '7766554433221100')->first();

        $response = $this->actingAs($this->user)->get(route('narasumber.show', $ns));
        $response->assertStatus(200);
        $response->assertSee('Narasumber Baru Test');
    }

    /**
     * Test Pelatihan, Angkatan, Mata Pelatihan & Penugasan
     */
    public function test_pelatihan_angkatan_mata_pelatihan_flow(): void
    {
        // 1. Pelatihan
        $this->actingAs($this->user)->post(route('pelatihan.store'), [
            'kode_pelatihan' => 'PEL-TEST-99',
            'nama_pelatihan' => 'Pelatihan Uji Alur',
            'kategori' => 'Manajerial',
            'penyelenggara' => 'PPSDM',
            'tanggal_mulai' => '2026-06-01',
            'tanggal_selesai' => '2026-06-25',
            'status' => 'Berjalan',
        ])->assertRedirect(route('pelatihan.index'));

        $pel = Pelatihan::where('kode_pelatihan', 'PEL-TEST-99')->first();
        $this->assertNotNull($pel);

        // 2. Angkatan
        $this->actingAs($this->user)->post(route('angkatan.store'), [
            'pelatihan_id' => $pel->id,
            'nomor_angkatan' => 'Angkatan Khusus',
            'tanggal_mulai' => '2026-06-01',
            'tanggal_selesai' => '2026-06-25',
            'jumlah_peserta' => 30,
            'status' => 'Berjalan',
        ])->assertRedirect(route('angkatan.index'));

        $ang = Angkatan::where('pelatihan_id', $pel->id)->first();
        $this->assertNotNull($ang);

        // 3. Mata Pelatihan
        $this->actingAs($this->user)->post(route('mata-pelatihan.store'), [
            'angkatan_id' => $ang->id,
            'nama_mata_pelatihan' => 'Kepemimpinan Strategis',
            'rencana_jp' => 12.00,
        ])->assertRedirect(route('mata-pelatihan.index'));

        $mp = MataPelatihan::where('angkatan_id', $ang->id)->first();
        $this->assertNotNull($mp);

        // 4. Penugasan Widyaiswara
        $wi = Widyaiswara::first();
        $this->actingAs($this->user)->post(route('penugasan.store'), [
            'mata_pelatihan_id' => $mp->id,
            'jenis_pengajar' => 'Widyaiswara',
            'widyaiswara_id' => $wi->id,
            'peran' => 'Pengampu Utama',
        ])->assertRedirect(route('penugasan.index'));

        $this->assertDatabaseHas('penugasan_pengajars', [
            'mata_pelatihan_id' => $mp->id,
            'widyaiswara_id' => $wi->id,
        ]);
    }

    /**
     * Test Rekapitulasi Views
     */
    public function test_rekapitulasi_views(): void
    {
        $wi = Widyaiswara::first();

        // Index
        $response = $this->actingAs($this->user)->get(route('rekapitulasi.index', ['bulan' => 11, 'tahun' => 2026]));
        $response->assertStatus(200);
        $response->assertSee('Rekapitulasi Jam Pelajaran');

        // Detail Individu
        $response = $this->actingAs($this->user)->get(route('rekapitulasi.detail', ['id' => $wi->id, 'bulan' => 11, 'tahun' => 2026]));
        $response->assertStatus(200);
        $response->assertSee('BAGIAN A');
        $response->assertSee('BAGIAN B');

        // Tahunan
        $response = $this->actingAs($this->user)->get(route('rekapitulasi.tahunan', ['tahun' => 2026]));
        $response->assertStatus(200);
    }

    /**
     * Test Excel Export Endpoints
     */
    public function test_excel_export_endpoints(): void
    {
        $wi = Widyaiswara::first();

        // Individu
        $response = $this->actingAs($this->user)->get(route('laporan.export.excel_individu', ['id' => $wi->id, 'bulan' => 11, 'tahun' => 2026]));
        $response->assertStatus(200);
        $this->assertTrue(str_contains($response->headers->get('content-disposition') ?? '', '.xlsx'));

        // Semua
        $response = $this->actingAs($this->user)->get(route('laporan.export.excel_semua', ['bulan' => 11, 'tahun' => 2026]));
        $response->assertStatus(200);
        $this->assertTrue(str_contains($response->headers->get('content-disposition') ?? '', '.xlsx'));

        // Tahunan
        $response = $this->actingAs($this->user)->get(route('laporan.export.excel_tahunan', ['tahun' => 2026]));
        $response->assertStatus(200);
        $this->assertTrue(str_contains($response->headers->get('content-disposition') ?? '', '.xlsx'));

        // Pelatihan
        $response = $this->actingAs($this->user)->get(route('laporan.export.excel_pelatihan'));
        $response->assertStatus(200);
        $this->assertTrue(str_contains($response->headers->get('content-disposition') ?? '', '.xlsx'));
    }

    /**
     * Test PDF Export Endpoints
     */
    public function test_pdf_export_endpoints(): void
    {
        $wi = Widyaiswara::first();
        $ns = Narasumber::first();

        // PDF Individu WI
        $response = $this->actingAs($this->user)->get(route('laporan.export.pdf_individu', ['id' => $wi->id, 'bulan' => 11, 'tahun' => 2026]));
        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('content-type'));

        // PDF Semua WI
        $response = $this->actingAs($this->user)->get(route('laporan.export.pdf_semua', ['bulan' => 11, 'tahun' => 2026]));
        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('content-type'));

        // PDF Tahunan
        $response = $this->actingAs($this->user)->get(route('laporan.export.pdf_tahunan', ['tahun' => 2026]));
        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('content-type'));

        // PDF Narasumber
        $response = $this->actingAs($this->user)->get(route('laporan.export.pdf_narasumber', ['id' => $ns->id, 'bulan' => 11, 'tahun' => 2026]));
        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('content-type'));
    }

    /**
     * Test Pengaturan & Profile Updates
     */
    public function test_pengaturan_and_profile_updates(): void
    {
        // 1. Update Pengaturan Instansi
        $this->actingAs($this->user)->put(route('pengaturan.update.umum'), [
            'instansi_nama' => 'Pusat Pengembangan SDM Unggul',
            'instansi_alamat' => 'Jl. Kalibata Raya No. 10',
            'pimpinan_nama' => 'Prof. Dr. Hendra, M.Si',
            'pimpinan_jabatan' => 'Kepala Pusat',
            'pimpinan_kota' => 'Jakarta Selatan',
            'minimum_jp_bulanan' => 32,
        ])->assertSessionHas('success');

        $this->assertDatabaseHas('pengaturans', [
            'key' => 'instansi_nama',
            'value' => 'Pusat Pengembangan SDM Unggul',
        ]);

        // 2. Update Profile
        $this->actingAs($this->user)->put(route('profile.update'), [
            'name' => 'Penyelenggara Updated',
            'email' => 'penyelenggara@sir-jp.go.id',
            'username' => 'penyelenggara',
            'phone' => '089999888877',
        ])->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'name' => 'Penyelenggara Updated',
            'phone' => '089999888877',
        ]);
    }
}
