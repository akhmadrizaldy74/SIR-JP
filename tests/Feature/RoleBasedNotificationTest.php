<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Peserta;
use App\Models\AttendanceRequest;
use App\Models\AttendanceSession;
use App\Models\KelasMapel;
use App\Models\Pelatihan;
use App\Models\MataPelatihan;
use App\Models\Tugas;
use App\Models\PengumpulanTugas;
use App\Services\NotificationService;
use Tests\TestCase;

class RoleBasedNotificationTest extends TestCase
{
    protected NotificationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(NotificationService::class);
    }

    public function test_admin_receives_guest_and_request_notifications(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'roles' => ['Administrator'],
        ]);

        $guest = User::factory()->create([
            'name' => 'Calon Guest Baru',
            'role' => 'guest',
            'roles' => ['Guest'],
        ]);

        $notifs = $this->service->getNotificationsForUser($admin);
        $this->assertNotEmpty($notifs);

        $guestNotif = collect($notifs)->firstWhere('id', 'guest_' . $guest->id);
        $this->assertNotNull($guestNotif);
        $this->assertStringContainsString('Calon Guest Baru', $guestNotif['message']);
        $this->assertEquals(route('users.index', ['role' => 'guest']), $guestNotif['url']);

        // Test pada view navbar
        $response = $this->actingAs($admin)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Calon Guest Baru');
        $response->assertSee('Akun Guest Baru Terdaftar');
    }

    public function test_pengajar_receives_task_and_schedule_notifications(): void
    {
        $pengajar = User::factory()->create([
            'name' => 'Widyaiswara Senior',
            'role' => 'widyaiswara',
            'roles' => ['Pengajar'],
        ]);

        $notifs = $this->service->getNotificationsForUser($pengajar);
        $this->assertIsArray($notifs);

        $response = $this->actingAs($pengajar)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Notifikasi');
    }

    public function test_guest_receives_guest_status_notification(): void
    {
        $guest = User::factory()->create([
            'name' => 'User Guest Pengunjung',
            'role' => 'guest',
            'roles' => ['Guest'],
        ]);

        $notifs = $this->service->getNotificationsForUser($guest);
        $this->assertNotEmpty($notifs);
        $this->assertEquals('guest_welcome', $notifs[0]['id']);
        $this->assertStringContainsString('Administrator sedang meninjau', $notifs[0]['message']);
    }

    public function test_user_can_mark_notifications_as_read(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'roles' => ['Administrator'],
        ]);

        User::factory()->create([
            'role' => 'guest',
            'roles' => ['Guest'],
        ]);

        $unreadBefore = $this->service->getUnreadCount($admin);
        $this->assertGreaterThan(0, $unreadBefore);

        // Tandai semua telah dibaca
        $response = $this->actingAs($admin)->post(route('notifications.mark-all-read'));
        $response->assertRedirect();

        $unreadAfter = $this->service->getUnreadCount($admin);
        $this->assertEquals(0, $unreadAfter);
    }

    public function test_single_notification_click_redirects_and_marks_as_read(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'roles' => ['Administrator'],
        ]);

        $guest = User::factory()->create([
            'role' => 'guest',
            'roles' => ['Guest'],
        ]);

        $targetUrl = route('users.index', ['role' => 'guest']);
        $response = $this->actingAs($admin)->get(route('notifications.read', [
            'id' => 'guest_' . $guest->id,
            'redirect' => $targetUrl,
        ]));

        $response->assertRedirect($targetUrl);
        $this->assertTrue(in_array('guest_' . $guest->id, session('read_notifications', [])));
    }
}
