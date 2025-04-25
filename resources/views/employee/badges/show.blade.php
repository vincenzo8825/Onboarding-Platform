<x-layout>
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Dettagli Badge</h2>
            <a href="{{ route('employee.badges.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Torna ai miei badge
            </a>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <div class="row">
            <div class="col-md-5">
                <!-- Badge Card -->
                <div class="card mb-4">
                    <div class="card-body text-center py-5">
                        <div class="badge-icon mb-4" style="color: {{ $badgeDetails->color }}">
                            <i class="{{ $badgeDetails->icon }} fa-5x"></i>
                        </div>
                        <h3 class="mb-2">{{ $badgeDetails->name }}</h3>
                        <p class="mb-4 text-muted">{{ $badgeDetails->description }}</p>

                        <div class="d-flex justify-content-center mb-4">
                            @php
                                $badgeType = $badgeDetails->type === 'achievement' ? 'bg-info' :
                                           ($badgeDetails->type === 'completion' ? 'bg-success' : 'bg-secondary');
                                $badgeLabel = $badgeDetails->type === 'achievement' ? 'Achievement' :
                                           ($badgeDetails->type === 'completion' ? 'Completamento' : 'Speciale');
                            @endphp
                            <span class="badge {{ $badgeType }} px-3 py-2">
                                {{ $badgeLabel }}
                            </span>
                        </div>

                        <!-- Toggle Featured -->
                        <form action="{{ route('employee.badges.toggle-featured', $badgeDetails->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn {{ $badgeDetails->pivot->is_featured ? 'btn-warning' : 'btn-outline-warning' }}">
                                <i class="fas {{ $badgeDetails->pivot->is_featured ? 'fa-star' : 'fa-star' }} me-1"></i>
                                {{ $badgeDetails->pivot->is_featured ? 'Rimuovi dai preferiti' : 'Imposta come preferito' }}
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Badge Details -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Informazioni Assegnazione</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                            <span class="text-muted">Data assegnazione:</span>
                            <span class="fw-bold">{{ $badgeDetails->pivot->awarded_at->format('d/m/Y H:i') }}</span>
                        </div>

                        <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                            <span class="text-muted">Valore XP:</span>
                            <span class="fw-bold">{{ $badgeDetails->xp_value ?? 0 }} punti</span>
                        </div>

                        @if($awardedBy)
                        <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                            <span class="text-muted">Assegnato da:</span>
                            <span class="fw-bold">{{ $awardedBy->name }}</span>
                        </div>
                        @endif

                        @if($badgeDetails->pivot->award_reason)
                        <div class="mb-3">
                            <div class="text-muted mb-2">Motivazione:</div>
                            <div class="p-3 bg-light rounded">
                                <i class="fas fa-quote-left me-2 text-muted"></i>
                                {{ $badgeDetails->pivot->award_reason }}
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-7">
                <!-- Badge Criteria -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Criteri di Assegnazione</h5>
                    </div>
                    <div class="card-body">
                        @if(isset($badgeDetails->criteria) && $badgeDetails->criteria)
                            <p>{{ $badgeDetails->criteria }}</p>
                        @else
                            <p class="text-muted">Questo badge può essere assegnato a discrezione dell'amministratore o ottenuto completando obiettivi specifici.</p>
                        @endif
                    </div>
                </div>

                <!-- Related Achievements -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Altre persone con questo badge</h5>
                    </div>
                    <div class="card-body">
                        @if($otherUsers->count() > 0)
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Utente</th>
                                            <th>Dipartimento</th>
                                            <th>Data assegnazione</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($otherUsers as $user)
                                            <tr>
                                                <td>
                                                    @if(isset($user->profile_photo) && $user->profile_photo)
                                                        <img src="{{ asset('storage/' . $user->profile_photo) }}" alt="{{ $user->name }}" class="rounded-circle me-2" width="32" height="32">
                                                    @else
                                                        <div class="avatar-placeholder rounded-circle me-2 d-inline-flex align-items-center justify-content-center bg-light" style="width: 32px; height: 32px;">
                                                            <span>{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                                        </div>
                                                    @endif
                                                    {{ $user->name }}
                                                </td>
                                                <td>{{ $user->department->name ?? 'N/A' }}</td>
                                                <td>{{ $user->pivot->awarded_at->format('d/m/Y') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="text-center mt-3">
                                <p class="text-muted small">
                                    <i class="fas fa-info-circle me-1"></i>
                                    @php
                                        $badgeFunc = $badgeDetails->users();
                                        $totalUsers = $badgeFunc->count();
                                        $otherCount = $totalUsers - 1;
                                    @endphp
                                    {{ $otherCount }} {{ $otherCount == 1 ? 'altra persona ha' : 'altre persone hanno' }} questo badge.
                                </p>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                <h5>Sei l'unico ad avere questo badge!</h5>
                                <p class="text-muted">Questo badge è un riconoscimento esclusivo per i tuoi risultati.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>
