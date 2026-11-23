<?php

namespace Tests\Feature;

use App\Models\Angkatan;
use App\Models\Pelatihan;
use App\Models\Peserta;
use App\Models\Tugas;
use App\Models\PengumpulanTugas;
use App\Services\AttendanceService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ParticipantTugasTest extends TestCase
{
    protected Peserta $peserta;
    protected Pelatihan $pelatihan;
    protected Angkatan $angkatan;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');

        $unique = rand(10000, 99999);

        $this->pelatihan = Pelatihan::create([
            'kode_pelatihan' => 'PEL-TGS-' . $unique,
            'nama_pelatihan' => 'Pengantar Kerja Ahli Muda ' . $unique,
            'kategori' => 'Teknis',
            'penyelenggara' => 'PPSDMK',
            'tanggal_mulai' => '2026-11-23',
            'tanggal_selesai' => '2026-12-04',
            'status' => 'Berjalan',
        ]);

        $this->angkatan = Angkatan::create([
            'pelatihan_id' => $this->pelatihan->id,
            'nomor_angkatan' => 'Angkatan TGS ' . $unique,
            'tanggal_mulai' => '2026-11-23',
            'tanggal_selesai' => '2026-12-04',
            'jumlah_peserta' => 30,
            'status' => 'Berjalan',
        ]);

        $this->peserta = Peserta::create([
            'nip' => '199' . time() . rand(1000, 9999),
            'nama' => 'Faizal Haqqi, S.I.P., M.M.',
            'email' => 'peserta_tugas' . $unique . '@kemnaker.go.id',
            'jenis_kelamin' => 'Laki-laki',
            'instansi' => 'PPSDM Kemnaker',
            'angkatan_id' => $this->angkatan->id,
        ]);

        app(AttendanceService::class)->getOrGenerateAccessCode($this->peserta, '123456');
    }

    public function test_unauthenticated_user_redirected_to_portal_login()
    {
        $response = $this->get(route('portal.tugas.index'));
        $response->assertRedirect(route('portal.login'));
    }

    public function test_peserta_can_view_tugas_list()
    {
        Tugas::create([
            'judul' => 'Contoh Tugas ' . rand(100, 999),
            'deskripsi' => 'Deskripsi Contoh Tugas',
            'kategori' => 'individu',
            'penyelenggara' => 'PPSDM Ketenagakerjaan',
            'tanggal_mulai' => now()->startOfDay(),
            'tanggal_selesai' => now()->endOfDay(),
            'nama_mata_pelatihan' => 'Overview Kebijakan Pelatihan',
        ]);

        $response = $this->withSession(['participant_id' => $this->peserta->id])
            ->get(route('portal.tugas.index', ['kategori' => 'individu']));

        $response->assertStatus(200);
        $response->assertSee('TUGAS');
        $response->assertSee('Overview Kebijakan Pelatihan');
        $response->assertSee('PPSDM Ketenagakerjaan');
        $response->assertSee('* Belum di upload');
    }

    public function test_peserta_can_upload_and_download_tugas()
    {
        $tugas = Tugas::create([
            'judul' => 'Tugas Praktik Mandiri ' . rand(100, 999),
            'deskripsi' => 'Deskripsi Praktik Mandiri',
            'kategori' => 'individu',
            'penyelenggara' => 'PPSDM Ketenagakerjaan',
            'tanggal_mulai' => now()->startOfDay(),
            'tanggal_selesai' => now()->endOfDay(),
            'nama_mata_pelatihan' => 'Overview Kebijakan Pelatihan',
        ]);

        $fakePdf = UploadedFile::fake()->create('certificate-test.pdf', 500, 'application/pdf');

        $response = $this->withSession(['participant_id' => $this->peserta->id])
            ->post(route('portal.tugas.upload', $tugas->id), [
                'file_jawaban' => $fakePdf,
                'catatan' => 'Catatan pengerjaan tugas oleh peserta.',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('pengumpulan_tugas', [
            'tugas_id' => $tugas->id,
            'peserta_id' => $this->peserta->id,
            'nama_file_asli' => 'certificate-test.pdf',
            'status' => 'dikumpulkan',
        ]);

        // Verifikasi download tugas
        $downloadResponse = $this->withSession(['participant_id' => $this->peserta->id])
            ->get(route('portal.tugas.download', $tugas->id));

        $downloadResponse->assertStatus(200);
    }
}
