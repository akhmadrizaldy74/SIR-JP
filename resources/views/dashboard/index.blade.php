@extends('layouts.app')

@section('title', 'Dashboard KCLC & SIR-JP')
@section('page_title', 'HOME')

@section('breadcrumb_custom')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">home</a></li>
    <li class="breadcrumb-item active" aria-current="page">Home</li>
@endsection

@section('content')
@php
    $currentActiveRole = auth()->check() ? auth()->user()->getActiveRole() : 'Penyelenggara';
    $isAdministrator = strcasecmp($currentActiveRole, 'Administrator') === 0;
    $isPengajar = strcasecmp($currentActiveRole, 'Pengajar') === 0;
    $isPenyelenggara = strcasecmp($currentActiveRole, 'Penyelenggara') === 0;
@endphp

@if($isPengajar)
    <!-- ============================================================== -->
    <!-- TAMPILAN DASHBOARD KHUSUS ROLE PENGAJAR (SESUAI GAMBAR KCLC)    -->
    <!-- ============================================================== -->
    <!-- Row: 2 Banners (Dashboard KCLC & Helpdesk) -->
    <div class="row g-3 mb-4">
        <div class="col-lg-7">
            <div class="card bg-white border-0 shadow-sm rounded-4 p-4 h-100 position-relative overflow-hidden">
                <div class="row align-items-center h-100">
                    <div class="col-sm-7">
                        <h4 class="fw-bold text-dark mb-1" style="font-size: 1.35rem; letter-spacing: -0.3px;">Dashboard KCLC Learning Center</h4>
                        <p class="text-muted mb-0" style="font-size: 0.88rem;">
                            Platform Pembelajaran Digital Kementerian Ketenagakerjaan
                        </p>
                    </div>
                    <div class="col-sm-5 text-end d-none d-sm-block">
                        <svg width="200" height="95" viewBox="0 0 240 110" fill="none" xmlns="http://www.w3.org/2000/svg" style="max-height: 95px;">
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
        </div>
        <div class="col-lg-5">
            <div class="card bg-white border-0 shadow-sm rounded-4 p-4 h-100 position-relative overflow-hidden">
                <div class="row align-items-center h-100">
                    <div class="col-sm-8">
                        <h5 class="fw-bold text-dark mb-1" style="font-size: 1.15rem;">Helpdesk</h5>
                        <p class="text-muted mb-0" style="font-size: 0.8rem; line-height: 1.45;">
                            Apabila permohonan Anda belum diproses atau menemukan kendala pada aplikasi, mohon segera menghubungi tim Helpdesk melalui email: <a href="mailto:helpdesk.ppsdm@kemnaker.go.id" class="text-primary text-decoration-none fw-semibold">Kirim Email ke Helpdesk</a>
                        </p>
                    </div>
                    <div class="col-sm-4 text-end d-none d-sm-block">
                        <svg width="120" height="90" viewBox="0 0 120 90" fill="none" xmlns="http://www.w3.org/2000/svg" style="max-height: 90px;">
                            <path d="M48 48 C 65 20, 80 35, 95 18" stroke="#1E293B" stroke-width="1.6" stroke-dasharray="3 3" fill="none"/>
                            <g transform="translate(82, 4) rotate(15)">
                                <polygon points="0,16 26,0 14,24" fill="#FFFFFF" stroke="#1E293B" stroke-width="2" stroke-linejoin="round"/>
                                <line x1="14" y1="24" x2="26" y2="0" stroke="#1E293B" stroke-width="2"/>
                                <polygon points="14,24 8,28 10,21" fill="#E2E8F0" stroke="#1E293B" stroke-width="1.5"/>
                            </g>
                            <rect x="10" y="32" width="68" height="46" rx="4" stroke="#1E293B" stroke-width="2.5" fill="#FFFFFF"/>
                            <path d="M11 34 L43 56 C43.8 56.5 44.8 56.5 45.6 56 L77 34" stroke="#1E293B" stroke-width="2.5" stroke-linecap="round"/>
                            <line x1="12" y1="76" x2="35" y2="52" stroke="#E2E8F0" stroke-width="2" stroke-linecap="round"/>
                            <line x1="76" y1="76" x2="53" y2="52" stroke="#E2E8F0" stroke-width="2" stroke-linecap="round"/>
                            <line x1="22" y1="46" x2="38" y2="46" stroke="#1E293B" stroke-width="2" stroke-linecap="round"/>
                            <line x1="22" y1="54" x2="34" y2="54" stroke="#1E293B" stroke-width="2" stroke-linecap="round"/>
                            <line x1="22" y1="62" x2="48" y2="62" stroke="#1E293B" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section: Learning Management System Permohonan (6 Cards) -->
    <div class="mb-3">
        <h5 class="fw-bold text-dark mb-1" style="font-size: 1.05rem; text-decoration: underline; text-underline-offset: 4px; text-decoration-color: #0284C7;">Learning Management System Permohonan</h5>
    </div>
    <div class="row row-cols-2 row-cols-md-3 row-cols-xl-6 g-3 mb-4">
        <!-- Total LMS -->
        <div class="col">
            <div class="card p-3 border shadow-sm rounded-3 bg-white h-100">
                <div class="text-muted small fw-medium mb-2">Total LMS</div>
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="fw-bold text-dark mb-0 lh-1">0</h3>
                        <div class="text-danger small mt-1" style="font-size: 0.75rem;"><i class="bi bi-arrow-down-short"></i></div>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background-color: #E0F2FE; color: #0284C7;">
                        <i class="bi bi-database"></i>
                    </div>
                </div>
            </div>
        </div>
        <!-- Status Proses -->
        <div class="col">
            <div class="card p-3 border shadow-sm rounded-3 bg-white h-100">
                <div class="text-muted small fw-medium mb-2">Status Proses</div>
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="fw-bold text-dark mb-0 lh-1">0</h3>
                        <div class="text-danger small mt-1" style="font-size: 0.75rem;"><i class="bi bi-arrow-down-short"></i></div>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background-color: #E0F2FE; color: #0284C7;">
                        <i class="bi bi-arrow-repeat"></i>
                    </div>
                </div>
            </div>
        </div>
        <!-- Status Disetujui -->
        <div class="col">
            <div class="card p-3 border shadow-sm rounded-3 bg-white h-100">
                <div class="text-muted small fw-medium mb-2">Status Disetujui</div>
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="fw-bold text-dark mb-0 lh-1">0</h3>
                        <div class="text-info small mt-1" style="font-size: 0.75rem;"><i class="bi bi-arrow-up-short"></i></div>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background-color: #E0F2FE; color: #0284C7;">
                        <i class="bi bi-check2-square"></i>
                    </div>
                </div>
            </div>
        </div>
        <!-- Status Ditolak -->
        <div class="col">
            <div class="card p-3 border shadow-sm rounded-3 bg-white h-100">
                <div class="text-muted small fw-medium mb-2">Status Ditolak</div>
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="fw-bold text-dark mb-0 lh-1">0</h3>
                        <div class="text-info small mt-1" style="font-size: 0.75rem;"><i class="bi bi-arrow-up-short"></i></div>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background-color: #E0F2FE; color: #0284C7;">
                        <i class="bi bi-x-circle"></i>
                    </div>
                </div>
            </div>
        </div>
        <!-- Status Revisi -->
        <div class="col">
            <div class="card p-3 border shadow-sm rounded-3 bg-white h-100">
                <div class="text-muted small fw-medium mb-2">Status Revisi</div>
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="fw-bold text-dark mb-0 lh-1">0</h3>
                        <div class="text-info small mt-1" style="font-size: 0.75rem;"><i class="bi bi-arrow-up-short"></i></div>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background-color: #E0F2FE; color: #0284C7;">
                        <i class="bi bi-pencil-square"></i>
                    </div>
                </div>
            </div>
        </div>
        <!-- Status Perbaikan -->
        <div class="col">
            <div class="card p-3 border shadow-sm rounded-3 bg-white h-100">
                <div class="text-muted small fw-medium mb-2">Status Perbaikan</div>
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="fw-bold text-dark mb-0 lh-1">0</h3>
                        <div class="text-info small mt-1" style="font-size: 0.75rem;"><i class="bi bi-arrow-up-short"></i></div>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background-color: #E0F2FE; color: #0284C7;">
                        <i class="bi bi-wrench"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section: Learning Management System (4 Cards) -->
    <div class="mb-3">
        <h5 class="fw-bold text-dark mb-1" style="font-size: 1.05rem;">Learning Management System</h5>
    </div>
    <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-4 g-3 mb-4">
        <!-- Pelatihan -->
        <div class="col">
            <div class="card p-3 border shadow-sm rounded-3 bg-white h-100">
                <div class="text-muted small fw-medium mb-2">Pelatihan</div>
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="fw-bold text-dark mb-0 lh-1">{{ $kclcStats['total_pelatihan'] ?? 0 }}</h3>
                        <div class="text-info small mt-1" style="font-size: 0.75rem;"><i class="bi bi-arrow-up-short"></i></div>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background-color: #E0F2FE; color: #0284C7;">
                        <i class="bi bi-database"></i>
                    </div>
                </div>
            </div>
        </div>
        <!-- Materi -->
        <div class="col">
            <div class="card p-3 border shadow-sm rounded-3 bg-white h-100">
                <div class="text-muted small fw-medium mb-2">Materi</div>
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="fw-bold text-dark mb-0 lh-1">0</h3>
                        <div class="text-info small mt-1" style="font-size: 0.75rem;"><i class="bi bi-arrow-up-short"></i></div>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background-color: #E0F2FE; color: #0284C7;">
                        <i class="bi bi-activity"></i>
                    </div>
                </div>
            </div>
        </div>
        <!-- Media Pembelajaran -->
        <div class="col">
            <div class="card p-3 border shadow-sm rounded-3 bg-white h-100">
                <div class="text-muted small fw-medium mb-2">Media Pembelajaran</div>
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="fw-bold text-dark mb-0 lh-1">0</h3>
                        <div class="text-info small mt-1" style="font-size: 0.75rem;"><i class="bi bi-arrow-up-short"></i></div>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background-color: #E0F2FE; color: #0284C7;">
                        <i class="bi bi-box-arrow-up-right"></i>
                    </div>
                </div>
            </div>
        </div>
        <!-- Bank Soal -->
        <div class="col">
            <div class="card p-3 border shadow-sm rounded-3 bg-white h-100">
                <div class="text-muted small fw-medium mb-2">Bank Soal</div>
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="fw-bold text-dark mb-0 lh-1">0</h3>
                        <div class="text-info small mt-1" style="font-size: 0.75rem;"><i class="bi bi-arrow-up-short"></i></div>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background-color: #E0F2FE; color: #0284C7;">
                        <i class="bi bi-box-arrow-up-right"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section: 2 Charts Sesuai Gambar KCLC Pengajar -->
    <div class="row g-4 mb-4">
        <!-- Chart 1: Grafik Jumlah Peserta LMS Berdasarkan Pelatihan -->
        <div class="col-lg-6">
            <div class="card border shadow-sm rounded-4 bg-white h-100">
                <div class="card-header bg-white border-0 pt-3 px-4 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold text-dark mb-0" style="font-size: 0.95rem;">Grafik Jumlah Peserta LMS Berdasarkan Pelatihan</h6>
                    <button type="button" class="btn btn-sm btn-link text-secondary p-0" title="Menu Grafik">
                        <i class="bi bi-list fs-5"></i>
                    </button>
                </div>
                <div class="card-body p-4 pt-2">
                    <canvas id="chartJumlahPesertaLms" height="230"></canvas>
                </div>
            </div>
        </div>

        <!-- Chart 2: Grafik Prosentase Peserta LMS Berdasarkan Pelatihan -->
        <div class="col-lg-6">
            <div class="card border shadow-sm rounded-4 bg-white h-100">
                <div class="card-header bg-white border-0 pt-3 px-4 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold text-dark mb-0" style="font-size: 0.95rem;">Grafik Prosentase Peserta LMS Berdasarkan Pelatihan</h6>
                    <button type="button" class="btn btn-sm btn-link text-secondary p-0" title="Menu Grafik">
                        <i class="bi bi-list fs-5"></i>
                    </button>
                </div>
                <div class="card-body p-4 pt-2">
                    <canvas id="chartProsentasePesertaLms" height="230"></canvas>
                </div>
            </div>
        </div>
    </div>

