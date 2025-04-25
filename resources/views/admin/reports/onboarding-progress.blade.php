<x-layout>
    <x-slot name="sidebar">
        @include('components.sidebar')
    </x-slot>

    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">Report Progresso Onboarding</h1>

        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Riepilogo Progresso Onboarding</h6>
                        <a href="{{ route('admin.reports.index') }}" class="btn btn-sm btn-secondary">
                            Torna alla Dashboard
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <canvas id="progressChart" style="max-height: 400px;"></canvas>
                            </div>
                            <div class="col-md-4">
                                <div class="card mb-3 border-left-success">
                                    <div class="card-body py-3">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                    Dipendenti che hanno completato l'onboarding
                                                </div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                    {{ $completedCount }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card mb-3 border-left-warning">
                                    <div class="card-body py-3">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                    Dipendenti in corso d'onboarding
                                                </div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                    {{ $inProgressCount }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card mb-3 border-left-danger">
                                    <div class="card-body py-3">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                                    Dipendenti con onboarding in ritardo
                                                </div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                    {{ $delayedCount }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Progresso individuale dipendenti</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="usersTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Dipendente</th>
                                        <th>Dipartimento</th>
                                        <th>Data assunzione</th>
                                        <th>Progresso</th>
                                        <th>Status</th>
                                        <th>Azioni</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($userProgress as $progress)
                                    <tr>
                                        <td>{{ $progress['user']->name }}</td>
                                        <td>{{ $progress['user']->department ? $progress['user']->department->name : 'N/A' }}</td>
                                        <td>{{ $progress['user']->created_at->format('d/m/Y') }}</td>
                                        <td>
                                            <div class="progress">
                                                <div class="progress-bar bg-{{ $progress['progress_class'] }}" role="progressbar"
                                                    style="width: {{ $progress['progress'] }}%"
                                                    aria-valuenow="{{ $progress['progress'] }}" aria-valuemin="0" aria-valuemax="100">
                                                    {{ $progress['progress'] }}%
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $progress['status_class'] }}">
                                                {{ $progress['status'] }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.users.checklists.index', $progress['user']->id) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i> Dettagli
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-slot name="scripts">
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            // Configurazione grafico progresso
            const progressLabels = ['Completato', 'In corso', 'In ritardo'];
            const progressData = [{{ $completedCount }}, {{ $inProgressCount }}, {{ $delayedCount }}];

            new Chart(document.getElementById('progressChart'), {
                type: 'pie',
                data: {
                    labels: progressLabels,
                    datasets: [{
                        data: progressData,
                        backgroundColor: [
                            'rgba(40, 167, 69, 0.8)',
                            'rgba(255, 193, 7, 0.8)',
                            'rgba(220, 53, 69, 0.8)'
                        ],
                        borderColor: [
                            'rgba(40, 167, 69, 1)',
                            'rgba(255, 193, 7, 1)',
                            'rgba(220, 53, 69, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'right'
                        },
                        title: {
                            display: true,
                            text: 'Stato Onboarding Dipendenti'
                        }
                    }
                }
            });

            // Inizializzazione datatable
            $(document).ready(function() {
                $('#usersTable').DataTable({
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Italian.json'
                    }
                });
            });
        </script>
    </x-slot>
</x-layout>
