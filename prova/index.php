<?php
/**
 * Homepage
 * Presentazione del gattile e ultimi 2 gatti arrivati
 * Contenuto dinamico estratto dal database
 */

'use strict';

session_start();

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';

if (isUserAuthenticated()) {
    checkSessionTimeout();
}

$page_title = 'Home - Gattile';

// Ottieni ultimi 2 gatti da DB
$latest_cats = [];
try {
    $db = getDbConnection('lecture');
    if ($db) {
        $query = "SELECT id, nome, descrizione, peso, colore_mantello, eta, data_arrivo 
                  FROM gatti 
                  ORDER BY data_arrivo DESC 
                  LIMIT 2";
        $latest_cats = executeQuery($db, $query) ?? [];
        closeDbConnection($db);
    }
} catch (Exception $e) {
    error_log('Homepage Latest Cats Error: ' . $e->getMessage());
}
?>
<?php require_once __DIR__ . '/includes/header.php'; ?>

<div class="container">
    <!-- Hero section -->
    <section class="hero" aria-label="Introduzione al sito">
        <article>
            <h2>Benvenuto al Gattile</h2>
            <p class="lead">
                Ogni anno, centinaia di gatti vengono abbandonati o nascono in strada, necessitando di cure 
                e di una famiglia. Allo stesso tempo, molte persone desiderano accogliere un felino o dedicare 
                il proprio tempo come volontari.
            </p>
            <p>
                Questo sito nasce per facilitare le adozioni e organizzare il supporto attivo alla struttura ospitante.
            </p>
        </article>
    </section>

    <!-- Sezione "Ultimi Arrivi" -->
    <section class="latest-arrivals" aria-label="Ultimi gatti arrivati">
        <h2>🆕 Ultimi Arrivi</h2>
        <p class="section-description">
            Scopri gli ultimi gatti che sono arrivati al nostro rifugio
        </p>

        <?php if (!empty($latest_cats)): ?>
            <div class="cats-showcase">
                <?php foreach ($latest_cats as $cat): ?>
                    <article class="cat-card" role="article" aria-label="Gatto: <?php echo htmlspecialchars($cat['nome']); ?>">
                        <figure>
                            <picture>
                                <source srcset="/PAI/prova/assets/images/cat-placeholder.svg" type="image/svg+xml">
                                <img 
                                    src="/PAI/prova/assets/images/cat-placeholder.svg" 
                                    alt="Foto del gatto <?php echo htmlspecialchars($cat['nome']); ?>"
                                    loading="lazy"
                                >
                            </picture>
                            <figcaption>
                                <h3><?php echo htmlspecialchars($cat['nome']); ?></h3>
                                <dl>
                                    <dt>Età:</dt>
                                    <dd><?php echo htmlspecialchars($cat['eta']); ?> mese/i</dd>
                                    <dt>Colore:</dt>
                                    <dd><?php echo htmlspecialchars($cat['colore_mantello']); ?></dd>
                                    <dt>Peso:</dt>
                                    <dd><?php echo htmlspecialchars($cat['peso']); ?> kg</dd>
                                    <dt>Arrivato:</dt>
                                    <dd><?php echo date('d/m/Y', strtotime($cat['data_arrivo'])); ?></dd>
                                </dl>
                                <p><?php echo htmlspecialchars(substr($cat['descrizione'], 0, 100)) . '...'; ?></p>
                            </figcaption>
                        </figure>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="alert alert-info">Nessun gatto disponibile al momento.</p>
        <?php endif; ?>

        <div class="cta-buttons">
            <a href="gatti.php" class="btn btn-primary">Visualizza tutti i gatti</a>
        </div>
    </section>

    <!-- Call to Action sections -->
    <section class="cta-section" aria-label="Cosa puoi fare">
        <h2>Come posso aiutare?</h2>
        <div class="cta-grid">
            <article class="cta-card" role="article">
                <h3>🐱 Adotta un gatto</h3>
                <p>
                    Se desideri portare un nuovo compagno nella tua casa, scopri i nostri gatti 
                    e prenota una visita conoscitiva.
                </p>
                <a href="gatti.php" class="btn btn-secondary">Scopri di più</a>
            </article>

            <article class="cta-card" role="article">
                <h3>🤝 Fai Volontariato</h3>
                <p>
                    Contribuisci al benessere dei nostri felini dedicando il tuo tempo 
                    come volontario del nostro rifugio.
                </p>
                <?php if (isUserAuthenticated()): ?>
                    <a href="volontariato.php" class="btn btn-secondary">Offri il tuo aiuto</a>
                <?php else: ?>
                    <a href="login.php?redirect=volontariato.php" class="btn btn-secondary">Accedi per accedere</a>
                <?php endif; ?>
            </article>

            <article class="cta-card" role="article">
                <h3>💬 Contattaci</h3>
                <p>
                    Hai domande? Desideri saperne di più sulla nostra associazione e sui nostri servizi?
                </p>
                <a href="#contact" class="btn btn-secondary">Scrivi a noi</a>
            </article>
        </div>
    </section>

    <!-- About section -->
    <section class="about-section" aria-label="Chi siamo">
        <h2>Chi siamo</h2>
        <article>
            <h3>La nostra missione</h3>
            <p>
                Gattile è un'associazione non profit dedicata al benessere e alla protezione dei gatti 
                abbandonati e in difficoltà. La nostra missione è:
            </p>
            <ul>
                <li>Fornire rifugio sicuro e cure veterinarie ai gatti bisognosi</li>
                <li>Facilitare le adozioni responsabili in famiglie amorevoli</li>
                <li>Sensibilizzare il pubblico sulla causa dei diritti animali</li>
                <li>Promuovere il volontariato e la solidarietà comunitaria</li>
            </ul>
        </article>
    </section>

    <!-- Stats section -->
    <section class="stats-section" aria-label="Statistiche del gattile">
        <h2>I nostri numeri</h2>
        <div class="stats-grid">
            <div class="stat-card">
                <meter value="50" min="0" max="100" aria-label="Gatti in rifugio: 50"></meter>
                <h3>Gatti in rifugio</h3>
                <p>50</p>
            </div>
            <div class="stat-card">
                <meter value="85" min="0" max="100" aria-label="Adozioni completate: 85"></meter>
                <h3>Adozioni completate</h3>
                <p>85</p>
            </div>
            <div class="stat-card">
                <meter value="120" min="0" max="100" aria-label="Volontari attivi: 120"></meter>
                <h3>Volontari attivi</h3>
                <p>120</p>
            </div>
            <div class="stat-card">
                <meter value="15" min="0" max="100" aria-label="Anni di attività: 15"></meter>
                <h3>Anni di attività</h3>
                <p>15</p>
            </div>
        </div>
    </section>
