@extends('layouts.app')

@section('title', 'Manajemen Kode Akses & PIN Peserta')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1">Kode Akses & PIN Peserta Diklat</h4>
        <div class="text-muted small">Kelola akun portal peserta, terbitkan kode akses mandiri, dan reset PIN</div>
    </div>
    <form action="{{ route('participant-access.massal') }}" method="POST" onsubmit="return confirm('Terbitkan kode akses & PIN acak untuk seluruh peserta yang belum memilikinya?');">
        @csrf
        <input type="hidden" name="angkatan_id" value="{{ $angkatanId }}">
        <input type="hidden" name="kelas_mapel_id" value="{{ $kelasMapelId }}">
        <button type="submit" class="btn btn-primary d-flex align-items-center gap-2 rounded-3 shadow-sm">
            <i class="bi bi-key-fill"></i>
            <span>Terbitkan Akses Massal</span>
        </button>
    </form>
</div>

<!-- Card Pengaturan Hak Akses Modul Peserta -->
<div class="card border-0 shadow-sm rounded-3 p-4 mb-4 bg-white">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h6 class="fw-bold text-dark mb-1"><i class="bi bi-toggles2 text-primary me-2"></i>Kelola Hak Akses Modul Peserta Diklat</h6>
            <div class="text-muted small">Pilih modul apa saja yang diizinkan tampil dan diakses pada portal peserta. Admin tinggal centang modul yang ingin diberikan ke peserta.</div>
        </div>
    </div>
    <form action="{{ route('participant-access.modules') }}" method="POST">
        @csrf
        <div class="row g-3">
            @php
                $availableList = [
                    'presensi' => ['title' => 'Presensi Sesi', 'icon' => 'bi-qr-code', 'desc' => 'Absen mandiri tiap sesi'],
                    'tugas'    => ['title' => 'Tugas Diklat', 'icon' => 'bi-check-square-fill', 'desc' => 'Tugas individu & kelompok'],
                    'materi'   => ['title' => 'Materi Pembelajaran', 'icon' => 'bi-journal-text', 'desc' => 'Download materi & slide'],
                    'pretest'  => ['title' => 'Pre Test', 'icon' => 'bi-question-circle', 'desc' => 'Ujian awal sebelum materi'],
                    'kuis'     => ['title' => 'Kuis', 'icon' => 'bi-chat-left-dots-fill', 'desc' => 'Kuis latihan pemahaman'],
                    'postest'  => ['title' => 'Pos Test', 'icon' => 'bi-file-earmark-check-fill', 'desc' => 'Evaluasi akhir materi'],
                    'ujian'    => ['title' => 'Ujian', 'icon' => 'bi-pencil-square', 'desc' => 'Ujian komprehensif diklat'],
                    'seminar'  => ['title' => 'Seminar', 'icon' => 'bi-easel-fill', 'desc' => 'Jadwal seminar / rancangan'],
                ];
            @endphp
            @foreach($availableList as $key => $mod)
            <div class="col-6 col-md-3">
                <div class="border rounded-3 p-3 h-100 {{ in_array($key, $activeModules) ? 'bg-primary-subtle border-primary' : 'bg-light' }}">
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" name="modules[]" value="{{ $key }}" id="mod_{{ $key }}" {{ in_array($key, $activeModules) ? 'checked' : '' }}>
                        <label class="form-check-label fw-bold text-dark small" for="mod_{{ $key }}">
                            <i class="bi {{ $mod['icon'] }} me-1"></i> {{ $mod['title'] }}
                        </label>
                    </div>
                    <div class="text-muted small" style="font-size: 0.72rem;">{{ $mod['desc'] }}</div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="mt-3 text-end">
            <button type="submit" class="btn btn-primary btn-sm px-4 fw-semibold rounded-2">
                <i class="bi bi-save me-1"></i> Simpan Hak Akses Peserta
            </button>
        </div>
    </form>
</div>

<!-- Filter Bar -->
<div class="card border-0 shadow-sm rounded-3 p-3 mb-4 bg-white">
    <form method="GET" action="{{ route('participant-access.index') }}" class="row g-2 align-items-end">
        <div class="col-md-4">
            <label class="form-label small fw-semibold text-muted mb-1">CARI PESERTA / KODE</label>
            <input type="text" name="search" value="{{ $search }}" class="form-control form-control-sm" placeholder="Ketik nama, NIP, atau kode peserta...">
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-semibold text-muted mb-1">ANGKATAN</label>
            <select name="angkatan_id" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Semua Angkatan</option>
                @foreach($angkatans as $a)
                    <option value="{{ $a->id }}" {{ $angkatanId == $a->id ? 'selected' : '' }}>
                        {{ $a->nomor_angkatan }} ({{ $a->pelatihan->nama_pelatihan ?? '-' }})
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-semibold text-muted mb-1">KELAS</label>
            <select name="kelas_mapel_id" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Semua Kelas</option>
                @foreach($kelasMapels as $km)
                    <option value="{{ $km->id }}" {{ $kelasMapelId == $km->id ? 'selected' : '' }}>
                        {{ $km->nama_kelas }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2 d-flex gap-1">
            <button type="submit" class="btn btn-sm btn-primary flex-grow-1">Filter</button>
            <a href="{{ route('participant-access.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
        </div>
    </form>
</div>

<!-- Table of Participants -->
<div class="card bg-white border-0 shadow-sm rounded-3 p-0 mb-5 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
            <thead class="table-light">
                <tr>
                    <th style="width: 40px;">No</th>
                    <th>Nama Peserta</th>
                    <th>NIP & Instansi</th>
                    <th>Kelas / Angkatan</th>
                    <th>Kode Peserta</th>
                    <th class="text-center">Status Akses</th>
                    <th>Login Terakhir</th>
                    <th class="text-center" style="width: 140px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pesertas as $pst)
                    @php $acc = $pst->accessCode; @endphp
                    <tr>
                        <td>{{ $loop->iteration + ($pesertas->currentPage() - 1) * $pesertas->perPage() }}</td>
                        <td class="fw-bold text-dark">{{ $pst->nama }}</td>
                        <td>
                            <div>{{ $pst->nip }}</div>
                            <small class="text-muted">{{ $pst->instansi ?: '-' }}</small>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark small">{{ $pst->kelasMapel->nama_kelas ?? '-' }}</div>
                            <small class="text-muted">{{ $pst->angkatan->nomor_angkatan ?? '-' }}</small>
                        </td>
                        <td>
                            @if($acc)
                                <span class="badge bg-light text-primary border font-monospace fs-6 px-2 py-1">
                                    {{ $acc->kode_peserta }}
                                </span>
                            @else
                                <span class="badge bg-light text-danger border">Belum Diterbitkan</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($acc && $acc->is_active)
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">Aktif</span>
                            @elseif($acc)
                                <span class="badge bg-danger-subtle text-danger border rounded-pill">Nonaktif</span>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                        <td>
                            @if($acc && $acc->last_login_at)
                                <small class="text-muted">{{ $acc->last_login_at->format('d/m/Y H:i') }}</small>
                            @else
                                <small class="text-muted fst-italic">Belum pernah login</small>
                            @endif
                        </td>
                        <td class="text-center">
                            <form action="{{ route('participant-access.reset', $pst->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Reset PIN akses untuk peserta ini? PIN baru akan dihasilkan.');">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-warning text-dark rounded-pill px-2 py-1" title="Reset PIN">
                                    <i class="bi bi-arrow-clockwise"></i> Reset PIN
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">Tidak ada data peserta diklat.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">
    {{ $pesertas->links('vendor.pagination.bootstrap-5') }}
</div>
@endsection
