<x-layout>
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Dettagli Evento</h2>
            <a href="{{ route('employee.events.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i> Torna all'elenco
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
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
                        <div class="mb-4">
                            <h3 class="mb-3">{{ $event->title }}</h3>
                            <p>{{ $event->description }}</p>
                        </div>

                        <div class="mb-3">
                            <h5>Dettagli</h5>
                            <div class="list-group">
                                <div class="list-group-item">
                                    <div class="row">
                                        <div class="col-md-4"><strong>Data:</strong></div>
                                        <div class="col-md-8">{{ $event->start_date->format('d/m/Y H:i') }} - {{ $event->end_date->format('d/m/Y H:i') }}</div>
                                    </div>
                                </div>
                                <div class="list-group-item">
                                    <div class="row">
                                        <div class="col-md-4"><strong>Luogo:</strong></div>
                                        <div class="col-md-8">{{ $event->location }}</div>
                                    </div>
                                </div>
                                <div class="list-group-item">
                                    <div class="row">
                                        <div class="col-md-4"><strong>Tipo:</strong></div>
                                        <div class="col-md-8">{{ ucfirst($event->type) }}</div>
                                    </div>
                                </div>
                                @if($event->max_participants)
                                <div class="list-group-item">
                                    <div class="row">
                                        <div class="col-md-4"><strong>Partecipanti:</strong></div>
                                        <div class="col-md-8">
                                            {{ $event->participants()->count() }} / {{ $event->max_participants }}
                                            @if($event->participants()->count() >= $event->max_participants)
                                                <span class="badge bg-danger ms-2">Completo</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>

                        <div class="mb-4">
                            @if(!$isRegistered && !$event->isPast() && (!$event->max_participants || $event->participants()->count() < $event->max_participants))
                                <form action="{{ route('employee.events.register', $event) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-clipboard-check me-2"></i> Registrati
                                    </button>
                                </form>
                            @elseif($isRegistered && !$event->isPast())
                                @php
                                    $status = DB::table('event_participants')
                                        ->where('event_id', $event->id)
                                        ->where('user_id', Auth::id())
                                        ->value('status');
                                @endphp

                                @if($status === 'invited')
                                    <div class="alert alert-warning">
                                        <i class="fas fa-envelope me-2"></i> Sei stato invitato a questo evento. Conferma o rifiuta la tua partecipazione.
                                    </div>

                                    <form action="{{ route('employee.events.confirm', $event) }}" method="POST" class="d-inline me-2">
                                        @csrf
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-check-circle me-2"></i> Conferma Partecipazione
                                        </button>
                                    </form>

                                    <form action="{{ route('employee.events.decline', $event) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-danger">
                                            <i class="fas fa-times-circle me-2"></i> Rifiuta Invito
                                        </button>
                                    </form>
                                @else
                                    <div class="alert alert-success">
                                        <i class="fas fa-check-circle me-2"></i> Sei registrato a questo evento
                                    </div>

                                    <form action="{{ route('employee.events.cancel', $event) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-danger">
                                            <i class="fas fa-calendar-times me-2"></i> Annulla Registrazione
                                        </button>
                                    </form>
                                @endif
                            @elseif($event->isPast())
                                <div class="alert alert-secondary">
                                    <i class="fas fa-history me-2"></i> Questo evento è già passato
                                </div>
                            @elseif($event->max_participants && $event->participants()->count() >= $event->max_participants)
                                <div class="alert alert-danger">
                                    <i class="fas fa-users-slash me-2"></i> Questo evento ha raggiunto il numero massimo di partecipanti
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                @if($isRegistered && $event->isPast() && $participantInfo->attended)
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Il tuo feedback</h5>
                        </div>
                        <div class="card-body">
                            @if($participantInfo->feedback)
                                <div class="mb-3">
                                    <strong>La tua valutazione:</strong>
                                    <div class="mt-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $participantInfo->rating)
                                                <i class="fas fa-star text-warning"></i>
                                            @else
                                                <i class="far fa-star text-warning"></i>
                                            @endif
                                        @endfor
                                        <span class="ms-2">{{ $participantInfo->rating }}/5</span>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <strong>Il tuo commento:</strong>
                                    <p class="mt-1">{{ $participantInfo->feedback }}</p>
                                </div>

                                <div class="text-muted">
                                    <small>Inviato il {{ $participantInfo->updated_at->format('d/m/Y H:i') }}</small>
                                </div>
                            @else
                                <form action="{{ route('employee.events.feedback', $event) }}" method="POST">
                                    @csrf

                                    <div class="mb-3">
                                        <label for="rating" class="form-label">Valutazione</label>
                                        <div class="rating">
                                            <div class="btn-group" role="group">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <input type="radio" class="btn-check" name="rating" id="rating{{ $i }}" value="{{ $i }}" {{ old('rating') == $i ? 'checked' : '' }} required>
                                                    <label class="btn btn-outline-warning" for="rating{{ $i }}">{{ $i }}</label>
                                                @endfor
                                            </div>
                                        </div>
                                        @error('rating')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="feedback" class="form-label">Il tuo feedback</label>
                                        <textarea class="form-control @error('feedback') is-invalid @enderror" id="feedback" name="feedback" rows="3" required>{{ old('feedback') }}</textarea>
                                        @error('feedback')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="d-flex justify-content-end">
                                        <button type="submit" class="btn btn-primary">Invia feedback</button>
                                    </div>
                                </form>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Stato Registrazione</h5>
                    </div>
                    <div class="card-body">
                        @if($isRegistered)
                            <div class="text-center mb-4">
                                @if($participantInfo->status === 'registered')
                                    <div class="alert alert-primary">
                                        <i class="fas fa-info-circle fa-2x mb-2"></i>
                                        <h5>Registrazione in attesa</h5>
                                        <p>La tua registrazione è in attesa di conferma.</p>
                                    </div>
                                @elseif($participantInfo->status === 'confirmed')
                                    <div class="alert alert-success">
                                        <i class="fas fa-check-circle fa-2x mb-2"></i>
                                        <h5>Registrazione confermata</h5>
                                        <p>Sei confermato per questo evento.</p>
                                    </div>
                                @elseif($participantInfo->status === 'cancelled')
                                    <div class="alert alert-danger">
                                        <i class="fas fa-times-circle fa-2x mb-2"></i>
                                        <h5>Registrazione annullata</h5>
                                        <p>La tua registrazione è stata annullata.</p>
                                    </div>
                                @endif
                            </div>

                            @if($event->isPast())
                                <div class="text-center mb-3">
                                    @if($participantInfo->attended)
                                        <div class="alert alert-success">
                                            <i class="fas fa-user-check fa-2x mb-2"></i>
                                            <h5>Hai partecipato</h5>
                                            <p>La tua presenza è stata registrata.</p>
                                        </div>
                                    @else
                                        <div class="alert alert-danger">
                                            <i class="fas fa-user-times fa-2x mb-2"></i>
                                            <h5>Non hai partecipato</h5>
                                            <p>La tua assenza è stata registrata.</p>
                                        </div>
                                    @endif
                                </div>
                            @endif

                            @if(!$event->isPast() && $participantInfo->status !== 'cancelled')
                                <form action="{{ route('employee.events.cancel', $event) }}" method="POST" onsubmit="return confirm('Sei sicuro di voler annullare la tua registrazione?')">
                                    @csrf
                                    <button type="submit" class="btn btn-danger w-100">
                                        <i class="fas fa-times-circle me-2"></i> Annulla registrazione
                                    </button>
                                </form>
                            @endif
                        @else
                            <div class="text-center mb-4">
                                <i class="fas fa-calendar-alt fa-4x text-muted mb-3"></i>
                                <h5>Non sei registrato</h5>
                                <p class="text-muted">Non sei ancora registrato a questo evento.</p>
                            </div>

                            @if(!$event->isPast())
                                @if($event->max_participants && $event->participants()->count() >= $event->max_participants)
                                    <div class="alert alert-warning text-center">
                                        <i class="fas fa-exclamation-circle me-2"></i>
                                        Evento al completo
                                    </div>
                                @else
                                    <form action="{{ route('employee.events.register', $event) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="fas fa-user-plus me-2"></i> Registrati all'evento
                                        </button>
                                    </form>
                                @endif
                            @endif
                        @endif
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Calendario</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="/employee/events/{{ $event->id }}/calendar" class="btn btn-outline-primary">
                                <i class="fas fa-calendar-plus me-2"></i> Aggiungi al calendario
                            </a>

                            @if($isRegistered && $participantInfo->status === 'confirmed')
                                <a href="{{ route('employee.events.download-ticket', $event) }}" class="btn btn-outline-success">
                                    <i class="fas fa-ticket-alt me-2"></i> Scarica biglietto
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">Altri partecipanti</h5>
            </div>
            <div class="card-body">
                @if($participants->count() > 0)
                    <div class="row">
                        @foreach($participants as $participant)
                            <div class="col-md-3 mb-3">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <div class="mb-3">
                                            <div class="avatar-circle mx-auto">
                                                <span class="initials">{{ substr($participant->name, 0, 1) }}</span>
                                            </div>
                                        </div>
                                        <h6 class="card-title">{{ $participant->name }}</h6>
                                        <p class="card-text text-muted small">{{ $participant->department }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if($participants->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $participants->appends(request()->query())->links() }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-users fa-4x text-muted mb-3"></i>
                        <h4>Nessun altro partecipante</h4>
                        <p class="text-muted">Non ci sono ancora altri partecipanti per questo evento.</p>
                    </div>
                @endif
            </div>
        </div>

        @if($event->materials && $event->materials->count() > 0)
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">Materiali didattici</h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        @foreach($event->materials as $material)
                            <a href="{{ route('employee.events.download-material', ['event' => $event->id, 'material' => $material->id]) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas {{ $material->getIconClass() }} me-3"></i>
                                    {{ $material->title }}
                                    <small class="text-muted d-block">{{ $material->description }}</small>
                                </div>
                                <span class="badge bg-primary rounded-pill">
                                    <i class="fas fa-download"></i>
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        @if($event->isPast() && $isRegistered && $participantInfo->attended)
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">Certificato di partecipazione</h5>
                </div>
                <div class="card-body text-center">
                    <i class="fas fa-certificate fa-4x text-success mb-3"></i>
                    <h4>Hai completato questo evento!</h4>
                    <p class="text-muted">Puoi scaricare il tuo certificato di partecipazione.</p>
                    <a href="{{ route('employee.events.download-certificate', $event) }}" class="btn btn-success mt-2">
                        <i class="fas fa-download me-2"></i> Scarica certificato
                    </a>
                </div>
            </div>
        @endif
    </div>

    <style>
        .avatar-circle {
            width: 60px;
            height: 60px;
            background-color: #007bff;
            text-align: center;
            border-radius: 50%;
            -webkit-border-radius: 50%;
            -moz-border-radius: 50%;
        }

        .initials {
            position: relative;
            top: 15px;
            font-size: 25px;
            line-height: 30px;
            color: #fff;
            font-weight: bold;
        }

        .rating .btn-outline-warning:hover,
        .rating .btn-check:checked + .btn-outline-warning {
            color: #000;
            background-color: #ffc107;
            border-color: #ffc107;
        }
    </style>
</x-layout>
