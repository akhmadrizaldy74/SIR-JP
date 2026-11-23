<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\JenisPelatihan;
use App\Models\TahunKurikulum;
use App\Models\Pelatihan;
use App\Models\MataPelatihan;
use Illuminate\Foundation\Testing\RefreshDatabase;

class KurikulumManagementTest extends TestCase
{
    protected function authenticateAdmin()
    {
        $user = User::where('role', 'admin')->first() ?? User::create([
            'name' => 'Admin Kurikulum Test',
            'nik' => '9999888877776666',
            'email' => 'adminkurikulum@test.com',
            'username' => 'adminkurikulum',
            'password' => bcrypt('password123'),
            'role' => 'admin',
            'roles' => ['Administrator', 'Penyelenggara'],
            'status' => 'Aktif',
        ]);
        $this->actingAs($user);
        return $user;
    }

    public function test_jenis_pelatihan_crud_and_delete()
    {
        $this->authenticateAdmin();
        $suffix = uniqid();
        $name = 'Pelatihan Uji ' . $suffix;
        $nameUpdated = 'Pelatihan Uji Update ' . $suffix;

        // 1. Index
        $response = $this->get(route('jenis-pelatihan.index'));
        $response->assertStatus(200);
        $response->assertSee('JENIS PELATIHAN');

        // 2. Store
        $storeResponse = $this->post(route('jenis-pelatihan.store'), [
            'nama_jenis' => $name,
            'status' => '1',
        ]);
        $storeResponse->assertRedirect(route('jenis-pelatihan.index'));
        $this->assertDatabaseHas('jenis_pelatihans', ['nama_jenis' => $name]);

        $jp = JenisPelatihan::where('nama_jenis', $name)->first();

        // 3. Toggle
        $toggleResponse = $this->patch(route('jenis-pelatihan.toggle', $jp->id));
        $toggleResponse->assertStatus(200);
        $toggleResponse->assertJson(['success' => true]);

        // 4. Update
        $updateResponse = $this->put(route('jenis-pelatihan.update', $jp->id), [
            'nama_jenis' => $nameUpdated,
            'status' => '1',
        ]);
        $updateResponse->assertRedirect(route('jenis-pelatihan.index'));
        $this->assertDatabaseHas('jenis_pelatihans', ['nama_jenis' => $nameUpdated]);

        // 5. Delete (Tong Sampah)
        $deleteResponse = $this->delete(route('jenis-pelatihan.destroy', $jp->id));
        $deleteResponse->assertRedirect(route('jenis-pelatihan.index'));
        $this->assertSoftDeleted('jenis_pelatihans', ['id' => $jp->id]);
    }

    public function test_tahun_kurikulum_crud_and_delete()
    {
        $this->authenticateAdmin();
        $year = '30' . rand(10, 99);
        $yearUpdated = '31' . rand(10, 99);

        // 1. Index
        $response = $this->get(route('tahun-kurikulum.index'));
        $response->assertStatus(200);
        $response->assertSee('TAHUN KURIKULUM');

        // 2. Store
        $storeResponse = $this->post(route('tahun-kurikulum.store'), [
            'tahun' => $year,
        ]);
        $storeResponse->assertRedirect(route('tahun-kurikulum.index'));
        $this->assertDatabaseHas('tahun_kurikulums', ['tahun' => $year]);

        $tk = TahunKurikulum::where('tahun', $year)->first();

        // 3. Update
        $updateResponse = $this->put(route('tahun-kurikulum.update', $tk->id), [
            'tahun' => $yearUpdated,
        ]);
        $updateResponse->assertRedirect(route('tahun-kurikulum.index'));
        $this->assertDatabaseHas('tahun_kurikulums', ['tahun' => $yearUpdated]);

        // 4. Delete (Tong Sampah)
        $deleteResponse = $this->delete(route('tahun-kurikulum.destroy', $tk->id));
        $deleteResponse->assertRedirect(route('tahun-kurikulum.index'));
        $this->assertSoftDeleted('tahun_kurikulums', ['id' => $tk->id]);
    }

    public function test_pelatihan_and_mata_pelatihan_delete_feature()
    {
        $this->authenticateAdmin();

        $uniq = uniqid();
        $pelatihan = Pelatihan::create([
            'kode_pelatihan' => 'TEST-KUR-' . $uniq,
            'nama_pelatihan' => 'Pelatihan Kurikulum Uji ' . $uniq,
            'kategori' => 'Pelatihan Teknis',
            'penyelenggara' => 'PPSDM',
            'tanggal_mulai' => now(),
            'tanggal_selesai' => now()->addDays(5),
            'status' => 'Berjalan',
        ]);

        $mapel = MataPelatihan::create([
            'pelatihan_id' => $pelatihan->id,
            'nama_mata_pelatihan' => 'Mata Diklat Uji',
            'rencana_jp' => 4,
            'tahun_kurikulum' => '2026',
        ]);

        // Toggle status pelatihan
        $togglePel = $this->patch(route('pelatihan.toggle', $pelatihan->id));
        $togglePel->assertStatus(200);
        $togglePel->assertJson(['success' => true]);

        // Delete mapel (Tong sampah)
        $delMapel = $this->delete(route('mata-pelatihan.destroy', $mapel->id));
        $delMapel->assertRedirect(route('mata-pelatihan.index'));
        $this->assertSoftDeleted('mata_pelatihans', ['id' => $mapel->id]);

        // Delete pelatihan (Tong sampah)
        $delPel = $this->delete(route('pelatihan.destroy', $pelatihan->id));
        $delPel->assertRedirect(route('pelatihan.index'));
        $this->assertSoftDeleted('pelatihans', ['id' => $pelatihan->id]);
    }
}
