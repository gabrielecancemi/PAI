<?php
/**
 * API - Booking Visite
 * Endpoint per prenotare visite conoscitive ai gatti
 * Utilizza utente 'modifier' (select, insert, update)
 */

'use strict';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('X-Content-Type-Options: nosniff');

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

session_start();

/**
 * Crea una prenotazione di visita
 */
function createBooking() {
    // Verifica autenticazione
    if (!isUserAuthenticated()) {
        return errorResponse('Non autenticato', 401);
    }

    try {
        // Leggi JSON dal body
        $rawInput = file_get_contents('php://input');
        $data = json_decode($rawInput, true);

        if (!$data) {
            return errorResponse('Dati JSON non validi', 400);
        }

        // Valida parametri
        $visitDate = $data['visit_date'] ?? '';
        $visitTime = $data['visit_time'] ?? '';
        $catIds = $data['cat_ids'] ?? [];

        // Validazione data
        if (empty($visitDate) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $visitDate)) {
            return errorResponse('Data non valida', 400);
        }

        // Validazione orario
        if (empty($visitTime) || !preg_match('/^\d{2}:\d{2}$/', $visitTime)) {
            return errorResponse('Orario non valido', 400);
        }

        // Validazione gatti
        if (empty($catIds) || !is_array($catIds)) {
            return errorResponse('Seleziona almeno un gatto', 400);
        }

        // Verifica che la data sia nel futuro
        $visitDateTime = new DateTime($visitDate . ' ' . $visitTime);
        if ($visitDateTime <= new DateTime()) {
            return errorResponse('La data deve essere nel futuro', 400);
        }

        // Connessione al database
        $db = getDbConnection('modifier');
        if (!$db) {
            return errorResponse('Errore di connessione al database', 503);
        }

        $userId = getCurrentUserId();

        // Crea prenotazione
        $datetimeStr = $visitDate . ' ' . $visitTime . ':00';
        $query = "INSERT INTO prenotazioni_visite (utente_id, data_ora) VALUES (?, ?)";
        $result = executeModifyQuery($db, $query, [$userId, $datetimeStr], 'is');

        if ($result <= 0) {
            closeDbConnection($db);
            return errorResponse('Errore durante la creazione della prenotazione', 500);
        }

        $bookingId = getLastInsertId($db);

        // Aggiungi gatti alla prenotazione
        $errors = [];
        foreach ($catIds as $catId) {
            $catId = (int)$catId;
            if ($catId > 0) {
                $query = "INSERT INTO visita_gatti (prenotazione_id, gatto_id) VALUES (?, ?)";
                $res = executeModifyQuery($db, $query, [$bookingId, $catId], 'ii');
                if ($res <= 0) {
                    $errors[] = "Errore nell'associazione gatto ID $catId";
                }
            }
        }

        closeDbConnection($db);

        if (!empty($errors)) {
            return errorResponse('Prenotazione creata ma con errori: ' . implode('; ', $errors), 500);
        }

        return successResponse([
            'booking_id' => $bookingId,
            'message' => 'Visita prenotata con successo'
        ], 201);

    } catch (Exception $e) {
        error_log('Booking API Error: ' . $e->getMessage());
        return errorResponse('Errore interno del server', 500);
    }
}

/**
 * Ottiene prenotazioni dell'utente autenticato
 */
function getMyBookings() {
    if (!isUserAuthenticated()) {
        return errorResponse('Non autenticato', 401);
    }

    try {
        $db = getDbConnection('lecture');
        if (!$db) {
            return errorResponse('Errore di connessione al database', 503);
        }

        $userId = getCurrentUserId();
        $query = "SELECT pv.id, pv.data_ora, 
                         GROUP_CONCAT(g.nome SEPARATOR ', ') as gatti_nomi
                  FROM prenotazioni_visite pv
                  LEFT JOIN visita_gatti vg ON pv.id = vg.prenotazione_id
                  LEFT JOIN gatti g ON vg.gatto_id = g.id
                  WHERE pv.utente_id = ?
                  GROUP BY pv.id
                  ORDER BY pv.data_ora DESC";

        $results = executeQuery($db, $query, [$userId], 'i');
        closeDbConnection($db);

        if ($results === null) {
            return errorResponse('Errore nel recupero delle prenotazioni', 500);
        }

        return successResponse($results);

    } catch (Exception $e) {
        error_log('Get Bookings API Error: ' . $e->getMessage());
        return errorResponse('Errore interno del server', 500);
    }
}

/**
 * Cancella una prenotazione
 */
function cancelBooking() {
    if (!isUserAuthenticated()) {
        return errorResponse('Non autenticato', 401);
    }

    try {
        $rawInput = file_get_contents('php://input');
        $data = json_decode($rawInput, true);

        $bookingId = (int)($data['booking_id'] ?? 0);
        if ($bookingId <= 0) {
            return errorResponse('ID prenotazione non valido', 400);
        }

        $db = getDbConnection('modifier');
        if (!$db) {
            return errorResponse('Errore di connessione al database', 503);
        }

        $userId = getCurrentUserId();

        // Verifica ownership
        $query = "SELECT id FROM prenotazioni_visite WHERE id = ? AND utente_id = ?";
        $check = executeQuery($db, $query, [$bookingId, $userId], 'ii');

        if (!$check || empty($check)) {
            closeDbConnection($db);
            return errorResponse('Prenotazione non trovata o non autorizzato', 404);
        }

        // Cancella prenotazione (cascade delete su visita_gatti)
        $query = "DELETE FROM prenotazioni_visite WHERE id = ?";
        $result = executeModifyQuery($db, $query, [$bookingId], 'i');

        closeDbConnection($db);

        if ($result > 0) {
            return successResponse(['message' => 'Prenotazione cancellata']);
        } else {
            return errorResponse('Errore nella cancellazione', 500);
        }

    } catch (Exception $e) {
        error_log('Cancel Booking API Error: ' . $e->getMessage());
        return errorResponse('Errore interno del server', 500);
    }
}

/**
 * Risposta di successo
 */
function successResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    return json_encode([
        'success' => true,
        'data' => $data,
        'timestamp' => date('c')
    ], JSON_UNESCAPED_UNICODE);
}

/**
 * Risposta di errore
 */
function errorResponse($message, $statusCode = 400) {
    http_response_code($statusCode);
    return json_encode([
        'success' => false,
        'error' => $message,
        'timestamp' => date('c')
    ], JSON_UNESCAPED_UNICODE);
}

// Route handling
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    echo createBooking();
} elseif ($method === 'GET') {
    $action = htmlspecialchars($_GET['action'] ?? 'my');
    switch ($action) {
        case 'my':
            echo getMyBookings();
            break;
        default:
            echo errorResponse('Azione non riconosciuta', 400);
    }
} else {
    echo errorResponse('Metodo HTTP non supportato', 405);
}
?>
