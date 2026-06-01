<?php

declare(strict_types=1);

session_start();

require_once "../includes/db.php";

header("Content-Type: application/json");

if (
    !isset($_SESSION["user_id"]) ||
    !isset($_SESSION["is_admin"]) ||
    $_SESSION["is_admin"] !== true
) {

    echo json_encode([
        "success" => false,
        "message" => "Accesso non autorizzato."
    ]);

    exit;
}

$data = json_decode(
    file_get_contents("php://input"),
    true
);

$nome =
    trim($data["nome"] ?? "");

$descrizione =
    trim($data["descrizione"] ?? "");

$peso =
    (float)($data["peso"] ?? 0);

$coloreMantello =
    trim($data["colore_mantello"] ?? "");

$lunghezzaPelo =
    trim($data["lunghezza_pelo"] ?? "");

$razza =
    trim($data["razza"] ?? "");

$coloreOcchi =
    trim($data["colore_occhi"] ?? "");

$eta =
    (int)($data["eta"] ?? -1);

$sesso =
    trim($data["sesso"] ?? "");

$dataArrivo =
    trim($data["data_arrivo"] ?? "");

if (
    $nome === "" ||
    $descrizione === "" ||
    $peso <= 0 ||
    $coloreMantello === "" ||
    $lunghezzaPelo === "" ||
    $razza === "" ||
    $coloreOcchi === "" ||
    $eta < 0 ||
    $dataArrivo === "" ||
    !in_array($sesso, ["M", "F"], true)
) {

    echo json_encode([
        "success" => false,
        "message" => "Dati non validi."
    ]);

    exit;
}

$conn =
    getModifierConnection();

$sql = "
    INSERT INTO gatti
    (
        nome,
        descrizione,
        peso,
        colore_mantello,
        lunghezza_pelo,
        razza,
        colore_occhi,
        eta,
        sesso,
        data_arrivo
    )
    VALUES
    (
        ?,
        ?,
        ?,
        ?,
        ?,
        ?,
        ?,
        ?,
        ?,
        ?
    )
";

$stmt =
    $conn->prepare($sql);

$stmt->bind_param(
    "ssdssssiss",
    $nome,
    $descrizione,
    $peso,
    $coloreMantello,
    $lunghezzaPelo,
    $razza,
    $coloreOcchi,
    $eta,
    $sesso,
    $dataArrivo
);

$success =
    $stmt->execute();

$stmt->close();
$conn->close();

echo json_encode([
    "success" => $success
]);