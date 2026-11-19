@extends('layouts.app')

@section('title', 'Tambah Kelas Mapel - KCLC')
@section('page_title', 'TAMBAH KELAS MAPEL')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">LMS Diklat</a></li>
    <li class="breadcrumb-item"><a href="{{ route('kelas-mapel.index') }}">Kelas Mapel</a></li>
    <li class="breadcrumb-item active" aria-current="page">Tambah</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card card-custom">
            <div class="card-custom-header">
                <h5 class="card-custom-title">
                    <i class="bi bi-plus-circle text-primary"></i> Formulir Tambah Kelas Mapel (KCLC)
                </h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('kelas-mapel.store') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Pelatihan <span class="text-danger">*</span></label>
                            <select name="pelatihan_id" class="form-select" required>
                                <option value="">-- Pilih Program Pelatihan --</option>
                                @foreach($pelatihans as $p)
                                    <option value="{{ $p->id }}" {{ old('pelatihan_id') == $p->id ? 'selected' : '' }}>{{ $p->nama_pelatihan }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Angkatan <span class="text-danger">*</span></label>
                            <select name="angkatan_id" class="form-select" required>
                                <option value="">-- Pilih Angkatan --</option>
                                @foreach($angkatans as $a)
                                    <option value="{{ $a->id }}" {{ old('angkatan_id') == $a->id ? 'selected' : '' }}>{{ $a->nomor_angkatan }} ({{ $a->pelatihan->nama_pelatihan ?? '' }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Mata Pelatihan</label>
                            <select name="mata_pelatihan_id" class="form-select">
                                <option value="">-- Pilih Mata Pelatihan --</option>
                                @foreach($mataPelatihans as $mp)
                                    <option value="{{ $mp->id }}" {{ old('mata_pelatihan_id') == $mp->id ? 'selected' : '' }}>{{ $mp->nama_mata_pelatihan }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nama Kelas <span class="text-danger">*</span></label>
                            <input type="text" name="nama_kelas" class="form-control" placeholder="Contoh: PK PPPK / Kelas A" value="{{ old('nama_kelas') }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Kapasitas Peserta <span class="text-danger">*</span></label>
                            <input type="number" name="kapasitas" class="form-control" value="{{ old('kapasitas', 350) }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Kurikulum <span class="text-danger">*</span></label>
                            <input type="text" name="kurikulum" class="form-control" value="{{ old('kurikulum', '2026') }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Jam Pelajaran (JP) <span class="text-danger">*</span></label>
                            <input type="number" step="any" name="jp" class="form-control" value="{{ old('jp', 4) }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai" class="form-control" value="{{ old('tanggal_mulai', date('Y-m-d')) }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tanggal Selesai</label>
                            <input type="date" name="tanggal_selesai" class="form-control" value="{{ old('tanggal_selesai', date('Y-m-d')) }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Ada Kelompok? <span class="text-danger">*</span></label>
                            <select name="ada_kelompok" class="form-select" required>
                                <option value="tidak" {{ old('ada_kelompok') == 'tidak' ? 'selected' : '' }}>Tidak</option>
                                <option value="ya" {{ old('ada_kelompok') == 'ya' ? 'selected' : '' }}>Ya</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Ruangan Pembelajaran</label>
                            <select name="ruangan_id" class="form-select">
                                <option value="">-- Pilih Ruangan --</option>
                                @foreach($ruangans as $r)
                                    <option value="{{ $r->id }}" {{ old('ruangan_id') == $r->id ? 'selected' : '' }}>{{ $r->nama_ruangan }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Metode Pembelajaran <span class="text-danger">*</span></label>
                            <select name="metode_pembelajaran" class="form-select" required>
                                <option value="Synchronous" {{ old('metode_pembelajaran') == 'Synchronous' ? 'selected' : '' }}>Synchronous (Virtual / Online)</option>
                                <option value="Asynchronous" {{ old('metode_pembelajaran') == 'Asynchronous' ? 'selected' : '' }}>Asynchronous (Mandiri)</option>
                                <option value="Klasikal" {{ old('metode_pembelajaran') == 'Klasikal' ? 'selected' : '' }}>Klasikal (Tatap Muka Langsung)</option>
                            </select>
                        </div>

                        <div class="col-12 mt-4">
                            <h6 class="fw-bold border-bottom pb-2 text-primary">Jadwal Mingguan Default</h6>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Hari</label>
                            <select name="hari" class="form-select">
                                <option value="Senin">Senin</option>
                                <option value="Selasa">Selasa</option>
                                <option value="Rabu" selected>Rabu</option>
                                <option value="Kamis">Kamis</option>
                                <option value="Jumat">Jumat</option>
                                <option value="Sabtu">Sabtu</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Jam Mulai</label>
                            <input type="time" name="jam_mulai" class="form-control" value="{{ old('jam_mulai', '08:00') }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Jam Selesai</label>
                            <input type="time" name="jam_selesai" class="form-control" value="{{ old('jam_selesai', '12:00') }}">
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                        <a href="{{ route('kelas-mapel.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
                        </a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check-lg me-1"></i> Simpan Kelas & Lanjut
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
