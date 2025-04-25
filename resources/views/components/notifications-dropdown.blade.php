<div class="dropdown">
    <a class="nav-link" href="#" role="button" id="notificationsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fas fa-bell"></i>
        @if($unreadCount > 0)
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                {{ $unreadCount }}
                <span class="visually-hidden">notifiche non lette</span>
            </span>
        @endif
    </a>
    <div class="dropdown-menu dropdown-menu-end p-0" aria-labelledby="notificationsDropdown" style="width: 350px; max-height: 400px; overflow-y: auto;">
        <div class="card border-0">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Notifiche</h6>
                @if($unreadCount > 0)
                    <button type="button" class="btn btn-sm btn-link text-decoration-none p-0" id="markAllAsReadBtn">
                        Segna tutte come lette
                    </button>
                @endif
            </div>
            <div class="list-group list-group-flush">
                @forelse($notifications as $notification)
                    <div class="list-group-item list-group-item-action {{ $notification->read_at ? '' : 'bg-light' }}" data-id="{{ $notification->id }}">
                        <div class="d-flex justify-content-between">
                            <h6 class="mb-1">
                                @if($notification->type === 'App\\Notifications\\EmployeeRegistered')
                                    <i class="fas fa-user-plus text-success me-2"></i> Nuova registrazione dipendente
                                @elseif($notification->type === 'App\\Notifications\\DocumentUploaded')
                                    <i class="fas fa-file-upload text-primary me-2"></i> Documento caricato
                                @elseif($notification->type === 'App\\Notifications\\DocumentApproved')
                                    <i class="fas fa-file-check text-success me-2"></i> Documento approvato
                                @elseif($notification->type === 'App\\Notifications\\DocumentRejected')
                                    <i class="fas fa-file-times text-danger me-2"></i> Documento rifiutato
                                @elseif($notification->type === 'App\\Notifications\\NewTicketReply')
                                    <i class="fas fa-comment-dots text-primary me-2"></i> Nuovo messaggio
                                @else
                                    <i class="fas fa-bell text-secondary me-2"></i> Notifica
                                @endif
                            </h6>
                            <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                        </div>
                        <p class="mb-1">{{ $notification->data['message'] ?? 'Nuova notifica' }}</p>

                        <div class="d-flex justify-content-between align-items-center mt-2">
                            @if(!$notification->read_at)
                                <button class="btn btn-sm btn-outline-secondary mark-as-read-btn" data-id="{{ $notification->id }}">
                                    Segna come letta
                                </button>
                            @else
                                <span class="badge bg-secondary">Letta</span>
                            @endif

                            @if(isset($notification->data['url']))
                                <a href="{{ $notification->data['url'] }}" class="btn btn-sm btn-primary">Visualizza</a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="list-group-item text-center py-3">
                        <i class="fas fa-bell-slash fa-2x text-muted mb-2"></i>
                        <p class="mb-0">Non hai notifiche.</p>
                    </div>
                @endforelse
            </div>
            <div class="card-footer text-center">
                <a href="{{ route('notifications.index') }}" class="text-decoration-none">Vedi tutte le notifiche</a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Segna singola notifica come letta
    document.querySelectorAll('.mark-as-read-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const id = this.getAttribute('data-id');

            fetch(`/notifications/${id}/mark-as-read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Aggiorna visivamente l'elemento della notifica
                    const notificationItem = document.querySelector(`.list-group-item[data-id="${id}"]`);
                    notificationItem.classList.remove('bg-light');

                    // Sostituisci il pulsante con il badge "Letta"
                    const markAsReadBtn = notificationItem.querySelector('.mark-as-read-btn');
                    const badge = document.createElement('span');
                    badge.className = 'badge bg-secondary';
                    badge.textContent = 'Letta';
                    markAsReadBtn.parentNode.replaceChild(badge, markAsReadBtn);

                    // Aggiorna il contatore delle notifiche non lette
                    updateUnreadCount();
                }
            });
        });
    });

    // Segna tutte le notifiche come lette
    const markAllBtn = document.getElementById('markAllAsReadBtn');
    if (markAllBtn) {
        markAllBtn.addEventListener('click', function(e) {
            e.preventDefault();

            fetch('/notifications/mark-all-as-read', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Rimuovi lo sfondo da tutte le notifiche non lette
                    document.querySelectorAll('.list-group-item.bg-light').forEach(item => {
                        item.classList.remove('bg-light');
                    });

                    // Sostituisci tutti i pulsanti "Segna come letta" con il badge "Letta"
                    document.querySelectorAll('.mark-as-read-btn').forEach(btn => {
                        const badge = document.createElement('span');
                        badge.className = 'badge bg-secondary';
                        badge.textContent = 'Letta';
                        btn.parentNode.replaceChild(badge, btn);
                    });

                    // Rimuovi il badge con il conteggio delle notifiche non lette
                    const badge = document.querySelector('#notificationsDropdown .badge.bg-danger');
                    if (badge) {
                        badge.remove();
                    }

                    // Nascondi il pulsante "Segna tutte come lette"
                    markAllBtn.style.display = 'none';
                }
            });
        });
    }

    function updateUnreadCount() {
        // Calcola il nuovo numero di notifiche non lette
        const unreadCount = document.querySelectorAll('.list-group-item.bg-light').length;

        // Aggiorna o rimuovi il badge
        const badge = document.querySelector('#notificationsDropdown .badge.bg-danger');
        if (unreadCount > 0) {
            if (badge) {
                badge.textContent = unreadCount;
            } else {
                // Crea un nuovo badge se non esiste
                const newBadge = document.createElement('span');
                newBadge.className = 'position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger';
                newBadge.textContent = unreadCount;

                const hiddenSpan = document.createElement('span');
                hiddenSpan.className = 'visually-hidden';
                hiddenSpan.textContent = 'notifiche non lette';

                newBadge.appendChild(hiddenSpan);
                document.querySelector('#notificationsDropdown').appendChild(newBadge);
            }
        } else if (badge) {
            badge.remove();

            // Nascondi anche il pulsante "Segna tutte come lette"
            if (markAllBtn) {
                markAllBtn.style.display = 'none';
            }
        }
    }
});
</script>
