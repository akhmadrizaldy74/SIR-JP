<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MataPelatihan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'mata_pelatihans';

    protected $fillable = [
        'angkatan_id',
        'nama_mata_pelatihan',
        'rencana_jp',
        'deskripsi',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'rencana_jp' => 'float',
        ];
    }

    public function angkatan()
    {
        return $this->belongsTo(Angkatan::class);
    }

    public function penugasans()
    {
        return $this->hasMany(PenugasanPengajar::class);
    }

    public function jadwals()
    {
        return $this->hasMany(Jadwal::class);
    }

    public function kelasMapels()
    {
        return $this->hasMany(KelasMapel::class, 'mata_pelatihan_id');
    }
}
