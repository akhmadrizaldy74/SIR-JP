<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengaturanSesi extends Model
{
    use HasFactory;

    protected $table = 'pengaturan_sesies';

    protected $fillable = [
        'nama_sesi',
        'tipe',
        'nomor_sesi',
        'jam_mulai',
        'jam_selesai',
        'durasi_menit',
        'jumlah_jp',
        'urutan',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'nomor_sesi' => 'integer',
            'durasi_menit' => 'integer',
            'jumlah_jp' => 'decimal:2',
            'urutan' => 'integer',
            'is_active' => 'boolean',
        ];
    }
}
