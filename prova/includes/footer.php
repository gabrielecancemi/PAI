<?php
/**
 * Footer Template
 * Componente semantico HTML5 per piede di pagina
 * WCAG 2.1 Level AA - Link logici e informazioni cookie/privacy
 */

'use strict';
?>
    </main>

    <!-- Cookie Banner -->
    <section id="cookie-notice" class="cookie-banner" role="region" aria-label="Avviso cookie" aria-live="polite">
        <div class="container">
            <div class="cookie-content">
                <h2>Gestione Cookie e Privacy</h2>
                <p>
                    Questo sito utilizza cookie essenziali per le funzionalità di autenticazione e sessione.
                    Utilizziamo anche cookie "Remember me" con token opaco per ricordare le tue preferenze di login.
                    <a href="#privacy-details" class="cookie-details-link">Visualizza i dettagli</a>
                </p>
                <div id="privacy-details" class="privacy-details" hidden>
                    <h3>Tipi di Cookie utilizzati:</h3>
                    <ul>
                        <li><strong>PHPSESSID:</strong> Cookie di sessione essenziale per mantenere l'accesso</li>
                        <li><strong>gattile_remember_token:</strong> Token opaco per ricordare l'accesso (72 ore)</li>
                    </ul>
                    <p>
                        Nessun dato personale è memorizzato in chiaro nei cookie.
                        Il token di remember è associato al tuo account solo lato server.
                    </p>
                    <h3>Come gestire i cookie:</h3>
                    <p>
                        Puoi cancellare i cookie dalle impostazioni del tuo browser.
                        <a href="#delete-cookies" class="delete-cookies-btn" role="button" tabindex="0">
                            Cancella cookie di questo sito
                        </a>
                    </p>
                </div>
                <div class="cookie-actions">
                    <button id="cookie-accept" class="btn btn-primary" type="button">
                        Accetto
                    </button>
                    <button id="cookie-details-toggle" class="btn btn-secondary" type="button">
                        Dettagli
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer role="contentinfo" class="site-footer">
        <div class="container">
            <div class="footer-content">
                <section class="footer-section">
                    <h2>Chi Siamo</h2>
                    <p>
                        Gattile è un'associazione dedicata al benessere e all'adozione di gatti.
                        Lavoriamo per offrire una seconda possibilità a ogni felino.
                    </p>
                </section>

                <section class="footer-section">
                    <h2>Link Veloci</h2>
                    <nav aria-label="Link veloci">
                        <ul>
                            <li><a href="/PAI/prova/index.php">Home</a></li>
                            <li><a href="/PAI/prova/gatti.php">Visualizza Gatti</a></li>
                            <li><a href="/PAI/prova/login.php">Login</a></li>
                            <li><a href="/PAI/prova/registrazione.php">Registrati</a></li>
                        </ul>
                    </nav>
                </section>

                <section class="footer-section">
                    <h2>Privacy e Sicurezza</h2>
                    <p>
                        I tuoi dati personali sono protetti secondo le norme GDPR e le migliori pratiche di sicurezza.
                    </p>
                    <ul class="footer-links">
                        <li><a href="#" aria-label="Informativa sulla privacy">Informativa Privacy</a></li>
                        <li><a href="#" aria-label="Termini di servizio">Termini di Servizio</a></li>
                        <li><a href="#cookie-notice" aria-label="Gestione cookie">Gestione Cookie</a></li>
                    </ul>
                </section>
            </div>

            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> Gattile - Adozioni e Volontariato. Tutti i diritti riservati.</p>
                <p>
                    <small>
                        Sviluppato seguendo i principi WCAG 2.1 Level AA, Nielsen Norman e OWASP.
                        <a href="#wcag-statement">Dichiarazione di accessibilità</a>
                    </small>
                </p>
            </div>
        </div>
    </footer>

    <!-- Script gestione cookie e privacy -->
    <script>
    'use strict';

    // Gestione banner cookie
    document.addEventListener('DOMContentLoaded', function() {
        const cookieBanner = document.getElementById('cookie-notice');
        const acceptBtn = document.getElementById('cookie-accept');
        const detailsToggle = document.getElementById('cookie-details-toggle');
        const detailsDiv = document.getElementById('privacy-details');
        const deleteCookiesBtn = document.querySelector('.delete-cookies-btn');

        // Toggle dettagli
        detailsToggle.addEventListener('click', function() {
            const isHidden = detailsDiv.hasAttribute('hidden');
            if (isHidden) {
                detailsDiv.removeAttribute('hidden');
                this.textContent = 'Nascondi dettagli';
            } else {
                detailsDiv.setAttribute('hidden', '');
                this.textContent = 'Dettagli';
            }
        });

        // Cancella cookie
        if (deleteCookiesBtn) {
            deleteCookiesBtn.addEventListener('click', function() {
                // Cancella cookie di sessione (non è completamente possibile lato client)
                // Ma possiamo logout e informare l'utente
                document.cookie = 'PHPSESSID=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
                document.cookie = 'gattile_remember_token=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
                
                alert('Cookie cancellati. Se eri loggato, effettua il logout dal menu.');
            });
        }

        // Accetta cookie
        acceptBtn.addEventListener('click', function() {
            // Salva consenso in localStorage
            localStorage.setItem('gattile_cookie_consent', 'accepted');
            localStorage.setItem('gattile_cookie_consent_time', new Date().toISOString());
            
            // Nascondi banner
            cookieBanner.style.display = 'none';
        });

        // Controlla consenso precedente
        const consentGiven = localStorage.getItem('gattile_cookie_consent');
        if (consentGiven === 'accepted') {
            cookieBanner.style.display = 'none';
        }
    });
    </script>

    <!-- Script per accessibilità e funzionalità base -->
    <script>
    'use strict';

    // Toggle menu mobile
    document.addEventListener('DOMContentLoaded', function() {
        const navToggle = document.querySelector('.nav-toggle');
        const navMenu = document.getElementById('nav-menu');

        if (navToggle) {
            navToggle.addEventListener('click', function() {
                const isExpanded = this.getAttribute('aria-expanded') === 'true';
                this.setAttribute('aria-expanded', !isExpanded);
                navMenu.classList.toggle('active');
            });
        }

        // Chiudi menu quando si clicca su un link
        const navLinks = document.querySelectorAll('.nav-link');
        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                navToggle.setAttribute('aria-expanded', 'false');
                navMenu.classList.remove('active');
            });
        });

        // Chiudi menu con Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && navMenu.classList.contains('active')) {
                navToggle.setAttribute('aria-expanded', 'false');
                navMenu.classList.remove('active');
            }
        });
    });
    </script>

    <!-- Script diagnostico errori -->
    <script>
    'use strict';

    window.addEventListener('error', function(event) {
        console.error('Errore globale:', event.message, event.filename, event.lineno);
        // In produzione, potrebbe inviare l'errore a un servizio di logging
    });

    window.addEventListener('unhandledrejection', function(event) {
        console.error('Promise rejection non gestita:', event.reason);
    });
    </script>
</body>
</html>
