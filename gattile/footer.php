
<footer class="footer">

    <a href="index.php" class="brand-logo" aria-label="Torna alla Home Page">

        <img src="img/logo.png" alt="" class="logo-img">

        <strong>Gattile San Paolo</strong>
    </a>

    <nav aria-label="Menu principale di navigazione">
        <ul>
            <li><a href="index.php" class="<?= $currentPage === 'index.php' ? 'active' : '' ?>">Home</a></li>

            <li><a href="adozioni.php" class="<?= $currentPage === 'adozioni.php' ? 'active' : '' ?>">
                    Adotta un gatto
                </a></li>
            <li><a href="volontariato.php" class="<?= $currentPage === 'volontariato.php' ? 'active' : '' ?>">
                    Diventa Volontario
                </a></li>
            <?php if (!empty($_SESSION['is_admin'])): ?>
                <li><a href="inserimento.php" class="<?= $currentPage === 'inserimento.php' ? 'active' : '' ?>">
                        Aggiungi Gatto
                    </a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <section class="footer-info">

        <address>

            <strong>Contatti</strong><br>

            Via San Paolo 100, Torino<br>

            <a href="tel:+390111234567">
                011 1234567
            </a><br>

            <a href="mailto:info@gattilesanpaolo.it">
                info@gattilesanpaolo.it
            </a>

        </address>

        <?php if (isset($_SESSION['username'])): ?>

            <p class="utente-info">
                👤
                <strong><?= $_SESSION['username'] ?></strong>

                <?php if (!empty($_SESSION['is_admin'])): ?>
                    <small class="badge-gatto">
                        Amministratore
                    </small>
                <?php else: ?>
                    <small class="badge-gatto">
                        Utente
                    </small>
                <?php endif; ?>
            </p>

        <?php endif; ?>

    </section>

    <small class="footer-copy">
        © <?= date('Y') ?> Gattile San Paolo ·
        Tutti i diritti riservati
    </small>

</footer>

<a href="faq.php"
   class="faq-button"
   aria-label="Domande frequenti"
   title="Domande frequenti">
    ?
</a>

</body>

</html>