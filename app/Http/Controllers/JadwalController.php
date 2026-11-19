<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use App\Models\MataPelatihan;
use App\Models\PenugasanPengajar;
use App\Models\Pelatihan;
use App\Models\PengaturanSesi;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class JadwalController extends Controller
{
    public function index(Request $request)
    {
        $bulan = (int) $request->get('bulan', Carbon::now()->month);
        $tahun = (int) $request->get('tahun', Carbon::now()->year);
        $status = $request->get('status');
        $pelatihanId = $request->get('pelatihan_id');
        $viewMode = $request->get('view', 'tabel'); // 'tabel' atau 'kalender'

        $query = Jadwal::with([
            'mataPelatihan.angkatan.pelatihan',
            'penugasanPengajar.widyaiswara',
            'penugasanPengajar.narasumber',
            'realisasi'
        ]);

        if ($bulan) {
            $query->whereMonth('tanggal', $bulan);
        }

        if ($tahun) {
            $query->whereYear('tanggal', $tahun);
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($pelatihanId) {
            $query->whereHas('mataPelatihan.angkatan', function ($q) use ($pelatihanId) {
                $q->where('pelatihan_id', $pelatihanId);
            });
        }

        $jadwals = $query->orderBy('tanggal', 'desc')->orderBy('jam_mulai', 'asc')->paginate(15)->withQueryString();

        // Data untuk Kalender jika view mode kalender
        $allJadwalsForCalendar = Jadwal::with([
            'mataPelatihan.angkatan.pelatihan',
            'penugasanPengajar.widyaiswara',
            'penugasanPengajar.narasumber'
        ])
        ->whereMonth('tanggal', $bulan)
        ->whereYear('tanggal', $tahun)
        ->get();

        $pelatihans = Pelatihan::orderBy('nama_pelatihan', 'asc')->get();
        $templateSesies = PengaturanSesi::where('is_active', true)->orderBy('urutan', 'asc')->get();

        return view('jadwal.index', compact(
            'jadwals',
            'allJadwalsForCalendar',
            'pelatihans',
            'templateSesies',
            'bulan',
            'tahun',
            'status',
            'pelatihanId',
            'viewMode'
        ));
    }

    public function create(Request $request)
    {
        $mataPelatihans = MataPelatihan::with(['angkatan.pelatihan', 'penugasans.widyaiswara', 'penugasans.narasumber'])
            ->orderBy('nama_mata_pelatihan', 'asc')
            ->get();

        $templateSesies = PengaturanSesi::where('is_active', true)
            ->where('tipe', 'Pembelajaran')
            ->orderBy('urutan', 'asc')
            ->get();

        $selectedMataPelatihanId = $request->get('mata_pelatihan_id');

        return view('jadwal.create', compact('mataPelatihans', 'templateSesies', 'selectedMataPelatihanId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'mata_pelatihan_id' => ['required', 'exists:mata_pelatihans,id'],
            'penugasan_pengajar_id' => ['nullable', 'exists:penugasan_pengajars,id'],
            'tanggal' => ['required', 'date'],
            'metode_input' => ['required', 'in:template,manual'],
            'sesi_id' => ['nullable', 'required_if:metode_input,template'],
            'jam_mulai' => ['nullable', 'required_if:metode_input,manual', 'date_format:H:i'],
            'jam_selesai' => ['nullable', 'required_if:metode_input,manual', 'date_format:H:i', 'after:jam_mulai'],
            'jp_rencana' => ['nullable', 'numeric', 'min:0.5'],
            'ruangan' => ['nullable', 'string', 'max:100'],
            'metode' => ['required', 'in:Tatap Muka / Klasikal,Daring / Online,Blended'],
            'status' => ['required', 'in:Terjadwal,Terlaksana,Dibatalkan'],
            'keterangan' => ['nullable', 'string'],
        ], [
            'mata_pelatihan_id.required' => 'Mata pelatihan wajib dipilih.',
            'tanggal.required' => 'Tanggal jadwal wajib diisi.',
            'jam_selesai.after' => 'Jam selesai harus lebih akhir dari jam mulai.',
        ]);

        DB::beginTransaction();
        try {
            if ($validated['metode_input'] === 'template') {
                $sesi = PengaturanSesi::findOrFail($validated['sesi_id']);
                $jamMulai = $sesi->jam_mulai;
                $jamSelesai = $sesi->jam_selesai;
                $jp = $sesi->jumlah_jp > 0 ? $sesi->jumlah_jp : 1.00;
                $nomorSesi = $sesi->nomor_sesi;
            } else {
                $jamMulai = $validated['jam_mulai'] . ':00';
                $jamSelesai = $validated['jam_selesai'] . ':00';

                // Hitung otomatis JP bila user tidak mengisi manual (1 sesi 45 menit = 1 JP)
                if (!empty($validated['jp_rencana'])) {
                    $jp = (float) $validated['jp_rencana'];
                } else {
                    $start = Carbon::parse($jamMulai);
                    $end = Carbon::parse($jamSelesai);
                    $menit = $start->diffInMinutes($end);
                    $jp = round($menit / 45, 1);
                }
                $nomorSesi = null;
            }

            $jadwal = Jadwal::create([
                'mata_pelatihan_id' => $validated['mata_pelatihan_id'],
                'penugasan_pengajar_id' => $validated['penugasan_pengajar_id'] ?? null,
                'tanggal' => $validated['tanggal'],
                'jam_mulai' => $jamMulai,
                'jam_selesai' => $jamSelesai,
                'sesi_ke' => $nomorSesi,
                'jp_rencana' => $jp,
                'ruangan' => $validated['ruangan'] ?? 'Ruang Kelas',
                'metode' => $validated['metode'],
                'status' => $validated['status'],
                'keterangan' => $validated['keterangan'] ?? null,
            ]);

            LogAktivitas::catat('Jadwal Pelatihan', 'Buat Jadwal', 'Menambahkan jadwal pada ' . Carbon::parse($jadwal->tanggal)->format('d/m/Y') . ' (' . $jp . ' JP)');

            DB::commit();
            return redirect()->route('jadwal.index')->with('success', 'Jadwal pembelajaran berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal menyimpan jadwal: ' . $e->getMessage());
        }
    }

    public function edit(Jadwal $jadwal)
    {
        $jadwal->load(['mataPelatihan.angkatan.pelatihan', 'penugasanPengajar']);
        $mataPelatihans = MataPelatihan::with(['angkatan.pelatihan'])->orderBy('nama_mata_pelatihan', 'asc')->get();
        $penugasans = PenugasanPengajar::with(['widyaiswara', 'narasumber'])
            ->where('mata_pelatihan_id', $jadwal->mata_pelatihan_id)
            ->get();

        return view('jadwal.edit', compact('jadwal', 'mataPelatihans', 'penugasans'));
    }

    public function update(Request $request, Jadwal $jadwal)
    {
        $validated = $request->validate([
            'mata_pelatihan_id' => ['required', 'exists:mata_pelatihans,id'],
            'penugasan_pengajar_id' => ['nullable', 'exists:penugasan_pengajars,id'],
            'tanggal' => ['required', 'date'],
            'jam_mulai' => ['required'],
            'jam_selesai' => ['required'],
            'jp_rencana' => ['required', 'numeric', 'min:0.5'],
            'ruangan' => ['nullable', 'string', 'max:100'],
            'metode' => ['required', 'in:Tatap Muka / Klasikal,Daring / Online,Blended'],
            'status' => ['required', 'in:Terjadwal,Terlaksana,Dibatalkan'],
            'keterangan' => ['nullable', 'string'],
        ]);

        $jadwal->update($validated);

        LogAktivitas::catat('Jadwal Pelatihan', 'Ubah Jadwal', 'Memperbarui jadwal id #' . $jadwal->id);

        return redirect()->route('jadwal.index')->with('success', 'Jadwal pembelajaran berhasil diperbarui.');
    }

    public function destroy(Jadwal $jadwal)
    {
        $jadwal->delete();
        LogAktivitas::catat('Jadwal Pelatihan', 'Hapus Jadwal', 'Menghapus jadwal id #' . $jadwal->id);
        return redirect()->route('jadwal.index')->with('success', 'Jadwal pembelajaran berhasil dihapus.');
    }

    /**
     * API JSON: Dapatkan daftar pengajar yang ditugaskan pada mata pelatihan tertentu
     */
    public function getPengajarsByMataPelatihan($mataPelatihanId)
    {
        $penugasans = PenugasanPengajar::with(['widyaiswara', 'narasumber'])
            ->where('mata_pelatihan_id', $mataPelatihanId)
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'nama' => $p->pengajar_nama,
                    'jenis' => $p->jenis_pengajar,
                    'peran' => $p->peran,
                ];
            });

        return response()->json($penugasans);
    }
}
