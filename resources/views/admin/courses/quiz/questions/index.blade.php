<x-layout>
    <x-slot name="sidebar">
        @include('components.sidebar')
    </x-slot>

    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Domande del quiz - {{ $course->title }}</h2>
            <div>
                <a href="{{ route('admin.courses.quiz.show', $course) }}" class="btn btn-secondary me-2">
                    <i class="fas fa-arrow-left me-1"></i> Torna al quiz
                </a>
                <a href="{{ route('admin.courses.quiz.questions.create', $course) }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> Aggiungi domanda
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Elenco domande</h5>
                <span class="badge bg-primary">{{ $questions->count() }} domande</span>
            </div>

            @if($questions->count() > 0)
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 60px">#</th>
                                    <th>Domanda</th>
                                    <th style="width: 150px">Tipo</th>
                                    <th style="width: 100px">Punti</th>
                                    <th style="width: 180px">Azioni</th>
                                </tr>
                            </thead>
                            <tbody class="questions-list">
                                @foreach($questions as $index => $question)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ Str::limit($question->text, 80) }}</td>
                                        <td>
                                            @if($question->type == 'multiple_choice')
                                                <span class="badge bg-info">Scelta multipla</span>
                                            @elseif($question->type == 'single_choice')
                                                <span class="badge bg-primary">Scelta singola</span>
                                            @elseif($question->type == 'true_false')
                                                <span class="badge bg-secondary">Vero/Falso</span>
                                            @elseif($question->type == 'text')
                                                <span class="badge bg-success">Testo</span>
                                            @endif
                                        </td>
                                        <td>{{ $question->points }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('admin.courses.quiz.questions.edit', ['course' => $course, 'question' => $question]) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-danger delete-question" data-bs-toggle="modal" data-bs-target="#deleteQuestionModal" data-question-id="{{ $question->id }}" data-question-text="{{ Str::limit($question->text, 50) }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="card-body text-center py-5">
                    <div class="mb-3">
                        <i class="fas fa-question-circle fa-4x text-muted"></i>
                    </div>
                    <h5>Nessuna domanda presente</h5>
                    <p class="text-muted">Clicca sul pulsante "Aggiungi domanda" per iniziare a creare il quiz.</p>
                    <a href="{{ route('admin.courses.quiz.questions.create', $course) }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Aggiungi domanda
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal Elimina Domanda -->
    <div class="modal fade" id="deleteQuestionModal" tabindex="-1" aria-labelledby="deleteQuestionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteQuestionModalLabel">Conferma eliminazione</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Sei sicuro di voler eliminare questa domanda?</p>
                    <p id="question-to-delete" class="text-danger fw-bold"></p>
                    <p>Questa azione non può essere annullata.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                    <form id="delete-question-form" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Elimina</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Gestione del modal di eliminazione
            const deleteButtons = document.querySelectorAll('.delete-question');
            const deleteForm = document.getElementById('delete-question-form');
            const questionToDelete = document.getElementById('question-to-delete');

            deleteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const questionId = this.getAttribute('data-question-id');
                    const questionText = this.getAttribute('data-question-text');

                    questionToDelete.textContent = questionText;
                    deleteForm.action = "{{ route('admin.courses.quiz.questions.index', $course) }}/" + questionId;
                });
            });

            // Sortable per riordinare le domande (se necessario)
            if (typeof Sortable !== 'undefined') {
                const questionsList = document.querySelector('.questions-list');
                if (questionsList) {
                    Sortable.create(questionsList, {
                        animation: 150,
                        handle: 'tr',
                        onEnd: function(evt) {
                            // Aggiorna l'ordine se necessario con una chiamata AJAX
                        }
                    });
                }
            }
        });
    </script>
    @endpush
</x-layout>
