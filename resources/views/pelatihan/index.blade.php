@extends('layouts.app')

@section('title', 'Data Pelatihan')
@section('page_title', 'Data Pelatihan')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Pelatihan</li>
@endsection

@section('page_actions')
    <a href="{{ route('pelatihan.create') }}" class="btn btn-navy d-flex align-items-center gap-2">
        <i class="bi bi-plus-lg"></i>
        <span>Tambah Pelatihan</span>
    </a>
@endsection

@section('content')
<div class="card-custom">
    <div class="card-custom-header flex-column flex-md-row gap-3">
        <h5 class="card-custom-title">
            <i class="bi bi-mortarboard-fill text-primary"></i> Daftar Program Pelatihan
        </h5>

        <!-- Filter & Search -->
        <form method="GET" action="{{ route('pelatihan.index') }}" class="d-flex flex-wrap align-items-center gap-2">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari nama / kode pelatihan..." value="{{ $search }}" style="width: 220px;">
            <select name="kategori" class="form-select form-select-sm" style="width: 150px;">
                <option value="">-- Kategori --</option>
                @foreach($kategoriOptions as $kat)
                    <option value="{{ $kat }}" {{ $kategori == $kat ? 'selected' : '' }}>{{ $kat }}</option>
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
            @if($search || $kategori || $status)
                <a href="{{ route('pelatihan.index') }}" class="btn btn-sm btn-outline-secondary" title="Reset Filter">
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
                    <th>Kode</th>
                    <th>Nama Program Pelatihan</th>
                    <th>Kategori</th>
                    <th>Periode Waktu</th>
                    <th class="text-center">Angkatan</th>
                    <th class="text-center">Status</th>
                    <th class="text-center" style="width: 140px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pelatihans as $index => $pel)
                    <tr>
                        <td class="text-center">{{ $pelatihans->firstItem() + $index }}</td>
                        <td><span class="badge bg-light text-primary border fw-semibold">{{ $pel->kode_pelatihan }}</span></td>
                        <td>
                            <div class="fw-bold text-dark">{{ $pel->nama_pelatihan }}</div>
                            <div class="text-muted small">Penyelenggara: {{ $pel->penyelenggara }}</div>
                        </td>
                        <td><span class="badge bg-secondary-subtle text-dark border">{{ $pel->kategori }}</span></td>
                        <td>
                            <div class="small fw-semibold text-dark">{{ $pel->tanggal_mulai->format('d/m/Y') }}</div>
                            <div class="text-muted small">s.d. {{ $pel->tanggal_selesai->format('d/m/Y') }}</div>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-light text-dark border px-2 py-1">
                                <i class="bi bi-people me-1 text-primary"></i> {{ $pel->angkatans_count }} Angkatan
                            </span>
                        </td>
                        <td class="text-center">
                            @php
                                $badgeColor = match($pel->status) {
                                    'Direncanakan' => 'warning text-dark',
                                    'Berjalan' => 'primary',
                                    'Selesai' => 'success',
                                    'Dibatalkan' => 'danger',
                                    default => 'secondary'
                                };
                            @endphp
                            <span class="badge bg-{{ $badgeColor }} badge-custom">
                                {{ $pel->status }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('pelatihan.show', $pel) }}" class="btn btn-outline-info" title="Detail Data">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('pelatihan.edit', $pel) }}" class="btn btn-outline-warning" title="Edit Data">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button" class="btn btn-outline-danger" title="Hapus Data" onclick="confirmDelete('delete-pel-{{ $pel->id }}', 'Hapus program pelatihan {{ $pel->nama_pelatihan }}?')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                            <form id="delete-pel-{{ $pel->id }}" action="{{ route('pelatihan.destroy', $pel) }}" method="POST" class="d-none">
                                @csrf
                                @method('DELETE')
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                            Tidak ada data program pelatihan yang ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($pelatihans->hasPages())
        <div class="p-3 border-top d-flex justify-content-between align-items-center">
            <span class="text-muted small">
                Menampilkan {{ $pelatihans->firstItem() }} - {{ $pelatihans->lastItem() }} dari {{ $pelatihans->total() }} data
            </span>
            {{ $pelatihans->links() }}
        </div>
    @endif
</div>
@endsection
