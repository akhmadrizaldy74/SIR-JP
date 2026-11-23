@extends('portal.layout')

@section('title', 'Scan QR Code Presensi')

@section('styles')
<style>
    #reader {
        width: 100%;
        border-radius: 12px;
        overflow: hidden;
        background: #000;
    }
    #reader video {
        border-radius: 12px;
        object-fit: cover;
    }
    .scanner-box {
        position: relative;
        background: #0F172A;
        border-radius: 16px;
        padding: 12px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
    }
</style>
@endsection

@section('content')
<div class="mb-3 d-flex align-items-center justify-content-between">
    <a href="{{ route('portal.dashboard') }}" class="btn btn-sm btn-light border rounded-pill px-3">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
    <h6 class="fw-bold mb-0 text-dark">Scan Presensi Sesi</h6>
    <span style="width: 70px;"></span>
</div>

@if($selectedSession)
<div class="alert alert-info border-info-subtle py-2 px-3 rounded-3 small mb-3">
    <div class="fw-bold text-dark">{{ $selectedSession->judul_sesi }}</div>
    <div class="text-muted">{{ substr($selectedSession->jam_mulai, 0, 5) }} - {{ substr($selectedSession->jam_selesai, 0, 5) }} WIB</div>
</div>
@endif

<!-- Camera Scanner Container -->
<div class="scanner-box text-center mb-3 text-white">
    <div class="mb-2 d-flex justify-content-between align-items-center px-1">
        <small class="text-white-50"><i class="bi bi-camera-fill me-1"></i> Arahkan kamera ke QR Code di layar</small>
        <button id="btnSwitchCamera" class="btn btn-sm btn-outline-light rounded-pill py-0 px-2 small" style="font-size: 0.72rem;">
            <i class="bi bi-arrow-repeat"></i> Ganti Kamera
        </button>
    </div>

    <!-- HTML5 QR Reader Viewport -->
    <div id="reader" style="min-height: 280px;" class="d-flex align-items-center justify-content-center">
        <div id="scannerLoading" class="text-center p-4">
            <div class="spinner-border text-primary mb-2" role="status"></div>
            <div class="small text-white-50">Menyiapkan kamera HP Anda...</div>
        </div>
    </div>

    <!-- Scanner Controls -->
    <div class="mt-3 d-flex justify-content-center gap-2">
        <label for="qrFileInput" class="btn btn-sm btn-secondary rounded-pill px-3 py-1 cursor-pointer">
            <i class="bi bi-image"></i> Scan dari Galeri / Foto
        </label>
        <input type="file" id="qrFileInput" accept="image/*" class="d-none">
    </div>
</div>

<!-- Fallback / Opsi Input Kode Token Manual -->
<div class="card card-custom p-3 border shadow-sm">
    <div class="text-center mb-2">
        <small class="text-muted fw-semibold">Kamera tidak berfungsi atau izin ditolak?</small>
    </div>
    <form id="formManualToken">
        <div class="input-group">
            <input type="text" 
                   id="manualPayloadInput" 
                   class="form-control form-control-sm text-center font-monospace" 
                   placeholder="Tempel / Masukkan Token QR di sini..." 
                   required>
            <button type="submit" class="btn btn-sm btn-primary px-3 fw-bold" id="btnSubmitManual">
                Verifikasi
            </button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script src="{{ asset('assets/js/html5-qrcode.min.js') }}"></script>
