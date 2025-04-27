<header>
<nav class="navbar navbar-expand-md navbar-dark bg-primary shadow-sm {{ !request()->routeIs('login') && !request()->routeIs('register') && !request()->routeIs('password.*') ? 'fixed-top' : '' }}">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">
            {{ config('app.name', 'Onboarding Platform') }}
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <!-- Left Side Of Navbar -->
            <ul class="navbar-nav me-auto">
                @auth
                    @if(auth()->user()->role === 'admin')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                                <i class="fas fa-tachometer-alt me-1"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.employees.*') ? 'active' : '' }}" href="{{ route('admin.employees.index') }}">
                                <i class="fas fa-users me-1"></i> Dipendenti
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.programs.*') ? 'active' : '' }}" href="{{ route('admin.programs.index') }}">
                                <i class="fas fa-graduation-cap me-1"></i> Programmi
                            </a>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('employee.dashboard') ? 'active' : '' }}" href="{{ route('employee.dashboard') }}">
                                <i class="fas fa-tachometer-alt me-1"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('employee.programs.*') ? 'active' : '' }}" href="{{ route('employee.programs.index') }}">
                                <i class="fas fa-graduation-cap me-1"></i> Programmi
                            </a>
                        </li>
                    @endif
                @endauth
            </ul>

            <!-- Right Side Of Navbar -->
            <ul class="navbar-nav ms-auto">
                @guest

                    <li class="nav-item">
                        <a class="btn btn-light text-primary ms-2" href="{{ route('login') }}">
                            Accedi
                        </a>
                    </li>
                @else
                    <!-- Notifiche con pannello laterale moderno -->
                    <li class="nav-item notifications-nav-item">
                        <button class="nav-link px-3 py-2 position-relative notification-button" id="notificationsToggle" title="Notifiche">
                            <i class="fas fa-bell fa-lg"></i>
                            @if(auth()->user()->unreadNotifications->count() > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge bg-danger rounded-circle notification-badge">
                                    {{ auth()->user()->unreadNotifications->count() }}
                                    <span class="visually-hidden">notifiche non lette</span>
                                </span>
                            @endif
                        </button>
                    </li>

                    <!-- Nome utente e logout -->
                    <li class="nav-item d-flex align-items-center ms-3">
                        <span class="text-white me-2">
                            <i class="fas fa-user-circle me-1"></i> {{ Auth::user()->name }}
                        </span>
                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="fas fa-sign-out-alt me-1"></i> Esci
                            </button>
                        </form>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>
</header>

<style>
/* Stili per migliorare l'area cliccabile delle notifiche */
.notifications-nav-item {
    margin: 0 5px;
}

.notification-button {
    background: transparent;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    transition: background-color 0.3s ease;
}

.notification-button:hover, .notification-button:focus {
    background-color: rgba(255, 255, 255, 0.2);
    cursor: pointer;
}

.notification-button:active {
    background-color: rgba(255, 255, 255, 0.3);
}

/* Nuovo stile per badge notifiche circolare */
.notification-badge {
    width: 1.5rem;
    height: 1.5rem;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    line-height: 1;
}

/* Stili per il pannello laterale delle notifiche */
.notifications-panel {
    position: fixed;
    top: 0;
    right: -400px; /* Inizialmente fuori dallo schermo */
    width: 400px;
    height: 100vh;
    background-color: #fff;
    box-shadow: -5px 0 15px rgba(0, 0, 0, 0.1);
    z-index: 1050;
    transition: right 0.3s ease;
    display: flex;
    flex-direction: column;
}

.notifications-panel.show {
    right: 0;
}

.notifications-panel__header {
    padding: 20px;
    background-color: var(--bs-primary);
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.notifications-panel__title {
    margin: 0;
    font-size: 1.25rem;
}

.notifications-panel__close {
    background: transparent;
    border: none;
    color: white;
    font-size: 1.25rem;
    cursor: pointer;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: background-color 0.2s;
}

.notifications-panel__close:hover {
    background-color: rgba(255, 255, 255, 0.2);
}

.notifications-panel__tabs {
    display: flex;
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.notifications-panel__tab {
    flex: 1;
    padding: 12px;
    text-align: center;
    cursor: pointer;
    border-bottom: 3px solid transparent;
    transition: all 0.2s;
    font-weight: 500;
}

.notifications-panel__tab.active {
    border-bottom-color: var(--bs-primary);
    color: var(--bs-primary);
}

.notifications-panel__content {
    flex: 1;
    overflow-y: auto;
    padding: 0;
}

.notification-list {
    list-style: none;
    margin: 0;
    padding: 0;
}

.notification-item {
    padding: 15px 20px;
    border-bottom: 1px solid #eee;
    transition: background-color 0.2s;
    display: flex;
    align-items: flex-start;
}

.notification-item:hover {
    background-color: #f8f9fa;
}

.notification-item--unread {
    background-color: rgba(13, 110, 253, 0.05);
    border-left: 4px solid var(--bs-primary);
}

.notification-item__icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-color: #f1f3f5;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
    flex-shrink: 0;
}

.notification-item__content {
    flex: 1;
}

.notification-item__title {
    margin: 0 0 5px;
    font-weight: 600;
    font-size: 0.95rem;
}

.notification-item__message {
    margin: 0 0 10px;
    color: #6c757d;
    font-size: 0.9rem;
}

.notification-item__meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.8rem;
    color: #adb5bd;
}

.notifications-panel__footer {
    padding: 15px 20px;
    border-top: 1px solid #dee2e6;
    text-align: center;
}

.notifications-panel__footer a {
    color: var(--bs-primary);
    text-decoration: none;
    font-weight: 500;
}

.notifications-panel__empty {
    padding: 40px 20px;
    text-align: center;
    color: #6c757d;
}

.notifications-panel__empty i {
    font-size: 3rem;
    margin-bottom: 15px;
    color: #dee2e6;
}

.notifications-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 1040;
    display: none;
}

