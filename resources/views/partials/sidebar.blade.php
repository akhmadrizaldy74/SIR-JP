<aside class="app-sidebar">
    <div class="sidebar-header">
        <a href="{{ route('dashboard') }}" class="sidebar-brand">
            <div class="brand-icon">
                <i class="bi bi-clock-history"></i>
            </div>
            <div class="sidebar-brand-text">
                <span class="title">SIR-JP</span>
                <span class="subtitle">Rekapitulasi Jam Pelajaran</span>
            </div>
        </a>
    </div>

    <div class="sidebar-menu-wrapper">
        <div class="sidebar-section-title">Menu Utama</div>
        <ul class="nav flex-column mb-3">
            <li class="nav-item">
                <a class="nav-link-custom {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                    <i class="bi bi-grid-1x2-fill"></i>
                    <span class="nav-text">Dashboard</span>
                </a>
            </li>
        </ul>

        <div class="sidebar-section-title">Master Data</div>
        <ul class="nav flex-column mb-3">
            <li class="nav-item">
                <a class="nav-link-custom {{ request()->routeIs('widyaiswara.*') ? 'active' : '' }}" href="{{ route('widyaiswara.index') }}">
                    <i class="bi bi-person-badge-fill"></i>
                    <span class="nav-text">Data Widyaiswara</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link-custom {{ request()->routeIs('narasumber.*') ? 'active' : '' }}" href="{{ route('narasumber.index') }}">
                    <i class="bi bi-person-lines-fill"></i>
                    <span class="nav-text">Data Narasumber</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link-custom {{ request()->routeIs('pelatihan.*') ? 'active' : '' }}" href="{{ route('pelatihan.index') }}">
                    <i class="bi bi-mortarboard-fill"></i>
                    <span class="nav-text">Data Pelatihan</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link-custom {{ request()->routeIs('angkatan.*') ? 'active' : '' }}" href="{{ route('angkatan.index') }}">
                    <i class="bi bi-people-fill"></i>
                    <span class="nav-text">Data Angkatan</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link-custom {{ request()->routeIs('mata-pelatihan.*') ? 'active' : '' }}" href="{{ route('mata-pelatihan.index') }}">
                    <i class="bi bi-journal-bookmark-fill"></i>
                    <span class="nav-text">Mata Pelatihan</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link-custom {{ request()->routeIs('penugasan.*') ? 'active' : '' }}" href="{{ route('penugasan.index') }}">
                    <i class="bi bi-person-check-fill"></i>
                    <span class="nav-text">Penugasan Pengajar</span>
                </a>
            </li>
        </ul>

        <div class="sidebar-section-title">Jadwal & Realisasi</div>
        <ul class="nav flex-column mb-3">
            <li class="nav-item">
                <a class="nav-link-custom {{ request()->routeIs('jadwal.*') ? 'active' : '' }}" href="{{ route('jadwal.index') }}">
                    <i class="bi bi-calendar3"></i>
                    <span class="nav-text">Jadwal Pelatihan</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link-custom {{ request()->routeIs('realisasi.*') ? 'active' : '' }}" href="{{ route('realisasi.index') }}">
                    <i class="bi bi-check2-circle"></i>
                    <span class="nav-text">Realisasi Kegiatan</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link-custom {{ request()->routeIs('konversi.*') ? 'active' : '' }}" href="{{ route('konversi.index') }}">
                    <i class="bi bi-arrow-left-right"></i>
                    <span class="nav-text">Konversi JP</span>
                </a>
            </li>
        </ul>

        <div class="sidebar-section-title">Rekap & Laporan</div>
        <ul class="nav flex-column mb-3">
            <li class="nav-item">
                <a class="nav-link-custom {{ request()->routeIs('rekapitulasi.*') ? 'active' : '' }}" href="{{ route('rekapitulasi.index') }}">
                    <i class="bi bi-calculator-fill"></i>
                    <span class="nav-text">Rekapitulasi JP</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link-custom {{ request()->routeIs('laporan.*') ? 'active' : '' }}" href="{{ route('laporan.index') }}">
                    <i class="bi bi-file-earmark-bar-graph-fill"></i>
                    <span class="nav-text">Laporan</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link-custom {{ request()->routeIs('pengaturan.*') ? 'active' : '' }}" href="{{ route('pengaturan.index') }}">
                    <i class="bi bi-gear-fill"></i>
                    <span class="nav-text">Pengaturan</span>
                </a>
            </li>
        </ul>
    </div>

    <div class="sidebar-footer">
        <form action="{{ route('logout') }}" method="POST" id="logoutForm">
            @csrf
            <button type="submit" class="btn btn-outline-light w-100 btn-sm d-flex align-items-center justify-content-center gap-2" style="border-color: rgba(255,255,255,0.25);">
                <i class="bi bi-box-arrow-right"></i>
                <span class="nav-text">Keluar (Logout)</span>
            </button>
        </form>
    </div>
</aside>
<div class="sidebar-backdrop" id="sidebarBackdrop"></div>
