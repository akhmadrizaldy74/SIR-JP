<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassDocumentation extends Model
{
    use HasFactory;

    protected $table = 'class_documentations';

    protected $fillable = [
        'attendance_session_id',
        'kelas_mapel_id',
        'nama_file_asli',
        'path_file',
        'mime_type',
        'kategori',
        'keterangan',
        'uploaded_by',
    ];

    public function session()
    {
        return $this->belongsTo(AttendanceSession::class, 'attendance_session_id');
    }

    public function kelasMapel()
    {
        return $this->belongsTo(KelasMapel::class, 'kelas_mapel_id');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
