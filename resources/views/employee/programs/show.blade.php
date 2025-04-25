<x-layout>
    <x-slot name="sidebar">
        @include('components.sidebar')
    </x-slot>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ $program->name }}</h5>
                        <span class="badge {{ $program->is_active ? 'bg-success' : 'bg-secondary' }}">
                            {{ $program->is_active ? 'Attivo' : 'Inattivo' }}
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <p class="card-text">{{ $program->description }}</p>

                                <div class="mt-3">
                                    <h6>Periodo:</h6>
                                    <p>Dal {{ \Carbon\Carbon::parse($program->start_date)->format('d/m/Y') }}
                                       al {{ \Carbon\Carbon::parse($program->end_date)->format('d/m/Y') }}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">Il tuo progresso</h6>

                                        <div class="progress mb-2">
                                            <div class="progress-bar bg-primary" role="progressbar"
                                                 style="width: {{ $progress['percentage'] }}%"
                                                 aria-valuenow="{{ $progress['percentage'] }}"
                                                 aria-valuemin="0" aria-valuemax="100">
                                                {{ round($progress['percentage']) }}%
                                            </div>
                                        </div>
                                        <p class="small text-muted mb-0">{{ $progress['completedItems'] }} di {{ $progress['totalItems'] }} completati</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sezione Corsi -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Corsi del programma</h5>
                    </div>
                    <div class="card-body">
                        @if($program->courses && $program->courses->count() > 0)
                            <div class="list-group">
                                @foreach($program->courses as $course)
                                    <a href="{{ route('employee.courses.show', $course->id) }}" class="list-group-item list-group-item-action">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1">{{ $course->title }}</h6>
                                            @php
                                                // Qui determina lo stato del corso per l'utente
                                                $status = 'Non iniziato'; // Predefinito
                                                $statusClass = 'bg-secondary';

                                                // Logica per determinare lo stato (da implementare)
                                                // Esempio:
                                                // if ($course->isCompletedByUser(Auth::user())) {
                                                //     $status = 'Completato';
                                                //     $statusClass = 'bg-success';
                                                // } elseif ($course->isStartedByUser(Auth::user())) {
                                                //     $status = 'In corso';
                                                //     $statusClass = 'bg-primary';
                                                // }
                                            @endphp
                                            <span class="badge {{ $statusClass }}">{{ $status }}</span>
                                        </div>
                                        <p class="mb-1">{{ Str::limit($course->description, 100) }}</p>
                                        <small class="text-muted">Durata stimata: {{ $course->duration_minutes ? $course->duration_minutes . ' min' : 'N/A' }}</small>
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <p>Nessun corso disponibile per questo programma.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Sezione Checklist -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Checklist del programma</h5>
                    </div>
                    <div class="card-body">
                        @if($program->checklists && $program->checklists->count() > 0)
                            <div class="list-group">
                                @foreach($program->checklists as $checklist)
                                    <a href="{{ route('employee.checklists.show', $checklist->id) }}" class="list-group-item list-group-item-action">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1">{{ $checklist->name }}</h6>
                                            @php
                                                // Calcola lo stato della checklist
                                                $completed = 0;
                                                $total = $checklist->items ? $checklist->items->count() : 0;
                                                // La logica potrebbe essere più complessa
                                            @endphp
                                            <span class="badge bg-info">{{ $completed }}/{{ $total }} completati</span>
                                        </div>
                                        <p class="mb-1">{{ Str::limit($checklist->description, 100) }}</p>
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <p>Nessuna checklist disponibile per questo programma.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Sezione Eventi -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Eventi correlati</h5>
                    </div>
                    <div class="card-body">
                        @if($program->events && $program->events->count() > 0)
                            <div class="list-group">
                                @foreach($program->events as $event)
                                    <a href="{{ route('employee.events.show', $event->id) }}" class="list-group-item list-group-item-action">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1">{{ $event->title }}</h6>
                                            <small>{{ \Carbon\Carbon::parse($event->start_date)->format('d/m/Y H:i') }}</small>
                                        </div>
                                        <p class="mb-1">{{ Str::limit($event->description, 100) }}</p>
                                        <small class="text-muted">Luogo: {{ $event->location ?? 'Online' }}</small>
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <p>Nessun evento correlato a questo programma.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>
