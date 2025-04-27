<x-layout>
    <x-slot name="styles">
        @vite('resources/css/pages/notifications.css')
    </x-slot>

    <div class="container py-4">
        <div class="row">
            <div class="col-12">
                <div class="notifications-page__header">
                    <h1 class="notifications-page__title">Notifiche</h1>

                    <div class="notifications-page__actions">
                        <div class="btn-group" role="group">
                            <a href="{{ route('notifications.index') }}" class="btn btn-outline-primary {{ !request('filter') ? 'active' : '' }}">
                                Tutte
                            </a>
                            <a href="{{ route('notifications.index', ['filter' => 'unread']) }}" class="btn btn-outline-primary {{ request('filter') == 'unread' ? 'active' : '' }}">
                                Non lette
                            </a>
                            <a href="{{ route('notifications.index', ['filter' => 'read']) }}" class="btn btn-outline-primary {{ request('filter') == 'read' ? 'active' : '' }}">
                                Lette
                            </a>
                        </div>

                        @if($notifications->where('read_at', null)->count() > 0)
                        <form id="markAllAsReadForm" method="POST" action="{{ route('notifications.mark-all-as-read') }}" style="display: inline;">
                            @csrf
                            <button id="markAllAsReadBtn" type="button" class="btn btn-outline-primary">
                            <i class="fas fa-check-double me-1"></i> Segna tutte come lette
                        </button>
                        </form>
                        @endif
                    </div>
                </div>

                <div class="notifications-page__list">
                    @forelse($notifications as $notification)
                        <div class="notifications-page-item {{ $notification->read_at ? '' : 'notifications-page-item--unread' }}" data-id="{{ $notification->id }}">
                            <div class="notifications-page-item__header">
                                <div class="d-flex align-items-center">
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
                                    <div class="notification-icon bg-{{ $bgColor }} text-white rounded-circle p-2 me-3">
                                        <i class="{{ $iconClass }}"></i>
                                    </div>
                                    <h4 class="notifications-page-item__title">{{ $notification->data['title'] ?? 'Notifica' }}</h4>
                                </div>
                                <span class="notifications-page-item__date">{{ $notification->created_at->format('d/m/Y H:i') }} ({{ $notification->created_at->diffForHumans() }})</span>
                            </div>
                            <div class="notifications-page-item__content">
                                <p>{{ $notification->data['message'] ?? 'Nessun messaggio' }}</p>
                            </div>
                            <div class="notifications-page-item__actions">
                                @if(!$notification->read_at)
                                    <form method="POST" action="{{ route('notifications.mark-as-read', $notification->id) }}" style="display:inline;">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-secondary mark-as-read-btn" type="button" data-id="{{ $notification->id }}">
                                        <i class="fas fa-check me-1"></i> Segna come letta
                                    </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="notifications-page__empty-state">
                            <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                            <h4>Nessuna notifica</h4>
                            <p class="text-muted">Non hai notifiche da visualizzare</p>
                        </div>
                    @endforelse
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $notifications->links() }}
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Gestione pulsante segna come letta
            document.querySelectorAll('.mark-as-read-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const form = this.closest('form');
                    if (form) {
                        // Invia il form tramite AJAX
                        const formData = new FormData(form);
                        markAsReadWithForm(form.action, formData, id, this);
                    }
                });
            });

            // Gestione pulsante segna tutte come lette
            const markAllAsReadBtn = document.getElementById('markAllAsReadBtn');
            if (markAllAsReadBtn) {
                markAllAsReadBtn.addEventListener('click', function() {
                    const form = document.getElementById('markAllAsReadForm');
                    if (form) {
                        // Invia il form tramite AJAX
                        const formData = new FormData(form);
                        markAllAsReadWithForm(form.action, formData);
                    }
                });
            }

            // Ascolta gli eventi di aggiornamento notifiche dal dropdown
            document.addEventListener('notifications-updated', function(e) {
                // Aggiorna la UI se necessario
                if (e.detail && e.detail.unreadCount === 0) {
                    // Se siamo nella vista delle notifiche non lette, aggiorna
                    if (window.location.href.includes('filter=unread')) {
                        window.location.reload();
                    }
                }
            });

            // Funzione per marcare una notifica come letta usando un form
            function markAsReadWithForm(url, formData, id, buttonElement) {
                fetch(url, {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin'
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Errore nel server');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        const notificationItem = document.querySelector(`div[data-id="${id}"]`);
                        notificationItem.classList.remove('notifications-page-item--unread');

                        // Rimuovi il form che contiene il pulsante
                        if (buttonElement) {
                            const form = buttonElement.closest('form');
                            if (form) form.remove();
                    }

                        // Aggiornare il conteggio nella navbar se presente
                        updateNavbarCounter();
                    }
                })
                .catch(error => {
                    console.error('Errore:', error);
                    alert('Si è verificato un errore durante la marcatura della notifica come letta.');
                });
            }

            // Funzione per marcare tutte le notifiche come lette
            function markAllAsReadWithForm(url, formData) {
                fetch(url, {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin'
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Errore nel server');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Rimuovi tutte le classi unread
                        document.querySelectorAll('.notifications-page-item--unread').forEach(item => {
                            item.classList.remove('notifications-page-item--unread');
                        });

                        // Rimuovi tutti i form con pulsanti
                        document.querySelectorAll('.mark-as-read-btn').forEach(button => {
                            const form = button.closest('form');
                            if (form) form.remove();
                        });

                        // Nascondi il pulsante segna tutte come lette
                        const form = document.getElementById('markAllAsReadForm');
                        if (form) {
                            form.style.display = 'none';
                        }

                        // Aggiornare il conteggio nella navbar se presente
                        updateNavbarCounter();
                    }
                })
                .catch(error => {
                    console.error('Errore:', error);
                    alert('Si è verificato un errore durante la marcatura di tutte le notifiche come lette.');
                });
            }

            // Aggiorna il conteggio delle notifiche nella navbar se presente
            function updateNavbarCounter() {
                // Il badge sta dentro il link .notification-link ed è un elemento con classe 'badge'
                const navbarBadge = document.querySelector('.notification-link .badge.rounded-pill');
                // Se tutte le notifiche sono state lette, rimuovi il badge
                const unreadCount = document.querySelectorAll('.notifications-page-item--unread').length;

                // Se non c'è badge e ci sono notifiche non lette, ricarica la pagina per mostrarlo
                if (!navbarBadge && unreadCount > 0) {
                    window.location.reload();
                    return;
                }

                // Se c'è il badge, aggiornalo
                if (navbarBadge) {
                    if (unreadCount > 0) {
                        // Aggiorna il testo con il conteggio aggiornato
                        navbarBadge.textContent = unreadCount;
                        navbarBadge.style.display = '';
                    } else {
                        // Se non ci sono notifiche non lette, nascondi il badge completamente
                        navbarBadge.remove();

                        // Se siamo nella pagina delle notifiche non lette, ricarica la pagina
                        if (window.location.href.includes('filter=unread')) {
                            window.location.reload();
                        }
                    }
                }

                // Aggiorna anche la UI della pagina corrente
                if (window.location.href.includes('filter=unread') && unreadCount === 0) {
                    // Se tutte le notifiche sono state lette in questa vista, mostra messaggio vuoto
                    const container = document.querySelector('.notifications-page__list');
                    if (container) {
                        container.innerHTML = `
                            <div class="notifications-page__empty-state">
                                <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                                <h4>Nessuna notifica non letta</h4>
                                <p class="text-muted">Non hai notifiche da visualizzare</p>
                            </div>
                        `;
                    }

                    // Nascondi anche il pulsante "Segna tutte come lette"
                    const markAllBtn = document.getElementById('markAllAsReadBtn');
                    if (markAllBtn && markAllBtn.closest('form')) {
                        markAllBtn.closest('form').style.display = 'none';
                    }
                }
            }
        });
    </script>
    @endpush
</x-layout>
