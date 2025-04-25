<x-layout>
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Gestione Badge</h2>
            <a href="{{ route('admin.badges.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i> Nuovo Badge
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

        <div class="card">
            <div class="card-body">
                @if($badges->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Badge</th>
                                    <th>Nome</th>
                                    <th>Descrizione</th>
                                    <th>Automatico</th>
                                    <th>Assegnazioni</th>
                                    <th>Azioni</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($badges as $badge)
                                    <tr>
                                        <td>
                                            <div class="badge-icon" style="color: {{ $badge->color }}">
                                                <i class="{{ $badge->icon }} fa-2x"></i>
                                            </div>
                                        </td>
                                        <td>{{ $badge->name }}</td>
                                        <td>{{ Str::limit($badge->description, 50) }}</td>
                                        <td>
                                            @if($badge->is_automatic)
                                                <span class="badge bg-success">Sì</span>
                                            @else
                                                <span class="badge bg-secondary">No</span>
                                            @endif
                                        </td>
                                        <td>{{ $badge->users->count() }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.badges.show', $badge) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.badges.edit', $badge) }}" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteBadgeModal{{ $badge->id }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-award fa-4x text-muted mb-3"></i>
                        <h4>Nessun badge creato</h4>
                        <p class="text-muted">Crea il tuo primo badge per premiare i dipendenti.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Delete Badge Modals -->
    @foreach($badges as $badge)
        <div class="modal fade" id="deleteBadgeModal{{ $badge->id }}" tabindex="-1" aria-labelledby="deleteBadgeModalLabel{{ $badge->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteBadgeModalLabel{{ $badge->id }}">Conferma eliminazione</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Sei sicuro di voler eliminare il badge <strong>{{ $badge->name }}</strong>?</p>
                        <p class="text-danger">Questa azione è irreversibile e rimuoverà il badge da tutti gli utenti a cui è stato assegnato.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                        <form action="{{ route('admin.badges.destroy', $badge) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Elimina</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</x-layout>