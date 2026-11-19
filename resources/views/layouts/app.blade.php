<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Informasi Rekapitulasi Jam Pelajaran') - SIR-JP</title>

    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-icons.min.css') }}">
    <!-- Custom Enterprise CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">

    @stack('styles')
</head>
<body>
    <div class="app-wrapper">
        <!-- Sidebar Navigation -->
        @include('partials.sidebar')
        <!-- Mobile Sidebar Backdrop -->
        <div id="sidebarBackdrop" class="sidebar-backdrop"></div>

        <!-- Main Workspace -->
        <div class="app-main">
            <!-- Top Navbar -->
            @include('partials.navbar')

            <!-- Content Area -->
            <main class="page-container">
                <div class="page-header d-flex justify-content-between align-items-center mb-3">
                    <h1 class="page-title mb-0" style="font-size: 1.25rem; font-weight: 700; color: #1E293B;">@yield('page_title', 'Dashboard')</h1>
                    <div class="d-flex align-items-center gap-3">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0" style="font-size: 0.85rem;">
                                @if(trim($__env->yieldContent('breadcrumb_custom')))
                                    @yield('breadcrumb_custom')
                                @else
                                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bi bi-house-door"></i> Home</a></li>
                                    @yield('breadcrumb')
                                @endif
                            </ol>
                        </nav>
                        @yield('page_actions')
                    </div>
                </div>

                <!-- Flash Messages -->
                @include('partials.alerts')

                <!-- Main Page Content -->
                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="app-footer">
                <div class="container-fluid d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
                    <div>
                        <strong>SIR-JP</strong> &copy; {{ date('Y') }} - {{ \App\Models\Pengaturan::get('instansi_nama', 'Pusat Pengembangan Sumber Daya Manusia') }}
                    </div>
                    <div class="text-muted">
                        Sistem Informasi Rekapitulasi Jam Pelajaran v1.0
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <!-- Chart.js -->
    <script src="{{ asset('assets/js/chart.umd.min.js') }}"></script>
    <!-- SweetAlert2 Notification Engine -->
    <script src="{{ asset('assets/js/sweetalert2.all.min.js') }}"></script>
    <!-- Custom Vanilla App JS -->
    <script src="{{ asset('assets/js/app.js') }}"></script>

    @stack('scripts')
</body>
</html>
