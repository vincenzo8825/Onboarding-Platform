<div class="dropdown">
    <a class="nav-link notifications__toggle position-relative" href="javascript:void(0);" role="button" id="notificationsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fas fa-bell"></i>
        @if($unreadCount > 0)
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger notification-badge">
                {{ $unreadCount }}
                <span class="visually-hidden">notifiche non lette</span>
            </span>
        @endif
    </a>
    <div class="dropdown-menu dropdown-menu-end p-0 notifications-dropdown shadow-lg" style="min-width: 300px; max-width: 350px;" aria-labelledby="notificationsDropdown">
        <div class="card border-0">
            <div class="notifications-dropdown__header card-header d-flex justify-content-between align-items-center bg-primary text-white py-2">
                <h6 class="notifications-dropdown__title mb-0">
                    <i class="fas fa-bell me-2"></i>Notifiche
                </h6>
                @if($unreadCount > 0)
                    <button type="button" class="notifications-dropdown__mark-all btn btn-sm btn-link text-white text-decoration-none p-0" id="markAllAsReadBtn">
                        <i class="fas fa-check-double me-1"></i>Segna tutte come lette
                    </button>
                @endif
            </div>

            <div class="list-group list-group-flush" style="max-height: 400px; overflow-y: auto;">
                @forelse($notifications as $notification)
                    <div class="notification-item list-group-item list-group-item-action {{ $notification->read_at ? '' : 'notification-item--unread' }} border-0 border-bottom" data-id="{{ $notification->id }}">
                        <div class="d-flex w-100 align-items-start">
                            <div class="me-3">
                                @php
                                    $iconClass = 'fas fa-bell';
                                    $bgColor = 'info';

                                    if (Str::contains($notification->type, 'DocumentRequest')) {
                                        $iconClass = 'fas fa-file-upload';
                                        $bgColor = 'danger';
                                    } elseif (Str::contains($notification->type, 'Document')) {
                                        $iconClass = 'fas fa-file-alt';
                                        $bgColor = 'success';
                                    } elseif (Str::contains($notification->type, 'Ticket')) {
                                        $iconClass = 'fas fa-ticket-alt';
                                        $bgColor = 'warning';
                                    } elseif (Str::contains($notification->type, 'Event')) {
                                        $iconClass = 'fas fa-calendar-alt';
                                        $bgColor = 'primary';
                                    } elseif (Str::contains($notification->type, 'Course')) {
                                        $iconClass = 'fas fa-graduation-cap';
                                        $bgColor = 'secondary';
                                    } elseif (Str::contains($notification->type, 'Checklist')) {
                                        $iconClass = 'fas fa-tasks';
                                        $bgColor = 'info';
                                    } elseif (Str::contains($notification->type, 'Badge')) {
                                        $iconClass = 'fas fa-award';
                                        $bgColor = 'warning';
                                    }
                                @endphp
                                <div class="notification-icon bg-{{ $bgColor }} text-white rounded-circle p-2">
                                    <i class="{{ $iconClass }}"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between w-100">
                                    <h6 class="notification-item__title mb-1 fw-bold">{{ $notification->data['title'] ?? 'Notifica' }}</h6>
                                    <small class="notification-item__date text-muted ms-2">{{ $notification->created_at->diffForHumans() }}</small>
                                </div>
                                <p class="notification-item__content mb-1 small">{{ $notification->data['message'] ?? '' }}</p>
                                <div class="notification-item__actions d-flex justify-content-between align-items-center mt-2">
                                    @if(isset($notification->data['url']))
                                        <a href="{{ $notification->data['url'] }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-external-link-alt me-1"></i> Visualizza
                                        </a>
                                    @endif

                                    @if(!$notification->read_at)
                                        <button class="mark-as-read-btn btn btn-sm btn-outline-secondary" data-id="{{ $notification->id }}">
                                            <i class="fas fa-check me-1"></i>Segna come letta
                                        </button>
                                    @else
                                        <span class="badge bg-secondary text-white">Letta</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="notifications-dropdown__empty py-4">
                        <div class="text-center">
                            <i class="fas fa-bell-slash mb-2 text-muted" style="font-size: 2rem;"></i>
                            <p class="mb-0 text-muted">Nessuna notifica disponibile</p>
                        </div>
                    </div>
                @endforelse
            </div>

            @if(count($notifications) > 0)
                <div class="card-footer text-center py-2 bg-light">
                    <a href="{{ route('notifications.index') }}" class="text-decoration-none fw-bold text-primary">
                        Vedi tutte <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.notification-icon {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 32px;
    height: 32px;
}

