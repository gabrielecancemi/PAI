<footer class="footer">
    <section class="footer-sezione" aria-labelledby="footer-info">
        <h2 id="footer-info">Gattile San Paolo</h2>

        <p>
            Rifugio dedicato all'accoglienza, alla cura e
            all'adozione responsabile dei gatti.
        </p>

        <address>
            Via San Paolo 100, Torino<br>
            Tel: <a href="tel:+390111234567">011 1234567</a><br>
            Email:
            <a href="mailto:info@gattilesanpaolo.it">
                info@gattilesanpaolo.it
            </a>
        </address>
    </section>

    <nav class="footer-sezione" aria-label="Link utili del sito">

        <h2>Link utili</h2>

        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="adozioni.php">Adotta un gatto</a></li>
            <li><a href="volontariato.php">Diventa Volontario</a></li>

            <?php if (!empty($_SESSION['is_admin'])): ?>
                <li>
                    <a href="inserimento.php">
                        Aggiungi Gatto
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>

    <section class="footer-sezione" aria-labelledby="footer-account">

        <h2 id="footer-account">Account</h2>

        <?php if (isset($_SESSION['username'])): ?>

            <p>
                Accesso effettuato come
                <strong><?= $_SESSION['username'] ?></strong>
            </p>

            <p>
                <a href="logout.php" class="footer-btn">
                    Logout
                </a>
            </p>

        <?php else: ?>

            <p>Non hai effettuato l'accesso.</p>

            <ul>
                <li>
                    <a href="login.php">
                        Accedi
                    </a>
                </li>
                <li>
                    <a href="registrazione.php">
                        Registrati
                    </a>
                </li>
            </ul>

        <?php endif; ?>
    </section>

    <small class="copyright">
        © <?= date('Y') ?> Gattile San Paolo —
        Tutti i diritti riservati.
    </small>
</footer>
<script src="js/validazione.js"></script>
<script src="js/volontariato.js"></script>
<script src="js/visite.js"></script>


</body>

</html>