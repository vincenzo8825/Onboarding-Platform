<x-layout>
    <x-slot name="sidebar">
        @include('components.sidebar')
    </x-slot>

    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">Dashboard Statistiche</h1>

        <!-- Contatori principali -->
        <div class="row">
            <div class="col-md-3 mb-4">
                <div class="card border-left-primary shadow h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Utenti Totali</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalUsers }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-4">
                <div class="card border-left-success shadow h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Dipendenti Attivi</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $activeUsers }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-4">
                <div class="card border-left-info shadow h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Onboarding Completati</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $completedOnboarding }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-4">
                <div class="card border-left-warning shadow h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Ticket Aperti</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $openTickets }} / {{ $openTickets + $resolvedTickets }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grafici principali -->
        <div class="row">
            <!-- Completamento Checklist per Dipartimento -->
            <div class="col-md-6 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Completamento Checklist per Dipartimento</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="departmentChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Stato Utenti -->
            <div class="col-md-6 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Stato Completamento Utenti</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="userCompletionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Progresso Corsi -->
            <div class="col-md-6 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Stato Completamento Corsi</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="courseChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Trend Mensili -->
            <div class="col-md-6 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Trend Mensili</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="trendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Link alle statistiche dettagliate -->
        <div class="row">
            <div class="col-md-12 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Report Dettagliati</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <a href="{{ route('admin.reports.onboarding-progress') }}" class="btn btn-primary btn-block">
                                    Progresso Onboarding Dipendenti
                                </a>
                            </div>
                            <div class="col-md-4 mb-3">
                                <a href="{{ route('admin.reports.department-stats') }}" class="btn btn-primary btn-block">
                                    Statistiche per Dipartimento
                                </a>
                            </div>
                            <div class="col-md-4 mb-3">
                                <a href="{{ route('admin.reports.course-stats') }}" class="btn btn-primary btn-block">
                                    Statistiche Corsi
                                </a>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <a href="{{ route('admin.reports.quiz-stats') }}" class="btn btn-primary btn-block">
                                    Statistiche Quiz
                                </a>
                            </div>
                            <div class="col-md-4 mb-3">
                                <a href="{{ route('admin.reports.monthly-trends') }}" class="btn btn-primary btn-block">
                                    Trend Mensili
                                </a>
                            </div>
                            <div class="col-md-4 mb-3">
                                <a href="{{ route('admin.reports.ticket-stats') }}" class="btn btn-primary btn-block">
                                    Statistiche Ticket
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-slot name="scripts">
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            // Configurazione tema grafici
            Chart.defaults.color = '#858796';
            Chart.defaults.font.family = "'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,\"Segoe UI\",Roboto,\"Helvetica Neue\",Arial,sans-serif'";

            // Grafico dipartimenti
            const departmentLabels = @json($departmentData['labels']);
            const departmentData = @json($departmentData['data']);

            new Chart(document.getElementById('departmentChart'), {
                type: 'bar',
                data: {
                    labels: departmentLabels,
                    datasets: [{
                        label: '% Completamento',
                        data: departmentData,
                        backgroundColor: 'rgba(78, 115, 223, 0.8)',
                        borderColor: 'rgba(78, 115, 223, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            ticks: {
                                callback: function(value) {
                                    return value + '%';
                                }
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': ' + context.parsed.y + '%';
                                }
                            }
                        }
                    }
                }
            });

            // Grafico completamento utenti
            const userCompletionLabels = @json($userCompletionData['labels']);
            const userCompletionData = @json($userCompletionData['data']);

            new Chart(document.getElementById('userCompletionChart'), {
                type: 'doughnut',
                data: {
                    labels: userCompletionLabels,
                    datasets: [{
                        data: userCompletionData,
                        backgroundColor: [
                            'rgba(40, 167, 69, 0.8)',
                            'rgba(255, 193, 7, 0.8)'
                        ],
                        borderColor: [
                            'rgba(40, 167, 69, 1)',
                            'rgba(255, 193, 7, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });

            // Grafico corsi
            const courseLabels = @json($courseCompletionData['labels']);
            const courseCompleted = @json($courseCompletionData['completed']);
            const courseInProgress = @json($courseCompletionData['inProgress']);
            const courseNotStarted = @json($courseCompletionData['notStarted']);

            new Chart(document.getElementById('courseChart'), {
                type: 'bar',
                data: {
                    labels: courseLabels,
                    datasets: [
                        {
                            label: 'Completati',
                            data: courseCompleted,
                            backgroundColor: 'rgba(40, 167, 69, 0.8)',
                            borderColor: 'rgba(40, 167, 69, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'In corso',
                            data: courseInProgress,
                            backgroundColor: 'rgba(255, 193, 7, 0.8)',
                            borderColor: 'rgba(255, 193, 7, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Non iniziato',
                            data: courseNotStarted,
                            backgroundColor: 'rgba(220, 53, 69, 0.8)',
                            borderColor: 'rgba(220, 53, 69, 1)',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            stacked: true
                        },
                        y: {
                            stacked: true,
                            beginAtZero: true
                        }
                    }
                }
            });

            // Grafico trend mensili
            const trendLabels = @json($monthlyProgressData['labels']);
            const completionsData = @json($monthlyProgressData['completions']);
            const newHiresData = @json($monthlyProgressData['newHires']);

            new Chart(document.getElementById('trendChart'), {
                type: 'line',
                data: {
                    labels: trendLabels,
                    datasets: [
                        {
                            label: 'Completamenti',
                            data: completionsData,
                            fill: false,
                            borderColor: 'rgba(40, 167, 69, 1)',
                            tension: 0.1
                        },
                        {
                            label: 'Nuovi dipendenti',
                            data: newHiresData,
                            fill: false,
                            borderColor: 'rgba(0, 123, 255, 1)',
                            tension: 0.1
                        }
                    ]
                },
                options: {
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        </script>
    </x-slot>
</x-layout>
