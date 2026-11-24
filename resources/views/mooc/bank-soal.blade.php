@extends('layouts.app')

@section('title', 'LMS MOOC - Bank Soal')
@section('page_title', 'Bank Soal LMS MOOC')

@section('breadcrumb_custom')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">home</a></li>
    <li class="breadcrumb-item">LMS MOOC</li>
    <li class="breadcrumb-item active" aria-current="page">Bank Soal</li>
@endsection

@section('content')
@include('partials.alerts')

<div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h5 class="fw-bold text-dark mb-1">Manajemen Bank Soal &amp; Kuis</h5>
            <p class="text-muted small mb-0">Kelola butir pertanyaan pre-test, kuis materi, post-test, dan evaluasi modul pembelajaran.</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-primary btn-sm px-3 rounded-3 d-inline-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#createBankSoalModal">
                <i class="bi bi-plus-lg"></i> Tambah Bank Soal
            </button>
        </div>
    </div>

    <!-- Toolbar Filter, Search, & Sortir -->
    <div class="card border rounded-3 p-3 bg-light mb-4 shadow-none">
        <form action="{{ route('mooc.bank-soal') }}" method="GET" class="row g-2 align-items-center">
            <!-- Search Input -->
            <div class="col-md-5">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0 text-muted">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" 
                           name="search" 
                           id="bankSoalSearch"
                           class="form-control form-control-sm border-start-0" 
                           placeholder="Ketik nama pelatihan, kategori, butir soal..." 
                           value="{{ $search ?? '' }}"
                           autocomplete="off">
                </div>
            </div>

            <!-- Filter Status -->
            <div class="col-md-3">
                <select name="status" id="bankSoalStatus" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="aktif" {{ ($status ?? '') === 'aktif' ? 'selected' : '' }}>Status: Aktif</option>
                    <option value="nonaktif" {{ ($status ?? '') === 'nonaktif' ? 'selected' : '' }}>Status: Nonaktif / Selesai</option>
                </select>
            </div>

            <!-- Sort By -->
            <div class="col-md-3">
                <select name="sort" id="bankSoalSort" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="latest" {{ ($sort ?? '') === 'latest' ? 'selected' : '' }}>Urutkan: Terbaru</option>
                    <option value="oldest" {{ ($sort ?? '') === 'oldest' ? 'selected' : '' }}>Urutkan: Terlama</option>
                    <option value="name_asc" {{ ($sort ?? '') === 'name_asc' ? 'selected' : '' }}>Nama: A - Z</option>
                    <option value="name_desc" {{ ($sort ?? '') === 'name_desc' ? 'selected' : '' }}>Nama: Z - A</option>
                </select>
            </div>

            <!-- Actions (Filter & Reset) -->
            <div class="col-md-1 d-flex gap-1">
                <button type="submit" class="btn btn-sm btn-primary w-100" title="Terapkan Filter">
                    Filter
                </button>
                @if(!empty($search) || !empty($status) || (($sort ?? 'latest') !== 'latest'))
                    <a href="{{ route('mooc.bank-soal') }}" class="btn btn-sm btn-outline-secondary" title="Reset Pencarian &amp; Filter">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <div class="row row-cols-1 row-cols-md-3 g-3" id="bankSoalGrid">
        @forelse($pelatihans as $p)
        <div class="col bank-soal-card-col">
            <div class="card border rounded-3 p-3 h-100 shadow-sm">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="badge bg-light text-primary border">Paket Soal</span>
                    @if(strtolower($p->status) === 'nonaktif')
                        <span class="badge bg-danger-subtle text-danger">Nonaktif</span>
                    @else
                        <span class="badge bg-success-subtle text-success">Aktif</span>
                    @endif
                </div>
                <h6 class="fw-bold text-dark mb-1">{{ $p->nama_pelatihan }}</h6>
                <p class="text-muted small mb-3">
                    {{ $p->keterangan ?: 'Tersedia bank soal Pre-Test, Kuis 1-4, dan Post-Test.' }}
                </p>
                <div class="d-flex justify-content-between align-items-center pt-2 border-top mt-auto">
                    <span class="text-muted small"><i class="bi bi-question-circle me-1"></i> 25 Butir Soal</span>
                    <div class="d-flex gap-1 align-items-center">
                        <!-- Tombol Edit Modal Nyata -->
                        <button type="button" 
                                class="btn btn-sm d-inline-flex align-items-center justify-content-center" 
                                style="width: 32px; height: 32px; background-color: #E0F2FE; color: #0284C7; border: none; border-radius: 6px;" 
                                title="Edit Paket Soal" 
                                data-bs-toggle="modal" 
                                data-bs-target="#editBankSoalModal-{{ $p->id }}">
                            <i class="bi bi-pencil-square"></i>
                        </button>

                        <!-- Tombol Hapus Nyata dengan Dialog Konfirmasi SweetAlert2 -->
                        <button type="button" 
                                class="btn btn-sm d-inline-flex align-items-center justify-content-center" 
                                style="width: 32px; height: 32px; background-color: #FEE2E2; color: #DC2626; border: none; border-radius: 6px;" 
                                title="Hapus Paket Soal" 
                                onclick="confirmDelete('delete-bank-soal-{{ $p->id }}', 'Apakah Anda yakin ingin menghapus paket soal {{ addslashes($p->nama_pelatihan) }}?')">
                            <i class="bi bi-trash3"></i>
                        </button>
                        <form id="delete-bank-soal-{{ $p->id }}" action="{{ route('mooc.bank-soal.destroy', $p->id) }}" method="POST" class="d-none">
                            @csrf
                            @method('DELETE')
                        </form>

                        <!-- Tombol Kelola Butir Soal -->
                        <button type="button" 
                                class="btn btn-sm btn-outline-primary px-2 d-inline-flex align-items-center gap-1" 
                                data-bs-toggle="modal" 
                                data-bs-target="#manageBankSoalModal-{{ $p->id }}">
                            <i class="bi bi-gear"></i> Kelola
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Edit Paket Soal Nyata untuk ID {{ $p->id }} -->
        <div class="modal fade" id="editBankSoalModal-{{ $p->id }}" tabindex="-1" aria-labelledby="editModalLabel-{{ $p->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <form action="{{ route('mooc.bank-soal.update', $p->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header border-bottom px-4 py-3" style="background-color: #0B1727; color: #ffffff;">
                            <h6 class="modal-title fw-bold text-white mb-0" id="editModalLabel-{{ $p->id }}">
                                <i class="bi bi-pencil-square me-2 text-warning"></i>Edit Paket Soal LMS MOOC
                            </h6>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="mb-3">
                                <label for="nama_pelatihan_{{ $p->id }}" class="form-label small fw-bold text-secondary">
                                    NAMA PELATIHAN / PAKET SOAL <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control rounded-3" 
                                       id="nama_pelatihan_{{ $p->id }}" 
                                       name="nama_pelatihan" 
                                       value="{{ $p->nama_pelatihan }}" 
                                       required>
                            </div>

                            <div class="row g-2 mb-3">
                                <div class="col-sm-6">
                                    <label for="kategori_{{ $p->id }}" class="form-label small fw-bold text-secondary">KATEGORI BIDANG</label>
                                    <input type="text" 
                                           class="form-control rounded-3" 
                                           id="kategori_{{ $p->id }}" 
                                           name="kategori" 
                                           value="{{ $p->kategori ?? 'MOOC Diklat' }}">
                                </div>
                                <div class="col-sm-6">
                                    <label for="status_{{ $p->id }}" class="form-label small fw-bold text-secondary">STATUS PAKET</label>
                                    <select class="form-select rounded-3" id="status_{{ $p->id }}" name="status">
                                        <option value="Aktif" {{ strtolower($p->status) !== 'nonaktif' ? 'selected' : '' }}>Aktif</option>
                                        <option value="Nonaktif" {{ strtolower($p->status) === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-2">
                                <label for="keterangan_{{ $p->id }}" class="form-label small fw-bold text-secondary">DESKRIPSI &amp; RINCIAN PAKET</label>
                                <textarea class="form-control rounded-3" 
                                          id="keterangan_{{ $p->id }}" 
                                          name="keterangan" 
                                          rows="3" 
                                          placeholder="Contoh: Tersedia bank soal Pre-Test, Kuis 1-4, dan Post-Test.">{{ $p->keterangan }}</textarea>
                            </div>
                        </div>
                        <div class="modal-footer bg-light px-4 py-3 border-top d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-light border px-3 rounded-3" data-bs-dismiss="modal">
                                <i class="bi bi-x-circle me-1"></i> Batal
                            </button>
                            <button type="submit" class="btn btn-primary px-3 rounded-3" style="background-color: #183B65; border-color: #183B65;">
                                <i class="bi bi-check2-circle me-1"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Kelola Butir Soal untuk ID {{ $p->id }} -->
        <div class="modal fade" id="manageBankSoalModal-{{ $p->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <div class="modal-header border-bottom px-4 py-3" style="background-color: #0B1727; color: #ffffff;">
                        <div>
                            <h6 class="modal-title fw-bold text-white mb-0">
                                <i class="bi bi-journal-text me-2 text-warning"></i>Kelola Butir Soal: {{ $p->nama_pelatihan }}
                            </h6>
                            <small class="text-white-50" style="font-size: 0.75rem;">Total 25 butir soal terdaftar aktif (Pre-Test, Kuis, &amp; Post-Test)</small>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    @php
                        $allItems = $p->getBankSoalItems();
                        $tabs = [
                            'pretest' => [
                                'label' => 'Pre-Test',
                                'icon' => 'bi-flag',
                                'desc' => 'Daftar pertanyaan diagnostik awal sebelum materi dimulai.',
                                'badge_color' => 'bg-primary',
                                'items' => array_values(array_filter($allItems, fn($i) => ($i['tipe'] ?? '') === 'pretest')),
                            ],
                            'kuis' => [
                                'label' => 'Kuis Modul',
                                'icon' => 'bi-lightning-charge',
                                'desc' => 'Kuis formatif dirancang untuk menguji pemahaman peserta di setiap sesi materi.',
                                'badge_color' => 'bg-warning text-dark',
                                'items' => array_values(array_filter($allItems, fn($i) => ($i['tipe'] ?? '') === 'kuis')),
                            ],
                            'posttest' => [
                                'label' => 'Post-Test',
                                'icon' => 'bi-award',
                                'desc' => 'Soal evaluasi akhir (Post-Test) untuk menentukan kelulusan diklat.',
                                'badge_color' => 'bg-danger',
                                'items' => array_values(array_filter($allItems, fn($i) => ($i['tipe'] ?? '') === 'posttest')),
                            ],
                        ];
                    @endphp

                    <div class="modal-body p-4">
                        <!-- Navigation Tabs -->
                        <ul class="nav nav-pills mb-3 border-bottom pb-2" role="tablist">
                            @foreach($tabs as $tabKey => $tabData)
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link {{ $loop->first ? 'active' : '' }} rounded-pill px-3 py-1.5 small fw-semibold" 
                                            id="{{ $tabKey }}-tab-{{ $p->id }}" 
                                            data-bs-toggle="pill" 
                                            data-bs-target="#tab-pane-{{ $tabKey }}-{{ $p->id }}" 
                                            type="button" 
                                            role="tab">
                                        <i class="bi {{ $tabData['icon'] }} me-1"></i>{{ $tabData['label'] }} ({{ count($tabData['items']) }} Soal)
                                    </button>
                                </li>
                            @endforeach
                        </ul>

                        <div class="tab-content">
                            @foreach($tabs as $tabKey => $tabData)
                            <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" 
                                 id="tab-pane-{{ $tabKey }}-{{ $p->id }}" 
                                 role="tabpanel">
                                
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="small text-muted">{{ $tabData['desc'] }}</span>
                                    <button type="button" 
                                            class="btn btn-outline-primary btn-sm rounded-pill px-2.5 d-inline-flex align-items-center gap-1"
                                            data-bs-toggle="collapse" 
                                            data-bs-target="#collapseAddSoal-{{ $p->id }}-{{ $tabKey }}">
                                        <i class="bi bi-plus-circle"></i> Tambah Soal
                                    </button>
                                </div>

                                <!-- Form Tambah Butir Soal Baru (Collapsible) -->
                                <div class="collapse mb-3" id="collapseAddSoal-{{ $p->id }}-{{ $tabKey }}">
                                    <div class="card border border-primary-subtle rounded-3 p-3 bg-light-subtle shadow-sm">
                                        <form action="{{ route('mooc.bank-soal.butir.add', $p->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="tipe" value="{{ $tabKey }}">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <strong class="text-primary small">
                                                    <i class="bi bi-plus-circle-dotted me-1"></i>Formulir Tambah Butir Soal {{ $tabData['label'] }}
                                                </strong>
                                                <button type="button" class="btn-close btn-sm" data-bs-toggle="collapse" data-bs-target="#collapseAddSoal-{{ $p->id }}-{{ $tabKey }}"></button>
                                            </div>

                                            <div class="mb-2">
                                                <label class="form-label small fw-bold text-secondary mb-1">Pertanyaan / Butir Soal <span class="text-danger">*</span></label>
                                                <textarea name="pertanyaan" class="form-control form-control-sm rounded-2" rows="2" placeholder="Tuliskan pertanyaan soal pilihan ganda..." required></textarea>
                                            </div>

                                            <div class="row g-2 mb-2">
                                                <div class="col-md-6">
                                                    <label class="form-label small text-secondary mb-1">Pilihan A <span class="text-danger">*</span></label>
                                                    <input type="text" name="pilihan_a" class="form-control form-control-sm" placeholder="Opsi A" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small text-secondary mb-1">Pilihan B <span class="text-danger">*</span></label>
                                                    <input type="text" name="pilihan_b" class="form-control form-control-sm" placeholder="Opsi B" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small text-secondary mb-1">Pilihan C <span class="text-danger">*</span></label>
                                                    <input type="text" name="pilihan_c" class="form-control form-control-sm" placeholder="Opsi C" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small text-secondary mb-1">Pilihan D <span class="text-danger">*</span></label>
                                                    <input type="text" name="pilihan_d" class="form-control form-control-sm" placeholder="Opsi D" required>
                                                </div>
                                            </div>

                                            <div class="row g-2 mb-3">
                                                <div class="col-sm-6">
                                                    <label class="form-label small fw-bold text-secondary mb-1">Kunci Jawaban Benar <span class="text-danger">*</span></label>
                                                    <select name="kunci" class="form-select form-select-sm" required>
                                                        <option value="A">A</option>
                                                        <option value="B">B</option>
                                                        <option value="C">C</option>
                                                        <option value="D">D</option>
                                                    </select>
                                                </div>
                                                <div class="col-sm-6">
                                                    <label class="form-label small fw-bold text-secondary mb-1">Bobot Poin</label>
                                                    <input type="number" name="bobot" class="form-control form-control-sm" value="10" min="1" max="100">
                                                </div>
                                            </div>

                                            <div class="d-flex justify-content-end gap-2">
                                                <button type="button" class="btn btn-sm btn-light border" data-bs-toggle="collapse" data-bs-target="#collapseAddSoal-{{ $p->id }}-{{ $tabKey }}">Batal</button>
                                                <button type="submit" class="btn btn-sm btn-primary px-3">
                                                    <i class="bi bi-save me-1"></i> Simpan Soal Baru
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <!-- Daftar Kartu Soal -->
                                @forelse($tabData['items'] as $itemIndex => $item)
                                    <div class="card border rounded-3 p-3 mb-2 bg-light shadow-none">
                                        <!-- Header Soal (Display Mode) -->
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                                <span class="badge {{ $tabData['badge_color'] }} rounded-pill">
                                                    Soal #{{ $itemIndex + 1 }} (Bobot: {{ $item['bobot'] ?? 10 }} Poin)
                                                </span>
                                                <span class="badge bg-success-subtle text-success">
                                                    Kunci Jawaban: {{ $item['kunci'] ?? 'A' }}
                                                </span>
                                            </div>
                                            <!-- Tombol Aksi Edit & Hapus Soal -->
                                            <div class="d-flex align-items-center gap-1">
                                                <!-- Tombol Edit Soal Ini -->
                                                <button type="button" 
                                                        class="btn btn-sm text-primary p-0 px-1 border-0" 
                                                        title="Ubah / Edit Butir Soal Ini" 
                                                        data-bs-toggle="collapse" 
                                                        data-bs-target="#collapseEditSoal-{{ $p->id }}-{{ $item['id'] }}">
                                                    <i class="bi bi-pencil-square fs-6"></i>
                                                </button>

                                                <!-- Tombol Hapus Soal Ini -->
                                                <form action="{{ route('mooc.bank-soal.butir.delete', ['id' => $p->id, 'soalId' => $item['id']]) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus butir soal #{{ $itemIndex + 1 }} ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm text-danger p-0 px-1 border-0" title="Hapus Soal Ini">
                                                        <i class="bi bi-trash3 fs-6"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>

                                        <!-- Teks Pertanyaan & Pilihan (Display Mode) -->
                                        <p class="small fw-semibold text-dark mb-2">
                                            {{ $item['pertanyaan'] }}
                                        </p>
                                        <div class="small text-secondary ps-2 mb-2">
                                            @foreach(['A', 'B', 'C', 'D'] as $opt)
                                                @php $isCorrect = (strtoupper($item['kunci'] ?? '') === $opt); @endphp
                                                <div class="{{ $isCorrect ? 'fw-bold text-success' : '' }}">
                                                    @if($isCorrect)
                                                        <i class="bi bi-check-circle-fill me-1"></i>
                                                    @endif
                                                    {{ $opt }}. {{ $item['pilihan'][$opt] ?? '-' }}
                                                </div>
                                            @endforeach
                                        </div>

                                        <!-- Formulir Edit Soal Ini (Collapsible) -->
                                        <div class="collapse mt-3 pt-3 border-top" id="collapseEditSoal-{{ $p->id }}-{{ $item['id'] }}">
                                            <form action="{{ route('mooc.bank-soal.butir.update', ['id' => $p->id, 'soalId' => $item['id']]) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="tipe" value="{{ $item['tipe'] ?? $tabKey }}">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <strong class="text-dark small">
                                                        <i class="bi bi-pencil me-1 text-primary"></i>Ubah Butir Soal #{{ $itemIndex + 1 }}
                                                    </strong>
                                                    <button type="button" class="btn-close btn-sm" data-bs-toggle="collapse" data-bs-target="#collapseEditSoal-{{ $p->id }}-{{ $item['id'] }}"></button>
                                                </div>

                                                <div class="mb-2">
                                                    <label class="form-label small fw-bold text-secondary mb-1">Pertanyaan Soal</label>
                                                    <textarea name="pertanyaan" class="form-control form-control-sm rounded-2" rows="2" required>{{ $item['pertanyaan'] }}</textarea>
                                                </div>

                                                <div class="row g-2 mb-2">
                                                    <div class="col-md-6">
                                                        <label class="form-label small text-secondary mb-1">Pilihan A</label>
                                                        <input type="text" name="pilihan_a" class="form-control form-control-sm" value="{{ $item['pilihan']['A'] ?? '' }}" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label small text-secondary mb-1">Pilihan B</label>
                                                        <input type="text" name="pilihan_b" class="form-control form-control-sm" value="{{ $item['pilihan']['B'] ?? '' }}" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label small text-secondary mb-1">Pilihan C</label>
                                                        <input type="text" name="pilihan_c" class="form-control form-control-sm" value="{{ $item['pilihan']['C'] ?? '' }}" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label small text-secondary mb-1">Pilihan D</label>
                                                        <input type="text" name="pilihan_d" class="form-control form-control-sm" value="{{ $item['pilihan']['D'] ?? '' }}" required>
                                                    </div>
                                                </div>

                                                <div class="row g-2 mb-3">
                                                    <div class="col-sm-6">
                                                        <label class="form-label small fw-bold text-secondary mb-1">Kunci Jawaban Benar</label>
                                                        <select name="kunci" class="form-select form-select-sm" required>
                                                            <option value="A" {{ strtoupper($item['kunci'] ?? '') === 'A' ? 'selected' : '' }}>A</option>
                                                            <option value="B" {{ strtoupper($item['kunci'] ?? '') === 'B' ? 'selected' : '' }}>B</option>
                                                            <option value="C" {{ strtoupper($item['kunci'] ?? '') === 'C' ? 'selected' : '' }}>C</option>
                                                            <option value="D" {{ strtoupper($item['kunci'] ?? '') === 'D' ? 'selected' : '' }}>D</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <label class="form-label small fw-bold text-secondary mb-1">Bobot Poin</label>
                                                        <input type="number" name="bobot" class="form-control form-control-sm" value="{{ $item['bobot'] ?? 10 }}" min="1" max="100">
                                                    </div>
                                                </div>

                                                <div class="d-flex justify-content-end gap-2">
                                                    <button type="button" class="btn btn-sm btn-light border" data-bs-toggle="collapse" data-bs-target="#collapseEditSoal-{{ $p->id }}-{{ $item['id'] }}">Batal</button>
                                                    <button type="submit" class="btn btn-sm btn-primary px-3">
                                                        <i class="bi bi-check2-circle me-1"></i> Simpan Perubahan Soal
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-4 text-muted bg-light rounded-3">
                                        <i class="bi bi-journal-x fs-3 d-block mb-1 text-secondary opacity-50"></i>
                                        <span class="small">Belum ada butir soal {{ $tabData['label'] }}.</span>
                                        <div class="mt-2">
                                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" data-bs-toggle="collapse" data-bs-target="#collapseAddSoal-{{ $p->id }}-{{ $tabKey }}">
                                                <i class="bi bi-plus-circle me-1"></i> Tambah Soal Pertama
                                            </button>
                                        </div>
                                    </div>
                                @endforelse
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="modal-footer bg-light px-4 py-3 border-top d-flex justify-content-between">
                        <span class="text-muted small">Status sinkronisasi bank soal: <strong class="text-success">Siap Diujikan</strong></span>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-light border px-3 rounded-3" data-bs-dismiss="modal">Tutup</button>
                            <form action="{{ route('mooc.bank-soal.butir.save', $p->id) }}" method="POST" class="m-0">
                                @csrf
                                <button type="submit" class="btn btn-primary px-3 rounded-3" style="background-color: #183B65; border-color: #183B65;">
                                    <i class="bi bi-save me-1"></i> Simpan Butir Soal
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5 text-muted">
            <i class="bi bi-folder-x fs-1 d-block mb-2 text-secondary opacity-50"></i>
            <div class="fw-semibold">Belum Ada Paket Bank Soal</div>
            <p class="small mb-3">Klik tombol "Tambah Bank Soal" di atas untuk membuat paket soal baru atau reset kata kunci pencarian Anda.</p>
        </div>
        @endforelse
    </div>

    @if($pelatihans->hasPages())
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 mt-4 pt-3 border-top">
        <span class="text-muted small">
            Menampilkan {{ $pelatihans->firstItem() }} - {{ $pelatihans->lastItem() }} dari total {{ $pelatihans->total() }} paket bank soal
        </span>
        <div>
            {{ $pelatihans->links() }}
        </div>
    </div>
    @endif
</div>

<!-- Modal Tambah Paket Bank Soal Baru -->
<div class="modal fade" id="createBankSoalModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form action="{{ route('mooc.bank-soal.store') }}" method="POST">
                @csrf
                <div class="modal-header border-bottom px-4 py-3" style="background-color: #0B1727; color: #ffffff;">
                    <h6 class="modal-title fw-bold text-white mb-0" id="createModalLabel">
                        <i class="bi bi-plus-circle me-2 text-warning"></i>Tambah Paket Bank Soal Baru
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="new_nama_pelatihan" class="form-label small fw-bold text-secondary">
                            NAMA PELATIHAN / PAKET SOAL <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control rounded-3" 
                               id="new_nama_pelatihan" 
                               name="nama_pelatihan" 
                               placeholder="Contoh: Pelatihan Investigasi Kecelakaan Kerja K3" 
                               required>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-sm-6">
                            <label for="new_kategori" class="form-label small fw-bold text-secondary">KATEGORI BIDANG</label>
                            <input type="text" 
                                   class="form-control rounded-3" 
                                   id="new_kategori" 
                                   name="kategori" 
                                   value="MOOC Diklat" 
                                   placeholder="Contoh: K3 / Teknis">
                        </div>
                        <div class="col-sm-6">
                            <label for="new_status" class="form-label small fw-bold text-secondary">STATUS PAKET</label>
                            <select class="form-select rounded-3" id="new_status" name="status">
                                <option value="Aktif" selected>Aktif</option>
                                <option value="Nonaktif">Nonaktif</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label for="new_keterangan" class="form-label small fw-bold text-secondary">DESKRIPSI &amp; RINCIAN PAKET</label>
                        <textarea class="form-control rounded-3" 
                                  id="new_keterangan" 
                                  name="keterangan" 
                                  rows="3" 
                                  placeholder="Contoh: Tersedia bank soal Pre-Test, Kuis 1-4, dan Post-Test.">Tersedia bank soal Pre-Test, Kuis 1-4, dan Post-Test.</textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light px-4 py-3 border-top d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-light border px-3 rounded-3" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary px-3 rounded-3" style="background-color: #183B65; border-color: #183B65;">
                        <i class="bi bi-check2-circle me-1"></i> Simpan Paket Soal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('bankSoalSearch');
    if (!searchInput) return;

    let debounceTimer;
    searchInput.addEventListener('input', function () {
        const query = this.value.toLowerCase().trim();
        const cards = document.querySelectorAll('.bank-soal-card-col');

        cards.forEach(card => {
            const text = card.textContent.toLowerCase();
            if (query === '' || text.includes(query)) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });

        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            if (searchInput.form) {
                searchInput.form.submit();
            }
        }, 750);
    });
});
</script>
@endpush
@endsection
