<x-layout>
    <x-slot name="sidebar">
        @include('components.sidebar')
    </x-slot>

    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">Statistiche per Dipartimento</h1>

        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Riepilogo Dipartimenti</h6>
                        <a href="{{ route('admin.reports.index') }}" class="btn btn-sm btn-secondary">
                            Torna alla Dashboard
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <canvas id="departmentProgressChart" style="max-height: 400px;"></canvas>
                            </div>
                            <div class="col-md-4">
                                <div class="card mb-3 border-left-info">
                                    <div class="card-body py-3">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                    Totale Dipartimenti
                                                </div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                    {{ count($departmentStats) }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card mb-3 border-left-primary">
                                    <div class="card-body py-3">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                    Dipartimento con Maggior Progresso
                                                </div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                    @php
                                                        $maxProgress = 0;
                                                        $topDept = 'N/A';
                                                        foreach($departmentStats as $dept) {
                                                            if($dept['avg_progress'] > $maxProgress) {
                                                                $maxProgress = $dept['avg_progress'];
                                                                $topDept = $dept['name'];
                                                            }
                                                        }
                                                        echo $topDept . ' (' . $maxProgress . '%)';
                                                    @endphp
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
                                                    Dipartimento con Minor Progresso
                                                </div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                    @php
                                                        $minProgress = 100;
                                                        $bottomDept = 'N/A';
                                                        foreach($departmentStats as $dept) {
                                                            if($dept['avg_progress'] < $minProgress && $dept['users_count'] > 0) {
                                                                $minProgress = $dept['avg_progress'];
                                                                $bottomDept = $dept['name'];
                                                            }
                                                        }
                                                        echo $bottomDept . ' (' . $minProgress . '%)';
                                                    @endphp
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
                        <h6 class="m-0 font-weight-bold text-primary">Dettaglio Dipartimenti</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="departmentsTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Dipartimento</th>
                                        <th>Numero Dipendenti</th>
                                        <th>Progresso Medio Checklist</th>
                                        <th>Visualizzazione</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($departmentStats as $department)
                                    <tr>
                                        <td>{{ $department['name'] }}</td>
                                        <td>{{ $department['users_count'] }}</td>
                                        <td>
                                            <div class="progress">
                                                <div class="progress-bar bg-{{ $department['avg_progress'] > 75 ? 'success' : ($department['avg_progress'] > 50 ? 'info' : ($department['avg_progress'] > 25 ? 'warning' : 'danger')) }}"
                                                    role="progressbar"
                                                    style="width: {{ $department['avg_progress'] }}%"
                                                    aria-valuenow="{{ $department['avg_progress'] }}"
                                                    aria-valuemin="0"
                                                    aria-valuemax="100">
                                                    {{ $department['avg_progress'] }}%
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <a href="#" class="btn btn-sm btn-primary">
                                                <i class="fas fa-chart-bar"></i> Dettaglio
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
            // Configurazione grafico progresso dipartimenti
            const departmentLabels = [
                @foreach($departmentStats as $department)
                    '{{ $department['name'] }}',
                @endforeach
            ];

            const departmentData = [
                @foreach($departmentStats as $department)
                    {{ $department['avg_progress'] }},
                @endforeach
            ];

            const departmentUsers = [
                @foreach($departmentStats as $department)
                    {{ $department['users_count'] }},
                @endforeach
            ];

            new Chart(document.getElementById('departmentProgressChart'), {
                type: 'bar',
                data: {
                    labels: departmentLabels,
                    datasets: [{
                        label: '% Progresso Medio',
                        data: departmentData,
                        backgroundColor: 'rgba(78, 115, 223, 0.8)',
                        borderColor: 'rgba(78, 115, 223, 1)',
                        borderWidth: 1,
                        yAxisID: 'y'
                    }, {
                        label: 'Numero Dipendenti',
                        data: departmentUsers,
                        backgroundColor: 'rgba(28, 200, 138, 0.5)',
                        borderColor: 'rgba(28, 200, 138, 1)',
                        borderWidth: 1,
                        type: 'line',
                        yAxisID: 'y1'
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            position: 'left',
                            ticks: {
                                callback: function(value) {
                                    return value + '%';
                                }
                            }
                        },
                        y1: {
                            beginAtZero: true,
                            position: 'right',
                            grid: {
                                drawOnChartArea: false
                            },
                            title: {
                                display: true,
                                text: 'Numero Dipendenti'
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const datasetLabel = context.dataset.label || '';
                                    const value = context.parsed.y;
                                    return datasetLabel + ': ' + value + (datasetLabel.includes('%') ? '%' : '');
                                }
                            }
                        }
                    }
                }
            });

            // Inizializzazione datatable
            $(document).ready(function() {
                $('#departmentsTable').DataTable({
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Italian.json'
                    }
                });
            });
        </script>
    </x-slot>
</x-layout>
