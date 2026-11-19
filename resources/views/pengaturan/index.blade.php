@extends('layouts.app')

@section('title', 'Pengaturan Sistem')
@section('page_title', 'Pengaturan Sistem & Kebijakan')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Pengaturan</li>
@endsection

@section('content')
<div class="row g-4">
    <!-- Kolom Kiri: Instansi, Tanda Tangan & Aturan Beban JP -->
    <div class="col-lg-7">
        <div class="card-custom mb-4">
            <div class="card-custom-header">
                <h5 class="card-custom-title">
                    <i class="bi bi-building text-primary"></i> Profil Instansi &amp; Penandatangan Laporan
                </h5>
            </div>
            <div class="p-4">
                <form action="{{ route('pengaturan.update.umum') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">1. Identitas Instansi</h6>
                    <div class="mb-3">
                        <label for="instansi_nama" class="form-label fw-semibold small">Nama Instansi / Lembaga Penyelenggara <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="instansi_nama" name="instansi_nama" value="{{ old('instansi_nama', $settings['instansi_nama'] ?? 'PPSDM Kemendagri') }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="instansi_alamat" class="form-label fw-semibold small">Alamat Instansi</label>
                        <textarea class="form-control" id="instansi_alamat" name="instansi_alamat" rows="2">{{ old('instansi_alamat', $settings['instansi_alamat'] ?? '') }}</textarea>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6 mb-2">
                            <label for="instansi_telepon" class="form-label fw-semibold small">Nomor Telepon</label>
                            <input type="text" class="form-control" id="instansi_telepon" name="instansi_telepon" value="{{ old('instansi_telepon', $settings['instansi_telepon'] ?? '') }}">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label for="instansi_email" class="form-label fw-semibold small">Email Instansi</label>
                            <input type="email" class="form-control" id="instansi_email" name="instansi_email" value="{{ old('instansi_email', $settings['instansi_email'] ?? '') }}">
                        </div>
                    </div>

                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">2. Pejabat Penandatangan Laporan Rekapitulasi</h6>
                    <div class="row mb-3">
                        <div class="col-md-7 mb-2">
                            <label for="pimpinan_nama" class="form-label fw-semibold small">Nama Lengkap & Gelar Pimpinan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="pimpinan_nama" name="pimpinan_nama" value="{{ old('pimpinan_nama', $settings['pimpinan_nama'] ?? '') }}" required>
                        </div>
                        <div class="col-md-5 mb-2">
                            <label for="pimpinan_nip" class="form-label fw-semibold small">NIP Pimpinan</label>
                            <input type="text" class="form-control" id="pimpinan_nip" name="pimpinan_nip" value="{{ old('pimpinan_nip', $settings['pimpinan_nip'] ?? '') }}">
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-7 mb-2">
                            <label for="pimpinan_jabatan" class="form-label fw-semibold small">Jabatan Penandatangan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="pimpinan_jabatan" name="pimpinan_jabatan" value="{{ old('pimpinan_jabatan', $settings['pimpinan_jabatan'] ?? '') }}" required>
                        </div>
                        <div class="col-md-5 mb-2">
                            <label for="pimpinan_kota" class="form-label fw-semibold small">Kota Domisili Penandatangan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="pimpinan_kota" name="pimpinan_kota" value="{{ old('pimpinan_kota', $settings['pimpinan_kota'] ?? 'Jakarta') }}" required>
                        </div>
                    </div>

                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">3. Kebijakan Batas Minimum Jam Pelajaran (JP)</h6>
                    <div class="mb-4 p-3 bg-light rounded border">
                        <label for="minimum_jp_bulanan" class="form-label fw-semibold small">Batas Beban Mengajar Minimal Widyaiswara / Bulan (JP) <span class="text-danger">*</span></label>
                        <div class="input-group" style="max-width: 200px;">
                            <input type="number" step="1" class="form-control fw-bold fs-5 text-primary" id="minimum_jp_bulanan" name="minimum_jp_bulanan" value="{{ old('minimum_jp_bulanan', $settings['minimum_jp_bulanan'] ?? '32') }}" min="1" required>
                            <span class="input-group-text bg-white">JP / Bulan</span>
                        </div>
                        <div class="form-text small mt-1">Berdasarkan kebijakan PPSDM standar adalah 32 JP per bulan. Dapat dikonfigurasi bila terdapat kebijakan baru.</div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-navy">
                            <i class="bi bi-check-circle me-1"></i> Simpan Seluruh Pengaturan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Template Sesi Standar -->
    <div class="col-lg-5">
        <div class="card-custom">
            <div class="card-custom-header">
                <h5 class="card-custom-title">
                    <i class="bi bi-clock-history text-primary"></i> Template Sesi Standar
                </h5>
                <span class="badge bg-light text-secondary border">1 Sesi = 45m = 1 JP</span>
            </div>
            <div class="p-3">
                <p class="text-muted small mb-3">
                    Jadwal standar pembelajaran yang digunakan dalam generate sesi harian:
                </p>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm small align-middle mb-0">
                        <thead class="table-light text-center">
                            <tr>
                                <th>Sesi</th>
                                <th>Jam</th>
                                <th>Durasi</th>
                                <th>JP</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($templateSesies as $sesi)
                                <tr class="{{ $sesi->tipe !== 'Pembelajaran' ? 'table-light text-muted' : '' }}">
                                    <td class="fw-semibold">{{ $sesi->nama_sesi }}</td>
                                    <td class="text-center">{{ substr($sesi->jam_mulai, 0, 5) }} - {{ substr($sesi->jam_selesai, 0, 5) }}</td>
                                    <td class="text-center">{{ $sesi->durasi_menit }}m</td>
                                    <td class="text-center fw-bold {{ $sesi->jumlah_jp > 0 ? 'text-primary' : '' }}">{{ $sesi->jumlah_jp }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ $sesi->is_active ? 'success' : 'secondary' }}" style="font-size: 0.65rem;">
                                            {{ $sesi->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
