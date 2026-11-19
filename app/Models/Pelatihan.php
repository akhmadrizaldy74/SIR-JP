<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pelatihan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pelatihans';

    protected $fillable = [
        'kode_pelatihan',
        'nama_pelatihan',
        'kategori',
        'penyelenggara',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
        ];
    }

    public function angkatans()
    {
        return $this->hasMany(Angkatan::class);
    }

    public function aspekPenilaianSikaps()
    {
        return $this->hasMany(AspekPenilaianSikap::class, 'pelatihan_id');
    }

    public function formEvaluasis()
    {
        return $this->hasMany(FormEvaluasi::class, 'pelatihan_id');
    }

    public function kelasMapels()
    {
        return $this->hasMany(KelasMapel::class, 'pelatihan_id');
    }
}
