<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Masuk') - SIR-JP & KCLC Kemnaker</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('assets/images/logo-kclc.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/images/apple-touch-icon.png') }}">

    <!-- Bootstrap 5 CSS & Bootstrap Icons -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-icons.min.css') }}">

    <style>
        :root {
            --primary-blue: #0284C7;
            --primary-dark: #0B1727;
        }

        body {
            /* Background gradien mendalam terinspirasi dari template */
            background: radial-gradient(circle at 85% 20%, #0284C7 0%, #0369A1 25%, #0B1727 65%, #050B14 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            color: #334155;
            position: relative;
            overflow-x: hidden;
        }

        /* Efek pencahayaan ambient pada latar belakang */
        body::before {
            content: '';
            position: absolute;
            width: 650px;
            height: 650px;
            background: radial-gradient(circle, rgba(2, 132, 199, 0.22) 0%, rgba(2, 132, 199, 0) 70%);
            top: -120px;
            right: -120px;
            border-radius: 50%;
            pointer-events: none;
        }

        body::after {
            content: '';
            position: absolute;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.12) 0%, rgba(16, 185, 129, 0) 70%);
            bottom: -80px;
            left: -80px;
            border-radius: 50%;
            pointer-events: none;
        }

        /* Container Card Split 2 Kolom Sesuai Template */
        .auth-container-card {
            background: #FFFFFF;
            border-radius: 20px;
            box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.6), 0 0 50px rgba(2, 132, 199, 0.18);
            max-width: 1000px;
            width: 100%;
            overflow: hidden;
            display: flex;
            position: relative;
            z-index: 10;
        }

        /* Kolom Kiri: Visual Banner Night Sky Landscape dari Template */
        .auth-banner-side {
            width: 48%;
            position: relative;
            background-color: #06112C;
            background-image: url("{{ asset('assets/images/auth-banner.jpg') }}");
            background-size: cover;
            background-position: center;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 2.75rem 2.25rem;
            color: #FFFFFF;
        }

        .auth-banner-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(6, 17, 44, 0.65) 0%, rgba(6, 17, 44, 0.25) 50%, rgba(6, 17, 44, 0.9) 100%);
            z-index: 1;
        }

        .auth-banner-content {
            position: relative;
            z-index: 2;
        }

        /* Kolom Kanan: Form Putih Bersih */
        .auth-form-side {
            width: 52%;
            background: #FFFFFF;
            padding: 3rem 2.75rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .auth-greeting-title {
            font-size: 2.3rem;
            font-weight: 800;
            color: #0284C7;
            letter-spacing: -0.5px;
            line-height: 1.1;
            margin-bottom: 0.4rem;
        }

        .auth-greeting-subtitle {
            color: #94A3B8;
            font-size: 0.9rem;
            margin-bottom: 1.85rem;
            line-height: 1.5;
        }

        /* Input styling presisi template */
        .auth-input-group {
            background-color: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 8px;
            transition: all 0.2s ease;
            overflow: hidden;
        }

        .auth-input-group:focus-within {
            background-color: #FFFFFF;
            border-color: #0284C7;
            box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15);
        }

        .auth-input-group .input-group-text {
            background: transparent;
            border: none;
            color: #94A3B8;
            padding-left: 1rem;
            padding-right: 0.5rem;
        }

        .auth-input-group .form-control {
            background: transparent;
            border: none;
            font-size: 0.925rem;
            padding: 0.75rem 1rem 0.75rem 0.25rem;
            color: #1E293B;
        }

        .auth-input-group .form-control:focus {
            box-shadow: none;
            background: transparent;
        }

        .auth-input-group .form-control::placeholder {
            color: #94A3B8;
            font-size: 0.88rem;
        }

        /* Tombol NEXT Gradien Biru Sesuai Template */
        .btn-auth-next {
            background: linear-gradient(90deg, #0B1727 0%, #0284C7 100%);
            border: none;
            color: #FFFFFF;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            padding: 0.8rem 1.75rem;
            border-radius: 8px;
            font-size: 0.95rem;
            transition: all 0.25s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 4px 15px rgba(2, 132, 199, 0.3);
            text-decoration: none;
        }

        .btn-auth-next:hover {
            background: linear-gradient(90deg, #0284C7 0%, #0369A1 100%);
            transform: translateY(-1px);
            box-shadow: 0 8px 25px rgba(2, 132, 199, 0.45);
            color: #FFFFFF;
        }

        /* Responsif pada Mobile / Layar Kecil */
        @media (max-width: 860px) {
            .auth-container-card {
                flex-direction: column;
                max-width: 480px;
            }
            .auth-banner-side {
                width: 100%;
                min-height: 220px;
                padding: 1.75rem 1.5rem;
            }
            .auth-form-side {
                width: 100%;
                padding: 2.25rem 1.5rem;
            }
            .auth-greeting-title {
                font-size: 1.85rem;
            }
        }
    </style>
    @yield('styles')
</head>
<body>

    <div class="auth-container-card">
        <!-- Kolom Kiri: Visual Banner Night Landscape Template + Brand KCLC & SIR-JP -->
        <div class="auth-banner-side">
            <div class="auth-banner-overlay"></div>

            <!-- Top Brand Info -->
            <div class="auth-banner-content">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <img src="{{ asset('assets/images/logo-kclc.png') }}" alt="Logo Kemnaker Corpu" style="height: 44px; width: 44px; object-fit: contain;">
                    <div>
                        <div class="fw-bold text-white lh-1" style="font-size: 1.05rem; letter-spacing: 0.5px;">KEMNAKER CORPU</div>
                        <span class="text-white-50" style="font-size: 0.7rem;">PPSDM Ketenagakerjaan RI</span>
                    </div>
                </div>
            </div>

            <!-- Bottom Motto & Info -->
            <div class="auth-banner-content">
                <span class="badge rounded-pill px-3 py-1 mb-2" style="background: rgba(2, 132, 199, 0.35); border: 1px solid rgba(56, 189, 248, 0.5); color: #BAE6FD; font-size: 0.75rem;">
                    <i class="bi bi-shield-check me-1"></i> Sistem Terintegrasi
                </span>
                <h4 class="fw-bold text-white mb-2" style="font-size: 1.35rem; line-height: 1.3;">
                    SIR-JP & KCLC Learning Center
                </h4>
                <p class="text-white-50 small mb-0" style="line-height: 1.5;">
                    Platform digital terpadu penyelenggaraan diklat, presensi harian, dan perhitungan Jam Pelajaran (JP) Widyaiswara.
                </p>
            </div>
        </div>

        <!-- Kolom Kanan: Panel Form Bersih & Modern -->
        <div class="auth-form-side">
            @yield('content')
        </div>
    </div>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/sweetalert2.all.min.js') }}"></script>
    @yield('scripts')
</body>
</html>
