<?php

declare(strict_types=1);

session_start();

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
        content="Registrazione nuovo utente">

    <title>
        Registrazione
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
            Registrazione
        </h2>

        <p>
            Compila il modulo per creare
            un nuovo account.
        </p>

        <form
            id="registrationForm"
            novalidate>

            <div>

                <label for="nome">

                    Nome

                </label>

                <input
                    type="text"
                    id="nome"
                    name="nome"
                    required>

            </div>

            <div>

                <label for="cognome">

                    Cognome

                </label>

                <input
                    type="text"
                    id="cognome"
                    name="cognome"
                    required>

            </div>

            <div>

                <label for="indirizzo">

                    Indirizzo

                </label>

                <input
                    type="text"
                    id="indirizzo"
                    name="indirizzo"
                    required>

            </div>

            <div>

                <label for="username">

                    Username

                </label>

                <input
                    type="text"
                    id="username"
                    name="username"
                    required>

            </div>

            <div>

                <label for="password">

                    Password

                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    required>

            </div>

            <div>

                <label for="confirmPassword">

                    Conferma Password

                </label>

                <input
                    type="password"
                    id="confirmPassword"
                    name="confirmPassword"
                    required>

            </div>

            <button type="submit">

                Registrati

            </button>

        </form>

        <div
            id="registrationMessage"
            aria-live="polite">
        </div>

    </section>

</main>

<?php require_once "includes/footer.php"; ?>

<script src="js/registrazione.js"></script>

</body>

</html>