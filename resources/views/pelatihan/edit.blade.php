@extends('layouts.app')

@section('title', 'Edit Pelatihan')
@section('page_title', 'Edit Program Pelatihan')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('pelatihan.index') }}">Pelatihan</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit Program</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card-custom">
            <div class="card-custom-header">
                <h5 class="card-custom-title">
                    <i class="bi bi-pencil-square text-primary"></i> Edit Program Pelatihan
                </h5>
            </div>
            <div class="p-4">
                <form action="{{ route('pelatihan.update', $pelatihan) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="kode_pelatihan" class="form-label fw-semibold small">Kode Pelatihan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('kode_pelatihan') is-invalid @enderror" id="kode_pelatihan" name="kode_pelatihan" value="{{ old('kode_pelatihan', $pelatihan->kode_pelatihan) }}" required>
                            @error('kode_pelatihan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-8 mb-3">
                            <label for="nama_pelatihan" class="form-label fw-semibold small">Nama Program Pelatihan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nama_pelatihan') is-invalid @enderror" id="nama_pelatihan" name="nama_pelatihan" value="{{ old('nama_pelatihan', $pelatihan->nama_pelatihan) }}" required>
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
                                    <option value="{{ $kat }}" {{ old('kategori', $pelatihan->kategori) == $kat ? 'selected' : '' }}>{{ $kat }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="penyelenggara" class="form-label fw-semibold small">Penyelenggara <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('penyelenggara') is-invalid @enderror" id="penyelenggara" name="penyelenggara" value="{{ old('penyelenggara', $pelatihan->penyelenggara) }}" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="tanggal_mulai" class="form-label fw-semibold small">Tanggal Mulai <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('tanggal_mulai') is-invalid @enderror" id="tanggal_mulai" name="tanggal_mulai" value="{{ old('tanggal_mulai', $pelatihan->tanggal_mulai->format('Y-m-d')) }}" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="tanggal_selesai" class="form-label fw-semibold small">Tanggal Selesai <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('tanggal_selesai') is-invalid @enderror" id="tanggal_selesai" name="tanggal_selesai" value="{{ old('tanggal_selesai', $pelatihan->tanggal_selesai->format('Y-m-d')) }}" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="status" class="form-label fw-semibold small">Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                @foreach($statusOptions as $st)
                                    <option value="{{ $st }}" {{ old('status', $pelatihan->status) == $st ? 'selected' : '' }}>{{ $st }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="keterangan" class="form-label fw-semibold small">Keterangan / Kurikulum Acuan</label>
                        <textarea class="form-control" id="keterangan" name="keterangan" rows="3">{{ old('keterangan', $pelatihan->keterangan) }}</textarea>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('pelatihan.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-navy">
                            <i class="bi bi-check-circle me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
