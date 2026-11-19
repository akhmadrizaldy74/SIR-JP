@extends('layouts.app')

@section('title', 'Rekapitulasi Jam Pelajaran (JP)')
@section('page_title', 'Rekapitulasi Jam Pelajaran (JP)')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Rekapitulasi JP</li>
@endsection

@section('page_actions')
    <div class="d-flex gap-2">
        <a href="{{ route('rekapitulasi.tahunan', ['tahun' => $tahun]) }}" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-calendar-range me-1"></i> Rekap Tahunan
        </a>
        <a href="{{ route('laporan.index', ['bulan' => $bulan, 'tahun' => $tahun]) }}" class="btn btn-navy btn-sm">
            <i class="bi bi-file-earmark-arrow-down me-1"></i> Ekspor Laporan
        </a>
    </div>
@endsection

@section('content')
<!-- Filter Box -->
<div class="card-custom mb-4">
    <div class="p-3">
        <form method="GET" action="{{ route('rekapitulasi.index') }}" class="row g-2 align-items-center">
            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted mb-1">Bulan</label>
                <select name="bulan" class="form-select form-select-sm">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                            {{ (new \App\Services\JPRecapitulationService)->getNamaBulan($m) }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold text-muted mb-1">Tahun</label>
                <select name="tahun" class="form-select form-select-sm">
                    @for($y = date('Y') - 3; $y <= date('Y') + 1; $y++)
                        <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted mb-1">Jabatan Widyaiswara</label>
                <select name="jabatan" class="form-select form-select-sm">
                    <option value="">-- Semua Jabatan --</option>
                    @foreach($jabatanOptions as $jab)
                        <option value="{{ $jab }}" {{ $jabatan == $jab ? 'selected' : '' }}>{{ $jab }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted mb-1">Pencarian Nama / NIP</label>
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Ketik nama atau NIP..." value="{{ $search }}">
            </div>
            <div class="col-md-1 d-flex align-items-end pt-3">
                <button type="submit" class="btn btn-sm btn-primary w-100" title="Terapkan Filter">
                    <i class="bi bi-filter"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Nav Tabs: Widyaiswara vs Narasumber -->
<ul class="nav nav-pills mb-3" id="rekapTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link {{ $jenisPengajar == 'Widyaiswara' ? 'active' : '' }}" id="wi-tab" data-bs-toggle="tab" data-bs-target="#tab-wi" type="button" role="tab">
            <i class="bi bi-person-badge me-1"></i> Rekapitulasi Widyaiswara (Batas Minimum: {{ $minimumJp }} JP)
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link {{ $jenisPengajar == 'Narasumber' ? 'active' : '' }}" id="ns-tab" data-bs-toggle="tab" data-bs-target="#tab-ns" type="button" role="tab">
            <i class="bi bi-person-lines-fill me-1"></i> Rekapitulasi Narasumber (Non-Batas JP)
        </button>
    </li>
</ul>

<div class="tab-content" id="rekapTabContent">
    <!-- TAB WIDYAISWARA -->
    <div class="tab-pane fade {{ $jenisPengajar == 'Widyaiswara' ? 'show active' : '' }}" id="tab-wi" role="tabpanel">
        <div class="card-custom">
            <div class="card-custom-header">
                <h5 class="card-custom-title">
                    <i class="bi bi-table text-primary"></i> Rekapitulasi Jam Pelajaran Seluruh Widyaiswara &bull; Periode {{ $namaBulan }} {{ $tahun }}
                </h5>
                <span class="badge bg-light text-secondary border">Standar Minimum: {{ $minimumJp }} JP/Bulan</span>
            </div>
            <div class="table-responsive">
                <table class="table table-custom table-bordered align-middle mb-0">
                    <thead class="text-center align-middle">
                        <tr>
                            <th rowspan="2" style="width: 45px;">No.</th>
                            <th rowspan="2" class="text-start">Nama Widyaiswara & Jabatan</th>
                            <th colspan="3" class="bg-light-blue text-primary border-bottom-0">Perhitungan Jam Pelajaran (JP)</th>
                            <th rowspan="2" class="text-center">JP Dibayarkan</th>
                            <th rowspan="2" class="text-center">Batas Min</th>
                            <th rowspan="2" class="text-center">Kekurangan</th>
                            <th rowspan="2" class="text-center">Status Pencapaian</th>
                            <th rowspan="2" style="width: 110px;">Aksi</th>
                        </tr>
                        <tr>
                            <th class="text-center bg-light" style="width: 95px;">Tatap Muka (A)</th>
                            <th class="text-center bg-light" style="width: 95px;">Konversi (B)</th>
                            <th class="text-center bg-light text-primary" style="width: 105px;">Total (A+B)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rekapWidyaiswara as $idx => $r)
                            <tr>
                                <td class="text-center">{{ $idx + 1 }}</td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $r['nama_gelar'] }}</div>
                                    <div class="text-muted small">NIP. {{ $r['nip'] ?? '-' }} &bull; <span class="badge bg-light text-dark border">{{ $r['jabatan'] }}</span></div>
                                </td>
                                <td class="text-center fw-semibold text-secondary">{{ $r['total_a'] }} JP</td>
                                <td class="text-center fw-semibold text-secondary">{{ $r['total_b'] }} JP</td>
                                <td class="text-center fw-bold text-primary fs-6">{{ $r['total_ab'] }} JP</td>
                                <td class="text-center fw-semibold text-dark">{{ $r['total_dibayarkan'] }} JP</td>
                                <td class="text-center text-muted">{{ $r['minimum_jp'] }} JP</td>
                                <td class="text-center fw-semibold {{ $r['kekurangan_jp'] > 0 ? 'text-danger' : 'text-success' }}">
                                    {{ $r['kekurangan_jp'] > 0 ? $r['kekurangan_jp'] . ' JP' : '-' }}
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $r['badge_class'] }} badge-custom">
                                        {{ $r['status_pencapaian'] }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('rekapitulasi.detail', ['id' => $r['id'], 'bulan' => $bulan, 'tahun' => $tahun]) }}" class="btn btn-sm btn-outline-primary" title="Lihat Rekap Individual Format Excel">
                                        <i class="bi bi-file-earmark-spreadsheet me-1"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                    Tidak ada data Widyaiswara yang sesuai dengan filter.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB NARASUMBER -->
    <div class="tab-pane fade {{ $jenisPengajar == 'Narasumber' ? 'show active' : '' }}" id="tab-ns" role="tabpanel">
        <div class="card-custom">
            <div class="card-custom-header">
                <h5 class="card-custom-title">
                    <i class="bi bi-person-lines-fill text-primary"></i> Rekapitulasi Jam Mengajar Narasumber &bull; Periode {{ $namaBulan }} {{ $tahun }}
                </h5>
                <span class="badge bg-light text-secondary border">Non-Batas 32 JP</span>
            </div>
            <div class="table-responsive">
                <table class="table table-custom table-bordered align-middle mb-0">
                    <thead class="text-center align-middle">
                        <tr>
                            <th style="width: 45px;">No.</th>
                            <th class="text-start">Nama Narasumber</th>
                            <th>Instansi Asal</th>
                            <th class="text-center">Jumlah Sesi</th>
                            <th class="text-center">Total JP Terlaksana</th>
                            <th class="text-center">Total JP Dibayarkan</th>
                            <th class="text-center" style="width: 110px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rekapNarasumber as $idx => $r)
                            <tr>
                                <td class="text-center">{{ $idx + 1 }}</td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $r['nama_gelar'] }}</div>
                                    <div class="text-muted small">NIK: {{ $r['nik'] }}</div>
                                </td>
                                <td><span class="badge bg-light text-dark border">{{ $r['instansi'] }}</span></td>
                                <td class="text-center">{{ $r['jumlah_kegiatan'] }} Sesi</td>
                                <td class="text-center fw-bold text-primary fs-6">{{ $r['total_jp'] }} JP</td>
                                <td class="text-center fw-semibold text-dark">{{ $r['total_dibayarkan'] }} JP</td>
                                <td class="text-center">
                                    <a href="{{ route('rekapitulasi.detail_narasumber', ['id' => $r['id'], 'bulan' => $bulan, 'tahun' => $tahun]) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye me-1"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                    Tidak ada data aktivitas narasumber pada periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
