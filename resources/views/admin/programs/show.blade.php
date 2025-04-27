<x-layout>
    <x-slot name="sidebar">
        @include('components.sidebar')
    </x-slot>
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Dettagli Programma: {{ $program->name }}</h2>
            <div>
                <a href="{{ route('admin.programs.edit', $program) }}" class="btn btn-warning">
                    <i class="fas fa-edit me-2"></i> Modifica
                </a>
                <a href="{{ route('admin.programs.index') }}" class="btn btn-secondary ms-2">
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
            <div class="col-md-5">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Informazioni Programma</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <th style="width: 30%">Nome:</th>
                                <td>{{ $program->name }}</td>
                            </tr>
                            <tr>
                                <th>Descrizione:</th>
                                <td>{{ $program->description ?: 'Nessuna descrizione disponibile' }}</td>
                            </tr>
                            <tr>
                                <th>Data di Inizio:</th>
                                <td>{{ $program->start_date->format('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <th>Data di Fine:</th>
                                <td>{{ $program->end_date ? $program->end_date->format('d/m/Y') : 'Nessuna' }}</td>
                            </tr>
                            <tr>
                                <th>Stato:</th>
                                <td>
                                    @if($program->isActive())
                                        <span class="badge bg-success">Attivo</span>
                                    @else
                                        <span class="badge bg-secondary">Inattivo</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Partecipanti:</th>
                                <td>{{ $program->users->count() }} utenti</td>
                            </tr>
                            <tr>
                                <th>Data Creazione:</th>
                                <td>{{ $program->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Ultimo Aggiornamento:</th>
                                <td>{{ $program->updated_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Aggiungi Partecipanti</h5>
                    </div>
                    <div class="card-body">
                        @if($availableUsers->count() > 0)
                            <form action="{{ route('admin.programs.add-users', $program) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="user_ids" class="form-label">Seleziona Utenti</label>
                                    <select class="form-select @error('user_ids') is-invalid @enderror"
                                            id="user_ids" name="user_ids[]" multiple size="10">
                                        @foreach($availableUsers as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                        @endforeach
                                    </select>
                                    <div class="form-text">Tieni premuto CTRL per selezionare più utenti</div>
                                    @error('user_ids')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-plus me-2"></i> Aggiungi Utenti
                                </button>
                            </form>
                        @else
                            <div class="text-center py-3">
                                <i class="fas fa-users-slash fa-3x text-muted mb-3"></i>
                                <p class="mb-0">Tutti gli utenti sono già iscritti a questo programma.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-7">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Partecipanti al Programma</h5>
                        <span class="badge bg-info">{{ $program->users->count() }} partecipanti</span>
                    </div>
                    <div class="card-body">
                        @if($program->users->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Nome</th>
                                            <th>Email</th>
                                            <th>Data Iscrizione</th>
                                            <th>Azioni</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($program->users as $user)
                                            <tr>
                                                <td>{{ $user->name }}</td>
                                                <td>{{ $user->email }}</td>
                                                <td>{{ $user->pivot->created_at->format('d/m/Y H:i') }}</td>
                                                <td>
                                                    <form action="{{ route('admin.programs.remove-user', [$program, $user]) }}" method="POST"
                                                          onsubmit="return confirm('Sei sicuro di voler rimuovere questo utente dal programma?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger">
                                                            <i class="fas fa-user-minus"></i> Rimuovi
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-users fa-4x text-muted mb-3"></i>
                                <h4>Nessun partecipante iscritto</h4>
                                <p class="text-muted">Questo programma non ha ancora partecipanti.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>
