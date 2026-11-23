<?php

namespace App\Http\Controllers;

use App\Services\AttendanceService;
use App\Models\AttendanceSession;
use App\Models\Peserta;
use App\Models\Presensi;
use App\Models\AttendanceLog;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;

class AttendanceManualController extends Controller
{
    protected AttendanceService $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    public function index(Request $request, AttendanceSession $attendanceSession)
    {
        $attendanceSession->load(['jadwal.mataPelatihan', 'kelasMapel.ruangan']);

        $search = $request->get('search');
        $filterStatus = $request->get('status');

        $stats = $this->attendanceService->getSessionStatistics($attendanceSession);
        $allPesertas = $stats['pesertas'];
        $presensis = $stats['presensis'];

        // Filter search dan status di level collection
        $filteredPesertas = $allPesertas->filter(function ($peserta) use ($search, $filterStatus, $presensis) {
            if ($search) {
                $match = str_contains(strtolower($peserta->nama), strtolower($search)) ||
                         str_contains(strtolower($peserta->nip), strtolower($search)) ||
                         str_contains(strtolower($peserta->instansi ?? ''), strtolower($search));
                if (!$match) return false;
            }

            if ($filterStatus) {
                $currentStatus = isset($presensis[$peserta->id]) ? $presensis[$peserta->id]->status_kehadiran : 'Belum Presensi';
                if ($currentStatus !== $filterStatus) {
                    return false;
                }
            }

            return true;
        });

        return view('presensi.manual.index', compact('attendanceSession', 'filteredPesertas', 'presensis', 'stats', 'search', 'filterStatus'));
    }

    public function updateSingle(Request $request, AttendanceSession $attendanceSession)
    {
        $validated = $request->validate([
            'peserta_id' => 'required|exists:pesertas,id',
            'status' => 'required|in:Hadir,Terlambat,Izin,Sakit,Alpa,Dispensasi',
            'keterangan' => 'nullable|string|max:500',
            'alasan_perubahan' => 'required|string|max:500',
        ], [
            'status.required' => 'Pilih status kehadiran.',
            'alasan_perubahan.required' => 'Alasan perubahan/koreksi kehadiran wajib dicatat untuk audit.',
        ]);

        $peserta = Peserta::findOrFail($validated['peserta_id']);

        $presensi = $this->attendanceService->recordManualAttendance(
            $attendanceSession,
            $peserta->id,
            $validated['status'],
            $validated['keterangan'] ?? null,
            auth()->id(),
            $validated['alasan_perubahan']
        );

        LogAktivitas::catat('Presensi', 'Koreksi Presensi Manual', "Mengubah status presensi {$peserta->nama} menjadi {$validated['status']} pada sesi #{$attendanceSession->id}");

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Status kehadiran berhasil diperbarui!',
                'status' => $validated['status'],
                'badge' => $presensi->status_badge,
            ]);
        }

        return back()->with('success', "Status kehadiran {$peserta->nama} berhasil diubah menjadi {$validated['status']}!");
    }

    public function bulkUpdate(Request $request, AttendanceSession $attendanceSession)
    {
        $validated = $request->validate([
            'peserta_ids' => 'required|array|min:1',
            'peserta_ids.*' => 'exists:pesertas,id',
            'target_status' => 'required|in:Hadir,Terlambat,Izin,Sakit,Alpa,Dispensasi',
            'alasan_perubahan' => 'required|string|max:500',
        ], [
            'peserta_ids.required' => 'Pilih minimal satu peserta yang akan diperbarui.',
            'target_status.required' => 'Pilih target status kehadiran.',
            'alasan_perubahan.required' => 'Alasan pembaruan massal wajib diisi.',
        ]);

        $list = [];
        foreach ($validated['peserta_ids'] as $id) {
            $list[] = [
                'peserta_id' => (int) $id,
                'status' => $validated['target_status'],
                'keterangan' => 'Pembaruan massal oleh admin: ' . $validated['alasan_perubahan'],
            ];
        }

        $count = $this->attendanceService->bulkUpdateManual(
            $attendanceSession,
            $list,
            $validated['alasan_perubahan'],
            auth()->id()
        );

        LogAktivitas::catat('Presensi', 'Pembaruan Massal Presensi', "Memperbarui {$count} peserta menjadi {$validated['target_status']} pada sesi #{$attendanceSession->id}");

        return back()->with('success', "Berhasil memperbarui kehadiran {$count} peserta secara massal menjadi {$validated['target_status']}!");
    }

    public function getLogs(AttendanceSession $attendanceSession, Peserta $peserta)
    {
        $logs = AttendanceLog::with('user')
            ->where('attendance_session_id', $attendanceSession->id)
            ->where('peserta_id', $peserta->id)
            ->latest('created_at')
            ->get();

        return response()->json([
            'peserta' => $peserta,
            'logs' => $logs->map(fn($l) => [
                'id' => $l->id,
                'status_sebelum' => $l->status_sebelum ?: 'Belum Presensi',
                'status_sesudah' => $l->status_sesudah,
                'alasan' => $l->alasan_perubahan,
                'metode' => $l->metode_input,
                'oleh' => $l->user ? $l->user->name : ($l->metode_input === 'QR' ? 'Mandiri (Peserta)' : 'Sistem'),
                'waktu' => $l->created_at ? $l->created_at->format('d/m/Y H:i:s') : '-',
                'ip' => $l->ip_address,
            ]),
        ]);
    }
}
