<x-layout>
    <x-slot name="sidebar">
        @include('components.sidebar')
    </x-slot>
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Gestione Programmi</h2>
            <a href="{{ route('admin.programs.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i> Nuovo Programma
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                @if($programs->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Descrizione</th>
                                    <th>Data Inizio</th>
                                    <th>Data Fine</th>
                                    <th>Stato</th>
                                    <th>Partecipanti</th>
                                    <th>Azioni</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($programs as $program)
                                    <tr>
                                        <td>{{ $program->name }}</td>
                                        <td>{{ Str::limit($program->description, 50) }}</td>
                                        <td>{{ $program->start_date->format('d/m/Y') }}</td>
                                        <td>{{ $program->end_date ? $program->end_date->format('d/m/Y') : 'Nessuna' }}</td>
                                        <td>
                                            @if($program->isActive())
                                                <span class="badge bg-success">Attivo</span>
                                            @else
                                                <span class="badge bg-secondary">Inattivo</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $program->users_count }} utenti</span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.programs.show', $program) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.programs.edit', $program) }}" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.programs.destroy', $program) }}" method="POST" class="d-inline" onsubmit="return confirm('Sei sicuro di voler eliminare questo programma?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        {{ $programs->links() }}
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-book fa-4x text-muted mb-3"></i>
                        <h4>Nessun programma trovato</h4>
                        <p class="text-muted">Inizia creando il tuo primo programma.</p>
                        <a href="{{ route('admin.programs.create') }}" class="btn btn-primary mt-2">
                            <i class="fas fa-plus me-2"></i> Crea Programma
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layout>
