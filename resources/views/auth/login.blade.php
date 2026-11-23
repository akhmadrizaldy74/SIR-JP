@extends('layouts.auth')

@section('title', 'Masuk ke Sistem - Penyelenggara')

@section('content')
<div class="auth-greeting-title">HELLO!</div>
<p class="auth-greeting-subtitle">
    Silakan masuk ke akun penyelenggara Anda untuk mengelola pelatihan, jadwal, dan rekapitulasi JP.
</p>

@include('partials.alerts')

<form action="{{ route('login.post') }}" method="POST">
    @csrf

    <!-- Input Username / NIK / Email -->
    <div class="mb-3">
        <label for="login" class="form-label small fw-bold text-secondary text-uppercase" style="letter-spacing: 0.5px; font-size: 0.75rem;">
            Email or Username / NIK
        </label>
        <div class="input-group auth-input-group @error('login') border-danger @enderror">
            <span class="input-group-text"><i class="bi bi-person fs-5"></i></span>
            <input type="text" 
                   class="form-control" 
                   id="login" 
                   name="login" 
                   value="{{ old('login', 'penyelenggara@sir-jp.go.id') }}" 
                   placeholder="email or username" 
                   required 
                   autofocus>
        </div>
        @error('login')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>

    <!-- Input Password -->
    <div class="mb-3">
        <label for="password" class="form-label small fw-bold text-secondary text-uppercase" style="letter-spacing: 0.5px; font-size: 0.75rem;">
            Password
        </label>
        <div class="input-group auth-input-group @error('password') border-danger @enderror">
            <span class="input-group-text"><i class="bi bi-lock fs-5"></i></span>
            <input type="password" 
                   class="form-control" 
                   id="password" 
                   name="password" 
                   placeholder="password" 
                   required>
            <button class="btn btn-link text-muted pe-3 text-decoration-none" type="button" onclick="const p = document.getElementById('password'); p.type = p.type === 'password' ? 'text' : 'password'; this.querySelector('i').classList.toggle('bi-eye'); this.querySelector('i').classList.toggle('bi-eye-slash');">
                <i class="bi bi-eye"></i>
            </button>
        </div>
        @error('password')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>

    <!-- Remember Me & Forgot Password Row Sesuai Template Gambar -->
    <div class="d-flex justify-content-between align-items-center mb-4 text-muted small">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="remember" id="remember" checked style="cursor: pointer;">
            <label class="form-check-label text-secondary" for="remember" style="cursor: pointer;">
                Remember
            </label>
        </div>
        <a href="javascript:void(0)" onclick="Swal.fire('Bantuan Kata Sandi', 'Untuk mengatur ulang kata sandi akun admin, silakan hubungi Administrator Sistem Pusdiklat PPSDM Kemnaker.', 'info');" class="text-muted text-decoration-none">
            Forgot your password?
        </a>
    </div>

    <!-- Tombol NEXT Sesuai Template -->
    <button type="submit" class="btn btn-auth-next w-100 mb-3">
        <span>NEXT</span>
        <i class="bi bi-arrow-right"></i>
    </button>
</form>

<!-- Navigation Switcher -->
<div class="text-center pt-2 border-top">
    <div class="small text-muted mb-2">
        Login sebagai Peserta Pelatihan?
        <a href="{{ route('portal.login') }}" class="fw-bold text-primary text-decoration-none ms-1">
            Portal Presensi Peserta &rarr;
        </a>
    </div>
    <div class="small text-muted">
        Belum memiliki akun?
        <a href="{{ route('register') }}" class="fw-semibold text-secondary text-decoration-none ms-1">
            Daftar Penyelenggara Baru
        </a>
    </div>
</div>

<!-- Quick Test Credentials Box -->
<div class="mt-4 p-2 rounded-3 bg-light border text-center small text-muted">
    <i class="bi bi-info-circle me-1 text-primary"></i> Akun Uji Coba: <strong class="user-select-all text-dark">penyelenggara</strong> / <strong class="user-select-all text-dark">password123</strong>
</div>
@endsection
