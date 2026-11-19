@extends('layouts.auth')

@section('title', 'Masuk ke Sistem - Penyelenggara')

@section('content')
<h5 class="fw-bold text-dark mb-1">Masuk Penyelenggara</h5>
<p class="text-muted small mb-4">Gunakan NIK, email, atau username untuk mengakses sistem SIR-JP.</p>

<form action="{{ route('login.post') }}" method="POST">
    @csrf
    <div class="mb-3">
        <label for="login" class="form-label fw-semibold small text-dark">NIK / Email / Username</label>
        <div class="input-group">
            <span class="input-group-text bg-light text-muted border-end-0">
                <i class="bi bi-person-badge"></i>
            </span>
            <input type="text" class="form-control border-start-0 ps-0 @error('login') is-invalid @enderror" 
                   id="login" name="login" value="{{ old('login', 'penyelenggara@sir-jp.go.id') }}" 
                   placeholder="Contoh: 3171012345670001 atau penyelenggara" required autofocus>
        </div>
        @error('login')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="password" class="form-label fw-semibold small text-dark">Kata Sandi</label>
        <div class="input-group">
            <span class="input-group-text bg-light text-muted border-end-0">
                <i class="bi bi-lock"></i>
            </span>
            <input type="password" class="form-control border-start-0 ps-0 @error('password') is-invalid @enderror" 
                   id="password" name="password" placeholder="Masukkan kata sandi" required>
        </div>
        @error('password')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="remember" id="remember" checked>
            <label class="form-check-label small text-muted" for="remember">
                Ingat saya di perangkat ini
            </label>
        </div>
    </div>

    <button type="submit" class="btn btn-navy w-100 py-2 fw-semibold d-flex align-items-center justify-content-center gap-2 shadow-sm mb-3">
        <i class="bi bi-box-arrow-in-right fs-5"></i>
        <span>Masuk ke SIR-JP</span>
    </button>
</form>

<div class="text-center pt-2 mb-3">
    <span class="small text-muted">Belum terdaftar sebagai penyelenggara?</span><br>
    <a href="{{ route('register') }}" class="small fw-semibold text-primary text-decoration-none">
        <i class="bi bi-person-plus me-1"></i> Registrasi Akun Penyelenggara Baru (NIK)
    </a>
</div>

<div class="p-3 rounded bg-light border text-center small text-muted">
    <i class="bi bi-info-circle me-1 text-primary"></i>
    Akun Default: NIK <strong>3171012345670001</strong> / user: <strong>penyelenggara</strong> (Pass: <strong>password123</strong>)
</div>
@endsection
