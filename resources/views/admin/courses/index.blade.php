<x-layout>
    <x-slot name="sidebar">
        @include('components.sidebar')
    </x-slot>

    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Gestione Corsi Formativi</h2>
            <a href="{{ route('admin.courses.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Nuovo Corso
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
                <form action="{{ route('admin.courses.index') }}" method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label for="category" class="form-label">Categoria</label>
                        <select class="form-select" id="category" name="category">
                            <option value="">Tutte le categorie</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">Filtra</button>
                        <a href="{{ route('admin.courses.index') }}" class="btn btn-secondary">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                @if($courses->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Titolo</th>
                                    <th>Categoria</th>
                                    <th>Tipo</th>
                                    <th>Durata</th>
                                    <th>Quiz</th>
                                    <th>Assegnato a</th>
                                    <th>Creato il</th>
                                    <th>Azioni</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($courses as $course)
                                    <tr>
                                        <td>{{ $course->id }}</td>
                                        <td>{{ Str::limit($course->title, 30) }}</td>
                                        <td>{{ $course->category }}</td>
                                        <td>
                                            @if($course->content_type === 'pdf')
                                                <span class="badge bg-danger">PDF</span>
                                            @elseif($course->content_type === 'video')
                                                <span class="badge bg-primary">Video</span>
                                            @elseif($course->content_type === 'link')
                                                <span class="badge bg-info">Link</span>
                                            @else
                                                <span class="badge bg-secondary">Testo</span>
                                            @endif
                                        </td>
                                        <td>{{ $course->duration_minutes ? $course->duration_minutes . ' min' : 'N/A' }}</td>
                                        <td>
                                            @if($course->has_quiz)
                                                <span class="badge bg-success">Sì</span>
                                            @else
                                                <span class="badge bg-secondary">No</span>
                                            @endif
                                        </td>
                                        <td>{{ $course->users()->count() }}</td>
                                        <td>{{ $course->created_at->format('d/m/Y') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.courses.show', $course) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.courses.edit', $course) }}" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @if(!$course->has_quiz)
                                                    <a href="{{ route('admin.courses.quiz.create', $course) }}" class="btn btn-sm btn-info">
                                                        <i class="fas fa-question-circle"></i>
                                                    </a>
                                                @endif
                                                <form action="{{ route('admin.courses.destroy', $course) }}" method="POST" class="d-inline" onsubmit="return confirm('Sei sicuro di voler eliminare questo corso?');">
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
                        {{ $courses->links() }}
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-book fa-4x text-muted mb-3"></i>
                        <h4>Nessun corso trovato</h4>
                        <p class="text-muted">Non ci sono corsi che corrispondono ai criteri di ricerca.</p>
                        <a href="{{ route('admin.courses.create') }}" class="btn btn-primary mt-2">
                            <i class="fas fa-plus me-1"></i> Crea il primo corso
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layout>
