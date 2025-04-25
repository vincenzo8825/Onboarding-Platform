<x-layout>
    <x-slot name="sidebar">
        @include('components.sidebar')
    </x-slot>

    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Dettaglio Documento</h2>
            <a href="{{ route('employee.documents.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> Torna all'elenco
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{ $document->title }}</h5>
                <span class="badge {{
                    $document->status === 'approved' ? 'bg-success' :
                    ($document->status === 'rejected' ? 'bg-danger' : 'bg-warning')
                }}">
                    {{
                        $document->status === 'approved' ? 'Approvato' :
                        ($document->status === 'rejected' ? 'Rifiutato' : 'In attesa')
                    }}
                </span>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-8">
                        @if($document->description)
                            <h6 class="text-muted mb-3">Descrizione</h6>
                            <p>{{ $document->description }}</p>
                        @endif

                        <div class="row mt-4">
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2">Categoria</h6>
                                <p>{{ $document->category }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2">Caricato il</h6>
                                <p>{{ $document->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>

                        @if($document->status === 'approved' && $document->approved_at)
                            <div class="alert alert-success mt-3">
                                <i class="fas fa-check-circle me-2"></i>
                                Questo documento è stato approvato il {{ \Carbon\Carbon::parse($document->approved_at)->format('d/m/Y') }}.
                            </div>
                        @elseif($document->status === 'rejected')
                            <div class="alert alert-danger mt-3">
                                <i class="fas fa-times-circle me-2"></i>
                                Questo documento è stato rifiutato.
                                @if(isset($document->notes) && $document->notes)
                                    <p class="mt-2 mb-0"><strong>Motivo:</strong> {{ $document->notes }}</p>
                                @endif
                            </div>
                        @endif
                    </div>
                    <div class="col-md-4">
                        <div class="card h-100">
                            <div class="card-body text-center d-flex flex-column align-items-center justify-content-center">
                                <div class="mb-3">
                                    @if(strtolower($document->file_type) === 'application/pdf')
                                        <i class="far fa-file-pdf fa-4x text-danger"></i>
                                    @elseif(strpos(strtolower($document->file_type), 'image/') !== false)
                                        <i class="far fa-file-image fa-4x text-primary"></i>
                                    @elseif(strpos(strtolower($document->file_type), 'word') !== false)
                                        <i class="far fa-file-word fa-4x text-primary"></i>
                                    @elseif(strpos(strtolower($document->file_type), 'excel') !== false || strpos(strtolower($document->file_type), 'spreadsheet') !== false)
                                        <i class="far fa-file-excel fa-4x text-success"></i>
                                    @else
                                        <i class="far fa-file-alt fa-4x text-muted"></i>
                                    @endif
                                </div>
                                <h5 class="font-weight-bold">{{ pathinfo($document->file_path, PATHINFO_FILENAME) }}</h5>
                                <p class="text-muted">{{ strtoupper(pathinfo($document->file_path, PATHINFO_EXTENSION)) }} - {{ number_format($document->file_size / 1024, 2) }} KB</p>
                                <a href="{{ route('employee.documents.download', $document) }}" class="btn btn-primary w-100">
                                    <i class="fas fa-download me-2"></i> Scarica
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-light">
                <div class="d-flex justify-content-end">
                    @if($document->status !== 'approved')
                        <a href="{{ route('employee.documents.edit', $document) }}" class="btn btn-warning me-2">
                            <i class="fas fa-edit me-2"></i> Modifica
                        </a>
                    @endif
                    <form action="{{ route('employee.documents.destroy', $document) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Sei sicuro di voler eliminare questo documento?')">
                            <i class="fas fa-trash me-2"></i> Elimina
                        </button>
                    </form>
                </div>
            </div>
        </div>

        @if($document->status === 'approved' && strpos(strtolower($document->file_type), 'pdf') !== false)
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Anteprima Documento</h5>
                </div>
                <div class="card-body">
                    <div class="ratio ratio-16x9">
                        <iframe src="{{ Storage::url($document->file_path) }}" frameborder="0"></iframe>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-layout>
