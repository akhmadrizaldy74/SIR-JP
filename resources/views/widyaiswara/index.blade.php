@extends('layouts.app')

@section('title', 'Data Widyaiswara')
@section('page_title', 'Data Widyaiswara')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Widyaiswara</li>
@endsection

@section('page_actions')
    <a href="{{ route('widyaiswara.create') }}" class="btn btn-navy d-flex align-items-center gap-2">
        <i class="bi bi-plus-lg"></i>
        <span>Tambah Widyaiswara</span>
    </a>
@endsection

@section('content')
<div class="card-custom">
    <div class="card-custom-header flex-column flex-md-row gap-3">
        <h5 class="card-custom-title">
            <i class="bi bi-person-badge-fill text-primary"></i> Daftar Pengajar Internal (Widyaiswara)
        </h5>

        <!-- Filter & Search Form -->
        <form method="GET" action="{{ route('widyaiswara.index') }}" class="d-flex flex-wrap align-items-center gap-2">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari nama, NIK, NIP..." value="{{ $search }}" style="width: 200px;">
            <select name="jabatan" class="form-select form-select-sm" style="width: 190px;">
                <option value="">-- Semua Jabatan --</option>
                @foreach($jabatanOptions as $jab)
                    <option value="{{ $jab }}" {{ $jabatan == $jab ? 'selected' : '' }}>{{ $jab }}</option>
                @endforeach
            </select>
            <select name="status" class="form-select form-select-sm" style="width: 130px;">
                <option value="">-- Status --</option>
                <option value="Aktif" {{ $status == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="Nonaktif" {{ $status == 'Nonaktif' ? 'selected' : '' }}>Nonaktif</option>
            </select>
            <button type="submit" class="btn btn-sm btn-primary">
                <i class="bi bi-search"></i>
            </button>
            @if($search || $jabatan || $status)
                <a href="{{ route('widyaiswara.index') }}" class="btn btn-sm btn-outline-secondary" title="Reset Filter">
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
                    <th>NIK / NIP</th>
                    <th>Nama Lengkap & Gelar</th>
                    <th>Jabatan</th>
                    <th>Instansi</th>
                    <th class="text-center">Status</th>
                    <th class="text-center" style="width: 140px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($widyaiswaras as $index => $wi)
                    <tr>
                        <td class="text-center">{{ $widyaiswaras->firstItem() + $index }}</td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $wi->nik }}</div>
                            <div class="text-muted small">NIP. {{ $wi->nip ?? '-' }}</div>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $wi->nama_gelar }}</div>
                            <div class="text-muted small">
                                <i class="bi bi-telephone me-1"></i>{{ $wi->nomor_telepon ?? '-' }}
                                @if($wi->email) &bull; <i class="bi bi-envelope me-1"></i>{{ $wi->email }} @endif
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-light text-primary border fw-medium">{{ $wi->jabatan }}</span>
                        </td>
                        <td>{{ $wi->instansi }}</td>
                        <td class="text-center">
                            <span class="badge bg-{{ $wi->status == 'Aktif' ? 'success' : 'secondary' }} badge-custom">
                                {{ $wi->status }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('widyaiswara.show', $wi) }}" class="btn btn-outline-info" title="Detail Data">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('widyaiswara.edit', $wi) }}" class="btn btn-outline-warning" title="Edit Data">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button" class="btn btn-outline-danger" title="Hapus Data" onclick="confirmDelete('delete-form-{{ $wi->id }}', 'Hapus data Widyaiswara {{ $wi->nama_gelar }}?')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                            <form id="delete-form-{{ $wi->id }}" action="{{ route('widyaiswara.destroy', $wi) }}" method="POST" class="d-none">
                                @csrf
                                @method('DELETE')
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                            Tidak ada data Widyaiswara yang ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($widyaiswaras->hasPages())
        <div class="table-pagination-footer">
            <span class="text-muted small">
                Menampilkan {{ $widyaiswaras->firstItem() }} - {{ $widyaiswaras->lastItem() }} dari {{ $widyaiswaras->total() }} data
            </span>
            <div>
                {{ $widyaiswaras->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
