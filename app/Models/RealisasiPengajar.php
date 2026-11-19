<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RealisasiPengajar extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'realisasi_pengajars';

    protected $fillable = [
        'realisasi_jadwal_id',
        'penugasan_pengajar_id',
        'jenis_pengajar',
        'widyaiswara_id',
        'narasumber_id',
        'jp_aktual',
        'jp_dibayarkan',
        'status_kehadiran',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'jp_aktual' => 'decimal:2',
            'jp_dibayarkan' => 'decimal:2',
        ];
    }

    public function realisasiJadwal()
    {
        return $this->belongsTo(RealisasiJadwal::class);
    }

    public function penugasanPengajar()
    {
        return $this->belongsTo(PenugasanPengajar::class);
    }

    public function widyaiswara()
    {
        return $this->belongsTo(Widyaiswara::class);
    }

    public function narasumber()
    {
        return $this->belongsTo(Narasumber::class);
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
}
