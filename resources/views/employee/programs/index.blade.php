<x-layout>
    <x-slot name="sidebar">
        @include('components.sidebar')
    </x-slot>

    <div class="container py-4">
        <h2 class="mb-4">I miei programmi</h2>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($programs->count() > 0)
            <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
                @foreach($programs as $program)
                    <div class="col">
                        <div class="card h-100 shadow-sm">
                            @if($program->image)
                                <img src="{{ Storage::url($program->image) }}" class="card-img-top" alt="{{ $program->name }}">
                            @else
                                <div class="card-img-top text-center py-5 bg-light">
                                    <i class="fas fa-graduation-cap fa-3x text-muted"></i>
                                </div>
                            @endif
                            <div class="card-body">
                                <h5 class="card-title">{{ $program->name }}</h5>
                                <p class="card-text text-muted small">
                                    <i class="fas fa-calendar-alt me-1"></i> Assegnato: {{ $program->pivot->assigned_at ? $program->pivot->assigned_at->format('d/m/Y') : 'N/A' }}
                                </p>
                                <p class="card-text">{{ Str::limit($program->description, 100) }}</p>

                                @php
                                    $totalItems = isset($program->total_items) ? $program->total_items : 0;
                                    $completedItems = isset($program->completed_items) ? $program->completed_items : 0;
                                    $progressPercentage = $totalItems > 0 ? ($completedItems / $totalItems) * 100 : 0;
                                @endphp

                                <div class="progress mb-2" style="height: 8px;">
                                    <div class="progress-bar" role="progressbar" style="width: {{ $progressPercentage }}%;"
                                        aria-valuenow="{{ $progressPercentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <p class="small text-muted mb-3">
                                    Progresso: {{ $completedItems }}/{{ $totalItems }} elementi completati ({{ round($progressPercentage) }}%)
                                </p>

                                @if($program->status == 'completed')
                                    <span class="badge bg-success">Completato</span>
                                @elseif($program->status == 'in_progress')
                                    <span class="badge bg-primary">In corso</span>
                                @else
                                    <span class="badge bg-secondary">Non iniziato</span>
                                @endif
                            </div>
                            <div class="card-footer bg-white border-0">
                                <a href="{{ route('employee.programs.show', $program) }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-eye me-1"></i> Visualizza dettagli
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-graduation-cap fa-4x text-muted mb-3"></i>
                    <h4>Nessun programma disponibile</h4>
                    <p class="text-muted">Al momento non sei iscritto a nessun programma.</p>
                </div>
            </div>
        @endif
    </div>
</x-layout>
