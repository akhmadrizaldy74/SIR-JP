<?php

namespace App\Http\Controllers;

use App\Models\MataPelatihan;
use App\Models\Angkatan;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;

class MataPelatihanController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $angkatanId = $request->get('angkatan_id');

        $query = MataPelatihan::with(['angkatan.pelatihan', 'penugasans.widyaiswara', 'penugasans.narasumber']);

        if ($search) {
            $query->where('nama_mata_pelatihan', 'like', "%{$search}%")
                  ->orWhereHas('angkatan', function ($a) use ($search) {
                      $a->where('nomor_angkatan', 'like', "%{$search}%")
                        ->orWhereHas('pelatihan', function ($p) use ($search) {
                            $p->where('nama_pelatihan', 'like', "%{$search}%");
                        });
                  });
        }

        if ($angkatanId) {
            $query->where('angkatan_id', $angkatanId);
        }

        $mataPelatihans = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();
        $angkatans = Angkatan::with('pelatihan')->orderBy('created_at', 'desc')->get();

        return view('mata_pelatihan.index', compact('mataPelatihans', 'angkatans', 'search', 'angkatanId'));
    }

    public function create(Request $request)
    {
        $angkatans = Angkatan::with('pelatihan')->orderBy('created_at', 'desc')->get();
        $selectedAngkatanId = $request->get('angkatan_id');
        return view('mata_pelatihan.create', compact('angkatans', 'selectedAngkatanId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'angkatan_id' => ['required', 'exists:angkatans,id'],
            'nama_mata_pelatihan' => ['required', 'string', 'max:255'],
            'rencana_jp' => ['required', 'numeric', 'min:0.5'],
            'deskripsi' => ['nullable', 'string'],
            'keterangan' => ['nullable', 'string'],
        ], [
            'angkatan_id.required' => 'Angkatan pelatihan wajib dipilih.',
            'nama_mata_pelatihan.required' => 'Nama mata pelatihan wajib diisi.',
            'rencana_jp.required' => 'Rencana JP wajib diisi.',
        ]);

        $mp = MataPelatihan::create($validated);

        LogAktivitas::catat('Mata Pelatihan', 'Tambah Data', 'Menambahkan Mata Pelatihan: ' . $mp->nama_mata_pelatihan);

        return redirect()->route('mata-pelatihan.index')->with('success', 'Mata Pelatihan berhasil ditambahkan.');
    }

    public function show(MataPelatihan $mataPelatihan)
    {
        $mataPelatihan->load(['angkatan.pelatihan', 'penugasans.widyaiswara', 'penugasans.narasumber', 'jadwals']);
        return view('mata_pelatihan.show', compact('mataPelatihan'));
    }

    public function edit(MataPelatihan $mataPelatihan)
    {
        $angkatans = Angkatan::with('pelatihan')->orderBy('created_at', 'desc')->get();
        return view('mata_pelatihan.edit', compact('mataPelatihan', 'angkatans'));
    }

    public function update(Request $request, MataPelatihan $mataPelatihan)
    {
        $validated = $request->validate([
            'angkatan_id' => ['required', 'exists:angkatans,id'],
            'nama_mata_pelatihan' => ['required', 'string', 'max:255'],
            'rencana_jp' => ['required', 'numeric', 'min:0.5'],
            'deskripsi' => ['nullable', 'string'],
            'keterangan' => ['nullable', 'string'],
        ], [
            'angkatan_id.required' => 'Angkatan pelatihan wajib dipilih.',
            'nama_mata_pelatihan.required' => 'Nama mata pelatihan wajib diisi.',
            'rencana_jp.required' => 'Rencana JP wajib diisi.',
        ]);

        $mataPelatihan->update($validated);

        LogAktivitas::catat('Mata Pelatihan', 'Ubah Data', 'Memperbarui Mata Pelatihan: ' . $mataPelatihan->nama_mata_pelatihan);

        return redirect()->route('mata-pelatihan.index')->with('success', 'Mata Pelatihan berhasil diperbarui.');
    }

    public function destroy(MataPelatihan $mataPelatihan)
    {
        $nama = $mataPelatihan->nama_mata_pelatihan;
        $mataPelatihan->delete();

        LogAktivitas::catat('Mata Pelatihan', 'Hapus Data', 'Menghapus Mata Pelatihan: ' . $nama);

        return redirect()->route('mata-pelatihan.index')->with('success', 'Mata Pelatihan berhasil dihapus.');
    }
}
