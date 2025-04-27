<x-layout>
    <x-slot name="sidebar">
        @include('components.sidebar')
    </x-slot>

    <div class="container-fluid py-4">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800 fw-bold">Utenti in attesa di approvazione</h1>
            <a href="{{ route('admin.dashboard') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50 me-1"></i> Torna alla Dashboard
            </a>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center bg-light">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-user-clock me-2"></i> Nuovi utenti registrati
                </h6>
                <a href="{{ route('admin.employees.index') }}" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-users me-1"></i> Lista dipendenti
                </a>
            </div>
            <div class="card-body">
                @if(count($pendingUsers) > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">#</th>
                                <th width="20%">Nome</th>
                                <th width="20%">Email</th>
                                <th width="15%">Ruolo</th>
                                <th width="15%">Dipartimento</th>
                                <th width="10%">Data registrazione</th>
                                <th width="15%">Azioni</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingUsers as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>
                                    @if($user->photo)
                                        <img src="{{ asset('storage/' . $user->photo) }}" alt="{{ $user->name }}" class="img-profile rounded-circle me-2" style="width: 32px; height: 32px;">
                                    @else
                                        <div class="avatar-circle me-2 d-inline-flex align-items-center justify-content-center bg-primary text-white rounded-circle" style="width: 32px; height: 32px;">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    {{ $user->name }}
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @foreach($user->roles as $role)
                                        <span class="badge {{ $role->name == 'admin' ? 'bg-danger' : ($role->name == 'employee' ? 'bg-primary' : 'bg-info') }} rounded-pill">
                                            {{ ucfirst($role->name) }}
                                        </span>
                                    @endforeach
                                </td>
                                <td>{{ $user->department ? $user->department->name : 'Non assegnato' }}</td>
                                <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <form action="{{ route('admin.employees.approve', $user) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm" data-bs-toggle="tooltip" title="Approva utente">
                                                <i class="fas fa-check-circle"></i>
                                            </button>
                                        </form>

                                        <!-- Reject button with modal -->
                                        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $user->id }}" title="Rifiuta utente">
                                            <i class="fas fa-times-circle"></i>
                                        </button>

                                        <!-- User details button -->
                                        <a href="{{ route('admin.employees.show', $user) }}" class="btn btn-info btn-sm" title="Visualizza dettagli">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        <!-- Edit button -->
                                        <a href="{{ route('admin.employees.edit', $user) }}" class="btn btn-primary btn-sm" title="Modifica utente">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>

                            <!-- Reject Modal -->
                            <div class="modal fade" id="rejectModal{{ $user->id }}" tabindex="-1" aria-labelledby="rejectModalLabel{{ $user->id }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger text-white">
                                            <h5 class="modal-title" id="rejectModalLabel{{ $user->id }}">
                                                <i class="fas fa-exclamation-triangle me-2"></i> Conferma rifiuto
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('admin.employees.reject', $user) }}" method="POST">
                                            @csrf
                                            <div class="modal-body">
                                                <div class="alert alert-warning">
                                                    <i class="fas fa-exclamation-circle me-2"></i>
                                                    Sei sicuro di voler rifiutare l'utente <strong>{{ $user->name }}</strong>?
                                                </div>
                                                <p class="text-danger">
                                                    <i class="fas fa-trash me-1"></i>
                                                    <strong>Attenzione:</strong> Questa azione eliminerà l'account dell'utente in modo permanente.
                                                </p>

                                                <div class="mb-3">
                                                    <label for="rejection_reason" class="form-label">Motivo del rifiuto (opzionale):</label>
                                                    <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="3" placeholder="Inserisci un motivo per il rifiuto..."></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                    <i class="fas fa-times me-1"></i> Annulla
                                                </button>
                                                <button type="submit" class="btn btn-danger">
                                                    <i class="fas fa-trash me-1"></i> Conferma rifiuto
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{ $pendingUsers->links() }}

                @else
                <div class="alert alert-info shadow-sm" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-info-circle fa-2x me-3"></i>
                        <div>
                            <h5 class="alert-heading mb-1">Nessuna richiesta in attesa</h5>
                            <p class="mb-0">Non ci sono utenti in attesa di approvazione al momento.</p>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="row">
            <div class="col-md-4">
                <div class="card shadow-sm border-left-primary mb-4">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Nuovi utenti (oggi)</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pendingUsers->where('created_at', '>=', \Carbon\Carbon::today())->count() }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-user-plus fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm border-left-success mb-4">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Utenti totali approvati</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ \App\Models\User::where('is_approved', true)->count() }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm border-left-warning mb-4">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Tempo medio approvazione</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">~24h</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-clock fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Inizializza i tooltip
        document.addEventListener('DOMContentLoaded', function() {
            // Inizializza i tooltip
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        });
    </script>

    <style>
        .border-left-primary {
            border-left: .25rem solid #4e73df!important;
        }
        .border-left-success {
            border-left: .25rem solid #1cc88a!important;
        }
        .border-left-warning {
            border-left: .25rem solid #f6c23e!important;
        }
    </style>
</x-layout>
