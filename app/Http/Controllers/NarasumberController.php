<?php

namespace App\Http\Controllers;

use App\Models\Narasumber;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class NarasumberController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status');

        $query = Narasumber::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('instansi', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        $narasumbers = $query->orderBy('nama_lengkap', 'asc')->paginate(10)->withQueryString();

        return view('narasumber.index', compact('narasumbers', 'search', 'status'));
    }

    public function create()
    {
        return view('narasumber.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nik' => ['required', 'string', 'max:30', 'unique:narasumbers,nik'],
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'gelar_depan' => ['nullable', 'string', 'max:50'],
            'gelar_belakang' => ['nullable', 'string', 'max:50'],
            'instansi' => ['required', 'string', 'max:255'],
            'nomor_telepon' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'status' => ['required', Rule::in(['Aktif', 'Nonaktif'])],
            'keterangan' => ['nullable', 'string'],
        ], [
            'nik.required' => 'NIK wajib diisi.',
            'nik.unique' => 'NIK Narasumber sudah terdaftar.',
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'instansi.required' => 'Instansi narasumber wajib diisi.',
        ]);

        $ns = Narasumber::create($validated);

        LogAktivitas::catat('Narasumber', 'Tambah Data', 'Menambahkan Narasumber: ' . $ns->nama_gelar);

        return redirect()->route('narasumber.index')->with('success', 'Data Narasumber berhasil ditambahkan.');
    }

    public function show(Narasumber $narasumber)
    {
        $narasumber->load(['penugasans.mataPelatihan.angkatan.pelatihan']);
        return view('narasumber.show', compact('narasumber'));
    }

    public function edit(Narasumber $narasumber)
    {
        return view('narasumber.edit', compact('narasumber'));
    }

    public function update(Request $request, Narasumber $narasumber)
    {
        $validated = $request->validate([
            'nik' => ['required', 'string', 'max:30', Rule::unique('narasumbers', 'nik')->ignore($narasumber->id)],
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'gelar_depan' => ['nullable', 'string', 'max:50'],
            'gelar_belakang' => ['nullable', 'string', 'max:50'],
            'instansi' => ['required', 'string', 'max:255'],
            'nomor_telepon' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'status' => ['required', Rule::in(['Aktif', 'Nonaktif'])],
            'keterangan' => ['nullable', 'string'],
        ], [
            'nik.required' => 'NIK wajib diisi.',
            'nik.unique' => 'NIK sudah digunakan.',
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'instansi.required' => 'Instansi wajib diisi.',
        ]);

        $narasumber->update($validated);

        LogAktivitas::catat('Narasumber', 'Ubah Data', 'Memperbarui data Narasumber: ' . $narasumber->nama_gelar);

        return redirect()->route('narasumber.index')->with('success', 'Data Narasumber berhasil diperbarui.');
    }

    public function destroy(Narasumber $narasumber)
    {
        $nama = $narasumber->nama_gelar;
        $narasumber->delete();

        LogAktivitas::catat('Narasumber', 'Hapus Data', 'Menghapus data Narasumber: ' . $nama);

        return redirect()->route('narasumber.index')->with('success', 'Data Narasumber berhasil dihapus.');
    }
}
