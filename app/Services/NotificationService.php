<?php

namespace App\Services;

use App\Models\User;
use App\Models\Peserta;
use App\Models\AttendanceRequest;
use App\Models\AttendanceSession;
use App\Models\PengumpulanTugas;
use App\Models\Tugas;
use App\Models\Jadwal;
use Carbon\Carbon;

class NotificationService
{
    /**
     * Dapatkan daftar notifikasi cerdas berbasis peran pengguna aktif (Role-based).
     */
    public function getNotificationsForUser($user = null): array
    {
        $user = $user ?? auth()->user();
        if (!$user) {
            // Jika login sebagai peserta portal (via session participant_id)
            if (session()->has('participant_id')) {
                $pesertaId = session('participant_id');
                $peserta = Peserta::find($pesertaId);
                return $this->getPesertaNotifications($peserta);
            }
            return [];
        }

        // 1. Role: GUEST
        if ($user->isGuest()) {
            return $this->getGuestNotifications($user);
        }

        $activeRole = strtolower($user->getActiveRole());

        // 2. Role: PESERTA
        if ($user->isPeserta() || $activeRole === 'peserta' || $activeRole === 'peserta diklat') {
            $peserta = Peserta::where('email', $user->email)->orWhere('nip', $user->nik)->first();
            return $this->getPesertaNotifications($peserta);
        }

        // 3. Role: PENGAJAR / WIDYAISWARA
        if ($user->isPengajar() || $activeRole === 'pengajar' || $user->role === 'widyaiswara') {
            return $this->getPengajarNotifications($user);
        }

        // 4. Role: ADMINISTRATOR / PENYELENGGARA
        return $this->getAdminNotifications($user);
    }

    /**
     * Hitung jumlah notifikasi yang belum dibaca.
     */
    public function getUnreadCount($user = null): int
    {
        $notifications = $this->getNotificationsForUser($user);
        $readIds = session('read_notifications', []);
        $readAt = session('notifications_read_at');

        $unread = 0;
        foreach ($notifications as $notif) {
            if (in_array($notif['id'], $readIds)) {
                continue;
            }
            if ($readAt && isset($notif['created_at']) && $notif['created_at'] <= $readAt) {
                continue;
            }
            $unread++;
        }

        return $unread;
    }

    /**
     * Notifikasi untuk Administrator & Penyelenggara.
     */
    protected function getAdminNotifications(User $user): array
    {
        $notifications = [];

        // A. Akun Guest Baru Menunggu Penetapan Peran (Ambil ID terbaru)
        $guestUsers = User::where(function ($q) {
            $q->where('role', 'guest')
              ->orWhereJsonContains('roles', 'Guest')
              ->orWhereJsonContains('roles', 'guest');
        })
        ->orderByDesc('id')
        ->take(25)
        ->get()
        ->filter(function ($u) {
            return $u->isGuest();
        });

        foreach ($guestUsers as $guest) {
            $notifications[] = [
                'id' => 'guest_' . $guest->id,
                'title' => 'Akun Guest Baru Terdaftar',
                'message' => "{$guest->name} mendaftar sebagai Guest & menunggu penetapan peran hak akses.",
                'url' => route('users.index', ['role' => 'guest']),
                'time_formatted' => $this->formatDiffForHumans($guest->created_at),
                'created_at' => $guest->created_at,
                'icon' => 'bi-person-plus-fill',
                'bg_color' => '#FEF3C7',
                'text_color' => '#B45309',
                'badge' => 'Guest Baru',
            ];
        }

        // B. Permohonan Izin / Sakit Presensi Menunggu Verifikasi
        try {
            $pendingRequests = AttendanceRequest::where('status', 'Pending')
                ->with(['peserta', 'session'])
                ->latest()
                ->take(5)
                ->get();

            foreach ($pendingRequests as $req) {
                $namaPeserta = $req->peserta->nama ?? 'Peserta Diklat';
                $jenis = $req->jenis_pengajuan;
                $notifications[] = [
                    'id' => 'att_req_' . $req->id,
                    'title' => "Permohonan {$jenis} Presensi",
                    'message' => "{$namaPeserta} mengajukan {$jenis}: \"" . \Illuminate\Support\Str::limit($req->alasan, 35) . "\"",
                    'url' => route('presensi.index'),
                    'time_formatted' => $this->formatDiffForHumans($req->created_at),
                    'created_at' => $req->created_at,
                    'icon' => 'bi-envelope-exclamation-fill',
                    'bg_color' => '#EDE9FE',
                    'text_color' => '#6D28D9',
                    'badge' => "Izin {$jenis}",
                ];
            }
        } catch (\Throwable $e) {
            // Fail-safe jika tabel hybrid attendance belum terisi
        }

        // C. Tugas Baru Dikumpulkan oleh Peserta
        try {
            $tugasSubmissions = PengumpulanTugas::with(['peserta', 'tugas'])
                ->latest()
                ->take(3)
                ->get();

            foreach ($tugasSubmissions as $sub) {
                $namaPeserta = $sub->peserta->nama ?? 'Peserta';
                $judulTugas = $sub->tugas->judul ?? 'Tugas Mandiri';
                $notifications[] = [
                    'id' => 'sub_tugas_' . $sub->id,
                    'title' => 'Tugas Dikumpulkan',
                    'message' => "{$namaPeserta} telah mengunggah berkas untuk: \"{$judulTugas}\"",
                    'url' => route('pelatihan.index'),
                    'time_formatted' => $this->formatDiffForHumans($sub->waktu_upload ?? $sub->created_at),
                    'created_at' => $sub->waktu_upload ?? $sub->created_at,
                    'icon' => 'bi-file-earmark-arrow-up-fill',
                    'bg_color' => '#E0F2FE',
                    'text_color' => '#0369A1',
                    'badge' => 'Tugas',
                ];
            }
        } catch (\Throwable $e) {
            // Fail-safe
        }

        return $this->sortByLatest($notifications);
    }

