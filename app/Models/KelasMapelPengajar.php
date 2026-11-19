<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KelasMapelPengajar extends Model
{
    use HasFactory;

    protected $table = 'kelas_mapel_pengajars';

    protected $fillable = [
        'kelas_mapel_id',
        'pengajar_type',
        'pengajar_id',
        'jp',
        'jadwal_keterangan',
        'peran',
        'kelompok',
    ];

    protected $casts = [
        'jp' => 'float',
    ];

    public function kelasMapel()
    {
        return $this->belongsTo(KelasMapel::class, 'kelas_mapel_id');
    }

    public function pengajar()
    {
        return $this->morphTo();
    }
}
