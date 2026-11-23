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

        <!-- User Profile Dropdown Sesuai Gambar KCLC -->
        @php
            $currentUser = auth()->user();
            $currentActiveRole = $currentUser ? $currentUser->getActiveRole() : 'Penyelenggara';
            $allRoles = $currentUser ? $currentUser->getAvailableRoles() : ['Penyelenggara'];
            $roleSubtitle = strtolower(str_replace(' ', '_', $currentActiveRole));
            if ($currentActiveRole === 'Penyelenggara') {
                $roleSubtitle = 'admin_penyelenggara';
            }
        @endphp
        <div class="dropdown d-flex align-items-center">
            <button class="btn btn-link text-decoration-none d-flex align-items-center gap-2 p-0 text-dark dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <div class="avatar-circle rounded-circle bg-light border d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                    <i class="bi bi-person-fill text-secondary fs-5"></i>
                </div>
                <div class="text-start d-none d-sm-block">
                    <div class="fw-bold text-dark lh-1" style="font-size: 0.85rem; letter-spacing: 0.3px;">{{ strtoupper($currentUser->name ?? 'AKHMAD RIZALDY') }}</div>
                    <span class="text-muted" style="font-size: 0.72rem;">{{ $roleSubtitle }}</span>
                </div>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm border py-2" style="min-width: 220px; border-radius: 8px;">
                @foreach($allRoles as $roleOpt)
                    @php
                        $isActiveMode = strcasecmp($currentActiveRole, $roleOpt) === 0;
                    @endphp
                    <li>
                        <a class="dropdown-item d-flex align-items-center justify-content-between py-2 px-3 {{ $isActiveMode ? 'fw-bold text-dark' : 'text-secondary' }}" 
                           href="{{ route('switch-role', ['role' => $roleOpt]) }}">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-person text-secondary"></i>
                                <span>{{ $roleOpt }}</span>
                            </div>
                            @if($isActiveMode)
                                <span class="badge" style="background-color: #DCFCE7; color: #16A34A; font-size: 0.68rem; font-weight: 600; padding: 3px 8px; border-radius: 4px;">Active</span>
                            @endif
                        </a>
                    </li>
                @endforeach

                <li><hr class="dropdown-divider my-1"></li>
                <li>
                    <a class="dropdown-item py-2 px-3 text-secondary d-flex align-items-center gap-2" href="{{ route('profile.index') }}">
                        <i class="bi bi-person-gear"></i> Profil Saya
                    </a>
                </li>
                <li>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="dropdown-item py-2 px-3 text-danger d-flex align-items-center gap-2 border-0 bg-transparent w-100">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>
