<?php

namespace App\Http\Controllers;

use App\Models\Pelatihan;
use App\Models\JenisPelatihan;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PelatihanController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $kategori = $request->get('kategori') ?? $request->get('jenis');
        $status = $request->get('status');
        $perPage = (int) $request->get('per_page', 10);

        $query = Pelatihan::withCount('angkatans');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_pelatihan', 'like', "%{$search}%")
                  ->orWhere('kode_pelatihan', 'like', "%{$search}%")
                  ->orWhere('penyelenggara', 'like', "%{$search}%");
            });
        }

        if ($kategori) {
            $query->where('kategori', $kategori);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $pelatihans = $query->orderBy('created_at', 'desc')->paginate($perPage)->withQueryString();

        $kategoriOptions = ['Pelatihan Teknis', 'Pelatihan Fungsional', 'Pelatihan Manajemen Aparatur', 'Manajerial', 'Teknis', 'Fungsional', 'Sosio Kultural'];
        $jenisPelatihans = JenisPelatihan::where('status', true)->get();
        if ($jenisPelatihans->count() > 0) {
            $kategoriOptions = array_unique(array_merge($jenisPelatihans->pluck('nama_jenis')->toArray(), $kategoriOptions));
        }
        $statusOptions = ['Direncanakan', 'Berjalan', 'Selesai', 'Dibatalkan'];

        return view('pelatihan.index', compact('pelatihans', 'search', 'kategori', 'status', 'kategoriOptions', 'statusOptions', 'perPage', 'jenisPelatihans'));
    }

    public function create()
    {
        $kategoriOptions = ['Manajerial', 'Teknis', 'Fungsional', 'Sosio Kultural'];
        return view('pelatihan.create', compact('kategoriOptions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_pelatihan' => ['required', 'string', 'max:50', 'unique:pelatihans,kode_pelatihan'],
            'nama_pelatihan' => ['required', 'string', 'max:255'],
            'kategori' => ['required', 'string', 'max:100'],
            'penyelenggara' => ['required', 'string', 'max:255'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'status' => ['required', Rule::in(['Direncanakan', 'Berjalan', 'Selesai', 'Dibatalkan'])],
            'keterangan' => ['nullable', 'string'],
        ], [
            'kode_pelatihan.required' => 'Kode pelatihan wajib diisi.',
            'kode_pelatihan.unique' => 'Kode pelatihan sudah digunakan.',
            'nama_pelatihan.required' => 'Nama pelatihan wajib diisi.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
        ]);

        $pelatihan = Pelatihan::create($validated);

        LogAktivitas::catat('Pelatihan', 'Tambah Data', 'Menambahkan Pelatihan: ' . $pelatihan->nama_pelatihan);

        return redirect()->route('pelatihan.index')->with('success', 'Data Pelatihan berhasil ditambahkan.');
    }

    public function show(Pelatihan $pelatihan)
    {
        $pelatihan->load(['angkatans.mataPelatihans']);
        return view('pelatihan.show', compact('pelatihan'));
    }

    public function edit(Pelatihan $pelatihan)
    {
        $kategoriOptions = ['Manajerial', 'Teknis', 'Fungsional', 'Sosio Kultural'];
        $statusOptions = ['Direncanakan', 'Berjalan', 'Selesai', 'Dibatalkan'];
        return view('pelatihan.edit', compact('pelatihan', 'kategoriOptions', 'statusOptions'));
    }

    public function update(Request $request, Pelatihan $pelatihan)
    {
        $validated = $request->validate([
            'kode_pelatihan' => ['required', 'string', 'max:50', Rule::unique('pelatihans', 'kode_pelatihan')->ignore($pelatihan->id)],
            'nama_pelatihan' => ['required', 'string', 'max:255'],
            'kategori' => ['required', 'string', 'max:100'],
            'penyelenggara' => ['required', 'string', 'max:255'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'status' => ['required', Rule::in(['Direncanakan', 'Berjalan', 'Selesai', 'Dibatalkan'])],
            'keterangan' => ['nullable', 'string'],
        ], [
            'kode_pelatihan.required' => 'Kode pelatihan wajib diisi.',
            'kode_pelatihan.unique' => 'Kode pelatihan sudah digunakan.',
            'nama_pelatihan.required' => 'Nama pelatihan wajib diisi.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
        ]);

        $pelatihan->update($validated);

        LogAktivitas::catat('Pelatihan', 'Ubah Data', 'Memperbarui data Pelatihan: ' . $pelatihan->nama_pelatihan);

        if ($request->filled('redirect_to')) {
            return redirect($request->input('redirect_to'))->with('success', 'Data Pelatihan berhasil diperbarui.');
        }

        return redirect()->route('pelatihan.index')->with('success', 'Data Pelatihan berhasil diperbarui.');
    }

    public function destroy(Request $request, Pelatihan $pelatihan)
    {
        $nama = $pelatihan->nama_pelatihan;
        $pelatihan->delete();

        LogAktivitas::catat('Pelatihan', 'Hapus Data', 'Menghapus data Pelatihan: ' . $nama);

        if ($request->filled('redirect_to')) {
            return redirect($request->input('redirect_to'))->with('success', 'Data Pelatihan ' . $nama . ' berhasil dihapus.');
        }

        return redirect()->route('pelatihan.index')->with('success', 'Data Pelatihan ' . $nama . ' berhasil dihapus.');
    }

    public function toggle(Pelatihan $pelatihan)
    {
        $pelatihan->status = ($pelatihan->status === 'Berjalan' || $pelatihan->status === 'Direncanakan') ? 'Selesai' : 'Berjalan';
        $pelatihan->save();

        LogAktivitas::catat('Pelatihan', 'Toggle Status', 'Mengubah status pelatihan ' . $pelatihan->nama_pelatihan . ' menjadi ' . $pelatihan->status);

        return response()->json([
            'success' => true,
            'status' => $pelatihan->status,
            'is_active' => in_array($pelatihan->status, ['Berjalan', 'Direncanakan']),
        ]);
    }
}
