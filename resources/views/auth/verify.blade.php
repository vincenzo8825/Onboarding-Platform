<x-layout>
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">{{ __('Verifica il tuo indirizzo email') }}</h4>
                    </div>

                    <div class="card-body p-4">
                        @if (session('resent'))
                            <div class="alert alert-success d-flex align-items-center" role="alert">
                                <i class="fas fa-check-circle me-2"></i>
                                <div>
                                    {{ __('Un nuovo link di verifica è stato inviato al tuo indirizzo email.') }}
                                </div>
                            </div>
                        @endif

                        <div class="text-center mb-4">
                            <div class="mb-4">
                                <i class="fas fa-envelope fa-4x text-primary"></i>
                            </div>
                            <h5 class="mb-3">{{ __('Controlla la tua casella di posta') }}</h5>
                            <p class="text-muted mb-4">
                                {{ __('Prima di continuare, controlla la tua email per un link di verifica.') }}
                                {{ __('Se non hai ricevuto l\'email, controlla anche nella cartella spam.') }}
                            </p>
                        </div>

                        <div class="text-center">
                            <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                                @csrf
                                <p class="mb-0">
                                    {{ __('Non hai ricevuto l\'email?') }}
                                    <button type="submit" class="btn btn-link p-0 m-0 align-baseline">{{ __('Clicca qui per richiederne un\'altra') }}</button>
                                </p>
                            </form>
                        </div>

                        <hr class="my-4">

                        <div class="text-center">
                            <h6 class="mb-3">{{ __('Perché devo verificare la mia email?') }}</h6>
                            <p class="small text-muted">
                                {{ __('La verifica dell\'email ci aiuta a confermare che sei tu il proprietario di questo indirizzo email e a mantenere il tuo account sicuro.') }}
                            </p>
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
