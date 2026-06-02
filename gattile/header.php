<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$currentPage = basename($_SERVER['PHP_SELF']);

$pageTitles = [
    'index.php' => 'Home',
    'adozioni.php' => 'Adotta un gatto',
    'volontariato.php' => 'Diventa Volontario',
    'login.php' => 'Accedi',
    'registrazione.php' => 'Registrati',
    'inserimento.php' => 'Aggiungi Gatto'
];

$pageTitle = $pageTitles[$currentPage] ?? 'Gattile ';
?>

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <meta name="author" content="Gabriele Cancemi">
    <meta name="description" content="Gattile Municipale - Rifugio e Adozioni">
    <meta name="keywords" content="gattile, rifugio, adozioni, gatti, volontariato">
    <title><?= $pageTitle ?></title>
    <link rel="stylesheet" href="css/stile.css">
    <script src="https://unpkg.com/react@18/umd/react.development.js" crossorigin></script>
    <script src="https://unpkg.com/react-dom@18/umd/react-dom.development.js" crossorigin></script>
    <script src="https://unpkg.com/@babel/standalone/babel.min.js"></script>
</head>

<body>
    <header class="header" role="banner">
        <a href="index.php" class="brand-logo" aria-label="Torna alla Home Page">Gattile Rifugio</a>
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
        <section class="stato-autenticazione" id="userStatusBox">
            <?php if (isset($_SESSION['username'])): ?>
                <p class="utente-info">
                    👤
                    <strong><?= $_SESSION['username'] ?></strong>
                    <?php if (!empty($_SESSION['is_admin'])): ?>
                        <small class="badge-gatto">Amministratore</small>
                    <?php else: ?>
                        <small class="badge-gatto">Utente</small>
                    <?php endif; ?>
                </p>
                <ul class="azioni-account">
                    <li>
                        <a href="logout.php" class="btn-account btn-logout">
                            Logout
                        </a>
                    </li>
                </ul>

            <?php else: ?>
                <p>
                    Stato:
                    <em>non loggato</em>
                </p>
                <ul class="azioni-account">
                    <li>
                        <a href="login.php"
                            class="btn-account btn-login <?= $currentPage === 'login.php' ? 'active' : '' ?>">
                            Accedi
                        </a>
                    </li>
                    <li>
                        <a href="registrazione.php"
                            class="btn-account btn-login <?= $currentPage === 'registrazione.php' ? 'active' : '' ?>">
                            Registrati
                        </a>
                    </li>
                </ul>

            <?php endif; ?>

        </section>
    </header>