@extends('layouts.app')

@section('title', 'Tambah Jadwal')
@section('page_title', 'Tambah Jadwal Pembelajaran')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('jadwal.index') }}">Jadwal</a></li>
    <li class="breadcrumb-item active" aria-current="page">Tambah Jadwal</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <!-- Visual Flow Card -->
        <div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #183B65 0%, #2563EB 100%); color: white; border-radius: 12px;">
            <div class="p-3">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2 small">
                        <span class="badge bg-white text-dark fw-bold">1</span> <span>Nama Pelatihan</span>
                        <i class="bi bi-chevron-right text-white-50"></i>
                        <span class="badge bg-white text-dark fw-bold">2</span> <span>Angkatan</span>
                        <i class="bi bi-chevron-right text-white-50"></i>
                        <span class="badge bg-white text-dark fw-bold">3</span> <span>Mata Pelatihan</span>
                        <i class="bi bi-chevron-right text-white-50"></i>
                        <span class="badge bg-white text-dark fw-bold">4</span> <span>Pengampu Materi (2 Jenis)</span>
                    </div>
                    <span class="badge bg-warning text-dark"><i class="bi bi-clock-history me-1"></i> 1 JP = 45 Menit</span>
                </div>
            </div>
        </div>

        <div class="card-custom">
            <div class="card-custom-header d-flex justify-content-between align-items-center">
                <h5 class="card-custom-title mb-0">
                    <i class="bi bi-calendar-plus text-primary"></i> Formulir Penyusunan Jadwal
                </h5>
                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalQuickPengajar">
                    <i class="bi bi-person-plus-fill me-1"></i> + Daftarkan Pengajar Baru
                </button>
            </div>
            <div class="p-4">
                <form action="{{ route('jadwal.store') }}" method="POST" id="formJadwal">
                    @csrf

                    <!-- TAHAP 1: ALUR HIERARKI PELATIHAN -> ANGKATAN -> MATA PELATIHAN -->
                    <div class="p-3 bg-light rounded border mb-4">
                        <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                            <i class="bi bi-diagram-3-fill text-primary me-1"></i> Hierarki Program Pelatihan
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="select_pelatihan" class="form-label fw-semibold small">1. Nama Pelatihan <span class="text-danger">*</span></label>
                                <select class="form-select form-select-sm" id="select_pelatihan">
                                    <option value="">-- Pilih Pelatihan --</option>
                                    @foreach($pelatihans as $p)
                                        <option value="{{ $p->id }}">{{ $p->nama_pelatihan }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="select_angkatan" class="form-label fw-semibold small">2. Angkatan <span class="text-danger">*</span></label>
                                <select class="form-select form-select-sm" id="select_angkatan" disabled>
                                    <option value="">-- Pilih Pelatihan Terlebih Dahulu --</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="mata_pelatihan_id" class="form-label fw-semibold small">3. Mata Pelatihan <span class="text-danger">*</span></label>
                                <select class="form-select form-select-sm @error('mata_pelatihan_id') is-invalid @enderror" 
                                        id="mata_pelatihan_id" name="mata_pelatihan_id" required>
                                    <option value="">-- Pilih Mata Pelatihan --</option>
                                    @foreach($mataPelatihans as $mp)
                                        <option value="{{ $mp->id }}" data-pelatihan="{{ $mp->angkatan->pelatihan_id }}" data-angkatan="{{ $mp->angkatan_id }}"
                                            {{ (old('mata_pelatihan_id') ?? $selectedMataPelatihanId) == $mp->id ? 'selected' : '' }}>
                                            {{ $mp->nama_mata_pelatihan }} ({{ $mp->rencana_jp }} JP)
                                        </option>
                                    @endforeach
                                </select>
                                @error('mata_pelatihan_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- TAHAP 2: PENGAMPU MATERI (DIBAGI 2 SESUAI LOGIKA) -->
                    <div class="p-3 bg-light rounded border mb-4">
                        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                            <h6 class="fw-bold text-dark mb-0">
                                <i class="bi bi-people-fill text-primary me-1"></i> Pengampu Materi (Dibagi 2 Jenis Pengajar)
                            </h6>
                            <a href="javascript:void(0)" class="small text-primary text-decoration-none fw-semibold" data-bs-toggle="modal" data-bs-target="#modalQuickPengajar">
                                <i class="bi bi-plus-circle me-1"></i> Pengajar belum terdaftar? Tambah Cepat
                            </a>
                        </div>

                        <!-- Radio Pilihan 2 Jenis Pengajar -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <div class="card h-100 border p-3 cursor-pointer shadow-sm" id="card_wi" style="border-left: 4px solid #183B65 !important;">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="direct_jenis_pengajar" id="jenis_wi" value="Widyaiswara" checked>
                                        <label class="form-check-label fw-bold text-dark" for="jenis_wi">
                                            Widyaiswara PPSDM
                                        </label>
                                    </div>
                                    <div class="mt-2 text-muted small">
                                        <span class="badge bg-primary text-white mb-1">Wajib Min. 32 JP / Bulan</span><br>
                                        Pembayaran honorarium mengajar hanya berlaku untuk jam yang <strong>melebihi 32 JP</strong>.
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card h-100 border p-3 cursor-pointer shadow-sm" id="card_ns" style="border-left: 4px solid #2563EB !important;">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="direct_jenis_pengajar" id="jenis_ns" value="Narasumber">
                                        <label class="form-check-label fw-bold text-dark" for="jenis_ns">
                                            Narasumber Eksternal / Pakar
                                        </label>
                                    </div>
                                    <div class="mt-2 text-muted small">
                                        <span class="badge bg-success text-white mb-1">Tidak Ada Batasan Jam Minimal</span><br>
                                        Seluruh jam mengajar langsung dihitung untuk <strong>Pembayaran Honor Penuh</strong>.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Dropdown Pilih Pengajar -->
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label for="direct_pengajar_id" class="form-label fw-semibold small">Pilih Pengajar Yang Bertugas</label>
                                <select class="form-select form-select-sm" id="direct_pengajar_id" name="direct_pengajar_id">
                                    <option value="">-- Pilih Pengajar --</option>
                                    <!-- Options will be populated by JavaScript based on radio WI / NS -->
                                </select>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="button" class="btn btn-sm btn-outline-secondary w-100" data-bs-toggle="modal" data-bs-target="#modalQuickPengajar">
                                    <i class="bi bi-person-plus me-1"></i> Daftarkan Pengajar Baru
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- TAHAP 3: WAKTU & SESI PEMBELAJARAN (TEMPLATE SESI ATAU MANUAL) -->
                    <div class="p-3 bg-light rounded border mb-4">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="tanggal" class="form-label fw-semibold small">Tanggal Pembelajaran <span class="text-danger">*</span></label>
                                <input type="date" class="form-control form-control-sm @error('tanggal') is-invalid @enderror" 
                                       id="tanggal" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" required>
                                @error('tanggal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold small">Metode Sesi <span class="text-danger">*</span></label>
                                <div class="d-flex gap-3 pt-1">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="metode_input" id="metode_template" value="template" checked>
                                        <label class="form-check-label fw-semibold text-primary small" for="metode_template">
                                            <i class="bi bi-grid-3x3-gap me-1"></i> Template Sesi Standar (45m = 1 JP)
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="metode_input" id="metode_manual" value="manual">
                                        <label class="form-check-label fw-semibold text-secondary small" for="metode_manual">
                                            <i class="bi bi-clock me-1"></i> Input Jam Manual
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Template Sesi Standar (Sesi 1 s.d. Sesi 10 + Istirahat / Ishoma) -->
                        <div class="mb-2" id="wrapper_template_sesi">
                            <label for="sesi_id" class="form-label fw-semibold small text-primary">Pilih Slot Sesi Standar Pembelajaran:</label>
                            <select class="form-select form-select-sm" id="sesi_id" name="sesi_id">
                                @foreach($templateSesies as $sesi)
                                    <option value="{{ $sesi->id }}">
                                        {{ $sesi->nama_sesi }}: {{ substr($sesi->jam_mulai, 0, 5) }} &ndash; {{ substr($sesi->jam_selesai, 0, 5) }} ({{ $sesi->durasi_menit }} Menit / {{ $sesi->jumlah_jp }} JP)
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text small text-muted">
                                Template: Sesi 1-3 (08.00-10.15) &bull; Istirahat 15m &bull; Sesi 4-5 (10.30-12.00) &bull; Ishoma 1 Jam &bull; Sesi 6-8 (13.00-15.15) &bull; Istirahat 30m &bull; Sesi 9-10 (15.45-17.15).
                            </div>
                        </div>

                        <!-- Manual Sesi -->
                        <div class="p-3 bg-white rounded border mb-2 d-none" id="wrapper_manual_sesi">
                            <div class="row">
                                <div class="col-md-4 mb-2">
                                    <label for="jam_mulai" class="form-label fw-semibold small">Jam Mulai <span class="text-danger">*</span></label>
                                    <input type="time" class="form-control form-control-sm" id="jam_mulai" name="jam_mulai" value="{{ old('jam_mulai', '08:00') }}">
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label for="jam_selesai" class="form-label fw-semibold small">Jam Selesai <span class="text-danger">*</span></label>
                                    <input type="time" class="form-control form-control-sm" id="jam_selesai" name="jam_selesai" value="{{ old('jam_selesai', '09:30') }}">
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label for="jp_rencana" class="form-label fw-semibold small">JP Rencana</label>
                                    <input type="number" step="0.5" class="form-control form-control-sm" id="jp_rencana" name="jp_rencana" value="{{ old('jp_rencana', 2.0) }}">
                                    <div class="form-text small text-primary" id="kalkulasi_jp_text">Durasi: 90 menit = 2 JP</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAHAP 4: METODE & STATUS -->
                    <div class="row mb-4">
                        <div class="col-md-4 mb-2">
                            <label for="ruangan" class="form-label fw-semibold small">Ruangan / Tempat</label>
                            <input type="text" class="form-control form-control-sm" id="ruangan" name="ruangan" value="{{ old('ruangan', 'Ruang Kelas 101') }}">
                        </div>
                        <div class="col-md-4 mb-2">
                            <label for="metode" class="form-label fw-semibold small">Metode Pembelajaran</label>
                            <select class="form-select form-select-sm" id="metode" name="metode">
                                <option value="Tatap Muka / Klasikal" selected>Tatap Muka / Klasikal</option>
                                <option value="Daring / Online">Daring / Online</option>
                                <option value="Blended">Blended Learning</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-2">
                            <label for="status" class="form-label fw-semibold small">Status Awal</label>
                            <select class="form-select form-select-sm" id="status" name="status">
                                <option value="Terjadwal" selected>Terjadwal</option>
                                <option value="Terlaksana">Terlaksana Langsung</option>
                                <option value="Dibatalkan">Dibatalkan</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="keterangan" class="form-label fw-semibold small">Keterangan / Pokok Bahasan</label>
                        <textarea class="form-control form-control-sm" id="keterangan" name="keterangan" rows="2" placeholder="Catatan materi atau modul yang dibutuhkan">{{ old('keterangan') }}</textarea>
                    </div>

                    <div class="d-flex justify-content-between pt-2 border-top">
                        <a href="{{ route('jadwal.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Batal / Kembali
                        </a>
                        <button type="submit" class="btn btn-navy px-4">
                            <i class="bi bi-save me-1"></i> Simpan Jadwal Pembelajaran
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- MODAL QUICK ADD PENGAJAR (SOLUSI: PENGAJAR YG BELUM DAFTAR KESULITAN) -->
<div class="modal fade" id="modalQuickPengajar" tabindex="-1" aria-labelledby="modalQuickPengajarLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-navy text-white">
                <h6 class="modal-title" id="modalQuickPengajarLabel">
                    <i class="bi bi-person-plus-fill me-1"></i> Pendaftaran Cepat Pengajar
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formQuickPengajar">
                @csrf
                <div class="modal-body p-4">
                    <div id="quickPengajarAlert" class="alert alert-danger d-none small"></div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Jenis Pengajar <span class="text-danger">*</span></label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="quick_jenis" id="q_wi" value="Widyaiswara" checked>
                                <label class="form-check-label small fw-semibold" for="q_wi">Widyaiswara PPSDM</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="quick_jenis" id="q_ns" value="Narasumber">
                                <label class="form-check-label small fw-semibold" for="q_ns">Narasumber Eksternal</label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="q_nama" class="form-label fw-semibold small">Nama Lengkap (Tanpa Gelar) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" id="q_nama" name="q_nama" placeholder="Contoh: Faizal Haqqi" required>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="q_nik" class="form-label fw-semibold small">NIK (16 Digit) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm" id="q_nik" name="q_nik" maxlength="16" placeholder="Contoh: 3171012304850001" required>
                        </div>
                        <div class="col-md-6">
                            <label for="q_gelar" class="form-label fw-semibold small">Gelar (Depan / Belakang)</label>
                            <input type="text" class="form-control form-control-sm" id="q_gelar" name="q_gelar" placeholder="Contoh: S.IP., M.M">
                        </div>
                    </div>

                    <div class="mb-3" id="wrapper_q_jabatan">
                        <label for="q_jabatan" class="form-label fw-semibold small">Jabatan Widyaiswara</label>
                        <select class="form-select form-select-sm" id="q_jabatan" name="q_jabatan">
                            <option value="Widyaiswara Ahli Pertama">Widyaiswara Ahli Pertama</option>
                            <option value="Widyaiswara Ahli Muda">Widyaiswara Ahli Muda</option>
                            <option value="Widyaiswara Ahli Madya">Widyaiswara Ahli Madya</option>
                            <option value="Widyaiswara Ahli Utama">Widyaiswara Ahli Utama</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="q_instansi" class="form-label fw-semibold small">Instansi Asal</label>
                        <input type="text" class="form-control form-control-sm" id="q_instansi" name="q_instansi" value="Pusat Pengembangan SDM Ketenagakerjaan" placeholder="Instansi / Universitas">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label for="q_telepon" class="form-label fw-semibold small">No. HP / WhatsApp</label>
                            <input type="text" class="form-control form-control-sm" id="q_telepon" name="q_telepon" placeholder="0812...">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label for="q_email" class="form-label fw-semibold small">Alamat Email</label>
                            <input type="email" class="form-control form-control-sm" id="q_email" name="q_email" placeholder="nama@kemnaker.go.id">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-sm btn-navy" id="btnSubmitQuickPengajar">
                        <i class="bi bi-check-circle me-1"></i> Daftarkan & Pilih Pengajar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. DATA PENGUMPUL (Widyaiswara & Narasumber)
    const widyaiswaraList = @json($widyaiswaras);
    const narasumberList = @json($narasumbers);

    const radioWI = document.getElementById('jenis_wi');
    const radioNS = document.getElementById('jenis_ns');
    const selectPengajar = document.getElementById('direct_pengajar_id');

    function renderPengajarOptions(selectedId = null) {
        selectPengajar.innerHTML = '<option value="">-- Pilih Pengajar --</option>';
        if (radioWI.checked) {
            widyaiswaraList.forEach(wi => {
                const opt = document.createElement('option');
                opt.value = wi.id;
                opt.textContent = `${wi.nama_lengkap}${wi.gelar ? ', ' + wi.gelar : ''} (${wi.jabatan})`;
                if (selectedId && wi.id == selectedId) opt.selected = true;
                selectPengajar.appendChild(opt);
            });
        } else {
            narasumberList.forEach(ns => {
                const opt = document.createElement('option');
                opt.value = ns.id;
                opt.textContent = `${ns.nama_lengkap}${ns.gelar ? ', ' + ns.gelar : ''} [${ns.instansi}]`;
                if (selectedId && ns.id == selectedId) opt.selected = true;
                selectPengajar.appendChild(opt);
            });
        }
    }

    radioWI.addEventListener('change', () => renderPengajarOptions());
    radioNS.addEventListener('change', () => renderPengajarOptions());
    renderPengajarOptions();

    // 2. CASCADING: Pelatihan -> Angkatan -> Mata Pelatihan
    const selectPelatihan = document.getElementById('select_pelatihan');
    const selectAngkatan = document.getElementById('select_angkatan');
    const selectMp = document.getElementById('mata_pelatihan_id');

    selectPelatihan.addEventListener('change', function () {
        const pelId = this.value;
        selectAngkatan.innerHTML = '<option value="">-- Memuat Angkatan... --</option>';
        selectAngkatan.disabled = true;

        if (!pelId) {
            selectAngkatan.innerHTML = '<option value="">-- Pilih Pelatihan Terlebih Dahulu --</option>';
            return;
        }

        fetch(`{{ url('api/angkatans-by-pelatihan') }}/${pelId}`)
            .then(res => res.json())
            .then(data => {
                let html = '<option value="">-- Pilih Angkatan --</option>';
                data.forEach(a => {
                    html += `<option value="${a.id}">${a.nomor_angkatan}</option>`;
                });
                selectAngkatan.innerHTML = html;
                selectAngkatan.disabled = false;
            });
    });

    selectAngkatan.addEventListener('change', function () {
        const angId = this.value;
        selectMp.innerHTML = '<option value="">-- Memuat Mata Pelatihan... --</option>';

        if (!angId) {
            selectMp.innerHTML = '<option value="">-- Pilih Angkatan Terlebih Dahulu --</option>';
            return;
        }

        fetch(`{{ url('api/mata-pelatihans-by-angkatan') }}/${angId}`)
            .then(res => res.json())
            .then(data => {
                let html = '<option value="">-- Pilih Mata Pelatihan --</option>';
                data.forEach(mp => {
                    html += `<option value="${mp.id}">${mp.nama_mata_pelatihan} (${mp.rencana_jp} JP)</option>`;
                });
                selectMp.innerHTML = html;
            });
    });

    // 3. TOGGLE METODE SESI (Template Sesi vs Manual)
    const radioTemplate = document.getElementById('metode_template');
    const radioManual = document.getElementById('metode_manual');
    const wrapTemplate = document.getElementById('wrapper_template_sesi');
    const wrapManual = document.getElementById('wrapper_manual_sesi');

    const jamMulaiInput = document.getElementById('jam_mulai');
    const jamSelesaiInput = document.getElementById('jam_selesai');
    const jpInput = document.getElementById('jp_rencana');
    const kalkulasiText = document.getElementById('kalkulasi_jp_text');

    function toggleMetode() {
        if (radioTemplate.checked) {
            wrapTemplate.classList.remove('d-none');
            wrapManual.classList.add('d-none');
        } else {
            wrapTemplate.classList.add('d-none');
            wrapManual.classList.remove('d-none');
        }
    }

    radioTemplate.addEventListener('change', toggleMetode);
    radioManual.addEventListener('change', toggleMetode);

    function kalkulasiJP() {
        if (!jamMulaiInput.value || !jamSelesaiInput.value) return;
        const start = jamMulaiInput.value.split(':');
        const end = jamSelesaiInput.value.split(':');
        const startDate = new Date(0, 0, 0, start[0], start[1], 0);
        const endDate = new Date(0, 0, 0, end[0], end[1], 0);

        let diff = (endDate.getTime() - startDate.getTime()) / 1000 / 60;
        if (diff > 0) {
            const jp = Math.round((diff / 45) * 10) / 10;
            jpInput.value = jp;
            kalkulasiText.textContent = `Durasi: ${diff} menit = ${jp} JP`;
        }
    }

    jamMulaiInput.addEventListener('change', kalkulasiJP);
    jamSelesaiInput.addEventListener('change', kalkulasiJP);

    // 4. QUICK ADD PENGAJAR (MODAL AJAX)
    const qWi = document.getElementById('q_wi');
    const qNs = document.getElementById('q_ns');
    const wrapQJabatan = document.getElementById('wrapper_q_jabatan');
    const qInstansi = document.getElementById('q_instansi');

    qWi.addEventListener('change', function () {
        wrapQJabatan.classList.remove('d-none');
        qInstansi.value = 'Pusat Pengembangan SDM Ketenagakerjaan';
    });

    qNs.addEventListener('change', function () {
        wrapQJabatan.classList.add('d-none');
        qInstansi.value = 'Eksternal / Pakar';
    });

    const btnSubmitQuick = document.getElementById('btnSubmitQuickPengajar');
    const quickAlert = document.getElementById('quickPengajarAlert');

    btnSubmitQuick.addEventListener('click', function () {
        quickAlert.classList.add('d-none');

        const jenis = qWi.checked ? 'Widyaiswara' : 'Narasumber';
        const nama = document.getElementById('q_nama').value.trim();
        const nik = document.getElementById('q_nik').value.trim();
        const gelar = document.getElementById('q_gelar').value.trim();
        const jabatan = document.getElementById('q_jabatan').value;
        const instansi = qInstansi.value.trim();
        const telp = document.getElementById('q_telepon').value.trim();
        const email = document.getElementById('q_email').value.trim();
        const mpId = selectMp.value;

        if (!nama || !nik) {
            quickAlert.textContent = 'Nama lengkap dan NIK wajib diisi.';
            quickAlert.classList.remove('d-none');
            return;
        }

        btnSubmitQuick.disabled = true;
        btnSubmitQuick.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...';

        fetch('{{ route("api.pengajar.quick_store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                jenis_pengajar: jenis,
                nama_lengkap: nama,
                nik: nik,
                gelar: gelar,
                jabatan: jabatan,
                instansi: instansi,
                nomor_telepon: telp,
                email: email,
                mata_pelatihan_id: mpId || null
            })
        })
        .then(res => res.json())
        .then(data => {
            btnSubmitQuick.disabled = false;
            btnSubmitQuick.innerHTML = '<i class="bi bi-check-circle me-1"></i> Daftarkan & Pilih Pengajar';

            if (data.success) {
                // Tambahkan ke array lokal
                if (jenis === 'Widyaiswara') {
                    widyaiswaraList.push({
                        id: data.pengajar.id,
                        nama_lengkap: nama,
                        gelar: gelar,
                        jabatan: jabatan
                    });
                    radioWI.checked = true;
                } else {
                    narasumberList.push({
                        id: data.pengajar.id,
                        nama_lengkap: nama,
                        gelar: gelar,
                        instansi: instansi
                    });
                    radioNS.checked = true;
                }

                renderPengajarOptions(data.pengajar.id);

                // Tutup modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('modalQuickPengajar'));
                if (modal) modal.hide();

                // Reset form modal
                document.getElementById('formQuickPengajar').reset();

                SirAlert.success(data.message, 'Informasi !');
            } else {
                quickAlert.textContent = data.message || 'Gagal mendaftarkan pengajar.';
                quickAlert.classList.remove('d-none');
            }
        })
        .catch(err => {
            btnSubmitQuick.disabled = false;
            btnSubmitQuick.innerHTML = '<i class="bi bi-check-circle me-1"></i> Daftarkan & Pilih Pengajar';
            quickAlert.textContent = 'Terjadi kesalahan sistem: ' + err;
            quickAlert.classList.remove('d-none');
        });
    });
});
</script>
@endpush
