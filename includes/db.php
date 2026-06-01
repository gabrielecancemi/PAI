<?php

declare(strict_types=1);

/**
 * Connessione in sola lettura.
 * Utilizzata per:
 * - homepage
 * - login
 * - elenco gatti
 * - controlli volontariato
 */
function getLectureConnection(): mysqli
{
    $connection = new mysqli(
        "localhost",
        "lecture",
        "P@ssw0rd!",
        "gattile_db"
    );

    if ($connection->connect_error) {
        die("Errore connessione database.");
    }

    $connection->set_charset("utf8mb4");

    return $connection;
}

/**
 * Connessione lettura/scrittura.
 * Utilizzata per:
 * - inserimento gatti
 * - prenotazioni visite
 * - volontariato
 */
function getModifierConnection(): mysqli
{
    $connection = new mysqli(
        "localhost",
        "modifier",
        "Str0ng#Admin9",
        "gattile_db"
    );

    if ($connection->connect_error) {
        die("Errore connessione database.");
    }

    $connection->set_charset("utf8mb4");

    return $connection;
}

/**
 * Connessione dedicata esclusivamente
 * alla registrazione utenti.
 */
function getRegistratorConnection(): mysqli
{
    $connection = new mysqli(
        "localhost",
        "registrator",
        "ToB31nsert?",
        "gattile_db"
    );

    if ($connection->connect_error) {
        die("Errore connessione database.");
    }

    $connection->set_charset("utf8mb4");

    return $connection;
}