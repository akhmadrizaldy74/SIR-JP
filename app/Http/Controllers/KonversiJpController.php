<?php

namespace App\Http\Controllers;

use App\Models\KonversiJp;
use App\Models\Widyaiswara;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Carbon\Carbon;

class KonversiJpController extends Controller
{
    public function index(Request $request)
    {
        $bulan = (int) $request->get('bulan', Carbon::now()->month);
        $tahun = (int) $request->get('tahun', Carbon::now()->year);
        $widyaiswaraId = $request->get('widyaiswara_id');
        $kategori = $request->get('kategori');
        $search = $request->get('search');

        $query = KonversiJp::with('widyaiswara');

        if ($bulan) {
            $query->whereMonth('tanggal_kegiatan', $bulan);
        }

        if ($tahun) {
            $query->whereYear('tanggal_kegiatan', $tahun);
        }

        if ($widyaiswaraId) {
            $query->where('widyaiswara_id', $widyaiswaraId);
        }

        if ($kategori) {
            $query->where('kategori_konversi', $kategori);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_kegiatan', 'like', "%{$search}%")
                  ->orWhere('nomor_sk', 'like', "%{$search}%")
                  ->orWhere('informasi_pembayaran', 'like', "%{$search}%")
                  ->orWhereHas('widyaiswara', function ($w) use ($search) {
                      $w->where('nama_lengkap', 'like', "%{$search}%");
                  });
            });
        }

        $konversis = $query->orderBy('tanggal_kegiatan', 'desc')->paginate(15)->withQueryString();
        $widyaiswaras = Widyaiswara::where('status', 'Aktif')->orderBy('nama_lengkap', 'asc')->get();

        $kategoriOptions = [
            'Penyusunan Modul',
            'Karya Tulis Ilmiah',
            'Bimbingan / Mentoring',
            'Evaluasi / Penguji',
            'Pengembangan Kurikulum',
            'Tim Kerja / Pokja',
            'Lainnya',
        ];

        return view('konversi.index', compact('konversis', 'widyaiswaras', 'kategoriOptions', 'bulan', 'tahun', 'widyaiswaraId', 'kategori', 'search'));
    }

    public function create()
    {
        $widyaiswaras = Widyaiswara::where('status', 'Aktif')->orderBy('nama_lengkap', 'asc')->get();
        $kategoriOptions = [
            'Penyusunan Modul',
            'Karya Tulis Ilmiah',
            'Bimbingan / Mentoring',
            'Evaluasi / Penguji',
            'Pengembangan Kurikulum',
            'Tim Kerja / Pokja',
            'Lainnya',
        ];
        return view('konversi.create', compact('widyaiswaras', 'kategoriOptions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'widyaiswara_id' => ['required', 'exists:widyaiswaras,id'],
            'tanggal_kegiatan' => ['required', 'date'],
            'nama_kegiatan' => ['required', 'string', 'max:255'],
            'kategori_konversi' => ['required', 'string', 'max:100'],
            'jumlah_jp' => ['required', 'numeric', 'min:0.5'],
            'jp_dibayarkan' => ['required', 'numeric', 'min:0'],
            'informasi_pembayaran' => ['nullable', 'string', 'max:255'],
            'nomor_sk' => ['nullable', 'string', 'max:100'],
            'status_validasi' => ['required', 'in:Disetujui,Menunggu,Ditolak'],
            'keterangan' => ['nullable', 'string'],
        ], [
            'widyaiswara_id.required' => 'Widyaiswara wajib dipilih.',
            'nama_kegiatan.required' => 'Nama kegiatan konversi wajib diisi.',
            'jumlah_jp.required' => 'Jumlah JP konversi wajib diisi.',
        ]);

        $konversi = KonversiJp::create($validated);

        LogAktivitas::catat('Konversi JP', 'Tambah Konversi', 'Menambahkan Konversi JP: ' . $konversi->nama_kegiatan . ' (' . $konversi->jumlah_jp . ' JP) untuk ' . $konversi->widyaiswara->nama_gelar);

        return redirect()->route('konversi.index')->with('success', 'Data Konversi JP berhasil ditambahkan.');
    }

    public function show(KonversiJp $konversi)
    {
        $konversi->load('widyaiswara');
        return view('konversi.show', compact('konversi'));
    }

    public function edit(KonversiJp $konversi)
    {
        $widyaiswaras = Widyaiswara::where('status', 'Aktif')->orderBy('nama_lengkap', 'asc')->get();
        $kategoriOptions = [
            'Penyusunan Modul',
            'Karya Tulis Ilmiah',
            'Bimbingan / Mentoring',
            'Evaluasi / Penguji',
            'Pengembangan Kurikulum',
            'Tim Kerja / Pokja',
            'Lainnya',
        ];
        return view('konversi.edit', compact('konversi', 'widyaiswaras', 'kategoriOptions'));
    }

    public function update(Request $request, KonversiJp $konversi)
    {
        $validated = $request->validate([
            'widyaiswara_id' => ['required', 'exists:widyaiswaras,id'],
            'tanggal_kegiatan' => ['required', 'date'],
            'nama_kegiatan' => ['required', 'string', 'max:255'],
            'kategori_konversi' => ['required', 'string', 'max:100'],
            'jumlah_jp' => ['required', 'numeric', 'min:0.5'],
            'jp_dibayarkan' => ['required', 'numeric', 'min:0'],
            'informasi_pembayaran' => ['nullable', 'string', 'max:255'],
            'nomor_sk' => ['nullable', 'string', 'max:100'],
            'status_validasi' => ['required', 'in:Disetujui,Menunggu,Ditolak'],
            'keterangan' => ['nullable', 'string'],
        ]);

        $konversi->update($validated);

        LogAktivitas::catat('Konversi JP', 'Ubah Konversi', 'Memperbarui Konversi JP #' . $konversi->id);

        return redirect()->route('konversi.index')->with('success', 'Data Konversi JP berhasil diperbarui.');
    }

    public function destroy(KonversiJp $konversi)
    {
        $nama = $konversi->nama_kegiatan;
        $konversi->delete();

        LogAktivitas::catat('Konversi JP', 'Hapus Konversi', 'Menghapus Konversi JP: ' . $nama);

        return redirect()->route('konversi.index')->with('success', 'Data Konversi JP berhasil dihapus.');
    }
}
