<?php

namespace App\Http\Controllers;

use App\Services\AttendanceService;
use App\Models\Peserta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class ParticipantAuthController extends Controller
{
    protected AttendanceService $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    public function showLoginForm()
    {
        if (session()->has('participant_id')) {
            return redirect()->route('portal.dashboard');
        }

        return view('portal.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'kode_peserta' => 'required|string',
            'pin' => 'required|string|min:4',
        ], [
            'kode_peserta.required' => 'Kode peserta wajib diisi.',
            'pin.required' => 'PIN akses wajib diisi.',
            'pin.min' => 'PIN akses minimal 4 digit.',
        ]);

        $throttleKey = 'participant-login:' . Str::lower($request->ip()) . '|' . Str::lower($request->kode_peserta);

        // Rate limiting: maksimal 5 percobaan per menit
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'kode_peserta' => "Terlalu banyak percobaan login gagal. Silakan coba lagi dalam {$seconds} detik.",
            ])->onlyInput('kode_peserta');
        }

        $peserta = $this->attendanceService->authenticateParticipant(
            $request->kode_peserta,
            $request->pin
        );

        if (!$peserta) {
            RateLimiter::hit($throttleKey, 60);
            return back()->withErrors([
                'kode_peserta' => 'Kode peserta atau PIN akses yang Anda masukkan tidak cocok / tidak aktif.',
            ])->onlyInput('kode_peserta');
        }

        // Login berhasil, bersihkan rate limiter
        RateLimiter::clear($throttleKey);

        // Regenerasi session untuk mencegah session fixation
        $request->session()->regenerate();
        $request->session()->put('participant_id', $peserta->id);

        $intended = $request->session()->pull('url.intended_participant', route('portal.dashboard'));
        return redirect()->to($intended)->with('success', 'Selamat datang, ' . $peserta->nama . '!');
    }

    public function logout(Request $request)
    {
        $request->session()->forget('participant_id');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('portal.login')->with('info', 'Anda telah berhasil keluar dari Portal Presensi Peserta.');
    }
}