.notifications-dropdown__empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 2rem;
}

.notification-item--unread {
    background-color: rgba(67, 97, 238, 0.05);
    border-left: 3px solid var(--bs-primary) !important;
}

.notification-item {
    transition: all 0.2s ease;
}

.notification-item:hover {
    background-color: rgba(0, 0, 0, 0.02);
}

.notifications-dropdown {
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

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
                    const notificationItem = document.querySelector(`.notification-item[data-id="${id}"]`);
                    notificationItem.classList.remove('notification-item--unread');

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
            e.stopPropagation();

            fetch('/notifications/mark-all-as-read', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Rimuovi lo sfondo da tutte le notifiche non lette
                    document.querySelectorAll('.notification-item--unread').forEach(item => {
                        item.classList.remove('notification-item--unread');
                    });

                    // Sostituisci tutti i pulsanti "Segna come letta" con il badge "Letta"
                    document.querySelectorAll('.mark-as-read-btn').forEach(btn => {
                        const badge = document.createElement('span');
                        badge.className = 'badge bg-secondary';
                        badge.textContent = 'Letta';
                        btn.parentNode.replaceChild(badge, btn);
                    });

                    // Aggiorna il contatore delle notifiche
                    updateUnreadCount();

                    // Mostra conferma visiva
                    const notificationsHeader = document.querySelector('.notifications-dropdown__header');
                    if (notificationsHeader) {
                        const confirmDiv = document.createElement('div');
                        confirmDiv.className = 'notification-confirmation bg-success text-white py-1 px-2 text-center small';
                        confirmDiv.textContent = 'Tutte le notifiche sono state segnate come lette';

                        // Inserisci dopo l'header
                        notificationsHeader.parentNode.insertBefore(confirmDiv, notificationsHeader.nextSibling);

                        // Rimuovi dopo 3 secondi
                        setTimeout(() => {
                            confirmDiv.style.opacity = '0';
                            confirmDiv.style.transition = 'opacity 0.5s';
                            setTimeout(() => confirmDiv.remove(), 500);
                        }, 3000);
                    }

                    // Nascondi il pulsante "Segna tutte come lette"
                    markAllBtn.style.display = 'none';
                }
            })
            .catch(error => {
                console.error('Errore:', error);
                alert('Si è verificato un errore durante la marcatura di tutte le notifiche come lette.');
            });
        });
    }

    function updateUnreadCount() {
        // Calcola il nuovo numero di notifiche non lette
        const unreadCount = document.querySelectorAll('.notification-item--unread').length;

        // Aggiorna o rimuovi il badge
        const badge = document.querySelector('#notificationsDropdown .badge.bg-danger');
        if (unreadCount > 0) {
            if (badge) {
                badge.textContent = unreadCount;
            } else {
                // Crea un nuovo badge se non esiste
                const newBadge = document.createElement('span');
                newBadge.className = 'position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger notification-badge';
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

            // Se non ci sono più notifiche non lette, mostra messaggio vuoto
            const dropdownContent = document.querySelector('.list-group');
            if (dropdownContent) {
                dropdownContent.innerHTML = `
                    <div class="notifications-dropdown__empty py-4">
                        <div class="text-center">
                            <i class="fas fa-bell-slash mb-2 text-muted" style="font-size: 2rem;"></i>
                            <p class="mb-0 text-muted">Nessuna notifica non letta</p>
                        </div>
                    </div>
                `;
            }
        }

        // Aggiorna anche il badge globale nella navbar (se esiste un'altra implementazione)
        const navbarBadges = document.querySelectorAll('.badge.rounded-pill.bg-danger');
        navbarBadges.forEach(navBadge => {
            if (navBadge !== badge) { // Se non è lo stesso badge che abbiamo già gestito
                if (unreadCount > 0) {
                    navBadge.textContent = unreadCount;
                } else {
                    navBadge.remove();
                }
            }
        });

        // Aggiorna lo stato del documento per riflettere il cambiamento
        document.dispatchEvent(new CustomEvent('notifications-updated', {
            detail: { unreadCount: unreadCount }
        }));
    }
});
</script>
