<?php
/**
 * Authentication and Session Management
 * Gestisce login, logout, sessioni e cookie di remembering
 * Implementa principi OWASP e sicurezza avanzata
 */

'use strict';

if (session_status() === PHP_SESSION_NONE) {
    // Configurazione di sicurezza delle sessioni
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => true, // HTTPS only
        'httponly' => true, // Non accessibile via JavaScript
        'samesite' => 'Strict' // Protezione CSRF
    ]);
    session_start();
}

require_once __DIR__ . '/db.php';

// Costanti per la gestione dei cookie
const REMEMBER_TOKEN_EXPIRY = 72 * 3600; // 72 ore in secondi
const REMEMBER_COOKIE_NAME = 'gattile_remember_token';
const SESSION_TIMEOUT = 30 * 60; // 30 minuti di inattività

/**
 * Controlla se un utente è autenticato
 * 
 * @return bool True se autenticato
 */
function isUserAuthenticated() {
    return isset($_SESSION['user_id']) && isset($_SESSION['username']);
}

/**
 * Controlla se l'utente autenticato è amministratore
 * 
 * @return bool True se admin
 */
function isUserAdmin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
}

/**
 * Esegue il login dell'utente
 * 
 * @param string $username Username
 * @param string $password Password
 * @param bool $remember Se ricordare l'utente (cookie token)
 * @return array Array con status e messaggio di errore
 */
function loginUser($username, $password, $remember = false) {
    $response = [
        'success' => false,
        'message' => '',
        'user_id' => null,
        'username' => null,
        'is_admin' => false
    ];

    try {
        // Validazione input base
        if (empty($username) || empty($password)) {
            $response['message'] = 'Username e password sono obbligatori';
            return $response;
        }

        // Connessione al database con privilegi di lettura
        $db = getDbConnection('lecture');
        if (!$db) {
            $response['message'] = 'Errore di connessione al database. Riprova più tardi.';
            return $response;
        }

        // Query preparata per prevenire SQL injection
        $query = "SELECT id, username, password, is_admin, nome, cognome FROM utenti WHERE username = ?";
        $users = executeQuery($db, $query, [$username], 's');
        closeDbConnection($db);

        if (!$users || empty($users)) {
            // Messaggio generico per sicurezza (non rivelare se l'utente esiste)
            $response['message'] = 'Credenziali non valide';
            return $response;
        }

        $user = $users[0];

        // Verifica password con password_verify (hash bcrypt)
        if (!password_verify($password, $user['password'])) {
            $response['message'] = 'Credenziali non valide';
            return $response;
        }

        // Login riuscito - imposta sessione
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['is_admin'] = (bool)$user['is_admin'];
        $_SESSION['nome'] = $user['nome'];
        $_SESSION['cognome'] = $user['cognome'];
        $_SESSION['login_time'] = time();
        $_SESSION['last_activity'] = time();

        // Se selezionato "ricordami", genera token e cookie
        if ($remember) {
            createRememberToken($user['id'], $username);
        }

        $response['success'] = true;
        $response['message'] = 'Login avvenuto con successo';
        $response['user_id'] = $user['id'];
        $response['username'] = $user['username'];
        $response['is_admin'] = (bool)$user['is_admin'];

        return $response;

    } catch (Exception $e) {
        error_log('Login Error: ' . $e->getMessage());
        $response['message'] = 'Errore durante il login';
        return $response;
    }
}

/**
 * Crea un token di remember e lo salva in cookie
 * Il token non contiene credenziali in chiaro
 * 
 * @param int $user_id ID dell'utente
 * @param string $username Username
 */
