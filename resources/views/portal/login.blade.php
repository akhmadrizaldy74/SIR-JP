@extends('layouts.auth')

@section('title', 'Login Peserta Diklat - KCLC Learning Center')

@section('content')
<div class="auth-greeting-title">HELLO!</div>
<p class="auth-greeting-subtitle">
    Masukkan Kode Peserta dan PIN akses 6-digit Anda untuk masuk ke portal pembelajaran diklat.
</p>

@include('partials.alerts')

<form action="{{ route('portal.login.post') }}" method="POST">
    @csrf

    <!-- Kode Peserta -->
    <div class="mb-3">
        <label for="kode_peserta" class="form-label small fw-bold text-secondary text-uppercase" style="letter-spacing: 0.5px; font-size: 0.75rem;">
            Kode Peserta
        </label>
        <div class="input-group auth-input-group @error('kode_peserta') border-danger @enderror">
            <span class="input-group-text"><i class="bi bi-person-badge fs-5"></i></span>
            <input type="text" 
                   class="form-control text-uppercase fw-bold" 
                   id="kode_peserta" 
                   name="kode_peserta" 
                   value="{{ old('kode_peserta') }}" 
                   placeholder="Contoh: PST-2026-0001" 
                   required 
                   autofocus>
        </div>
        @error('kode_peserta')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>

    <!-- PIN Akses -->
    <div class="mb-3">
        <label for="pin" class="form-label small fw-bold text-secondary text-uppercase" style="letter-spacing: 0.5px; font-size: 0.75rem;">
            PIN Akses (6 Digit)
        </label>
        <div class="input-group auth-input-group @error('pin') border-danger @enderror">
            <span class="input-group-text"><i class="bi bi-shield-lock fs-5"></i></span>
            <input type="password" 
                   class="form-control fs-5 letter-spacing-2" 
                   id="pin" 
                   name="pin" 
                   maxlength="10" 
                   placeholder="••••••" 
                   inputmode="numeric" 
                   required>
            <button class="btn btn-link text-muted pe-3 text-decoration-none" type="button" id="btnTogglePin">
                <i class="bi bi-eye" id="iconTogglePin"></i>
            </button>
        </div>
        @error('pin')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>

    <!-- Info Bantuan PIN Row Sesuai Template -->
    <div class="d-flex justify-content-between align-items-center mb-4 text-muted small">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="rememberMePeserta" checked style="cursor: pointer;">
            <label class="form-check-label text-secondary" for="rememberMePeserta" style="cursor: pointer;">
                Ingat perangkat ini
            </label>
        </div>
        <a href="javascript:void(0)" onclick="Swal.fire('Lupa Kode / PIN Peserta?', 'Kode Peserta dan PIN diterbitkan oleh panitia penyelenggara pelatihan. Silakan hubungi operator kelas atau admin diklat untuk reset PIN Anda.', 'info');" class="text-muted text-decoration-none">
            Lupa PIN akses?
        </a>
    </div>

    <!-- Tombol NEXT Sesuai Template -->
    <button type="submit" class="btn btn-auth-next w-100 mb-3">
        <span>NEXT</span>
        <i class="bi bi-arrow-right"></i>
    </button>
</form>

<!-- Navigation Switcher ke Login Penyelenggara -->
<div class="text-center pt-2 border-top">
    <div class="small text-muted mb-2">
        Login sebagai Panitia / Penyelenggara?
        <a href="{{ route('login') }}" class="fw-bold text-primary text-decoration-none ms-1">
            Login Penyelenggara SIR-JP &rarr;
        </a>
    </div>
</div>

<!-- Quick Test Credentials Box -->
<div class="mt-4 p-2 rounded-3 bg-light border text-center small text-muted">
    <i class="bi bi-info-circle me-1 text-primary"></i> Akun Peserta Uji Coba: <strong class="user-select-all text-dark">PST-2026-0001</strong> / PIN: <strong class="user-select-all text-dark">123456</strong>
</div>
@endsection

@section('scripts')
<script>
    const btnTogglePin = document.getElementById('btnTogglePin');
    const inputPin = document.getElementById('pin');
    const iconToggle = document.getElementById('iconTogglePin');

    if (btnTogglePin && inputPin && iconToggle) {
        btnTogglePin.addEventListener('click', function() {
            const isPassword = inputPin.getAttribute('type') === 'password';
            inputPin.setAttribute('type', isPassword ? 'text' : 'password');
            iconToggle.classList.toggle('bi-eye');
            iconToggle.classList.toggle('bi-eye-slash');
        });
    }
</script>
@endsection
