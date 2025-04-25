<x-layout>
    <x-slot name="sidebar">
        @include('components.sidebar')
    </x-slot>

    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Checklist di {{ $user->name }}</h2>
            <a href="{{ route('admin.employees.show', $user) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i> Torna al profilo
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row mb-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-3">
                                <img src="{{ $user->profile_image ? asset('storage/' . $user->profile_image) : asset('images/default-avatar.png') }}"
                                     class="rounded-circle"
                                     alt="{{ $user->name }}"
                                     width="60">
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="mb-1">{{ $user->name }}</h5>
                                <p class="mb-0 text-muted">{{ $user->email }}</p>
                                <div class="mt-1">
                                    @foreach($user->roles as $role)
                                        <span class="badge bg-primary me-1">{{ ucfirst($role->name) }}</span>
                                    @endforeach
                                    <span class="badge bg-secondary">{{ $user->department->name ?? 'Nessun reparto' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Checklist assegnate</h5>
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#assignChecklistModal">
                            <i class="fas fa-plus me-1"></i> Assegna nuova checklist
                        </button>
                    </div>
                    <div class="card-body p-0">
                        @if(count($checklists) === 0)
                            <div class="text-center p-4">
                                <i class="fas fa-clipboard-list fs-1 text-muted mb-3"></i>
                                <p class="mb-1">Nessuna checklist assegnata</p>
                                <small class="text-muted">Assegna una checklist a questo utente utilizzando il pulsante "Assegna nuova checklist"</small>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Checklist</th>
                                            <th>Completamento</th>
                                            <th>Stato elementi</th>
                                            <th>Data assegnazione</th>
                                            <th>Azioni</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($checklists as $checklistId => $checklistData)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div>
                                                            <h6 class="mb-0">{{ $checklistData['checklist']->name }}</h6>
                                                            @if($checklistData['checklist']->description)
                                                                <small class="text-muted">{{ Str::limit($checklistData['checklist']->description, 50) }}</small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    @php
                                                        $percentage = $checklistData['total'] > 0
                                                            ? round(($checklistData['completed'] / $checklistData['total']) * 100)
                                                            : 0;
                                                    @endphp
                                                    <div class="d-flex align-items-center">
                                                        <div class="progress flex-grow-1" style="height: 8px;">
                                                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $percentage }}%;"
                                                                aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                                                        </div>
                                                        <span class="ms-2">{{ $percentage }}%</span>
                                                    </div>
                                                    <small class="text-muted">{{ $checklistData['completed'] }} di {{ $checklistData['total'] }} completati</small>
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        @if($checklistData['needs_review'] > 0)
                                                            <span class="badge bg-warning me-1">
                                                                {{ $checklistData['needs_review'] }} in attesa di approvazione
                                                            </span>
                                                        @endif

                                                        @if($checklistData['rejected'] > 0)
                                                            <span class="badge bg-danger me-1">
                                                                {{ $checklistData['rejected'] }} rifiutati
                                                            </span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    {{ $checklistData['items'][0]->pivot->created_at->format('d/m/Y') }}
                                                </td>
                                                <td>
                                                    <a href="#" class="btn btn-sm btn-primary me-1"
                                                       data-bs-toggle="modal"
                                                       data-bs-target="#checklistDetailsModal{{ $checklistId }}">
                                                        <i class="fas fa-eye me-1"></i> Dettagli
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal dettagli checklist -->
    @foreach($checklists as $checklistId => $checklistData)
        <div class="modal fade" id="checklistDetailsModal{{ $checklistId }}" tabindex="-1" aria-labelledby="checklistDetailsModalLabel{{ $checklistId }}" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="checklistDetailsModalLabel{{ $checklistId }}">
                            Dettagli Checklist: {{ $checklistData['checklist']->name }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <h6>Progresso: {{ $checklistData['completed'] }} di {{ $checklistData['total'] }} elementi completati</h6>
                        <div class="progress mb-4" style="height: 10px;">
                            @php
                                $percentage = $checklistData['total'] > 0
                                    ? round(($checklistData['completed'] / $checklistData['total']) * 100)
                                    : 0;
                            @endphp
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $percentage }}%;"
                                aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>

                        <ul class="list-group">
                            @foreach($checklistData['items'] as $item)
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1 {{ $item->pivot->is_completed ? 'text-muted' : '' }}">
                                                {{ $item->order }}. {{ $item->title }}
                                                @if($item->is_mandatory)
                                                    <span class="badge bg-danger">Obbligatorio</span>
                                                @endif
                                            </h6>
                                            @if($item->description)
                                                <p class="mb-1 small">{{ $item->description }}</p>
                                            @endif

                                            @if($item->pivot->file_path)
                                                <div class="mb-1">
                                                    <a href="{{ Storage::url($item->pivot->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-file me-1"></i> Visualizza file
                                                    </a>
                                                </div>
                                            @endif

                                            @if($item->pivot->notes)
                                                <div class="mb-1">
                                                    <strong>Note:</strong> {{ $item->pivot->notes }}
                                                </div>
                                            @endif
                                        </div>

                                        <div class="ms-3">
                                            @if($item->pivot->status === 'needs_review')
                                                <div class="d-flex flex-column text-end">
                                                    <span class="badge bg-warning mb-2">In attesa di approvazione</span>
                                                    <div class="d-flex">
                                                        <form action="{{ route('admin.users.checklist-items.approve', ['user' => $user, 'item' => $item]) }}" method="POST" class="d-inline me-1">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-success">
                                                                <i class="fas fa-check me-1"></i> Approva
                                                            </button>
                                                        </form>
                                                        <button class="btn btn-sm btn-danger"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#rejectModal{{ $item->id }}"
                                                                data-bs-dismiss="modal">
                                                            <i class="fas fa-times me-1"></i> Rifiuta
                                                        </button>
                                                    </div>
                                                </div>
                                            @elseif($item->pivot->status === 'rejected')
                                                <span class="badge bg-danger">Rifiutato</span>
                                            @elseif($item->pivot->status === 'completed')
                                                <span class="badge bg-success">Completato</span>
                                            @elseif($item->pivot->status === 'pending')
                                                <span class="badge bg-secondary">In attesa</span>
                                            @endif
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <!-- Modal rifiuto elementi -->
    @foreach($checklists as $checklistId => $checklistData)
        @foreach($checklistData['items'] as $item)
            <div class="modal fade" id="rejectModal{{ $item->id }}" tabindex="-1" aria-labelledby="rejectModalLabel{{ $item->id }}" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="rejectModalLabel{{ $item->id }}">Rifiuta elemento</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ route('admin.users.checklist-items.reject', ['user' => $user, 'item' => $item]) }}" method="POST">
                            @csrf
                            <div class="modal-body">
                                <p>Stai per rifiutare l'elemento: <strong>{{ $item->title }}</strong></p>
                                <p>Fornisci un motivo per aiutare l'utente a correggere il problema:</p>
                                <div class="mb-3">
                                    <label for="notes" class="form-label">Motivo del rifiuto</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="3" required></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-times me-1"></i> Rifiuta elemento
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    @endforeach

    <!-- Modal assegnazione checklist -->
    <div class="modal fade" id="assignChecklistModal" tabindex="-1" aria-labelledby="assignChecklistModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="assignChecklistModalLabel">Assegna Checklist</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="assignChecklistForm" method="POST">
                    @csrf
                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="checklist_id" class="form-label">Seleziona Checklist</label>
                            <select class="form-select" id="checklist_id" name="checklist_id" required>
                                <option value="">Seleziona una checklist...</option>
                                @foreach(\App\Models\Checklist::where('is_active', true)->orderBy('name')->get() as $checklist)
                                    <option value="{{ $checklist->id }}">{{ $checklist->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-check me-1"></i> Assegna
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('assignChecklistForm');
        const select = document.getElementById('checklist_id');

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const checklistId = select.value;

            if (checklistId) {
                this.action = "{{ url('admin/checklists') }}/" + checklistId + "/assign";
                this.submit();
            } else {
                alert('Seleziona una checklist prima di procedere.');
            }
        });
    });
</script>
@endpush
