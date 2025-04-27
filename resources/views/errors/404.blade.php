<x-layout>
    <x-slot name="styles">
        @vite('resources/css/pages/error.css')
    </x-slot>
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8 text-center">
                <div class="mb-4">
                    <img src="https://cdn.pixabay.com/photo/2017/01/31/16/46/lost-2025863_1280.png" alt="404" class="img-fluid" style="max-height: 300px;">
                </div>

                <h1 class="display-4 mb-3 fw-bold">404</h1>
                <h2 class="h3 mb-4">Ops! Pagina non trovata</h2>

                <p class="lead text-muted mb-4">
                    La pagina che stai cercando potrebbe essere stata rimossa, rinominata o temporaneamente non disponibile.
                </p>

                <div class="mb-5 d-flex flex-column flex-md-row justify-content-center">
                    <a href="{{ url('/') }}" class="btn btn-primary btn-lg mb-3 mb-md-0 me-md-3">
                        <i class="fas fa-home me-2"></i> Torna alla Home
                    </a>
                    <button onclick="history.back()" class="btn btn-outline-secondary btn-lg">
                        <i class="fas fa-arrow-left me-2"></i> Torna indietro
                    </button>
                </div>

                <div class="card mt-5">
                    <div class="card-body">
                        <h5 class="mb-3">Prova a:</h5>
                        <ul class="list-unstyled text-start">
                            <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Verificare che l'URL sia stato digitato correttamente</li>
                            <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Cancellare la cache del browser e provare a ricaricare la pagina</li>
                            <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Navigare tramite il menu principale</li>
                            <li><i class="fas fa-check-circle text-success me-2"></i> Contattare l'amministratore di sistema se il problema persiste</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>
