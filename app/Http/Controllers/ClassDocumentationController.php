<?php

namespace App\Http\Controllers;

use App\Models\ClassDocumentation;
use App\Models\AttendanceSession;
use App\Models\KelasMapel;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ClassDocumentationController extends Controller
{
    public function index(Request $request)
    {
        $kelasMapelId = $request->get('kelas_mapel_id');
        $sessionId = $request->get('attendance_session_id');
        $kategori = $request->get('kategori');

        $query = ClassDocumentation::with(['session.jadwal.mataPelatihan', 'kelasMapel.pelatihan', 'uploader']);

        if ($kelasMapelId) {
            $query->where('kelas_mapel_id', $kelasMapelId);
        }

        if ($sessionId) {
            $query->where('attendance_session_id', $sessionId);
        }

        if ($kategori) {
            $query->where('kategori', $kategori);
        }

        $documentations = $query->latest()->paginate(16)->withQueryString();
        $kelasMapels = KelasMapel::with(['pelatihan', 'angkatan'])->orderBy('nama_kelas')->get();
        $sessions = AttendanceSession::with('jadwal.mataPelatihan')->latest('tanggal')->take(30)->get();

        return view('presensi.documentations.index', compact('documentations', 'kelasMapels', 'sessions', 'kelasMapelId', 'sessionId', 'kategori'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kelas_mapel_id' => 'required|exists:kelas_mapels,id',
            'attendance_session_id' => 'nullable|exists:attendance_sessions,id',
            'kategori' => 'required|in:Kegiatan,Pengajar,Peserta,Praktik,Diskusi',
            'keterangan' => 'nullable|string|max:1000',
            'foto' => 'required|image|mimes:jpeg,png,jpg|max:5120',
        ], [
            'kelas_mapel_id.required' => 'Pilih kelas pembelajaran.',
            'kategori.required' => 'Pilih kategori foto dokumentasi.',
            'foto.required' => 'Pilih berkas foto kegiatan.',
            'foto.image' => 'File harus berupa gambar foto (JPEG, PNG, JPG).',
            'foto.max' => 'Ukuran foto maksimal 5 MB.',
        ]);

        $file = $request->file('foto');
        $origName = $file->getClientOriginalName();
        $safeName = 'doc_' . time() . '_' . Str::random(12) . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('class_documentations', $safeName, 'local');

        $doc = ClassDocumentation::create([
            'kelas_mapel_id' => $validated['kelas_mapel_id'],
            'attendance_session_id' => $validated['attendance_session_id'] ?? null,
            'nama_file_asli' => $origName,
            'path_file' => $path,
            'mime_type' => $file->getClientMimeType() ?: 'image/jpeg',
            'kategori' => $validated['kategori'],
            'keterangan' => $validated['keterangan'] ?? null,
            'uploaded_by' => auth()->id(),
        ]);

        LogAktivitas::catat('Dokumentasi', 'Upload Foto Kegiatan', "Mengunggah foto dokumentasi {$doc->kategori} untuk kelas #{$doc->kelas_mapel_id}");

        return back()->with('success', 'Foto dokumentasi kegiatan berhasil diunggah!');
    }

    public function viewFile(ClassDocumentation $classDocumentation)
    {
        if (!Storage::disk('local')->exists($classDocumentation->path_file)) {
            abort(404, 'Berkas foto tidak ditemukan di server.');
        }

        return Storage::disk('local')->response(
            $classDocumentation->path_file,
            $classDocumentation->nama_file_asli,
            ['Content-Type' => $classDocumentation->mime_type]
        );
    }

    public function destroy(ClassDocumentation $classDocumentation)
    {
        $id = $classDocumentation->id;
        $kategori = $classDocumentation->kategori;

        if (Storage::disk('local')->exists($classDocumentation->path_file)) {
            Storage::disk('local')->delete($classDocumentation->path_file);
        }

        $classDocumentation->delete();

        LogAktivitas::catat('Dokumentasi', 'Hapus Foto Kegiatan', "Menghapus foto dokumentasi {$kategori} ID #{$id}");

        return back()->with('success', 'Foto dokumentasi berhasil dihapus.');
    }
}
