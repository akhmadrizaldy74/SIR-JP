@extends('layouts.app')

@section('title', 'LMS MOOC - Komentar Pelatihan')
@section('page_title', 'Komentar Pelatihan LMS MOOC')

@section('breadcrumb_custom')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">home</a></li>
    <li class="breadcrumb-item">LMS MOOC</li>
    <li class="breadcrumb-item active" aria-current="page">Komentar Pelatihan</li>
@endsection

@section('content')
<div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h5 class="fw-bold text-dark mb-1">Forum Diskusi & Ulasan Materi Pembelajaran</h5>
            <p class="text-muted small mb-0">Pertanyaan dan tanggapan dari peserta diklat terkait materi yang diajarkan oleh Pengajar.</p>
        </div>
    </div>

    <div class="list-group list-group-flush border-top">
        <div class="list-group-item py-3 px-0">
            <div class="d-flex align-items-start gap-3">
                <div class="avatar-circle rounded-circle bg-light border d-flex align-items-center justify-content-center flex-shrink-0" style="width: 40px; height: 40px;">
                    <i class="bi bi-person text-secondary"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <h6 class="fw-bold text-dark mb-0" style="font-size: 0.9rem;">Budi Santoso (Peserta)</h6>
                        <span class="text-muted small">2 jam yang lalu</span>
                    </div>
                    <div class="badge bg-light text-primary border mb-2">Mata Pelatihan: Pengantar Hukum Ketenagakerjaan</div>
                    <p class="text-secondary small mb-2">Mohon izin bertanya kepada Pengajar, untuk studi kasus pada modul 2 apakah format penyelesaiannya menggunakan template standar atau bebas?</p>
                    <button class="btn btn-sm btn-outline-primary" onclick="Swal.fire('Balas Peserta', 'Fitur membalas komentar peserta oleh Pengajar.', 'info')">
                        <i class="bi bi-reply-fill me-1"></i> Balas
                    </button>
                </div>
            </div>
        </div>

        <div class="list-group-item py-3 px-0">
            <div class="d-flex align-items-start gap-3">
                <div class="avatar-circle rounded-circle bg-light border d-flex align-items-center justify-content-center flex-shrink-0" style="width: 40px; height: 40px;">
                    <i class="bi bi-person text-secondary"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <h6 class="fw-bold text-dark mb-0" style="font-size: 0.9rem;">Siti Aminah (Peserta)</h6>
                        <span class="text-muted small">Kemarin, 14:20</span>
                    </div>
                    <div class="badge bg-light text-primary border mb-2">Mata Pelatihan: Mediasi & Konsiliasi Hubungan Industrial</div>
                    <p class="text-secondary small mb-2">Terima kasih banyak atas materi videonya Bapak Pengajar, pembahasannya sangat jelas dan runtut.</p>
                    <button class="btn btn-sm btn-outline-primary" onclick="Swal.fire('Balas Peserta', 'Fitur membalas komentar peserta oleh Pengajar.', 'info')">
                        <i class="bi bi-reply-fill me-1"></i> Balas
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
