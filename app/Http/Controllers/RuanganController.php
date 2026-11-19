<?php

namespace App\Http\Controllers;

use App\Models\Ruangan;
use Illuminate\Http\Request;

class RuanganController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $query = Ruangan::query();

        if ($search) {
            $query->where('nama_ruangan', 'like', "%{$search}%")
                  ->orWhere('lokasi', 'like', "%{$search}%");
        }

        $ruangans = $query->latest()->paginate(10)->withQueryString();

        return view('ruangan.index', compact('ruangans', 'search'));
    }

    public function create()
    {
        return view('ruangan.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_ruangan' => 'required|string|max:255|unique:ruangans,nama_ruangan',
            'kapasitas' => 'nullable|string|max:100',
            'lokasi' => 'nullable|string|max:255',
            'is_aktif' => 'nullable|boolean',
        ]);

        $validated['is_aktif'] = $request->has('is_aktif');

        Ruangan::create($validated);

        return redirect()->route('ruangan.index')->with('success', 'Data ruangan berhasil disimpan !');
    }

    public function edit(Ruangan $ruangan)
    {
        return view('ruangan.edit', compact('ruangan'));
    }

    public function update(Request $request, Ruangan $ruangan)
    {
        $validated = $request->validate([
            'nama_ruangan' => 'required|string|max:255|unique:ruangans,nama_ruangan,' . $ruangan->id,
            'kapasitas' => 'nullable|string|max:100',
            'lokasi' => 'nullable|string|max:255',
            'is_aktif' => 'nullable|boolean',
        ]);

        $validated['is_aktif'] = $request->has('is_aktif');

        $ruangan->update($validated);

        return redirect()->route('ruangan.index')->with('success', 'Data ruangan berhasil diperbarui !');
    }

    public function destroy(Ruangan $ruangan)
    {
        $ruangan->delete();
        return redirect()->route('ruangan.index')->with('success', 'Data ruangan berhasil dihapus !');
    }
}
