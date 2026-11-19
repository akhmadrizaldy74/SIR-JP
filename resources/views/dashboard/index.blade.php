@extends('layouts.app')

@section('title', 'Dashboard Administrasi')
@section('page_title', 'Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Dashboard Ringkasan</li>
@endsection

@section('page_actions')
    <form method="GET" action="{{ route('dashboard') }}" class="d-flex align-items-center gap-2 bg-white p-2 rounded border shadow-sm">
        <div class="d-flex align-items-center gap-1">
            <i class="bi bi-calendar-event text-primary"></i>
            <span class="small fw-semibold text-muted">Periode:</span>
        </div>
        <select name="bulan" class="form-select form-select-sm" style="width: 140px;">
            @for($m = 1; $m <= 12; $m++)
                <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                    {{ (new \App\Services\JPRecapitulationService)->getNamaBulan($m) }}
                </option>
            @endfor
        </select>
        <select name="tahun" class="form-select form-select-sm" style="width: 100px;">
            @for($y = date('Y') - 2; $y <= date('Y') + 1; $y++)
                <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endfor
        </select>
        <button type="submit" class="btn btn-sm btn-navy px-3">
            <i class="bi bi-filter"></i> Filter
        </button>
    </form>
@endsection

@section('content')
<!-- Filter Info Banner -->
<div class="alert alert-light border d-flex align-items-center justify-content-between p-3 mb-4 rounded-3 shadow-sm">
    <div class="d-flex align-items-center gap-2">
        <div class="badge bg-primary p-2 rounded-circle">
            <i class="bi bi-info-circle text-white"></i>
        </div>
        <div>
            <span class="text-muted small">Menampilkan data operasional periode:</span>
            <strong class="text-dark d-block">{{ $namaBulan }} {{ $tahun }}</strong>
        </div>
    </div>
    <div class="text-end d-none d-sm-block">
        <span class="text-muted small">Ketentuan Beban:</span>
        <span class="badge bg-navy px-3 py-1">Minimum 32 JP / Bulan</span>
    </div>
</div>

<!-- 6 Stat Cards -->
<div class="row g-3 mb-4">
    <!-- Total Widyaiswara -->
    <div class="col-xl-2 col-md-4 col-sm-6">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="stat-label">Widyaiswara</span>
                <div class="stat-icon navy">
                    <i class="bi bi-person-badge"></i>
                </div>
            </div>
            <div class="stat-value">{{ $stats['total_widyaiswara'] }}</div>
            <div class="small text-muted mt-1">Pengajar internal aktif</div>
        </div>
    </div>

    <!-- Total Narasumber -->
    <div class="col-xl-2 col-md-4 col-sm-6">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="stat-label">Narasumber</span>
                <div class="stat-icon blue">
                    <i class="bi bi-person-lines-fill"></i>
                </div>
            </div>
            <div class="stat-value">{{ $stats['total_narasumber'] }}</div>
            <div class="small text-muted mt-1">Pakar eksternal aktif</div>
        </div>
    </div>

    <!-- Total Pelatihan -->
    <div class="col-xl-2 col-md-4 col-sm-6">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="stat-label">Program Pelatihan</span>
                <div class="stat-icon green">
                    <i class="bi bi-mortarboard"></i>
                </div>
            </div>
            <div class="stat-value">{{ $stats['total_pelatihan'] }}</div>
            <div class="small text-muted mt-1">Program terdaftar</div>
        </div>
    </div>

    <!-- Total Angkatan -->
    <div class="col-xl-2 col-md-4 col-sm-6">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="stat-label">Total Angkatan</span>
                <div class="stat-icon amber">
                    <i class="bi bi-people"></i>
                </div>
            </div>
            <div class="stat-value">{{ $stats['total_angkatan'] }}</div>
            <div class="small text-muted mt-1">Rombongan belajar</div>
        </div>
    </div>

    <!-- Total Jadwal Bulan Ini -->
    <div class="col-xl-2 col-md-4 col-sm-6">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="stat-label">Jadwal Sesi</span>
                <div class="stat-icon blue">
                    <i class="bi bi-calendar-check"></i>
                </div>
            </div>
            <div class="stat-value">{{ $stats['jadwal_terlaksana_count'] }} <span class="fs-6 fw-normal text-muted">/ {{ $stats['jadwal_bulan_ini_count'] }}</span></div>
            <div class="small text-success mt-1 fw-semibold">{{ $stats['jadwal_terlaksana_count'] }} sesi terlaksana</div>
        </div>
    </div>

    <!-- Total JP Terealisasi -->
    <div class="col-xl-2 col-md-4 col-sm-6">
        <div class="stat-card border-primary" style="background: linear-gradient(180deg, #FFFFFF 0%, #EFF6FF 100%);">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="stat-label text-primary fw-bold">Total JP Terealisasi</span>
                <div class="stat-icon blue text-white" style="background-color: var(--secondary-blue);">
                    <i class="bi bi-stopwatch-fill"></i>
                </div>
            </div>
            <div class="stat-value text-primary">{{ $stats['total_jp_terealisasi'] }} <span class="fs-6 fw-normal">JP</span></div>
            <div class="small text-muted mt-1">TM: {{ $stats['jp_tatap_muka'] }} | KV: {{ $stats['jp_konversi'] }}</div>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="row mb-4">
    <!-- Bar/Line Chart Rekap JP Bulanan -->
    <div class="col-lg-8 mb-4 mb-lg-0">
        <div class="card-custom h-100">
            <div class="card-custom-header">
                <h5 class="card-custom-title">
                    <i class="bi bi-bar-chart-line-fill text-primary"></i> Tren Rekapitulasi Jam Pelajaran Tahun {{ $tahun }}
                </h5>
                <span class="badge bg-light text-secondary border">Tatap Muka & Konversi</span>
            </div>
            <div class="p-3">
                <canvas id="jpMonthlyChart" height="130"></canvas>
            </div>
        </div>
    </div>

    <!-- Doughnut Chart Pencapaian Widyaiswara -->
    <div class="col-lg-4">
        <div class="card-custom h-100">
            <div class="card-custom-header">
                <h5 class="card-custom-title">
                    <i class="bi bi-pie-chart-fill text-primary"></i> Status Widyaiswara
                </h5>
                <span class="badge bg-light text-secondary border">{{ $namaBulan }}</span>
            </div>
            <div class="p-4 d-flex flex-column align-items-center justify-content-center">
                <div style="width: 200px; height: 200px; position: relative;">
                    <canvas id="wiStatusChart"></canvas>
                </div>
                <div class="w-100 mt-4">
                    <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                        <span class="small"><i class="bi bi-circle-fill text-primary me-2"></i>Melebihi Target (&gt; 32 JP)</span>
                        <span class="badge bg-primary rounded-pill">{{ $stats['wi_melebihi'] }} Orang</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                        <span class="small"><i class="bi bi-circle-fill text-success me-2"></i>Memenuhi Target (= 32 JP)</span>
                        <span class="badge bg-success rounded-pill">{{ $stats['wi_memenuhi'] }} Orang</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-1">
                        <span class="small"><i class="bi bi-circle-fill text-danger me-2"></i>Belum Memenuhi (&lt; 32 JP)</span>
                        <span class="badge bg-danger rounded-pill">{{ $stats['wi_belum_memenuhi'] }} Orang</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tables & Activity Feed Section -->
<div class="row g-4">
    <!-- Top Widyaiswara Achievement Table -->
    <div class="col-lg-7">
        <div class="card-custom mb-4">
            <div class="card-custom-header">
                <h5 class="card-custom-title">
                    <i class="bi bi-trophy-fill text-warning"></i> Ringkasan JP Widyaiswara Periode {{ $namaBulan }}
                </h5>
                <a href="{{ route('rekapitulasi.index', ['bulan' => $bulan, 'tahun' => $tahun]) }}" class="btn btn-sm btn-outline-primary">
                    Lihat Semua <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th>Widyaiswara</th>
                            <th>Jabatan</th>
                            <th class="text-center">TM (A)</th>
                            <th class="text-center">KV (B)</th>
                            <th class="text-center">Total (A+B)</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stats['top_widyaiswara'] as $wi)
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark">{{ $wi['nama_gelar'] }}</div>
                                    <div class="text-muted" style="font-size: 0.75rem;">NIP. {{ $wi['nip'] ?? '-' }}</div>
                                </td>
                                <td><span class="badge bg-light text-dark border">{{ $wi['jabatan'] }}</span></td>
                                <td class="text-center fw-semibold text-secondary">{{ $wi['total_a'] }} JP</td>
                                <td class="text-center fw-semibold text-secondary">{{ $wi['total_b'] }} JP</td>
                                <td class="text-center fw-bold text-primary">{{ $wi['total_ab'] }} JP</td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $wi['badge_class'] }} badge-custom">
                                        {{ $wi['status_pencapaian'] }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Belum ada data rekapitulasi pada periode ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Jadwal Pelatihan Mendatang -->
        <div class="card-custom">
            <div class="card-custom-header">
                <h5 class="card-custom-title">
                    <i class="bi bi-calendar-event-fill text-primary"></i> Jadwal Pelatihan Mendatang
                </h5>
                <a href="{{ route('jadwal.index') }}" class="btn btn-sm btn-outline-primary">
                    Kelola Jadwal <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th>Tanggal & Waktu</th>
                            <th>Pelatihan & Angkatan</th>
                            <th>Mata Pelatihan</th>
                            <th>Pengajar</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stats['upcoming_jadwals'] as $jdw)
                            <tr>
                                <td>
                                    <div class="fw-semibold text-dark">{{ \Carbon\Carbon::parse($jdw->tanggal)->format('d M Y') }}</div>
                                    <div class="text-muted small">{{ substr($jdw->jam_mulai, 0, 5) }} - {{ substr($jdw->jam_selesai, 0, 5) }} WIB</div>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $jdw->mataPelatihan->angkatan->pelatihan->nama_pelatihan }}</div>
                                    <span class="badge bg-light text-secondary border">{{ $jdw->mataPelatihan->angkatan->nomor_angkatan }}</span>
                                </td>
                                <td>{{ $jdw->mataPelatihan->nama_mata_pelatihan }}</td>
                                <td>
                                    <span class="text-dark fw-medium">{{ $jdw->penugasanPengajar ? $jdw->penugasanPengajar->pengajar_nama : 'Belum Ditugaskan' }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-warning text-dark badge-custom">{{ $jdw->status }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">Tidak ada jadwal mendatang yang terjadwal.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent Activity Feed -->
    <div class="col-lg-5">
        <div class="card-custom h-100">
            <div class="card-custom-header">
                <h5 class="card-custom-title">
                    <i class="bi bi-clock-history text-primary"></i> Aktivitas Sistem Terbaru
                </h5>
            </div>
            <div class="p-3">
                <div class="timeline">
                    @forelse($recentActivities as $act)
                        <div class="d-flex align-items-start gap-3 py-2 border-bottom">
                            <div class="p-2 rounded bg-light text-primary border" style="min-width: 36px; text-align: center;">
                                <i class="bi bi-activity"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center">
                                    <strong class="small text-dark">{{ $act->aktivitas }}</strong>
                                    <span class="text-muted" style="font-size: 0.7rem;">{{ $act->created_at->diffForHumans() }}</span>
                                </div>
                                <div class="text-muted small mt-1">{{ $act->detail ?? '-' }}</div>
                                <div class="mt-1">
                                    <span class="badge bg-light text-secondary border" style="font-size: 0.65rem;">{{ $act->modul }}</span>
                                    <span class="text-muted" style="font-size: 0.7rem;">oleh {{ $act->user->name ?? 'Sistem' }}</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">Belum ada riwayat aktivitas.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Chart Bar/Line Bulanan
    const ctxMonthly = document.getElementById('jpMonthlyChart');
    if (ctxMonthly) {
        new Chart(ctxMonthly, {
            type: 'bar',
            data: {
                labels: {!! json_encode($stats['chart_labels']) !!},
                datasets: [
                    {
                        label: 'JP Tatap Muka (A)',
                        data: {!! json_encode($stats['chart_tatap_muka']) !!},
                        backgroundColor: '#2563EB',
                        borderRadius: 4,
                    },
                    {
                        label: 'JP Konversi (B)',
                        data: {!! json_encode($stats['chart_konversi']) !!},
                        backgroundColor: '#60A5FA',
                        borderRadius: 4,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.parsed.y + ' JP';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 10
                        }
                    }
                }
            }
        });
    }

    // 2. Donut Chart Status WI
    const ctxStatus = document.getElementById('wiStatusChart');
    if (ctxStatus) {
        new Chart(ctxStatus, {
            type: 'doughnut',
            data: {
                labels: ['Melebihi', 'Memenuhi', 'Belum Memenuhi'],
                datasets: [{
                    data: [
                        {{ $stats['wi_melebihi'] }},
                        {{ $stats['wi_memenuhi'] }},
                        {{ $stats['wi_belum_memenuhi'] }}
                    ],
                    backgroundColor: ['#2563EB', '#16A34A', '#DC2626'],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                cutout: '70%'
            }
        });
    }
});
</script>
@endpush
