@extends('layouts.app')

@section('title', 'Pelatihan')
@section('page_title', 'PELATIHAN')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none text-muted">pelatihandiklat</a></li>
    <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">Pelatihan</li>
@endsection

@section('content')
<div class="container-fluid p-0">
    <div class="card border-0 shadow-sm" style="border-radius: 10px;">
        <div class="card-body p-4">
            <!-- Filter Jenis Pelatihan & Tambah Data Button Row -->
            <form method="GET" action="{{ route('pelatihan.index') }}" id="filterPelatihanForm" class="row g-3 align-items-center mb-4">
                <div class="col-12 col-md-5 d-flex gap-2 align-items-center">
                    <select name="kategori" class="form-select" style="font-size: 0.9rem;">
                        <option value="">Semua Jenis Pelatihan</option>
                        @foreach($kategoriOptions as $kat)
                            <option value="{{ $kat }}" {{ $kategori == $kat ? 'selected' : '' }}>{{ $kat }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-primary px-3 fw-semibold" style="background-color: #2563EB; height: 38px;">
                        Go
                    </button>
                    @if($kategori || $search || $status)
                        <a href="{{ route('pelatihan.index') }}" class="btn btn-light border d-inline-flex align-items-center" style="height: 38px;" title="Reset Filter">
                            <i class="bi bi-x-circle"></i>
                        </a>
                    @endif
                </div>
                <div class="col-12 col-md-auto ms-md-auto">
                    <a href="{{ route('pelatihan.create') }}" 
                       class="btn text-white fw-semibold px-3 py-2 d-inline-flex align-items-center gap-2"
                       style="background-color: #0B1727; border-radius: 6px; font-size: 0.85rem;">
                        <i class="bi bi-plus-lg"></i>
                        <span>Tambah Data</span>
                    </a>
                </div>
            </form>

            <!-- Table Secondary Controls (Per Page & Search) -->
            <div class="row g-3 align-items-center mb-3">
                <div class="col-12 col-md-auto d-flex align-items-center gap-2">
                    <select name="per_page" class="form-select form-select-sm" style="width: 80px;" onchange="updatePelatihanPerPage(this.value)">
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
                        <input type="text" name="search" id="searchPelatihanInput" class="form-control" placeholder="Cari..." value="{{ $search }}">
                        @if($search)
                            <a href="{{ route('pelatihan.index', ['kategori' => $kategori]) }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x"></i>
                            </a>
                        @endif
                        <button class="btn btn-outline-secondary" type="button" onclick="submitPelatihanSearch()">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table align-middle mb-0" style="border-collapse: separate; border-spacing: 0;">
                    <thead>
                        <tr style="background-color: #3B5998; color: #FFFFFF;">
                            <th class="py-3 px-3 fw-semibold text-center" style="width: 60px; border-top-left-radius: 4px;">No</th>
                            <th class="py-3 px-3 fw-semibold">Nama Pelatihan</th>
                            <th class="py-3 px-3 fw-semibold" style="width: 180px;">Status</th>
                            <th class="py-3 px-3 fw-semibold text-center" style="width: 140px; border-top-right-radius: 4px;">Tools</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse($pelatihans as $index => $pel)
                            @php
                                $isActive = in_array($pel->status, ['Berjalan', 'Direncanakan']);
                            @endphp
                            <tr class="border-bottom">
                                <td class="text-center text-muted fw-medium py-3">
                                    {{ $pelatihans->firstItem() + $index }}
                                </td>
                                <td class="py-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="d-flex align-items-center justify-content-center text-white" 
                                             style="width: 38px; height: 38px; background-color: #0284C7; border-radius: 6px; flex-shrink: 0;">
                                            <i class="bi bi-grid-fill fs-5"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark" style="font-size: 0.95rem;">
                                                {{ $pel->nama_pelatihan }}
                                            </div>
                                            <div class="d-flex align-items-center gap-2 flex-wrap mt-1">
                                                <span class="text-muted small">{{ $pel->kategori ?? 'Pelatihan Fungsional' }}</span>
                                                <span class="badge bg-light text-primary border" style="font-size: 0.72rem;" title="Kode Token Registrasi Kelas Peserta">
                                                    <i class="bi bi-key-fill text-warning me-1"></i>Token: <strong>{{ $pel->kode_pelatihan }}</strong>
                                                </span>
                                                <button type="button" class="btn btn-sm btn-link p-0 text-muted" title="Salin Kode Token" onclick="navigator.clipboard.writeText('{{ $pel->kode_pelatihan }}'); if(window.SirAlert){ SirAlert.success('Kode Token Pelatihan {{ $pel->kode_pelatihan }} berhasil disalin!', 'Tersalin !'); } else { alert('Tersalin!'); }">
                                                    <i class="bi bi-copy" style="font-size: 0.8rem;"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3">
                                    <div class="form-check form-switch d-inline-flex align-items-center gap-2 m-0 p-0">
                                        <input class="form-check-input ms-0 toggle-pelatihan-status" type="checkbox" role="switch" 
                                               style="cursor: pointer; width: 2.5rem; height: 1.25rem;"
                                               data-id="{{ $pel->id }}" {{ $isActive ? 'checked' : '' }}>
                                        <label class="form-check-label small fw-semibold text-dark mb-0 status-label-pel-{{ $pel->id }}">
                                            Active
                                        </label>
                                    </div>
                                </td>
                                <td class="py-3 text-center">
                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                        <!-- Edit Button -->
                                        <a href="{{ route('pelatihan.edit', $pel) }}" 
                                           class="btn btn-sm d-inline-flex align-items-center justify-content-center" 
                                           style="width: 32px; height: 32px; background-color: #E0F2FE; border: none; border-radius: 6px; color: #0284C7;"
                                           title="Edit Data">
                                            <i class="bi bi-pencil-square fs-6"></i>
                                        </a>

                                        <!-- Delete / Tong Sampah Button (Permintaan Khusus User) -->
                                        <button type="button" class="btn btn-sm d-inline-flex align-items-center justify-content-center" 
                                                style="width: 32px; height: 32px; background-color: #FEE2E2; border: none; border-radius: 6px; color: #DC2626;"
                                                onclick="confirmDelete('delete-pel-form-{{ $pel->id }}', 'Hapus pelatihan {{ addslashes($pel->nama_pelatihan) }}?')"
                                                title="Hapus Data (Tong Sampah)">
                                            <i class="bi bi-trash3 fs-6"></i>
                                        </button>
                                        <form id="delete-pel-form-{{ $pel->id }}" action="{{ route('pelatihan.destroy', $pel) }}" method="POST" class="d-none">
                                            @csrf
                                            @method('DELETE')
                                        </form>

                                        <!-- View / Detail Button -->
                                        <a href="{{ route('pelatihan.show', $pel) }}" 
                                           class="btn btn-sm d-inline-flex align-items-center justify-content-center" 
                                           style="width: 32px; height: 32px; background-color: #E0F2FE; border: none; border-radius: 6px; color: #0284C7;"
                                           title="Detail Pelatihan">
                                            <i class="bi bi-search fs-6"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary"></i>
                                    Tidak ada data pelatihan yang ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Footer Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
                <div class="text-muted small">
                    Menampilkan {{ $pelatihans->firstItem() ?? 0 }} s/d {{ $pelatihans->lastItem() ?? 0 }} dari <strong>{{ $pelatihans->total() }}</strong> data
                </div>
                <div>
                    {{ $pelatihans->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function updatePelatihanPerPage(val) {
        const url = new URL(window.location.href);
        url.searchParams.set('per_page', val);
        window.location.href = url.toString();
    }

    function submitPelatihanSearch() {
        const val = document.getElementById('searchPelatihanInput').value;
        const url = new URL(window.location.href);
        if (val) {
            url.searchParams.set('search', val);
        } else {
            url.searchParams.delete('search');
        }
        window.location.href = url.toString();
    }

    document.getElementById('searchPelatihanInput')?.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            submitPelatihanSearch();
        }
    });

    // Handle AJAX Toggle Status Switch
    document.querySelectorAll('.toggle-pelatihan-status').forEach(toggle => {
        toggle.addEventListener('change', function() {
            const id = this.getAttribute('data-id');
            const checked = this.checked;

            fetch("{{ url('pelatihan') }}/" + id + "/toggle", {
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
                        title: 'Status pelatihan berhasil diperbarui'
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