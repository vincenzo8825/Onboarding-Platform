<x-layout>
    <x-slot name="sidebar">
        @include('components.sidebar')
    </x-slot>

    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Gestione Checklist</h2>
            <a href="{{ route('admin.checklists.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i> Nuova Checklist
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
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h5 class="mb-0">Tutte le checklist</h5>
                    </div>
                    <div class="col-md-4">
                        <form action="{{ route('admin.checklists.index') }}" method="GET" class="d-flex">
                            <input type="text" name="search" class="form-control form-control-sm me-2" placeholder="Cerca checklist..." value="{{ request('search') }}">
                            <button type="submit" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nome</th>
                                <th>Elementi</th>
                                <th>Stato</th>
                                <th>Assegnabile a</th>
                                <th>Creata da</th>
                                <th>Data creazione</th>
                                <th>Azioni</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($checklists as $checklist)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.checklists.show', $checklist) }}" class="text-decoration-none fw-medium">
                                            {{ $checklist->name }}
                                        </a>
                                        @if($checklist->is_default)
                                            <span class="badge bg-info ms-2">Default</span>
                                        @endif
                                    </td>
                                    <td>{{ $checklist->total_items_count }}</td>
                                    <td>
                                        @if($checklist->is_active)
                                            <span class="badge bg-success">Attiva</span>
                                        @else
                                            <span class="badge bg-danger">Inattiva</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($checklist->assignable_to === 'all')
                                            <span class="badge bg-primary">Tutti</span>
                                        @elseif($checklist->assignable_to === 'admin')
                                            <span class="badge bg-warning">Admin</span>
                                        @else
                                            <span class="badge bg-info">Dipendenti</span>
                                        @endif
                                    </td>
                                    <td>{{ $checklist->creator->name }}</td>
                                    <td>{{ $checklist->created_at->format('d/m/Y') }}</td>
                                    <td>
                                        <div class="d-flex">
                                            <a href="{{ route('admin.checklists.show', $checklist) }}" class="btn btn-sm btn-outline-primary me-1" title="Visualizza">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.checklists.edit', $checklist) }}" class="btn btn-sm btn-outline-warning me-1" title="Modifica">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.checklists.destroy', $checklist) }}" method="POST" class="d-inline" onsubmit="return confirm('Sei sicuro di voler eliminare questa checklist? Questa azione non può essere annullata.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Elimina">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <p class="mb-0">Nessuna checklist trovata</p>
                                        <small class="text-muted">Crea la tua prima checklist per iniziare a gestire l'onboarding</small>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                {{ $checklists->links() }}
            </div>
        </div>

        <div class="card bg-light">
            <div class="card-body">
                <h5 class="card-title">Guida rapida</h5>
                <p class="card-text">Le checklist sono strumenti essenziali per guidare i dipendenti attraverso il processo di onboarding. Puoi:</p>
                <ul class="mb-0">
                    <li>Creare checklist personalizzate per diversi ruoli o reparti</li>
                    <li>Aggiungere elementi con descrizioni dettagliate</li>
                    <li>Richiedere l'upload di documenti specifici</li>
                    <li>Monitorare lo stato di completamento per ciascun dipendente</li>
                </ul>
            </div>
        </div>
    </div>
</x-layout>
