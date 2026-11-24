@extends('layouts.app')

@section('title', 'Dokumentasi Foto Kegiatan Pembelajaran')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1">Dokumentasi Foto Kegiatan</h4>
        <div class="text-muted small">Galeri foto kegiatan pembelajaran, pengajar, praktik, dan diskusi per sesi diklat</div>
    </div>
    <button type="button" class="btn btn-primary d-flex align-items-center gap-2 rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalUploadDoc">
        <i class="bi bi-cloud-arrow-up-fill"></i>
        <span>Upload Foto Baru</span>
    </button>
</div>

<!-- Filter Bar -->
<div class="card border-0 shadow-sm rounded-3 p-3 mb-4 bg-white">
    <form method="GET" action="{{ route('class-documentations.index') }}" class="row g-2 align-items-end">
        <div class="col-md-4">
            <label class="form-label small fw-semibold text-muted mb-1">KELAS DIKLAT</label>
            <select name="kelas_mapel_id" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Semua Kelas</option>
                @foreach($kelasMapels as $km)
                    <option value="{{ $km->id }}" {{ $kelasMapelId == $km->id ? 'selected' : '' }}>
                        {{ $km->nama_kelas }} ({{ $km->pelatihan->nama_pelatihan ?? '-' }})
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-semibold text-muted mb-1">SESI PEMBELAJARAN</label>
            <select name="attendance_session_id" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Semua Sesi</option>
                @foreach($sessions as $sess)
                    <option value="{{ $sess->id }}" {{ $sessionId == $sess->id ? 'selected' : '' }}>
                        {{ $sess->tanggal->format('d/m/Y') }} &bull; {{ $sess->judul_sesi }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-semibold text-muted mb-1">KATEGORI FOTO</label>
            <select name="kategori" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Semua Kategori</option>
                <option value="Kegiatan" {{ $kategori === 'Kegiatan' ? 'selected' : '' }}>Foto Kegiatan Pembelajaran</option>
                <option value="Pengajar" {{ $kategori === 'Pengajar' ? 'selected' : '' }}>Foto Pengajar / Widyaiswara</option>
                <option value="Peserta" {{ $kategori === 'Peserta' ? 'selected' : '' }}>Foto Peserta Diklat</option>
                <option value="Praktik" {{ $kategori === 'Praktik' ? 'selected' : '' }}>Foto Praktik / Simulasi</option>
                <option value="Diskusi" {{ $kategori === 'Diskusi' ? 'selected' : '' }}>Foto Diskusi Kelompok</option>
            </select>
        </div>
        <div class="col-md-2 d-flex gap-1">
            <button type="submit" class="btn btn-sm btn-primary flex-grow-1">Filter</button>
            <a href="{{ route('class-documentations.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
        </div>
    </form>
</div>

<!-- Photo Gallery Grid -->
<div class="row g-3">
    @forelse($documentations as $doc)
        @php
            $catBadge = match($doc->kategori) {
                'Pengajar' => 'bg-primary text-white',
                'Peserta' => 'bg-success text-white',
                'Praktik' => 'bg-warning text-dark',
                'Diskusi' => 'bg-info text-white',
                default => 'bg-secondary text-white',
            };
        @endphp
        <div class="col-sm-6 col-md-4 col-lg-3">
            <div class="card h-100 border shadow-sm rounded-3 overflow-hidden bg-white">
                <div class="position-relative bg-dark" style="height: 180px; overflow: hidden;">
                    <img src="{{ route('class-documentations.view', $doc->id) }}" 
                         alt="{{ $doc->nama_file_asli }}" 
                         class="w-100 h-100 object-fit-cover"
                         loading="lazy">
                    <span class="badge {{ $catBadge }} position-absolute top-0 start-0 m-2 rounded-pill px-2 py-1 small">
                        {{ $doc->kategori }}
                    </span>
                </div>
                <div class="card-body p-3 d-flex flex-column justify-content-between">
                    <div>
                        <div class="fw-bold text-dark small text-truncate" title="{{ $doc->kelasMapel->nama_kelas ?? '-' }}">
                            {{ $doc->kelasMapel->nama_kelas ?? 'Kelas Diklat' }}
                        </div>
                        <div class="text-muted small mb-2 text-truncate">
                            {{ $doc->session->judul_sesi ?? 'Dokumentasi Kelas' }}
                        </div>
                        @if($doc->keterangan)
                            <p class="text-secondary small mb-2 line-clamp-2" style="font-size: 0.78rem;">
                                {{ $doc->keterangan }}
                            </p>
                        @endif
                    </div>
                    <div class="d-flex justify-content-between align-items-center pt-2 border-top text-muted" style="font-size: 0.72rem;">
                        <span><i class="bi bi-clock"></i> {{ $doc->created_at->format('d/m/Y') }}</span>
                        <div class="d-flex gap-1">
                            <a href="{{ route('class-documentations.view', $doc->id) }}" target="_blank" class="btn btn-xs btn-outline-primary py-0 px-2" title="Lihat Foto Penuh">
                                <i class="bi bi-arrows-fullscreen"></i>
                            </a>
                            <button type="button" 
                                    class="btn btn-xs btn-outline-danger py-0 px-2" 
                                    title="Hapus"
                                    onclick="confirmDelete('del-doc-{{ $doc->id }}', 'Hapus foto dokumentasi ini?')">
                                <i class="bi bi-trash"></i>
                            </button>
                            <form id="del-doc-{{ $doc->id }}" action="{{ route('class-documentations.destroy', $doc->id) }}" method="POST" class="d-none">
                                @csrf
                                @method('DELETE')
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="card bg-white p-5 text-center border rounded-3 text-muted">
                <i class="bi bi-images fs-1 text-secondary mb-2"></i>
                <h5 class="fw-bold mb-1">Belum Ada Foto Dokumentasi</h5>
                <p class="small text-muted mb-3">Unggah foto kegiatan, praktik, pengajar, atau diskusi kelompok untuk melengkapi laporan diklat.</p>
                <div>
                    <button type="button" class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalUploadDoc">
                        <i class="bi bi-cloud-arrow-up-fill me-1"></i> Upload Foto Sekarang
                    </button>
                </div>
            </div>
        </div>
    @endforelse
</div>

<div class="mt-4">
    {{ $documentations->links('vendor.pagination.bootstrap-5') }}
</div>

<!-- Modal Upload Foto -->
<div class="modal fade" id="modalUploadDoc" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('class-documentations.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h6 class="modal-title fw-bold">Unggah Foto Dokumentasi Pembelajaran</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Pilih Kelas -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-secondary">KELAS MAPEL <span class="text-danger">*</span></label>
                        <select name="kelas_mapel_id" class="form-select" required>
                            <option value="">-- Pilih Kelas Terkait --</option>
                            @foreach($kelasMapels as $km)
                                <option value="{{ $km->id }}" {{ $kelasMapelId == $km->id ? 'selected' : '' }}>
                                    {{ $km->nama_kelas }} ({{ $km->pelatihan->nama_pelatihan ?? '-' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Pilih Sesi (Opsional) -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-secondary">SESI PEMBELAJARAN (OPSIONAL)</label>
                        <select name="attendance_session_id" class="form-select">
                            <option value="">-- Terhubung ke kelas secara umum --</option>
                            @foreach($sessions as $sess)
                                <option value="{{ $sess->id }}" {{ $sessionId == $sess->id ? 'selected' : '' }}>
                                    {{ $sess->tanggal->format('d/m/Y') }} &bull; {{ $sess->judul_sesi }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Kategori -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-secondary">KATEGORI FOTO <span class="text-danger">*</span></label>
                        <select name="kategori" class="form-select" required>
                            <option value="Kegiatan">Foto Kegiatan Pembelajaran</option>
                            <option value="Pengajar">Foto Pengajar / Widyaiswara</option>
                            <option value="Peserta">Foto Peserta Diklat</option>
                            <option value="Praktik">Foto Praktik / Simulasi</option>
                            <option value="Diskusi">Foto Diskusi Kelompok</option>
                        </select>
                    </div>

                    <!-- Pilih File Foto -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-secondary">BERKAS FOTO (JPEG, PNG, JPG) <span class="text-danger">*</span></label>
                        <input type="file" name="foto" class="form-control" accept="image/jpeg,image/png,image/jpg" required>
                        <small class="text-muted" style="font-size: 0.72rem;">Maksimal ukuran file: 5 MB.</small>
                    </div>

                    <!-- Keterangan / Catatan -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-secondary">KETERANGAN / CAPTION</label>
                        <textarea name="keterangan" rows="2" class="form-control" placeholder="Contoh: Peserta aktif mempraktikkan simulasi tata naskah dinas"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary px-3 fw-bold">Unggah Foto</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
