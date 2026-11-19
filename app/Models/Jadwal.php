<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Jadwal extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'jadwals';

    protected $fillable = [
        'mata_pelatihan_id',
        'penugasan_pengajar_id',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'sesi_ke',
        'jp_rencana',
        'ruangan',
        'metode',
        'status',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'jp_rencana' => 'decimal:2',
            'sesi_ke' => 'integer',
        ];
    }

    public function mataPelatihan()
    {
        return $this->belongsTo(MataPelatihan::class);
    }

    public function penugasanPengajar()
    {
        return $this->belongsTo(PenugasanPengajar::class);
    }

    public function realisasi()
    {
        return $this->hasOne(RealisasiJadwal::class);
    }
}
