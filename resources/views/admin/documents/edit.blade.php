<x-layout>
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Modifica Documento</h2>
            <a href="{{ route('admin.documents.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i> Torna all'elenco
            </a>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.documents.update', $document) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="title" class="form-label">Titolo</label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $document->title) }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Descrizione</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $document->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="category" class="form-label">Categoria</label>
                        <input type="text" class="form-control @error('category') is-invalid @enderror" id="category" name="category" value="{{ old('category', $document->category) }}" required list="category-suggestions">
                        <datalist id="category-suggestions">
                            <option value="Onboarding">
                            <option value="Procedure">
                            <option value="Manuali">
                            <option value="Modulistica">
                            <option value="Regolamenti">
                            <option value="Formazione">
                        </datalist>
                        <small class="form-text text-muted">Puoi selezionare una categoria esistente o crearne una nuova</small>
                        @error('category')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="document_file" class="form-label">File</label>
                        <div class="d-flex align-items-center mb-2">
                            <span class="me-3">File attuale:</span>
                            <a href="{{ Storage::url($document->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-file me-1"></i> {{ basename($document->file_path) }}
                            </a>
                        </div>
                        <input type="file" class="form-control @error('document_file') is-invalid @enderror" id="document_file" name="document_file">
                        <small class="form-text text-muted">Carica un nuovo file solo se desideri sostituire quello esistente. Dimensione massima: 10MB</small>
                        @error('document_file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="is_required" name="is_required" {{ old('is_required', $document->is_required) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_required">Documento richiesto</label>
                        <small class="form-text text-muted d-block">Se selezionato, i dipendenti dovranno visualizzare questo documento</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="visibility" class="form-label">Visibilità</label>
                        <select class="form-select @error('visibility') is-invalid @enderror" id="visibility" name="visibility" required>
                            <option value="all" {{ old('visibility', $document->visibility) === 'all' ? 'selected' : '' }}>Tutti</option>
                            <option value="admin" {{ old('visibility', $document->visibility) === 'admin' ? 'selected' : '' }}>Solo Admin</option>
                            <option value="specific_departments" {{ old('visibility', $document->visibility) === 'specific_departments' ? 'selected' : '' }}>Dipartimenti specifici</option>
                        </select>
                        @error('visibility')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">Aggiorna Documento</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout>