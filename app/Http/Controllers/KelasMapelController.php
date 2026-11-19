<?php

namespace App\Http\Controllers;

use App\Models\KelasMapel;
use App\Models\KelasMapelPengajar;
use App\Models\Pelatihan;
use App\Models\Angkatan;
use App\Models\MataPelatihan;
use App\Models\Ruangan;
use App\Models\Widyaiswara;
use App\Models\Narasumber;
use App\Models\Peserta;
use App\Models\Jadwal;
use App\Models\PenugasanPengajar;
use Illuminate\Http\Request;

class KelasMapelController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $query = KelasMapel::with(['pelatihan', 'angkatan', 'mataPelatihan', 'ruangan', 'pesertas']);

        if ($search) {
            $query->where('nama_kelas', 'like', "%{$search}%")
                  ->orWhereHas('pelatihan', fn($q) => $q->where('nama_pelatihan', 'like', "%{$search}%"))
                  ->orWhereHas('angkatan', fn($q) => $q->where('nomor_angkatan', 'like', "%{$search}%"));
        }

        $kelasMapels = $query->latest()->paginate(10)->withQueryString();

        return view('kelas_mapel.index', compact('kelasMapels', 'search'));
    }

    public function create()
    {
        $pelatihans = Pelatihan::orderBy('nama_pelatihan')->get();
        $angkatans = Angkatan::orderBy('nomor_angkatan')->get();
        $ruangans = Ruangan::where('is_aktif', true)->orderBy('nama_ruangan')->get();
        $mataPelatihans = MataPelatihan::orderBy('nama_mata_pelatihan')->get();

        return view('kelas_mapel.create', compact('pelatihans', 'angkatans', 'ruangans', 'mataPelatihans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pelatihan_id' => 'required|exists:pelatihans,id',
            'angkatan_id' => 'required|exists:angkatans,id',
            'mata_pelatihan_id' => 'nullable|exists:mata_pelatihans,id',
            'ruangan_id' => 'nullable|exists:ruangans,id',
            'nama_kelas' => 'required|string|max:255',
            'kapasitas' => 'required|integer|min:1',
            'kurikulum' => 'required|string|max:50',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date',
            'jp' => 'required|numeric|min:0',
            'ada_kelompok' => 'required|in:ya,tidak',
            'hari' => 'nullable|string',
            'jam_mulai' => 'nullable',
            'jam_selesai' => 'nullable',
            'metode_pembelajaran' => 'required|string',
        ]);

        $kelasMapel = KelasMapel::create($validated);

        return redirect()->route('kelas-mapel.show', $kelasMapel->id)
            ->with('success', 'Data Kelas Mapel berhasil dibuat ! Silakan lengkapi Pengajar dan Jadwal.');
    }

    public function show(KelasMapel $kelasMapel, Request $request)
    {
        $kelasMapel->load(['pelatihan', 'angkatan', 'mataPelatihan', 'ruangan', 'pengajars', 'pesertas']);
        $tab = $request->get('tab', 'detail');

        $pelatihans = Pelatihan::orderBy('nama_pelatihan')->get();
        $angkatans = Angkatan::orderBy('nomor_angkatan')->get();
        $ruangans = Ruangan::where('is_aktif', true)->orderBy('nama_ruangan')->get();
        $mataPelatihans = MataPelatihan::where('angkatan_id', $kelasMapel->angkatan_id)->get();
        if ($mataPelatihans->isEmpty()) {
            $mataPelatihans = MataPelatihan::all();
        }

        $widyaiswaras = Widyaiswara::where('status', '!=', 'Nonaktif')->orderBy('nama_lengkap')->get();
        $narasumbers = Narasumber::where('status', 'Aktif')->orderBy('nama_lengkap')->get();

        // Linked Jadwal in SIR-JP via Mata Pelatihan
        $jadwals = Jadwal::whereHas('mataPelatihan', fn($q) => $q->where('angkatan_id', $kelasMapel->angkatan_id))
            ->orderBy('tanggal')
            ->get();

        // Peserta Diklat (baik yang langsung terhubung ke kelas mapel ini atau angkatan yang sama)
        $pesertas = Peserta::where(function($q) use ($kelasMapel) {
            $q->where('kelas_mapel_id', $kelasMapel->id)
              ->orWhere('angkatan_id', $kelasMapel->angkatan_id);
        })->orderBy('id')->get();

        return view('kelas_mapel.show', compact(
            'kelasMapel',
            'tab',
            'pelatihans',
            'angkatans',
            'ruangans',
            'mataPelatihans',
            'widyaiswaras',
            'narasumbers',
            'jadwals',
            'pesertas'
        ));
    }

    public function edit(KelasMapel $kelasMapel)
    {
        return redirect()->route('kelas-mapel.show', ['kelas_mapel' => $kelasMapel->id, 'tab' => 'detail']);
    }

    public function update(Request $request, KelasMapel $kelasMapel)
    {
        $validated = $request->validate([
            'pelatihan_id' => 'required|exists:pelatihans,id',
            'angkatan_id' => 'required|exists:angkatans,id',
            'mata_pelatihan_id' => 'nullable|exists:mata_pelatihans,id',
            'ruangan_id' => 'nullable|exists:ruangans,id',
            'nama_kelas' => 'required|string|max:255',
            'kapasitas' => 'required|integer|min:1',
            'kurikulum' => 'required|string|max:50',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date',
            'jp' => 'required|numeric|min:0',
            'ada_kelompok' => 'required|in:ya,tidak',
            'hari' => 'nullable|string',
            'jam_mulai' => 'nullable',
            'jam_selesai' => 'nullable',
            'metode_pembelajaran' => 'required|string',
        ]);

        $kelasMapel->update($validated);

        return redirect()->route('kelas-mapel.show', ['kelas_mapel' => $kelasMapel->id, 'tab' => 'detail'])
            ->with('success', 'Detail Kelas berhasil diperbarui !');
    }

    public function destroy(KelasMapel $kelasMapel)
    {
        $kelasMapel->delete();
        return redirect()->route('kelas-mapel.index')->with('success', 'Data Kelas Mapel berhasil dihapus !');
    }

    // Add Pengajar to Kelas Mapel (Tab 2)
    public function storePengajar(Request $request, KelasMapel $kelasMapel)
    {
        $validated = $request->validate([
            'pengajar_combo' => 'required|string', // format: "Widyaiswara:id" or "Narasumber:id"
            'jp' => 'required|numeric|min:0',
            'jadwal_keterangan' => 'nullable|string|max:255',
            'peran' => 'required|in:widyaiswara,narasumber,coach,mentor,evaluator',
            'kelompok' => 'nullable|string|max:100',
        ]);

        [$type, $id] = explode(':', $validated['pengajar_combo']);
        $modelClass = $type === 'Widyaiswara' ? Widyaiswara::class : Narasumber::class;

        KelasMapelPengajar::create([
            'kelas_mapel_id' => $kelasMapel->id,
            'pengajar_type' => $modelClass,
            'pengajar_id' => $id,
            'jp' => $validated['jp'],
            'jadwal_keterangan' => $validated['jadwal_keterangan'] ?? ($kelasMapel->hari . ' ' . $kelasMapel->jam_mulai . '-' . $kelasMapel->jam_selesai),
            'peran' => $validated['peran'],
            'kelompok' => $validated['kelompok'] ?? null,
        ]);

        // Otomatis sinkronkan ke Penugasan Pengajar SIR-JP jika mata pelatihan ada
        if ($kelasMapel->mata_pelatihan_id) {
            $isWi = ($type === 'Widyaiswara');
            PenugasanPengajar::firstOrCreate([
                'mata_pelatihan_id' => $kelasMapel->mata_pelatihan_id,
                'jenis_pengajar' => $type,
                'widyaiswara_id' => $isWi ? $id : null,
                'narasumber_id' => $isWi ? null : $id,
            ], [
                'peran' => 'Pengampu Utama',
                'keterangan' => 'Penugasan terintegrasi KCLC Kelas ' . $kelasMapel->nama_kelas . ' (' . ucfirst($validated['peran']) . ')',
            ]);
        }

        return redirect()->route('kelas-mapel.show', ['kelas_mapel' => $kelasMapel->id, 'tab' => 'pengajar'])
            ->with('success', 'Pengajar berhasil ditambahkan ke kelas ini !');
    }

    public function sinkronJadwal(KelasMapel $kelasMapel)
    {
        if (!$kelasMapel->mata_pelatihan_id) {
            return redirect()->back()->with('error', 'Kelas Mapel belum memiliki Mata Pelatihan terkait !');
        }

        $primaryPengajar = $kelasMapel->pengajars()->first();
        $penugasan = null;
        if ($primaryPengajar) {
            $isWi = ($primaryPengajar->pengajar_type === Widyaiswara::class);
            $penugasan = PenugasanPengajar::where('mata_pelatihan_id', $kelasMapel->mata_pelatihan_id)
                ->where('jenis_pengajar', $isWi ? 'Widyaiswara' : 'Narasumber')
                ->where($isWi ? 'widyaiswara_id' : 'narasumber_id', $primaryPengajar->pengajar_id)
                ->first();
        }

        $tanggal = $kelasMapel->tanggal_mulai ? $kelasMapel->tanggal_mulai->format('Y-m-d') : date('Y-m-d');
        $ruanganNama = $kelasMapel->ruangan ? $kelasMapel->ruangan->nama_ruangan : 'Ruang Kelas Online';
        $metode = $kelasMapel->metode_pembelajaran === 'Klasikal' ? 'Tatap Muka / Klasikal' : 'Daring / Online';

        $existingCount = Jadwal::where('mata_pelatihan_id', $kelasMapel->mata_pelatihan_id)->count();

        $jadwal = Jadwal::create([
            'mata_pelatihan_id' => $kelasMapel->mata_pelatihan_id,
            'penugasan_pengajar_id' => $penugasan?->id,
            'tanggal' => $tanggal,
            'jam_mulai' => $kelasMapel->jam_mulai ?? '08:00:00',
            'jam_selesai' => $kelasMapel->jam_selesai ?? '12:00:00',
            'sesi_ke' => $existingCount + 1,
            'jp_rencana' => $kelasMapel->jp > 0 ? $kelasMapel->jp : 4,
            'ruangan' => $ruanganNama,
            'metode' => $metode,
            'status' => 'Terjadwal',
            'keterangan' => 'Sesi KCLC ' . $kelasMapel->nama_kelas . ' (' . ($kelasMapel->hari ?? 'Jadwal Mingguan') . ')',
        ]);

        return redirect()->route('kelas-mapel.show', ['kelas_mapel' => $kelasMapel->id, 'tab' => 'jadwal'])
            ->with('success', 'Sesi Jadwal KCLC berhasil dibuat dan terhubung ke SIR-JP !');
    }

    public function destroyPengajar(KelasMapel $kelasMapel, KelasMapelPengajar $pengajar)
    {
        $pengajar->delete();
        return redirect()->route('kelas-mapel.show', ['kelas_mapel' => $kelasMapel->id, 'tab' => 'pengajar'])
            ->with('success', 'Pengajar berhasil dihapus dari kelas ini !');
    }
}
