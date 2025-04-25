<x-layout>
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Dettagli Evento</h2>
            <div>
                <a href="{{ route('admin.events.edit', $event) }}" class="btn btn-warning me-2">
                    <i class="fas fa-edit me-2"></i> Modifica
                </a>
                <a href="{{ route('admin.events.participants', $event) }}" class="btn btn-info me-2">
                    <i class="fas fa-users me-2"></i> Gestisci Partecipanti
                </a>
                <a href="{{ route('admin.events.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i> Torna all'elenco
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Informazioni Evento</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <h3>{{ $event->title }}</h3>
                            <span class="badge bg-primary">{{ ucfirst($event->type) }}</span>
                            @if($event->is_mandatory)
                                <span class="badge bg-danger ms-2">Obbligatorio</span>
                            @endif

                            @if($event->isPast())
                                <span class="badge bg-secondary ms-2">Passato</span>
                            @elseif($event->isInProgress())
                                <span class="badge bg-success ms-2">In corso</span>
                            @else
                                <span class="badge bg-info ms-2">Futuro</span>
                            @endif
                        </div>

                        <div class="mb-3">
                            <strong>Descrizione:</strong>
                            <p class="mt-2">{{ $event->description ?: 'Nessuna descrizione disponibile' }}</p>
                        </div>

                        <div class="mb-3">
                            <strong>Luogo:</strong>
                            <p>{{ $event->location }}</p>
                        </div>

                        <div class="mb-3">
                            <strong>Data e ora inizio:</strong>
                            <p>{{ $event->start_date->format('d/m/Y H:i') }}</p>
                        </div>

                        <div class="mb-3">
                            <strong>Data e ora fine:</strong>
                            <p>{{ $event->end_date->format('d/m/Y H:i') }}</p>
                        </div>

                        <div class="mb-3">
                            <strong>Durata:</strong>
                            <p>{{ $event->start_date->diffForHumans($event->end_date, ['parts' => 2]) }}</p>
                        </div>

                        <div class="mb-3">
                            <strong>Numero massimo partecipanti:</strong>
                            <p>{{ $event->max_participants ?: 'Illimitato' }}</p>
                        </div>

                        <div class="mb-3">
                            <strong>Creato da:</strong>
                            <p>{{ $event->creator->name }}</p>
                        </div>

                        <div class="mb-3">
                            <strong>Data creazione:</strong>
                            <p>{{ $event->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Statistiche Partecipanti</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3 text-center">
                            <h3 class="mb-0">{{ $event->participants()->count() }}</h3>
                            <p class="text-muted">
                                Partecipanti totali
                                @if($event->max_participants)
                                    / {{ $event->max_participants }}
                                @endif
                            </p>

                            @if($event->max_participants)
                                <div class="progress mt-2">
                                    @php
                                        $percentage = ($event->participants()->count() / $event->max_participants) * 100;
                                    @endphp
                                    <div class="progress-bar {{ $percentage >= 90 ? 'bg-danger' : 'bg-success' }}" role="progressbar" style="width: {{ $percentage }}%" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <small class="text-muted">{{ round($percentage) }}% occupato</small>
                            @endif
                        </div>

                        <hr>

                        <div class="mb-3">
                            <h6>Stato partecipanti:</h6>
                            <ul class="list-group mt-2">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Registrati
                                    <span class="badge bg-primary rounded-pill">{{ $event->participants()->wherePivot('status', 'registered')->count() }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Confermati
                                    <span class="badge bg-success rounded-pill">{{ $event->participants()->wherePivot('status', 'confirmed')->count() }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Annullati
                                    <span class="badge bg-danger rounded-pill">{{ $event->participants()->wherePivot('status', 'cancelled')->count() }}</span>
                                </li>
                            </ul>
                        </div>

                        @if($event->isPast())
                            <div class="mb-3">
                                <h6>Presenze:</h6>
                                <ul class="list-group mt-2">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Presenti
                                        <span class="badge bg-success rounded-pill">{{ $event->participants()->wherePivot('attended', true)->count() }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Assenti
                                        <span class="badge bg-danger rounded-pill">{{ $event->participants()->wherePivot('attended', false)->wherePivot('status', '!=', 'cancelled')->count() }}</span>
                                    </li>
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Elenco Partecipanti</h5>
                <a href="{{ route('admin.events.participants', $event) }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-user-plus me-1"></i> Aggiungi Partecipanti
                </a>
            </div>
            <div class="card-body">
                @if($participants->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Email</th>
                                    <th>Stato</th>
                                    @if($event->isPast())
                                        <th>Presenza</th>
                                    @endif
                                    <th>Data registrazione</th>
                                    <th>Azioni</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($participants as $participant)
                                    <tr>
                                        <td>{{ $participant->name }}</td>
                                        <td>{{ $participant->email }}</td>
                                        <td>
                                            @if($participant->pivot->status === 'registered')
                                                <span class="badge bg-primary">Registrato</span>
                                            @elseif($participant->pivot->status === 'confirmed')
                                                <span class="badge bg-success">Confermato</span>
                                            @elseif($participant->pivot->status === 'cancelled')
                                                <span class="badge bg-danger">Annullato</span>
                                            @endif
                                        </td>
                                        @if($event->isPast())
                                            <td>
                                                @if($participant->pivot->attended)
                                                    <span class="badge bg-success">Presente</span>
                                                @else
                                                    <span class="badge bg-danger">Assente</span>
                                                @endif
                                            </td>
                                        @endif
                                        <td>{{ $participant->pivot->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <form action="{{ route('admin.events.participants', $event) }}" method="POST" onsubmit="return confirm('Sei sicuro di voler rimuovere questo partecipante?')">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="participant_id" value="{{ $participant->id }}">
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-user-minus me-1"></i> Rimuovi
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        {{ $participants->appends(request()->query())->links() }}
                    </div>

                    @if($event->isPast() && $participants->where('pivot.status', 'confirmed')->count() > 0)
                        <div class="mt-4">
                            <form action="{{ route('admin.events.mark-attendance', $event) }}" method="POST">
                                @csrf
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0">Registra presenze</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Nome</th>
                                                        <th>Presente</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($participants->where('pivot.status', 'confirmed') as $participant)
                                                        <tr>
                                                            <td>{{ $participant->name }}</td>
                                                            <td>
                                                                <div class="form-check form-switch">
                                                                    <input class="form-check-input" type="checkbox" id="attendance{{ $participant->id }}" name="attendance[{{ $participant->id }}]" value="1" {{ $participant->pivot->attended ? 'checked' : '' }}>
                                                                    <label class="form-check-label" for="attendance{{ $participant->id }}">
                                                                        {{ $participant->pivot->attended ? 'Presente' : 'Assente' }}
                                                                    </label>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="d-flex justify-content-end mt-3">
                                            <button type="submit" class="btn btn-primary">Salva presenze</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    @endif
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-users fa-4x text-muted mb-3"></i>
                        <h4>Nessun partecipante</h4>
                        <p class="text-muted">Non ci sono ancora partecipanti per questo evento.</p>
                        <a href="{{ route('admin.events.participants', $event) }}" class="btn btn-primary mt-2">
                            <i class="fas fa-user-plus me-2"></i> Aggiungi partecipanti
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layout>
