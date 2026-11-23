<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\LogAktivitas;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            if (Auth::user()->role === 'peserta') {
                return redirect()->route('portal.dashboard');
            }
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
            'login.required' => 'NIK, email, atau username wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $input = trim($credentials['login']);
        if (filter_var($input, FILTER_VALIDATE_EMAIL)) {
            $loginField = 'email';
        } elseif (is_numeric($input) && strlen($input) >= 10) {
            $loginField = 'nik';
        } else {
            $loginField = 'username';
        }

        $attempt = Auth::attempt([
            $loginField => $input,
            'password' => $credentials['password'],
            'status' => 'Aktif',
        ], $request->boolean('remember'));

        // Fallback: If login was entered as username but matches NIK
        if (!$attempt && $loginField === 'username') {
            $attempt = Auth::attempt([
                'nik' => $input,
                'password' => $credentials['password'],
                'status' => 'Aktif',
            ], $request->boolean('remember'));
            if ($attempt) {
                $loginField = 'nik';
            }
        }

        if ($attempt) {
            $request->session()->regenerate();
            $user = Auth::user();

            LogAktivitas::catat('Autentikasi', 'User Login Berhasil', 'Login menggunakan ' . $loginField . ': ' . $input);

            // Jika peran adalah Peserta, sambungkan ke sesi Peserta dan arahkan ke Dashboard Peserta
            if ($user->role === 'peserta') {
                $peserta = \App\Models\Peserta::where('nip', $user->nik)
                    ->orWhere('email', $user->email)
                    ->first();

                if (!$peserta) {
                    $peserta = \App\Models\Peserta::create([
                        'nip' => $user->nik,
                        'nama' => $user->name,
                        'email' => $user->email,
                        'jenis_kelamin' => 'Laki-laki',
                        'instansi' => 'PPSDM Kemnaker',
                    ]);
                }

                session(['participant_id' => $peserta->id]);

                return redirect()->route('portal.dashboard')
                    ->with('success', 'Selamat datang di Portal Diklat, ' . $user->name . '!');
            }

            return redirect()->intended(route('dashboard'))
                ->with('success', 'Selamat datang kembali, ' . Auth::user()->name . '!');
        }

        LogAktivitas::catat('Autentikasi', 'Percobaan Login Gagal', 'Gagal login untuk: ' . $input);

        return back()->withErrors([
            'login' => 'NIK, email/username atau kata sandi tidak cocok, atau akun Anda nonaktif.',
        ])->onlyInput('login');
    }

    public function showRegisterForm()
    {
        if (Auth::check()) {
            if (Auth::user()->role === 'peserta') {
                return redirect()->route('portal.dashboard');
            }
            return redirect()->route('dashboard');
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nik' => ['required', 'string', 'size:16', 'unique:users,nik'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'username' => ['nullable', 'string', 'max:50', 'unique:users,username'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'nik.required' => 'NIK (Nomor Induk Kependudukan) 16 digit wajib diisi.',
            'nik.size' => 'NIK harus tepat 16 digit angka.',
            'nik.unique' => 'NIK ini sudah terdaftar sebagai Penyelenggara.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.unique' => 'Alamat email ini sudah terdaftar.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $username = $validated['username'] ?? explode('@', $validated['email'])[0];
        $baseUsername = $username;
        $counter = 1;
        while (User::where('username', $username)->exists()) {
            $username = $baseUsername . $counter;
            $counter++;
        }

        $user = User::create([
            'name' => $validated['name'],
            'nik' => $validated['nik'],
            'email' => $validated['email'],
            'username' => $username,
            'phone' => $validated['phone'] ?? null,
            'role' => 'penyelenggara',
            'status' => 'Aktif',
            'password' => Hash::make($validated['password']),
        ]);

        Auth::login($user);

        LogAktivitas::catat('Autentikasi', 'Registrasi Penyelenggara Baru', 'Registrasi akun baru via NIK: ' . $user->nik);

        return redirect()->route('dashboard')
            ->with('success', 'Selamat datang! Akun Penyelenggara Anda berhasil didaftarkan.');
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
