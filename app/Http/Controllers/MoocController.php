<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pelatihan;
use App\Models\Angkatan;
use App\Models\Peserta;

class MoocController extends Controller
{
    /**
     * Halaman Modul LMS MOOC - Pelatihan
     */
    public function pelatihan(Request $request)
    {
        $pelatihans = Pelatihan::withCount('angkatans')->paginate(10);
        return view('mooc.pelatihan', compact('pelatihans'));
    }

    /**
     * Halaman Modul LMS MOOC - Bank Soal
     */
    public function bankSoal(Request $request)
    {
        $pelatihans = Pelatihan::orderBy('id', 'asc')->get();
        return view('mooc.bank-soal', compact('pelatihans'));
    }

    /**
     * Tambah Paket Bank Soal Baru
     */
    public function storeBankSoal(Request $request)
    {
        $validated = $request->validate([
            'nama_pelatihan' => 'required|string|max:255',
            'kategori' => 'nullable|string|max:100',
            'status' => 'nullable|string|max:50',
            'keterangan' => 'nullable|string|max:500',
        ]);

        $rawStatus = $validated['status'] ?? 'Berjalan';
        if (strcasecmp($rawStatus, 'aktif') === 0) {
            $status = 'Berjalan';
        } elseif (strcasecmp($rawStatus, 'nonaktif') === 0) {
            $status = 'Selesai';
        } elseif (in_array($rawStatus, ['Direncanakan', 'Berjalan', 'Selesai', 'Dibatalkan'])) {
            $status = $rawStatus;
        } else {
            $status = 'Berjalan';
        }

        $pelatihan = Pelatihan::create([
            'nama_pelatihan' => $validated['nama_pelatihan'],
            'kode_pelatihan' => 'MOOC-' . strtoupper(\Illuminate\Support\Str::random(6)),
            'kategori' => $validated['kategori'] ?? 'MOOC Diklat',
            'status' => $status,
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->addMonths(6)->toDateString(),
            'keterangan' => $validated['keterangan'] ?? 'Tersedia bank soal Pre-Test, Kuis 1-4, dan Post-Test.',
            'penyelenggara' => 'PPSDM Kemnaker',
        ]);

        \App\Models\LogAktivitas::catat('LMS MOOC', 'Tambah Bank Soal', 'Menambahkan paket bank soal: ' . $pelatihan->nama_pelatihan);

        return redirect()->route('mooc.bank-soal')
            ->with('success', 'Paket Bank Soal baru "' . $pelatihan->nama_pelatihan . '" berhasil ditambahkan.');
    }

    /**
     * Ubah Paket Bank Soal
     */
    public function updateBankSoal(Request $request, $id)
    {
        $pelatihan = Pelatihan::findOrFail($id);

        $validated = $request->validate([
            'nama_pelatihan' => 'required|string|max:255',
            'kategori' => 'nullable|string|max:100',
            'status' => 'nullable|string|max:50',
            'keterangan' => 'nullable|string|max:500',
        ]);

        $rawStatus = $validated['status'] ?? $pelatihan->status;
        if (strcasecmp($rawStatus, 'aktif') === 0) {
            $status = 'Berjalan';
        } elseif (strcasecmp($rawStatus, 'nonaktif') === 0) {
            $status = 'Selesai';
        } elseif (in_array($rawStatus, ['Direncanakan', 'Berjalan', 'Selesai', 'Dibatalkan'])) {
            $status = $rawStatus;
        } else {
            $status = $pelatihan->status ?? 'Berjalan';
        }

        $pelatihan->update([
            'nama_pelatihan' => $validated['nama_pelatihan'],
            'kategori' => $validated['kategori'] ?? $pelatihan->kategori,
            'status' => $status,
            'keterangan' => $validated['keterangan'] ?? $pelatihan->keterangan,
        ]);

        \App\Models\LogAktivitas::catat('LMS MOOC', 'Ubah Bank Soal', 'Memperbarui paket bank soal ID: ' . $pelatihan->id);

        return redirect()->route('mooc.bank-soal')
            ->with('success', 'Paket Bank Soal "' . $pelatihan->nama_pelatihan . '" berhasil diperbarui.');
    }

    /**
     * Hapus Paket Bank Soal
     */
    public function destroyBankSoal($id)
    {
        $pelatihan = Pelatihan::findOrFail($id);
        $nama = $pelatihan->nama_pelatihan;
        $pelatihan->delete();

        \App\Models\LogAktivitas::catat('LMS MOOC', 'Hapus Bank Soal', 'Menghapus paket bank soal: ' . $nama);

        return redirect()->route('mooc.bank-soal')
            ->with('success', 'Paket Bank Soal "' . $nama . '" berhasil dihapus.');
    }

    /**
     * Simpan Butir Soal Bank Soal
     */
    public function saveButirSoal(Request $request, $id)
    {
        $pelatihan = Pelatihan::findOrFail($id);
        \App\Models\LogAktivitas::catat('LMS MOOC', 'Kelola Butir Soal', 'Memperbarui butir pertanyaan untuk: ' . $pelatihan->nama_pelatihan);

        return redirect()->route('mooc.bank-soal')
            ->with('success', 'Butir pertanyaan untuk paket soal "' . $pelatihan->nama_pelatihan . '" berhasil disimpan.');
    }

    /**
     * Halaman Modul LMS MOOC - Riwayat Pelatihan Peserta
     */
    public function riwayat(Request $request)
    {
        $pesertas = Peserta::with('angkatan.pelatihan')->paginate(15);
        return view('mooc.riwayat', compact('pesertas'));
    }

    /**
     * Halaman Modul LMS MOOC - Komentar Pelatihan
     */
    public function komentar(Request $request)
    {
        return view('mooc.komentar');
    }

    /**
     * Halaman Modul LMS MOOC - Nilai Peserta Ujian Permodul
     */
    public function nilaiUjian(Request $request)
    {
        $pesertas = Peserta::with('angkatan.pelatihan')->paginate(15);
        return view('mooc.nilai-ujian', compact('pesertas'));
    }
}
