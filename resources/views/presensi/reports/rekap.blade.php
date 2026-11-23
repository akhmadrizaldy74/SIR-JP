@extends('layouts.app')

@section('title', 'Rekapitulasi Kehadiran Peserta Diklat')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1">Rekapitulasi Kehadiran Peserta</h4>
        <div class="text-muted small">Matriks kehadiran per sesi pembelajaran untuk evaluasi kelulusan peserta diklat</div>
    </div>
    @if($selectedKelas)
        <div class="d-flex gap-2">
            <a href="{{ route('presensi.export.excel', ['kelas_mapel_id' => $selectedKelas->id]) }}" class="btn btn-success d-flex align-items-center gap-2 rounded-3 shadow-sm">
                <i class="bi bi-file-earmark-excel-fill"></i>
                <span>Download Excel</span>
            </a>
            <a href="{{ route('presensi.export.pdf', ['kelas_mapel_id' => $selectedKelas->id]) }}" target="_blank" class="btn btn-danger d-flex align-items-center gap-2 rounded-3 shadow-sm">
                <i class="bi bi-file-earmark-pdf-fill"></i>
                <span>Download PDF</span>
            </a>
        </div>
    @endif
</div>

<!-- Pilih Kelas -->
<div class="card border-0 shadow-sm rounded-3 p-3 mb-4 bg-white">
    <form method="GET" action="{{ route('presensi.rekap') }}" class="row g-2 align-items-end">
        <div class="col-md-6">
            <label class="form-label small fw-semibold text-muted mb-1">PILIH KELAS MATA PELATIHAN</label>
            <select name="kelas_mapel_id" class="form-select form-select-sm" onchange="this.form.submit()">
                @foreach($kelasMapels as $km)
                    <option value="{{ $km->id }}" {{ $selectedKelas && $selectedKelas->id == $km->id ? 'selected' : '' }}>
                        {{ $km->nama_kelas }} &bull; Angkatan: {{ $km->angkatan->nomor_angkatan ?? '-' }} ({{ $km->pelatihan->nama_pelatihan ?? '-' }})
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6 text-end">
            @if($selectedKelas)
                <span class="text-muted small">
                    Total Sesi: <strong>{{ $sessions->count() }}</strong> sesi &bull; 
                    Total Peserta: <strong>{{ $pesertas->count() }}</strong> orang
                </span>
            @endif
        </div>
    </form>
</div>

@if($selectedKelas)
<!-- Matriks Rekapitulasi Kehadiran -->
<div class="card bg-white border-0 shadow-sm rounded-3 p-0 mb-5 overflow-hidden">
    <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-light">
        <div>
            <h6 class="fw-bold text-dark mb-0">Tabel Matriks Presensi: {{ $selectedKelas->nama_kelas }}</h6>
            <small class="text-muted">Status: H (Hadir), T (Terlambat), I (Izin), S (Sakit), A (Alpa), D (Dispensasi)</small>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle mb-0 text-nowrap" style="font-size: 0.82rem;">
            <thead class="table-light text-center">
                <tr>
                    <th rowspan="2" style="width: 35px;">No</th>
                    <th rowspan="2">NIP</th>
                    <th rowspan="2" class="text-start">Nama Peserta</th>
                    <th rowspan="2" class="text-start">Instansi</th>
                    <th colspan="{{ max(1, $sessions->count()) }}" class="bg-primary-subtle text-primary fw-bold">Sesi Pembelajaran</th>
                    <th colspan="6" class="bg-secondary-subtle">Rekap Status</th>
                    <th rowspan="2" class="bg-success-subtle text-success fw-bold" style="width: 60px;">% Hadir</th>
                </tr>
                <tr>
                    @forelse($sessions as $idx => $s)
                        <th style="min-width: 45px; font-size: 0.72rem;" title="{{ $s->judul_sesi }} ({{ $s->tanggal->format('d/m/Y') }})">
                            S{{ $idx + 1 }}<br>
                            <span class="text-muted fw-normal">{{ $s->tanggal->format('d/m') }}</span>
                        </th>
                    @empty
                        <th class="text-muted small">Belum ada sesi</th>
                    @endforelse
                    <th style="width: 30px; background-color: #D1FAE5; color: #065F46;">H</th>
                    <th style="width: 30px; background-color: #DBEAFE; color: #1E40AF;">T</th>
                    <th style="width: 30px; background-color: #FEF3C7; color: #92400E;">I</th>
                    <th style="width: 30px; background-color: #FCE7F3; color: #9D174D;">S</th>
                    <th style="width: 30px; background-color: #FEE2E2; color: #991B1B;">A</th>
                    <th style="width: 30px; background-color: #EDE9FE; color: #5B21B6;">D</th>
                </tr>
            </thead>
            <tbody>
                @forelse($matrix as $item)
                    @php $pst = $item['peserta']; @endphp
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>{{ $pst->nip }}</td>
                        <td class="fw-bold text-dark">{{ $pst->nama }}</td>
                        <td class="text-muted small">{{ $pst->instansi ?: '-' }}</td>

                        @forelse($sessions as $s)
                            @php
                                $rec = $item['sessions'][$s->id] ?? null;
                                $st = $rec ? $rec->status_kehadiran : '-';
                                $bg = match($st) {
                                    'Hadir' => 'background-color: #D1FAE5; color: #065F46;',
                                    'Terlambat' => 'background-color: #DBEAFE; color: #1E40AF;',
                                    'Izin' => 'background-color: #FEF3C7; color: #92400E;',
                                    'Sakit' => 'background-color: #FCE7F3; color: #9D174D;',
                                    'Alpa' => 'background-color: #FEE2E2; color: #991B1B;',
                                    'Dispensasi' => 'background-color: #EDE9FE; color: #5B21B6;',
                                    default => 'color: #94A3B8;',
                                };
                                $code = match($st) {
                                    'Hadir' => 'H',
                                    'Terlambat' => 'T',
                                    'Izin' => 'I',
                                    'Sakit' => 'S',
                                    'Alpa' => 'A',
                                    'Dispensasi' => 'D',
                                    default => '-',
                                };
                            @endphp
                            <td class="text-center fw-bold" style="{{ $bg }}" title="{{ $s->judul_sesi }}: {{ $st }}">
                                {{ $code }}
                            </td>
                        @empty
                            <td class="text-center text-muted">-</td>
                        @endforelse

                        <td class="text-center fw-bold">{{ $item['hadir'] }}</td>
                        <td class="text-center fw-bold">{{ $item['terlambat'] }}</td>
                        <td class="text-center fw-bold">{{ $item['izin'] }}</td>
                        <td class="text-center fw-bold">{{ $item['sakit'] }}</td>
                        <td class="text-center fw-bold">{{ $item['alpa'] }}</td>
                        <td class="text-center fw-bold">{{ $item['dispensasi'] }}</td>
                        <td class="text-center fw-bold {{ $item['persen'] >= 80 ? 'text-success' : 'text-danger' }}">
                            {{ $item['persen'] }}%
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ 11 + max(1, $sessions->count()) }}" class="text-center text-muted py-4">
                            Belum ada data peserta yang terdaftar pada kelas ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection
