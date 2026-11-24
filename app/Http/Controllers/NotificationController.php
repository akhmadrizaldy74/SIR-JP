<?php

namespace App\Http\Controllers;

use App\Services\NotificationService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Endpoint API JSON Live Feed untuk real-time polling background & on-click refresh.
     */
    public function live(Request $request)
    {
        $user = auth()->user();
        if (!$user && !session()->has('participant_id')) {
            return response()->json([
                'unread_count' => 0,
                'count' => 0,
                'notifications' => [],
                'html' => '',
            ], 401);
        }

        $notifications = $this->notificationService->getNotificationsForUser($user);
        $unreadCount = $this->notificationService->getUnreadCount($user);

        $html = view('partials.notifications_list', compact('notifications'))->render();

        return response()->json([
            'unread_count' => $unreadCount,
            'count' => count($notifications),
            'notifications' => $notifications,
            'html' => $html,
        ]);
    }

    /**
     * Tandai semua notifikasi telah dibaca.
     */
    public function markAllRead(Request $request)
    {
        session(['notifications_read_at' => now()]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Semua notifikasi telah ditandai sebagai dibaca.');
    }

    /**
     * Tandai satu notifikasi telah dibaca lalu arahkan ke tujuannya.
     */
    public function markSingleRead(Request $request, string $id)
    {
        $readIds = session('read_notifications', []);
        if (!in_array($id, $readIds)) {
            $readIds[] = $id;
            session(['read_notifications' => $readIds]);
        }

        $targetUrl = $request->query('redirect', route('dashboard'));
        return redirect($targetUrl);
    }
}
