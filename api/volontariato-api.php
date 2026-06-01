<?php

declare(strict_types=1);

session_start();

require_once "../includes/db.php";

header("Content-Type: application/json");

if (!isset($_SESSION["user_id"])) {

    echo json_encode([
        "success" => false,
        "code" => "NOT_AUTHENTICATED",
        "message" => "Accesso richiesto."
    ]);

    exit;
}

$data = json_decode(
    file_get_contents("php://input"),
    true
);

$fasciaOraria =
    trim(
        $data["fascia_oraria"] ?? ""
    );

if ($fasciaOraria === "") {

    echo json_encode([
        "success" => false,
        "code" => "INVALID_DATA",
        "message" => "Data e ora non valide."
    ]);

    exit;
}

$conn =
    getModifierConnection();

try {

    /*
     * Controllo numero volontari
     */

    $sql = "
        SELECT COUNT(*) AS totale
        FROM turni_volontariato
        WHERE fascia_oraria = ?
    ";

    $stmt =
        $conn->prepare($sql);

    $stmt->bind_param(
        "s",
        $fasciaOraria
    );

    $stmt->execute();

    $result =
        $stmt->get_result();

    $row =
        $result->fetch_assoc();

    $totale =
        (int)$row["totale"];

    $stmt->close();

    if ($totale >= 2) {

        echo json_encode([
            "success" => false,
            "code" => "FULL_SLOT",
            "message" =>
                "Fascia oraria già completa."
        ]);

        $conn->close();

        exit;
    }

    /*
     * Evita doppia prenotazione
     * dello stesso utente
     */

    $utenteId =
        (int)$_SESSION["user_id"];

    $sql = "
        INSERT INTO turni_volontariato
        (
            utente_id,
            fascia_oraria
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
        $fasciaOraria
    );

    $stmt->execute();

    $stmt->close();

    echo json_encode([
        "success" => true
    ]);

} catch (mysqli_sql_exception $exception) {

    /*
     * Vincolo UNIQUE
     * (utente_id, fascia_oraria)
     */

    echo json_encode([
        "success" => false,
        "code" => "DUPLICATE_BOOKING",
        "message" =>
            "Hai già prenotato questa fascia oraria."
    ]);
}

$conn->close();