@else
@if($isAdministrator)
    <!-- ============================================================== -->
    <!-- DASHBOARD ADMINISTRATOR (KMS, LMS & 4 CHARTS SESUAI GAMBAR 1-2)-->
    <!-- ============================================================== -->
    <!-- Banner KCLC Learning Center Sesuai Gambar 1 -->
    <div class="card bg-white border-0 shadow-sm rounded-4 mb-4 p-4 position-relative overflow-hidden">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h4 class="fw-bold text-dark mb-1" style="font-size: 1.45rem; letter-spacing: -0.3px;">Dashboard KCLC Learning Center</h4>
                <p class="text-muted mb-0" style="font-size: 0.95rem;">
                    Platform Pembelajaran Digital Kementerian Ketenagakerjaan
                </p>
            </div>
            <div class="col-md-4 text-end d-none d-md-block position-relative">
                <!-- Ilustrasi Workspace Meja & Laptop Presisi Gambar KCLC -->
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

    <!-- Section 1: Knowledge Management System Sesuai Gambar 1 -->
    <div class="mb-3">
        <h5 class="fw-bold text-dark mb-1" style="font-size: 1.05rem; text-decoration: underline; text-underline-offset: 4px; text-decoration-color: #0284C7;">Knowledge Management System</h5>
    </div>
    <div class="row row-cols-2 row-cols-md-3 row-cols-xl-6 g-3 mb-4">
        <!-- Total KMS -->
        <div class="col">
            <div class="card p-3 border shadow-sm rounded-3 bg-white h-100">
                <div class="text-muted small fw-medium mb-2">Total KMS</div>
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="fw-bold text-dark mb-0 lh-1">141</h3>
                        <div class="text-info small mt-1" style="font-size: 0.75rem;"><i class="bi bi-arrow-up-short"></i></div>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background-color: #E0F2FE; color: #0284C7;">
                        <i class="bi bi-people-fill"></i>
                    </div>
                </div>
            </div>
        </div>
        <!-- Status Proses -->
        <div class="col">
            <div class="card p-3 border shadow-sm rounded-3 bg-white h-100">
                <div class="text-muted small fw-medium mb-2">Status Proses</div>
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="fw-bold text-dark mb-0 lh-1">10</h3>
                        <div class="text-danger small mt-1" style="font-size: 0.75rem;"><i class="bi bi-arrow-down-short"></i></div>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background-color: #E0F2FE; color: #0284C7;">
                        <i class="bi bi-activity"></i>
                    </div>
                </div>
            </div>
        </div>
        <!-- Status Disetujui -->
        <div class="col">
            <div class="card p-3 border shadow-sm rounded-3 bg-white h-100">
                <div class="text-muted small fw-medium mb-2">Status Disetujui</div>
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="fw-bold text-dark mb-0 lh-1">90</h3>
                        <div class="text-info small mt-1" style="font-size: 0.75rem;"><i class="bi bi-arrow-up-short"></i></div>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background-color: #E0F2FE; color: #0284C7;">
                        <i class="bi bi-box-arrow-up-right"></i>
                    </div>
                </div>
            </div>
        </div>
        <!-- Status Ditolak -->
        <div class="col">
            <div class="card p-3 border shadow-sm rounded-3 bg-white h-100">
                <div class="text-muted small fw-medium mb-2">Status Ditolak</div>
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="fw-bold text-dark mb-0 lh-1">25</h3>
                        <div class="text-info small mt-1" style="font-size: 0.75rem;"><i class="bi bi-arrow-up-short"></i></div>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background-color: #E0F2FE; color: #0284C7;">
                        <i class="bi bi-box-arrow-up-right"></i>
                    </div>
                </div>
            </div>
        </div>
        <!-- Status Revisi -->
        <div class="col">
            <div class="card p-3 border shadow-sm rounded-3 bg-white h-100">
                <div class="text-muted small fw-medium mb-2">Status Revisi</div>
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="fw-bold text-dark mb-0 lh-1">15</h3>
                        <div class="text-info small mt-1" style="font-size: 0.75rem;"><i class="bi bi-arrow-up-short"></i></div>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background-color: #E0F2FE; color: #0284C7;">
                        <i class="bi bi-box-arrow-up-right"></i>
                    </div>
                </div>
            </div>
        </div>
        <!-- Status Perbaikan -->
        <div class="col">
            <div class="card p-3 border shadow-sm rounded-3 bg-white h-100">
                <div class="text-muted small fw-medium mb-2">Status Perbaikan</div>
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="fw-bold text-dark mb-0 lh-1">1</h3>
                        <div class="text-info small mt-1" style="font-size: 0.75rem;"><i class="bi bi-arrow-up-short"></i></div>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background-color: #E0F2FE; color: #0284C7;">
                        <i class="bi bi-box-arrow-up-right"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section 2: Learning Management System Sesuai Gambar 1 -->
    <div class="mb-3">
        <h5 class="fw-bold text-dark mb-1" style="font-size: 1.05rem; text-decoration: underline; text-underline-offset: 4px; text-decoration-color: #0284C7;">Learning Management System</h5>
    </div>
    <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-4 g-3 mb-4">
        <!-- TOTAL INSTRUKTUR -->
        <div class="col">
            <div class="card p-3 border shadow-sm rounded-3 bg-white h-100 position-relative overflow-hidden">
                <div class="text-muted small fw-semibold text-uppercase mb-2">TOTAL INSTRUKTUR</div>
                <h3 class="fw-bold text-dark mb-0 lh-1">5</h3>
                <div class="position-absolute end-0 bottom-0 top-0 d-flex align-items-center pe-3 opacity-25">
                    <i class="bi bi-person-video3 fs-1 text-primary"></i>
                </div>
            </div>
        </div>
        <!-- TOTAL MODUL -->
        <div class="col">
            <div class="card p-3 border shadow-sm rounded-3 bg-white h-100 position-relative overflow-hidden">
                <div class="text-muted small fw-semibold text-uppercase mb-2">TOTAL MODUL</div>
                <h3 class="fw-bold text-dark mb-0 lh-1">10</h3>
                <div class="position-absolute end-0 bottom-0 top-0 d-flex align-items-center pe-3 opacity-25">
                    <i class="bi bi-journal-bookmark-fill fs-1 text-primary"></i>
                </div>
            </div>
        </div>
        <!-- TOTAL MATERI -->
        <div class="col">
            <div class="card p-3 border shadow-sm rounded-3 bg-white h-100 position-relative overflow-hidden">
                <div class="text-muted small fw-semibold text-uppercase mb-2">TOTAL MATERI</div>
                <h3 class="fw-bold text-dark mb-0 lh-1">188</h3>
                <div class="position-absolute end-0 bottom-0 top-0 d-flex align-items-center pe-3 opacity-25">
                    <i class="bi bi-file-earmark-text-fill fs-1 text-primary"></i>
                </div>
            </div>
        </div>
        <!-- TOTAL MEDIA PEMBELAJARAN -->
        <div class="col">
            <div class="card p-3 border shadow-sm rounded-3 bg-white h-100 position-relative overflow-hidden">
                <div class="text-muted small fw-semibold text-uppercase mb-2">TOTAL MEDIA PEMBELAJARAN</div>
                <h3 class="fw-bold text-dark mb-0 lh-1">404</h3>
                <div class="position-absolute end-0 bottom-0 top-0 d-flex align-items-center pe-3 opacity-25">
                    <i class="bi bi-collection-play-fill fs-1 text-primary"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Section 3: 4 Grafik Analitik KMS & LMS Sesuai Gambar 2 (2x2 Grid) -->
    <div class="row g-4 mb-4">
        <!-- Chart 1: Grafik Jumlah View KMS Berdasarkan Pelatihan -->
        <div class="col-lg-6">
            <div class="card border shadow-sm rounded-4 bg-white h-100">
                <div class="card-header bg-white border-0 pt-3 px-4 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold text-dark mb-0" style="font-size: 0.95rem;">Grafik Jumlah View KMS Berdasarkan Pelatihan</h6>
                    <button type="button" class="btn btn-sm btn-link text-secondary p-0" title="Menu Grafik">
                        <i class="bi bi-list fs-5"></i>
                    </button>
                </div>
                <div class="card-body p-4 pt-2">
                    <canvas id="chartJumlahViewKms" height="260"></canvas>
                </div>
            </div>
        </div>

        <!-- Chart 2: Grafik Prosentase View KMS Berdasarkan Pelatihan -->
        <div class="col-lg-6">
            <div class="card border shadow-sm rounded-4 bg-white h-100">
                <div class="card-header bg-white border-0 pt-3 px-4 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold text-dark mb-0" style="font-size: 0.95rem;">Grafik Prosentase View KMS Berdasarkan Pelatihan</h6>
                    <button type="button" class="btn btn-sm btn-link text-secondary p-0" title="Menu Grafik">
                        <i class="bi bi-list fs-5"></i>
                    </button>
                </div>
                <div class="card-body p-4 pt-2">
                    <canvas id="chartProsentaseViewKms" height="260"></canvas>
                </div>
            </div>
        </div>

        <!-- Chart 3: Grafik Prosentase Peserta LMS Berdasarkan Pelatihan -->
        <div class="col-lg-6">
            <div class="card border shadow-sm rounded-4 bg-white h-100">
                <div class="card-header bg-white border-0 pt-3 px-4 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold text-dark mb-0" style="font-size: 0.95rem;">Grafik Prosentase Peserta LMS Berdasarkan Pelatihan</h6>
                    <button type="button" class="btn btn-sm btn-link text-secondary p-0" title="Menu Grafik">
                        <i class="bi bi-list fs-5"></i>
                    </button>
                </div>
                <div class="card-body p-4 pt-2">
                    <canvas id="chartProsentasePesertaLmsAdmin" height="260"></canvas>
                </div>
            </div>
        </div>

        <!-- Chart 4: Grafik Jumlah Peserta LMS Berdasarkan Pelatihan -->
        <div class="col-lg-6">
            <div class="card border shadow-sm rounded-4 bg-white h-100">
                <div class="card-header bg-white border-0 pt-3 px-4 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold text-dark mb-0" style="font-size: 0.95rem;">Grafik Jumlah Peserta LMS Berdasarkan Pelatihan</h6>
                    <button type="button" class="btn btn-sm btn-link text-secondary p-0" title="Menu Grafik">
                        <i class="bi bi-list fs-5"></i>
                    </button>
                </div>
                <div class="card-body p-4 pt-2">
                    <canvas id="chartJumlahPesertaLmsAdmin" height="260"></canvas>
                </div>
            </div>
        </div>
    </div>

