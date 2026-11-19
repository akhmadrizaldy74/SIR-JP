@extends('layouts.app')

@section('title', 'Detail Widyaiswara')
@section('page_title', 'Detail Widyaiswara')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('widyaiswara.index') }}">Widyaiswara</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $widyaiswara->nama_gelar }}</li>
@endsection

@section('page_actions')
    <div class="d-flex gap-2">
        <a href="{{ route('widyaiswara.edit', $widyaiswara) }}" class="btn btn-warning btn-sm">
            <i class="bi bi-pencil me-1"></i> Edit Data
        </a>
        <a href="{{ route('rekapitulasi.detail', ['id' => $widyaiswara->id, 'bulan' => date('m'), 'tahun' => date('Y')]) }}" class="btn btn-primary btn-sm">
            <i class="bi bi-calculator me-1"></i> Lihat Rekap JP
        </a>
        <a href="{{ route('widyaiswara.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>
@endsection

@section('content')
<div class="row">
    <!-- Profil Singkat -->
    <div class="col-lg-4 mb-4">
        <div class="card-custom p-4 text-center">
            <div class="mx-auto mb-3 rounded-circle bg-light d-flex align-items-center justify-content-center text-primary" style="width: 80px; height: 80px; font-size: 2.2rem; border: 3px solid #DBEAFE;">
                <i class="bi bi-person-fill"></i>
            </div>
            <h5 class="fw-bold text-dark mb-1">{{ $widyaiswara->nama_gelar }}</h5>
            <p class="text-muted small mb-2">NIP. {{ $widyaiswara->nip ?? '-' }}</p>
            <span class="badge bg-primary px-3 py-2 rounded-pill mb-3">{{ $widyaiswara->jabatan }}</span>
            <span class="badge bg-{{ $widyaiswara->status == 'Aktif' ? 'success' : 'secondary' }} d-block mx-auto mb-3" style="width: fit-content;">Status: {{ $widyaiswara->status }}</span>

            <div class="text-start border-top pt-3 small">
                <div class="mb-2">
                    <span class="text-muted d-block">NIK:</span>
                    <strong class="text-dark">{{ $widyaiswara->nik }}</strong>
                </div>
                <div class="mb-2">
                    <span class="text-muted d-block">Instansi:</span>
                    <strong class="text-dark">{{ $widyaiswara->instansi }}</strong>
                </div>
                <div class="mb-2">
                    <span class="text-muted d-block">Telepon:</span>
                    <span class="text-dark">{{ $widyaiswara->nomor_telepon ?? '-' }}</span>
                </div>
                <div class="mb-2">
                    <span class="text-muted d-block">Email:</span>
                    <span class="text-dark">{{ $widyaiswara->email ?? '-' }}</span>
                </div>
                <div>
                    <span class="text-muted d-block">Keahlian / Keterangan:</span>
                    <span class="text-dark">{{ $widyaiswara->keterangan ?? '-' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Riwayat Penugasan & Konversi -->
    <div class="col-lg-8">
        <!-- Tab Penugasan Mengajar -->
        <div class="card-custom mb-4">
            <div class="card-custom-header">
                <h5 class="card-custom-title">
                    <i class="bi bi-person-check-fill text-primary"></i> Riwayat Penugasan Mata Pelatihan
                </h5>
                <span class="badge bg-light text-secondary border">{{ $widyaiswara->penugasans->count() }} Penugasan</span>
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
                        @forelse($widyaiswara->penugasans as $tugas)
                            <tr>
                                <td class="fw-bold text-dark">{{ $tugas->mataPelatihan->angkatan->pelatihan->nama_pelatihan }}</td>
                                <td><span class="badge bg-light text-dark border">{{ $tugas->mataPelatihan->angkatan->nomor_angkatan }}</span></td>
                                <td>{{ $tugas->mataPelatihan->nama_mata_pelatihan }}</td>
                                <td><span class="badge bg-info text-dark">{{ $tugas->peran }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3">Belum ada riwayat penugasan pelatihan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tab Konversi JP -->
        <div class="card-custom">
            <div class="card-custom-header">
                <h5 class="card-custom-title">
                    <i class="bi bi-arrow-left-right text-primary"></i> Riwayat Kegiatan Konversi JP
                </h5>
                <span class="badge bg-light text-secondary border">{{ $widyaiswara->konversiJps->count() }} Kegiatan</span>
            </div>
            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Nama Kegiatan</th>
                            <th>Kategori</th>
                            <th class="text-center">JP</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($widyaiswara->konversiJps as $kv)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($kv->tanggal_kegiatan)->format('d/m/Y') }}</td>
                                <td class="fw-semibold text-dark">{{ $kv->nama_kegiatan }}</td>
                                <td><span class="badge bg-light text-secondary border">{{ $kv->kategori_konversi }}</span></td>
                                <td class="text-center fw-bold text-primary">{{ $kv->jumlah_jp }} JP</td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $kv->status_validasi == 'Disetujui' ? 'success' : 'warning' }} badge-custom">
                                        {{ $kv->status_validasi }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-3">Belum ada riwayat konversi JP.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
