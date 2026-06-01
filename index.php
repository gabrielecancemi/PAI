<?php

declare(strict_types=1);

session_start();

require_once "includes/db.php";

$conn = getLectureConnection();

$sql = "
    SELECT *
    FROM gatti
    ORDER BY data_arrivo DESC
    LIMIT 2
";

$result = $conn->query($sql);

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
        content="Gattile Felice - adozioni e volontariato">

    <title>
        Gattile Felice
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

    <section class="hero">

        <h2>
            Trova il tuo nuovo amico felino
        </h2>

        <p>
            Ogni anno molti gatti vengono
            accolti nella nostra struttura.
            Aiutaci a trovare una famiglia
            per ciascuno di loro.
        </p>

    </section>

    <section>

        <h2>
            Chi siamo
        </h2>

        <p>
            Il Gattile Felice si occupa
            dell'accoglienza, della cura
            e dell'adozione di gatti
            abbandonati o in difficoltà.
        </p>

    </section>

    <section>

        <h2>
            Adozioni
        </h2>

        <p>
            Consulta le schede dei nostri
            ospiti e prenota una visita
            conoscitiva presso la struttura.
        </p>

    </section>

    <section>

        <h2>
            Volontariato
        </h2>

        <p>
            Se desideri aiutarci puoi
            offrire il tuo tempo come
            volontario scegliendo le
            fasce orarie disponibili.
        </p>

    </section>

    <section>

        <h2>
            Nuovi Arrivi
        </h2>

        <div class="new-arrivals">

            <?php while ($gatto = $result->fetch_assoc()): ?>

                <article class="cat-card">

                    <img
                        src="images/placeholder-cat.png"
                        alt="Immagine segnaposto di <?= htmlspecialchars($gatto["nome"]) ?>">

                    <h3>

                        <?= htmlspecialchars($gatto["nome"]) ?>

                    </h3>

                    <p>

                        <?= htmlspecialchars($gatto["descrizione"]) ?>

                    </p>

                    <p>

                        <strong>Età:</strong>

                        <?= htmlspecialchars((string)$gatto["eta"]) ?>
                        mesi

                    </p>

                    <p>

                        <strong>Arrivato il:</strong>

                        <?= htmlspecialchars($gatto["data_arrivo"]) ?>

                    </p>

                </article>

            <?php endwhile; ?>

        </div>

    </section>

</main>

<?php require_once "includes/footer.php"; ?>

</body>

</html>