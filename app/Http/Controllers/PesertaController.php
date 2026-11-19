<?php

namespace App\Http\Controllers;

use App\Models\Peserta;
use App\Models\Angkatan;
use App\Models\KelasMapel;
use Illuminate\Http\Request;

class PesertaController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $angkatanId = $request->get('angkatan_id');

        $query = Peserta::with(['angkatan.pelatihan', 'kelasMapel']);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhere('instansi', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($angkatanId) {
            $query->where('angkatan_id', $angkatanId);
        }

        $pesertas = $query->latest()->paginate(10)->withQueryString();
        $angkatans = Angkatan::with('pelatihan')->orderBy('nomor_angkatan')->get();

        return view('peserta.index', compact('pesertas', 'angkatans', 'search', 'angkatanId'));
    }

    public function create()
    {
        $angkatans = Angkatan::with('pelatihan')->orderBy('nomor_angkatan')->get();
        $kelasMapels = KelasMapel::orderBy('nama_kelas')->get();
        return view('peserta.create', compact('angkatans', 'kelasMapels'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nip' => 'required|string|max:30|unique:pesertas,nip',
            'nama' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'instansi' => 'nullable|string|max:255',
            'jabatan' => 'nullable|string|max:255',
            'golongan' => 'nullable|string|max:50',
            'angkatan_id' => 'nullable|exists:angkatans,id',
            'kelas_mapel_id' => 'nullable|exists:kelas_mapels,id',
        ]);

        Peserta::create($validated);

        return redirect()->route('peserta.index')->with('success', 'Data peserta diklat berhasil disimpan !');
    }

    public function edit(Peserta $peserta)
    {
        $angkatans = Angkatan::with('pelatihan')->orderBy('nomor_angkatan')->get();
        $kelasMapels = KelasMapel::orderBy('nama_kelas')->get();
        return view('peserta.edit', compact('peserta', 'angkatans', 'kelasMapels'));
    }

    public function update(Request $request, Peserta $peserta)
    {
        $validated = $request->validate([
            'nip' => 'required|string|max:30|unique:pesertas,nip,' . $peserta->id,
            'nama' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'instansi' => 'nullable|string|max:255',
            'jabatan' => 'nullable|string|max:255',
            'golongan' => 'nullable|string|max:50',
            'angkatan_id' => 'nullable|exists:angkatans,id',
            'kelas_mapel_id' => 'nullable|exists:kelas_mapels,id',
        ]);

        $peserta->update($validated);

        return redirect()->route('peserta.index')->with('success', 'Data peserta diklat berhasil diperbarui !');
    }

    public function destroy(Peserta $peserta)
    {
        $peserta->delete();
        return redirect()->route('peserta.index')->with('success', 'Data peserta diklat berhasil dihapus !');
    }
}
