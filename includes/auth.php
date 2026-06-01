<?php

declare(strict_types=1);

/**
 * Verifica se esiste una sessione attiva.
 */
function isLogged(): bool
{
    return isset($_SESSION["user_id"]);
}

/**
 * Verifica se l'utente autenticato è amministratore.
 */
function isAdmin(): bool
{
    return isset($_SESSION["is_admin"])
        && $_SESSION["is_admin"] === true;
}

/**
 * Permette l'accesso solo agli utenti autenticati.
 */
function requireLogin(): void
{
    if (!isLogged()) {

        header("Location: login.php");
        exit;
    }
}

/**
 * Permette l'accesso solo agli amministratori.
 */
function requireAdmin(): void
{
    if (!isAdmin()) {

        header("Location: index.php");
        exit;
    }
}