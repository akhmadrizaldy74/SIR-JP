<?php

namespace App\Http\Controllers;

use App\Models\Widyaiswara;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WidyaiswaraController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $jabatan = $request->get('jabatan');
        $status = $request->get('status');

        $query = Widyaiswara::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('instansi', 'like', "%{$search}%");
            });
        }

        if ($jabatan) {
            $query->where('jabatan', $jabatan);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $widyaiswaras = $query->orderBy('nama_lengkap', 'asc')->paginate(10)->withQueryString();

        $jabatanOptions = [
            'Widyaiswara Ahli Pertama',
            'Widyaiswara Ahli Muda',
            'Widyaiswara Ahli Madya',
            'Widyaiswara Ahli Utama',
        ];

        return view('widyaiswara.index', compact('widyaiswaras', 'jabatanOptions', 'search', 'jabatan', 'status'));
    }

    public function create()
    {
        $jabatanOptions = [
            'Widyaiswara Ahli Pertama',
            'Widyaiswara Ahli Muda',
            'Widyaiswara Ahli Madya',
            'Widyaiswara Ahli Utama',
        ];
        return view('widyaiswara.create', compact('jabatanOptions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nik' => ['required', 'string', 'max:30', 'unique:widyaiswaras,nik'],
            'nip' => ['nullable', 'string', 'max:30', 'unique:widyaiswaras,nip'],
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'gelar_depan' => ['nullable', 'string', 'max:50'],
            'gelar_belakang' => ['nullable', 'string', 'max:50'],
            'jabatan' => ['required', Rule::in([
                'Widyaiswara Ahli Pertama',
                'Widyaiswara Ahli Muda',
                'Widyaiswara Ahli Madya',
                'Widyaiswara Ahli Utama'
            ])],
            'instansi' => ['required', 'string', 'max:255'],
            'nomor_telepon' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'status' => ['required', Rule::in(['Aktif', 'Nonaktif'])],
            'keterangan' => ['nullable', 'string'],
        ], [
            'nik.required' => 'NIK wajib diisi.',
            'nik.unique' => 'NIK sudah terdaftar dalam sistem.',
            'nip.unique' => 'NIP sudah terdaftar dalam sistem.',
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'jabatan.required' => 'Jabatan Widyaiswara wajib dipilih.',
            'instansi.required' => 'Instansi wajib diisi.',
        ]);

        $wi = Widyaiswara::create($validated);

        LogAktivitas::catat('Widyaiswara', 'Tambah Data', 'Menambahkan Widyaiswara: ' . $wi->nama_gelar);

        return redirect()->route('widyaiswara.index')->with('success', 'Data Widyaiswara berhasil ditambahkan.');
    }

    public function show(Widyaiswara $widyaiswara)
    {
        $widyaiswara->load(['penugasans.mataPelatihan.angkatan.pelatihan', 'konversiJps']);
        return view('widyaiswara.show', compact('widyaiswara'));
    }

    public function edit(Widyaiswara $widyaiswara)
    {
        $jabatanOptions = [
            'Widyaiswara Ahli Pertama',
            'Widyaiswara Ahli Muda',
            'Widyaiswara Ahli Madya',
            'Widyaiswara Ahli Utama',
        ];
        return view('widyaiswara.edit', compact('widyaiswara', 'jabatanOptions'));
    }

    public function update(Request $request, Widyaiswara $widyaiswara)
    {
        $validated = $request->validate([
            'nik' => ['required', 'string', 'max:30', Rule::unique('widyaiswaras', 'nik')->ignore($widyaiswara->id)],
            'nip' => ['nullable', 'string', 'max:30', Rule::unique('widyaiswaras', 'nip')->ignore($widyaiswara->id)],
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'gelar_depan' => ['nullable', 'string', 'max:50'],
            'gelar_belakang' => ['nullable', 'string', 'max:50'],
            'jabatan' => ['required', Rule::in([
                'Widyaiswara Ahli Pertama',
                'Widyaiswara Ahli Muda',
                'Widyaiswara Ahli Madya',
                'Widyaiswara Ahli Utama'
            ])],
            'instansi' => ['required', 'string', 'max:255'],
            'nomor_telepon' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'status' => ['required', Rule::in(['Aktif', 'Nonaktif'])],
            'keterangan' => ['nullable', 'string'],
        ], [
            'nik.required' => 'NIK wajib diisi.',
            'nik.unique' => 'NIK sudah digunakan.',
            'nip.unique' => 'NIP sudah digunakan.',
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'jabatan.required' => 'Jabatan Widyaiswara wajib dipilih.',
            'instansi.required' => 'Instansi wajib diisi.',
        ]);

        $widyaiswara->update($validated);

        LogAktivitas::catat('Widyaiswara', 'Ubah Data', 'Memperbarui data Widyaiswara: ' . $widyaiswara->nama_gelar);

        return redirect()->route('widyaiswara.index')->with('success', 'Data Widyaiswara berhasil diperbarui.');
    }

    public function destroy(Widyaiswara $widyaiswara)
    {
        $nama = $widyaiswara->nama_gelar;
        $widyaiswara->delete();

        LogAktivitas::catat('Widyaiswara', 'Hapus Data', 'Menghapus data Widyaiswara: ' . $nama);

        return redirect()->route('widyaiswara.index')->with('success', 'Data Widyaiswara berhasil dihapus.');
    }
}
