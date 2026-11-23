<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Portal Presensi Peserta') — KCLC PPSDMK</title>

    <!-- Bootstrap 5 CSS & Bootstrap Icons -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-icons.min.css') }}">

    <style>
        :root {
            --primary-color: #2563EB;
            --primary-dark: #1D4ED8;
            --accent-color: #10B981;
            --bg-color: #F8FAFC;
        }

        body {
            background-color: var(--bg-color);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            color: #1E293B;
            padding-bottom: 70px;
        }

        .portal-header {
            background: linear-gradient(135deg, #1E293B 0%, #0F172A 100%);
            color: #FFFFFF;
            padding: 1.25rem 1rem;
            border-bottom-left-radius: 20px;
            border-bottom-right-radius: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .portal-bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: #FFFFFF;
            border-top: 1px solid #E2E8F0;
            display: flex;
            justify-content: space-around;
            padding: 0.5rem 0;
            z-index: 1040;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.05);
        }

        .portal-nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            font-size: 0.75rem;
            color: #64748B;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .portal-nav-item i {
            font-size: 1.25rem;
            margin-bottom: 2px;
        }

        .portal-nav-item.active {
            color: var(--primary-color);
            font-weight: 600;
        }

        .card-custom {
            border: 1px solid #E2E8F0;
            border-radius: 14px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            background: #FFFFFF;
        }

        .badge-hadir { background-color: #D1FAE5; color: #065F46; }
        .badge-terlambat { background-color: #DBEAFE; color: #1E40AF; }
        .badge-izin { background-color: #FEF3C7; color: #92400E; }
        .badge-sakit { background-color: #FCE7F3; color: #9D174D; }
        .badge-alpa { background-color: #FEE2E2; color: #991B1B; }
        .badge-dispensasi { background-color: #EDE9FE; color: #5B21B6; }

        .btn-scan-floating {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background: linear-gradient(135deg, #2563EB 0%, #10B981 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: -24px;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.4);
            border: 3px solid white;
        }

        .btn-scan-floating i {
            font-size: 1.5rem;
        }
    </style>
    @yield('styles')
</head>
<body>

    @if(session()->has('participant_id'))
    <!-- Portal Header -->
    <header class="portal-header">
        <div class="container-fluid max-w-md d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <span class="badge fw-bold px-2 py-1 text-white" style="background: linear-gradient(135deg, #F59E0B 0%, #10B981 100%); font-size: 1rem; border-radius: 6px;">KC</span>
                <div>
                    <h6 class="mb-0 fw-bold text-white" style="letter-spacing: 0.5px;">KCLC PRESENSI</h6>
                    <small class="text-white-50" style="font-size: 0.7rem;">PPSDM Ketenagakerjaan</small>
                </div>
            </div>
            <div>
                <form action="{{ route('portal.logout') }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin keluar dari portal?');">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-light rounded-pill px-2 py-1" style="font-size: 0.75rem;">
                        <i class="bi bi-box-arrow-right"></i> Keluar
                    </button>
                </form>
            </div>
        </div>
    </header>
    @endif

    <!-- Main Container -->
    <main class="container my-3" style="max-width: 600px;">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show rounded-3 shadow-sm" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm" role="alert">
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>

    @if(session()->has('participant_id'))
    <!-- Bottom Navigation Bar for Mobile -->
    <nav class="portal-bottom-nav">
        <a href="{{ route('portal.dashboard') }}" class="portal-nav-item {{ request()->routeIs('portal.dashboard') ? 'active' : '' }}">
            <i class="bi bi-house-door-fill"></i>
            <span>Beranda</span>
        </a>
        <a href="{{ route('portal.riwayat') }}" class="portal-nav-item {{ request()->routeIs('portal.riwayat') ? 'active' : '' }}">
            <i class="bi bi-calendar-check-fill"></i>
            <span>Riwayat</span>
        </a>
        <a href="{{ route('portal.scan') }}" class="portal-nav-item">
            <div class="btn-scan-floating">
                <i class="bi bi-qr-code-scan"></i>
            </div>
            <span class="mt-1 fw-bold text-primary">Scan QR</span>
        </a>
        <a href="{{ route('portal.pengajuan') }}" class="portal-nav-item {{ request()->routeIs('portal.pengajuan') ? 'active' : '' }}">
            <i class="bi bi-file-earmark-medical-fill"></i>
            <span>Izin/Sakit</span>
        </a>
    </nav>
    @endif

    <!-- Scripts -->
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/sweetalert2.all.min.js') }}"></script>
    @yield('scripts')
</body>
</html>
