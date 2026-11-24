@extends('layouts.app')

@section('title', 'Jadwal Pelatihan')
@section('page_title', 'Jadwal Pembelajaran')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Jadwal Pelatihan</li>
@endsection

@section('page_actions')
    <div class="d-flex gap-2">
        <a href="{{ route('jadwal.create') }}" class="btn btn-navy d-flex align-items-center gap-2">
            <i class="bi bi-calendar-plus"></i>
            <span>Tambah Jadwal</span>
        </a>
    </div>
@endsection

@section('content')
<!-- Filter bar & View Mode Toggles -->
<div class="card-custom">
    <div class="card-custom-header flex-column flex-lg-row gap-3">
        <div class="d-flex align-items-center gap-2">
            <h5 class="card-custom-title mb-0">
                <i class="bi bi-calendar3 text-primary"></i> Kelola Jadwal Pembelajaran
            </h5>
            <!-- Toggle Tabel / Kalender -->
            <div class="btn-group btn-group-sm ms-3" role="group">
                <a href="{{ route('jadwal.index', array_merge(request()->all(), ['view' => 'tabel'])) }}" class="btn {{ $viewMode == 'tabel' ? 'btn-primary' : 'btn-outline-primary' }}">
                    <i class="bi bi-table me-1"></i> Tampilan Tabel
                </a>
                <a href="{{ route('jadwal.index', array_merge(request()->all(), ['view' => 'kalender'])) }}" class="btn {{ $viewMode == 'kalender' ? 'btn-primary' : 'btn-outline-primary' }}">
                    <i class="bi bi-calendar-month me-1"></i> Tampilan Kalender
                </a>
            </div>
        </div>

        <!-- Filter Form -->
        <form method="GET" action="{{ route('jadwal.index') }}" class="d-flex flex-wrap align-items-center gap-2">
            <input type="hidden" name="view" value="{{ $viewMode }}">
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
                <option value="Terjadwal" {{ $status == 'Terjadwal' ? 'selected' : '' }}>Terjadwal</option>
                <option value="Terlaksana" {{ $status == 'Terlaksana' ? 'selected' : '' }}>Terlaksana</option>
                <option value="Dibatalkan" {{ $status == 'Dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
            </select>
            <button type="submit" class="btn btn-sm btn-primary">
                <i class="bi bi-filter"></i> Filter
            </button>
            @if(request()->has('status') || request()->has('pelatihan_id'))
                <a href="{{ route('jadwal.index') }}" class="btn btn-sm btn-outline-secondary" title="Reset Filter">
                    <i class="bi bi-x-circle"></i>
                </a>
            @endif
        </form>
    </div>

    @if($viewMode === 'kalender')
        <!-- KALENDER BULANAN VIEW -->
        <div class="p-4">
            <div class="alert alert-info py-2 px-3 small d-flex align-items-center mb-3">
                <i class="bi bi-info-circle me-2 fs-5"></i>
                <div>Kalender menampilkan seluruh jadwal pada periode <strong>{{ (new \App\Services\JPRecapitulationService)->getNamaBulan($bulan) }} {{ $tahun }}</strong>. Klik jadwal untuk melihat detail atau mencatat realisasi.</div>
            </div>

            @php
                $startOfMonth = \Carbon\Carbon::createFromDate($tahun, $bulan, 1);
                $daysInMonth = $startOfMonth->daysInMonth;
                $firstDayOfWeek = $startOfMonth->dayOfWeekIso; // 1 (Senin) - 7 (Minggu)
            @endphp

            <div class="table-responsive">
                <table class="table table-bordered text-center align-middle" style="table-layout: fixed;">
                    <thead>
                        <tr class="table-light">
                            <th style="width: 14.28%;">Senin</th>
                            <th style="width: 14.28%;">Selasa</th>
                            <th style="width: 14.28%;">Rabu</th>
                            <th style="width: 14.28%;">Kamis</th>
                            <th style="width: 14.28%;">Jumat</th>
                            <th style="width: 14.28%;" class="text-danger">Sabtu</th>
                            <th style="width: 14.28%;" class="text-danger">Minggu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $currentDay = 1;
                            $dayCounter = 1;
                        @endphp
                        @while($currentDay <= $daysInMonth)
                            <tr style="height: 110px;">
                                @for($col = 1; $col <= 7; $col++)
                                    @if(($dayCounter < $firstDayOfWeek && $currentDay == 1) || $currentDay > $daysInMonth)
                                        <td class="bg-light text-muted"></td>
                                    @else
                                        @php
                                            $dateStr = sprintf('%04d-%02d-%02d', $tahun, $bulan, $currentDay);
                                            $events = $allJadwalsForCalendar->filter(function($j) use ($dateStr) {
                                                return $j->tanggal->format('Y-m-d') === $dateStr;
                                            });
                                            $isToday = $dateStr === date('Y-m-d');
                                        @endphp
                                        <td class="text-start align-top p-1 {{ $isToday ? 'bg-light-blue border-primary' : '' }}" style="position: relative;">
                                            <div class="d-flex justify-content-between align-items-center mb-1 px-1">
                                                <span class="fw-bold {{ $col >= 6 ? 'text-danger' : 'text-dark' }}" style="font-size: 0.85rem;">
                                                    {{ $currentDay }}
                                                </span>
                                                @if($isToday)
                                                    <span class="badge bg-primary" style="font-size: 0.6rem;">Hari Ini</span>
                                                @endif
                                            </div>
                                            <div class="d-flex flex-column gap-1 overflow-auto" style="max-height: 85px;">
                                                @foreach($events as $ev)
                                                    @php
                                                        $bgBadge = $ev->status === 'Terlaksana' ? 'bg-success' : ($ev->status === 'Terjadwal' ? 'bg-primary' : 'bg-danger');
                                                    @endphp
                                                    <div class="badge {{ $bgBadge }} text-start p-1 text-truncate" style="font-size: 0.7rem; font-weight: 500;" title="{{ $ev->mataPelatihan->nama_mata_pelatihan }} - {{ $ev->penugasanPengajar ? $ev->penugasanPengajar->pengajar_nama : 'Pengajar Belum Ditugaskan' }}">
                                                        <div class="fw-bold">{{ substr($ev->jam_mulai, 0, 5) }} ({{ $ev->jp_rencana }} JP)</div>
                                                        <div class="text-truncate">{{ $ev->mataPelatihan->nama_mata_pelatihan }}</div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </td>
                                        @php $currentDay++; @endphp
                                    @endif
                                    @php $dayCounter++; @endphp
                                @endfor
                            </tr>
                        @endwhile
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <!-- TABEL VIEW -->
        <div class="table-responsive">
            <table class="table table-custom">
                <thead>
                    <tr>
                        <th style="width: 50px;">No.</th>
                        <th>Tanggal & Jam</th>
                        <th>Program & Angkatan</th>
                        <th>Mata Pelatihan</th>
                        <th>Pengajar</th>
                        <th class="text-center">JP Rencana</th>
                        <th class="text-center">Status Jadwal</th>
                        <th class="text-center" style="width: 170px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jadwals as $index => $jdw)
                        <tr>
                            <td class="text-center">{{ $jadwals->firstItem() + $index }}</td>
                            <td>
                                <div class="fw-bold text-dark">{{ $jdw->tanggal->format('d/m/Y') }}</div>
                                <div class="text-muted small">
                                    <i class="bi bi-clock me-1"></i>{{ substr($jdw->jam_mulai, 0, 5) }} - {{ substr($jdw->jam_selesai, 0, 5) }} WIB
                                </div>
                                @if($jdw->sesi_ke)
                                    <span class="badge bg-light text-secondary border mt-1" style="font-size: 0.65rem;">Sesi {{ $jdw->sesi_ke }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $jdw->mataPelatihan->angkatan->pelatihan->nama_pelatihan }}</div>
                                <span class="badge bg-light text-primary border" style="font-size: 0.7rem;">{{ $jdw->mataPelatihan->angkatan->nomor_angkatan }}</span>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $jdw->mataPelatihan->nama_mata_pelatihan }}</div>
                                <div class="text-muted small"><i class="bi bi-geo-alt me-1"></i>{{ $jdw->ruangan ?? 'Ruang Kelas' }} &bull; {{ $jdw->metode }}</div>
                            </td>
                            <td>
                                @if($jdw->penugasanPengajar)
                                    <div class="fw-bold text-dark">{{ $jdw->penugasanPengajar->pengajar_nama }}</div>
                                    <span class="badge bg-{{ $jdw->penugasanPengajar->jenis_pengajar == 'Widyaiswara' ? 'primary' : 'secondary' }}-subtle text-dark border" style="font-size: 0.68rem;">
                                        {{ $jdw->penugasanPengajar->jenis_pengajar }} ({{ $jdw->penugasanPengajar->peran }})
                                    </span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning-emphasis border">Belum Ditugaskan</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-primary border fw-bold px-2 py-1 fs-6">{{ $jdw->jp_rencana }} JP</span>
                            </td>
                            <td class="text-center">
                                @php
                                    $badgeSt = match($jdw->status) {
                                        'Terlaksana' => 'success',
                                        'Terjadwal' => 'warning text-dark',
                                        'Dibatalkan' => 'danger',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge bg-{{ $badgeSt }} badge-custom">{{ $jdw->status }}</span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm" role="group">
                                    <!-- Tombol Absensi Pertemuan -->
                                    @php
                                        $attSession = $jdw->attendanceSessions->first();
                                    @endphp
                                    @if($attSession)
                                        <a href="{{ route('attendance-sessions.show', $attSession->id) }}" class="btn btn-sm btn-primary" title="Buka Presensi Kelas Pertemuan Ini">
                                            <i class="bi bi-person-check-fill"></i> Absensi
                                        </a>
                                    @else
                                        <a href="{{ route('attendance-sessions.create', ['jadwal_id' => $jdw->id]) }}" class="btn btn-sm btn-outline-primary" title="Buka Sesi Absensi Baru">
                                            <i class="bi bi-calendar-check"></i> Absensi
                                        </a>
                                    @endif

                                    <!-- Tombol Realisasi -->
                                    <a href="{{ route('realisasi.create', ['jadwal_id' => $jdw->id]) }}" class="btn btn-sm btn-success" title="Konfirmasi Realisasi Kegiatan">
                                        <i class="bi bi-check2-circle"></i> Realisasi
                                    </a>
                                    <a href="{{ route('jadwal.edit', $jdw) }}" class="btn btn-outline-warning" title="Edit Jadwal">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-danger" title="Hapus Jadwal" onclick="confirmDelete('del-jdw-{{ $jdw->id }}', 'Hapus jadwal pembelajaran ini?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                                <form id="del-jdw-{{ $jdw->id }}" action="{{ route('jadwal.destroy', $jdw) }}" method="POST" class="d-none">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                <i class="bi bi-calendar-x fs-3 d-block mb-2"></i>
                                Tidak ada jadwal pelatihan pada periode atau filter yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($jadwals->hasPages())
            <div class="table-pagination-footer">
                <span class="text-muted small">
                    Menampilkan {{ $jadwals->firstItem() }} - {{ $jadwals->lastItem() }} dari {{ $jadwals->total() }} data
                </span>
                <div>
                    {{ $jadwals->links() }}
                </div>
            </div>
        @endif
    @endif
</div>
@endsection
