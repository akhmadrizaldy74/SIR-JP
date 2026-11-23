<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Narasumber extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'narasumbers';

    protected $fillable = [
        'nik',
        'nama_lengkap',
        'gelar_depan',
        'gelar_belakang',
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

    public function getMaskedNikAttribute(): string
    {
        return \App\Services\SecurityMaskingService::maskNik($this->nik);
    }

    public function getMaskedPhoneAttribute(): string
    {
        return \App\Services\SecurityMaskingService::maskPhone($this->nomor_telepon);
    }

    public function getMaskedEmailAttribute(): string
    {
        return \App\Services\SecurityMaskingService::maskEmail($this->email);
    }

    public function penugasans()
    {
        return $this->hasMany(PenugasanPengajar::class);
    }

    public function realisasiPengajars()
    {
        return $this->hasMany(RealisasiPengajar::class);
    }
}
