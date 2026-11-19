@extends('layouts.app')

@section('title', 'Detail Pelatihan')
@section('page_title', 'Detail Program Pelatihan')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('pelatihan.index') }}">Pelatihan</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $pelatihan->nama_pelatihan }}</li>
@endsection

@section('page_actions')
    <div class="d-flex gap-2">
        <a href="{{ route('angkatan.create', ['pelatihan_id' => $pelatihan->id]) }}" class="btn btn-navy btn-sm">
            <i class="bi bi-plus-lg me-1"></i> Tambah Angkatan
        </a>
        <a href="{{ route('pelatihan.edit', $pelatihan) }}" class="btn btn-warning btn-sm">
            <i class="bi bi-pencil me-1"></i> Edit Data
        </a>
        <a href="{{ route('pelatihan.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>
@endsection

@section('content')
<div class="row">
    <!-- Informasi Pelatihan -->
    <div class="col-lg-4 mb-4">
        <div class="card-custom p-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="badge bg-light text-primary border fw-semibold">{{ $pelatihan->kode_pelatihan }}</span>
                <span class="badge bg-primary">{{ $pelatihan->status }}</span>
            </div>
            <h5 class="fw-bold text-dark mb-1">{{ $pelatihan->nama_pelatihan }}</h5>
            <p class="text-muted small mb-3">Kategori: {{ $pelatihan->kategori }}</p>

            <div class="border-top pt-3 small">
                <div class="mb-2">
                    <span class="text-muted d-block">Penyelenggara:</span>
                    <strong class="text-dark">{{ $pelatihan->penyelenggara }}</strong>
                </div>
                <div class="mb-2">
                    <span class="text-muted d-block">Periode Pelaksanaan:</span>
                    <span class="text-dark">{{ $pelatihan->tanggal_mulai->format('d M Y') }} s.d. {{ $pelatihan->tanggal_selesai->format('d M Y') }}</span>
                </div>
                <div class="mb-2">
                    <span class="text-muted d-block">Total Angkatan Terdaftar:</span>
                    <span class="fw-bold text-primary">{{ $pelatihan->angkatans->count() }} Angkatan</span>
                </div>
                <div>
                    <span class="text-muted d-block">Keterangan:</span>
                    <span class="text-dark">{{ $pelatihan->keterangan ?? '-' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Daftar Angkatan pada Pelatihan ini -->
    <div class="col-lg-8">
        <div class="card-custom">
            <div class="card-custom-header">
                <h5 class="card-custom-title">
                    <i class="bi bi-people-fill text-primary"></i> Daftar Angkatan
                </h5>
                <span class="badge bg-light text-secondary border">{{ $pelatihan->angkatans->count() }} Angkatan</span>
            </div>
            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th>Nomor Angkatan</th>
                            <th>Periode Waktu</th>
                            <th class="text-center">Peserta</th>
                            <th class="text-center">Mata Pelatihan</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pelatihan->angkatans as $ang)
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark">{{ $ang->nomor_angkatan }}</div>
                                    <div class="text-muted small">{{ $ang->lokasi ?? 'Kampus PPSDM' }}</div>
                                </td>
                                <td>
                                    <div class="small fw-semibold text-dark">{{ $ang->tanggal_mulai->format('d/m/Y') }}</div>
                                    <div class="text-muted small">s.d. {{ $ang->tanggal_selesai->format('d/m/Y') }}</div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border">{{ $ang->jumlah_peserta }} Orang</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-primary border">{{ $ang->mataPelatihans->count() }} MP</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info text-dark badge-custom">{{ $ang->status }}</span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('angkatan.show', $ang) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-arrow-right"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Belum ada angkatan yang dibuat untuk pelatihan ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
