@extends('layouts.app')

@section('title', 'Daftar Mata Pelatihan')
@section('page_title', 'DAFTAR MATA PELATIHAN')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none text-muted">mapel</a></li>
    <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">Daftar Mata Pelatihan</li>
@endsection

@section('content')
<div class="container-fluid p-0">
    <div class="card border-0 shadow-sm" style="border-radius: 10px;">
        <div class="card-body p-4">
            <!-- Header Filters & Tambah Mata Pelatihan Row -->
            <form method="GET" action="{{ route('mata-pelatihan.index') }}" id="filterMapelForm" class="row g-3 align-items-end mb-4">
                <div class="col-12 col-md-4">
                    <label class="form-label small fw-semibold text-dark mb-1">Pelatihan <span class="text-danger">*</span></label>
                    <select name="pelatihan_id" class="form-select" style="font-size: 0.9rem;">
                        <option value="">~ Pilih ~</option>
                        @foreach($pelatihans as $p)
                            <option value="{{ $p->id }}" {{ $pelatihanId == $p->id ? 'selected' : '' }}>
                                {{ $p->nama_pelatihan }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label small fw-semibold text-dark mb-1">Tahun Kurikulum <span class="text-danger">*</span></label>
                    <select name="tahun_kurikulum" class="form-select" style="font-size: 0.9rem;">
                        <option value="">~ Pilih ~</option>
                        @foreach($tahuns as $t)
                            <option value="{{ $t->tahun }}" {{ $tahunKurikulum == $t->tahun ? 'selected' : '' }}>
                                {{ $t->tahun }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-auto d-flex gap-2">
                    <button type="submit" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1 px-3" style="height: 38px;">
                        <i class="bi bi-search"></i>
                        <span>Filter</span>
                    </button>
                    @if($pelatihanId || $tahunKurikulum || $search || $angkatanId)
                        <a href="{{ route('mata-pelatihan.index') }}" class="btn btn-light border d-inline-flex align-items-center" style="height: 38px;" title="Reset Filter">
                            <i class="bi bi-x-circle"></i>
                        </a>
                    @endif
                </div>
                <div class="col-12 col-md-auto ms-md-auto">
                    <a href="{{ route('mata-pelatihan.create', ['pelatihan_id' => $pelatihanId, 'tahun_kurikulum' => $tahunKurikulum]) }}" 
                       class="btn text-white fw-semibold px-3 py-2 d-inline-flex align-items-center gap-2"
                       style="background-color: #0B1727; border-radius: 6px; font-size: 0.85rem;">
                        <i class="bi bi-plus-lg"></i>
                        <span>Tambah Mata Pelatihan</span>
                    </a>
                </div>
            </form>

            <!-- Table Secondary Controls (Per Page & Search) -->
            <div class="row g-3 align-items-center mb-3">
                <div class="col-12 col-md-auto d-flex align-items-center gap-2">
                    <select name="per_page" class="form-select form-select-sm" style="width: 80px;" onchange="updatePerPage(this.value)">
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
                        <input type="text" name="search" id="searchInput" class="form-control" placeholder="Cari..." value="{{ $search }}">
                        @if($search)
                            <a href="{{ route('mata-pelatihan.index', ['pelatihan_id' => $pelatihanId, 'tahun_kurikulum' => $tahunKurikulum]) }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x"></i>
                            </a>
                        @endif
                        <button class="btn btn-outline-secondary" type="button" onclick="submitSearch()">
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
                            <th class="py-3 px-3 fw-semibold" style="width: 120px;">Kurikulum</th>
                            <th class="py-3 px-3 fw-semibold">Mata Pelatihan</th>
                            <th class="py-3 px-3 fw-semibold text-center" style="width: 80px;">JP</th>
                            <th class="py-3 px-3 fw-semibold">Pelatihan</th>
                            <th class="py-3 px-3 fw-semibold text-center" style="width: 140px; border-top-right-radius: 4px;">Tools</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse($mataPelatihans as $index => $mp)
                            @php
                                $pelatihanItem = $mp->pelatihan ?? ($mp->angkatan ? $mp->angkatan->pelatihan : null);
                            @endphp
                            <tr class="border-bottom">
                                <td class="text-center text-muted fw-medium py-3">
                                    {{ $mataPelatihans->firstItem() + $index }}
                                </td>
                                <td class="py-3 text-dark fw-semibold">
                                    {{ $mp->tahun_kurikulum ?? '2026' }}
                                </td>
                                <td class="py-3">
                                    <div class="fw-semibold text-dark">{{ $mp->nama_mata_pelatihan }}</div>
                                    @if($mp->keterangan)
                                        <div class="text-muted small">{{ $mp->keterangan }}</div>
                                    @endif
                                </td>
                                <td class="py-3 text-center fw-bold text-dark">
                                    {{ $mp->rencana_jp }}
                                </td>
                                <td class="py-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="d-flex align-items-center justify-content-center text-white" 
                                             style="width: 36px; height: 36px; background-color: #0284C7; border-radius: 6px; flex-shrink: 0;">
                                            <i class="bi bi-grid-fill fs-5"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold text-dark text-break" style="font-size: 0.9rem;">
                                                {{ $pelatihanItem ? $pelatihanItem->nama_pelatihan : 'Umum / Mandiri' }}
                                            </div>
                                            <div class="text-muted small">
                                                {{ $pelatihanItem ? $pelatihanItem->kategori : 'Pelatihan Teknis' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 text-center">
                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                        <!-- Edit Button -->
                                        <a href="{{ route('mata-pelatihan.edit', $mp) }}" 
                                           class="btn btn-sm d-inline-flex align-items-center justify-content-center" 
                                           style="width: 32px; height: 32px; background-color: #E0F2FE; border: none; border-radius: 6px; color: #0284C7;"
                                           title="Edit Data">
                                            <i class="bi bi-pencil-square fs-6"></i>
                                        </a>

                                        <!-- Delete / Tong Sampah Button (Permintaan Khusus User) -->
                                        <button type="button" class="btn btn-sm d-inline-flex align-items-center justify-content-center" 
                                                style="width: 32px; height: 32px; background-color: #FEE2E2; border: none; border-radius: 6px; color: #DC2626;"
                                                onclick="confirmDelete('delete-mp-form-{{ $mp->id }}', 'Hapus mata pelatihan {{ addslashes($mp->nama_mata_pelatihan) }}?')"
                                                title="Hapus Data (Tong Sampah)">
                                            <i class="bi bi-trash3 fs-6"></i>
                                        </button>
                                        <form id="delete-mp-form-{{ $mp->id }}" action="{{ route('mata-pelatihan.destroy', $mp) }}" method="POST" class="d-none">
                                            @csrf
                                            @method('DELETE')
                                        </form>

                                        <!-- View / Detail Button -->
                                        <a href="{{ route('mata-pelatihan.show', $mp) }}" 
                                           class="btn btn-sm d-inline-flex align-items-center justify-content-center" 
                                           style="width: 32px; height: 32px; background-color: #E0F2FE; border: none; border-radius: 6px; color: #0284C7;"
                                           title="Detail Data">
                                            <i class="bi bi-search fs-6"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary"></i>
                                    Tidak ada mata pelatihan yang ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Footer Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
                <div class="text-muted small">
                    Menampilkan {{ $mataPelatihans->firstItem() ?? 0 }} s/d {{ $mataPelatihans->lastItem() ?? 0 }} dari <strong>{{ $mataPelatihans->total() }}</strong> data
                </div>
                <div>
                    {{ $mataPelatihans->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function updatePerPage(val) {
        const url = new URL(window.location.href);
        url.searchParams.set('per_page', val);
        window.location.href = url.toString();
    }

    function submitSearch() {
        const val = document.getElementById('searchInput').value;
        const url = new URL(window.location.href);
        if (val) {
            url.searchParams.set('search', val);
        } else {
            url.searchParams.delete('search');
        }
        window.location.href = url.toString();
    }

    document.getElementById('searchInput')?.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            submitSearch();
        }
    });
</script>
@endpush
@endsection