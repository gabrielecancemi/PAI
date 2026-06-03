<?php
/**
 * Logout Page
 * Gestisce il logout sicuro dell'utente
 */

'use strict';

require_once __DIR__ . '/includes/auth.php';

// Esegui logout
logoutUser();

// Redirigidi a home con messaggio
header('Location: index.php?logout=success');
exit;
?>
