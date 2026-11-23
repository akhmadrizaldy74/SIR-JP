<?php

namespace App\Http\Controllers;

use App\Services\AttendanceService;
use App\Models\AttendanceRequest;
use App\Models\AttendanceEvidence;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AttendanceRequestController extends Controller
{
    protected AttendanceService $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    public function index(Request $request)
    {
        $status = $request->get('status', 'Pending');
        $search = $request->get('search');

        $query = AttendanceRequest::with([
            'peserta.angkatan.pelatihan',
            'session.jadwal.mataPelatihan',
            'evidences',
            'verifikator'
        ]);

        if ($status && $status !== 'Semua') {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('peserta', function ($p) use ($search) {
                    $p->where('nama', 'like', "%{$search}%")
                      ->orWhere('nip', 'like', "%{$search}%");
                })->orWhere('alasan', 'like', "%{$search}%");
            });
        }

        $requests = $query->latest()->paginate(15)->withQueryString();

        $countPending = AttendanceRequest::where('status', 'Pending')->count();
        $countApproved = AttendanceRequest::where('status', 'Approved')->count();
        $countRejected = AttendanceRequest::where('status', 'Rejected')->count();

        return view('presensi.requests.index', compact('requests', 'status', 'search', 'countPending', 'countApproved', 'countRejected'));
    }

    public function verify(Request $request, AttendanceRequest $attendanceRequest)
    {
        $validated = $request->validate([
            'keputusan' => 'required|in:Approved,Rejected',
            'catatan_admin' => 'nullable|string|max:1000',
        ], [
            'keputusan.required' => 'Keputusan verifikasi (Setujui / Tolak) harus dipilih.',
        ]);

        if ($validated['keputusan'] === 'Rejected' && empty(trim($validated['catatan_admin'] ?? ''))) {
            return back()->withErrors(['catatan_admin' => 'Alasan penolakan wajib dicatat ketika pengajuan ditolak!']);
        }

        $this->attendanceService->verifyAttendanceRequest(
            $attendanceRequest,
            $validated['keputusan'],
            $validated['catatan_admin'] ?? null,
            auth()->id()
        );

        $statusId = $validated['keputusan'] === 'Approved' ? 'disetujui' : 'ditolak';
        LogAktivitas::catat('Presensi', 'Verifikasi Izin', "Pengajuan {$attendanceRequest->jenis_pengajuan} dari {$attendanceRequest->peserta->nama} telah {$statusId}");

        return back()->with('success', "Pengajuan {$attendanceRequest->jenis_pengajuan} {$attendanceRequest->peserta->nama} berhasil {$statusId}!");
    }

    public function downloadEvidence(AttendanceEvidence $evidence)
    {
        if (!Storage::disk('local')->exists($evidence->path_file)) {
            abort(404, 'Berkas bukti tidak ditemukan pada server.');
        }

        return Storage::disk('local')->response(
            $evidence->path_file,
            $evidence->nama_file_asli,
            ['Content-Type' => $evidence->mime_type]
        );
    }
}
