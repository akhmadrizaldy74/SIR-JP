@extends('layouts.app')

@section('title', 'Dashboard Presensi KCLC')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1">Dashboard Monitoring Presensi</h4>
        <div class="text-muted small">Ringkasan aktivitas kehadiran peserta diklat KCLC PPSDMK secara realtime</div>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('attendance-sessions.create') }}" class="btn btn-primary d-flex align-items-center gap-2 rounded-3 shadow-sm">
            <i class="bi bi-plus-circle"></i>
            <span>Buka Sesi Presensi</span>
        </a>
        <a href="{{ route('presensi.rekap') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2 rounded-3">
            <i class="bi bi-table"></i>
            <span>Rekap Kehadiran</span>
        </a>
    </div>
</div>

<!-- Filter Pelatihan / Angkatan -->
<div class="card border-0 shadow-sm rounded-3 p-3 mb-4 bg-white">
    <form method="GET" action="{{ route('presensi.dashboard') }}" class="row g-2 align-items-end">
        <div class="col-md-4">
            <label class="form-label small fw-semibold text-muted mb-1">PROGRAM PELATIHAN</label>
            <select name="pelatihan_id" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Semua Program Pelatihan</option>
                @foreach($pelatihans as $p)
                    <option value="{{ $p->id }}" {{ $pelatihanId == $p->id ? 'selected' : '' }}>
                        {{ $p->nama_pelatihan }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-semibold text-muted mb-1">ANGKATAN</label>
            <select name="angkatan_id" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Semua Angkatan</option>
                @foreach($angkatans as $a)
                    <option value="{{ $a->id }}" {{ $angkatanId == $a->id ? 'selected' : '' }}>
                        {{ $a->nomor_angkatan }} ({{ $a->pelatihan->nama_pelatihan ?? '-' }})
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-semibold text-muted mb-1">KELAS</label>
            <select name="kelas_mapel_id" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Semua Kelas</option>
                @foreach($kelasMapels as $k)
                    <option value="{{ $k->id }}" {{ $kelasMapelId == $k->id ? 'selected' : '' }}>
                        {{ $k->nama_kelas }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <a href="{{ route('presensi.dashboard') }}" class="btn btn-sm btn-outline-secondary w-100">Reset Filter</a>
        </div>
    </form>
</div>

<!-- 8 Stat Cards Presensi -->
<div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-3 mb-4">
    <!-- Total Peserta Terdaftar -->
    <div class="col">
        <div class="card p-3 border shadow-sm rounded-3 bg-white h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-2" style="width: 46px; height: 46px; background-color: #EEF2FF; color: #4F46E5;">
                        <i class="bi bi-people-fill fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-medium">Total Peserta</div>
                        <h3 class="fw-bold text-dark mb-0 lh-1">{{ number_format($stats['total_peserta']) }}</h3>
                    </div>
                </div>
                <span class="badge rounded-pill fw-medium" style="background-color: #EEF2FF; color: #4F46E5; font-size: 0.75rem;">Peserta</span>
            </div>
        </div>
    </div>

    <!-- Sesi Aktif -->
    <div class="col">
        <div class="card p-3 border shadow-sm rounded-3 bg-white h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-2" style="width: 46px; height: 46px; background-color: #ECFDF5; color: #059669;">
                        <i class="bi bi-broadcast fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-medium">Sesi Dibuka</div>
                        <h3 class="fw-bold text-dark mb-0 lh-1">{{ $stats['sesi_aktif'] }} <span class="fs-6 text-muted fw-normal">/ {{ $stats['total_sesi'] }}</span></h3>
                    </div>
                </div>
                <span class="badge rounded-pill fw-medium" style="background-color: #D1FAE5; color: #059669; font-size: 0.75rem;">Sesi Aktif</span>
            </div>
        </div>
    </div>

    <!-- Hadir -->
    <div class="col">
        <div class="card p-3 border shadow-sm rounded-3 bg-white h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-2" style="width: 46px; height: 46px; background-color: #D1FAE5; color: #065F46;">
                        <i class="bi bi-check-circle-fill fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-medium">Hadir Tepat Waktu</div>
                        <h3 class="fw-bold text-dark mb-0 lh-1">{{ number_format($stats['hadir']) }}</h3>
                    </div>
                </div>
                <span class="badge rounded-pill fw-medium" style="background-color: #D1FAE5; color: #065F46; font-size: 0.75rem;">Hadir</span>
            </div>
        </div>
    </div>

    <!-- Terlambat -->
    <div class="col">
        <div class="card p-3 border shadow-sm rounded-3 bg-white h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-2" style="width: 46px; height: 46px; background-color: #DBEAFE; color: #1E40AF;">
                        <i class="bi bi-hourglass-bottom fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-medium">Terlambat</div>
                        <h3 class="fw-bold text-dark mb-0 lh-1">{{ number_format($stats['terlambat']) }}</h3>
                    </div>
                </div>
                <span class="badge rounded-pill fw-medium" style="background-color: #DBEAFE; color: #1E40AF; font-size: 0.75rem;">Terlambat</span>
            </div>
        </div>
    </div>

    <!-- Izin -->
    <div class="col">
        <div class="card p-3 border shadow-sm rounded-3 bg-white h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-2" style="width: 46px; height: 46px; background-color: #FEF3C7; color: #92400E;">
                        <i class="bi bi-envelope-paper-fill fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-medium">Izin Resmi</div>
                        <h3 class="fw-bold text-dark mb-0 lh-1">{{ number_format($stats['izin']) }}</h3>
                    </div>
                </div>
                <span class="badge rounded-pill fw-medium" style="background-color: #FEF3C7; color: #92400E; font-size: 0.75rem;">Izin</span>
            </div>
        </div>
    </div>

    <!-- Sakit -->
    <div class="col">
        <div class="card p-3 border shadow-sm rounded-3 bg-white h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-2" style="width: 46px; height: 46px; background-color: #FCE7F3; color: #9D174D;">
                        <i class="bi bi-heart-pulse-fill fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-medium">Sakit</div>
                        <h3 class="fw-bold text-dark mb-0 lh-1">{{ number_format($stats['sakit']) }}</h3>
                    </div>
                </div>
                <span class="badge rounded-pill fw-medium" style="background-color: #FCE7F3; color: #9D174D; font-size: 0.75rem;">Sakit</span>
            </div>
        </div>
    </div>

    <!-- Alpa -->
    <div class="col">
        <div class="card p-3 border shadow-sm rounded-3 bg-white h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-2" style="width: 46px; height: 46px; background-color: #FEE2E2; color: #991B1B;">
                        <i class="bi bi-x-circle-fill fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-medium">Tanpa Keterangan</div>
                        <h3 class="fw-bold text-dark mb-0 lh-1">{{ number_format($stats['alpa']) }}</h3>
                    </div>
                </div>
                <span class="badge rounded-pill fw-medium" style="background-color: #FEE2E2; color: #991B1B; font-size: 0.75rem;">Alpa</span>
            </div>
        </div>
    </div>

    <!-- Pengajuan Pending -->
    <div class="col">
        <div class="card p-3 border shadow-sm rounded-3 bg-white h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-2" style="width: 46px; height: 46px; background-color: #EDE9FE; color: #5B21B6;">
                        <i class="bi bi-bell-fill fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-medium">Pending Verifikasi</div>
                        <h3 class="fw-bold text-dark mb-0 lh-1">{{ number_format($stats['pending_requests']) }}</h3>
                    </div>
                </div>
                <a href="{{ route('attendance-requests.index') }}" class="badge rounded-pill fw-medium text-decoration-none" style="background-color: #EDE9FE; color: #5B21B6; font-size: 0.75rem;">
                    Lihat &rarr;
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Grafik & Sesi Terkini -->
<div class="row g-4 mb-4">
    <!-- Chart Donut Status Kehadiran -->
    <div class="col-lg-5">
        <div class="card bg-white border shadow-sm rounded-3 p-4 h-100">
            <h5 class="fw-bold text-dark mb-1">Distribusi Status Kehadiran</h5>
            <div class="text-muted small mb-3">Persentase kehadiran total: <strong class="text-success">{{ $stats['persen_kehadiran'] }}%</strong></div>
            <div style="height: 250px;" class="d-flex align-items-center justify-content-center">
                <canvas id="chartPresensiDonut"></canvas>
            </div>
        </div>
    </div>

    <!-- Sesi Pembelajaran Terbaru -->
    <div class="col-lg-7">
        <div class="card bg-white border shadow-sm rounded-3 p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="fw-bold text-dark mb-0">Sesi Presensi Terkini</h5>
                    <div class="text-muted small">Status pelaksanaan sesi pembelajaran</div>
                </div>
                <a href="{{ route('attendance-sessions.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                    Semua Sesi &rarr;
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                    <thead class="table-light">
                        <tr>
                            <th>Sesi & Pelatihan</th>
                            <th>Jadwal</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentSessions as $rs)
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark">{{ $rs->judul_sesi }}</div>
                                    <small class="text-muted">{{ $rs->kelasMapel->nama_kelas ?? ($rs->jadwal->mataPelatihan->nama_mata_pelatihan ?? '-') }}</small>
                                </td>
                                <td>
                                    <div>{{ $rs->tanggal->format('d/m/Y') }}</div>
                                    <small class="text-muted">{{ substr($rs->jam_mulai, 0, 5) }} - {{ substr($rs->jam_selesai, 0, 5) }}</small>
                                </td>
                                <td>
                                    @if($rs->status === 'Aktif')
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">Aktif</span>
                                    @elseif($rs->status === 'Selesai')
                                        <span class="badge bg-secondary-subtle text-secondary border rounded-pill">Selesai</span>
                                    @else
                                        <span class="badge bg-light text-muted border rounded-pill">Draft</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        @if($rs->status === 'Aktif')
                                            <a href="{{ route('attendance-sessions.qr_display', $rs->id) }}" target="_blank" class="btn btn-outline-primary" title="Layar Proyektor QR">
                                                <i class="bi bi-qr-code"></i>
                                            </a>
                                        @endif
                                        <a href="{{ route('attendance-sessions.show', $rs->id) }}" class="btn btn-outline-secondary" title="Detail Presensi">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('attendance-manual.index', $rs->id) }}" class="btn btn-outline-info" title="Input Manual">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3">Belum ada sesi presensi yang dibuat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('chartPresensiDonut');
        if (ctx) {
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Hadir', 'Terlambat', 'Izin', 'Sakit', 'Alpa', 'Dispensasi'],
                    datasets: [{
                        data: [
                            {{ $stats['hadir'] }},
                            {{ $stats['terlambat'] }},
                            {{ $stats['izin'] }},
                            {{ $stats['sakit'] }},
                            {{ $stats['alpa'] }},
                            {{ $stats['dispensasi'] }}
                        ],
                        backgroundColor: [
                            '#10B981', // Hadir - Hijau
                            '#3B82F6', // Terlambat - Biru
                            '#F59E0B', // Izin - Kuning
                            '#EC4899', // Sakit - Merah Muda
                            '#EF4444', // Alpa - Merah
                            '#8B5CF6'  // Dispensasi - Ungu
                        ],
                        borderWidth: 2,
                        borderColor: '#FFFFFF'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { boxWidth: 12, font: { size: 11 } }
                        }
                    },
                    cutout: '65%'
                }
            });
        }
    });
</script>
@endsection
