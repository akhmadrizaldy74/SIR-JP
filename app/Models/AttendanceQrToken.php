<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceQrToken extends Model
{
    use HasFactory;

    protected $table = 'attendance_qr_tokens';

    protected $fillable = [
        'attendance_session_id',
        'token_hash',
        'expired_at',
        'is_active',
    ];

    protected $casts = [
        'expired_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function session()
    {
        return $this->belongsTo(AttendanceSession::class, 'attendance_session_id');
    }

    public function isValid(): bool
    {
        return $this->is_active && $this->expired_at->isFuture();
    }
}
