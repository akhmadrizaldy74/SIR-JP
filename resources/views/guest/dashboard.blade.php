@extends('layouts.app')

@section('title', 'Status Akun Guest - Menunggu Penetapan Role')
@section('page_title', 'Status Akun Pengguna')

@section('breadcrumb_custom')
    <li class="breadcrumb-item"><a href="{{ route('guest.dashboard') }}" class="text-decoration-none">Home</a></li>
    <li class="breadcrumb-item active" aria-current="page">Status Guest</li>
@endsection

@section('content')
<div class="container-fluid px-0">
    <!-- Notice / Alert Header -->
    <div class="alert border-0 shadow-sm rounded-4 p-4 mb-4" style="background: linear-gradient(135deg, #FFFBEB 0%, #FEF3C7 100%); border-left: 5px solid #F59E0B !important;">
        <div class="d-flex align-items-start gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" 
                 style="width: 48px; height: 48px; background-color: #FDE68A; color: #B45309;">
                <i class="bi bi-hourglass-split fs-4"></i>
            </div>
            <div>
                <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                    <h5 class="fw-bold mb-0" style="color: #92400E;">Akun Terdaftar sebagai Guest (Menunggu Penetapan Hak Akses)</h5>
                    <span class="badge rounded-pill px-3 py-1 fw-semibold" style="background-color: #F59E0B; color: #FFFFFF; font-size: 0.75rem;">
                        Status: Pending Role Assignment
                    </span>
                </div>
                <p class="mb-0 text-muted" style="font-size: 0.925rem; line-height: 1.5;">
                    Selamat datang di <strong>Kemnaker Corporate University (KCLC)</strong>. Akun Anda berhasil terdaftar di sistem. Untuk alasan keamanan dan kepatuhan tata kelola data pelatihan, Administrator kami akan segera memeriksa profil Anda dan memberikan peran serta hak akses yang sesuai.
                </p>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Kolom Kiri: Rincian Akun & Status -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 h-100">
                <div class="d-flex align-items-center justify-content-between pb-3 mb-3 border-bottom">
                    <h6 class="fw-bold text-dark mb-0">
                        <i class="bi bi-person-lines-fill text-primary me-2"></i>Informasi Akun Anda
                    </h6>
                    <span class="badge bg-light text-secondary border px-2 py-1 small">ID: #{{ $user->id }}</span>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-sm-6">
                        <label class="text-muted small fw-medium d-block">Nama Lengkap</label>
                        <div class="fw-bold text-dark fs-6">{{ $user->name }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="text-muted small fw-medium d-block">Email Terdaftar</label>
                        <div class="fw-semibold text-dark">{{ $user->email }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="text-muted small fw-medium d-block">Nomor Induk Kependudukan (NIK)</label>
                        <div class="fw-semibold font-monospace text-secondary">{{ $user->nik ? substr($user->nik, 0, 6) . '******' . substr($user->nik, -4) : 'Belum dilengkapi' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="text-muted small fw-medium d-block">Tanggal Pendaftaran</label>
                        <div class="fw-semibold text-dark">
                            {{ $user->created_at ? $user->created_at->translatedFormat('d F Y, H:i') . ' WIB' : '-' }}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <label class="text-muted small fw-medium d-block">Peran Saat Ini</label>
                        <div class="mt-1">
                            <span class="badge rounded-pill px-3 py-1 fw-semibold" style="background-color: #FEF3C7; color: #B45309; border: 1px solid #FDE68A;">
                                <i class="bi bi-shield-lock me-1"></i> Guest (Tamu)
                            </span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <label class="text-muted small fw-medium d-block">Status Akun</label>
                        <div class="mt-1">
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1 fw-semibold">
                                <i class="bi bi-check-circle me-1"></i> Aktif Terdaftar
                            </span>
                        </div>
                    </div>
                </div>

                <div class="p-3 rounded-3" style="background-color: #F8FAFC; border: 1px dashed #CBD5E1;">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div class="small text-muted">
                            <i class="bi bi-arrow-repeat me-1 text-primary"></i>
                            Periksa apakah Administrator telah memperbarui hak akses Anda:
                        </div>
                        <button type="button" class="btn btn-primary btn-sm px-3 rounded-pill fw-semibold shadow-sm" onclick="window.location.reload();">
                            <i class="bi bi-arrow-clockwise me-1"></i> Muat Ulang Status
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Panduan & Bantuan -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 h-100">
                <h6 class="fw-bold text-dark mb-3 pb-2 border-bottom">
                    <i class="bi bi-shield-check text-success me-2"></i>Alur Penugasan Hak Akses
                </h6>

                <ol class="list-unstyled mb-4" style="position: relative;">
                    <li class="d-flex align-items-start gap-3 mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary text-white fw-bold flex-shrink-0" style="width: 28px; height: 28px; font-size: 0.8rem;">1</div>
                        <div>
                            <strong class="text-dark d-block small">Registrasi Berhasil</strong>
                            <span class="text-muted small">Akun Anda otomatis dibuat dengan peran tamu (Guest).</span>
                        </div>
                    </li>
                    <li class="d-flex align-items-start gap-3 mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-warning text-dark fw-bold flex-shrink-0" style="width: 28px; height: 28px; font-size: 0.8rem;">2</div>
                        <div>
                            <strong class="text-dark d-block small">Verifikasi Administrator</strong>
                            <span class="text-muted small">Admin akan memeriksa data diri Anda dan menetapkan peran operasional (Penyelenggara, Pengajar, Peserta, dsb).</span>
                        </div>
                    </li>
                    <li class="d-flex align-items-start gap-3 mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-light text-secondary border fw-bold flex-shrink-0" style="width: 28px; height: 28px; font-size: 0.8rem;">3</div>
                        <div>
                            <strong class="text-dark d-block small">Akses Terbuka Otomatis</strong>
                            <span class="text-muted small">Begitu peran disetujui, Anda akan langsung diarahkan ke menu dashboard yang sesuai.</span>
                        </div>
                    </li>
                </ol>

                <div class="mt-auto pt-3 border-top">
                    <div class="small fw-semibold text-secondary mb-2">Membutuhkan Bantuan Mendesak?</div>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="mailto:admin.corpu@kemnaker.go.id?subject=Permohonan%20Aktivasi%20Peran%20KCLC%20-%20{{ urlencode($user->name) }}" class="btn btn-outline-secondary btn-sm rounded-3">
                            <i class="bi bi-envelope me-1"></i> Email Administrator
                        </a>
                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-3">
                                <i class="bi bi-box-arrow-right me-1"></i> Keluar (Logout)
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
