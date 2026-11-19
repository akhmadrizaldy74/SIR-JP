@extends('layouts.auth')

@section('title', 'Registrasi Akun Penyelenggara - SIR-JP')

@section('content')
<h5 class="fw-bold text-dark mb-1">Registrasi Penyelenggara</h5>
<p class="text-muted small mb-4">Daftarkan akun penyelenggara baru dengan NIK resmi.</p>

<form action="{{ route('register.post') }}" method="POST">
    @csrf

    <div class="mb-3">
        <label for="name" class="form-label fw-semibold small text-dark">Nama Lengkap <span class="text-danger">*</span></label>
        <div class="input-group">
            <span class="input-group-text bg-light text-muted border-end-0">
                <i class="bi bi-person"></i>
            </span>
            <input type="text" class="form-control border-start-0 ps-0 @error('name') is-invalid @enderror" 
                   id="name" name="name" value="{{ old('name') }}" 
                   placeholder="Contoh: Andi Kurniawan, S.Kom." required autofocus>
        </div>
        @error('name')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="nik" class="form-label fw-semibold small text-dark">NIK (16 Digit) <span class="text-danger">*</span></label>
        <div class="input-group">
            <span class="input-group-text bg-light text-muted border-end-0">
                <i class="bi bi-card-heading"></i>
            </span>
            <input type="text" class="form-control border-start-0 ps-0 @error('nik') is-invalid @enderror" 
                   id="nik" name="nik" value="{{ old('nik') }}" maxlength="16" pattern="[0-9]{16}"
                   placeholder="Contoh: 3171012304900001" required>
        </div>
        <div class="form-text small">Nomor Induk Kependudukan 16 digit angka sesuai KTP.</div>
        @error('nik')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="email" class="form-label fw-semibold small text-dark">Alamat Email <span class="text-danger">*</span></label>
        <div class="input-group">
            <span class="input-group-text bg-light text-muted border-end-0">
                <i class="bi bi-envelope"></i>
            </span>
            <input type="email" class="form-control border-start-0 ps-0 @error('email') is-invalid @enderror" 
                   id="email" name="email" value="{{ old('email') }}" 
                   placeholder="Contoh: andi@kemnaker.go.id" required>
        </div>
        @error('email')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="username" class="form-label fw-semibold small text-dark">Username (Opsional)</label>
            <input type="text" class="form-control @error('username') is-invalid @enderror" 
                   id="username" name="username" value="{{ old('username') }}" 
                   placeholder="andikurniawan">
            @error('username')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-md-6 mb-3">
            <label for="phone" class="form-label fw-semibold small text-dark">No. Handphone</label>
            <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                   id="phone" name="phone" value="{{ old('phone') }}" 
                   placeholder="08123456789">
            @error('phone')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="password" class="form-label fw-semibold small text-dark">Kata Sandi <span class="text-danger">*</span></label>
            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                   id="password" name="password" placeholder="Min. 6 karakter" required>
            @error('password')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-md-6 mb-3">
            <label for="password_confirmation" class="form-label fw-semibold small text-dark">Konfirmasi Sandi <span class="text-danger">*</span></label>
            <input type="password" class="form-control" 
                   id="password_confirmation" name="password_confirmation" placeholder="Ulangi sandi" required>
        </div>
    </div>

    <button type="submit" class="btn btn-navy w-100 py-2 fw-semibold d-flex align-items-center justify-content-center gap-2 shadow-sm mb-3">
        <i class="bi bi-person-check fs-5"></i>
        <span>Daftar Akun Penyelenggara</span>
    </button>
</form>

<div class="text-center pt-2">
    <span class="small text-muted">Sudah punya akun penyelenggara?</span><br>
    <a href="{{ route('login') }}" class="small fw-semibold text-primary text-decoration-none">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Halaman Masuk
    </a>
</div>
@endsection
