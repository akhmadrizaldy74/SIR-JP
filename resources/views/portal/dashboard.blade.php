@extends('portal.layout')

@section('title', 'Dashboard Peserta')

@section('content')
<!-- Profil Peserta Card -->
<div class="card card-custom p-3 mb-3 border shadow-sm">
    <div class="d-flex align-items-center gap-3">
        <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm flex-shrink-0" 
             style="width: 52px; height: 52px; background: linear-gradient(135deg, #3B82F6 0%, #1D4ED8 100%); font-size: 1.25rem;">
            {{ strtoupper(substr($peserta->nama, 0, 1)) }}
        </div>
        <div class="overflow-hidden">
            <h6 class="fw-bold text-dark mb-0 text-truncate">{{ $peserta->nama }}</h6>
            <div class="text-muted small">NIP: {{ $peserta->nip }}</div>
            <div class="badge rounded-pill bg-light text-primary border small fw-normal mt-1">
                {{ $peserta->instansi ?: 'PPSDM Ketenagakerjaan' }}
            </div>
        </div>
    </div>
    <hr class="my-2 border-light">
    <div class="row g-2 text-center" style="font-size: 0.75rem;">
        <div class="col-4 border-end">
            <span class="text-muted d-block">Program</span>
            <span class="fw-bold text-dark text-truncate d-block">{{ $peserta->angkatan->pelatihan->nama_pelatihan ?? '-' }}</span>
        </div>
        <div class="col-4 border-end">
            <span class="text-muted d-block">Angkatan</span>
            <span class="fw-bold text-dark text-truncate d-block">{{ $peserta->angkatan->nomor_angkatan ?? '-' }}</span>
        </div>
        <div class="col-4">
            <span class="text-muted d-block">Kelas</span>
            <span class="fw-bold text-dark text-truncate d-block">{{ $peserta->kelasMapel->nama_kelas ?? '-' }}</span>
        </div>
    </div>
</div>

<!-- Sesi Pembelajaran Hari Ini -->
<div class="mb-3">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h6 class="fw-bold text-dark mb-0">
            <i class="bi bi-clock-history text-primary me-1"></i> Sesi Pembelajaran Hari Ini
        </h6>
        <span class="badge bg-light text-secondary border small">{{ now()->translatedFormat('d F Y') }}</span>
    </div>

    @forelse($sessionsToday as $sess)
        @php
            $presensiUser = $presensisToday->get($sess->id);
            $badge = $presensiUser ? $presensiUser->status_badge : null;
        @endphp
        <div class="card card-custom p-3 mb-2 border {{ $sess->status === 'Aktif' ? 'border-primary' : '' }}">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <span class="badge bg-light text-dark border small fw-semibold">
                        {{ substr($sess->jam_mulai, 0, 5) }} - {{ substr($sess->jam_selesai, 0, 5) }} WIB
                    </span>
                    <h6 class="fw-bold text-dark mt-1 mb-0">{{ $sess->judul_sesi }}</h6>
                    <div class="text-muted small">
                        {{ $sess->jadwal->mataPelatihan->nama_mata_pelatihan ?? ($sess->kelasMapel->nama_kelas ?? 'Sesi Pembelajaran') }}
                    </div>
                </div>
                <div>
                    @if($presensiUser)
                        <span class="badge rounded-pill px-3 py-2 fw-semibold" style="background-color: {{ $badge['bg'] }}; color: {{ $badge['color'] }}; font-size: 0.8rem;">
                            <i class="bi bi-check2-circle"></i> {{ $badge['label'] }}
                        </span>
                    @elseif($sess->status === 'Aktif')
                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1 small">
                            <span class="spinner-grow spinner-grow-sm me-1" style="width: 0.5rem; height: 0.5rem;"></span> Dibuka
                        </span>
                    @elseif($sess->status === 'Selesai')
                        <span class="badge bg-secondary-subtle text-secondary border rounded-pill px-2 py-1 small">Selesai</span>
                    @else
                        <span class="badge bg-light text-muted border rounded-pill px-2 py-1 small">Belum Dibuka</span>
                    @endif
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center pt-2 border-top" style="font-size: 0.75rem;">
                <span class="text-muted">
                    <i class="bi bi-geo-alt"></i> {{ $sess->kelasMapel->ruangan->nama_ruangan ?? ($sess->jadwal->ruangan ?? 'Ruang Kelas') }}
                </span>
                
                @if(!$presensiUser && $sess->status === 'Aktif')
                    <a href="{{ route('portal.scan', ['session_id' => $sess->id]) }}" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold d-flex align-items-center gap-1 shadow-sm">
                        <i class="bi bi-qr-code-scan"></i>
                        <span>Scan Presensi</span>
                    </a>
                @elseif($presensiUser)
                    <span class="text-muted small">
                        Check-in: {{ $presensiUser->waktu_check_in ? $presensiUser->waktu_check_in->format('H:i:s') : '-' }} WIB ({{ $presensiUser->metode_presensi }})
                    </span>
                @else
                    <span class="text-muted fst-italic">Menunggu admin membuka sesi</span>
                @endif
            </div>
        </div>
    @empty
        <div class="card card-custom p-4 text-center border text-muted">
            <i class="bi bi-calendar2-x fs-2 text-secondary mb-2"></i>
            <h6 class="fw-semibold mb-1">Tidak ada sesi pembelajaran hari ini</h6>
            <small>Jadwal sesi berikutnya akan ditampilkan saat admin menjadwalkan presensi.</small>
        </div>
    @endforelse
