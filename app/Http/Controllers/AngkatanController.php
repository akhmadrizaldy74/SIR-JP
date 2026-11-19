<?php

namespace App\Http\Controllers;

use App\Models\Angkatan;
use App\Models\Pelatihan;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AngkatanController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $pelatihanId = $request->get('pelatihan_id');
        $status = $request->get('status');

        $query = Angkatan::with(['pelatihan'])->withCount('mataPelatihans');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nomor_angkatan', 'like', "%{$search}%")
                  ->orWhere('lokasi', 'like', "%{$search}%")
                  ->orWhereHas('pelatihan', function ($p) use ($search) {
                      $p->where('nama_pelatihan', 'like', "%{$search}%");
                  });
            });
        }

        if ($pelatihanId) {
            $query->where('pelatihan_id', $pelatihanId);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $angkatans = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();
        $pelatihans = Pelatihan::orderBy('nama_pelatihan', 'asc')->get();
        $statusOptions = ['Direncanakan', 'Berjalan', 'Selesai', 'Dibatalkan'];

        return view('angkatan.index', compact('angkatans', 'pelatihans', 'search', 'pelatihanId', 'status', 'statusOptions'));
    }

    public function create(Request $request)
    {
        $pelatihans = Pelatihan::orderBy('nama_pelatihan', 'asc')->get();
        $selectedPelatihanId = $request->get('pelatihan_id');
        return view('angkatan.create', compact('pelatihans', 'selectedPelatihanId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pelatihan_id' => ['required', 'exists:pelatihans,id'],
            'nomor_angkatan' => ['required', 'string', 'max:100'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'jumlah_peserta' => ['required', 'integer', 'min:1'],
            'lokasi' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(['Direncanakan', 'Berjalan', 'Selesai', 'Dibatalkan'])],
            'keterangan' => ['nullable', 'string'],
        ], [
            'pelatihan_id.required' => 'Program pelatihan wajib dipilih.',
            'nomor_angkatan.required' => 'Nomor / Nama angkatan wajib diisi.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
            'jumlah_peserta.required' => 'Jumlah peserta wajib diisi.',
        ]);

        $angkatan = Angkatan::create($validated);

        LogAktivitas::catat('Angkatan', 'Tambah Data', 'Menambahkan ' . $angkatan->nomor_angkatan . ' untuk ' . $angkatan->pelatihan->nama_pelatihan);

        return redirect()->route('angkatan.index')->with('success', 'Data Angkatan berhasil ditambahkan.');
    }

    public function show(Angkatan $angkatan)
    {
        $angkatan->load(['pelatihan', 'mataPelatihans.penugasans.widyaiswara', 'mataPelatihans.penugasans.narasumber']);
        return view('angkatan.show', compact('angkatan'));
    }

    public function edit(Angkatan $angkatan)
    {
        $pelatihans = Pelatihan::orderBy('nama_pelatihan', 'asc')->get();
        $statusOptions = ['Direncanakan', 'Berjalan', 'Selesai', 'Dibatalkan'];
        return view('angkatan.edit', compact('angkatan', 'pelatihans', 'statusOptions'));
    }

    public function update(Request $request, Angkatan $angkatan)
    {
        $validated = $request->validate([
            'pelatihan_id' => ['required', 'exists:pelatihans,id'],
            'nomor_angkatan' => ['required', 'string', 'max:100'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'jumlah_peserta' => ['required', 'integer', 'min:1'],
            'lokasi' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(['Direncanakan', 'Berjalan', 'Selesai', 'Dibatalkan'])],
            'keterangan' => ['nullable', 'string'],
        ], [
            'pelatihan_id.required' => 'Program pelatihan wajib dipilih.',
            'nomor_angkatan.required' => 'Nomor / Nama angkatan wajib diisi.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
        ]);

        $angkatan->update($validated);

        LogAktivitas::catat('Angkatan', 'Ubah Data', 'Memperbarui data Angkatan: ' . $angkatan->nomor_angkatan);

        return redirect()->route('angkatan.index')->with('success', 'Data Angkatan berhasil diperbarui.');
    }

    public function destroy(Angkatan $angkatan)
    {
        $nama = $angkatan->nomor_angkatan;
        $angkatan->delete();

        LogAktivitas::catat('Angkatan', 'Hapus Data', 'Menghapus Angkatan: ' . $nama);

        return redirect()->route('angkatan.index')->with('success', 'Data Angkatan berhasil dihapus.');
    }
}
