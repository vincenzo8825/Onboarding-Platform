<x-layout>
    <x-slot name="sidebar">
        @include('components.sidebar')
    </x-slot>

    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Richieste di Documenti</h2>
            <a href="{{ route('employee.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i> Dashboard
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
                <h5 class="mb-0">Documenti da Caricare</h5>
            </div>
            <div class="card-body">
                @if($pendingRequests->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Documento</th>
                                    <th>Richiesto il</th>
                                    <th>Richiesto da</th>
                                    <th>Scadenza</th>
                                    <th>Stato</th>
                                    <th>Azioni</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingRequests as $request)
                                    <tr>
                                        <td>{{ $request->document_type }}</td>
                                        <td>{{ $request->created_at->format('d/m/Y') }}</td>
                                        <td>{{ $request->admin->name }}</td>
                                        <td>
                                            @if($request->due_date)
                                                <span class="text-{{ \Carbon\Carbon::parse($request->due_date)->isPast() ? 'danger' : 'dark' }}">
                                                    {{ \Carbon\Carbon::parse($request->due_date)->format('d/m/Y') }}
                                                    @if(\Carbon\Carbon::parse($request->due_date)->isPast())
                                                        <span class="badge bg-danger ms-1">Scaduto</span>
                                                    @endif
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-warning">Da caricare</span>
                                        </td>
                                        <td>
                                            <a href="{{ route('employee.document-requests.show', $request) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-upload me-1"></i> Carica
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                        <p>Non hai richieste di documenti in attesa.</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Richieste Completate</h5>
            </div>
            <div class="card-body">
                @if($completedRequests->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Documento</th>
                                    <th>Caricato il</th>
                                    <th>Richiesto da</th>
                                    <th>Stato</th>
                                    <th>Azioni</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($completedRequests as $request)
                                    <tr>
                                        <td>{{ $request->document_type }}</td>
                                        <td>
                                            @if($request->submittedDocument)
                                                {{ $request->submittedDocument->created_at->format('d/m/Y') }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>{{ $request->admin->name }}</td>
                                        <td>
                                            @if($request->isSubmitted())
                                                <span class="badge bg-info">In attesa di approvazione</span>
                                            @elseif($request->isApproved())
                                                <span class="badge bg-success">Approvato</span>
                                            @elseif($request->isRejected())
                                                <span class="badge bg-danger">Rifiutato</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('employee.document-requests.show', $request) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye me-1"></i> Visualizza
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                        <p>Non hai richieste di documenti completate.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layout>
