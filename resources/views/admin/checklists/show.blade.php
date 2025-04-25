<x-layout>
    <x-slot name="sidebar">
        @include('components.sidebar')
    </x-slot>

    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>{{ $checklist->name }}</h2>
            <div>
                <a href="{{ route('admin.checklists.edit', $checklist) }}" class="btn btn-warning me-2">
                    <i class="fas fa-edit me-1"></i> Modifica
                </a>
                <a href="{{ route('admin.checklists.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Torna alla lista
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
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Informazioni</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Stato:</span>
                                @if($checklist->isActive())
                                    <span class="badge bg-success">Attiva</span>
                                @else
                                    <span class="badge bg-danger">Inattiva</span>
                                @endif
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Default per nuovi utenti:</span>
                                @if($checklist->isDefault())
                                    <span class="badge bg-primary">Sì</span>
                                @else
                                    <span class="badge bg-secondary">No</span>
                                @endif
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Assegnabile a:</span>
                                @if($checklist->isAssignableToAll())
                                    <span class="badge bg-primary">Tutti</span>
                                @elseif($checklist->isAssignableToAdmin())
                                    <span class="badge bg-warning">Admin</span>
                                @else
                                    <span class="badge bg-info">Dipendenti</span>
                                @endif
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Creata da:</span>
                                <span>{{ $checklist->creator->name }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Data creazione:</span>
                                <span>{{ $checklist->created_at->format('d/m/Y') }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Ultimo aggiornamento:</span>
                                <span>{{ $checklist->updated_at->format('d/m/Y H:i') }}</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">Descrizione</h5>
                    </div>
                    <div class="card-body">
                        @if($checklist->description)
                            {{ $checklist->description }}
                        @else
                            <div class="text-muted fst-italic">Nessuna descrizione disponibile</div>
                        @endif
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">Azioni</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#assignChecklistModal">
                                <i class="fas fa-user-plus me-2"></i> Assegna a dipendenti
                            </button>
                            <a href="{{ route('admin.checklists.items.create', $checklist) }}" class="btn btn-success">
                                <i class="fas fa-plus me-2"></i> Aggiungi elemento
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Elementi della checklist</h5>
                        <span class="badge bg-primary">{{ $checklist->items->count() }} elementi</span>
                    </div>
                    <div class="card-body p-0">
                        @if($checklist->items->isEmpty())
                            <div class="text-center p-4">
                                <i class="fas fa-tasks fs-1 text-muted mb-3"></i>
                                <p class="mb-1">Nessun elemento nella checklist</p>
                                <small class="text-muted">Aggiungi elementi per rendere questa checklist utilizzabile</small>
                                <div class="mt-3">
                                    <a href="{{ route('admin.checklists.items.create', $checklist) }}" class="btn btn-primary">
                                        <i class="fas fa-plus me-1"></i> Aggiungi primo elemento
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="list-group list-group-flush">
                                @foreach($checklist->items->sortBy('order') as $item)
                                    <div class="list-group-item list-group-item-action">
                                        <div class="d-flex w-100 justify-content-between align-items-center">
                                            <h6 class="mb-1">{{ $item->order }}. {{ $item->title }}</h6>
                                            <div>
                                                <a href="{{ route('admin.checklists.items.edit', ['checklist' => $checklist, 'item' => $item]) }}" class="btn btn-sm btn-outline-warning me-1">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.checklists.items.destroy', ['checklist' => $checklist, 'item' => $item]) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Sei sicuro di voler eliminare questo elemento?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>

                                        @if($item->description)
                                            <p class="mb-1 text-muted">{{ $item->description }}</p>
                                        @endif

                                        <div class="mt-2">
                                            <span class="badge bg-secondary me-1">{{ $item->due_date ? 'Scadenza: ' . $item->due_date->format('d/m/Y') : 'Nessuna scadenza' }}</span>

                                            @if($item->requires_file)
                                                <span class="badge bg-info me-1">Richiede file</span>
                                            @endif

                                            @if($item->requires_approval)
                                                <span class="badge bg-warning me-1">Richiede approvazione</span>
                                            @endif

                                            @if($item->is_mandatory)
                                                <span class="badge bg-danger">Obbligatorio</span>
                                            @else
                                                <span class="badge bg-secondary">Opzionale</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal per assegnare la checklist -->
    <div class="modal fade" id="assignChecklistModal" tabindex="-1" aria-labelledby="assignChecklistModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="assignChecklistModalLabel">Assegna Checklist a Dipendenti</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.checklists.assign-multiple', $checklist) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <p>Seleziona i dipendenti a cui assegnare questa checklist:</p>
                        <div class="mb-3">
                            <select name="user_ids[]" multiple class="form-select" size="10" required>
                                <!-- Qui andrà il loop degli utenti con ruolo dipendente -->
                                @foreach(\App\Models\User::whereHas('roles', function($query) {
                                    $query->where('name', 'employee');
                                })->orderBy('name')->get() as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Tieni premuto CTRL per selezionare più dipendenti</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annulla</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-check me-1"></i> Assegna Checklist
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout>
