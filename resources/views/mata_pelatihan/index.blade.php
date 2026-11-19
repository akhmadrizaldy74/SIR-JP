@extends('layouts.app')

@section('title', 'Mata Pelatihan')
@section('page_title', 'Mata Pelatihan')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Mata Pelatihan</li>
@endsection

@section('page_actions')
    <a href="{{ route('mata-pelatihan.create') }}" class="btn btn-navy d-flex align-items-center gap-2">
        <i class="bi bi-plus-lg"></i>
        <span>Tambah Mata Pelatihan</span>
    </a>
@endsection

@section('content')
<div class="card-custom">
    <div class="card-custom-header flex-column flex-md-row gap-3">
        <h5 class="card-custom-title">
            <i class="bi bi-journal-bookmark-fill text-primary"></i> Daftar Mata Pelatihan & Rencana JP
        </h5>

        <!-- Filter & Search -->
        <form method="GET" action="{{ route('mata-pelatihan.index') }}" class="d-flex flex-wrap align-items-center gap-2">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari nama mata pelatihan..." value="{{ $search }}" style="width: 220px;">
            <select name="angkatan_id" class="form-select form-select-sm" style="width: 240px;">
                <option value="">-- Semua Angkatan --</option>
                @foreach($angkatans as $ang)
                    <option value="{{ $ang->id }}" {{ $angkatanId == $ang->id ? 'selected' : '' }}>
                        {{ $ang->pelatihan->nama_pelatihan }} - {{ $ang->nomor_angkatan }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-sm btn-primary">
                <i class="bi bi-search"></i>
            </button>
            @if($search || $angkatanId)
                <a href="{{ route('mata-pelatihan.index') }}" class="btn btn-sm btn-outline-secondary" title="Reset Filter">
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
                    <th>Program Pelatihan & Angkatan</th>
                    <th>Nama Mata Pelatihan</th>
                    <th class="text-center">Rencana JP</th>
                    <th>Pengajar Ditugaskan</th>
                    <th class="text-center" style="width: 150px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($mataPelatihans as $index => $mp)
                    <tr>
                        <td class="text-center">{{ $mataPelatihans->firstItem() + $index }}</td>
                        <td>
                            <div class="fw-bold text-dark">{{ $mp->angkatan->pelatihan->nama_pelatihan }}</div>
                            <span class="badge bg-light text-primary border">{{ $mp->angkatan->nomor_angkatan }}</span>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $mp->nama_mata_pelatihan }}</div>
                            <div class="text-muted small">{{ $mp->keterangan ?? '-' }}</div>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-light text-primary border fw-bold px-2 py-1 fs-6">
                                {{ $mp->rencana_jp }} JP
                            </span>
                        </td>
                        <td>
                            @forelse($mp->penugasans as $tugas)
                                <div class="small mb-1">
                                    <span class="badge bg-{{ $tugas->jenis_pengajar == 'Widyaiswara' ? 'primary' : 'secondary' }}-subtle text-dark border">
                                        {{ $tugas->jenis_pengajar }}
                                    </span>
                                    <strong class="text-dark ms-1">{{ $tugas->pengajar_nama }}</strong>
                                    <span class="text-muted small">({{ $tugas->peran }})</span>
                                </div>
                            @empty
                                <a href="{{ route('penugasan.create', ['mata_pelatihan_id' => $mp->id]) }}" class="badge bg-light text-warning border text-decoration-none">
                                    <i class="bi bi-plus-circle me-1"></i> + Tugaskan Pengajar
                                </a>
                            @endforelse
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('mata-pelatihan.show', $mp) }}" class="btn btn-outline-info" title="Detail Data">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('mata-pelatihan.edit', $mp) }}" class="btn btn-outline-warning" title="Edit Data">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button" class="btn btn-outline-danger" title="Hapus Data" onclick="confirmDelete('delete-mp-{{ $mp->id }}', 'Hapus mata pelatihan {{ $mp->nama_mata_pelatihan }}?')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                            <form id="delete-mp-{{ $mp->id }}" action="{{ route('mata-pelatihan.destroy', $mp) }}" method="POST" class="d-none">
                                @csrf
                                @method('DELETE')
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                            Tidak ada data mata pelatihan yang ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($mataPelatihans->hasPages())
        <div class="p-3 border-top d-flex justify-content-between align-items-center">
            <span class="text-muted small">
                Menampilkan {{ $mataPelatihans->firstItem() }} - {{ $mataPelatihans->lastItem() }} dari {{ $mataPelatihans->total() }} data
            </span>
            {{ $mataPelatihans->links() }}
        </div>
    @endif
</div>
@endsection
