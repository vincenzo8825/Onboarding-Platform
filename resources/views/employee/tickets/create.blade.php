<x-layout>
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Crea Nuovo Ticket</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('employee.tickets.store') }}" method="POST">
                            @csrf
                            
                            <div class="mb-3">
                                <label for="title" class="form-label">Titolo</label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="category" class="form-label">Categoria</label>
                                <select class="form-select @error('category') is-invalid @enderror" id="category" name="category" required>
                                    <option value="">Seleziona una categoria...</option>
                                    <option value="Accesso" {{ old('category') === 'Accesso' ? 'selected' : '' }}>Accesso</option>
                                    <option value="Documenti" {{ old('category') === 'Documenti' ? 'selected' : '' }}>Documenti</option>
                                    <option value="Corsi" {{ old('category') === 'Corsi' ? 'selected' : '' }}>Corsi</option>
                                    <option value="Checklist" {{ old('category') === 'Checklist' ? 'selected' : '' }}>Checklist</option>
                                    <option value="Profilo" {{ old('category') === 'Profilo' ? 'selected' : '' }}>Profilo</option>
                                    <option value="Altro" {{ old('category') === 'Altro' ? 'selected' : '' }}>Altro</option>
                                </select>
                                @error('category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="priority" class="form-label">Priorità</label>
                                <select class="form-select @error('priority') is-invalid @enderror" id="priority" name="priority" required>
                                    <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>Bassa</option>
                                    <option value="medium" {{ old('priority') === 'medium' ? 'selected' : '' }} selected>Media</option>
                                    <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>Alta</option>
                                    <option value="urgent" {{ old('priority') === 'urgent' ? 'selected' : '' }}>Urgente</option>
                                </select>
                                @error('priority')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">Descrizione</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="6" required>{{ old('description') }}</textarea>
                                <small class="form-text text-muted">Descrivi dettagliatamente il problema o la richiesta.</small>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('employee.tickets.index') }}" class="btn btn-secondary">Annulla</a>
                                <button type="submit" class="btn btn-primary">Invia Ticket</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>