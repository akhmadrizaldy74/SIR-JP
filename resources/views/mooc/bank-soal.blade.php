@extends('layouts.app')

@section('title', 'LMS MOOC - Bank Soal')
@section('page_title', 'Bank Soal LMS MOOC')

@section('breadcrumb_custom')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">home</a></li>
    <li class="breadcrumb-item">LMS MOOC</li>
    <li class="breadcrumb-item active" aria-current="page">Bank Soal</li>
@endsection

@section('content')
<div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h5 class="fw-bold text-dark mb-1">Manajemen Bank Soal & Kuis</h5>
            <p class="text-muted small mb-0">Kelola butir pertanyaan pre-test, kuis materi, post-test, dan evaluasi modul pembelajaran.</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-primary btn-sm px-3 rounded-3" onclick="Swal.fire('Buat Butir Soal', 'Formulir input soal pilihan ganda dan essay.', 'info')">
                <i class="bi bi-plus-lg me-1"></i> Tambah Bank Soal
            </button>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-md-3 g-3">
        @forelse($pelatihans as $p)
        <div class="col">
            <div class="card border rounded-3 p-3 h-100 shadow-sm">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="badge bg-light text-primary border">Paket Soal</span>
                    <span class="text-muted small">Aktif</span>
                </div>
                <h6 class="fw-bold text-dark mb-1">{{ $p->nama_pelatihan }}</h6>
                <p class="text-muted small mb-3">Tersedia bank soal Pre-Test, Kuis 1-4, dan Post-Test.</p>
                <div class="d-flex justify-content-between align-items-center pt-2 border-top mt-auto">
                    <span class="text-muted small"><i class="bi bi-question-circle me-1"></i> 25 Butir Soal</span>
                    <button class="btn btn-sm btn-outline-primary" onclick="Swal.fire('Bank Soal: {{ $p->nama_pelatihan }}', 'Daftar 25 butir soal siap dievaluasi oleh Pengajar.', 'info')">
                        Kelola Soal
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-4 text-muted">Belum ada paket bank soal.</div>
        @endforelse
    </div>
</div>
@endsection
