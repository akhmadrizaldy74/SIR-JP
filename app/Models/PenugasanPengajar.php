<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PenugasanPengajar extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'penugasan_pengajars';

    protected $fillable = [
        'mata_pelatihan_id',
        'jenis_pengajar',
        'widyaiswara_id',
        'narasumber_id',
        'peran',
        'keterangan',
    ];

    public function mataPelatihan()
    {
        return $this->belongsTo(MataPelatihan::class);
    }

    public function widyaiswara()
    {
        return $this->belongsTo(Widyaiswara::class);
    }

    public function narasumber()
    {
        return $this->belongsTo(Narasumber::class);
    }

    public function jadwals()
    {
        return $this->hasMany(Jadwal::class);
    }

    public function getPengajarNamaAttribute(): string
    {
        if ($this->jenis_pengajar === 'Widyaiswara' && $this->widyaiswara) {
            return $this->widyaiswara->nama_gelar;
        }
        if ($this->jenis_pengajar === 'Narasumber' && $this->narasumber) {
            return $this->narasumber->nama_gelar;
        }
        return '-';
    }

    public function getPengajarInstansiAttribute(): string
    {
        if ($this->jenis_pengajar === 'Widyaiswara' && $this->widyaiswara) {
            return $this->widyaiswara->instansi;
        }
        if ($this->jenis_pengajar === 'Narasumber' && $this->narasumber) {
            return $this->narasumber->instansi;
        }
        return '-';
    }
}
