@extends('layouts.app')

@section('title', 'Kelas Mapel - ' . $kelasMapel->nama_kelas)
@section('page_title', 'KELAS MAPEL')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">LMS Diklat</a></li>
    <li class="breadcrumb-item"><a href="{{ route('kelas-mapel.index') }}">Kelas Mapel</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $kelasMapel->nama_kelas }}</li>
@endsection

@section('content')
<div class="card card-custom mb-4">
    <!-- Tab Navigation Sesuai KCLC -->
    <div class="card-header bg-white border-bottom p-0">
        <ul class="nav nav-tabs card-header-tabs m-0 border-0">
            <li class="nav-item">
                <a class="nav-link px-4 py-3 {{ $tab == 'detail' ? 'active fw-bold text-primary border-bottom border-primary border-3' : 'text-secondary' }}" 
                   href="{{ route('kelas-mapel.show', ['kelas_mapel' => $kelasMapel->id, 'tab' => 'detail']) }}">
                    <i class="bi bi-card-text me-1"></i> Detail Kelas
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link px-4 py-3 {{ $tab == 'pengajar' ? 'active fw-bold text-primary border-bottom border-primary border-3' : 'text-secondary' }}" 
                   href="{{ route('kelas-mapel.show', ['kelas_mapel' => $kelasMapel->id, 'tab' => 'pengajar']) }}">
                    <i class="bi bi-person-workspace me-1"></i> Pengajar
                    @if($kelasMapel->pengajars->count() > 0)
                        <span class="badge bg-primary-subtle text-primary ms-1">{{ $kelasMapel->pengajars->count() }}</span>
                    @endif
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link px-4 py-3 {{ $tab == 'jadwal' ? 'active fw-bold text-primary border-bottom border-primary border-3' : 'text-secondary' }}" 
                   href="{{ route('kelas-mapel.show', ['kelas_mapel' => $kelasMapel->id, 'tab' => 'jadwal']) }}">
                    <i class="bi bi-calendar-event me-1"></i> Jadwal Pelaksanaan
                    @if($jadwals->count() > 0)
                        <span class="badge bg-info-subtle text-info ms-1">{{ $jadwals->count() }}</span>
                    @endif
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link px-4 py-3 {{ $tab == 'peserta' ? 'active fw-bold text-primary border-bottom border-primary border-3' : 'text-secondary' }}" 
                   href="{{ route('kelas-mapel.show', ['kelas_mapel' => $kelasMapel->id, 'tab' => 'peserta']) }}">
                    <i class="bi bi-people me-1"></i> Peserta Kelas
                    @if(isset($pesertas) && $pesertas->count() > 0)
                        <span class="badge bg-success text-white ms-1">{{ $pesertas->count() }}</span>
                    @elseif($kelasMapel->pesertas->count() > 0)
                        <span class="badge bg-success text-white ms-1">{{ $kelasMapel->pesertas->count() }}</span>
                    @endif
                </a>
            </li>
        </ul>
    </div>

    <div class="card-body p-4">
        <!-- ========================================== -->
        <!-- TAB 1: DETAIL KELAS                        -->
        <!-- ========================================== -->
        @if($tab == 'detail')
            <form action="{{ route('kelas-mapel.update', $kelasMapel->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Angkatan <span class="text-danger">*</span></label>
                        <select name="angkatan_id" class="form-select border-success" required>
                            @foreach($angkatans as $a)
                                <option value="{{ $a->id }}" {{ $kelasMapel->angkatan_id == $a->id ? 'selected' : '' }}>
                                    {{ $a->nomor_angkatan }} &check;
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Kapasitas <span class="text-danger">*</span></label>
                        <input type="number" name="kapasitas" class="form-control" value="{{ $kelasMapel->kapasitas }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Kurikulum <span class="text-danger">*</span></label>
                        <input type="text" name="kurikulum" class="form-control" value="{{ $kelasMapel->kurikulum }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Tgl. Mulai</label>
                        <input type="date" name="tanggal_mulai" class="form-control" value="{{ $kelasMapel->tanggal_mulai ? $kelasMapel->tanggal_mulai->format('Y-m-d') : '' }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Pelatihan <span class="text-danger">*</span></label>
                        <select name="pelatihan_id" class="form-select" required>
                            @foreach($pelatihans as $p)
                                <option value="{{ $p->id }}" {{ $kelasMapel->pelatihan_id == $p->id ? 'selected' : '' }}>
                                    {{ $p->nama_pelatihan }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Tgl. Selesai</label>
                        <input type="date" name="tanggal_selesai" class="form-control" value="{{ $kelasMapel->tanggal_selesai ? $kelasMapel->tanggal_selesai->format('Y-m-d') : '' }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Mata Pelatihan <span class="text-danger">*</span></label>
                        <select name="mata_pelatihan_id" class="form-select">
                            <option value="">-- Pilih Mata Pelatihan --</option>
                            @foreach($mataPelatihans as $mp)
                                <option value="{{ $mp->id }}" {{ $kelasMapel->mata_pelatihan_id == $mp->id ? 'selected' : '' }}>
                                    {{ $mp->nama_mata_pelatihan }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Ada Kelompok?</label>
                        <select name="ada_kelompok" class="form-select">
                            <option value="tidak" {{ $kelasMapel->ada_kelompok == 'tidak' ? 'selected' : '' }}>tidak</option>
                            <option value="ya" {{ $kelasMapel->ada_kelompok == 'ya' ? 'selected' : '' }}>ya</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">JP</label>
                        <input type="number" step="any" name="jp" class="form-control" value="{{ $kelasMapel->jp }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nama kelas <span class="text-danger">*</span></label>
                        <input type="text" name="nama_kelas" class="form-control" value="{{ $kelasMapel->nama_kelas }}" required>
                    </div>
                </div>

                <!-- Sub-bagian Buat Jadwal Mingguan -->
                <div class="mt-4 pt-3 border-top">
                    <div class="p-2 mb-3 rounded text-white fw-bold" style="background-color: #2563EB;">
                        <i class="bi bi-calendar3 me-1"></i> Buat Jadwal Mingguan
                    </div>

                    <div class="table-responsive mb-3">
                        <table class="table table-bordered align-middle">
                            <thead class="table-primary text-center">
                                <tr>
                                    <th style="width: 40px;">#</th>
                                    <th>HARI</th>
                                    <th>JAM MULAI</th>
                                    <th>JAM SELESAI</th>
                                    <th>METODE PEMBELAJARAN</th>
                                    <th>RUANGAN</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center fw-bold">1</td>
                                    <td>
                                        <select name="hari" class="form-select form-select-sm">
                                            @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $h)
                                                <option value="{{ $h }}" {{ $kelasMapel->hari == $h ? 'selected' : '' }}>{{ $h }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="time" name="jam_mulai" class="form-control form-control-sm" value="{{ $kelasMapel->jam_mulai ? substr($kelasMapel->jam_mulai, 0, 5) : '08:00' }}">
                                    </td>
                                    <td>
                                        <input type="time" name="jam_selesai" class="form-control form-control-sm" value="{{ $kelasMapel->jam_selesai ? substr($kelasMapel->jam_selesai, 0, 5) : '12:00' }}">
                                    </td>
                                    <td>
                                        <select name="metode_pembelajaran" class="form-select form-select-sm">
                                            <option value="Synchronous" {{ $kelasMapel->metode_pembelajaran == 'Synchronous' ? 'selected' : '' }}>Synchronous</option>
                                            <option value="Asynchronous" {{ $kelasMapel->metode_pembelajaran == 'Asynchronous' ? 'selected' : '' }}>Asynchronous</option>
                                            <option value="Klasikal" {{ $kelasMapel->metode_pembelajaran == 'Klasikal' ? 'selected' : '' }}>Klasikal</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select name="ruangan_id" class="form-select form-select-sm">
                                            <option value="">-- Pilih Ruangan --</option>
                                            @foreach($ruangans as $r)
                                                <option value="{{ $r->id }}" {{ $kelasMapel->ruangan_id == $r->id ? 'selected' : '' }}>
                                                    {{ $r->nama_ruangan }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                    <a href="{{ route('kelas-mapel.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
                    </a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-check2-circle me-1"></i> Update
                    </button>
                </div>
            </form>
        @endif

        <!-- ========================================== -->
        <!-- TAB 2: PENGAJAR                            -->
        <!-- ========================================== -->
        @if($tab == 'pengajar')
            <div class="mb-4">
                <h5 class="fw-bold text-dark mb-1">Informasi Daftar Pengajar</h5>
                <p class="text-muted small">Masukkan daftar pengajar yang mengajar pada kelas ini beserta peran dan alokasi JP.</p>
            </div>

            <!-- Tabel Daftar Pengajar Sesuai Gambar KCLC -->
            <div class="table-responsive mb-4">
                <table class="table table-bordered align-middle">
                    <thead class="table-primary text-center">
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>PENGAJAR</th>
                            <th style="width: 100px;">JP</th>
                            <th>JADWAL</th>
                            <th>PERAN</th>
                            <th>KELOMPOK</th>
                            <th style="width: 80px;">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kelasMapel->pengajars as $idx => $kmp)
                            <tr>
                                <td class="text-center fw-bold">{{ $idx + 1 }}</td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $kmp->pengajar->nama_lengkap ?? $kmp->pengajar->nama ?? '-' }}</div>
                                    <span class="badge bg-light text-secondary border">
                                        {{ class_basename($kmp->pengajar_type) }} &bull; NIK: {{ $kmp->pengajar->nik ?? $kmp->pengajar->nip ?? '-' }}
                                    </span>
                                </td>
                                <td class="text-center fw-bold text-primary">{{ $kmp->jp }}</td>
                                <td>{{ $kmp->jadwal_keterangan ?? ($kelasMapel->hari . ' ' . $kelasMapel->jam_mulai . '-' . $kelasMapel->jam_selesai) }}</td>
                                <td>
                                    <span class="badge bg-primary text-capitalize px-3 py-2 fs-6">
                                        {{ $kmp->peran }}
                                    </span>
                                </td>
                                <td>{{ $kmp->kelompok ?? '--' }}</td>
                                <td class="text-center">
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-danger" 
                                            title="Hapus Pengajar" 
                                            onclick="confirmDelete('del-kmp-{{ $kmp->id }}', 'Hapus penugasan pengajar ini?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    <form id="del-kmp-{{ $kmp->id }}" action="{{ route('kelas-mapel.pengajar.destroy', ['kelas_mapel' => $kelasMapel->id, 'pengajar' => $kmp->id]) }}" method="POST" class="d-none">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    Belum ada pengajar yang ditugaskan pada kelas ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Form Tambah Pengajar Baru -->
            <div class="card border border-primary-subtle bg-light p-3 rounded mb-4">
                <h6 class="fw-bold text-primary mb-3"><i class="bi bi-plus-circle me-1"></i> Tambah Pengajar ke Kelas</h6>
                <form action="{{ route('kelas-mapel.pengajar.store', $kelasMapel->id) }}" method="POST">
                    @csrf
                    <div class="row g-2 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold">Pilih Pengajar <span class="text-danger">*</span></label>
                            <select name="pengajar_combo" class="form-select form-select-sm" required>
                                <option value="">-- Pilih Pengajar --</option>
                                <optgroup label="Widyaiswara (Internal)">
                                    @foreach($widyaiswaras as $wi)
                                        <option value="Widyaiswara:{{ $wi->id }}">{{ $wi->nama_gelar }} ({{ $wi->jabatan }})</option>
                                    @endforeach
                                </optgroup>
                                <optgroup label="Narasumber (Eksternal)">
                                    @foreach($narasumbers as $ns)
                                        <option value="Narasumber:{{ $ns->id }}">{{ $ns->nama_gelar }} ({{ $ns->instansi }})</option>
                                    @endforeach
                                </optgroup>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-semibold">JP <span class="text-danger">*</span></label>
                            <input type="number" step="any" name="jp" class="form-control form-control-sm" value="{{ $kelasMapel->jp }}" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-semibold">Peran <span class="text-danger">*</span></label>
                            <select name="peran" class="form-select form-select-sm" required>
                                <option value="widyaiswara">widyaiswara</option>
                                <option value="narasumber">narasumber</option>
                                <option value="coach">coach</option>
                                <option value="mentor">mentor</option>
                                <option value="evaluator">evaluator</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold">Jadwal Keterangan</label>
                            <input type="text" name="jadwal_keterangan" class="form-control form-control-sm" value="{{ $kelasMapel->hari }} {{ substr($kelasMapel->jam_mulai, 0, 5) }}-{{ substr($kelasMapel->jam_selesai, 0, 5) }}">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-sm btn-primary w-100">
                                <i class="bi bi-save me-1"></i> Simpan
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                <a href="{{ route('kelas-mapel.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
                </a>
            </div>
        @endif

        <!-- ========================================== -->
        <!-- TAB 3: JADWAL PELAKSANAAN                  -->
        <!-- ========================================== -->
        @if($tab == 'jadwal')
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-4">
                <div>
                    <h5 class="fw-bold text-dark mb-1">Jadwal Pelaksanaan Sesi (Terkoneksi ke SIR-JP)</h5>
                    <p class="text-muted small mb-0">Jadwal pembelajaran yang tercatat pada sistem SIR-JP untuk angkatan ini.</p>
                </div>
                <div class="d-flex gap-2">
                    <form action="{{ route('kelas-mapel.sinkron_jadwal', $kelasMapel->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-success">
                            <i class="bi bi-arrow-repeat me-1"></i> Buat Sesi Jadwal ke SIR-JP
                        </button>
                    </form>
                    <a href="{{ route('jadwal.create') }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-plus-lg me-1"></i> Jadwal Manual
                    </a>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Tanggal & Sesi</th>
                            <th>Mata Pelatihan</th>
                            <th>Pengajar & Peran</th>
                            <th>Ruangan</th>
                            <th class="text-center">JP</th>
                            <th class="text-center">Status Sesi</th>
                            <th class="text-center">Aksi SIR-JP</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jadwals as $i => $j)
                            <tr>
                                <td class="text-center">{{ $i + 1 }}</td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $j->tanggal->format('d/m/Y') }}</div>
                                    <span class="badge bg-light text-secondary border">
                                        {{ $j->waktu_mulai }} - {{ $j->waktu_selesai }}
                                    </span>
                                </td>
                                <td>{{ $j->mataPelatihan->nama_mata_pelatihan ?? '-' }}</td>
                                <td>
                                    @forelse($j->pengajars as $pengajar)
                                        <div class="small fw-semibold text-dark">
                                            &bull; {{ $pengajar->nama_gelar }}
                                            <span class="badge bg-secondary-subtle text-secondary small">{{ $pengajar->peran_mengajar }}</span>
                                        </div>
                                    @empty
                                        <span class="text-muted small">Belum ada pengajar</span>
                                    @endforelse
                                </td>
                                <td>{{ $j->ruangan ?? $kelasMapel->ruangan->nama_ruangan ?? 'Online' }}</td>
                                <td class="text-center fw-bold text-primary">{{ $j->jp_rencana }}</td>
                                <td class="text-center">
                                    @if($j->status == 'Terlaksana')
                                        <span class="badge bg-success">Terlaksana</span>
                                    @elseif($j->status == 'Terjadwal')
                                        <span class="badge bg-info">Terjadwal</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $j->status }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($j->status == 'Terjadwal')
                                        <a href="{{ route('realisasi.create', ['jadwal_id' => $j->id]) }}" class="btn btn-sm btn-success" title="Konfirmasi Realisasi">
                                            <i class="bi bi-check-circle"></i> Realisasi
                                        </a>
                                    @else
                                        <span class="text-success small fw-semibold"><i class="bi bi-check2-all"></i> Masuk Rekap</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">
                                    Belum ada jadwal pelaksanaan sesi untuk angkatan ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                <a href="{{ route('kelas-mapel.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
                </a>
            </div>
        @endif

        <!-- ========================================== -->
        <!-- TAB 4: PESERTA KELAS                       -->
        <!-- ========================================== -->
        @if($tab == 'peserta')
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h5 class="fw-bold text-dark mb-1">Daftar Peserta Pelatihan</h5>
                    <p class="text-muted small">Data peserta diklat yang terdaftar pada angkatan dan kelas ini.</p>
                </div>
                <a href="{{ route('peserta.create') }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-person-plus me-1"></i> Tambah Peserta
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th style="width: 40px;">No</th>
                            <th>NIP</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Jenis Kelamin</th>
                            <th>Instansi</th>
                            <th>Jabatan</th>
                            <th class="text-center">Golongan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pesertas ?? $kelasMapel->pesertas as $i => $pst)
                            <tr>
                                <td class="text-center">{{ $i + 1 }}</td>
                                <td class="fw-semibold text-dark">{{ $pst->nip }}</td>
                                <td class="fw-bold text-primary">{{ $pst->nama }}</td>
                                <td>{{ $pst->email ?? '-' }}</td>
                                <td>{{ $pst->jenis_kelamin }}</td>
                                <td>{{ $pst->instansi ?? '-' }}</td>
                                <td>{{ $pst->jabatan ?? '-' }}</td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border">{{ $pst->golongan ?? '-' }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">
                                    Belum ada peserta yang didaftarkan ke kelas ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                <a href="{{ route('kelas-mapel.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