.notifications-backdrop.show {
    display: block;
}

@media (max-width: 576px) {
    .notifications-panel {
        width: 100%;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inizializzazione popup e tooltip
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl)
    })

    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })

    // Configurazione pannello notifiche
    const notificationsConfig = {
        showActionButtons: true, // Manteniamo i pulsanti "Segna come letta"
        enableLinkToDetails: false // Disabilitiamo i link ai dettagli
    };

    // Funzionalità del pannello laterale delle notifiche
    setupNotificationsPanel(notificationsConfig);
});

function setupNotificationsPanel(config = {}) {
    // Valori predefiniti per la configurazione
    const defaultConfig = {
        showActionButtons: true,
        enableLinkToDetails: false
    };

    // Combina la configurazione predefinita con quella personalizzata
    const finalConfig = {...defaultConfig, ...config};

    // Salva la configurazione globalmente per essere usata in altre funzioni
    window.notificationsConfig = finalConfig;

    // Aggiungiamo gli elementi del pannello notifiche al body se non esistono già
    if (!document.getElementById('notificationsPanel')) {
        createNotificationsPanel();
    }

    // Aggiungiamo il listener al pulsante delle notifiche
    const notificationsToggle = document.getElementById('notificationsToggle');
    if (notificationsToggle) {
        notificationsToggle.addEventListener('click', function() {
            toggleNotificationsPanel();
        });
    }
}

