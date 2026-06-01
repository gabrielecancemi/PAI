<?php
require_once 'config/db.php';
include 'header.php';

try {
    $db = getDBConnection('lecture'); // Solo permessi di SELECT
    $stmt = $db->query("SELECT nome, descrizione, data_arrivo FROM gatti ORDER BY data_arrivo DESC LIMIT 2");
    $nuovi_gatti = $stmt->fetchAll();
} catch (Exception $e) {
    $nuovi_gatti = [];
}
?>

<main class="contenuto-principale">
    <section class="presentazione-struttura">
        <h1>Benvenuti al Gattile Municipale</h1>
        <p>Ogni anno, centinaia di gatti vengono abbandonati o nascono in strada, necessitando di cure e di una famiglia. Questo sito nasce per facilitare le adozioni e organizzare il supporto attivo alla struttura ospitante attraverso turni coordinati e visite guidate.</p>
    </section>

    <section class="sezione-nuovi-arrivi" aria-labelledby="titolo-nuovi-arrivi">
        <h2 id="titolo-nuovi-arrivi">Nuovi Arrivi in Struttura 🐾</h2>
        <?php if (empty($nuovi_gatti)): ?>
            <p>Nessun nuovo ospite registrato di recente.</p>
        <?php else: ?>
            <ul class="lista-nuovi-gatti">
                <?php foreach ($nuovi_gatti as $gatto): ?>
                    <li>
                        <article class="scheda-nuovo-arrivo">
                            <h3><?php echo htmlspecialchars($gatto['nome']); ?></h3>
                            <time datetime="<?php echo $gatto['data_arrivo']; ?>">
                                Arrivato il: <?php echo date('d/m/Y', strtotime($gatto['data_arrivo'])); ?>
                            </time>
                            <p><?php echo htmlspecialchars($gatto['descrizione']); ?></p>
                        </article>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    </section>
</main>

<?php include 'footer.php'; ?>
