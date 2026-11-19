@extends('layouts.app')

@section('title', 'Penugasan Pengajar')
@section('page_title', 'Penugasan Pengajar')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Penugasan Pengajar</li>
@endsection

@section('page_actions')
    <a href="{{ route('penugasan.create') }}" class="btn btn-navy d-flex align-items-center gap-2">
        <i class="bi bi-person-plus-fill"></i>
        <span>Tugaskan Pengajar</span>
    </a>
@endsection

@section('content')
<div class="card-custom">
    <div class="card-custom-header flex-column flex-md-row gap-3">
        <h5 class="card-custom-title">
            <i class="bi bi-person-check-fill text-primary"></i> Daftar Penugasan Pengajar pada Mata Pelatihan
        </h5>

        <!-- Filter & Search -->
        <form method="GET" action="{{ route('penugasan.index') }}" class="d-flex flex-wrap align-items-center gap-2">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari nama pengajar, materi..." value="{{ $search }}" style="width: 220px;">
            <select name="jenis_pengajar" class="form-select form-select-sm" style="width: 170px;">
                <option value="">-- Semua Jenis --</option>
                <option value="Widyaiswara" {{ $jenisPengajar == 'Widyaiswara' ? 'selected' : '' }}>Widyaiswara</option>
                <option value="Narasumber" {{ $jenisPengajar == 'Narasumber' ? 'selected' : '' }}>Narasumber</option>
            </select>
            <select name="pelatihan_id" class="form-select form-select-sm" style="width: 200px;">
                <option value="">-- Semua Pelatihan --</option>
                @foreach($pelatihans as $p)
                    <option value="{{ $p->id }}" {{ $pelatihanId == $p->id ? 'selected' : '' }}>{{ $p->nama_pelatihan }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-sm btn-primary">
                <i class="bi bi-search"></i>
            </button>
            @if($search || $jenisPengajar || $pelatihanId)
                <a href="{{ route('penugasan.index') }}" class="btn btn-sm btn-outline-secondary" title="Reset Filter">
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
                    <th>Mata Pelatihan</th>
                    <th>Jenis</th>
                    <th>Nama Pengajar</th>
                    <th>Peran</th>
                    <th>Keterangan</th>
                    <th class="text-center" style="width: 100px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($penugasans as $index => $tugas)
                    <tr>
                        <td class="text-center">{{ $penugasans->firstItem() + $index }}</td>
                        <td>
                            <div class="fw-bold text-dark">{{ $tugas->mataPelatihan->angkatan->pelatihan->nama_pelatihan }}</div>
                            <span class="badge bg-light text-primary border" style="font-size: 0.7rem;">{{ $tugas->mataPelatihan->angkatan->nomor_angkatan }}</span>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $tugas->mataPelatihan->nama_mata_pelatihan }}</div>
                            <span class="badge bg-light text-secondary border">Rencana: {{ $tugas->mataPelatihan->rencana_jp }} JP</span>
                        </td>
                        <td>
                            <span class="badge bg-{{ $tugas->jenis_pengajar == 'Widyaiswara' ? 'primary' : 'secondary' }}-subtle text-dark border">
                                {{ $tugas->jenis_pengajar }}
                            </span>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $tugas->pengajar_nama }}</div>
                            <div class="text-muted small">{{ $tugas->pengajar_instansi }}</div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ $tugas->peran }}</span>
                        </td>
                        <td><span class="text-muted small">{{ $tugas->keterangan ?? '-' }}</span></td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('penugasan.edit', $tugas) }}" class="btn btn-outline-warning" title="Edit Penugasan">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button" class="btn btn-outline-danger" title="Batalkan Penugasan" onclick="confirmDelete('del-tugas-main-{{ $tugas->id }}', 'Batalkan penugasan {{ $tugas->pengajar_nama }}?')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                            <form id="del-tugas-main-{{ $tugas->id }}" action="{{ route('penugasan.destroy', $tugas) }}" method="POST" class="d-none">
                                @csrf
                                @method('DELETE')
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                            Tidak ada data penugasan pengajar yang ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($penugasans->hasPages())
        <div class="table-pagination-footer">
            <span class="text-muted small">
                Menampilkan {{ $penugasans->firstItem() }} - {{ $penugasans->lastItem() }} dari {{ $penugasans->total() }} data
            </span>
            <div>
                {{ $penugasans->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
