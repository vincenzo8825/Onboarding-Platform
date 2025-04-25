<x-layout>
    <x-slot name="sidebar">
        @include('components.sidebar')
    </x-slot>

    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">Trend Mensili</h1>

        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Andamento Mensile</h6>
                        <a href="{{ route('admin.reports.index') }}" class="btn btn-sm btn-secondary">
                            Torna alla Dashboard
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <canvas id="monthlyTrendChart" style="max-height: 400px;"></canvas>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-md-4">
                                <div class="card mb-3 border-left-primary">
                                    <div class="card-body py-3">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                    Mese con Più Completamenti
                                                </div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                    @php
                                                        $maxCompletions = 0;
                                                        $maxMonth = 'N/A';
                                                        foreach($completions as $index => $count) {
                                                            if($count > $maxCompletions) {
                                                                $maxCompletions = $count;
                                                                $maxMonth = $monthLabels[$index] ?? 'N/A';
                                                            }
                                                        }
                                                        echo $maxMonth . ' (' . $maxCompletions . ')';
                                                    @endphp
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card mb-3 border-left-success">
                                    <div class="card-body py-3">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                    Media Mensile Completamenti
                                                </div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                    @php
                                                        $totalCompletions = array_sum($completions);
                                                        $monthCount = count($completions);
                                                        echo $monthCount > 0 ? round($totalCompletions / $monthCount, 1) : 'N/A';
                                                    @endphp
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card mb-3 border-left-info">
                                    <div class="card-body py-3">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                    Trend Attuale
                                                </div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                    @php
                                                        $lastMonth = end($completions);
                                                        $prevMonth = prev($completions);
                                                        $trend = $prevMonth > 0 ? (($lastMonth - $prevMonth) / $prevMonth) * 100 : 0;
                                                        $trendSymbol = $trend >= 0 ? '↑' : '↓';
                                                        $trendClass = $trend >= 0 ? 'text-success' : 'text-danger';
                                                        echo '<span class="' . $trendClass . '">' . $trendSymbol . ' ' . abs(round($trend, 1)) . '%</span>';
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
            <!-- Confronto con nuovi dipendenti -->
            <div class="col-md-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Completamenti vs Nuovi Dipendenti</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="comparisonChart" style="max-height: 400px;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Dettaglio Mensile</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="monthlyTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Mese</th>
                                        <th>Completamenti</th>
                                        <th>Nuovi Dipendenti</th>
                                        <th>Rapporto</th>
                                        <th>Variazione rispetto al mese precedente</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($monthLabels as $index => $month)
                                    <tr>
                                        <td>{{ $month }}</td>
                                        <td>{{ $completions[$index] }}</td>
                                        <td>{{ $newHires[$index] ?? 0 }}</td>
                                        <td>
                                            @php
                                                $ratio = $newHires[$index] > 0 ? $completions[$index] / $newHires[$index] : 0;
                                                echo round($ratio, 2);
                                            @endphp
                                        </td>
                                        <td>
                                            @php
                                                if($index > 0) {
                                                    $prevValue = $completions[$index-1];
                                                    $currentValue = $completions[$index];
                                                    $change = $prevValue > 0 ? (($currentValue - $prevValue) / $prevValue) * 100 : 0;
                                                    $changeSymbol = $change >= 0 ? '↑' : '↓';
                                                    $changeClass = $change >= 0 ? 'text-success' : 'text-danger';
                                                    echo '<span class="' . $changeClass . '">' . $changeSymbol . ' ' . abs(round($change, 1)) . '%</span>';
                                                } else {
                                                    echo 'N/A';
                                                }
                                            @endphp
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
            // Configurazione grafico trend mensile
            const monthlyLabels = @json($monthLabels);
            const completionsData = @json($completions);
            const newHiresData = @json($newHires ?? array_fill(0, count($monthLabels), 0));

            // Grafico trend mensile
            new Chart(document.getElementById('monthlyTrendChart'), {
                type: 'line',
                data: {
                    labels: monthlyLabels,
                    datasets: [{
                        label: 'Completamenti',
                        data: completionsData,
                        fill: false,
                        borderColor: 'rgba(78, 115, 223, 1)',
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Numero di completamenti'
                            }
                        }
                    }
                }
            });

            // Grafico confronto
            new Chart(document.getElementById('comparisonChart'), {
                type: 'bar',
                data: {
                    labels: monthlyLabels,
                    datasets: [
                        {
                            label: 'Completamenti',
                            data: completionsData,
                            backgroundColor: 'rgba(78, 115, 223, 0.8)',
                            borderColor: 'rgba(78, 115, 223, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Nuovi dipendenti',
                            data: newHiresData,
                            backgroundColor: 'rgba(28, 200, 138, 0.8)',
                            borderColor: 'rgba(28, 200, 138, 1)',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Inizializzazione datatable
            $(document).ready(function() {
                $('#monthlyTable').DataTable({
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Italian.json'
                    },
                    order: [[0, 'desc']]
                });
            });
        </script>
    </x-slot>
</x-layout>
