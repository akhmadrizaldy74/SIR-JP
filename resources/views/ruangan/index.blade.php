@extends('layouts.app')

@section('title', 'Ruangan Diklat - KCLC')
@section('page_title', 'RUANGAN')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">LMS Diklat</a></li>
    <li class="breadcrumb-item active" aria-current="page">Ruangan</li>
@endsection

@section('page_actions')
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahRuangan">
        <i class="bi bi-plus-lg me-1"></i> Tambah Data
    </button>
@endsection

@section('content')
<div class="card card-custom">
    <div class="card-custom-header flex-column flex-md-row gap-3">
        <h5 class="card-custom-title">
            <i class="bi bi-door-open-fill text-primary"></i> Data Ruangan Kelas Pembelajaran
        </h5>

        <form method="GET" action="{{ route('ruangan.index') }}" class="d-flex flex-wrap align-items-center gap-2">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari nama ruangan..." value="{{ $search }}" style="width: 220px;">
            <button type="submit" class="btn btn-sm btn-primary">
                <i class="bi bi-search"></i>
            </button>
            @if($search)
                <a href="{{ route('ruangan.index') }}" class="btn btn-sm btn-outline-secondary" title="Reset Filter">
                    <i class="bi bi-x-circle"></i>
                </a>
            @endif
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-custom">
            <thead>
                <tr>
                    <th style="width: 60px;">No</th>
                    <th>Nama</th>
                    <th>Kapasitas & Lokasi</th>
                    <th style="width: 120px;" class="text-center">Status</th>
                    <th style="width: 120px;" class="text-center">Tools</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ruangans as $index => $r)
                    <tr>
                        <td class="text-center">{{ $ruangans->firstItem() + $index }}</td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $r->nama_ruangan }}</div>
                        </td>
                        <td>
                            <span class="text-muted small">
                                <i class="bi bi-people me-1"></i> {{ $r->kapasitas ?? 'Default (30-40)' }}
                                @if($r->lokasi) &bull; <i class="bi bi-geo-alt me-1"></i> {{ $r->lokasi }} @endif
                            </span>
                        </td>
                        <td class="text-center">
                            @if($r->is_aktif)
                                <span class="badge bg-success-subtle text-success border border-success-subtle">Aktif</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">Nonaktif</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalEditRuangan{{ $r->id }}" title="Edit Data">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <button type="button" class="btn btn-outline-danger" title="Hapus Data" onclick="confirmDelete('del-ruang-{{ $r->id }}', 'Hapus ruangan {{ $r->nama_ruangan }}?')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>

                            <form id="del-ruang-{{ $r->id }}" action="{{ route('ruangan.destroy', $r->id) }}" method="POST" class="d-none">
                                @csrf
                                @method('DELETE')
                            </form>
                        </td>
                    </tr>

                    <!-- Modal Edit Ruangan -->
                    <div class="modal fade" id="modalEditRuangan{{ $r->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title fw-bold">Edit Data Ruangan</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form action="{{ route('ruangan.update', $r->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Nama Ruangan <span class="text-danger">*</span></label>
                                            <input type="text" name="nama_ruangan" class="form-control" value="{{ $r->nama_ruangan }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Kapasitas</label>
                                            <input type="text" name="kapasitas" class="form-control" value="{{ $r->kapasitas }}" placeholder="Contoh: 40 Peserta">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Lokasi / Gedung</label>
                                            <input type="text" name="lokasi" class="form-control" value="{{ $r->lokasi }}" placeholder="Contoh: Gedung Diklat Lt. 2">
                                        </div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="is_aktif" value="1" id="aktifSwitch{{ $r->id }}" {{ $r->is_aktif ? 'checked' : '' }}>
                                            <label class="form-check-label" for="aktifSwitch{{ $r->id }}">Ruangan Aktif</label>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary">Update</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-3 d-block mb-2"></i> Belum ada data ruangan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($ruangans->hasPages())
        <div class="table-pagination-footer">
            <span class="text-muted small">
                Menampilkan {{ $ruangans->firstItem() }} - {{ $ruangans->lastItem() }} dari {{ $ruangans->total() }} data
            </span>
            <div>
                {{ $ruangans->links() }}
            </div>
        </div>
    @endif
</div>

<!-- Modal Tambah Ruangan -->
<div class="modal fade" id="modalTambahRuangan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Tambah Data Ruangan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('ruangan.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Ruangan <span class="text-danger">*</span></label>
                        <input type="text" name="nama_ruangan" class="form-control" placeholder="Contoh: Ruang Kelas Online" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kapasitas</label>
                        <input type="text" name="kapasitas" class="form-control" placeholder="Contoh: 350 Peserta">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Lokasi / Gedung</label>
                        <input type="text" name="lokasi" class="form-control" placeholder="Contoh: Gedung Diklat Lt. 2 / Zoom LMS">
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_aktif" value="1" id="tambahAktifSwitch" checked>
                        <label class="form-check-label" for="tambahAktifSwitch">Ruangan Aktif</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
