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
