@extends('layouts.app')

@section('title', 'Tambah Jadwal')
@section('page_title', 'Tambah Jadwal Pembelajaran')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('jadwal.index') }}">Jadwal</a></li>
    <li class="breadcrumb-item active" aria-current="page">Tambah Jadwal</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card-custom">
            <div class="card-custom-header">
                <h5 class="card-custom-title">
                    <i class="bi bi-calendar-plus text-primary"></i> Formulir Jadwal Pembelajaran
                </h5>
            </div>
            <div class="p-4">
                <form action="{{ route('jadwal.store') }}" method="POST">
                    @csrf

                    <!-- Pilih Mata Pelatihan -->
                    <div class="mb-3">
                        <label for="mata_pelatihan_id" class="form-label fw-semibold small">Mata Pelatihan & Angkatan <span class="text-danger">*</span></label>
                        <select class="form-select @error('mata_pelatihan_id') is-invalid @enderror" id="mata_pelatihan_id" name="mata_pelatihan_id" required>
                            <option value="">-- Pilih Mata Pelatihan --</option>
                            @foreach($mataPelatihans as $mp)
                                <option value="{{ $mp->id }}" {{ (old('mata_pelatihan_id') ?? $selectedMataPelatihanId) == $mp->id ? 'selected' : '' }}>
                                    {{ $mp->angkatan->pelatihan->nama_pelatihan }} ({{ $mp->angkatan->nomor_angkatan }}) &rarr; {{ $mp->nama_mata_pelatihan }} [Rencana: {{ $mp->rencana_jp }} JP]
                                </option>
                            @endforeach
                        </select>
                        @error('mata_pelatihan_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Pilih Pengajar Ditugaskan -->
                    <div class="mb-3">
                        <label for="penugasan_pengajar_id" class="form-label fw-semibold small">Pilih Pengajar Bertugas</label>
                        <select class="form-select @error('penugasan_pengajar_id') is-invalid @enderror" id="penugasan_pengajar_id" name="penugasan_pengajar_id">
                            <option value="">-- Pengajar Belum Ditugaskan / Pilih Nanti --</option>
                        </select>
                        <div class="form-text small">Daftar pengajar diambil dari penugasan pada mata pelatihan yang dipilih di atas.</div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="tanggal" class="form-label fw-semibold small">Tanggal Pembelajaran <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('tanggal') is-invalid @enderror" id="tanggal" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" required>
                            @error('tanggal')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold small">Metode Input Waktu Sesi <span class="text-danger">*</span></label>
                            <div class="d-flex gap-3 pt-1">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="metode_input" id="metode_template" value="template" checked>
                                    <label class="form-check-label fw-semibold text-primary" for="metode_template">
                                        <i class="bi bi-grid-3x3-gap me-1"></i> Template Sesi Standar (1 JP = 45m)
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="metode_input" id="metode_manual" value="manual">
                                    <label class="form-check-label fw-semibold text-secondary" for="metode_manual">
                                        <i class="bi bi-clock me-1"></i> Input Jam Manual
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Metode A: Pilihan Template Sesi -->
                    <div class="mb-3 p-3 bg-light rounded border" id="wrapper_template_sesi">
                        <label for="sesi_id" class="form-label fw-semibold small text-primary">Pilih Slot Sesi Standar (45 Menit = 1 JP)</label>
                        <select class="form-select" id="sesi_id" name="sesi_id">
                            @foreach($templateSesies as $sesi)
                                <option value="{{ $sesi->id }}">
                                    {{ $sesi->nama_sesi }}: {{ substr($sesi->jam_mulai, 0, 5) }} &ndash; {{ substr($sesi->jam_selesai, 0, 5) }} ({{ $sesi->durasi_menit }} Menit / {{ $sesi->jumlah_jp }} JP)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Metode B: Input Jam Manual -->
                    <div class="p-3 bg-light rounded border mb-3 d-none" id="wrapper_manual_sesi">
                        <div class="row">
                            <div class="col-md-4 mb-2">
                                <label for="jam_mulai" class="form-label fw-semibold small">Jam Mulai <span class="text-danger">*</span></label>
                                <input type="time" class="form-control" id="jam_mulai" name="jam_mulai" value="{{ old('jam_mulai', '08:00') }}">
                            </div>
                            <div class="col-md-4 mb-2">
                                <label for="jam_selesai" class="form-label fw-semibold small">Jam Selesai <span class="text-danger">*</span></label>
                                <input type="time" class="form-control" id="jam_selesai" name="jam_selesai" value="{{ old('jam_selesai', '09:30') }}">
                            </div>
                            <div class="col-md-4 mb-2">
                                <label for="jp_rencana" class="form-label fw-semibold small">JP Rencana (1 JP = 45m)</label>
                                <input type="number" step="0.5" class="form-control" id="jp_rencana" name="jp_rencana" value="{{ old('jp_rencana', 2.0) }}">
                                <div class="form-text small" id="kalkulasi_jp_text">Durasi: 90 menit = 2 JP</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="ruangan" class="form-label fw-semibold small">Ruangan / Tempat</label>
                            <input type="text" class="form-control" id="ruangan" name="ruangan" value="{{ old('ruangan', 'Ruang Kelas 101') }}" placeholder="Ruang Kelas / Lab">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="metode" class="form-label fw-semibold small">Metode Pembelajaran <span class="text-danger">*</span></label>
                            <select class="form-select" id="metode" name="metode" required>
                                <option value="Tatap Muka / Klasikal" {{ old('metode') == 'Tatap Muka / Klasikal' ? 'selected' : '' }}>Tatap Muka / Klasikal</option>
                                <option value="Daring / Online" {{ old('metode') == 'Daring / Online' ? 'selected' : '' }}>Daring / Online</option>
                                <option value="Blended" {{ old('metode') == 'Blended' ? 'selected' : '' }}>Blended</option>
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="status" class="form-label fw-semibold small">Status Awal <span class="text-danger">*</span></label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="Terjadwal" selected>Terjadwal</option>
                                <option value="Terlaksana">Terlaksana Langsung</option>
                                <option value="Dibatalkan">Dibatalkan</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="keterangan" class="form-label fw-semibold small">Keterangan / Pokok Bahasan</label>
                        <textarea class="form-control" id="keterangan" name="keterangan" rows="2" placeholder="Catatan materi atau perlengkapan yang dibutuhkan">{{ old('keterangan') }}</textarea>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('jadwal.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-navy">
                            <i class="bi bi-save me-1"></i> Simpan Jadwal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
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

    // Hitung otomatis durasi dan JP saat jam diubah
    function kalkulasiJP() {
        if (!jamMulaiInput.value || !jamSelesaiInput.value) return;

        const start = jamMulaiInput.value.split(':');
        const end = jamSelesaiInput.value.split(':');

        const startDate = new Date(0, 0, 0, start[0], start[1], 0);
        const endDate = new Date(0, 0, 0, end[0], end[1], 0);

        let diff = (endDate.getTime() - startDate.getTime()) / 1000 / 60; // dalam menit
        if (diff > 0) {
            const jp = Math.round((diff / 45) * 10) / 10;
            jpInput.value = jp;
            kalkulasiText.textContent = `Durasi: ${diff} menit = ${jp} JP`;
        }
    }

    jamMulaiInput.addEventListener('change', kalkulasiJP);
    jamSelesaiInput.addEventListener('change', kalkulasiJP);

    // Cascading Pengajar berdasarkan Mata Pelatihan
    const selectMp = document.getElementById('mata_pelatihan_id');
    const selectPengajar = document.getElementById('penugasan_pengajar_id');

    function loadPengajar(mpId) {
        if (!mpId) {
            selectPengajar.innerHTML = '<option value="">-- Pengajar Belum Ditugaskan / Pilih Nanti --</option>';
            return;
        }

        fetch(`{{ url('api/pengajars-by-mata-pelatihan') }}/${mpId}`)
            .then(res => res.json())
            .then(data => {
                let html = '<option value="">-- Pengajar Belum Ditugaskan / Pilih Nanti --</option>';
                data.forEach(p => {
                    html += `<option value="${p.id}">${p.nama} (${p.jenis} - ${p.peran})</option>`;
                });
                selectPengajar.innerHTML = html;
            });
    }

    selectMp.addEventListener('change', function () {
        loadPengajar(this.value);
    });

    if (selectMp.value) {
        loadPengajar(selectMp.value);
    }
});
</script>
@endpush
