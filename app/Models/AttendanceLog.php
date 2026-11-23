<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceLog extends Model
{
    use HasFactory;

    protected $table = 'attendance_logs';

    public $timestamps = false; // We use created_at manually via Eloquent or DB timestamp

    protected $fillable = [
        'presensi_id',
        'attendance_session_id',
        'peserta_id',
        'user_id',
        'status_sebelum',
        'status_sesudah',
        'alasan_perubahan',
        'metode_input',
        'ip_address',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (!$model->created_at) {
                $model->created_at = now();
            }
        });
    }

    public function presensi()
    {
        return $this->belongsTo(Presensi::class, 'presensi_id');
    }

    public function session()
    {
        return $this->belongsTo(AttendanceSession::class, 'attendance_session_id');
    }

    public function peserta()
    {
        return $this->belongsTo(Peserta::class, 'peserta_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
