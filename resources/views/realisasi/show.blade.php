@extends('layouts.app')

@section('title', 'Detail Realisasi Kegiatan - SIR-JP')

@section('page-title', 'Detail Realisasi Kegiatan')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('realisasi.index') }}">Realisasi Kegiatan</a></li>
    <li class="breadcrumb-item active" aria-current="page">Detail #{{ $realisasi->id }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card-custom mb-4">
            <div class="card-custom-header d-flex justify-content-between align-items-center">
                <h5 class="card-custom-title">
                    <i class="bi bi-calendar-check text-primary me-2"></i>Informasi Pelaksanaan Pembelajaran
                </h5>
                <span class="badge bg-{{ $realisasi->status == 'Terlaksana' ? 'success' : 'danger' }} fs-6 px-3 py-2">
                    {{ $realisasi->status }}
                </span>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-bordered mb-0">
                    <tbody>
                        <tr>
                            <th style="width: 250px;" class="text-secondary bg-light">Program Pelatihan</th>
                            <td class="fw-bold text-dark">{{ $realisasi->jadwal->mataPelatihan->angkatan->pelatihan->nama_pelatihan }}</td>
                        </tr>
                        <tr>
                            <th class="text-secondary bg-light">Angkatan</th>
                            <td>
                                <span class="badge bg-light text-primary border">{{ $realisasi->jadwal->mataPelatihan->angkatan->nomor_angkatan }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th class="text-secondary bg-light">Mata Pelatihan</th>
                            <td class="fw-bold text-primary">{{ $realisasi->jadwal->mataPelatihan->nama_mata_pelatihan }}</td>
                        </tr>
                        <tr>
                            <th class="text-secondary bg-light">Tanggal Realisasi</th>
                            <td class="fw-semibold">{{ $realisasi->tanggal_realisasi->format('d F Y') }}</td>
                        </tr>
                        <tr>
                            <th class="text-secondary bg-light">Waktu Pembelajaran</th>
                            <td>{{ substr($realisasi->jam_mulai_aktual, 0, 5) }} - {{ substr($realisasi->jam_selesai_aktual, 0, 5) }} WIB</td>
                        </tr>
                        <tr>
                            <th class="text-secondary bg-light">Total JP Aktual</th>
                            <td class="fw-bold text-primary fs-5">{{ $realisasi->jp_aktual }} JP</td>
                        </tr>
                        <tr>
                            <th class="text-secondary bg-light">Total JP Dibayarkan</th>
                            <td class="fw-semibold text-secondary">{{ $realisasi->jp_dibayarkan }} JP</td>
                        </tr>
                        @if($realisasi->status === 'Dibatalkan')
                            <tr>
                                <th class="text-secondary bg-light text-danger">Alasan Pembatalan</th>
                                <td class="text-danger fw-semibold">{{ $realisasi->alasan_batal ?? '-' }}</td>
                            </tr>
                        @endif
                        <tr>
                            <th class="text-secondary bg-light">Catatan / Keterangan</th>
                            <td>{{ $realisasi->catatan ?? '-' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Alokasi Pengajar -->
        <div class="card-custom mb-4">
            <div class="card-custom-header">
                <h5 class="card-custom-title">
                    <i class="bi bi-people-fill text-primary me-2"></i>Alokasi Pengajar Terlibat
                </h5>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0">
                    <thead class="table-light text-center small">
                        <tr>
                            <th>No.</th>
                            <th>Nama Pengajar</th>
                            <th>Jenis Pengajar</th>
                            <th>JP Aktual</th>
                            <th>JP Dibayarkan</th>
                            <th>Status Kehadiran</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($realisasi->pengajarRealisasis as $idx => $pr)
                            <tr>
                                <td class="text-center">{{ $idx + 1 }}</td>
                                <td class="fw-bold text-dark">{{ $pr->pengajar_nama }}</td>
                                <td class="text-center">
                                    <span class="badge bg-light text-{{ $pr->jenis_pengajar === 'Widyaiswara' ? 'primary' : 'success' }} border">
                                        {{ $pr->jenis_pengajar }}
                                    </span>
                                </td>
                                <td class="text-center fw-bold text-primary">{{ $pr->jp_aktual }} JP</td>
                                <td class="text-center fw-semibold text-secondary">{{ $pr->jp_dibayarkan }} JP</td>
                                <td class="text-center">
                                    <span class="badge bg-success">{{ $pr->status_kehadiran ?? 'Hadir' }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-3 text-muted">Tidak ada data pembagian pengajar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card-custom mb-4">
            <div class="card-custom-header">
                <h5 class="card-custom-title"><i class="bi bi-gear-fill text-primary me-2"></i>Aksi Tindakan</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('realisasi.edit', $realisasi) }}" class="btn btn-warning text-white">
                        <i class="bi bi-pencil me-1"></i> Edit Realisasi
                    </a>
                    <button type="button" class="btn btn-outline-danger" onclick="confirmDelete('del-realisasi-show', 'Hapus catatan realisasi ini?')">
                        <i class="bi bi-trash me-1"></i> Hapus Realisasi
                    </button>
                    <a href="{{ route('realisasi.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
                    </a>
                </div>
                <form id="del-realisasi-show" action="{{ route('realisasi.destroy', $realisasi) }}" method="POST" class="d-none">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
