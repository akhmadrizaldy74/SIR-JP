@extends('layouts.app')

@section('title', 'Detail Angkatan')
@section('page_title', 'Detail Angkatan Pelatihan')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('angkatan.index') }}">Angkatan</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $angkatan->nomor_angkatan }}</li>
@endsection

@section('page_actions')
    <div class="d-flex gap-2">
        <a href="{{ route('mata-pelatihan.create', ['angkatan_id' => $angkatan->id]) }}" class="btn btn-navy btn-sm">
            <i class="bi bi-plus-lg me-1"></i> Tambah Mata Pelatihan
        </a>
        <a href="{{ route('angkatan.edit', $angkatan) }}" class="btn btn-warning btn-sm">
            <i class="bi bi-pencil me-1"></i> Edit Data
        </a>
        <a href="{{ route('angkatan.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>
@endsection

@section('content')
<div class="row">
    <!-- Profil Angkatan -->
    <div class="col-lg-4 mb-4">
        <div class="card-custom p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="badge bg-light text-primary border">{{ $angkatan->pelatihan->kode_pelatihan }}</span>
                <span class="badge bg-primary">{{ $angkatan->status }}</span>
            </div>
            <h5 class="fw-bold text-dark mb-1">{{ $angkatan->nomor_angkatan }}</h5>
            <p class="text-muted small mb-3">{{ $angkatan->pelatihan->nama_pelatihan }}</p>

            <div class="border-top pt-3 small">
                <div class="mb-2">
                    <span class="text-muted d-block">Jumlah Peserta:</span>
                    <strong class="text-dark">{{ $angkatan->jumlah_peserta }} Orang</strong>
                </div>
                <div class="mb-2">
                    <span class="text-muted d-block">Periode Pelaksanaan:</span>
                    <span class="text-dark">{{ $angkatan->tanggal_mulai->format('d M Y') }} s.d. {{ $angkatan->tanggal_selesai->format('d M Y') }}</span>
                </div>
                <div class="mb-2">
                    <span class="text-muted d-block">Lokasi / Ruang:</span>
                    <span class="text-dark">{{ $angkatan->lokasi ?? 'Kampus PPSDM' }}</span>
                </div>
                <div>
                    <span class="text-muted d-block">Keterangan:</span>
                    <span class="text-dark">{{ $angkatan->keterangan ?? '-' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Mata Pelatihan pada Angkatan ini -->
    <div class="col-lg-8">
        <div class="card-custom">
            <div class="card-custom-header">
                <h5 class="card-custom-title">
                    <i class="bi bi-journal-bookmark-fill text-primary"></i> Daftar Mata Pelatihan
                </h5>
                <span class="badge bg-light text-secondary border">{{ $angkatan->mataPelatihans->count() }} Mata Pelatihan</span>
            </div>
            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th>Mata Pelatihan</th>
                            <th class="text-center">Rencana JP</th>
                            <th>Pengajar Ditugaskan</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($angkatan->mataPelatihans as $mp)
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark">{{ $mp->nama_mata_pelatihan }}</div>
                                    <div class="text-muted small">{{ $mp->keterangan ?? '-' }}</div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-primary border fw-bold">{{ $mp->rencana_jp }} JP</span>
                                </td>
                                <td>
                                    @forelse($mp->penugasans as $tugas)
                                        <div class="small mb-1">
                                            <span class="badge bg-{{ $tugas->jenis_pengajar == 'Widyaiswara' ? 'primary' : 'secondary' }}-subtle text-dark border">
                                                {{ $tugas->jenis_pengajar }}
                                            </span>
                                            <strong class="text-dark ms-1">{{ $tugas->pengajar_nama }}</strong>
                                            <span class="text-muted">({{ $tugas->peran }})</span>
                                        </div>
                                    @empty
                                        <span class="text-muted small fst-italic">Belum ada pengajar ditugaskan</span>
                                    @endforelse
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('penugasan.create', ['mata_pelatihan_id' => $mp->id]) }}" class="btn btn-sm btn-outline-primary" title="Tugaskan Pengajar">
                                        <i class="bi bi-person-plus"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">Belum ada mata pelatihan pada angkatan ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
