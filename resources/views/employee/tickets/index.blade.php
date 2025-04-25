<x-layout>
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>I miei Ticket</h2>
            <a href="{{ route('employee.tickets.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i> Nuovo Ticket
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                @if($tickets->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Titolo</th>
                                    <th>Categoria</th>
                                    <th>Stato</th>
                                    <th>Priorità</th>
                                    <th>Data creazione</th>
                                    <th>Ultima attività</th>
                                    <th>Azioni</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tickets as $ticket)
                                    <tr>
                                        <td>{{ $ticket->id }}</td>
                                        <td>{{ $ticket->title }}</td>
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
                                        <td>{{ $ticket->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            @if($ticket->replies->count() > 0)
                                                {{ $ticket->replies->sortByDesc('created_at')->first()->created_at->format('d/m/Y H:i') }}
                                            @else
                                                {{ $ticket->created_at->format('d/m/Y H:i') }}
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('employee.tickets.show', $ticket) }}" class="btn btn-sm btn-primary">
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
                        <p class="text-muted">Non hai ancora creato nessun ticket di supporto.</p>
                        <a href="{{ route('employee.tickets.create') }}" class="btn btn-primary mt-2">
                            <i class="fas fa-plus me-2"></i> Crea il tuo primo ticket
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layout>