@extends('layouts.app')

@section('title', 'LMS MOOC - Nilai Peserta Ujian Permodul')
@section('page_title', 'Nilai Peserta Ujian Permodul')

@section('breadcrumb_custom')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">home</a></li>
    <li class="breadcrumb-item">LMS MOOC</li>
    <li class="breadcrumb-item active" aria-current="page">Nilai Peserta Ujian Permodul</li>
@endsection

@section('content')
<div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h5 class="fw-bold text-dark mb-1">Rekapitulasi Nilai Ujian Permodul Peserta</h5>
            <p class="text-muted small mb-0">Daftar perolehan nilai pre-test, kuis modul, dan post-test hasil evaluasi belajar.</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-success btn-sm px-3 rounded-3" onclick="Swal.fire('Ekspor Nilai', 'Mengunduh rekap nilai peserta dalam format Excel.', 'success')">
                <i class="bi bi-file-earmark-excel me-1"></i> Ekspor Excel
            </button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr class="text-muted small text-uppercase">
                    <th style="width: 50px;">No</th>
                    <th>Nama Peserta</th>
                    <th>Program Pelatihan</th>
                    <th class="text-center">Pre-Test</th>
                    <th class="text-center">Rata-Rata Kuis</th>
                    <th class="text-center">Post-Test</th>
                    <th class="text-center">Nilai Akhir</th>
                    <th class="text-center">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pesertas as $index => $item)
                @php
                    $pretest = 65 + (($index * 7) % 25);
                    $kuis = 75 + (($index * 5) % 20);
                    $posttest = 80 + (($index * 4) % 18);
                    $nilaiAkhir = round(($pretest * 0.2) + ($kuis * 0.3) + ($posttest * 0.5), 1);
                @endphp
                <tr>
                    <td class="text-muted small">{{ $pesertas->firstItem() + $index }}</td>
                    <td>
                        <div class="fw-semibold text-dark">{{ $item->nama }}</div>
                        <div class="text-muted small">NIP/NIK: {{ $item->nip ?? '-' }}</div>
                    </td>
                    <td>
                        <div class="text-dark small">{{ $item->angkatan->pelatihan->nama_pelatihan ?? 'Pelatihan Teknis Kemenaker' }}</div>
                    </td>
                    <td class="text-center fw-medium">{{ $pretest }}</td>
                    <td class="text-center fw-medium">{{ $kuis }}</td>
                    <td class="text-center fw-bold text-primary">{{ $posttest }}</td>
                    <td class="text-center">
                        <span class="badge bg-primary-subtle text-primary fw-bold px-2 py-1" style="font-size: 0.85rem;">{{ $nilaiAkhir }}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-success-subtle text-success">Lulus</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-4 text-muted">Belum ada data nilai peserta.</td>
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
