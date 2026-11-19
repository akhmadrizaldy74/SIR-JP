@extends('layouts.app')

@section('title', 'Tambah Konversi JP')
@section('page_title', 'Tambah Kegiatan Konversi JP')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('konversi.index') }}">Konversi JP</a></li>
    <li class="breadcrumb-item active" aria-current="page">Tambah Kegiatan</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card-custom">
            <div class="card-custom-header">
                <h5 class="card-custom-title">
                    <i class="bi bi-arrow-left-right text-primary"></i> Formulir Pencatatan JP Konversi
                </h5>
            </div>
            <div class="p-4">
                <form action="{{ route('konversi.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="widyaiswara_id" class="form-label fw-semibold small">Nama Widyaiswara <span class="text-danger">*</span></label>
                        <select class="form-select @error('widyaiswara_id') is-invalid @enderror" id="widyaiswara_id" name="widyaiswara_id" required>
                            <option value="">-- Pilih Widyaiswara --</option>
                            @foreach($widyaiswaras as $wi)
                                <option value="{{ $wi->id }}" {{ old('widyaiswara_id') == $wi->id ? 'selected' : '' }}>
                                    {{ $wi->nama_gelar }} - {{ $wi->jabatan }}
                                </option>
                            @endforeach
                        </select>
                        @error('widyaiswara_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="tanggal_kegiatan" class="form-label fw-semibold small">Tanggal Kegiatan <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('tanggal_kegiatan') is-invalid @enderror" id="tanggal_kegiatan" name="tanggal_kegiatan" value="{{ old('tanggal_kegiatan', date('Y-m-d')) }}" required>
                            @error('tanggal_kegiatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-8 mb-3">
                            <label for="nama_kegiatan" class="form-label fw-semibold small">Nama Kegiatan Konversi <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nama_kegiatan') is-invalid @enderror" id="nama_kegiatan" name="nama_kegiatan" value="{{ old('nama_kegiatan') }}" placeholder="Contoh: Penyusunan Modul Pelatihan Kepemimpinan Pengawas" required>
                            @error('nama_kegiatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="kategori_konversi" class="form-label fw-semibold small">Kategori Konversi <span class="text-danger">*</span></label>
                            <select class="form-select @error('kategori_konversi') is-invalid @enderror" id="kategori_konversi" name="kategori_konversi" required>
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($kategoriOptions as $kat)
                                    <option value="{{ $kat }}" {{ old('kategori_konversi') == $kat ? 'selected' : '' }}>{{ $kat }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="jumlah_jp" class="form-label fw-semibold small">Jumlah Jam Pelajaran (JP) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" step="0.5" class="form-control fw-bold @error('jumlah_jp') is-invalid @enderror" id="jumlah_jp" name="jumlah_jp" value="{{ old('jumlah_jp') }}" min="0.5" required>
                                <span class="input-group-text bg-light">JP</span>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="jp_dibayarkan" class="form-label fw-semibold small">JP Dibayarkan <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" step="0.5" class="form-control @error('jp_dibayarkan') is-invalid @enderror" id="jp_dibayarkan" name="jp_dibayarkan" value="{{ old('jp_dibayarkan', 0) }}" min="0" required>
                                <span class="input-group-text bg-light">JP</span>
                            </div>
                            <div class="form-text small">Kosongkan/isi 0 jika non-budgeter.</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="informasi_pembayaran" class="form-label fw-semibold small">Informasi Pembayaran</label>
                            <input type="text" class="form-control" id="informasi_pembayaran" name="informasi_pembayaran" value="{{ old('informasi_pembayaran') }}" placeholder="Contoh: DIPA PPSDM 2026 / Non-Budgeter">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="nomor_sk" class="form-label fw-semibold small">Nomor SK / Surat Tugas</label>
                            <input type="text" class="form-control" id="nomor_sk" name="nomor_sk" value="{{ old('nomor_sk') }}" placeholder="Contoh: SK.800/124/PPSDM/2026">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="status_validasi" class="form-label fw-semibold small">Status Validasi <span class="text-danger">*</span></label>
                            <select class="form-select" id="status_validasi" name="status_validasi" required>
                                <option value="Disetujui" selected>Disetujui</option>
                                <option value="Menunggu">Menunggu</option>
                                <option value="Ditolak">Ditolak</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="keterangan" class="form-label fw-semibold small">Keterangan / Uraian Tugas</label>
                        <textarea class="form-control" id="keterangan" name="keterangan" rows="3" placeholder="Uraian kegiatan atau output yang dihasilkan">{{ old('keterangan') }}</textarea>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('konversi.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-navy">
                            <i class="bi bi-save me-1"></i> Simpan Kegiatan Konversi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
