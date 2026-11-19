@extends('layouts.app')

@section('title', 'Data Narasumber')
@section('page_title', 'Data Narasumber')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Narasumber</li>
@endsection

@section('page_actions')
    <a href="{{ route('narasumber.create') }}" class="btn btn-navy d-flex align-items-center gap-2">
        <i class="bi bi-plus-lg"></i>
        <span>Tambah Narasumber</span>
    </a>
@endsection

@section('content')
<div class="card-custom">
    <div class="card-custom-header flex-column flex-md-row gap-3">
        <h5 class="card-custom-title">
            <i class="bi bi-person-lines-fill text-primary"></i> Daftar Pengajar Eksternal (Narasumber)
        </h5>

        <!-- Filter & Search Form -->
        <form method="GET" action="{{ route('narasumber.index') }}" class="d-flex flex-wrap align-items-center gap-2">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari nama, NIK, instansi..." value="{{ $search }}" style="width: 240px;">
            <select name="status" class="form-select form-select-sm" style="width: 130px;">
                <option value="">-- Status --</option>
                <option value="Aktif" {{ $status == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="Nonaktif" {{ $status == 'Nonaktif' ? 'selected' : '' }}>Nonaktif</option>
            </select>
            <button type="submit" class="btn btn-sm btn-primary">
                <i class="bi bi-search"></i>
            </button>
            @if($search || $status)
                <a href="{{ route('narasumber.index') }}" class="btn btn-sm btn-outline-secondary" title="Reset Filter">
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
                    <th>NIK</th>
                    <th>Nama Lengkap & Gelar</th>
                    <th>Instansi Asal</th>
                    <th>Kontak</th>
                    <th class="text-center">Status</th>
                    <th class="text-center" style="width: 140px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($narasumbers as $index => $ns)
                    <tr>
                        <td class="text-center">{{ $narasumbers->firstItem() + $index }}</td>
                        <td class="fw-semibold text-dark">{{ $ns->nik }}</td>
                        <td>
                            <div class="fw-bold text-dark">{{ $ns->nama_gelar }}</div>
                            <div class="text-muted small">{{ $ns->keterangan ?? '-' }}</div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ $ns->instansi }}</span>
                        </td>
                        <td>
                            <div class="small">
                                <div><i class="bi bi-telephone me-1 text-primary"></i>{{ $ns->nomor_telepon ?? '-' }}</div>
                                @if($ns->email)
                                    <div><i class="bi bi-envelope me-1 text-secondary"></i>{{ $ns->email }}</div>
                                @endif
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-{{ $ns->status == 'Aktif' ? 'success' : 'secondary' }} badge-custom">
                                {{ $ns->status }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('narasumber.show', $ns) }}" class="btn btn-outline-info" title="Detail Data">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('narasumber.edit', $ns) }}" class="btn btn-outline-warning" title="Edit Data">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button" class="btn btn-outline-danger" title="Hapus Data" onclick="confirmDelete('delete-ns-{{ $ns->id }}', 'Hapus data Narasumber {{ $ns->nama_gelar }}?')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                            <form id="delete-ns-{{ $ns->id }}" action="{{ route('narasumber.destroy', $ns) }}" method="POST" class="d-none">
                                @csrf
                                @method('DELETE')
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                            Tidak ada data Narasumber yang ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($narasumbers->hasPages())
        <div class="p-3 border-top d-flex justify-content-between align-items-center">
            <span class="text-muted small">
                Menampilkan {{ $narasumbers->firstItem() }} - {{ $narasumbers->lastItem() }} dari {{ $narasumbers->total() }} data
            </span>
            {{ $narasumbers->links() }}
        </div>
    @endif
</div>
@endsection
