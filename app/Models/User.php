<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'nik',
        'name',
        'username',
        'email',
        'phone',
        'role',
        'roles',
        'bidang',
        'avatar',
        'status',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'roles' => 'array',
            'bidang' => 'array',
        ];
    }

    public function isPenyelenggara(): bool
    {
        return $this->role === 'penyelenggara' || $this->role === 'admin' 
            || (is_array($this->roles) && (in_array('Administrator', $this->roles) || in_array('Penyelenggara', $this->roles)));
    }

    public function isPeserta(): bool
    {
        if ($this->role === 'peserta') {
            return true;
        }
        if (is_array($this->roles)) {
            $hasPeserta = in_array('Peserta', $this->roles) || in_array('Peserta Diklat', $this->roles);
            $hasAdmin = in_array('Administrator', $this->roles) || in_array('Penyelenggara', $this->roles);
            return $hasPeserta && !$hasAdmin;
        }
        return false;
    }

    public function logAktivitas()
    {
        return $this->hasMany(LogAktivitas::class);
    }
}
