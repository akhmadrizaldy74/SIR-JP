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
        $search = $request->get('search');
        $status = $request->get('status');
        $sort = $request->get('sort', 'latest');

        $query = Pelatihan::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_pelatihan', 'like', "%{$search}%")
                  ->orWhere('kategori', 'like', "%{$search}%")
                  ->orWhere('kode_pelatihan', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        if ($status) {
            if (strcasecmp($status, 'aktif') === 0) {
                $query->whereIn('status', ['Berjalan', 'Direncanakan']);
            } elseif (strcasecmp($status, 'nonaktif') === 0) {
                $query->whereIn('status', ['Selesai', 'Dibatalkan']);
            } else {
                $query->where('status', $status);
            }
        }

        if ($sort === 'name_asc') {
            $query->orderBy('nama_pelatihan', 'asc');
        } elseif ($sort === 'name_desc') {
            $query->orderBy('nama_pelatihan', 'desc');
        } elseif ($sort === 'oldest') {
            $query->orderBy('id', 'asc');
        } else {
            $query->orderBy('id', 'desc');
        }

        $pelatihans = $query->paginate(9)->withQueryString();

        return view('mooc.bank-soal', compact('pelatihans', 'search', 'status', 'sort'));
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
        \App\Models\LogAktivitas::catat('LMS MOOC', 'Kelola Butir Soal', 'Menyimpan butir pertanyaan untuk: ' . $pelatihan->nama_pelatihan);

        return redirect()->route('mooc.bank-soal')
            ->with('success', 'Butir pertanyaan untuk paket soal "' . $pelatihan->nama_pelatihan . '" siap diujikan.');
    }

    /**
     * Tambah Butir Soal Baru ke Paket Soal
     */
    public function addButirSoal(Request $request, $id)
    {
        $pelatihan = Pelatihan::findOrFail($id);

        $validated = $request->validate([
            'tipe' => 'required|string|in:pretest,kuis,posttest',
            'pertanyaan' => 'required|string',
            'pilihan_a' => 'required|string',
            'pilihan_b' => 'required|string',
            'pilihan_c' => 'required|string',
            'pilihan_d' => 'required|string',
            'kunci' => 'required|string|in:A,B,C,D',
            'bobot' => 'nullable|integer|min:1',
        ]);

        $items = $pelatihan->getBankSoalItems();
        $nextId = count($items) > 0 ? (max(array_column($items, 'id')) + 1) : 1;

        $newItem = [
            'id' => $nextId,
            'tipe' => $validated['tipe'],
            'pertanyaan' => $validated['pertanyaan'],
            'pilihan' => [
                'A' => $validated['pilihan_a'],
                'B' => $validated['pilihan_b'],
                'C' => $validated['pilihan_c'],
                'D' => $validated['pilihan_d'],
            ],
            'kunci' => strtoupper($validated['kunci']),
            'bobot' => (int) ($validated['bobot'] ?? 10),
        ];

        $items[] = $newItem;
        $pelatihan->bank_soal_data = $items;
        $pelatihan->save();

        \App\Models\LogAktivitas::catat('LMS MOOC', 'Tambah Butir Soal', 'Menambahkan butir soal baru pada: ' . $pelatihan->nama_pelatihan);

        return redirect()->route('mooc.bank-soal')
            ->with('success', 'Butir soal baru berhasil ditambahkan ke paket "' . $pelatihan->nama_pelatihan . '".');
    }

    /**
     * Ubah / Edit Butir Soal yang Sudah Ada
     */
    public function updateButirSoal(Request $request, $id, $soalId)
    {
        $pelatihan = Pelatihan::findOrFail($id);

        $validated = $request->validate([
            'tipe' => 'required|string|in:pretest,kuis,posttest',
            'pertanyaan' => 'required|string',
            'pilihan_a' => 'required|string',
            'pilihan_b' => 'required|string',
            'pilihan_c' => 'required|string',
            'pilihan_d' => 'required|string',
            'kunci' => 'required|string|in:A,B,C,D',
            'bobot' => 'nullable|integer|min:1',
        ]);

        $items = $pelatihan->getBankSoalItems();
        $found = false;

        foreach ($items as &$item) {
            if ($item['id'] == $soalId) {
                $item['tipe'] = $validated['tipe'];
                $item['pertanyaan'] = $validated['pertanyaan'];
                $item['pilihan'] = [
                    'A' => $validated['pilihan_a'],
                    'B' => $validated['pilihan_b'],
                    'C' => $validated['pilihan_c'],
                    'D' => $validated['pilihan_d'],
                ];
                $item['kunci'] = strtoupper($validated['kunci']);
                $item['bobot'] = (int) ($validated['bobot'] ?? 10);
                $found = true;
                break;
            }
        }

        if ($found) {
            $pelatihan->bank_soal_data = $items;
            $pelatihan->save();
            \App\Models\LogAktivitas::catat('LMS MOOC', 'Ubah Butir Soal', "Memperbarui butir soal ID {$soalId} pada: " . $pelatihan->nama_pelatihan);

            return redirect()->route('mooc.bank-soal')
                ->with('success', 'Butir soal berhasil diperbarui!');
        }

        return redirect()->route('mooc.bank-soal')
            ->with('error', 'Butir soal tidak ditemukan.');
    }

    /**
     * Hapus Butir Soal Tertentu
     */
    public function deleteButirSoal(Request $request, $id, $soalId)
    {
        $pelatihan = Pelatihan::findOrFail($id);
        $items = $pelatihan->getBankSoalItems();

        $filtered = array_values(array_filter($items, function ($item) use ($soalId) {
            return $item['id'] != $soalId;
        }));

        $pelatihan->bank_soal_data = $filtered;
        $pelatihan->save();

        \App\Models\LogAktivitas::catat('LMS MOOC', 'Hapus Butir Soal', "Menghapus butir soal ID {$soalId} pada: " . $pelatihan->nama_pelatihan);

        return redirect()->route('mooc.bank-soal')
            ->with('success', 'Butir soal berhasil dihapus.');
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
