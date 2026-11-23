@extends('layouts.app')

@section('title', $isEdit ? 'Edit Users' : 'Tambah User')

@section('styles')
<style>
    /* Styling Presisi Sesuai Screenshot User */
    .kclc-edit-form-card {
        background-color: #FFFFFF;
        border-radius: 8px;
        border: 1px solid #E2E8F0;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        padding: 2.25rem 2.5rem;
    }

    .form-label-side {
        font-weight: 600;
        color: #1E293B;
        font-size: 0.95rem;
        text-align: right;
        padding-top: 6px;
    }

    @media (max-width: 767.98px) {
        .form-label-side {
            text-align: left;
            margin-bottom: 0.35rem;
        }
    }

    /* Checkbox Styling */
    .form-check-input {
        width: 17px;
        height: 17px;
        margin-top: 0.2rem;
        border: 1.5px solid #94A3B8;
        border-radius: 3px;
        cursor: pointer;
    }

    .form-check-input:checked {
        background-color: #0B1727;
        border-color: #0B1727;
    }

    /* Bidang Checkbox Green Accent Sesuai Gambar */
    .form-check-bidang .form-check-input:checked {
        background-color: #10B981 !important;
        border-color: #10B981 !important;
    }

    .form-check-label {
        font-size: 0.88rem;
        color: #1E293B;
        cursor: pointer;
        user-select: none;
    }

    .btn-navy-kclc {
        background-color: #0B1727;
        border-color: #0B1727;
        color: #FFFFFF;
        font-weight: 600;
        border-radius: 6px;
        transition: all 0.15s ease-in-out;
    }

    .btn-navy-kclc:hover {
        background-color: #1E293B;
        border-color: #1E293B;
        color: #FFFFFF;
    }

    .btn-orange-kclc {
        background-color: #FF5722;
        border-color: #FF5722;
        color: #FFFFFF;
        font-weight: 600;
        border-radius: 6px;
        transition: all 0.15s ease-in-out;
    }

    .btn-orange-kclc:hover {
        background-color: #F4511E;
        border-color: #F4511E;
        color: #FFFFFF;
    }
</style>
@endsection

@section('content')
<!-- Top Header: Judul Kiri & Tombol Kembali Kanan Sesuai Gambar -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold text-dark mb-0" style="font-size: 1.1rem; letter-spacing: 0.3px;">
        {{ $isEdit ? 'Edit Users' : 'Tambah User' }}
    </h5>
    <a href="{{ route('users.index') }}" class="btn btn-navy-kclc btn-sm px-4 py-2">
        Kembali
    </a>
</div>

<!-- Main Form Card -->
<div class="kclc-edit-form-card mb-5">
    <form action="{{ $isEdit ? route('users.update', $user->id) : route('users.store') }}" method="POST">
        @csrf
        @if($isEdit)
            @method('PUT')
        @endif

        <!-- 1. Row: Roles (3 Kolom Checkbox Sesuai Gambar) -->
        <div class="row mb-4 align-items-start">
            <label class="col-md-2 form-label-side">Roles</label>
            <div class="col-md-10">
                <div class="row g-2">
                    @foreach($availableRoles as $colIndex => $roleGroup)
                        <div class="col-md-4">
                            @foreach($roleGroup as $rName)
                                @php
                                    $isChecked = false;
                                    if (is_array(old('roles', $user->roles))) {
                                        $isChecked = in_array($rName, old('roles', $user->roles ?? []))
                                            || ($rName === 'Peserta' && in_array('Peserta Diklat', old('roles', $user->roles ?? [])));
                                    } elseif ($isEdit) {
                                        // Default fallback jika belum pernah set roles array
                                        if ($user->role === 'peserta' && $rName === 'Peserta') {
                                            $isChecked = true;
                                        } elseif (in_array($user->role, ['penyelenggara', 'admin']) && in_array($rName, ['Administrator', 'Penyelenggara', 'Evaluasi', 'Kontributor', 'Pengajar', 'Penguji', 'Kurikulum'])) {
                                            $isChecked = true;
                                        } elseif ($user->role === 'widyaiswara' && in_array($rName, ['Pengajar'])) {
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
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- 2. Row: Nama -->
        <div class="row mb-4 align-items-center">
            <label class="col-md-2 form-label-side">Nama</label>
            <div class="col-md-10">
                <input type="text" 
                       name="name" 
                       class="form-control rounded-2 py-2 @error('name') is-invalid @enderror" 
                       value="{{ old('name', $user->name ?? 'PPSDM Ketenagakerjaan') }}" 
                       required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- 3. Row: Bidang (3 Kolom Checkbox Sesuai Gambar) -->
        <div class="row mb-4 align-items-start">
            <label class="col-md-2 form-label-side">Bidang</label>
            <div class="col-md-10">
                <div class="row g-2">
                    @foreach($availableBidang as $colIndex => $bidangGroup)
                        <div class="col-md-4">
                            @foreach($bidangGroup as $bName)
                                @php
                                    $isBidangChecked = false;
                                    if (is_array(old('bidang', $user->bidang))) {
                                        $isBidangChecked = in_array($bName, old('bidang', $user->bidang ?? []));
                                    } elseif ($isEdit) {
                                        // Secara default aktifkan untuk PPSDM sesuai tangkapan layar user
                                        $isBidangChecked = true;
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

        <!-- 4. Row: Email -->
        <div class="row mb-4 align-items-center">
            <label class="col-md-2 form-label-side">Email</label>
            <div class="col-md-10">
                <input type="email" 
                       name="email" 
                       class="form-control rounded-2 py-2 @error('email') is-invalid @enderror" 
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
                       class="form-control rounded-2 py-2 @error('username') is-invalid @enderror" 
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
                       class="form-control rounded-2 py-2 @error('password') is-invalid @enderror" 
                       placeholder="Password"
                       {{ $isEdit ? '' : 'required' }}>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                @if($isEdit)
                    <div class="text-muted small mt-1">Kosongkan jika tidak ingin mengganti kata sandi.</div>
                @endif
            </div>
        </div>

        <!-- Hidden NIK fallback jika ada atau generate -->
        @if($isEdit && $user->nik)
            <input type="hidden" name="nik" value="{{ $user->nik }}">
        @endif

        <!-- 7. Action Buttons (Update & Batal Sesuai Gambar) -->
        <div class="row mt-4">
            <div class="col-md-10 offset-md-2">
                <div class="d-flex align-items-center gap-2">
                    <button type="submit" class="btn btn-navy-kclc px-4 py-2 d-flex align-items-center gap-2">
                        <i class="bi bi-floppy"></i>
                        <span>{{ $isEdit ? 'Update' : 'Simpan' }}</span>
                    </button>
                    <a href="{{ route('users.index') }}" class="btn btn-orange-kclc px-4 py-2 d-flex align-items-center gap-2">
                        <i class="bi bi-x-circle"></i>
                        <span>Batal</span>
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
