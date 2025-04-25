<x-layout>
    <x-slot name="sidebar">
        @include('components.sidebar')
    </x-slot>

    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>{{ $course->title }}</h2>
            <a href="{{ route('admin.courses.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Torna all'elenco
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

        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Informazioni Corso</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <p class="mb-0 text-muted">Categoria</p>
                                <p class="fw-bold">{{ $course->category }}</p>
                            </div>
                            <div class="col-md-4">
                                <p class="mb-0 text-muted">Tipo contenuto</p>
                                <p class="fw-bold">
                                    @if($course->content_type === 'pdf')
                                        <span class="badge bg-danger">PDF</span>
                                    @elseif($course->content_type === 'video')
                                        <span class="badge bg-primary">Video</span>
                                    @elseif($course->content_type === 'link')
                                        <span class="badge bg-info">Link</span>
                                    @else
                                        <span class="badge bg-secondary">Testo</span>
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-4">
                                <p class="mb-0 text-muted">Durata</p>
                                <p class="fw-bold">{{ $course->duration_minutes ? $course->duration_minutes . ' min' : 'N/A' }}</p>
                            </div>
                        </div>

                        <div class="mb-3">
                            <p class="mb-1 text-muted">Descrizione</p>
                            <p>{{ $course->description }}</p>
                        </div>

                        @if($course->content_type === 'pdf' || $course->content_type === 'video')
                            <div class="mb-3">
                                <p class="mb-1 text-muted">File</p>
                                @if($course->file_path)
                                    <a href="{{ Storage::url($course->file_path) }}" class="btn btn-outline-primary" target="_blank">
                                        <i class="fas {{ $course->content_type === 'pdf' ? 'fa-file-pdf' : 'fa-file-video' }} me-1"></i>
                                        Visualizza {{ $course->content_type === 'pdf' ? 'PDF' : 'Video' }}
                                    </a>
                                @else
                                    <p class="text-danger">Nessun file caricato</p>
                                @endif
                            </div>
                        @elseif($course->content_type === 'link')
                            <div class="mb-3">
                                <p class="mb-1 text-muted">Link</p>
                                <a href="{{ $course->content }}" class="btn btn-outline-primary" target="_blank">
                                    <i class="fas fa-external-link-alt me-1"></i> Apri Link
                                </a>
                            </div>
                        @elseif($course->content_type === 'text')
                            <div class="mb-3">
                                <p class="mb-1 text-muted">Contenuto</p>
                                <div class="p-3 border rounded">
                                    {{ $course->content }}
                                </div>
                            </div>
                        @endif

                        <div class="mt-3">
                            <p class="mb-1 text-muted">Creato da</p>
                            <p>{{ $course->creator->name }} il {{ $course->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.courses.edit', $course) }}" class="btn btn-warning">
                                <i class="fas fa-edit me-1"></i> Modifica
                            </a>
                            <form action="{{ route('admin.courses.destroy', $course) }}" method="POST" class="d-inline" onsubmit="return confirm('Sei sicuro di voler eliminare questo corso?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-trash me-1"></i> Elimina
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                @if($course->has_quiz && $course->quiz)
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Quiz</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <p class="mb-0 text-muted">Titolo</p>
                                    <p class="fw-bold">{{ $course->quiz->title }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-0 text-muted">Punteggio minimo</p>
                                    <p class="fw-bold">{{ $course->quiz->passing_score }}%</p>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <p class="mb-0 text-muted">Tempo limite</p>
                                    <p class="fw-bold">{{ $course->quiz->time_limit_minutes ? $course->quiz->time_limit_minutes . ' min' : 'Nessun limite' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-0 text-muted">Tentativi consentiti</p>
                                    <p class="fw-bold">{{ $course->quiz->attempts_allowed }}</p>
                                </div>
                            </div>
                            <div class="mb-3">
                                <p class="mb-1 text-muted">Domande</p>
                                <p class="fw-bold">{{ $course->quiz->questions->count() }}</p>
                            </div>
                            <div class="d-flex justify-content-end">
                                <a href="{{ route('admin.courses.quiz.edit', $course) }}" class="btn btn-primary">
                                    <i class="fas fa-edit me-1"></i> Modifica Quiz
                                </a>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Quiz</h5>
                        </div>
                        <div class="card-body text-center py-4">
                            <i class="fas fa-question-circle fa-3x text-muted mb-3"></i>
                            <h5>Nessun quiz associato a questo corso</h5>
                            <p class="text-muted mb-3">Puoi aggiungere un quiz per verificare l'apprendimento degli utenti.</p>
                            <a href="{{ route('admin.courses.quiz.create', $course) }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i> Crea Quiz
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Stato corso</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Utenti assegnati:</span>
                            <span>{{ $assignedUsers->total() }}</span>
                        </div>

                        @php
                            $completedCount = $course->completedByUsers()->count();
                            $inProgressCount = $course->inProgressByUsers()->count();
                            $notStartedCount = $course->notStartedByUsers()->count();
                            $totalAssigned = $completedCount + $inProgressCount + $notStartedCount;
                        @endphp

                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Completato</span>
                                <span>{{ $completedCount }}</span>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $totalAssigned > 0 ? ($completedCount / $totalAssigned) * 100 : 0 }}%"></div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>In corso</span>
                                <span>{{ $inProgressCount }}</span>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $totalAssigned > 0 ? ($inProgressCount / $totalAssigned) * 100 : 0 }}%"></div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Non iniziato</span>
                                <span>{{ $notStartedCount }}</span>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-secondary" role="progressbar" style="width: {{ $totalAssigned > 0 ? ($notStartedCount / $totalAssigned) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Utenti assegnati</h5>
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#assignUsersModal">
                            <i class="fas fa-user-plus me-1"></i> Assegna
                        </button>
                    </div>
                    <div class="card-body">
                        @if($assignedUsers->count() > 0)
                            <div class="list-group">
                                @foreach($assignedUsers as $user)
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            {{ $user->name }}
                                            <small class="d-block text-muted">{{ $user->email }}</small>
                                        </div>
                                        <span class="badge {{
                                            $user->pivot->status === 'completed' ? 'bg-success' :
                                            ($user->pivot->status === 'in_progress' ? 'bg-warning' : 'bg-secondary')
                                        }}">
                                            {{
                                                $user->pivot->status === 'completed' ? 'Completato' :
                                                ($user->pivot->status === 'in_progress' ? 'In corso' : 'Non iniziato')
                                            }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>

                            <div class="d-flex justify-content-center mt-3">
                                {{ $assignedUsers->links() }}
                            </div>
                        @else
                            <div class="text-center py-3">
                                <i class="fas fa-users fa-2x text-muted mb-2"></i>
                                <p class="mb-0">Nessun utente assegnato a questo corso</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal per assegnare utenti -->
    <div class="modal fade" id="assignUsersModal" tabindex="-1" aria-labelledby="assignUsersModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="{{ route('admin.courses.assign-users', $course) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="assignUsersModalLabel">Assegna Corso a Utenti</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="user_ids" class="form-label">Seleziona Utenti</label>
                            <select class="form-select" id="user_ids" name="user_ids[]" multiple size="10" required>
                                @foreach(\App\Models\User::whereHas('roles', function($q) {
                                    $q->whereIn('name', ['employee', 'new_hire']);
                                })->orderBy('name')->get() as $user)
                                    <option value="{{ $user->id }}" {{ $course->users->contains($user->id) ? 'disabled' : '' }}>
                                        {{ $user->name }} ({{ $user->email }}) {{ $course->users->contains($user->id) ? '- Già assegnato' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Tieni premuto CTRL per selezionare più utenti</small>
                        </div>

                        <div class="mb-3">
                            <label for="due_date" class="form-label">Data di scadenza (opzionale)</label>
                            <input type="date" class="form-control" id="due_date" name="due_date" min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                        <button type="submit" class="btn btn-primary">Assegna</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout>
