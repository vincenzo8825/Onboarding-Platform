<x-layout>
    <x-slot name="sidebar">
        @include('components.sidebar')
    </x-slot>

    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 fw-bold text-gray-800">Statistiche e Report</h1>
        </div>

        <!-- Dashboard Analytics Grid -->
        <div class="report-dashboard">
            <!-- Reports Categories Section -->
            <div class="row g-4 mb-5">
                <!-- Main Reports -->
                <div class="col-12 col-xl-8">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-gradient-primary-to-secondary p-4">
                            <h4 class="card-title mb-0 text-white fw-bold">
                                <i class="fas fa-chart-line me-2"></i>Report Principali
                            </h4>
                            <p class="text-white-50 mb-0 mt-2">
                                Seleziona un report da visualizzare o esportare
                            </p>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-4">
                                <!-- Dashboard Stats -->
                                <div class="col-md-6">
                                    <div class="report-item d-flex align-items-center p-3 rounded-3 border">
                                        <div class="report-icon bg-primary-soft rounded-circle p-3 me-3">
                                            <i class="fas fa-chart-line text-primary fa-lg"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="fw-bold mb-1">Dashboard Statistiche</h5>
                                            <p class="text-muted small mb-2">Panoramica completa sui dati</p>
                                            <a href="{{ route('admin.reports.index') }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye me-1"></i> Visualizza
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <!-- Onboarding Progress -->
                                <div class="col-md-6">
                                    <div class="report-item d-flex align-items-center p-3 rounded-3 border">
                                        <div class="report-icon bg-success-soft rounded-circle p-3 me-3">
                                            <i class="fas fa-users text-success fa-lg"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="fw-bold mb-1">Progresso Onboarding</h5>
                                            <p class="text-muted small mb-2">Monitoraggio avanzamento</p>
                                            <a href="{{ route('admin.reports.onboarding-progress') }}" class="btn btn-sm btn-success">
                                                <i class="fas fa-eye me-1"></i> Visualizza
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <!-- Department Stats -->
                                <div class="col-md-6">
                                    <div class="report-item d-flex align-items-center p-3 rounded-3 border">
                                        <div class="report-icon bg-info-soft rounded-circle p-3 me-3">
                                            <i class="fas fa-building text-info fa-lg"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="fw-bold mb-1">Statistiche Dipartimenti</h5>
                                            <p class="text-muted small mb-2">Performance per dipartimento</p>
                                            <a href="{{ route('admin.reports.department-stats') }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye me-1"></i> Visualizza
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <!-- Course Stats -->
                                <div class="col-md-6">
                                    <div class="report-item d-flex align-items-center p-3 rounded-3 border">
                                        <div class="report-icon bg-warning-soft rounded-circle p-3 me-3">
                                            <i class="fas fa-book text-warning fa-lg"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="fw-bold mb-1">Statistiche Corsi</h5>
                                            <p class="text-muted small mb-2">Analisi corsi e completamenti</p>
                                            <a href="{{ route('admin.reports.course-stats') }}" class="btn btn-sm btn-warning">
                                                <i class="fas fa-eye me-1"></i> Visualizza
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <!-- Quiz Stats -->
                                <div class="col-md-6">
                                    <div class="report-item d-flex align-items-center p-3 rounded-3 border">
                                        <div class="report-icon bg-danger-soft rounded-circle p-3 me-3">
                                            <i class="fas fa-question-circle text-danger fa-lg"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="fw-bold mb-1">Statistiche Quiz</h5>
                                            <p class="text-muted small mb-2">Risultati e performance</p>
                                            <a href="{{ route('admin.reports.quiz-stats') }}" class="btn btn-sm btn-danger">
                                                <i class="fas fa-eye me-1"></i> Visualizza
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <!-- Monthly Trends -->
                                <div class="col-md-6">
                                    <div class="report-item d-flex align-items-center p-3 rounded-3 border">
                                        <div class="report-icon bg-primary-soft rounded-circle p-3 me-3">
                                            <i class="fas fa-calendar-alt text-primary fa-lg"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="fw-bold mb-1">Trend Mensili</h5>
                                            <p class="text-muted small mb-2">Analisi temporale dei trend</p>
                                            <a href="{{ route('admin.reports.monthly-trends') }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye me-1"></i> Visualizza
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <!-- Ticket Stats -->
                                <div class="col-md-6">
                                    <div class="report-item d-flex align-items-center p-3 rounded-3 border">
                                        <div class="report-icon bg-secondary-soft rounded-circle p-3 me-3">
                                            <i class="fas fa-ticket-alt text-secondary fa-lg"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="fw-bold mb-1">Statistiche Ticket</h5>
                                            <p class="text-muted small mb-2">Analisi ticket e risposte</p>
                                            <a href="{{ route('admin.reports.ticket-stats') }}" class="btn btn-sm btn-secondary">
                                                <i class="fas fa-eye me-1"></i> Visualizza
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebars -->
                <div class="col-12 col-xl-4">
                    <div class="row g-4 h-100">
                        <!-- Export Options -->
                        <div class="col-md-6 col-xl-12">
                            <div class="card shadow-sm border-0 h-100">
                                <div class="card-header bg-gradient-dark p-3">
                                    <h5 class="card-title mb-0 text-white fw-bold">
                                        <i class="fas fa-file-export me-2"></i>Esportazione
                                    </h5>
                                </div>
                                <div class="card-body p-4">
                                    <p class="text-muted mb-4">Esporta statistiche e report nei formati più comuni per le presentazioni o analisi offline.</p>

                                    <div class="d-grid gap-3">
                                        <a href="#" class="btn btn-outline-success d-flex align-items-center">
                                            <i class="far fa-file-excel me-2 fa-lg"></i>
                                            <div>
                                                <span class="d-block fw-bold">Esporta Excel</span>
                                                <small class="text-muted">Per analisi avanzate</small>
                                            </div>
                                        </a>

                                        <a href="#" class="btn btn-outline-danger d-flex align-items-center">
                                            <i class="far fa-file-pdf me-2 fa-lg"></i>
                                            <div>
                                                <span class="d-block fw-bold">Esporta PDF</span>
                                                <small class="text-muted">Per presentazioni</small>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Automatic Reports -->
                        <div class="col-md-6 col-xl-12">
                            <div class="card shadow-sm border-0 h-100">
                                <div class="card-header bg-gradient-dark p-3">
                                    <h5 class="card-title mb-0 text-white fw-bold">
                                        <i class="fas fa-cog me-2"></i>Report Automatici
                                    </h5>
                                </div>
                                <div class="card-body p-4">
                                    <p class="text-muted mb-4">Configura l'invio automatico di report periodici agli stakeholder attraverso email o notifiche.</p>

                                    <div class="d-grid">
                                        <a href="#" class="btn btn-primary d-flex align-items-center justify-content-center">
                                            <i class="fas fa-cog me-2"></i>
                                            <span>Configura Report Automatici</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Custom gradients */
        .bg-gradient-primary-to-secondary {
            background: linear-gradient(135deg, var(--bs-primary) 0%, #4361ee 100%);
        }

        .bg-gradient-dark {
            background: linear-gradient(135deg, #2d3748 0%, #1a202c 100%);
        }

        /* Soft background colors */
        .bg-primary-soft {
            background-color: rgba(13, 110, 253, 0.15);
        }

        .bg-success-soft {
            background-color: rgba(25, 135, 84, 0.15);
        }

        .bg-info-soft {
            background-color: rgba(13, 202, 240, 0.15);
        }

        .bg-warning-soft {
            background-color: rgba(255, 193, 7, 0.15);
        }

        .bg-danger-soft {
            background-color: rgba(220, 53, 69, 0.15);
        }

        .bg-secondary-soft {
            background-color: rgba(108, 117, 125, 0.15);
        }

        /* Report items */
        .report-item {
            transition: all 0.3s ease;
            border-color: rgba(0, 0, 0, 0.05);
        }

        .report-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 .5rem 1rem rgba(0,0,0,.1);
            border-color: rgba(0, 0, 0, 0.08);
        }

        .report-icon {
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Card styles */
        .card {
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 1rem 3rem rgba(0,0,0,.175);
        }
    </style>
</x-layout>
