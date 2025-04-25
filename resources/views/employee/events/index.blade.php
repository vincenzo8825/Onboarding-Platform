<x-layout>
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Eventi e Formazione</h2>
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
                <form action="{{ route('employee.events.index') }}" method="GET" class="row g-3">
                    <div class="col-md-3">
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
                    
                    <div class="col-md-3">
                        <label for="status" class="form-label">Stato</label>
                        <select class="form-select" id="status" name="status">
                            <option value="upcoming" {{ $status === 'upcoming' ? 'selected' : '' }}>Futuri</option>
                            <option value="ongoing" {{ $status === 'ongoing' ? 'selected' : '' }}>In corso</option>
                            <option value="past" {{ $status === 'past' ? 'selected' : '' }}>Passati</option>
                            <option value="all" {{ $status === 'all' ? 'selected' : '' }}>Tutti</option>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="view" class="form-label">Visualizza</label>
                        <select class="form-select" id="view" name="view">
                            <option value="all" {{ $view === 'all' ? 'selected' : '' }}>Tutti gli eventi</option>
                            <option value="registered" {{ $view === 'registered' ? 'selected' : '' }}>I miei eventi</option>
                        </select>
                    </div>
                    
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">Filtra</button>
                        <a href="{{ route('employee.events.index') }}" class="btn btn-secondary">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="row">
            @if($events->count() > 0)
                @foreach($events as $event)
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge bg-primary">{{ ucfirst($event->type) }}</span>
                                    @if($event->is_mandatory)
                                        <span class="badge bg-danger">Obbligatorio</span>
                                    @endif
                                </div>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">{{ $event->title }}</h5>
                                <p class="card-text text-muted">{{ Str::limit($event->description, 100) }}</p>
                                
                                <div class="mb-2">
                                    <i class="fas fa-map-marker-alt text-primary me-2"></i> {{ $event->location }}
                                </div>
                                
                                <div class="mb-2">
                                    <i class="fas fa-calendar-alt text-primary me-2"></i> {{ $event->start_date->format('d/m/Y') }}
                                </div>
                                
                                <div class="mb-2">
                                    <i class="fas fa-clock text-primary me-2"></i> {{ $event->start_date->format('H:i') }} - {{ $event->end_date->format('H:i') }}
                                </div>
                                
                                <div class="mb-2">
                                    <i class="fas fa-users text-primary me-2"></i> 
                                    {{ $event->participants()->count() }}
                                    @if($event->max_participants)
                                        / {{ $event->max_participants }}
                                    @endif
                                    partecipanti
                                </div>
                                
                                <div class="mt-3">
                                    @if(in_array($event->id, $registeredEvents))
                                        <span class="badge bg-success mb-2">Sei registrato</span>
                                    @endif
                                    
                                    @if($event->isPast())
                                        <span class="badge bg-secondary mb-2">Evento passato</span>
                                    @elseif($event->isInProgress())
                                        <span class="badge bg-success mb-2">In corso</span>
                                    @endif
                                </div>
                            </div>
                            <div class="card-footer">
                                <a href="{{ route('employee.events.show', $event) }}" class="btn btn-primary w-100">
                                    <i class="fas fa-info-circle me-2"></i> Dettagli
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
                
                <div class="col-12">
                    <div class="d-flex justify-content-center mt-4">
                        {{ $events->links() }}
                    </div>
                </div>
            @else
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-calendar-alt fa-4x text-muted mb-3"></i>
                            <h4>Nessun evento trovato</h4>
                            <p class="text-muted">Non ci sono eventi che corrispondono ai criteri di ricerca.</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-layout>