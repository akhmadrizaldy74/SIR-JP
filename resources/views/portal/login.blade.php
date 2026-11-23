@extends('portal.layout')

@section('title', 'Login Peserta Diklat')

@section('content')
<div class="d-flex flex-column align-items-center justify-content-center pt-4">
    <!-- Logo & Header -->
    <div class="text-center mb-4">
        <div class="d-inline-flex align-items-center justify-content-center p-3 rounded-circle shadow-sm mb-3" style="background: linear-gradient(135deg, #1E293B 0%, #0F172A 100%); width: 72px; height: 72px;">
            <span class="badge fw-bold text-white fs-4" style="background: linear-gradient(135deg, #F59E0B 0%, #10B981 100%); border-radius: 8px;">KC</span>
        </div>
        <h4 class="fw-bold text-dark mb-1">Portal Presensi Diklat</h4>
        <p class="text-muted small">Kemnaker Corpu Learning Center (KCLC) PPSDMK</p>
    </div>

    <!-- Login Card -->
    <div class="card card-custom w-100 p-4 border shadow-sm">
        <div class="mb-3 text-center border-bottom pb-3">
            <span class="text-muted small">Masukkan <strong>Kode Peserta</strong> dan <strong>PIN</strong> yang diterbitkan oleh panitia diklat</span>
        </div>

        <form action="{{ route('portal.login.post') }}" method="POST">
            @csrf

            <!-- Kode Peserta -->
            <div class="mb-3">
                <label for="kode_peserta" class="form-label fw-semibold small text-secondary">KODE PESERTA</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-person-badge text-muted"></i></span>
                    <input type="text" 
                           class="form-control border-start-0 py-2 text-uppercase fw-bold @error('kode_peserta') is-invalid @enderror" 
                           id="kode_peserta" 
                           name="kode_peserta" 
                           value="{{ old('kode_peserta') }}" 
                           placeholder="Contoh: PST-2026-0001" 
                           required 
                           autofocus>
                </div>
                @error('kode_peserta')
                    <div class="invalid-feedback d-block small">{{ $message }}</div>
                @enderror
            </div>

            <!-- PIN Akses -->
            <div class="mb-4">
                <label for="pin" class="form-label fw-semibold small text-secondary">PIN AKSES (6 DIGIT)</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-shield-lock text-muted"></i></span>
                    <input type="password" 
                           class="form-control border-start-0 border-end-0 py-2 fs-5 letter-spacing-2 @error('pin') is-invalid @enderror" 
                           id="pin" 
                           name="pin" 
                           maxlength="10" 
                           placeholder="••••••" 
                           inputmode="numeric" 
                           required>
                    <button class="btn btn-outline-secondary border-start-0 bg-light" type="button" id="btnTogglePin">
                        <i class="bi bi-eye" id="iconTogglePin"></i>
                    </button>
                </div>
                @error('pin')
                    <div class="invalid-feedback d-block small">{{ $message }}</div>
                @enderror
            </div>

            <!-- Tombol Masuk -->
            <button type="submit" class="btn btn-primary w-100 py-2 fw-bold rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2">
                <span>Masuk ke Portal</span>
                <i class="bi bi-arrow-right"></i>
            </button>
        </form>

        <div class="mt-4 text-center">
            <small class="text-muted">
                Belum memiliki Kode atau lupa PIN? <br>
                <span class="text-secondary fw-semibold">Silakan hubungi Panitia Penyelenggara Diklat.</span>
            </small>
        </div>
    </div>

    <!-- Link Admin Login -->
    <div class="mt-4 text-center">
        <a href="{{ route('login') }}" class="text-decoration-none text-muted small">
            <i class="bi bi-person-fill-lock"></i> Login sebagai Administrator / Penyelenggara
        </a>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.getElementById('btnTogglePin').addEventListener('click', function() {
        const pinInput = document.getElementById('pin');
        const icon = document.getElementById('iconTogglePin');
        if (pinInput.type === 'password') {
            pinInput.type = 'text';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            pinInput.type = 'password';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    });
</script>
@endsection
