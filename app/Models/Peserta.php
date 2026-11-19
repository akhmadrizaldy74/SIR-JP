<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peserta extends Model
{
    use HasFactory;

    protected $table = 'pesertas';

    protected $fillable = [
        'angkatan_id',
        'kelas_mapel_id',
        'nip',
        'nama',
        'email',
        'jenis_kelamin',
        'instansi',
        'jabatan',
        'golongan',
    ];

    public function angkatan()
    {
        return $this->belongsTo(Angkatan::class, 'angkatan_id');
    }

    public function kelasMapel()
    {
        return $this->belongsTo(KelasMapel::class, 'kelas_mapel_id');
    }

    public function presensis()
    {
        return $this->hasMany(Presensi::class, 'peserta_id');
    }
}
