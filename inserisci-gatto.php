<?php

declare(strict_types=1);

session_start();

if (
    !isset($_SESSION["user_id"]) ||
    !isset($_SESSION["is_admin"]) ||
    $_SESSION["is_admin"] !== true
) {

    http_response_code(403);

    exit("Accesso negato.");
}

?>

<!DOCTYPE html>

<html lang="it">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>
        Inserimento nuovo gatto
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
    href="#main-content"
    class="skip-link">

    Salta al contenuto

</a>

<?php require_once "includes/header.php"; ?>

<main id="main-content">

    <h1>
        Inserisci un nuovo gatto
    </h1>

    <form
        id="catForm"
        novalidate>

        <div class="form-group">
            <label for="nome">Nome</label>
            <input
                type="text"
                id="nome"
                name="nome"
                required>
        </div>

        <div class="form-group">
            <label for="descrizione">
                Descrizione
            </label>

            <textarea
                id="descrizione"
                name="descrizione"
                rows="5"
                required>
            </textarea>
        </div>

        <div class="form-group">
            <label for="peso">
                Peso (kg)
            </label>

            <input
                type="number"
                step="0.01"
                id="peso"
                name="peso"
                required>
        </div>

        <div class="form-group">
            <label for="colore_mantello">
                Colore mantello
            </label>

            <input
                type="text"
                id="colore_mantello"
                name="colore_mantello"
                required>
        </div>

        <div class="form-group">
            <label for="lunghezza_pelo">
                Lunghezza pelo
            </label>

            <input
                type="text"
                id="lunghezza_pelo"
                name="lunghezza_pelo"
                required>
        </div>

        <div class="form-group">
            <label for="razza">
                Razza
            </label>

            <input
                type="text"
                id="razza"
                name="razza"
                required>
        </div>

        <div class="form-group">
            <label for="colore_occhi">
                Colore occhi
            </label>

            <input
                type="text"
                id="colore_occhi"
                name="colore_occhi"
                required>
        </div>

        <div class="form-group">
            <label for="eta">
                Età (mesi)
            </label>

            <input
                type="number"
                id="eta"
                name="eta"
                required>
        </div>

        <div class="form-group">

            <label for="sesso">
                Sesso
            </label>

            <select
                id="sesso"
                name="sesso"
                required>

                <option value="">
                    Seleziona
                </option>

                <option value="M">
                    Maschio
                </option>

                <option value="F">
                    Femmina
                </option>

            </select>

        </div>

        <div class="form-group">

            <label for="data_arrivo">
                Data arrivo
            </label>

            <input
                type="date"
                id="data_arrivo"
                name="data_arrivo"
                required>

        </div>

        <button type="submit">

            Inserisci gatto

        </button>

    </form>

    <div
        id="catMessage"
        aria-live="polite">

    </div>

</main>

<?php require_once "includes/footer.php"; ?>

<script
    src="assets/js/inserisci-gatto.js"
    defer>
</script>

</body>

</html>