<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Peserta;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserRoleManagementTest extends TestCase
{
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::where('role', 'admin')->first() 
            ?? User::where('role', 'penyelenggara')->first() 
            ?? User::create([
                'name' => 'Admin Test',
                'nik' => '1234567890123456',
                'email' => 'admin_test@sir-jp.go.id',
                'username' => 'admin_test',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'status' => 'Aktif',
            ]);
    }

    public function test_unauthenticated_user_cannot_access_user_management(): void
    {
        $response = $this->get(route('users.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_admin_can_view_user_management_and_sidebar_links(): void
    {
        $response = $this->actingAs($this->admin)->get(route('users.index'));

        $response->assertOk();
        $response->assertSeeText('Manajemen Pengguna', false);
        $response->assertSeeText('Kelola Pengguna', false);
        $response->assertSeeText('LMS DIKLAT', false);
        $response->assertSeeText('REKAPITULASI JP', false);
        $response->assertSeeText('MANAJEMEN SISTEM', false);
    }

    public function test_admin_can_create_user_with_peserta_role_and_it_syncs_peserta(): void
    {
        $unique = rand(100000, 999999);
        $nik = '320101' . $unique . '0001';
        $email = "peserta_{$unique}@example.com";

        $response = $this->actingAs($this->admin)->post(route('users.store'), [
            'name' => 'Peserta Baru ' . $unique,
            'nik' => $nik,
            'email' => $email,
            'role' => 'peserta',
            'status' => 'Aktif',
            'password' => 'secret123',
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'email' => $email,
            'role' => 'peserta',
            'status' => 'Aktif',
        ]);

        // Pastikan record Peserta otomatis terbentuk
        $this->assertDatabaseHas('pesertas', [
            'nip' => $nik,
            'email' => $email,
        ]);
    }

    public function test_user_with_peserta_role_logs_in_and_is_redirected_to_participant_portal(): void
    {
        $unique = rand(100000, 999999);
        $nik = '320102' . $unique . '0002';
        $email = "peserta_login_{$unique}@example.com";

        $user = User::create([
            'name' => 'Peserta Login ' . $unique,
            'nik' => $nik,
            'email' => $email,
            'username' => 'user_peserta_' . $unique,
            'role' => 'peserta',
            'status' => 'Aktif',
            'password' => Hash::make('password123'),
        ]);

        // Login via form /login
        $response = $this->post(route('login.post'), [
            'login' => $email,
            'password' => 'password123',
        ]);

        // Harus diarahkan ke dashboard portal peserta
        $response->assertRedirect(route('portal.dashboard'));
        $response->assertSessionHas('participant_id');

        // Buka dashboard peserta
        $portalResponse = $this->actingAs($user)->get(route('portal.dashboard'));
        $portalResponse->assertOk();

        // Verifikasi sidebar khusus peserta tampil
        $portalResponse->assertSee('KEMNAKER CORPU');
        $portalResponse->assertSee('Lebih Cerdas Lebih Unggul');
        $portalResponse->assertSee('Dashboard');
        $portalResponse->assertSee('Presensi');
        $portalResponse->assertSee('Tugas');
    }

    public function test_participant_is_redirected_away_from_admin_dashboard(): void
    {
        $unique = rand(100000, 999999);
        $nik = '320103' . $unique . '0003';
        $email = "peserta_block_{$unique}@example.com";

        $user = User::create([
            'name' => 'Peserta Block ' . $unique,
            'nik' => $nik,
            'email' => $email,
            'username' => 'peserta_block_' . $unique,
            'role' => 'peserta',
            'status' => 'Aktif',
            'password' => Hash::make('password123'),
        ]);

        // Peserta mencoba membuka /dashboard admin
        $response = $this->actingAs($user)->get(route('dashboard'));

        // Harus diarahkan ke portal peserta
        $response->assertRedirect(route('portal.dashboard'));
    }

    public function test_admin_can_update_user_role_and_status(): void
    {
        $unique = rand(100000, 999999);
        $nik = '320104' . $unique . '0004';
        $email = "user_change_{$unique}@example.com";

        $user = User::create([
            'name' => 'User Ubah Role ' . $unique,
            'nik' => $nik,
            'email' => $email,
            'username' => 'user_ubah_' . $unique,
            'role' => 'penyelenggara',
            'status' => 'Aktif',
            'password' => Hash::make('password123'),
        ]);

        // Admin mengubah role menjadi peserta dan status menjadi Nonaktif
        $response = $this->actingAs($this->admin)->put(route('users.update', $user->id), [
            'name' => 'User Ubah Role ' . $unique,
            'nik' => $nik,
            'email' => $email,
            'role' => 'peserta',
            'status' => 'Nonaktif',
        ]);

        $response->assertSessionHas('success');

        $user->refresh();
        $this->assertEquals('peserta', $user->role);
        $this->assertEquals('Nonaktif', $user->status);

        // Record peserta harus ada
        $this->assertDatabaseHas('pesertas', [
            'nip' => $nik,
        ]);
    }

    public function test_admin_can_view_edit_users_page_matching_kclc_design(): void
    {
        $unique = rand(100000, 999999);
        $user = User::create([
            'name' => 'PPSDM Ketenagakerjaan ' . $unique,
            'nik' => '320105' . $unique . '0005',
            'email' => "ppsdm_{$unique}@gmail.com",
            'username' => 'admin_' . $unique,
            'role' => 'penyelenggara',
            'status' => 'Aktif',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->actingAs($this->admin)->get(route('users.edit', $user->id));

        $response->assertOk();
        $response->assertSee('Edit Users');
        $response->assertSee('Kembali');
        $response->assertSee('Roles');
        $response->assertSee('Administrator');
        $response->assertSee('Peserta Diklat');
        $response->assertSee('Penyelenggara');
        $response->assertSee('Bidang');
        $response->assertSee('Pelatihan Kepemimpinan Struktural');
        $response->assertSee('Pelatihan Dasar CPNS/PPPK');
        $response->assertSee('Update');
        $response->assertSee('Batal');
    }

    public function test_admin_can_update_user_with_multiple_roles_and_bidang(): void
    {
        $unique = rand(100000, 999999);
        $user = User::create([
            'name' => 'User Multi ' . $unique,
            'nik' => '320106' . $unique . '0006',
            'email' => "multi_{$unique}@gmail.com",
            'username' => 'multi_' . $unique,
            'role' => 'penyelenggara',
            'status' => 'Aktif',
            'password' => Hash::make('password123'),
        ]);

        $roles = ['Administrator', 'Pengajar', 'Peserta Diklat', 'Penyelenggara'];
        $bidang = ['Pelatihan Kepemimpinan Struktural', 'Pelatihan Dasar CPNS/PPPK', 'Pelatihan SDM Aparatur'];

        $response = $this->actingAs($this->admin)->put(route('users.update', $user->id), [
            'name' => 'PPSDM Ketenagakerjaan Updated',
            'nik' => $user->nik,
            'email' => $user->email,
            'username' => 'admin_upd_' . $unique,
            'roles' => $roles,
            'bidang' => $bidang,
            'status' => 'Aktif',
        ]);

        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('success');

        $user->refresh();
        $this->assertEquals('PPSDM Ketenagakerjaan Updated', $user->name);
        $this->assertEquals($roles, $user->roles);
        $this->assertEquals($bidang, $user->bidang);
    }
}
