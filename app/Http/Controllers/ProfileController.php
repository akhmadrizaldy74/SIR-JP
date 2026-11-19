<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Models\LogAktivitas;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('profile.index', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'username' => ['required', 'string', 'alpha_dash', 'max:50', 'unique:users,username,' . $user->id],
            'phone' => ['nullable', 'string', 'max:30'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email ini sudah digunakan oleh akun lain.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan.',
        ]);

        $user->update($validated);

        LogAktivitas::catat('Pengguna', 'Memperbarui Profil', 'Penyelenggara memperbarui data profil akun.');

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function password()
    {
        return view('profile.password');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(6)],
        ], [
            'current_password.required' => 'Kata sandi saat ini wajib diisi.',
            'current_password.current_password' => 'Kata sandi saat ini salah.',
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'password.min' => 'Kata sandi minimal 6 karakter.',
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        LogAktivitas::catat('Pengguna', 'Ganti Kata Sandi', 'Penyelenggara berhasil mengganti kata sandi.');

        return back()->with('success', 'Kata sandi berhasil diperbarui.');
    }
}
