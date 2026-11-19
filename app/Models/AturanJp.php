<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AturanJp extends Model
{
    use HasFactory;

    protected $table = 'aturan_jps';

    protected $fillable = [
        'nama_aturan',
        'minimum_jp_bulanan',
        'durasi_per_jp_menit',
        'is_active',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'minimum_jp_bulanan' => 'float',
            'durasi_per_jp_menit' => 'integer',
            'is_active' => 'boolean',
        ];
    }
}
