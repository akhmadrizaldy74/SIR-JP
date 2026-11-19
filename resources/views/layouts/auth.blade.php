<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Login') - SIR-JP</title>

    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-icons.min.css') }}">
    <!-- Custom Enterprise CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">

    <style>
        body {
            background: linear-gradient(135deg, #0F172A 0%, #183B65 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }
        .auth-card {
            background: #FFFFFF;
            border-radius: 16px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2), 0 10px 10px -5px rgba(0, 0, 0, 0.1);
            max-width: 440px;
            width: 100%;
            overflow: hidden;
        }
        .auth-header {
            background-color: #183B65;
            color: #FFFFFF;
            padding: 2.25rem 2rem;
            text-align: center;
            position: relative;
        }
        .auth-logo {
            width: 56px;
            height: 56px;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(4px);
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.85rem;
            color: #60A5FA;
            margin-bottom: 1rem;
        }
        .auth-body {
            padding: 2.25rem 2rem;
        }
    </style>
</head>
<body>
    <div class="auth-card">
        <div class="auth-header">
            <div class="auth-logo">
                <i class="bi bi-clock-history"></i>
            </div>
            <h3 class="fw-bold mb-1">SIR-JP</h3>
            <p class="text-blue-200 small mb-0" style="color: #93C5FD;">Sistem Informasi Rekapitulasi Jam Pelajaran</p>
        </div>
        <div class="auth-body">
            @include('partials.alerts')
            @yield('content')
        </div>
        <div class="bg-light py-3 px-4 text-center border-top text-muted small">
            &copy; {{ date('Y') }} {{ \App\Models\Pengaturan::get('instansi_nama', 'PPSDM') }}
        </div>
    </div>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>
