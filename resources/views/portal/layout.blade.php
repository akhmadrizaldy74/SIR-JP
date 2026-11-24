<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard Diklat') - KCLC Learning Center</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('assets/images/logo-kclc.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/images/apple-touch-icon.png') }}">

    <!-- Bootstrap 5 CSS & Bootstrap Icons -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-icons.min.css') }}">
    <!-- Custom Enterprise CSS Unified (Model Admin) -->
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}?v={{ time() }}">

    <style>
        /* Floating Pen Action Button di Pojok Kiri Bawah Sesuai Gambar */
        .btn-floating-pen {
            position: fixed;
            bottom: 24px;
            left: 24px;
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: #0B1727;
            border: 2px solid #10B981;
            color: #10B981;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1035;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            text-decoration: none;
            transition: transform 0.2s ease, background 0.2s ease;
        }

        .btn-floating-pen:hover {
            transform: scale(1.05);
            background: #10B981;
            color: #FFFFFF;
        }

        .kclc-card {
            background-color: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 10px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }
    </style>
    @yield('styles')
</head>
<body>

    @if(session()->has('participant_id') || auth()->check())
    <div class="app-wrapper">
        <!-- Sidebar Unified KCLC Sesuai Model Admin -->
        @include('partials.sidebar')
        <div id="sidebarBackdrop" class="sidebar-backdrop"></div>

        <!-- Floating Green Edit/Pen Icon (Pojok Kiri Bawah Sesuai Gambar) -->
        <a href="{{ route('portal.dashboard') }}" class="btn-floating-pen" title="Menu Cepat Presensi">
            <i class="bi bi-pencil fs-5"></i>
        </a>

        <!-- Main Workspace Sesuai Model Admin (.app-main) -->
        <div class="app-main">
            <!-- Top Navbar Sesuai Model Admin & KCLC -->
            <header class="app-navbar d-flex align-items-center justify-content-between px-3 bg-white border-bottom" style="height: 64px;">
                <!-- Left: Hamburger Menu -->
                <div class="d-flex align-items-center gap-3">
                    <button type="button" class="navbar-toggle-btn btn btn-light border-0 p-1" id="sidebarToggleBtn" title="Buka/Tutup Sidebar" style="font-size: 1.4rem;">
                        <i class="bi bi-list"></i>
                    </button>
                </div>

                <!-- Right: Fullscreen, Bell, Profile Sesuai Gambar KCLC -->
                <div class="d-flex align-items-center gap-3">
                    <button type="button" class="btn btn-link text-secondary p-1" onclick="document.fullscreenElement ? document.exitFullscreen() : document.documentElement.requestFullscreen()" title="Fullscreen">
                        <i class="bi bi-fullscreen fs-5"></i>
                    </button>
                    <!-- Notification Bell Dinamis Berbasis Peran Peserta -->
                    @php
                        $notifService = app(\App\Services\NotificationService::class);
                        $portalNotifications = $notifService->getNotificationsForUser(auth()->user());
                        $portalUnreadCount = $notifService->getUnreadCount(auth()->user());
                    @endphp
                    <div class="dropdown">
                        <button type="button" class="btn btn-link text-secondary p-1 position-relative" data-bs-toggle="dropdown" aria-expanded="false" title="Notifikasi ({{ $portalUnreadCount }} baru)">
                            <i class="bi bi-bell fs-5"></i>
                            @if($portalUnreadCount > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem; padding: 0.25em 0.45em;">
                                    {{ $portalUnreadCount > 9 ? '9+' : $portalUnreadCount }}
                                </span>
                            @endif
                        </button>
                        <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 py-0 mt-2" style="width: 360px; max-width: 90vw; border-radius: 12px; overflow: hidden; z-index: 1060;">
                            <div class="d-flex align-items-center justify-content-between px-3 py-2.5 text-white" style="background-color: #0B1727;">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-bell-fill text-warning"></i>
                                    <span class="fw-bold small">Notifikasi Peserta</span>
                                    @if($portalUnreadCount > 0)
                                        <span class="badge bg-danger rounded-pill px-2" style="font-size: 0.7rem;">{{ $portalUnreadCount }} Baru</span>
                                    @endif
                                </div>
                                @if(count($portalNotifications) > 0)
                                    <form action="{{ route('notifications.mark-all-read') }}" method="POST" class="m-0">
                                        @csrf
                                        <button type="submit" class="btn btn-link text-white-50 p-0 text-decoration-none small" style="font-size: 0.75rem;" title="Tandai semua telah dibaca">
                                            <i class="bi bi-check2-all"></i> Tandai Dibaca
                                        </button>
                                    </form>
                                @endif
                            </div>

                            <div class="list-group list-group-flush" style="max-height: 380px; overflow-y: auto;">
                                @forelse($portalNotifications as $notif)
                                    <a href="{{ route('notifications.read', ['id' => $notif['id'], 'redirect' => $notif['url']]) }}" 
                                       class="list-group-item list-group-item-action d-flex align-items-start gap-2.5 py-2.5 px-3 border-bottom transition-all">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 mt-1" 
                                             style="width: 36px; height: 36px; background-color: {{ $notif['bg_color'] }}; color: {{ $notif['text_color'] }}; font-size: 1rem;">
                                            <i class="bi {{ $notif['icon'] }}"></i>
                                        </div>
                                        <div class="flex-grow-1 overflow-hidden">
                                            <div class="d-flex align-items-center justify-content-between gap-1 mb-1">
                                                <strong class="text-dark small text-truncate" style="font-size: 0.825rem;">{{ $notif['title'] }}</strong>
                                                <span class="badge rounded-pill px-2 py-0.5 text-nowrap" style="font-size: 0.65rem; background-color: {{ $notif['bg_color'] }}; color: {{ $notif['text_color'] }};">
                                                    {{ $notif['badge'] }}
                                                </span>
                                            </div>
                                            <p class="text-muted small mb-1 lh-sm" style="font-size: 0.785rem;">
                                                {{ $notif['message'] }}
                                            </p>
                                            <span class="text-secondary" style="font-size: 0.7rem;">
                                                <i class="bi bi-clock me-1"></i>{{ $notif['time_formatted'] }}
                                            </span>
                                        </div>
                                    </a>
                                @empty
                                    <div class="text-center py-4 px-3 text-muted">
                                        <i class="bi bi-bell-slash fs-3 d-block mb-2 text-secondary opacity-50"></i>
                                        <div class="small fw-semibold">Tidak ada notifikasi baru</div>
                                        <span class="small" style="font-size: 0.75rem;">Seluruh informasi presensi & tugas up to date.</span>
                                    </div>
                                @endforelse
                            </div>

                            @if(count($portalNotifications) > 0)
                                <div class="bg-light px-3 py-2 text-center border-top">
                                    <span class="text-muted small" style="font-size: 0.75rem;">Notifikasi diperbarui otomatis untuk aktivitas pelatihan Anda</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Profile User Sesuai Model Admin & Role Switcher -->
                    @php
                        $portalAuthUser = auth()->user();
                        $portalRoles = $portalAuthUser ? $portalAuthUser->getAvailableRoles() : [];
                        $hasMultiPortalRoles = count($portalRoles) > 1;
                    @endphp
                    <div class="dropdown d-flex align-items-center">
                        <button class="btn btn-link text-decoration-none d-flex align-items-center gap-2 p-0 text-dark dropdown-toggle" 
                                type="button" 
                                data-bs-toggle="dropdown" 
                                aria-expanded="false">
                            <div class="avatar-circle rounded-circle bg-light border d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                <i class="bi bi-person-fill text-secondary fs-5"></i>
                            </div>
                            <div class="text-start d-none d-sm-block">
                                <div class="fw-bold text-dark lh-1" style="font-size: 0.85rem;">{{ $peserta->nama ?? ($portalAuthUser ? $portalAuthUser->name : 'Peserta Diklat') }}</div>
                                <span class="text-muted" style="font-size: 0.72rem;">peserta</span>
                            </div>
                        </button>
                        @if($hasMultiPortalRoles)
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border py-2" style="min-width: 220px; border-radius: 8px;">
                            @foreach($portalRoles as $roleOpt)
                                @php
                                    $isCurrentPeserta = strcasecmp('Peserta', $roleOpt) === 0 || strcasecmp('Peserta Diklat', $roleOpt) === 0;
                                @endphp
                                <li>
                                    <a class="dropdown-item d-flex align-items-center justify-content-between py-2 px-3 {{ $isCurrentPeserta ? 'fw-bold text-dark' : 'text-secondary' }}" 
                                       href="{{ route('switch-role', ['role' => $roleOpt]) }}">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi bi-person text-secondary"></i>
                                            <span>{{ $roleOpt }}</span>
                                        </div>
                                        @if($isCurrentPeserta)
                                            <span class="badge" style="background-color: #DCFCE7; color: #16A34A; font-size: 0.68rem; font-weight: 600; padding: 3px 8px; border-radius: 4px;">Active</span>
                                        @endif
                                    </a>
                                </li>
                            @endforeach
                            <li><hr class="dropdown-divider my-1"></li>
                            <li>
                                <form action="{{ route('portal.logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item py-2 px-3 text-danger d-flex align-items-center gap-2 border-0 bg-transparent w-100">
                                        <i class="bi bi-box-arrow-right"></i> Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                        @endif
                    </div>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="page-container p-4">
                <!-- Header Title & Breadcrumb Sesuai Gambar: DASHBOARD DIKLAT & home_diklat > Dashboard Diklat -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold text-dark mb-0 text-uppercase" style="letter-spacing: 0.5px;">@yield('page_title', 'DASHBOARD DIKLAT')</h5>
                    <div class="text-muted small" style="font-size: 0.8rem;">
                        @hasSection('custom_breadcrumb')
                            @yield('custom_breadcrumb')
                        @else
                            <span class="text-secondary">home_diklat</span> &gt; <span class="fw-semibold text-dark">@yield('breadcrumb', 'Dashboard Diklat')</span>
                        @endif
                    </div>
                </div>

                <!-- Alert Notifikasi -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm border-0" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="alert alert-warning alert-dismissible fade show rounded-3 shadow-sm border-0" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('warning') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm border-0" role="alert">
                        <i class="bi bi-x-circle-fill me-2"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
    @else
    <!-- Guest Layout untuk Halaman Login Peserta -->
    <main class="container my-5">
        @yield('content')
    </main>
    @endif

    <!-- Scripts -->
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/sweetalert2.all.min.js') }}"></script>
    <!-- Custom Vanilla App JS Unified (Model Admin) -->
    <script src="{{ asset('assets/js/app.js') }}?v={{ time() }}"></script>
    @yield('scripts')
</body>
</html>
