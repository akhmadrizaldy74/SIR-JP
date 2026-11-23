<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TahunKurikulum extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tahun_kurikulums';

    protected $fillable = [
        'tahun',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];
}
