<?php

namespace App\Http\Controllers;

use App\Models\RealisasiJadwal;
use App\Models\RealisasiPengajar;
use App\Models\Jadwal;
use App\Models\PenugasanPengajar;
use App\Models\Widyaiswara;
use App\Models\Narasumber;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RealisasiController extends Controller
{
    public function index(Request $request)
    {
        $bulan = (int) $request->get('bulan', Carbon::now()->month);
        $tahun = (int) $request->get('tahun', Carbon::now()->year);
        $status = $request->get('status');
        $search = $request->get('search');

        $query = RealisasiJadwal::with([
            'jadwal.mataPelatihan.angkatan.pelatihan',
            'pengajarRealisasis.widyaiswara',
            'pengajarRealisasis.narasumber',
            'verifikator'
        ]);

        if ($bulan) {
            $query->whereMonth('tanggal_realisasi', $bulan);
        }

        if ($tahun) {
            $query->whereYear('tanggal_realisasi', $tahun);
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('jadwal.mataPelatihan', function ($m) use ($search) {
                    $m->where('nama_mata_pelatihan', 'like', "%{$search}%");
                })->orWhereHas('pengajarRealisasis.widyaiswara', function ($w) use ($search) {
                    $w->where('nama_lengkap', 'like', "%{$search}%");
                })->orWhereHas('pengajarRealisasis.narasumber', function ($n) use ($search) {
                    $n->where('nama_lengkap', 'like', "%{$search}%");
                });
            });
        }

        $realisasis = $query->orderBy('tanggal_realisasi', 'desc')->paginate(15)->withQueryString();

        return view('realisasi.index', compact('realisasis', 'bulan', 'tahun', 'status', 'search'));
    }

    public function create(Request $request)
    {
        $jadwalId = $request->get('jadwal_id');
        $jadwal = Jadwal::with(['mataPelatihan.angkatan.pelatihan', 'penugasanPengajar.widyaiswara', 'penugasanPengajar.narasumber'])
            ->findOrFail($jadwalId);

        // Cegah pencatatan realisasi ganda
        $existing = RealisasiJadwal::where('jadwal_id', $jadwalId)->first();
        if ($existing) {
            return redirect()->route('realisasi.edit', $existing)
                ->with('info', 'Jadwal ini sudah pernah direalisasikan. Anda dialihkan ke halaman edit realisasi.');
        }

        // Ambil seluruh pengajar yang ditugaskan pada mata pelatihan ini
        $penugasans = PenugasanPengajar::with(['widyaiswara', 'narasumber'])
            ->where('mata_pelatihan_id', $jadwal->mata_pelatihan_id)
            ->get();

        return view('realisasi.create', compact('jadwal', 'penugasans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'jadwal_id' => ['required', 'exists:jadwals,id', 'unique:realisasi_jadwals,jadwal_id'],
            'tanggal_realisasi' => ['required', 'date'],
            'jam_mulai_aktual' => ['required'],
            'jam_selesai_aktual' => ['required'],
            'status' => ['required', 'in:Terlaksana,Dibatalkan'],
            'total_jp_aktual' => ['required', 'numeric', 'min:0'],
            'total_jp_dibayarkan' => ['required', 'numeric', 'min:0'],
            'alasan_batal' => ['nullable', 'required_if:status,Dibatalkan', 'string'],
            'catatan' => ['nullable', 'string'],
            'pengajar' => ['nullable', 'array'],
        ], [
            'jadwal_id.unique' => 'Jadwal ini sudah memiliki catatan realisasi (mencegah duplikasi).',
            'alasan_batal.required_if' => 'Alasan pembatalan wajib diisi jika status Dibatalkan.',
        ]);

        DB::beginTransaction();
        try {
            $jadwal = Jadwal::findOrFail($validated['jadwal_id']);

            $realisasi = RealisasiJadwal::create([
                'jadwal_id' => $validated['jadwal_id'],
                'tanggal_realisasi' => $validated['tanggal_realisasi'],
                'jam_mulai_aktual' => $validated['jam_mulai_aktual'],
                'jam_selesai_aktual' => $validated['jam_selesai_aktual'],
                'jp_aktual' => $validated['total_jp_aktual'],
                'jp_dibayarkan' => $validated['total_jp_dibayarkan'],
                'status' => $validated['status'],
                'alasan_batal' => $validated['status'] === 'Dibatalkan' ? $validated['alasan_batal'] : null,
                'catatan' => $validated['catatan'] ?? null,
                'diverifikasi_oleh' => Auth::id(),
                'diverifikasi_pada' => Carbon::now(),
            ]);

            // Update status pada tabel induk Jadwal
            $jadwal->update([
                'status' => $validated['status'],
            ]);

            // Catat realisasi masing-masing pengajar
            if ($validated['status'] === 'Terlaksana' && !empty($request->input('pengajar'))) {
                foreach ($request->input('pengajar') as $pData) {
                    if (isset($pData['selected']) && $pData['selected'] == 1) {
                        RealisasiPengajar::create([
                            'realisasi_jadwal_id' => $realisasi->id,
                            'penugasan_pengajar_id' => $pData['penugasan_pengajar_id'] ?? null,
                            'jenis_pengajar' => $pData['jenis_pengajar'],
                            'widyaiswara_id' => $pData['jenis_pengajar'] === 'Widyaiswara' ? ($pData['pengajar_id'] ?? null) : null,
                            'narasumber_id' => $pData['jenis_pengajar'] === 'Narasumber' ? ($pData['pengajar_id'] ?? null) : null,
                            'jp_aktual' => $pData['jp_aktual'] ?? 0.00,
                            'jp_dibayarkan' => $pData['jp_dibayarkan'] ?? 0.00,
                            'status_kehadiran' => $pData['status_kehadiran'] ?? 'Hadir',
                            'keterangan' => $pData['keterangan'] ?? null,
                        ]);
                    }
                }
            }

            LogAktivitas::catat('Realisasi Kegiatan', 'Konfirmasi Realisasi', 'Konfirmasi realisasi jadwal #' . $jadwal->id . ' status: ' . $validated['status']);

            DB::commit();
            return redirect()->route('realisasi.index')->with('success', 'Realisasi kegiatan berhasil dicatat.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal mencatat realisasi: ' . $e->getMessage());
        }
    }

    public function show(RealisasiJadwal $realisasi)
    {
        $realisasi->load([
            'jadwal.mataPelatihan.angkatan.pelatihan',
            'pengajarRealisasis.widyaiswara',
            'pengajarRealisasis.narasumber',
            'verifikator',
        ]);

        return view('realisasi.show', compact('realisasi'));
    }

    public function edit(RealisasiJadwal $realisasi)
    {
        $realisasi->load(['jadwal.mataPelatihan.angkatan.pelatihan', 'pengajarRealisasis.widyaiswara', 'pengajarRealisasis.narasumber']);
        $penugasans = PenugasanPengajar::with(['widyaiswara', 'narasumber'])
            ->where('mata_pelatihan_id', $realisasi->jadwal->mata_pelatihan_id)
            ->get();

        return view('realisasi.edit', compact('realisasi', 'penugasans'));
    }

    public function update(Request $request, RealisasiJadwal $realisasi)
    {
        $validated = $request->validate([
            'tanggal_realisasi' => ['required', 'date'],
            'jam_mulai_aktual' => ['required'],
            'jam_selesai_aktual' => ['required'],
            'status' => ['required', 'in:Terlaksana,Dibatalkan'],
            'total_jp_aktual' => ['required', 'numeric', 'min:0'],
            'total_jp_dibayarkan' => ['required', 'numeric', 'min:0'],
            'alasan_batal' => ['nullable', 'required_if:status,Dibatalkan', 'string'],
            'catatan' => ['nullable', 'string'],
        ]);

        DB::beginTransaction();
        try {
            $realisasi->update([
                'tanggal_realisasi' => $validated['tanggal_realisasi'],
                'jam_mulai_aktual' => $validated['jam_mulai_aktual'],
                'jam_selesai_aktual' => $validated['jam_selesai_aktual'],
                'jp_aktual' => $validated['total_jp_aktual'],
                'jp_dibayarkan' => $validated['total_jp_dibayarkan'],
                'status' => $validated['status'],
                'alasan_batal' => $validated['status'] === 'Dibatalkan' ? $validated['alasan_batal'] : null,
                'catatan' => $validated['catatan'] ?? null,
                'diverifikasi_oleh' => Auth::id(),
                'diverifikasi_pada' => Carbon::now(),
            ]);

            $realisasi->jadwal->update([
                'status' => $validated['status'],
            ]);

            // Perbarui pembagian JP pengajar
            if (!empty($request->input('pengajar'))) {
                // Hapus dulu entri lama untuk di-refresh
                RealisasiPengajar::where('realisasi_jadwal_id', $realisasi->id)->delete();

                if ($validated['status'] === 'Terlaksana') {
                    foreach ($request->input('pengajar') as $pData) {
                        if (isset($pData['selected']) && $pData['selected'] == 1) {
                            RealisasiPengajar::create([
                                'realisasi_jadwal_id' => $realisasi->id,
                                'penugasan_pengajar_id' => $pData['penugasan_pengajar_id'] ?? null,
                                'jenis_pengajar' => $pData['jenis_pengajar'],
                                'widyaiswara_id' => $pData['jenis_pengajar'] === 'Widyaiswara' ? ($pData['pengajar_id'] ?? null) : null,
                                'narasumber_id' => $pData['jenis_pengajar'] === 'Narasumber' ? ($pData['pengajar_id'] ?? null) : null,
                                'jp_aktual' => $pData['jp_aktual'] ?? 0.00,
                                'jp_dibayarkan' => $pData['jp_dibayarkan'] ?? 0.00,
                                'status_kehadiran' => $pData['status_kehadiran'] ?? 'Hadir',
                                'keterangan' => $pData['keterangan'] ?? null,
                            ]);
                        }
                    }
                }
            }

            LogAktivitas::catat('Realisasi Kegiatan', 'Ubah Realisasi', 'Memperbarui realisasi #' . $realisasi->id);

            DB::commit();
            return redirect()->route('realisasi.index')->with('success', 'Data realisasi kegiatan berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal memperbarui realisasi: ' . $e->getMessage());
        }
    }

    public function destroy(RealisasiJadwal $realisasi)
    {
        DB::beginTransaction();
        try {
            $jadwal = $realisasi->jadwal;
            if ($jadwal) {
                $jadwal->update(['status' => 'Terjadwal']);
            }

            RealisasiPengajar::where('realisasi_jadwal_id', $realisasi->id)->delete();
            $realisasi->delete();

            LogAktivitas::catat('Realisasi Kegiatan', 'Hapus Realisasi', 'Membatalkan realisasi jadwal #' . ($jadwal->id ?? '-'));

            DB::commit();
            return redirect()->route('realisasi.index')->with('success', 'Catatan realisasi berhasil dihapus dan status jadwal dikembalikan ke Terjadwal.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus realisasi: ' . $e->getMessage());
        }
    }
}
