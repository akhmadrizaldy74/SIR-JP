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

    <div class="row row-cols-1 row-cols-md-3 g-3">
        @forelse($pelatihans as $p)
        <div class="col">
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
                    <div class="modal-body p-4">
                        <!-- Navigation Tabs -->
                        <ul class="nav nav-pills mb-3 border-bottom pb-2" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active rounded-pill px-3 py-1.5 small fw-semibold" id="pretest-tab-{{ $p->id }}" data-bs-toggle="pill" data-bs-target="#pretest-{{ $p->id }}" type="button" role="tab">
                                    <i class="bi bi-flag me-1"></i>Pre-Test (10 Soal)
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link rounded-pill px-3 py-1.5 small fw-semibold" id="kuis-tab-{{ $p->id }}" data-bs-toggle="pill" data-bs-target="#kuis-{{ $p->id }}" type="button" role="tab">
                                    <i class="bi bi-lightning-charge me-1"></i>Kuis Modul (10 Soal)
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link rounded-pill px-3 py-1.5 small fw-semibold" id="posttest-tab-{{ $p->id }}" data-bs-toggle="pill" data-bs-target="#posttest-{{ $p->id }}" type="button" role="tab">
                                    <i class="bi bi-award me-1"></i>Post-Test (5 Soal)
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <!-- Tab Pre-Test -->
                            <div class="tab-pane fade show active" id="pretest-{{ $p->id }}" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="small text-muted">Daftar pertanyaan diagnostik awal sebelum materi dimulai.</span>
                                    <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-2.5" onclick="Swal.fire({title: 'Tambah Soal Baru', text: 'Formulir input butir soal baru pilihan ganda siap ditambahkan.', icon: 'info'})">
                                        <i class="bi bi-plus-circle me-1"></i> Tambah Soal
                                    </button>
                                </div>

                                <!-- Contoh Soal 1 -->
                                <div class="card border rounded-3 p-3 mb-2 bg-light shadow-none">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <span class="badge bg-primary rounded-pill">Soal #1 (Bobot: 10 Poin)</span>
                                        <span class="badge bg-success-subtle text-success">Kunci Jawaban: B</span>
                                    </div>
                                    <p class="small fw-semibold text-dark mb-2">
                                        Apa prinsip utama dalam pelaksanaan tata kelola kepemimpinan dan pelayanan ketenagakerjaan berbasis Corporate University?
                                    </p>
                                    <div class="small text-secondary ps-2">
                                        <div>A. Sentralisasi seluruh keputusan teknis tanpa delegasi</div>
                                        <div class="fw-bold text-success"><i class="bi bi-check-circle-fill me-1"></i>B. Integrasi pembelajaran terpadu selaras dengan tujuan strategis organisasi</div>
                                        <div>C. Mengurangi jam pelatihan pegawai aparatur sipil negara</div>
                                        <div>D. Pembatasan akses informasi antar unit kerja</div>
                                    </div>
                                </div>

                                <!-- Contoh Soal 2 -->
                                <div class="card border rounded-3 p-3 mb-2 bg-light shadow-none">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <span class="badge bg-primary rounded-pill">Soal #2 (Bobot: 10 Poin)</span>
                                        <span class="badge bg-success-subtle text-success">Kunci Jawaban: C</span>
                                    </div>
                                    <p class="small fw-semibold text-dark mb-2">
                                        Dalam sistem evaluasi pembelajaran Kirkpatrick, level berapakah yang mengukur perubahan perilaku kerja nyata peserta di tempat tugas?
                                    </p>
                                    <div class="small text-secondary ps-2">
                                        <div>A. Level 1 - Reaction</div>
                                        <div>B. Level 2 - Learning</div>
                                        <div class="fw-bold text-success"><i class="bi bi-check-circle-fill me-1"></i>C. Level 3 - Behavior</div>
                                        <div>D. Level 4 - Results</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab Kuis Modul -->
                            <div class="tab-pane fade" id="kuis-{{ $p->id }}" role="tabpanel">
                                <div class="alert alert-info py-2 small mb-3">
                                    <i class="bi bi-info-circle me-1"></i> Kuis formatif dirancang untuk menguji pemahaman peserta di setiap akhir sesi bab pembelajaran.
                                </div>
                                <div class="card border rounded-3 p-3 mb-2 bg-light shadow-none">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <span class="badge bg-warning text-dark rounded-pill">Kuis Modul #1 (Bobot: 10 Poin)</span>
                                        <span class="badge bg-success-subtle text-success">Kunci: A</span>
                                    </div>
                                    <p class="small fw-semibold text-dark mb-2">
                                        Landasan hukum pengawasan ketenagakerjaan di Indonesia termuat dalam regulasi:
                                    </p>
                                    <div class="small text-secondary ps-2">
                                        <div class="fw-bold text-success"><i class="bi bi-check-circle-fill me-1"></i>A. Undang-Undang Nomor 3 Tahun 1951</div>
                                        <div>B. Undang-Undang Nomor 1 Tahun 1970</div>
                                        <div>C. Peraturan Pemerintah Nomor 35 Tahun 2021</div>
                                        <div>D. Permenaker Nomor 5 Tahun 2021</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab Post-Test -->
                            <div class="tab-pane fade" id="posttest-{{ $p->id }}" role="tabpanel">
                                <div class="alert alert-success py-2 small mb-3">
                                    <i class="bi bi-check2-circle me-1"></i> Soal evaluasi akhir (Post-Test) untuk menentukan kelulusan dan penerbitan sertifikat digital.
                                </div>
                                <div class="card border rounded-3 p-3 mb-2 bg-light shadow-none">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <span class="badge bg-danger rounded-pill">Post-Test #1 (Bobot: 20 Poin)</span>
                                        <span class="badge bg-success-subtle text-success">Kunci: D</span>
                                    </div>
                                    <p class="small fw-semibold text-dark mb-2">
                                        Indikator keberhasilan dari penyelenggaraan diklat aparatur yang berdampak tinggi adalah:
                                    </p>
                                    <div class="small text-secondary ps-2">
                                        <div>A. Tingkat kehadiran peserta 100% tanpa evaluasi lanjut</div>
                                        <div>B. Banyaknya materi yang disampaikan dalam slide presentasi</div>
                                        <div>C. Jumlah anggaran yang terserap habis</div>
                                        <div class="fw-bold text-success"><i class="bi bi-check-circle-fill me-1"></i>D. Peningkatan kinerja pelayanan publik dan capaian target strategis instansi</div>
                                    </div>
                                </div>
                            </div>
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
            <p class="small mb-3">Klik tombol "Tambah Bank Soal" di atas untuk membuat paket soal baru.</p>
        </div>
        @endforelse
    </div>
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
@endsection
