<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceSession extends Model
{
    use HasFactory;

    protected $table = 'attendance_sessions';

    protected $fillable = [
        'jadwal_id',
        'kelas_mapel_id',
        'judul_sesi',
        'link_meeting',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'waktu_buka_presensi',
        'waktu_tutup_presensi',
        'toleransi_terlambat_menit',
        'status',
        'qr_secret_key',
        'created_by',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'waktu_buka_presensi' => 'datetime',
        'waktu_tutup_presensi' => 'datetime',
        'toleransi_terlambat_menit' => 'integer',
    ];

    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class, 'jadwal_id');
    }

    public function kelasMapel()
    {
        return $this->belongsTo(KelasMapel::class, 'kelas_mapel_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function presensis()
    {
        return $this->hasMany(Presensi::class, 'attendance_session_id');
    }

    public function qrTokens()
    {
        return $this->hasMany(AttendanceQrToken::class, 'attendance_session_id');
    }

    public function activeQrToken()
    {
        return $this->hasOne(AttendanceQrToken::class, 'attendance_session_id')
            ->where('is_active', true)
            ->where('expired_at', '>', now())
            ->latest('id');
    }

    public function requests()
    {
        return $this->hasMany(AttendanceRequest::class, 'attendance_session_id');
    }

    public function documentations()
    {
        return $this->hasMany(ClassDocumentation::class, 'attendance_session_id');
    }

    public function logs()
    {
        return $this->hasMany(AttendanceLog::class, 'attendance_session_id');
    }

    public function isBuka(): bool
    {
        if ($this->status !== 'Aktif') {
            return false;
        }

        if (!$this->waktu_buka_presensi) {
            return false;
        }

        $now = now();
        if ($now->lt($this->waktu_buka_presensi)) {
            return false;
        }

        if ($this->waktu_tutup_presensi && $now->gt($this->waktu_tutup_presensi)) {
            return false;
        }

        // Sesi yang dijadwalkan pada hari yang telah lewat (kemarin atau sebelumnya) otomatis ditutup
        if ($this->tanggal && $this->tanggal->copy()->endOfDay()->lt($now)) {
            return false;
        }

        return true;
    }
}
