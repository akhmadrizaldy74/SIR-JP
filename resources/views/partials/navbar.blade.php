<header class="app-navbar d-flex align-items-center justify-content-between px-3 bg-white border-bottom" style="height: 64px;">
    <div class="d-flex align-items-center gap-3">
        <button type="button" class="navbar-toggle-btn btn btn-light border-0 p-1" id="sidebarToggleBtn" title="Buka/Tutup Sidebar" style="font-size: 1.4rem;">
            <i class="bi bi-list"></i>
        </button>
    </div>

    <div class="d-flex align-items-center gap-3">
        <!-- Fullscreen Button Sesuai Gambar KCLC -->
        <button type="button" class="btn btn-link text-secondary p-1" onclick="document.fullscreenElement ? document.exitFullscreen() : document.documentElement.requestFullscreen()" title="Fullscreen">
            <i class="bi bi-fullscreen fs-5"></i>
        </button>

        <!-- Notification Bell Sesuai Gambar KCLC -->
        <button type="button" class="btn btn-link text-secondary p-1" title="Notifikasi">
            <i class="bi bi-bell fs-5"></i>
        </button>

        <!-- User Profile Dropdown Sesuai Gambar KCLC: AKHMAD RIZALDY / admin_penyelenggara -->
        <div class="dropdown d-flex align-items-center">
            <button class="btn btn-link text-decoration-none d-flex align-items-center gap-2 p-0 text-dark dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <div class="avatar-circle rounded-circle bg-light border d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                    <i class="bi bi-person-fill text-secondary fs-5"></i>
                </div>
                <div class="text-start d-none d-sm-block">
                    <div class="fw-bold text-dark lh-1" style="font-size: 0.85rem; letter-spacing: 0.3px;">{{ strtoupper(auth()->user()->name ?? 'AKHMAD RIZALDY') }}</div>
                    <span class="text-muted text-capitalize" style="font-size: 0.72rem;">{{ auth()->user()->role ?? 'penyelenggara' }}</span>
                </div>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm border py-2" style="min-width: 200px;">
                <li class="px-3 py-2 border-bottom">
                    <div class="fw-bold text-dark">{{ auth()->user()->name ?? 'AKHMAD RIZALDY' }}</div>
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
