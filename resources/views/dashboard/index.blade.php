@extends('layouts.app')

@section('title', 'Dashboard KCLC & SIR-JP')
@section('page_title', 'HOME')

@section('breadcrumb_custom')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">home</a></li>
    <li class="breadcrumb-item active" aria-current="page">Home</li>
@endsection

@section('content')
<!-- Banner KCLC Learning Center Sesuai Gambar 10 -->
<div class="card bg-white border-0 shadow-sm rounded-4 mb-4 p-4 position-relative overflow-hidden">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h4 class="fw-bold text-dark mb-1" style="font-size: 1.45rem; letter-spacing: -0.3px;">Dashboard KCLC Learning Center</h4>
            <p class="text-muted mb-0" style="font-size: 0.95rem;">
                Platform Pembelajaran Digital PPSDM Kementerian Ketenagakerjaan
            </p>
        </div>
        <div class="col-md-4 text-end d-none d-md-block position-relative">
            <!-- Ilustrasi Workspace Meja & Laptop Presisi Gambar KCLC 10 -->
            <svg width="240" height="110" viewBox="0 0 240 110" fill="none" xmlns="http://www.w3.org/2000/svg" style="max-height: 110px;">
                <circle cx="190" cy="55" r="50" stroke="#E2E8F0" stroke-width="1.2" stroke-dasharray="4 3"/>
                <circle cx="190" cy="55" r="75" stroke="#F1F5F9" stroke-width="1.5"/>
                <circle cx="190" cy="55" r="100" stroke="#F8FAFC" stroke-width="1.5"/>
                <rect x="80" y="74" width="135" height="5" rx="2.5" fill="#475569"/>
                <rect x="95" y="79" width="4" height="26" rx="2" fill="#94A3B8"/>
                <rect x="195" y="79" width="4" height="26" rx="2" fill="#94A3B8"/>
                <rect x="135" y="36" width="46" height="30" rx="4" fill="#0284C7"/>
                <rect x="138" y="39" width="40" height="24" rx="2" fill="#E0F2FE"/>
                <rect x="156" y="66" width="4" height="8" fill="#64748B"/>
                <rect x="148" y="73" width="20" height="2" rx="1" fill="#475569"/>
                <circle cx="112" cy="42" r="9" fill="#F59E0B"/>
                <path d="M107 40 H117" stroke="#1E293B" stroke-width="2" stroke-linecap="round"/>
                <path d="M100 66 C100 54, 124 54, 124 66 Z" fill="#059669"/>
                <rect x="96" y="64" width="28" height="5" rx="2.5" fill="#10B981"/>
                <rect x="108" y="69" width="4" height="18" fill="#64748B"/>
                <path d="M100 87 L120 87" stroke="#64748B" stroke-width="3" stroke-linecap="round"/>
                <path d="M200 68 C200 62, 206 62, 206 68 Z" fill="#10B981"/>
                <rect x="201" y="68" width="4" height="6" rx="1" fill="#F59E0B"/>
            </svg>
        </div>
    </div>
</div>

<!-- Section: Statistik KCLC Learning Center (10 Cards Sesuai Gambar 10) -->
<div class="mb-3">
    <h5 class="fw-bold text-dark mb-1" style="font-size: 1.15rem;">Statistik KCLC Learning Center</h5>
    <div class="text-muted small">Ringkasan data dan aktivitas sistem pembelajaran</div>
</div>

