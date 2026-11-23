<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Tugas extends Model
{
    use HasFactory;

    protected $table = 'tugas';

    protected $fillable = [
        'mata_pelatihan_id',
        'nama_mata_pelatihan',
        'judul',
        'deskripsi',
        'kategori',
        'penyelenggara',
        'tanggal_mulai',
        'tanggal_selesai',
        'file_materi',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_mulai' => 'datetime',
        'tanggal_selesai' => 'datetime',
    ];

    public function mataPelatihan(): BelongsTo
    {
        return $this->belongsTo(MataPelatihan::class, 'mata_pelatihan_id');
    }

    public function pengumpulan(): HasMany
    {
        return $this->hasMany(PengumpulanTugas::class, 'tugas_id');
    }

    public function pengumpulanByUser(int $pesertaId): HasOne
    {
        return $this->hasOne(PengumpulanTugas::class, 'tugas_id')->where('peserta_id', $pesertaId);
    }
}
