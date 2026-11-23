@extends('layouts.auth')

@section('title', 'Registrasi Akun Penyelenggara - SIR-JP')

@section('content')
<div class="auth-greeting-title">DAFTAR</div>
<p class="auth-greeting-subtitle">
    Daftarkan akun penyelenggara baru menggunakan NIK resmi Anda.
</p>

@include('partials.alerts')

<form action="{{ route('register.post') }}" method="POST">
    @csrf

    <!-- Nama Lengkap -->
    <div class="mb-3">
        <label for="name" class="form-label small fw-bold text-secondary text-uppercase" style="letter-spacing: 0.5px; font-size: 0.75rem;">
            Nama Lengkap <span class="text-danger">*</span>
        </label>
        <div class="input-group auth-input-group @error('name') border-danger @enderror">
            <span class="input-group-text"><i class="bi bi-person fs-5"></i></span>
            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" placeholder="Contoh: Andi Kurniawan, S.Kom." required autofocus>
        </div>
        @error('name')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div class="row g-2 mb-3">
        <!-- NIK -->
        <div class="col-sm-6">
            <label for="nik" class="form-label small fw-bold text-secondary text-uppercase" style="letter-spacing: 0.5px; font-size: 0.75rem;">
                NIK (16 Digit) <span class="text-danger">*</span>
            </label>
            <div class="input-group auth-input-group @error('nik') border-danger @enderror">
                <span class="input-group-text"><i class="bi bi-card-heading fs-5"></i></span>
                <input type="text" class="form-control" id="nik" name="nik" value="{{ old('nik') }}" maxlength="16" pattern="[0-9]{16}" placeholder="16 Digit NIK" required>
            </div>
            @error('nik')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <!-- Username -->
        <div class="col-sm-6">
            <label for="username" class="form-label small fw-bold text-secondary text-uppercase" style="letter-spacing: 0.5px; font-size: 0.75rem;">
                Username
            </label>
            <div class="input-group auth-input-group @error('username') border-danger @enderror">
                <span class="input-group-text"><i class="bi bi-at fs-5"></i></span>
                <input type="text" class="form-control" id="username" name="username" value="{{ old('username') }}" placeholder="username">
            </div>
            @error('username')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <!-- Email -->
    <div class="mb-3">
        <label for="email" class="form-label small fw-bold text-secondary text-uppercase" style="letter-spacing: 0.5px; font-size: 0.75rem;">
            Alamat Email <span class="text-danger">*</span>
        </label>
        <div class="input-group auth-input-group @error('email') border-danger @enderror">
            <span class="input-group-text"><i class="bi bi-envelope fs-5"></i></span>
            <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" placeholder="email@kemnaker.go.id" required>
        </div>
        @error('email')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div class="row g-2 mb-4">
        <!-- Password -->
        <div class="col-sm-6">
            <label for="password" class="form-label small fw-bold text-secondary text-uppercase" style="letter-spacing: 0.5px; font-size: 0.75rem;">
                Password <span class="text-danger">*</span>
            </label>
            <div class="input-group auth-input-group @error('password') border-danger @enderror">
                <span class="input-group-text"><i class="bi bi-lock fs-5"></i></span>
                <input type="password" class="form-control" id="password" name="password" placeholder="min 8 karakter" required>
            </div>
            @error('password')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <!-- Konfirmasi Password -->
        <div class="col-sm-6">
            <label for="password_confirmation" class="form-label small fw-bold text-secondary text-uppercase" style="letter-spacing: 0.5px; font-size: 0.75rem;">
                Konfirmasi <span class="text-danger">*</span>
            </label>
            <div class="input-group auth-input-group">
                <span class="input-group-text"><i class="bi bi-shield-check fs-5"></i></span>
                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="ulang password" required>
            </div>
        </div>
    </div>

    <!-- Submit Button -->
    <button type="submit" class="btn btn-auth-next w-100 mb-3">
        <span>DAFTAR SEKARANG</span>
        <i class="bi bi-arrow-right"></i>
    </button>
</form>

<div class="text-center pt-2 border-top">
    <div class="small text-muted">
        Sudah memiliki akun?
        <a href="{{ route('login') }}" class="fw-bold text-primary text-decoration-none ms-1">
            Masuk ke Akun Penyelenggara &rarr;
        </a>
    </div>
</div>
@endsection
