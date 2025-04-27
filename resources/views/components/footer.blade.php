<footer class="footer mt-auto py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 mb-4 mb-lg-0">
                <h5 class="footer-brand mb-3"><i class="fas fa-project-diagram me-2"></i>Onboarding Platform</h5>
                <p class="footer-description text-muted mb-4">La piattaforma completa per gestire il processo di onboarding dei nuovi dipendenti e garantire un'integrazione efficace in azienda.</p>
                <div class="footer-social d-flex gap-3 mb-4">
                    <a href="#" class="footer-social-link"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="footer-social-link"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="footer-social-link"><i class="fab fa-linkedin-in"></i></a>
                    <a href="#" class="footer-social-link"><i class="fab fa-instagram"></i></a>
                </div>
            </div>

            <div class="col-lg-2 col-md-4 mb-4 mb-md-0">
                <h6 class="footer-heading mb-3">Link Utili</h6>
                <ul class="footer-links list-unstyled">
                    <li class="mb-2"><a href="{{ url('/') }}" class="text-decoration-none">Home</a></li>
                    <li class="mb-2"><a href="#" class="text-decoration-none">Chi Siamo</a></li>
                    <li class="mb-2"><a href="#" class="text-decoration-none">Servizi</a></li>
                    <li class="mb-2"><a href="#" class="text-decoration-none">Contatti</a></li>
                    <li class="mb-2"><a href="#" class="text-decoration-none">FAQ</a></li>
                </ul>
            </div>

            <div class="col-lg-2 col-md-4 mb-4 mb-md-0">
                <h6 class="footer-heading mb-3">Risorse</h6>
                <ul class="footer-links list-unstyled">
                    <li class="mb-2"><a href="#" class="text-decoration-none">Blog</a></li>
                    <li class="mb-2"><a href="#" class="text-decoration-none">Documentazione</a></li>
                    <li class="mb-2"><a href="#" class="text-decoration-none">Webinar</a></li>
                    <li class="mb-2"><a href="#" class="text-decoration-none">Guide</a></li>
                    <li class="mb-2"><a href="#" class="text-decoration-none">Support Center</a></li>
                </ul>
            </div>

            <div class="col-lg-4 col-md-4">
                <h6 class="footer-heading mb-3">Contattaci</h6>
                <div class="footer-contact-item d-flex align-items-center mb-3">
                    <div class="footer-contact-icon me-3">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <span class="text-muted">Via Roma 123, Milano</span>
                </div>
                <div class="footer-contact-item d-flex align-items-center mb-3">
                    <div class="footer-contact-icon me-3">
                        <i class="fas fa-phone"></i>
                    </div>
                    <span class="text-muted">+39 02 1234567</span>
                </div>
                <div class="footer-contact-item d-flex align-items-center mb-4">
                    <div class="footer-contact-icon me-3">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <span class="text-muted">info@onboardingplatform.com</span>
                </div>

                <div class="footer-newsletter mt-4">
                    <h6 class="footer-newsletter-title mb-3">Newsletter</h6>
                    <form class="footer-newsletter-form d-flex">
                        <input type="email" class="form-control me-2" placeholder="La tua email">
                        <button type="submit" class="btn btn-primary">Iscriviti</button>
                    </form>
                </div>
            </div>
        </div>

        <hr class="my-4 opacity-25">

        <div class="footer-bottom d-flex flex-column flex-md-row justify-content-between align-items-center">
            <div class="footer-copyright text-muted mb-3 mb-md-0">
                © {{ date('Y') }} Onboarding Platform. Tutti i diritti riservati.
            </div>
            <div class="footer-legal d-flex gap-3">
                <a href="{{ route('privacy') }}" class="text-decoration-none text-muted">Privacy Policy</a>
                <a href="{{ route('terms') }}" class="text-decoration-none text-muted">Termini di Servizio</a>
                <a href="#" class="text-decoration-none text-muted">Cookie Policy</a>
            </div>
        </div>
    </div>
</footer>
