<x-layout>
    <x-slot name="sidebar">
        @include('components.sidebar')
    </x-slot>
    <div class="container py-4">
        <!-- Intestazione con statistiche -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card bg-light border-0 shadow-sm">
                    <div class="card-body text-center py-4">
                        <h2 class="mb-3 fw-bold">I miei Badge e Riconoscimenti</h2>
                        <div class="badge-stats">
                            <div class="badge-stat">
                                <div class="badge-stat__value">{{ $badges->count() }}</div>
                                <div class="badge-stat__label">Badge Ottenuti</div>
                            </div>
                            @if($badges->count() > 0)
                            <div class="badge-stat">
                                <div class="badge-stat__value">{{ $badges->sum('xp_value') }}</div>
                                <div class="badge-stat__label">Punti Totali</div>
                            </div>
                            <div class="badge-stat">
                                @php
                                    $oldestBadge = $badges->sortBy(function($badge) {
                                        return $badge->pivot->awarded_at->getTimestamp();
                                    })->first();
                                    $oldestDate = $oldestBadge ? $oldestBadge->pivot->awarded_at->diff(now())->format('%y anni, %m mesi') : 'N/A';
                                @endphp
                                <div class="badge-stat__value">{{ $oldestDate }}</div>
                                <div class="badge-stat__label">Primo Badge</div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($badges->count() > 0)
            <!-- Badge in Evidenza -->
            @php
                $featuredBadges = $badges->filter(function($badge) {
                    return $badge->pivot->is_featured;
                });
                $recentBadges = $badges->sortByDesc(function($badge) {
                    return $badge->pivot->awarded_at->getTimestamp();
                })->take(4);
            @endphp

            @if($featuredBadges->count() > 0)
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-star me-2"></i> Badge in Evidenza</h5>
                </div>
                <div class="card-body">
                    <div class="badge-list">
                        @foreach($featuredBadges as $badge)
                            <div class="badge-card badge-card--featured">
                                <div class="badge-card__body">
                                    <div class="badge-icon" style="color: {{ $badge->color }}">
                                        <i class="{{ $badge->icon }} fa-4x"></i>
                                    </div>
                                    <h5 class="badge-card__title">{{ $badge->name }}</h5>
                                    <p class="badge-card__description">{{ $badge->description }}</p>
                                    <div class="badge-meta">
                                        <i class="fas fa-calendar-alt"></i> {{ $badge->pivot->awarded_at->format('d/m/Y') }}
                                    </div>
                                </div>
                                <div class="badge-card__footer">
                                    <a href="{{ route('employee.badges.show', $badge->id) }}" class="btn btn-sm btn-outline-primary">Dettagli</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Ultime Conquiste -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-trophy me-2"></i> Ultime Conquiste</h5>
                </div>
                <div class="card-body">
                    <div class="badge-list">
                        @foreach($recentBadges as $badge)
                            <div class="badge-card">
                                <div class="badge-card__body">
                                    <div class="badge-icon" style="color: {{ $badge->color }}">
                                        <i class="{{ $badge->icon }} fa-4x"></i>
                                    </div>
                                    <h5 class="badge-card__title">{{ $badge->name }}</h5>
                                    <p class="badge-card__description">{{ $badge->description }}</p>
                                    <div class="badge-meta">
                                        <i class="fas fa-calendar-alt"></i> {{ $badge->pivot->awarded_at->format('d/m/Y') }}
                                    </div>
                                    @if($badge->pivot->award_reason)
                                        <div class="badge-meta mt-2">
                                            <i class="fas fa-quote-left"></i> {{ $badge->pivot->award_reason }}
                                        </div>
                                    @endif
                                </div>
                                <div class="badge-card__footer">
                                    <a href="{{ route('employee.badges.show', $badge->id) }}" class="btn btn-sm btn-outline-primary">Dettagli</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Tutti i Miei Badge -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-award me-2"></i> Tutti i Miei Badge</h5>
                </div>
                <div class="card-body">
                    @php
                        // Raggruppa i badge per categoria
                        $badgesByCategory = $badges->groupBy('category');
                    @endphp

                    @foreach($badgesByCategory as $category => $categoryBadges)
                        <h6 class="mt-3 mb-3 border-bottom pb-2">{{ $category ?: 'Altro' }}</h6>
                        <div class="badge-list">
                            @foreach($categoryBadges as $badge)
                                <div class="badge-card {{ $badge->pivot->is_featured ? 'badge-card--featured' : '' }}">
                                    <div class="badge-card__body">
                                        <div class="badge-icon" style="color: {{ $badge->color }}">
                                            <i class="{{ $badge->icon }} fa-4x"></i>
                                        </div>
                                        <h5 class="badge-card__title">{{ $badge->name }}</h5>
                                        <p class="badge-card__description">{{ $badge->description }}</p>
                                        <div class="badge-type badge-type--{{ $badge->type }}">
                                            {{ $badge->type === 'achievement' ? 'Achievement' : ($badge->type === 'completion' ? 'Completamento' : 'Speciale') }}
                                        </div>
                                        <div class="badge-meta">
                                            <i class="fas fa-calendar-alt"></i> {{ $badge->pivot->awarded_at->format('d/m/Y') }}
                                        </div>
                                    </div>
                                    <div class="badge-card__footer">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('employee.badges.show', $badge->id) }}" class="btn btn-sm btn-outline-primary">Dettagli</a>
                                            <form action="{{ route('employee.badges.toggle-featured', $badge->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm {{ $badge->pivot->is_featured ? 'btn-warning' : 'btn-outline-warning' }}">
                                                    <i class="fas fa-star"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="card">
                <div class="card-body">
                    <div class="text-center py-5">
                        <div class="mb-4">
                            <i class="fas fa-award fa-4x text-muted"></i>
                        </div>
                        <h4>Non hai ancora ricevuto badge</h4>
                        <p class="text-muted">Completa le attività di onboarding e i corsi formativi per guadagnare badge e riconoscimenti.</p>

                        <div class="mt-4">
                            <h5>Come guadagnare badge?</h5>
                            <div class="badge-list my-4">
                                <div class="badge-card">
                                    <div class="badge-card__body py-4">
                                        <i class="fas fa-graduation-cap fa-2x text-primary mb-3"></i>
                                        <h6 class="badge-card__title">Completa i Corsi</h6>
                                        <p class="badge-card__description">Accedi ai corsi formativi e completa i relativi quiz</p>
                                    </div>
                                </div>
                                <div class="badge-card">
                                    <div class="badge-card__body py-4">
                                        <i class="fas fa-tasks fa-2x text-success mb-3"></i>
                                        <h6 class="badge-card__title">Checklist Onboarding</h6>
                                        <p class="badge-card__description">Completa le attività assegnate nel processo di onboarding</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mt-4" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mt-4" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
    </div>
</x-layout>
