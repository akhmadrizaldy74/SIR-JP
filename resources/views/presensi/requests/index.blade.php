@extends('layouts.app')

@section('title', 'Verifikasi Pengajuan Izin / Sakit / Dispensasi')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1">Verifikasi Pengajuan Ketidakhadiran</h4>
        <div class="text-muted small">Pemeriksaan dan persetujuan pengajuan izin, sakit, dan dispensasi peserta diklat</div>
    </div>
    <div class="d-flex gap-2">
        <span class="badge bg-warning text-dark border px-3 py-2">
            Pending: <strong>{{ $countPending }}</strong>
        </span>
        <span class="badge bg-success text-white border px-3 py-2">
            Disetujui: <strong>{{ $countApproved }}</strong>
        </span>
        <span class="badge bg-danger text-white border px-3 py-2">
            Ditolak: <strong>{{ $countRejected }}</strong>
        </span>
    </div>
</div>

<!-- Filter Bar -->
<div class="card border-0 shadow-sm rounded-3 p-3 mb-4 bg-white">
    <form method="GET" action="{{ route('attendance-requests.index') }}" class="row g-2 align-items-center">
        <div class="col-md-5">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="search" value="{{ $search }}" class="form-control border-start-0" placeholder="Cari nama peserta, NIP, atau alasan pengajuan...">
            </div>
        </div>
        <div class="col-md-4">
            <div class="btn-group btn-group-sm w-100" role="group">
                <a href="{{ route('attendance-requests.index', ['status' => 'Pending']) }}" class="btn {{ $status === 'Pending' ? 'btn-warning text-dark fw-bold' : 'btn-outline-secondary' }}">
                    Pending ({{ $countPending }})
                </a>
                <a href="{{ route('attendance-requests.index', ['status' => 'Approved']) }}" class="btn {{ $status === 'Approved' ? 'btn-success fw-bold' : 'btn-outline-secondary' }}">
                    Disetujui ({{ $countApproved }})
                </a>
                <a href="{{ route('attendance-requests.index', ['status' => 'Rejected']) }}" class="btn {{ $status === 'Rejected' ? 'btn-danger fw-bold' : 'btn-outline-secondary' }}">
                    Ditolak ({{ $countRejected }})
                </a>
                <a href="{{ route('attendance-requests.index', ['status' => 'Semua']) }}" class="btn {{ $status === 'Semua' ? 'btn-dark fw-bold' : 'btn-outline-secondary' }}">
                    Semua
                </a>
            </div>
        </div>
        <div class="col-md-3 d-flex gap-1">
            <button type="submit" class="btn btn-sm btn-primary flex-grow-1">Cari</button>
            <a href="{{ route('attendance-requests.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
        </div>
    </form>
</div>

