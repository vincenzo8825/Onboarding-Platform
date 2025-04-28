<x-layout>
    <x-slot name="sidebar">
        @include('components.sidebar')
    </x-slot>
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Gestione Ticket</h2>
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
                <form action="{{ route('admin.tickets.index') }}" method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label for="status" class="form-label">Stato</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">Tutti</option>
                            <option value="open" {{ $status === 'open' ? 'selected' : '' }}>Aperti</option>
                            <option value="in_progress" {{ $status === 'in_progress' ? 'selected' : '' }}>In lavorazione</option>
                            <option value="resolved" {{ $status === 'resolved' ? 'selected' : '' }}>Risolti</option>
                            <option value="closed" {{ $status === 'closed' ? 'selected' : '' }}>Chiusi</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="priority" class="form-label">Priorità</label>
                        <select class="form-select" id="priority" name="priority">
                            <option value="">Tutte</option>
                            <option value="low" {{ $priority === 'low' ? 'selected' : '' }}>Bassa</option>
                            <option value="medium" {{ $priority === 'medium' ? 'selected' : '' }}>Media</option>
                            <option value="high" {{ $priority === 'high' ? 'selected' : '' }}>Alta</option>
                            <option value="urgent" {{ $priority === 'urgent' ? 'selected' : '' }}>Urgente</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="category" class="form-label">Categoria</label>
                        <select class="form-select" id="category" name="category">
                            <option value="">Tutte</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}" {{ $category === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">Filtra</button>
                        <a href="{{ route('admin.tickets.index') }}" class="btn btn-secondary">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                @if($tickets->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Titolo</th>
                                    <th>Utente</th>
                                    <th>Categoria</th>
                                    <th>Stato</th>
                                    <th>Priorità</th>
                                    <th>Assegnato a</th>
                                    <th>Data creazione</th>
                                    <th>Ultima attività</th>
                                    <th>Azioni</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tickets as $ticket)
                                    <tr>
                                        <td>{{ $ticket->id }}</td>
                                        <td>{{ Str::limit($ticket->title, 30) }}</td>
                                        <td>{{ $ticket->creator->name }}</td>
                                        <td>{{ $ticket->category }}</td>
                                        <td>
                                            @if($ticket->status === 'open')
                                                <span class="badge bg-primary">Aperto</span>
                                            @elseif($ticket->status === 'in_progress')
                                                <span class="badge bg-info">In lavorazione</span>
                                            @elseif($ticket->status === 'resolved')
                                                <span class="badge bg-success">Risolto</span>
                                            @elseif($ticket->status === 'closed')
                                                <span class="badge bg-secondary">Chiuso</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($ticket->priority === 'low')
                                                <span class="badge bg-success">Bassa</span>
                                            @elseif($ticket->priority === 'medium')
                                                <span class="badge bg-info">Media</span>
                                            @elseif($ticket->priority === 'high')
                                                <span class="badge bg-warning">Alta</span>
                                            @elseif($ticket->priority === 'urgent')
                                                <span class="badge bg-danger">Urgente</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($ticket->assignee)
                                                {{ $ticket->assignee->name }}
                                            @else
                                                <span class="text-muted">Non assegnato</span>
                                            @endif
                                        </td>
                                        <td>{{ $ticket->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            @if($ticket->replies->count() > 0)
                                                {{ $ticket->replies->sortByDesc('created_at')->first()->created_at->format('d/m/Y H:i') }}
                                            @else
                                                {{ $ticket->created_at->format('d/m/Y H:i') }}
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.tickets.show', $ticket) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        {{ $tickets->links() }}
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-ticket-alt fa-4x text-muted mb-3"></i>
                        <h4>Nessun ticket trovato</h4>
                        <p class="text-muted">Non ci sono ticket che corrispondono ai criteri di ricerca.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layout>