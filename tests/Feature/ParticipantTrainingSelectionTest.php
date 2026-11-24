<?php

namespace Tests\Feature;

use App\Models\Angkatan;
use App\Models\AttendanceSession;
use App\Models\Jadwal;
use App\Models\KelasMapel;
use App\Models\MataPelatihan;
use App\Models\Pelatihan;
use App\Models\Peserta;
use App\Models\User;
use Tests\TestCase;

class ParticipantTrainingSelectionTest extends TestCase
{
    public function test_participant_without_angkatan_sees_selection_view_and_cannot_see_other_sessions()
    {
        $unique = rand(10000, 99999);
        $user = User::factory()->create([
            'role' => 'peserta',
            'roles' => ['Peserta'],
        ]);

        $peserta = $user->getOrCreatePesertaProfile();
        $this->assertNull($peserta->angkatan_id);

        $response = $this->actingAs($user)
            ->withSession(['participant_id' => $peserta->id])
            ->get(route('portal.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Menunggu Penetapan Angkatan');
        $response->assertSee('Memiliki Kode Token / Akses Pelatihan?');
        $response->assertDontSee('Pengantar Kerja Ahli Muda A.1');
    }

    public function test_participant_can_select_active_angkatan_and_dashboard_updates()
    {
        $unique = rand(10000, 99999);
        $user = User::factory()->create([
            'role' => 'peserta',
            'roles' => ['Peserta'],
        ]);
        $peserta = $user->getOrCreatePesertaProfile();

        $pelatihan = Pelatihan::create([
            'kode_pelatihan' => 'PEL-SEL-' . $unique,
            'nama_pelatihan' => 'Pelatihan Khusus ' . $unique,
            'kategori' => 'Teknis',
            'penyelenggara' => 'PPSDMK',
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->addDays(10)->toDateString(),
            'status' => 'Berjalan',
        ]);

        $angkatan = Angkatan::create([
            'pelatihan_id' => $pelatihan->id,
            'nomor_angkatan' => 'Angkatan SEL ' . $unique,
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->addDays(5)->toDateString(),
            'status' => 'Berjalan',
        ]);

        $mapel = MataPelatihan::create([
            'angkatan_id' => $angkatan->id,
            'nama_mata_pelatihan' => 'Materi Uji ' . $unique,
            'rencana_jp' => 2,
        ]);

        $kelas = KelasMapel::create([
            'pelatihan_id' => $pelatihan->id,
            'angkatan_id' => $angkatan->id,
            'mata_pelatihan_id' => $mapel->id,
            'nama_kelas' => 'Kelas SEL ' . $unique,
            'kapasitas' => 20,
            'kurikulum' => '2026',
            'jp' => 2,
            'ada_kelompok' => 'tidak',
            'metode_pembelajaran' => 'Synchronous',
        ]);

        $postResponse = $this->actingAs($user)
            ->withSession(['participant_id' => $peserta->id])
            ->post(route('portal.pilih-pelatihan'), [
                'angkatan_id' => $angkatan->id,
            ]);

        $postResponse->assertRedirect(route('portal.dashboard'));
        $postResponse->assertSessionHas('success');

        $peserta->refresh();
        $this->assertEquals($angkatan->id, $peserta->angkatan_id);

        $dashResponse = $this->actingAs($user)
            ->withSession(['participant_id' => $peserta->id])
            ->get(route('portal.dashboard'));

        $dashResponse->assertStatus(200);
        $dashResponse->assertSee('Pelatihan Khusus ' . $unique);
        $dashResponse->assertSee('Angkatan SEL ' . $unique);
        $dashResponse->assertDontSee('Menunggu Penetapan Angkatan');
    }

    public function test_invalid_token_is_rejected_with_error_alert()
    {
        $user = User::factory()->create([
            'role' => 'peserta',
            'roles' => ['Peserta'],
        ]);
        $peserta = $user->getOrCreatePesertaProfile();

        $postResponse = $this->actingAs($user)
            ->withSession(['participant_id' => $peserta->id])
            ->post(route('portal.pilih-pelatihan'), [
                'kode_token' => 'TOKEN_PALSU_TIDAK_ADA',
            ]);

        $postResponse->assertRedirect(route('portal.dashboard'));
        $postResponse->assertSessionHas('error');
    }
}
