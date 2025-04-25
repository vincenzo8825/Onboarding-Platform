<x-layout>
    <div class="container py-4">
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        @if($user->profile_photo)
                            <img src="{{ Storage::url($user->profile_photo) }}" alt="Profile Photo" class="img-fluid rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                        @else
                            <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 150px; height: 150px;">
                                <i class="fas fa-user fa-4x"></i>
                            </div>
                        @endif

                        <h4>{{ $user->name }}</h4>
                        <p class="text-muted">{{ ucfirst($user->role) }}</p>

                        <form action="{{ route('employee.profile.upload-photo') }}" method="POST" enctype="multipart/form-data" class="mt-3">
                            @csrf
                            <div class="mb-3">
                                <label for="photo" class="form-label">Cambia foto profilo</label>
                                <input type="file" class="form-control" id="photo" name="photo">
                                @error('photo')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary">Carica foto</button>
                        </form>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">Informazioni Account</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Email:</strong> {{ $user->email }}</p>
                        <p><strong>Data registrazione:</strong> {{ $user->created_at->format('d/m/Y') }}</p>
                        <p><strong>Ultimo accesso:</strong> {{ $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : 'Mai' }}</p>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Informazioni Personali</h5>
                        <a href="{{ route('employee.profile.edit') }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-edit me-1"></i> Modifica
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <strong>Nome completo</strong>
                            </div>
                            <div class="col-md-8">
                                {{ $user->name }}
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <strong>Telefono</strong>
                            </div>
                            <div class="col-md-8">
                                {{ $user->phone ?? 'Non specificato' }}
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <strong>Indirizzo</strong>
                            </div>
                            <div class="col-md-8">
                                {{ $user->address ?? 'Non specificato' }}
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <strong>Contatto di emergenza</strong>
                            </div>
                            <div class="col-md-8">
                                {{ $user->emergency_contact ?? 'Non specificato' }}
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <strong>Dipartimento</strong>
                            </div>
                            <div class="col-md-8">
                                {{ $user->department ?? 'Non assegnato' }}
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <strong>Bio</strong>
                            </div>
                            <div class="col-md-8">
                                {{ $user->bio ?? 'Nessuna informazione disponibile' }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">Badge e Riconoscimenti</h5>
                    </div>
                    <div class="card-body">
                        @if($user->badges && $user->badges->count() > 0)
                            <div class="row">
                                @foreach($user->badges as $badge)
                                    <div class="col-md-3 text-center mb-4">
                                        <div class="badge-icon mb-2">
                                            <i class="{{ $badge->icon }} fa-3x text-primary"></i>
                                        </div>
                                        <h6>{{ $badge->name }}</h6>
                                        <small class="text-muted">
                                            @if($badge->pivot->awarded_at)
                                                {{ \Carbon\Carbon::parse($badge->pivot->awarded_at)->format('d/m/Y') }}
                                            @else
                                                Data sconosciuta
                                            @endif
                                        </small>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted">Non hai ancora ricevuto badge o riconoscimenti.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>