    /**
     * Notifikasi untuk Pengajar / Widyaiswara.
     */
    protected function getPengajarNotifications(User $user): array
    {
        $notifications = [];

        // A. Pengumpulan tugas oleh peserta yang perlu diperiksa
        try {
            $submissions = PengumpulanTugas::with(['peserta', 'tugas'])
                ->latest()
                ->take(5)
                ->get();

            foreach ($submissions as $sub) {
                $nama = $sub->peserta->nama ?? 'Peserta';
                $judul = $sub->tugas->judul ?? 'Tugas Pembelajaran';
                $notifications[] = [
                    'id' => 'pengajar_tugas_' . $sub->id,
                    'title' => 'Tugas Masuk untuk Diperiksa',
                    'message' => "{$nama} telah mengumpulkan tugas \"{$judul}\".",
                    'url' => route('portal.tugas.index'),
                    'time_formatted' => $this->formatDiffForHumans($sub->waktu_upload ?? $sub->created_at),
                    'created_at' => $sub->waktu_upload ?? $sub->created_at,
                    'icon' => 'bi-clipboard-check-fill',
                    'bg_color' => '#E0F2FE',
                    'text_color' => '#0284C7',
                    'badge' => 'Perlu Dinilai',
                ];
            }
        } catch (\Throwable $e) {
        }

        // B. Jadwal sesi mengajar terdekat
        try {
            $jadwalMendatang = Jadwal::with(['mataPelatihan'])
                ->whereDate('tanggal', '>=', now()->toDateString())
                ->orderBy('tanggal')
                ->take(3)
                ->get();

            foreach ($jadwalMendatang as $jdw) {
                $mapel = $jdw->mataPelatihan->nama_mata_pelatihan ?? 'Mata Pelatihan';
                $tgl = date('d/m/Y', strtotime($jdw->tanggal));
                $jam = substr($jdw->jam_mulai, 0, 5) . ' - ' . substr($jdw->jam_selesai, 0, 5);
                $notifications[] = [
                    'id' => 'jdw_' . $jdw->id,
                    'title' => 'Jadwal Mengajar Mendatang',
                    'message' => "{$mapel} ({$tgl}, {$jam} WIB)",
                    'url' => route('jadwal.index'),
                    'time_formatted' => $this->formatDiffForHumans($jdw->created_at),
                    'created_at' => $jdw->created_at,
                    'icon' => 'bi-calendar-event-fill',
                    'bg_color' => '#DCFCE7',
                    'text_color' => '#15803D',
                    'badge' => 'Jadwal',
                ];
            }
        } catch (\Throwable $e) {
        }

        return $this->sortByLatest($notifications);
    }

