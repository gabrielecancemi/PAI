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

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        Volontariato
    </title>

    <link rel="stylesheet" href="assets/css/style.css">

    <link rel="stylesheet" href="assets/css/print.css" media="print">

</head>

<body>

    <a href="#main-content" class="skip-link">

        Salta al contenuto

    </a>

    <?php require_once "includes/header.php"; ?>

    <main id="main-content">

        <h1>
            Diventa volontario
        </h1>

        <?php if (!$isLogged): ?>

            <section class="warning-box">

                <p>

                    Devi effettuare
                    l'accesso per prenotare
                    un turno di volontariato.

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

        <?php else: ?>

            <section>

                <h2>
                    Prenotazione turno
                </h2>

                <form id="volunteerForm" novalidate>

                    <div class="form-group">

                        <label for="volunteerDateTime">

                            Data e ora del turno

                        </label>

                        <input type="datetime-local" id="volunteerDateTime" name="volunteerDateTime" required>

                    </div>

                    <p id="slotStatus" aria-live="polite">

                    </p>

                    <button type="submit">

                        Prenota turno

                    </button>

                </form>

                <div id="volunteerMessage" aria-live="polite">

                </div>

            </section>

        <?php endif; ?>

    </main>

    <?php require_once "includes/footer.php"; ?>

    <?php if ($isLogged): ?>

        <script src="assets/js/volontariato.js" defer>
        </script>

    <?php endif; ?>

</body>

</html>