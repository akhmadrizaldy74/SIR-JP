@extends('layouts.app')

@section('title', 'Rekapitulasi Tahunan')
@section('page_title', 'Rekapitulasi Jam Pelajaran Tahunan')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('rekapitulasi.index') }}">Rekapitulasi JP</a></li>
    <li class="breadcrumb-item active" aria-current="page">Tahunan {{ $tahun }}</li>
@endsection

@section('page_actions')
    <div class="d-flex gap-2">
        <a href="{{ route('laporan.export.excel_tahunan', ['tahun' => $tahun]) }}" class="btn btn-success btn-sm">
            <i class="bi bi-file-earmark-excel me-1"></i> Unduh Excel Tahunan
        </a>
        <a href="{{ route('laporan.export.pdf_tahunan', ['tahun' => $tahun]) }}" target="_blank" class="btn btn-danger btn-sm">
            <i class="bi bi-file-earmark-pdf me-1"></i> Unduh PDF Tahunan
        </a>
        <a href="{{ route('rekapitulasi.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Bulanan
        </a>
    </div>
@endsection

@section('content')
<div class="card-custom mb-4">
    <div class="p-3">
        <form method="GET" action="{{ route('rekapitulasi.tahunan') }}" class="row g-2 align-items-center">
            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted mb-1">Pilih Tahun</label>
                <select name="tahun" class="form-select form-select-sm">
                    @for($y = date('Y') - 3; $y <= date('Y') + 1; $y++)
                        <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-semibold text-muted mb-1">Filter Jabatan</label>
                <select name="jabatan" class="form-select form-select-sm">
                    <option value="">-- Semua Jabatan --</option>
                    @foreach($jabatanOptions as $jab)
                        <option value="{{ $jab }}" {{ $jabatan == $jab ? 'selected' : '' }}>{{ $jab }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end pt-3">
                <button type="submit" class="btn btn-sm btn-primary w-100">
                    <i class="bi bi-filter me-1"></i> Terapkan
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card-custom">
    <div class="card-custom-header">
        <h5 class="card-custom-title">
            <i class="bi bi-calendar-range text-primary"></i> Matriks Capaian Jam Pelajaran (JP) 12 Bulan &bull; Tahun {{ $tahun }}
        </h5>
        <span class="badge bg-light text-primary border">12 Bulan Transaksi</span>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered table-sm small align-middle text-center mb-0" style="font-size: 0.8rem;">
            <thead class="table-light">
                <tr>
                    <th rowspan="2" class="text-start" style="min-width: 200px;">Nama Widyaiswara</th>
                    <th colspan="12">Bulan (Total JP A + B)</th>
                    <th colspan="3" class="bg-light-blue text-primary">Total Kumulatif {{ $tahun }}</th>
                </tr>
                <tr>
                    @for($m = 1; $m <= 12; $m++)
                        <th style="width: 50px;">{{ substr((new \App\Services\JPRecapitulationService)->getNamaBulan($m), 0, 3) }}</th>
                    @endfor
                    <th class="bg-light text-dark" style="width: 65px;">Total A</th>
                    <th class="bg-light text-dark" style="width: 65px;">Total B</th>
                    <th class="bg-light text-primary fw-bold" style="width: 75px;">Total (A+B)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rekapTahunan as $wi)
                    <tr>
                        <td class="text-start">
                            <div class="fw-bold text-dark">{{ $wi['nama_gelar'] }}</div>
                            <div class="text-muted" style="font-size: 0.7rem;">NIP. {{ $wi['nip'] ?? '-' }}</div>
                        </td>
                        @for($m = 1; $m <= 12; $m++)
                            @php
                                $val = $wi['bulanan'][$m]['total_ab'];
                                $badgeClass = $val == 0 ? 'text-muted' : ($val >= 32 ? 'fw-bold text-success' : 'fw-semibold text-danger');
                            @endphp
                            <td class="{{ $badgeClass }}">
                                {{ $val > 0 ? $val : '-' }}
                            </td>
                        @endfor
                        <td class="fw-semibold text-secondary">{{ $wi['total_a'] }}</td>
                        <td class="fw-semibold text-secondary">{{ $wi['total_b'] }}</td>
                        <td class="fw-bold text-primary fs-6">{{ $wi['total_ab'] }} JP</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="16" class="text-center py-4 text-muted">
                            Tidak ada data Widyaiswara ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
