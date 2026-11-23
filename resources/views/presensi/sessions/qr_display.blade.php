<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Layar Presensi QR — {{ $attendanceSession->judul_sesi }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('assets/images/logo-kclc.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/images/apple-touch-icon.png') }}">

    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-icons.min.css') }}">

    <style>
        body {
            background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%);
            color: #F8FAFC;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        }

        .qr-card-container {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 24px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
            padding: 2.5rem;
        }

        #qrContainer {
            background: #FFFFFF;
            padding: 16px;
            border-radius: 16px;
            display: inline-flex;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .progress-bar-animated-custom {
            height: 6px;
            border-radius: 3px;
            background: linear-gradient(90deg, #10B981 0%, #3B82F6 100%);
            transition: width 1s linear;
        }
    </style>
</head>
<body>
    <!-- Top Header Bar -->
    <header class="py-3 px-4 d-flex justify-content-between align-items-center border-bottom border-secondary border-opacity-25">
        <div class="d-flex align-items-center gap-3">
            <img src="{{ asset('assets/images/logo-kclc.png') }}" alt="Logo KC" style="height: 48px; width: 48px; object-fit: contain;">
            <div>
                <h5 class="mb-0 fw-bold text-white letter-spacing-1">KEMNAKER CORPU LEARNING CENTER</h5>
                <small class="text-white-50">Sistem Presensi Digital Peserta Diklat PPSDMK</small>
            </div>
        </div>
        <div class="d-flex align-items-center gap-3">
            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-2">
                <span class="spinner-grow spinner-grow-sm me-1" style="width: 0.5rem; height: 0.5rem;"></span> Presensi Terbuka
            </span>
            <button id="btnFullscreen" class="btn btn-outline-light rounded-pill btn-sm px-3">
                <i class="bi bi-arrows-fullscreen"></i> Layar Penuh
            </button>
            <a href="{{ route('attendance-sessions.show', $attendanceSession->id) }}" class="btn btn-outline-secondary rounded-pill btn-sm px-3 text-white">
                <i class="bi bi-x-lg"></i> Tutup
            </a>
        </div>
    </header>

    <!-- Main QR Display Area -->
    <main class="flex-grow-1 d-flex align-items-center justify-content-center p-4">
        <div class="container text-center" style="max-width: 780px;">
            <!-- Session Title -->
            <div class="mb-4">
                <h2 class="fw-bold text-white mb-2">{{ $attendanceSession->judul_sesi }}</h2>
                <p class="text-white-50 fs-6 mb-0">
                    Mata Pelatihan: <strong class="text-white">{{ $attendanceSession->jadwal->mataPelatihan->nama_mata_pelatihan ?? '-' }}</strong> &bull;
                    Kelas: <strong class="text-white">{{ $attendanceSession->kelasMapel->nama_kelas ?? '-' }}</strong> &bull;
                    Ruangan: <strong class="text-white">{{ $attendanceSession->kelasMapel->ruangan->nama_ruangan ?? ($attendanceSession->jadwal->ruangan ?? '-') }}</strong>
                </p>
                <p class="text-white-50 small">
                    Pukul: <strong class="text-white">{{ substr($attendanceSession->jam_mulai, 0, 5) }} - {{ substr($attendanceSession->jam_selesai, 0, 5) }} WIB</strong>
                </p>
            </div>

            <!-- QR Box Card -->
            <div class="qr-card-container">
                <div class="mb-3">
                    <span class="text-white-50 small text-uppercase letter-spacing-1">
                        Arahkan Kamera HP ke QR Code di bawah untuk Presensi Sesi Ini
                    </span>
                </div>

                <!-- QR Code Display -->
                <div id="qrContainer" class="mb-3">
                    <div id="qrcode"></div>
                </div>

                <!-- Countdown Timer & Progress Bar -->
                <div class="mx-auto" style="max-width: 380px;">
                    <div class="d-flex justify-content-between align-items-center small text-white-50 mb-1">
                        <span><i class="bi bi-shield-check text-success me-1"></i> QR Code Dinamis Terenkripsi</span>
                        <span>Rotasi: <strong id="countdownText" class="text-white">45</strong>s</span>
                    </div>
                    <div class="progress bg-secondary bg-opacity-25" style="height: 6px;">
                        <div id="progressBar" class="progress-bar-animated-custom" style="width: 100%;"></div>
                    </div>
                </div>
            </div>

            <!-- Petunjuk Peserta -->
            @php
                $lanHost = request()->getHost();
                if (in_array($lanHost, ['localhost', '127.0.0.1', '::1'])) {
                    $ip = gethostbyname(gethostname());
                    if ($ip && filter_var($ip, FILTER_VALIDATE_IP) && $ip !== '127.0.0.1') {
                        $lanHost = $ip;
                    }
                }
                $lanPort = request()->getPort();
                $portStr = ($lanPort && !in_array($lanPort, [80, 443])) ? ':' . $lanPort : '';
                $phoneUrl = request()->getScheme() . '://' . $lanHost . $portStr . '/presensi';
            @endphp
            <div class="mt-4 p-3 rounded-3 text-start mx-auto" style="background: rgba(255, 255, 255, 0.07); border: 1px solid rgba(255, 255, 255, 0.15); max-width: 580px;">
                <div class="fw-bold text-white mb-2 text-center">
                    <i class="bi bi-phone me-1 text-warning"></i> Panduan Presensi Peserta Lewat HP
                </div>
                <div class="small text-white-50 mb-2">
                    <strong class="text-white">Cara 1 (Paling Cepat):</strong> Buka <strong>Kamera bawaan HP / Google Lens</strong> Anda &rarr; Arahkan ke QR Code di atas &rarr; Ketuk tautan link yang muncul di layar HP.
                </div>
                <div class="small text-white-50">
                    <strong class="text-white">Cara 2 (Lewat Portal):</strong> Buka browser di HP Anda (pastikan terhubung ke Wi-Fi yang sama), ketik:
                    <div class="my-1 text-center">
                        <span class="badge bg-dark bg-opacity-75 text-warning font-monospace fs-6 px-3 py-2 border border-warning border-opacity-50 user-select-all">{{ $phoneUrl }}</span>
                    </div>
                    Masuk dengan <strong>Kode Peserta</strong> & <strong>PIN</strong> Anda, lalu tekan tombol menu <strong>Scan QR</strong>.
                </div>
            </div>
        </div>
    </main>

    <!-- Scripts -->
    <script src="{{ asset('assets/js/qrcode.min.js') }}"></script>
    <script>
        let qrcode = null;
        let totalTtl = 45;
        let remainingTime = 45;
        let countdownInterval = null;

        const tokenUrl = "{{ route('attendance-sessions.qr_token', $attendanceSession->id) }}";

        function renderQr(payloadText) {
            const qrElement = document.getElementById("qrcode");
            qrElement.innerHTML = "";
            qrcode = new QRCode(qrElement, {
                text: payloadText,
                width: 260,
                height: 260,
                colorDark: "#0F172A",
                colorLight: "#FFFFFF",
                correctLevel: QRCode.CorrectLevel.M
            });
        }

        function fetchNewToken() {
            fetch(tokenUrl, {
                headers: { 'Accept': 'application/json' }
            })
            .then(res => res.json())
            .then(data => {
                if (data.active && data.payload) {
                    renderQr(data.payload);
                    totalTtl = data.ttl || 45;
                    remainingTime = totalTtl;
                    updateProgress();
                }
            })
            .catch(err => console.error("Gagal memperbarui token QR:", err));
        }

        function updateProgress() {
            const pct = (remainingTime / totalTtl) * 100;
            document.getElementById('progressBar').style.width = pct + '%';
            document.getElementById('countdownText').textContent = remainingTime;
        }

        // Mulai rotasi awal
        document.addEventListener('DOMContentLoaded', function() {
            renderQr(@json($tokenData['payload']));
            totalTtl = {{ $tokenData['ttl'] }};
            remainingTime = totalTtl;

            countdownInterval = setInterval(() => {
                remainingTime--;
                if (remainingTime <= 0) {
                    fetchNewToken();
                } else {
                    updateProgress();
                }
            }, 1000);
        });

        // Fullscreen Toggle
        document.getElementById('btnFullscreen').addEventListener('click', function() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(err => {});
            } else {
                document.exitFullscreen();
            }
        });
    </script>
</body>
</html>
