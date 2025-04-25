<x-layout>
    <x-slot name="sidebar">
        @include('components.sidebar')
    </x-slot>

    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">Statistiche Corsi</h1>

        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Riepilogo Corsi</h6>
                        <a href="{{ route('admin.reports.index') }}" class="btn btn-sm btn-secondary">
                            Torna alla Dashboard
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <canvas id="courseCompletionChart" style="max-height: 400px;"></canvas>
                            </div>
                            <div class="col-md-4">
                                <div class="card mb-3 border-left-success">
                                    <div class="card-body py-3">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                    Corsi con Maggior Completamento
                                                </div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                    @php
                                                        $maxCompletion = 0;
                                                        $topCourse = 'N/A';
                                                        foreach($courseStats as $course) {
                                                            if($course['completion_rate'] > $maxCompletion && $course['total_users'] > 0) {
                                                                $maxCompletion = $course['completion_rate'];
                                                                $topCourse = $course['title'];
                                                            }
                                                        }
                                                        echo $topCourse . ' (' . $maxCompletion . '%)';
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
                                                    Corsi con Minor Completamento
                                                </div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                    @php
                                                        $minCompletion = 100;
                                                        $bottomCourse = 'N/A';
                                                        foreach($courseStats as $course) {
                                                            if($course['completion_rate'] < $minCompletion && $course['total_users'] > 0) {
                                                                $minCompletion = $course['completion_rate'];
                                                                $bottomCourse = $course['title'];
                                                            }
                                                        }
                                                        echo $bottomCourse . ' (' . $minCompletion . '%)';
                                                    @endphp
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
                                                    Tasso Medio di Completamento
                                                </div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                    @php
                                                        $totalRates = 0;
                                                        $validCourses = 0;
                                                        foreach($courseStats as $course) {
                                                            if($course['total_users'] > 0) {
                                                                $totalRates += $course['completion_rate'];
                                                                $validCourses++;
                                                            }
                                                        }
                                                        echo $validCourses > 0 ? round($totalRates / $validCourses, 1) . '%' : 'N/A';
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
                        <h6 class="m-0 font-weight-bold text-primary">Dettaglio Corsi</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="coursesTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Corso</th>
                                        <th>Utenti Totali</th>
                                        <th>Completati</th>
                                        <th>In Corso</th>
                                        <th>Non Iniziati</th>
                                        <th>Tasso Completamento</th>
                                        <th>Azioni</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($courseStats as $course)
                                    <tr>
                                        <td>{{ $course['title'] }}</td>
                                        <td>{{ $course['total_users'] }}</td>
                                        <td>{{ $course['completed_users'] }}</td>
                                        <td>{{ $course['in_progress_users'] }}</td>
                                        <td>{{ $course['not_started_users'] }}</td>
                                        <td>
                                            <div class="progress">
                                                <div class="progress-bar bg-{{ $course['completion_rate'] > 75 ? 'success' : ($course['completion_rate'] > 50 ? 'info' : ($course['completion_rate'] > 25 ? 'warning' : 'danger')) }}"
                                                    role="progressbar"
                                                    style="width: {{ $course['completion_rate'] }}%"
                                                    aria-valuenow="{{ $course['completion_rate'] }}"
                                                    aria-valuemin="0"
                                                    aria-valuemax="100">
                                                    {{ $course['completion_rate'] }}%
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.courses.show', $course['id']) }}" class="btn btn-sm btn-primary">
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

        <!-- Grafico di confronto tempo -->
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Completamento Corsi nel Tempo</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="courseTimeChart" style="max-height: 400px;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-slot name="scripts">
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            // Configurazione grafico completamento corsi
            const courseLabels = [
                @foreach($courseStats as $course)
                    '{{ $course['title'] }}',
                @endforeach
            ];

            const completedData = [
                @foreach($courseStats as $course)
                    {{ $course['completed_users'] }},
                @endforeach
            ];

            const inProgressData = [
                @foreach($courseStats as $course)
                    {{ $course['in_progress_users'] }},
                @endforeach
            ];

            const notStartedData = [
                @foreach($courseStats as $course)
                    {{ $course['not_started_users'] }},
                @endforeach
            ];

            // Grafico a barre completamento corsi
            new Chart(document.getElementById('courseCompletionChart'), {
                type: 'bar',
                data: {
                    labels: courseLabels,
                    datasets: [
                        {
                            label: 'Completati',
                            data: completedData,
                            backgroundColor: 'rgba(40, 167, 69, 0.8)',
                            borderColor: 'rgba(40, 167, 69, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'In corso',
                            data: inProgressData,
                            backgroundColor: 'rgba(255, 193, 7, 0.8)',
                            borderColor: 'rgba(255, 193, 7, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Non iniziati',
                            data: notStartedData,
                            backgroundColor: 'rgba(220, 53, 69, 0.8)',
                            borderColor: 'rgba(220, 53, 69, 1)',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
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

            // Simulazione dati per grafico completamento nel tempo (ultimi 6 mesi)
            const months = ['Gen', 'Feb', 'Mar', 'Apr', 'Mag', 'Giu'];
            const courseTimeData = {
                @foreach($courseStats as $index => $course)
                    @if($index < 5) // Limita a 5 corsi per leggibilità
                    '{{ $course['title'] }}': [
                        Math.floor(Math.random() * 10),
                        Math.floor(Math.random() * 10),
                        Math.floor(Math.random() * 10),
                        Math.floor(Math.random() * 10),
                        Math.floor(Math.random() * 10),
                        Math.floor(Math.random() * 10)
                    ],
                    @endif
                @endforeach
            };

            // Genera dataset per ogni corso
            const timeDatasets = [];
            const colors = [
                'rgba(78, 115, 223, 1)',
                'rgba(28, 200, 138, 1)',
                'rgba(246, 194, 62, 1)',
                'rgba(231, 74, 59, 1)',
                'rgba(54, 185, 204, 1)'
            ];

            let colorIndex = 0;
            for (const [course, data] of Object.entries(courseTimeData)) {
                timeDatasets.push({
                    label: course,
                    data: data,
                    fill: false,
                    borderColor: colors[colorIndex],
                    tension: 0.1
                });
                colorIndex = (colorIndex + 1) % colors.length;
            }

            // Grafico lineare completamenti nel tempo
            new Chart(document.getElementById('courseTimeChart'), {
                type: 'line',
                data: {
                    labels: months,
                    datasets: timeDatasets
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Completamenti mensili'
                            }
                        }
                    }
                }
            });

            // Inizializzazione datatable
            $(document).ready(function() {
                $('#coursesTable').DataTable({
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Italian.json'
                    }
                });
            });
        </script>
    </x-slot>
</x-layout>
