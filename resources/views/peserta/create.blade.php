@extends('layouts.app')

@section('title', 'Tambah Peserta Diklat - KCLC')
@section('page_title', 'TAMBAH PESERTA PELATIHAN')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">LMS Diklat</a></li>
    <li class="breadcrumb-item"><a href="{{ route('peserta.index') }}">Peserta</a></li>
    <li class="breadcrumb-item active" aria-current="page">Tambah</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card card-custom">
            <div class="card-custom-header">
                <h5 class="card-custom-title">
                    <i class="bi bi-person-plus text-primary"></i> Formulir Tambah Peserta Diklat
                </h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('peserta.store') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">NIP <span class="text-danger">*</span></label>
                            <input type="text" name="nip" class="form-control" placeholder="Nomor Induk Pegawai" value="{{ old('nip') }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control" placeholder="Nama Peserta" value="{{ old('nama') }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" class="form-control" placeholder="email@instansi.go.id" value="{{ old('email') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Jenis Kelamin <span class="text-danger">*</span></label>
                            <select name="jenis_kelamin" class="form-select" required>
                                <option value="Laki-laki" {{ old('jenis_kelamin') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="Perempuan" {{ old('jenis_kelamin') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Instansi Asal</label>
                            <input type="text" name="instansi" class="form-control" placeholder="Contoh: Balai Pelatihan Vokasi Padang" value="{{ old('instansi') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Jabatan</label>
                            <input type="text" name="jabatan" class="form-control" placeholder="Contoh: Pengantar Kerja Ahli Pertama" value="{{ old('jabatan') }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Golongan</label>
                            <input type="text" name="golongan" class="form-control" placeholder="Contoh: IX / III/a" value="{{ old('golongan') }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Angkatan Diklat</label>
                            <select name="angkatan_id" class="form-select">
                                <option value="">-- Pilih Angkatan --</option>
                                @foreach($angkatans as $a)
                                    <option value="{{ $a->id }}" {{ old('angkatan_id') == $a->id ? 'selected' : '' }}>
                                        {{ $a->nomor_angkatan }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Kelas Mapel</label>
                            <select name="kelas_mapel_id" class="form-select">
                                <option value="">-- Pilih Kelas Mapel --</option>
                                @foreach($kelasMapels as $km)
                                    <option value="{{ $km->id }}" {{ old('kelas_mapel_id') == $km->id ? 'selected' : '' }}>
                                        {{ $km->nama_kelas }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                        <a href="{{ route('peserta.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-1"></i> Simpan Data Peserta
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
