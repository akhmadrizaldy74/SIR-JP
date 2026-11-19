<aside class="app-sidebar">
    <div class="sidebar-header d-flex align-items-center justify-content-between px-3">
        <a href="{{ route('dashboard') }}" class="sidebar-brand d-flex align-items-center gap-2 text-decoration-none">
            <!-- Logo KC KEMNAKER CORPU Presisi Gambar -->
            <div class="brand-logo-kc d-flex align-items-center">
                <span class="badge fw-bold px-2 py-1 me-1 text-white" style="background: linear-gradient(135deg, #F59E0B 0%, #10B981 100%); font-size: 1.15rem; letter-spacing: -0.5px; border-radius: 6px;">KC</span>
                <div class="d-flex flex-column text-start sidebar-brand-text">
                    <span class="fw-bold text-white lh-1" style="font-size: 0.95rem; letter-spacing: 0.5px;">KEMNAKER CORPU</span>
                    <span class="text-white-50" style="font-size: 0.65rem;">Lebih Cerdas Lebih Unggul</span>
                </div>
            </div>
        </a>
    </div>

    <div class="sidebar-menu-wrapper">
        <div class="sidebar-section-title text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px; color: #94A3B8;">
            MENU
        </div>
        <ul class="nav flex-column mb-2">
            <li class="nav-item">
                <a class="nav-link-custom {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                    <i class="bi bi-grid-fill"></i>
                    <span class="nav-text">Dashboard</span>
                </a>
            </li>
        </ul>

        <!-- ============================================== -->
        <!-- LEARNING MANAGEMENT SYSTEM (LMS DIKLAT KCLC)   -->
        <!-- ============================================== -->
        <div class="sidebar-section-title text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px; color: #94A3B8;">
            Learning Management System
        </div>
        <div class="sidebar-section-title pt-0 text-white-50" style="font-size: 0.75rem; font-weight: 700;">
            LMS DIKLAT
        </div>
        <ul class="nav flex-column mb-3">
            <li class="nav-item">
                <a class="nav-link-custom {{ request()->routeIs('pelatihan.*') ? 'active' : '' }}" href="{{ route('pelatihan.index') }}">
                    <i class="bi bi-mortarboard-fill"></i>
                    <span class="nav-text">Pelatihan Diklat</span>
                </a>
            </li>

            <!-- Data Kelas Dropdown Group Sesuai Gambar KCLC -->
            @php
                $isDataKelasActive = request()->routeIs('angkatan.*') || request()->routeIs('kelas-mapel.*') || request()->routeIs('ruangan.*') || request()->routeIs('peserta.*');
            @endphp
            <li class="nav-item">
                <a class="nav-link-custom d-flex justify-content-between align-items-center {{ $isDataKelasActive ? 'active' : '' }}" 
                   data-bs-toggle="collapse" href="#menuDataKelas" role="button" aria-expanded="{{ $isDataKelasActive ? 'true' : 'false' }}">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-grid-3x3-gap-fill"></i>
                        <span class="nav-text">Data Kelas</span>
                    </div>
                    <i class="bi bi-chevron-down small transition-icon"></i>
                </a>
                <div class="collapse {{ $isDataKelasActive ? 'show' : '' }} ps-3" id="menuDataKelas">
                    <ul class="nav flex-column py-1 border-start border-secondary ms-2 ps-2">
                        <li class="nav-item">
                            <a class="nav-link-custom py-1 small {{ request()->routeIs('angkatan.*') ? 'active' : '' }}" href="{{ route('angkatan.index') }}">
                                <i class="bi bi-dash"></i> <span class="nav-text">Angkatan</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link-custom py-1 small {{ request()->routeIs('kelas-mapel.*') ? 'active' : '' }}" href="{{ route('kelas-mapel.index') }}">
                                <i class="bi bi-dash"></i> <span class="nav-text">Kelas</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link-custom py-1 small {{ request()->routeIs('ruangan.*') ? 'active' : '' }}" href="{{ route('ruangan.index') }}">
                                <i class="bi bi-dash"></i> <span class="nav-text">Ruangan</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link-custom py-1 small {{ request()->routeIs('peserta.*') ? 'active' : '' }}" href="{{ route('peserta.index') }}">
                                <i class="bi bi-dash"></i> <span class="nav-text">Peserta Diklat</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link-custom {{ request()->routeIs('sikap-form.*') ? 'active' : '' }}" href="{{ route('sikap-form.index') }}">
                    <i class="bi bi-ui-checks"></i>
                    <span class="nav-text">Aspek Penilaian Sikap</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link-custom {{ request()->routeIs('evaluasi-form.*') ? 'active' : '' }}" href="{{ route('evaluasi-form.index') }}">
                    <i class="bi bi-file-earmark-check-fill"></i>
                    <span class="nav-text">Form Evaluasi</span>
                </a>
            </li>
        </ul>

        <!-- ============================================== -->
        <!-- SISTEM REKAPITULASI JAM PELAJARAN (SIR-JP)     -->
        <!-- ============================================== -->
        <div class="sidebar-section-title text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px; color: #94A3B8;">
            Perhitungan Beban Mengajar
        </div>
        <div class="sidebar-section-title pt-0 text-white-50" style="font-size: 0.75rem; font-weight: 700;">
            REKAPITULASI JP (SIR-JP)
        </div>
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
            <li class="nav-item">
                <a class="nav-link-custom {{ request()->routeIs('jadwal.*') ? 'active' : '' }}" href="{{ route('jadwal.index') }}">
                    <i class="bi bi-calendar3"></i>
                    <span class="nav-text">Jadwal Pembelajaran</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link-custom {{ request()->routeIs('realisasi.*') ? 'active' : '' }}" href="{{ route('realisasi.index') }}">
                    <i class="bi bi-check2-circle"></i>
                    <span class="nav-text">Realisasi Mengajar</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link-custom {{ request()->routeIs('konversi.*') ? 'active' : '' }}" href="{{ route('konversi.index') }}">
                    <i class="bi bi-arrow-left-right"></i>
                    <span class="nav-text">Konversi JP</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link-custom {{ request()->routeIs('rekapitulasi.*') ? 'active' : '' }}" href="{{ route('rekapitulasi.index') }}">
                    <i class="bi bi-calculator-fill"></i>
                    <span class="nav-text">Rekapitulasi Bulanan</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link-custom {{ request()->routeIs('laporan.*') ? 'active' : '' }}" href="{{ route('laporan.index') }}">
                    <i class="bi bi-file-earmark-bar-graph-fill"></i>
                    <span class="nav-text">Laporan (Excel & PDF)</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link-custom {{ request()->routeIs('pengaturan.*') ? 'active' : '' }}" href="{{ route('pengaturan.index') }}">
                    <i class="bi bi-gear-fill"></i>
                    <span class="nav-text">Pengaturan Sistem</span>
                </a>
            </li>
        </ul>

        <div class="sidebar-section-title" style="font-size: 0.7rem;">Sesi Pengguna</div>
        <ul class="nav flex-column mb-4">
            <li class="nav-item">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="nav-link-custom w-100 text-start border-0 bg-transparent text-danger">
                        <i class="bi bi-power"></i>
                        <span class="nav-text">Logout</span>
                    </button>
                </form>
            </li>
        </ul>
    </div>
</aside>
