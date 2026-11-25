@extends('portal.layout')

@section('title', 'Dashboard Diklat')
@section('page_title', 'DASHBOARD DIKLAT')
@section('breadcrumb', 'Dashboard Diklat')

@section('styles')
<style>
    .presensi-choice-label {
        border: 1.5px solid #E2E8F0;
        background-color: #FFFFFF;
        color: #1E293B;
        border-radius: 10px;
        padding: 1rem 0.75rem;
        cursor: pointer;
        transition: all 0.15s ease-in-out;
    }
    .presensi-choice-label:hover {
        border-color: #94A3B8;
        background-color: #F8FAFC;
    }
    .presensi-choice-label .choice-icon {
        font-size: 1.75rem;
        color: #1B4965;
        margin-bottom: 0.35rem;
        transition: color 0.15s ease-in-out;
    }
    .presensi-choice-label .choice-title {
        font-weight: 700;
        font-size: 0.92rem;
        color: #1E293B;
        line-height: 1.2;
    }
    .presensi-choice-label .choice-desc {
        font-size: 0.7rem;
        color: #64748B;
        margin-top: 2px;
    }
    /* Pilihan Aktif Sesuai Sketsa User: Solid Dark Blue Petrol */
    .btn-check:checked + .presensi-choice-label {
        background-color: #1B4965 !important;
        border-color: #1B4965 !important;
        box-shadow: 0 4px 14px rgba(27, 73, 101, 0.28);
    }
    .btn-check:checked + .presensi-choice-label .choice-icon,
    .btn-check:checked + .presensi-choice-label .choice-title,
    .btn-check:checked + .presensi-choice-label .choice-desc {
        color: #FFFFFF !important;
    }

    .kclc-card {
        background: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 10px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .session-item-card {
        background-color: #F0F7FF;
        border: 1px solid #BAE6FD;
        border-radius: 10px;
        padding: 16px;
        margin-bottom: 12px;
        transition: all 0.2s ease;
    }

    .session-item-card:hover {
        border-color: #38BDF8;
        box-shadow: 0 4px 12px rgba(2, 132, 199, 0.08);
    }

    .badge-activity-done {
        background-color: #DCFCE7;
        color: #15803D;
        font-weight: 500;
        font-size: 0.72rem;
        padding: 4px 8px;
        border-radius: 4px;
    }

    .badge-activity-pending {
        background-color: #FFE4E6;
        color: #E11D48;
        font-weight: 500;
        font-size: 0.72rem;
        padding: 4px 8px;
        border-radius: 4px;
    }

    .badge-activity-info {
        background-color: #E0E7FF;
        color: #4338CA;
        font-weight: 500;
        font-size: 0.72rem;
        padding: 4px 8px;
        border-radius: 4px;
    }
</style>
@endsection

@section('content')

<div class="row g-3">
    <!-- ============================================================== -->
    <!-- KOLOM KIRI (7-8 COLS): PELATIHAN, FILTER, & SESI PERTEMUAN     -->
    <!-- ============================================================== -->
    <div class="col-lg-8">
        @if(!$angkatan)
            <!-- Tampilan Jika Peserta Belum Ditempatkan / Belum Terdaftar di Angkatan Pelatihan -->
            <div class="kclc-card p-4 mb-4 border-0 shadow-sm rounded-4">
                <div class="text-center py-2 mb-3">
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px; background-color: #FEF3C7; color: #D97706;">
                        <i class="bi bi-clock-history fs-2"></i>
                    </div>
                    <div class="mb-2">
                        <span class="badge bg-warning-subtle text-warning border px-3 py-1 rounded-pill fw-bold" style="font-size: 0.8rem;">
                            <i class="bi bi-hourglass-split me-1"></i> STATUS: MENUNGGU PENUGASAN KELAS
                        </span>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Akun Peserta Terdaftar - Menunggu Penetapan Angkatan</h5>
                    <p class="text-muted small mb-0" style="max-width: 600px; margin: 0 auto; line-height: 1.6;">
                        Selamat datang, <strong>{{ $peserta->nama }}</strong>! Akun peserta Anda telah aktif di sistem SIR-JP. Anda belum ditempatkan dalam kelas atau angkatan pelatihan aktif oleh Penyelenggara Diklat.
                    </p>
                </div>

                <!-- Kartu Verifikasi Data Peserta -->
                <div class="card border rounded-3 p-3 bg-light shadow-none mx-auto mb-4" style="max-width: 620px;">
                    <div class="d-flex align-items-center justify-content-between mb-2 pb-2 border-bottom">
                        <span class="small fw-bold text-secondary text-uppercase" style="letter-spacing: 0.5px;">
                            <i class="bi bi-person-badge me-1"></i> Identitas Peserta Diklat
                        </span>
                        <span class="badge bg-success-subtle text-success small">Akun Siap Digunakan</span>
                    </div>
                    <div class="row g-2 small text-dark">
                        <div class="col-sm-6">
                            <span class="text-muted d-block" style="font-size: 0.75rem;">NAMA LENGKAP:</span>
                            <span class="fw-semibold">{{ $peserta->nama }}</span>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted d-block" style="font-size: 0.75rem;">NIK / NIP:</span>
                            <span class="fw-semibold">{{ $peserta->nip }}</span>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted d-block" style="font-size: 0.75rem;">INSTANSI ASAL:</span>
                            <span>{{ $peserta->instansi ?? 'PPSDM Ketenagakerjaan' }}</span>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted d-block" style="font-size: 0.75rem;">PENETAPAN KELAS:</span>
                            <span class="text-warning fw-semibold"><i class="bi bi-clock me-1"></i>Menunggu Input Panitia</span>
                        </div>
                    </div>
                </div>

                <!-- Opsi Gabung via Kode Token / PIN Pelatihan Resmi dari Panitia -->
                <div class="card border rounded-3 p-3 bg-white shadow-sm mx-auto" style="max-width: 620px;">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-primary" style="width: 28px; height: 28px; background: #E0F2FE;">
                            <i class="bi bi-key-fill small"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-0 small">Memiliki Kode Token / Akses Pelatihan?</h6>
                    </div>
                    <p class="text-muted small mb-3" style="font-size: 0.78rem;">
                        Jika panitia penyelenggara telah mencantumkan Kode Pelatihan / Token Registrasi pada Surat Pemanggilan Resmi Anda, masukkan kode tersebut di bawah ini untuk bergabung langsung ke kelas Anda:
                    </p>

                    <form action="{{ route('portal.pilih-pelatihan') }}" method="POST">
                        @csrf
                        <div class="input-group mb-2">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-ticket-perforated"></i></span>
                            <input type="text" 
                                   name="kode_token" 
                                   class="form-control form-control-sm border-start-0" 
                                   placeholder="Contoh: PKP-2026 atau MOOC-12345" 
                                   autocomplete="off" 
                                   required>
                            <button type="submit" class="btn btn-primary btn-sm px-3 fw-semibold" style="background-color: #183B65; border-color: #183B65;">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Gabung ke Kelas
                            </button>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <span class="text-muted small" style="font-size: 0.72rem;">
                                <i class="bi bi-shield-lock text-success me-1"></i>Hanya kode resmi yang dapat divalidasi.
                            </span>
                            <span class="text-muted small" style="font-size: 0.72rem;">
                                Tidak punya token? Hubungi Panitia Diklat.
                            </span>
                        </div>
                    </form>
                </div>
            </div>
        @else
            <!-- 1. Card Banner Info Pelatihan Nyata Sesuai Gambar KCLC -->
            <div class="kclc-card p-3 mb-3">
                <div class="d-flex justify-content-between align-items-start gap-2 flex-wrap">
                    <div>
                        <h6 class="fw-bold text-dark mb-1" style="font-size: 1.05rem;">
                            Nama Pelatihan : {{ $pelatihan->nama_pelatihan ?? $angkatan->nomor_angkatan }}
                        </h6>
                        <div class="text-muted small">
                            Diselenggarakan Tanggal 
                            <strong>{{ $angkatan->tanggal_mulai ? $angkatan->tanggal_mulai->translatedFormat('d F Y') : '-' }}</strong> s/d 
                            <strong>{{ $angkatan->tanggal_selesai ? $angkatan->tanggal_selesai->translatedFormat('d F Y') : '-' }}</strong>
                            &bull; Angkatan: <span class="badge bg-primary-subtle text-primary">{{ $angkatan->nomor_angkatan }}</span>
                        </div>
                    </div>
                    <form action="{{ route('portal.ganti-pelatihan') }}" method="POST" class="m-0" data-confirm="Apakah Anda yakin ingin membatalkan pendaftaran kelas ini dan keluar dari pelatihan?">
                        @csrf
                        <button type="submit" class="btn btn-outline-secondary btn-sm rounded-2 py-1 px-2" title="Ganti Pelatihan" style="font-size: 0.78rem;">
                            <i class="bi bi-arrow-left-right me-1"></i> Ganti Pelatihan
                        </button>
                    </form>
                </div>
            </div>

            <!-- 2. Card Filter Jadwal Sesuai Gambar KCLC -->
            <div class="kclc-card p-3 mb-3">
                <form action="{{ route('portal.dashboard') }}" method="GET" class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="fw-bold text-dark me-2 small">Filter Jadwal</span>
                    
                    <input type="date" 
                           name="filter_tanggal" 
                           class="form-control form-control-sm text-muted" 
                           style="width: 150px;" 
                           value="{{ request('filter_tanggal') }}">

                    <select name="filter_mapel" class="form-select form-select-sm" style="width: 170px;">
                        <option value="">-- Pilih --</option>
                        @foreach($mataPelatihans as $mp)
                            <option value="{{ $mp->id }}" {{ request('filter_mapel') == $mp->id ? 'selected' : '' }}>
                                {{ $mp->nama_mata_pelatihan }}
                            </option>
                        @endforeach
                    </select>

                    <button type="submit" class="btn btn-primary btn-sm px-3 fw-semibold">
                        Cari
                    </button>
                    <a href="{{ route('portal.dashboard', ['filter_tanggal' => now()->toDateString()]) }}" 
                       class="btn btn-sm {{ request('filter_tanggal') == now()->toDateString() ? 'btn-primary' : 'btn-outline-primary' }} px-3 fw-semibold">
                        <i class="bi bi-calendar-check me-1"></i> Hari Ini
                    </a>
                    <a href="{{ route('portal.dashboard') }}" class="btn btn-outline-warning btn-sm px-3 fw-semibold text-dark">
                        Semua
                    </a>
                </form>
            </div>

            <!-- 3. Header Pelatihan Sesuai Gambar KCLC -->
            <div class="kclc-card p-3 mb-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-2 d-flex align-items-center justify-content-center text-white flex-shrink-0" 
                         style="width: 52px; height: 52px; background-color: #1E3A8A;">
                        <i class="bi bi-book fs-3"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-0" style="font-size: 1.15rem;">
                            {{ $pelatihan->nama_pelatihan ?? $angkatan->nomor_angkatan }}
                        </h5>
                        <div class="text-muted small mt-1">
                            <i class="bi bi-compass text-secondary me-1"></i> {{ $pelatihan->kategori ?? 'Pelatihan Diklat' }} &bull; {{ $angkatan->nomor_angkatan }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. Daftar Pertemuan / Sesi Presensi Sesuai Gambar KCLC -->
            <div class="mb-4">
                @forelse($sessionsList as $idx => $sess)
                    @php
                        $presensiUser = $presensisToday->get($sess->id);
                        $pengajarNama = $sess->jadwal->penugasanPengajar->pengajar_nama 
                            ?? ($sess->jadwal->widyaiswara->nama 
                            ?? ($sess->creator->name 
                            ?? ($sess->jadwal->pengajar ?? 'Pengajar')));
                        $pengajarPeran = $sess->jadwal->penugasanPengajar->peran 
                            ?? ($sess->jadwal->penugasanPengajar->jenis_pengajar 
                            ?? 'Pengajar');
                        $ruanganNama = $sess->kelasMapel->ruangan->nama_ruangan 
                            ?? ($sess->jadwal->ruangan ?? 'Ruang Kelas');
                        $zoomMeetingUrl = $sess->link_meeting 
                            ?: (\Illuminate\Support\Str::startsWith($sess->jadwal->ruangan ?? '', ['http://', 'https://']) ? $sess->jadwal->ruangan : null);
                        $isSessOpen = $sess->isBuka();
                    @endphp

                    <div class="session-item-card">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                            <!-- Left Details -->
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-2 d-flex align-items-center justify-content-center text-white flex-shrink-0 shadow-sm" 
                                     style="width: 44px; height: 44px; background-color: #0284C7;">
                                    <i class="bi bi-calendar-event fs-4"></i>
                                </div>
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <h6 class="fw-bold text-dark mb-0">Pertemuan {{ $idx + 1 }}</h6>
                                        <span class="badge" style="background-color: #E0E7FF; color: #4338CA; font-size: 0.72rem; padding: 2px 8px; border-radius: 4px;">
                                             {{ $sess->jadwal->jp ?? 1 }} JP
                                        </span>
                                    </div>
                                    <div class="text-muted small mb-1">
                                        <i class="bi bi-calendar3 text-secondary me-1"></i> {{ $sess->tanggal ? $sess->tanggal->translatedFormat('l, Y-m-d') : '-' }} &bull; 
                                        <i class="bi bi-clock text-secondary me-1"></i> {{ substr($sess->jam_mulai, 0, 5) }} - {{ substr($sess->jam_selesai, 0, 5) }} WIB
                                    </div>
                                    <div class="small text-muted d-flex align-items-center gap-2 flex-wrap">
                                        <span>
                                            <i class="bi bi-person text-secondary me-1"></i> {{ $pengajarNama }}
                                        </span>
                                        <span class="badge bg-secondary-subtle text-secondary small py-0 px-2" style="font-size: 0.68rem;">{{ strtolower($pengajarPeran) }}</span>
                                    </div>
                                    
                                    @if($zoomMeetingUrl)
                                        <div class="mt-2 d-flex align-items-center gap-2 flex-wrap">
                                            <a href="{{ $zoomMeetingUrl }}" target="_blank" class="btn btn-sm btn-primary rounded-pill px-3 py-1 shadow-sm d-inline-flex align-items-center gap-1 text-white fw-semibold" style="font-size: 0.82rem; background: linear-gradient(135deg, #0284c7 0%, #2563eb 100%);">
                                                <i class="bi bi-camera-video-fill"></i>
                                                <span>Gabung Zoom / Kelas Daring</span>
                                                <i class="bi bi-box-arrow-up-right ms-1 small"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-2 py-1" style="font-size: 0.78rem;" title="Salin Tautan Zoom" onclick="navigator.clipboard.writeText('{{ $zoomMeetingUrl }}'); Swal.fire({toast: true, position: 'top-end', icon: 'success', title: 'Tautan Zoom tersalin!', showConfirmButton: false, timer: 1500});">
                                                <i class="bi bi-copy me-1"></i> Salin
                                            </button>
                                        </div>
                                    @else
                                        <div class="small text-primary mt-1">
                                            <i class="bi bi-geo-alt me-1"></i> {{ $ruanganNama }}
                                            <i class="bi bi-copy text-muted ms-1 cursor-pointer" title="Salin Info Ruangan" onclick="navigator.clipboard.writeText('Pertemuan {{ $idx + 1 }}: {{ $ruanganNama }}'); Swal.fire({toast: true, position: 'top-end', icon: 'success', title: 'Tersalin!', showConfirmButton: false, timer: 1200});"></i>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Right Action: Tombol Presensi -->
                            <div>
                                @if($presensiUser)
                                    <div class="d-flex align-items-center gap-1">
                                        <button type="button" class="btn btn-success btn-sm px-3 py-2 rounded-2 fw-semibold shadow-sm d-flex align-items-center gap-1" disabled>
                                            <i class="bi bi-check2-circle fs-6"></i>
                                            <span>{{ $presensiUser->status_kehadiran }} ({{ $presensiUser->waktu_check_in ? $presensiUser->waktu_check_in->format('H:i') : 'OK' }})</span>
                                        </button>
                                        @if($presensiUser->bukti_file)
                                            <a href="{{ route('portal.presensi.bukti', $presensiUser->id) }}" target="_blank" class="btn btn-outline-secondary btn-sm px-2 py-2 rounded-2" title="Lihat Bukti Foto/PDF">
                                                <i class="bi bi-paperclip"></i>
                                            </a>
                                        @endif
                                    </div>
                                @elseif($isSessOpen)
                                    <button type="button" class="btn btn-primary btn-sm px-3 py-2 rounded-2 fw-semibold shadow-sm d-flex align-items-center gap-2" 
                                            data-bs-toggle="modal" data-bs-target="#modalPresensi{{ $sess->id }}">
                                        <i class="bi bi-person-check fs-6"></i>
                                        <span>Presensi</span>
                                    </button>
                                @elseif($sess->tanggal && $sess->tanggal->isFuture())
                                    <button type="button" class="btn btn-secondary btn-sm px-3 py-2 rounded-2 fw-semibold opacity-75" disabled title="Jadwal pertemuan belum dimulai">
                                        <i class="bi bi-hourglass-split me-1"></i> Belum Dimulai
                                    </button>
                                @elseif($sess->status !== 'Aktif' || ($sess->tanggal && $sess->tanggal->isPast() && !$sess->tanggal->isToday()))
                                    <button type="button" class="btn btn-secondary btn-sm px-3 py-2 rounded-2 fw-semibold opacity-75" disabled title="Sesi presensi ditutup atau jadwal telah lewat">
                                        <i class="bi bi-lock me-1"></i> Ditutup
                                    </button>
                                @else
                                    <button type="button" class="btn btn-secondary btn-sm px-3 py-2 rounded-2 fw-semibold opacity-75" disabled title="Presensi belum dibuka oleh pengajar">
                                        <i class="bi bi-clock me-1"></i> Presensi Belum Dibuka
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Modal Presensi Langsung Sesuai Template User (Pilihan Hadir, Keterangan, Bukti Foto/PDF) -->
                    @if(!$presensiUser && $isSessOpen)
                <div class="modal fade" id="modalPresensi{{ $sess->id }}" tabindex="-1" aria-labelledby="modalPresensiLabel{{ $sess->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                            <!-- Modal Header Bersih & Simple Sesuai Permintaan (Tidak Mencolok) -->
                            <div class="modal-header bg-white py-3 px-4 border-bottom">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: #F1F5F9; color: #1B4965;">
                                        <i class="bi bi-person-check fs-6"></i>
                                    </div>
                                    <h6 class="modal-title fw-bold mb-0 text-dark" id="modalPresensiLabel{{ $sess->id }}">
                                        Form Presensi Peserta: Pertemuan {{ $idx + 1 }}
                                    </h6>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>

                            <form action="{{ route('portal.presensi.direct.post', $sess->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="modal-body p-4">
                                    <!-- Info Sesi Pembelajaran -->
                                    <div class="p-3 rounded-3 mb-4 border d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2" style="background-color: #F8FAFC;">
                                        <div>
                                            <div class="fw-bold text-dark fs-6">{{ $sess->judul_sesi }}</div>
                                            <div class="text-secondary small mt-1">
                                                <i class="bi bi-calendar-event me-1"></i> {{ \Carbon\Carbon::parse($sess->tanggal)->translatedFormat('l, d F Y') }} &bull;
                                                <i class="bi bi-clock me-1"></i> {{ substr($sess->jam_mulai, 0, 5) }} - {{ substr($sess->jam_selesai, 0, 5) }} WIB
                                            </div>
                                        </div>
                                        <div class="badge px-3 py-2 rounded-pill bg-light text-dark border align-self-start align-self-sm-center">
                                            <i class="bi bi-geo-alt me-1 text-primary"></i> {{ $ruanganNama }}
                                        </div>
                                    </div>

                                    <!-- 4 KOTAK STATUS UNIFORM DARK BLUE PETROL SESUAI GAMBAR TEMPLATE USER -->
                                    <div class="mb-4">
                                        <label class="form-label fw-bold text-dark small text-uppercase mb-2" style="letter-spacing: 0.5px;">
                                            1. Pilih Status Kehadiran <span class="text-danger">*</span>
                                        </label>
                                        <div class="row g-2">
                                            <!-- Box 1: Hadir -->
                                            <div class="col-6 col-md-3">
                                                <input type="radio" class="btn-check" name="status_kehadiran" id="statusHadir{{ $sess->id }}" value="Hadir" checked autocomplete="off">
                                                <label class="presensi-choice-label w-100 text-center d-flex flex-column align-items-center justify-content-center h-100" for="statusHadir{{ $sess->id }}">
                                                    <i class="bi bi-check-circle-fill choice-icon"></i>
                                                    <span class="choice-title">Hadir</span>
                                                    <span class="choice-desc">Mengikuti Sesi</span>
                                                </label>
                                            </div>

                                            <!-- Box 2: Sakit -->
                                            <div class="col-6 col-md-3">
                                                <input type="radio" class="btn-check" name="status_kehadiran" id="statusSakit{{ $sess->id }}" value="Sakit" autocomplete="off">
                                                <label class="presensi-choice-label w-100 text-center d-flex flex-column align-items-center justify-content-center h-100" for="statusSakit{{ $sess->id }}">
                                                    <i class="bi bi-heart-pulse-fill choice-icon"></i>
                                                    <span class="choice-title">Sakit</span>
                                                    <span class="choice-desc">Kondisi Sakit</span>
                                                </label>
                                            </div>

                                            <!-- Box 3: Izin -->
                                            <div class="col-6 col-md-3">
                                                <input type="radio" class="btn-check" name="status_kehadiran" id="statusIzin{{ $sess->id }}" value="Izin" autocomplete="off">
                                                <label class="presensi-choice-label w-100 text-center d-flex flex-column align-items-center justify-content-center h-100" for="statusIzin{{ $sess->id }}">
                                                    <i class="bi bi-envelope-paper-fill choice-icon"></i>
                                                    <span class="choice-title">Izin</span>
                                                    <span class="choice-desc">Ada Keperluan</span>
                                                </label>
                                            </div>

                                            <!-- Box 4: Dispensasi -->
                                            <div class="col-6 col-md-3">
                                                <input type="radio" class="btn-check" name="status_kehadiran" id="statusDispensasi{{ $sess->id }}" value="Dispensasi" autocomplete="off">
                                                <label class="presensi-choice-label w-100 text-center d-flex flex-column align-items-center justify-content-center h-100" for="statusDispensasi{{ $sess->id }}">
                                                    <i class="bi bi-briefcase-fill choice-icon"></i>
                                                    <span class="choice-title">Dispensasi</span>
                                                    <span class="choice-desc">Tugas Kedinasan</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- KOTAK 5: KETERANGAN SESUAI GAMBAR TEMPLATE USER -->
                                    <div class="mb-4">
                                        <label for="keterangan{{ $sess->id }}" class="form-label fw-bold text-dark small text-uppercase mb-2" style="letter-spacing: 0.5px;">
                                            2. Keterangan / Catatan
                                        </label>
                                        <textarea name="keterangan" id="keterangan{{ $sess->id }}" rows="2" class="form-control" placeholder="Tuliskan keterangan singkat (wajib diisi apabila izin/sakit/dispensasi)..."></textarea>
                                    </div>

                                    <!-- KOTAK 6: BUKTI FOTO / PDF SESUAI GAMBAR TEMPLATE USER -->
                                    <div class="mb-3">
                                        <label for="buktiFile{{ $sess->id }}" class="form-label fw-bold text-dark small text-uppercase mb-2" style="letter-spacing: 0.5px;">
                                            3. Bukti Kehadiran / Surat Izin (Foto atau PDF)
                                        </label>
                                        <input type="file" name="bukti_file" id="buktiFile{{ $sess->id }}" class="form-control" accept="image/*,application/pdf" capture="user">
                                        <div class="form-text text-muted small mt-1">
                                            <i class="bi bi-info-circle me-1"></i> Lampirkan foto selfie kehadiran di ruang kelas / tangkapan layar Zoom, atau dokumen surat izin (JPG, PNG, PDF &bull; Maks. 2 MB).
                                        </div>
                                    </div>
                                </div>

                                <!-- Modal Footer Sederhana & Elegan -->
                                <div class="modal-footer border-0 px-4 pb-4 pt-0 d-flex justify-content-between align-items-center">
                                    <span class="text-muted small"><span class="text-danger">*</span> Waktu kehadiran otomatis tercatat secara real-time (WIB)</span>
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-light px-3 fw-medium" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn text-white fw-bold px-4 rounded-2 shadow-sm" style="background-color: #1B4965;">
                                            <i class="bi bi-send-check me-1"></i> Simpan Presensi
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @endif

            @empty
                <div class="kclc-card p-5 text-center text-muted">
                    <i class="bi bi-calendar-x fs-1 d-block mb-2 text-secondary"></i>
                    <h6 class="fw-bold text-dark">Tidak Ada Sesi Pembelajaran</h6>
                    <p class="small mb-0">Belum ada sesi pembelajaran yang terjadwal untuk kriteria pencarian ini.</p>
                </div>
            @endforelse
        </div>
        @endif
    </div>

    <!-- ============================================================== -->
    <!-- KOLOM KANAN (4-5 COLS): MENU AKTIFITAS PERSISI GAMBAR KCLC     -->
    <!-- ============================================================== -->
    <div class="col-lg-4">
        <div class="kclc-card p-3">
            <h5 class="fw-bold text-dark mb-3" style="font-size: 1.15rem;">Menu Aktifitas</h5>

            <div class="d-flex flex-column gap-3">
                <!-- 1. MATERI PEMBELAJARAN -->
                <div class="d-flex align-items-center justify-content-between pb-3 border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0" 
                             style="width: 36px; height: 36px; background-color: #DBEAFE; color: #1D4ED8;">
                            <i class="bi bi-journal-text fs-5"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark small text-uppercase" style="letter-spacing: 0.3px;">MATERI PEMBELAJARAN</div>
                            <div class="d-flex gap-1 mt-1">
                                <span class="badge-activity-info">1 Materi</span>
                                <span class="badge-activity-info">1 Media Pembelajaran</span>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-outline-secondary btn-sm py-1 px-2 small rounded-2" style="font-size: 0.75rem;" onclick="Swal.fire('Materi Pembelajaran', 'Materi dan modul pembelajaran siap diunduh.', 'info');">
                        <i class="bi bi-eye"></i> Detail
                    </button>
                </div>

                <!-- 2. PRE TEST -->
                <div class="d-flex align-items-center justify-content-between pb-3 border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0" 
                             style="width: 36px; height: 36px; background-color: #DBEAFE; color: #1D4ED8;">
                            <i class="bi bi-question-square-fill fs-5"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark small text-uppercase" style="letter-spacing: 0.3px;">PRE TEST</div>
                            <div class="mt-1">
                                <span class="badge-activity-done">Ada 0 Pre Test yang sudah di kerjakan</span>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-outline-secondary btn-sm py-1 px-2 small rounded-2" style="font-size: 0.75rem;" onclick="Swal.fire('Pre Test', 'Tidak ada pre test yang tertunda.', 'info');">
                        <i class="bi bi-eye"></i> Detail
                    </button>
                </div>

                <!-- 3. KUIS -->
                <div class="d-flex align-items-center justify-content-between pb-3 border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0" 
                             style="width: 36px; height: 36px; background-color: #DBEAFE; color: #1D4ED8;">
                            <i class="bi bi-chat-left-dots-fill fs-5"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark small text-uppercase" style="letter-spacing: 0.3px;">KUIS</div>
                            <div class="mt-1">
                                <span class="badge-activity-done">Ada 0 Kuis yang sudah di kerjakan</span>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-outline-secondary btn-sm py-1 px-2 small rounded-2" style="font-size: 0.75rem;" onclick="Swal.fire('Kuis Pembelajaran', 'Kuis akan dibuka sesuai jadwal materi.', 'info');">
                        <i class="bi bi-eye"></i> Detail
                    </button>
                </div>

                <!-- 4. TUGAS -->
                <div class="d-flex align-items-center justify-content-between pb-3 border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0" 
                             style="width: 36px; height: 36px; background-color: #DBEAFE; color: #1D4ED8;">
                            <i class="bi bi-check-square-fill fs-5"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark small text-uppercase" style="letter-spacing: 0.3px;">TUGAS</div>
                            <div class="mt-1">
                                <span class="badge-activity-pending">Ada 1 Tugas yang belum di kerjakan</span>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('portal.tugas.index') }}" class="btn btn-outline-secondary btn-sm py-1 px-2 small rounded-2 text-decoration-none" style="font-size: 0.75rem;">
                        <i class="bi bi-eye"></i> Detail
                    </a>
                </div>

                <!-- 5. POS TEST -->
                <div class="d-flex align-items-center justify-content-between pb-3 border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0" 
                             style="width: 36px; height: 36px; background-color: #DBEAFE; color: #1D4ED8;">
                            <i class="bi bi-file-earmark-check-fill fs-5"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark small text-uppercase" style="letter-spacing: 0.3px;">POS TEST</div>
                            <div class="mt-1">
                                <span class="badge-activity-done">Ada 0 Post Test yang sudah di kerjakan</span>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-outline-secondary btn-sm py-1 px-2 small rounded-2" style="font-size: 0.75rem;" onclick="Swal.fire('Post Test', 'Post test akan dibuka di akhir sesi pelatihan.', 'info');">
                        <i class="bi bi-eye"></i> Detail
                    </button>
                </div>

                <!-- 6. UJIAN -->
                <div class="d-flex align-items-center justify-content-between pb-3 border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0" 
                             style="width: 36px; height: 36px; background-color: #DBEAFE; color: #1D4ED8;">
                            <i class="bi bi-pencil-square fs-5"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark small text-uppercase" style="letter-spacing: 0.3px;">UJIAN</div>
                            <div class="mt-1">
                                <span class="badge-activity-done">Ada 0 Ujian yang sudah di kerjakan</span>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-outline-secondary btn-sm py-1 px-2 small rounded-2" style="font-size: 0.75rem;" onclick="Swal.fire('Ujian Komprehensif', 'Ujian sertifikasi dijadwalkan pada hari terakhir diklat.', 'info');">
                        <i class="bi bi-eye"></i> Detail
                    </button>
                </div>

                <!-- 7. SEMINAR -->
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0" 
                             style="width: 36px; height: 36px; background-color: #DBEAFE; color: #1D4ED8;">
                            <i class="bi bi-easel-fill fs-5"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark small text-uppercase" style="letter-spacing: 0.3px;">SEMINAR</div>
                            <div class="mt-1">
                                <span class="badge-activity-done">Ada 0 Seminar yang sudah di kerjakan</span>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-outline-secondary btn-sm py-1 px-2 small rounded-2" style="font-size: 0.75rem;" onclick="Swal.fire('Seminar Akhir', 'Jadwal seminar rancangan aksi perubahan.', 'info');">
                        <i class="bi bi-eye"></i> Detail
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Handle submission check-in via form manual token di modal
    document.querySelectorAll('.formSubmitToken').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const input = this.querySelector('input[name="qr_payload"]');
            if (!input || !input.value.trim()) return;

            Swal.fire({
                title: 'Memvalidasi Presensi...',
                text: 'Mohon tunggu sesaat.',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            fetch("{{ route('portal.scan.post') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ qr_payload: input.value.trim() })
            })
            .then(res => res.json().then(data => ({ status: res.status, body: data })))
            .then(({ status, body }) => {
                if (status === 200 && body.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Presensi Berhasil!',
                        text: body.message,
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#2563EB'
                    }).then(() => window.location.reload());
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Presensi Gagal',
                        text: body.message || 'Token presensi tidak valid atau kedaluwarsa.',
                        confirmButtonText: 'Coba Lagi'
                    });
                }
            })
            .catch(() => {
                Swal.fire('Error', 'Gagal menghubungi server.', 'error');
            });
        });
    });
</script>
@endsection
