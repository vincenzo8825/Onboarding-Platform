<x-layout>
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Nuova Richiesta di Documento</h2>
            <a href="{{ url()->previous() }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i> Indietro
            </a>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.document-requests.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="employee_id" class="form-label">Dipendente</label>
                        @if(request()->route()->hasParameter('employee'))
                            @php
                                $employee = \App\Models\User::find(request()->route('employee'));
                            @endphp
                            <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                            <input type="text" class="form-control" value="{{ $employee->name }}" readonly>
                        @else
                            <select class="form-select @error('employee_id') is-invalid @enderror" id="employee_id" name="employee_id" required>
                                <option value="">Seleziona dipendente</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->name }} ({{ $employee->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('employee_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        @endif
                    </div>

                    <div class="mb-3">
                        <label for="document_type" class="form-label">Tipo di Documento</label>
                        <select class="form-select @error('document_type') is-invalid @enderror" id="document_type" name="document_type" required>
                            <option value="">Seleziona tipo documento</option>
                            @foreach($documentTypes as $type)
                                <option value="{{ $type }}" {{ old('document_type') == $type ? 'selected' : '' }}>
                                    {{ $type }}
                                </option>
                            @endforeach
                            <option value="Altro">Altro (specificare nella descrizione)</option>
                        </select>
                        @error('document_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Descrizione/Istruzioni</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                        <small class="form-text text-muted">Specifica eventuali dettagli o requisiti per il documento</small>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="due_date" class="form-label">Data Scadenza (opzionale)</label>
                        <input type="date" class="form-control @error('due_date') is-invalid @enderror" id="due_date" name="due_date" value="{{ old('due_date') }}">
                        <small class="form-text text-muted">Entro quando il documento deve essere caricato</small>
                        @error('due_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">Invia Richiesta</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout>
