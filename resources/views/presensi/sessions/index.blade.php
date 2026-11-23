@extends('layouts.app')

@section('title', 'Daftar Sesi Presensi Pembelajaran')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1">Sesi Presensi Pembelajaran</h4>
        <div class="text-muted small">Kelola sesi presensi per mata pelatihan, tampilkan QR Code, dan verifikasi kehadiran</div>
    </div>
    <a href="{{ route('attendance-sessions.create') }}" class="btn btn-primary d-flex align-items-center gap-2 rounded-3 shadow-sm">
        <i class="bi bi-plus-circle"></i>
        <span>Buka Sesi Baru</span>
    </a>
</div>

<!-- Filter Bar -->
<div class="card border-0 shadow-sm rounded-3 p-3 mb-4 bg-white">
    <form method="GET" action="{{ route('attendance-sessions.index') }}" class="row g-2 align-items-end">
        <div class="col-md-4">
            <label class="form-label small fw-semibold text-muted mb-1">CARI SESI / MATA PELATIHAN</label>
            <input type="text" name="search" value="{{ $search }}" class="form-control form-control-sm" placeholder="Ketik judul sesi, kelas, atau materi...">
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-semibold text-muted mb-1">STATUS SESI</label>
            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Semua Status</option>
                <option value="Aktif" {{ $status === 'Aktif' ? 'selected' : '' }}>Aktif (Presensi Dibuka)</option>
                <option value="Draft" {{ $status === 'Draft' ? 'selected' : '' }}>Draft (Belum Dibuka)</option>
                <option value="Selesai" {{ $status === 'Selesai' ? 'selected' : '' }}>Selesai (Presensi Ditutup)</option>
                <option value="Dibatalkan" {{ $status === 'Dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-semibold text-muted mb-1">TANGGAL SESI</label>
            <input type="date" name="tanggal" value="{{ $tanggal }}" class="form-control form-control-sm" onchange="this.form.submit()">
        </div>
        <div class="col-md-2 d-flex gap-1">
            <button type="submit" class="btn btn-sm btn-primary flex-grow-1">Filter</button>
            <a href="{{ route('attendance-sessions.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
        </div>
    </form>
</div>

<!-- Grid Sesi Presensi -->
<div class="row g-3">
    @forelse($sessions as $sess)
        @php
            $hadirCount = $sess->presensis->where('status_kehadiran', 'Hadir')->count();
            $terlambatCount = $sess->presensis->where('status_kehadiran', 'Terlambat')->count();
            $totalRecorded = $sess->presensis->count();
        @endphp
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border shadow-sm rounded-3 bg-white d-flex flex-column {{ $sess->status === 'Aktif' ? 'border-primary' : '' }}">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="badge bg-light text-dark border small fw-semibold">
                            <i class="bi bi-calendar3"></i> {{ $sess->tanggal->format('d/m/Y') }}
                        </span>
                        @if($sess->status === 'Aktif')
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1">
                                <span class="spinner-grow spinner-grow-sm me-1" style="width: 0.5rem; height: 0.5rem;"></span> Aktif
                            </span>
                        @elseif($sess->status === 'Selesai')
                            <span class="badge bg-secondary-subtle text-secondary border rounded-pill px-2 py-1">Selesai</span>
                        @else
                            <span class="badge bg-light text-muted border rounded-pill px-2 py-1">Draft</span>
                        @endif
                    </div>

                    <h6 class="fw-bold text-dark mb-1">{{ $sess->judul_sesi }}</h6>
                    <div class="text-muted small mb-2 text-truncate">
                        <i class="bi bi-journal-text me-1"></i> {{ $sess->jadwal->mataPelatihan->nama_mata_pelatihan ?? '-' }}
                    </div>

                    <div class="bg-light p-2 rounded-2 mb-3" style="font-size: 0.78rem;">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Kelas:</span>
                            <span class="fw-semibold text-dark">{{ $sess->kelasMapel->nama_kelas ?? '-' }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Jam Sesi:</span>
                            <span class="fw-semibold text-dark">{{ substr($sess->jam_mulai, 0, 5) }} - {{ substr($sess->jam_selesai, 0, 5) }} WIB</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Ruangan:</span>
                            <span class="fw-semibold text-dark">{{ $sess->kelasMapel->ruangan->nama_ruangan ?? ($sess->jadwal->ruangan ?? '-') }}</span>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center text-muted small pt-2 border-top">
                        <div>
                            <span>Kehadiran:</span>
                            <strong class="text-success">{{ $hadirCount + $terlambatCount }}</strong> presensi
                        </div>
                        <span class="badge bg-light text-secondary border">Toleransi: {{ $sess->toleransi_terlambat_menit }} mnt</span>
                    </div>
                </div>

                <!-- Footer Card Actions -->
                <div class="card-footer bg-white border-top p-2 d-flex justify-content-between align-items-center">
                    <div class="d-flex gap-1">
                        @if($sess->status === 'Aktif')
                            <a href="{{ route('attendance-sessions.qr_display', $sess->id) }}" target="_blank" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold" title="Tampilkan Layar QR Proyektor">
                                <i class="bi bi-qr-code-scan"></i> Layar QR
                            </a>
                        @endif
                        <a href="{{ route('attendance-sessions.show', $sess->id) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-2" title="Detail & Monitor Presensi">
                            <i class="bi bi-eye"></i> Monitor
                        </a>
                        <a href="{{ route('attendance-manual.index', $sess->id) }}" class="btn btn-sm btn-outline-info rounded-pill px-2" title="Input / Koreksi Presensi Manual">
                            <i class="bi bi-pencil-square"></i> Manual
                        </a>
                    </div>

                    <div>
                        @if($sess->status === 'Draft')
                            <form action="{{ route('attendance-sessions.activate', $sess->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success rounded-pill px-2 py-1" title="Buka Presensi Sekarang">
                                    <i class="bi bi-play-fill"></i> Buka
                                </button>
                            </form>
                        @elseif($sess->status === 'Aktif')
                            <form action="{{ route('attendance-sessions.close', $sess->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menutup sesi presensi ini? Presensi QR tidak dapat dilakukan lagi setelah ditutup.');">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-warning text-dark rounded-pill px-2 py-1" title="Tutup Sesi Presensi">
                                    <i class="bi bi-stop-fill"></i> Tutup
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="card bg-white p-5 text-center border rounded-3 text-muted">
                <i class="bi bi-calendar-x fs-1 text-secondary mb-2"></i>
                <h5 class="fw-bold mb-1">Belum Ada Sesi Presensi</h5>
                <p class="small text-muted mb-3">Klik tombol "Buka Sesi Baru" untuk membuat sesi presensi dari jadwal pelatihan.</p>
                <div>
                    <a href="{{ route('attendance-sessions.create') }}" class="btn btn-primary rounded-pill px-4">
                        <i class="bi bi-plus-circle me-1"></i> Buka Sesi Baru
                    </a>
                </div>
            </div>
        </div>
    @endforelse
</div>

<div class="mt-4">
    {{ $sessions->links('vendor.pagination.bootstrap-5') }}
</div>
@endsection
