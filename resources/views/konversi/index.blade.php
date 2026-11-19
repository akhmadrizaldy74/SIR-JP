@extends('layouts.app')

@section('title', 'Konversi JP Widyaiswara')
@section('page_title', 'Konversi Jam Pelajaran (JP)')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Konversi JP</li>
@endsection

@section('page_actions')
    <a href="{{ route('konversi.create') }}" class="btn btn-navy d-flex align-items-center gap-2">
        <i class="bi bi-plus-lg"></i>
        <span>Tambah Kegiatan Konversi</span>
    </a>
@endsection

@section('content')
<div class="card-custom">
    <div class="card-custom-header flex-column flex-md-row gap-3">
        <h5 class="card-custom-title">
            <i class="bi bi-arrow-left-right text-primary"></i> Daftar Jam Pelajaran (JP) Konversi Widyaiswara
        </h5>

        <!-- Filter & Search -->
        <form method="GET" action="{{ route('konversi.index') }}" class="d-flex flex-wrap align-items-center gap-2">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari kegiatan, SK..." value="{{ $search }}" style="width: 180px;">
            <select name="bulan" class="form-select form-select-sm" style="width: 140px;">
                @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                        {{ (new \App\Services\JPRecapitulationService)->getNamaBulan($m) }}
                    </option>
                @endfor
            </select>
            <select name="tahun" class="form-select form-select-sm" style="width: 95px;">
                @for($y = date('Y') - 2; $y <= date('Y') + 1; $y++)
                    <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
            <select name="widyaiswara_id" class="form-select form-select-sm" style="width: 180px;">
                <option value="">-- Semua Widyaiswara --</option>
                @foreach($widyaiswaras as $wi)
                    <option value="{{ $wi->id }}" {{ $widyaiswaraId == $wi->id ? 'selected' : '' }}>{{ $wi->nama_gelar }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-sm btn-primary">
                <i class="bi bi-filter"></i>
            </button>
            @if(request()->has('widyaiswara_id') || request()->has('search') || request()->has('kategori'))
                <a href="{{ route('konversi.index') }}" class="btn btn-sm btn-outline-secondary" title="Reset Filter">
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
                    <th>Tanggal</th>
                    <th>Widyaiswara</th>
                    <th>Nama Kegiatan Konversi</th>
                    <th>Kategori</th>
                    <th class="text-center">Jumlah JP</th>
                    <th class="text-center">JP Dibayar</th>
                    <th>Info Pembayaran / SK</th>
                    <th class="text-center">Status</th>
                    <th class="text-center" style="width: 140px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($konversis as $index => $kv)
                    <tr>
                        <td class="text-center">{{ $konversis->firstItem() + $index }}</td>
                        <td class="fw-semibold text-dark">{{ $kv->tanggal_kegiatan->format('d/m/Y') }}</td>
                        <td>
                            <div class="fw-bold text-dark">{{ $kv->widyaiswara->nama_gelar }}</div>
                            <div class="text-muted small">{{ $kv->widyaiswara->jabatan }}</div>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $kv->nama_kegiatan }}</div>
                            <div class="text-muted small">{{ $kv->keterangan ?? '-' }}</div>
                        </td>
                        <td><span class="badge bg-light text-secondary border">{{ $kv->kategori_konversi }}</span></td>
                        <td class="text-center fw-bold text-primary fs-6">{{ $kv->jumlah_jp }} JP</td>
                        <td class="text-center fw-semibold text-secondary">{{ $kv->jp_dibayarkan }} JP</td>
                        <td>
                            <div class="small fw-semibold text-dark">{{ $kv->informasi_pembayaran ?? '-' }}</div>
                            <div class="text-muted small">SK: {{ $kv->nomor_sk ?? '-' }}</div>
                        </td>
                        <td class="text-center">
                            @php
                                $badgeValid = match($kv->status_validasi) {
                                    'Disetujui' => 'success',
                                    'Menunggu' => 'warning text-dark',
                                    'Ditolak' => 'danger',
                                    default => 'secondary'
                                };
                            @endphp
                            <span class="badge bg-{{ $badgeValid }} badge-custom">{{ $kv->status_validasi }}</span>
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('konversi.show', $kv) }}" class="btn btn-outline-info" title="Detail Data">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('konversi.edit', $kv) }}" class="btn btn-outline-warning" title="Edit Data">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button" class="btn btn-outline-danger" title="Hapus Data" onclick="confirmDelete('del-kv-{{ $kv->id }}', 'Hapus data kegiatan konversi ini?')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                            <form id="del-kv-{{ $kv->id }}" action="{{ route('konversi.destroy', $kv) }}" method="POST" class="d-none">
                                @csrf
                                @method('DELETE')
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                            Tidak ada data kegiatan konversi JP pada periode ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($konversis->hasPages())
        <div class="table-pagination-footer">
            <span class="text-muted small">
                Menampilkan {{ $konversis->firstItem() }} - {{ $konversis->lastItem() }} dari {{ $konversis->total() }} data
            </span>
            <div>
                {{ $konversis->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
