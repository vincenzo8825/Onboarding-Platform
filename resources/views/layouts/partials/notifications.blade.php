<div class="dropdown-menu dropdown-menu-right notifications-dropdown">
    <div class="dropdown-header">
        {{ __('notifications.notifications') }}
        @if($unreadCount > 0)
            <a href="{{ route('notifications.mark-all-read') }}" class="float-right text-sm mark-all-read">
                {{ __('notifications.mark_all_read') }}
            </a>
        @endif
    </div>
    <div class="dropdown-divider"></div>
    <!-- Verifica che le notifiche vengano visualizzate correttamente -->
    @forelse($notifications as $notification)
        <a href="{{ $notification->getUrl() }}" 
           class="dropdown-item {{ $notification->isUnread() ? 'unread' : '' }}"
           data-id="{{ $notification->id }}">
            <div class="notification-title">{{ $notification->getTitle() }}</div>
            <div class="notification-text">{{ $notification->getMessage() }}</div>
            <div class="notification-time">{{ $notification->created_at->diffForHumans() }}</div>
        </a>
    @empty
        <div class="dropdown-item text-center">{{ __('notifications.no_notifications') }}</div>
    @endforelse
</div>