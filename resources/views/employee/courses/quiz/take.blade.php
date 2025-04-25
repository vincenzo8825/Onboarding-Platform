<x-layout>
    <x-slot name="sidebar">
        @include('components.sidebar')
    </x-slot>

    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Quiz: {{ $quiz->title }}</h2>
            <a href="{{ route('employee.courses.show', $course) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Torna al corso
            </a>
        </div>

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    <div>
                        <h5 class="mb-2">Informazioni Quiz</h5>
                        <p class="mb-1"><strong>Punteggio minimo per superare:</strong> {{ $quiz->passing_score }}%</p>
                        <p class="mb-1"><strong>Tentativo:</strong> {{ $attemptsUsed + 1 }} di {{ $quiz->attempts_allowed }}</p>
                        <p class="mb-0"><strong>Domande totali:</strong> {{ $quiz->questions->count() }}</p>
                    </div>
                </div>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i> Rispondi a tutte le domande e poi clicca su "Termina Quiz" in fondo alla pagina per inviare le tue risposte.
                </div>
            </div>
        </div>

        <form id="quizForm" action="{{ route('employee.courses.quiz.submit', ['course' => $course->id, 'quiz' => $quiz->id]) }}" method="POST">
            @csrf
            <input type="hidden" name="start_time" value="{{ now()->toISOString() }}">

            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">{{ $quiz->title }}</h3>
                </div>
                <div class="card-body">
                    @foreach($quiz->questions as $index => $question)
                    <div class="question-container mb-5 pb-4 border-bottom">
                        <h4 class="mb-3">Domanda {{ $index + 1 }} di {{ $quiz->questions->count() }}</h4>
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5>{{ $question->text }}</h5>

                                @if($question->image)
                                <div class="my-3 text-center">
                                    <img src="{{ asset('storage/' . $question->image) }}" class="img-fluid rounded" alt="Immagine domanda" style="max-height: 300px;">
                                </div>
                                @endif

                                <div class="mt-4">
                                    @if($question->type === 'multiple_choice')
                                        <p class="text-muted small mb-3"><i class="fas fa-info-circle"></i> Seleziona tutte le risposte corrette</p>
                                        @foreach($question->answers as $option)
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" name="answers[{{ $question->id }}][]" id="option_{{ $option->id }}" value="{{ $option->id }}">
                                            <label class="form-check-label" for="option_{{ $option->id }}">{{ $option->text }}</label>
                                        </div>
                                        @endforeach
                                    @elseif($question->type === 'single_choice')
                                        <p class="text-muted small mb-3"><i class="fas fa-info-circle"></i> Seleziona una risposta</p>
                                        @foreach($question->answers as $option)
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="radio" name="answers[{{ $question->id }}]" id="option_{{ $option->id }}" value="{{ $option->id }}">
                                            <label class="form-check-label" for="option_{{ $option->id }}">{{ $option->text }}</label>
                                        </div>
                                        @endforeach
                                    @elseif($question->type === 'true_false')
                                        <p class="text-muted small mb-3"><i class="fas fa-info-circle"></i> Seleziona Vero o Falso</p>
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="radio" name="answers[{{ $question->id }}]" id="true_{{ $question->id }}" value="true">
                                            <label class="form-check-label" for="true_{{ $question->id }}">Vero</label>
                                        </div>
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="radio" name="answers[{{ $question->id }}]" id="false_{{ $question->id }}" value="false">
                                            <label class="form-check-label" for="false_{{ $question->id }}">Falso</label>
                                        </div>
                                    @elseif($question->type === 'text')
                                        <p class="text-muted small mb-3"><i class="fas fa-info-circle"></i> Inserisci la tua risposta</p>
                                        <div class="form-group">
                                            <textarea class="form-control" name="answers[{{ $question->id }}]" rows="4" placeholder="Scrivi la tua risposta qui..."></textarea>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach

                    <div class="text-center mt-5">
                        <div class="alert alert-warning mb-4">
                            <i class="fas fa-exclamation-triangle me-2"></i> Assicurati di aver risposto a tutte le domande prima di inviare il quiz.
                        </div>
                        <button type="submit" class="btn btn-success btn-lg px-5 py-3">
                            <i class="fas fa-check-circle me-2"></i> Termina Quiz
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
    @endpush
</x-layout>
