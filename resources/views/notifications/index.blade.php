<x-layout>
    <x-slot name="styles">
        <style>
            /* Stili aggiuntivi per la pagina delle notifiche */
            .notification-item {
                transition: background-color 0.3s ease;
            }
            .notification-date {
                font-size: 0.9rem;
                color: #6c757d;
            }
        </style>
    </x-slot>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Notifiche</h5>

                        @if (count(auth()->user()->unreadNotifications) > 0)
                            <button id="mark-all" class="btn btn-success">Segna tutte come lette</button>
                        @endif
                    </div>

                    <div class="card-body">
                        @if (count($notifications) > 0)
                            <div class="list-group">
                                @foreach ($notifications as $notification)
                                    <div class="list-group-item list-group-item-action {{ $notification->read_at ? '' : 'list-group-item-primary' }} notification-item"
                                         data-id="{{ $notification->id }}">
                                        <div class="d-flex w-100 justify-content-between mb-2">
                                            <h5 class="mb-1 fw-bold">
                                                @if ($notification->type === 'App\Notifications\EmployeeRegistered')
                                                    <i class="fas fa-user-plus text-success me-2"></i>Nuova registrazione dipendente
                                                @elseif ($notification->type === 'App\Notifications\DocumentUploaded')
                                                    <i class="fas fa-file-upload text-primary me-2"></i>Documento caricato
                                                @elseif ($notification->type === 'App\Notifications\DocumentApproved')
                                                    <i class="fas fa-file-check text-success me-2"></i>Documento approvato
                                                @elseif ($notification->type === 'App\Notifications\DocumentRejected')
                                                    <i class="fas fa-file-times text-danger me-2"></i>Documento rifiutato
                                                @else
                                                    <i class="fas fa-bell text-secondary me-2"></i>Notifica
                                                @endif
                                            </h5>
                                            <small class="notification-date">{{ $notification->created_at->format('d/m/Y H:i') }}</small>
                                        </div>

                                        <p class="mb-3">{{ $notification->data['message'] ?? '' }}</p>

                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>

                                            <div>
                                                @if (!$notification->read_at)
                                                    <button class="btn btn-sm btn-outline-primary mark-as-read" data-id="{{ $notification->id }}">
                                                        Segna come letta
                                                    </button>
                                                @else
                                                    <span class="badge bg-secondary">Letta</span>
                                                @endif

                                                @if(isset($notification->data['url']))
                                                    <a href="{{ $notification->data['url'] }}" class="btn btn-sm btn-primary ms-2">
                                                        Visualizza
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mt-4 d-flex justify-content-center">
                                {{ $notifications->links() }}
                            </div>
                        @else
                            <div class="alert alert-info text-center py-5">
                                <i class="fas fa-bell-slash fa-3x mb-3"></i>
                                <p class="mb-0">Non hai notifiche.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-slot name="sidebar">
        <div class="nav flex-column">
            <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('employee.dashboard') }}"
               class="nav-link link-dark">
                <i class="fas fa-tachometer-alt me-2"></i>
                Dashboard
            </a>
            <a href="{{ route('notifications.index') }}"
               class="nav-link link-dark active">
                <i class="fas fa-bell me-2"></i>
                Notifiche
            </a>
        </div>
    </x-slot>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Segna singola notifica come letta
            document.querySelectorAll('.mark-as-read').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const button = this;

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
                            const notificationItem = button.closest('.list-group-item');
                            notificationItem.classList.remove('list-group-item-primary');

                            // Sostituisci il pulsante con il badge "Letta"
                            const badge = document.createElement('span');
                            badge.className = 'badge bg-secondary';
                            badge.textContent = 'Letta';

                            // Sostituisci il pulsante col badge
                            button.parentNode.replaceChild(badge, button);

                            // Aggiorna il contatore delle notifiche non lette nell'interfaccia
                            updateUnreadCount();
                        }
                    });
                });
            });

            // Segna tutte le notifiche come lette
            const markAllButton = document.getElementById('mark-all');
            if (markAllButton) {
                markAllButton.addEventListener('click', function() {
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
                            // Rimuovi evidenziazione da tutte le notifiche
                            document.querySelectorAll('.list-group-item-primary').forEach(item => {
                                item.classList.remove('list-group-item-primary');
                            });

                            // Sostituisci tutti i pulsanti "Segna come letta" con il badge
                            document.querySelectorAll('.mark-as-read').forEach(btn => {
                                const badge = document.createElement('span');
                                badge.className = 'badge bg-secondary';
                                badge.textContent = 'Letta';
                                btn.parentNode.replaceChild(badge, btn);
                            });

                            // Nascondi il pulsante "Segna tutte come lette"
                            markAllButton.style.display = 'none';

                            // Mostra un messaggio di successo
                            const alertDiv = document.createElement('div');
                            alertDiv.className = 'alert alert-success alert-dismissible fade show mt-3';
                            alertDiv.innerHTML = `
                                ${data.message}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            `;
                            document.querySelector('.card-body').prepend(alertDiv);
                        }
                    });
                });
            }

            function updateUnreadCount() {
                // Conta quante notifiche non lette ci sono ancora
                const unreadCount = document.querySelectorAll('.list-group-item-primary').length;

                // Nascondi il pulsante "Segna tutte come lette" se non ci sono più notifiche non lette
                if (unreadCount === 0 && markAllButton) {
                    markAllButton.style.display = 'none';
                }
            }
        });
    </script>
    @endpush
</x-layout>
