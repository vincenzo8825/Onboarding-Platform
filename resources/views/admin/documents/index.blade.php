<x-layout>
    <x-slot name="sidebar">
        @include('components.sidebar')
    </x-slot>
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Gestione Documenti</h2>
            <a href="{{ route('admin.documents.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i> Nuovo Documento
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Filtri</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.documents.index') }}" method="GET" class="row g-3">
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
                        <a href="{{ route('admin.documents.index') }}" class="btn btn-secondary">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                @if($documents->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Titolo</th>
                                    <th>Categoria</th>
                                    <th>Caricato da</th>
                                    <th>Data caricamento</th>
                                    <th>Richiesto</th>
                                    <th>Visibilità</th>
                                    <th>Azioni</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($documents as $document)
                                    <tr>
                                        <td>{{ $document->id }}</td>
                                        <td>{{ Str::limit($document->title, 30) }}</td>
                                        <td>{{ $document->category }}</td>
                                        <td>{{ $document->uploader->name }}</td>
                                        <td>{{ $document->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            @if($document->is_required)
                                                <span class="badge bg-success">Sì</span>
                                            @else
                                                <span class="badge bg-secondary">No</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($document->visibility === 'all')
                                                <span class="badge bg-primary">Tutti</span>
                                            @elseif($document->visibility === 'admin')
                                                <span class="badge bg-warning">Solo Admin</span>
                                            @elseif($document->visibility === 'specific_departments')
                                                <span class="badge bg-info">Dipartimenti specifici</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('admin.documents.show', $document) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.documents.edit', $document) }}" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="{{ Storage::url($document->file_path) }}" class="btn btn-sm btn-info" target="_blank">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $document->id }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>

                                            <!-- Modal di conferma eliminazione -->
                                            <div class="modal fade" id="deleteModal{{ $document->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $document->id }}" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="deleteModalLabel{{ $document->id }}">Conferma eliminazione</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            Sei sicuro di voler eliminare il documento "{{ $document->title }}"?
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                                                            <form action="{{ route('admin.documents.destroy', $document) }}" method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-danger">Elimina</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        {{ $documents->links() }}
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-file-alt fa-4x text-muted mb-3"></i>
                        <h4>Nessun documento trovato</h4>
                        <p class="text-muted">Non ci sono documenti che corrispondono ai criteri di ricerca.</p>
                        <a href="{{ route('admin.documents.create') }}" class="btn btn-primary mt-2">
                            <i class="fas fa-plus me-2"></i> Carica il primo documento
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layout>