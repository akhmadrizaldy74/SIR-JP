@extends('layouts.app')

@section('title', 'Pusat Ekspor Laporan')
@section('page_title', 'Pusat Ekspor Laporan')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Laporan</li>
@endsection

@section('content')
<div class="row g-4">
    <!-- 1. Laporan Individu Widyaiswara -->
    <div class="col-lg-6">
        <div class="card-custom h-100">
            <div class="card-custom-header">
                <h5 class="card-custom-title">
                    <i class="bi bi-person-badge text-primary"></i> 1. Laporan Rekapitulasi Individu Widyaiswara
                </h5>
                <span class="badge bg-light text-secondary border">Excel &amp; PDF</span>
            </div>
            <div class="p-4">
                <p class="text-muted small mb-3">
                    Format cetak rekapitulasi individu mengikuti struktur formulir Excel operasional: Identitas, Bagian A (Tatap Muka), Bagian B (Konversi), Ringkasan Perhitungan, dan Area Tanda Tangan Resmi.
                </p>
                <form id="formIndividu" method="GET" target="_blank">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Pilih Widyaiswara <span class="text-danger">*</span></label>
                        <select id="wi_id_select" class="form-select form-select-sm" required>
                            @foreach($widyaiswaras as $wi)
                                <option value="{{ $wi->id }}">{{ $wi->nama_gelar }} - {{ $wi->jabatan }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row g-2 mb-4">
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Bulan</label>
                            <select id="wi_bulan_select" class="form-select form-select-sm">
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                                        {{ (new \App\Services\JPRecapitulationService)->getNamaBulan($m) }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Tahun</label>
                            <select id="wi_tahun_select" class="form-select form-select-sm">
                                @for($y = date('Y') - 3; $y <= date('Y') + 1; $y++)
                                    <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-success btn-sm w-50" onclick="exportIndividu('excel')">
                            <i class="bi bi-file-earmark-excel me-1"></i> Unduh Excel (.xlsx)
                        </button>
                        <button type="button" class="btn btn-danger btn-sm w-50" onclick="exportIndividu('pdf')">
                            <i class="bi bi-file-earmark-pdf me-1"></i> Unduh PDF (.pdf)
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- 2. Rekapitulasi Seluruh Widyaiswara Bulanan -->
    <div class="col-lg-6">
        <div class="card-custom h-100">
            <div class="card-custom-header">
                <h5 class="card-custom-title">
                    <i class="bi bi-table text-primary"></i> 2. Rekapitulasi Seluruh Widyaiswara (Bulanan)
                </h5>
                <span class="badge bg-light text-secondary border">Excel &amp; PDF</span>
            </div>
            <div class="p-4">
                <p class="text-muted small mb-3">
                    Laporan kompilasi seluruh pengajar internal dalam satu tabel terpadu memuat Tatap Muka (A), Konversi (B), Total (A+B), Status Memenuhi/Belum, dan Kekurangan JP.
                </p>
                <form id="formSemua" method="GET" target="_blank">
                    <div class="row g-2 mb-4">
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Bulan Periode</label>
                            <select id="all_bulan_select" class="form-select form-select-sm">
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                                        {{ (new \App\Services\JPRecapitulationService)->getNamaBulan($m) }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Tahun Anggaran</label>
                            <select id="all_tahun_select" class="form-select form-select-sm">
                                @for($y = date('Y') - 3; $y <= date('Y') + 1; $y++)
                                    <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-4">
                        <button type="button" class="btn btn-success btn-sm w-50" onclick="exportSemua('excel')">
                            <i class="bi bi-file-earmark-excel me-1"></i> Unduh Excel (.xlsx)
                        </button>
                        <button type="button" class="btn btn-danger btn-sm w-50" onclick="exportSemua('pdf')">
                            <i class="bi bi-file-earmark-pdf me-1"></i> Unduh PDF (.pdf)
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- 3. Rekapitulasi Tahunan (12 Bulan) -->
    <div class="col-lg-4">
        <div class="card-custom h-100">
            <div class="card-custom-header">
                <h5 class="card-custom-title">
                    <i class="bi bi-calendar-range text-primary"></i> 3. Rekapitulasi Tahunan
                </h5>
            </div>
            <div class="p-4">
                <p class="text-muted small mb-3">Matriks kumulatif capaian Jam Pelajaran 12 bulan (Januari s.d. Desember).</p>
                <div class="mb-4">
                    <label class="form-label small fw-semibold">Tahun</label>
                    <select id="thn_tahun_select" class="form-select form-select-sm">
                        @for($y = date('Y') - 3; $y <= date('Y') + 1; $y++)
                            <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-success btn-sm w-50" onclick="exportTahunan('excel')">
                        <i class="bi bi-file-earmark-excel me-1"></i> Excel
                    </button>
                    <button type="button" class="btn btn-outline-danger btn-sm w-50" onclick="exportTahunan('pdf')">
                        <i class="bi bi-file-earmark-pdf me-1"></i> PDF
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. Laporan Aktivitas Narasumber -->
    <div class="col-lg-4">
        <div class="card-custom h-100">
            <div class="card-custom-header">
                <h5 class="card-custom-title">
                    <i class="bi bi-person-lines-fill text-primary"></i> 4. Aktivitas Narasumber
                </h5>
            </div>
            <div class="p-4">
                <p class="text-muted small mb-3">Laporan jam mengajar pakar/narasumber eksternal non-batas 32 JP.</p>
                <div class="mb-2">
                    <label class="form-label small fw-semibold">Pilih Narasumber</label>
                    <select id="ns_id_select" class="form-select form-select-sm">
                        @foreach($narasumbers as $ns)
                            <option value="{{ $ns->id }}">{{ $ns->nama_gelar }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <select id="ns_bulan_select" class="form-select form-select-sm">
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>{{ $m }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-6">
                        <select id="ns_tahun_select" class="form-select form-select-sm">
                            @for($y = date('Y') - 2; $y <= date('Y') + 1; $y++)
                                <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
                <button type="button" class="btn btn-danger btn-sm w-100" onclick="exportNarasumber()">
                    <i class="bi bi-file-earmark-pdf me-1"></i> Unduh PDF Aktivitas
                </button>
            </div>
        </div>
    </div>

    <!-- 5. Laporan Master Pelatihan -->
    <div class="col-lg-4">
        <div class="card-custom h-100">
            <div class="card-custom-header">
                <h5 class="card-custom-title">
                    <i class="bi bi-mortarboard text-primary"></i> 5. Data Program Pelatihan
                </h5>
            </div>
            <div class="p-4">
                <p class="text-muted small mb-4">Laporan daftar program pelatihan, kode pelatihan, jadwal pelaksanaan, dan status berjalan.</p>
                <a href="{{ route('laporan.export.excel_pelatihan') }}" class="btn btn-success btn-sm w-100 mt-4">
                    <i class="bi bi-file-earmark-excel me-1"></i> Unduh Master Pelatihan (.xlsx)
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function exportIndividu(type) {
    const wiId = document.getElementById('wi_id_select').value;
    const bulan = document.getElementById('wi_bulan_select').value;
    const tahun = document.getElementById('wi_tahun_select').value;

    let baseUrl = type === 'excel' 
        ? '{{ url("laporan/export/excel-individu") }}/' + wiId 
        : '{{ url("laporan/export/pdf-individu") }}/' + wiId;

    window.open(`${baseUrl}?bulan=${bulan}&tahun=${tahun}`, '_blank');
}

function exportSemua(type) {
    const bulan = document.getElementById('all_bulan_select').value;
    const tahun = document.getElementById('all_tahun_select').value;

    let baseUrl = type === 'excel'
        ? '{{ route("laporan.export.excel_semua") }}'
        : '{{ route("laporan.export.pdf_semua") }}';

    window.open(`${baseUrl}?bulan=${bulan}&tahun=${tahun}`, '_blank');
}

function exportTahunan(type) {
    const tahun = document.getElementById('thn_tahun_select').value;

    let baseUrl = type === 'excel'
        ? '{{ route("laporan.export.excel_tahunan") }}'
        : '{{ route("laporan.export.pdf_tahunan") }}';

    window.open(`${baseUrl}?tahun=${tahun}`, '_blank');
}

function exportNarasumber() {
    const nsId = document.getElementById('ns_id_select').value;
    const bulan = document.getElementById('ns_bulan_select').value;
    const tahun = document.getElementById('ns_tahun_select').value;

    window.open(`{{ url("laporan/export/pdf-narasumber") }}/${nsId}?bulan=${bulan}&tahun=${tahun}`, '_blank');
}
</script>
@endpush
