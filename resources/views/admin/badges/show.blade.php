<x-layout>
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Dettagli Badge</h2>
            <div>
                <a href="{{ route('admin.badges.edit', $badge) }}" class="btn btn-warning me-2">
                    <i class="fas fa-edit me-1"></i> Modifica
                </a>
                <a href="{{ route('admin.badges.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Torna all'elenco
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="badge-icon mb-3" style="color: {{ $badge->color }}">
                            <i class="{{ $badge->icon }} fa-5x"></i>
                        </div>
                        <h3>{{ $badge->name }}</h3>
                        <p class="text-muted">{{ $badge->description }}</p>

                        <div class="mt-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Tipo:</span>
                                <span>
                                    @if($badge->is_automatic)
                                        <span class="badge bg-success">Automatico</span>
                                    @else
                                        <span class="badge bg-secondary">Manuale</span>
                                    @endif
                                </span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Assegnazioni:</span>
                                <span>{{ $badge->users->count() }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Creato il:</span>
                                <span>{{ $badge->created_at->format('d/m/Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">Criteri di assegnazione</h5>
                    </div>
                    <div class="card-body">
                        @if($badge->criteria)
                            <p>{{ $badge->criteria }}</p>
                        @else
                            <p class="text-muted">Nessun criterio specificato.</p>
                        @endif
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">Assegna Badge</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.badges.award', $badge) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="user_id" class="form-label">Seleziona Utente</label>
                                <select class="form-select @error('user_id') is-invalid @enderror" id="user_id" name="user_id" required>
                                    <option value="">Seleziona un utente...</option>
                                    @foreach(\App\Models\User::where('role', 'employee')->orderBy('name')->get() as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                    @endforeach
                                </select>
                                @error('user_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Assegna Badge</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Utenti con questo Badge</h5>
                    </div>
                    <div class="card-body">
                        @if($users->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Utente</th>
                                            <th>Email</th>
                                            <th>Dipartimento</th>
                                            <th>Data assegnazione</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($users as $user)
                                            <tr>
                                                <td>{{ $user->name }}</td>
                                                <td>{{ $user->email }}</td>
                                                <td>{{ $user->department ?? 'N/A' }}</td>
                                                <td>
                                                    @if($user->pivot->awarded_at)
                                                        {{ \Carbon\Carbon::parse($user->pivot->awarded_at)->format('d/m/Y H:i') }}
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex justify-content-center mt-4">
                                {{ $users->links() }}
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                <h5>Nessun utente ha ancora ricevuto questo badge</h5>
                                <p class="text-muted">Utilizza il form a sinistra per assegnare questo badge a un utente.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>
