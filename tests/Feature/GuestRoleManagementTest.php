<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Peserta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuestRoleManagementTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_newly_registered_user_defaults_to_guest_role(): void
    {
        $uniqueNik = '320101' . rand(1000000000, 9999999999);
        $uniqueEmail = 'guest_' . time() . '_' . rand(100, 999) . '@test.com';

        $response = $this->post('/register', [
            'name' => 'Calon Pengguna Guest',
            'nik' => $uniqueNik,
            'email' => $uniqueEmail,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();

        $user = User::where('email', $uniqueEmail)->first();
        $this->assertNotNull($user);
        $this->assertEquals('guest', strtolower($user->role));
        $this->assertTrue($user->isGuest());
        $this->assertFalse($user->isAdministrator());
        $this->assertFalse($user->isPenyelenggara());
        $this->assertFalse($user->isPengajar());
        $this->assertFalse($user->isPeserta());
    }

    public function test_guest_is_redirected_to_guest_dashboard_when_accessing_protected_routes(): void
    {
        $guest = User::factory()->create([
            'role' => 'guest',
            'roles' => ['Guest'],
        ]);

        // Akses dashboard umum -> dialihkan ke /guest
        $response = $this->actingAs($guest)->get('/dashboard');
        $response->assertRedirect(route('guest.dashboard'));

        // Akses menu internal users -> dialihkan ke /guest dengan peringatan
        $response = $this->actingAs($guest)->get('/users');
        $response->assertRedirect(route('guest.dashboard'));
        $response->assertSessionHas('warning');

        // Akses menu kurikulum / pelatihan -> dialihkan ke /guest
        $response = $this->actingAs($guest)->get('/pelatihan');
        $response->assertRedirect(route('guest.dashboard'));
    }

    public function test_guest_can_access_guest_dashboard_and_profile(): void
    {
        $guest = User::factory()->create([
            'name' => 'Budi Tamu Baru',
            'role' => 'guest',
            'roles' => ['Guest'],
        ]);

        $response = $this->actingAs($guest)->get(route('guest.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Budi Tamu Baru');
        $response->assertSee('Status: Pending Role Assignment');
        $response->assertSee('Guest (Tamu)');

        // Profil saya tetap dapat diakses
        $response = $this->actingAs($guest)->get(route('profile.index'));
        $response->assertStatus(200);
    }

    public function test_admin_sees_guest_pending_alert_and_can_filter_guest(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'roles' => ['Administrator', 'Penyelenggara'],
        ]);

        $guest = User::factory()->create([
            'name' => 'Pengguna Menunggu Verifikasi',
            'role' => 'guest',
            'roles' => ['Guest'],
        ]);

        $response = $this->actingAs($admin)->get(route('users.index'));
        $response->assertStatus(200);
        $response->assertSee('Perhatian Administrator: Terdapat');
        $response->assertSee('Pengguna Menunggu Verifikasi');
        $response->assertSee('Guest (Perlu Diset)');

        // Filter role guest
        $filterResponse = $this->actingAs($admin)->get(route('users.index', ['role' => 'guest']));
        $filterResponse->assertStatus(200);
        $filterResponse->assertSee('Pengguna Menunggu Verifikasi');
    }

    public function test_admin_can_assign_roles_to_guest_and_user_immediately_gains_access(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'roles' => ['Administrator'],
        ]);

        $guest = User::factory()->create([
            'name' => 'Rina Staff Diklat',
            'email' => 'rina_' . time() . '@test.com',
            'role' => 'guest',
            'roles' => ['Guest'],
        ]);

        $this->assertTrue($guest->isGuest());

        // Admin menetapkan peran Penyelenggara & Administrator
        $updateResponse = $this->actingAs($admin)->put(route('users.update', $guest->id), [
            'name' => 'Rina Staff Diklat',
            'email' => $guest->email,
            'roles' => ['Penyelenggara', 'Administrator'],
            'bidang' => ['Pelatihan Kepemimpinan Struktural'],
            'status' => 'Aktif',
        ]);

        $updateResponse->assertRedirect(route('users.index'));

        $guest->refresh();
        $this->assertFalse($guest->isGuest());
        $this->assertTrue($guest->isPenyelenggara());
        $this->assertEquals('penyelenggara', $guest->role);

        // Setelah peran disetujui, jika membuka /guest akan otomatis dialihkan ke /dashboard
        $guestResponse = $this->actingAs($guest)->get(route('guest.dashboard'));
        $guestResponse->assertRedirect(route('dashboard'));

        // Dan sekarang bisa membuka /dashboard secara normal
        $dashResponse = $this->actingAs($guest)->get(route('dashboard'));
        $dashResponse->assertStatus(200);
    }

    public function test_admin_can_assign_peserta_role_to_guest_and_peserta_profile_created(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'roles' => ['Administrator'],
        ]);

        $guest = User::factory()->create([
            'name' => 'Agus Calon Peserta',
            'email' => 'agus_peserta_' . time() . '@test.com',
            'role' => 'guest',
            'roles' => ['Guest'],
        ]);

        // Admin menetapkan peran Peserta
        $this->actingAs($admin)->put(route('users.update', $guest->id), [
            'name' => 'Agus Calon Peserta',
            'email' => $guest->email,
            'roles' => ['Peserta'],
            'status' => 'Aktif',
        ]);

        $guest->refresh();
        $this->assertFalse($guest->isGuest());
        $this->assertTrue($guest->isPeserta());

        // Pastikan model Peserta otomatis tersinkronisasi
        $this->assertDatabaseHas('pesertas', [
            'email' => $guest->email,
            'nama' => 'Agus Calon Peserta',
        ]);

        // Peserta mengakses /guest dialihkan ke portal peserta
        $redirectResponse = $this->actingAs($guest)->get(route('guest.dashboard'));
        $redirectResponse->assertRedirect(route('portal.dashboard'));
    }
}
