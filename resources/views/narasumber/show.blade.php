@extends('layouts.app')

@section('title', 'Detail Narasumber')
@section('page_title', 'Detail Narasumber')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('narasumber.index') }}">Narasumber</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $narasumber->nama_gelar }}</li>
@endsection

@section('page_actions')
    <div class="d-flex gap-2">
        <a href="{{ route('narasumber.edit', $narasumber) }}" class="btn btn-warning btn-sm">
            <i class="bi bi-pencil me-1"></i> Edit Data
        </a>
        <a href="{{ route('narasumber.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>
@endsection

@section('content')
<div class="row">
    <!-- Profil Singkat Narasumber -->
    <div class="col-lg-4 mb-4">
        <div class="card-custom p-4 text-center">
            <div class="mx-auto mb-3 rounded-circle bg-light d-flex align-items-center justify-content-center text-primary" style="width: 80px; height: 80px; font-size: 2.2rem; border: 3px solid #DBEAFE;">
                <i class="bi bi-person-lines-fill"></i>
            </div>
            <h5 class="fw-bold text-dark mb-1">{{ $narasumber->nama_gelar }}</h5>
            <p class="text-muted small mb-2">Pengajar Eksternal / Narasumber</p>
            <span class="badge bg-secondary px-3 py-2 rounded-pill mb-3">{{ $narasumber->instansi }}</span>
            <span class="badge bg-{{ $narasumber->status == 'Aktif' ? 'success' : 'secondary' }} d-block mx-auto mb-3" style="width: fit-content;">Status: {{ $narasumber->status }}</span>

            <div class="text-start border-top pt-3 small">
                <div class="mb-2">
                    <span class="text-muted d-block">NIK:</span>
                    <strong class="text-dark">{{ $narasumber->nik }}</strong>
                </div>
                <div class="mb-2">
                    <span class="text-muted d-block">Telepon:</span>
                    <span class="text-dark">{{ $narasumber->nomor_telepon ?? '-' }}</span>
                </div>
                <div class="mb-2">
                    <span class="text-muted d-block">Email:</span>
                    <span class="text-dark">{{ $narasumber->email ?? '-' }}</span>
                </div>
                <div>
                    <span class="text-muted d-block">Keahlian / Keterangan:</span>
                    <span class="text-dark">{{ $narasumber->keterangan ?? '-' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Riwayat Penugasan Mengajar -->
    <div class="col-lg-8">
        <div class="card-custom">
            <div class="card-custom-header">
                <h5 class="card-custom-title">
                    <i class="bi bi-journal-check text-primary"></i> Riwayat Penugasan Pelatihan
                </h5>
                <span class="badge bg-light text-secondary border">{{ $narasumber->penugasans->count() }} Penugasan</span>
            </div>
            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th>Program Pelatihan</th>
                            <th>Angkatan</th>
                            <th>Mata Pelatihan</th>
                            <th>Peran</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($narasumber->penugasans as $tugas)
                            <tr>
                                <td class="fw-bold text-dark">{{ $tugas->mataPelatihan->angkatan->pelatihan->nama_pelatihan }}</td>
                                <td><span class="badge bg-light text-dark border">{{ $tugas->mataPelatihan->angkatan->nomor_angkatan }}</span></td>
                                <td>{{ $tugas->mataPelatihan->nama_mata_pelatihan }}</td>
                                <td><span class="badge bg-info text-dark">{{ $tugas->peran }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">Belum ada riwayat penugasan mata pelatihan untuk narasumber ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
