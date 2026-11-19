<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RealisasiJadwal extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'realisasi_jadwals';

    protected $fillable = [
        'jadwal_id',
        'tanggal_realisasi',
        'jam_mulai_aktual',
        'jam_selesai_aktual',
        'jp_aktual',
        'jp_dibayarkan',
        'status',
        'alasan_batal',
        'catatan',
        'diverifikasi_oleh',
        'diverifikasi_pada',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_realisasi' => 'date',
            'jp_aktual' => 'decimal:2',
            'jp_dibayarkan' => 'decimal:2',
            'diverifikasi_pada' => 'datetime',
        ];
    }

    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class);
    }

    public function pengajarRealisasis()
    {
        return $this->hasMany(RealisasiPengajar::class);
    }

    public function verifikator()
    {
        return $this->belongsTo(User::class, 'diverifikasi_oleh');
    }
}
