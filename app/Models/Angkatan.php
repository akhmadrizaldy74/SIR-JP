<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Angkatan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'angkatans';

    protected $fillable = [
        'pelatihan_id',
        'nomor_angkatan',
        'tanggal_mulai',
        'tanggal_selesai',
        'jumlah_peserta',
        'lokasi',
        'status',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
            'jumlah_peserta' => 'integer',
        ];
    }

    public function pelatihan()
    {
        return $this->belongsTo(Pelatihan::class);
    }

    public function mataPelatihans()
    {
        return $this->hasMany(MataPelatihan::class);
    }
}
