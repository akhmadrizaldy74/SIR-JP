@extends('layouts.app')

@section('title', $isEdit ? 'Edit Pengguna & Hak Akses Peran' : 'Tambah User Baru')

@section('styles')
<style>
    .kclc-edit-form-card {
        background-color: #FFFFFF;
        border-radius: 12px;
        border: 1px solid #E2E8F0;
        box-shadow: 0 2px 8px rgba(11, 23, 39, 0.06);
        padding: 2rem 2.5rem;
    }

    .form-label-side {
        font-weight: 700;
        color: #0F172A;
        font-size: 0.925rem;
        text-align: right;
        padding-top: 6px;
    }

    @media (max-width: 767.98px) {
        .form-label-side {
            text-align: left;
            margin-bottom: 0.35rem;
        }
    }

    .form-check-input {
        width: 18px;
        height: 18px;
        margin-top: 0.15rem;
        border: 1.5px solid #94A3B8;
        border-radius: 4px;
        cursor: pointer;
    }

    .form-check-input:checked {
        background-color: #0B1727;
        border-color: #0B1727;
    }

    .form-check-bidang .form-check-input:checked {
        background-color: #10B981 !important;
        border-color: #10B981 !important;
    }

    .form-check-label {
        font-size: 0.885rem;
        color: #1E293B;
        cursor: pointer;
        user-select: none;
    }

    .role-box-card {
        background-color: #F8FAFC;
        border: 1px solid #E2E8F0;
        border-radius: 8px;
        padding: 1rem;
        transition: all 0.2s;
    }

    .role-box-card:hover {
        border-color: #CBD5E1;
        background-color: #F1F5F9;
    }
</style>
@endsection

@section('content')
<!-- Top Header: Judul Kiri & Tombol Navigasi Kembali -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
            <h5 class="fw-bold text-dark mb-0" style="font-size: 1.2rem; letter-spacing: 0.2px;">
                <i class="bi bi-person-gear text-primary me-2"></i>{{ $isEdit ? 'Edit Users' : 'Tambah User' }}
                <span class="text-muted fw-normal fs-6 ms-1">(Atur Peran &amp; Hak Akses)</span>
            </h5>
            @if($isEdit && $user->isGuest())
                <span class="badge rounded-pill px-3 py-1 fw-semibold" style="background-color: #FEF3C7; color: #B45309; border: 1px solid #FDE68A; font-size: 0.75rem;">
                    <i class="bi bi-clock-history me-1"></i>Status: Guest (Menunggu Penugasan Role)
                </span>
            @elseif($isEdit)
                <span class="badge rounded-pill px-3 py-1 fw-semibold" style="background-color: #EFF6FF; color: #1E40AF; border: 1px solid #BFDBFE; font-size: 0.75rem;">
                    Mode: {{ $user->getActiveRole() }}
                </span>
            @endif
        </div>
        <p class="text-muted small mb-0">
            Tetapkan peran (role), modul hak akses, serta penempatan bidang tugas untuk akun ini.
        </p>
    </div>

    <!-- Tombol KEMBALI di Kanan Atas dengan Desain Jelas -->
    <div>
        <a href="{{ route('users.index') }}" 
           class="btn btn-outline-secondary px-3 py-2 fw-semibold d-inline-flex align-items-center gap-2 shadow-sm rounded-3"
           title="Kembali ke halaman Daftar Pengguna tanpa menyimpan">
            <i class="bi bi-arrow-left fs-6"></i>
            <span>Kembali ke Daftar Pengguna</span>
        </a>
    </div>
</div>

