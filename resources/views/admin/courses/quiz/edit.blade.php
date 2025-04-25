<x-layout>
    <x-slot name="sidebar">
        @include('components.sidebar')
    </x-slot>

    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Modifica Quiz: {{ $quiz->title }}</h2>
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
                <form id="quiz-form" action="{{ route('admin.courses.quiz.update', $course) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="title" class="form-label">Titolo Quiz *</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $quiz->title) }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="description" class="form-label">Descrizione</label>
                            <input type="text" class="form-control @error('description') is-invalid @enderror" id="description" name="description" value="{{ old('description', $quiz->description) }}">
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="passing_score" class="form-label">Punteggio minimo (%) *</label>
                            <input type="number" class="form-control @error('passing_score') is-invalid @enderror" id="passing_score" name="passing_score" value="{{ old('passing_score', $quiz->passing_score) }}" min="1" max="100" required>
                            @error('passing_score')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="time_limit_minutes" class="form-label">Tempo limite (minuti)</label>
                            <input type="number" class="form-control @error('time_limit_minutes') is-invalid @enderror" id="time_limit_minutes" name="time_limit_minutes" value="{{ old('time_limit_minutes', $quiz->time_limit_minutes) }}" min="1">
                            @error('time_limit_minutes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="attempts_allowed" class="form-label">Tentativi consentiti *</label>
                            <input type="number" class="form-control @error('attempts_allowed') is-invalid @enderror" id="attempts_allowed" name="attempts_allowed" value="{{ old('attempts_allowed', $quiz->attempts_allowed) }}" min="1" required>
                            @error('attempts_allowed')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="mb-3">
                        <h4>Domande</h4>
                        <p class="text-muted">Modifica le domande del quiz o aggiungine di nuove. Puoi inserire domande a scelta singola, scelta multipla, vero/falso o a risposta libera.</p>
                    </div>

                    <div id="questions-container">
                        <!-- Domande esistenti -->
                        @foreach($quiz->questions as $index => $question)
                            <div class="card mb-4 question-card" id="question-existing-{{ $question->id }}">
                                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">
                                        @if($question->type == 'multiple_choice')
                                            Domanda a scelta multipla
                                        @elseif($question->type == 'single_choice')
                                            Domanda a scelta singola
                                        @elseif($question->type == 'true_false')
                                            Domanda vero/falso
                                        @else
                                            Domanda a risposta libera
                                        @endif
                                    </h5>
                                    <button type="button" class="btn btn-danger btn-sm" onclick="removeExistingQuestion({{ $question->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="questions[existing_{{ $question->id }}][text]" class="form-label">Testo della domanda *</label>
                                        <textarea class="form-control" id="questions[existing_{{ $question->id }}][text]" name="questions[existing_{{ $question->id }}][text]" rows="2" required>{{ $question->text }}</textarea>
                                    </div>

                                    <input type="hidden" name="questions[existing_{{ $question->id }}][id]" value="{{ $question->id }}">
                                    <input type="hidden" name="questions[existing_{{ $question->id }}][type]" value="{{ $question->type }}">

                                    <div class="mb-3">
                                        <label for="questions[existing_{{ $question->id }}][points]" class="form-label">Punteggio *</label>
                                        <input type="number" class="form-control" id="questions[existing_{{ $question->id }}][points]" name="questions[existing_{{ $question->id }}][points]" value="{{ $question->points }}" min="1" required>
                                    </div>

                                    @if($question->type !== 'text')
                                        <div class="mb-3">
                                            <label for="questions[existing_{{ $question->id }}][answers]" class="form-label">Risposte *</label>
                                            <div class="answers-container">
                                                @foreach($question->answers as $aIndex => $answer)
                                                    <div class="input-group mb-2">
                                                        <input type="hidden" name="questions[existing_{{ $question->id }}][answers][existing_{{ $answer->id }}][id]" value="{{ $answer->id }}">
                                                        <input type="text" class="form-control" name="questions[existing_{{ $question->id }}][answers][existing_{{ $answer->id }}][text]" value="{{ $answer->text }}" required>
                                                        <div class="input-group-text">
                                                            @if($question->type == 'multiple_choice')
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox" name="questions[existing_{{ $question->id }}][answers][existing_{{ $answer->id }}][is_correct]" value="1" {{ $answer->is_correct ? 'checked' : '' }}>
                                                                    <label class="form-check-label">Corretta</label>
                                                                </div>
                                                            @elseif($question->type == 'single_choice' || $question->type == 'true_false')
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="radio" name="single_choice_correct_existing_{{ $question->id }}" value="existing_{{ $answer->id }}" {{ $answer->is_correct ? 'checked' : '' }} onchange="updateSingleChoiceCorrect('existing_{{ $question->id }}', 'existing_{{ $answer->id }}')">
                                                                    <label class="form-check-label">Corretta</label>
                                                                </div>
                                                                <input type="hidden" id="questions[existing_{{ $question->id }}][answers][existing_{{ $answer->id }}][is_correct]" name="questions[existing_{{ $question->id }}][answers][existing_{{ $answer->id }}][is_correct]" value="{{ $answer->is_correct ? '1' : '0' }}">
                                                            @endif
                                                        </div>
                                                        @if($question->type != 'true_false')
                                                            <button type="button" class="btn btn-outline-danger" onclick="removeAnswer(this)">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                @endforeach

                                                @if($question->type != 'true_false')
                                                    <button type="button" class="btn btn-outline-primary btn-sm mt-2" onclick="addAnswerToExistingQuestion({{ $question->id }}, '{{ $question->type }}')">
                                                        <i class="fas fa-plus me-1"></i> Aggiungi risposta
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                        <!-- Nuove domande verranno aggiunte qui -->
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
                        <button type="submit" class="btn btn-primary" id="submit-quiz">Aggiorna Quiz</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Configura validazione del form
            document.getElementById('quiz-form').addEventListener('submit', function(e) {
                const questionsContainer = document.getElementById('questions-container');
                if (questionsContainer.children.length === 0) {
                    e.preventDefault();
                    alert('Aggiungi almeno una domanda al quiz.');
                }
            });

            // Configura radio buttons per domande esistenti a scelta singola
            document.querySelectorAll('[name^="single_choice_correct_existing_"]').forEach(radio => {
                radio.addEventListener('change', function() {
                    const questionId = this.name.replace('single_choice_correct_existing_', '');
                    const answerId = this.value;
                    updateSingleChoiceCorrect('existing_' + questionId, answerId);
                });
            });
        });

        let questionCounter = {{ $quiz->questions->count() }};

        function addQuestion(type) {
            questionCounter++;
            const questionId = 'new_' + questionCounter;

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
                        <button type="button" class="btn btn-danger btn-sm" onclick="removeQuestion('${questionId}')">
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

                        ${type !== 'text' ? renderAnswerSection(type, questionId) : ''}
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
                        <div class="answers-container">
                            ${createAnswerInput(questionId, 0, type)}
                            ${createAnswerInput(questionId, 1, type)}
                            <button type="button" class="btn btn-outline-primary btn-sm mt-2" onclick="addAnswer('${questionId}', '${type}')">
                                <i class="fas fa-plus me-1"></i> Aggiungi risposta
                            </button>
                        </div>
                    </div>
                `;
            }
        }

        function createAnswerInput(questionId, index, type) {
            if (type === 'multiple_choice') {
                return `
                    <div class="input-group mb-2">
                        <input type="text" class="form-control" name="questions[${questionId}][answers][${index}][text]" placeholder="Risposta ${index + 1}" required>
                        <div class="input-group-text">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="questions[${questionId}][answers][${index}][is_correct]" value="1">
                                <label class="form-check-label">Corretta</label>
                            </div>
                        </div>
                        <button type="button" class="btn btn-outline-danger" onclick="removeAnswer(this)">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `;
            } else {
                return `
                    <div class="input-group mb-2">
                        <input type="text" class="form-control" name="questions[${questionId}][answers][${index}][text]" placeholder="Risposta ${index + 1}" required>
                        <div class="input-group-text">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="single_choice_correct_${questionId}" value="${index}" ${index === 0 ? 'checked' : ''} onchange="updateSingleChoiceCorrect('${questionId}', ${index})">
                                <label class="form-check-label">Corretta</label>
                            </div>
                        </div>
                        <input type="hidden" id="questions[${questionId}][answers][${index}][is_correct]" name="questions[${questionId}][answers][${index}][is_correct]" value="${index === 0 ? '1' : '0'}">
                        <button type="button" class="btn btn-outline-danger" onclick="removeAnswer(this)">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `;
            }
        }

        function addAnswer(questionId, type) {
            const answersContainer = document.querySelector(`#question-${questionId} .answers-container`);
            const answerCount = answersContainer.querySelectorAll('.input-group').length;

            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = createAnswerInput(questionId, answerCount, type);

            // Inserisci il nuovo input prima del pulsante "Aggiungi risposta"
            answersContainer.insertBefore(tempDiv.firstElementChild, answersContainer.lastElementChild);
        }

        function addAnswerToExistingQuestion(questionId, type) {
            const answersContainer = document.querySelector(`#question-existing-${questionId} .answers-container`);
            const answerCount = answersContainer.querySelectorAll('.input-group').length;
            const newIndex = 'new_' + (answerCount);

            let answerHtml;
            if (type === 'multiple_choice') {
                answerHtml = `
                    <div class="input-group mb-2">
                        <input type="text" class="form-control" name="questions[existing_${questionId}][answers][${newIndex}][text]" placeholder="Nuova risposta" required>
                        <div class="input-group-text">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="questions[existing_${questionId}][answers][${newIndex}][is_correct]" value="1">
                                <label class="form-check-label">Corretta</label>
                            </div>
                        </div>
                        <button type="button" class="btn btn-outline-danger" onclick="removeAnswer(this)">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `;
            } else {
                answerHtml = `
                    <div class="input-group mb-2">
                        <input type="text" class="form-control" name="questions[existing_${questionId}][answers][${newIndex}][text]" placeholder="Nuova risposta" required>
                        <div class="input-group-text">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="single_choice_correct_existing_${questionId}" value="${newIndex}" onchange="updateSingleChoiceCorrect('existing_${questionId}', '${newIndex}')">
                                <label class="form-check-label">Corretta</label>
                            </div>
                        </div>
                        <input type="hidden" id="questions[existing_${questionId}][answers][${newIndex}][is_correct]" name="questions[existing_${questionId}][answers][${newIndex}][is_correct]" value="0">
                        <button type="button" class="btn btn-outline-danger" onclick="removeAnswer(this)">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `;
            }

            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = answerHtml;

            // Inserisci il nuovo input prima del pulsante "Aggiungi risposta"
            answersContainer.insertBefore(tempDiv.firstElementChild, answersContainer.lastElementChild);
        }

        function removeAnswer(button) {
            button.closest('.input-group').remove();
        }

        function removeQuestion(questionId) {
            document.getElementById(`question-${questionId}`).remove();
        }

        function removeExistingQuestion(questionId) {
            document.getElementById(`question-existing-${questionId}`).remove();
        }

        function setupTrueFalseQuestion(questionId) {
            const trueOption = document.getElementById(`true_option_${questionId}`);
            const falseOption = document.getElementById(`false_option_${questionId}`);

            trueOption.addEventListener('change', function() {
                document.getElementById(`questions[${questionId}][answers][0][is_correct]`).value = "1";
                document.getElementById(`questions[${questionId}][answers][1][is_correct]`).value = "0";
            });

            falseOption.addEventListener('change', function() {
                document.getElementById(`questions[${questionId}][answers][0][is_correct]`).value = "0";
                document.getElementById(`questions[${questionId}][answers][1][is_correct]`).value = "1";
            });
        }

        function updateSingleChoiceCorrect(questionId, selectedAnswerId) {
            // Reset all answers to not correct
            document.querySelectorAll(`[name^="questions[${questionId}][answers]"][name$="[is_correct]"]`).forEach(input => {
                input.value = "0";
            });

            // Set the selected answer as correct
            document.getElementById(`questions[${questionId}][answers][${selectedAnswerId}][is_correct]`).value = "1";
        }
    </script>
</x-layout>
