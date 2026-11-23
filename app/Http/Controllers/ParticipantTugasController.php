<?php

namespace App\Http\Controllers;

use App\Models\Peserta;
use App\Models\Tugas;
use App\Models\PengumpulanTugas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ParticipantTugasController extends Controller
{
    protected function getAuthenticatedPeserta(): Peserta
    {
        $id = session('participant_id');
        return Peserta::with(['angkatan.pelatihan', 'kelasMapel.ruangan'])->findOrFail($id);
    }

    /**
     * Tampilkan halaman daftar tugas peserta (Individu & Kelompok).
     */
    public function index(Request $request)
    {
        $peserta = $this->getAuthenticatedPeserta();
        $kategori = $request->query('kategori', 'individu');
        if (!in_array($kategori, ['individu', 'kelompok'])) {
            $kategori = 'individu';
        }

        $perPage = (int) $request->query('per_page', 10);
        if ($perPage < 1 || $perPage > 100) {
            $perPage = 10;
        }

        $search = trim($request->query('search', ''));

        // Query tugas sesuai kategori dan relevansi diklat/angkatan peserta
        $query = Tugas::query()
            ->where('kategori', $kategori)
            ->with(['pengumpulan' => function ($q) use ($peserta) {
                $q->where('peserta_id', $peserta->id);
            }]);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('nama_mata_pelatihan', 'like', "%{$search}%")
                  ->orWhere('penyelenggara', 'like', "%{$search}%");
            });
        }

        $tugasList = $query->orderBy('tanggal_mulai', 'desc')->paginate($perPage)->withQueryString();

        // Hitung total untuk badges/info
        $countIndividu = Tugas::where('kategori', 'individu')->count();
        $countKelompok = Tugas::where('kategori', 'kelompok')->count();

        return view('portal.tugas.index', compact(
            'peserta',
            'tugasList',
            'kategori',
            'search',
            'perPage',
            'countIndividu',
            'countKelompok'
        ));
    }

    /**
     * Upload / Update pengumpulan tugas peserta.
     */
    public function upload(Request $request, int $id)
    {
        $peserta = $this->getAuthenticatedPeserta();
        $tugas = Tugas::findOrFail($id);

        $request->validate([
            'file_jawaban' => 'required|file|mimes:pdf,doc,docx,zip,png,jpg,jpeg|max:10240',
            'catatan'      => 'nullable|string|max:1000',
        ], [
            'file_jawaban.required' => 'File tugas wajib dipilih.',
            'file_jawaban.mimes'    => 'Format file harus berupa PDF, DOC, DOCX, ZIP, PNG, atau JPG.',
            'file_jawaban.max'      => 'Ukuran file maksimal adalah 10 MB.',
        ]);

        $file = $request->file('file_jawaban');
        $originalName = $file->getClientOriginalName();
        $fileName = 'tugas_' . $tugas->id . '_peserta_' . $peserta->id . '_' . time() . '.' . $file->getClientOriginalExtension();
        
        // Simpan file ke storage privat non-publik
        $path = $file->storeAs('tugas_submissions/' . $tugas->id, $fileName);

        // Hapus file lama jika ada
        $existing = PengumpulanTugas::where('tugas_id', $tugas->id)
            ->where('peserta_id', $peserta->id)
            ->first();

        if ($existing && $existing->file_jawaban && Storage::exists($existing->file_jawaban)) {
            Storage::delete($existing->file_jawaban);
        }

        PengumpulanTugas::updateOrCreate(
            [
                'tugas_id'   => $tugas->id,
                'peserta_id' => $peserta->id,
            ],
            [
                'file_jawaban'   => $path,
                'nama_file_asli' => $originalName,
                'catatan'        => $request->input('catatan'),
                'waktu_upload'   => now(),
                'status'         => 'dikumpulkan',
            ]
        );

        return redirect()->back()->with('success', 'Tugas "' . $tugas->judul . '" berhasil diunggah!');
    }

    /**
     * Download file tugas yang telah dikumpulkan oleh peserta yang bersangkutan.
     */
    public function download(int $id)
    {
        $peserta = $this->getAuthenticatedPeserta();
        $pengumpulan = PengumpulanTugas::where('tugas_id', $id)
            ->where('peserta_id', $peserta->id)
            ->firstOrFail();

        if (!Storage::exists($pengumpulan->file_jawaban)) {
            abort(404, 'File tugas tidak ditemukan di server.');
        }

        return Storage::download($pengumpulan->file_jawaban, $pengumpulan->nama_file_asli ?? 'tugas_jawaban.pdf');
    }
}
