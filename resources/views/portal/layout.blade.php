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

    <style>
        :root {
            --sidebar-width: 240px;
            --topbar-height: 60px;
            --sidebar-bg: #0B1727;
            --body-bg: #F4F6F9;
        }

        body {
            background-color: var(--body-bg);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            color: #334155;
            min-height: 100vh;
        }

        /* Sidebar KCLC Presisi Gambar */
        .portal-sidebar {
            width: var(--sidebar-width);
            background-color: var(--sidebar-bg);
            color: #94A3B8;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 1030;
            transition: transform 0.25s ease-in-out;
            display: flex;
            flex-direction: column;
        }

        .portal-sidebar-brand {
            padding: 1.25rem 1rem;
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .portal-sidebar-brand-text {
            line-height: 1.1;
        }

        .portal-sidebar-brand-title {
            color: #FFFFFF;
            font-weight: 800;
            font-size: 0.95rem;
            letter-spacing: 0.5px;
        }

        .portal-sidebar-brand-subtitle {
            color: #64748B;
            font-size: 0.65rem;
        }

        .sidebar-section-label {
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #64748B;
            padding: 1.25rem 1.25rem 0.5rem 1.25rem;
        }

        .portal-sidebar-nav {
            list-style: none;
            padding: 0 0.75rem;
            margin: 0;
        }

        .portal-sidebar-nav .nav-link-custom {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0.65rem 0.85rem;
            color: #94A3B8;
            border-radius: 8px;
            text-decoration: none;
            font-size: 0.88rem;
            font-weight: 500;
            transition: all 0.15s ease;
        }

        .portal-sidebar-nav .nav-link-custom:hover {
            color: #FFFFFF;
            background: rgba(255, 255, 255, 0.05);
        }

        .portal-sidebar-nav .nav-link-custom.active {
            color: #38BDF8;
            background: rgba(56, 189, 248, 0.1);
            font-weight: 600;
        }

        .portal-sidebar-nav .nav-link-custom i {
            font-size: 1.15rem;
        }

        /* Floating Pen Action Button di Sidebar Kiri Bawah Sesuai Gambar */
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

        /* Main Workspace Wrapper */
        .portal-wrapper {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: margin-left 0.25s ease-in-out;
        }

        /* Top Navbar */
        .portal-topbar {
            height: var(--topbar-height);
            background: #FFFFFF;
            border-bottom: 1px solid #E2E8F0;
            padding: 0 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 1020;
        }

        /* Page Content Area */
        .portal-content {
            padding: 1.5rem;
            flex-grow: 1;
        }

        /* Responsive Mobile Behavior */
        @media (max-width: 991.98px) {
            .portal-sidebar {
                transform: translateX(-100%);
            }
            .portal-sidebar.show {
                transform: translateX(0);
            }
            .portal-wrapper {
                margin-left: 0;
            }
            .sidebar-backdrop {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.5);
                z-index: 1025;
            }
            .sidebar-backdrop.show {
                display: block;
            }
        }
    </style>
    @yield('styles')
