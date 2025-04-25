<x-layout>
    <x-slot name="sidebar">
        @include('components.sidebar')
    </x-slot>

    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">Statistiche Ticket</h1>

        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Riepilogo Ticket</h6>
                        <a href="{{ route('admin.reports.index') }}" class="btn btn-sm btn-secondary">
                            Torna alla Dashboard
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <canvas id="ticketStatusChart" style="max-height: 400px;"></canvas>
                            </div>
                            <div class="col-md-4">
                                <div class="card mb-3 border-left-primary">
                                    <div class="card-body py-3">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                    Totale Ticket
                                                </div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                    {{ $totalTickets }}
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
                                                    Ticket Aperti
                                                </div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                    {{ $openTickets }} ({{ $totalTickets > 0 ? round(($openTickets / $totalTickets) * 100, 1) : 0 }}%)
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card mb-3 border-left-info">
                                    <div class="card-body py-3">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                    Tempo Medio di Risoluzione
                                                </div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                    {{ round($avgResolutionTime, 1) }} giorni
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
            <!-- Ticket per categoria -->
            <div class="col-md-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Ticket per Categoria</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="ticketCategoryChart" style="max-height: 350px;"></canvas>
                    </div>
                </div>
            </div>
            <!-- Trend risoluzione -->
            <div class="col-md-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Efficienza Risoluzione Ticket</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="resolutionEfficiencyChart" style="max-height: 350px;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Dettaglio Categorie</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="categoryTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Categoria</th>
                                        <th>Totale Ticket</th>
                                        <th>Percentuale</th>
                                        <th>Tempo Medio di Risoluzione</th>
                                        <th>Azioni</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($categoryCounts as $category)
                                    <tr>
                                        <td>{{ $category->category }}</td>
                                        <td>{{ $category->total }}</td>
                                        <td>
                                            <div class="progress">
                                                <div class="progress-bar"
                                                    role="progressbar"
                                                    style="width: {{ ($category->total / $totalTickets) * 100 }}%"
                                                    aria-valuenow="{{ ($category->total / $totalTickets) * 100 }}"
                                                    aria-valuemin="0"
                                                    aria-valuemax="100">
                                                    {{ round(($category->total / $totalTickets) * 100, 1) }}%
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @php
                                                // Simulazione: nella realtà ci sarebbe un dato effettivo dal DB
                                                $avgTime = rand(1, 10);
                                                echo $avgTime . ' giorni';
                                            @endphp
                                        </td>
                                        <td>
                                            <a href="#" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i> Visualizza
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
            // Dati per grafici stato ticket
            const ticketStatusLabels = ['Aperti', 'In Corso', 'Chiusi'];
            const ticketStatusData = [
                {{ $openTickets }},
                {{ $inProgressTickets }},
                {{ $closedTickets }}
            ];
            const ticketStatusColors = [
                'rgba(255, 193, 7, 0.8)',
                'rgba(54, 185, 204, 0.8)',
                'rgba(40, 167, 69, 0.8)'
            ];

            // Grafico stato ticket
            new Chart(document.getElementById('ticketStatusChart'), {
                type: 'doughnut',
                data: {
                    labels: ticketStatusLabels,
                    datasets: [{
                        data: ticketStatusData,
                        backgroundColor: ticketStatusColors,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right'
                        }
                    }
                }
            });

            // Dati per grafico categorie
            const categoryLabels = [
                @foreach($categoryCounts as $category)
                    '{{ $category->category }}',
                @endforeach
            ];

            const categoryData = [
                @foreach($categoryCounts as $category)
                    {{ $category->total }},
                @endforeach
            ];

            // Grafico categorie
            new Chart(document.getElementById('ticketCategoryChart'), {
                type: 'pie',
                data: {
                    labels: categoryLabels,
                    datasets: [{
                        data: categoryData,
                        backgroundColor: [
                            'rgba(78, 115, 223, 0.8)',
                            'rgba(28, 200, 138, 0.8)',
                            'rgba(246, 194, 62, 0.8)',
                            'rgba(231, 74, 59, 0.8)',
                            'rgba(54, 185, 204, 0.8)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });

            // Simulazione dati per efficienza risoluzione
            const months = ['Gen', 'Feb', 'Mar', 'Apr', 'Mag', 'Giu'];
            const avgResolutionTimes = [7, 6, 5, 6, 4, {{ round($avgResolutionTime, 1) }}];
            const ticketsPerMonth = [15, 20, 18, 25, 22, {{ $totalTickets }}];

            // Grafico efficienza risoluzione
            new Chart(document.getElementById('resolutionEfficiencyChart'), {
                type: 'bar',
                data: {
                    labels: months,
                    datasets: [
                        {
                            label: 'Tempo medio di risoluzione (giorni)',
                            data: avgResolutionTimes,
                            backgroundColor: 'rgba(78, 115, 223, 0.8)',
                            borderColor: 'rgba(78, 115, 223, 1)',
                            borderWidth: 1,
                            yAxisID: 'y'
                        },
                        {
                            label: 'Ticket mensili',
                            data: ticketsPerMonth,
                            backgroundColor: 'rgba(28, 200, 138, 0.5)',
                            borderColor: 'rgba(28, 200, 138, 1)',
                            borderWidth: 1,
                            type: 'line',
                            yAxisID: 'y1'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            position: 'left',
                            title: {
                                display: true,
                                text: 'Giorni'
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
                                text: 'Numero Ticket'
                            }
                        }
                    }
                }
            });

            // Inizializzazione datatable
            $(document).ready(function() {
                $('#categoryTable').DataTable({
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Italian.json'
                    }
                });
            });
        </script>
    </x-slot>
</x-layout>
