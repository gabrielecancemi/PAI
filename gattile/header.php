
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gattile Municipale - Rifugio e Adozioni</title>
    <link rel="stylesheet" href="css/stile.css">
    <script src="https://unpkg.com/react@18/umd/react.development.js" crossorigin></script>
    <script src="https://unpkg.com/react-dom@18/umd/react-dom.development.js" crossorigin></script>
    <script src="https://unpkg.com/@babel/standalone/babel.min.js"></script>
</head>
<body>

<header class="testata-principale">
    <a href="index.php" class="brand-logo" aria-label="Torna alla Home Page">🐾 Gattile Rifugio</a>
    <nav aria-label="Menu principale di navigazione">
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="adozioni.php">Adotta un gatto</a></li>
            <li><a href="volontariato.php">Diventa Volontario</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <?php if ($_SESSION['is_admin']): ?>
                    <li><a href="inserimento.php">Aggiungi Gatto</a></li>
                <?php endif; ?>
                <li><a href="logout.php">Logout</a></li>
            <?php else: ?>
                <li><a href="login.php">Accedi</a></li>
                <li><a href="registrazione.php">Registrati</a></li>
            <?php endif; ?>
        </ul>
    </nav>
    <section class="stato-autenticazione" id="userStatusBox">
        <?php if (isset($_SESSION['username'])): ?>
            Utente: <strong id="loggedUsername"><?php echo htmlspecialchars($_SESSION['username']); ?></strong>
        <?php else: ?>
            <span>Stato: <em>non loggato</em></span>
        <?php endif; ?>
    </section>
</header>
