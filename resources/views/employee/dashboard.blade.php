@php
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Auth;
@endphp

<x-layout>
    <x-slot name="sidebar">
        @include('components.sidebar')
    </x-slot>

    <div class="container-fluid py-4">
        <h1 class="mb-4">Dashboard Dipendente</h1>

        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Notifiche -->
        @if (count(auth()->user()->unreadNotifications) > 0)
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center bg-primary text-white">
                <h5 class="m-0 font-weight-bold">Notifiche recenti</h5>
                <a href="#" id="mark-all-read" class="text-white">
                    <i class="fas fa-check-double"></i> Segna tutte come lette
                </a>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @foreach(auth()->user()->unreadNotifications as $notification)
                        <div class="list-group-item notification-item" data-id="{{ $notification->id }}">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1 font-weight-bold">
                                    @if (isset($notification->data['checklist_name']))
                                        <i class="fas fa-clipboard-list text-primary me-2"></i>
                                    @elseif (isset($notification->data['document_type']))
                                        <i class="fas fa-file-alt text-warning me-2"></i>
                                    @elseif (isset($notification->data['ticket_id']))
                                        <i class="fas fa-ticket-alt text-info me-2"></i>
                                    @else
                                        <i class="fas fa-bell text-secondary me-2"></i>
                                    @endif
                                    {{ $notification->data['message'] ?? 'Nuova notifica' }}
                                </h6>
                                <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                            </div>
                            @if (isset($notification->data['url']))
                                <a href="{{ $notification->data['url'] }}" class="btn btn-sm btn-outline-primary mt-2 mark-as-read" data-id="{{ $notification->id }}">
                                    Visualizza dettagli
                                </a>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Stato Onboarding -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h5 class="m-0 font-weight-bold">Il tuo percorso di onboarding</h5>
                <span class="badge bg-primary py-2 px-3">{{ $onboardingPercentage ?? 0 }}% Completato</span>
            </div>
            <div class="card-body">
                <div class="progress mb-3" style="height: 25px;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $onboardingPercentage ?? 0 }}%"
                         aria-valuenow="{{ $onboardingPercentage ?? 0 }}" aria-valuemin="0" aria-valuemax="100">
                        {{ $onboardingPercentage ?? 0 }}%
                    </div>
                </div>

                <div class="row text-center mt-4">
                    <div class="col-md-3 mb-3">
                        <div class="p-3 border rounded">
                            <h3 class="text-primary mb-0">{{ $myTasksCompleted ?? 0 }} / {{ $myTotalTasks ?? 0 }}</h3>
                            <p class="mb-0 text-muted">Attività Completate</p>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="p-3 border rounded">
                            <h3 class="text-info mb-0">{{ $myCoursesCompleted ?? 0 }} / {{ $myTotalCourses ?? 0 }}</h3>
                            <p class="mb-0 text-muted">Corsi Completati</p>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="p-3 border rounded">
                            <h3 class="text-success mb-0">{{ $documentsApproved ?? 0 }} / {{ $documentsSubmitted ?? 0 }}</h3>
                            <p class="mb-0 text-muted">Documenti Approvati</p>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="p-3 border rounded">
                            <h3 class="text-warning mb-0">{{ $daysAtCompany ?? 0 }}</h3>
                            <p class="mb-0 text-muted">Giorni in Azienda</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Checklist e Task -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h5 class="m-0 font-weight-bold">Le tue Attività</h5>
                        <a href="{{ route('employee.checklists.index') }}" class="btn btn-sm btn-primary">Visualizza Tutte</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @forelse($checklistItems ?? [] as $item)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="form-check-input task-checkbox" id="task-{{ $item->id }}"
                                                   {{ $item->status === 'completed' ? 'checked' : '' }}
                                                   data-id="{{ $item->id }}" data-action="update-task-status">
                                            <label class="form-check-label {{ $item->status === 'completed' ? 'text-decoration-line-through' : '' }}"
                                                   for="task-{{ $item->id }}">
                                                {{ $item->text }}
                                            </label>
                                        </div>
                                        @if($item->due_date)
                                            <span class="badge {{ \Carbon\Carbon::parse($item->due_date)->isPast() ? 'bg-danger' : 'bg-info' }}">
                                                {{ \Carbon\Carbon::parse($item->due_date)->format('d/m/Y') }}
                                            </span>
                                        @endif
                                    </div>
                                    @if($item->description)
                                        <p class="text-muted small mb-0 mt-1">{{ Str::limit($item->description, 100) }}</p>
                                    @endif
                                </div>
                            @empty
                                <div class="text-center py-4">
                                    <i class="fas fa-clipboard-check fa-3x text-muted mb-3"></i>
                                    <h6>Nessuna attività da completare</h6>
                                    <p class="text-muted mb-0">Tutte le attività sono state completate</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Corsi e Formazione -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h5 class="m-0 font-weight-bold">I tuoi Corsi Formativi</h5>
                        <a href="{{ route('employee.courses.index') }}" class="btn btn-sm btn-primary">Visualizza Tutti</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @forelse($courses ?? [] as $course)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <h6 class="mb-0">{{ $course->title }}</h6>
                                        <span class="badge {{ $course->pivot->status === 'completed' ? 'bg-success' : ($course->pivot->status === 'in_progress' ? 'bg-warning' : 'bg-secondary') }}">
                                            {{ $course->pivot->status === 'completed' ? 'Completato' : ($course->pivot->status === 'in_progress' ? 'In corso' : 'Non iniziato') }}
                                        </span>
                                    </div>
                                    <p class="text-muted small mb-2">{{ Str::limit($course->description, 100) }}</p>
                                    <div class="d-flex justify-content-between">
                                        <small class="text-muted">{{ $course->duration_minutes }} min</small>
                                        <a href="{{ route('employee.courses.show', $course) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-play-circle me-1"></i> {{ $course->pivot->status === 'completed' ? 'Rivedi' : 'Inizia' }}
                                        </a>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-4">
                                    <i class="fas fa-graduation-cap fa-3x text-muted mb-3"></i>
                                    <h6>Nessun corso disponibile</h6>
                                    <p class="text-muted mb-0">Non hai corsi assegnati</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Documenti -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h5 class="m-0 font-weight-bold">Documenti</h5>
                        <a href="{{ route('employee.documents.index') }}" class="btn btn-sm btn-primary">Gestisci Documenti</a>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="card border-left-primary h-100 py-2">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                    Documenti da Caricare
                                                </div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $documentsTodo ?? 0 }}</div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-upload fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="card border-left-success h-100 py-2">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                    Documenti Approvati
                                                </div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $documentsApproved ?? 0 }}</div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive mt-3">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Nome</th>
                                        <th>Data Caricamento</th>
                                        <th>Stato</th>
                                        <th>Azioni</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($documents ?? [] as $document)
                                        <tr>
                                            <td>{{ $document->name }}</td>
                                            <td>{{ $document->created_at->format('d/m/Y') }}</td>
                                            <td>
                                                <span class="badge {{ $document->status === 'approved' ? 'bg-success' : ($document->status === 'rejected' ? 'bg-danger' : 'bg-warning') }}">
                                                    {{ $document->status === 'approved' ? 'Approvato' : ($document->status === 'rejected' ? 'Rifiutato' : 'In revisione') }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('employee.documents.show', $document) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">Nessun documento caricato</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Supporto e Ticket -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h5 class="m-0 font-weight-bold">Supporto</h5>
                        <div>
                            <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#newTicketModal">
                                <i class="fas fa-plus me-1"></i> Nuovo Ticket
                            </button>
                            <a href="{{ route('employee.tickets.index') }}" class="btn btn-sm btn-primary">Tutti i Ticket</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Oggetto</th>
                                        <th>Data</th>
                                        <th>Stato</th>
                                        <th>Ultima Risposta</th>
                                        <th>Azioni</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($tickets ?? [] as $ticket)
                                        <tr>
                                            <td>{{ Str::limit($ticket->subject, 30) }}</td>
                                            <td>{{ $ticket->created_at->format('d/m/Y') }}</td>
                                            <td>
                                                <span class="badge {{ $ticket->status === 'open' ? 'bg-danger' : ($ticket->status === 'in_progress' ? 'bg-warning' : 'bg-success') }}">
                                                    {{ $ticket->status === 'open' ? 'Aperto' : ($ticket->status === 'in_progress' ? 'In lavorazione' : 'Risolto') }}
                                                </span>
                                            </td>
                                            <td>{{ $ticket->updated_at->diffForHumans() }}</td>
                                            <td>
                                                <a href="{{ route('employee.tickets.show', $ticket) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">Nessun ticket aperto</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Eventi e Calendario -->
        <div class="row mb-4">
            <div class="col-lg-12">
                <div class="card shadow">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h5 class="m-0 font-weight-bold">Prossimi Eventi</h5>
                        <a href="{{ route('employee.events.index') }}" class="btn btn-sm btn-primary">Visualizza Calendario</a>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @forelse($events ?? [] as $event)
                                <div class="col-md-4 mb-3">
                                    <div class="card border-left-info h-100">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <h6 class="mb-0">{{ $event->title }}</h6>
                                                <span class="badge bg-info">{{ $event->type }}</span>
                                            </div>
                                            <p class="text-muted small">{{ Str::limit($event->description, 80) }}</p>
                                            <div class="d-flex justify-content-between align-items-center mt-3">
                                                <small class="text-muted">
                                                    <i class="far fa-calendar-alt me-1"></i> {{ \Carbon\Carbon::parse($event->start_date)->format('d/m/Y H:i') }}
                                                </small>
                                                <div>
                                                    @php
                                                        $participantStatus = DB::table('event_participants')
                                                            ->where('event_id', $event->id)
                                                            ->where('user_id', Auth::id())
                                                            ->value('status');
                                                    @endphp

                                                    @if($participantStatus === 'invited')
                                                        <span class="badge bg-warning me-2">Invitato</span>
                                                    @elseif($participantStatus === 'confirmed')
                                                        <span class="badge bg-success me-2">Confermato</span>
                                                    @endif

                                                    <a href="{{ route('employee.events.show', $event) }}" class="btn btn-sm btn-outline-info">
                                                        <i class="fas fa-eye me-1"></i> Dettagli
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <div class="text-center py-4">
                                        <i class="far fa-calendar-alt fa-3x text-muted mb-3"></i>
                                        <h6>Nessun evento in programma</h6>
                                        <p class="text-muted mb-0">Non hai eventi pianificati</p>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Aggiungo il riquadro per le richieste di documenti -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Richieste Documenti</h5>
                </div>
                <div class="card-body">
                    @php
                        $pendingRequests = \App\Models\DocumentRequest::where('employee_id', Auth::id())
                            ->where('status', 'pending')
                            ->latest()
                            ->take(5)
                            ->get();
                    @endphp

                    @if($pendingRequests->count() > 0)
                        <div class="list-group">
                            @foreach($pendingRequests as $request)
                                <a href="{{ route('employee.document-requests.show', $request) }}" class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">{{ $request->document_type }}</h6>
                                        <small class="text-muted">{{ $request->created_at->diffForHumans() }}</small>
                                    </div>
                                    <p class="mb-1 text-truncate">{{ $request->description ?: 'Nessuna descrizione' }}</p>

                                    @if($request->due_date)
                                        <small class="text-{{ \Carbon\Carbon::parse($request->due_date)->isPast() ? 'danger' : 'muted' }}">
                                            Scadenza: {{ \Carbon\Carbon::parse($request->due_date)->format('d/m/Y') }}
                                        </small>
                                    @endif
                                </a>
                            @endforeach
                        </div>

                        @if($pendingRequests->count() > 0)
                            <div class="d-flex justify-content-end mt-3">
                                <a href="{{ route('employee.document-requests.index') }}" class="btn btn-sm btn-outline-primary">
                                    Visualizza tutte
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                            <p>Non ci sono richieste di documenti in attesa.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Nuovo Ticket -->
    <div class="modal fade" id="newTicketModal" tabindex="-1" aria-labelledby="newTicketModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('employee.tickets.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="newTicketModalLabel">Crea Nuovo Ticket</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="subject" class="form-label">Oggetto</label>
                            <input type="text" class="form-control" id="subject" name="subject" required>
                        </div>
                        <div class="mb-3">
                            <label for="category" class="form-label">Categoria</label>
                            <select class="form-select" id="category" name="category" required>
                                <option value="">Seleziona categoria</option>
                                <option value="technical">Problemi Tecnici</option>
                                <option value="administrative">Richieste Amministrative</option>
                                <option value="onboarding">Processo di Onboarding</option>
                                <option value="other">Altro</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">Messaggio</label>
                            <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                        <button type="submit" class="btn btn-primary">Invia Ticket</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Gestione checkbox task
            document.querySelectorAll('.task-checkbox').forEach(function(checkbox) {
                checkbox.addEventListener('change', function() {
                    const taskId = this.dataset.id;
                    const status = this.checked ? 'completed' : 'pending';
                    const label = this.nextElementSibling;

                    if (this.checked) {
                        label.classList.add('text-decoration-line-through');
                    } else {
                        label.classList.remove('text-decoration-line-through');
                    }

                    // Invia lo stato aggiornato al server
                    fetch(`/employee/checklist-items/${taskId}/update-status`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ status: status })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Aggiorna l'interfaccia utente se necessario
                        }
                    })
                    .catch(error => console.error('Errore:', error));
                });
            });

            // Gestione mark-as-read per singole notifiche
            const markAsReadButtons = document.querySelectorAll('.mark-as-read');
            markAsReadButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    const notificationId = this.getAttribute('data-id');
                    markAsRead(notificationId);
                });
            });

            // Gestione mark-all-read
            const markAllReadButton = document.getElementById('mark-all-read');
            if (markAllReadButton) {
                markAllReadButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    markAllAsRead();
                });
            }

            // Funzione per marcare una notifica come letta
            function markAsRead(id) {
                fetch(`/notifications/${id}/mark-as-read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const notificationItem = document.querySelector(`.notification-item[data-id="${id}"]`);
                        if (notificationItem) {
                            notificationItem.classList.add('bg-light');
                            setTimeout(() => {
                                notificationItem.remove();

                                // Se non ci sono più notifiche, nascondi il container
                                const notificationItems = document.querySelectorAll('.notification-item');
                                if (notificationItems.length === 0) {
                                    const notificationContainer = document.querySelector('.card.shadow.mb-4');
                                    if (notificationContainer) {
                                        notificationContainer.remove();
                                    }
                                }
                            }, 500);
                        }
                    }
                });
            }

            // Funzione per marcare tutte le notifiche come lette
            function markAllAsRead() {
                fetch('/notifications/mark-all-as-read', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const notificationContainer = document.querySelector('.card.shadow.mb-4');
                        if (notificationContainer) {
                            notificationContainer.classList.add('fade-out');
                            setTimeout(() => {
                                notificationContainer.remove();
                            }, 500);
                        }
                    }
                });
            }
        });
    </script>
    @endpush
</x-layout>
