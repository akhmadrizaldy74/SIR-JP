<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KelasMapel extends Model
{
    use HasFactory;

    protected $table = 'kelas_mapels';

    protected $fillable = [
        'pelatihan_id',
        'angkatan_id',
        'mata_pelatihan_id',
        'ruangan_id',
        'nama_kelas',
        'kapasitas',
        'kurikulum',
        'tanggal_mulai',
        'tanggal_selesai',
        'jp',
        'ada_kelompok',
        'hari',
        'jam_mulai',
        'jam_selesai',
        'metode_pembelajaran',
    ];

    protected $casts = [
        'jp' => 'float',
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    public function pelatihan()
    {
        return $this->belongsTo(Pelatihan::class, 'pelatihan_id');
    }

    public function angkatan()
    {
        return $this->belongsTo(Angkatan::class, 'angkatan_id');
    }

    public function mataPelatihan()
    {
        return $this->belongsTo(MataPelatihan::class, 'mata_pelatihan_id');
    }

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_id');
    }

    public function pengajars()
    {
        return $this->hasMany(KelasMapelPengajar::class, 'kelas_mapel_id');
    }

    public function pesertas()
    {
        return $this->hasMany(Peserta::class, 'kelas_mapel_id');
    }

    public function jadwals()
    {
        return $this->hasMany(Jadwal::class, 'kelas_mapel_id');
    }

    public function attendanceSessions()
    {
        return $this->hasMany(AttendanceSession::class, 'kelas_mapel_id');
    }

    public function documentations()
    {
        return $this->hasMany(ClassDocumentation::class, 'kelas_mapel_id');
    }
}
