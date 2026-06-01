<?php

declare(strict_types=1);

session_start();

require_once "../includes/db.php";

header("Content-Type: application/json");

if (!isset($_SESSION["user_id"])) {

    echo json_encode([
        "success" => false,
        "message" => "Accesso richiesto."
    ]);

    exit;
}

$data = json_decode(
    file_get_contents("php://input"),
    true
);

$dataOra =
    trim($data["dataOra"] ?? "");

$gatti =
    $data["gatti"] ?? [];

if (
    $dataOra === "" ||
    !is_array($gatti) ||
    count($gatti) === 0
) {

    echo json_encode([
        "success" => false,
        "message" => "Dati non validi."
    ]);

    exit;
}

$conn =
    getModifierConnection();

$conn->begin_transaction();

try {

    $utenteId =
        $_SESSION["user_id"];

    /*
     * Inserimento prenotazione
     */

    $sql = "
        INSERT INTO prenotazioni_visite
        (
            utente_id,
            data_ora
        )
        VALUES
        (
            ?,
            ?
        )
    ";

    $stmt =
        $conn->prepare($sql);

    $stmt->bind_param(
        "is",
        $utenteId,
        $dataOra
    );

    $stmt->execute();

    $prenotazioneId =
        $conn->insert_id;

    $stmt->close();

    /*
     * Associazione gatti
     */

    $sql = "
        INSERT INTO visita_gatti
        (
            prenotazione_id,
            gatto_id
        )
        VALUES
        (
            ?,
            ?
        )
    ";

    $stmt =
        $conn->prepare($sql);

    foreach ($gatti as $gattoId) {

        $gattoId =
            (int)$gattoId;

        $stmt->bind_param(
            "ii",
            $prenotazioneId,
            $gattoId
        );

        $stmt->execute();
    }

    $stmt->close();

    $conn->commit();

    echo json_encode([
        "success" => true
    ]);

} catch (Throwable $exception) {

    $conn->rollback();

    echo json_encode([
        "success" => false,
        "message" =>
            "Errore durante la prenotazione."
    ]);
}

$conn->close();