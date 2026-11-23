@extends('layouts.app')

@section('title', 'LMS MOOC - Pelatihan')
@section('page_title', 'Pelatihan LMS MOOC')

@section('breadcrumb_custom')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">home</a></li>
    <li class="breadcrumb-item">LMS MOOC</li>
    <li class="breadcrumb-item active" aria-current="page">Pelatihan</li>
@endsection

@section('content')
<div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h5 class="fw-bold text-dark mb-1">Daftar Modul & Kursus LMS MOOC</h5>
            <p class="text-muted small mb-0">Kelola materi mandiri, kursus online, dan modul digital bagi peserta pelatihan.</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-primary btn-sm px-3 rounded-3" onclick="Swal.fire('Tambah Pelatihan MOOC', 'Fitur pembuatan modul kursus MOOC baru.', 'info')">
                <i class="bi bi-plus-lg me-1"></i> Tambah Kursus MOOC
            </button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr class="text-muted small text-uppercase">
                    <th style="width: 50px;">No</th>
                    <th>Nama Pelatihan / Modul</th>
                    <th>Kategori</th>
                    <th>Jumlah Angkatan</th>
                    <th>Status</th>
                    <th class="text-center" style="width: 120px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pelatihans as $index => $item)
                <tr>
                    <td class="text-muted small">{{ $pelatihans->firstItem() + $index }}</td>
                    <td>
                        <div class="fw-semibold text-dark">{{ $item->nama_pelatihan }}</div>
                        <div class="text-muted small">Kode: {{ $item->kode_pelatihan ?? 'KCLC-MOOC-'.$item->id }}</div>
                    </td>
                    <td><span class="badge bg-light text-primary border">{{ $item->kategori ?? 'Umum' }}</span></td>
                    <td>{{ $item->angkatans_count ?? 0 }} Angkatan</td>
                    <td><span class="badge bg-success-subtle text-success">Aktif</span></td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-secondary" title="Detail Modul" onclick="Swal.fire('{{ $item->nama_pelatihan }}', 'Modul MOOC dalam proses pembelajaran digital.', 'info')">
                            <i class="bi bi-eye"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">Belum ada modul pelatihan MOOC yang terdaftar.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($pelatihans->hasPages())
    <div class="d-flex justify-content-end mt-4">
        {{ $pelatihans->links() }}
    </div>
    @endif
</div>
@endsection