<!-- Row 1: 3 Cards -->
<div class="row g-3 mb-3">
    <div class="col-lg-4 col-md-6">
        <div class="card p-3 border shadow-sm rounded-3 bg-white h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-2" style="width: 46px; height: 46px; background-color: #EEF2FF; color: #4F46E5;">
                        <i class="bi bi-easel fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-medium">Pelatihan Diklat</div>
                        <h3 class="fw-bold text-dark mb-0 lh-1 counter-animate" data-target="{{ $kclcStats['total_pelatihan'] }}">{{ $kclcStats['total_pelatihan'] }}</h3>
                    </div>
                </div>
                <span class="badge rounded-pill fw-medium" style="background-color: #EEF2FF; color: #4F46E5; font-size: 0.75rem; padding: 0.35rem 0.65rem;">Pelatihan</span>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-md-6">
        <div class="card p-3 border shadow-sm rounded-3 bg-white h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-2" style="width: 46px; height: 46px; background-color: #E0F2FE; color: #0284C7;">
                        <i class="bi bi-journal-text fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-medium">Kelas</div>
                        <h3 class="fw-bold text-dark mb-0 lh-1 counter-animate" data-target="{{ $kclcStats['total_kelas'] }}">{{ $kclcStats['total_kelas'] }}</h3>
                    </div>
                </div>
                <span class="badge rounded-pill fw-medium" style="background-color: #E0F2FE; color: #0284C7; font-size: 0.75rem; padding: 0.35rem 0.65rem;">Kelas</span>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-md-6">
        <div class="card p-3 border shadow-sm rounded-3 bg-white h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-2" style="width: 46px; height: 46px; background-color: #ECFDF5; color: #059669;">
                        <i class="bi bi-door-open fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-medium">Ruangan</div>
                        <h3 class="fw-bold text-dark mb-0 lh-1 counter-animate" data-target="{{ $kclcStats['total_ruangan'] }}">{{ $kclcStats['total_ruangan'] }}</h3>
                    </div>
                </div>
                <span class="badge rounded-pill fw-medium" style="background-color: #D1FAE5; color: #059669; font-size: 0.75rem; padding: 0.35rem 0.65rem;">Ruangan</span>
            </div>
        </div>
    </div>
</div>

<!-- Row 2: 3 Cards -->
<div class="row g-3 mb-3">
    <div class="col-lg-4 col-md-6">
        <div class="card p-3 border shadow-sm rounded-3 bg-white h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-2" style="width: 46px; height: 46px; background-color: #FEF3C7; color: #D97706;">
                        <i class="bi bi-card-checklist fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-medium">Form Aspek Penilaian Sikap</div>
                        <h3 class="fw-bold text-dark mb-0 lh-1 counter-animate" data-target="{{ $kclcStats['total_sikap'] }}">{{ $kclcStats['total_sikap'] }}</h3>
                    </div>
                </div>
                <span class="badge rounded-pill fw-medium" style="background-color: #FEF3C7; color: #D97706; font-size: 0.75rem; padding: 0.35rem 0.65rem;">Penilaian</span>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-md-6">
        <div class="card p-3 border shadow-sm rounded-3 bg-white h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-2" style="width: 46px; height: 46px; background-color: #FEE2E2; color: #DC2626;">
                        <i class="bi bi-file-earmark-check fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-medium">Form Evaluasi</div>
                        <h3 class="fw-bold text-dark mb-0 lh-1 counter-animate" data-target="{{ $kclcStats['total_evaluasi'] }}">{{ $kclcStats['total_evaluasi'] }}</h3>
                    </div>
                </div>
                <span class="badge rounded-pill fw-medium" style="background-color: #FEE2E2; color: #DC2626; font-size: 0.75rem; padding: 0.35rem 0.65rem;">Evaluasi</span>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-md-6">
        <div class="card p-3 border shadow-sm rounded-3 bg-white h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-2" style="width: 46px; height: 46px; background-color: #EEF2FF; color: #4F46E5;">
                        <i class="bi bi-people-fill fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-medium">Angkatan</div>
                        <h3 class="fw-bold text-dark mb-0 lh-1 counter-animate" data-target="{{ $kclcStats['total_angkatan'] }}">{{ $kclcStats['total_angkatan'] }}</h3>
                    </div>
                </div>
                <span class="badge rounded-pill fw-medium" style="background-color: #EEF2FF; color: #4F46E5; font-size: 0.75rem; padding: 0.35rem 0.65rem;">Angkatan</span>
            </div>
        </div>
    </div>
</div>