function createNotificationsPanel() {
    const panel = document.createElement('div');
    panel.id = 'notificationsPanel';
    panel.className = 'notifications-panel';

    // Creiamo la struttura del pannello
    panel.innerHTML = `
        <div class="notifications-panel__header">
            <h5 class="notifications-panel__title">
                <i class="fas fa-bell me-2"></i>Notifiche
            </h5>
            <button type="button" class="notifications-panel__close" id="closeNotificationsPanel">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="notifications-panel__tabs">
            <div class="notifications-panel__tab active" data-tab="all">Tutte</div>
            <div class="notifications-panel__tab" data-tab="unread">Non lette</div>
        </div>
        <div class="notifications-panel__content">
            <div class="loading text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Caricamento...</span>
                </div>
            </div>
        </div>
        <div class="notifications-panel__footer">
            <a href="/notifications">
                Vedi tutte le notifiche <i class="fas fa-arrow-right ms-1"></i>
            </a>
        </div>
    `;

    // Creiamo il backdrop
    const backdrop = document.createElement('div');
    backdrop.id = 'notificationsBackdrop';
    backdrop.className = 'notifications-backdrop';

    // Aggiungiamo al body
    document.body.appendChild(panel);
    document.body.appendChild(backdrop);

    // Aggiungiamo gli event listener
    document.getElementById('closeNotificationsPanel').addEventListener('click', function() {
        toggleNotificationsPanel(false);
    });

    backdrop.addEventListener('click', function() {
        toggleNotificationsPanel(false);
    });

    // Event listener per le tab
    document.querySelectorAll('.notifications-panel__tab').forEach(tab => {
        tab.addEventListener('click', function() {
            const tabType = this.getAttribute('data-tab');
            // Aggiorniamo le classi attive
            document.querySelectorAll('.notifications-panel__tab').forEach(t => {
                t.classList.remove('active');
            });
            this.classList.add('active');

            // Carichiamo le notifiche
            loadNotifications(tabType);
        });
    });

    // Carichiamo le notifiche all'inizio
    loadNotifications('all');
}

function toggleNotificationsPanel(show) {
    const panel = document.getElementById('notificationsPanel');
    const backdrop = document.getElementById('notificationsBackdrop');

    if (panel) {
        if (show === undefined) {
            // Toggle
            panel.classList.toggle('show');
            backdrop.classList.toggle('show');

            // Se stiamo aprendo, ricarichiamo le notifiche
            if (panel.classList.contains('show')) {
                const activeTab = document.querySelector('.notifications-panel__tab.active');
                if (activeTab) {
                    loadNotifications(activeTab.getAttribute('data-tab'));
                } else {
                    loadNotifications('all');
                }
            }
        } else if (show) {
            // Mostra
            panel.classList.add('show');
            backdrop.classList.add('show');

            // Ricarichiamo le notifiche
            const activeTab = document.querySelector('.notifications-panel__tab.active');
            if (activeTab) {
                loadNotifications(activeTab.getAttribute('data-tab'));
            } else {
                loadNotifications('all');
            }
        } else {
            // Nascondi
            panel.classList.remove('show');
            backdrop.classList.remove('show');
        }
    }
}

