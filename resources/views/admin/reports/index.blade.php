<x-layout>
    <x-slot name="sidebar">
        @include('components.sidebar')
    </x-slot>

    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">Statistiche e Report</h1>

        <div class="row">
            <div class="col-md-12 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Dashboard e Report</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <a href="{{ route('admin.reports.index') }}" class="btn btn-primary btn-block">
                                    <i class="fas fa-chart-line mr-2"></i> Dashboard Statistiche
                                </a>
                            </div>
                            <div class="col-md-4 mb-3">
                                <a href="{{ route('admin.reports.onboarding-progress') }}" class="btn btn-primary btn-block">
                                    <i class="fas fa-users mr-2"></i> Progresso Onboarding
                                </a>
                            </div>
                            <div class="col-md-4 mb-3">
                                <a href="{{ route('admin.reports.department-stats') }}" class="btn btn-primary btn-block">
                                    <i class="fas fa-building mr-2"></i> Statistiche Dipartimenti
                                </a>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <a href="{{ route('admin.reports.course-stats') }}" class="btn btn-primary btn-block">
                                    <i class="fas fa-book mr-2"></i> Statistiche Corsi
                                </a>
                            </div>
                            <div class="col-md-4 mb-3">
                                <a href="{{ route('admin.reports.quiz-stats') }}" class="btn btn-primary btn-block">
                                    <i class="fas fa-question-circle mr-2"></i> Statistiche Quiz
                                </a>
                            </div>
                            <div class="col-md-4 mb-3">
                                <a href="{{ route('admin.reports.monthly-trends') }}" class="btn btn-primary btn-block">
                                    <i class="fas fa-calendar-alt mr-2"></i> Trend Mensili
                                </a>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <a href="{{ route('admin.reports.ticket-stats') }}" class="btn btn-primary btn-block">
                                    <i class="fas fa-ticket-alt mr-2"></i> Statistiche Ticket
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Esportazione Report</h6>
                    </div>
                    <div class="card-body">
                        <p>Esporta statistiche in diversi formati:</p>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <a href="#" class="btn btn-outline-primary btn-block">
                                    <i class="far fa-file-excel mr-2"></i> Esporta Excel
                                </a>
                            </div>
                            <div class="col-md-6 mb-3">
                                <a href="#" class="btn btn-outline-primary btn-block">
                                    <i class="far fa-file-pdf mr-2"></i> Esporta PDF
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Report Schedulati</h6>
                    </div>
                    <div class="card-body">
                        <p>Configura l'invio automatico di report periodici:</p>
                        <a href="#" class="btn btn-outline-primary btn-block">
                            <i class="fas fa-cog mr-2"></i> Configura Report Automatici
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>
