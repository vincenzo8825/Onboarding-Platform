<x-layout>
    <x-slot name="sidebar">
        @include('components.sidebar')
    </x-slot>
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-0 fw-bold">{{ $badge->name }}</h2>
                <div class="text-muted">
                    <span class="badge badge-type badge-type--{{ $badge->type }} mt-2">
                        {{ $badge->type === 'achievement' ? 'Achievement' : ($badge->type === 'completion' ? 'Completamento' : 'Speciale') }}
                    </span>
                    @if($badge->is_automatic)
                        <span class="badge bg-success ms-2">Automatico</span>
                    @endif
                </div>
            </div>
            <div>
                <a href="{{ route('admin.badges.index') }}" class="btn btn-outline-secondary me-2">
                    <i class="fas fa-arrow-left me-1"></i> Indietro
                </a>
                <a href="{{ route('admin.badges.edit', $badge) }}" class="btn btn-warning">
                    <i class="fas fa-edit me-1"></i> Modifica
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i> Dettagli Badge</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-4 mb-md-0 text-center">
                                <div class="badge-display mb-3" style="background-color: rgba({{ hexToRgb($badge->color) }}, 0.15); color: {{ $badge->color }}; border: 2px solid {{ $badge->color }};">
                                    <i class="{{ $badge->icon }} fa-3x"></i>
                                </div>
                                <div class="badge-points">
                                    <span class="badge-points__value">{{ $badge->points }}</span>
                                    <span class="badge-points__label">punti</span>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <div class="mb-3">
                                    <h6 class="text-muted mb-1">Descrizione</h6>
                                    <p>{{ $badge->description }}</p>
                                </div>
                                <div class="mb-3">
                                    <h6 class="text-muted mb-1">Criteri di assegnazione</h6>
                                    <p>{{ $badge->is_automatic ? $badge->criteria : 'Assegnazione manuale da parte degli amministratori.' }}</p>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <h6 class="text-muted mb-1">Data creazione</h6>
                                        <p>{{ $badge->created_at->format('d/m/Y') }}</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <h6 class="text-muted mb-1">Ultimo aggiornamento</h6>
                                        <p>{{ $badge->updated_at->format('d/m/Y') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="fas fa-users me-2"></i> Statistiche Assegnazioni</h5>
                    </div>
                    <div class="card-body">
                        <div class="badge-stats badge-stats--vertical">
                            <div class="badge-stat">
                                <div class="badge-stat__value">{{ $badge->users->count() }}</div>
                                <div class="badge-stat__label">Dipendenti con questo badge</div>
                            </div>
                            <div class="badge-stat">
                                <div class="badge-stat__value">{{ $badge->created_at->diffInDays() < 1 ? 'Oggi' : $badge->created_at->diffForHumans() }}</div>
                                <div class="badge-stat__label">Creato</div>
                            </div>
                            <div class="badge-stat">
                                <div class="badge-stat__value">{{ $badge->user_badges()->orderBy('created_at', 'desc')->first() ? $badge->user_badges()->orderBy('created_at', 'desc')->first()->created_at->diffForHumans() : 'Mai' }}</div>
                                <div class="badge-stat__label">Ultima assegnazione</div>
                            </div>
                        </div>
                    </div>
                </div>

                @if(!$badge->is_automatic)
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="fas fa-trophy me-2"></i> Azioni Badge</h5>
                    </div>
                    <div class="card-body">
                        <a href="{{ route('admin.badge-assignments.create', ['badge' => $badge->id]) }}" class="btn btn-primary w-100 mb-2">
                            <i class="fas fa-user-plus me-2"></i> Assegna ad un dipendente
                        </a>
                        <button type="button" class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#deleteBadgeModal">
                            <i class="fas fa-trash me-2"></i> Elimina badge
                        </button>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <div class="card shadow-sm mt-4">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-award me-2"></i> Dipendenti con questo badge</h5>
                @if($badge->users->count() > 0 && !$badge->is_automatic)
                <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#removeAllModal">
                    <i class="fas fa-trash me-1"></i> Rimuovi tutte le assegnazioni
                </button>
                @endif
            </div>
            <div class="card-body">
                @if($badge->users->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th>Dipendente</th>
                                    <th>Email</th>
                                    <th>Data assegnazione</th>
                                    @if(!$badge->is_automatic)
                                    <th class="text-center">Azioni</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($badge->user_badges()->orderBy('created_at', 'desc')->get() as $userBadge)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar me-3">
                                                    @if($userBadge->user->profile_image)
                                                        <img src="{{ asset('storage/' . $userBadge->user->profile_image) }}" alt="{{ $userBadge->user->name }}" class="rounded-circle" width="40" height="40">
                                                    @else
                                                        <div class="avatar-placeholder">
                                                            {{ substr($userBadge->user->name, 0, 1) }}{{ substr($userBadge->user->surname, 0, 1) }}
                                                        </div>
                                                    @endif
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $userBadge->user->name }} {{ $userBadge->user->surname }}</h6>
                                                    <small class="text-muted">{{ $userBadge->user->job_title }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $userBadge->user->email }}</td>
                                        <td>{{ $userBadge->created_at->format('d/m/Y H:i') }}</td>
                                        @if(!$badge->is_automatic)
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#removeBadgeModal{{ $userBadge->id }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-users fa-4x text-muted mb-3 employee-dashboard__empty-state-icon"></i>
                        <h4>Nessun dipendente con questo badge</h4>
                        <p class="text-muted">Questo badge non è stato ancora assegnato ad alcun dipendente.</p>
                        @if(!$badge->is_automatic)
                        <a href="{{ route('admin.badge-assignments.create', ['badge' => $badge->id]) }}" class="btn btn-primary mt-3">
                            <i class="fas fa-user-plus me-2"></i> Assegna ad un dipendente
                        </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Delete Badge Modal -->
    <div class="modal fade" id="deleteBadgeModal" tabindex="-1" aria-labelledby="deleteBadgeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteBadgeModalLabel">Conferma eliminazione</h5>
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

    <!-- Remove All Assignments Modal -->
    @if($badge->users->count() > 0 && !$badge->is_automatic)
    <div class="modal fade" id="removeAllModal" tabindex="-1" aria-labelledby="removeAllModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="removeAllModalLabel">Conferma rimozione</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Sei sicuro di voler rimuovere questo badge da <strong>tutti i {{ $badge->users->count() }} dipendenti</strong>?</p>
                    <p class="text-danger">Questa azione rimuoverà tutte le assegnazioni del badge {{ $badge->name }}.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                    <form action="{{ route('admin.badge-assignments.remove-all', $badge) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Rimuovi tutte</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Individual Remove Badge Modals -->
    @if(!$badge->is_automatic)
    @foreach($badge->user_badges as $userBadge)
    <div class="modal fade" id="removeBadgeModal{{ $userBadge->id }}" tabindex="-1" aria-labelledby="removeBadgeModalLabel{{ $userBadge->id }}" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="removeBadgeModalLabel{{ $userBadge->id }}">Conferma rimozione</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Sei sicuro di voler rimuovere il badge <strong>{{ $badge->name }}</strong> da <strong>{{ $userBadge->user->name }} {{ $userBadge->user->surname }}</strong>?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                    <form action="{{ route('admin.badge-assignments.destroy', $userBadge) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Rimuovi</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endforeach
    @endif
</x-layout>

@php
function hexToRgb($hex) {
    $hex = str_replace('#', '', $hex);
    if(strlen($hex) == 3) {
        $r = hexdec(substr($hex, 0, 1).substr($hex, 0, 1));
        $g = hexdec(substr($hex, 1, 1).substr($hex, 1, 1));
        $b = hexdec(substr($hex, 2, 1).substr($hex, 2, 1));
    } else {
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
    }
    return "$r, $g, $b";
}
@endphp
