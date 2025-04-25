<x-layout>
    <x-slot name="sidebar">
        @include('components.sidebar')
    </x-slot>

    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Risultati Quiz: {{ $quiz->title }}</h2>
            <a href="{{ route('employee.courses.show', $course) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Torna al corso
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row mb-4">
            <div class="col-md-7">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Riepilogo</h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <div class="mb-3">
                                @if($userQuiz->passed)
                                    <div class="badge bg-success p-3 mb-3 fs-5">
                                        <i class="fas fa-trophy me-2"></i> Quiz Superato!
                                    </div>
                                @else
                                    <div class="badge bg-danger p-3 mb-3 fs-5">
                                        <i class="fas fa-times-circle me-2"></i> Quiz Non Superato
                                    </div>
                                @endif
                            </div>

                            <div class="progress mb-2" style="height: 25px;">
                                <div class="progress-bar {{ $userQuiz->passed ? 'bg-success' : 'bg-danger' }}"
                                    role="progressbar"
                                    style="width: {{ $userQuiz->score }}%;"
                                    aria-valuenow="{{ $userQuiz->score }}"
                                    aria-valuemin="0"
                                    aria-valuemax="100">
                                    {{ $userQuiz->score }}%
                                </div>
                            </div>
                            <p class="text-muted">Punteggio minimo richiesto: {{ $quiz->passing_score }}%</p>
                        </div>

                        <div class="row text-center">
                            <div class="col-md-4 mb-3">
                                <div class="border rounded p-3">
                                    <h5 class="mb-1 text-primary">{{ $userQuiz->score }}%</h5>
                                    <p class="mb-0 text-muted small">Punteggio ottenuto</p>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="border rounded p-3">
                                    <h5 class="mb-1 text-info">
                                        @php
                                            $timeSpent = $userQuiz->time_spent ?? 0;
                                            $minutes = floor($timeSpent / 60);
                                            $seconds = $timeSpent % 60;
                                            echo sprintf('%02d:%02d', $minutes, $seconds);
                                        @endphp
                                    </h5>
                                    <p class="mb-0 text-muted small">Tempo impiegato</p>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="border rounded p-3">
                                    <h5 class="mb-1 text-warning">{{ $userQuiz->completed_at->format('d/m/Y H:i') }}</h5>
                                    <p class="mb-0 text-muted small">Data completamento</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-5">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Statistiche</h5>
                    </div>
                    <div class="card-body">
                        @php
                            $answersLog = json_decode($userQuiz->answers_log, true);
                            $totalCorrect = 0;
                            $totalQuestions = count($answersLog);

                            foreach ($answersLog as $answer) {
                                if ($answer['is_correct']) {
                                    $totalCorrect++;
                                }
                            }
                            $incorrectAnswers = $totalQuestions - $totalCorrect;
                        @endphp

                        <canvas id="answersChart" height="220"></canvas>

                        <div class="mt-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span><i class="fas fa-circle text-success me-1"></i> Risposte corrette</span>
                                <span>{{ $totalCorrect }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span><i class="fas fa-circle text-danger me-1"></i> Risposte errate</span>
                                <span>{{ $incorrectAnswers }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span><i class="fas fa-circle text-primary me-1"></i> Percentuale di successo</span>
                                <span>{{ $totalQuestions > 0 ? round(($totalCorrect / $totalQuestions) * 100) : 0 }}%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Risposte</h5>
            </div>
            <div class="card-body">
                <div class="accordion" id="answersAccordion">
                    @foreach($quiz->questions as $index => $question)
                        @php
                            $questionLog = $answersLog[$question->id] ?? null;
                            $isCorrect = $questionLog ? $questionLog['is_correct'] : false;
                        @endphp

                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading{{ $question->id }}">
                                <button class="accordion-button collapsed {{ $isCorrect ? 'bg-success bg-opacity-10' : 'bg-danger bg-opacity-10' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $question->id }}" aria-expanded="false" aria-controls="collapse{{ $question->id }}">
                                    <div class="d-flex justify-content-between w-100 me-3">
                                        <span>Domanda {{ $index + 1 }}</span>
                                        <span>
                                            @if($isCorrect)
                                                <i class="fas fa-check-circle text-success"></i>
                                            @else
                                                <i class="fas fa-times-circle text-danger"></i>
                                            @endif
                                        </span>
                                    </div>
                                </button>
                            </h2>
                            <div id="collapse{{ $question->id }}" class="accordion-collapse collapse" aria-labelledby="heading{{ $question->id }}" data-bs-parent="#answersAccordion">
                                <div class="accordion-body">
                                    <div class="mb-3">
                                        <strong>{{ $question->text }}</strong>
                                        <span class="ms-2 badge {{ $isCorrect ? 'bg-success' : 'bg-danger' }}">
                                            {{ $isCorrect ? 'Risposta corretta' : 'Risposta errata' }} ({{ $question->points }} punti)
                                        </span>
                                    </div>

                                    @if($question->type === 'text')
                                        <div class="mb-3">
                                            <p class="fw-bold mb-1">La tua risposta:</p>
                                            <div class="p-2 border rounded {{ $isCorrect ? 'border-success' : 'border-danger' }}">
                                                {{ $questionLog['user_answer'] ?? 'Nessuna risposta' }}
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <p class="fw-bold mb-1">Risposte accettate:</p>
                                            <ul class="list-group">
                                                @foreach($questionLog['correct_answers'] ?? [] as $correctAnswer)
                                                    <li class="list-group-item list-group-item-success">{{ $correctAnswer }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @else
                                        <div class="mb-3">
                                            <p class="fw-bold mb-1">Risposte:</p>
                                            <ul class="list-group">
                                                @foreach($question->answers()->with(['question'])->get() as $answer)
                                                    @php
                                                        $userAnswers = $questionLog['user_answer'] ?? [];
                                                        $userSelected = is_array($userAnswers) ?
                                                            in_array($answer->id, $userAnswers) :
                                                            $userAnswers == $answer->id;

                                                        $isCorrectAnswer = $answer->is_correct;

                                                        $itemClass = '';
                                                        if ($userSelected && $isCorrectAnswer) {
                                                            $itemClass = 'list-group-item-success';
                                                        } elseif ($userSelected && !$isCorrectAnswer) {
                                                            $itemClass = 'list-group-item-danger';
                                                        } elseif (!$userSelected && $isCorrectAnswer) {
                                                            $itemClass = 'list-group-item-warning';
                                                        }
                                                    @endphp

                                                    <li class="list-group-item d-flex justify-content-between align-items-center {{ $itemClass }}">
                                                        <div>
                                                            @if($userSelected)
                                                                <i class="fas fa-check me-2"></i>
                                                            @endif

                                                            {{ $answer->text }}
                                                        </div>

                                                        @if($isCorrectAnswer)
                                                            <span class="badge bg-success rounded-pill">Risposta corretta</span>
                                                        @endif
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    @if(!$isCorrect)
                                        <div class="alert alert-warning mt-3">
                                            <i class="fas fa-info-circle me-2"></i>
                                            <strong>Suggerimento:</strong> Rivedi il materiale del corso relativo a questa domanda per migliorare la tua comprensione.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between">
            <a href="{{ route('employee.courses.show', $course) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Torna al corso
            </a>

            @php
                // Verifica se $attemptsUsed è già definito, altrimenti calcolalo
                if (!isset($attemptsUsed)) {
                    $attemptsUsed = $quiz->userQuizzes()
                        ->where('user_id', auth()->id())
                        ->count();
                }
            @endphp

            @if(!$userQuiz->passed && $attemptsUsed < $quiz->attempts_allowed)
                <a href="{{ route('employee.courses.quiz.take', $course) }}" class="btn btn-primary">
                    <i class="fas fa-redo me-1"></i> Riprova Quiz
                </a>
            @endif
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('answersChart').getContext('2d');

            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Risposte corrette', 'Risposte errate'],
                    datasets: [{
                        data: [{{ $totalCorrect }}, {{ $incorrectAnswers }}],
                        backgroundColor: ['#28a745', '#dc3545'],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    },
                    cutout: '70%'
                }
            });
        });
    </script>
    @endpush
</x-layout>
