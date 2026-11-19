@extends('layouts.app')

@section('title', 'Tambah Angkatan')
@section('page_title', 'Tambah Angkatan Pelatihan')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('angkatan.index') }}">Angkatan</a></li>
    <li class="breadcrumb-item active" aria-current="page">Tambah Angkatan</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card-custom">
            <div class="card-custom-header">
                <h5 class="card-custom-title">
                    <i class="bi bi-people-fill text-primary"></i> Formulir Tambah Angkatan
                </h5>
            </div>
            <div class="p-4">
                <form action="{{ route('angkatan.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="pelatihan_id" class="form-label fw-semibold small">Program Pelatihan Induk <span class="text-danger">*</span></label>
                        <select class="form-select @error('pelatihan_id') is-invalid @enderror" id="pelatihan_id" name="pelatihan_id" required>
                            <option value="">-- Pilih Program Pelatihan --</option>
                            @foreach($pelatihans as $p)
                                <option value="{{ $p->id }}" {{ (old('pelatihan_id') ?? $selectedPelatihanId) == $p->id ? 'selected' : '' }}>
                                    {{ $p->nama_pelatihan }} ({{ $p->kode_pelatihan }})
                                </option>
                            @endforeach
                        </select>
                        @error('pelatihan_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nomor_angkatan" class="form-label fw-semibold small">Nama / Nomor Angkatan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nomor_angkatan') is-invalid @enderror" id="nomor_angkatan" name="nomor_angkatan" value="{{ old('nomor_angkatan') }}" placeholder="Contoh: Angkatan I / Gelombang 2" required>
                            @error('nomor_angkatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="jumlah_peserta" class="form-label fw-semibold small">Jumlah Peserta <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('jumlah_peserta') is-invalid @enderror" id="jumlah_peserta" name="jumlah_peserta" value="{{ old('jumlah_peserta', 40) }}" min="1" required>
                            @error('jumlah_peserta')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="tanggal_mulai" class="form-label fw-semibold small">Tanggal Mulai <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('tanggal_mulai') is-invalid @enderror" id="tanggal_mulai" name="tanggal_mulai" value="{{ old('tanggal_mulai', date('Y-m-d')) }}" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="tanggal_selesai" class="form-label fw-semibold small">Tanggal Selesai <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('tanggal_selesai') is-invalid @enderror" id="tanggal_selesai" name="tanggal_selesai" value="{{ old('tanggal_selesai', date('Y-m-d', strtotime('+30 days'))) }}" required>
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

                    <div class="mb-3">
                        <label for="lokasi" class="form-label fw-semibold small">Lokasi / Ruang Pembelajaran</label>
                        <input type="text" class="form-control" id="lokasi" name="lokasi" value="{{ old('lokasi') }}" placeholder="Contoh: Gedung A Lantai 2 / Zoom Online">
                    </div>

                    <div class="mb-4">
                        <label for="keterangan" class="form-label fw-semibold small">Keterangan Tambahan</label>
                        <textarea class="form-control" id="keterangan" name="keterangan" rows="3">{{ old('keterangan') }}</textarea>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('angkatan.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-navy">
                            <i class="bi bi-save me-1"></i> Simpan Angkatan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
