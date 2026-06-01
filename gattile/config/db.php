<?php
// Impostazione dei parametri di connessione al database locale
define('DB_HOST', 'localhost');
define('DB_NAME', 'gattile_db');

/**
 * Ritorna un'istanza PDO configurata in base al principio del minimo privilegio.
 * @param string $ruolo Può essere 'lecture', 'modifier' o 'registrator'
 * @return PDO
 */
function getDBConnection($ruolo) {
    switch ($ruolo) {
        case 'lecture':
            $user = 'lecture';
            $pass = 'P@ssw0rd!';
            break;
        case 'modifier':
            $user = 'modifier';
            $pass = 'Str0ng#Admin9';
            break;
        case 'registrator':
            $user = 'registrator';
            $pass = 'ToB31nsert?';
            break;
        default:
            die("Errore di configurazione interna: ruolo database non riconosciuto.");
    }

    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Errori catturati come eccezioni
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Array associativi semplici
            PDO::ATTR_EMULATE_PREPARES   => false,                  // Disabilita l'emulazione per prevenire SQL injection
        ];
        return new PDO($dsn, $user, $pass, $options);
    } catch (PDOException $e) {
        // Log dell'errore lato server; nessun dettaglio tecnico sensibile trapela all'utente
        error_log($e->getMessage());
        die("Servizio temporaneamente non disponibile per motivi di sicurezza.");
    }
}
