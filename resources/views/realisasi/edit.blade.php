@extends('layouts.app')

@section('title', 'Edit Realisasi Kegiatan')
@section('page_title', 'Edit Realisasi Kegiatan')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('realisasi.index') }}">Realisasi</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit Realisasi #{{ $realisasi->id }}</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card-custom">
            <div class="card-custom-header">
                <h5 class="card-custom-title">
                    <i class="bi bi-pencil-square text-primary"></i> Edit Realisasi Pembelajaran
                </h5>
                <span class="badge bg-light text-secondary border">ID Realisasi: #{{ $realisasi->id }}</span>
            </div>
            <div class="p-4">
                <div class="card border-0 bg-light p-3 mb-4 rounded-3">
                    <div class="row g-2">
                        <div class="col-md-4">
                            <span class="text-muted small d-block">Program Pelatihan:</span>
                            <strong class="text-dark">{{ $realisasi->jadwal->mataPelatihan->angkatan->pelatihan->nama_pelatihan }}</strong>
                            <div class="text-muted small">{{ $realisasi->jadwal->mataPelatihan->angkatan->nomor_angkatan }}</div>
                        </div>
                        <div class="col-md-4">
                            <span class="text-muted small d-block">Mata Pelatihan:</span>
                            <strong class="text-dark">{{ $realisasi->jadwal->mataPelatihan->nama_mata_pelatihan }}</strong>
                        </div>
                        <div class="col-md-4">
                            <span class="text-muted small d-block">Diverifikasi Oleh:</span>
                            <span class="text-dark fw-bold">{{ $realisasi->verifikator->name ?? 'Penyelenggara' }}</span>
                            <div class="text-muted small">{{ $realisasi->diverifikasi_pada ? $realisasi->diverifikasi_pada->format('d/m/Y H:i') : '-' }}</div>
                        </div>
                    </div>
                </div>

                <form action="{{ route('realisasi.update', $realisasi) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="status_realisasi" class="form-label fw-semibold small">Status Kegiatan <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status_realisasi" name="status" required>
                                <option value="Terlaksana" {{ old('status', $realisasi->status) == 'Terlaksana' ? 'selected' : '' }}>Terlaksana</option>
                                <option value="Dibatalkan" {{ old('status', $realisasi->status) == 'Dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="tanggal_realisasi" class="form-label fw-semibold small">Tanggal Realisasi <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="tanggal_realisasi" name="tanggal_realisasi" value="{{ old('tanggal_realisasi', $realisasi->tanggal_realisasi->format('Y-m-d')) }}" required>
                        </div>
                        <div class="col-md-2">
                            <label for="jam_mulai_aktual" class="form-label fw-semibold small">Jam Mulai <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" id="jam_mulai_aktual" name="jam_mulai_aktual" value="{{ old('jam_mulai_aktual', substr($realisasi->jam_mulai_aktual, 0, 5)) }}" required>
                        </div>
                        <div class="col-md-2">
                            <label for="jam_selesai_aktual" class="form-label fw-semibold small">Jam Selesai <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" id="jam_selesai_aktual" name="jam_selesai_aktual" value="{{ old('jam_selesai_aktual', substr($realisasi->jam_selesai_aktual, 0, 5)) }}" required>
                        </div>
                    </div>

                    <div class="mb-3 d-none" id="wrapper_alasan_batal">
                        <label for="alasan_batal" class="form-label fw-semibold small text-danger">Alasan Pembatalan <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="alasan_batal" name="alasan_batal" rows="2">{{ old('alasan_batal', $realisasi->alasan_batal) }}</textarea>
                    </div>

                    <div id="wrapper_terlaksana_section">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="total_jp_aktual" class="form-label fw-semibold small">Total JP Aktual <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" step="0.5" class="form-control fw-bold" id="total_jp_aktual" name="total_jp_aktual" value="{{ old('total_jp_aktual', $realisasi->jp_aktual) }}" required>
                                    <span class="input-group-text bg-light">JP</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="total_jp_dibayarkan" class="form-label fw-semibold small">Total JP Dibayarkan <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" step="0.5" class="form-control" id="total_jp_dibayarkan" name="total_jp_dibayarkan" value="{{ old('total_jp_dibayarkan', $realisasi->jp_dibayarkan) }}" required>
                                    <span class="input-group-text bg-light">JP</span>
                                </div>
                            </div>
                        </div>

                        <!-- Tabel Alokasi Pengajar -->
                        <div class="card border mb-3">
                            <div class="card-header bg-light py-2">
                                <span class="fw-semibold small text-dark"><i class="bi bi-people-fill me-1 text-primary"></i> Alokasi JP Pengajar</span>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm mb-0 align-middle">
                                    <thead class="table-light small">
                                        <tr>
                                            <th style="width: 40px;" class="text-center">Pilih</th>
                                            <th>Nama Pengajar</th>
                                            <th>Jenis & Peran</th>
                                            <th style="width: 140px;" class="text-center">JP Aktual Diakui</th>
                                            <th style="width: 140px;" class="text-center">JP Dibayarkan</th>
                                            <th style="width: 130px;">Kehadiran</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($penugasans as $idx => $tgs)
                                            @php
                                                $pengajarId = $tgs->jenis_pengajar === 'Widyaiswara' ? $tgs->widyaiswara_id : $tgs->narasumber_id;
                                                $existingPr = $realisasi->pengajarRealisasis->first(function($pr) use ($tgs, $pengajarId) {
                                                    return $pr->jenis_pengajar === $tgs->jenis_pengajar && 
                                                        (($pr->jenis_pengajar === 'Widyaiswara' && $pr->widyaiswara_id == $pengajarId) ||
                                                         ($pr->jenis_pengajar === 'Narasumber' && $pr->narasumber_id == $pengajarId));
                                                });
                                                $isChecked = $existingPr !== null;
                                                $jpAktual = $existingPr ? $existingPr->jp_aktual : 0;
                                                $jpBayar = $existingPr ? $existingPr->jp_dibayarkan : 0;
                                                $kehadiran = $existingPr ? $existingPr->status_kehadiran : 'Hadir';
                                            @endphp
                                            <tr>
                                                <td class="text-center">
                                                    <input type="checkbox" name="pengajar[{{ $idx }}][selected]" value="1" class="form-check-input" {{ $isChecked ? 'checked' : '' }}>
                                                    <input type="hidden" name="pengajar[{{ $idx }}][penugasan_pengajar_id]" value="{{ $tgs->id }}">
                                                    <input type="hidden" name="pengajar[{{ $idx }}][jenis_pengajar]" value="{{ $tgs->jenis_pengajar }}">
                                                    <input type="hidden" name="pengajar[{{ $idx }}][pengajar_id]" value="{{ $pengajarId }}">
                                                </td>
                                                <td>
                                                    <div class="fw-bold text-dark">{{ $tgs->pengajar_nama }}</div>
                                                    <div class="text-muted" style="font-size: 0.72rem;">{{ $tgs->pengajar_instansi }}</div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $tgs->jenis_pengajar == 'Widyaiswara' ? 'primary' : 'secondary' }}-subtle text-dark border">
                                                        {{ $tgs->jenis_pengajar }}
                                                    </span>
                                                    <span class="badge bg-light text-dark border ms-1">{{ $tgs->peran }}</span>
                                                </td>
                                                <td>
                                                    <input type="number" step="0.5" name="pengajar[{{ $idx }}][jp_aktual]" class="form-control form-control-sm text-center fw-bold" value="{{ $jpAktual }}">
                                                </td>
                                                <td>
                                                    <input type="number" step="0.5" name="pengajar[{{ $idx }}][jp_dibayarkan]" class="form-control form-control-sm text-center" value="{{ $jpBayar }}">
                                                </td>
                                                <td>
                                                    <select name="pengajar[{{ $idx }}][status_kehadiran]" class="form-select form-select-sm">
                                                        <option value="Hadir" {{ $kehadiran == 'Hadir' ? 'selected' : '' }}>Hadir</option>
                                                        <option value="Izin" {{ $kehadiran == 'Izin' ? 'selected' : '' }}>Izin</option>
                                                        <option value="Sakit" {{ $kehadiran == 'Sakit' ? 'selected' : '' }}>Sakit</option>
                                                        <option value="Digantikan" {{ $kehadiran == 'Digantikan' ? 'selected' : '' }}>Digantikan</option>
                                                    </select>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="catatan" class="form-label fw-semibold small">Catatan Realisasi</label>
                        <textarea class="form-control" id="catatan" name="catatan" rows="2">{{ old('catatan', $realisasi->catatan) }}</textarea>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('realisasi.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-navy">
                            <i class="bi bi-check-circle me-1"></i> Simpan Perubahan Realisasi
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
    const statusSelect = document.getElementById('status_realisasi');
    const wrapBatal = document.getElementById('wrapper_alasan_batal');
    const wrapTerlaksana = document.getElementById('wrapper_terlaksana_section');

    function checkStatus() {
        if (statusSelect.value === 'Dibatalkan') {
            wrapBatal.classList.remove('d-none');
            wrapTerlaksana.classList.add('d-none');
        } else {
            wrapBatal.classList.add('d-none');
            wrapTerlaksana.classList.remove('d-none');
        }
    }

    statusSelect.addEventListener('change', checkStatus);
    checkStatus();
});
</script>
@endpush
