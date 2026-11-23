<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class ParticipantAccessCode extends Model
{
    use HasFactory;

    protected $table = 'participant_access_codes';

    protected $fillable = [
        'peserta_id',
        'kode_peserta',
        'pin_hash',
        'is_active',
        'last_login_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
    ];

    protected $hidden = [
        'pin_hash',
    ];

    public function peserta()
    {
        return $this->belongsTo(Peserta::class, 'peserta_id');
    }

    public function verifyPin(string $pin): bool
    {
        return Hash::check($pin, $this->pin_hash);
    }
}
