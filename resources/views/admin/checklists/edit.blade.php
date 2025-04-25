<x-layout>
    <x-slot name="sidebar">
        @include('components.sidebar')
    </x-slot>

    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Modifica Checklist</h2>
            <div>
                <a href="{{ route('admin.checklists.show', $checklist) }}" class="btn btn-info me-2">
                    <i class="fas fa-eye me-2"></i> Visualizza
                </a>
                <a href="{{ route('admin.checklists.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i> Torna alla lista
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Informazioni checklist</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.checklists.update', $checklist) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label">Nome checklist <span class="text-danger">*</span></label>
                        <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $checklist->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Scegli un nome descrittivo (es. "Onboarding Sviluppatori", "Documenti HR")</small>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Descrizione</label>
                        <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description', $checklist->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Fornisci una descrizione per aiutare i dipendenti a capire lo scopo della checklist</small>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="assignable_to" class="form-label">Assegnabile a <span class="text-danger">*</span></label>
                            <select id="assignable_to" name="assignable_to" class="form-select @error('assignable_to') is-invalid @enderror" required>
                                <option value="employee" @if(old('assignable_to', $checklist->assignable_to) == 'employee') selected @endif>Solo Dipendenti</option>
                                <option value="admin" @if(old('assignable_to', $checklist->assignable_to) == 'admin') selected @endif>Solo Admin</option>
                                <option value="all" @if(old('assignable_to', $checklist->assignable_to) == 'all') selected @endif>Tutti</option>
                            </select>
                            @error('assignable_to')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <fieldset class="mt-4">
                                <div class="form-check">
                                    <input type="hidden" name="is_active" value="0">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" @if(old('is_active', $checklist->is_active)) checked @endif>
                                    <label class="form-check-label" for="is_active">
                                        Checklist attiva
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input type="hidden" name="is_default" value="0">
                                    <input class="form-check-input" type="checkbox" id="is_default" name="is_default" value="1" @if(old('is_default', $checklist->is_default)) checked @endif>
                                    <label class="form-check-label" for="is_default">
                                        Assegna automaticamente ai nuovi utenti
                                    </label>
                                </div>
                            </fieldset>
                        </div>
                    </div>

                    <div class="alert alert-info mb-4">
                        <div class="d-flex">
                            <div class="me-3">
                                <i class="fas fa-info-circle fs-4"></i>
                            </div>
                            <div>
                                <h5 class="alert-heading mb-1">Nota</h5>
                                <p class="mb-0">Per gestire gli elementi della checklist, torna alla pagina di visualizzazione dopo aver salvato le modifiche.</p>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <a href="{{ route('admin.checklists.show', $checklist) }}" class="btn btn-outline-secondary me-2">Annulla</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i> Salva Modifiche
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout>