<script>
    let html5QrCode = null;
    let currentCameraId = null;
    let cameraList = [];
    let isProcessing = false;

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    function onScanSuccess(decodedText, decodedResult) {
        if (isProcessing) return;
        isProcessing = true;

        // Beri getaran haptic jika didukung HP
        if (navigator.vibrate) {
            navigator.vibrate(100);
        }

        // Tampilkan loading
        Swal.fire({
            title: 'Memvalidasi Presensi...',
            text: 'Mohon tunggu sesaat.',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Kirim ke server
        fetch("{{ route('portal.scan.post') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ qr_payload: decodedText })
        })
        .then(response => response.json().then(data => ({ status: response.status, body: data })))
        .then(({ status, body }) => {
            if (status === 200 && body.success) {
                // Hentikan scanner
                if (html5QrCode && html5QrCode.isScanning) {
                    html5QrCode.stop();
                }

                Swal.fire({
                    icon: 'success',
                    title: 'Presensi Berhasil!',
                    text: body.message,
                    confirmButtonText: 'Kembali ke Beranda',
                    confirmButtonColor: '#2563EB',
                }).then(() => {
                    window.location.href = body.redirect || "{{ route('portal.dashboard') }}";
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Presensi Gagal',
                    text: body.message || 'Terjadi kesalahan validasi QR Code.',
                    confirmButtonText: 'Coba Lagi',
                    confirmButtonColor: '#EF4444',
                }).then(() => {
                    isProcessing = false;
                });
            }
        })
        .catch(err => {
            console.error(err);
            Swal.fire({
                icon: 'error',
                title: 'Koneksi Terganggu',
                text: 'Gagal menghubungi server. Periksa koneksi internet Anda.',
            }).then(() => {
                isProcessing = false;
            });
        });
    }

    // Inisialisasi Scanner Kamera
    document.addEventListener('DOMContentLoaded', function () {
        html5QrCode = new Html5Qrcode("reader");

        Html5Qrcode.getCameras().then(devices => {
            document.getElementById('scannerLoading').style.display = 'none';
            if (devices && devices.length) {
                cameraList = devices;
                // Pilih kamera belakang jika ada
                let backCamera = devices.find(d => d.label.toLowerCase().includes('back') || d.label.toLowerCase().includes('belakang'));
                currentCameraId = backCamera ? backCamera.id : devices[0].id;
                startScanning(currentCameraId);
            } else {
                showCameraError('Tidak ada perangkat kamera yang terdeteksi pada smartphone Anda.');
            }
        }).catch(err => {
            document.getElementById('scannerLoading').style.display = 'none';
            showCameraError('Izin akses kamera ditolak. Silakan aktifkan izin kamera pada browser Anda atau gunakan tombol upload foto QR.');
        });

        // Switch Camera
        document.getElementById('btnSwitchCamera').addEventListener('click', function () {
            if (cameraList.length <= 1) {
                Swal.fire({ icon: 'info', title: 'Info Kamera', text: 'Hanya ada 1 kamera yang terdeteksi.' });
                return;
            }
            if (html5QrCode.isScanning) {
                html5QrCode.stop().then(() => {
                    let currentIndex = cameraList.findIndex(d => d.id === currentCameraId);
                    let nextIndex = (currentIndex + 1) % cameraList.length;
                    currentCameraId = cameraList[nextIndex].id;
                    startScanning(currentCameraId);
                });
            }
        });

        // Scan from Image file input
        document.getElementById('qrFileInput').addEventListener('change', function (e) {
            if (e.target.files.length == 0) return;
            const imageFile = e.target.files[0];
            html5QrCode.scanFile(imageFile, true)
                .then(decodedText => {
                    onScanSuccess(decodedText, null);
                })
                .catch(err => {
                    Swal.fire({ icon: 'error', title: 'QR Tidak Terbaca', text: 'QR Code tidak terdeteksi pada gambar yang diunggah. Pastikan gambar jelas dan terang.' });
                });
        });

        // Form Manual Token Submit
        document.getElementById('formManualToken').addEventListener('submit', function (e) {
            e.preventDefault();
            const val = document.getElementById('manualPayloadInput').value.trim();
            if (val) {
                onScanSuccess(val, null);
            }
        });
    });

    function startScanning(cameraId) {
        html5QrCode.start(
            cameraId, 
            {
                fps: 10,
                qrbox: { width: 220, height: 220 }
            },
            onScanSuccess,
            (errorMessage) => {
                // Abaikan frame scan gagal biasa
            }
        ).catch(err => {
            showCameraError('Gagal memulai kamera: ' + err);
        });
    }

    function showCameraError(msg) {
        document.getElementById('reader').innerHTML = `
            <div class="p-4 text-center text-white-50">
                <i class="bi bi-camera-video-off fs-2 text-danger mb-2"></i>
                <div class="small">${msg}</div>
            </div>
        `;
    }
</script>
@endsection
