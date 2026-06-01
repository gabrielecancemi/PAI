<?php

declare(strict_types=1);

session_start();

$savedUsername = "";

$filePath = "data/remember_tokens.json";

if (
    isset($_COOKIE["remember_token"]) &&
    file_exists($filePath)
) {

    $tokens = json_decode(
        file_get_contents($filePath),
        true
    );

    if (!is_array($tokens)) {
        $tokens = [];
    }

    $token = $_COOKIE["remember_token"];

    if (isset($tokens[$token])) {

        $savedUsername = $tokens[$token];
    }
}

?>

<!DOCTYPE html>

<html lang="it">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <meta
        name="description"
        content="Accesso al Gattile Felice">

    <title>
        Login
    </title>

    <link
        rel="stylesheet"
        href="assets/css/style.css">

    <link
        rel="stylesheet"
        href="assets/css/print.css"
        media="print">

</head>

<body>

<a
    href="#contenuto"
    class="skip-link">

    Salta al contenuto principale

</a>

<?php require_once "includes/header.php"; ?>

<main id="contenuto">

    <section>

        <h2>
            Accesso utenti
        </h2>

        <p>
            Inserisci le tue credenziali
            per accedere ai servizi del sito.
        </p>

        <form
            id="loginForm"
            novalidate>

            <div>

                <label for="username">

                    Username

                </label>

                <input
                    type="text"
                    id="username"
                    name="username"
                    required
                    autocomplete="username"
                    value="<?= htmlspecialchars($savedUsername) ?>">

            </div>

            <div>

                <label for="password">

                    Password

                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                    autocomplete="current-password">

            </div>

            <div>

                <input
                    type="checkbox"
                    id="remember"
                    name="remember">

                <label for="remember">

                    Ricordami per 72 ore

                </label>

            </div>

            <button type="submit">

                Accedi

            </button>

        </form>

        <div
            id="loginMessage"
            aria-live="polite">
        </div>

    </section>

</main>

<?php require_once "includes/footer.php"; ?>

<script src="assets/js/login.js"></script>

</body>

</html>