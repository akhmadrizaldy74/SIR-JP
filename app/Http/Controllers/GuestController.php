<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GuestController extends Controller
{
    /**
     * Tampilkan halaman dashboard Guest (Menunggu Penetapan Hak Akses oleh Admin).
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Jika bukan guest lagi (sudah ditetapkan peran oleh administrator)
        if (!$user->isGuest()) {
            if ($user->isPeserta()) {
                return redirect()->route('portal.dashboard');
            }
            return redirect()->route('dashboard');
        }

        return view('guest.dashboard', compact('user'));
    }
}
