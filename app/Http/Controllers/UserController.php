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
    public static array $availableRoles = [
        ['Administrator', 'Pengajar', 'Evaluasi', 'Kurikulum'],
        ['Kontributor', 'Pimpinan', 'Penyelenggara', 'Penguji'],
        ['Tim Kurasi', 'Peserta', 'Guest'],
    ];

    public static array $availableBidang = [
        [
            'Pelatihan Kepemimpinan Struktural',
            'Pelatihan Bidang Penempatan Tenaga Kerja dan Perluasan Kesempatan Kerja',
            'Pelatihan Bidang Hubungan Industrial & Jamsos',
            'Testing Instruktur',
        ],
        [
            'Pelatihan Dasar CPNS/PPPK',
            'Pelatihan Pengawasan Ketenagakerjaan',
            'Pelatihan SDM Aparatur',
        ],
        [
            'Pelatihan Bidang Pelatihan Vokasi dan Produktivitas',
            'Pelatihan Pengujian Keselamatan dan Kesehatan Kerja',
            'Pelatihan Teknis',
        ],
    ];

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
            $query->where(function ($q) use ($role) {
                $q->where('role', $role)
                  ->orWhereJsonContains('roles', $role)
                  ->orWhereJsonContains('roles', ucfirst($role));
            });
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
     * Tampilkan form pembuatan pengguna baru.
     */
    public function create()
    {
        $user = new User();
        $availableRoles = self::$availableRoles;
        $availableBidang = self::$availableBidang;
        $isEdit = false;

        return view('users.edit', compact('user', 'availableRoles', 'availableBidang', 'isEdit'));
    }

    /**
     * Tampilkan form edit pengguna dan roles/bidang sesuai sketsa.
     */
    public function edit(User $user)
    {
        $availableRoles = self::$availableRoles;
        $availableBidang = self::$availableBidang;
        $isEdit = true;

        return view('users.edit', compact('user', 'availableRoles', 'availableBidang', 'isEdit'));
    }

    /**
     * Simpan pengguna baru dengan peran (Role) yang dipilih admin.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'nik'      => ['nullable', 'string', 'max:20', Rule::unique('users', 'nik')],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'username' => ['nullable', 'string', 'max:50', 'unique:users,username'],
            'role'     => ['nullable', 'string'],
            'roles'    => ['nullable', 'array'],
            'bidang'   => ['nullable', 'array'],
            'status'   => ['nullable', 'string', 'in:Aktif,Nonaktif'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        $rolesInput = $request->input('roles', []);
        $primaryRole = $request->input('role');

        if (!empty($rolesInput)) {
            if (in_array('Administrator', $rolesInput) || in_array('Penyelenggara', $rolesInput)) {
                $primaryRole = 'penyelenggara';
            } elseif (in_array('Pengajar', $rolesInput)) {
                $primaryRole = 'widyaiswara';
            } elseif (in_array('Peserta', $rolesInput) || in_array('Peserta Diklat', $rolesInput)) {
                $primaryRole = 'peserta';
            }
        }
        if (!$primaryRole) {
            $primaryRole = 'penyelenggara';
        }

        $nik = $request->input('nik');
        if (empty($nik)) {
            $nik = '10' . substr(str_pad((string) preg_replace('/\D/', '', md5($validated['email'])), 14, '0'), 0, 14);
        }

        $username = $validated['username'] ?? explode('@', $validated['email'])[0];
        $baseUsername = $username;
        $counter = 1;
        while (User::where('username', $username)->exists()) {
            $username = $baseUsername . $counter;
            $counter++;
        }

        $user = User::create([
            'name'     => $validated['name'],
            'nik'      => $nik,
            'email'    => $validated['email'],
            'username' => $username,
            'role'     => $primaryRole,
            'roles'    => !empty($rolesInput) ? $rolesInput : [$primaryRole],
            'bidang'   => $request->input('bidang', []),
            'status'   => $validated['status'] ?? 'Aktif',
            'password' => Hash::make($validated['password']),
        ]);

        // Jika peran peserta, buatkan profil peserta otomatis jika belum ada
        if ($user->role === 'peserta' || $user->isPeserta()) {
            Peserta::firstOrCreate(
                ['email' => $user->email],
                [
                    'nip' => $user->nik,
                    'nama' => $user->name,
                    'jenis_kelamin' => 'Laki-laki',
                    'instansi' => 'PPSDM Kemnaker',
                ]
            );
        }

        LogAktivitas::catat('Manajemen Pengguna', 'Tambah Pengguna Baru', "Menambahkan akun baru: {$user->name} ({$user->nik}) dengan Role: {$user->role}");

        return redirect()->route('users.index')->with('success', "Akun untuk {$user->name} berhasil dibuat!");
    }

    /**
     * Perbarui data atau ubah peran (Role) & bidang pengguna.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'nik'      => ['nullable', 'string', 'max:20', Rule::unique('users', 'nik')->ignore($user->id)],
            'email'    => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'username' => ['nullable', 'string', 'max:50', Rule::unique('users', 'username')->ignore($user->id)],
            'role'     => ['nullable', 'string'],
            'roles'    => ['nullable', 'array'],
            'bidang'   => ['nullable', 'array'],
            'status'   => ['nullable', 'string', 'in:Aktif,Nonaktif'],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        $user->name = $validated['name'];
        if (!empty($validated['nik'])) {
            $user->nik = $validated['nik'];
        }
        $user->email = $validated['email'];
        if (!empty($validated['username'])) {
            $user->username = $validated['username'];
        }

        if ($request->has('roles')) {
            $rolesInput = $request->input('roles', []);
            $user->roles = $rolesInput;
            if (in_array('Administrator', $rolesInput) || in_array('Penyelenggara', $rolesInput)) {
                $user->role = 'penyelenggara';
            } elseif (in_array('Pengajar', $rolesInput)) {
                $user->role = 'widyaiswara';
            } elseif (in_array('Peserta', $rolesInput) || in_array('Peserta Diklat', $rolesInput)) {
                $user->role = 'peserta';
            }
        } elseif ($request->filled('role')) {
            $user->role = $validated['role'];
            $user->roles = [$validated['role']];
        }

        if ($request->has('bidang')) {
            $user->bidang = $request->input('bidang', []);
        }

        if ($request->filled('status')) {
            $user->status = $validated['status'];
        }

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        // Jika role diubah menjadi peserta, pastikan data Peserta terhubung
        if ($user->role === 'peserta' || $user->isPeserta()) {
            Peserta::firstOrCreate(
                ['email' => $user->email],
                [
                    'nip' => $user->nik ?: 'NIK-' . substr(md5($user->email), 0, 10),
                    'nama' => $user->name,
                    'jenis_kelamin' => 'Laki-laki',
                    'instansi' => 'PPSDM Kemnaker',
                ]
            );
        }

        LogAktivitas::catat('Manajemen Pengguna', 'Ubah Pengguna/Role', "Memperbarui akun {$user->name} (Role: {$user->role})");

        return redirect()->route('users.index')->with('success', "Data akun {$user->name} berhasil diperbarui!");
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
