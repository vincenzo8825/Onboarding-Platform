<x-layout>
    <x-slot name="sidebar">
        @include('components.sidebar')
    </x-slot>

    <div class="container py-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Modifica Corso</h3>
                    </div>
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('admin.courses.update', $course) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="form-group mb-3">
                                <label for="title">Titolo</label>
                                <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $course->title) }}" required>
                            </div>

                            <div class="form-group mb-3">
                                <label for="description">Descrizione</label>
                                <textarea class="form-control" id="description" name="description" rows="4" required>{{ old('description', $course->description) }}</textarea>
                            </div>

                            <div class="form-group mb-3">
                                <label for="category">Categoria</label>
                                <input type="text" class="form-control" id="category" name="category" value="{{ old('category', $course->category) }}" required>
                            </div>

                            <div class="form-group mb-3">
                                <label for="content_type">Tipo di Contenuto</label>
                                <select class="form-select" id="content_type" name="content_type" required>
                                    <option value="text" {{ old('content_type', $course->content_type) == 'text' ? 'selected' : '' }}>Testo</option>
                                    <option value="pdf" {{ old('content_type', $course->content_type) == 'pdf' ? 'selected' : '' }}>PDF</option>
                                    <option value="video" {{ old('content_type', $course->content_type) == 'video' ? 'selected' : '' }}>Video</option>
                                    <option value="link" {{ old('content_type', $course->content_type) == 'link' ? 'selected' : '' }}>Link</option>
                                </select>
                            </div>

                            <div class="form-group mb-3 content-field" id="text-field" style="{{ old('content_type', $course->content_type) == 'text' ? '' : 'display: none;' }}">
                                <label for="content">Contenuto Testuale</label>
                                <textarea class="form-control" id="content" name="content" rows="6">{{ old('content', $course->content) }}</textarea>
                            </div>

                            <div class="form-group mb-3 content-field" id="link-field" style="{{ old('content_type', $course->content_type) == 'link' ? '' : 'display: none;' }}">
                                <label for="content">URL</label>
                                <input type="url" class="form-control" id="content-link" name="content" value="{{ old('content', $course->content) }}">
                            </div>

                            <div class="form-group mb-3 content-field" id="file-field" style="{{ old('content_type', $course->content_type) == 'pdf' || old('content_type', $course->content_type) == 'video' ? '' : 'display: none;' }}">
                                <label for="file">File (PDF o Video)</label>
                                @if($course->file_path)
                                    <p>File attuale: <a href="{{ Storage::url($course->file_path) }}" target="_blank">{{ basename($course->file_path) }}</a></p>
                                @endif
                                <input type="file" class="form-control" id="file" name="file">
                                <small class="form-text text-muted">Lascia vuoto per mantenere il file attuale.</small>
                            </div>

                            <div class="form-group mb-3">
                                <label for="duration_minutes">Durata (minuti)</label>
                                <input type="number" class="form-control" id="duration_minutes" name="duration_minutes" value="{{ old('duration_minutes', $course->duration_minutes) }}" min="1">
                            </div>

                            <div class="form-check mb-3">
                                <input type="checkbox" class="form-check-input" id="has_quiz" name="has_quiz" value="1" {{ old('has_quiz', $course->has_quiz) ? 'checked' : '' }}>
                                <label class="form-check-label" for="has_quiz">Questo corso ha un quiz</label>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Aggiorna Corso</button>
                                <a href="{{ route('admin.courses.index') }}" class="btn btn-secondary">Annulla</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const contentTypeSelect = document.getElementById('content_type');
            const contentFields = document.querySelectorAll('.content-field');
            const textField = document.getElementById('text-field');
            const linkField = document.getElementById('link-field');
            const fileField = document.getElementById('file-field');

            contentTypeSelect.addEventListener('change', function() {
                // Nascondi tutti i campi
                contentFields.forEach(field => field.style.display = 'none');

                // Mostra il campo appropriato in base alla selezione
                switch(this.value) {
                    case 'text':
                        textField.style.display = 'block';
                        break;
                    case 'link':
                        linkField.style.display = 'block';
                        break;
                    case 'pdf':
                    case 'video':
                        fileField.style.display = 'block';
                        break;
                }
            });
        });
    </script>
    @endpush
</x-layout>
