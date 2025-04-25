<x-layout>
    <x-slot name="sidebar">
        @include('components.sidebar')
    </x-slot>

    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Crea Quiz per: {{ $course->title }}</h2>
            <a href="{{ route('admin.courses.show', $course) }}" class="btn btn-secondary">
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

        <div class="card">
            <div class="card-body">
                <form id="quiz-form" action="{{ route('admin.courses.quiz.store', $course) }}" method="POST">
                    @csrf

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="title" class="form-label">Titolo Quiz *</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="description" class="form-label">Descrizione</label>
                            <input type="text" class="form-control @error('description') is-invalid @enderror" id="description" name="description" value="{{ old('description') }}">
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="passing_score" class="form-label">Punteggio minimo (%) *</label>
                            <input type="number" class="form-control @error('passing_score') is-invalid @enderror" id="passing_score" name="passing_score" value="{{ old('passing_score', 70) }}" min="1" max="100" required>
                            @error('passing_score')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="time_limit_minutes" class="form-label">Tempo limite (minuti)</label>
                            <input type="number" class="form-control @error('time_limit_minutes') is-invalid @enderror" id="time_limit_minutes" name="time_limit_minutes" value="{{ old('time_limit_minutes') }}" min="1">
                            @error('time_limit_minutes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="attempts_allowed" class="form-label">Tentativi consentiti *</label>
                            <input type="number" class="form-control @error('attempts_allowed') is-invalid @enderror" id="attempts_allowed" name="attempts_allowed" value="{{ old('attempts_allowed', 1) }}" min="1" required>
                            @error('attempts_allowed')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="mb-3">
                        <h4>Domande</h4>
                        <p class="text-muted">Aggiungi domande per il quiz. Puoi inserire domande a scelta singola, scelta multipla, vero/falso o a risposta libera.</p>
                    </div>

                    <div id="questions-container">
                        <!-- Le domande verranno aggiunte dinamicamente qui -->
                    </div>

                    <div class="d-flex justify-content-center mb-4 mt-2">
                        <div class="btn-group">
                            <button type="button" class="btn btn-outline-primary" onclick="addQuestion('multiple_choice')">
                                <i class="fas fa-list-ul me-1"></i> Scelta multipla
                            </button>
                            <button type="button" class="btn btn-outline-primary" onclick="addQuestion('single_choice')">
                                <i class="fas fa-dot-circle me-1"></i> Scelta singola
                            </button>
                            <button type="button" class="btn btn-outline-primary" onclick="addQuestion('true_false')">
                                <i class="fas fa-toggle-on me-1"></i> Vero/Falso
                            </button>
                            <button type="button" class="btn btn-outline-primary" onclick="addQuestion('text')">
                                <i class="fas fa-font me-1"></i> Testo libero
                            </button>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <button type="button" class="btn btn-secondary" onclick="window.location.href = '{{ route('admin.courses.show', $course) }}'">Annulla</button>
                        <button type="submit" class="btn btn-primary" id="submit-quiz">Crea Quiz</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Aggiungi una prima domanda di default
            addQuestion('single_choice');

            // Configura validazione del form
            document.getElementById('quiz-form').addEventListener('submit', function(e) {
                const questionsContainer = document.getElementById('questions-container');
                if (questionsContainer.children.length === 0) {
                    e.preventDefault();
                    alert('Aggiungi almeno una domanda al quiz.');
                }
            });
        });

        let questionCounter = 0;

        function addQuestion(type) {
            questionCounter++;
            const questionId = questionCounter;

            let questionTitle;
            switch(type) {
                case 'multiple_choice':
                    questionTitle = 'Domanda a scelta multipla';
                    break;
                case 'single_choice':
                    questionTitle = 'Domanda a scelta singola';
                    break;
                case 'true_false':
                    questionTitle = 'Domanda vero/falso';
                    break;
                case 'text':
                    questionTitle = 'Domanda a risposta libera';
                    break;
            }

            const questionHtml = `
                <div class="card mb-4 question-card" id="question-${questionId}">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">${questionTitle}</h5>
                        <button type="button" class="btn btn-danger btn-sm" onclick="removeQuestion(${questionId})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="questions[${questionId}][text]" class="form-label">Testo della domanda *</label>
                            <textarea class="form-control" id="questions[${questionId}][text]" name="questions[${questionId}][text]" rows="2" required></textarea>
                        </div>

                        <input type="hidden" name="questions[${questionId}][type]" value="${type}">

                        <div class="mb-3">
                            <label for="questions[${questionId}][points]" class="form-label">Punteggio *</label>
                            <input type="number" class="form-control" id="questions[${questionId}][points]" name="questions[${questionId}][points]" value="1" min="1" required>
                        </div>

                        ${type !== 'text' ? renderAnswerSection(type, questionId) : `
                        <!-- Campo hidden per le domande di tipo text, necessario per la validazione -->
                        <input type="hidden" name="questions[${questionId}][answers][0][text]" value="Risposta di testo">
                        <input type="hidden" name="questions[${questionId}][answers][0][is_correct]" value="1">
                        <input type="hidden" name="questions[${questionId}][answers][1][text]" value="Alternativa">
                        <input type="hidden" name="questions[${questionId}][answers][1][is_correct]" value="0">
                        `}
                    </div>
                </div>
            `;

            const questionsContainer = document.getElementById('questions-container');
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = questionHtml;
            questionsContainer.appendChild(tempDiv.firstElementChild);

            if (type === 'true_false') {
                setupTrueFalseQuestion(questionId);
            }
        }

        function renderAnswerSection(type, questionId) {
            if (type === 'true_false') {
                return `
                    <div class="mb-3">
                        <label class="form-label">Seleziona la risposta corretta *</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="true_false_correct_${questionId}" id="true_option_${questionId}" value="true" checked>
                            <label class="form-check-label" for="true_option_${questionId}">Vero</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="true_false_correct_${questionId}" id="false_option_${questionId}" value="false">
                            <label class="form-check-label" for="false_option_${questionId}">Falso</label>
                        </div>

                        <input type="hidden" id="questions[${questionId}][answers][0][text]" name="questions[${questionId}][answers][0][text]" value="Vero">
                        <input type="hidden" id="questions[${questionId}][answers][0][is_correct]" name="questions[${questionId}][answers][0][is_correct]" value="1">
                        <input type="hidden" id="questions[${questionId}][answers][1][text]" name="questions[${questionId}][answers][1][text]" value="Falso">
                        <input type="hidden" id="questions[${questionId}][answers][1][is_correct]" name="questions[${questionId}][answers][1][is_correct]" value="0">
                    </div>
                `;
            } else {
                return `
                    <div class="mb-3">
                        <label class="form-label">Risposte *</label>
                        <p class="text-muted small">${type === 'multiple_choice' ? 'Seleziona una o più risposte corrette' : 'Seleziona la risposta corretta'}</p>

                        <div id="answers-container-${questionId}">
                            <!-- Le risposte verranno aggiunte qui -->
                        </div>

                        <div class="mt-2">
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="addAnswer(${questionId}, '${type}')">
                                <i class="fas fa-plus me-1"></i> Aggiungi risposta
                            </button>
                        </div>
                    </div>
                `;
            }
        }

        function setupTrueFalseQuestion(questionId) {
            const trueOption = document.getElementById(`true_option_${questionId}`);
            const falseOption = document.getElementById(`false_option_${questionId}`);

            trueOption.addEventListener('change', function() {
                if (this.checked) {
                    document.getElementById(`questions[${questionId}][answers][0][is_correct]`).value = '1';
                    document.getElementById(`questions[${questionId}][answers][1][is_correct]`).value = '0';
                }
            });

            falseOption.addEventListener('change', function() {
                if (this.checked) {
                    document.getElementById(`questions[${questionId}][answers][0][is_correct]`).value = '0';
                    document.getElementById(`questions[${questionId}][answers][1][is_correct]`).value = '1';
                }
            });
        }

        let answerCounters = {};

        function addAnswer(questionId, type) {
            if (!answerCounters[questionId]) {
                answerCounters[questionId] = 0;
            }

            const answerId = answerCounters[questionId]++;
            const answerContainer = document.getElementById(`answers-container-${questionId}`);

            const answerHtml = `
                <div class="input-group mb-2" id="answer-${questionId}-${answerId}">
                    <div class="input-group-text">
                        <input type="${type === 'multiple_choice' ? 'checkbox' : 'radio'}"
                               name="${type === 'multiple_choice' ? `questions[${questionId}][answers][${answerId}][is_correct]` : `correct_answer_${questionId}`}"
                               value="${type === 'multiple_choice' ? '1' : answerId}"
                               aria-label="Risposta corretta">
                    </div>
                    <input type="text" class="form-control"
                           name="questions[${questionId}][answers][${answerId}][text]"
                           placeholder="Testo della risposta" required>
                    <input type="hidden"
                           name="questions[${questionId}][answers][${answerId}][is_correct]"
                           value="0"
                           id="is_correct_${questionId}_${answerId}">
                    <button class="btn btn-outline-danger" type="button" onclick="removeAnswer(${questionId}, ${answerId})">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;

            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = answerHtml;
            answerContainer.appendChild(tempDiv.firstElementChild);

            // Per domande a scelta singola, configura la gestione della risposta corretta
            if (type === 'single_choice') {
                const radioInput = document.querySelector(`input[name="correct_answer_${questionId}"][value="${answerId}"]`);
                radioInput.addEventListener('change', function() {
                    // Resetta tutti i valori is_correct a 0
                    document.querySelectorAll(`input[name^="questions[${questionId}][answers]"][name$="[is_correct]"]`).forEach(input => {
                        input.value = '0';
                    });

                    // Imposta il valore is_correct a 1 per la risposta selezionata
                    document.getElementById(`is_correct_${questionId}_${this.value}`).value = '1';
                });
            } else if (type === 'multiple_choice') {
                const checkboxInput = document.querySelector(`input[name="questions[${questionId}][answers][${answerId}][is_correct]"]`);
                checkboxInput.addEventListener('change', function() {
                    document.getElementById(`is_correct_${questionId}_${answerId}`).value = this.checked ? '1' : '0';
                });
            }

            // Se è la prima risposta aggiunta, selezionala come predefinita
            if (answerId === 0) {
                if (type === 'single_choice') {
                    document.querySelector(`input[name="correct_answer_${questionId}"][value="0"]`).checked = true;
                    document.getElementById(`is_correct_${questionId}_0`).value = '1';
                }
            }
        }

        function removeAnswer(questionId, answerId) {
            const answerElement = document.getElementById(`answer-${questionId}-${answerId}`);
            answerElement.remove();
        }

        function removeQuestion(questionId) {
            const questionElement = document.getElementById(`question-${questionId}`);
            questionElement.remove();
        }
    </script>
</x-layout>
