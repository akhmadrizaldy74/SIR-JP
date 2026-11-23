@extends('portal.layout')

@section('title', 'Riwayat Presensi Peserta')

@section('content')
<div class="mb-3 d-flex align-items-center justify-content-between">
    <a href="{{ route('portal.dashboard') }}" class="btn btn-sm btn-light border rounded-pill px-3">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
    <h6 class="fw-bold mb-0 text-dark">Riwayat Kehadiran</h6>
    <span style="width: 70px;"></span>
</div>

<!-- Filter Status -->
<div class="card card-custom p-2 mb-3 border shadow-sm">
    <div class="d-flex gap-1 overflow-x-auto pb-1" style="scrollbar-width: none;">
        <a href="{{ route('portal.riwayat') }}" class="btn btn-sm rounded-pill px-3 {{ !$statusFilter ? 'btn-primary' : 'btn-light border' }}" style="white-space: nowrap; font-size: 0.75rem;">
            Semua
        </a>
        <a href="{{ route('portal.riwayat', ['status' => 'Hadir']) }}" class="btn btn-sm rounded-pill px-3 {{ $statusFilter === 'Hadir' ? 'btn-success' : 'btn-light border' }}" style="white-space: nowrap; font-size: 0.75rem;">
            Hadir
        </a>
        <a href="{{ route('portal.riwayat', ['status' => 'Terlambat']) }}" class="btn btn-sm rounded-pill px-3 {{ $statusFilter === 'Terlambat' ? 'btn-info text-white' : 'btn-light border' }}" style="white-space: nowrap; font-size: 0.75rem;">
            Terlambat
        </a>
        <a href="{{ route('portal.riwayat', ['status' => 'Izin']) }}" class="btn btn-sm rounded-pill px-3 {{ $statusFilter === 'Izin' ? 'btn-warning text-dark' : 'btn-light border' }}" style="white-space: nowrap; font-size: 0.75rem;">
            Izin
        </a>
        <a href="{{ route('portal.riwayat', ['status' => 'Sakit']) }}" class="btn btn-sm rounded-pill px-3 {{ $statusFilter === 'Sakit' ? 'btn-danger' : 'btn-light border' }}" style="white-space: nowrap; font-size: 0.75rem;">
            Sakit
        </a>
        <a href="{{ route('portal.riwayat', ['status' => 'Alpa']) }}" class="btn btn-sm rounded-pill px-3 {{ $statusFilter === 'Alpa' ? 'btn-danger' : 'btn-light border' }}" style="white-space: nowrap; font-size: 0.75rem;">
            Alpa
        </a>
        <a href="{{ route('portal.riwayat', ['status' => 'Dispensasi']) }}" class="btn btn-sm rounded-pill px-3 {{ $statusFilter === 'Dispensasi' ? 'btn-dark' : 'btn-light border' }}" style="white-space: nowrap; font-size: 0.75rem;">
            Dispensasi
        </a>
    </div>
</div>

<!-- List Riwayat Kehadiran -->
<div class="d-flex flex-column gap-2">
    @forelse($presensis as $item)
        @php $badge = $item->status_badge; @endphp
        <div class="card card-custom p-3 border shadow-sm">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <span class="badge bg-light text-secondary border small mb-1">
                        {{ $item->tanggal->translatedFormat('l, d F Y') }}
                    </span>
                    <h6 class="fw-bold text-dark mb-0">
                        {{ $item->session->judul_sesi ?? 'Sesi Pembelajaran' }}
                    </h6>
                    <small class="text-muted">
                        {{ $item->session->jadwal->mataPelatihan->nama_mata_pelatihan ?? ($item->session->kelasMapel->nama_kelas ?? '-') }}
                    </small>
                </div>
                <span class="badge rounded-pill px-3 py-1 fw-semibold" style="background-color: {{ $badge['bg'] }}; color: {{ $badge['color'] }}; font-size: 0.78rem;">
                    {{ $badge['label'] }}
                </span>
            </div>

            <div class="border-top pt-2 d-flex justify-content-between align-items-center text-muted" style="font-size: 0.75rem;">
                <div>
                    <i class="bi bi-clock"></i> 
                    Check-in: <strong>{{ $item->waktu_presensi ? substr($item->waktu_presensi, 0, 5) . ' WIB' : '-' }}</strong>
                    <span class="badge bg-light text-secondary border ms-1">{{ $item->metode_presensi }}</span>
                </div>
                @if($item->keterangan)
                    <span class="text-truncate" style="max-width: 140px;" title="{{ $item->keterangan }}">
                        <i class="bi bi-info-circle"></i> {{ $item->keterangan }}
                    </span>
                @endif
            </div>
        </div>
    @empty
        <div class="card card-custom p-4 text-center border text-muted">
            <i class="bi bi-inbox fs-2 text-secondary mb-2"></i>
            <h6 class="fw-semibold mb-1">Belum ada riwayat kehadiran</h6>
            <small>Riwayat akan muncul setelah Anda melakukan presensi atau admin menginput kehadiran Anda.</small>
        </div>
    @endforelse
</div>

<!-- Pagination -->
<div class="mt-3">
    {{ $presensis->links('vendor.pagination.bootstrap-5') }}
</div>
@endsection
