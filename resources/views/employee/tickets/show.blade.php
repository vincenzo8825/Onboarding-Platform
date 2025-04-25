<x-layout>
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Ticket #{{ $ticket->id }}: {{ $ticket->title }}</h2>
            <a href="{{ route('employee.tickets.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i> Torna all'elenco
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Informazioni Ticket</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>Stato:</strong>
                            @if($ticket->status === 'open')
                                <span class="badge bg-primary">Aperto</span>
                            @elseif($ticket->status === 'in_progress')
                                <span class="badge bg-info">In lavorazione</span>
                            @elseif($ticket->status === 'resolved')
                                <span class="badge bg-success">Risolto</span>
                            @elseif($ticket->status === 'closed')
                                <span class="badge bg-secondary">Chiuso</span>
                            @endif
                        </div>

                        <div class="mb-3">
                            <strong>Priorità:</strong>
                            @if($ticket->priority === 'low')
                                <span class="badge bg-success">Bassa</span>
                            @elseif($ticket->priority === 'medium')
                                <span class="badge bg-info">Media</span>
                            @elseif($ticket->priority === 'high')
                                <span class="badge bg-warning">Alta</span>
                            @elseif($ticket->priority === 'urgent')
                                <span class="badge bg-danger">Urgente</span>
                            @endif
                        </div>

                        <div class="mb-3">
                            <strong>Categoria:</strong>
                            <span>{{ $ticket->category }}</span>
                        </div>

                        <div class="mb-3">
                            <strong>Creato il:</strong>
                            <span>{{ $ticket->created_at->format('d/m/Y H:i') }}</span>
                        </div>

                        <div class="mb-3">
                            <strong>Assegnato a:</strong>
                            <span>{{ $ticket->assignee ? $ticket->assignee->name : 'Non assegnato' }}</span>
                        </div>

                        @if($ticket->closed_at)
                            <div class="mb-3">
                                <strong>Chiuso il:</strong>
                                <span>{{ $ticket->closed_at->format('d/m/Y H:i') }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="ticket-message">
                            <div class="d-flex align-items-center mb-2">
                                <div class="me-3">
                                    @if($ticket->creator && $ticket->creator->profile_photo_path)
                                        <img src="{{ Storage::url($ticket->creator->profile_photo_path) }}" alt="Profile Photo" class="rounded-circle" width="40" height="40">
                                    @else
                                        <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i class="fas fa-user"></i>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <h5 class="mb-0">{{ $ticket->creator ? $ticket->creator->name : 'Utente' }}</h5>
                                    <small class="text-muted">{{ $ticket->created_at->format('d/m/Y H:i') }}</small>
                                </div>
                            </div>
                            <div class="ticket-content mt-3">
                                <p>{!! nl2br(e($ticket->description)) !!}</p>
                            </div>
                        </div>
                    </div>
                </div>

                @if(isset($replies) && $replies->count() > 0)
                    <h5 class="mb-3">Risposte</h5>

                    @foreach($replies as $reply)
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="ticket-reply">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="me-3">
                                            @if($reply->user && $reply->user->profile_photo_path)
                                                <img src="{{ Storage::url($reply->user->profile_photo_path) }}" alt="Profile Photo" class="rounded-circle" width="40" height="40">
                                            @else
                                                <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                    <i class="fas fa-user"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <h5 class="mb-0">
                                                {{ $reply->user ? $reply->user->name : 'Utente eliminato' }}
                                                @if($reply->user && $reply->user->role === 'admin')
                                                    <span class="badge bg-primary ms-2">Staff</span>
                                                @endif
                                            </h5>
                                            <small class="text-muted">{{ $reply->created_at->format('d/m/Y H:i') }}</small>
                                        </div>
                                    </div>
                                    <div class="reply-content mt-3">
                                        <p>{!! nl2br(e($reply->message)) !!}</p>

                                        @if($reply->attachment)
                                            <div class="mt-3">
                                                <a href="{{ Storage::url($reply->attachment) }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                                    <i class="fas fa-paperclip me-1"></i> Visualizza allegato
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif

                @if($ticket->status !== 'closed')
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="mb-0">Aggiungi risposta</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('employee.tickets.reply', $ticket) }}" method="POST" enctype="multipart/form-data">
                                @csrf

                                <div class="mb-3">
                                    <label for="message" class="form-label">Messaggio</label>
                                    <textarea class="form-control @error('message') is-invalid @enderror" id="message" name="message" rows="4" required>{{ old('message') }}</textarea>
                                    @error('message')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="attachment" class="form-label">Allegato (opzionale)</label>
                                    <input type="file" class="form-control @error('attachment') is-invalid @enderror" id="attachment" name="attachment">
                                    <small class="form-text text-muted">Dimensione massima: 10MB</small>
                                    @error('attachment')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <button type="submit" class="btn btn-primary">Invia risposta</button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="alert alert-info mt-4">
                        <i class="fas fa-info-circle me-2"></i> Questo ticket è chiuso. Se hai bisogno di ulteriore assistenza, crea un nuovo ticket.
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layout>
