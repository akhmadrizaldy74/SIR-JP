@extends('layouts.app')

@section('title', 'Buka Sesi Presensi Baru')

@section('content')
<div class="mb-4">
    <a href="{{ route('attendance-sessions.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 mb-2">
        <i class="bi bi-arrow-left"></i> Kembali ke Daftar Sesi
    </a>
    <h4 class="fw-bold text-dark mb-1">Buka Sesi Presensi Pembelajaran</h4>
    <div class="text-muted small">Pilih jadwal pembelajaran dan tentukan batas waktu presensi untuk peserta</div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-3 p-4 bg-white">
            <form action="{{ route('attendance-sessions.store') }}" method="POST">
                @csrf

                <!-- Pilih Jadwal Pembelajaran -->
                <div class="mb-3">
                    <label for="jadwal_id" class="form-label fw-semibold small text-secondary">
                        JADWAL PEMBELAJARAN <span class="text-danger">*</span>
                    </label>
                    <select name="jadwal_id" id="jadwal_id" class="form-select @error('jadwal_id') is-invalid @enderror" required>
                        <option value="">-- Pilih Jadwal Pembelajaran --</option>
                        @foreach($jadwals as $j)
                            @php
                                $wi = $j->penugasanPengajar->widyaiswara->nama_lengkap ?? ($j->penugasanPengajar->narasumber->nama_lengkap ?? '-');
                                $mp = $j->mataPelatihan->nama_mata_pelatihan ?? '-';
                                $pel = $j->mataPelatihan->angkatan->pelatihan->nama_pelatihan ?? '-';
                                $tgl = $j->tanggal->format('d/m/Y');
                            @endphp
                            <option value="{{ $j->id }}" 
                                    data-tanggal="{{ $j->tanggal->format('Y-m-d') }}"
                                    data-mulai="{{ $j->jam_mulai }}"
                                    data-selesai="{{ $j->jam_selesai }}"
                                    data-judul="Presensi {{ $mp }} - {{ $tgl }}"
                                    {{ (old('jadwal_id', $selectedJadwal->id ?? null) == $j->id) ? 'selected' : '' }}>
                                {{ $tgl }} &bull; {{ $mp }} ({{ substr($j->jam_mulai, 0, 5) }}-{{ substr($j->jam_selesai, 0, 5) }}) - Pengajar: {{ $wi }}
                            </option>
                        @endforeach
                    </select>
                    @error('jadwal_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Kelas Mapel Terkait (Opsional) -->
                <div class="mb-3">
                    <label for="kelas_mapel_id" class="form-label fw-semibold small text-secondary">
                        KELAS MAPEL KCLC (OPSIONAL)
                    </label>
                    <select name="kelas_mapel_id" id="kelas_mapel_id" class="form-select @error('kelas_mapel_id') is-invalid @enderror">
                        <option value="">-- Otomatis mengikuti kelas angkatan --</option>
                        @foreach($kelasMapels as $km)
                            <option value="{{ $km->id }}" {{ old('kelas_mapel_id') == $km->id ? 'selected' : '' }}>
                                {{ $km->nama_kelas }} (Angkatan: {{ $km->angkatan->nomor_angkatan ?? '-' }})
                            </option>
                        @endforeach
                    </select>
                    @error('kelas_mapel_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Judul Sesi -->
                <div class="mb-3">
                    <label for="judul_sesi" class="form-label fw-semibold small text-secondary">
                        JUDUL SESI PRESENSI <span class="text-danger">*</span>
                    </label>
                    <input type="text" 
                           name="judul_sesi" 
                           id="judul_sesi" 
                           class="form-control @error('judul_sesi') is-invalid @enderror" 
                           value="{{ old('judul_sesi') }}" 
                           placeholder="Contoh: Sesi 1 - Pembelajaran Kepemimpinan ASN" 
                           required>
                    @error('judul_sesi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Tanggal & Jam Pelaksanaan -->
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label for="tanggal" class="form-label fw-semibold small text-secondary">
                            TANGGAL SESI <span class="text-danger">*</span>
                        </label>
                        <input type="date" 
                               name="tanggal" 
                               id="tanggal" 
                               class="form-control @error('tanggal') is-invalid @enderror" 
                               value="{{ old('tanggal', date('Y-m-d')) }}" 
                               required>
                        @error('tanggal')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="jam_mulai" class="form-label fw-semibold small text-secondary">
                            JAM MULAI <span class="text-danger">*</span>
                        </label>
                        <input type="time" 
                               name="jam_mulai" 
                               id="jam_mulai" 
                               class="form-control @error('jam_mulai') is-invalid @enderror" 
                               value="{{ old('jam_mulai', '08:00') }}" 
                               required>
                        @error('jam_mulai')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="jam_selesai" class="form-label fw-semibold small text-secondary">
                            JAM SELESAI <span class="text-danger">*</span>
                        </label>
                        <input type="time" 
                               name="jam_selesai" 
                               id="jam_selesai" 
                               class="form-control @error('jam_selesai') is-invalid @enderror" 
                               value="{{ old('jam_selesai', '09:30') }}" 
                               required>
                        @error('jam_selesai')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Toleransi Terlambat & Status Sesi -->
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="toleransi_terlambat_menit" class="form-label fw-semibold small text-secondary">
                            TOLERANSI KETERLAMBATAN (MENIT) <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <input type="number" 
                                   name="toleransi_terlambat_menit" 
                                   id="toleransi_terlambat_menit" 
                                   class="form-control @error('toleransi_terlambat_menit') is-invalid @enderror" 
                                   value="{{ old('toleransi_terlambat_menit', 15) }}" 
                                   min="0" 
                                   max="120" 
                                   required>
                            <span class="input-group-text bg-light text-muted">menit</span>
                        </div>
                        <small class="text-muted d-block mt-1">Presensi lewat dari batas ini otomatis berstatus <strong>Terlambat</strong>.</small>
                        @error('toleransi_terlambat_menit')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="status" class="form-label fw-semibold small text-secondary">
                            STATUS AWAL SESI <span class="text-danger">*</span>
                        </label>
                        <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="Draft" {{ old('status') === 'Draft' ? 'selected' : '' }}>Simpan sebagai Draft (Belum dibuka)</option>
                            <option value="Aktif" {{ old('status', 'Aktif') === 'Aktif' ? 'selected' : '' }}>Langsung Aktifkan (Siap Presensi & QR)</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 border-top pt-3">
                    <a href="{{ route('attendance-sessions.index') }}" class="btn btn-outline-secondary px-4">Batal</a>
                    <button type="submit" class="btn btn-primary px-4 fw-bold">
                        <i class="bi bi-check-circle me-1"></i> Simpan Sesi Presensi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.getElementById('jadwal_id').addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        if (selected.value) {
            const tanggal = selected.getAttribute('data-tanggal');
            const mulai = selected.getAttribute('data-mulai');
            const selesai = selected.getAttribute('data-selesai');
            const judul = selected.getAttribute('data-judul');

            if (tanggal) document.getElementById('tanggal').value = tanggal;
            if (mulai) document.getElementById('jam_mulai').value = mulai.substring(0, 5);
            if (selesai) document.getElementById('jam_selesai').value = selesai.substring(0, 5);
            if (judul && !document.getElementById('judul_sesi').value) {
                document.getElementById('judul_sesi').value = judul;
            }
        }
    });
</script>
@endsection
