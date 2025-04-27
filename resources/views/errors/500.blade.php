<x-layout>
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8 text-center">
                <div class="mb-4">
                    <img src="https://cdn.pixabay.com/photo/2021/07/12/17/02/maintenance-6409003_1280.png" alt="500" class="img-fluid" style="max-height: 300px;">
                </div>

                <h1 class="display-4 mb-3 fw-bold">500</h1>
                <h2 class="h3 mb-4">Errore interno del server</h2>

                <p class="lead text-muted mb-4">
                    Si è verificato un errore interno del server. Il nostro team tecnico è stato automaticamente avvisato e sta lavorando per risolvere il problema.
                </p>

                <div class="mb-5 d-flex flex-column flex-md-row justify-content-center">
                    <a href="{{ url('/') }}" class="btn btn-primary btn-lg mb-3 mb-md-0 me-md-3">
                        <i class="fas fa-home me-2"></i> Torna alla Home
                    </a>
                    <button onclick="location.reload()" class="btn btn-outline-secondary btn-lg">
                        <i class="fas fa-sync-alt me-2"></i> Ricarica la pagina
                    </button>
                </div>

                <div class="alert alert-info d-inline-flex align-items-center mb-5" role="alert">
                    <i class="fas fa-info-circle me-2 fa-lg"></i>
                    <div class="text-start">
                        Puoi provare a ricaricare la pagina tra qualche minuto. Se il problema persiste, ti preghiamo di contattare il supporto tecnico.
                    </div>
                </div>

                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Contatta il Supporto Tecnico</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <i class="fas fa-envelope fa-2x text-primary"></i>
                                    </div>
                                    <div class="text-start">
                                        <h6 class="mb-1">Via Email</h6>
                                        <a href="mailto:support@onboardingplatform.com" class="text-decoration-none">support@onboardingplatform.com</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <i class="fas fa-phone-alt fa-2x text-primary"></i>
                                    </div>
                                    <div class="text-start">
                                        <h6 class="mb-1">Via Telefono</h6>
                                        <a href="tel:+390123456789" class="text-decoration-none">+39 01 2345 6789</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <p class="mt-5 text-muted small">
                    Codice errore: {{ $exception->getStatusCode() ?? '500' }} • Riferimento: {{ now()->format('YmdHis') }}-{{ Str::random(6) }}
                </p>
            </div>
        </div>
    </div>
</x-layout>
