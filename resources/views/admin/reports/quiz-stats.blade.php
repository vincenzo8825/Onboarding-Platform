<x-layout>
    <x-slot name="sidebar">
        @include('components.sidebar')
    </x-slot>

    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">Statistiche Quiz</h1>

        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Riepilogo Quiz</h6>
                        <a href="{{ route('admin.reports.index') }}" class="btn btn-sm btn-secondary">
                            Torna alla Dashboard
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <canvas id="quizPassRateChart" style="max-height: 400px;"></canvas>
                            </div>
                            <div class="col-md-4">
                                <div class="card mb-3 border-left-success">
                                    <div class="card-body py-3">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                    Quiz con Miglior Tasso di Superamento
                                                </div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                    @php
                                                        $maxPassRate = 0;
                                                        $topQuiz = 'N/A';
                                                        foreach($quizStats as $quiz) {
                                                            if($quiz['pass_rate'] > $maxPassRate && $quiz['attempts'] > 0) {
                                                                $maxPassRate = $quiz['pass_rate'];
                                                                $topQuiz = $quiz['quiz_name'];
                                                            }
                                                        }
                                                        echo $topQuiz . ' (' . $maxPassRate . '%)';
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
                                                    Quiz con Peggior Tasso di Superamento
                                                </div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                    @php
                                                        $minPassRate = 100;
                                                        $worstQuiz = 'N/A';
                                                        foreach($quizStats as $quiz) {
                                                            if($quiz['pass_rate'] < $minPassRate && $quiz['attempts'] > 0) {
                                                                $minPassRate = $quiz['pass_rate'];
                                                                $worstQuiz = $quiz['quiz_name'];
                                                            }
                                                        }
                                                        echo $worstQuiz . ' (' . $minPassRate . '%)';
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
                                                    Punteggio Medio Globale
                                                </div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                    @php
                                                        $totalScore = 0;
                                                        $totalQuizzes = 0;
                                                        foreach($quizStats as $quiz) {
                                                            if($quiz['attempts'] > 0) {
                                                                $totalScore += $quiz['avg_score'];
                                                                $totalQuizzes++;
                                                            }
                                                        }
                                                        echo $totalQuizzes > 0 ? round($totalScore / $totalQuizzes, 1) . ' / 100' : 'N/A';
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
            <!-- Punteggi medi e tentativi -->
            <div class="col-md-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Punteggi Medi</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="quizScoreChart" style="max-height: 350px;"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Tentativi per Quiz</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="quizAttemptsChart" style="max-height: 350px;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Dettaglio Quiz</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="quizTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Quiz</th>
                                        <th>Punteggio Medio</th>
                                        <th>Tentativi</th>
                                        <th>Tasso di Superamento</th>
                                        <th>Difficoltà Stimata</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($quizStats as $quiz)
                                    <tr>
                                        <td>{{ $quiz['quiz_name'] }}</td>
                                        <td>{{ $quiz['avg_score'] }} / 100</td>
                                        <td>{{ $quiz['attempts'] }}</td>
                                        <td>
                                            <div class="progress">
                                                <div class="progress-bar bg-{{ $quiz['pass_rate'] > 75 ? 'success' : ($quiz['pass_rate'] > 50 ? 'info' : ($quiz['pass_rate'] > 25 ? 'warning' : 'danger')) }}"
                                                    role="progressbar"
                                                    style="width: {{ $quiz['pass_rate'] }}%"
                                                    aria-valuenow="{{ $quiz['pass_rate'] }}"
                                                    aria-valuemin="0"
                                                    aria-valuemax="100">
                                                    {{ $quiz['pass_rate'] }}%
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @php
                                                $difficulty = '';
                                                $badgeClass = '';

                                                if($quiz['pass_rate'] >= 90) {
                                                    $difficulty = 'Facile';
                                                    $badgeClass = 'success';
                                                } elseif($quiz['pass_rate'] >= 70) {
                                                    $difficulty = 'Moderato';
                                                    $badgeClass = 'info';
                                                } elseif($quiz['pass_rate'] >= 40) {
                                                    $difficulty = 'Impegnativo';
                                                    $badgeClass = 'warning';
                                                } else {
                                                    $difficulty = 'Difficile';
                                                    $badgeClass = 'danger';
                                                }
                                            @endphp
                                            <span class="badge badge-{{ $badgeClass }}">{{ $difficulty }}</span>
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
            // Dati per i grafici
            const quizLabels = [
                @foreach($quizStats as $quiz)
                    '{{ $quiz['quiz_name'] }}',
                @endforeach
            ];

            const passRateData = [
                @foreach($quizStats as $quiz)
                    {{ $quiz['pass_rate'] }},
                @endforeach
            ];

            const avgScoreData = [
                @foreach($quizStats as $quiz)
                    {{ $quiz['avg_score'] }},
                @endforeach
            ];

            const attemptsData = [
                @foreach($quizStats as $quiz)
                    {{ $quiz['attempts'] }},
                @endforeach
            ];

            // Grafico tasso di superamento
            new Chart(document.getElementById('quizPassRateChart'), {
                type: 'bar',
                data: {
                    labels: quizLabels,
                    datasets: [{
                        label: 'Tasso di Superamento (%)',
                        data: passRateData,
                        backgroundColor: 'rgba(78, 115, 223, 0.8)',
                        borderColor: 'rgba(78, 115, 223, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
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

            // Grafico punteggi medi
            new Chart(document.getElementById('quizScoreChart'), {
                type: 'bar',
                data: {
                    labels: quizLabels,
                    datasets: [{
                        label: 'Punteggio Medio',
                        data: avgScoreData,
                        backgroundColor: 'rgba(28, 200, 138, 0.8)',
                        borderColor: 'rgba(28, 200, 138, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100
                        }
                    }
                }
            });

            // Grafico tentativi
            new Chart(document.getElementById('quizAttemptsChart'), {
                type: 'doughnut',
                data: {
                    labels: quizLabels,
                    datasets: [{
                        data: attemptsData,
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
                    plugins: {
                        legend: {
                            position: 'right'
                        }
                    }
                }
            });

            // Inizializzazione datatable
            $(document).ready(function() {
                $('#quizTable').DataTable({
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Italian.json'
                    }
                });
            });
        </script>
    </x-slot>
</x-layout>
