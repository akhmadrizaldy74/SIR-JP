@extends('layouts.app')

@section('title', 'Edit Angkatan')
@section('page_title', 'Edit Angkatan Pelatihan')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('angkatan.index') }}">Angkatan</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit Angkatan</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card-custom">
            <div class="card-custom-header">
                <h5 class="card-custom-title">
                    <i class="bi bi-pencil-square text-primary"></i> Edit Angkatan Pelatihan
                </h5>
            </div>
            <div class="p-4">
                <form action="{{ route('angkatan.update', $angkatan) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="pelatihan_id" class="form-label fw-semibold small">Program Pelatihan <span class="text-danger">*</span></label>
                        <select class="form-select @error('pelatihan_id') is-invalid @enderror" id="pelatihan_id" name="pelatihan_id" required>
                            @foreach($pelatihans as $p)
                                <option value="{{ $p->id }}" {{ old('pelatihan_id', $angkatan->pelatihan_id) == $p->id ? 'selected' : '' }}>
                                    {{ $p->nama_pelatihan }} ({{ $p->kode_pelatihan }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nomor_angkatan" class="form-label fw-semibold small">Nama / Nomor Angkatan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nomor_angkatan') is-invalid @enderror" id="nomor_angkatan" name="nomor_angkatan" value="{{ old('nomor_angkatan', $angkatan->nomor_angkatan) }}" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="jumlah_peserta" class="form-label fw-semibold small">Jumlah Peserta <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('jumlah_peserta') is-invalid @enderror" id="jumlah_peserta" name="jumlah_peserta" value="{{ old('jumlah_peserta', $angkatan->jumlah_peserta) }}" min="1" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="tanggal_mulai" class="form-label fw-semibold small">Tanggal Mulai <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('tanggal_mulai') is-invalid @enderror" id="tanggal_mulai" name="tanggal_mulai" value="{{ old('tanggal_mulai', $angkatan->tanggal_mulai->format('Y-m-d')) }}" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="tanggal_selesai" class="form-label fw-semibold small">Tanggal Selesai <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('tanggal_selesai') is-invalid @enderror" id="tanggal_selesai" name="tanggal_selesai" value="{{ old('tanggal_selesai', $angkatan->tanggal_selesai->format('Y-m-d')) }}" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="status" class="form-label fw-semibold small">Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                @foreach($statusOptions as $st)
                                    <option value="{{ $st }}" {{ old('status', $angkatan->status) == $st ? 'selected' : '' }}>{{ $st }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="lokasi" class="form-label fw-semibold small">Lokasi / Ruang</label>
                        <input type="text" class="form-control" id="lokasi" name="lokasi" value="{{ old('lokasi', $angkatan->lokasi) }}">
                    </div>

                    <div class="mb-4">
                        <label for="keterangan" class="form-label fw-semibold small">Keterangan Tambahan</label>
                        <textarea class="form-control" id="keterangan" name="keterangan" rows="3">{{ old('keterangan', $angkatan->keterangan) }}</textarea>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('angkatan.index') }}" class="btn btn-outline-secondary">
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