function createRememberToken($user_id, $username) {
    try {
        // Genera token casuale opaco
        $token = bin2hex(random_bytes(32));
        $token_hash = hash('sha256', $token);
        $expiry = time() + REMEMBER_TOKEN_EXPIRY;

        // Salva token nel file JSON (in produzione usare database)
        $remember_tokens_file = __DIR__ . '/../data/remember_tokens.json';
        $tokens = [];

        if (file_exists($remember_tokens_file)) {
            $json = file_get_contents($remember_tokens_file);
            $tokens = json_decode($json, true) ?: [];
        }

        // Pulisci token scaduti
        $tokens = array_filter($tokens, function($t) {
            return $t['expiry'] > time();
        });

        // Aggiungi nuovo token
        $tokens[] = [
            'user_id' => $user_id,
            'username' => $username,
            'token_hash' => $token_hash,
            'expiry' => $expiry,
            'created_at' => time()
        ];

        file_put_contents(
            $remember_tokens_file,
            json_encode($tokens, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
        );

        // Imposta cookie con il token opaco
        setcookie(
            REMEMBER_COOKIE_NAME,
            $token,
            $expiry,
            '/',
            '',
            true, // Secure
            true  // HttpOnly
        );

    } catch (Exception $e) {
        error_log('Remember Token Creation Error: ' . $e->getMessage());
    }
}

/**
 * Verifica il cookie di remember token
 * Se valido, pre-compila il login form
 * 
 * @return string|null Username da pre-compilare o null
 */
function getRememberedUsername() {
    if (!isset($_COOKIE[REMEMBER_COOKIE_NAME])) {
        return null;
    }

    try {
        $token = $_COOKIE[REMEMBER_COOKIE_NAME];
        $token_hash = hash('sha256', $token);

        // Leggi i token salvati
        $remember_tokens_file = __DIR__ . '/../data/remember_tokens.json';
        if (!file_exists($remember_tokens_file)) {
            return null;
        }

        $json = file_get_contents($remember_tokens_file);
        $tokens = json_decode($json, true) ?: [];

        // Cerca token valido
        foreach ($tokens as $saved_token) {
            if ($saved_token['token_hash'] === $token_hash && $saved_token['expiry'] > time()) {
                return $saved_token['username'];
            }
        }

        return null;

    } catch (Exception $e) {
        error_log('Remember Token Verification Error: ' . $e->getMessage());
        return null;
    }
}

/**
 * Esegue il logout dell'utente
 * Rimuove sessione e cookie di remember
 */
function logoutUser() {
    // Rimuovi cookie di remember
    if (isset($_COOKIE[REMEMBER_COOKIE_NAME])) {
        setcookie(REMEMBER_COOKIE_NAME, '', time() - 3600, '/');
    }

    // Distruggi sessione
    $_SESSION = [];
    session_destroy();
}

/**
 * Controlla timeout di inattività della sessione
 * 
 * @return bool True se sessione ancora valida
 */
function checkSessionTimeout() {
    if (!isUserAuthenticated()) {
        return false;
    }

    $now = time();
    $last_activity = $_SESSION['last_activity'] ?? $now;

    if (($now - $last_activity) > SESSION_TIMEOUT) {
        logoutUser();
        return false;
    }

    $_SESSION['last_activity'] = $now;
    return true;
}

/**
 * Redirige a login se utente non autenticato
 * Utile per proteggere pagine admin
 * 
 * @param bool $admin_only Se true, richiede essere admin
 */
function requireAuthentication($admin_only = false) {
    if (!isUserAuthenticated()) {
        header('Location: login.php');
        exit;
    }

    if ($admin_only && !isUserAdmin()) {
        header('Location: gatti.php');
        exit;
    }
}

/**
 * Restituisce il nome completo dell'utente autenticato
 * 
 * @return string Nome completo o "Non loggato"
 */
function getUserDisplayName() {
    if (!isUserAuthenticated()) {
        return 'Non loggato';
    }

    $nome = $_SESSION['nome'] ?? '';
    $cognome = $_SESSION['cognome'] ?? '';

    return trim($nome . ' ' . $cognome) ?: $_SESSION['username'];
}

/**
 * Restituisce lo username dell'utente autenticato
 * 
 * @return string Username o null
 */
function getCurrentUsername() {
    return $_SESSION['username'] ?? null;
}

/**
 * Restituisce l'ID dell'utente autenticato
 * 
 * @return int|null ID utente o null
 */
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

?>