function loadNotifications(type = 'all') {
    const content = document.querySelector('.notifications-panel__content');
    if (!content) return;

    // Mostriamo il loading
    content.innerHTML = `
        <div class="loading text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Caricamento...</span>
            </div>
        </div>
    `;

    // Facciamo la fetch per ottenere le notifiche
    fetch(`/api/notifications?type=${type}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        renderNotifications(data, content);
    })
    .catch(error => {
        content.innerHTML = `
            <div class="alert alert-danger m-3">
                Si è verificato un errore durante il caricamento delle notifiche.
            </div>
        `;
        console.error('Errore durante il caricamento delle notifiche:', error);
    });
}

function renderNotifications(data, container) {
    if (!data.notifications || data.notifications.length === 0) {
        container.innerHTML = `
            <div class="notifications-panel__empty">
                <i class="fas fa-bell-slash"></i>
                <p>Nessuna notifica disponibile</p>
            </div>
        `;
        return;
    }

    // Ottieni la configurazione
    const config = window.notificationsConfig || { showActionButtons: true, enableLinkToDetails: false };

    let html = '<ul class="notification-list">';

    data.notifications.forEach(notification => {
        let iconClass = 'fas fa-bell';
        let bgColor = 'primary';

        // Determiniamo l'icona in base al tipo di notifica
        if (notification.type.includes('DocumentRequest')) {
            iconClass = 'fas fa-file-upload';
            bgColor = 'danger';
        } else if (notification.type.includes('Document')) {
            iconClass = 'fas fa-file-alt';
            bgColor = 'success';
        } else if (notification.type.includes('Ticket')) {
            iconClass = 'fas fa-ticket-alt';
            bgColor = 'warning';
        } else if (notification.type.includes('Event')) {
            iconClass = 'fas fa-calendar-alt';
            bgColor = 'primary';
        } else if (notification.type.includes('Course')) {
            iconClass = 'fas fa-graduation-cap';
            bgColor = 'secondary';
        } else if (notification.type.includes('Checklist')) {
            iconClass = 'fas fa-tasks';
            bgColor = 'info';
        } else if (notification.type.includes('Badge')) {
            iconClass = 'fas fa-award';
            bgColor = 'warning';
        }

        // Analizziamo il tipo di notifica per determinare il testo del pulsante e il target
        let buttonText = 'Vedi dettagli';
        let url = notification.data.url || '#';

        if (notification.type.includes('DocumentRequest')) {
            buttonText = 'Vedi documento';
        } else if (notification.type.includes('Document')) {
            buttonText = 'Apri documento';
        } else if (notification.type.includes('Ticket')) {
            buttonText = 'Vedi ticket';
        } else if (notification.type.includes('Event')) {
            buttonText = 'Vai all\'evento';
        } else if (notification.type.includes('Course')) {
            buttonText = 'Vai al corso';
        } else if (notification.type.includes('Checklist')) {
            buttonText = 'Vedi checklist';
        } else if (notification.type.includes('Badge')) {
            buttonText = 'Vedi badge';
        }

        // Verifico se l'URL è impostato correttamente
        if (url === '#' || !url) {
            // Se non abbiamo un URL valido, cerchiamo di costruirne uno basato sul tipo
            if (notification.type.includes('Badge')) {
                url = '/employee/badges';
            } else if (notification.type.includes('Document')) {
                url = '/employee/documents';
            } else if (notification.type.includes('Ticket')) {
                // Estraiamo l'ID del ticket dai dati della notifica, se presente
                const ticketId = notification.data.ticket_id || '';
                url = ticketId ? `/employee/tickets/${ticketId}` : '/employee/tickets';
            } else if (notification.type.includes('Event')) {
                const eventId = notification.data.event_id || '';
                url = eventId ? `/employee/events/${eventId}` : '/employee/events';
            } else if (notification.type.includes('Course')) {
                url = '/employee/courses';
            } else if (notification.type.includes('Checklist')) {
                url = '/employee/checklists';
            } else {
                // Per altri tipi di notifiche, mandiamo alla pagina notifiche
                url = '/notifications';
            }
        } else if (url.startsWith('http')) {
            // Se l'URL è assoluto, controlliamo se è esterno o interno
            try {
                const urlObj = new URL(url);
                // Se il dominio è lo stesso della pagina corrente, rendilo relativo
                if (urlObj.hostname === window.location.hostname) {
                    url = urlObj.pathname + urlObj.search + urlObj.hash;
                }
            } catch (e) {
                // Se l'URL non è valido, impostiamo un URL di fallback
                console.error('URL non valido:', url);
                url = '/notifications';
            }
        }

        html += `
            <li class="notification-item ${!notification.read_at ? 'notification-item--unread' : ''}" data-id="${notification.id}">
                <div class="notification-item__icon bg-${bgColor} text-white">
                    <i class="${iconClass}"></i>
                </div>
                <div class="notification-item__content">
                    <h6 class="notification-item__title">${notification.data.title || 'Notifica'}</h6>
                    <p class="notification-item__message">${notification.data.message || ''}</p>
                    <div class="notification-item__meta">
                        <span>${new Date(notification.created_at).toLocaleString()}</span>
                        <div class="notification-item__actions">
                            ${config.enableLinkToDetails && url !== '#' ?
                                `<a href="${url}" class="btn btn-sm btn-primary" onclick="toggleNotificationsPanel(false)">
                                    <i class="fas fa-external-link-alt me-1"></i> ${buttonText}
                                </a>` : ''}
                            ${config.showActionButtons && !notification.read_at ?
                                `<button class="btn btn-sm btn-outline-secondary ms-2 mark-as-read-btn" data-id="${notification.id}">
                                    <i class="fas fa-check me-1"></i> Segna come letta
                                </button>` : ''}
                        </div>
                    </div>
                </div>
            </li>
        `;
    });

    html += '</ul>';

    // Se ci sono notifiche non lette, aggiungiamo anche un pulsante "Segna tutte come lette"
    if (data.unread_count > 0) {
        html += `
            <div class="p-3 bg-light">
                <button id="markAllReadBtn" class="btn btn-outline-primary btn-sm w-100">
                    <i class="fas fa-check-double me-1"></i> Segna tutte come lette
                </button>
            </div>
        `;
    }

    container.innerHTML = html;

    // Aggiungiamo gli event listener per i pulsanti
    container.querySelectorAll('.mark-as-read-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const id = this.getAttribute('data-id');
            markNotificationAsRead(id);
        });
    });

    const markAllBtn = document.getElementById('markAllReadBtn');
    if (markAllBtn) {
        markAllBtn.addEventListener('click', function(e) {
            e.preventDefault();
            markAllNotificationsAsRead();
        });
    }
}

function markNotificationAsRead(id) {
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
            const notification = document.querySelector(`.notification-item[data-id="${id}"]`);
            if (notification) {
                notification.classList.remove('notification-item--unread');
                const btn = notification.querySelector('.mark-as-read-btn');
                if (btn) btn.remove();
            }

            // Aggiorniamo il contatore
            updateNotificationCount();
        }
    })
    .catch(error => console.error('Errore:', error));
}

function markAllNotificationsAsRead() {
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
            // Rimuoviamo la classe unread da tutte le notifiche
            document.querySelectorAll('.notification-item--unread').forEach(item => {
                item.classList.remove('notification-item--unread');
            });

            // Rimuoviamo tutti i pulsanti
            document.querySelectorAll('.mark-as-read-btn').forEach(btn => {
                btn.remove();
            });

            // Rimuoviamo il pulsante "Segna tutte come lette"
            const markAllBtn = document.getElementById('markAllReadBtn');
            if (markAllBtn) {
                markAllBtn.parentElement.remove();
            }

            // Aggiorniamo il contatore
            updateNotificationCount();
        }
    })
    .catch(error => console.error('Errore:', error));
}

function updateNotificationCount() {
    // Facciamo una chiamata API per ottenere il conteggio aggiornato
    fetch('/api/notifications/count', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        const badge = document.querySelector('.notification-badge');

        if (data.count > 0) {
            // Se il badge non esiste, lo creiamo
            if (!badge) {
                const newBadge = document.createElement('span');
                newBadge.className = 'position-absolute top-0 start-100 translate-middle badge bg-danger rounded-circle notification-badge';
                newBadge.innerHTML = `
                    ${data.count}
                    <span class="visually-hidden">notifiche non lette</span>
                `;
                document.getElementById('notificationsToggle').appendChild(newBadge);
            } else {
                badge.textContent = data.count;
            }
        } else if (badge) {
            // Se non ci sono notifiche non lette, rimuoviamo il badge
            badge.remove();
        }

        // Inviamo un evento personalizzato per altre parti dell'app
        document.dispatchEvent(new CustomEvent('notifications-updated', {
            detail: { unreadCount: data.count }
        }));
    })
    .catch(error => console.error('Errore durante l\'aggiornamento del conteggio delle notifiche:', error));
}
</script>
