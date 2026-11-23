<?php

namespace App\Http\Controllers;

use App\Models\MataPelatihan;
use App\Models\Angkatan;
use App\Models\Pelatihan;
use App\Models\TahunKurikulum;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;

class MataPelatihanController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $pelatihanId = $request->get('pelatihan_id');
        $tahunKurikulum = $request->get('tahun_kurikulum');
        $angkatanId = $request->get('angkatan_id');
        $perPage = (int) $request->get('per_page', 10);

        $query = MataPelatihan::with(['pelatihan', 'angkatan.pelatihan', 'penugasans.widyaiswara', 'penugasans.narasumber']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_mata_pelatihan', 'like', "%{$search}%")
                  ->orWhere('tahun_kurikulum', 'like', "%{$search}%")
                  ->orWhereHas('pelatihan', function ($p) use ($search) {
                      $p->where('nama_pelatihan', 'like', "%{$search}%");
                  })
                  ->orWhereHas('angkatan', function ($a) use ($search) {
                      $a->where('nomor_angkatan', 'like', "%{$search}%")
                        ->orWhereHas('pelatihan', function ($p) use ($search) {
                            $p->where('nama_pelatihan', 'like', "%{$search}%");
                        });
                  });
            });
        }

        if ($pelatihanId) {
            $query->where(function($q) use ($pelatihanId) {
                $q->where('pelatihan_id', $pelatihanId)
                  ->orWhereHas('angkatan', function($a) use ($pelatihanId) {
                      $a->where('pelatihan_id', $pelatihanId);
                  });
            });
        }

        if ($tahunKurikulum) {
            $query->where('tahun_kurikulum', $tahunKurikulum);
        }

        if ($angkatanId) {
            $query->where('angkatan_id', $angkatanId);
        }

        $mataPelatihans = $query->orderBy('id', 'asc')->paginate($perPage)->withQueryString();
        $pelatihans = Pelatihan::orderBy('nama_pelatihan', 'asc')->get();
        $tahuns = TahunKurikulum::orderBy('tahun', 'desc')->get();
        $angkatans = Angkatan::with('pelatihan')->orderBy('created_at', 'desc')->get();

        return view('mata_pelatihan.index', compact('mataPelatihans', 'pelatihans', 'tahuns', 'angkatans', 'search', 'pelatihanId', 'tahunKurikulum', 'angkatanId', 'perPage'));
    }

    public function create(Request $request)
    {
        $angkatans = Angkatan::with('pelatihan')->orderBy('created_at', 'desc')->get();
        $pelatihans = Pelatihan::orderBy('nama_pelatihan', 'asc')->get();
        $tahuns = TahunKurikulum::orderBy('tahun', 'desc')->get();
        $selectedAngkatanId = $request->get('angkatan_id');
        $selectedPelatihanId = $request->get('pelatihan_id');
        return view('mata_pelatihan.create', compact('angkatans', 'pelatihans', 'tahuns', 'selectedAngkatanId', 'selectedPelatihanId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pelatihan_id' => ['nullable', 'exists:pelatihans,id'],
            'angkatan_id' => ['nullable', 'exists:angkatans,id'],
            'nama_mata_pelatihan' => ['required', 'string', 'max:255'],
            'rencana_jp' => ['required', 'numeric', 'min:0.5'],
            'tahun_kurikulum' => ['nullable', 'string', 'max:10'],
            'deskripsi' => ['nullable', 'string'],
            'keterangan' => ['nullable', 'string'],
        ], [
            'nama_mata_pelatihan.required' => 'Nama mata pelatihan wajib diisi.',
            'rencana_jp.required' => 'Rencana JP wajib diisi.',
        ]);

        if (empty($validated['pelatihan_id']) && !empty($validated['angkatan_id'])) {
            $ang = Angkatan::find($validated['angkatan_id']);
            if ($ang) {
                $validated['pelatihan_id'] = $ang->pelatihan_id;
            }
        }

        if (empty($validated['tahun_kurikulum'])) {
            $validated['tahun_kurikulum'] = '2026';
        }

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
            'pelatihan_id' => ['nullable', 'exists:pelatihans,id'],
            'angkatan_id' => ['nullable', 'exists:angkatans,id'],
            'nama_mata_pelatihan' => ['required', 'string', 'max:255'],
            'rencana_jp' => ['required', 'numeric', 'min:0.5'],
            'tahun_kurikulum' => ['nullable', 'string', 'max:10'],
            'deskripsi' => ['nullable', 'string'],
            'keterangan' => ['nullable', 'string'],
        ], [
            'nama_mata_pelatihan.required' => 'Nama mata pelatihan wajib diisi.',
            'rencana_jp.required' => 'Rencana JP wajib diisi.',
        ]);

        if (empty($validated['pelatihan_id']) && !empty($validated['angkatan_id'])) {
            $ang = Angkatan::find($validated['angkatan_id']);
            if ($ang) {
                $validated['pelatihan_id'] = $ang->pelatihan_id;
            }
        }

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