    /**
     * Notifikasi untuk Peserta Pelatihan.
     */
    protected function getPesertaNotifications(?Peserta $peserta): array
    {
        $notifications = [];

        // A. Sesi Presensi Aktif Terbuka & Link Zoom
        try {
            $activeSessionsQuery = AttendanceSession::with('jadwal.mataPelatihan')
                ->where('status', 'Aktif');

            if ($peserta && $peserta->angkatan_id) {
                $activeSessionsQuery->where(function($q) use ($peserta) {
                    $q->whereHas('kelasMapel', fn($k) => $k->where('angkatan_id', $peserta->angkatan_id))
                      ->orWhereHas('jadwal.mataPelatihan', fn($m) => $m->where('angkatan_id', $peserta->angkatan_id));
                });
            }

            $activeSessions = $activeSessionsQuery->latest()->take(3)->get();
            foreach ($activeSessions as $as) {
                $judul = $as->judul_sesi;
                $hasZoom = !empty($as->link_meeting);
                $notifications[] = [
                    'id' => 'sesi_pres_' . $as->id,
                    'title' => $hasZoom ? 'Tautan Zoom & Presensi Dibuka!' : 'Sesi Presensi Aktif Dibuka!',
                    'message' => $hasZoom 
                        ? "Pengajar telah membagikan tautan Zoom untuk {$judul}. Silakan masuk ke kelas daring dan lakukan presensi."
                        : "Sesi presensi {$judul} sedang dibuka. Silakan lakukan absensi sekarang.",
                    'url' => route('portal.dashboard'),
                    'time_formatted' => $this->formatDiffForHumans($as->waktu_buka_presensi ?? $as->created_at),
                    'created_at' => $as->waktu_buka_presensi ?? $as->created_at,
                    'icon' => $hasZoom ? 'bi-camera-video-fill' : 'bi-qr-code-scan',
                    'bg_color' => '#DCFCE7',
                    'text_color' => '#15803D',
                    'badge' => $hasZoom ? 'Zoom & Presensi' : 'Presensi Dibuka',
                ];
            }
        } catch (\Throwable $e) {
        }

        // B. Status Pengajuan Izin / Sakit Peserta
        if ($peserta) {
            try {
                $myRequests = AttendanceRequest::where('peserta_id', $peserta->id)->latest()->take(3)->get();
                foreach ($myRequests as $req) {
                    $statusText = $req->status === 'Approved' ? 'Disetujui' : ($req->status === 'Rejected' ? 'Ditolak' : 'Sedang Diproses');
                    $bg = $req->status === 'Approved' ? '#DCFCE7' : ($req->status === 'Rejected' ? '#FEE2E2' : '#FEF3C7');
                    $tc = $req->status === 'Approved' ? '#15803D' : ($req->status === 'Rejected' ? '#B91C1C' : '#B45309');
                    $icon = $req->status === 'Approved' ? 'bi-check-circle-fill' : ($req->status === 'Rejected' ? 'bi-x-circle-fill' : 'bi-hourglass-split');

                    $notifications[] = [
                        'id' => 'my_req_' . $req->id,
                        'title' => "Status Izin: {$statusText}",
                        'message' => "Pengajuan {$req->jenis_pengajuan} Anda telah {$statusText}." . ($req->catatan_admin ? " Catatan: {$req->catatan_admin}" : ''),
                        'url' => route('portal.dashboard'),
                        'time_formatted' => $this->formatDiffForHumans($req->diverifikasi_pada ?? $req->created_at),
                        'created_at' => $req->diverifikasi_pada ?? $req->created_at,
                        'icon' => $icon,
                        'bg_color' => $bg,
                        'text_color' => $tc,
                        'badge' => $req->status,
                    ];
                }
            } catch (\Throwable $e) {
            }
        }

        // C. Tugas Baru Tersedia
        try {
            $tugasList = Tugas::latest()->take(3)->get();
            foreach ($tugasList as $t) {
                $deadline = $t->batas_waktu ? date('d/m/Y H:i', strtotime($t->batas_waktu)) : 'Tidak ada batas';
                $notifications[] = [
                    'id' => 'new_tugas_' . $t->id,
                    'title' => 'Tugas Pembelajaran Baru',
                    'message' => "Tugas: {$t->judul}. Batas pengumpulan: {$deadline} WIB.",
                    'url' => route('portal.tugas.index'),
                    'time_formatted' => $this->formatDiffForHumans($t->created_at),
                    'created_at' => $t->created_at,
                    'icon' => 'bi-journal-check',
                    'bg_color' => '#E0F2FE',
                    'text_color' => '#0369A1',
                    'badge' => 'Tugas Baru',
                ];
            }
        } catch (\Throwable $e) {
        }

        return $this->sortByLatest($notifications);
    }

    /**
     * Notifikasi untuk Akun Guest.
     */
    protected function getGuestNotifications(User $user): array
    {
        return [
            [
                'id' => 'guest_welcome',
                'title' => 'Menunggu Penetapan Hak Akses',
                'message' => 'Pendaftaran akun Anda berhasil. Administrator sedang meninjau dan menetapkan hak akses peran untuk akun Anda.',
                'url' => route('guest.dashboard'),
                'time_formatted' => $this->formatDiffForHumans($user->created_at),
                'created_at' => $user->created_at,
                'icon' => 'bi-hourglass-split',
                'bg_color' => '#FEF3C7',
                'text_color' => '#B45309',
                'badge' => 'Guest Pending',
            ]
        ];
    }

    /**
     * Helper sortir data notifikasi terbaru di atas.
     */
    protected function sortByLatest(array $items): array
    {
        usort($items, function ($a, $b) {
            $tA = isset($a['created_at']) ? Carbon::parse($a['created_at'])->timestamp : 0;
            $tB = isset($b['created_at']) ? Carbon::parse($b['created_at'])->timestamp : 0;
            if ($tA === $tB) {
                return strnatcmp($b['id'] ?? '', $a['id'] ?? '');
            }
            return $tB <=> $tA;
        });

        return array_slice($items, 0, 20);
    }

    /**
     * Format waktu dalam bahasa Indonesia.
     */
    protected function formatDiffForHumans($dateTime): string
    {
        if (!$dateTime) {
            return 'Baru saja';
        }
        $c = Carbon::parse($dateTime);
        if (abs(now()->diffInSeconds($c)) < 45) {
            return 'Baru saja';
        }
        return $c->diffForHumans();
    }
}
