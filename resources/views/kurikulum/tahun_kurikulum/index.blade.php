@extends('layouts.app')

@section('title', 'Tahun Kurikulum')
@section('page_title', 'TAHUN KURIKULUM')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none text-muted">tahun</a></li>
    <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">Tahun Kurikulum</li>
@endsection

@section('content')
<div class="container-fluid p-0">
    <div class="card border-0 shadow-sm" style="border-radius: 10px;">
        <div class="card-body p-4">
            <!-- Header Row with Tambah Data Button -->
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <h5 class="fw-bold mb-0 text-uppercase" style="letter-spacing: 0.5px; color: #1E293B;">
                    TAHUN KURIKULUM
                </h5>
                <button type="button" class="btn text-white fw-semibold px-3 py-2 d-inline-flex align-items-center gap-2" 
                        style="background-color: #0B1727; border-radius: 6px; font-size: 0.85rem;"
                        data-bs-toggle="modal" data-bs-target="#modalTambahTahun">
                    <i class="bi bi-plus-lg"></i>
                    <span>Tambah Data</span>
                </button>
            </div>

            <!-- Filter & Search Row -->
            <form method="GET" action="{{ route('tahun-kurikulum.index') }}" id="filterFormTahun" class="row g-3 align-items-center mb-3">
                <div class="col-12 col-md-auto d-flex align-items-center gap-2">
                    <select name="per_page" class="form-select form-select-sm" style="width: 80px;" onchange="document.getElementById('filterFormTahun').submit()">
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
                            <a href="{{ route('tahun-kurikulum.index') }}" class="btn btn-outline-secondary">
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
                            <th class="py-3 px-3 fw-semibold">Nama Kurikulum</th>
                            <th class="py-3 px-3 fw-semibold text-center" style="width: 140px; border-top-right-radius: 4px;">Tools</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse($tahunKurikulums as $index => $item)
                            <tr class="border-bottom">
                                <td class="text-center text-muted fw-medium py-3">
                                    {{ $tahunKurikulums->firstItem() + $index }}
                                </td>
                                <td class="py-3 text-dark fw-bold fs-6">
                                    {{ $item->tahun }}
                                </td>
                                <td class="py-3 text-center">
                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                        <!-- Edit Button -->
                                        <button type="button" class="btn btn-sm d-inline-flex align-items-center justify-content-center edit-tahun-btn" 
                                                style="width: 32px; height: 32px; background-color: #E0F2FE; border: none; border-radius: 6px; color: #0284C7;"
                                                data-id="{{ $item->id }}" 
                                                data-tahun="{{ $item->tahun }}"
                                                title="Edit Data">
                                            <i class="bi bi-pencil-square fs-6"></i>
                                        </button>

                                        <!-- Delete / Tong Sampah Button (Permintaan Khusus User) -->
                                        <button type="button" class="btn btn-sm d-inline-flex align-items-center justify-content-center delete-btn" 
                                                style="width: 32px; height: 32px; background-color: #FEE2E2; border: none; border-radius: 6px; color: #DC2626;"
                                                onclick="confirmDelete('delete-tahun-form-{{ $item->id }}', 'Hapus tahun kurikulum {{ $item->tahun }}?')"
                                                title="Hapus Data (Tong Sampah)">
                                            <i class="bi bi-trash3 fs-6"></i>
                                        </button>
                                        <form id="delete-tahun-form-{{ $item->id }}" action="{{ route('tahun-kurikulum.destroy', $item->id) }}" method="POST" class="d-none">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary"></i>
                                    Belum ada data Tahun Kurikulum.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Footer Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
                <div class="text-muted small">
                    Menampilkan {{ $tahunKurikulums->firstItem() ?? 0 }} s/d {{ $tahunKurikulums->lastItem() ?? 0 }} dari <strong>{{ $tahunKurikulums->total() }}</strong> data
                </div>
                <div>
                    {{ $tahunKurikulums->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Data -->
<div class="modal fade" id="modalTambahTahun" tabindex="-1" aria-labelledby="modalTambahTahunLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('tahun-kurikulum.store') }}" method="POST">
                @csrf
                <div class="modal-header text-white" style="background-color: #0B1727;">
                    <h5 class="modal-title fw-semibold fs-6" id="modalTambahTahunLabel">
                        <i class="bi bi-plus-circle me-1"></i> Tambah Tahun Kurikulum
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-dark">Nama / Tahun Kurikulum <span class="text-danger">*</span></label>
                        <input type="text" name="tahun" class="form-control" placeholder="Contoh: 2027 atau Kurikulum 2026" required>
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
<div class="modal fade" id="modalEditTahun" tabindex="-1" aria-labelledby="modalEditTahunLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form id="formEditTahun" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header text-white" style="background-color: #0B1727;">
                    <h5 class="modal-title fw-semibold fs-6" id="modalEditTahunLabel">
                        <i class="bi bi-pencil-square me-1"></i> Edit Tahun Kurikulum
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-dark">Nama / Tahun Kurikulum <span class="text-danger">*</span></label>
                        <input type="text" name="tahun" id="editTahunValue" class="form-control" required>
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
    document.querySelectorAll('.edit-tahun-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const tahun = this.getAttribute('data-tahun');

            document.getElementById('editTahunValue').value = tahun;
            document.getElementById('formEditTahun').action = "{{ url('tahun-kurikulum') }}/" + id;

            const modal = new bootstrap.Modal(document.getElementById('modalEditTahun'));
            modal.show();
        });
    });
</script>
@endpush
@endsection