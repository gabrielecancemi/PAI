<?php
require_once 'config/db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$errore = '';
$utente_precompilato = '';

// Controllo del cookie per precompilare il modulo se l'utente lo aveva richiesto
if (isset($_COOKIE['ricordami_token']) && isset($_SESSION['saved_username'])) {
    $utente_precompilato = $_SESSION['saved_username'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $ricordami = isset($_POST['remember']);

    $db = getDBConnection('lecture');
    $stmt = $db->prepare("SELECT * FROM utenti WHERE username = ?");
    $stmt->execute([$username]);
    $utente = $stmt->fetch();

    if ($utente && $utente['password'] === $password) { // Verifica password diretta da traccia
        $_SESSION['user_id'] = $utente['id'];
        $_SESSION['username'] = $utente['username'];
        $_SESSION['is_admin'] = (bool)$utente['is_admin'];

        if ($ricordami) {
            $token_casuale = bin2hex(random_bytes(16));
            // Cookie sicuro accessibile solo via HTTP impostato per 72 ore
            setcookie('ricordami_token', $token_casuale, time() + (72 * 3600), "/", "", false, true);
            $_SESSION['saved_username'] = $utente['username'];
        } else {
            setcookie('ricordami_token', '', time() - 3600, "/");
            unset($_SESSION['saved_username']);
        }

        header('Location: index.php');
        exit;
    } else {
        $errore = "Autenticazione fallita. Credenziali errate.";
    }
}

include 'header.php';
?>

<main class="contenuto-principale">
    <section class="box-formulario">
        <h2>Accesso al Portale</h2>
        <?php if ($errore): ?><p class="notifica errore" role="alert"><?php echo $errore; ?></p><?php endif; ?>

        <form id="formLogin" method="post" action="login.php">
            <p class="campo-form">
                <label for="username">Nome Utente:</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($utente_precompilato); ?>" required>
            </p>
            <p class="campo-form">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </p>
            <p class="campo-form opzione-checkbox">
                <input type="checkbox" id="remember" name="remember" <?php echo !empty($utente_precompilato) ? 'checked' : ''; ?>>
                <label for="remember">Ricordami per 72 ore su questo browser</label>
            </p>
            <p class="pulsanti-form">
                <button type="submit" id="btnLoginSubmit">Accedi</button>
            </p>
        </form>
    </section>
</main>

<?php include 'footer.php'; ?>
