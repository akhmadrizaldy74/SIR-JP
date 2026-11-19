@extends('layouts.app')

@section('title', 'Edit Mata Pelatihan')
@section('page_title', 'Edit Mata Pelatihan')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('mata-pelatihan.index') }}">Mata Pelatihan</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit Data</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card-custom">
            <div class="card-custom-header">
                <h5 class="card-custom-title">
                    <i class="bi bi-pencil-square text-primary"></i> Edit Mata Pelatihan
                </h5>
            </div>
            <div class="p-4">
                <form action="{{ route('mata-pelatihan.update', $mataPelatihan) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="angkatan_id" class="form-label fw-semibold small">Angkatan Pelatihan <span class="text-danger">*</span></label>
                        <select class="form-select @error('angkatan_id') is-invalid @enderror" id="angkatan_id" name="angkatan_id" required>
                            @foreach($angkatans as $ang)
                                <option value="{{ $ang->id }}" {{ old('angkatan_id', $mataPelatihan->angkatan_id) == $ang->id ? 'selected' : '' }}>
                                    {{ $ang->pelatihan->nama_pelatihan }} - {{ $ang->nomor_angkatan }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="nama_mata_pelatihan" class="form-label fw-semibold small">Nama Mata Pelatihan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nama_mata_pelatihan') is-invalid @enderror" id="nama_mata_pelatihan" name="nama_mata_pelatihan" value="{{ old('nama_mata_pelatihan', $mataPelatihan->nama_mata_pelatihan) }}" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="rencana_jp" class="form-label fw-semibold small">Rencana Jam Pelajaran (JP) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" step="0.5" class="form-control @error('rencana_jp') is-invalid @enderror" id="rencana_jp" name="rencana_jp" value="{{ old('rencana_jp', $mataPelatihan->rencana_jp) }}" min="0.5" required>
                                <span class="input-group-text bg-light">JP</span>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="deskripsi" class="form-label fw-semibold small">Deskripsi Singkat Materi</label>
                        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="2">{{ old('deskripsi', $mataPelatihan->deskripsi) }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label for="keterangan" class="form-label fw-semibold small">Keterangan / Metode</label>
                        <input type="text" class="form-control" id="keterangan" name="keterangan" value="{{ old('keterangan', $mataPelatihan->keterangan) }}">
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('mata-pelatihan.index') }}" class="btn btn-outline-secondary">
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