</div>

<style>
    .hero {
        background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-secondary) 100%);
        color: white;
        padding: var(--spacing-xxl);
        border-radius: var(--border-radius-lg);
        margin-bottom: var(--spacing-xxl);
        text-align: center;
    }

    .hero h2 {
        color: white;
        margin-bottom: var(--spacing-lg);
        font-size: 36px;
    }

    .lead {
        font-size: 18px;
        line-height: 1.8;
        margin-bottom: var(--spacing-lg);
    }

    .latest-arrivals {
        margin-bottom: var(--spacing-xxl);
    }

    .latest-arrivals h2 {
        text-align: center;
        margin-bottom: var(--spacing-lg);
        color: var(--color-primary);
    }

    .section-description {
        text-align: center;
        margin-bottom: var(--spacing-xl);
        font-size: var(--font-size-large);
        color: var(--color-text-light);
    }

    .cats-showcase {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: var(--spacing-lg);
        margin-bottom: var(--spacing-xl);
    }

    .cat-card {
        background-color: var(--color-bg-alt);
        border-radius: var(--border-radius-lg);
        overflow: hidden;
        transition: var(--transition);
        box-shadow: var(--shadow-md);
    }

    .cat-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
    }

    .cat-card figure {
        margin: 0;
        padding: 0;
    }

    .cat-card img {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }

    .cat-card figcaption {
        padding: var(--spacing-md);
    }

    .cat-card h3 {
        margin: 0 0 var(--spacing-md) 0;
    }

    .cat-card dl {
        background-color: transparent;
        padding: 0;
        margin-bottom: var(--spacing-md);
    }

    .cat-card dt,
    .cat-card dd {
        display: inline;
        margin: 0;
    }

    .cat-card dt {
        font-weight: 600;
    }

    .cat-card dt::after {
        content: ': ';
    }

    .cat-card dd::after {
        content: ' | ';
        color: var(--color-border);
    }

    .cat-card dd:last-child::after {
        content: '';
    }

    .cat-card p {
        font-size: var(--font-size-small);
        margin: 0;
        line-height: 1.4;
    }

    .cta-buttons {
        display: flex;
        justify-content: center;
        gap: var(--spacing-md);
        margin-top: var(--spacing-xl);
    }

    .cta-section {
        background-color: var(--color-bg-alt);
        padding: var(--spacing-xl);
        border-radius: var(--border-radius-lg);
        margin-bottom: var(--spacing-xxl);
    }

    .cta-section h2 {
        text-align: center;
        margin-bottom: var(--spacing-xl);
    }

    .cta-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: var(--spacing-lg);
    }

    .cta-card {
        background-color: white;
        padding: var(--spacing-lg);
        border-radius: var(--border-radius-md);
        text-align: center;
        box-shadow: var(--shadow-sm);
        transition: var(--transition);
    }

    .cta-card:hover {
        box-shadow: var(--shadow-md);
    }

    .cta-card h3 {
        margin-bottom: var(--spacing-md);
        font-size: 20px;
    }

    .cta-card p {
        margin-bottom: var(--spacing-lg);
        line-height: 1.6;
    }

    .about-section {
        margin-bottom: var(--spacing-xxl);
    }

    .about-section h2 {
        text-align: center;
        margin-bottom: var(--spacing-xl);
    }

    .about-section h3 {
        color: var(--color-primary);
        margin: var(--spacing-lg) 0 var(--spacing-md) 0;
    }

    .about-section ul {
        list-style-position: inside;
        line-height: 1.8;
    }

    .stats-section {
        background-color: var(--color-primary);
        color: white;
        padding: var(--spacing-xl);
        border-radius: var(--border-radius-lg);
        text-align: center;
    }

    .stats-section h2 {
        color: white;
        margin-bottom: var(--spacing-xl);
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: var(--spacing-lg);
    }

    .stat-card {
        background-color: rgba(255, 255, 255, 0.1);
        padding: var(--spacing-lg);
        border-radius: var(--border-radius-md);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .stat-card h3 {
        color: white;
        margin: var(--spacing-md) 0;
        font-size: 16px;
    }

    .stat-card p {
        font-size: 24px;
        font-weight: bold;
        margin: 0;
    }

    .stat-card meter {
        border-color: rgba(255, 255, 255, 0.3);
        width: 100%;
        margin-bottom: var(--spacing-md);
    }

    @media (max-width: 768px) {
        .hero {
            padding: var(--spacing-lg);
        }

        .hero h2 {
            font-size: 28px;
        }

        .cta-buttons {
            flex-direction: column;
        }

        .cta-buttons a {
            width: 100%;
        }
    }
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
