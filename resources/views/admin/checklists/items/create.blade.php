<x-layout>
    <x-slot name="sidebar">
        @include('components.sidebar')
    </x-slot>

    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Aggiungi Elemento alla Checklist</h2>
            <a href="{{ route('admin.checklists.show', $checklist) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i> Torna alla checklist
            </a>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Nuovo elemento per: {{ $checklist->name }}</h5>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.checklists.items.store', $checklist) }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="title" class="form-label">Titolo elemento <span class="text-danger">*</span></label>
                        <input type="text" id="title" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Descrizione</label>
                        <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Fornisci istruzioni dettagliate su come completare questo elemento</small>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="order" class="form-label">Ordine</label>
                            <input type="number" id="order" name="order" class="form-control @error('order') is-invalid @enderror" value="{{ old('order', $checklist->items()->count() + 1) }}" min="1">
                            @error('order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Posizione nell'elenco (1 è il primo)</small>
                        </div>
                        <div class="col-md-6">
                            <label for="due_date" class="form-label">Data di scadenza</label>
                            <input type="date" id="due_date" name="due_date" class="form-control @error('due_date') is-invalid @enderror" value="{{ old('due_date') }}">
                            @error('due_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Opzionale: entro quando deve essere completato</small>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="form-check form-switch mb-2">
                                <input type="hidden" name="requires_file" value="0">
                                <input class="form-check-input" type="checkbox" id="requires_file" name="requires_file" value="1" {{ old('requires_file') ? 'checked' : '' }}>
                                <label class="form-check-label" for="requires_file">
                                    Richiede il caricamento di un file
                                </label>
                            </div>
                            <div class="form-check form-switch mb-2">
                                <input type="hidden" name="requires_approval" value="0">
                                <input class="form-check-input" type="checkbox" id="requires_approval" name="requires_approval" value="1" {{ old('requires_approval') ? 'checked' : '' }}>
                                <label class="form-check-label" for="requires_approval">
                                    Richiede approvazione di un admin
                                </label>
                            </div>
                            <div class="form-check form-switch">
                                <input type="hidden" name="is_mandatory" value="0">
                                <input class="form-check-input" type="checkbox" id="is_mandatory" name="is_mandatory" value="1" {{ old('is_mandatory', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_mandatory">
                                    Obbligatorio per completare la checklist
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info mt-3 mb-4">
                        <div class="d-flex">
                            <div class="me-3">
                                <i class="fas fa-info-circle fs-4"></i>
                            </div>
                            <div>
                                <h5 class="alert-heading mb-1">Suggerimenti</h5>
                                <ul class="mb-0">
                                    <li>Per documenti da caricare, seleziona "Richiede il caricamento di un file"</li>
                                    <li>Per elementi che necessitano verifica, seleziona "Richiede approvazione"</li>
                                    <li>Scrivi descrizioni chiare per aiutare i dipendenti a capire cosa devono fare</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <a href="{{ route('admin.checklists.show', $checklist) }}" class="btn btn-outline-secondary me-2">Annulla</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i> Salva Elemento
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout>
