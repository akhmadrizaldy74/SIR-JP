@forelse($notifications as $notif)
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
