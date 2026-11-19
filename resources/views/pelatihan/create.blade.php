@extends('layouts.app')

@section('title', 'Tambah Pelatihan')
@section('page_title', 'Tambah Program Pelatihan')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('pelatihan.index') }}">Pelatihan</a></li>
    <li class="breadcrumb-item active" aria-current="page">Tambah Program</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card-custom">
            <div class="card-custom-header">
                <h5 class="card-custom-title">
                    <i class="bi bi-mortarboard-fill text-primary"></i> Formulir Program Pelatihan Baru
                </h5>
            </div>
            <div class="p-4">
                <form action="{{ route('pelatihan.store') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="kode_pelatihan" class="form-label fw-semibold small">Kode Pelatihan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('kode_pelatihan') is-invalid @enderror" id="kode_pelatihan" name="kode_pelatihan" value="{{ old('kode_pelatihan') }}" placeholder="Contoh: PKP-2026-01" required>
                            @error('kode_pelatihan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-8 mb-3">
                            <label for="nama_pelatihan" class="form-label fw-semibold small">Nama Program Pelatihan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nama_pelatihan') is-invalid @enderror" id="nama_pelatihan" name="nama_pelatihan" value="{{ old('nama_pelatihan') }}" placeholder="Contoh: Pelatihan Kepemimpinan Pengawas" required>
                            @error('nama_pelatihan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="kategori" class="form-label fw-semibold small">Kategori Pelatihan <span class="text-danger">*</span></label>
                            <select class="form-select @error('kategori') is-invalid @enderror" id="kategori" name="kategori" required>
                                @foreach($kategoriOptions as $kat)
                                    <option value="{{ $kat }}" {{ old('kategori') == $kat ? 'selected' : '' }}>{{ $kat }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="penyelenggara" class="form-label fw-semibold small">Instansi Penyelenggara <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('penyelenggara') is-invalid @enderror" id="penyelenggara" name="penyelenggara" value="{{ old('penyelenggara', \App\Models\Pengaturan::get('instansi_nama', 'PPSDM Kemendagri')) }}" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="tanggal_mulai" class="form-label fw-semibold small">Tanggal Mulai <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('tanggal_mulai') is-invalid @enderror" id="tanggal_mulai" name="tanggal_mulai" value="{{ old('tanggal_mulai', date('Y-m-d')) }}" required>
                            @error('tanggal_mulai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="tanggal_selesai" class="form-label fw-semibold small">Tanggal Selesai <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('tanggal_selesai') is-invalid @enderror" id="tanggal_selesai" name="tanggal_selesai" value="{{ old('tanggal_selesai', date('Y-m-d', strtotime('+30 days'))) }}" required>
                            @error('tanggal_selesai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="status" class="form-label fw-semibold small">Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="Direncanakan" {{ old('status') == 'Direncanakan' ? 'selected' : '' }}>Direncanakan</option>
                                <option value="Berjalan" {{ old('status') == 'Berjalan' ? 'selected' : '' }}>Berjalan</option>
                                <option value="Selesai" {{ old('status') == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                                <option value="Dibatalkan" {{ old('status') == 'Dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="keterangan" class="form-label fw-semibold small">Keterangan / Kurikulum Acuan</label>
                        <textarea class="form-control" id="keterangan" name="keterangan" rows="3" placeholder="Informasi tambahan mengenai sasaran pelatihan, pola penyelenggaraan, dll.">{{ old('keterangan') }}</textarea>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('pelatihan.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-navy">
                            <i class="bi bi-save me-1"></i> Simpan Pelatihan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
