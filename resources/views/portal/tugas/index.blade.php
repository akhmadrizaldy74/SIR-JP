@extends('portal.layout')

@section('title', 'Tugas')
@section('page_title', 'TUGAS')
@section('custom_breadcrumb')
    <span class="text-secondary">tugas</span> &gt; <span class="fw-semibold text-dark">Tugas</span>
@endsection

@section('content')
<div class="card border-0 shadow-sm rounded-3 mb-4" style="background: #FFFFFF;">
    <!-- Header Card -->
    <div class="card-header bg-transparent border-0 px-4 pt-4 pb-2 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold text-dark mb-0" style="font-size: 1.15rem;">Tugas</h5>
        <a href="{{ route('portal.dashboard') }}" class="btn btn-sm text-white fw-medium rounded-2 px-3 py-1 d-inline-flex align-items-center gap-1 shadow-sm" style="background-color: #2b76cb; font-size: 0.85rem;">
            <i class="bi bi-chevron-double-left"></i> Kembali ke Daftar
        </a>
    </div>

    <div class="card-body px-4 pt-2 pb-4">
        <!-- Sub-Tabs: Tugas Individu & Tugas Kelompok Sesuai Gambar -->
        <div class="rounded-3 overflow-hidden border mb-4" style="background-color: #F1F5F9;">
            <div class="row g-0 text-center">
                <div class="col-6">
                    <a href="{{ route('portal.tugas.index', ['kategori' => 'individu']) }}" 
                       class="d-flex align-items-center justify-content-center py-2 px-3 text-decoration-none fw-semibold transition-all {{ $kategori === 'individu' ? 'bg-white text-dark shadow-sm' : 'text-secondary' }}"
                       style="font-size: 0.92rem; border-right: 1px solid #E2E8F0;">
                        <i class="bi bi-person me-2 fs-6"></i> Tugas Individu
                        @if(isset($countIndividu) && $countIndividu > 0)
                            <span class="badge rounded-pill bg-light text-dark ms-2 border" style="font-size: 0.75rem;">{{ $countIndividu }}</span>
                        @endif
                    </a>
                </div>
                <div class="col-6">
                    <a href="{{ route('portal.tugas.index', ['kategori' => 'kelompok']) }}" 
                       class="d-flex align-items-center justify-content-center py-2 px-3 text-decoration-none fw-semibold transition-all {{ $kategori === 'kelompok' ? 'bg-white text-dark shadow-sm' : 'text-secondary' }}"
                       style="font-size: 0.92rem;">
                        <i class="bi bi-people me-2 fs-6"></i> Tugas Kelompok
                        @if(isset($countKelompok) && $countKelompok > 0)
                            <span class="badge rounded-pill bg-light text-dark ms-2 border" style="font-size: 0.75rem;">{{ $countKelompok }}</span>
                        @endif
                    </a>
                </div>
            </div>
        </div>

        <!-- Filter Row: Per Halaman & Pencarian Sesuai Gambar -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
            <!-- Data Per Halaman -->
            <div class="d-flex align-items-center gap-2">
                <select class="form-select form-select-sm" style="width: 75px; border-color: #CBD5E1;" onchange="window.location.href='{{ route('portal.tugas.index', ['kategori' => $kategori, 'search' => $search]) }}&per_page=' + this.value">
                    <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                    <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                </select>
                <span class="small text-secondary">Data Per Halaman</span>
            </div>

            <!-- Pencarian -->
            <form action="{{ route('portal.tugas.index') }}" method="GET" class="d-flex align-items-center gap-2">
                <input type="hidden" name="kategori" value="{{ $kategori }}">
                <input type="hidden" name="per_page" value="{{ $perPage }}">
                <label for="inputSearch" class="small text-secondary mb-0">Pencarian:</label>
                <div class="input-group input-group-sm" style="width: 220px;">
                    <input type="text" id="inputSearch" name="search" value="{{ $search }}" class="form-control" placeholder="Cari soal/materi..." style="border-color: #CBD5E1;">
                    @if(!empty($search))
                        <a href="{{ route('portal.tugas.index', ['kategori' => $kategori, 'per_page' => $perPage]) }}" class="btn btn-outline-secondary" title="Hapus Filter">
                            <i class="bi bi-x"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Tabel Tugas Sesuai Screenshot 1 -->
        <div class="table-responsive rounded-2 overflow-hidden border">
            <table class="table align-middle mb-0" style="font-size: 0.88rem;">
                <thead style="background-color: #274C77; color: #FFFFFF;">
                    <tr>
                        <th class="py-3 px-3 fw-bold text-uppercase" style="width: 45%;">
                            SOAL <i class="bi bi-arrow-down-up small opacity-75 ms-1"></i>
                        </th>
                        <th class="py-3 px-3 fw-bold text-uppercase" style="width: 30%;">
                            MATA PELATIHAN <i class="bi bi-arrow-down-up small opacity-75 ms-1"></i>
                        </th>
                        <th class="py-3 px-3 fw-bold text-uppercase" style="width: 15%;">
                            STATUS
                        </th>
                        <th class="py-3 px-3 fw-bold text-uppercase text-center" style="width: 10%;">
                            ACTION <i class="bi bi-arrow-down-up small opacity-75 ms-1"></i>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @forelse($tugasList as $index => $item)
                        @php
                            $userSubmission = $item->pengumpulan->first();
                            $isSubmitted = !empty($userSubmission);
                        @endphp
                        <tr>
                            <!-- Kolom SOAL Sesuai Gambar -->
                            <td class="py-3 px-3">
                                <div class="d-flex align-items-center gap-3">
                                    <span class="text-secondary fw-semibold small" style="min-width: 14px;">
                                        {{ $tugasList->firstItem() + $index }}
                                    </span>
                                    <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0" 
                                         style="width: 38px; height: 38px; background-color: #274C77; color: #FFFFFF;">
                                        <i class="bi bi-check-square fs-5"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark lh-sm mb-1" style="font-size: 0.95rem;">
                                            {{ $item->judul }}
                                        </div>
                                        <div class="d-flex flex-wrap gap-1 align-items-center">
                                            <!-- Badge Penyelenggara Tosca -->
                                            <span class="badge py-1 px-2 rounded-1 fw-normal" style="background-color: #06B6D4; color: #FFFFFF; font-size: 0.72rem;">
                                                <i class="bi bi-person me-1"></i> {{ $item->penyelenggara ?? 'PPSDM Ketenagakerjaan' }}
                                            </span>
                                            <!-- Badge Waktu Gelap -->
                                            <span class="badge py-1 px-2 rounded-1 fw-normal" style="background-color: #334155; color: #FFFFFF; font-size: 0.72rem;">
                                                {{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d/m/Y H:i') }} s.d {{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d/m/Y H:i') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Kolom MATA PELATIHAN Sesuai Gambar -->
                            <td class="py-3 px-3 text-secondary">
                                <span class="fw-medium text-dark">{{ $item->nama_mata_pelatihan ?? $item->mataPelatihan->nama ?? '-' }}</span>
                            </td>

                            <!-- Kolom STATUS Sesuai Gambar -->
                            <td class="py-3 px-3">
                                @if($isSubmitted)
                                    <span class="badge px-2 py-1 rounded-1 fw-medium" style="background-color: #DCFCE7; color: #15803D; font-size: 0.75rem;">
                                        <i class="bi bi-check-circle me-1"></i> Sudah di upload
                                    </span>
                                    <div class="small text-muted mt-1" style="font-size: 0.7rem;">
                                        {{ $userSubmission->waktu_upload ? $userSubmission->waktu_upload->format('d/m/Y H:i') : '' }}
                                    </div>
                                @else
                                    <span class="badge px-2 py-1 rounded-1 fw-medium" style="background-color: #FEE2E2; color: #DC2626; font-size: 0.75rem;">
                                        * Belum di upload
                                    </span>
                                @endif
                            </td>

                            <!-- Kolom ACTION Sesuai Gambar (Tombol Bulat Cyan) -->
                            <td class="py-3 px-3 text-center">
                                <button type="button" 
                                        class="btn btn-sm rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm" 
                                        style="width: 34px; height: 34px; background-color: #CCFBF1; color: #0D9488; border: 1px solid #99F6E4;"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalUploadTugas{{ $item->id }}"
                                        title="Upload Tugas">
                                    <i class="bi bi-upload"></i>
                                </button>
                            </td>
                        </tr>

                        <!-- MODAL UPLOAD TUGAS SESUAI SCREENSHOT 2 -->
                        <div class="modal fade" id="modalUploadTugas{{ $item->id }}" tabindex="-1" aria-labelledby="modalUploadTugasLabel{{ $item->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content border-0 shadow-lg rounded-3">
                                    <!-- Modal Header -->
                                    <div class="modal-header border-0 pb-0 px-4 pt-4">
                                        <h5 class="modal-title fw-bold text-dark" id="modalUploadTugasLabel{{ $item->id }}" style="font-size: 1.15rem;">Upload Tugas</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>

                                    <!-- Form Upload -->
                                    <form action="{{ route('portal.tugas.upload', $item->id) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="modal-body px-4 pt-3 pb-2">
                                            <div class="d-flex flex-column gap-3">
                                                <!-- Row: Mata Pelatihan -->
                                                <div class="row align-items-center py-1">
                                                    <div class="col-sm-3 fw-semibold text-secondary small">Mata Pelatihan</div>
                                                    <div class="col-sm-9 text-dark fw-medium">{{ $item->nama_mata_pelatihan ?? $item->mataPelatihan->nama ?? '-' }}</div>
                                                </div>

                                                <!-- Row: Soal -->
                                                <div class="row align-items-center py-1 border-top">
                                                    <div class="col-sm-3 fw-semibold text-secondary small">Soal</div>
                                                    <div class="col-sm-9 text-dark">{{ $item->judul }}</div>
                                                </div>

                                                <!-- Row: File Materi -->
                                                <div class="row align-items-center py-1 border-top">
                                                    <div class="col-sm-3 fw-semibold text-secondary small">File Materi</div>
                                                    <div class="col-sm-9 text-muted small">
                                                        @if(!empty($item->file_materi))
                                                            <a href="{{ asset('storage/' . $item->file_materi) }}" target="_blank" class="text-primary text-decoration-none">
                                                                <i class="bi bi-file-earmark-arrow-down me-1"></i> Unduh File Materi
                                                            </a>
                                                        @else
                                                            Tidak ada file materi
                                                        @endif
                                                    </div>
                                                </div>

                                                <!-- Row: Tanggal -->
                                                <div class="row align-items-center py-1 border-top">
                                                    <div class="col-sm-3 fw-semibold text-secondary small">Tanggal</div>
                                                    <div class="col-sm-9 text-dark small">
                                                        {{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d/m/Y') }}
                                                    </div>
                                                </div>

                                                <!-- Row: Kategori -->
                                                <div class="row align-items-center py-1 border-top">
                                                    <div class="col-sm-3 fw-semibold text-secondary small">Kategori</div>
                                                    <div class="col-sm-9 text-dark small">{{ $item->kategori }}</div>
                                                </div>

                                                <!-- Row: Keterangan -->
                                                <div class="row align-items-center py-1 border-top">
                                                    <div class="col-sm-3 fw-semibold text-secondary small">Keterangan</div>
                                                    <div class="col-sm-9 text-muted small">{{ $item->keterangan ?? '-' }}</div>
                                                </div>

                                                <!-- Row: Upload File Sesuai Screenshot 2 -->
                                                <div class="row align-items-start py-2 border-top">
                                                    <div class="col-sm-3 fw-semibold text-secondary small pt-2">Upload File</div>
                                                    <div class="col-sm-9">
                                                        <div class="input-group">
                                                            <input type="file" name="file_jawaban" class="form-control" id="fileJawaban{{ $item->id }}" required accept=".pdf,.doc,.docx,.zip,.png,.jpg,.jpeg">
                                                        </div>
                                                        <div class="small mt-1">
                                                            @if($isSubmitted && !empty($userSubmission->nama_file_asli))
                                                                <span class="text-success">
                                                                    <i class="bi bi-check-circle-fill me-1"></i> File tersimpan: 
                                                                    <a href="{{ route('portal.tugas.download', $item->id) }}" class="fw-semibold text-success text-decoration-underline">
                                                                        {{ $userSubmission->nama_file_asli }}
                                                                    </a>
                                                                </span>
                                                            @else
                                                                <span class="text-secondary">Tidak ada File Jawaban</span>
                                                            @endif
                                                        </div>
                                                        <div class="text-muted small mt-1" style="font-size: 0.72rem;">
                                                            Format diizinkan: PDF, DOC, DOCX, ZIP, PNG, JPG (Maks. 10MB)
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Row: Catatan Sesuai Screenshot 2 -->
                                                <div class="row align-items-start py-2 border-top">
                                                    <div class="col-sm-3 fw-semibold text-secondary small pt-2">Catatan</div>
                                                    <div class="col-sm-9">
                                                        <textarea name="catatan" rows="3" class="form-control" placeholder="Tuliskan catatan tambahan jika ada...">{{ $isSubmitted ? $userSubmission->catatan : '' }}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Modal Footer Sesuai Screenshot 2 -->
                                        <div class="modal-footer border-0 px-4 pb-4 pt-2 d-flex justify-content-between align-items-center">
                                            <span class="text-danger small fw-medium">* Harus diisi</span>
                                            <div class="d-flex gap-2">
                                                <button type="submit" class="btn text-white fw-medium px-4 py-1 rounded-2 shadow-sm" style="background-color: #0F172A; font-size: 0.88rem;">
                                                    Update
                                                </button>
                                                <button type="button" class="btn text-white fw-medium px-4 py-1 rounded-2 shadow-sm" style="background-color: #2563EB; font-size: 0.88rem;" data-bs-dismiss="modal">
                                                    Cancel
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                    @empty
                        <tr>
                            <td colspan="4" class="py-5 text-center text-muted">
                                <i class="bi bi-file-earmark-x fs-1 d-block mb-2 text-secondary"></i>
                                <h6 class="fw-bold text-dark">Belum Ada Tugas</h6>
                                <p class="small mb-0">Belum ada tugas {{ $kategori }} yang tersedia untuk saat ini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination & Counter Sesuai Gambar -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 mt-3 pt-2">
            <div class="small text-secondary">
                Menampilkan {{ $tugasList->firstItem() ?? 0 }} s/d {{ $tugasList->lastItem() ?? 0 }} dari {{ $tugasList->total() }} data
            </div>
            <div>
                {{ $tugasList->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection
