<x-layout>
    <x-slot name="sidebar">
        @include('components.sidebar')
    </x-slot>

    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Carica Documento Richiesto</h2>
            <a href="{{ route('employee.document-requests.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i> Torna all'elenco
            </a>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Dettagli della Richiesta</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="fw-bold">Tipo di Documento</label>
                            <p>{{ $documentRequest->document_type }}</p>
                        </div>

                        <div class="mb-3">
                            <label class="fw-bold">Descrizione</label>
                            <p>{{ $documentRequest->description ?: 'Nessuna descrizione fornita' }}</p>
                        </div>

                        <div class="mb-3">
                            <label class="fw-bold">Richiesto da</label>
                            <p>{{ $documentRequest->admin->name }}</p>
                        </div>

                        <div class="mb-3">
                            <label class="fw-bold">Richiesto il</label>
                            <p>{{ $documentRequest->created_at->format('d/m/Y H:i') }}</p>
                        </div>

                        @if($documentRequest->due_date)
                            <div class="mb-3">
                                <label class="fw-bold">Scadenza</label>
                                <p class="text-{{ \Carbon\Carbon::parse($documentRequest->due_date)->isPast() ? 'danger' : 'dark' }}">
                                    {{ \Carbon\Carbon::parse($documentRequest->due_date)->format('d/m/Y') }}
                                    @if(\Carbon\Carbon::parse($documentRequest->due_date)->isPast())
                                        <span class="badge bg-danger ms-2">Scaduto</span>
                                    @endif
                                </p>
                            </div>
                        @endif

                        <div class="mb-3">
                            <label class="fw-bold">Stato</label>
                            @if($documentRequest->isPending())
                                <p><span class="badge bg-warning">In attesa</span></p>
                            @elseif($documentRequest->isSubmitted())
                                <p><span class="badge bg-info">Caricato</span></p>
                            @elseif($documentRequest->isApproved())
                                <p><span class="badge bg-success">Approvato</span></p>
                            @elseif($documentRequest->isRejected())
                                <p><span class="badge bg-danger">Rifiutato</span></p>
                            @endif
                        </div>

                        @if($documentRequest->notes)
                            <div class="mb-3">
                                <label class="fw-bold">Note</label>
                                <p>{{ $documentRequest->notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Carica Documento</h5>
                    </div>
                    <div class="card-body">
                        @if($documentRequest->isPending())
                            <form action="{{ route('employee.document-requests.submit', $documentRequest) }}" method="POST" enctype="multipart/form-data">
                                @csrf

                                <div class="mb-3">
                                    <label for="title" class="form-label">Titolo Documento</label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $documentRequest->document_type) }}" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Descrizione (opzionale)</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="2">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="file" class="form-label">File</label>
                                    <input type="file" class="form-control @error('file') is-invalid @enderror" id="file" name="file" required>
                                    <small class="form-text text-muted">
                                        Formati supportati: PDF, DOC, DOCX, JPG, JPEG, PNG (max 10MB)
                                    </small>
                                    @error('file')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary">Carica Documento</button>
                                </div>
                            </form>
                        @elseif($documentRequest->submittedDocument)
                            <div class="mb-4">
                                <h6>Documento Caricato</h6>
                                <div class="card mb-3 bg-light">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h5 class="mb-1">{{ $documentRequest->submittedDocument->title }}</h5>
                                                <p class="mb-1 text-muted">{{ $documentRequest->submittedDocument->description }}</p>
                                                <small class="text-muted">Caricato il {{ $documentRequest->submittedDocument->created_at->format('d/m/Y H:i') }}</small>
                                            </div>
                                            <div>
                                                <a href="{{ Storage::url($documentRequest->submittedDocument->file_path) }}" class="btn btn-primary" target="_blank">
                                                    <i class="fas fa-download me-1"></i> Scarica
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @if($documentRequest->isSubmitted())
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i> Il tuo documento è in attesa di approvazione.
                                    </div>
                                @elseif($documentRequest->isApproved())
                                    <div class="alert alert-success">
                                        <i class="fas fa-check-circle me-2"></i> Il tuo documento è stato approvato.
                                    </div>
                                @elseif($documentRequest->isRejected())
                                    <div class="alert alert-danger">
                                        <i class="fas fa-times-circle me-2"></i> Il tuo documento è stato rifiutato.
                                        @if($documentRequest->notes)
                                            <p class="mb-0 mt-2"><strong>Motivo:</strong> {{ $documentRequest->notes }}</p>
                                        @endif
                                    </div>

                                    <div class="mt-4">
                                        <form action="{{ route('employee.document-requests.submit', $documentRequest) }}" method="POST" enctype="multipart/form-data">
                                            @csrf

                                            <h6>Carica un nuovo documento</h6>

                                            <div class="mb-3">
                                                <label for="title" class="form-label">Titolo Documento</label>
                                                <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $documentRequest->document_type) }}" required>
                                                @error('title')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="mb-3">
                                                <label for="description" class="form-label">Descrizione (opzionale)</label>
                                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="2">{{ old('description') }}</textarea>
                                                @error('description')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="mb-3">
                                                <label for="file" class="form-label">File</label>
                                                <input type="file" class="form-control @error('file') is-invalid @enderror" id="file" name="file" required>
                                                <small class="form-text text-muted">
                                                    Formati supportati: PDF, DOC, DOCX, JPG, JPEG, PNG (max 10MB)
                                                </small>
                                                @error('file')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="d-flex justify-content-end">
                                                <button type="submit" class="btn btn-primary">Carica Documento</button>
                                            </div>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>
