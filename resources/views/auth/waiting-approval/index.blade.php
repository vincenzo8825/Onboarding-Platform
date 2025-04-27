<x-layout>
    <x-slot name="sidebar">
        @include('components.sidebar')
    </x-slot>
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-warning text-dark">
                        <h4 class="mb-0">{{ __('Account in attesa di approvazione') }}</h4>
                    </div>

                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <div class="mb-4">
                                <i class="fas fa-user-clock fa-4x text-warning"></i>
                            </div>
                            <h5 class="mb-3">{{ __('Il tuo account è in attesa di approvazione') }}</h5>
                            <p class="text-muted mb-4">
                                {{ __('Grazie per esserti registrato! Il tuo account è stato creato con successo, ma deve ancora essere approvato da un amministratore.') }}
                                {{ __('Riceverai una notifica email quando il tuo account sarà stato approvato.') }}
                            </p>
                        </div>

                        <div class="alert alert-info d-flex align-items-center" role="alert">
                            <i class="fas fa-info-circle me-2"></i>
                            <div>
                                {{ __('Il tempo medio di approvazione è di 24-48 ore lavorative.') }}
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="text-center">
                            <h6 class="mb-3">{{ __('Hai domande?') }}</h6>
                            <p class="small text-muted mb-4">
                                {{ __('Se hai domande o se hai bisogno di assistenza, non esitare a contattare il nostro team di supporto.') }}
                            </p>
                            <a href="mailto:support@onboardingplatform.com" class="btn btn-outline-primary">
                                <i class="fas fa-envelope me-1"></i> {{ __('Contatta il supporto') }}
                            </a>
                        </div>

                        <div class="mt-4 text-center">
                            <a href="{{ route('logout') }}"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                               class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-sign-out-alt me-1"></i> {{ __('Logout') }}
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>
