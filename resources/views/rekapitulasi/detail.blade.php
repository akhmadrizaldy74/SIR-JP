@extends('layouts.app')

@section('title', 'Rekapitulasi Individu - ' . $rekap['widyaiswara']->nama_gelar)
@section('page_title', 'Rekapitulasi Jam Pelajaran Individu')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('rekapitulasi.index', ['bulan' => $bulan, 'tahun' => $tahun]) }}">Rekapitulasi JP</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $rekap['widyaiswara']->nama_lengkap }}</li>
@endsection

@section('page_actions')
    <div class="d-flex gap-2">
        <a href="{{ route('laporan.export.excel_individu', ['id' => $rekap['widyaiswara']->id, 'bulan' => $bulan, 'tahun' => $tahun]) }}" class="btn btn-success btn-sm">
            <i class="bi bi-file-earmark-excel me-1"></i> Unduh Excel
        </a>
        <a href="{{ route('laporan.export.pdf_individu', ['id' => $rekap['widyaiswara']->id, 'bulan' => $bulan, 'tahun' => $tahun]) }}" target="_blank" class="btn btn-danger btn-sm">
            <i class="bi bi-file-earmark-pdf me-1"></i> Cetak / Unduh PDF
        </a>
        <a href="{{ route('rekapitulasi.index', ['bulan' => $bulan, 'tahun' => $tahun]) }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>
@endsection

@section('content')
<!-- Filter Ganti Periode atau Pengajar -->
<div class="card-custom mb-4 no-print">
    <div class="p-3">
        <form method="GET" action="{{ route('rekapitulasi.detail', ['id' => $rekap['widyaiswara']->id]) }}" class="row g-2 align-items-center">
            <div class="col-md-5">
                <label class="form-label small fw-semibold text-muted mb-1">Ganti Widyaiswara</label>
                <select class="form-select form-select-sm" onchange="window.location.href='{{ url('rekapitulasi/detail') }}/' + this.value + '?bulan={{ $bulan }}&tahun={{ $tahun }}'">
                    @foreach($allWidyaiswaras as $wi)
                        <option value="{{ $wi->id }}" {{ $rekap['widyaiswara']->id == $wi->id ? 'selected' : '' }}>
                            {{ $wi->nama_gelar }} - {{ $wi->jabatan }}
                        </option>
                    @endforeach
                </select>
            </div>
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
            <div class="col-md-2 d-flex align-items-end pt-3">
                <button type="submit" class="btn btn-sm btn-primary w-100">
                    <i class="bi bi-arrow-repeat me-1"></i> Tampilkan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- DOKUMEN REKAPITULASI RESMI (Format Standar Excel) -->
