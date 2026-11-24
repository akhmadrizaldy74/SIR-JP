@extends('layouts.app')

@section('title', 'Presensi Manual: ' . $attendanceSession->judul_sesi)

@section('content')
<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <a href="{{ route('attendance-sessions.show', $attendanceSession->id) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
            <i class="bi bi-arrow-left"></i> Kembali ke Detail Sesi
        </a>
        <div class="d-flex gap-2">
            <span class="badge bg-light text-dark border px-3 py-2 small">
                Total: <strong>{{ $filteredPesertas->count() }}</strong> Peserta
            </span>
        </div>
    </div>

    <!-- Session Info Card -->
    <div class="card border-0 shadow-sm rounded-3 p-3 bg-white mb-3">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold text-dark mb-0">{{ $attendanceSession->judul_sesi }}</h5>
                <small class="text-muted">
                    {{ $attendanceSession->tanggal->format('d/m/Y') }} &bull; 
                    Pukul {{ substr($attendanceSession->jam_mulai, 0, 5) }} - {{ substr($attendanceSession->jam_selesai, 0, 5) }} WIB &bull; 
                    Kelas: <strong>{{ $attendanceSession->kelasMapel->nama_kelas ?? '-' }}</strong>
                </small>
            </div>
            <div>
                <a href="{{ route('attendance-sessions.qr_display', $attendanceSession->id) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                    <i class="bi bi-qr-code"></i> Layar QR
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Filter & Search Bar -->
<div class="card border-0 shadow-sm rounded-3 p-3 mb-3 bg-white">
    <form method="GET" action="{{ route('attendance-manual.index', $attendanceSession->id) }}" class="row g-2 align-items-center">
        <div class="col-md-5">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="search" value="{{ $search }}" class="form-control border-start-0" placeholder="Cari nama, NIP, atau instansi peserta...">
            </div>
        </div>
        <div class="col-md-4">
            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Semua Status Kehadiran</option>
                <option value="Hadir" {{ $filterStatus === 'Hadir' ? 'selected' : '' }}>Hadir</option>
                <option value="Terlambat" {{ $filterStatus === 'Terlambat' ? 'selected' : '' }}>Terlambat</option>
                <option value="Izin" {{ $filterStatus === 'Izin' ? 'selected' : '' }}>Izin</option>
                <option value="Sakit" {{ $filterStatus === 'Sakit' ? 'selected' : '' }}>Sakit</option>
                <option value="Alpa" {{ $filterStatus === 'Alpa' ? 'selected' : '' }}>Alpa</option>
                <option value="Dispensasi" {{ $filterStatus === 'Dispensasi' ? 'selected' : '' }}>Dispensasi</option>
                <option value="Belum Presensi" {{ $filterStatus === 'Belum Presensi' ? 'selected' : '' }}>Belum Presensi</option>
            </select>
        </div>
        <div class="col-md-3 d-flex gap-1">
            <button type="submit" class="btn btn-sm btn-primary flex-grow-1">Filter</button>
            <a href="{{ route('attendance-manual.index', $attendanceSession->id) }}" class="btn btn-sm btn-outline-secondary">Reset</a>
        </div>
    </form>
</div>

