<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Peserta;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Tampilkan daftar seluruh pengguna sistem & peran (Role).
     */
    public function index(Request $request)
    {
        $search = trim($request->get('search', ''));
        $role = $request->get('role', '');
        $status = $request->get('status', '');

        $query = User::query();

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }

        if (!empty($role)) {
            $query->where('role', $role);
        }

        if (!empty($status)) {
            $query->where('status', $status);
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        // Hitung total per role
        $countAll = User::count();
        $countPenyelenggara = User::whereIn('role', ['penyelenggara', 'admin'])->count();
        $countPeserta = User::where('role', 'peserta')->count();
        $countWidyaiswara = User::where('role', 'widyaiswara')->count();

        return view('users.index', compact(
            'users',
            'search',
            'role',
            'status',
            'countAll',
            'countPenyelenggara',
            'countPeserta',
            'countWidyaiswara'
        ));
    }

    /**
     * Simpan pengguna baru dengan peran (Role) yang dipilih admin.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'nik'      => ['required', 'string', 'size:16', 'unique:users,nik'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'role'     => ['required', 'string', 'in:penyelenggara,admin,peserta,widyaiswara'],
            'status'   => ['required', 'string', 'in:Aktif,Nonaktif'],
            'password' => ['required', 'string', 'min:6'],
        ], [
            'nik.size' => 'NIK harus tepat 16 digit.',
            'nik.unique' => 'NIK sudah digunakan oleh akun lain.',
            'email.unique' => 'Alamat email sudah terdaftar.',
            'role.in' => 'Peran (Role) tidak valid.',
        ]);

        $username = explode('@', $validated['email'])[0];
        $baseUsername = $username;
        $counter = 1;
        while (User::where('username', $username)->exists()) {
            $username = $baseUsername . $counter;
            $counter++;
        }

        $user = User::create([
            'name'     => $validated['name'],
            'nik'      => $validated['nik'],
            'email'    => $validated['email'],
            'username' => $username,
            'role'     => $validated['role'],
            'status'   => $validated['status'],
            'password' => Hash::make($validated['password']),
        ]);

        // Jika peran peserta, buatkan profil peserta otomatis jika belum ada
        if ($user->role === 'peserta') {
            Peserta::firstOrCreate(
                ['nip' => $user->nik],
                [
                    'nama' => $user->name,
                    'email' => $user->email,
                    'jenis_kelamin' => 'Laki-laki',
                    'instansi' => 'PPSDM Kemnaker',
                ]
            );
        }

        LogAktivitas::catat('Manajemen Pengguna', 'Tambah Pengguna Baru', "Menambahkan akun baru: {$user->name} ({$user->nik}) dengan Role: {$user->role}");

        return back()->with('success', "Akun untuk {$user->name} berhasil dibuat dengan Role: {$user->role}!");
    }

    /**
     * Perbarui data atau ubah peran (Role) pengguna.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'nik'      => ['required', 'string', 'size:16', Rule::unique('users')->ignore($user->id)],
            'email'    => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role'     => ['required', 'string', 'in:penyelenggara,admin,peserta,widyaiswara'],
            'status'   => ['required', 'string', 'in:Aktif,Nonaktif'],
            'password' => ['nullable', 'string', 'min:6'],
        ], [
            'nik.size' => 'NIK harus tepat 16 digit.',
            'role.in' => 'Peran (Role) tidak valid.',
        ]);

        $user->name = $validated['name'];
        $user->nik = $validated['nik'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];
        $user->status = $validated['status'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        // Jika role diubah menjadi peserta, pastikan data Peserta terhubung
        if ($user->role === 'peserta') {
            Peserta::firstOrCreate(
                ['nip' => $user->nik],
                [
                    'nama' => $user->name,
                    'email' => $user->email,
                    'jenis_kelamin' => 'Laki-laki',
                    'instansi' => 'PPSDM Kemnaker',
                ]
            );
        }

        LogAktivitas::catat('Manajemen Pengguna', 'Ubah Pengguna/Role', "Memperbarui akun {$user->name} menjadi Role: {$user->role}, Status: {$user->status}");

        return back()->with('success', "Data akun {$user->name} berhasil diperbarui (Role: {$user->role})!");
    }

    /**
     * Hapus pengguna sistem.
     */
    public function destroy(User $user)
    {
        if (auth()->id() === $user->id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $nama = $user->name;
        $user->delete();

        LogAktivitas::catat('Manajemen Pengguna', 'Hapus Pengguna', "Menghapus akun {$nama}");

        return back()->with('success', "Akun {$nama} berhasil dihapus dari sistem.");
    }
}
