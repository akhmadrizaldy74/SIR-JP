<header class="app-navbar">
    <div class="d-flex align-items-center gap-3">
        <button type="button" class="navbar-toggle-btn" id="sidebarToggleBtn" title="Buka/Tutup Sidebar">
            <i class="bi bi-list"></i>
        </button>
        <div class="d-none d-md-block">
            <span class="badge bg-light text-secondary border px-3 py-2 fw-normal">
                <i class="bi bi-building me-1 text-primary"></i>
                {{ \App\Models\Pengaturan::get('instansi_nama', 'Pusat Pengembangan Sumber Daya Manusia') }}
            </span>
        </div>
    </div>

    <div class="d-flex align-items-center gap-3">
        <div class="text-end d-none d-sm-block">
            <div class="fw-semibold text-dark small">{{ auth()->user()->name ?? 'Penyelenggara' }}</div>
            <div class="text-muted" style="font-size: 0.75rem;">Role: Penyelenggara (Administrator)</div>
        </div>

        <div class="dropdown">
            <button class="btn btn-light rounded-circle p-1 border dropdown-toggle d-flex align-items-center justify-content-center" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="width: 40px; height: 40px;">
                <i class="bi bi-person-fill fs-5 text-primary"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 py-2" style="min-width: 200px;">
                <li class="px-3 py-2 border-bottom">
                    <div class="fw-bold text-dark">{{ auth()->user()->name ?? 'Penyelenggara' }}</div>
                    <div class="text-muted small">{{ auth()->user()->email ?? 'penyelenggara@sir-jp.go.id' }}</div>
                </li>
                <li>
                    <a class="dropdown-item py-2" href="{{ route('profile.index') }}">
                        <i class="bi bi-person me-2 text-primary"></i> Profil Saya
                    </a>
                </li>
                <li>
                    <a class="dropdown-item py-2" href="{{ route('profile.password') }}">
                        <i class="bi bi-key me-2 text-warning"></i> Ganti Password
                    </a>
                </li>
                <li><hr class="dropdown-divider my-1"></li>
                <li>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="dropdown-item py-2 text-danger">
                            <i class="bi bi-box-arrow-right me-2"></i> Keluar
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>
