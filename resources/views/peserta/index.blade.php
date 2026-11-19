@extends('layouts.app')

@section('title', 'Peserta Diklat - KCLC')
@section('page_title', 'DATA PESERTA PELATIHAN')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">LMS Diklat</a></li>
    <li class="breadcrumb-item active" aria-current="page">Peserta</li>
@endsection

@section('page_actions')
    <a href="{{ route('peserta.create') }}" class="btn btn-primary">
        <i class="bi bi-person-plus me-1"></i> Tambah Data
    </a>
@endsection

@section('content')
<div class="card card-custom">
    <div class="card-custom-header flex-column flex-md-row gap-3">
        <h5 class="card-custom-title">
            <i class="bi bi-people-fill text-primary"></i> Daftar Seluruh Peserta Diklat (KCLC)
        </h5>

        <form method="GET" action="{{ route('peserta.index') }}" class="d-flex flex-wrap align-items-center gap-2">
            <select name="angkatan_id" class="form-select form-select-sm" style="width: 220px;">
                <option value="">-- Semua Angkatan --</option>
                @foreach($angkatans as $a)
                    <option value="{{ $a->id }}" {{ $angkatanId == $a->id ? 'selected' : '' }}>
                        {{ $a->nomor_angkatan }}
                    </option>
                @endforeach
            </select>
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari nama, NIP, instansi..." value="{{ $search }}" style="width: 200px;">
            <button type="submit" class="btn btn-sm btn-primary">
                <i class="bi bi-search"></i>
            </button>
            @if($search || $angkatanId)
                <a href="{{ route('peserta.index') }}" class="btn btn-sm btn-outline-secondary" title="Reset Filter">
                    <i class="bi bi-x-circle"></i>
                </a>
            @endif
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-custom">
            <thead>
                <tr>
                    <th style="width: 40px;">No</th>
                    <th>NIP</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Jenis Kelamin</th>
                    <th>Instansi</th>
                    <th>Jabatan</th>
                    <th>Golongan</th>
                    <th style="width: 100px;" class="text-center">Tools</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pesertas as $index => $p)
                    <tr>
                        <td class="text-center">{{ $pesertas->firstItem() + $index }}</td>
                        <td class="fw-semibold text-dark">{{ $p->nip }}</td>
                        <td class="fw-bold text-primary">{{ $p->nama }}</td>
                        <td>{{ $p->email ?? '-' }}</td>
                        <td>{{ $p->jenis_kelamin }}</td>
                        <td>{{ $p->instansi ?? '-' }}</td>
                        <td>{{ $p->jabatan ?? '-' }}</td>
                        <td class="text-center">
                            <span class="badge bg-light text-dark border">{{ $p->golongan ?? '-' }}</span>
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('peserta.edit', $p->id) }}" class="btn btn-outline-primary" title="Edit Data">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <button type="button" class="btn btn-outline-danger" title="Hapus Data" onclick="confirmDelete('del-peserta-{{ $p->id }}', 'Hapus peserta {{ $p->nama }}?')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>

                            <form id="del-peserta-{{ $p->id }}" action="{{ route('peserta.destroy', $p->id) }}" method="POST" class="d-none">
                                @csrf
                                @method('DELETE')
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-3 d-block mb-2"></i> Belum ada data peserta diklat.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($pesertas->hasPages())
        <div class="table-pagination-footer">
            <span class="text-muted small">
                Menampilkan {{ $pesertas->firstItem() }} - {{ $pesertas->lastItem() }} dari {{ $pesertas->total() }} data
            </span>
            <div>
                {{ $pesertas->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
