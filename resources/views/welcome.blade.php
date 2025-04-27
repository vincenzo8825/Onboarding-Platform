<x-layout>
    <x-slot name="styles">
        <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    </x-slot>

    <div class="main-welcome-content">
        <!-- Hero Section -->
        <section class="hero text-white">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <h1 class="hero__title">Semplifica il processo di onboarding dei nuovi dipendenti</h1>
                        <p class="hero__description">Una piattaforma completa per gestire l'intero processo di inserimento dei nuovi assunti, dalla documentazione ai corsi di formazione.</p>
                        <div class="d-flex flex-wrap gap-3">
                            <a href="{{ route('register') }}" class="btn btn-light me-3">
                                <i class="fas fa-rocket me-2"></i>Inizia Gratuitamente
                            </a>
                            <a href="#features" class="btn btn-outline-light">
                                <i class="fas fa-arrow-down me-2"></i>Scopri di più
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-6 d-none d-lg-block">
                    </div>
                </div>
            </div>

            <div class="hero__shapes">
                <div class="hero__shape hero__shape--1"></div>
                <div class="hero__shape hero__shape--2"></div>
                <div class="hero__shape hero__shape--3"></div>
            </div>
        </section>

        <!-- Clients Section -->
        <section class="clients py-4">
            <div class="container">
                <div class="text-center mb-4">
                    <p class="clients__title">Usato da aziende leader in Italia</p>
                </div>
                <div class="clients__logos">
                    <div class="clients__logo" data-aos="fade-up" data-aos-delay="100">
                        Progetto Uno
                    </div>
                    <div class="clients__logo" data-aos="fade-up" data-aos-delay="200">
                        Soluzioni Avanzate
                    </div>
                    <div class="clients__logo" data-aos="fade-up" data-aos-delay="300">
                        InnovaTech
                    </div>
                    <div class="clients__logo" data-aos="fade-up" data-aos-delay="400">
                        Logistica Rapida
                    </div>
                    <div class="clients__logo" data-aos="fade-up" data-aos-delay="500">
                        Futura S.p.A.
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="features" id="features">
            <div class="container">
                <div class="section-heading text-center" data-aos="fade-up">
                    <p class="section-heading__subtitle">FUNZIONALITÀ PRINCIPALI</p>
                    <h2 class="section-heading__title">Tutto ciò di cui hai bisogno</h2>
                    <p class="section-heading__description">La nostra piattaforma offre strumenti potenti per semplificare l'intero processo di onboarding.</p>
                </div>

                <div class="row g-4">
                    <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="100">
                        <div class="card feature-card text-center p-4">
                            <div class="feature-card__icon-wrapper">
                                <i class="fas fa-file-alt feature-card__icon"></i>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title fw-bold mb-3">Gestione Documenti</h5>
                                <p class="card-text">Carica, organizza e condividi facilmente tutti i documenti necessari per l'onboarding dei nuovi dipendenti.</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="200">
                        <div class="card feature-card text-center p-4">
                            <div class="feature-card__icon-wrapper">
                                <i class="fas fa-graduation-cap feature-card__icon"></i>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title fw-bold mb-3">Corsi di Formazione</h5>
                                <p class="card-text">Crea percorsi di formazione personalizzati per garantire che i nuovi assunti acquisiscano tutte le competenze necessarie.</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="300">
                        <div class="card feature-card text-center p-4">
                            <div class="feature-card__icon-wrapper">
                                <i class="fas fa-tasks feature-card__icon"></i>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title fw-bold mb-3">Checklist e Task</h5>
                                <p class="card-text">Monitora il progresso dei nuovi assunti con checklist personalizzabili e task che guidano ogni fase del processo.</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="400">
                        <div class="card feature-card text-center p-4">
                            <div class="feature-card__icon-wrapper">
                                <i class="fas fa-trophy feature-card__icon"></i>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title fw-bold mb-3">Badge e Riconoscimenti</h5>
                                <p class="card-text">Motiva i dipendenti con un sistema di badge e riconoscimenti che celebra il completamento delle attività.</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="500">
                        <div class="card feature-card text-center p-4">
                            <div class="feature-card__icon-wrapper">
                                <i class="fas fa-chart-line feature-card__icon"></i>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title fw-bold mb-3">Dashboard e Analisi</h5>
                                <p class="card-text">Visualizza i progressi e le performance con dashboard intuitive e report personalizzabili.</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="600">
                        <div class="card feature-card text-center p-4">
                            <div class="feature-card__icon-wrapper">
                                <i class="fas fa-bell feature-card__icon"></i>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title fw-bold mb-3">Notifiche e Promemoria</h5>
                                <p class="card-text">Ricevi avvisi automatici su scadenze e attività pendenti per non perdere mai un passaggio importante.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <section class="stats py-5">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <div class="stats__card p-4 p-md-5">
                            <div class="row text-center">
                                <div class="col-md-4 mb-4 mb-md-0" data-aos="fade-up" data-aos-delay="100">
                                    <div class="stats__item">
                                        <div class="stats__value">96<span>%</span></div>
                                        <div class="stats__label">Soddisfazione clienti</div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-4 mb-md-0" data-aos="fade-up" data-aos-delay="200">
                                    <div class="stats__item">
                                        <div class="stats__value">78<span>%</span></div>
                                        <div class="stats__label">Risparmio di tempo</div>
                                    </div>
                                </div>
                                <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                                    <div class="stats__item">
                                        <div class="stats__value">5.000<span>+</span></div>
                                        <div class="stats__label">Dipendenti onboarded</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Platform Features Section -->
        <section class="platform-features">
            <div class="container">
                <div class="section-heading text-center" data-aos="fade-up">
                    <p class="section-heading__subtitle">VANTAGGI</p>
                    <h2 class="section-heading__title">Perché scegliere la nostra piattaforma</h2>
                    <p class="section-heading__description">Ottimizza il processo di onboarding e offri un'esperienza eccezionale ai nuovi assunti.</p>
                </div>

                <div class="row g-4 mt-3">
                    <div class="col-lg-6" data-aos="fade-right">
                        <div class="platform-feature-item">
                            <i class="fas fa-rocket platform-feature-item__icon"></i>
                            <h3 class="platform-feature-item__title h5 fw-bold mb-3">Onboarding più veloce</h3>
                            <p>Riduci i tempi di inserimento e massimizza la produttività dei nuovi dipendenti.</p>
                            <ul class="platform-feature-item__list mt-3">
                                <li>Automazione del processo documentale</li>
                                <li>Accesso immediato a materiali formativi</li>
                                <li>Monitoraggio in tempo reale dei progressi</li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-lg-6" data-aos="fade-left">
                        <div class="platform-feature-item">
                            <i class="fas fa-users platform-feature-item__icon"></i>
                            <h3 class="platform-feature-item__title h5 fw-bold mb-3">Esperienza personalizzata</h3>
                            <p>Crea percorsi di onboarding su misura per diversi ruoli e dipartimenti.</p>
                            <ul class="platform-feature-item__list mt-3">
                                <li>Template personalizzabili per ogni ruolo</li>
                                <li>Contenuti formativi specifici per dipartimento</li>
                                <li>Adattamento alle esigenze individuali</li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-lg-6" data-aos="fade-right" data-aos-delay="100">
                        <div class="platform-feature-item">
                            <i class="fas fa-chart-pie platform-feature-item__icon"></i>
                            <h3 class="platform-feature-item__title h5 fw-bold mb-3">Analisi dettagliate</h3>
                            <p>Ottieni insight preziosi sul processo di onboarding e sui progressi dei dipendenti.</p>
                            <ul class="platform-feature-item__list mt-3">
                                <li>Dashboard interattive con metriche chiave</li>
                                <li>Report personalizzabili per la direzione</li>
                                <li>Identificazione di aree di miglioramento</li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-lg-6" data-aos="fade-left" data-aos-delay="100">
                        <div class="platform-feature-item">
                            <i class="fas fa-shield-alt platform-feature-item__icon"></i>
                            <h3 class="platform-feature-item__title h5 fw-bold mb-3">Conformità garantita</h3>
                            <p>Assicurati che tutti i documenti e la formazione obbligatoria siano completati in tempo.</p>
                            <ul class="platform-feature-item__list mt-3">
                                <li>Tracciamento di tutta la documentazione legale</li>
                                <li>Promemoria automatici per adempimenti mancanti</li>
                                <li>Report di conformità generati automaticamente</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>



        <!-- Testimonials Section -->
        <section class="testimonials">
            <div class="container">
                <div class="section-heading text-center" data-aos="fade-up">
                    <p class="section-heading__subtitle">OPINIONI</p>
                    <h2 class="section-heading__title">Cosa dicono i nostri clienti</h2>
                    <p class="section-heading__description">Scopri le esperienze delle aziende che utilizzano la nostra piattaforma.</p>
                </div>

                <div class="row g-4 mt-4">
                    <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="100">
                        <div class="card testimonial-card h-100">
                            <div class="card-body">
                                <div class="mb-3">
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                </div>
                                <p class="testimonial-card__text mb-4">"La piattaforma è intuitiva e facile da usare. I nostri nuovi dipendenti la adorano e noi abbiamo notato un significativo miglioramento nella velocità con cui diventano operativi. Assolutamente consigliata!"</p>
                                <div class="d-flex align-items-center">
                                    <div class="testimonial-avatar me-3">
                                        <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="Testimonial" class="rounded-circle">
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold">Marco Bianchi</h6>
                                        <p class="mb-0 text-muted">HR Director, Innovate Inc.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="200">
                        <div class="card testimonial-card h-100">
                            <div class="card-body">
                                <div class="mb-3">
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                </div>
                                <p class="testimonial-card__text mb-4">"Da quando abbiamo implementato questa soluzione, il tempo di onboarding è diminuito del 65%. I manager sono entusiasti e i nuovi assunti si sentono subito parte del team. Un investimento che ha davvero fatto la differenza!"</p>
                                <div class="d-flex align-items-center">
                                    <div class="testimonial-avatar me-3">
                                        <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="Testimonial" class="rounded-circle">
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold">Laura Rossi</h6>
                                        <p class="mb-0 text-muted">CEO, Tech Solutions</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="300">
                        <div class="card testimonial-card h-100">
                            <div class="card-body">
                                <div class="mb-3">
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                </div>
                                <p class="testimonial-card__text mb-4">"Il sistema di badge e gamification ha trasformato il nostro processo di onboarding in un'esperienza coinvolgente e divertente. I nuovi dipendenti sono più motivati e completano le attività in metà del tempo rispetto a prima."</p>
                                <div class="d-flex align-items-center">
                                    <div class="testimonial-avatar me-3">
                                        <img src="https://randomuser.me/api/portraits/men/67.jpg" alt="Testimonial" class="rounded-circle">
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold">Alessandro Verdi</h6>
                                        <p class="mb-0 text-muted">HR Manager, GrowFast SpA</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </section>


    </div>

    <x-slot name="scripts">
        <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
        <script>
            // Inizializzazione AOS
            AOS.init({
                duration: 800,
                once: true,
                offset: 100,
            });

            // Aggiungiamo la classe welcome-page al body per i CSS specifici
            document.body.classList.add('welcome-page');

            // Smooth scroll per i link interni
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    document.querySelector(this.getAttribute('href')).scrollIntoView({
                        behavior: 'smooth'
                    });
                });
            });
        </script>
    </x-slot>
</x-layout>
