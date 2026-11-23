<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceRequest extends Model
{
    use HasFactory;

    protected $table = 'attendance_requests';

    protected $fillable = [
        'peserta_id',
        'attendance_session_id',
        'jenis_pengajuan',
        'alasan',
        'status',
        'catatan_admin',
        'diverifikasi_oleh',
        'diverifikasi_pada',
    ];

    protected $casts = [
        'diverifikasi_pada' => 'datetime',
    ];

    public function peserta()
    {
        return $this->belongsTo(Peserta::class, 'peserta_id');
    }

    public function session()
    {
        return $this->belongsTo(AttendanceSession::class, 'attendance_session_id');
    }

    public function verifikator()
    {
        return $this->belongsTo(User::class, 'diverifikasi_oleh');
    }

    public function evidences()
    {
        return $this->hasMany(AttendanceEvidence::class, 'attendance_request_id');
    }
}
