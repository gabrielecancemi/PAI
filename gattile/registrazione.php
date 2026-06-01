<?php
require_once 'config/db.php';

$errore = '';
$successo = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $cognome = trim($_POST['cognome'] ?? '');
    $indirizzo = trim($_POST['indirizzo'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validazione server-side speculare ai vincoli della traccia
    if (preg_match('/^[a-zA-Z]/', $username) && strlen($password) >= 8 && strlen($password) <= 16 
        && preg_match('/[A-Z]/', $password) && preg_match('/[a-z]/', $password) 
        && preg_match('/[0-9]/', $password) && preg_match('/[^A-Za-z0-9]/', $password)) {
        
        try {
            $db = getDBConnection('registrator'); // Sicurezza restrittiva
            $stmt = $db->prepare("INSERT INTO utenti (nome, cognome, indirizzo, username, password, is_admin) VALUES (?, ?, ?, ?, ?, FALSE)");
            $stmt->execute([$nome, $cognome, $indirizzo, $username, $password]);
            $successo = "Profilo creato! Puoi procedere all'autenticazione.";
        } catch (PDOException $e) {
            $errore = "Questo nome utente è già in uso all'interno del sistema.";
        }
    } else {
        $errore = "I dati inseriti violano le politiche di sicurezza stabilite.";
    }
}

include 'header.php';
?>

<main class="contenuto-principale">
    <section class="box-formulario">
        <h2>Registrazione Profilo</h2>
        <?php if ($errore): ?><p class="notifica errore" role="alert"><?php echo $errore; ?></p><?php endif; ?>
        <?php if ($successo): ?><p class="notifica successo"><?php echo $successo; ?></p><?php endif; ?>

        <form id="formRegistrazione" method="post" action="registrazione.php" novalidate>
            <p class="campo-form">
                <label for="nome">Nome:</label>
                <input type="text" id="nome" name="nome" required>
            </p>
            <p class="campo-form">
                <label for="cognome">Cognome:</label>
                <input type="text" id="cognome" name="cognome" required>
            </p>
            <p class="campo-form">
                <label for="indirizzo">Indirizzo di residenza:</label>
                <input type="text" id="indirizzo" name="indirizzo" required>
            </p>
            <p class="campo-form">
                <label for="username">Username (deve iniziare con una lettera):</label>
                <input type="text" id="username" name="username" required>
            </p>
            <p class="campo-form">
                <label for="password">Password (8-16 caratteri, 1 Maiuscola, 1 Minuscola, 1 Numero, 1 Speciale):</label>
                <input type="password" id="password" name="password" required>
            </p>
            <p class="campo-form">
                <label id="lbl_conf" for="conf_password">Conferma Password:</label>
                <input type="password" id="conf_password" name="conf_password" required>
            </p>
            <p class="pulsanti-form">
                <button type="submit" id="btnInviaReg">Invia Dati</button>
                <button type="reset" id="btnAnnullaReg">Annulla</button>
            </p>
        </form>
    </section>
</main>

<script src="js/validazione.js"></script>
<?php include 'footer.php'; ?>
