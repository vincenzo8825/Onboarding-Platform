<x-layout>
    <x-slot name="styles">
        @vite('resources/css/pages/static-pages.css')
    </x-slot>
    <div class="container my-5 static-page">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow">
                    <div class="card-header">
                        <h1 class="text-center mb-0">Termini di Servizio</h1>
                    </div>
                    <div class="card-body p-4">
                        <div class="alert alert-info mb-4">
                            <i class="fas fa-info-circle me-2"></i> Ultimo aggiornamento: {{ date('d/m/Y') }}
                        </div>

                        <div class="mb-5">
                            <h2>1. Accettazione dei Termini</h2>
                            <p>
                                Utilizzando questa piattaforma di onboarding (di seguito "Piattaforma"), accetti di essere vincolato dai presenti Termini di Servizio.
                                Se non accetti questi termini, ti preghiamo di non utilizzare la Piattaforma.
                            </p>
                            <p>
                                Questi Termini di Servizio costituiscono un accordo legalmente vincolante tra te e [Nome della Società] relativamente all'utilizzo della Piattaforma.
                            </p>
                        </div>

                        <div class="mb-5">
                            <h2>2. Descrizione del Servizio</h2>
                            <p>
                                La Piattaforma di onboarding è progettata per facilitare il processo di inserimento dei nuovi dipendenti
                                nell'organizzazione, fornendo accesso a materiali formativi, documenti e attività necessarie per un
                                efficace inserimento nell'ambiente lavorativo.
                            </p>
                            <p>
                                I servizi offerti dalla Piattaforma includono, ma non sono limitati a:
                            </p>
                            <ul>
                                <li>Gestione dei documenti necessari per l'onboarding</li>
                                <li>Formazione tramite corsi online e quiz interattivi</li>
                                <li>Partecipazione a eventi e sessioni di orientamento</li>
                                <li>Completamento di checklist e attività di inserimento</li>
                                <li>Comunicazione con team leader e colleghi</li>
                            </ul>
                        </div>

                        <div class="mb-5">
                            <h2>3. Account Utente</h2>
                            <p>
                                Per utilizzare la Piattaforma, è necessario creare un account. Sei responsabile del mantenimento della
                                riservatezza delle tue credenziali di accesso e di tutte le attività che si verificano sotto il tuo account.
                            </p>
                            <p>
                                Ti impegni a:
                            </p>
                            <ul>
                                <li>Fornire informazioni accurate, aggiornate e complete durante il processo di registrazione</li>
                                <li>Mantenere e aggiornare tempestivamente i tuoi dati per garantirne l'accuratezza</li>
                                <li>Proteggere la sicurezza del tuo account e della password</li>
                                <li>Notificare immediatamente qualsiasi accesso non autorizzato o violazione della sicurezza</li>
                                <li>Essere l'unico responsabile di tutte le attività che avvengono tramite il tuo account</li>
                            </ul>
                        </div>

                        <div class="mb-5">
                            <h2>4. Privacy e Dati Personali</h2>
                            <p>
                                La raccolta e l'utilizzo dei tuoi dati personali sono regolati dalla nostra <a href="{{ route('privacy') }}">Privacy Policy</a>.
                                Utilizzando la Piattaforma, acconsenti alla raccolta e all'utilizzo dei tuoi dati come descritto nella policy.
                            </p>
                            <p>
                                Trattiamo i tuoi dati personali in conformità con il Regolamento Generale sulla Protezione dei Dati (GDPR) e altre normative
                                applicabili sulla protezione dei dati.
                            </p>
                        </div>

                        <div class="mb-5">
                            <h2>5. Proprietà Intellettuale</h2>
                            <p>
                                Tutti i contenuti presenti sulla Piattaforma, inclusi testi, grafica, loghi, immagini, video, audio, software e altri materiali,
                                sono di proprietà di [Nome della Società] o dei suoi licenzianti e sono protetti dalle leggi sul copyright e altre leggi
                                sulla proprietà intellettuale.
                            </p>
                            <p>
                                Non sei autorizzato a:
                            </p>
                            <ul>
                                <li>Copiare, modificare, riprodurre, distribuire o creare opere derivate basate sui contenuti della Piattaforma</li>
                                <li>Utilizzare i contenuti per scopi commerciali senza autorizzazione esplicita</li>
                                <li>Rimuovere o alterare qualsiasi avviso di copyright, marchio o altro avviso proprietario</li>
                                <li>Decompilare, decodificare o tentare di accedere al codice sorgente della Piattaforma</li>
                            </ul>
                        </div>

                        <div class="mb-5">
                            <h2>6. Comportamento dell'Utente</h2>
                            <p>
                                Utilizzando la Piattaforma, ti impegni a non:
                            </p>
                            <ul>
                                <li>Violare qualsiasi legge o regolamento applicabile</li>
                                <li>Violare i diritti di terzi, inclusi diritti di proprietà intellettuale e privacy</li>
                                <li>Caricare, pubblicare o trasmettere contenuti offensivi, minacciosi, diffamatori o illegali</li>
                                <li>Tentare di accedere ad aree riservate della Piattaforma o a dati di altri utenti</li>
                                <li>Utilizzare la Piattaforma per scopi diversi da quelli previsti</li>
                                <li>Interferire con il normale funzionamento della Piattaforma</li>
                            </ul>
                        </div>

                        <div class="mb-5">
                            <h2>7. Limitazioni di Responsabilità</h2>
                            <p>
                                Nei limiti consentiti dalla legge applicabile, [Nome della Società] non sarà responsabile per:
                            </p>
                            <ul>
                                <li>Danni diretti, indiretti, incidentali, speciali, consequenziali o punitivi</li>
                                <li>Perdita di profitti, avviamento, uso, dati o altre perdite intangibili</li>
                                <li>Accessi non autorizzati o alterazione dei tuoi dati</li>
                                <li>Dichiarazioni o condotte di terzi sulla Piattaforma</li>
                                <li>Errori, omissioni, interruzioni, cancellazioni o difetti nei contenuti o nella comunicazione</li>
                            </ul>
                            <p>
                                La responsabilità complessiva di [Nome della Società] derivante dall'utilizzo della Piattaforma non potrà in
                                alcun caso superare l'importo pagato dall'utente, se applicabile, per l'accesso alla Piattaforma.
                            </p>
                        </div>

                        <div class="mb-5">
                            <h2>8. Modifiche ai Termini</h2>
                            <p>
                                [Nome della Società] si riserva il diritto di modificare questi Termini di Servizio in qualsiasi momento.
                                Le modifiche saranno effettive dopo la pubblicazione dei termini aggiornati sulla Piattaforma.
                            </p>
                            <p>
                                È tua responsabilità rivedere periodicamente questi Termini. L'uso continuato della Piattaforma dopo
                                la pubblicazione delle modifiche costituisce accettazione di tali modifiche.
                            </p>
                        </div>

                        <div class="mb-5">
                            <h2>9. Risoluzione</h2>
                            <p>
                                [Nome della Società] può, a sua esclusiva discrezione, terminare o sospendere il tuo accesso alla Piattaforma in
                                qualsiasi momento, con o senza preavviso, per qualsiasi motivo, inclusa la violazione di questi Termini.
                            </p>
                            <p>
                                In caso di risoluzione, le disposizioni di questi Termini che per loro natura dovrebbero sopravvivere alla risoluzione
                                continueranno a essere efficaci, incluse le disposizioni relative alla proprietà intellettuale, alle limitazioni di
                                responsabilità e alla legge applicabile.
                            </p>
                        </div>

                        <div class="mb-5">
                            <h2>10. Legge Applicabile</h2>
                            <p>
                                Questi Termini di Servizio sono regolati e interpretati in conformità con le leggi italiane, senza riguardo
                                alle disposizioni in materia di conflitto di leggi.
                            </p>
                            <p>
                                Qualsiasi controversia derivante da o relativa a questi Termini o all'utilizzo della Piattaforma sarà soggetta
                                alla giurisdizione esclusiva dei tribunali di [Città], Italia.
                            </p>
                        </div>

                        <div class="mb-5">
                            <h2>11. Contatti</h2>
                            <p>
                                Per domande o chiarimenti su questi Termini di Servizio, contattaci all'indirizzo email:
                                <a href="mailto:info@onboardingplatform.com">info@onboardingplatform.com</a>
                            </p>
                        </div>

                        <div class="text-center mt-5">
                            <p class="text-muted">
                                Accedendo o utilizzando la Piattaforma, confermi di aver letto, compreso e accettato questi Termini di Servizio.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>
