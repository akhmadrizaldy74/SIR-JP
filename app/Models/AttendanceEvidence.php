<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceEvidence extends Model
{
    use HasFactory;

    protected $table = 'attendance_evidences';

    protected $fillable = [
        'attendance_request_id',
        'nama_file_asli',
        'path_file',
        'mime_type',
        'file_size',
        'keterangan',
    ];

    public function request()
    {
        return $this->belongsTo(AttendanceRequest::class, 'attendance_request_id');
    }
}
