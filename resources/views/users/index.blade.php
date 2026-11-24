@extends('layouts.app')

@section('title', 'Manajemen Pengguna & Role')
@section('page_title', 'Manajemen Pengguna & Role')

@section('page_actions')
<a href="{{ route('users.create') }}" class="btn btn-primary d-flex align-items-center gap-2 rounded-3 shadow-sm">
    <i class="bi bi-person-plus-fill"></i>
    <span>Tambah Pengguna</span>
</a>
@endsection

@section('content')
<!-- Ringkasan Statistik Pengguna -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background-color: #EFF6FF; color: #2563EB;">
                    <i class="bi bi-people-fill fs-5"></i>
                </div>
                <div>
                    <div class="text-secondary small fw-medium">Total Akun</div>
                    <div class="fs-4 fw-bold text-dark">{{ $countAll }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background-color: #F0FDF4; color: #16A34A;">
                    <i class="bi bi-person-badge-fill fs-5"></i>
                </div>
                <div>
                    <div class="text-secondary small fw-medium">Penyelenggara</div>
                    <div class="fs-4 fw-bold text-dark">{{ $countPenyelenggara }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background-color: #ECFEFF; color: #0891B2;">
                    <i class="bi bi-mortarboard-fill fs-5"></i>
                </div>
                <div>
                    <div class="text-secondary small fw-medium">Peserta Diklat</div>
                    <div class="fs-4 fw-bold text-dark">{{ $countPeserta }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background-color: #FEF3C7; color: #D97706;">
                    <i class="bi bi-person-workspace fs-5"></i>
                </div>
                <div>
                    <div class="text-secondary small fw-medium">Widyaiswara</div>
                    <div class="fs-4 fw-bold text-dark">{{ $countWidyaiswara }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

@if(isset($countGuest) && $countGuest > 0)
    <div class="alert alert-warning border-0 shadow-sm rounded-3 d-flex align-items-center justify-content-between p-3 mb-4" style="background-color: #FEF3C7; color: #92400E; border-left: 4px solid #F59E0B !important;">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center bg-warning text-dark fw-bold" style="width: 38px; height: 38px;">
                <i class="bi bi-person-exclamation fs-5"></i>
            </div>
            <div>
                <strong class="d-block">Perhatian Administrator: Terdapat {{ $countGuest }} Akun Guest Baru</strong>
                <span class="small">Pengguna baru mendaftar dengan peran tamu (Guest). Klik tombol di samping untuk menetapkan peran dan hak akses pengguna.</span>
            </div>
        </div>
        <a href="{{ route('users.index', ['role' => 'guest']) }}" class="btn btn-warning btn-sm fw-semibold shadow-sm text-dark px-3 text-nowrap">
            <i class="bi bi-pencil-square me-1"></i> Tinjau &amp; Beri Akses
        </a>
    </div>
@endif

<!-- Filter Bar -->
<div class="card border-0 shadow-sm rounded-3 p-3 mb-4 bg-white">
    <form method="GET" action="{{ route('users.index') }}" class="row g-2 align-items-end">
        <div class="col-md-5">
            <label class="form-label small fw-semibold text-secondary mb-1">CARI PENGGUNA</label>
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-secondary"></i></span>
                <input type="text" name="search" value="{{ $search }}" class="form-control border-start-0" placeholder="Ketik nama, NIK, username, atau email...">
            </div>
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-semibold text-secondary mb-1">PERAN (ROLE)</label>
            <select name="role" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Semua Peran (Role)</option>
                <option value="guest" {{ $role === 'guest' ? 'selected' : '' }}>Guest (Menunggu Penugasan Role)</option>
                <option value="penyelenggara" {{ $role === 'penyelenggara' ? 'selected' : '' }}>Penyelenggara / Admin</option>
                <option value="peserta" {{ $role === 'peserta' ? 'selected' : '' }}>Peserta Diklat</option>
                <option value="widyaiswara" {{ $role === 'widyaiswara' ? 'selected' : '' }}>Widyaiswara</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label small fw-semibold text-secondary mb-1">STATUS</label>
            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Semua Status</option>
                <option value="Aktif" {{ $status === 'Aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="Nonaktif" {{ $status === 'Nonaktif' ? 'selected' : '' }}>Nonaktif</option>
            </select>
        </div>
        <div class="col-md-2 d-flex gap-1">
            <button type="submit" class="btn btn-sm btn-primary flex-grow-1">Filter</button>
            <a href="{{ route('users.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
        </div>
    </form>
</div>

<!-- Tabel Pengguna -->
<div class="card bg-white border-0 shadow-sm rounded-3 p-0 mb-4 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
            <thead class="table-light">
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Nama & Akun</th>
                    <th>NIK / NIP</th>
                    <th>Email</th>
                    <th class="text-center">Peran (Role)</th>
                    <th class="text-center">Status</th>
                    <th>Terdaftar</th>
                    <th class="text-center" style="width: 120px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $index => $u)
                    <tr>
                        <td class="text-secondary small">{{ $users->firstItem() + $index }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar-circle rounded-circle bg-light border d-flex align-items-center justify-content-center flex-shrink-0" style="width: 34px; height: 34px;">
                                    <i class="bi bi-person text-secondary"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark">{{ $u->name }}</div>
                                    <div class="text-muted small" style="font-size: 0.75rem;">@<span>{{ $u->username ?: '-' }}</span></div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="font-monospace text-dark">{{ $u->nik ?: '-' }}</span>
                        </td>
                        <td>
                            <span class="text-secondary small">{{ $u->email }}</span>
                        </td>
                        <td class="text-center">
                            @if($u->isGuest())
                                <span class="badge rounded-pill px-3 py-1 fw-semibold" style="background-color: #FEF3C7; color: #B45309; border: 1px solid #FDE68A;">
                                    <i class="bi bi-clock-history me-1"></i> Guest (Perlu Diset)
                                </span>
                            @elseif(is_array($u->roles) && count($u->roles) > 0)
                                <div class="d-flex flex-wrap justify-content-center gap-1">
                                    @foreach(array_slice($u->roles, 0, 2) as $r)
                                        <span class="badge rounded-pill px-2 py-1 small" style="background-color: #EFF6FF; color: #1E40AF; border: 1px solid #BFDBFE;">
                                            {{ $r }}
                                        </span>
                                    @endforeach
                                    @if(count($u->roles) > 2)
                                        <span class="badge rounded-pill bg-light text-secondary border px-2 py-1" title="{{ implode(', ', array_slice($u->roles, 2)) }}">
                                            +{{ count($u->roles) - 2 }}
                                        </span>
                                    @endif
                                </div>
                            @elseif($u->role === 'peserta')
                                <span class="badge rounded-pill px-3 py-1 fw-semibold" style="background-color: #ECFEFF; color: #0891B2; border: 1px solid #A5F3FC;">
                                    <i class="bi bi-mortarboard me-1"></i> Peserta Diklat
                                </span>
                            @elseif($u->role === 'widyaiswara')
                                <span class="badge rounded-pill px-3 py-1 fw-semibold" style="background-color: #FEF3C7; color: #D97706; border: 1px solid #FDE68A;">
                                    <i class="bi bi-person-workspace me-1"></i> Widyaiswara
                                </span>
                            @else
                                <span class="badge rounded-pill px-3 py-1 fw-semibold" style="background-color: #EFF6FF; color: #2563EB; border: 1px solid #BFDBFE;">
                                    <i class="bi bi-shield-check me-1"></i> Penyelenggara
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($u->status === 'Aktif')
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1">Aktif</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary border rounded-pill px-2 py-1">Nonaktif</span>
                            @endif
                        </td>
                        <td class="text-muted small">
                            {{ $u->created_at ? $u->created_at->format('d/m/Y') : '-' }}
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('users.edit', $u->id) }}" class="btn btn-outline-primary" title="Ubah Data / Role (Edit Users)">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                @if(auth()->id() !== $u->id)
                                    <form action="{{ route('users.destroy', $u->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun {{ $u->name }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Hapus Akun">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>

                    <!-- MODAL EDIT PENGGUNA & ROLE -->
                    <div class="modal fade" id="modalEditUser{{ $u->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow-lg rounded-3">
                                <div class="modal-header border-bottom py-3 px-4">
                                    <h6 class="modal-title fw-bold text-dark">
                                        <i class="bi bi-person-gear text-primary me-2"></i>Edit Pengguna & Peran (Role)
                                    </h6>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('users.update', $u->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-body p-4">
                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold text-secondary">Nama Lengkap <span class="text-danger">*</span></label>
                                            <input type="text" name="name" class="form-control" value="{{ $u->name }}" required>
                                        </div>
                                        <div class="row g-2 mb-3">
                                            <div class="col-sm-6">
                                                <label class="form-label small fw-semibold text-secondary">NIK (16 Digit) <span class="text-danger">*</span></label>
                                                <input type="text" name="nik" class="form-control font-monospace" value="{{ $u->nik }}" maxlength="16" required>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="form-label small fw-semibold text-secondary">Email <span class="text-danger">*</span></label>
                                                <input type="email" name="email" class="form-control" value="{{ $u->email }}" required>
                                            </div>
                                        </div>

                                        <!-- Pilihan Peran (Role) Sesuai Permintaan User -->
                                        <div class="mb-3 p-3 rounded-3 border bg-light">
                                            <label class="form-label small fw-bold text-dark text-uppercase mb-2">
                                                PILIH PERAN / AKSES PENGGUNA <span class="text-danger">*</span>
                                            </label>
                                            <select name="role" class="form-select" required>
                                                <option value="peserta" {{ $u->role === 'peserta' ? 'selected' : '' }}>
                                                    🎓 Peserta Diklat (Sidebar Khusus Peserta & Portal LMS)
                                                </option>
                                                <option value="penyelenggara" {{ in_array($u->role, ['penyelenggara', 'admin']) ? 'selected' : '' }}>
                                                    🛡️ Penyelenggara / Admin (Sidebar Lengkap Seluruh Fitur)
                                                </option>
                                                <option value="widyaiswara" {{ $u->role === 'widyaiswara' ? 'selected' : '' }}>
                                                    👨‍🏫 Widyaiswara / Pengajar (Jadwal & Rekap Jam Pelajaran)
                                                </option>
                                            </select>
                                            <div class="text-muted small mt-2" style="font-size: 0.75rem;">
                                                <i class="bi bi-info-circle me-1"></i> Apabila dipilih <b>Peserta Diklat</b>, saat login pengguna ini langsung melihat <b>Dashboard & Sidebar khusus Peserta</b>.
                                            </div>
                                        </div>

                                        <div class="row g-2 mb-3">
                                            <div class="col-sm-6">
                                                <label class="form-label small fw-semibold text-secondary">Status Akun <span class="text-danger">*</span></label>
                                                <select name="status" class="form-select" required>
                                                    <option value="Aktif" {{ $u->status === 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                                    <option value="Nonaktif" {{ $u->status === 'Nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                                                </select>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="form-label small fw-semibold text-secondary">Password Baru (Opsional)</label>
                                                <input type="password" name="password" class="form-control" placeholder="Biarkan kosong jika tetap">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-top py-2 px-4">
                                        <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-sm btn-primary px-4 fw-semibold">Simpan Perubahan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-person-x fs-1 d-block mb-2"></i>
                            Belum ada data pengguna yang sesuai dengan pencarian.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="p-3 border-top d-flex justify-content-between align-items-center">
        <div class="text-secondary small">
            Menampilkan {{ $users->firstItem() ?? 0 }} - {{ $users->lastItem() ?? 0 }} dari {{ $users->total() }} akun
        </div>
        <div>
            {{ $users->links() }}
        </div>
    </div>
</div>

<!-- MODAL TAMBAH PENGGUNA BARU -->
<div class="modal fade" id="modalCreateUser" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <div class="modal-header border-bottom py-3 px-4">
                <h6 class="modal-title fw-bold text-dark">
                    <i class="bi bi-person-plus text-primary me-2"></i>Tambah Pengguna Baru
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="Nama lengkap peserta atau staf" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-sm-6">
                            <label class="form-label small fw-semibold text-secondary">NIK (16 Digit) <span class="text-danger">*</span></label>
                            <input type="text" name="nik" class="form-control font-monospace" maxlength="16" placeholder="16 digit angka" required>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label small fw-semibold text-secondary">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" placeholder="email@kemnaker.go.id" required>
                        </div>
                    </div>

                    <!-- Role Dropdown -->
                    <div class="mb-3 p-3 rounded-3 border bg-light">
                        <label class="form-label small fw-bold text-dark text-uppercase mb-2">
                            PILIH PERAN / AKSES PENGGUNA <span class="text-danger">*</span>
                        </label>
                        <select name="role" class="form-select" required>
                            <option value="peserta" selected>🎓 Peserta Diklat (Sidebar Khusus Peserta & Portal LMS)</option>
                            <option value="penyelenggara">🛡️ Penyelenggara / Admin (Sidebar Lengkap Seluruh Fitur)</option>
                            <option value="widyaiswara">👨‍🏫 Widyaiswara / Pengajar (Jadwal & Rekap Jam Pelajaran)</option>
                        </select>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-sm-6">
                            <label class="form-label small fw-semibold text-secondary">Status Akun <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                <option value="Aktif" selected>Aktif</option>
                                <option value="Nonaktif">Nonaktif</option>
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label small fw-semibold text-secondary">Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top py-2 px-4">
                    <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary px-4 fw-semibold">Buat Akun</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
