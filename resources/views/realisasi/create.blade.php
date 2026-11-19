@extends('layouts.app')

@section('title', 'Konfirmasi Realisasi Kegiatan')
@section('page_title', 'Konfirmasi Realisasi Kegiatan')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('realisasi.index') }}">Realisasi</a></li>
    <li class="breadcrumb-item active" aria-current="page">Konfirmasi Jadwal #{{ $jadwal->id }}</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card-custom">
            <div class="card-custom-header">
                <h5 class="card-custom-title">
                    <i class="bi bi-check2-circle text-success"></i> Konfirmasi Realisasi Pembelajaran
                </h5>
                <span class="badge bg-light text-primary border">ID Jadwal: #{{ $jadwal->id }}</span>
            </div>
            <div class="p-4">
                <!-- Info Jadwal Asal -->
                <div class="card border-0 bg-light p-3 mb-4 rounded-3">
                    <div class="row g-2">
                        <div class="col-md-4">
                            <span class="text-muted small d-block">Program Pelatihan:</span>
                            <strong class="text-dark">{{ $jadwal->mataPelatihan->angkatan->pelatihan->nama_pelatihan }}</strong>
                            <div class="text-muted small">{{ $jadwal->mataPelatihan->angkatan->nomor_angkatan }}</div>
                        </div>
                        <div class="col-md-4">
                            <span class="text-muted small d-block">Mata Pelatihan:</span>
                            <strong class="text-dark">{{ $jadwal->mataPelatihan->nama_mata_pelatihan }}</strong>
                            <div class="text-muted small">Ruangan: {{ $jadwal->ruangan ?? 'Kelas' }} &bull; {{ $jadwal->metode }}</div>
                        </div>
                        <div class="col-md-4">
                            <span class="text-muted small d-block">Rencana Tanggal & Sesi:</span>
                            <span class="text-dark fw-bold">{{ $jadwal->tanggal->format('d/m/Y') }}</span>
                            <span class="text-muted small">({{ substr($jadwal->jam_mulai, 0, 5) }} - {{ substr($jadwal->jam_selesai, 0, 5) }} WIB &bull; {{ $jadwal->jp_rencana }} JP)</span>
                        </div>
                    </div>
                </div>

                <form action="{{ route('realisasi.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="jadwal_id" value="{{ $jadwal->id }}">

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="status_realisasi" class="form-label fw-semibold small">Status Kegiatan <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status_realisasi" name="status" required>
                                <option value="Terlaksana" selected>Terlaksana</option>
                                <option value="Dibatalkan">Dibatalkan</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="tanggal_realisasi" class="form-label fw-semibold small">Tanggal Realisasi Aktual <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="tanggal_realisasi" name="tanggal_realisasi" value="{{ old('tanggal_realisasi', $jadwal->tanggal->format('Y-m-d')) }}" required>
                        </div>
                        <div class="col-md-2">
                            <label for="jam_mulai_aktual" class="form-label fw-semibold small">Jam Mulai <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" id="jam_mulai_aktual" name="jam_mulai_aktual" value="{{ old('jam_mulai_aktual', substr($jadwal->jam_mulai, 0, 5)) }}" required>
                        </div>
                        <div class="col-md-2">
                            <label for="jam_selesai_aktual" class="form-label fw-semibold small">Jam Selesai <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" id="jam_selesai_aktual" name="jam_selesai_aktual" value="{{ old('jam_selesai_aktual', substr($jadwal->jam_selesai, 0, 5)) }}" required>
                        </div>
                    </div>

                    <!-- Input Alasan Batal jika Dibatalkan -->
                    <div class="mb-3 d-none" id="wrapper_alasan_batal">
                        <label for="alasan_batal" class="form-label fw-semibold small text-danger">Alasan Pembatalan <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="alasan_batal" name="alasan_batal" rows="2" placeholder="Tuliskan alasan pembatalan sesi..."></textarea>
                        <div class="form-text small text-danger">Catatan: Sesi yang dibatalkan tidak akan dihitung ke dalam rekapitulasi Jam Pelajaran (JP).</div>
                    </div>

                    <!-- Bagian Terlaksana: Total JP & Pembagian Pengajar -->
                    <div id="wrapper_terlaksana_section">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="total_jp_aktual" class="form-label fw-semibold small">Total JP Aktual Sesi <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" step="0.5" class="form-control fw-bold" id="total_jp_aktual" name="total_jp_aktual" value="{{ old('total_jp_aktual', $jadwal->jp_rencana) }}" required>
                                    <span class="input-group-text bg-light">JP</span>
                                </div>
                                <div class="form-text small">Hanya kegiatan Terlaksana yang dihitung ke rekapitulasi JP.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="total_jp_dibayarkan" class="form-label fw-semibold small">Total JP Dibayarkan Sesi <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" step="0.5" class="form-control" id="total_jp_dibayarkan" name="total_jp_dibayarkan" value="{{ old('total_jp_dibayarkan', $jadwal->jp_rencana) }}" required>
                                    <span class="input-group-text bg-light">JP</span>
                                </div>
                                <div class="form-text small">Aturan: JP Dibayarkan tidak boleh otomatis disamakan dengan Total JP tanpa konfirmasi.</div>
                            </div>
                        </div>

                        <!-- Tabel Alokasi Pengajar -->
                        <div class="card border mb-3">
                            <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                                <span class="fw-semibold small text-dark"><i class="bi bi-people-fill me-1 text-primary"></i> Alokasi JP untuk Pengajar Bertugas</span>
                                <span class="small text-muted">Centang pengajar yang hadir dan masukkan pembagian JP</span>
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
                                        @forelse($penugasans as $idx => $tgs)
                                            @php
                                                $isDefaultAssigned = ($jadwal->penugasan_pengajar_id == $tgs->id) || ($penugasans->count() == 1);
                                                $pengajarId = $tgs->jenis_pengajar === 'Widyaiswara' ? $tgs->widyaiswara_id : $tgs->narasumber_id;
                                            @endphp
                                            <tr>
                                                <td class="text-center">
                                                    <input type="checkbox" name="pengajar[{{ $idx }}][selected]" value="1" class="form-check-input check-pengajar" {{ $isDefaultAssigned ? 'checked' : '' }}>
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
                                                    <input type="number" step="0.5" name="pengajar[{{ $idx }}][jp_aktual]" class="form-control form-control-sm text-center fw-bold" value="{{ $isDefaultAssigned ? $jadwal->jp_rencana : 0 }}">
                                                </td>
                                                <td>
                                                    <input type="number" step="0.5" name="pengajar[{{ $idx }}][jp_dibayarkan]" class="form-control form-control-sm text-center" value="{{ $isDefaultAssigned ? $jadwal->jp_rencana : 0 }}">
                                                </td>
                                                <td>
                                                    <select name="pengajar[{{ $idx }}][status_kehadiran]" class="form-select form-select-sm">
                                                        <option value="Hadir" selected>Hadir</option>
                                                        <option value="Izin">Izin</option>
                                                        <option value="Sakit">Sakit</option>
                                                        <option value="Digantikan">Digantikan</option>
                                                    </select>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center text-muted py-3">
                                                    Belum ada pengajar yang ditugaskan pada mata pelatihan ini. Silakan tambahkan penugasan pengajar terlebih dahulu.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="catatan" class="form-label fw-semibold small">Catatan Realisasi Pembelajaran</label>
                        <textarea class="form-control" id="catatan" name="catatan" rows="2" placeholder="Catatan tambahan jalannya sesi pembelajaran...">{{ old('catatan') }}</textarea>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('jadwal.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-navy">
                            <i class="bi bi-check-circle me-1"></i> Simpan & Konfirmasi Realisasi
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
