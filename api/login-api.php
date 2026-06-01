<?php

declare(strict_types=1);

session_start();

require_once "../includes/db.php";

header("Content-Type: application/json");

$username =
    trim($_POST["username"] ?? "");

$password =
    trim($_POST["password"] ?? "");

if (
    $username === "" ||
    $password === ""
) {

    echo json_encode([
        "success" => false,
        "message" => "Compilare tutti i campi."
    ]);

    exit;
}

$conn = getLectureConnection();

$sql = "
    SELECT
        id,
        username,
        password,
        is_admin
    FROM utenti
    WHERE username = ?
";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "s",
    $username
);

$stmt->execute();

$result = $stmt->get_result();

$user = $result->fetch_assoc();

$stmt->close();

$conn->close();

if (!$user) {

    echo json_encode([
        "success" => false,
        "message" => "Credenziali non valide."
    ]);

    exit;
}

/*
 * Password in chiaro nel dump.
 */
if (
    $password !== $user["password"]
) {

    echo json_encode([
        "success" => false,
        "message" => "Credenziali non valide."
    ]);

    exit;
}

/*
 * Sessione
 */

$_SESSION["user_id"] =
    $user["id"];

$_SESSION["username"] =
    $user["username"];

$_SESSION["is_admin"] =
    (bool)$user["is_admin"];

/*
 * Ricordami
 */

if (isset($_POST["remember"])) {

    $token =
        bin2hex(
            random_bytes(32)
        );

    $file =
        "../data/remember_tokens.json";

    $tokens = [];

    if (file_exists($file)) {

        $tokens =
            json_decode(
                file_get_contents($file),
                true
            );

        if (!is_array($tokens)) {
            $tokens = [];
        }
    }

    $tokens[$token] =
        $user["username"];

    file_put_contents(
        $file,
        json_encode(
            $tokens,
            JSON_PRETTY_PRINT
        )
    );

    setcookie(
        "remember_token",
        $token,
        time() + 259200,
        "/",
        "",
        false,
        true
    );
}

echo json_encode([
    "success" => true
]);