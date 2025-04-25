<x-layout>
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Gestione Partecipanti</h2>
            <a href="{{ route('admin.events.show', $event) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i> Torna ai dettagli evento
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Informazioni Evento</h5>
                    </div>
                    <div class="card-body">
                        <h4>{{ $event->title }}</h4>
                        <p class="text-muted">{{ Str::limit($event->description, 100) }}</p>

                        <div class="mb-2">
                            <strong>Luogo:</strong> {{ $event->location }}
                        </div>

                        <div class="mb-2">
                            <strong>Data:</strong> {{ $event->start_date->format('d/m/Y H:i') }} - {{ $event->end_date->format('d/m/Y H:i') }}
                        </div>

                        <div class="mb-2">
                            <strong>Tipo:</strong> {{ ucfirst($event->type) }}
                        </div>

                        <div class="mb-2">
                            <strong>Partecipanti:</strong>
                            {{ $event->participants()->count() }}
                            @if($event->max_participants)
                                / {{ $event->max_participants }}
                            @endif
                        </div>

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
                </div>
            </div>

            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Invita Partecipanti</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.events.add-participants', $event) }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label for="user_ids" class="form-label">Seleziona dipendenti da invitare</label>
                                <select class="form-select" id="user_ids" name="user_ids[]" multiple size="10" required>
                                    @foreach($availableUsers as $user)
                                        <option value="{{ $user->id }}">
                                            {{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">Tieni premuto Ctrl (o Cmd su Mac) per selezionare più dipendenti</small>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-envelope me-2"></i> Invia Inviti
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>
