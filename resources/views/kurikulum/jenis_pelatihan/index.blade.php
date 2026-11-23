@extends('layouts.app')

@section('title', 'Jenis Pelatihan')
@section('page_title', 'JENIS PELATIHAN')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none text-muted">bidang</a></li>
    <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">Jenis Pelatihan</li>
@endsection

@section('content')
<div class="container-fluid p-0">
    <div class="card border-0 shadow-sm" style="border-radius: 10px;">
        <div class="card-body p-4">
            <!-- Header Row with Tambah Data Button -->
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <h5 class="fw-bold mb-0 text-uppercase" style="letter-spacing: 0.5px; color: #1E293B;">
                    JENIS PELATIHAN
                </h5>
                <button type="button" class="btn text-white fw-semibold px-3 py-2 d-inline-flex align-items-center gap-2" 
                        style="background-color: #0B1727; border-radius: 6px; font-size: 0.85rem;"
                        data-bs-toggle="modal" data-bs-target="#modalTambahJenis">
                    <i class="bi bi-plus-lg"></i>
                    <span>Tambah Data</span>
                </button>
            </div>

            <!-- Filter & Search Row -->
            <form method="GET" action="{{ route('jenis-pelatihan.index') }}" id="filterForm" class="row g-3 align-items-center mb-3">
                <div class="col-12 col-md-auto d-flex align-items-center gap-2">
                    <select name="per_page" class="form-select form-select-sm" style="width: 80px;" onchange="document.getElementById('filterForm').submit()">
                        <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                    </select>
                    <span class="text-muted small fw-medium text-nowrap">Data Per Halaman</span>
                </div>
                <div class="col-12 col-md-auto ms-md-auto d-flex align-items-center gap-2">
                    <label class="text-muted small fw-medium mb-0 text-nowrap">Pencarian :</label>
                    <div class="input-group input-group-sm" style="width: 250px;">
                        <input type="text" name="search" class="form-control" placeholder="Cari..." value="{{ $search }}">
                        @if($search)
                            <a href="{{ route('jenis-pelatihan.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x"></i>
                            </a>
                        @endif
                        <button class="btn btn-outline-secondary" type="submit">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>
            </form>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table align-middle mb-0" style="border-collapse: separate; border-spacing: 0;">
                    <thead>
                        <tr style="background-color: #3B5998; color: #FFFFFF;">
                            <th class="py-3 px-3 fw-semibold text-center" style="width: 60px; border-top-left-radius: 4px;">No</th>
                            <th class="py-3 px-3 fw-semibold">Jenis</th>
                            <th class="py-3 px-3 fw-semibold" style="width: 180px;">Status</th>
                            <th class="py-3 px-3 fw-semibold text-center" style="width: 140px; border-top-right-radius: 4px;">Tools</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse($jenisPelatihans as $index => $item)
                            <tr class="border-bottom">
                                <td class="text-center text-muted fw-medium py-3">
                                    {{ $jenisPelatihans->firstItem() + $index }}
                                </td>
                                <td class="py-3 text-dark fw-medium">
                                    {{ $item->nama_jenis }}
                                </td>
                                <td class="py-3">
                                    <div class="form-check form-switch d-inline-flex align-items-center gap-2 m-0 p-0">
                                        <input class="form-check-input ms-0 toggle-status-switch" type="checkbox" role="switch" 
                                               style="cursor: pointer; width: 2.5rem; height: 1.25rem;"
                                               data-id="{{ $item->id }}" {{ $item->status ? 'checked' : '' }}>
                                        <label class="form-check-label small fw-semibold text-dark mb-0 status-label-{{ $item->id }}">
                                            Active
                                        </label>
                                    </div>
                                </td>
                                <td class="py-3 text-center">
                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                        <!-- Edit Button -->
                                        <button type="button" class="btn btn-sm d-inline-flex align-items-center justify-content-center edit-btn" 
                                                style="width: 32px; height: 32px; background-color: #E0F2FE; border: none; border-radius: 6px; color: #0284C7;"
                                                data-id="{{ $item->id }}" 
                                                data-nama="{{ $item->nama_jenis }}"
                                                data-status="{{ $item->status ? '1' : '0' }}"
                                                title="Edit Data">
                                            <i class="bi bi-pencil-square fs-6"></i>
                                        </button>

                                        <!-- Delete / Tong Sampah Button (Permintaan Khusus User) -->
                                        <button type="button" class="btn btn-sm d-inline-flex align-items-center justify-content-center delete-btn" 
                                                style="width: 32px; height: 32px; background-color: #FEE2E2; border: none; border-radius: 6px; color: #DC2626;"
                                                onclick="confirmDelete('delete-form-{{ $item->id }}', 'Hapus jenis pelatihan {{ addslashes($item->nama_jenis) }}?')"
                                                title="Hapus Data (Tong Sampah)">
                                            <i class="bi bi-trash3 fs-6"></i>
                                        </button>
                                        <form id="delete-form-{{ $item->id }}" action="{{ route('jenis-pelatihan.destroy', $item->id) }}" method="POST" class="d-none">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary"></i>
                                    Belum ada data Jenis Pelatihan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Footer Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
                <div class="text-muted small">
                    Menampilkan {{ $jenisPelatihans->firstItem() ?? 0 }} s/d {{ $jenisPelatihans->lastItem() ?? 0 }} dari <strong>{{ $jenisPelatihans->total() }}</strong> data
                </div>
                <div>
                    {{ $jenisPelatihans->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Data -->
<div class="modal fade" id="modalTambahJenis" tabindex="-1" aria-labelledby="modalTambahJenisLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('jenis-pelatihan.store') }}" method="POST">
                @csrf
                <div class="modal-header text-white" style="background-color: #0B1727;">
                    <h5 class="modal-title fw-semibold fs-6" id="modalTambahJenisLabel">
                        <i class="bi bi-plus-circle me-1"></i> Tambah Jenis Pelatihan
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-dark">Nama Jenis Pelatihan <span class="text-danger">*</span></label>
                        <input type="text" name="nama_jenis" class="form-control" placeholder="Contoh: Pelatihan Teknis" required>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="status" value="1" id="statusTambah" checked>
                        <label class="form-check-label fw-medium small text-dark" for="statusTambah">Status Aktif</label>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary" style="background-color: #2563EB;">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Data -->
<div class="modal fade" id="modalEditJenis" tabindex="-1" aria-labelledby="modalEditJenisLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form id="formEditJenis" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header text-white" style="background-color: #0B1727;">
                    <h5 class="modal-title fw-semibold fs-6" id="modalEditJenisLabel">
                        <i class="bi bi-pencil-square me-1"></i> Edit Jenis Pelatihan
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-dark">Nama Jenis Pelatihan <span class="text-danger">*</span></label>
                        <input type="text" name="nama_jenis" id="editNamaJenis" class="form-control" required>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="status" value="1" id="editStatus">
                        <label class="form-check-label fw-medium small text-dark" for="editStatus">Status Aktif</label>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary" style="background-color: #2563EB;">Update Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Handle Edit Modal
    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const nama = this.getAttribute('data-nama');
            const status = this.getAttribute('data-status');

            document.getElementById('editNamaJenis').value = nama;
            document.getElementById('editStatus').checked = (status === '1');
            document.getElementById('formEditJenis').action = "{{ url('jenis-pelatihan') }}/" + id;

            const modal = new bootstrap.Modal(document.getElementById('modalEditJenis'));
            modal.show();
        });
    });

    // Handle AJAX Toggle Status Switch
    document.querySelectorAll('.toggle-status-switch').forEach(toggle => {
        toggle.addEventListener('change', function() {
            const id = this.getAttribute('data-id');
            const checked = this.checked;

            fetch("{{ url('jenis-pelatihan') }}/" + id + "/toggle", {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true
                    });
                    Toast.fire({
                        icon: 'success',
                        title: data.message || 'Status berhasil diperbarui'
                    });
                }
            })
            .catch(err => {
                console.error(err);
                this.checked = !checked;
                Swal.fire('Error', 'Gagal memperbarui status', 'error');
            });
        });
    });
</script>
@endpush
@endsection