<!-- Bulk Action Bar (Floating / Conditional) -->
<form id="bulkForm" action="{{ route('attendance-manual.bulk', $attendanceSession->id) }}" method="POST">
    @csrf
    <div id="bulkActionBar" class="card border-primary shadow-sm rounded-3 p-2 mb-3 bg-primary-subtle d-none">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-check-square-fill text-primary fs-5"></i>
                <span class="fw-bold small text-dark"><span id="selectedCount">0</span> peserta dipilih</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <select name="target_status" id="bulkTargetStatus" class="form-select form-select-sm" required style="width: 140px;">
                    <option value="">-- Ubah Status --</option>
                    <option value="Hadir">Hadir</option>
                    <option value="Terlambat">Terlambat</option>
                    <option value="Izin">Izin</option>
                    <option value="Sakit">Sakit</option>
                    <option value="Alpa">Alpa</option>
                    <option value="Dispensasi">Dispensasi</option>
                </select>
                <input type="text" name="alasan_perubahan" class="form-control form-control-sm" placeholder="Alasan perubahan massal..." required style="min-width: 200px;">
                <button type="submit" class="btn btn-sm btn-primary px-3 fw-bold" onclick="return confirm('Terapkan perubahan status pada semua peserta yang dipilih?');">
                    Terapkan
                </button>
            </div>
        </div>
    </div>

    <!-- Table of Participants -->
    <div class="card bg-white border-0 shadow-sm rounded-3 p-0 mb-5 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                <thead class="table-light">
                    <tr>
                        <th style="width: 35px;" class="text-center">
                            <input type="checkbox" id="selectAllCheckbox" class="form-check-input">
                        </th>
                        <th>Peserta Diklat</th>
                        <th>NIP & Instansi</th>
                        <th class="text-center">Status Saat Ini</th>
                        <th>Waktu & Metode</th>
                        <th>Keterangan</th>
                        <th class="text-center" style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($filteredPesertas as $pst)
                        @php
                            $rec = $presensis->get($pst->id);
                            $badge = $rec ? $rec->status_badge : ['bg' => '#F1F5F9', 'color' => '#64748B', 'label' => 'Belum Presensi'];
                            $currentStatus = $rec ? $rec->status_kehadiran : '';
                        @endphp
                        <tr>
                            <td class="text-center">
                                <input type="checkbox" name="peserta_ids[]" value="{{ $pst->id }}" class="form-check-input row-checkbox">
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $pst->nama }}</div>
                                <small class="text-muted">{{ $pst->jenis_kelamin }}</small>
                            </td>
                            <td>
                                <div>{{ $pst->nip }}</div>
                                <small class="text-muted">{{ $pst->instansi ?: '-' }}</small>
                            </td>
                            <td class="text-center">
                                <span class="badge rounded-pill px-3 py-1 fw-semibold" style="background-color: {{ $badge['bg'] }}; color: {{ $badge['color'] }}; font-size: 0.78rem;">
                                    {{ $badge['label'] }}
                                </span>
                            </td>
                            <td>
                                @if($rec && $rec->waktu_presensi)
                                    <div><i class="bi bi-clock me-1"></i> {{ substr($rec->waktu_presensi, 0, 5) }} WIB</div>
                                    <small class="badge bg-light text-dark border">{{ $rec->metode_presensi }}</small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="small text-muted">
                                <div>{{ $rec->keterangan ?? '-' }}</div>
                                @if($rec && $rec->bukti_file)
                                    <div class="mt-1">
                                        <a href="{{ route('portal.presensi.bukti', $rec->id) }}" target="_blank" class="badge bg-primary text-white text-decoration-none shadow-sm">
                                            <i class="bi bi-paperclip me-1"></i> Bukti Lampiran
                                        </a>
                                    </div>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <button type="button" 
                                            class="btn btn-outline-primary btn-edit-single" 
                                            data-id="{{ $pst->id }}"
                                            data-nama="{{ $pst->nama }}"
                                            data-status="{{ $currentStatus }}"
                                            data-keterangan="{{ $rec->keterangan ?? '' }}"
                                            title="Ubah Status">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button type="button" 
                                            class="btn btn-outline-secondary btn-view-logs" 
                                            data-id="{{ $pst->id }}"
                                            data-nama="{{ $pst->nama }}"
                                            title="Riwayat Audit Perubahan">
                                        <i class="bi bi-clock-history"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Tidak ada data peserta yang cocok dengan filter.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</form>

<!-- Modal Ubah Status Single -->
<div class="modal fade" id="modalSingleEdit" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('attendance-manual.update', $attendanceSession->id) }}" method="POST">
                @csrf
                <input type="hidden" name="peserta_id" id="editPesertaId">
                <div class="modal-header">
                    <h6 class="modal-title fw-bold">Ubah Status Presensi: <span id="editPesertaNama" class="text-primary"></span></h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Pilihan Status -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-secondary">STATUS KEHADIRAN <span class="text-danger">*</span></label>
                        <select name="status" id="editStatusSelect" class="form-select" required>
                            <option value="Hadir">Hadir (Hijau)</option>
                            <option value="Terlambat">Terlambat (Biru)</option>
                            <option value="Izin">Izin (Kuning)</option>
                            <option value="Sakit">Sakit (Merah Muda)</option>
                            <option value="Alpa">Alpa / Tanpa Keterangan (Merah)</option>
                            <option value="Dispensasi">Dispensasi (Ungu)</option>
                        </select>
                    </div>

                    <!-- Keterangan Tambahan -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-secondary">KETERANGAN TAMBAHAN</label>
                        <input type="text" name="keterangan" id="editKeteranganInput" class="form-control" placeholder="Contoh: Mengikuti dinas luar instansi">
                    </div>

                    <!-- Alasan Perubahan (Mandatory for Audit) -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-secondary">ALASAN PERUBAHAN (AUDIT LOG) <span class="text-danger">*</span></label>
                        <textarea name="alasan_perubahan" class="form-control" rows="2" placeholder="Wajib diisi untuk audit trail (contoh: Konfirmasi izin via telepon)" required></textarea>
                        <small class="text-muted" style="font-size: 0.72rem;">Catatan ini akan direkam secara permanen dalam riwayat audit log sistem.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary px-3 fw-bold">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Audit Log Peserta -->
