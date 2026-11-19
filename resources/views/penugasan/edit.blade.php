@extends('layouts.app')

@section('title', 'Edit Penugasan')
@section('page_title', 'Edit Penugasan Pengajar')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('penugasan.index') }}">Penugasan</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit Penugasan</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card-custom">
            <div class="card-custom-header">
                <h5 class="card-custom-title">
                    <i class="bi bi-pencil-square text-primary"></i> Edit Data Penugasan Pengajar
                </h5>
            </div>
            <div class="p-4">
                <div class="alert alert-light border mb-4">
                    <div class="small text-muted">Mata Pelatihan:</div>
                    <strong class="text-dark fs-6">{{ $penugasan->mataPelatihan->nama_mata_pelatihan }}</strong>
                    <div class="small text-muted mt-1">
                        {{ $penugasan->mataPelatihan->angkatan->pelatihan->nama_pelatihan }} &bull; {{ $penugasan->mataPelatihan->angkatan->nomor_angkatan }}
                    </div>
                </div>

                <form action="{{ route('penugasan.update', $penugasan) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Jenis Pengajar <span class="text-danger">*</span></label>
                        <div class="d-flex gap-4">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="jenis_pengajar" id="jenis_wi" value="Widyaiswara" {{ old('jenis_pengajar', $penugasan->jenis_pengajar) == 'Widyaiswara' ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold text-primary" for="jenis_wi">
                                    <i class="bi bi-person-badge me-1"></i> Widyaiswara (Internal)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="jenis_pengajar" id="jenis_ns" value="Narasumber" {{ old('jenis_pengajar', $penugasan->jenis_pengajar) == 'Narasumber' ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold text-secondary" for="jenis_ns">
                                    <i class="bi bi-person-lines-fill me-1"></i> Narasumber (Eksternal / Pakar)
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3" id="wrapper_widyaiswara">
                        <label for="widyaiswara_id" class="form-label fw-semibold small">Pilih Widyaiswara <span class="text-danger">*</span></label>
                        <select class="form-select @error('widyaiswara_id') is-invalid @enderror" id="widyaiswara_id" name="widyaiswara_id">
                            <option value="">-- Pilih Widyaiswara --</option>
                            @foreach($widyaiswaras as $wi)
                                <option value="{{ $wi->id }}" {{ old('widyaiswara_id', $penugasan->widyaiswara_id) == $wi->id ? 'selected' : '' }}>
                                    {{ $wi->nama_gelar }} - {{ $wi->jabatan }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3 d-none" id="wrapper_narasumber">
                        <label for="narasumber_id" class="form-label fw-semibold small">Pilih Narasumber <span class="text-danger">*</span></label>
                        <select class="form-select @error('narasumber_id') is-invalid @enderror" id="narasumber_id" name="narasumber_id">
                            <option value="">-- Pilih Narasumber --</option>
                            @foreach($narasumbers as $ns)
                                <option value="{{ $ns->id }}" {{ old('narasumber_id', $penugasan->narasumber_id) == $ns->id ? 'selected' : '' }}>
                                    {{ $ns->nama_gelar }} ({{ $ns->instansi }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="peran" class="form-label fw-semibold small">Peran Penugasan <span class="text-danger">*</span></label>
                            <select class="form-select @error('peran') is-invalid @enderror" id="peran" name="peran" required>
                                <option value="Pengampu Utama" {{ old('peran', $penugasan->peran) == 'Pengampu Utama' ? 'selected' : '' }}>Pengampu Utama</option>
                                <option value="Pendamping Fasilitator" {{ old('peran', $penugasan->peran) == 'Pendamping Fasilitator' ? 'selected' : '' }}>Pendamping Fasilitator</option>
                                <option value="Narasumber Ahli" {{ old('peran', $penugasan->peran) == 'Narasumber Ahli' ? 'selected' : '' }}>Narasumber Ahli</option>
                                <option value="Co-Trainer" {{ old('peran', $penugasan->peran) == 'Co-Trainer' ? 'selected' : '' }}>Co-Trainer</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label for="keterangan" class="form-label fw-semibold small">Keterangan / Catatan</label>
                            <input type="text" class="form-control" id="keterangan" name="keterangan" value="{{ old('keterangan', $penugasan->keterangan) }}">
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('penugasan.index') }}" class="btn btn-outline-secondary">
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const jenisWi = document.getElementById('jenis_wi');
    const jenisNs = document.getElementById('jenis_ns');
    const wrapWi = document.getElementById('wrapper_widyaiswara');
    const wrapNs = document.getElementById('wrapper_narasumber');

    function updateJenis() {
        if (jenisWi.checked) {
            wrapWi.classList.remove('d-none');
            wrapNs.classList.add('d-none');
        } else {
            wrapWi.classList.add('d-none');
            wrapNs.classList.remove('d-none');
        }
    }

    jenisWi.addEventListener('change', updateJenis);
    jenisNs.addEventListener('change', updateJenis);
    updateJenis();
});
</script>
@endpush
