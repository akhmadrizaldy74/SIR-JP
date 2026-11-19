@extends('layouts.app')

@section('title', 'Realisasi Kegiatan')
@section('page_title', 'Realisasi Kegiatan Pembelajaran')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Realisasi Kegiatan</li>
@endsection

@section('page_actions')
    <a href="{{ route('jadwal.index') }}" class="btn btn-navy d-flex align-items-center gap-2">
        <i class="bi bi-calendar-check"></i>
        <span>Pilih Jadwal untuk Realisasi</span>
    </a>
@endsection

@section('content')
<div class="card-custom">
    <div class="card-custom-header flex-column flex-md-row gap-3">
        <h5 class="card-custom-title">
            <i class="bi bi-check2-circle text-primary"></i> Daftar Realisasi Kegiatan & Jam Pelajaran Aktual
        </h5>

        <!-- Filter & Search -->
        <form method="GET" action="{{ route('realisasi.index') }}" class="d-flex flex-wrap align-items-center gap-2">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari materi / pengajar..." value="{{ $search }}" style="width: 200px;">
            <select name="bulan" class="form-select form-select-sm" style="width: 140px;">
                @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                        {{ (new \App\Services\JPRecapitulationService)->getNamaBulan($m) }}
                    </option>
                @endfor
            </select>
            <select name="tahun" class="form-select form-select-sm" style="width: 95px;">
                @for($y = date('Y') - 2; $y <= date('Y') + 1; $y++)
                    <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
            <select name="status" class="form-select form-select-sm" style="width: 130px;">
                <option value="">-- Status --</option>
                <option value="Terlaksana" {{ $status == 'Terlaksana' ? 'selected' : '' }}>Terlaksana</option>
                <option value="Dibatalkan" {{ $status == 'Dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
            </select>
            <button type="submit" class="btn btn-sm btn-primary">
                <i class="bi bi-filter"></i> Filter
            </button>
            @if(request()->has('status') || request()->has('search'))
                <a href="{{ route('realisasi.index') }}" class="btn btn-sm btn-outline-secondary" title="Reset Filter">
                    <i class="bi bi-x-circle"></i>
                </a>
            @endif
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-custom">
            <thead>
                <tr>
                    <th style="width: 50px;">No.</th>
                    <th>Tgl Realisasi</th>
                    <th>Program & Angkatan</th>
                    <th>Mata Pelatihan</th>
                    <th>Pengajar & JP Realisasi</th>
                    <th class="text-center">JP Aktual</th>
                    <th class="text-center">JP Dibayar</th>
                    <th class="text-center">Status</th>
                    <th class="text-center" style="width: 130px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($realisasis as $index => $r)
                    <tr>
                        <td class="text-center">{{ $realisasis->firstItem() + $index }}</td>
                        <td>
                            <div class="fw-bold text-dark">{{ $r->tanggal_realisasi->format('d/m/Y') }}</div>
                            <div class="text-muted small">{{ substr($r->jam_mulai_aktual, 0, 5) }} - {{ substr($r->jam_selesai_aktual, 0, 5) }} WIB</div>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $r->jadwal->mataPelatihan->angkatan->pelatihan->nama_pelatihan }}</div>
                            <span class="badge bg-light text-primary border" style="font-size: 0.7rem;">{{ $r->jadwal->mataPelatihan->angkatan->nomor_angkatan }}</span>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $r->jadwal->mataPelatihan->nama_mata_pelatihan }}</div>
                            <div class="text-muted small">{{ $r->catatan ?? '-' }}</div>
                        </td>
                        <td>
                            @forelse($r->pengajarRealisasis as $pr)
                                <div class="small mb-1">
                                    <strong class="text-dark">{{ $pr->pengajar_nama }}</strong>
                                    <span class="badge bg-light text-primary border ms-1">{{ $pr->jp_aktual }} JP</span>
                                    @if($pr->jp_dibayarkan != $pr->jp_aktual)
                                        <span class="badge bg-light text-warning border" title="JP Dibayarkan">Bayar: {{ $pr->jp_dibayarkan }} JP</span>
                                    @endif
                                </div>
                            @empty
                                <span class="text-muted small fst-italic">Tidak ada alokasi pengajar</span>
                            @endforelse
                        </td>
                        <td class="text-center fw-bold text-primary fs-6">{{ $r->jp_aktual }} JP</td>
                        <td class="text-center fw-semibold text-secondary">{{ $r->jp_dibayarkan }} JP</td>
                        <td class="text-center">
                            <span class="badge bg-{{ $r->status == 'Terlaksana' ? 'success' : 'danger' }} badge-custom">
                                {{ $r->status }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('realisasi.show', $r) }}" class="btn btn-outline-info" title="Detail Realisasi">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('realisasi.edit', $r) }}" class="btn btn-outline-warning" title="Edit Realisasi">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button" class="btn btn-outline-danger" title="Hapus Realisasi" onclick="confirmDelete('del-real-{{ $r->id }}', 'Hapus catatan realisasi ini?')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                            <form id="del-real-{{ $r->id }}" action="{{ route('realisasi.destroy', $r) }}" method="POST" class="d-none">
                                @csrf
                                @method('DELETE')
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-4 text-muted">
                            <i class="bi bi-journal-x fs-3 d-block mb-2"></i>
                            Belum ada catatan realisasi kegiatan pada periode ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($realisasis->hasPages())
        <div class="table-pagination-footer">
            <span class="text-muted small">
                Menampilkan {{ $realisasis->firstItem() }} - {{ $realisasis->lastItem() }} dari {{ $realisasis->total() }} data
            </span>
            <div>
                {{ $realisasis->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
