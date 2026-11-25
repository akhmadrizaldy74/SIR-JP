<?php

namespace App\Services;

use App\Models\AttendanceSession;
use App\Models\AttendanceQrToken;
use App\Models\ParticipantAccessCode;
use App\Models\AttendanceRequest;
use App\Models\AttendanceEvidence;
use App\Models\AttendanceLog;
use App\Models\Presensi;
use App\Models\Peserta;
use App\Models\KelasMapel;
use App\Models\Jadwal;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AttendanceService
{
    /**
     * Dapatkan atau generate kode akses dan PIN unik untuk peserta.
     */
    public function getOrGenerateAccessCode(Peserta $peserta, ?string $customPin = null): array
    {
        $access = ParticipantAccessCode::where('peserta_id', $peserta->id)->first();
        $rawPin = $customPin;

        if (!$access) {
            // Format kode peserta: PST-YYYY-XXXX (contoh: PST-2026-0001)
            $kode = 'PST-' . date('Y') . '-' . str_pad($peserta->id, 4, '0', STR_PAD_LEFT);
            if (!$rawPin) {
                // PIN default 6 digit acak
                $rawPin = (string) mt_rand(100000, 999999);
            }

            $access = ParticipantAccessCode::create([
                'peserta_id' => $peserta->id,
                'kode_peserta' => $kode,
                'pin_hash' => Hash::make($rawPin),
                'is_active' => true,
            ]);
        } elseif ($customPin) {
            $access->update([
                'pin_hash' => Hash::make($customPin),
                'is_active' => true,
            ]);
        }

        return [
            'access_code' => $access,
            'kode_peserta' => $access->kode_peserta,
            'raw_pin' => $rawPin, // null jika mengambil data lama tanpa reset
        ];
    }

    /**
     * Reset PIN peserta dan aktifkan kembali jika diblokir.
     */
    public function resetPin(Peserta $peserta, ?string $newPin = null): array
    {
        $rawPin = $newPin ?: (string) mt_rand(100000, 999999);
        $access = ParticipantAccessCode::firstOrNew(['peserta_id' => $peserta->id]);

        if (!$access->kode_peserta) {
            $access->kode_peserta = 'PST-' . date('Y') . '-' . str_pad($peserta->id, 4, '0', STR_PAD_LEFT);
        }

        $access->pin_hash = Hash::make($rawPin);
        $access->is_active = true;
        $access->save();

        return [
            'access_code' => $access,
            'kode_peserta' => $access->kode_peserta,
            'raw_pin' => $rawPin,
        ];
    }

    /**
     * Autentikasi peserta melalui kode peserta dan PIN.
     */
    public function authenticateParticipant(string $kodePeserta, string $pin): ?Peserta
    {
        $access = ParticipantAccessCode::where('kode_peserta', trim($kodePeserta))
            ->where('is_active', true)
            ->first();

        if (!$access || !Hash::check($pin, $access->pin_hash)) {
            return null;
        }

        $access->update(['last_login_at' => now()]);

        return $access->peserta;
    }

    /**
     * Generate QR Token acak baru untuk sesi presensi dengan masa berlaku (default 45 detik).
     */
    public function generateQrToken(AttendanceSession $session, int $ttlSeconds = 45): array
    {
        // Nonaktifkan token lama sesi ini
        AttendanceQrToken::where('attendance_session_id', $session->id)
            ->where('is_active', true)
            ->update(['is_active' => false]);

        $rawRandom = Str::random(32);
        $expiredAt = now()->addSeconds($ttlSeconds);
        $tokenHash = hash('sha256', $session->id . '|' . $rawRandom . '|' . $session->qr_secret_key);

        $qrToken = AttendanceQrToken::create([
            'attendance_session_id' => $session->id,
            'token_hash' => $tokenHash,
            'expired_at' => $expiredAt,
            'is_active' => true,
        ]);

        // Dapatkan URL scan lengkap agar saat di-scan kamera HP bawaan langsung bisa diklik
        $host = request()->getHost();
        $scheme = request()->getScheme();
        $port = request()->getPort();

        if (in_array($host, ['localhost', '127.0.0.1', '::1'])) {
            $lanIp = gethostbyname(gethostname());
            if ($lanIp && filter_var($lanIp, FILTER_VALIDATE_IP) && $lanIp !== '127.0.0.1') {
                $host = $lanIp;
            }
        }

        $portStr = ($port && !in_array($port, [80, 443])) ? ':' . $port : '';
        $payload = "{$scheme}://{$host}{$portStr}/presensi/scan?session_id={$session->id}&token={$tokenHash}";

        return [
            'token_record' => $qrToken,
            'payload' => $payload,
            'expired_at' => $expiredAt,
            'ttl' => $ttlSeconds,
        ];
    }

    /**
     * Validasi Token QR dan lakukan Check-in peserta.
     */
    public function checkInViaQr(Peserta $peserta, string $qrPayload): array
    {
        $data = null;

        // Cek jika payload berupa URL (misal scan lewat kamera bawaan HP)
        if (str_contains($qrPayload, 'token=')) {
            $query = parse_url($qrPayload, PHP_URL_QUERY);
            if ($query) {
                parse_str($query, $parsed);
                if (!empty($parsed['token'])) {
                    $data = [
                        'token' => $parsed['token'],
                        'session_id' => $parsed['session_id'] ?? null,
                    ];
                }
            }
        }

        // Cek jika payload berupa JSON
        if (!$data) {
            $jsonParsed = json_decode($qrPayload, true);
            if (is_array($jsonParsed) && isset($jsonParsed['token'])) {
                $data = $jsonParsed;
            }
        }

        // Fallback: anggap sebagai token string langsung
        if (!$data || empty($data['token'])) {
            $data = ['token' => trim($qrPayload)];
        }

        $tokenRecord = AttendanceQrToken::with('session')
            ->where('token_hash', $data['token'])
            ->first();

        if (!$tokenRecord) {
            return [
                'success' => false,
                'message' => 'QR Code tidak valid atau tidak dikenali oleh sistem.',
            ];
        }

        if (!$tokenRecord->is_active || $tokenRecord->expired_at->isPast()) {
            return [
                'success' => false,
                'message' => 'QR Code telah kedaluwarsa. Silakan scan QR Code terbaru di layar admin.',
            ];
        }

        $session = $tokenRecord->session;
        if (!$session || $session->status !== 'Aktif') {
            return [
                'success' => false,
                'message' => 'Sesi presensi ini belum dibuka atau sudah ditutup oleh admin.',
            ];
        }

        // Validasi keterkaitan peserta dengan sesi ini (melalui angkatan atau kelas mapel)
        $isRelatedAngkatan = false;
        if ($peserta->angkatan_id) {
            if ($session->kelasMapel && $session->kelasMapel->angkatan_id == $peserta->angkatan_id) {
                $isRelatedAngkatan = true;
            }
            if ($session->jadwal && $session->jadwal->mataPelatihan && $session->jadwal->mataPelatihan->angkatan_id == $peserta->angkatan_id) {
                $isRelatedAngkatan = true;
            }
        }

        $isRelatedKelas = false;
        if ($peserta->kelas_mapel_id && $session->kelas_mapel_id && $peserta->kelas_mapel_id == $session->kelas_mapel_id) {
            $isRelatedKelas = true;
        }

        if (!$isRelatedAngkatan && !$isRelatedKelas) {
            return [
                'success' => false,
                'message' => 'Anda tidak terdaftar dalam kelas atau angkatan pada sesi pembelajaran ini.',
            ];
        }

        // Cek apakah peserta sudah pernah presensi pada sesi ini
        $existing = Presensi::where('attendance_session_id', $session->id)
            ->where('peserta_id', $peserta->id)
            ->first();

        if ($existing) {
            return [
                'success' => false,
                'message' => 'Anda sudah tercatat melakukan presensi untuk sesi ini (' . $existing->status_kehadiran . ' pada ' . ($existing->waktu_check_in ? $existing->waktu_check_in->format('H:i') : '-') . ').',
            ];
        }

        // Tentukan status: Hadir atau Terlambat berdasarkan toleransi keterlambatan
        $now = now();
        $status = 'Hadir';

        // Jika jam buka presensi ada dan sekarang melebihi jam buka + toleransi menit
        $batasWaktu = null;
        if ($session->waktu_buka_presensi) {
            $batasWaktu = $session->waktu_buka_presensi->copy()->addMinutes($session->toleransi_terlambat_menit);
        } else {
            // Gunakan jam_mulai sesi hari ini
            $batasWaktu = Carbon::parse($session->tanggal->format('Y-m-d') . ' ' . $session->jam_mulai)->addMinutes($session->toleransi_terlambat_menit);
        }

        if ($batasWaktu && $now->gt($batasWaktu)) {
            $status = 'Terlambat';
        }

        return DB::transaction(function () use ($session, $peserta, $status, $now) {
            $presensi = Presensi::create([
                'attendance_session_id' => $session->id,
                'peserta_id' => $peserta->id,
                'jadwal_id' => $session->jadwal_id,
                'kelas_mapel_id' => $session->kelas_mapel_id,
                'tanggal' => $session->tanggal,
                'status_kehadiran' => $status,
                'metode_presensi' => 'QR',
                'waktu_presensi' => $now->format('H:i:s'),
                'waktu_check_in' => $now,
                'keterangan' => 'Check-in mandiri via scan QR Code portal peserta',
            ]);

            // Audit Log
            AttendanceLog::create([
                'presensi_id' => $presensi->id,
                'attendance_session_id' => $session->id,
                'peserta_id' => $peserta->id,
                'user_id' => null,
                'status_sebelum' => null,
                'status_sesudah' => $status,
                'alasan_perubahan' => 'Presensi mandiri peserta via QR Code',
                'metode_input' => 'QR',
                'ip_address' => request()->ip(),
            ]);

            return [
                'success' => true,
                'presensi' => $presensi,
                'status' => $status,
                'message' => 'Presensi berhasil dicatat! Status: ' . $status . ' (Waktu: ' . $now->format('H:i:s') . ' WIB)',
            ];
        });
    }

    /**
     * Presensi mandiri peserta via form langsung (tanpa perlu scan QR).
     * Mendukung pilihan status (Hadir, Sakit, Izin, Dispensasi), keterangan, dan unggah bukti (foto/pdf maks 2MB).
     */
    public function submitDirectAttendance(
        Peserta $peserta,
        AttendanceSession $session,
        string $statusKehadiran,
        ?string $keterangan = null,
        $uploadedFile = null
    ): array {
        if (!$session->isBuka()) {
            return [
                'success' => false,
                'message' => 'Sesi presensi ini belum dibuka oleh pengajar atau sudah ditutup.',
            ];
        }

        // Pastikan peserta sudah terdaftar di pelatihan / angkatan
        if (!$peserta->angkatan_id && !$peserta->kelas_mapel_id) {
            return [
                'success' => false,
                'message' => 'Anda belum terdaftar dalam pelatihan ini. Silakan pilih pelatihan terlebih dahulu.',
            ];
        }

        // Jika sesi presensi dikhususkan untuk kelas tertentu, pastikan peserta terdaftar di kelas tersebut
        if ($session->kelas_mapel_id && $peserta->kelas_mapel_id && $session->kelas_mapel_id != $peserta->kelas_mapel_id) {
            return [
                'success' => false,
                'message' => 'Sesi presensi ini dikhususkan untuk kelas lain. Anda tidak terdaftar dalam kelas ini.',
            ];
        }

        // Cek keterkaitan peserta dengan sesi ini (melalui angkatan atau kelas mapel)
        $isRelatedAngkatan = false;
        if ($peserta->angkatan_id) {
            if ($session->kelasMapel && $session->kelasMapel->angkatan_id == $peserta->angkatan_id) {
                $isRelatedAngkatan = true;
            }
            if ($session->jadwal && $session->jadwal->mataPelatihan && $session->jadwal->mataPelatihan->angkatan_id == $peserta->angkatan_id) {
                $isRelatedAngkatan = true;
            }
        }

        $isRelatedKelas = false;
        if ($peserta->kelas_mapel_id && $session->kelas_mapel_id && $peserta->kelas_mapel_id == $session->kelas_mapel_id) {
            $isRelatedKelas = true;
        }

        if (!$isRelatedAngkatan && !$isRelatedKelas) {
            return [
                'success' => false,
                'message' => 'Anda tidak terdaftar dalam kelas atau angkatan pada sesi pembelajaran ini.',
            ];
        }

        // Cek apakah peserta sudah pernah presensi pada sesi ini
        $existing = Presensi::where('attendance_session_id', $session->id)
            ->where('peserta_id', $peserta->id)
            ->first();

        if ($existing) {
            return [
                'success' => false,
                'message' => 'Anda sudah tercatat melakukan presensi untuk sesi ini (' . $existing->status_kehadiran . ').',
            ];
        }

        // Status yang valid
        $allowed = ['Hadir', 'Sakit', 'Izin', 'Dispensasi'];
        $status = in_array($statusKehadiran, $allowed) ? $statusKehadiran : 'Hadir';

        // Cek keterlambatan bila status Hadir
        $now = now();
        $batasWaktu = null;
        if ($session->waktu_buka_presensi) {
            $batasWaktu = $session->waktu_buka_presensi->copy()->addMinutes($session->toleransi_terlambat_menit);
        } else {
            $batasWaktu = Carbon::parse($session->tanggal->format('Y-m-d') . ' ' . $session->jam_mulai)->addMinutes($session->toleransi_terlambat_menit);
        }

        if ($status === 'Hadir' && $batasWaktu && $now->gt($batasWaktu)) {
            $status = 'Terlambat';
        }

        // Simpan file bukti jika dilampirkan
        $buktiPath = null;
        if ($uploadedFile && $uploadedFile->isValid()) {
            $fileName = 'bukti_sess_' . $session->id . '_pst_' . $peserta->id . '_' . time() . '.' . $uploadedFile->getClientOriginalExtension();
            $buktiPath = $uploadedFile->storeAs('presensi_evidence/' . $session->id, $fileName, 'public');
        }

        return DB::transaction(function () use ($session, $peserta, $status, $keterangan, $buktiPath, $now) {
            $presensi = Presensi::create([
                'attendance_session_id' => $session->id,
                'peserta_id' => $peserta->id,
                'jadwal_id' => $session->jadwal_id,
                'kelas_mapel_id' => $session->kelas_mapel_id,
                'tanggal' => $session->tanggal,
                'status_kehadiran' => $status,
                'metode_presensi' => 'Mandiri',
                'waktu_presensi' => $now->format('H:i:s'),
                'waktu_check_in' => $now,
                'keterangan' => $keterangan ?: 'Presensi mandiri peserta via form KCLC',
                'bukti_file' => $buktiPath,
            ]);

            // Audit Log
            AttendanceLog::create([
                'presensi_id' => $presensi->id,
                'attendance_session_id' => $session->id,
                'peserta_id' => $peserta->id,
                'user_id' => null,
                'status_sebelum' => null,
                'status_sesudah' => $status,
                'alasan_perubahan' => 'Presensi mandiri peserta via form portal',
                'metode_input' => 'Mandiri',
                'ip_address' => request()->ip(),
            ]);

            return [
                'success' => true,
                'presensi' => $presensi,
                'status' => $status,
                'message' => 'Presensi berhasil dicatat! Status: ' . $status . ' (Waktu: ' . $now->format('H:i:s') . ' WIB)',
            ];
        });
    }

    /**
     * Input atau perbarui kehadiran peserta secara manual oleh Admin/Operator.
     */
    public function recordManualAttendance(
        AttendanceSession $session,
        int $pesertaId,
        string $status,
        ?string $keterangan,
        int $userId,
        string $alasanPerubahan = 'Input/koreksi manual oleh admin'
    ): Presensi {
        return DB::transaction(function () use ($session, $pesertaId, $status, $keterangan, $userId, $alasanPerubahan) {
            $presensi = Presensi::where('attendance_session_id', $session->id)
                ->where('peserta_id', $pesertaId)
                ->first();

            $statusSebelum = $presensi ? $presensi->status_kehadiran : null;

            if ($presensi) {
                $presensi->update([
                    'status_kehadiran' => $status,
                    'keterangan' => $keterangan ?: $presensi->keterangan,
                    'diinput_oleh' => $userId,
                ]);
            } else {
                $presensi = Presensi::create([
                    'attendance_session_id' => $session->id,
                    'peserta_id' => $pesertaId,
                    'jadwal_id' => $session->jadwal_id,
                    'kelas_mapel_id' => $session->kelas_mapel_id,
                    'tanggal' => $session->tanggal,
                    'status_kehadiran' => $status,
                    'metode_presensi' => 'Manual',
                    'waktu_presensi' => now()->format('H:i:s'),
                    'waktu_check_in' => now(),
                    'keterangan' => $keterangan,
                    'diinput_oleh' => $userId,
                ]);
            }

            // Catat perubahan di AttendanceLog
            AttendanceLog::create([
                'presensi_id' => $presensi->id,
                'attendance_session_id' => $session->id,
                'peserta_id' => $pesertaId,
                'user_id' => $userId,
                'status_sebelum' => $statusSebelum,
                'status_sesudah' => $status,
                'alasan_perubahan' => $alasanPerubahan,
                'metode_input' => 'Manual',
                'ip_address' => request()->ip(),
            ]);

            return $presensi;
        });
    }

    /**
     * Bulk update kehadiran untuk sejumlah peserta dalam satu sesi.
     */
    public function bulkUpdateManual(
        AttendanceSession $session,
        array $pesertaStatusList, // array of ['peserta_id' => 1, 'status' => 'Hadir', 'keterangan' => '...']
        string $alasanUmum,
        int $userId
    ): int {
        return DB::transaction(function () use ($session, $pesertaStatusList, $alasanUmum, $userId) {
            $count = 0;
            foreach ($pesertaStatusList as $item) {
                if (!isset($item['peserta_id']) || !isset($item['status'])) {
                    continue;
                }
                $this->recordManualAttendance(
                    $session,
                    (int) $item['peserta_id'],
                    $item['status'],
                    $item['keterangan'] ?? null,
                    $userId,
                    $alasanUmum
                );
                $count++;
            }
            return $count;
        });
    }

    /**
     * Pengajuan Izin/Sakit/Dispensasi oleh peserta.
     */
    public function submitAttendanceRequest(
        Peserta $peserta,
        int $sessionId,
        string $jenis,
        string $alasan,
        array $uploadedFiles = []
    ): AttendanceRequest {
        return DB::transaction(function () use ($peserta, $sessionId, $jenis, $alasan, $uploadedFiles) {
            $session = AttendanceSession::findOrFail($sessionId);

            $request = AttendanceRequest::create([
                'peserta_id' => $peserta->id,
                'attendance_session_id' => $session->id,
                'jenis_pengajuan' => $jenis,
                'alasan' => $alasan,
                'status' => 'Pending',
            ]);

            foreach ($uploadedFiles as $file) {
                if (!$file) continue;

                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $safeName = 'evidence_' . time() . '_' . Str::random(12) . '.' . $extension;
                $path = $file->storeAs('attendance_evidences', $safeName, 'local');

                AttendanceEvidence::create([
                    'attendance_request_id' => $request->id,
                    'nama_file_asli' => $originalName,
                    'path_file' => $path,
                    'mime_type' => $file->getClientMimeType() ?: 'application/octet-stream',
                    'file_size' => $file->getSize(),
                    'keterangan' => 'Bukti surat/dokumen pendukung pengajuan ' . $jenis,
                ]);
            }

            return $request;
        });
    }

    /**
     * Verifikasi keputusan admin terhadap pengajuan izin/sakit/dispensasi.
     */
    public function verifyAttendanceRequest(
        AttendanceRequest $request,
        string $decision, // 'Approved' atau 'Rejected'
        ?string $catatanAdmin,
        int $verifierId
    ): bool {
        return DB::transaction(function () use ($request, $decision, $catatanAdmin, $verifierId) {
            $now = now();
            $request->update([
                'status' => $decision,
                'catatan_admin' => $catatanAdmin,
                'diverifikasi_oleh' => $verifierId,
                'diverifikasi_pada' => $now,
            ]);

            if ($decision === 'Approved') {
                $session = $request->session;
                $statusPresensi = $request->jenis_pengajuan; // Izin, Sakit, atau Dispensasi

                $presensi = Presensi::where('attendance_session_id', $session->id)
                    ->where('peserta_id', $request->peserta_id)
                    ->first();

                $statusSebelum = $presensi ? $presensi->status_kehadiran : null;

                if ($presensi) {
                    $presensi->update([
                        'status_kehadiran' => $statusPresensi,
                        'keterangan' => 'Pengajuan ' . $statusPresensi . ' disetujui admin. Alasan: ' . $request->alasan,
                        'diinput_oleh' => $verifierId,
                    ]);
                } else {
                    $presensi = Presensi::create([
                        'attendance_session_id' => $session->id,
                        'peserta_id' => $request->peserta_id,
                        'jadwal_id' => $session->jadwal_id,
                        'kelas_mapel_id' => $session->kelas_mapel_id,
                        'tanggal' => $session->tanggal,
                        'status_kehadiran' => $statusPresensi,
                        'metode_presensi' => 'Sistem',
                        'waktu_presensi' => $now->format('H:i:s'),
                        'waktu_check_in' => $now,
                        'keterangan' => 'Pengajuan ' . $statusPresensi . ' disetujui admin. Alasan: ' . $request->alasan,
                        'diinput_oleh' => $verifierId,
                    ]);
                }

                AttendanceLog::create([
                    'presensi_id' => $presensi->id,
                    'attendance_session_id' => $session->id,
                    'peserta_id' => $request->peserta_id,
                    'user_id' => $verifierId,
                    'status_sebelum' => $statusSebelum,
                    'status_sesudah' => $statusPresensi,
                    'alasan_perubahan' => 'Persetujuan pengajuan ' . $statusPresensi . ' oleh admin: ' . ($catatanAdmin ?: '-'),
                    'metode_input' => 'Sistem',
                    'ip_address' => request()->ip(),
                ]);
            }

            return true;
        });
    }

    /**
     * Hitung statistik kehadiran untuk satu sesi tertentu.
     */
    public function getSessionStatistics(AttendanceSession $session): array
    {
        // Ambil seluruh peserta yang terdaftar pada kelas mapel / angkatan sesi ini
        $queryPeserta = Peserta::query();
        if ($session->kelas_mapel_id) {
            $angkatanId = $session->kelasMapel ? $session->kelasMapel->angkatan_id : null;
            $queryPeserta->where(function ($q) use ($session, $angkatanId) {
                $q->where('kelas_mapel_id', $session->kelas_mapel_id);
                if ($angkatanId) {
                    $q->orWhere(function($sub) use ($angkatanId) {
                        $sub->where('angkatan_id', $angkatanId)->whereNull('kelas_mapel_id');
                    });
                }
            });
        } elseif ($session->jadwal && $session->jadwal->mataPelatihan && $session->jadwal->mataPelatihan->angkatan_id) {
            $queryPeserta->where('angkatan_id', $session->jadwal->mataPelatihan->angkatan_id);
        } else {
            // Jika tidak ada kelas atau angkatan terikat, jangan memuat peserta dari kelas/pelatihan lain
            $queryPeserta->whereRaw('1 = 0');
        }

        $allPesertas = $queryPeserta->orderBy('nama')->get();
        $totalPeserta = $allPesertas->count();

        // Ambil presensi yang sudah tercatat pada sesi ini
        $presensis = Presensi::where('attendance_session_id', $session->id)
            ->get()
            ->keyBy('peserta_id');

        $hadir = 0;
        $terlambat = 0;
        $izin = 0;
        $sakit = 0;
        $alpa = 0;
        $dispensasi = 0;
        $belumPresensi = 0;

        foreach ($allPesertas as $pst) {
            if (isset($presensis[$pst->id])) {
                $status = $presensis[$pst->id]->status_kehadiran;
                match ($status) {
                    'Hadir' => $hadir++,
                    'Terlambat' => $terlambat++,
                    'Izin' => $izin++,
                    'Sakit' => $sakit++,
                    'Alpa' => $alpa++,
                    'Dispensasi' => $dispensasi++,
                    default => $belumPresensi++,
                };
            } else {
                $belumPresensi++;
            }
        }

        // Persentase kehadiran = (Hadir + Terlambat) / Total * 100
        $persenKehadiran = $totalPeserta > 0 ? round((($hadir + $terlambat) / $totalPeserta) * 100, 1) : 0;

        $pendingRequests = AttendanceRequest::where('attendance_session_id', $session->id)
            ->where('status', 'Pending')
            ->count();

        return [
            'total_peserta' => $totalPeserta,
            'hadir' => $hadir,
            'terlambat' => $terlambat,
            'izin' => $izin,
            'sakit' => $sakit,
            'alpa' => $alpa,
            'dispensasi' => $dispensasi,
            'belum_presensi' => $belumPresensi,
            'persen_kehadiran' => $persenKehadiran,
            'pending_requests' => $pendingRequests,
            'pesertas' => $allPesertas,
            'presensis' => $presensis,
        ];
    }

    /**
     * Hitung statistik agregat global untuk Dashboard Presensi.
     */
    public function getDashboardStatistics(?int $pelatihanId = null, ?int $angkatanId = null, ?int $kelasMapelId = null): array
    {
        $sessionQuery = AttendanceSession::query();
        $presensiQuery = Presensi::whereNotNull('attendance_session_id');

        if ($kelasMapelId) {
            $sessionQuery->where('kelas_mapel_id', $kelasMapelId);
            $presensiQuery->where('kelas_mapel_id', $kelasMapelId);
        } elseif ($angkatanId) {
            $sessionQuery->whereHas('kelasMapel', fn($q) => $q->where('angkatan_id', $angkatanId));
            $presensiQuery->whereHas('kelasMapel', fn($q) => $q->where('angkatan_id', $angkatanId));
        } elseif ($pelatihanId) {
            $sessionQuery->whereHas('kelasMapel', fn($q) => $q->where('pelatihan_id', $pelatihanId));
            $presensiQuery->whereHas('kelasMapel', fn($q) => $q->where('pelatihan_id', $pelatihanId));
        }

        $sessionIds = (clone $sessionQuery)->pluck('id');

        $hadir = (clone $presensiQuery)->where('status_kehadiran', 'Hadir')->count();
        $terlambat = (clone $presensiQuery)->where('status_kehadiran', 'Terlambat')->count();
        $izin = (clone $presensiQuery)->where('status_kehadiran', 'Izin')->count();
        $sakit = (clone $presensiQuery)->where('status_kehadiran', 'Sakit')->count();
        $alpa = (clone $presensiQuery)->where('status_kehadiran', 'Alpa')->count();
        $dispensasi = (clone $presensiQuery)->where('status_kehadiran', 'Dispensasi')->count();

        $totalSesi = $sessionQuery->count();
        $sesiAktif = (clone $sessionQuery)->where('status', 'Aktif')->count();
        $totalPeserta = Peserta::count();

        $pendingRequests = AttendanceRequest::whereIn('attendance_session_id', $sessionIds)
            ->where('status', 'Pending')
            ->count();

        $totalRecorded = $hadir + $terlambat + $izin + $sakit + $alpa + $dispensasi;
        $persenHadir = $totalRecorded > 0 ? round((($hadir + $terlambat) / $totalRecorded) * 100, 1) : 0;

        return [
            'total_sesi' => $totalSesi,
            'sesi_aktif' => $sesiAktif,
            'total_peserta' => $totalPeserta,
            'hadir' => $hadir,
            'terlambat' => $terlambat,
            'izin' => $izin,
            'sakit' => $sakit,
            'alpa' => $alpa,
            'dispensasi' => $dispensasi,
            'total_recorded' => $totalRecorded,
            'persen_kehadiran' => $persenHadir,
            'pending_requests' => $pendingRequests,
        ];
    }
}
