@extends('layouts.app')

@section('title', 'Form Aspek Penilaian Sikap - KCLC')
@section('page_title', 'FORM ASPEK PENILAIAN SIKAP')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">LMS Diklat</a></li>
    <li class="breadcrumb-item active" aria-current="page">Form Aspek Penilaian Sikap</li>
@endsection

@section('page_actions')
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahSikap">
        <i class="bi bi-plus-lg me-1"></i> Tambah Data
    </button>
@endsection

@section('content')
<div class="card card-custom">
    <div class="card-custom-header flex-column flex-md-row gap-3">
        <!-- Filter Pelatihan Sesuai Gambar KCLC -->
        <form method="GET" action="{{ route('sikap-form.index') }}" class="d-flex flex-wrap align-items-center gap-2">
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
                <a href="{{ route('sikap-form.index') }}" class="btn btn-sm btn-outline-secondary" title="Reset Filter">
                    <i class="bi bi-x-circle"></i>
                </a>
            @endif
        </form>

        <form method="GET" action="{{ route('sikap-form.index') }}" class="d-flex align-items-center gap-2">
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
                    <th>Judul</th>
                    <th>Pelatihan</th>
                    <th style="width: 120px;" class="text-center">Status</th>
                    <th style="width: 120px;" class="text-center">Tools</th>
                </tr>
            </thead>
            <tbody>
                @forelse($aspekSikaps as $index => $item)
                    <tr>
                        <td class="text-center">{{ $aspekSikaps->firstItem() + $index }}</td>
                        <td>
                            <div class="fw-bold text-dark">{{ $item->judul }}</div>
                            @if($item->kategori)
                                <span class="badge bg-light text-secondary border small">{{ $item->kategori }}</span>
                            @endif
                            @if(!empty($item->aspek_penilaian))
                                <div class="mt-1 small text-muted">
                                    <i class="bi bi-check2-square text-success"></i>
                                    {{ implode(', ', array_slice($item->aspek_penilaian, 0, 4)) }}
                                    @if(count($item->aspek_penilaian) > 4) ... @endif
                                </div>
                            @endif
                        </td>
                        <td>
                            <div class="fw-semibold text-primary">{{ $item->pelatihan->nama_pelatihan ?? '-' }}</div>
                        </td>
                        <td class="text-center">
                            <form action="{{ route('sikap-form.toggle', $item->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-sm p-0 border-0" title="Klik untuk ubah status">
                                    @if($item->is_active)
                                        <span class="badge rounded-pill bg-dark px-3 py-2">
                                            <i class="bi bi-toggle-on text-success me-1 fs-6"></i> Active
                                        </span>
                                    @else
                                        <span class="badge rounded-pill bg-light text-secondary border px-3 py-2">
                                            <i class="bi bi-toggle-off me-1 fs-6"></i> Inactive
                                        </span>
                                    @endif
                                </button>
                            </form>
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalEditSikap{{ $item->id }}" title="Edit Data">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <button type="button" class="btn btn-outline-danger" title="Hapus Data" onclick="confirmDelete('del-sikap-{{ $item->id }}', 'Hapus form penilaian sikap ini?')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>

                            <form id="del-sikap-{{ $item->id }}" action="{{ route('sikap-form.destroy', $item->id) }}" method="POST" class="d-none">
                                @csrf
                                @method('DELETE')
                            </form>
                        </td>
                    </tr>

                    <!-- Modal Edit Data Sesuai Screenshot 7 KCLC -->
                    <div class="modal fade" id="modalEditSikap{{ $item->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title fw-bold">Edit Data</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form action="{{ route('sikap-form.update', $item->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-body p-4">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Kategori</label>
                                            <input type="text" name="kategori" class="form-control" value="{{ $item->kategori }}" placeholder="Contoh: Nilai Sikap dari Coach">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Judul <span class="text-danger">*</span></label>
                                            <input type="text" name="judul" class="form-control" value="{{ $item->judul }}" required>
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
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Aspek Penilaian</label>
                                            <div id="aspekContainer{{ $item->id }}">
                                                @php
                                                    $aspekList = !empty($item->aspek_penilaian) ? $item->aspek_penilaian : ['Kedisiplinan', 'Kepemimpinan', 'Kerjasama', 'Prakarsa'];
                                                @endphp
                                                @foreach($aspekList as $aIdx => $aspek)
                                                    <div class="input-group mb-2 aspek-row">
                                                        <input type="text" name="aspek_penilaian[]" class="form-control" value="{{ $aspek }}" placeholder="Butir Aspek Penilaian">
                                                        @if($aIdx === 0)
                                                            <button type="button" class="btn btn-outline-success" onclick="addAspekRow('aspekContainer{{ $item->id }}')">
                                                                <i class="bi bi-plus-lg"></i>
                                                            </button>
                                                        @else
                                                            <button type="button" class="btn btn-outline-danger" onclick="this.closest('.aspek-row').remove()">
                                                                <i class="bi bi-dash-lg"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                            <div class="text-danger small mt-1">* Harus diisi</div>
                                        </div>
                                        <div class="form-check form-switch mt-3">
                                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="switchActive{{ $item->id }}" {{ $item->is_active ? 'checked' : '' }}>
                                            <label class="form-check-label" for="switchActive{{ $item->id }}">Status Form Aktif</label>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-outline-primary px-4" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary px-4">Update</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-3 d-block mb-2"></i> Belum ada data Form Aspek Penilaian Sikap.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($aspekSikaps->hasPages())
        <div class="table-pagination-footer">
            <span class="text-muted small">
                Menampilkan {{ $aspekSikaps->firstItem() }} - {{ $aspekSikaps->lastItem() }} dari {{ $aspekSikaps->total() }} data
            </span>
            <div>
                {{ $aspekSikaps->links() }}
            </div>
        </div>
    @endif