<div class="modal fade" id="modalAuditLogs" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-bold">Riwayat Audit Perubahan: <span id="logPesertaNama" class="text-primary"></span></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0" style="font-size: 0.82rem;">
                        <thead class="table-dark">
                            <tr>
                                <th>Waktu</th>
                                <th>Sebelum</th>
                                <th>Menjadi</th>
                                <th>Alasan</th>
                                <th>Metode</th>
                                <th>Diubah Oleh</th>
                            </tr>
                        </thead>
                        <tbody id="logTableBody">
                            <tr>
                                <td colspan="6" class="text-center py-3 text-muted">Memuat data log...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('selectAllCheckbox');
        const rowCheckboxes = document.querySelectorAll('.row-checkbox');
        const bulkBar = document.getElementById('bulkActionBar');
        const selectedCount = document.getElementById('selectedCount');

        function updateBulkBar() {
            let checkedCount = document.querySelectorAll('.row-checkbox:checked').length;
            selectedCount.textContent = checkedCount;
            if (checkedCount > 0) {
                bulkBar.classList.remove('d-none');
            } else {
                bulkBar.classList.add('d-none');
            }
        }

        if (selectAll) {
            selectAll.addEventListener('change', function() {
                rowCheckboxes.forEach(cb => cb.checked = selectAll.checked);
                updateBulkBar();
            });
        }

        rowCheckboxes.forEach(cb => {
            cb.addEventListener('change', updateBulkBar);
        });

        // Single Edit Modal
        const modalSingle = new bootstrap.Modal(document.getElementById('modalSingleEdit'));
        document.querySelectorAll('.btn-edit-single').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const nama = this.getAttribute('data-nama');
                const status = this.getAttribute('data-status');
                const ket = this.getAttribute('data-keterangan');

                document.getElementById('editPesertaId').value = id;
                document.getElementById('editPesertaNama').textContent = nama;
                if (status) {
                    document.getElementById('editStatusSelect').value = status;
                }
                document.getElementById('editKeteranganInput').value = ket;

                modalSingle.show();
            });
        });

        // Audit Logs Modal
        const modalLogs = new bootstrap.Modal(document.getElementById('modalAuditLogs'));
        document.querySelectorAll('.btn-view-logs').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const nama = this.getAttribute('data-nama');
                document.getElementById('logPesertaNama').textContent = nama;
                const tbody = document.getElementById('logTableBody');
                tbody.innerHTML = '<tr><td colspan="6" class="text-center py-3 text-muted">Memuat riwayat audit...</td></tr>';
                modalLogs.show();

                const logUrl = "{{ url('attendance-sessions/' . $attendanceSession->id . '/logs') }}/" + id;
                fetch(logUrl, { headers: { 'Accept': 'application/json' } })
                    .then(res => res.json())
                    .then(data => {
                        if (data.logs && data.logs.length) {
                            tbody.innerHTML = data.logs.map(l => `
                                <tr>
                                    <td>${l.waktu}</td>
                                    <td><span class="badge bg-light text-dark border">${l.status_sebelum}</span></td>
                                    <td><span class="badge bg-primary">${l.status_sesudah}</span></td>
                                    <td>${l.alasan || '-'}</td>
                                    <td>${l.metode}</td>
                                    <td>${l.oleh}</td>
                                </tr>
                            `).join('');
                        } else {
                            tbody.innerHTML = '<tr><td colspan="6" class="text-center py-3 text-muted">Belum ada riwayat perubahan manual pada sesi ini.</td></tr>';
                        }
                    })
                    .catch(err => {
                        tbody.innerHTML = '<tr><td colspan="6" class="text-center py-3 text-danger">Gagal memuat log audit.</td></tr>';
                    });
            });
        });
    });
</script>
@endsection
