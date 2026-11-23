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

    public function isAdministrator(): bool
    {
        return strcasecmp($this->getActiveRole(), 'Administrator') === 0;
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

    /**
     * Dapatkan daftar seluruh peran (Roles) yang dimiliki user.
     */
    public function getAvailableRoles(): array
    {
        if (is_array($this->roles) && count($this->roles) > 0) {
            return array_values(array_unique($this->roles));
        }

        if ($this->role === 'peserta') {
            return ['Peserta Diklat'];
        }

        if ($this->role === 'widyaiswara') {
            return ['Pengajar'];
        }

        if ($this->role === 'admin') {
            return ['Administrator', 'Penyelenggara'];
        }

        // Default staf administrasi
        return ['Administrator', 'Penyelenggara'];
    }

    /**
     * Dapatkan peran yang sedang aktif saat ini (Active Mode).
     */
    public function getActiveRole(): string
    {
        $available = $this->getAvailableRoles();
        $currentSessionRole = session('active_role');

        if ($currentSessionRole) {
            foreach ($available as $role) {
                if (strcasecmp($role, $currentSessionRole) === 0) {
                    return $role;
                }
            }
        }

        if (in_array('Administrator', $available)) {
            return 'Administrator';
        }

        if (in_array('Penyelenggara', $available)) {
            return 'Penyelenggara';
        }

        return $available[0] ?? 'Penyelenggara';
    }

    public function logAktivitas()
    {
        return $this->hasMany(LogAktivitas::class);
    }
}
