<x-layout>
    <x-slot name="sidebar">
        @include('components.sidebar')
    </x-slot>

    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>{{ $course->title }}</h2>
            <a href="{{ route('employee.courses.index') }}" class="btn btn-secondary">
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

        @if(session('info'))
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                {{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Dettagli del Corso</h5>
                        <span class="badge {{
                            $completionStatus === 'completed' ? 'bg-success' :
                            ($completionStatus === 'in_progress' ? 'bg-warning' : 'bg-secondary')
                        }}">
                            {{
                                $completionStatus === 'completed' ? 'Completato' :
                                ($completionStatus === 'in_progress' ? 'In corso' : 'Non iniziato')
                            }}
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>Categoria:</strong> {{ $course->category }}
                        </div>
                        <div class="mb-3">
                            <strong>Descrizione:</strong>
                            <p class="mt-2">{{ $course->description }}</p>
                        </div>

                        <div class="mb-3">
                            <strong>Tipo di contenuto:</strong>
                            @if($course->content_type === 'pdf')
                                <span class="badge bg-danger">PDF</span>
                            @elseif($course->content_type === 'video')
                                <span class="badge bg-primary">Video</span>
                            @elseif($course->content_type === 'link')
                                <span class="badge bg-info">Link</span>
                            @else
                                <span class="badge bg-secondary">Testo</span>
                            @endif
                        </div>

                        <div class="mb-3">
                            <strong>Durata stimata:</strong> {{ $course->duration_minutes ? $course->duration_minutes . ' minuti' : 'Non specificata' }}
                        </div>

                        @if($dueDate)
                            <div class="mb-3">
                                <strong>Da completare entro:</strong> {{ \Carbon\Carbon::parse($dueDate)->format('d/m/Y') }}
                                @if(\Carbon\Carbon::parse($dueDate)->isPast())
                                    <span class="badge bg-danger ms-2">Scaduto</span>
                                @elseif(\Carbon\Carbon::parse($dueDate)->diffInDays(now()) < 7)
                                    <span class="badge bg-warning ms-2">In scadenza</span>
                                @endif
                            </div>
                        @endif

                        @if($startedDate)
                            <div class="mb-3">
                                <strong>Iniziato il:</strong> {{ \Carbon\Carbon::parse($startedDate)->format('d/m/Y H:i') }}
                            </div>
                        @endif

                        @if($completionDate)
                            <div class="mb-3">
                                <strong>Completato il:</strong> {{ \Carbon\Carbon::parse($completionDate)->format('d/m/Y H:i') }}
                            </div>
                        @endif
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Contenuto del Corso</h5>
                    </div>
                    <div class="card-body">
                        @if($completionStatus === 'not_started')
                            <div class="text-center py-4">
                                <i class="fas fa-lock fa-3x text-muted mb-3"></i>
                                <h5>Corso non ancora iniziato</h5>
                                <p class="text-muted">Clicca sul pulsante "Inizia Corso" per accedere al contenuto.</p>
                                <form action="{{ route('employee.courses.start', $course) }}" method="POST" class="mt-3">
                                    @csrf
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-play me-1"></i> Inizia Corso
                                    </button>
                                </form>
                            </div>
                        @else
                            @if($course->content_type === 'pdf' && $course->file_path)
                                <div class="text-center">
                                    <a href="{{ Storage::url($course->file_path) }}" target="_blank" class="btn btn-primary mb-3">
                                        <i class="fas fa-file-pdf me-1"></i> Apri PDF
                                    </a>
                                    <div class="embed-responsive" style="height: 500px;">
                                        <iframe class="embed-responsive-item w-100 h-100" src="{{ Storage::url($course->file_path) }}"></iframe>
                                    </div>
                                </div>
                            @elseif($course->content_type === 'video' && $course->file_path)
                                <div class="text-center">
                                    <video width="100%" controls class="mb-3">
                                        <source src="{{ Storage::url($course->file_path) }}" type="video/mp4">
                                        Il tuo browser non supporta la riproduzione video.
                                    </video>
                                </div>
                            @elseif($course->content_type === 'link' && $course->content)
                                <div class="text-center mb-3">
                                    <a href="{{ $course->content }}" target="_blank" class="btn btn-primary">
                                        <i class="fas fa-external-link-alt me-1"></i> Apri Link Esterno
                                    </a>
                                </div>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i> Il contenuto di questo corso è disponibile tramite il link esterno sopra.
                                </div>
                            @elseif($course->content_type === 'text' && $course->content)
                                <div class="p-3 bg-light rounded">
                                    {!! nl2br(e($course->content)) !!}
                                </div>
                            @else
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i> Nessun contenuto disponibile per questo corso.
                                </div>
                            @endif

                            @if($completionStatus !== 'completed')
                                <div class="mt-4 text-center">
                                    @if($course->has_quiz && $course->quiz)
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle me-2"></i> Per completare questo corso è necessario superare il quiz.
                                        </div>
                                    @else
                                        <form action="{{ route('employee.courses.complete', $course) }}" method="POST" class="mt-3">
                                            @csrf
                                            <button type="submit" class="btn btn-success">
                                                <i class="fas fa-check-circle me-1"></i> Segna come Completato
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            @endif
                        @endif
                    </div>
                </div>

                @if($course->has_quiz && $course->quiz)
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Quiz</h5>
                        </div>
                        <div class="card-body">
                            @if($completionStatus === 'not_started')
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i> Devi iniziare il corso prima di poter accedere al quiz.
                                </div>
                            @else
                                <div class="mb-3">
                                    <strong>Titolo:</strong> {{ $course->quiz->title }}
                                </div>

                                @if($course->quiz->description)
                                    <div class="mb-3">
                                        <strong>Descrizione:</strong>
                                        <p class="mt-2">{{ $course->quiz->description }}</p>
                                    </div>
                                @endif

                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <strong>Punteggio minimo:</strong> {{ $course->quiz->passing_score }}%
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Limite di tempo:</strong> {{ $course->quiz->time_limit_minutes ? $course->quiz->time_limit_minutes . ' minuti' : 'Nessun limite' }}
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Tentativi consentiti:</strong> {{ $course->quiz->attempts_allowed }}
                                    </div>
                                </div>

                                @if($quizResults->count() > 0)
                                    <div class="table-responsive mb-3">
                                        <table class="table table-bordered">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Tentativo</th>
                                                    <th>Data</th>
                                                    <th>Punteggio</th>
                                                    <th>Risultato</th>
                                                    <th>Azioni</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($quizResults as $index => $result)
                                                    <tr>
                                                        <td>{{ $quizResults->count() - $index }}</td>
                                                        <td>{{ \Carbon\Carbon::parse($result->completed_at)->format('d/m/Y H:i') }}</td>
                                                        <td>{{ $result->score }}%</td>
                                                        <td>
                                                            @if($result->passed)
                                                                <span class="badge bg-success">Superato</span>
                                                            @else
                                                                <span class="badge bg-danger">Non superato</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <a href="{{ route('employee.courses.quiz.results', ['course' => $course, 'userQuiz' => $result]) }}" class="btn btn-sm btn-info">
                                                                <i class="fas fa-eye"></i> Dettagli
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    @php
                                        $attemptsUsed = $quizResults->count();
                                        $attemptsRemaining = $course->quiz->attempts_allowed - $attemptsUsed;
                                        $canTakeQuiz = $attemptsRemaining > 0;
                                        $hasPassed = $quizResults->where('passed', true)->count() > 0;
                                    @endphp

                                    @if(!$hasPassed && !$canTakeQuiz)
                                        <div class="alert alert-danger">
                                            <i class="fas fa-exclamation-triangle me-2"></i> Hai esaurito tutti i tentativi disponibili per questo quiz.
                                        </div>
                                    @elseif(!$hasPassed)
                                        <div class="alert alert-warning">
                                            <i class="fas fa-info-circle me-2"></i> Hai ancora {{ $attemptsRemaining }} tentativ{{ $attemptsRemaining === 1 ? 'o' : 'i' }} disponibil{{ $attemptsRemaining === 1 ? 'e' : 'i' }}.
                                        </div>
                                        <div class="text-center">
                                            <a href="{{ route('employee.courses.quiz.take', $course) }}" class="btn btn-primary">
                                                <i class="fas fa-question-circle me-1"></i> Fai il Quiz
                                            </a>
                                        </div>
                                    @else
                                        <div class="alert alert-success">
                                            <i class="fas fa-check-circle me-2"></i> Hai superato il quiz! Il corso è stato completato.
                                        </div>
                                    @endif
                                @else
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i> Non hai ancora sostenuto questo quiz.
                                    </div>
                                    <div class="text-center">
                                        <a href="{{ route('employee.courses.quiz.take', $course) }}" class="btn btn-primary">
                                            <i class="fas fa-question-circle me-1"></i> Inizia il Quiz
                                        </a>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Stato del Corso</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            @php
                                $progressPercentage = $course->completionPercentage(auth()->user());
                            @endphp
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <strong>Avanzamento:</strong>
                                <span>{{ $progressPercentage }}%</span>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar {{
                                    $progressPercentage === 100 ? 'bg-success' :
                                    ($progressPercentage > 0 ? 'bg-warning' : 'bg-secondary')
                                }}" role="progressbar" style="width: {{ $progressPercentage }}%;" aria-valuenow="{{ $progressPercentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <strong>Stato:</strong>
                            <span class="badge {{
                                $completionStatus === 'completed' ? 'bg-success' :
                                ($completionStatus === 'in_progress' ? 'bg-warning' : 'bg-secondary')
                            }}">
                                {{
                                    $completionStatus === 'completed' ? 'Completato' :
                                    ($completionStatus === 'in_progress' ? 'In corso' : 'Non iniziato')
                                }}
                            </span>
                        </div>

                        @if($course->has_quiz && $course->quiz)
                            <div class="mb-3">
                                <strong>Quiz:</strong>
                                @php
                                    $quizPassed = $quizResults->where('passed', true)->count() > 0;
                                @endphp
                                @if($quizPassed)
                                    <span class="badge bg-success">Superato</span>
                                @else
                                    <span class="badge bg-danger">Non superato</span>
                                @endif
                            </div>
                        @endif

                        @if($completionStatus === 'not_started')
                            <div class="d-grid mt-4">
                                <form action="{{ route('employee.courses.start', $course) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-play me-1"></i> Inizia Corso
                                    </button>
                                </form>
                            </div>
                        @elseif($completionStatus === 'in_progress' && (!$course->has_quiz || $quizPassed))
                            <div class="d-grid mt-4">
                                <form action="{{ route('employee.courses.complete', $course) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-check-circle me-1"></i> Segna come Completato
                                    </button>
                                </form>
                            </div>
                        @elseif($completionStatus === 'in_progress' && $course->has_quiz && !$quizPassed)
                            <div class="d-grid mt-4">
                                <a href="{{ route('employee.courses.quiz.take', $course) }}" class="btn btn-warning">
                                    <i class="fas fa-question-circle me-1"></i> Fai il Quiz
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                @if($course->has_quiz && $course->quiz && $quizResults->count() > 0)
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Risultati Quiz</h5>
                        </div>
                        <div class="card-body">
                            @php
                                $bestQuiz = $quizResults->sortByDesc('score')->first();
                                $firstQuiz = $quizResults->last();
                                $lastQuiz = $quizResults->first();
                                $attempts = $quizResults->count();
                                $passedCount = $quizResults->where('passed', true)->count();
                            @endphp

                            <div class="mb-3">
                                <strong>Miglior punteggio:</strong> {{ $bestQuiz->score }}%
                                @if($bestQuiz->passed)
                                    <span class="badge bg-success ms-1">Superato</span>
                                @endif
                            </div>

                            <div class="mb-3">
                                <strong>Tentativi effettuati:</strong> {{ $attempts }} / {{ $course->quiz->attempts_allowed }}
                            </div>

                            <div class="mb-3">
                                <strong>Quiz superati:</strong> {{ $passedCount }} / {{ $attempts }}
                            </div>

                            <div class="mb-3">
                                <strong>Primo tentativo:</strong> {{ \Carbon\Carbon::parse($firstQuiz->completed_at)->format('d/m/Y H:i') }}
                            </div>

                            <div class="mb-3">
                                <strong>Ultimo tentativo:</strong> {{ \Carbon\Carbon::parse($lastQuiz->completed_at)->format('d/m/Y H:i') }}
                            </div>

                            <div class="text-center mt-4">
                                <a href="{{ route('employee.courses.quiz.results', ['course' => $course, 'userQuiz' => $lastQuiz]) }}" class="btn btn-info">
                                    <i class="fas fa-eye me-1"></i> Vedi Ultimo Risultato
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layout>