<!-- Main Form Card -->
<div class="kclc-edit-form-card mb-5">
    <form action="{{ $isEdit ? route('users.update', $user->id) : route('users.store') }}" method="POST">
        @csrf
        @if($isEdit)
            @method('PUT')
        @endif

        <!-- 1. Row: Roles (Pilihan Peran Pengguna) -->
        <div class="row mb-4 align-items-start">
            <label class="col-md-2 form-label-side">
                Roles / Hak Akses <span class="text-danger">*</span>
            </label>
            <div class="col-md-10">
                <div class="p-3 rounded-3 mb-2" style="background-color: #F8FAFC; border: 1px dashed #CBD5E1;">
                    <div class="small text-muted mb-2">
                        <i class="bi bi-info-circle text-primary me-1"></i>
                        Centang peran yang ingin diberikan kepada pengguna. Pengguna dapat memiliki lebih dari 1 peran sekaligus:
                    </div>
                    <div class="row g-3">
                        @foreach($availableRoles as $colIndex => $roleGroup)
                            <div class="col-md-4">
                                <div class="p-2.5 rounded-2 bg-white border h-100">
                                    @foreach($roleGroup as $rName)
                                        @php
                                            $isChecked = false;
                                            if (is_array(old('roles', $user->roles))) {
                                                $isChecked = in_array($rName, old('roles', $user->roles ?? []))
                                                    || ($rName === 'Peserta' && in_array('Peserta Diklat', old('roles', $user->roles ?? [])));
                                            } elseif ($isEdit) {
                                                if ($user->role === 'peserta' && $rName === 'Peserta') {
                                                    $isChecked = true;
                                                } elseif (in_array($user->role, ['penyelenggara', 'admin']) && in_array($rName, ['Administrator', 'Penyelenggara', 'Evaluasi', 'Kontributor', 'Pengajar', 'Penguji', 'Kurikulum'])) {
                                                    $isChecked = true;
                                                } elseif ($user->role === 'widyaiswara' && in_array($rName, ['Pengajar'])) {
                                                    $isChecked = true;
                                                } elseif ($user->role === 'guest' && $rName === 'Guest') {
                                                    $isChecked = true;
                                                }
                                            }
                                        @endphp
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   name="roles[]" 
                                                   value="{{ $rName }}" 
                                                   id="role_{{ Str::slug($rName) }}"
                                                   {{ $isChecked ? 'checked' : '' }}>
                                            <label class="form-check-label fw-semibold" for="role_{{ Str::slug($rName) }}">
                                                {{ $rName }}
                                                @if($rName === 'Guest')
                                                    <span class="text-muted fw-normal small">(Tamu)</span>
                                                @endif
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Row: Nama -->
        <div class="row mb-4 align-items-center">
            <label class="col-md-2 form-label-side">
                Nama Lengkap <span class="text-danger">*</span>
            </label>
            <div class="col-md-10">
                <input type="text" 
                       name="name" 
                       class="form-control rounded-2 py-2 px-3 @error('name') is-invalid @enderror" 
                       value="{{ old('name', $user->name ?? 'PPSDM Ketenagakerjaan') }}" 
                       required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- 3. Row: Bidang (Penempatan Pelatihan) -->
        <div class="row mb-4 align-items-start">
            <label class="col-md-2 form-label-side">Bidang Tugas</label>
            <div class="col-md-10">
                <div class="p-3 rounded-3" style="background-color: #F8FAFC; border: 1px solid #E2E8F0;">
                    <div class="row g-2">
                        @foreach($availableBidang as $colIndex => $bidangGroup)
                            <div class="col-md-4">
                                @foreach($bidangGroup as $bName)
                                    @php
                                        $isBidangChecked = false;
                                        if (is_array(old('bidang', $user->bidang))) {
                                            $isBidangChecked = in_array($bName, old('bidang', $user->bidang ?? []));
                                        } elseif ($isEdit) {
                                            $isBidangChecked = is_array($user->bidang) ? in_array($bName, $user->bidang) : true;
                                        }
                                    @endphp
                                    <div class="form-check form-check-bidang mb-2">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               name="bidang[]" 
                                               value="{{ $bName }}" 
                                               id="bidang_{{ Str::slug($bName) }}"
                                               {{ $isBidangChecked ? 'checked' : '' }}>
                                        <label class="form-check-label text-dark" for="bidang_{{ Str::slug($bName) }}">
                                            {{ $bName }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Row: Email -->
        <div class="row mb-4 align-items-center">
            <label class="col-md-2 form-label-side">
                Alamat Email <span class="text-danger">*</span>
            </label>
            <div class="col-md-10">
                <input type="email" 
                       name="email" 
                       class="form-control rounded-2 py-2 px-3 @error('email') is-invalid @enderror" 
                       value="{{ old('email', $user->email ?? 'admin@gmail.com') }}" 
                       required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- 5. Row: Username -->
        <div class="row mb-4 align-items-center">
            <label class="col-md-2 form-label-side">Username</label>
            <div class="col-md-10">
                <input type="text" 
                       name="username" 
                       class="form-control rounded-2 py-2 px-3 @error('username') is-invalid @enderror" 
                       value="{{ old('username', $user->username ?? 'admin') }}">
                @error('username')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- 6. Row: Password -->
        <div class="row mb-4 align-items-center">
            <label class="col-md-2 form-label-side">Password</label>
            <div class="col-md-10">
                <input type="password" 
                       name="password" 
                       class="form-control rounded-2 py-2 px-3 @error('password') is-invalid @enderror" 
                       placeholder="Masukkan kata sandi baru jika ingin mengubah"
                       {{ $isEdit ? '' : 'required' }}>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                @if($isEdit)
                    <div class="text-muted small mt-1">
                        <i class="bi bi-info-circle me-1"></i>Biarkan kosong jika tidak ingin mengganti kata sandi pengguna ini.
                    </div>
                @endif
            </div>
        </div>

        <!-- Hidden NIK fallback jika ada atau generate -->
        @if($isEdit && $user->nik)
            <input type="hidden" name="nik" value="{{ $user->nik }}">
        @endif

        <!-- ============================================================== -->
        <!-- ACTION BAR: TOMBOL KEMBALI, BATAL, DAN UPDATE DENGAN WARNA JELAS -->
        <!-- ============================================================== -->
        <div class="border-top pt-4 mt-4">
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-stretch align-items-sm-center gap-3">
                <!-- Sisi Kiri: Tombol Navigasi Kembali -->
                <div>
                    <a href="{{ route('users.index') }}" 
                       class="btn btn-outline-secondary px-3 py-2 fw-semibold d-inline-flex align-items-center gap-2 rounded-3 w-100 w-sm-auto"
                       title="Kembali ke halaman utama Manajemen Pengguna">
                        <i class="bi bi-arrow-left"></i>
                        <span>Kembali ke Daftar</span>
                    </a>
                </div>

                <!-- Sisi Kanan: Tombol Batal & Tombol Utama Update/Simpan -->
                <div class="d-flex align-items-center justify-content-end gap-2 flex-wrap">
                    <!-- Tombol BATAL (Cancel) -->
                    <a href="{{ route('users.index') }}" 
                       class="btn btn-outline-danger px-3 py-2 fw-semibold d-inline-flex align-items-center gap-2 rounded-3 shadow-sm"
                       title="Batalkan perubahan formulir ini">
                        <i class="bi bi-x-circle"></i>
                        <span>Batal</span>
                    </a>

                    <!-- Tombol UTAMA: UPDATE / SIMPAN -->
                    <button type="submit" 
                            class="btn text-white px-4 py-2 fw-bold d-inline-flex align-items-center gap-2 rounded-3 shadow"
                            style="background-color: #0B1727; border: 1px solid #0B1727; min-height: 40px;"
                            title="Simpan hak akses peran dan data pengguna ini">
                        <i class="bi bi-check2-circle fs-5"></i>
                        <span>{{ $isEdit ? 'Update Data Pengguna' : 'Simpan Pengguna Baru' }}</span>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
