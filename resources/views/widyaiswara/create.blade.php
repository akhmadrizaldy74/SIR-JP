@extends('layouts.app')

@section('title', 'Tambah Widyaiswara')
@section('page_title', 'Tambah Widyaiswara')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('widyaiswara.index') }}">Widyaiswara</a></li>
    <li class="breadcrumb-item active" aria-current="page">Tambah Data</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card-custom">
            <div class="card-custom-header">
                <h5 class="card-custom-title">
                    <i class="bi bi-person-plus-fill text-primary"></i> Formulir Tambah Data Widyaiswara
                </h5>
            </div>
            <div class="p-4">
                <form action="{{ route('widyaiswara.store') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nik" class="form-label fw-semibold small">NIK (Nomor Induk Kependudukan) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nik') is-invalid @enderror" id="nik" name="nik" value="{{ old('nik') }}" placeholder="16 digit NIK" required>
                            @error('nik')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="nip" class="form-label fw-semibold small">NIP (Nomor Induk Pegawai)</label>
                            <input type="text" class="form-control @error('nip') is-invalid @enderror" id="nip" name="nip" value="{{ old('nip') }}" placeholder="Contoh: 19850101 201001 1 001">
                            @error('nip')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="gelar_depan" class="form-label fw-semibold small">Gelar Depan</label>
                            <input type="text" class="form-control @error('gelar_depan') is-invalid @enderror" id="gelar_depan" name="gelar_depan" value="{{ old('gelar_depan') }}" placeholder="Dr. / Drs. / Ir.">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="nama_lengkap" class="form-label fw-semibold small">Nama Lengkap (Tanpa Gelar) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nama_lengkap') is-invalid @enderror" id="nama_lengkap" name="nama_lengkap" value="{{ old('nama_lengkap') }}" required placeholder="Masukkan nama lengkap">
                            @error('nama_lengkap')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="gelar_belakang" class="form-label fw-semibold small">Gelar Belakang</label>
                            <input type="text" class="form-control @error('gelar_belakang') is-invalid @enderror" id="gelar_belakang" name="gelar_belakang" value="{{ old('gelar_belakang') }}" placeholder="M.Pd / M.Si / Ph.D">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="jabatan" class="form-label fw-semibold small">Jabatan Fungsional <span class="text-danger">*</span></label>
                            <select class="form-select @error('jabatan') is-invalid @enderror" id="jabatan" name="jabatan" required>
                                <option value="">-- Pilih Jabatan --</option>
                                @foreach($jabatanOptions as $jab)
                                    <option value="{{ $jab }}" {{ old('jabatan') == $jab ? 'selected' : '' }}>{{ $jab }}</option>
                                @endforeach
                            </select>
                            @error('jabatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="instansi" class="form-label fw-semibold small">Instansi Asal <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('instansi') is-invalid @enderror" id="instansi" name="instansi" value="{{ old('instansi', 'PPSDM Kemendagri') }}" required>
                            @error('instansi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="nomor_telepon" class="form-label fw-semibold small">Nomor Telepon / WhatsApp</label>
                            <input type="text" class="form-control @error('nomor_telepon') is-invalid @enderror" id="nomor_telepon" name="nomor_telepon" value="{{ old('nomor_telepon') }}" placeholder="08xxxxxxxxxx">
                        </div>

                        <div class="col-md-5 mb-3">
                            <label for="email" class="form-label fw-semibold small">Alamat Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="nama@instansi.go.id">
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="status" class="form-label fw-semibold small">Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="Aktif" {{ old('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="Nonaktif" {{ old('status') == 'Nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="keterangan" class="form-label fw-semibold small">Keterangan / Keahlian Khusus</label>
                        <textarea class="form-control @error('keterangan') is-invalid @enderror" id="keterangan" name="keterangan" rows="3" placeholder="Bidang kepakaran atau catatan khusus">{{ old('keterangan') }}</textarea>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('widyaiswara.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-navy">
                            <i class="bi bi-save me-1"></i> Simpan Widyaiswara
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