<!-- Requests Table -->
<div class="card bg-white border-0 shadow-sm rounded-3 p-0 mb-5 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
            <thead class="table-light">
                <tr>
                    <th style="width: 40px;">No</th>
                    <th>Peserta Diklat</th>
                    <th>Sesi Pembelajaran</th>
                    <th class="text-center">Jenis</th>
                    <th>Alasan & Bukti</th>
                    <th class="text-center">Status</th>
                    <th>Verifikator</th>
                    <th class="text-center" style="width: 140px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $req)
                    @php
                        $jenisBadge = match($req->jenis_pengajuan) {
                            'Izin' => 'background-color: #FEF3C7; color: #92400E;',
                            'Sakit' => 'background-color: #FCE7F3; color: #9D174D;',
                            'Dispensasi' => 'background-color: #EDE9FE; color: #5B21B6;',
                            default => 'background-color: #F1F5F9; color: #475569;',
                        };
                        $statusBadge = match($req->status) {
                            'Approved' => 'bg-success text-white',
                            'Rejected' => 'bg-danger text-white',
                            default => 'bg-warning text-dark',
                        };
                    @endphp
                    <tr>
                        <td>{{ $loop->iteration + ($requests->currentPage() - 1) * $requests->perPage() }}</td>
                        <td>
                            <div class="fw-bold text-dark">{{ $req->peserta->nama ?? '-' }}</div>
                            <small class="text-muted">NIP: {{ $req->peserta->nip ?? '-' }}</small>
                            <div class="text-muted" style="font-size: 0.72rem;">{{ $req->peserta->angkatan->pelatihan->nama_pelatihan ?? '-' }}</div>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $req->session->judul_sesi ?? '-' }}</div>
                            <small class="text-muted">{{ $req->session->tanggal ? $req->session->tanggal->format('d/m/Y') : '-' }}</small>
                        </td>
                        <td class="text-center">
                            <span class="badge rounded-pill px-3 py-1 fw-bold" style="{{ $jenisBadge }}">
                                {{ $req->jenis_pengajuan }}
                            </span>
                        </td>
                        <td>
                            <div class="text-dark small mb-1">{{ $req->alasan }}</div>
                            @if($req->evidences->isNotEmpty())
                                <div class="d-flex gap-1 flex-wrap">
                                    @foreach($req->evidences as $ev)
                                        <a href="{{ route('attendance-evidences.download', $ev->id) }}" target="_blank" class="badge bg-light text-primary border text-decoration-none">
                                            <i class="bi bi-file-earmark-arrow-down"></i> {{ $ev->nama_file_asli }}
                                        </a>
                                    @endforeach
                                </div>
                            @else
                                <small class="text-muted fst-italic">Tanpa lampiran file</small>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge {{ $statusBadge }} rounded-pill px-3 py-1">
                                {{ $req->status === 'Approved' ? 'Disetujui' : ($req->status === 'Rejected' ? 'Ditolak' : 'Pending') }}
                            </span>
                        </td>
                        <td>
                            @if($req->verifikator)
                                <div class="fw-semibold small text-dark">{{ $req->verifikator->name }}</div>
                                <small class="text-muted">{{ $req->diverifikasi_pada ? $req->diverifikasi_pada->format('d/m/Y H:i') : '-' }}</small>
                                @if($req->catatan_admin)
                                    <div class="text-muted fst-italic small">"{{ $req->catatan_admin }}"</div>
                                @endif
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($req->status === 'Pending')
                                <div class="btn-group btn-group-sm">
                                    <form action="{{ route('attendance-requests.verify', $req->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Setujui pengajuan ketidakhadiran ini? Status presensi peserta akan otomatis diubah.');">
                                        @csrf
                                        <input type="hidden" name="keputusan" value="Approved">
                                        <button type="submit" class="btn btn-success" title="Setujui">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                    </form>
                                    <button type="button" class="btn btn-outline-danger btn-reject-modal" data-id="{{ $req->id }}" data-nama="{{ $req->peserta->nama }}" title="Tolak">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </div>
                            @else
                                <span class="text-muted small fst-italic">Selesai</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">Tidak ada data pengajuan ketidakhadiran.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">
    {{ $requests->links('vendor.pagination.bootstrap-5') }}
</div>

<!-- Modal Tolak Pengajuan -->
<div class="modal fade" id="modalRejectRequest" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formRejectRequest" method="POST">
                @csrf
                <input type="hidden" name="keputusan" value="Rejected">
                <div class="modal-header">
                    <h6 class="modal-title fw-bold text-danger">Tolak Pengajuan: <span id="rejectPesertaNama"></span></h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-secondary">ALASAN PENOLAKAN <span class="text-danger">*</span></label>
                        <textarea name="catatan_admin" class="form-control" rows="3" placeholder="Sebutkan alasan penolakan pengajuan (wajib diisi)..." required></textarea>
                        <small class="text-muted" style="font-size: 0.72rem;">Alasan penolakan akan terlihat oleh peserta di portal mereka.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-danger px-3 fw-bold">Konfirmasi Tolak</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modalReject = new bootstrap.Modal(document.getElementById('modalRejectRequest'));
        const formReject = document.getElementById('formRejectRequest');
        const rejectNama = document.getElementById('rejectPesertaNama');

        document.querySelectorAll('.btn-reject-modal').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const nama = this.getAttribute('data-nama');
                rejectNama.textContent = nama;
                formReject.action = "{{ url('attendance-requests') }}/" + id + "/verify";
                modalReject.show();
            });
        });
    });
</script>
@endsection
