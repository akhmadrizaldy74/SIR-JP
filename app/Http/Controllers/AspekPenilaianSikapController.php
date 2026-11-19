<?php

namespace App\Http\Controllers;

use App\Models\AspekPenilaianSikap;
use App\Models\Pelatihan;
use Illuminate\Http\Request;

class AspekPenilaianSikapController extends Controller
{
    public function index(Request $request)
    {
        $pelatihanId = $request->get('pelatihan_id');
        $search = $request->get('search');

        $query = AspekPenilaianSikap::with('pelatihan');

        if ($pelatihanId) {
            $query->where('pelatihan_id', $pelatihanId);
        }

        if ($search) {
            $query->where('judul', 'like', "%{$search}%")
                  ->orWhere('kategori', 'like', "%{$search}%");
        }

        $aspekSikaps = $query->latest()->paginate(10)->withQueryString();
        $pelatihans = Pelatihan::orderBy('nama_pelatihan')->get();

        return view('sikap_form.index', compact('aspekSikaps', 'pelatihans', 'pelatihanId', 'search'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pelatihan_id' => 'required|exists:pelatihans,id',
            'judul' => 'required|string|max:255',
            'kategori' => 'nullable|string|max:255',
            'aspek_penilaian' => 'nullable|array',
            'aspek_penilaian.*' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $filteredAspek = array_values(array_filter($request->input('aspek_penilaian', []), fn($val) => !empty(trim($val))));

        AspekPenilaianSikap::create([
            'pelatihan_id' => $validated['pelatihan_id'],
            'judul' => $validated['judul'],
            'kategori' => $validated['kategori'],
            'aspek_penilaian' => $filteredAspek,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('sikap-form.index')->with('success', 'Form Aspek Penilaian Sikap berhasil ditambahkan !');
    }

    public function update(Request $request, AspekPenilaianSikap $sikapForm)
    {
        $validated = $request->validate([
            'pelatihan_id' => 'required|exists:pelatihans,id',
            'judul' => 'required|string|max:255',
            'kategori' => 'nullable|string|max:255',
            'aspek_penilaian' => 'nullable|array',
            'aspek_penilaian.*' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $filteredAspek = array_values(array_filter($request->input('aspek_penilaian', []), fn($val) => !empty(trim($val))));

        $sikapForm->update([
            'pelatihan_id' => $validated['pelatihan_id'],
            'judul' => $validated['judul'],
            'kategori' => $validated['kategori'],
            'aspek_penilaian' => $filteredAspek,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('sikap-form.index')->with('success', 'Form Aspek Penilaian Sikap berhasil diperbarui !');
    }

    public function toggle(AspekPenilaianSikap $sikapForm)
    {
        $sikapForm->update(['is_active' => !$sikapForm->is_active]);
        return redirect()->back()->with('success', 'Status form berhasil diubah !');
    }

    public function destroy(AspekPenilaianSikap $sikapForm)
    {
        $sikapForm->delete();
        return redirect()->route('sikap-form.index')->with('success', 'Form Aspek Penilaian Sikap berhasil dihapus !');
    }
}
