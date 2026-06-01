<?php

declare(strict_types=1);

session_start();

$isLogged =
    isset($_SESSION["user_id"]);

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
        content="Visualizza i gatti ospitati dal gattile e prenota una visita conoscitiva.">

    <title>
        I nostri gatti
    </title>

    <link
        rel="stylesheet"
        href="assets/css/style.css">

    <link
        rel="stylesheet"
        href="assets/css/print.css"
        media="print">

    <link
        rel="stylesheet"
        href="react-app/dist/assets/index-nqMpL4T3.css">

</head>

<body>

<a
    href="#main-content"
    class="skip-link">

    Salta al contenuto principale

</a>

<?php require_once "includes/header.php"; ?>

<main id="main-content">

    <header class="page-header">

        <h1>
            I nostri gatti
        </h1>

        <p>
            Consulta tutti i felini presenti in struttura.
            Puoi cercare, filtrare e ordinare le schede.
        </p>

    </header>

    <script>

        window.isLogged =
            <?= $isLogged ? "true" : "false" ?>;

    </script>

    <section
        aria-labelledby="cats-title">

        <h2 id="cats-title">
            Elenco gatti
        </h2>

        <div id="root"></div>

    </section>

<?php if ($isLogged): ?>

    <section
        aria-labelledby="visit-title"
        class="visit-section">

        <h2 id="visit-title">
            Prenota una visita
        </h2>

        <form
            id="visitForm"
            novalidate>

            <div class="form-group">

                <label for="visitDateTime">

                    Data e ora della visita

                </label>

                <input
                    type="datetime-local"
                    id="visitDateTime"
                    name="visitDateTime"
                    required>

            </div>

            <div
                id="selectedCatsContainer">

                <p>

                    Nessun gatto selezionato.

                </p>

            </div>

            <button type="submit">

                Prenota visita

            </button>

        </form>

        <div
            id="visitMessage"
            aria-live="polite">

        </div>

    </section>

<?php else: ?>

    <section
        class="warning-box">

        <h2>
            Prenotazione visite
        </h2>

        <p>

            Per prenotare una visita è necessario
            effettuare l'accesso oppure registrarsi.

        </p>

        <p>

            <a href="login.php">
                Accedi
            </a>

            oppure

            <a href="registrazione.php">
                registrati
            </a>

        </p>

    </section>

<?php endif; ?>

</main>

<?php require_once "includes/footer.php"; ?>

<?php if ($isLogged): ?>

<script
    src="assets/js/visita.js"
    defer>
</script>

<?php endif; ?>

<script
    type="module"
    src="react-app/dist/assets/index-ClcLagr-.js">
</script>

</body>

</html>