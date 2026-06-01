<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'header.php';
?>

<main class="contenuto-principale adozioni-layout">
    
    <section id="react-catalogo-root">
        <p>Inizializzazione catalogo dinamico in corso...</p>
    </section>

    <section class="prenotazione-sezione">
        <h2>Modulo di Richiesta Visita</h2>
        
        <?php if (!isset($_SESSION['user_id'])): ?>
            <aside class="avviso-autenticazione">
                <p>⚠️ Per prenotare un appuntamento conoscitivo e selezionare i gatti, effettua il <a href="login.php">Login</a> o completa la <a href="registrazione.php">Registrazione</a>.</p>
            </aside>
        <?php else: ?>
            <form id="formPrenotazioneVisita" novalidate>
                <fieldset class="gruppo-campi-visita">
                    <legend>Dettagli appuntamento</legend>
                    <p class="campo-form">
                        <label>Animali selezionati dal catalogo:</label>
                        <span id="listaGattiSelezionati" class="contenitore-selezionati-badge">
                            <em>Nessun gatto selezionato nella griglia a sinistra.</em>
                        </span>
                    </p>
                    <p class="campo-form">
                        <label for="data_ora_visita">Pianifica Data e Ora:</label>
                        <input type="datetime-local" id="data_ora_visita" name="data_ora_visita" required>
                    </p>
                    <p class="pulsanti-form">
                        <button type="submit" id="btnInviaVisita">Invia Richiesta Visita</button>
                    </p>
                </fieldset>
                <p id="visitaFeedbackMessage" class="notifica" style="display:none;">&nbsp;</p>
            </form>
        <?php endif; ?>
    </section>
</main>

<script type="text/babel" src="js/react-components.js"></script>
<script src="js/visite.js"></script>

<?php include 'footer.php'; ?>

<section>
    <h2>Adozioni</h2>
    <p>Elenco gatti in adozione.</p>
</section>
<?php require_once 'footer.php'; ?>
