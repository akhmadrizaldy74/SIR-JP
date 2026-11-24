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

        <!-- Notification Bell Dinamis Berbasis Peran (KCLC Model) -->
        @php
            $notifService = app(\App\Services\NotificationService::class);
            $appNotifications = $notifService->getNotificationsForUser(auth()->user());
            $unreadNotifCount = $notifService->getUnreadCount(auth()->user());
        @endphp
        <div class="dropdown">
            <button type="button" class="btn btn-link text-secondary p-1 position-relative" data-bs-toggle="dropdown" aria-expanded="false" title="Notifikasi ({{ $unreadNotifCount }} baru)">
                <i class="bi bi-bell fs-5"></i>
                @if($unreadNotifCount > 0)
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem; padding: 0.25em 0.45em;">
                        {{ $unreadNotifCount > 9 ? '9+' : $unreadNotifCount }}
                    </span>
                @endif
            </button>
            <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 py-0 mt-2" style="width: 360px; max-width: 90vw; border-radius: 12px; overflow: hidden; z-index: 1060;">
                <!-- Header Notifikasi -->
                <div class="d-flex align-items-center justify-content-between px-3 py-2.5 text-white" style="background-color: #0B1727;">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-bell-fill text-warning"></i>
                        <span class="fw-bold small">Notifikasi</span>
                        @if($unreadNotifCount > 0)
                            <span class="badge bg-danger rounded-pill px-2" style="font-size: 0.7rem;">{{ $unreadNotifCount }} Baru</span>
                        @endif
                    </div>
                    @if(count($appNotifications) > 0)
                        <form action="{{ route('notifications.mark-all-read') }}" method="POST" class="m-0">
                            @csrf
                            <button type="submit" class="btn btn-link text-white-50 p-0 text-decoration-none small" style="font-size: 0.75rem;" title="Tandai semua telah dibaca">
                                <i class="bi bi-check2-all"></i> Tandai Dibaca
                            </button>
                        </form>
                    @endif
                </div>

                <!-- Daftar Item Notifikasi -->
                <div class="list-group list-group-flush" style="max-height: 380px; overflow-y: auto;">
                    @forelse($appNotifications as $notif)
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
                            <span class="small" style="font-size: 0.75rem;">Semua tugas dan permohonan telah diperbarui.</span>
                        </div>
                    @endforelse
                </div>

                <!-- Footer Notifikasi -->
                @if(count($appNotifications) > 0)
                    <div class="bg-light px-3 py-2 text-center border-top">
                        @if(isset($isAdministrator) && $isAdministrator)
                            <a href="{{ route('users.index', ['role' => 'guest']) }}" class="text-decoration-none small text-primary fw-semibold" style="font-size: 0.75rem;">
                                <i class="bi bi-people me-1"></i>Tinjau Pengguna &amp; Penetapan Akses
                            </a>
                        @else
                            <span class="text-muted small" style="font-size: 0.75rem;">Notifikasi diperbarui otomatis sesuai peran aktif Anda</span>
                        @endif
                    </div>
                @endif
            </div>
        </div>

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
