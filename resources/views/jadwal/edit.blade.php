@extends('layouts.app')

@section('title', 'Edit Jadwal')
@section('page_title', 'Edit Jadwal Pembelajaran')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('jadwal.index') }}">Jadwal</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit Jadwal</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card-custom">
            <div class="card-custom-header">
                <h5 class="card-custom-title">
                    <i class="bi bi-pencil-square text-primary"></i> Edit Jadwal Pembelajaran
                </h5>
            </div>
            <div class="p-4">
                <form action="{{ route('jadwal.update', $jadwal) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="mata_pelatihan_id" class="form-label fw-semibold small">Mata Pelatihan & Angkatan <span class="text-danger">*</span></label>
                        <select class="form-select @error('mata_pelatihan_id') is-invalid @enderror" id="mata_pelatihan_id" name="mata_pelatihan_id" required>
                            @foreach($mataPelatihans as $mp)
                                <option value="{{ $mp->id }}" {{ old('mata_pelatihan_id', $jadwal->mata_pelatihan_id) == $mp->id ? 'selected' : '' }}>
                                    {{ $mp->angkatan->pelatihan->nama_pelatihan }} ({{ $mp->angkatan->nomor_angkatan }}) &rarr; {{ $mp->nama_mata_pelatihan }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="penugasan_pengajar_id" class="form-label fw-semibold small">Pengajar Bertugas</label>
                        <select class="form-select @error('penugasan_pengajar_id') is-invalid @enderror" id="penugasan_pengajar_id" name="penugasan_pengajar_id">
                            <option value="">-- Belum Ditugaskan / Pilih Nanti --</option>
                            @foreach($penugasans as $p)
                                <option value="{{ $p->id }}" {{ old('penugasan_pengajar_id', $jadwal->penugasan_pengajar_id) == $p->id ? 'selected' : '' }}>
                                    {{ $p->pengajar_nama }} ({{ $p->jenis_pengajar }} - {{ $p->peran }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="tanggal" class="form-label fw-semibold small">Tanggal Pembelajaran <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="tanggal" name="tanggal" value="{{ old('tanggal', $jadwal->tanggal->format('Y-m-d')) }}" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="jam_mulai" class="form-label fw-semibold small">Jam Mulai <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" id="jam_mulai" name="jam_mulai" value="{{ old('jam_mulai', substr($jadwal->jam_mulai, 0, 5)) }}" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="jam_selesai" class="form-label fw-semibold small">Jam Selesai <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" id="jam_selesai" name="jam_selesai" value="{{ old('jam_selesai', substr($jadwal->jam_selesai, 0, 5)) }}" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="jp_rencana" class="form-label fw-semibold small">JP Rencana <span class="text-danger">*</span></label>
                            <input type="number" step="0.5" class="form-control" id="jp_rencana" name="jp_rencana" value="{{ old('jp_rencana', $jadwal->jp_rencana) }}" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="ruangan" class="form-label fw-semibold small">Ruangan</label>
                            <input type="text" class="form-control" id="ruangan" name="ruangan" value="{{ old('ruangan', $jadwal->ruangan) }}">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="metode" class="form-label fw-semibold small">Metode <span class="text-danger">*</span></label>
                            <select class="form-select" id="metode" name="metode" required>
                                <option value="Tatap Muka / Klasikal" {{ old('metode', $jadwal->metode) == 'Tatap Muka / Klasikal' ? 'selected' : '' }}>Tatap Muka / Klasikal</option>
                                <option value="Daring / Online" {{ old('metode', $jadwal->metode) == 'Daring / Online' ? 'selected' : '' }}>Daring / Online</option>
                                <option value="Blended" {{ old('metode', $jadwal->metode) == 'Blended' ? 'selected' : '' }}>Blended</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label fw-semibold small">Status Jadwal <span class="text-danger">*</span></label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="Terjadwal" {{ old('status', $jadwal->status) == 'Terjadwal' ? 'selected' : '' }}>Terjadwal</option>
                                <option value="Terlaksana" {{ old('status', $jadwal->status) == 'Terlaksana' ? 'selected' : '' }}>Terlaksana</option>
                                <option value="Dibatalkan" {{ old('status', $jadwal->status) == 'Dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label for="keterangan" class="form-label fw-semibold small">Keterangan / Pokok Bahasan</label>
                            <textarea class="form-control" id="keterangan" name="keterangan" rows="2">{{ old('keterangan', $jadwal->keterangan) }}</textarea>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('jadwal.index') }}" class="btn btn-outline-secondary">
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
