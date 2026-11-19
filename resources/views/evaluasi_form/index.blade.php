@extends('layouts.app')

@section('title', 'Form Evaluasi - KCLC')
@section('page_title', 'FORM EVALUASI')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">LMS Diklat</a></li>
    <li class="breadcrumb-item active" aria-current="page">Form Evaluasi</li>
@endsection

@section('page_actions')
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahEvaluasi">
        <i class="bi bi-plus-lg me-1"></i> Tambah Data
    </button>
@endsection

@section('content')
<div class="card card-custom">
    <div class="card-custom-header flex-column flex-md-row gap-3">
        <!-- Filter Pelatihan Sesuai Gambar 8 KCLC -->
        <form method="GET" action="{{ route('evaluasi-form.index') }}" class="d-flex flex-wrap align-items-center gap-2">
            <select name="pelatihan_id" class="form-select form-select-sm" style="min-width: 280px;">
                <option value="">Semua Pelatihan</option>
                @foreach($pelatihans as $p)
                    <option value="{{ $p->id }}" {{ $pelatihanId == $p->id ? 'selected' : '' }}>
                        {{ $p->nama_pelatihan }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-sm btn-primary px-3">
                Go
            </button>
            @if($pelatihanId)
                <a href="{{ route('evaluasi-form.index') }}" class="btn btn-sm btn-outline-secondary" title="Reset Filter">
                    <i class="bi bi-x-circle"></i>
                </a>
            @endif
        </form>

        <form method="GET" action="{{ route('evaluasi-form.index') }}" class="d-flex align-items-center gap-2">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Pencarian..." value="{{ $search }}" style="width: 200px;">
            <button type="submit" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-search"></i>
            </button>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-custom">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Judul Form</th>
                    <th>Pelatihan</th>
                    <th style="width: 140px;" class="text-center">Tools</th>
                </tr>
            </thead>
            <tbody>
                @forelse($formEvaluasis as $index => $item)
                    <tr>
                        <td class="text-center">{{ $formEvaluasis->firstItem() + $index }}</td>
                        <td>
                            <div class="fw-bold text-dark">{{ $item->judul_form }}</div>
                            @if($item->deskripsi)
                                <div class="text-muted small mt-1">{{ Str::limit($item->deskripsi, 90) }}</div>
                            @endif
                        </td>
                        <td>
                            <div class="fw-semibold text-primary">{{ $item->pelatihan->nama_pelatihan ?? '-' }}</div>
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalEditEvaluasi{{ $item->id }}" title="Edit Data">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <button type="button" class="btn btn-outline-danger" title="Hapus Data" onclick="confirmDelete('del-eval-{{ $item->id }}', 'Hapus form evaluasi ini?')">
                                    <i class="bi bi-trash"></i>
                                </button>
                                <button type="button" class="btn btn-outline-info" title="Salin Tautan Form" onclick="navigator.clipboard.writeText(window.location.origin + '/evaluasi/isi/{{ $item->id }}'); SirAlert.info('Tautan form evaluasi disalin ke clipboard !', 'Berhasil !');">
                                    <i class="bi bi-link-45deg"></i>
                                </button>
                            </div>

                            <form id="del-eval-{{ $item->id }}" action="{{ route('evaluasi-form.destroy', $item->id) }}" method="POST" class="d-none">
                                @csrf
                                @method('DELETE')
                            </form>
                        </td>
                    </tr>

                    <!-- Modal Edit Data Sesuai Screenshot 9 KCLC -->
                    <div class="modal fade" id="modalEditEvaluasi{{ $item->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title fw-bold">Edit Data</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form action="{{ route('evaluasi-form.update', $item->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-body p-4">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Judul <span class="text-danger">*</span></label>
                                            <input type="text" name="judul_form" class="form-control" value="{{ $item->judul_form }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Deskripsi</label>
                                            <textarea name="deskripsi" class="form-control" rows="3" placeholder="Deskripsi instrumen evaluasi">{{ $item->deskripsi }}</textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Pelatihan <span class="text-danger">*</span></label>
                                            <select name="pelatihan_id" class="form-select" required>
                                                @foreach($pelatihans as $p)
                                                    <option value="{{ $p->id }}" {{ $item->pelatihan_id == $p->id ? 'selected' : '' }}>
                                                        {{ $p->nama_pelatihan }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="text-danger small mt-1">* Harus diisi</div>
                                        </div>
                                        <div class="form-check form-switch mt-3">
                                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="swActiveEval{{ $item->id }}" {{ $item->is_active ? 'checked' : '' }}>
                                            <label class="form-check-label" for="swActiveEval{{ $item->id }}">Form Aktif</label>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary px-4">Update</button>
                                        <button type="button" class="btn btn-primary px-4" style="background-color: #2563EB;" data-bs-dismiss="modal">Cancel</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-3 d-block mb-2"></i> Belum ada data Form Evaluasi.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($formEvaluasis->hasPages())
        <div class="table-pagination-footer">
            <span class="text-muted small">
                Menampilkan {{ $formEvaluasis->firstItem() }} s/d {{ $formEvaluasis->lastItem() }} dari {{ $formEvaluasis->total() }} data
            </span>
            <div>
                {{ $formEvaluasis->links() }}
            </div>
        </div>
    @endif
</div>

<!-- Modal Tambah Data Baru -->
<div class="modal fade" id="modalTambahEvaluasi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Tambah Form Evaluasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('evaluasi-form.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Judul <span class="text-danger">*</span></label>
                        <input type="text" name="judul_form" class="form-control" placeholder="Contoh: Penilaian Widyaiswara/pengajar oleh Peserta" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="3" placeholder="Tuliskan deskripsi instrumen kuesioner evaluasi..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Pelatihan <span class="text-danger">*</span></label>
                        <select name="pelatihan_id" class="form-select" required>
                            <option value="">-- Pilih Program Pelatihan --</option>
                            @foreach($pelatihans as $p)
                                <option value="{{ $p->id }}">{{ $p->nama_pelatihan }}</option>
                            @endforeach
                        </select>
                        <div class="text-danger small mt-1">* Harus diisi</div>
                    </div>
                    <div class="form-check form-switch mt-3">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="swActiveEvalNew" checked>
                        <label class="form-check-label" for="swActiveEvalNew">Form Aktif</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
