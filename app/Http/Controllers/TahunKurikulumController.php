<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TahunKurikulum;
use App\Models\LogAktivitas;

class TahunKurikulumController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $perPage = (int) $request->get('per_page', 10);

        $query = TahunKurikulum::query();

        if ($search) {
            $query->where('tahun', 'like', "%{$search}%");
        }

        $tahunKurikulums = $query->orderBy('tahun', 'desc')->paginate($perPage)->withQueryString();

        return view('kurikulum.tahun_kurikulum.index', compact('tahunKurikulums', 'search', 'perPage'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tahun' => ['required', 'string', 'max:50', 'unique:tahun_kurikulums,tahun'],
        ], [
            'tahun.required' => 'Nama / tahun kurikulum wajib diisi.',
            'tahun.unique' => 'Tahun kurikulum ini sudah terdaftar.',
        ]);

        $tk = TahunKurikulum::create([
            'tahun' => $validated['tahun'],
            'status' => true,
        ]);

        LogAktivitas::catat('Kurikulum', 'Tambah Tahun Kurikulum', 'Menambahkan Tahun Kurikulum: ' . $tk->tahun);

        return redirect()->route('tahun-kurikulum.index')->with('success', 'Tahun Kurikulum berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $tk = TahunKurikulum::findOrFail($id);

        $validated = $request->validate([
            'tahun' => ['required', 'string', 'max:50', 'unique:tahun_kurikulums,tahun,' . $id],
        ], [
            'tahun.required' => 'Nama / tahun kurikulum wajib diisi.',
        ]);

        $tk->update([
            'tahun' => $validated['tahun'],
        ]);

        LogAktivitas::catat('Kurikulum', 'Ubah Tahun Kurikulum', 'Memperbarui Tahun Kurikulum: ' . $tk->tahun);

        return redirect()->route('tahun-kurikulum.index')->with('success', 'Tahun Kurikulum berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $tk = TahunKurikulum::findOrFail($id);
        $tahun = $tk->tahun;
        $tk->delete();

        LogAktivitas::catat('Kurikulum', 'Hapus Tahun Kurikulum', 'Menghapus Tahun Kurikulum: ' . $tahun);

        return redirect()->route('tahun-kurikulum.index')->with('success', 'Tahun Kurikulum ' . $tahun . ' berhasil dihapus.');
    }
}
