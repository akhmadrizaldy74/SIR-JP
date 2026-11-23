<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JenisPelatihan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'jenis_pelatihans';

    protected $fillable = [
        'nama_jenis',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];
}