</div>

<!-- Modal Tambah Data Baru -->
<div class="modal fade" id="modalTambahSikap" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Tambah Form Aspek Penilaian Sikap</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('sikap-form.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kategori</label>
                        <input type="text" name="kategori" class="form-control" placeholder="Contoh: Nilai Sikap dari Coach">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Judul <span class="text-danger">*</span></label>
                        <input type="text" name="judul" class="form-control" placeholder="Contoh: Penilaian Sikap Perilaku Peserta oleh Coach" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Pelatihan <span class="text-danger">*</span></label>
                        <select name="pelatihan_id" class="form-select" required>
                            <option value="">-- Pilih Program Pelatihan --</option>
                            @foreach($pelatihans as $p)
                                <option value="{{ $p->id }}">{{ $p->nama_pelatihan }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Aspek Penilaian</label>
                        <div id="aspekContainerNew">
                            <div class="input-group mb-2 aspek-row">
                                <input type="text" name="aspek_penilaian[]" class="form-control" value="Kedisiplinan" placeholder="Butir Aspek Penilaian">
                                <button type="button" class="btn btn-outline-success" onclick="addAspekRow('aspekContainerNew')">
                                    <i class="bi bi-plus-lg"></i>
                                </button>
                            </div>
                            <div class="input-group mb-2 aspek-row">
                                <input type="text" name="aspek_penilaian[]" class="form-control" value="Kepemimpinan" placeholder="Butir Aspek Penilaian">
                                <button type="button" class="btn btn-outline-danger" onclick="this.closest('.aspek-row').remove()">
                                    <i class="bi bi-dash-lg"></i>
                                </button>
                            </div>
                            <div class="input-group mb-2 aspek-row">
                                <input type="text" name="aspek_penilaian[]" class="form-control" value="Kerjasama" placeholder="Butir Aspek Penilaian">
                                <button type="button" class="btn btn-outline-danger" onclick="this.closest('.aspek-row').remove()">
                                    <i class="bi bi-dash-lg"></i>
                                </button>
                            </div>
                            <div class="input-group mb-2 aspek-row">
                                <input type="text" name="aspek_penilaian[]" class="form-control" value="Prakarsa" placeholder="Butir Aspek Penilaian">
                                <button type="button" class="btn btn-outline-danger" onclick="this.closest('.aspek-row').remove()">
                                    <i class="bi bi-dash-lg"></i>
                                </button>
                            </div>
                        </div>
                        <div class="text-danger small mt-1">* Harus diisi</div>
                    </div>
                    <div class="form-check form-switch mt-3">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="switchActiveNew" checked>
                        <label class="form-check-label" for="switchActiveNew">Status Form Aktif</label>
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

@push('scripts')
<script>
function addAspekRow(containerId) {
    const container = document.getElementById(containerId);
    if (!container) return;
    const div = document.createElement('div');
    div.className = 'input-group mb-2 aspek-row';
    div.innerHTML = `
        <input type="text" name="aspek_penilaian[]" class="form-control" placeholder="Butir Aspek Penilaian">
        <button type="button" class="btn btn-outline-danger" onclick="this.closest('.aspek-row').remove()">
            <i class="bi bi-dash-lg"></i>
        </button>
    `;
    container.appendChild(div);
}
</script>
@endpush
@endsection
