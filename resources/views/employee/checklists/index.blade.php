<x-layout>
    <x-slot name="sidebar">
        @include('components.sidebar')
    </x-slot>

    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Le mie Checklist</h2>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(count($checklists) === 0)
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-clipboard-list fs-1 text-muted mb-3"></i>
                    <h5>Nessuna checklist assegnata</h5>
                    <p class="text-muted">Non hai ancora checklist assegnate per il tuo processo di onboarding.</p>
                </div>
            </div>
        @else
            <div class="row">
                @foreach($checklists as $checklistId => $checklistData)
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="mb-0">{{ $checklistData['checklist']->name }}</h5>
                            </div>
                            <div class="card-body">
                                @if($checklistData['checklist']->description)
                                    <p class="mb-3">{{ Str::limit($checklistData['checklist']->description, 100) }}</p>
                                @endif

                                <div class="progress mb-3" style="height: 10px;">
                                    @php
                                        $percentage = $checklistData['total'] > 0
                                            ? round(($checklistData['completed'] / $checklistData['total']) * 100)
                                            : 0;
                                    @endphp
                                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $percentage }}%;" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <p class="text-center mb-3">
                                    <strong>{{ $checklistData['completed'] }}</strong> di <strong>{{ $checklistData['total'] }}</strong> elementi completati
                                    <span class="ms-2 badge {{ $percentage == 100 ? 'bg-success' : 'bg-primary' }}">
                                        {{ $percentage }}%
                                    </span>
                                </p>

                                <ul class="list-group list-group-flush mb-3">
                                    @php $shownItems = 0; @endphp
                                    @foreach($checklistData['items'] as $item)
                                        @if($shownItems < 3)
                                            <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                                <span class="{{ $item->pivot->is_completed ? 'text-decoration-line-through text-muted' : '' }}">
                                                    {{ Str::limit($item->title, 30) }}
                                                </span>
                                                @if($item->pivot->is_completed)
                                                    <span class="badge bg-success rounded-pill">
                                                        <i class="fas fa-check"></i>
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary rounded-pill">
                                                        <i class="fas fa-hourglass"></i>
                                                    </span>
                                                @endif
                                            </li>
                                            @php $shownItems++; @endphp
                                        @endif
                                    @endforeach

                                    @if(count($checklistData['items']) > 3)
                                        <li class="list-group-item px-0 text-center">
                                            <small class="text-muted">
                                                + altri {{ count($checklistData['items']) - 3 }} elementi
                                            </small>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                            <div class="card-footer">
                                <a href="{{ route('employee.checklists.show', $checklistId) }}" class="btn btn-primary w-100">
                                    <i class="fas fa-tasks me-2"></i> Visualizza e completa
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="card bg-light mt-4">
            <div class="card-body">
                <h5 class="card-title">Cos'è una checklist di onboarding?</h5>
                <p class="card-text">Le checklist ti guidano attraverso le attività da completare per il tuo processo di onboarding aziendale. Segui questi passaggi:</p>
                <ol class="mb-0">
                    <li>Visualizza tutte le attività assegnate</li>
                    <li>Leggi attentamente le istruzioni per ogni elemento</li>
                    <li>Completa le attività e carica eventuali documenti richiesti</li>
                    <li>Segna le attività come completate man mano che procedi</li>
                </ol>
            </div>
        </div>
    </div>
</x-layout>