@else
    <!-- Banner KCLC Learning Center untuk Penyelenggara Sesuai Gambar 3 -->
    <div class="card bg-white border-0 shadow-sm rounded-4 mb-4 p-4 position-relative overflow-hidden">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h4 class="fw-bold text-dark mb-1" style="font-size: 1.45rem; letter-spacing: -0.3px;">Dashboard KCLC Learning Center</h4>
                <p class="text-muted mb-0" style="font-size: 0.95rem;">
                    Platform Pembelajaran Digital PPSDM Kementerian Ketenagakerjaan
                </p>
            </div>
            <div class="col-md-4 text-end d-none d-md-block position-relative">
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
@endif

<!-- Section: Statistik KCLC Learning Center (10 Cards Sesuai Gambar 3) -->
<div class="mb-3">
    <h5 class="fw-bold text-dark mb-1" style="font-size: 1.15rem;">Statistik KCLC Learning Center</h5>
    <div class="text-muted small">Ringkasan data dan aktivitas sistem pembelajaran</div>
</div>

<!-- 10 Stat Cards: Fluid Responsive Grid (Tanpa Celah / Bolong) -->
<div class="row row-cols-1 row-cols-md-2 row-cols-xl-5 g-3 mb-4">
    <!-- Card 1 -->
    <div class="col">
        <div class="card p-3 border shadow-sm rounded-3 bg-white h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-2 flex-shrink-0" style="width: 44px; height: 44px; background-color: #EEF2FF; color: #4F46E5;">
                        <i class="bi bi-easel fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-medium text-truncate">Pelatihan Diklat</div>
                        <h3 class="fw-bold text-dark mb-0 lh-1 counter-animate" data-target="{{ $kclcStats['total_pelatihan'] }}">{{ $kclcStats['total_pelatihan'] }}</h3>
                    </div>
                </div>
                <span class="badge rounded-pill fw-medium" style="background-color: #EEF2FF; color: #4F46E5; font-size: 0.72rem; padding: 0.35rem 0.6rem;">Pelatihan</span>
            </div>
        </div>
    </div>

    <!-- Card 2 -->
    <div class="col">
        <div class="card p-3 border shadow-sm rounded-3 bg-white h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-2 flex-shrink-0" style="width: 44px; height: 44px; background-color: #E0F2FE; color: #0284C7;">
                        <i class="bi bi-journal-text fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-medium text-truncate">Kelas</div>
                        <h3 class="fw-bold text-dark mb-0 lh-1 counter-animate" data-target="{{ $kclcStats['total_kelas'] }}">{{ $kclcStats['total_kelas'] }}</h3>
                    </div>
                </div>
                <span class="badge rounded-pill fw-medium" style="background-color: #E0F2FE; color: #0284C7; font-size: 0.72rem; padding: 0.35rem 0.6rem;">Kelas</span>
            </div>
        </div>
    </div>

    <!-- Card 3 -->
    <div class="col">
        <div class="card p-3 border shadow-sm rounded-3 bg-white h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-2 flex-shrink-0" style="width: 44px; height: 44px; background-color: #ECFDF5; color: #059669;">
                        <i class="bi bi-door-open fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-medium text-truncate">Ruangan</div>
                        <h3 class="fw-bold text-dark mb-0 lh-1 counter-animate" data-target="{{ $kclcStats['total_ruangan'] }}">{{ $kclcStats['total_ruangan'] }}</h3>
                    </div>
                </div>
                <span class="badge rounded-pill fw-medium" style="background-color: #D1FAE5; color: #059669; font-size: 0.72rem; padding: 0.35rem 0.6rem;">Ruangan</span>
            </div>
        </div>
    </div>

    <!-- Card 4 -->
    <div class="col">
        <div class="card p-3 border shadow-sm rounded-3 bg-white h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-2 flex-shrink-0" style="width: 44px; height: 44px; background-color: #FEF3C7; color: #D97706;">
                        <i class="bi bi-card-checklist fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-medium text-truncate">Penilaian Sikap</div>
                        <h3 class="fw-bold text-dark mb-0 lh-1 counter-animate" data-target="{{ $kclcStats['total_sikap'] }}">{{ $kclcStats['total_sikap'] }}</h3>
                    </div>
                </div>
                <span class="badge rounded-pill fw-medium" style="background-color: #FEF3C7; color: #D97706; font-size: 0.72rem; padding: 0.35rem 0.6rem;">Penilaian</span>
            </div>
        </div>
    </div>

    <!-- Card 5 -->
    <div class="col">
        <div class="card p-3 border shadow-sm rounded-3 bg-white h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-2 flex-shrink-0" style="width: 44px; height: 44px; background-color: #FEE2E2; color: #DC2626;">
                        <i class="bi bi-file-earmark-check fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-medium text-truncate">Form Evaluasi</div>
                        <h3 class="fw-bold text-dark mb-0 lh-1 counter-animate" data-target="{{ $kclcStats['total_evaluasi'] }}">{{ $kclcStats['total_evaluasi'] }}</h3>
                    </div>
                </div>
                <span class="badge rounded-pill fw-medium" style="background-color: #FEE2E2; color: #DC2626; font-size: 0.72rem; padding: 0.35rem 0.6rem;">Evaluasi</span>
            </div>
        </div>
    </div>

    <!-- Card 6 -->
    <div class="col">
        <div class="card p-3 border shadow-sm rounded-3 bg-white h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-2 flex-shrink-0" style="width: 44px; height: 44px; background-color: #EEF2FF; color: #4F46E5;">
                        <i class="bi bi-people-fill fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-medium text-truncate">Angkatan</div>
                        <h3 class="fw-bold text-dark mb-0 lh-1 counter-animate" data-target="{{ $kclcStats['total_angkatan'] }}">{{ $kclcStats['total_angkatan'] }}</h3>
                    </div>
                </div>
                <span class="badge rounded-pill fw-medium" style="background-color: #EEF2FF; color: #4F46E5; font-size: 0.72rem; padding: 0.35rem 0.6rem;">Angkatan</span>
            </div>
        </div>
    </div>

    <!-- Card 7 -->
    <div class="col">
        <div class="card p-3 border shadow-sm rounded-3 bg-white h-100">
            <div class="d-flex align-items-center gap-3">
                <div class="d-flex align-items-center justify-content-center rounded-2 flex-shrink-0" style="width: 44px; height: 44px; background-color: #EEF2FF; color: #4F46E5;">
                    <i class="bi bi-easel fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small fw-medium text-truncate">Pelatihan Diklat</div>
                    <h3 class="fw-bold text-dark mb-0 lh-1 counter-animate" data-target="{{ $kclcStats['total_pelatihan'] }}">{{ $kclcStats['total_pelatihan'] }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 8 -->
    <div class="col">
        <div class="card p-3 border shadow-sm rounded-3 bg-white h-100">
            <div class="d-flex align-items-center gap-3">
                <div class="d-flex align-items-center justify-content-center rounded-2 flex-shrink-0" style="width: 44px; height: 44px; background-color: #E0F2FE; color: #0284C7;">
                    <i class="bi bi-person-fill fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small fw-medium text-truncate">Peserta</div>
                    <h3 class="fw-bold text-dark mb-0 lh-1 counter-animate" data-target="{{ $kclcStats['total_peserta'] }}">{{ number_format($kclcStats['total_peserta']) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 9 -->
    <div class="col">
        <div class="card p-3 border shadow-sm rounded-3 bg-white h-100">
            <div class="d-flex align-items-center gap-3">
                <div class="d-flex align-items-center justify-content-center rounded-2 flex-shrink-0" style="width: 44px; height: 44px; background-color: #ECFDF5; color: #059669;">
                    <i class="bi bi-check-circle-fill fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small fw-medium text-truncate">Presensi Hadir</div>
                    <h3 class="fw-bold text-dark mb-0 lh-1 counter-animate" data-target="{{ $kclcStats['total_presensi'] }}">{{ number_format($kclcStats['total_presensi']) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 10 -->
    <div class="col">
        <div class="card p-3 border shadow-sm rounded-3 bg-white h-100">
            <div class="d-flex align-items-center gap-3">
                <div class="d-flex align-items-center justify-content-center rounded-2 flex-shrink-0" style="width: 44px; height: 44px; background-color: #FEF3C7; color: #D97706;">
                    <i class="bi bi-clipboard2-check fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small fw-medium text-truncate">Evaluasi Selesai</div>
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

@if($isAdministrator)
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
@endif
@endif
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 0. Chart Pengajar (LMS MOOC)
    const ctxPesertaLms = document.getElementById('chartJumlahPesertaLms');
    if (ctxPesertaLms) {
        new Chart(ctxPesertaLms, {
            type: 'bar',
            data: {
                labels: ['Pengantar Kerja PPPK', 'Pengawas Naker', 'Mediator HI', 'Instruktur', 'K3 Listrik'],
                datasets: [{
                    label: 'Jumlah Peserta',
                    data: [143, 112, 104, 98, 76],
                    backgroundColor: '#0284C7',
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 50 }
                    }
                }
            }
        });
    }

    const ctxProsentaseLms = document.getElementById('chartProsentasePesertaLms');
    if (ctxProsentaseLms) {
        new Chart(ctxProsentaseLms, {
            type: 'doughnut',
            data: {
                labels: ['Pengantar Kerja', 'Pengawas Naker', 'Mediator HI', 'Instruktur', 'Lainnya'],
                datasets: [{
                    data: [35, 25, 20, 12, 8],
                    backgroundColor: ['#0284C7', '#10B981', '#F59E0B', '#6366F1', '#94A3B8'],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right'
                    }
                },
                cutout: '65%'
            }
        });
    }
    // 0.B Admin KMS & LMS Charts (Sesuai Gambar 2)
    const ctxViewKms = document.getElementById('chartJumlahViewKms');
    if (ctxViewKms) {
        new Chart(ctxViewKms, {
            type: 'bar',
            data: {
                labels: [
                    'Pelatihan Kepemimpinan Struktural',
                    'Pelatihan Dasar CPNS/PPPK',
                    'Pelatihan Vokasi & Produktivitas',
                    'Penempatan & Perluasan Naker',
                    'Pengawasan Ketenagakerjaan',
                    'Pengujian K3',
                    'Hubungan Industrial & Jamsos',
                    'Pelatihan SDM Aparatur',
                    'Pelatihan Teknis'
                ],
                datasets: [{
                    label: 'Total View KMS',
                    data: [445, 177, 3041, 2837, 5505, 2301, 5053, 7674, 1730],
                    backgroundColor: ['#2563EB', '#38BDF8', '#10B981', '#F97316', '#4F46E5', '#A855F7', '#06B6D4', '#EF4444', '#F59E0B'],
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value >= 1000 ? (value / 1000) + 'k' : value;
                            }
                        }
                    },
                    x: {
                        ticks: {
                            font: { size: 9 },
                            maxRotation: 45,
                            minRotation: 25
                        }
                    }
                }
            }
        });
    }

    const ctxPctViewKms = document.getElementById('chartProsentaseViewKms');
    if (ctxPctViewKms) {
        new Chart(ctxPctViewKms, {
            type: 'pie',
            data: {
                labels: [
                    'Pelatihan SDM Aparatur: 26.3 %',
                    'Pengawasan Ketenagakerjaan: 19.0 %',
                    'Hubungan Industrial & Jamsos: 17.5 %',
                    'Vokasi & Produktivitas: 10.5 %',
                    'Penempatan & Perluasan Naker: 10.2 %',
                    'Pengujian K3: 8.0 %',
                    'Pelatihan Teknis: 6.0 %',
                    'Kepemimpinan Struktural: 1.5 %',
                    'Dasar CPNS/PPPK: 0.6 %'
                ],
                datasets: [{
                    data: [26.3, 19.0, 17.5, 10.5, 10.2, 8.0, 6.0, 1.5, 0.6],
                    backgroundColor: ['#EF4444', '#4F46E5', '#06B6D4', '#10B981', '#F97316', '#A855F7', '#F59E0B', '#2563EB', '#38BDF8'],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            font: { size: 9 },
                            boxWidth: 12
                        }
                    }
                }
            }
        });
    }

    const ctxPctPesertaLms = document.getElementById('chartProsentasePesertaLmsAdmin');
    if (ctxPctPesertaLms) {
        new Chart(ctxPctPesertaLms, {
            type: 'pie',
            data: {
                labels: [
                    'Hubungan Industrial & Jamsos: 33.1 %',
                    'Penempatan & Perluasan Naker: 26.5 %',
                    'Pengawasan Ketenagakerjaan: 21.1 %',
                    'Pengujian K3: 12.2 %',
                    'Vokasi & Produktivitas: 2.8 %',
                    'Instruktur: 2.5 %',
                    'SDM Aparatur: 1.4 %',
                    'Dasar CPNS/PPPK: 0.5 %'
                ],
                datasets: [{
                    data: [33.1, 26.5, 21.1, 12.2, 2.8, 2.5, 1.4, 0.5],
                    backgroundColor: ['#A855F7', '#10B981', '#F97316', '#2563EB', '#06B6D4', '#6366F1', '#EAB308', '#38BDF8'],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            font: { size: 9 },
                            boxWidth: 12
                        }
                    }
                }
            }
        });
    }

    const ctxJmlPesertaLms = document.getElementById('chartJumlahPesertaLmsAdmin');
    if (ctxJmlPesertaLms) {
        new Chart(ctxJmlPesertaLms, {
            type: 'bar',
            data: {
                labels: [
                    'Dasar CPNS/PPPK',
                    'Vokasi & Produktivitas',
                    'Penempatan & Perluasan Naker',
                    'Pengawasan Ketenagakerjaan',
                    'Pengujian K3',
                    'Hubungan Industrial & Jamsos',
                    'SDM Aparatur',
                    'Pelatihan Teknis'
                ],
                datasets: [{
                    label: 'Total Peserta',
                    data: [23, 101, 1020, 810, 468, 1274, 55, 95],
                    backgroundColor: ['#2563EB', '#4F46E5', '#10B981', '#F97316', '#0284C7', '#A855F7', '#06B6D4', '#EF4444'],
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 500 }
                    },
                    x: {
                        ticks: {
                            font: { size: 9 },
                            maxRotation: 45,
                            minRotation: 25
                        }
                    }
                }
            }
        });
    }

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
