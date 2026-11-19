@extends('layouts.app')

@section('title', 'Daftar Kelas Mapel - KCLC')
@section('page_title', 'DAFTAR PELATIHAN & KELAS MAPEL')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">LMS Diklat</a></li>
    <li class="breadcrumb-item active" aria-current="page">Kelas Mapel</li>
@endsection

@section('page_actions')
    <a href="{{ route('kelas-mapel.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Tambah Data
    </a>
@endsection

@section('content')
<div class="card card-custom">
    <div class="card-custom-header flex-column flex-md-row gap-3">
        <h5 class="card-custom-title">
            <i class="bi bi-grid-fill text-primary"></i> Daftar Kelas Pembelajaran Mata Pelatihan
        </h5>

        <form method="GET" action="{{ route('kelas-mapel.index') }}" class="d-flex flex-wrap align-items-center gap-2">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari angkatan, pelatihan..." value="{{ $search }}" style="width: 240px;">
            <button type="submit" class="btn btn-sm btn-primary">
                <i class="bi bi-search"></i>
            </button>
            @if($search)
                <a href="{{ route('kelas-mapel.index') }}" class="btn btn-sm btn-outline-secondary" title="Reset Filter">
                    <i class="bi bi-x-circle"></i>
                </a>
            @endif
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-custom">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Angkatan</th>
                    <th>Pelatihan</th>
                    <th>Mata Pelatihan & Kelas</th>
                    <th>Ruangan & Sesi</th>
                    <th style="width: 140px;" class="text-center">Tools</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kelasMapels as $index => $km)
                    <tr>
                        <td class="text-center">{{ $kelasMapels->firstItem() + $index }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="p-2 bg-light rounded text-primary">
                                    <i class="bi bi-grid-3x3-gap-fill fs-5"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark">{{ $km->angkatan->nomor_angkatan ?? '-' }}</div>
                                    <div class="text-muted small">
                                        @if($km->tanggal_mulai && $km->tanggal_selesai)
                                            {{ $km->tanggal_mulai->format('d/m/Y') }} s.d {{ $km->tanggal_selesai->format('d/m/Y') }}
                                        @else
                                            {{ $km->angkatan->tanggal_mulai?->format('d/m/Y') }} s.d {{ $km->angkatan->tanggal_selesai?->format('d/m/Y') }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $km->pelatihan->nama_pelatihan ?? '-' }}</div>
                            <span class="badge bg-light text-secondary border mt-1">
                                Kurikulum {{ $km->kurikulum }} &bull; Kapasitas {{ $km->kapasitas }}
                            </span>
                        </td>
                        <td>
                            <div class="fw-semibold text-primary">{{ $km->mataPelatihan->nama_mata_pelatihan ?? '-' }}</div>
                            <span class="text-muted small">Kelas: {{ $km->nama_kelas }} &bull; <strong>{{ $km->jp }} JP</strong></span>
                        </td>
                        <td>
                            <div class="small fw-semibold text-dark">{{ $km->ruangan->nama_ruangan ?? 'Online' }}</div>
                            <div class="text-muted small">
                                <i class="bi bi-clock me-1"></i> {{ $km->hari ?? '-' }} {{ $km->jam_mulai ? substr($km->jam_mulai, 0, 5) : '' }}-{{ $km->jam_selesai ? substr($km->jam_selesai, 0, 5) : '' }}
                                ({{ $km->metode_pembelajaran }})
                            </div>
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('kelas-mapel.show', $km->id) }}" class="btn btn-outline-info" title="Kelola Kelas (4 Tab KCLC)">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('kelas-mapel.show', ['kelas_mapel' => $km->id, 'tab' => 'detail']) }}" class="btn btn-outline-primary" title="Edit Data">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <button type="button" class="btn btn-outline-danger" title="Hapus Data" onclick="confirmDelete('del-km-{{ $km->id }}', 'Hapus kelas mapel {{ $km->nama_kelas }}?')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>

                            <form id="del-km-{{ $km->id }}" action="{{ route('kelas-mapel.destroy', $km->id) }}" method="POST" class="d-none">
                                @csrf
                                @method('DELETE')
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-3 d-block mb-2"></i> Belum ada data Kelas Mapel.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($kelasMapels->hasPages())
        <div class="table-pagination-footer">
            <span class="text-muted small">
                Menampilkan {{ $kelasMapels->firstItem() }} - {{ $kelasMapels->lastItem() }} dari {{ $kelasMapels->total() }} data
            </span>
            <div>
                {{ $kelasMapels->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
