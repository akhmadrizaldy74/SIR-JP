<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AspekPenilaianSikap extends Model
{
    use HasFactory;

    protected $table = 'aspek_penilaian_sikaps';

    protected $fillable = [
        'pelatihan_id',
        'kategori',
        'judul',
        'aspek_penilaian',
        'is_active',
    ];

    protected $casts = [
        'aspek_penilaian' => 'array',
        'is_active' => 'boolean',
    ];

    public function pelatihan()
    {
        return $this->belongsTo(Pelatihan::class, 'pelatihan_id');
    }
}
