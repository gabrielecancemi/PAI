<?php

declare(strict_types=1);

require_once "../includes/db.php";

header("Content-Type: application/json");

$dateTime =
    trim($_GET["datetime"] ?? "");

if ($dateTime === "") {

    echo json_encode([
        "available" => false,
        "current" => 0,
        "max" => 2
    ]);

    exit;
}

$conn =
    getLectureConnection();

$sql = "
    SELECT COUNT(*) AS totale
    FROM turni_volontariato
    WHERE fascia_oraria = ?
";

$stmt =
    $conn->prepare($sql);

$stmt->bind_param(
    "s",
    $dateTime
);

$stmt->execute();

$result =
    $stmt->get_result();

$row =
    $result->fetch_assoc();

$current =
    (int)$row["totale"];

$stmt->close();
$conn->close();

echo json_encode([
    "available" => $current < 2,
    "current" => $current,
    "max" => 2
]);