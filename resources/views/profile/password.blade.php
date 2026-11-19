@extends('layouts.app')

@section('title', 'Ganti Kata Sandi')
@section('page_title', 'Ganti Kata Sandi')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('profile.index') }}">Profil Pengguna</a></li>
    <li class="breadcrumb-item active" aria-current="page">Ganti Kata Sandi</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6 col-md-8">
        <div class="card-custom">
            <div class="card-custom-header">
                <h5 class="card-custom-title">
                    <i class="bi bi-shield-lock text-warning"></i> Perbarui Kata Sandi Akun
                </h5>
            </div>
            <div class="p-4">
                <form action="{{ route('profile.password.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="current_password" class="form-label fw-semibold small">Kata Sandi Saat Ini <span class="text-danger">*</span></label>
                        <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" required>
                        @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold small">Kata Sandi Baru <span class="text-danger">*</span></label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                        <div class="form-text small">Minimal 6 karakter, kombinasikan huruf dan angka untuk keamanan optimal.</div>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label fw-semibold small">Konfirmasi Kata Sandi Baru <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('profile.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Kembali ke Profil
                        </a>
                        <button type="submit" class="btn btn-navy">
                            <i class="bi bi-check-circle me-1"></i> Perbarui Kata Sandi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
