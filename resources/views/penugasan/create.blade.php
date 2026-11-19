@extends('layouts.app')

@section('title', 'Tugaskan Pengajar')
@section('page_title', 'Penugasan Pengajar')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('penugasan.index') }}">Penugasan</a></li>
    <li class="breadcrumb-item active" aria-current="page">Tugaskan Pengajar</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card-custom">
            <div class="card-custom-header">
                <h5 class="card-custom-title">
                    <i class="bi bi-person-check-fill text-primary"></i> Formulir Penugasan Pengajar
                </h5>
            </div>
            <div class="p-4">
                <form action="{{ route('penugasan.store') }}" method="POST">
                    @csrf

                    <!-- 1. Pilih Pelatihan -->
                    <div class="mb-3">
                        <label for="select_pelatihan" class="form-label fw-semibold small">1. Pilih Program Pelatihan <span class="text-danger">*</span></label>
                        <select class="form-select" id="select_pelatihan" required>
                            <option value="">-- Pilih Program Pelatihan --</option>
                            @foreach($pelatihans as $p)
                                <option value="{{ $p->id }}" {{ ($selectedMataPelatihan && $selectedMataPelatihan->angkatan->pelatihan_id == $p->id) ? 'selected' : '' }}>
                                    {{ $p->nama_pelatihan }} ({{ $p->kode_pelatihan }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- 2. Pilih Angkatan -->
                    <div class="mb-3">
                        <label for="select_angkatan" class="form-label fw-semibold small">2. Pilih Angkatan <span class="text-danger">*</span></label>
                        <select class="form-select" id="select_angkatan" required>
                            <option value="">-- Pilih Angkatan Pelatihan --</option>
                            @if($selectedMataPelatihan)
                                @foreach($selectedMataPelatihan->angkatan->pelatihan->angkatans as $ang)
                                    <option value="{{ $ang->id }}" {{ $selectedMataPelatihan->angkatan_id == $ang->id ? 'selected' : '' }}>
                                        {{ $ang->nomor_angkatan }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <!-- 3. Pilih Mata Pelatihan -->
                    <div class="mb-4">
                        <label for="mata_pelatihan_id" class="form-label fw-semibold small">3. Pilih Mata Pelatihan <span class="text-danger">*</span></label>
                        <select class="form-select @error('mata_pelatihan_id') is-invalid @enderror" id="mata_pelatihan_id" name="mata_pelatihan_id" required>
                            <option value="">-- Pilih Mata Pelatihan --</option>
                            @if($selectedMataPelatihan)
                                @foreach($selectedMataPelatihan->angkatan->mataPelatihans as $mp)
                                    <option value="{{ $mp->id }}" {{ $selectedMataPelatihan->id == $mp->id ? 'selected' : '' }}>
                                        {{ $mp->nama_mata_pelatihan }} (Rencana: {{ $mp->rencana_jp }} JP)
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        @error('mata_pelatihan_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr class="my-4">

                    <!-- 4. Pilih Jenis Pengajar -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">4. Pilih Jenis Pengajar <span class="text-danger">*</span></label>
                        <div class="d-flex gap-4">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="jenis_pengajar" id="jenis_wi" value="Widyaiswara" checked>
                                <label class="form-check-label fw-semibold text-primary" for="jenis_wi">
                                    <i class="bi bi-person-badge me-1"></i> Widyaiswara (Internal)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="jenis_pengajar" id="jenis_ns" value="Narasumber">
                                <label class="form-check-label fw-semibold text-secondary" for="jenis_ns">
                                    <i class="bi bi-person-lines-fill me-1"></i> Narasumber (Eksternal / Pakar)
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- 5. Pilih Pengajar (WI / NS) -->
                    <!-- Bagian Widyaiswara -->
                    <div class="mb-3" id="wrapper_widyaiswara">
                        <label for="widyaiswara_id" class="form-label fw-semibold small">5. Pilih Widyaiswara <span class="text-danger">*</span></label>
                        <select class="form-select @error('widyaiswara_id') is-invalid @enderror" id="widyaiswara_id" name="widyaiswara_id">
                            <option value="">-- Pilih Widyaiswara --</option>
                            @foreach($widyaiswaras as $wi)
                                <option value="{{ $wi->id }}" {{ old('widyaiswara_id') == $wi->id ? 'selected' : '' }}>
                                    {{ $wi->nama_gelar }} - {{ $wi->jabatan }}
                                </option>
                            @endforeach
                        </select>
                        @error('widyaiswara_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Bagian Narasumber -->
                    <div class="mb-3 d-none" id="wrapper_narasumber">
                        <label for="narasumber_id" class="form-label fw-semibold small">5. Pilih Narasumber <span class="text-danger">*</span></label>
                        <select class="form-select @error('narasumber_id') is-invalid @enderror" id="narasumber_id" name="narasumber_id">
                            <option value="">-- Pilih Narasumber --</option>
                            @foreach($narasumbers as $ns)
                                <option value="{{ $ns->id }}" {{ old('narasumber_id') == $ns->id ? 'selected' : '' }}>
                                    {{ $ns->nama_gelar }} ({{ $ns->instansi }})
                                </option>
                            @endforeach
                        </select>
                        @error('narasumber_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="peran" class="form-label fw-semibold small">Peran Penugasan <span class="text-danger">*</span></label>
                            <select class="form-select @error('peran') is-invalid @enderror" id="peran" name="peran" required>
                                <option value="Pengampu Utama" {{ old('peran') == 'Pengampu Utama' ? 'selected' : '' }}>Pengampu Utama</option>
                                <option value="Pendamping Fasilitator" {{ old('peran') == 'Pendamping Fasilitator' ? 'selected' : '' }}>Pendamping Fasilitator</option>
                                <option value="Narasumber Ahli" {{ old('peran') == 'Narasumber Ahli' ? 'selected' : '' }}>Narasumber Ahli</option>
                                <option value="Co-Trainer" {{ old('peran') == 'Co-Trainer' ? 'selected' : '' }}>Co-Trainer</option>
                            </select>
                            @error('peran')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-4">
                            <label for="keterangan" class="form-label fw-semibold small">Keterangan / Catatan Penugasan</label>
                            <input type="text" class="form-control" id="keterangan" name="keterangan" value="{{ old('keterangan') }}" placeholder="Contoh: Mengampu materi teori dan studi kasus">
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('penugasan.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-navy">
                            <i class="bi bi-check-circle me-1"></i> Simpan Penugasan
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
    const selectPelatihan = document.getElementById('select_pelatihan');
    const selectAngkatan = document.getElementById('select_angkatan');
    const selectMataPelatihan = document.getElementById('mata_pelatihan_id');

    const jenisWi = document.getElementById('jenis_wi');
    const jenisNs = document.getElementById('jenis_ns');
    const wrapWi = document.getElementById('wrapper_widyaiswara');
    const wrapNs = document.getElementById('wrapper_narasumber');

    // Toggle jenis pengajar
    function updateJenisPengajar() {
        if (jenisWi.checked) {
            wrapWi.classList.remove('d-none');
            wrapNs.classList.add('d-none');
        } else {
            wrapWi.classList.add('d-none');
            wrapNs.classList.remove('d-none');
        }
    }

    jenisWi.addEventListener('change', updateJenisPengajar);
    jenisNs.addEventListener('change', updateJenisPengajar);
    updateJenisPengajar();

    // Cascading Pelatihan -> Angkatan
    selectPelatihan.addEventListener('change', function () {
        const pelId = this.value;
        selectAngkatan.innerHTML = '<option value="">-- Sedang memuat angkatan... --</option>';
        selectMataPelatihan.innerHTML = '<option value="">-- Pilih Mata Pelatihan --</option>';

        if (!pelId) {
            selectAngkatan.innerHTML = '<option value="">-- Pilih Angkatan Pelatihan --</option>';
            return;
        }

        fetch(`{{ url('api/angkatans-by-pelatihan') }}/${pelId}`)
            .then(res => res.json())
            .then(data => {
                let html = '<option value="">-- Pilih Angkatan Pelatihan --</option>';
                data.forEach(ang => {
                    html += `<option value="${ang.id}">${ang.nomor_angkatan}</option>`;
                });
                selectAngkatan.innerHTML = html;
            })
            .catch(err => {
                selectAngkatan.innerHTML = '<option value="">-- Gagal memuat angkatan --</option>';
            });
    });

    // Cascading Angkatan -> Mata Pelatihan
    selectAngkatan.addEventListener('change', function () {
        const angId = this.value;
        selectMataPelatihan.innerHTML = '<option value="">-- Sedang memuat mata pelatihan... --</option>';

        if (!angId) {
            selectMataPelatihan.innerHTML = '<option value="">-- Pilih Mata Pelatihan --</option>';
            return;
        }

        fetch(`{{ url('api/mata-pelatihans-by-angkatan') }}/${angId}`)
            .then(res => res.json())
            .then(data => {
                let html = '<option value="">-- Pilih Mata Pelatihan --</option>';
                data.forEach(mp => {
                    html += `<option value="${mp.id}">${mp.nama_mata_pelatihan} (Rencana: ${mp.rencana_jp} JP)</option>`;
                });
                selectMataPelatihan.innerHTML = html;
            })
            .catch(err => {
                selectMataPelatihan.innerHTML = '<option value="">-- Gagal memuat mata pelatihan --</option>';
            });
    });
});
</script>
@endpush
