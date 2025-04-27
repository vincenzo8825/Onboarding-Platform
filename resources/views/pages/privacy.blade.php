<x-layout>
    <x-slot name="styles">
        @vite('resources/css/pages/static-pages.css')
    </x-slot>
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h1 class="text-center mb-0">Privacy Policy</h1>
                    </div>
                    <div class="card-body">
                        <div class="mb-5">
                            <h2>Introduzione</h2>
                            <p>La presente Privacy Policy descrive le modalità di raccolta, utilizzo e condivisione dei dati personali degli utenti (di seguito "Utente" o "Utenti") che accedono o utilizzano la piattaforma di onboarding aziendale (di seguito "Piattaforma").</p>
                            <p>La protezione dei dati personali è una priorità per noi. Questa politica è conforme al Regolamento Generale sulla Protezione dei Dati (GDPR) e alle altre normative applicabili sulla protezione dei dati.</p>
                        </div>

                        <div class="mb-5">
                            <h2>Titolare del Trattamento</h2>
                            <p>Il titolare del trattamento dei dati raccolti attraverso la Piattaforma è [Nome della Società], con sede legale in [Indirizzo], P.IVA [Numero], e-mail [Email], (di seguito "Titolare").</p>
                        </div>

                        <div class="mb-5">
                            <h2>Dati Raccolti</h2>
                            <p>Trattiamo le seguenti categorie di dati personali:</p>
                            <ul>
                                <li><strong>Dati di identificazione</strong>: nome, cognome, data di nascita, codice fiscale, foto del profilo</li>
                                <li><strong>Dati di contatto</strong>: indirizzo e-mail, numero di telefono, indirizzo di residenza</li>
                                <li><strong>Dati professionali</strong>: curriculum vitae, esperienze lavorative, competenze, qualifiche, titoli di studio</li>
                                <li><strong>Dati di utilizzo della piattaforma</strong>: progressi nelle attività di onboarding, risultati di test e quiz, feedback, presenze agli eventi</li>
                                <li><strong>Dati tecnici</strong>: indirizzo IP, tipo di browser, provider di servizi Internet, informazioni sul dispositivo utilizzato</li>
                            </ul>
                        </div>

                        <div class="mb-5">
                            <h2>Finalità del Trattamento</h2>
                            <p>I dati personali sono trattati per le seguenti finalità:</p>
                            <ul>
                                <li>Gestione del processo di onboarding aziendale</li>
                                <li>Erogazione di formazione e corsi online</li>
                                <li>Monitoraggio dei progressi del dipendente</li>
                                <li>Gestione delle richieste di documenti e certificazioni</li>
                                <li>Organizzazione di eventi e gestione delle partecipazioni</li>
                                <li>Supporto tecnico e assistenza agli utenti</li>
                                <li>Analisi statistiche e miglioramento dei servizi offerti</li>
                                <li>Adempimento di obblighi legali, contabili e fiscali</li>
                            </ul>
                        </div>

                        <div class="mb-5">
                            <h2>Base Giuridica</h2>
                            <p>Il trattamento dei dati personali si basa sulle seguenti basi giuridiche:</p>
                            <ul>
                                <li>Esecuzione del contratto di lavoro di cui l'Utente è parte</li>
                                <li>Adempimento di obblighi legali</li>
                                <li>Legittimo interesse del Titolare</li>
                                <li>Consenso dell'Utente, ove richiesto</li>
                            </ul>
                        </div>

                        <div class="mb-5">
                            <h2>Conservazione dei Dati</h2>
                            <p>I dati personali saranno conservati per il tempo necessario al conseguimento delle finalità per le quali sono raccolti e trattati, e in ogni caso per la durata del rapporto di lavoro e per un periodo successivo di 10 anni per finalità amministrative e contabili, salvo diversi obblighi di legge.</p>
                        </div>

                        <div class="mb-5">
                            <h2>Diritti degli Utenti</h2>
                            <p>L'Utente può esercitare i seguenti diritti:</p>
                            <ul>
                                <li>Diritto di accesso ai propri dati personali</li>
                                <li>Diritto di rettifica dei dati inesatti</li>
                                <li>Diritto alla cancellazione dei dati</li>
                                <li>Diritto di limitazione del trattamento</li>
                                <li>Diritto di opposizione al trattamento</li>
                                <li>Diritto alla portabilità dei dati</li>
                                <li>Diritto di revoca del consenso</li>
                                <li>Diritto di proporre reclamo all'autorità di controllo</li>
                            </ul>
                            <p>Per esercitare i diritti sopra elencati, l'Utente può inviare una richiesta via e-mail a [email del DPO o ufficio privacy].</p>
                        </div>

                        <div class="mb-5">
                            <h2>Sicurezza dei Dati</h2>
                            <p>Adottiamo misure di sicurezza tecniche e organizzative adeguate per proteggere i dati personali da accessi non autorizzati, perdita, uso improprio o alterazione. Tali misure includono firewall, crittografia dei dati, controlli degli accessi fisici e logici.</p>
                        </div>

                        <div class="mb-5">
                            <h2>Modifiche alla Privacy Policy</h2>
                            <p>Il Titolare si riserva il diritto di modificare, aggiornare o integrare la presente Privacy Policy in qualsiasi momento. Le modifiche saranno notificate agli Utenti tramite pubblicazione sulla Piattaforma. Si consiglia pertanto di consultare regolarmente questa pagina.</p>
                        </div>

                        <div class="text-center mt-5">
                            <p>Ultimo aggiornamento: {{ date('d/m/Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>
