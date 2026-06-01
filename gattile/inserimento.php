<?php
require_once 'config/db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Intercettazione e blocco di utenti non autorizzati
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('HTTP/1.1 403 Forbidden');
    die("Accesso negato: area ad esclusivo uso amministrativo.");
}

$successo = '';
$errore = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $db = getDBConnection('modifier'); // Permessi completi di scrittura dati gatti
        $stmt = $db->prepare("INSERT INTO gatti (nome, descrizione, peso, colore_mantello, lunghezza_pelo, razza, colore_occhi, eta, sesso, data_arrivo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            trim($_POST['nome']),
            trim($_POST['descrizione']),
            floatval($_POST['peso']),
            trim($_POST['colore_mantello']),
            trim($_POST['lunghezza_pelo']),
            trim($_POST['razza']),
            trim($_POST['colore_occhi']),
            intval($_POST['eta']),
            $_POST['sesso'],
            $_POST['data_arrivo']
        ]);
        $successo = "Il felino è stato correttamente inserito nei registri comunali.";
    } catch (PDOException $e) {
        $errore = "Errore operativo durante il salvataggio dei dati.";
    }
}

include 'header.php';
?>

<main class="contenuto-principale">
    <section class="box-formulario broad">
        <h2>Scheda di Inserimento Animale</h2>
        <?php if ($successo): ?><p class="notifica successo"><?php echo $successo; ?></p><?php endif; ?>
        <?php if ($errore): ?><p class="notifica errore" role="alert"><?php echo $errore; ?></p><?php endif; ?>

        <form id="formGatto" method="post" action="inserimento.php" novalidate>
            <p class="campo-form"><label for="nome">Nome del gatto:</label><input type="text" id="nome" name="nome" required></p>
            <p class="campo-form"><label for="descrizione">Carattere e biografia:</label><textarea id="descrizione" name="descrizione" required></textarea></p>
            <p class="campo-form"><label for="peso">Peso attuale (in kg):</label><input type="number" step="0.01" id="peso" name="peso" required></p>
            <p class="campo-form"><label for="colore_mantello">Colore del mantello:</label><input type="text" id="colore_mantello" name="colore_mantello" required></p>
            <p class="campo-form"><label for="lunghezza_pelo">Tipologia pelo (Corto/Medio/Lungo):</label><input type="text" id="lunghezza_pelo" name="lunghezza_pelo" required></p>
            <p class="campo-form"><label for="razza">Razza dichiarata o incrocio:</label><input type="text" id="razza" name="razza" required></p>
            <p class="campo-form"><label for="colore_occhi">Colore iridi:</label><input type="text" id="colore_occhi" name="colore_occhi" required></p>
            <p class="campo-form"><label for="eta">Età stimata (in mesi):</label><input type="number" id="eta" name="eta" required></p>
            <p class="campo-form">
                <label for="sesso">Sesso biologico:</label>
                <select id="sesso" name="sesso" required>
                    <option value="M">Maschio (M)</option>
                    <option value="F">Femmina (F)</option>
                </select>
            </p>
            <p class="campo-form"><label for="data_arrivo">Data di ingresso nel rifugio:</label><input type="date" id="data_arrivo" name="data_arrivo" required></p>
            <p class="pulsanti-form"><button type="submit" id="btnSalvaGatto">Registra Animale</button></p>
        </form>
    </section>
</main>

<script src="js/validazione.js"></script>
<?php include 'footer.php'; ?>
