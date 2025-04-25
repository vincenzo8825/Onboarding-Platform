<x-layout>
    <x-slot name="sidebar">
        @include('components.sidebar')
    </x-slot>

    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Documenti e Risorse</h2>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadDocumentModal">
                <i class="fas fa-upload me-2"></i> Carica Documento
            </button>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Filtri</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('employee.documents.index') }}" method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label for="category" class="form-label">Categoria</label>
                        <select class="form-select" id="category" name="category">
                            <option value="">Tutte</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}" {{ $category === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">Filtra</button>
                        <a href="{{ route('employee.documents.index') }}" class="btn btn-secondary">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Documenti caricati dal dipendente -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">I miei documenti caricati</h5>
            </div>
            <div class="card-body">
                @if($myDocuments->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Categoria</th>
                                    <th>Data Caricamento</th>
                                    <th>Stato</th>
                                    <th>Azioni</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($myDocuments as $doc)
                                    <tr>
                                        <td>{{ $doc->title }}</td>
                                        <td><span class="badge bg-primary">{{ $doc->category }}</span></td>
                                        <td>{{ $doc->created_at->format('d/m/Y') }}</td>
                                        <td>
                                            @if($doc->status === 'approved')
                                                <span class="badge bg-success">Approvato</span>
                                            @elseif($doc->status === 'rejected')
                                                <span class="badge bg-danger">Respinto</span>
                                            @else
                                                <span class="badge bg-warning">In Attesa</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('employee.documents.show', $doc) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('employee.documents.download', $doc) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-download"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-3">
                        <i class="fas fa-file-upload fa-3x text-muted mb-3"></i>
                        <p>Non hai ancora caricato nessun documento.</p>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadDocumentModal">
                            <i class="fas fa-upload me-2"></i> Carica il tuo primo documento
                        </button>
                    </div>
                @endif
            </div>
        </div>

        <!-- Documenti richiesti da caricare -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Documenti richiesti da caricare</h5>
            </div>
            <div class="card-body">
                @php
                    $pendingRequests = \App\Models\DocumentRequest::where('employee_id', Auth::id())
                        ->where('status', 'pending')
                        ->latest()
                        ->get();
                @endphp

                @if($pendingRequests->count() > 0)
                    <div class="list-group">
                        @foreach($pendingRequests as $request)
                            <a href="{{ route('employee.document-requests.show', $request) }}" class="list-group-item list-group-item-action">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">{{ $request->document_type }}</h6>
                                        <p class="mb-1 text-muted small">{{ $request->description ?: 'Nessuna descrizione' }}</p>

                                        @if($request->due_date)
                                            <small class="text-{{ \Carbon\Carbon::parse($request->due_date)->isPast() ? 'danger' : 'muted' }}">
                                                Scadenza: {{ \Carbon\Carbon::parse($request->due_date)->format('d/m/Y') }}
                                            </small>
                                        @endif
                                    </div>
                                    <div>
                                        <span class="badge bg-warning rounded-pill">Da caricare</span>
                                        <br>
                                        <small class="text-muted">{{ $request->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                        <p>Non ci sono richieste di documenti in attesa.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Documenti disponibili -->
        <h4 class="mb-3">Documenti disponibili</h4>
        @if($documents->count() > 0)
            <div class="row">
                @foreach($documents as $document)
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-3">
                                    <h5 class="card-title">{{ $document->title }}</h5>
                                    @if(in_array($document->id, $viewedDocumentIds))
                                        <span class="badge bg-success">Letto</span>
                                    @elseif($document->is_required)
                                        <span class="badge bg-warning">Da leggere</span>
                                    @endif
                                </div>

                                <p class="card-text text-muted mb-3">
                                    {{ Str::limit($document->description, 100) ?: 'Nessuna descrizione disponibile' }}
                                </p>

                                <div class="mb-3">
                                    <span class="badge bg-primary">{{ $document->category }}</span>
                                    @if($document->is_required)
                                        <span class="badge bg-danger ms-1">Richiesto</span>
                                    @endif
                                </div>

                                <div class="text-muted small mb-3">
                                    Caricato il {{ $document->created_at->format('d/m/Y') }}
                                </div>
                            </div>
                            <div class="card-footer bg-transparent">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('employee.documents.show', $document) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye me-1"></i> Visualizza
                                    </a>
                                    <a href="{{ route('employee.documents.download', $document) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-download me-1"></i> Scarica
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $documents->links() }}
            </div>
        @else
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-file-alt fa-4x text-muted mb-3"></i>
                    <h4>Nessun documento trovato</h4>
                    <p class="text-muted">Non ci sono documenti disponibili nella categoria selezionata.</p>
                </div>
            </div>
        @endif
    </div>

    <!-- Modal Caricamento Documento -->
    <div class="modal fade" id="uploadDocumentModal" tabindex="-1" aria-labelledby="uploadDocumentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadDocumentModalLabel">Carica un nuovo documento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('employee.documents.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="title" class="form-label">Titolo documento <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Descrizione</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="category" class="form-label">Categoria <span class="text-danger">*</span></label>
                            <select class="form-select" id="upload_category" name="category" required>
                                <option value="">Seleziona categoria</option>
                                <option value="Documento d'identità">Documento d'identità</option>
                                <option value="Formazione">Formazione</option>
                                <option value="Contratto">Contratto</option>
                                <option value="Certificazione">Certificazione</option>
                                <option value="Altro">Altro</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="file" class="form-label">File <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" id="file" name="file" required>
                            <small class="text-muted">Formati supportati: PDF, DOC, DOCX, JPG, PNG (max 10MB)</small>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="needs_approval" name="needs_approval" checked>
                                <label class="form-check-label" for="needs_approval">
                                    Richiede approvazione dell'amministratore
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                        <button type="submit" class="btn btn-primary">Carica documento</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout>
