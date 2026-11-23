@extends('layouts.app')

@section('title', 'Detail Sesi Presensi: ' . $attendanceSession->judul_sesi)

@section('content')
<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <a href="{{ route('attendance-sessions.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
            <i class="bi bi-arrow-left"></i> Kembali ke Daftar Sesi
        </a>
        <div class="d-flex gap-2">
            @if($attendanceSession->status === 'Draft')
                <form action="{{ route('attendance-sessions.activate', $attendanceSession->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 fw-bold">
                        <i class="bi bi-play-circle me-1"></i> Buka Presensi Sesi Ini
                    </button>
                </form>
            @elseif($attendanceSession->status === 'Aktif')
                <a href="{{ route('attendance-sessions.qr_display', $attendanceSession->id) }}" target="_blank" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold shadow-sm">
                    <i class="bi bi-qr-code-scan me-1"></i> Buka Layar QR Proyektor
                </a>
                <form action="{{ route('attendance-sessions.close', $attendanceSession->id) }}" method="POST" onsubmit="return confirm('Tutup sesi presensi? Presensi QR peserta akan dihentikan.');">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-warning text-dark rounded-pill px-3 fw-semibold">
                        <i class="bi bi-stop-circle me-1"></i> Tutup Sesi
                    </button>
                </form>
            @endif

            <a href="{{ route('attendance-manual.index', $attendanceSession->id) }}" class="btn btn-sm btn-outline-info rounded-pill px-3">
                <i class="bi bi-pencil-square me-1"></i> Input Manual
            </a>
            <a href="{{ route('class-documentations.index', ['attendance_session_id' => $attendanceSession->id]) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                <i class="bi bi-camera me-1"></i> Foto Kegiatan ({{ $attendanceSession->documentations->count() }})
            </a>
        </div>
    </div>

    <!-- Info Banner Card -->
    <div class="card border-0 shadow-sm rounded-3 p-4 bg-white mb-4">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <span class="badge bg-light text-dark border small fw-semibold mb-2">
                    <i class="bi bi-calendar3"></i> {{ $attendanceSession->tanggal->translatedFormat('l, d F Y') }}
                </span>
                <h4 class="fw-bold text-dark mb-1">{{ $attendanceSession->judul_sesi }}</h4>
                <div class="text-muted small">
                    <i class="bi bi-journal-text me-1"></i> Mata Pelatihan: <strong>{{ $attendanceSession->jadwal->mataPelatihan->nama_mata_pelatihan ?? '-' }}</strong> &bull;
                    Kelas: <strong>{{ $attendanceSession->kelasMapel->nama_kelas ?? '-' }}</strong> &bull;
                    Ruangan: <strong>{{ $attendanceSession->kelasMapel->ruangan->nama_ruangan ?? ($attendanceSession->jadwal->ruangan ?? '-') }}</strong>
                </div>
            </div>
            <div>
                @if($attendanceSession->status === 'Aktif')
                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-2 fs-6">
                        <span class="spinner-grow spinner-grow-sm me-1" style="width: 0.6rem; height: 0.6rem;"></span> Presensi Dibuka
                    </span>
                @elseif($attendanceSession->status === 'Selesai')
                    <span class="badge bg-secondary-subtle text-secondary border rounded-pill px-3 py-2 fs-6">Presensi Selesai / Ditutup</span>
                @else
                    <span class="badge bg-light text-muted border rounded-pill px-3 py-2 fs-6">Draft (Belum Dibuka)</span>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- 8 Kartu Statistik Kehadiran Sesi -->
