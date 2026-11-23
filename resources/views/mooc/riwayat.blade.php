@extends('layouts.app')

@section('title', 'LMS MOOC - Riwayat Pelatihan Peserta')
@section('page_title', 'Riwayat Pelatihan Peserta')

@section('breadcrumb_custom')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">home</a></li>
    <li class="breadcrumb-item">LMS MOOC</li>
    <li class="breadcrumb-item active" aria-current="page">Riwayat Pelatihan Peserta</li>
@endsection

@section('content')
<div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h5 class="fw-bold text-dark mb-1">Riwayat Progres Belajar Peserta LMS MOOC</h5>
            <p class="text-muted small mb-0">Pantau progres penyelesaian materi video, kuis, dan sertifikasi peserta diklat.</p>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr class="text-muted small text-uppercase">
                    <th style="width: 50px;">No</th>
                    <th>Peserta</th>
                    <th>Program Diklat</th>
                    <th>Progres Pembelajaran</th>
                    <th>Status</th>
                    <th class="text-center" style="width: 100px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pesertas as $index => $item)
                <tr>
                    <td class="text-muted small">{{ $pesertas->firstItem() + $index }}</td>
                    <td>
                        <div class="fw-semibold text-dark">{{ $item->nama }}</div>
                        <div class="text-muted small">NIP/NIK: {{ $item->nip ?? '-' }}</div>
                    </td>
                    <td>
                        <div class="text-dark">{{ $item->angkatan->pelatihan->nama_pelatihan ?? 'Pelatihan Diklat Digital' }}</div>
                        <div class="text-muted small">{{ $item->angkatan->nomor_angkatan ?? 'Angkatan Reguler' }}</div>
                    </td>
                    <td style="width: 220px;">
                        <div class="d-flex align-items-center gap-2">
                            <div class="progress flex-grow-1" style="height: 6px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ (($index * 23) % 60) + 40 }}%;"></div>
                            </div>
                            <span class="text-muted small fw-medium">{{ (($index * 23) % 60) + 40 }}%</span>
                        </div>
                    </td>
                    <td><span class="badge bg-success-subtle text-success">Mengikuti</span></td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-secondary" onclick="Swal.fire('Progres Peserta', '{{ $item->nama }} sedang mempelajari modul.', 'info')">
                            <i class="bi bi-clock-history"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">Belum ada data riwayat peserta.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($pesertas->hasPages())
    <div class="d-flex justify-content-end mt-4">
        {{ $pesertas->links() }}
    </div>
    @endif
</div>
@endsection
