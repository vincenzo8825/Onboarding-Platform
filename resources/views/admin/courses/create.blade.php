<x-layout>
    <x-slot name="sidebar">
        @include('components.sidebar')
    </x-slot>

    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Crea Nuovo Corso</h2>
            <a href="{{ route('admin.courses.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Torna all'elenco
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
                <form action="{{ route('admin.courses.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="title" class="form-label">Titolo *</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="category" class="form-label">Categoria *</label>
                            <input type="text" class="form-control @error('category') is-invalid @enderror" id="category" name="category" value="{{ old('category') }}" required>
                            @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Descrizione *</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4" required>{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="content_type" class="form-label">Tipo di Contenuto *</label>
                            <select class="form-select @error('content_type') is-invalid @enderror" id="content_type" name="content_type" required>
                                <option value="">Seleziona...</option>
                                <option value="pdf" {{ old('content_type') == 'pdf' ? 'selected' : '' }}>PDF</option>
                                <option value="video" {{ old('content_type') == 'video' ? 'selected' : '' }}>Video</option>
                                <option value="link" {{ old('content_type') == 'link' ? 'selected' : '' }}>Link esterno</option>
                                <option value="text" {{ old('content_type') == 'text' ? 'selected' : '' }}>Testo</option>
                            </select>
                            @error('content_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="duration_minutes" class="form-label">Durata (in minuti)</label>
                            <input type="number" class="form-control @error('duration_minutes') is-invalid @enderror" id="duration_minutes" name="duration_minutes" value="{{ old('duration_minutes') }}" min="1">
                            @error('duration_minutes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3 content-field" id="file_field" style="display: none;">
                        <label for="file" class="form-label">File (PDF/Video) *</label>
                        <input type="file" class="form-control @error('file') is-invalid @enderror" id="file" name="file">
                        <small class="text-muted">Formati supportati: PDF, MP4, AVI, MOV (max 20MB)</small>
                        @error('file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3 content-field" id="content_field" style="display: none;">
                        <label for="content" class="form-label">Contenuto *</label>
                        <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content" rows="6">{{ old('content') }}</textarea>
                        @error('content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="has_quiz" name="has_quiz" value="1" {{ old('has_quiz') ? 'checked' : '' }}>
                        <label class="form-check-label" for="has_quiz">Il corso avrà un quiz (da creare successivamente)</label>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-primary">Crea Corso</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const contentTypeSelect = document.getElementById('content_type');
            const fileField = document.getElementById('file_field');
            const contentField = document.getElementById('content_field');

            function updateFields() {
                const contentType = contentTypeSelect.value;

                // Nascondi tutti i campi
                fileField.style.display = 'none';
                contentField.style.display = 'none';

                // Mostra i campi appropriati in base al tipo di contenuto
                if (contentType === 'pdf' || contentType === 'video') {
                    fileField.style.display = 'block';
                } else if (contentType === 'link' || contentType === 'text') {
                    contentField.style.display = 'block';
                }
            }

            // Inizializza i campi
            updateFields();

            // Aggiorna i campi quando cambia il tipo di contenuto
            contentTypeSelect.addEventListener('change', updateFields);
        });
    </script>
</x-layout>
