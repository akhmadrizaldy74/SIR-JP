@extends('portal.layout')

@section('title', 'Pengajuan Izin, Sakit & Dispensasi')

@section('content')
<div class="mb-3 d-flex align-items-center justify-content-between">
    <a href="{{ route('portal.dashboard') }}" class="btn btn-sm btn-light border rounded-pill px-3">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
    <h6 class="fw-bold mb-0 text-dark">Form Pengajuan Ketidakhadiran</h6>
    <span style="width: 70px;"></span>
</div>

<!-- Form Pengajuan Baru -->
<div class="card card-custom p-3 mb-4 border shadow-sm">
    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
        <i class="bi bi-pencil-square text-primary me-1"></i> Buat Pengajuan Baru
    </h6>

    <form action="{{ route('portal.pengajuan.post') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- Pilih Sesi Pembelajaran -->
        <div class="mb-3">
            <label for="attendance_session_id" class="form-label fw-semibold small text-secondary">SESI PEMBELAJARAN <span class="text-danger">*</span></label>
            <select name="attendance_session_id" id="attendance_session_id" class="form-select form-select-sm" required>
                <option value="">-- Pilih Sesi Pembelajaran Terkait --</option>
                @foreach($availableSessions as $sess)
                    <option value="{{ $sess->id }}" {{ old('attendance_session_id') == $sess->id ? 'selected' : '' }}>
                        {{ $sess->tanggal->format('d/m/Y') }} &bull; {{ $sess->judul_sesi }} ({{ substr($sess->jam_mulai, 0, 5) }}-{{ substr($sess->jam_selesai, 0, 5) }} WIB)
                    </option>
                @endforeach
            </select>
            @error('attendance_session_id')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <!-- Jenis Pengajuan -->
        <div class="mb-3">
            <label class="form-label fw-semibold small text-secondary d-block">JENIS PENGAJUAN <span class="text-danger">*</span></label>
            <div class="btn-group w-100" role="group">
                <input type="radio" class="btn-check" name="jenis_pengajuan" id="jenisIzin" value="Izin" {{ old('jenis_pengajuan', 'Izin') === 'Izin' ? 'checked' : '' }} required>
                <label class="btn btn-outline-warning text-dark btn-sm py-2" for="jenisIzin">
                    <i class="bi bi-envelope-paper"></i> Izin
                </label>

                <input type="radio" class="btn-check" name="jenis_pengajuan" id="jenisSakit" value="Sakit" {{ old('jenis_pengajuan') === 'Sakit' ? 'checked' : '' }}>
                <label class="btn btn-outline-danger btn-sm py-2" for="jenisSakit">
                    <i class="bi bi-heart-pulse"></i> Sakit
                </label>

                <input type="radio" class="btn-check" name="jenis_pengajuan" id="jenisDispensasi" value="Dispensasi" {{ old('jenis_pengajuan') === 'Dispensasi' ? 'checked' : '' }}>
                <label class="btn btn-outline-dark btn-sm py-2" for="jenisDispensasi">
                    <i class="bi bi-award"></i> Dispensasi
                </label>
            </div>
            @error('jenis_pengajuan')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <!-- Alasan & Keterangan -->
        <div class="mb-3">
            <label for="alasan" class="form-label fw-semibold small text-secondary">ALASAN & KETERANGAN <span class="text-danger">*</span></label>
            <textarea name="alasan" 
                      id="alasan" 
                      rows="3" 
                      class="form-control form-control-sm" 
                      placeholder="Jelaskan alasan ketidakhadiran Anda secara rinci..." 
                      required>{{ old('alasan') }}</textarea>
            @error('alasan')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <!-- Upload Berkas Bukti (Surat Izin / Surat Dokter / Tugas) -->
        <div class="mb-3">
            <label for="bukti_file" class="form-label fw-semibold small text-secondary">
                LAMPIRAN BUKTI DOKUMEN (SURAT DOKTER / SURAT TUGAS / SURAT IZIN)
            </label>
            <input type="file" 
                   name="bukti_file[]" 
                   id="bukti_file" 
                   class="form-control form-control-sm" 
                   accept=".jpg,.jpeg,.png,.pdf" 
                   multiple>
            <small class="text-muted d-block mt-1" style="font-size: 0.72rem;">
                Maksimal ukuran 5 MB. Format didukung: PDF, JPG, PNG.
            </small>
            @error('bukti_file.*')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2">
            <i class="bi bi-send-fill"></i>
            <span>Kirim Pengajuan</span>
        </button>
    </form>
</div>

<!-- Riwayat Pengajuan Saya -->
<div class="card card-custom p-3 border shadow-sm">
    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
        <i class="bi bi-clock-history text-primary me-1"></i> Riwayat Pengajuan Anda
    </h6>

    <div class="d-flex flex-column gap-2">
        @forelse($myRequests as $req)
            @php
                $statusBadge = match($req->status) {
                    'Approved' => ['class' => 'bg-success text-white', 'icon' => 'bi-check-circle-fill', 'label' => 'Disetujui'],
                    'Rejected' => ['class' => 'bg-danger text-white', 'icon' => 'bi-x-circle-fill', 'label' => 'Ditolak'],
                    default => ['class' => 'bg-warning text-dark', 'icon' => 'bi-hourglass-split', 'label' => 'Menunggu Verifikasi'],
                };
            @endphp
            <div class="card p-2 border rounded-3 bg-light">
                <div class="d-flex justify-content-between align-items-start mb-1">
                    <div>
                        <span class="badge bg-secondary small">{{ $req->jenis_pengajuan }}</span>
                        <span class="text-muted small ms-1">{{ $req->created_at->format('d/m/Y H:i') }}</span>
                        <div class="fw-bold text-dark small mt-1">{{ $req->session->judul_sesi ?? 'Sesi Pembelajaran' }}</div>
                    </div>
                    <span class="badge {{ $statusBadge['class'] }} rounded-pill px-2 py-1" style="font-size: 0.72rem;">
                        <i class="bi {{ $statusBadge['icon'] }}"></i> {{ $statusBadge['label'] }}
                    </span>
                </div>

                <div class="small text-muted bg-white p-2 rounded border mt-1">
                    <strong>Alasan:</strong> {{ $req->alasan }}
                </div>

                @if($req->catatan_admin)
                    <div class="small p-2 rounded border mt-1 {{ $req->status === 'Rejected' ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success' }}">
                        <strong>Catatan Admin:</strong> {{ $req->catatan_admin }}
                    </div>
                @endif

                @if($req->evidences->isNotEmpty())
                    <div class="mt-2 d-flex gap-1 flex-wrap">
                        @foreach($req->evidences as $ev)
                            <a href="{{ route('attendance-evidences.download', $ev->id) }}" target="_blank" class="btn btn-xs btn-outline-secondary py-0 px-2 rounded-pill" style="font-size: 0.7rem;">
                                <i class="bi bi-paperclip"></i> {{ $ev->nama_file_asli }}
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        @empty
            <div class="text-center text-muted py-3">
                <i class="bi bi-file-earmark-check fs-2 text-secondary mb-1"></i>
                <div class="small">Belum pernah membuat pengajuan ketidakhadiran.</div>
            </div>
        @endforelse
    </div>

    <div class="mt-2">
        {{ $myRequests->links('vendor.pagination.bootstrap-5') }}
    </div>
</div>
@endsection
