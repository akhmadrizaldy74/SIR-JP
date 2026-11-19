@extends('layouts.app')

@section('title', 'Detail Mata Pelatihan')
@section('page_title', 'Detail Mata Pelatihan')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('mata-pelatihan.index') }}">Mata Pelatihan</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $mataPelatihan->nama_mata_pelatihan }}</li>
@endsection

@section('page_actions')
    <div class="d-flex gap-2">
        <a href="{{ route('penugasan.create', ['mata_pelatihan_id' => $mataPelatihan->id]) }}" class="btn btn-navy btn-sm">
            <i class="bi bi-person-plus me-1"></i> Tugaskan Pengajar
        </a>
        <a href="{{ route('jadwal.create', ['mata_pelatihan_id' => $mataPelatihan->id]) }}" class="btn btn-primary btn-sm">
            <i class="bi bi-calendar-plus me-1"></i> Buat Jadwal
        </a>
        <a href="{{ route('mata-pelatihan.edit', $mataPelatihan) }}" class="btn btn-warning btn-sm">
            <i class="bi bi-pencil me-1"></i> Edit
        </a>
        <a href="{{ route('mata-pelatihan.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>
@endsection

@section('content')
<div class="row">
    <!-- Informasi Detail Mata Pelatihan -->
    <div class="col-lg-4 mb-4">
        <div class="card-custom p-4">
            <span class="badge bg-light text-primary border mb-2">{{ $mataPelatihan->angkatan->nomor_angkatan }}</span>
            <h5 class="fw-bold text-dark mb-1">{{ $mataPelatihan->nama_mata_pelatihan }}</h5>
            <p class="text-muted small mb-3">{{ $mataPelatihan->angkatan->pelatihan->nama_pelatihan }}</p>

            <div class="border-top pt-3 small">
                <div class="mb-2">
                    <span class="text-muted d-block">Rencana Jam Pelajaran (JP):</span>
                    <strong class="text-primary fs-5">{{ $mataPelatihan->rencana_jp }} JP</strong>
                </div>
                <div class="mb-2">
                    <span class="text-muted d-block">Metode / Keterangan:</span>
                    <span class="text-dark">{{ $mataPelatihan->keterangan ?? '-' }}</span>
                </div>
                <div class="mb-2">
                    <span class="text-muted d-block">Deskripsi Singkat:</span>
                    <span class="text-dark">{{ $mataPelatihan->deskripsi ?? '-' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Pengajar Ditugaskan & Jadwal Pembelajaran -->
    <div class="col-lg-8">
        <!-- Pengajar Ditugaskan -->
        <div class="card-custom mb-4">
            <div class="card-custom-header">
                <h5 class="card-custom-title">
                    <i class="bi bi-person-check-fill text-primary"></i> Pengajar yang Ditugaskan
                </h5>
                <a href="{{ route('penugasan.create', ['mata_pelatihan_id' => $mataPelatihan->id]) }}" class="btn btn-sm btn-outline-primary">
                    + Tambah Pengajar
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th>Tipe</th>
                            <th>Nama Pengajar</th>
                            <th>Instansi</th>
                            <th>Peran</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($mataPelatihan->penugasans as $tugas)
                            <tr>
                                <td>
                                    <span class="badge bg-{{ $tugas->jenis_pengajar == 'Widyaiswara' ? 'primary' : 'secondary' }}-subtle text-dark border">
                                        {{ $tugas->jenis_pengajar }}
                                    </span>
                                </td>
                                <td class="fw-bold text-dark">{{ $tugas->pengajar_nama }}</td>
                                <td>{{ $tugas->pengajar_instansi }}</td>
                                <td><span class="badge bg-light text-dark border">{{ $tugas->peran }}</span></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDelete('del-tugas-{{ $tugas->id }}', 'Batalkan penugasan {{ $tugas->pengajar_nama }}?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    <form id="del-tugas-{{ $tugas->id }}" action="{{ route('penugasan.destroy', $tugas) }}" method="POST" class="d-none">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-3">Belum ada pengajar yang ditugaskan pada mata pelatihan ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Jadwal Terkait -->
        <div class="card-custom">
            <div class="card-custom-header">
                <h5 class="card-custom-title">
                    <i class="bi bi-calendar3 text-primary"></i> Jadwal Sesi Pembelajaran
                </h5>
                <a href="{{ route('jadwal.create', ['mata_pelatihan_id' => $mataPelatihan->id]) }}" class="btn btn-sm btn-outline-primary">
                    + Tambah Sesi
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th>Tanggal & Waktu</th>
                            <th>Pengajar</th>
                            <th class="text-center">JP</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($mataPelatihan->jadwals as $jdw)
                            <tr>
                                <td>
                                    <div class="fw-semibold text-dark">{{ \Carbon\Carbon::parse($jdw->tanggal)->format('d/m/Y') }}</div>
                                    <div class="text-muted small">{{ substr($jdw->jam_mulai, 0, 5) }} - {{ substr($jdw->jam_selesai, 0, 5) }}</div>
                                </td>
                                <td>{{ $jdw->penugasanPengajar ? $jdw->penugasanPengajar->pengajar_nama : 'Belum Ditugaskan' }}</td>
                                <td class="text-center fw-bold">{{ $jdw->jp_rencana }} JP</td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $jdw->status == 'Terlaksana' ? 'success' : ($jdw->status == 'Terjadwal' ? 'warning text-dark' : 'danger') }} badge-custom">
                                        {{ $jdw->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3">Belum ada jadwal sesi yang dibuat untuk mata pelatihan ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
