@extends('layouts.app')

@section('title', 'Data Angkatan')
@section('page_title', 'Data Angkatan')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Angkatan Pelatihan</li>
@endsection

@section('page_actions')
    <a href="{{ route('angkatan.create') }}" class="btn btn-navy d-flex align-items-center gap-2">
        <i class="bi bi-plus-lg"></i>
        <span>Tambah Angkatan</span>
    </a>
@endsection

@section('content')
<div class="card-custom">
    <div class="card-custom-header flex-column flex-md-row gap-3">
        <h5 class="card-custom-title">
            <i class="bi bi-people-fill text-primary"></i> Daftar Angkatan Pelatihan
        </h5>

        <!-- Filter & Search -->
        <form method="GET" action="{{ route('angkatan.index') }}" class="d-flex flex-wrap align-items-center gap-2">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari angkatan, lokasi..." value="{{ $search }}" style="width: 200px;">
            <select name="pelatihan_id" class="form-select form-select-sm" style="width: 200px;">
                <option value="">-- Semua Pelatihan --</option>
                @foreach($pelatihans as $p)
                    <option value="{{ $p->id }}" {{ $pelatihanId == $p->id ? 'selected' : '' }}>{{ $p->nama_pelatihan }}</option>
                @endforeach
            </select>
            <select name="status" class="form-select form-select-sm" style="width: 140px;">
                <option value="">-- Status --</option>
                @foreach($statusOptions as $st)
                    <option value="{{ $st }}" {{ $status == $st ? 'selected' : '' }}>{{ $st }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-sm btn-primary">
                <i class="bi bi-search"></i>
            </button>
            @if($search || $pelatihanId || $status)
                <a href="{{ route('angkatan.index') }}" class="btn btn-sm btn-outline-secondary" title="Reset Filter">
                    <i class="bi bi-x-circle"></i>
                </a>
            @endif
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-custom">
            <thead>
                <tr>
                    <th style="width: 50px;">No.</th>
                    <th>Program Pelatihan</th>
                    <th>Nomor Angkatan</th>
                    <th>Waktu & Lokasi</th>
                    <th class="text-center">Peserta</th>
                    <th class="text-center">Mata Pelatihan</th>
                    <th class="text-center">Status</th>
                    <th class="text-center" style="width: 140px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($angkatans as $index => $ang)
                    <tr>
                        <td class="text-center">{{ $angkatans->firstItem() + $index }}</td>
                        <td>
                            <div class="fw-bold text-dark">{{ $ang->pelatihan->nama_pelatihan }}</div>
                            <span class="badge bg-light text-primary border" style="font-size: 0.7rem;">{{ $ang->pelatihan->kode_pelatihan }}</span>
                        </td>
                        <td>
                            <div class="fw-semibold text-primary">{{ $ang->nomor_angkatan }}</div>
                        </td>
                        <td>
                            <div class="small fw-semibold text-dark">{{ $ang->tanggal_mulai->format('d/m/Y') }} - {{ $ang->tanggal_selesai->format('d/m/Y') }}</div>
                            <div class="text-muted small"><i class="bi bi-geo-alt me-1"></i>{{ $ang->lokasi ?? 'Kampus PPSDM' }}</div>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('peserta.index', ['angkatan_id' => $ang->id]) }}" class="badge bg-light text-primary border text-decoration-none py-1 px-2" title="Lihat Peserta Diklat">
                                <i class="bi bi-people me-1"></i>{{ $ang->jumlah_peserta }} Orang
                            </a>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-light text-primary border">{{ $ang->mata_pelatihans_count }} MP</span>
                        </td>
                        <td class="text-center">
                            @php
                                $badgeColor = match($ang->status) {
                                    'Direncanakan' => 'warning text-dark',
                                    'Berjalan' => 'primary',
                                    'Selesai' => 'success',
                                    'Dibatalkan' => 'danger',
                                    default => 'secondary'
                                };
                            @endphp
                            <span class="badge bg-{{ $badgeColor }} badge-custom">{{ $ang->status }}</span>
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('angkatan.show', $ang) }}" class="btn btn-outline-info" title="Detail Data">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('angkatan.edit', $ang) }}" class="btn btn-outline-warning" title="Edit Data">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button" class="btn btn-outline-danger" title="Hapus Data" onclick="confirmDelete('delete-ang-{{ $ang->id }}', 'Hapus angkatan {{ $ang->nomor_angkatan }}?')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                            <form id="delete-ang-{{ $ang->id }}" action="{{ route('angkatan.destroy', $ang) }}" method="POST" class="d-none">
                                @csrf
                                @method('DELETE')
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                            Tidak ada data angkatan pelatihan yang ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($angkatans->hasPages())
        <div class="table-pagination-footer">
            <span class="text-muted small">
                Menampilkan {{ $angkatans->firstItem() }} - {{ $angkatans->lastItem() }} dari {{ $angkatans->total() }} data
            </span>
            <div>
                {{ $angkatans->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
