<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ruangan extends Model
{
    use HasFactory;

    protected $table = 'ruangans';

    protected $fillable = [
        'nama_ruangan',
        'kapasitas',
        'lokasi',
        'is_aktif',
    ];

    protected $casts = [
        'is_aktif' => 'boolean',
    ];

    public function kelasMapels()
    {
        return $this->hasMany(KelasMapel::class, 'ruangan_id');
    }

    public function jadwals()
    {
        return $this->hasMany(Jadwal::class, 'ruangan_id');
    }
}
