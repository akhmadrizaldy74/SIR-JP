<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogAktivitas extends Model
{
    use HasFactory;

    protected $table = 'log_aktivitas';

    protected $fillable = [
        'user_id',
        'modul',
        'aktivitas',
        'detail',
        'ip_address',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function catat(string $modul, string $aktivitas, ?string $detail = null)
    {
        return static::create([
            'user_id' => auth()->id(),
            'modul' => $modul,
            'aktivitas' => $aktivitas,
            'detail' => $detail,
            'ip_address' => request()->ip(),
        ]);
    }
}
