<x-layout>
    <x-slot name="sidebar">
        @include('components.sidebar')
    </x-slot>

    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Dettagli Dipendente</h2>
            <div>
                <a href="{{ route('admin.employees.edit', $employee) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Modifica
                </a>
                <a href="{{ route('admin.employees.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Torna all'elenco
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card shadow">
                    <div class="card-body text-center">
                        @if($employee->photo)
                            <img src="{{ asset('storage/' . $employee->photo) }}" alt="{{ $employee->name }}" class="rounded-circle img-thumbnail mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                        @else
                            <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 150px; height: 150px;">
                                <span class="text-white" style="font-size: 3rem;">{{ substr($employee->name, 0, 1) }}</span>
                            </div>
                        @endif
                        <h4 class="card-title">{{ $employee->name }}</h4>
                        <p class="text-muted">
                            @foreach($employee->roles as $role)
                                <span class="badge bg-primary">{{ ucfirst($role->name) }}</span>
                            @endforeach
                        </p>
                        <div class="text-start mt-4">
                            <p><strong>Email:</strong> {{ $employee->email }}</p>
                            <p><strong>Ruolo:</strong> {{ $employee->position ?? 'Non specificato' }}</p>
                            <p><strong>Dipartimento:</strong> {{ $employee->department->name ?? 'Non assegnato' }}</p>
                            <p><strong>Data Assunzione:</strong> {{ $employee->hire_date ? \Carbon\Carbon::parse($employee->hire_date)->format('d/m/Y') : 'Non specificata' }}</p>
                            <p><strong>Stato:</strong>
                                <span class="badge {{ $employee->status === 'active' ? 'bg-success' : ($employee->status === 'pending' ? 'bg-warning' : 'bg-danger') }}">
                                    {{ $employee->status === 'active' ? 'Attivo' : ($employee->status === 'pending' ? 'In attesa' : 'Bloccato') }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-8 mb-4">
                <div class="card shadow mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Stato Onboarding</h5>
                    </div>
                    <div class="card-body">
                        <div class="progress mb-3" style="height: 25px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $overallPercentage }}%"
                                 aria-valuenow="{{ $overallPercentage }}" aria-valuemin="0" aria-valuemax="100">
                                {{ $overallPercentage }}%
                            </div>
                        </div>

                        <div class="row text-center mt-4">
                            <div class="col-md-6 mb-3">
                                <div class="p-3 border rounded">
                                    <h3 class="text-primary mb-0">{{ $coursesCompleted }} / {{ $totalAssignedCourses }}</h3>
                                    <p class="mb-0 text-muted">Corsi Completati</p>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="p-3 border rounded">
                                    <h3 class="text-info mb-0">{{ $checklistsCompleted }} / {{ $totalChecklistItems }}</h3>
                                    <p class="mb-0 text-muted">Attività Completate</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" id="employeeTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="courses-tab" data-bs-toggle="tab" data-bs-target="#courses" type="button" role="tab" aria-controls="courses" aria-selected="true">Corsi</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="checklists-tab" data-bs-toggle="tab" data-bs-target="#checklists" type="button" role="tab" aria-controls="checklists" aria-selected="false">Attività</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="programs-tab" data-bs-toggle="tab" data-bs-target="#programs" type="button" role="tab" aria-controls="programs" aria-selected="false">Programmi</button>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="employeeTabContent">
                            <div class="tab-pane fade show active" id="courses" role="tabpanel" aria-labelledby="courses-tab">
                                @if($employee->courses->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Titolo</th>
                                                    <th>Assegnato il</th>
                                                    <th>Scadenza</th>
                                                    <th>Stato</th>
                                                    <th>Completato il</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($employee->courses as $course)
                                                    <tr>
                                                        <td>{{ $course->title }}</td>
                                                        <td>{{ $course->pivot->assigned_at ? \Carbon\Carbon::parse($course->pivot->assigned_at)->format('d/m/Y') : 'N/A' }}</td>
                                                        <td>{{ $course->pivot->due_date ? \Carbon\Carbon::parse($course->pivot->due_date)->format('d/m/Y') : 'N/A' }}</td>
                                                        <td>
                                                            <span class="badge {{ $course->pivot->status === 'completed' ? 'bg-success' : ($course->pivot->status === 'in_progress' ? 'bg-warning' : 'bg-secondary') }}">
                                                                {{ $course->pivot->status === 'completed' ? 'Completato' : ($course->pivot->status === 'in_progress' ? 'In corso' : 'Non iniziato') }}
                                                            </span>
                                                        </td>
                                                        <td>{{ $course->pivot->completed_at ? \Carbon\Carbon::parse($course->pivot->completed_at)->format('d/m/Y') : 'N/A' }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <i class="fas fa-graduation-cap fa-3x text-muted mb-3"></i>
                                        <h6>Nessun corso assegnato</h6>
                                        <p class="text-muted mb-0">Questo dipendente non ha corsi assegnati</p>
                                    </div>
                                @endif
                            </div>
                            <div class="tab-pane fade" id="checklists" role="tabpanel" aria-labelledby="checklists-tab">
                                @if($employee->checklistItems->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Attività</th>
                                                    <th>Checklist</th>
                                                    <th>Stato</th>
                                                    <th>Completata il</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($employee->checklistItems as $item)
                                                    <tr>
                                                        <td>{{ $item->text }}</td>
                                                        <td>{{ $item->checklist->name }}</td>
                                                        <td>
                                                            <span class="badge {{ $item->pivot->is_completed ? 'bg-success' : 'bg-secondary' }}">
                                                                {{ $item->pivot->is_completed ? 'Completata' : 'In sospeso' }}
                                                            </span>
                                                        </td>
                                                        <td>{{ $item->pivot->completed_at ? \Carbon\Carbon::parse($item->pivot->completed_at)->format('d/m/Y') : 'N/A' }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <i class="fas fa-tasks fa-3x text-muted mb-3"></i>
                                        <h6>Nessuna attività assegnata</h6>
                                        <p class="text-muted mb-0">Questo dipendente non ha attività assegnate</p>
                                    </div>
                                @endif
                            </div>
                            <div class="tab-pane fade" id="programs" role="tabpanel" aria-labelledby="programs-tab">
                                @if($employee->programs->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Nome</th>
                                                    <th>Descrizione</th>
                                                    <th>Data inizio</th>
                                                    <th>Data fine</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($employee->programs as $program)
                                                    <tr>
                                                        <td>{{ $program->name }}</td>
                                                        <td>{{ Str::limit($program->description, 50) }}</td>
                                                        <td>{{ \Carbon\Carbon::parse($program->start_date)->format('d/m/Y') }}</td>
                                                        <td>{{ \Carbon\Carbon::parse($program->end_date)->format('d/m/Y') }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <i class="fas fa-project-diagram fa-3x text-muted mb-3"></i>
                                        <h6>Nessun programma assegnato</h6>
                                        <p class="text-muted mb-0">Questo dipendente non è assegnato a nessun programma</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 mb-4">
                <div class="card shadow">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Azioni Dipendente</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('admin.courses.index') }}?assign={{ $employee->id }}" class="btn btn-primary">
                                <i class="fas fa-graduation-cap me-1"></i> Assegna Corsi
                            </a>
                            <a href="{{ route('admin.checklists.index') }}?assign={{ $employee->id }}" class="btn btn-info">
                                <i class="fas fa-tasks me-1"></i> Assegna Checklist
                            </a>
                            <a href="{{ route('admin.programs.index') }}?assign={{ $employee->id }}" class="btn btn-success">
                                <i class="fas fa-project-diagram me-1"></i> Assegna a Programma
                            </a>
                            <form action="{{ route('admin.employees.destroy', $employee) }}" method="POST" onsubmit="return confirm('Sei sicuro di voler eliminare questo dipendente? Questa azione non può essere annullata.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-trash me-1"></i> Elimina Dipendente
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Documenti</h5>
                <a href="{{ route('admin.employees.request-document', $employee) }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i> Richiedi Documento
                </a>
            </div>
            <div class="card-body">
                <h6 class="mb-3">Documenti Richiesti</h6>

                @php
                    $documentRequests = \App\Models\DocumentRequest::where('employee_id', $employee->id)
                        ->latest()
                        ->get();
                @endphp

                @if($documentRequests->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Documento</th>
                                    <th>Richiesto il</th>
                                    <th>Scadenza</th>
                                    <th>Stato</th>
                                    <th>Azioni</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($documentRequests as $request)
                                    <tr>
                                        <td>{{ $request->document_type }}</td>
                                        <td>{{ $request->created_at->format('d/m/Y') }}</td>
                                        <td>
                                            @if($request->due_date)
                                                {{ \Carbon\Carbon::parse($request->due_date)->format('d/m/Y') }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($request->isPending())
                                                <span class="badge bg-warning">In attesa</span>
                                            @elseif($request->isSubmitted())
                                                <span class="badge bg-info">Caricato</span>
                                            @elseif($request->isApproved())
                                                <span class="badge bg-success">Approvato</span>
                                            @elseif($request->isRejected())
                                                <span class="badge bg-danger">Rifiutato</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.document-requests.show', $request) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted">Nessun documento richiesto.</p>
                @endif

                <h6 class="mt-4 mb-3">Documenti Caricati</h6>

                @php
                    $documents = \App\Models\Document::where('uploaded_by', $employee->id)
                        ->latest()
                        ->get();
                @endphp

                @if($documents->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Titolo</th>
                                    <th>Categoria</th>
                                    <th>Caricato il</th>
                                    <th>Stato</th>
                                    <th>Azioni</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($documents as $document)
                                    <tr>
                                        <td>{{ $document->title }}</td>
                                        <td>{{ $document->category }}</td>
                                        <td>{{ $document->created_at->format('d/m/Y') }}</td>
                                        <td>
                                            @if($document->isApproved())
                                                <span class="badge bg-success">Approvato</span>
                                            @elseif($document->isPending())
                                                <span class="badge bg-warning">In attesa</span>
                                            @elseif($document->isRejected())
                                                <span class="badge bg-danger">Rifiutato</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('admin.documents.show', $document) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ Storage::url($document->file_path) }}" class="btn btn-sm btn-primary" target="_blank">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted">Nessun documento caricato.</p>
                @endif
            </div>
        </div>
    </div>
</x-layout>
