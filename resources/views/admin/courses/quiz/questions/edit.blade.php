<x-layout>
    <x-slot name="sidebar">
        @include('components.sidebar')
    </x-slot>

    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Modifica domanda - {{ $course->title }}</h2>
            <a href="{{ route('admin.courses.quiz.questions.index', $course) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Torna all'elenco domande
            </a>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Modifica domanda</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.courses.quiz.questions.update', ['course' => $course, 'question' => $question]) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="text" class="form-label">Testo della domanda *</label>
                        <textarea name="text" id="text" rows="3" class="form-control @error('text') is-invalid @enderror" required>{{ old('text', $question->text) }}</textarea>
                        @error('text')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="type" class="form-label">Tipo di domanda *</label>
                            <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" required>
                                <option value="">Seleziona un tipo</option>
                                <option value="text" {{ old('type', $question->type) == 'text' ? 'selected' : '' }}>Risposta testuale</option>
                                <option value="true_false" {{ old('type', $question->type) == 'true_false' ? 'selected' : '' }}>Vero/Falso</option>
                                <option value="single_choice" {{ old('type', $question->type) == 'single_choice' ? 'selected' : '' }}>Scelta singola</option>
                                <option value="multiple_choice" {{ old('type', $question->type) == 'multiple_choice' ? 'selected' : '' }}>Scelta multipla</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="points" class="form-label">Punteggio *</label>
                            <input type="number" name="points" id="points" min="1" max="100" class="form-control @error('points') is-invalid @enderror" value="{{ old('points', $question->points) }}" required>
                            @error('points')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Risposta testuale -->
                    <div id="text-answer-section" class="answer-section mb-3" style="display: none;">
                        <label for="correct_answer" class="form-label">Risposta corretta</label>
                        <textarea name="correct_answer" id="correct_answer" rows="2" class="form-control @error('correct_answer') is-invalid @enderror">{{ old('correct_answer', $question->correct_answer ?? '') }}</textarea>
                        <div class="form-text">Inserisci la risposta corretta che verrà utilizzata per la valutazione automatica.</div>
                        @error('correct_answer')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Risposta vero/falso -->
                    <div id="true_false-answer-section" class="answer-section mb-3" style="display: none;">
                        <label class="form-label">Risposta corretta</label>
                        <div class="d-flex">
                            <div class="form-check me-3">
                                <input class="form-check-input" type="radio" name="is_true" id="answer-true" value="1" {{ old('is_true', $question->is_true ?? '') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label" for="answer-true">Vero</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="is_true" id="answer-false" value="0" {{ old('is_true', $question->is_true ?? '') == '0' ? 'checked' : '' }}>
                                <label class="form-check-label" for="answer-false">Falso</label>
                            </div>
                        </div>
                        @error('is_true')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Risposta a scelta singola/multipla -->
                    <div id="choice-answer-section" class="answer-section mb-3" style="display: none;">
                        <label class="form-label">Opzioni di risposta</label>

                        <div class="choices-container">
                            @if(old('answers'))
                                @foreach(old('answers') as $key => $answer)
                                    <div class="choice-item mb-2 d-flex align-items-start">
                                        <div class="form-check mt-2 me-2 choice-correct">
                                            @if(old('type', $question->type) == 'single_choice')
                                                <input type="radio" class="form-check-input" name="correct[]" value="{{ $key }}" {{ isset(old('correct')[0]) && old('correct')[0] == $key ? 'checked' : '' }}>
                                            @else
                                                <input type="checkbox" class="form-check-input" name="correct[]" value="{{ $key }}" {{ isset(old('correct')) && in_array($key, old('correct')) ? 'checked' : '' }}>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1">
                                            <input type="text" name="answers[]" class="form-control @error('answers.'.$key) is-invalid @enderror" placeholder="Opzione di risposta" value="{{ $answer }}">
                                            @error('answers.'.$key)
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <button type="button" class="btn btn-outline-danger ms-2 remove-choice">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                @endforeach
                            @elseif($question->answers && count($question->answers) > 0)
                                @foreach($question->answers as $key => $answer)
                                    <div class="choice-item mb-2 d-flex align-items-start">
                                        <div class="form-check mt-2 me-2 choice-correct">
                                            @if($question->type == 'single_choice')
                                                <input type="radio" class="form-check-input" name="correct[]" value="{{ $key }}" {{ $answer->is_correct ? 'checked' : '' }}>
                                            @else
                                                <input type="checkbox" class="form-check-input" name="correct[]" value="{{ $key }}" {{ $answer->is_correct ? 'checked' : '' }}>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1">
                                            <input type="text" name="answers[]" class="form-control" placeholder="Opzione di risposta" value="{{ $answer->text }}">
                                        </div>
                                        <button type="button" class="btn btn-outline-danger ms-2 remove-choice">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                @endforeach
                            @else
                                <!-- Default options if no existing answers -->
                                <div class="choice-item mb-2 d-flex align-items-start">
                                    <div class="form-check mt-2 me-2 choice-correct">
                                        <input type="radio" class="form-check-input" name="correct[]" value="0">
                                    </div>
                                    <div class="flex-grow-1">
                                        <input type="text" name="answers[]" class="form-control" placeholder="Opzione di risposta">
                                    </div>
                                    <button type="button" class="btn btn-outline-danger ms-2 remove-choice">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <div class="choice-item mb-2 d-flex align-items-start">
                                    <div class="form-check mt-2 me-2 choice-correct">
                                        <input type="radio" class="form-check-input" name="correct[]" value="1">
                                    </div>
                                    <div class="flex-grow-1">
                                        <input type="text" name="answers[]" class="form-control" placeholder="Opzione di risposta">
                                    </div>
                                    <button type="button" class="btn btn-outline-danger ms-2 remove-choice">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            @endif
                        </div>

                        <button type="button" id="add-choice" class="btn btn-sm btn-outline-primary mt-2">
                            <i class="fas fa-plus me-1"></i> Aggiungi opzione
                        </button>

                        @error('answers')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                        @error('correct')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="button" class="btn btn-secondary me-2" onclick="window.history.back()">Annulla</button>
                        <button type="submit" class="btn btn-primary">Aggiorna domanda</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const typeSelect = document.getElementById('type');
            const textAnswerSection = document.getElementById('text-answer-section');
            const trueFalseAnswerSection = document.getElementById('true_false-answer-section');
            const choiceAnswerSection = document.getElementById('choice-answer-section');
            const choicesContainer = document.querySelector('.choices-container');
            const addChoiceBtn = document.getElementById('add-choice');

            // Mostra/nascondi sezioni in base al tipo di domanda
            function updateAnswerSections() {
                const selectedType = typeSelect.value;

                // Nascondi tutte le sezioni
                textAnswerSection.style.display = 'none';
                trueFalseAnswerSection.style.display = 'none';
                choiceAnswerSection.style.display = 'none';

                // Mostra la sezione corretta
                if (selectedType === 'text') {
                    textAnswerSection.style.display = 'block';
                } else if (selectedType === 'true_false') {
                    trueFalseAnswerSection.style.display = 'block';
                } else if (selectedType === 'single_choice' || selectedType === 'multiple_choice') {
                    choiceAnswerSection.style.display = 'block';
                    updateChoiceInputType(selectedType);
                }
            }

            // Aggiorna il tipo di input per le scelte (radio o checkbox)
            function updateChoiceInputType(type) {
                const choiceCorrectInputs = document.querySelectorAll('.choice-correct input');
                choiceCorrectInputs.forEach((input, index) => {
                    if (type === 'single_choice') {
                        input.type = 'radio';
                    } else {
                        input.type = 'checkbox';
                    }
                    input.name = 'correct[]';
                    input.value = index;
                });
            }

            // Aggiungi nuova opzione di scelta
            addChoiceBtn.addEventListener('click', function() {
                const choiceItems = document.querySelectorAll('.choice-item');
                const newIndex = choiceItems.length;

                const newChoice = document.createElement('div');
                newChoice.className = 'choice-item mb-2 d-flex align-items-start';

                const inputType = typeSelect.value === 'single_choice' ? 'radio' : 'checkbox';

                newChoice.innerHTML = `
                    <div class="form-check mt-2 me-2 choice-correct">
                        <input type="${inputType}" class="form-check-input" name="correct[]" value="${newIndex}">
                    </div>
                    <div class="flex-grow-1">
                        <input type="text" name="answers[]" class="form-control" placeholder="Opzione di risposta">
                    </div>
                    <button type="button" class="btn btn-outline-danger ms-2 remove-choice">
                        <i class="fas fa-times"></i>
                    </button>
                `;

                choicesContainer.appendChild(newChoice);

                // Aggiungi event listener al pulsante di rimozione
                newChoice.querySelector('.remove-choice').addEventListener('click', function() {
                    if (document.querySelectorAll('.choice-item').length > 2) {
                        newChoice.remove();
                        updateChoiceIndices();
                    } else {
                        alert('È necessario avere almeno due opzioni di risposta.');
                    }
                });
            });

            // Rimuovi opzione di scelta
            document.addEventListener('click', function(e) {
                if (e.target.closest('.remove-choice')) {
                    const choiceItems = document.querySelectorAll('.choice-item');
                    if (choiceItems.length > 2) {
                        e.target.closest('.choice-item').remove();
                        updateChoiceIndices();
                    } else {
                        alert('È necessario avere almeno due opzioni di risposta.');
                    }
                }
            });

            // Aggiorna gli indici delle opzioni di scelta
            function updateChoiceIndices() {
                const choiceCorrectInputs = document.querySelectorAll('.choice-correct input');
                choiceCorrectInputs.forEach((input, index) => {
                    input.value = index;
                });
            }

            // Event listener per il cambio di tipo di domanda
            typeSelect.addEventListener('change', updateAnswerSections);

            // Inizializza la visualizzazione corretta
            updateAnswerSections();
        });
    </script>
    @endpush
</x-layout>
