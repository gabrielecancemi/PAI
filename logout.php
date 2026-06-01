<?php

declare(strict_types=1);

session_start();

/*
 * Svuota la sessione.
 */
$_SESSION = [];

/*
 * Distrugge la sessione.
 */
session_destroy();

/*
 * Ritorno alla home.
 */
header("Location: index.php");

exit;