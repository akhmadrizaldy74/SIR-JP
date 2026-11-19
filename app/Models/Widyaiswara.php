<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Widyaiswara extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'widyaiswaras';

    protected $fillable = [
        'nik',
        'nip',
        'nama_lengkap',
        'gelar_depan',
        'gelar_belakang',
        'jabatan',
        'instansi',
        'nomor_telepon',
        'email',
        'status',
        'keterangan',
    ];

    public function getNamaGelarAttribute(): string
    {
        $depan = $this->gelar_depan ? trim($this->gelar_depan) . ' ' : '';
        $belakang = $this->gelar_belakang ? ', ' . trim($this->gelar_belakang) : '';
        return $depan . $this->nama_lengkap . $belakang;
    }

    public function penugasans()
    {
        return $this->hasMany(PenugasanPengajar::class);
    }

    public function realisasiPengajars()
    {
        return $this->hasMany(RealisasiPengajar::class);
    }

    public function konversiJps()
    {
        return $this->hasMany(KonversiJp::class);
    }
}
