<x-layout>
    <x-slot name="sidebar">
        @include('components.sidebar')
    </x-slot>

    <div class="container py-4">
        <div class="mb-4">
            <h2>I Miei Corsi Formativi</h2>
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
                @if($courses->count() > 0)
                    <div class="row">
                        @foreach($courses as $course)
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card h-100 {{
                                    $course->pivot->status === 'completed' ? 'border-success' :
                                    ($course->pivot->status === 'in_progress' ? 'border-warning' : 'border-secondary')
                                }}">
                                    <div class="card-header bg-white d-flex justify-content-between">
                                        <h5 class="mb-0 text-truncate">{{ $course->title }}</h5>
                                        <span class="badge {{
                                            $course->pivot->status === 'completed' ? 'bg-success' :
                                            ($course->pivot->status === 'in_progress' ? 'bg-warning' : 'bg-secondary')
                                        }}">
                                            {{
                                                $course->pivot->status === 'completed' ? 'Completato' :
                                                ($course->pivot->status === 'in_progress' ? 'In corso' : 'Non iniziato')
                                            }}
                                        </span>
                                    </div>
                                    <div class="card-body">
                                        <p class="card-text mb-3">{{ Str::limit($course->description, 100) }}</p>

                                        <div class="mb-3">
                                            <small class="text-muted"><strong>Categoria:</strong> {{ $course->category }}</small>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <div>
                                                <small class="text-muted">
                                                    <strong>Durata:</strong> {{ $course->duration_minutes ? $course->duration_minutes . ' min' : 'N/A' }}
                                                </small>
                                            </div>
                                            <div>
                                                @if($course->has_quiz)
                                                    @if($course->quiz_passed)
                                                        <span class="badge bg-success">Quiz Superato</span>
                                                    @else
                                                        <span class="badge bg-danger">Quiz Richiesto</span>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>

                                        @if($course->pivot->due_date)
                                            <div class="mb-3">
                                                <small class="text-{{ \Carbon\Carbon::parse($course->pivot->due_date)->isPast() ? 'danger' : 'muted' }}">
                                                    <strong>Da completare entro:</strong> {{ \Carbon\Carbon::parse($course->pivot->due_date)->format('d/m/Y') }}
                                                </small>
                                            </div>
                                        @endif

                                        <div class="progress mb-3" style="height: 6px;">
                                            @php
                                                $progressPercentage = $course->completionPercentage(auth()->user());
                                            @endphp
                                            <div class="progress-bar {{
                                                $progressPercentage === 100 ? 'bg-success' :
                                                ($progressPercentage > 0 ? 'bg-warning' : 'bg-secondary')
                                            }}" role="progressbar" style="width: {{ $progressPercentage }}%;" aria-valuenow="{{ $progressPercentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>

                                        <div class="text-end">
                                            <a href="{{ route('employee.courses.show', $course) }}" class="btn btn-primary">
                                                <i class="fas fa-book-open me-1"></i> Visualizza
                                            </a>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-white text-muted">
                                        <small>Assegnato: {{ \Carbon\Carbon::parse($course->pivot->assigned_at)->format('d/m/Y') }}</small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        {{ $courses->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-book fa-4x text-muted mb-3"></i>
                        <h4>Nessun corso assegnato</h4>
                        <p class="text-muted">Non hai ancora corsi formativi assegnati.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layout>
