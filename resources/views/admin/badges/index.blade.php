<x-layout>
    <x-slot name="sidebar">
        @include('components.sidebar')
    </x-slot>
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0 fw-bold">Gestione Badge</h2>
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

        <div class="card shadow-sm">
            <div class="card-body">
                @if($badges->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-center" style="width: 80px;">Badge</th>
                                    <th>Nome</th>
                                    <th>Descrizione</th>
                                    <th class="text-center">Automatico</th>
                                    <th class="text-center">Assegnazioni</th>
                                    <th class="text-center" style="width: 130px;">Azioni</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($badges as $badge)
                                    <tr>
                                        <td class="text-center">
                                            <div class="badge-icon" style="color: {{ $badge->color }}; width: 40px; height: 40px; margin: 0 auto;">
                                                <i class="{{ $badge->icon }} fa-lg"></i>
                                            </div>
                                        </td>
                                        <td>
                                            <strong>{{ $badge->name }}</strong>
                                            <span class="badge badge-type badge-type--{{ $badge->type }} d-block mt-1" style="width: fit-content">
                                                {{ $badge->type === 'achievement' ? 'Achievement' : ($badge->type === 'completion' ? 'Completamento' : 'Speciale') }}
                                            </span>
                                        </td>
                                        <td>{{ Str::limit($badge->description, 80) }}</td>
                                        <td class="text-center">
                                            @if($badge->is_automatic)
                                                <span class="badge bg-success">Sì</span>
                                            @else
                                                <span class="badge bg-secondary">No</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="badge-counter">
                                                <i class="fas fa-users"></i> {{ $badge->users->count() }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.badges.show', $badge) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.badges.edit', $badge) }}" class="btn btn-sm btn-outline-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteBadgeModal{{ $badge->id }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if(method_exists($badges, 'links'))
                    <div class="d-flex justify-content-center mt-4">
                        {{ $badges->links() }}
                    </div>
                    @endif
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-award fa-4x text-muted mb-3 employee-dashboard__empty-state-icon"></i>
                        <h4>Nessun badge creato</h4>
                        <p class="text-muted">Crea il tuo primo badge per premiare i dipendenti.</p>
                        <a href="{{ route('admin.badges.create') }}" class="btn btn-primary mt-3">
                            <i class="fas fa-plus me-2"></i> Crea il primo badge
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <div class="card mt-4 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i> Statistiche Badge</h5>
            </div>
            <div class="card-body">
                <div class="badge-stats">
                    <div class="badge-stat">
                        <div class="badge-stat__value">{{ $badges->count() }}</div>
                        <div class="badge-stat__label">Badge Totali</div>
                    </div>
                    <div class="badge-stat">
                        <div class="badge-stat__value">{{ $badges->where('is_automatic', true)->count() }}</div>
                        <div class="badge-stat__label">Badge Automatici</div>
                    </div>
                    <div class="badge-stat">
                        <div class="badge-stat__value">{{ $badges->sum(function($badge) { return $badge->users->count(); }) }}</div>
                        <div class="badge-stat__label">Assegnazioni</div>
                    </div>
                </div>
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