<div class="row row-cols-2 row-cols-md-4 row-cols-xl-8 g-2 mb-4 text-center">
    <div class="col">
        <div class="card p-2 border shadow-sm rounded-3 bg-white">
            <span class="text-muted small">Peserta</span>
            <h4 class="fw-bold text-dark mb-0">{{ $stats['total_peserta'] }}</h4>
        </div>
    </div>
    <div class="col">
        <div class="card p-2 border shadow-sm rounded-3" style="background-color: #D1FAE5;">
            <span class="fw-semibold small" style="color: #065F46;">Hadir</span>
            <h4 class="fw-bold mb-0" style="color: #065F46;">{{ $stats['hadir'] }}</h4>
        </div>
    </div>
    <div class="col">
        <div class="card p-2 border shadow-sm rounded-3" style="background-color: #DBEAFE;">
            <span class="fw-semibold small" style="color: #1E40AF;">Terlambat</span>
            <h4 class="fw-bold mb-0" style="color: #1E40AF;">{{ $stats['terlambat'] }}</h4>
        </div>
    </div>
    <div class="col">
        <div class="card p-2 border shadow-sm rounded-3" style="background-color: #FEF3C7;">
            <span class="fw-semibold small" style="color: #92400E;">Izin</span>
            <h4 class="fw-bold mb-0" style="color: #92400E;">{{ $stats['izin'] }}</h4>
        </div>
    </div>
    <div class="col">
        <div class="card p-2 border shadow-sm rounded-3" style="background-color: #FCE7F3;">
            <span class="fw-semibold small" style="color: #9D174D;">Sakit</span>
            <h4 class="fw-bold mb-0" style="color: #9D174D;">{{ $stats['sakit'] }}</h4>
        </div>
    </div>
    <div class="col">
        <div class="card p-2 border shadow-sm rounded-3" style="background-color: #FEE2E2;">
            <span class="fw-semibold small" style="color: #991B1B;">Alpa</span>
            <h4 class="fw-bold mb-0" style="color: #991B1B;">{{ $stats['alpa'] }}</h4>
        </div>
    </div>
    <div class="col">
        <div class="card p-2 border shadow-sm rounded-3" style="background-color: #EDE9FE;">
            <span class="fw-semibold small" style="color: #5B21B6;">Dispensasi</span>
            <h4 class="fw-bold mb-0" style="color: #5B21B6;">{{ $stats['dispensasi'] }}</h4>
        </div>
    </div>
    <div class="col">
        <div class="card p-2 border shadow-sm rounded-3 bg-light">
            <span class="text-muted small">Belum Presensi</span>
            <h4 class="fw-bold text-secondary mb-0">{{ $stats['belum_presensi'] }}</h4>
        </div>
    </div>
</div>

<!-- Daftar Peserta & Live Status Kehadiran -->
<div class="card bg-white border-0 shadow-sm rounded-3 p-4 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="fw-bold text-dark mb-0">Daftar Kehadiran Peserta</h5>
            <div class="text-muted small">Tingkat kehadiran: <strong class="text-success">{{ $stats['persen_kehadiran'] }}%</strong></div>
        </div>
        <div>
            <a href="{{ route('attendance-manual.index', $attendanceSession->id) }}" class="btn btn-sm btn-primary rounded-pill px-3">
                <i class="bi bi-pencil-square me-1"></i> Kelola & Update Manual
            </a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
            <thead class="table-light">
                <tr>
                    <th style="width: 40px;">No</th>
                    <th>Nama Peserta</th>
                    <th>NIP</th>
                    <th>Instansi</th>
                    <th class="text-center">Status Kehadiran</th>
                    <th>Waktu Check-in</th>
                    <th>Metode</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stats['pesertas'] as $pst)
                    @php
                        $rec = $stats['presensis']->get($pst->id);
                        $badge = $rec ? $rec->status_badge : ['bg' => '#F1F5F9', 'color' => '#64748B', 'label' => 'Belum Presensi'];
                    @endphp
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td class="fw-bold text-dark">{{ $pst->nama }}</td>
                        <td>{{ $pst->nip }}</td>
                        <td>{{ $pst->instansi ?: '-' }}</td>
                        <td class="text-center">
                            <span class="badge rounded-pill px-3 py-1 fw-semibold" style="background-color: {{ $badge['bg'] }}; color: {{ $badge['color'] }}; font-size: 0.78rem;">
                                {{ $badge['label'] }}
                            </span>
                        </td>
                        <td>
                            @if($rec && $rec->waktu_presensi)
                                <span class="fw-semibold text-dark">{{ substr($rec->waktu_presensi, 0, 5) }} WIB</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($rec)
                                <span class="badge bg-light text-dark border">{{ $rec->metode_presensi }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="small text-muted">{{ $rec->keterangan ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">Belum ada data peserta yang terdaftar pada kelas/angkatan ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
