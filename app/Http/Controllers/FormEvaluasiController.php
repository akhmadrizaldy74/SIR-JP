<?php

namespace App\Http\Controllers;

use App\Models\FormEvaluasi;
use App\Models\Pelatihan;
use Illuminate\Http\Request;

class FormEvaluasiController extends Controller
{
    public function index(Request $request)
    {
        $pelatihanId = $request->get('pelatihan_id');
        $search = $request->get('search');

        $query = FormEvaluasi::with('pelatihan');

        if ($pelatihanId) {
            $query->where('pelatihan_id', $pelatihanId);
        }

        if ($search) {
            $query->where('judul_form', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
        }

        $formEvaluasis = $query->latest()->paginate(10)->withQueryString();
        $pelatihans = Pelatihan::orderBy('nama_pelatihan')->get();

        return view('evaluasi_form.index', compact('formEvaluasis', 'pelatihans', 'pelatihanId', 'search'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pelatihan_id' => 'required|exists:pelatihans,id',
            'judul_form' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        FormEvaluasi::create($validated);

        return redirect()->route('evaluasi-form.index')->with('success', 'Form Evaluasi berhasil ditambahkan !');
    }

    public function update(Request $request, FormEvaluasi $evaluasiForm)
    {
        $validated = $request->validate([
            'pelatihan_id' => 'required|exists:pelatihans,id',
            'judul_form' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $evaluasiForm->update($validated);

        return redirect()->route('evaluasi-form.index')->with('success', 'Form Evaluasi berhasil diperbarui !');
    }

    public function destroy(FormEvaluasi $evaluasiForm)
    {
        $evaluasiForm->delete();
        return redirect()->route('evaluasi-form.index')->with('success', 'Form Evaluasi berhasil dihapus !');
    }
}
