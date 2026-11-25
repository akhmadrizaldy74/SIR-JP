<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
    use HasFactory;

    protected $table = 'presensis';

    protected $fillable = [
        'attendance_session_id',
        'peserta_id',
        'jadwal_id',
        'kelas_mapel_id',
        'tanggal',
        'status_kehadiran',
        'metode_presensi',
        'waktu_presensi',
        'waktu_check_in',
        'keterangan',
        'bukti_file',
        'status_verifikasi',
        'catatan_verifikasi',
        'diverifikasi_oleh',
        'diverifikasi_pada',
        'diinput_oleh',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'waktu_check_in' => 'datetime',
        'diverifikasi_pada' => 'datetime',
    ];

    public function session()
    {
        return $this->belongsTo(AttendanceSession::class, 'attendance_session_id');
    }

    public function peserta()
    {
        return $this->belongsTo(Peserta::class, 'peserta_id');
    }

    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class, 'jadwal_id');
    }

    public function kelasMapel()
    {
        return $this->belongsTo(KelasMapel::class, 'kelas_mapel_id');
    }

    public function operator()
    {
        return $this->belongsTo(User::class, 'diinput_oleh');
    }

    public function verifikator()
    {
        return $this->belongsTo(User::class, 'diverifikasi_oleh');
    }

    public function logs()
    {
        return $this->hasMany(AttendanceLog::class, 'presensi_id');
    }

    /**
     * Helper untuk warna status presensi sesuai ketentuan:
     * Hadir: hijau (#10B981)
     * Terlambat: biru (#3B82F6)
     * Izin: kuning/oranye (#F59E0B)
     * Sakit: merah muda (#EC4899)
     * Alpa: merah (#EF4444)
     * Dispensasi: ungu (#8B5CF6)
     */
    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status_kehadiran) {
            'Hadir' => ['bg' => '#D1FAE5', 'color' => '#065F46', 'label' => 'Hadir'],
            'Terlambat' => ['bg' => '#DBEAFE', 'color' => '#1E40AF', 'label' => 'Terlambat'],
            'Izin' => ['bg' => '#FEF3C7', 'color' => '#92400E', 'label' => 'Izin'],
            'Sakit' => ['bg' => '#FCE7F3', 'color' => '#9D174D', 'label' => 'Sakit'],
            'Alpa' => ['bg' => '#FEE2E2', 'color' => '#991B1B', 'label' => 'Alpa'],
            'Dispensasi' => ['bg' => '#EDE9FE', 'color' => '#5B21B6', 'label' => 'Dispensasi'],
            default => ['bg' => '#F1F5F9', 'color' => '#475569', 'label' => $this->status_kehadiran ?: 'Belum Presensi'],
        };
    }
}