<div class="card-custom p-4 shadow-sm bg-white border">
    <!-- Header Dokumen Instansi -->
    <div class="text-center border-bottom pb-3 mb-4">
        <h5 class="fw-bold text-dark mb-1 text-uppercase">{{ \App\Models\Pengaturan::get('instansi_nama', 'Pusat Pengembangan Sumber Daya Manusia') }}</h5>
        <h6 class="fw-semibold text-primary mb-2">REKAPITULASI BEBAN JAM PELAJARAN (JP) WIDYAISWARA</h6>
        <div class="badge bg-light text-secondary border px-3 py-1">
            PERIODE BULAN: {{ strtoupper($rekap['nama_bulan']) }} {{ $tahun }}
        </div>
    </div>

    <!-- INFORMASI PENGAJAR -->
    <div class="row g-2 mb-4 p-3 bg-light rounded border">
        <div class="col-md-6">
            <table class="table table-sm table-borderless mb-0 small">
                <tr>
                    <td style="width: 130px;" class="text-muted">Nama Lengkap:</td>
                    <td class="fw-bold text-dark">{{ $rekap['widyaiswara']->nama_gelar }}</td>
                </tr>
                <tr>
                    <td class="text-muted">NIP:</td>
                    <td>{{ $rekap['widyaiswara']->nip ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="text-muted">NIK:</td>
                    <td>{{ $rekap['widyaiswara']->nik }}</td>
                </tr>
            </table>
        </div>
        <div class="col-md-6">
            <table class="table table-sm table-borderless mb-0 small">
                <tr>
                    <td style="width: 130px;" class="text-muted">Jabatan:</td>
                    <td class="fw-bold text-primary">{{ $rekap['widyaiswara']->jabatan }}</td>
                </tr>
                <tr>
                    <td class="text-muted">Instansi:</td>
                    <td>{{ $rekap['widyaiswara']->instansi }}</td>
                </tr>
                <tr>
                    <td class="text-muted">Ketentuan Beban:</td>
                    <td><span class="badge bg-primary">Minimum {{ $rekap['minimum_jp'] }} JP / Bulan</span></td>
                </tr>
            </table>
        </div>
    </div>

    <!-- BAGIAN A — TATAP MUKA -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="fw-bold text-dark mb-0">
                <i class="bi bi-person-video3 text-primary me-1"></i> BAGIAN A &mdash; KEGIATAN TATAP MUKA (PEMBELAJARAN)
            </h6>
            <span class="badge bg-light text-primary border">Total Tatap Muka (A): {{ $rekap['total_a'] }} JP</span>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-sm small align-middle mb-0">
                <thead class="table-light text-center">
                    <tr>
                        <th style="width: 40px;">No.</th>
                        <th style="width: 90px;">Tanggal</th>
                        <th>Program Pelatihan & Angkatan</th>
                        <th>Mata Pelatihan</th>
                        <th style="width: 70px;" class="text-center">JP</th>
                        <th style="width: 90px;" class="text-center">JP Akumulasi</th>
                        <th style="width: 95px;" class="text-center">JP Dibayarkan</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rekap['tatap_muka'] as $idxA => $tm)
                        <tr>
                            <td class="text-center">{{ $idxA + 1 }}</td>
                            <td class="text-center">{{ $tm['tanggal'] }}</td>
                            <td>{{ $tm['nama_pelatihan'] }}</td>
                            <td class="fw-semibold">{{ $tm['mata_pelatihan'] }}</td>
                            <td class="text-center fw-bold text-primary">{{ $tm['jp'] }}</td>
                            <td class="text-center text-muted">{{ $tm['jp_akumulasi'] }}</td>
                            <td class="text-center text-dark">{{ $tm['jp_dibayarkan'] }}</td>
                            <td>{{ $tm['keterangan'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-3">Tidak ada jam tatap muka yang direalisasikan pada bulan ini.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td colspan="4" class="text-end">JUMLAH BAGIAN A (TATAP MUKA):</td>
                        <td class="text-center text-primary fs-6">{{ $rekap['total_a'] }} JP</td>
                        <td></td>
                        <td class="text-center text-dark">{{ $rekap['total_dibayarkan_a'] }} JP</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- BAGIAN B — KONVERSI -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="fw-bold text-dark mb-0">
                <i class="bi bi-arrow-left-right text-primary me-1"></i> BAGIAN B &mdash; KEGIATAN KONVERSI JAM PELAJARAN
            </h6>
            <span class="badge bg-light text-primary border">Total Konversi (B): {{ $rekap['total_b'] }} JP</span>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-sm small align-middle mb-0">
                <thead class="table-light text-center">
                    <tr>
                        <th style="width: 40px;">No.</th>
                        <th style="width: 90px;">Tanggal</th>
                        <th>Nama Kegiatan Konversi</th>
                        <th>Kategori</th>
                        <th style="width: 70px;" class="text-center">JP</th>
                        <th style="width: 90px;" class="text-center">JP Akumulasi</th>
                        <th style="width: 95px;" class="text-center">JP Dibayarkan</th>
                        <th>Keterangan / Info Pembayaran</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rekap['konversi'] as $idxB => $kv)
                        <tr>
                            <td class="text-center">{{ $idxB + 1 }}</td>
                            <td class="text-center">{{ $kv['tanggal'] }}</td>
                            <td class="fw-semibold">{{ $kv['nama_kegiatan'] }}</td>
                            <td><span class="badge bg-light text-secondary border">{{ $kv['kategori'] }}</span></td>
                            <td class="text-center fw-bold text-primary">{{ $kv['jp'] }}</td>
                            <td class="text-center text-muted">{{ $kv['jp_akumulasi'] }}</td>
                            <td class="text-center text-dark">{{ $kv['jp_dibayarkan'] }}</td>
                            <td>
                                {{ $kv['informasi_pembayaran'] }}
                                @if($kv['nomor_sk'] && $kv['nomor_sk'] !== '-') &bull; SK: {{ $kv['nomor_sk'] }} @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-3">Tidak ada kegiatan konversi yang divalidasi pada bulan ini.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td colspan="4" class="text-end">JUMLAH BAGIAN B (KONVERSI):</td>
                        <td class="text-center text-primary fs-6">{{ $rekap['total_b'] }} JP</td>
                        <td></td>
                        <td class="text-center text-dark">{{ $rekap['total_dibayarkan_b'] }} JP</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- RINGKASAN REKAPITULASI -->
    <div class="row g-3 mb-4">
        <div class="col-md-7">
            <div class="card border p-3 bg-light">
                <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">RINGKASAN AKUMULASI JAM PELAJARAN (JP)</h6>
                <div class="d-flex justify-content-between py-1 border-bottom small">
                    <span>1. Total Jam Pelajaran Tatap Muka (Bagian A):</span>
                    <strong class="text-dark">{{ $rekap['total_a'] }} JP</strong>
                </div>
                <div class="d-flex justify-content-between py-1 border-bottom small">
                    <span>2. Total Jam Pelajaran Konversi (Bagian B):</span>
                    <strong class="text-dark">{{ $rekap['total_b'] }} JP</strong>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom fw-bold fs-6 text-primary">
                    <span>TOTAL JAM PELAJARAN (A + B):</span>
                    <span>{{ $rekap['total_ab'] }} JP</span>
                </div>
                <div class="d-flex justify-content-between py-1 border-bottom small">
                    <span>Total JP yang Dibayarkan (Honorarium):</span>
                    <strong class="text-dark">{{ $rekap['total_dibayarkan'] }} JP</strong>
                </div>
                <div class="d-flex justify-content-between py-1 border-bottom small">
                    <span>Batas Minimum JP Wajib / Bulan:</span>
                    <span class="text-muted">{{ $rekap['minimum_jp'] }} JP</span>
                </div>
                <div class="d-flex justify-content-between py-1 small">
                    <span>Kekurangan Jam Pelajaran:</span>
                    <strong class="{{ $rekap['kekurangan_jp'] > 0 ? 'text-danger' : 'text-success' }}">
                        {{ $rekap['kekurangan_jp'] > 0 ? $rekap['kekurangan_jp'] . ' JP' : '0 JP (Nihil)' }}
                    </strong>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card border p-3 text-center h-100 d-flex flex-column justify-content-center" style="background-color: var(--light-blue);">
                <span class="text-muted small d-block mb-1">Status Pencapaian Bulan {{ $rekap['nama_bulan'] }}</span>
                <div class="fs-4 fw-bold text-{{ $rekap['badge_class'] }} mb-2">
                    {{ $rekap['status_pencapaian'] }}
                </div>
                <span class="badge bg-{{ $rekap['badge_class'] }} px-3 py-2 mx-auto mb-3">
                    Beban: {{ $rekap['total_ab'] }} / {{ $rekap['minimum_jp'] }} JP
                </span>
                <p class="small text-muted mb-0">
                    @if($rekap['status_pencapaian'] === 'Melebihi')
                        Widyaiswara telah melampaui beban jam pelajaran minimal sebesar {{ $rekap['kelebihan_jp'] }} JP.
                    @elseif($rekap['status_pencapaian'] === 'Memenuhi')
                        Widyaiswara telah memenuhi beban jam pelajaran minimal tepat waktu.
                    @else
                        Widyaiswara belum memenuhi batas minimal, kekurangan beban sebanyak {{ $rekap['kekurangan_jp'] }} JP.
                    @endif
                </p>
            </div>
        </div>
    </div>

    <!-- AREA TANDA TANGAN LAPORAN -->
    <div class="row pt-4 mt-2 border-top text-center" style="font-size: 0.875rem;">
        <div class="col-sm-6 mb-4 mb-sm-0">
            <div class="text-muted mb-1">Mengetahui / Menyetujui,</div>
            <div class="fw-bold text-dark">{{ \App\Models\Pengaturan::get('pimpinan_jabatan', 'Kepala Pusat Pengembangan SDM') }}</div>
            <div style="height: 70px;"></div>
            <div class="fw-bold text-dark text-decoration-underline">{{ \App\Models\Pengaturan::get('pimpinan_nama', 'Dr. H. Hendra Sukmana, M.Si') }}</div>
            <div class="text-muted small">NIP. {{ \App\Models\Pengaturan::get('pimpinan_nip', '19750815 199903 1 003') }}</div>
        </div>
        <div class="col-sm-6">
            <div class="text-muted mb-1">{{ \App\Models\Pengaturan::get('pimpinan_kota', 'Jakarta') }}, {{ date('d') }} {{ $rekap['nama_bulan'] }} {{ $tahun }}</div>
            <div class="fw-bold text-dark">Widyaiswara yang Bersangkutan,</div>
            <div style="height: 70px;"></div>
            <div class="fw-bold text-dark text-decoration-underline">{{ $rekap['widyaiswara']->nama_gelar }}</div>
            <div class="text-muted small">NIP. {{ $rekap['widyaiswara']->nip ?? '-' }}</div>
        </div>
    </div>
</div>
@endsection
