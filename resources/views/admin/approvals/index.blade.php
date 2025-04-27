<x-layout>
    <x-slot name="styles">
        @vite('resources/css/pages/admin.css')
    </x-slot>
    <x-slot name="sidebar">
        @include('components.sidebar')
    </x-slot>

    <div class="container-fluid py-4">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800 fw-bold">Gestione Approvazioni Utenti</h1>
            <a href="{{ route('admin.dashboard') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50 me-1"></i> Torna alla Dashboard
            </a>
        </div>

        <!--  messaggi di successo o errore -->
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @elseif(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow border-left-warning">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center bg-light">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-user-clock me-2"></i> Utenti in attesa di approvazione
                        </h6>
                        <span class="badge bg-warning">{{ $pendingUsers->total() }} in attesa</span>
                    </div>
                    <div class="card-body">
                        @if($pendingUsers->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Nome</th>
                                            <th>Email</th>
                                            <th>Ruolo</th>
                                            <th>Dipartimento</th>
                                            <th>Data registrazione</th>
                                            <th>Azioni</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($pendingUsers as $user)
                                            <tr>
                                                <td class="align-middle">
                                                    <div class="d-flex align-items-center">
                                                        @if($user->photo)
                                                            <img src="{{ asset('storage/' . $user->photo) }}" alt="{{ $user->name }}" class="img-profile rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                                        @else
                                                            <div class="avatar-circle me-2 d-inline-flex align-items-center justify-content-center bg-primary text-white rounded-circle" style="width: 40px; height: 40px;">
                                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                                            </div>
                                                        @endif
                                                        <div>
                                                            <div class="fw-bold">{{ $user->name }}</div>
                                                            @if($user->position)
                                                                <div class="small text-muted">{{ $user->position }}</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="align-middle">{{ $user->email }}</td>
                                                <td class="align-middle">
                                                    @foreach($user->roles as $role)
                                                        <span class="badge {{ $role->name == 'admin' ? 'bg-danger' : ($role->name == 'employee' ? 'bg-primary' : 'bg-info') }} rounded-pill">
                                                            {{ ucfirst($role->name) }}
                                                        </span>
                                                    @endforeach
                                                </td>
                                                <td class="align-middle">{{ $user->department ? $user->department->name : 'Non assegnato' }}</td>
                                                <td class="align-middle">{{ is_string($user->created_at) ? $user->created_at : $user->created_at->format('d/m/Y H:i') }}</td>
                                                <td class="align-middle">
                                                    <div class="d-flex gap-1">
                                                        <!-- Approva utente -->
                                                        <form action="{{ route('admin.approvals.approve', $user) }}" method="POST" class="me-1">
                                                            @csrf
                                                            <button type="submit" class="btn btn-success btn-sm" title="Approva utente">
                                                                <i class="fas fa-check-circle"></i> Approva
                                                            </button>
                                                        </form>

                                                        <!-- Rifiuta utente (senza modal) -->
                                                        <form action="{{ route('admin.approvals.reject', $user) }}" method="POST" class="me-1">
                                                            @csrf
                                                            <button type="submit" class="btn btn-danger btn-sm" title="Rifiuta utente" onclick="return confirm('Sei sicuro di voler rifiutare l\'utente {{ $user->name }}? Questa azione eliminerà l\'account in modo permanente.')">
                                                                <i class="fas fa-times-circle"></i> Rifiuta
                                                            </button>
                                                        </form>

                                                        <!-- Link alla pagina di modifica utente -->
                                                        <a href="{{ route('admin.employees.edit', $user) }}" class="btn btn-primary btn-sm" title="Modifica utente">
                                                            <i class="fas fa-edit"></i> Modifica
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Paginazione -->
                            <div class="d-flex justify-content-center mt-4">
                                {{ $pendingUsers->links() }}
                            </div>
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
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pendingUsers->filter(function($user) {
                                    if (is_string($user->created_at)) {
                                        return strpos($user->created_at, now()->format('Y-m-d')) !== false;
                                    }
                                    return $user->created_at >= now()->startOfDay();
                                })->count() }}</div>
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