<!-- Row 3: 4 Cards -->
<div class="row g-3 mb-4">
    <div class="col-lg-3 col-md-6">
        <div class="card p-3 border shadow-sm rounded-3 bg-white h-100">
            <div class="d-flex align-items-center gap-3">
                <div class="d-flex align-items-center justify-content-center rounded-2" style="width: 46px; height: 46px; background-color: #EEF2FF; color: #4F46E5;">
                    <i class="bi bi-easel fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small fw-medium">Pelatihan Diklat</div>
                    <h3 class="fw-bold text-dark mb-0 lh-1 counter-animate" data-target="{{ $kclcStats['total_pelatihan'] }}">{{ $kclcStats['total_pelatihan'] }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="card p-3 border shadow-sm rounded-3 bg-white h-100">
            <div class="d-flex align-items-center gap-3">
                <div class="d-flex align-items-center justify-content-center rounded-2" style="width: 46px; height: 46px; background-color: #E0F2FE; color: #0284C7;">
                    <i class="bi bi-person-fill fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small fw-medium">Peserta</div>
                    <h3 class="fw-bold text-dark mb-0 lh-1 counter-animate" data-target="{{ $kclcStats['total_peserta'] }}">{{ number_format($kclcStats['total_peserta']) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="card p-3 border shadow-sm rounded-3 bg-white h-100">
            <div class="d-flex align-items-center gap-3">
                <div class="d-flex align-items-center justify-content-center rounded-2" style="width: 46px; height: 46px; background-color: #ECFDF5; color: #059669;">
                    <i class="bi bi-check-circle-fill fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small fw-medium">Presensi Hadir</div>
                    <h3 class="fw-bold text-dark mb-0 lh-1 counter-animate" data-target="{{ $kclcStats['total_presensi'] }}">{{ number_format($kclcStats['total_presensi']) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="card p-3 border shadow-sm rounded-3 bg-white h-100">
            <div class="d-flex align-items-center gap-3">
                <div class="d-flex align-items-center justify-content-center rounded-2" style="width: 46px; height: 46px; background-color: #FEF3C7; color: #D97706;">
                    <i class="bi bi-clipboard2-check fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small fw-medium">Evaluasi Selesai</div>
                    <h3 class="fw-bold text-dark mb-0 lh-1 counter-animate" data-target="{{ $kclcStats['evaluasi_selesai'] }}">{{ $kclcStats['evaluasi_selesai'] }}</h3>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================== -->
<!-- TABEL: STATISTIK PELATIHAN (PRESISI 100% SESUAI GAMBAR 11 KCLC)-->
<!-- ============================================================== -->
<div class="card bg-white border shadow-sm rounded-3 p-4 mb-5">
    <div class="mb-3">
        <h5 class="fw-bold text-dark mb-1" style="font-size: 1.15rem;">Statistik Pelatihan</h5>
        <div class="text-muted small">Ringkasan aktivitas setiap pelatihan</div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="border-collapse: separate; border-spacing: 0;">
            <thead style="background-color: #F8FAFC;">
                <tr class="text-dark fw-bold" style="font-size: 0.9rem;">
                    <th class="ps-3 py-3" style="border-top-left-radius: 8px; border-bottom: 1px solid #E2E8F0;">Pelatihan</th>
                    <th class="text-center py-3" style="border-bottom: 1px solid #E2E8F0;">Peserta</th>
                    <th class="text-center py-3" style="border-bottom: 1px solid #E2E8F0;">Presensi</th>
                    <th class="text-center py-3" style="border-bottom: 1px solid #E2E8F0;">Pretest</th>
                    <th class="text-center py-3" style="border-bottom: 1px solid #E2E8F0;">Posttest</th>
                    <th class="text-center py-3" style="border-bottom: 1px solid #E2E8F0;">Kuis</th>
                    <th class="text-center py-3" style="border-bottom: 1px solid #E2E8F0;">Tugas</th>
                    <th class="text-center pe-3 py-3" style="border-top-right-radius: 8px; border-bottom: 1px solid #E2E8F0;">Evaluasi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pelatihanStats as $ps)
                    <tr style="border-bottom: 1px solid #F1F5F9;">
                        <td class="ps-3 py-3 text-dark fw-semibold" style="font-size: 0.925rem;">
                            {{ $ps['nama'] }}
                        </td>
                        <td class="text-center py-3 text-secondary" style="font-size: 0.925rem;">
                            {{ $ps['peserta'] }}
                        </td>
                        <td class="text-center py-3">
                            <span class="badge rounded-pill fw-medium" style="background-color: #E6FFFA; color: #0D9488; font-size: 0.85rem; padding: 0.35rem 0.75rem;">
                                {{ number_format($ps['presensi']) }}
                            </span>
                        </td>
                        <td class="text-center py-3 text-secondary" style="font-size: 0.925rem;">
                            {{ $ps['pretest'] }}
                        </td>
                        <td class="text-center py-3 text-secondary" style="font-size: 0.925rem;">
                            {{ $ps['posttest'] }}
                        </td>
                        <td class="text-center py-3 text-secondary" style="font-size: 0.925rem;">
                            {{ $ps['kuis'] }}
                        </td>
                        <td class="text-center py-3 text-secondary" style="font-size: 0.925rem;">
                            {{ $ps['tugas'] }}
                        </td>
                        <td class="text-center pe-3 py-3 text-secondary" style="font-size: 0.925rem;">
                            {{ $ps['evaluasi'] }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- ============================================================== -->
<!-- SISTEM INFORMASI REKAPITULASI JP (SIR-JP)                     -->
<!-- ============================================================== -->
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-3 pt-4 border-top">
    <div>
        <h5 class="fw-bold text-dark mb-0"><i class="bi bi-calculator text-primary me-2"></i>Rekapitulasi Jam Pelajaran Widyaiswara &amp; Narasumber</h5>
        <span class="text-muted small">Perhitungan beban kerja mengajar dan pencapaian target minimum 32 JP bulanan</span>
    </div>
    <form method="GET" action="{{ route('dashboard') }}" class="d-flex align-items-center gap-2 bg-white p-2 rounded border shadow-sm">
        <div class="d-flex align-items-center gap-1">
            <i class="bi bi-calendar-event text-primary"></i>
            <span class="small fw-semibold text-muted">Periode:</span>
        </div>
        <select name="bulan" class="form-select form-select-sm" style="width: 130px;">
            @for($m = 1; $m <= 12; $m++)
                <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                    {{ (new \App\Services\JPRecapitulationService)->getNamaBulan($m) }}
                </option>
            @endfor
        </select>
        <select name="tahun" class="form-select form-select-sm" style="width: 90px;">
            @for($y = date('Y') - 2; $y <= date('Y') + 1; $y++)
                <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endfor
        </select>
        <button type="submit" class="btn btn-sm btn-navy px-3">
            <i class="bi bi-filter"></i> Filter
        </button>
    </form>
</div>

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
            <div class="stat-value counter-animate" data-target="{{ $stats['total_widyaiswara'] }}">{{ $stats['total_widyaiswara'] }}</div>
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
            <div class="stat-value counter-animate" data-target="{{ $stats['total_narasumber'] }}">{{ $stats['total_narasumber'] }}</div>
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
            <div class="stat-value counter-animate" data-target="{{ $stats['total_pelatihan'] }}">{{ $stats['total_pelatihan'] }}</div>
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
            <div class="stat-value counter-animate" data-target="{{ $stats['total_angkatan'] }}">{{ $stats['total_angkatan'] }}</div>
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
            <div class="stat-value"><span class="counter-animate" data-target="{{ $stats['jadwal_terlaksana_count'] }}">{{ $stats['jadwal_terlaksana_count'] }}</span> <span class="fs-6 fw-normal text-muted">/ {{ $stats['jadwal_bulan_ini_count'] }}</span></div>
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
            <div class="stat-value text-primary"><span class="counter-animate" data-target="{{ $stats['total_jp_terealisasi'] }}">{{ $stats['total_jp_terealisasi'] }}</span> <span class="fs-6 fw-normal">JP</span></div>
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
