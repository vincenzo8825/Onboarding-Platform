<x-layout>
    <x-slot name="sidebar">
        @include('components.sidebar')
    </x-slot>

    <div class="card">
        <div class="card-header">My Profile</div>
        <div class="card-body">
            @if(isset($user))
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="profile-image mb-3">
                            @if($user->profile_image)
                                <img src="{{ asset('storage/' . $user->profile_image) }}" alt="Profile Image" class="img-fluid rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                            @else
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 150px; height: 150px; font-size: 50px;">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-8">
                        <h3>{{ $user->name }}</h3>
                        <p class="text-muted">{{ $user->email }}</p>

                        @if($user->department)
                            <p><strong>Dipartimento:</strong> {{ $user->department }}</p>
                        @endif

                        @if($user->position)
                            <p><strong>Posizione:</strong> {{ $user->position }}</p>
                        @endif

                        @if($user->hire_date)
                            <p><strong>Data di assunzione:</strong> {{ \Carbon\Carbon::parse($user->hire_date)->format('d/m/Y') }}</p>
                        @endif
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <h4 class="mb-3">Informazioni Personali</h4>
                        <hr>
                        <div class="row mb-2">
                            <div class="col-md-3"><strong>Telefono:</strong></div>
                            <div class="col-md-9">{{ $user->phone ?? 'Non specificato' }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-3"><strong>Indirizzo:</strong></div>
                            <div class="col-md-9">{{ $user->address ?? 'Non specificato' }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-3"><strong>Data di nascita:</strong></div>
                            <div class="col-md-9">{{ $user->birth_date ? \Carbon\Carbon::parse($user->birth_date)->format('d/m/Y') : 'Non specificato' }}</div>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <a href="{{ route('employee.profile.edit') }}" class="btn btn-primary">Modifica profilo</a>
                </div>
            @else
                <div class="alert alert-warning">
                    Impossibile caricare i dati del profilo.
                </div>
            @endif
        </div>
    </div>
</x-layout>
