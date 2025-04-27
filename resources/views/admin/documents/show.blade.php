<x-layout>
    <x-slot name="sidebar">
        @include('components.sidebar')
    </x-slot>
    @php
        $viewCount = $viewCount ?? 0;
        $recentViews = $recentViews ?? collect();
    @endphp
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Dettagli Documento</h2>
            <div>
                <a href="{{ route('admin.documents.edit', $document) }}" class="btn btn-warning me-2">
                    <i class="fas fa-edit me-2"></i> Modifica
                </a>
                <a href="{{ route('admin.documents.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i> Torna all'elenco
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Informazioni Documento</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <h3>{{ $document->title }}</h3>
                            <span class="badge bg-primary">{{ $document->category }}</span>
                            @if($document->is_required)
                                <span class="badge bg-success ms-2">Richiesto</span>
                            @endif
                        </div>

                        <div class="mb-3">
                            <strong>Descrizione:</strong>
                            <p class="mt-2">{{ $document->description ?: 'Nessuna descrizione disponibile' }}</p>
                        </div>

                        <div class="mb-3">
                            <strong>Visibilità:</strong>
                            @if($document->visibility === 'all')
                                <span class="badge bg-primary">Tutti</span>
                            @elseif($document->visibility === 'admin')
                                <span class="badge bg-warning">Solo Admin</span>
                            @elseif($document->visibility === 'specific_departments')
                                <span class="badge bg-info">Dipartimenti specifici</span>
                            @endif
                        </div>

                        <div class="mb-3">
                            <strong>Caricato da:</strong>
                            <span>{{ $document->uploader->name }}</span>
                        </div>

                        <div class="mb-3">
                            <strong>Data caricamento:</strong>
                            <span>{{ $document->created_at->format('d/m/Y H:i') }}</span>
                        </div>

                        @if($document->created_at != $document->updated_at)
                            <div class="mb-3">
                                <strong>Ultima modifica:</strong>
                                <span>{{ $document->updated_at->format('d/m/Y H:i') }}</span>
                            </div>
                        @endif

                        <div class="mt-4">
                            <a href="{{ Storage::url($document->file_path) }}" class="btn btn-primary" target="_blank">
                                <i class="fas fa-download me-2"></i> Scarica Documento
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Statistiche Visualizzazioni</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3 text-center">
                            <h3 class="mb-0">{{ $viewCount }}</h3>
                            <p class="text-muted">Visualizzazioni totali</p>
                        </div>

                        <hr>

                        <h6 class="mb-3">Visualizzazioni recenti</h6>

                        @if($recentViews->count() > 0)
                            <ul class="list-group">
                                @foreach($recentViews as $view)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $view->name }}</strong>
                                            <small class="d-block text-muted">{{ $view->email }}</small>
                                        </div>
                                        <small class="text-muted">{{ $view->pivot->viewed_at->format('d/m/Y H:i') }}</small>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-muted">Nessuna visualizzazione registrata</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>