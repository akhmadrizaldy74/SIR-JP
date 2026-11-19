<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KonversiJp extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'konversi_jps';

    protected $fillable = [
        'widyaiswara_id',
        'tanggal_kegiatan',
        'nama_kegiatan',
        'kategori_konversi',
        'jumlah_jp',
        'jp_dibayarkan',
        'informasi_pembayaran',
        'nomor_sk',
        'status_validasi',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_kegiatan' => 'date',
            'jumlah_jp' => 'decimal:2',
            'jp_dibayarkan' => 'decimal:2',
        ];
    }

    public function widyaiswara()
    {
        return $this->belongsTo(Widyaiswara::class);
    }
}
