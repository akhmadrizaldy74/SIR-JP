@extends('layouts.app')

@section('title', 'Rekap Aktivitas Narasumber - ' . $rekap['narasumber']->nama_gelar)
@section('page_title', 'Rekapitulasi Mengajar Narasumber')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('rekapitulasi.index', ['jenis_pengajar' => 'Narasumber', 'bulan' => $bulan, 'tahun' => $tahun]) }}">Rekapitulasi JP</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $rekap['narasumber']->nama_gelar }}</li>
@endsection

@section('page_actions')
    <div class="d-flex gap-2">
        <a href="{{ route('laporan.export.pdf_narasumber', ['id' => $rekap['narasumber']->id, 'bulan' => $bulan, 'tahun' => $tahun]) }}" target="_blank" class="btn btn-danger btn-sm">
            <i class="bi bi-file-earmark-pdf me-1"></i> Cetak PDF
        </a>
        <a href="{{ route('rekapitulasi.index', ['jenis_pengajar' => 'Narasumber', 'bulan' => $bulan, 'tahun' => $tahun]) }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>
@endsection

@section('content')
<div class="card-custom p-4 shadow-sm bg-white border">
    <div class="text-center border-bottom pb-3 mb-4">
        <h5 class="fw-bold text-dark mb-1 text-uppercase">{{ \App\Models\Pengaturan::get('instansi_nama', 'Pusat Pengembangan Sumber Daya Manusia') }}</h5>
        <h6 class="fw-semibold text-primary mb-2">REKAPITULASI AKTIVITAS MENGAJAR NARASUMBER EKSTERNAL</h6>
        <div class="badge bg-light text-secondary border px-3 py-1">
            PERIODE BULAN: {{ strtoupper($rekap['nama_bulan']) }} {{ $tahun }}
        </div>
    </div>

    <!-- Informasi Narasumber -->
    <div class="row g-2 mb-4 p-3 bg-light rounded border">
        <div class="col-md-6">
            <table class="table table-sm table-borderless mb-0 small">
                <tr>
                    <td style="width: 130px;" class="text-muted">Nama Narasumber:</td>
                    <td class="fw-bold text-dark">{{ $rekap['narasumber']->nama_gelar }}</td>
                </tr>
                <tr>
                    <td class="text-muted">NIK:</td>
                    <td>{{ $rekap['narasumber']->nik }}</td>
                </tr>
            </table>
        </div>
        <div class="col-md-6">
            <table class="table table-sm table-borderless mb-0 small">
                <tr>
                    <td style="width: 130px;" class="text-muted">Instansi Asal:</td>
                    <td class="fw-bold text-dark">{{ $rekap['narasumber']->instansi }}</td>
                </tr>
                <tr>
                    <td class="text-muted">Kontak:</td>
                    <td>{{ $rekap['narasumber']->nomor_telepon ?? '-' }} &bull; {{ $rekap['narasumber']->email ?? '-' }}</td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Tabel Sesi Mengajar -->
    <div class="table-responsive mb-4">
        <table class="table table-bordered table-sm small align-middle mb-0">
            <thead class="table-light text-center">
                <tr>
                    <th style="width: 40px;">No.</th>
                    <th style="width: 90px;">Tanggal</th>
                    <th>Program Pelatihan & Angkatan</th>
                    <th>Mata Pelatihan</th>
                    <th style="width: 80px;" class="text-center">JP Aktual</th>
                    <th style="width: 100px;" class="text-center">JP Dibayarkan</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rekap['tatap_muka'] as $idx => $tm)
                    <tr>
                        <td class="text-center">{{ $idx + 1 }}</td>
                        <td class="text-center">{{ $tm['tanggal'] }}</td>
                        <td>{{ $tm['nama_pelatihan'] }}</td>
                        <td class="fw-semibold">{{ $tm['mata_pelatihan'] }}</td>
                        <td class="text-center fw-bold text-primary">{{ $tm['jp'] }} JP</td>
                        <td class="text-center fw-semibold text-dark">{{ $tm['jp_dibayarkan'] }} JP</td>
                        <td>{{ $tm['keterangan'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">Tidak ada jam mengajar yang terealisasi pada bulan ini.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot class="table-light fw-bold">
                <tr>
                    <td colspan="4" class="text-end">TOTAL KESELURUHAN:</td>
                    <td class="text-center text-primary fs-6">{{ $rekap['total_jp'] }} JP</td>
                    <td class="text-center text-dark fs-6">{{ $rekap['total_dibayarkan'] }} JP</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Tanda Tangan -->
    <div class="row pt-4 mt-2 border-top text-center" style="font-size: 0.875rem;">
        <div class="col-sm-6 mb-4 mb-sm-0">
            <div class="text-muted mb-1">Mengetahui,</div>
            <div class="fw-bold text-dark">{{ \App\Models\Pengaturan::get('pimpinan_jabatan', 'Kepala Pusat') }}</div>
            <div style="height: 70px;"></div>
            <div class="fw-bold text-dark text-decoration-underline">{{ \App\Models\Pengaturan::get('pimpinan_nama', 'Pimpinan Instansi') }}</div>
            <div class="text-muted small">NIP. {{ \App\Models\Pengaturan::get('pimpinan_nip', '-') }}</div>
        </div>
        <div class="col-sm-6">
            <div class="text-muted mb-1">{{ \App\Models\Pengaturan::get('pimpinan_kota', 'Jakarta') }}, {{ date('d') }} {{ $rekap['nama_bulan'] }} {{ $tahun }}</div>
            <div class="fw-bold text-dark">Narasumber,</div>
            <div style="height: 70px;"></div>
            <div class="fw-bold text-dark text-decoration-underline">{{ $rekap['narasumber']->nama_gelar }}</div>
        </div>
    </div>
</div>
@endsection
