<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JenisPelatihan;
use App\Models\LogAktivitas;

class JenisPelatihanController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $perPage = (int) $request->get('per_page', 10);

        $query = JenisPelatihan::query();

        if ($search) {
            $query->where('nama_jenis', 'like', "%{$search}%");
        }

        $jenisPelatihans = $query->orderBy('id', 'asc')->paginate($perPage)->withQueryString();

        return view('kurikulum.jenis_pelatihan.index', compact('jenisPelatihans', 'search', 'perPage'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_jenis' => ['required', 'string', 'max:255', 'unique:jenis_pelatihans,nama_jenis'],
            'status' => ['nullable'],
        ], [
            'nama_jenis.required' => 'Nama jenis pelatihan wajib diisi.',
            'nama_jenis.unique' => 'Jenis pelatihan ini sudah terdaftar.',
        ]);

        $status = $request->has('status') ? (bool) $request->status : true;

        $jp = JenisPelatihan::create([
            'nama_jenis' => $validated['nama_jenis'],
            'status' => $status,
        ]);

        LogAktivitas::catat('Kurikulum', 'Tambah Jenis Pelatihan', 'Menambahkan Jenis Pelatihan: ' . $jp->nama_jenis);

        return redirect()->route('jenis-pelatihan.index')->with('success', 'Jenis Pelatihan berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $jp = JenisPelatihan::findOrFail($id);

        $validated = $request->validate([
            'nama_jenis' => ['required', 'string', 'max:255', 'unique:jenis_pelatihans,nama_jenis,' . $id],
            'status' => ['nullable'],
        ], [
            'nama_jenis.required' => 'Nama jenis pelatihan wajib diisi.',
        ]);

        $status = $request->has('status') ? (bool) $request->status : true;

        $jp->update([
            'nama_jenis' => $validated['nama_jenis'],
            'status' => $status,
        ]);

        LogAktivitas::catat('Kurikulum', 'Ubah Jenis Pelatihan', 'Memperbarui Jenis Pelatihan: ' . $jp->nama_jenis);

        return redirect()->route('jenis-pelatihan.index')->with('success', 'Jenis Pelatihan berhasil diperbarui.');
    }

    public function toggle($id)
    {
        $jp = JenisPelatihan::findOrFail($id);
        $jp->status = !$jp->status;
        $jp->save();

        LogAktivitas::catat('Kurikulum', 'Toggle Status Jenis Pelatihan', 'Mengubah status ' . $jp->nama_jenis . ' menjadi ' . ($jp->status ? 'Aktif' : 'Non-Aktif'));

        return response()->json([
            'success' => true,
            'status' => $jp->status,
            'message' => 'Status jenis pelatihan berhasil diperbarui.'
        ]);
    }

    public function destroy($id)
    {
        $jp = JenisPelatihan::findOrFail($id);
        $nama = $jp->nama_jenis;
        $jp->delete();

        LogAktivitas::catat('Kurikulum', 'Hapus Jenis Pelatihan', 'Menghapus Jenis Pelatihan: ' . $nama);

        return redirect()->route('jenis-pelatihan.index')->with('success', 'Jenis Pelatihan ' . $nama . ' berhasil dihapus.');
    }
}