</head>
<body>

    @if(session()->has('participant_id'))
    <!-- Sidebar KCLC Sesuai Gambar -->
    <aside class="portal-sidebar" id="portalSidebar">
        <!-- Logo Brand -->
        <a href="{{ route('portal.dashboard') }}" class="portal-sidebar-brand">
            <img src="{{ asset('assets/images/logo-kclc.png') }}" alt="Logo Kemnaker Corpu" style="height: 38px; width: 38px; object-fit: contain;">
            <div class="portal-sidebar-brand-text">
                <div class="portal-sidebar-brand-title">KEMNAKER CORPU</div>
                <div class="portal-sidebar-brand-subtitle">Lebih Cerdas Lebih Unggul</div>
            </div>
        </a>

        <!-- Section MENU -->
        <div class="sidebar-section-label">MENU</div>
        @php
            $allowedModules = json_decode(\App\Models\Pengaturan::get('participant_modules', '["presensi","tugas","materi"]'), true) ?? ['presensi', 'tugas'];
        @endphp
        <ul class="portal-sidebar-nav flex-column mb-auto">
            <li class="nav-item">
                <a class="nav-link-custom {{ request()->routeIs('portal.dashboard') && !request()->routeIs('portal.tugas.*') ? 'active' : '' }}" href="{{ route('portal.dashboard') }}">
                    <i class="bi bi-grid-fill"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            @if(in_array('presensi', $allowedModules))
            <li class="nav-item">
                <a class="nav-link-custom" href="{{ route('portal.dashboard') }}#areaJadwalSesi">
                    <i class="bi bi-calendar-check"></i>
                    <span>Presensi</span>
                </a>
            </li>
            @endif

            @if(in_array('tugas', $allowedModules))
            <li class="nav-item">
                <a class="nav-link-custom {{ request()->routeIs('portal.tugas.*') ? 'active' : '' }}" href="{{ route('portal.tugas.index') }}">
                    <i class="bi bi-check-square"></i>
                    <span>Tugas</span>
                </a>
            </li>
            @endif

            @if(in_array('materi', $allowedModules))
            <li class="nav-item">
                <a class="nav-link-custom" href="javascript:void(0)" onclick="Swal.fire('Materi Pembelajaran', 'Materi dan modul pembelajaran siap diakses pada menu aktifitas.', 'info');">
                    <i class="bi bi-journal-text"></i>
                    <span>Materi</span>
                </a>
            </li>
            @endif

            @if(in_array('pretest', $allowedModules))
            <li class="nav-item">
                <a class="nav-link-custom" href="javascript:void(0)" onclick="Swal.fire('Pre Test', 'Pre Test telah diaktifkan oleh panitia penyelenggara.', 'info');">
                    <i class="bi bi-question-circle"></i>
                    <span>Pre Test</span>
                </a>
            </li>
            @endif

            @if(in_array('kuis', $allowedModules))
            <li class="nav-item">
                <a class="nav-link-custom" href="javascript:void(0)" onclick="Swal.fire('Kuis', 'Kuis dibuka sesuai jadwal mata pelatihan.', 'info');">
                    <i class="bi bi-chat-left-dots"></i>
                    <span>Kuis</span>
                </a>
            </li>
            @endif

            @if(in_array('postest', $allowedModules))
            <li class="nav-item">
                <a class="nav-link-custom" href="javascript:void(0)" onclick="Swal.fire('Pos Test', 'Pos Test telah dibuka oleh panitia penyelenggara.', 'info');">
                    <i class="bi bi-file-earmark-check"></i>
                    <span>Pos Test</span>
                </a>
            </li>
            @endif

            @if(in_array('ujian', $allowedModules))
            <li class="nav-item">
                <a class="nav-link-custom" href="javascript:void(0)" onclick="Swal.fire('Ujian', 'Ujian evaluasi diklat telah diaktifkan.', 'info');">
                    <i class="bi bi-pencil-square"></i>
                    <span>Ujian</span>
                </a>
            </li>
            @endif

            @if(in_array('seminar', $allowedModules))
            <li class="nav-item">
                <a class="nav-link-custom" href="javascript:void(0)" onclick="Swal.fire('Seminar', 'Jadwal seminar telah diaktifkan.', 'info');">
                    <i class="bi bi-easel"></i>
                    <span>Seminar</span>
                </a>
            </li>
            @endif

            <li class="nav-item mt-3 pt-2 border-top border-secondary border-opacity-25">
                <form action="{{ route('portal.logout') }}" method="POST" id="formLogoutSidebar" onsubmit="return confirm('Apakah Anda yakin ingin keluar dari portal?');">
                    @csrf
                    <button type="submit" class="nav-link-custom bg-transparent border-0 w-100 text-start text-danger">
                        <i class="bi bi-power"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </li>
        </ul>

        <!-- Floating Green Edit/Pen Icon (Pojok Kiri Bawah Sesuai Gambar) -->
        <a href="{{ route('portal.dashboard') }}" class="btn-floating-pen" title="Menu Cepat Presensi">
            <i class="bi bi-pencil fs-5"></i>
        </a>
    </aside>

    <!-- Mobile Backdrop -->
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <!-- Main Workspace -->
    <div class="portal-wrapper">
        <!-- Top Navbar Sesuai Gambar -->
        <header class="portal-topbar">
            <!-- Left: Hamburger Menu -->
            <div class="d-flex align-items-center">
                <button type="button" class="btn btn-link text-secondary p-1 border-0" id="btnToggleSidebar" style="font-size: 1.4rem;">
                    <i class="bi bi-list"></i>
                </button>
            </div>

            <!-- Right: Fullscreen, Bell, Profile Sesuai Gambar -->
            <div class="d-flex align-items-center gap-3">
                <button type="button" class="btn btn-link text-secondary p-1" onclick="document.fullscreenElement ? document.exitFullscreen() : document.documentElement.requestFullscreen()" title="Fullscreen">
                    <i class="bi bi-fullscreen fs-5"></i>
                </button>
                <button type="button" class="btn btn-link text-secondary p-1" title="Notifikasi">
                    <i class="bi bi-bell fs-5"></i>
                </button>

                <!-- Profile User Sesuai Gambar: Faizal Haqqi / peserta_diklat -->
                @php
                    $portalAuthUser = auth()->user();
                    $portalRoles = $portalAuthUser ? $portalAuthUser->getAvailableRoles() : [];
                    $hasMultiPortalRoles = count($portalRoles) > 1;
                @endphp
                <div class="dropdown d-flex align-items-center ps-2 border-start">
                    <button class="btn btn-link text-decoration-none d-flex align-items-center gap-2 p-0 text-dark dropdown-toggle" 
                            type="button" 
                            data-bs-toggle="dropdown" 
                            aria-expanded="false">
                        <div class="avatar-circle rounded-circle bg-light border d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                            <i class="bi bi-person-fill text-secondary fs-5"></i>
                        </div>
                        <div class="text-start d-none d-sm-block">
                            <div class="fw-bold text-dark lh-1" style="font-size: 0.85rem;">{{ $peserta->nama ?? ($portalAuthUser ? $portalAuthUser->name : 'Peserta Diklat') }}</div>
                            <span class="text-muted" style="font-size: 0.72rem;">peserta_diklat</span>
                        </div>
                    </button>
                    @if($hasMultiPortalRoles)
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border py-2" style="min-width: 220px; border-radius: 8px;">
                        @foreach($portalRoles as $roleOpt)
                            @php
                                $isCurrentPeserta = strcasecmp('Peserta Diklat', $roleOpt) === 0 || strcasecmp('Peserta', $roleOpt) === 0;
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

        <!-- Content Area -->
        <main class="portal-content">
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
    @else
    <!-- Guest Layout untuk Halaman Login Peserta -->
    <main class="container my-5">
        @yield('content')
    </main>
    @endif

    <!-- Scripts -->
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/sweetalert2.all.min.js') }}"></script>
    <script>
        // Toggle Sidebar Mobile
        const sidebar = document.getElementById('portalSidebar');
        const backdrop = document.getElementById('sidebarBackdrop');
        const toggleBtn = document.getElementById('btnToggleSidebar');

        if (toggleBtn && sidebar && backdrop) {
            toggleBtn.addEventListener('click', () => {
                sidebar.classList.toggle('show');
                backdrop.classList.toggle('show');
            });

            backdrop.addEventListener('click', () => {
                sidebar.classList.remove('show');
                backdrop.classList.remove('show');
            });
        }
    </script>
    @yield('scripts')
</body>
</html>
