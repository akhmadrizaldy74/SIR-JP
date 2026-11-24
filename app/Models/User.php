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

    public function getMaskedNikAttribute(): string
    {
        return \App\Services\SecurityMaskingService::maskNik($this->nik);
    }

    public function getMaskedPhoneAttribute(): string
    {
        return \App\Services\SecurityMaskingService::maskPhone($this->phone);
    }

    public function getMaskedEmailAttribute(): string
    {
        return \App\Services\SecurityMaskingService::maskEmail($this->email);
    }

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
        if ($this->isGuest()) {
            return false;
        }
        return $this->role === 'penyelenggara' || $this->role === 'admin' 
            || (is_array($this->roles) && (in_array('Administrator', $this->roles) || in_array('Penyelenggara', $this->roles)));
    }

    public function isAdministrator(): bool
    {
        if ($this->isGuest()) {
            return false;
        }
        return strcasecmp($this->getActiveRole(), 'Administrator') === 0;
    }

    public function isPengajar(): bool
    {
        if ($this->isGuest()) {
            return false;
        }
        return strcasecmp($this->getActiveRole(), 'Pengajar') === 0;
    }

    public function isGuest(): bool
    {
        if (strcasecmp($this->role, 'guest') === 0) {
            if (is_array($this->roles) && count($this->roles) > 0) {
                $nonGuest = array_filter($this->roles, function($r) {
                    return !in_array(strtolower($r), ['guest']);
                });
                if (count($nonGuest) > 0) {
                    return false;
                }
            }
            return true;
        }

        if (is_array($this->roles) && count($this->roles) > 0) {
            $hasGuest = in_array('Guest', $this->roles) || in_array('guest', $this->roles);
            $nonGuest = array_filter($this->roles, function($r) {
                return !in_array(strtolower($r), ['guest']);
            });
            return $hasGuest && count($nonGuest) === 0;
        }

        return false;
    }

    public function isPeserta(): bool
    {
        if ($this->isGuest()) {
            return false;
        }
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
        if ($this->isGuest()) {
            return ['Guest'];
        }

        if (is_array($this->roles) && count($this->roles) > 0) {
            $roles = array_map(function($r) {
                return (in_array(strtolower($r), ['peserta', 'peserta diklat'])) ? 'Peserta' : $r;
            }, $this->roles);
            return array_values(array_unique($roles));
        }

        if ($this->role === 'peserta') {
            return ['Peserta'];
        }

        if ($this->role === 'widyaiswara' || $this->role === 'pengajar') {
            return ['Pengajar'];
        }

        if ($this->role === 'admin') {
            return ['Administrator', 'Penyelenggara', 'Pengajar'];
        }

        // Default staf administrasi
        return ['Administrator', 'Penyelenggara', 'Pengajar'];
    }

    /**
     * Dapatkan peran yang sedang aktif saat ini (Active Mode).
     */
    public function getActiveRole(): string
    {
        if ($this->isGuest()) {
            return 'Guest';
        }

        $available = $this->getAvailableRoles();
        $currentSessionRole = session('active_role');

        if ($currentSessionRole) {
            if (in_array(strtolower($currentSessionRole), ['peserta', 'peserta diklat'])) {
                $currentSessionRole = 'Peserta';
            }
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

        if (in_array('Pengajar', $available)) {
            return 'Pengajar';
        }

        if (in_array('Peserta', $available)) {
            return 'Peserta';
        }

        return $available[0] ?? 'Guest';
    }

    public function logAktivitas()
    {
        return $this->hasMany(LogAktivitas::class);
    }

    /**
     * Dapatkan atau buat profil Peserta yang tersinkronisasi aman dengan akun pengguna.
     */
    public function getOrCreatePesertaProfile(): Peserta
    {
        // 1. Cari Peserta yang sudah ada berdasarkan email atau NIK/NIP
        $peserta = Peserta::where(function ($q) {
            if (!empty($this->email)) {
                $q->where('email', $this->email);
            }
            if (!empty($this->nik)) {
                $q->orWhere('nip', $this->nik);
            }
        })->first();

        if ($peserta) {
            // Update data yang mungkin berubah
            $peserta->update([
                'nama' => $this->name,
                'email' => $this->email ?: $peserta->email,
                'nip' => $this->nik ?: $peserta->nip,
            ]);
            return $peserta;
        }

        // 2. Pastikan NIP unik jika NIK user kosong atau sudah digunakan data lain
        $nip = $this->nik;
        if (empty($nip) || Peserta::where('nip', $nip)->exists()) {
            $nip = 'NIK-' . substr(md5(($this->email ?: $this->username) . time()), 0, 10);
        }

        return Peserta::create([
            'nip' => $nip,
            'nama' => $this->name,
            'email' => $this->email,
            'jenis_kelamin' => 'Laki-laki',
            'instansi' => 'PPSDM Kemnaker',
        ]);
    }
}
