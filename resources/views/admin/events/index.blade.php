<x-layout>
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Gestione Eventi</h2>
            <a href="{{ route('admin.events.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i> Nuovo Evento
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Filtri</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.events.index') }}" method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label for="type" class="form-label">Tipo</label>
                        <select class="form-select" id="type" name="type">
                            <option value="">Tutti</option>
                            @foreach($types as $eventType)
                                <option value="{{ $eventType }}" {{ $type === $eventType ? 'selected' : '' }}>
                                    {{ ucfirst($eventType) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="status" class="form-label">Stato</label>
                        <select class="form-select" id="status" name="status">
                            <option value="all" {{ $status === 'all' ? 'selected' : '' }}>Tutti gli eventi</option>
                            <option value="upcoming" {{ $status === 'upcoming' ? 'selected' : '' }}>Solo futuri</option>
                            <option value="ongoing" {{ $status === 'ongoing' ? 'selected' : '' }}>In corso</option>
                            <option value="past" {{ $status === 'past' ? 'selected' : '' }}>Passati</option>
                        </select>
                    </div>

                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">Filtra</button>
                        <a href="{{ route('admin.events.index') }}" class="btn btn-secondary">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                @if($events->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Titolo</th>
                                    <th>Tipo</th>
                                    <th>Luogo</th>
                                    <th>Data inizio</th>
                                    <th>Data fine</th>
                                    <th>Partecipanti</th>
                                    <th>Stato</th>
                                    <th>Azioni</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($events as $event)
                                    <tr>
                                        <td>{{ $event->id }}</td>
                                        <td>{{ Str::limit($event->title, 30) }}</td>
                                        <td>{{ ucfirst($event->type) }}</td>
                                        <td>{{ Str::limit($event->location, 20) }}</td>
                                        <td>{{ $event->start_date->format('d/m/Y H:i') }}</td>
                                        <td>{{ $event->end_date->format('d/m/Y H:i') }}</td>
                                        <td>
                                            {{ $event->participants()->count() }}
                                            @if($event->max_participants)
                                                / {{ $event->max_participants }}
                                            @endif
                                        </td>
                                        <td>
                                            @if($event->isPast())
                                                <span class="badge bg-secondary">Passato</span>
                                            @elseif($event->isInProgress())
                                                <span class="badge bg-success">In corso</span>
                                            @else
                                                <span class="badge bg-primary">Futuro</span>
                                            @endif

                                            @if($event->is_mandatory)
                                                <span class="badge bg-danger ms-1">Obbligatorio</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('admin.events.show', $event) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.events.edit', $event) }}" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="{{ route('admin.events.participants', $event) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-users"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $event->id }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>

                                            <!-- Modal di conferma eliminazione -->
                                            <div class="modal fade" id="deleteModal{{ $event->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $event->id }}" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="deleteModalLabel{{ $event->id }}">Conferma eliminazione</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            Sei sicuro di voler eliminare l'evento "{{ $event->title }}"?
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                                                            <form action="{{ route('admin.events.destroy', $event) }}" method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-danger">Elimina</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        {{ $events->links() }}
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-calendar-alt fa-4x text-muted mb-3"></i>
                        <h4>Nessun evento trovato</h4>
                        <p class="text-muted">Non ci sono eventi che corrispondono ai criteri di ricerca.</p>
                        <a href="{{ route('admin.events.create') }}" class="btn btn-primary mt-2">
                            <i class="fas fa-plus me-2"></i> Crea il primo evento
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layout>