</div>

<!-- Statistik Kehadiran Saya -->
<div class="card card-custom p-3 mb-3 border shadow-sm">
    <h6 class="fw-bold text-dark mb-3">
        <i class="bi bi-bar-chart-fill text-primary me-1"></i> Rekap Kehadiran Saya
    </h6>
    <div class="row g-2 text-center">
        <div class="col-4">
            <div class="p-2 rounded-3" style="background-color: #D1FAE5;">
                <h4 class="fw-bold mb-0" style="color: #065F46;">{{ $totalHadir }}</h4>
                <small class="fw-semibold" style="color: #065F46; font-size: 0.75rem;">Hadir</small>
            </div>
        </div>
        <div class="col-4">
            <div class="p-2 rounded-3" style="background-color: #DBEAFE;">
                <h4 class="fw-bold mb-0" style="color: #1E40AF;">{{ $totalTerlambat }}</h4>
                <small class="fw-semibold" style="color: #1E40AF; font-size: 0.75rem;">Terlambat</small>
            </div>
        </div>
        <div class="col-4">
            <div class="p-2 rounded-3" style="background-color: #FEF3C7;">
                <h4 class="fw-bold mb-0" style="color: #92400E;">{{ $totalIzin }}</h4>
                <small class="fw-semibold" style="color: #92400E; font-size: 0.75rem;">Izin</small>
            </div>
        </div>
        <div class="col-4">
            <div class="p-2 rounded-3" style="background-color: #FCE7F3;">
                <h4 class="fw-bold mb-0" style="color: #9D174D;">{{ $totalSakit }}</h4>
                <small class="fw-semibold" style="color: #9D174D; font-size: 0.75rem;">Sakit</small>
            </div>
        </div>
        <div class="col-4">
            <div class="p-2 rounded-3" style="background-color: #FEE2E2;">
                <h4 class="fw-bold mb-0" style="color: #991B1B;">{{ $totalAlpa }}</h4>
                <small class="fw-semibold" style="color: #991B1B; font-size: 0.75rem;">Alpa</small>
            </div>
        </div>
        <div class="col-4">
            <div class="p-2 rounded-3" style="background-color: #EDE9FE;">
                <h4 class="fw-bold mb-0" style="color: #5B21B6;">{{ $totalDispensasi }}</h4>
                <small class="fw-semibold" style="color: #5B21B6; font-size: 0.75rem;">Dispensasi</small>
            </div>
        </div>
    </div>
</div>

<!-- Quick Action Menu -->
<div class="row g-2 mb-3">
    <div class="col-6">
        <a href="{{ route('portal.scan') }}" class="card card-custom p-3 text-decoration-none text-center h-100 border hover-shadow">
            <div class="d-inline-flex p-3 rounded-circle bg-primary-subtle text-primary mx-auto mb-2">
                <i class="bi bi-qr-code-scan fs-3"></i>
            </div>
            <h6 class="fw-bold text-dark mb-0">Scan QR Presensi</h6>
            <small class="text-muted" style="font-size: 0.72rem;">Kamera HP atau upload</small>
        </a>
    </div>
    <div class="col-6">
        <a href="{{ route('portal.pengajuan') }}" class="card card-custom p-3 text-decoration-none text-center h-100 border hover-shadow">
            <div class="d-inline-flex p-3 rounded-circle bg-warning-subtle text-warning mx-auto mb-2">
                <i class="bi bi-file-earmark-medical fs-3"></i>
            </div>
            <h6 class="fw-bold text-dark mb-0">Ajukan Izin/Sakit</h6>
            <small class="text-muted" style="font-size: 0.72rem;">Upload surat dokter/izin</small>
        </a>
    </div>
</div>

<!-- Riwayat Terakhir -->
@if($recentPresensis->isNotEmpty())
<div class="card card-custom p-3 border shadow-sm">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h6 class="fw-bold text-dark mb-0">
            <i class="bi bi-journal-check text-primary me-1"></i> Presensi Terakhir
        </h6>
        <a href="{{ route('portal.riwayat') }}" class="text-decoration-none small fw-semibold">Lihat Semua</a>
    </div>
    <div class="list-group list-group-flush">
        @foreach($recentPresensis as $rp)
            @php $badge = $rp->status_badge; @endphp
            <div class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center border-bottom">
                <div>
                    <div class="fw-bold text-dark small">{{ $rp->session->judul_sesi ?? 'Sesi Pembelajaran' }}</div>
                    <div class="text-muted" style="font-size: 0.7rem;">
                        {{ $rp->tanggal->format('d/m/Y') }} &bull; {{ $rp->waktu_presensi ? substr($rp->waktu_presensi, 0, 5) . ' WIB' : '-' }} ({{ $rp->metode_presensi }})
                    </div>
                </div>
                <span class="badge rounded-pill px-2 py-1" style="background-color: {{ $badge['bg'] }}; color: {{ $badge['color'] }}; font-size: 0.75rem;">
                    {{ $badge['label'] }}
                </span>
            </div>
        @endforeach
    </div>
</div>
@endif
@endsection
