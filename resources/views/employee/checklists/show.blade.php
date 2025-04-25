<x-layout>
    <x-slot name="sidebar">
        @include('components.sidebar')
    </x-slot>

    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>{{ $checklist->name }}</h2>
            <a href="{{ route('employee.checklists.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i> Torna alle checklist
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row mb-4">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Elementi da completare</h5>
                        <span class="badge bg-primary">{{ count($items) }} elementi</span>
                    </div>
                    <div class="card-body p-0">
                        @php
                            $pendingItems = $items->filter(function($item) {
                                return !$item->pivot->is_completed;
                            });
                            $completedItems = $items->filter(function($item) {
                                return $item->pivot->is_completed;
                            });
                        @endphp

                        @if($pendingItems->isEmpty() && $completedItems->isEmpty())
                            <div class="text-center p-4">
                                <i class="fas fa-clipboard-check fs-1 text-muted mb-3"></i>
                                <p class="mb-0">Nessun elemento trovato nella checklist</p>
                            </div>
                        @else
                            <div class="list-group list-group-flush">
                                <!-- Elementi in sospeso -->
                                @if($pendingItems->isNotEmpty())
                                    <div class="list-group-item bg-light">
                                        <h6 class="mb-0">Da completare</h6>
                                    </div>

                                    @foreach($pendingItems->sortBy('order') as $item)
                                        <div class="list-group-item"
                                             id="item-{{ $item->id }}"
                                             x-data="{ showDetails: false, showForm: false }">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="mb-1">{{ $item->order }}. {{ $item->title }}</h6>

                                                    @if($item->pivot->status === 'rejected')
                                                        <div class="badge bg-danger">Rifiutato - Da ricompletare</div>
                                                    @endif
                                                </div>
                                                <div>
                                                    <button @click="showDetails = !showDetails" class="btn btn-sm btn-outline-secondary me-1">
                                                        <i class="fas" :class="showDetails ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                                                    </button>
                                                    <button @click="showForm = !showForm; showDetails = true" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-check me-1"></i> Completa
                                                    </button>
                                                </div>
                                            </div>

                                            <div x-show="showDetails" x-transition class="mt-3">
                                                @if($item->description)
                                                    <div class="mb-3">
                                                        <strong>Descrizione:</strong>
                                                        <p class="mb-0">{{ $item->description }}</p>
                                                    </div>
                                                @endif

                                                <div class="mb-3">
                                                    <div class="d-flex flex-wrap gap-2">
                                                        @if($item->due_date)
                                                            <span class="badge bg-warning">
                                                                <i class="fas fa-calendar me-1"></i> Scadenza: {{ $item->due_date->format('d/m/Y') }}
                                                            </span>
                                                        @endif

                                                        @if($item->requires_file)
                                                            <span class="badge bg-info">
                                                                <i class="fas fa-file-upload me-1"></i> Richiede file
                                                            </span>
                                                        @endif

                                                        @if($item->requires_approval)
                                                            <span class="badge bg-secondary">
                                                                <i class="fas fa-user-check me-1"></i> Richiede approvazione
                                                            </span>
                                                        @endif

                                                        @if($item->is_mandatory)
                                                            <span class="badge bg-danger">
                                                                <i class="fas fa-exclamation-circle me-1"></i> Obbligatorio
                                                            </span>
                                                        @else
                                                            <span class="badge bg-secondary">
                                                                <i class="fas fa-info-circle me-1"></i> Opzionale
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                @if($item->pivot->notes)
                                                    <div class="alert alert-warning mb-3">
                                                        <strong>Note:</strong>
                                                        <p class="mb-0">{{ $item->pivot->notes }}</p>
                                                    </div>
                                                @endif

                                                <form x-show="showForm"
                                                      x-transition
                                                      action="{{ route('employee.checklists.items.complete', $item) }}"
                                                      method="POST"
                                                      enctype="multipart/form-data"
                                                      class="border rounded p-3 bg-light">
                                                    @csrf

                                                    @if($item->requires_file)
                                                        <div class="mb-3">
                                                            <label for="file-{{ $item->id }}" class="form-label">
                                                                Carica file <span class="text-danger">*</span>
                                                            </label>
                                                            <input type="file"
                                                                   id="file-{{ $item->id }}"
                                                                   name="file"
                                                                   class="form-control"
                                                                   {{ $item->requires_file ? 'required' : '' }}>
                                                            <small class="form-text text-muted">
                                                                Formati supportati: PDF, DOC, DOCX, JPG, PNG (max 10MB)
                                                            </small>
                                                        </div>
                                                    @endif

                                                    <div class="mb-3">
                                                        <label for="notes-{{ $item->id }}" class="form-label">Note (opzionale)</label>
                                                        <textarea id="notes-{{ $item->id }}"
                                                                  name="notes"
                                                                  class="form-control"
                                                                  rows="2"
                                                                  placeholder="Aggiungi note o commenti...">{{ $item->pivot->notes }}</textarea>
                                                    </div>

                                                    <div class="d-flex justify-content-end gap-2">
                                                        <button type="button" @click="showForm = false" class="btn btn-outline-secondary">
                                                            Annulla
                                                        </button>
                                                        <button type="submit" class="btn btn-success">
                                                            <i class="fas fa-check me-1"></i> Segna come completato
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif

                                <!-- Elementi completati -->
                                @if($completedItems->isNotEmpty())
                                    <div class="list-group-item bg-light">
                                        <h6 class="mb-0">Completati</h6>
                                    </div>

                                    @foreach($completedItems->sortBy('order') as $item)
                                        <div class="list-group-item"
                                             id="item-{{ $item->id }}"
                                             x-data="{ showDetails: false }">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="mb-1 text-muted">
                                                        <i class="fas fa-check-circle text-success me-2"></i>
                                                        <span class="text-decoration-line-through">
                                                            {{ $item->order }}. {{ $item->title }}
                                                        </span>
                                                    </h6>

                                                    @if($item->pivot->status === 'needs_review')
                                                        <div class="badge bg-warning">In attesa di approvazione</div>
                                                    @elseif($item->pivot->status === 'completed')
                                                        <div class="badge bg-success">Completato</div>
                                                    @endif
                                                </div>
                                                <div>
                                                    <button @click="showDetails = !showDetails" class="btn btn-sm btn-outline-secondary">
                                                        <i class="fas" :class="showDetails ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            <div x-show="showDetails" x-transition class="mt-3">
                                                <div class="mb-2">
                                                    <small class="text-muted">
                                                        Completato il {{ $item->pivot->completed_at ? \Carbon\Carbon::parse($item->pivot->completed_at)->format('d/m/Y H:i') : 'N/A' }}
                                                    </small>
                                                </div>

                                                @if($item->pivot->file_path)
                                                    <div class="mb-3">
                                                        <strong>File caricato:</strong>
                                                        <div class="mt-1">
                                                            <a href="{{ Storage::url($item->pivot->file_path) }}"
                                                               target="_blank"
                                                               class="btn btn-sm btn-outline-primary">
                                                                <i class="fas fa-file me-1"></i> Visualizza file
                                                            </a>
                                                        </div>
                                                    </div>
                                                @endif

                                                @if($item->pivot->notes)
                                                    <div class="mb-3">
                                                        <strong>Note:</strong>
                                                        <p class="mb-0 text-muted">{{ $item->pivot->notes }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Riepilogo</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-center mb-3">
                            <div class="position-relative" style="width: 120px; height: 120px;">
                                <div class="position-absolute top-50 start-50 translate-middle text-center">
                                    <h3 class="mb-0">{{ round($progress) }}%</h3>
                                    <small class="text-muted">Completato</small>
                                </div>
                                <canvas id="progressChart"></canvas>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="progress">
                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $progress }}%;"
                                     aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <div class="d-flex justify-content-between mt-1">
                                <small><strong>{{ $completedItems->count() }}</strong> completati</small>
                                <small><strong>{{ $pendingItems->count() }}</strong> rimanenti</small>
                            </div>
                        </div>

                        @if($checklist->description)
                            <div class="mb-3">
                                <h6>Descrizione:</h6>
                                <p class="text-muted">{{ $checklist->description }}</p>
                            </div>
                        @endif

                        <div class="alert alert-info mb-0">
                            <div class="d-flex">
                                <div class="me-3">
                                    <i class="fas fa-info-circle fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="alert-heading">Suggerimenti</h6>
                                    <ul class="mb-0 ps-3">
                                        <li>Completa tutti gli elementi obbligatori</li>
                                        <li>Carica i file richiesti nei formati corretti</li>
                                        <li>Alcuni elementi potrebbero richiedere approvazione</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('progressChart').getContext('2d');

            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: [{{ $completedItems->count() }}, {{ $pendingItems->count() }}],
                        backgroundColor: ['#28a745', '#e9ecef'],
                        borderWidth: 0
                    }]
                },
                options: {
                    cutout: '75%',
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            enabled: false
                        }
                    }
                }
            });
        });
    </script>
    @endpush
</x-layout>
