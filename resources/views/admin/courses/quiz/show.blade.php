<x-layout>
    <x-slot name="sidebar">
        @include('components.sidebar')
    </x-slot>

    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Quiz: {{ $quiz->title }}</h2>
            <div>
                <a href="{{ route('admin.courses.show', $course) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Torna al corso
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Informazioni Quiz</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <p class="mb-1 text-muted">Titolo</p>
                            <p class="fw-bold">{{ $quiz->title }}</p>
                        </div>

                        <div class="mb-3">
                            <p class="mb-1 text-muted">Descrizione</p>
                            <p>{{ $quiz->description }}</p>
                        </div>

                        <div class="mb-3">
                            <p class="mb-1 text-muted">Punteggio minimo</p>
                            <p class="fw-bold">{{ $quiz->passing_score }}%</p>
                        </div>

                        <div class="mb-3">
                            <p class="mb-1 text-muted">Tempo limite</p>
                            <p>{{ $quiz->time_limit_minutes ? $quiz->time_limit_minutes . ' minuti' : 'Nessun limite' }}</p>
                        </div>

                        <div class="mb-3">
                            <p class="mb-1 text-muted">Tentativi consentiti</p>
                            <p>{{ $quiz->attempts_allowed }}</p>
                        </div>

                        <div class="mb-3">
                            <p class="mb-1 text-muted">Totale domande</p>
                            <p class="fw-bold">{{ $quiz->questions->count() }}</p>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.courses.quiz.edit', $course) }}" class="btn btn-warning">
                                <i class="fas fa-edit me-1"></i> Modifica
                            </a>
                            <form action="{{ route('admin.courses.quiz.destroy', $course) }}" method="POST" class="d-inline" onsubmit="return confirm('Sei sicuro di voler eliminare questo quiz?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-trash me-1"></i> Elimina
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Statistiche Quiz</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <p class="mb-1 text-muted">Utenti che hanno completato</p>
                            <p class="fw-bold">{{ $quizStats['completed'] }}</p>
                        </div>

                        <div class="mb-3">
                            <p class="mb-1 text-muted">Punteggio medio</p>
                            <p class="fw-bold">{{ $quizStats['avgScore'] }}%</p>
                        </div>

                        <div class="mb-3">
                            <p class="mb-1 text-muted">Tasso di superamento</p>
                            <p class="fw-bold">{{ $quizStats['passRate'] }}%</p>
                        </div>

                        <div class="mb-3">
                            <p class="mb-1 text-muted">Tempo medio di completamento</p>
                            <p>{{ $quizStats['avgTime'] }} minuti</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Domande</h5>
                        <a href="{{ route('admin.courses.quiz.questions.create', $course) }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-plus me-1"></i> Aggiungi Domanda
                        </a>
                    </div>
                    <div class="card-body">
                        @if($quiz->questions->count() > 0)
                            <div class="accordion" id="accordionQuestions">
                                @foreach($quiz->questions as $index => $question)
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="heading{{ $question->id }}">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $question->id }}" aria-expanded="false" aria-controls="collapse{{ $question->id }}">
                                                <div class="d-flex w-100 justify-content-between align-items-center">
                                                    <span class="me-3">{{ $index + 1 }}. {{ Str::limit($question->text, 80) }}</span>
                                                    <span class="badge bg-{{
                                                        $question->type === 'multiple_choice' ? 'primary' :
                                                        ($question->type === 'single_choice' ? 'info' :
                                                        ($question->type === 'true_false' ? 'success' : 'warning'))
                                                    }} me-2">
                                                        {{
                                                            $question->type === 'multiple_choice' ? 'Scelta multipla' :
                                                            ($question->type === 'single_choice' ? 'Scelta singola' :
                                                            ($question->type === 'true_false' ? 'Vero/Falso' : 'Testo'))
                                                        }}
                                                    </span>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="collapse{{ $question->id }}" class="accordion-collapse collapse" aria-labelledby="heading{{ $question->id }}" data-bs-parent="#accordionQuestions">
                                            <div class="accordion-body">
                                                <div class="mb-3">
                                                    <p class="mb-1 fw-bold">Domanda:</p>
                                                    <p>{{ $question->text }}</p>
                                                </div>

                                                @if($question->type !== 'text')
                                                    <div class="mb-3">
                                                        <p class="mb-1 fw-bold">Risposte:</p>
                                                        <ul class="list-group">
                                                            @foreach($question->answers as $answer)
                                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                                    {{ $answer->text }}
                                                                    @if($answer->is_correct)
                                                                        <span class="badge bg-success rounded-pill"><i class="fas fa-check"></i> Corretta</span>
                                                                    @endif
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                @else
                                                    <div class="mb-3">
                                                        <p class="mb-1 fw-bold">Risposta corretta:</p>
                                                        <p>{{ $question->correct_answer }}</p>
                                                    </div>
                                                @endif

                                                <div class="d-flex justify-content-between mt-3">
                                                    <a href="{{ route('admin.courses.quiz.questions.edit', ['course' => $course, 'question' => $question]) }}" class="btn btn-sm btn-warning">
                                                        <i class="fas fa-edit me-1"></i> Modifica
                                                    </a>
                                                    <form action="{{ route('admin.courses.quiz.questions.destroy', ['course' => $course, 'question' => $question]) }}" method="POST" onsubmit="return confirm('Sei sicuro di voler eliminare questa domanda?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger">
                                                            <i class="fas fa-trash me-1"></i> Elimina
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-question-circle fa-3x text-muted mb-3"></i>
                                <h5>Nessuna domanda presente</h5>
                                <p class="text-muted mb-3">Aggiungi domande per completare il quiz</p>
                                <a href="{{ route('admin.courses.quiz.questions.create', $course) }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-1"></i> Aggiungi domanda
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Storico completamento quiz</h5>
                    </div>
                    <div class="card-body">
                        @if($quizAttempts->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Utente</th>
                                            <th>Data</th>
                                            <th>Punteggio</th>
                                            <th>Tempo</th>
                                            <th>Stato</th>
                                            <th>Azioni</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($quizAttempts as $attempt)
                                            <tr>
                                                <td>{{ $attempt->user->name }}</td>
                                                <td>{{ $attempt->completed_at->format('d/m/Y H:i') }}</td>
                                                <td>{{ $attempt->score }}%</td>
                                                <td>{{ $attempt->time_spent }} min</td>
                                                <td>
                                                    @if($attempt->passed)
                                                        <span class="badge bg-success">Superato</span>
                                                    @else
                                                        <span class="badge bg-danger">Non superato</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route('admin.courses.quiz.attempts.show', ['course' => $course, 'attempt' => $attempt]) }}" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye me-1"></i> Dettagli
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex justify-content-center mt-3">
                                {{ $quizAttempts->links() }}
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                                <h5>Nessun tentativo di quiz</h5>
                                <p class="text-muted">Ancora nessun utente ha completato il quiz</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>
