@extends('portal.layout')

@section('title', 'Dashboard Diklat')
@section('page_title', 'DASHBOARD DIKLAT')
@section('breadcrumb', 'Dashboard Diklat')

@section('styles')
<style>
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
        <!-- 1. Card Banner Info Pelatihan Sesuai Gambar KCLC -->
        <div class="kclc-card p-3 mb-3">
            <h6 class="fw-bold text-dark mb-1" style="font-size: 1.05rem;">
                Nama Pelatihan : {{ $pelatihan->nama_pelatihan ?? ($angkatan->nomor_angkatan ?? 'Pengantar Kerja Ahli Muda A.1') }}
            </h6>
            <div class="text-muted small">
                Diselenggarakan Tanggal 
                <strong>{{ $angkatan->tanggal_mulai ? $angkatan->tanggal_mulai->translatedFormat('d F Y') : '23 November 2026' }}</strong> s/d 
                <strong>{{ $angkatan->tanggal_selesai ? $angkatan->tanggal_selesai->translatedFormat('d F Y') : '4 Desember 2026' }}</strong>
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
                <a href="{{ route('portal.dashboard') }}" class="btn btn-outline-warning btn-sm px-3 fw-semibold text-dark">
                    Reset
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
                        {{ $pelatihan->nama_pelatihan ?? ($angkatan->nomor_angkatan ?? 'Pengantar Kerja Ahli Muda A.1') }}
                    </h5>
                    <div class="text-muted small mt-1">
                        <i class="bi bi-compass text-secondary me-1"></i> Pelatihan
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
                        ?? ($sess->jadwal->pengajar ?? 'Alfiah Pira Mundiarsih, S.Sos., M.A.'));
                    $ruanganNama = $sess->kelasMapel->ruangan->nama_ruangan 
                        ?? ($sess->jadwal->ruangan ?? 'Zoom Meeting');
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
                                    <i class="bi bi-calendar3 text-secondary me-1"></i> {{ $sess->tanggal->translatedFormat('l, Y-m-d') }} &bull; 
                                    <i class="bi bi-clock text-secondary me-1"></i> {{ substr($sess->jam_mulai, 0, 5) }} - {{ substr($sess->jam_selesai, 0, 5) }} WIB
                                </div>
                                <div class="small text-muted d-flex align-items-center gap-2 flex-wrap">
                                    <span>
                                        <i class="bi bi-person text-secondary me-1"></i> {{ $pengajarNama }}
                                    </span>
                                    <span class="badge bg-secondary-subtle text-secondary small py-0 px-2" style="font-size: 0.68rem;">widyaiswara</span>
                                </div>
                                <div class="small text-primary mt-1">
                                    <i class="bi bi-camera-video me-1"></i> {{ $ruanganNama }}
                                    <i class="bi bi-copy text-muted ms-1 cursor-pointer" title="Salin Info Pertemuan" onclick="navigator.clipboard.writeText('Pertemuan {{ $idx + 1 }}: {{ $ruanganNama }}'); Swal.fire({toast: true, position: 'top-end', icon: 'success', title: 'Tersalin!', showConfirmButton: false, timer: 1200});"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Right Action: Tombol Presensi Sesuai Gambar -->
                        <div>
                            @if($presensiUser)
                                <button type="button" class="btn btn-success btn-sm px-3 py-2 rounded-2 fw-semibold shadow-sm d-flex align-items-center gap-1" disabled>
                                    <i class="bi bi-check2-circle fs-6"></i>
                                    <span>{{ $presensiUser->status_kehadiran }} ({{ $presensiUser->waktu_check_in ? $presensiUser->waktu_check_in->format('H:i') : 'OK' }})</span>
                                </button>
                            @elseif($sess->status === 'Aktif')
                                <button type="button" class="btn btn-primary btn-sm px-3 py-2 rounded-2 fw-semibold shadow-sm d-flex align-items-center gap-2" 
                                        data-bs-toggle="modal" data-bs-target="#modalPresensi{{ $sess->id }}">
                                    <i class="bi bi-person-check fs-6"></i>
                                    <span>Presensi</span>
                                </button>
                            @else
                                <button type="button" class="btn btn-secondary btn-sm px-3 py-2 rounded-2 fw-semibold" disabled title="Sesi belum dibuka oleh panitia">
                                    <i class="bi bi-clock"></i> Presensi
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Modal Presensi untuk Pertemuan Ini -->
                @if(!$presensiUser && $sess->status === 'Aktif')
                <div class="modal fade" id="modalPresensi{{ $sess->id }}" tabindex="-1" aria-labelledby="modalPresensiLabel{{ $sess->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 shadow rounded-4 overflow-hidden">
                            <div class="modal-header bg-primary text-white py-3 px-4">
                                <h6 class="modal-title fw-bold" id="modalPresensiLabel{{ $sess->id }}">
                                    <i class="bi bi-person-check me-2"></i> Presensi: Pertemuan {{ $idx + 1 }}
                                </h6>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-4">
                                <div class="text-center mb-3">
                                    <h6 class="fw-bold text-dark mb-1">{{ $sess->judul_sesi }}</h6>
                                    <p class="text-muted small mb-0">{{ substr($sess->jam_mulai, 0, 5) }} - {{ substr($sess->jam_selesai, 0, 5) }} WIB &bull; {{ $ruanganNama }}</p>
                                </div>

                                <!-- Opsi 1: Scan Kamera HP Langsung -->
                                <div class="card p-3 border rounded-3 mb-3 text-center bg-light">
                                    <div class="fw-bold text-dark small mb-1"><i class="bi bi-qr-code-scan text-primary me-1"></i> Scan QR Code Proyektor</div>
                                    <p class="text-muted small mb-2">Arahkan kamera HP Anda ke QR Code yang sedang berputar di layar kelas</p>
                                    <a href="{{ route('portal.scan', ['session_id' => $sess->id]) }}" class="btn btn-primary btn-sm px-3 fw-bold rounded-pill">
                                        <i class="bi bi-camera me-1"></i> Buka Kamera Scanner QR
                                    </a>
                                </div>

                                <!-- Opsi 2: Konfirmasi / Input Kode Token Manual -->
                                <div class="card p-3 border rounded-3 text-center">
                                    <div class="fw-bold text-dark small mb-1"><i class="bi bi-shield-lock text-warning me-1"></i> Atau Masukkan Token QR</div>
                                    <form action="{{ route('portal.scan.post') }}" method="POST" class="mt-2 formSubmitToken">
                                        @csrf
                                        <div class="input-group input-group-sm mb-2">
                                            <input type="text" name="qr_payload" class="form-control text-center font-monospace" placeholder="Tempel URL / Token QR di sini" required>
                                            <button type="submit" class="btn btn-dark fw-semibold">Check-In</button>
                                        </div>
                                    </form>
                                </div>

                                <div class="text-center mt-3">
                                    <a href="{{ route('portal.pengajuan') }}" class="text-decoration-none small text-muted">
                                        <i class="bi bi-file-earmark-medical me-1 text-danger"></i> Berhalangan hadir? Ajukan Izin / Sakit di sini
                                    </a>
                                </div>
                            </div>
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
                        text: body.message || 'Token QR tidak valid atau kedaluwarsa.',
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
