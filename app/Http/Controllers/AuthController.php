<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LogAktivitas;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'login.required' => 'Email atau username wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $loginField = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $attempt = Auth::attempt([
            $loginField => $credentials['login'],
            'password' => $credentials['password'],
            'status' => 'Aktif',
        ], $request->boolean('remember'));

        if ($attempt) {
            $request->session()->regenerate();

            LogAktivitas::catat('Autentikasi', 'User Login Berhasil', 'Login menggunakan ' . $loginField . ': ' . $credentials['login']);

            return redirect()->intended(route('dashboard'))
                ->with('success', 'Selamat datang kembali, ' . Auth::user()->name . '!');
        }

        LogAktivitas::catat('Autentikasi', 'Percobaan Login Gagal', 'Gagal login untuk: ' . $credentials['login']);

        return back()->withErrors([
            'login' => 'Email/username atau kata sandi tidak cocok, atau akun Anda nonaktif.',
        ])->onlyInput('login');
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            LogAktivitas::catat('Autentikasi', 'User Logout', 'User ' . Auth::user()->name . ' keluar dari aplikasi.');
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('info', 'Anda telah berhasil keluar dari sistem.');
    }
}
