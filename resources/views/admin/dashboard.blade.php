<x-layout>
    <x-slot name="styles">
        @vite('resources/css/pages/admin.css')
    </x-slot>
    <x-slot name="sidebar">
        @include('components.sidebar')
    </x-slot>

    <div class="admin-dashboard container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="admin-dashboard__title">Dashboard Admin</h1>
            <div>
                <a href="{{ route('admin.approvals.index') }}" class="btn btn-primary">
                    <i class="fas fa-user-check me-1"></i> Gestione Approvazioni
                    @if($pendingApprovalCount > 0)
                        <span class="badge bg-danger ms-1">{{ $pendingApprovalCount }}</span>
                    @endif
                </a>
            </div>
        </div>

        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Sezione di benvenuto -->
        <div class="admin-dashboard__welcome mb-4">
            <div class="admin-dashboard__welcome-content">
                <h2 class="admin-dashboard__welcome-title">Benvenuto nella dashboard amministrativa</h2>
                <p class="admin-dashboard__welcome-text">Da qui puoi gestire tutti gli aspetti della piattaforma di onboarding, monitorare i progressi dei dipendenti e gestire le risorse formative.</p>
            </div>
        </div>

        <!-- Se ci sono utenti in attesa di approvazione, mostra un alert -->
        @if($pendingApprovalCount > 0)
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Attenzione!</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $pendingApprovalCount }} {{ $pendingApprovalCount == 1 ? 'utente' : 'utenti' }} in attesa di approvazione
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-user-clock fa-2x text-gray-300"></i>
                            </div>
                        </div>
                        <div class="mt-3"></div>
                            <a href="{{ route('admin.approvals.index') }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-check-circle me-1"></i> Gestisci approvazioni
                            </a>


                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Panoramica Cards -->
        <div class="admin-dashboard__stat-cards">
            <div class="stat-card border-left-primary">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Dipendenti</div>
                            <div class="h5 mb-0 font-weight-bold">{{ $employeeCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <a href="{{ route('admin.employees.index') }}" class="btn btn-sm btn-primary text-nowrap mt-3 w-100">Gestisci Dipendenti</a>
                </div>
            </div>

            <div class="stat-card border-left-success">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Corsi Formativi</div>
                            <div class="h5 mb-0 font-weight-bold">{{ \App\Models\Course::count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-graduation-cap fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <a href="{{ route('admin.courses.index') }}" class="btn btn-sm btn-success text-nowrap mt-3 w-100">Gestisci Corsi</a>
                </div>
            </div>

            <div class="stat-card border-left-info">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Ticket Attivi</div>
                            <div class="h5 mb-0 font-weight-bold">{{ $activeTicketCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-ticket-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <a href="{{ route('admin.tickets.index') }}" class="btn btn-sm btn-info text-nowrap mt-3 w-100">Visualizza Ticket</a>
                </div>
            </div>

            <div class="stat-card border-left-danger">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Richieste Documenti</div>
                            <div class="h5 mb-0 font-weight-bold">{{ \App\Models\DocumentRequest::where('status', 'pending')->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-upload fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <a href="{{ route('admin.document-requests.index') }}" class="btn btn-sm btn-danger text-nowrap mt-3 w-100">Gestisci Richieste</a>
                </div>
            </div>

            <div class="stat-card border-left-warning">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Eventi Programmati</div>
                            <div class="h5 mb-0 font-weight-bold">{{ \App\Models\Event::where('start_date', '>', now())->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <a href="{{ route('admin.events.index') }}" class="btn btn-sm btn-warning text-nowrap mt-3 w-100">Gestisci Eventi</a>
                </div>
            </div>
        </div>

        <!-- Grafici e Statistiche -->
        <div class="admin-dashboard__section">
            <div class="admin-dashboard__section-header">
                <h2 class="admin-dashboard__section-title">Analisi e Statistiche</h2>
            </div>

            <div class="row mb-4">
                <div class="col-lg-8 mb-4">
                    <div class="admin-dashboard__chart-container">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h3 class="admin-dashboard__chart-title">
                                <i class="fas fa-chart-line"></i> Stato Onboarding Dipendenti
                            </h3>
                            <div class="admin-dashboard__chart-filters">
                                <span class="admin-dashboard__chart-filter active" data-period="30">Ultimi 30 giorni</span>
                                <span class="admin-dashboard__chart-filter" data-period="7">Ultimi 7 giorni</span>
                                <span class="admin-dashboard__chart-filter" data-period="90">Ultimi 90 giorni</span>
                            </div>
                        </div>
                        <div class="chart-container" style="position: relative; height: 300px;">
                            <canvas id="onboardingChart"></canvas>
                        </div>

                        <div class="mt-4">
                            <h6 class="fw-bold">Stato Onboarding</h6>

                            @php
                                $allEmployees = \App\Models\User::whereHas('roles', function($q) {
                                    $q->whereIn('name', ['employee', 'new_hire']);
                                })->get();

                                $notStarted = 0;
                                $inProgress = 0;
                                $completed = 0;

                                foreach ($allEmployees as $employee) {
                                    $coursesAssigned = $employee->courses()->count();
                                    if ($coursesAssigned == 0) {
                                        $notStarted++;
                                        continue;
                                    }

                                    $coursesCompleted = $employee->courses()->wherePivot('status', 'completed')->count();
                                    if ($coursesCompleted == $coursesAssigned) {
                                        $completed++;
                                    } else {
                                        $inProgress++;
                                    }
                                }

                                $totalEmployees = count($allEmployees);
                                $notStartedPercentage = $totalEmployees > 0 ? round(($notStarted / $totalEmployees) * 100) : 0;
                                $inProgressPercentage = $totalEmployees > 0 ? round(($inProgress / $totalEmployees) * 100) : 0;
                                $completedPercentage = $totalEmployees > 0 ? round(($completed / $totalEmployees) * 100) : 0;
                            @endphp

                            <div class="admin-dashboard__metric-cards">
                                <div class="admin-dashboard__metric-card">
                                    <div class="admin-dashboard__metric-value text-danger">{{ $notStarted }}</div>
                                    <div class="admin-dashboard__metric-label">Non iniziati</div>
                                    <div class="admin-dashboard__metric-progress">
                                        <div class="admin-dashboard__metric-progress-bar bg-danger" style="width: {{ $notStartedPercentage }}%"></div>
                                    </div>
                                </div>
                                <div class="admin-dashboard__metric-card">
                                    <div class="admin-dashboard__metric-value text-warning">{{ $inProgress }}</div>
                                    <div class="admin-dashboard__metric-label">In corso</div>
                                    <div class="admin-dashboard__metric-progress">
                                        <div class="admin-dashboard__metric-progress-bar bg-warning" style="width: {{ $inProgressPercentage }}%"></div>
                                    </div>
                                </div>
                                <div class="admin-dashboard__metric-card">
                                    <div class="admin-dashboard__metric-value text-success">{{ $completed }}</div>
                                    <div class="admin-dashboard__metric-label">Completati</div>
                                    <div class="admin-dashboard__metric-progress">
                                        <div class="admin-dashboard__metric-progress-bar bg-success" style="width: {{ $completedPercentage }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 mb-4">
                    <div class="admin-dashboard__chart-container">
                        <h3 class="admin-dashboard__chart-title">
                            <i class="fas fa-users"></i> Stato Dipendenti
                        </h3>
                        <div class="chart-container" style="position: relative; height: 260px;">
                            <canvas id="employeeStatusChart"></canvas>
                        </div>

                        @php
                            $activeUsers = \App\Models\User::whereHas('roles', function($q) {
                                $q->whereIn('name', ['employee', 'new_hire']);
                            })->where('status', 'active')->count();

                            $pendingUsers = \App\Models\User::whereHas('roles', function($q) {
                                $q->whereIn('name', ['employee', 'new_hire']);
                            })->where('status', 'pending')->count();

                            $blockedUsers = \App\Models\User::whereHas('roles', function($q) {
                                $q->whereIn('name', ['employee', 'new_hire']);
                            })->where('status', 'blocked')->count();
                        @endphp

                        <div class="mt-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span><i class="fas fa-circle text-success"></i> Attivi</span>
                                <span>{{ $activeUsers }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span><i class="fas fa-circle text-warning"></i> In attesa</span>
                                <span>{{ $pendingUsers }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span><i class="fas fa-circle text-danger"></i> Bloccati</span>
                                <span>{{ $blockedUsers }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sezione Attività Recenti -->
        <div class="admin-dashboard__section">
            <div class="admin-dashboard__section-header">
                <h2 class="admin-dashboard__section-title">Attività Recenti</h2>
            </div>

            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="admin-dashboard__recent-section">
                        <div class="admin-dashboard__recent-header">
                            <h3 class="admin-dashboard__recent-title">
                                <i class="fas fa-user-plus"></i> Ultimi Dipendenti Aggiunti
                            </h3>
                            <a href="{{ route('admin.employees.index') }}" class="btn btn-sm btn-outline-primary">Vedi tutti</a>
                        </div>
                        <div class="admin-dashboard__recent-content">
                            @php
                                $recentEmployees = \App\Models\User::whereHas('roles', function($q) {
                                    $q->whereIn('name', ['employee', 'new_hire']);
                                })->orderBy('created_at', 'desc')->take(5)->get();
                            @endphp

                            @forelse($recentEmployees as $employee)
                                <div class="admin-dashboard__item">
                                    <div class="admin-dashboard__item-icon">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div class="admin-dashboard__item-content">
                                        <h4 class="admin-dashboard__item-title">{{ $employee->name }}</h4>
                                        <p class="admin-dashboard__item-subtitle">{{ $employee->email }}</p>
                                        <div class="admin-dashboard__item-meta">
                                            <span class="admin-dashboard__item-meta-item">
                                                <i class="far fa-clock"></i> {{ $employee->created_at->diffForHumans() }}
                                            </span>
                                        </div>
                                    </div>
                                    <a href="{{ route('admin.employees.show', $employee) }}" class="admin-dashboard__item-action">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                </div>
                            @empty
                                <div class="p-4 text-center text-muted">
                                    Nessun dipendente aggiunto di recente.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <div class="admin-dashboard__recent-section">
                        <div class="admin-dashboard__recent-header">
                            <h3 class="admin-dashboard__recent-title">
                                <i class="fas fa-file-upload"></i> Richieste Documenti Recenti
                            </h3>
                            <a href="{{ route('admin.document-requests.index') }}" class="btn btn-sm btn-outline-primary">Vedi tutte</a>
                        </div>
                        <div class="admin-dashboard__recent-content">
                            @php
                                $recentDocumentRequests = \App\Models\DocumentRequest::with(['employee'])
                                    ->orderBy('created_at', 'desc')
                                    ->take(5)
                                    ->get();
                            @endphp

                            @forelse($recentDocumentRequests as $request)
                                <div class="admin-dashboard__item">
                                    <div class="admin-dashboard__item-icon">
                                        <i class="fas fa-file-upload"></i>
                                    </div>
                                    <div class="admin-dashboard__item-content">
                                        <h4 class="admin-dashboard__item-title">{{ $request->document_type }}</h4>
                                        <p class="admin-dashboard__item-subtitle">Richiesto a: {{ $request->employee->name }}</p>
                                        <div class="admin-dashboard__item-meta">
                                            <span class="admin-dashboard__item-meta-item">
                                                <i class="far fa-clock"></i> {{ $request->created_at->diffForHumans() }}
                                            </span>
                                            <span class="admin-dashboard__item-meta-item">
                                                <i class="fas fa-info-circle"></i>
                                                @if($request->status == 'pending')
                                                    <span class="text-warning">In attesa</span>
                                                @elseif($request->status == 'submitted')
                                                    <span class="text-info">Inviato</span>
                                                @elseif($request->status == 'approved')
                                                    <span class="text-success">Approvato</span>
                                                @elseif($request->status == 'rejected')
                                                    <span class="text-danger">Rifiutato</span>
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                    <a href="{{ route('admin.document-requests.show', $request) }}" class="admin-dashboard__item-action">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                </div>
                            @empty
                                <div class="p-4 text-center text-muted">
                                    Nessuna richiesta documenti recente.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <div class="admin-dashboard__recent-section">
                        <div class="admin-dashboard__recent-header">
                            <h3 class="admin-dashboard__recent-title">
                                <i class="fas fa-ticket-alt"></i> Ticket Recenti
                            </h3>
                            <a href="{{ route('admin.tickets.index') }}" class="btn btn-sm btn-outline-primary">Vedi tutti</a>
                        </div>
                        <div class="admin-dashboard__recent-content">
                            @php
                                $recentTickets = \App\Models\Ticket::whereIn('status', ['open', 'in_progress'])
                                    ->orderBy('created_at', 'desc')
                                    ->take(5)
                                    ->get();
                            @endphp

                            @forelse($recentTickets as $ticket)
                                <div class="admin-dashboard__item">
                                    <div class="admin-dashboard__item-icon">
                                        <i class="fas fa-ticket-alt"></i>
                                    </div>
                                    <div class="admin-dashboard__item-content">
                                        <h4 class="admin-dashboard__item-title">{{ $ticket->title }}</h4>
                                        <p class="admin-dashboard__item-subtitle">{{ Str::limit($ticket->description, 50) }}</p>
                                        <div class="admin-dashboard__item-meta">
                                            <span class="admin-dashboard__item-meta-item">
                                                <i class="far fa-clock"></i> {{ $ticket->created_at->diffForHumans() }}
                                            </span>
                                            <span class="admin-dashboard__item-meta-item">
                                                <i class="fas fa-user"></i> {{ $ticket->user->name }}
                                            </span>
                                        </div>
                                    </div>
                                    <a href="{{ route('admin.tickets.show', $ticket) }}" class="admin-dashboard__item-action">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                </div>
                            @empty
                                <div class="p-4 text-center text-muted">
                                    Nessun ticket recente.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-slot name="scripts">
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Grafico stato onboarding
                const onboardingCtx = document.getElementById('onboardingChart').getContext('2d');
                const onboardingChart = new Chart(onboardingCtx, {
                    type: 'line',
                    data: {
                        labels: ['Gen', 'Feb', 'Mar', 'Apr', 'Mag', 'Giu', 'Lug', 'Ago', 'Set', 'Ott', 'Nov', 'Dic'],
                        datasets: [{
                            label: 'Completati',
                            data: [12, 15, 18, 25, 30, 35, 38, 42, 45, 48, 50, 55],
                            backgroundColor: 'rgba(54, 179, 126, 0.1)',
                            borderColor: '#36B37E',
                            tension: 0.4,
                            borderWidth: 2,
                            fill: true
                        },
                        {
                            label: 'In corso',
                            data: [25, 23, 28, 24, 26, 25, 22, 20, 18, 16, 14, 12],
                            backgroundColor: 'rgba(255, 187, 51, 0.1)',
                            borderColor: '#FFBB33',
                            tension: 0.4,
                            borderWidth: 2,
                            fill: true
                        },
                        {
                            label: 'Non iniziati',
                            data: [40, 35, 32, 28, 25, 22, 20, 18, 15, 12, 10, 8],
                            backgroundColor: 'rgba(255, 87, 87, 0.1)',
                            borderColor: '#FF5757',
                            tension: 0.4,
                            borderWidth: 2,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: {
                                    usePointStyle: true,
                                    padding: 20,
                                    color: '#44476a'
                                }
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false,
                                titleColor: '#44476a',
                                backgroundColor: 'rgba(255, 255, 255, 0.9)',
                                bodyColor: '#44476a',
                                borderColor: 'rgba(0, 0, 0, 0.1)',
                                borderWidth: 1
                            }
                        },
                        hover: {
                            mode: 'nearest',
                            intersect: true
                        },
                        scales: {
                            x: {
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)'
                                },
                                ticks: {
                                    color: '#44476a'
                                }
                            },
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)'
                                },
                                ticks: {
                                    precision: 0,
                                    color: '#44476a'
                                }
                            }
                        }
                    }
                });

                // Grafico stato dipendenti
                const statusCtx = document.getElementById('employeeStatusChart').getContext('2d');
                const statusChart = new Chart(statusCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Attivi', 'In attesa', 'Bloccati'],
                        datasets: [{
                            data: [{{ $activeUsers }}, {{ $pendingUsers }}, {{ $blockedUsers }}],
                            backgroundColor: [
                                '#36B37E',
                                '#FFBB33',
                                '#FF5757'
                            ],
                            borderWidth: 0,
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '70%',
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: 'rgba(255, 255, 255, 0.9)',
                                titleColor: '#44476a',
                                bodyColor: '#44476a',
                                borderColor: 'rgba(0, 0, 0, 0.1)',
                                borderWidth: 1,
                                callbacks: {
                                    label: function(context) {
                                        const label = context.label || '';
                                        const value = context.raw || 0;
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = Math.round((value / total) * 100);
                                        return `${label}: ${value} (${percentage}%)`;
                                    }
                                }
                            }
                        }
                    }
                });

                // Gestione filtri del grafico
                document.querySelectorAll('.admin-dashboard__chart-filter').forEach(filter => {
                    filter.addEventListener('click', function() {
                        document.querySelectorAll('.admin-dashboard__chart-filter').forEach(f => {
                            f.classList.remove('active');
                        });
                        this.classList.add('active');

                        // Qui puoi implementare la logica per aggiornare i dati del grafico
                        // In base al periodo selezionato (7, 30 o 90 giorni)
                        const period = this.getAttribute('data-period');
                        console.log(`Selezionato periodo di ${period} giorni`);

                        // Esempio di aggiornamento dati (simulazione)
                        if (period === '7') {
                            updateChartData(onboardingChart,
                                [5, 8, 10, 15, 18, 20, 22],
                                [15, 12, 10, 8, 7, 5, 4],
                                [25, 20, 18, 17, 15, 12, 10]
                            );
                        } else if (period === '30') {
                            updateChartData(onboardingChart,
                                [12, 15, 18, 25, 30, 35, 38, 42, 45, 48, 50, 55],
                                [25, 23, 28, 24, 26, 25, 22, 20, 18, 16, 14, 12],
                                [40, 35, 32, 28, 25, 22, 20, 18, 15, 12, 10, 8]
                            );
                        } else if (period === '90') {
                            updateChartData(onboardingChart,
                                [10, 12, 15, 20, 25, 30, 35, 40, 45, 50, 55, 60],
                                [30, 28, 26, 25, 22, 20, 18, 15, 12, 10, 8, 6],
                                [45, 40, 35, 30, 28, 25, 22, 18, 15, 12, 10, 5]
                            );
                        }
                    });
                });

                // Funzione per aggiornare i dati del grafico
                function updateChartData(chart, completedData, inProgressData, notStartedData) {
                    chart.data.datasets[0].data = completedData;
                    chart.data.datasets[1].data = inProgressData;
                    chart.data.datasets[2].data = notStartedData;
                    chart.update();
                }
            });
        </script>
    </x-slot>
</x-layout>

