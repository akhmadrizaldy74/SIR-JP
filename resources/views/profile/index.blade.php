@extends('layouts.app')

@section('title', 'Profil Pengguna')
@section('page_title', 'Profil Penyelenggara')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Profil Pengguna</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-4 col-md-5 mb-4">
        <div class="card-custom text-center p-4">
            <div class="mx-auto mb-3 rounded-circle bg-light d-flex align-items-center justify-content-center text-primary" style="width: 90px; height: 90px; font-size: 2.5rem; border: 3px solid #DBEAFE;">
                <i class="bi bi-person-fill"></i>
            </div>
            <h5 class="fw-bold mb-1">{{ $user->name }}</h5>
            <p class="text-muted small mb-3">{{ $user->email }}</p>
            <span class="badge bg-primary px-3 py-2 rounded-pill">Penyelenggara (Administrator)</span>
            <hr class="my-4">
            <div class="text-start small">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Username:</span>
                    <span class="fw-semibold">{{ $user->username ?? '-' }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Status:</span>
                    <span class="badge bg-success">{{ $user->status }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Terdaftar Sejak:</span>
                    <span class="fw-semibold">{{ $user->created_at->format('d M Y') }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8 col-md-7">
        <div class="card-custom">
            <div class="card-custom-header">
                <h5 class="card-custom-title">
                    <i class="bi bi-pencil-square text-primary"></i> Edit Informasi Akun
                </h5>
            </div>
            <div class="p-4">
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold small">Nama Lengkap Penyelenggara <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="username" class="form-label fw-semibold small">Username <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('username') is-invalid @enderror" id="username" name="username" value="{{ old('username', $user->username) }}" required>
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label fw-semibold small">Alamat Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="phone" class="form-label fw-semibold small">Nomor Telepon / WhatsApp</label>
                        <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="Contoh: 081234567890">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('profile.password') }}" class="btn btn-outline-warning">
                            <i class="bi bi-key me-1"></i> Ganti Password
                        </a>
                        <button type="submit" class="btn btn-navy">
                            <i class="bi bi-save me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
