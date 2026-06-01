<?php

declare(strict_types=1);

require_once "../includes/db.php";

header("Content-Type: application/json");

$nome = trim($_POST["nome"] ?? "");
$cognome = trim($_POST["cognome"] ?? "");
$indirizzo = trim($_POST["indirizzo"] ?? "");
$username = trim($_POST["username"] ?? "");
$password = $_POST["password"] ?? "";

if (
    $nome === "" ||
    $cognome === "" ||
    $indirizzo === "" ||
    $username === "" ||
    $password === ""
) {

    echo json_encode([
        "success" => false,
        "message" => "Tutti i campi sono obbligatori."
    ]);

    exit;
}

/*
 * Stesse regex del client.
 */

$usernameRegex =
    "/^[A-Za-z][A-Za-z0-9_]*$/";

$passwordRegex =
    "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z\d]).{8,16}$/";

if (!preg_match($usernameRegex, $username)) {

    echo json_encode([
        "success" => false,
        "message" => "Username non valido."
    ]);

    exit;
}

if (!preg_match($passwordRegex, $password)) {

    echo json_encode([
        "success" => false,
        "message" => "Password non valida."
    ]);

    exit;
}

$conn = getRegistratorConnection();

$sql = "
    INSERT INTO utenti
    (
        nome,
        cognome,
        indirizzo,
        username,
        password,
        is_admin
    )
    VALUES
    (
        ?,
        ?,
        ?,
        ?,
        ?,
        FALSE
    )
";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "sssss",
    $nome,
    $cognome,
    $indirizzo,
    $username,
    $password
);

try {

    $stmt->execute();

    echo json_encode([
        "success" => true
    ]);

} catch (mysqli_sql_exception $exception) {

    /*
     * Duplicate username
     * errore MySQL 1062
     */

    if ($exception->getCode() === 1062) {

        echo json_encode([
            "success" => false,
            "message" => "Username già esistente."
        ]);

    } else {

        echo json_encode([
            "success" => false,
            "message" => "Errore durante la registrazione."
        ]);
    }
}

$stmt->close();
$conn->close();