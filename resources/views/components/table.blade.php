@props([
    'headers' => [],
    'items' => collect(),
    'emptyIcon' => 'fas fa-info-circle',
    'emptyTitle' => 'Nessun elemento trovato',
    'emptyMessage' => 'Non ci sono elementi da visualizzare in questa tabella.',
    'emptyActionText' => null,
    'emptyActionUrl' => null,
    'responsive' => true,
    'hover' => true
])

@if($items->isEmpty())
    <div class="text-center py-5">
        <div class="mb-3">
            <i class="{{ $emptyIcon }} fa-3x text-secondary"></i>
        </div>
        <h5 class="fw-bold">{{ $emptyTitle }}</h5>
        <p class="text-muted">{{ $emptyMessage }}</p>
        @if($emptyActionText && $emptyActionUrl)
            <a href="{{ $emptyActionUrl }}" class="btn btn-primary">
                {{ $emptyActionText }}
            </a>
        @endif
    </div>
@else
    <div class="{{ $responsive ? 'table-responsive' : '' }}">
        <table class="table{{ $hover ? ' table-hover' : '' }}">
            <thead>
                <tr>
                    @foreach($headers as $header)
                        <th>{{ $header }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                {{ $slot }}
            </tbody>
        </table>
    </div>
@endif
