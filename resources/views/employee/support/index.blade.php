<x-layout>
    <x-slot name="sidebar">
        @include('components.sidebar')
    </x-slot>
    <div class="container py-5">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h1 class="h3 mb-0"><i class="fas fa-info-circle me-2"></i>Domande Frequenti (FAQ)</h1>
                    </div>
                    <div class="card-body">
                        <div class="accordion" id="faqAccordion">
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="faq1">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faqContent1" aria-expanded="true" aria-controls="faqContent1">
                                        Come posso completare il mio profilo?
                                    </button>
                                </h2>
                                <div id="faqContent1" class="accordion-collapse collapse show" aria-labelledby="faq1" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        <p>Per completare il tuo profilo:</p>
                                        <ol>
                                            <li>Vai alla sezione "Profilo" dal menu principale</li>
                                            <li>Clicca su "Modifica profilo"</li>
                                            <li>Compila tutti i campi richiesti</li>
                                            <li>Carica una foto profilo</li>
                                            <li>Salva le modifiche</li>
                                        </ol>
                                        <p>Assicurati di mantenere sempre aggiornate le tue informazioni personali.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item">
                                <h2 class="accordion-header" id="faq2">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqContent2" aria-expanded="false" aria-controls="faqContent2">
                                        Come posso caricare documenti richiesti?
                                    </button>
                                </h2>
                                <div id="faqContent2" class="accordion-collapse collapse" aria-labelledby="faq2" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        <p>Per caricare documenti richiesti:</p>
                                        <ol>
                                            <li>Vai alla sezione "Documenti" dalla dashboard</li>
                                            <li>Trova il documento contrassegnato come "Da caricare"</li>
                                            <li>Clicca sull'icona di caricamento</li>
                                            <li>Seleziona il file dal tuo computer</li>
                                            <li>Conferma il caricamento</li>
                                        </ol>
                                        <p>I documenti vengono verificati dal team HR prima dell'approvazione definitiva.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item">
                                <h2 class="accordion-header" id="faq3">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqContent3" aria-expanded="false" aria-controls="faqContent3">
                                        Come posso completare i corsi di formazione?
                                    </button>
                                </h2>
                                <div id="faqContent3" class="accordion-collapse collapse" aria-labelledby="faq3" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        <p>Per completare i corsi di formazione:</p>
                                        <ol>
                                            <li>Accedi alla sezione "Corsi" dalla dashboard</li>
                                            <li>Seleziona il corso che desideri seguire</li>
                                            <li>Visualizza tutti i contenuti del corso (video, slide, documenti)</li>
                                            <li>Completa eventuali quiz o valutazioni</li>
                                            <li>Clicca su "Segna come completato" al termine</li>
                                        </ol>
                                        <p>Ricorda che alcuni corsi potrebbero richiedere il superamento di un test finale per essere considerati completati.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item">
                                <h2 class="accordion-header" id="faq4">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqContent4" aria-expanded="false" aria-controls="faqContent4">
                                        Come posso registrarmi a un evento?
                                    </button>
                                </h2>
                                <div id="faqContent4" class="accordion-collapse collapse" aria-labelledby="faq4" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        <p>Per registrarti a un evento:</p>
                                        <ol>
                                            <li>Vai alla sezione "Eventi" dal menu principale</li>
                                            <li>Trova l'evento a cui desideri partecipare</li>
                                            <li>Clicca sul pulsante "Dettagli"</li>
                                            <li>Nella pagina dell'evento, clicca su "Registrati"</li>
                                            <li>Conferma la tua partecipazione</li>
                                        </ol>
                                        <p>Dopo la registrazione, riceverai una notifica di conferma e, per eventi in presenza, potrai scaricare il tuo biglietto.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item">
                                <h2 class="accordion-header" id="faq5">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqContent5" aria-expanded="false" aria-controls="faqContent5">
                                        Come posso aprire un ticket di supporto?
                                    </button>
                                </h2>
                                <div id="faqContent5" class="accordion-collapse collapse" aria-labelledby="faq5" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        <p>Per aprire un ticket di supporto:</p>
                                        <ol>
                                            <li>Vai alla sezione "Supporto" o clicca su "Apri un Ticket" nella dashboard</li>
                                            <li>Compila il modulo con il titolo e la descrizione del problema</li>
                                            <li>Seleziona la priorità della richiesta</li>
                                            <li>Allega eventuali file o screenshot utili</li>
                                            <li>Invia la richiesta</li>
                                        </ol>
                                        <p>Il nostro team di supporto risponderà alla tua richiesta nel più breve tempo possibile, in base alla priorità assegnata.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <p class="mb-3">Non hai trovato risposta alla tua domanda?</p>
                    <a href="{{ route('employee.tickets.create') }}" class="btn btn-primary">
                        <i class="fas fa-ticket-alt me-2"></i>Apri un nuovo ticket
                    </a>
                    <a href="{{ route('employee.dashboard') }}" class="btn btn-outline-secondary ms-2">
                        <i class="fas fa-arrow-left me-2"></i>Torna alla Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-layout>
