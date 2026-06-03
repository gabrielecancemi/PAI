<?php
/**
 * API - Volontariato
 * Gestione turni di volontariato con limite 2 per fascia
 * Utilizza 'modifier' per accesso DB
 */

'use strict';

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

session_start();

const MAX_VOLUNTEERS_PER_SLOT = 2;

/**
 * Ottiene disponibilità per una data
 */
function getAvailability() {
    try {
        $date = sanitizeParam($_GET['date'] ?? '');
        
        if (empty($date) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return errorResponse('Data non valida', 400);
        }

        $db = getDbConnection('lecture');
        if (!$db) {
            return errorResponse('Errore di connessione', 503);
        }

        // Conta volontari per ogni fascia oraria
        $query = "SELECT 
                    TIME(fascia_oraria) as ora,
                    COUNT(*) as count
                  FROM turni_volontariato
                  WHERE DATE(fascia_oraria) = ?
                  GROUP BY ora
                  ORDER BY ora";

        $results = executeQuery($db, $query, [$date], 's');
        closeDbConnection($db);

        if (!$results) {
            $results = [];
        }

        // Costruisci array con tutte le fasce orarie
        $timeslots = [
            '09:00', '10:00', '11:00', '12:00',
            '14:00', '15:00', '16:00', '17:00', '18:00', '19:00'
        ];

        $availability = [];
        foreach ($timeslots as $ts) {
            $availability[$ts] = 0;
        }

        foreach ($results as $row) {
            $availability[$row['ora']] = (int)$row['count'];
        }

        return successResponse($availability);

    } catch (Exception $e) {
        error_log('Get Availability Error: ' . $e->getMessage());
        return errorResponse('Errore interno', 500);
    }
}

/**
 * Ottiene i miei turni di volontariato
 */
function getMyShifts() {
    if (!isUserAuthenticated()) {
        return errorResponse('Non autenticato', 401);
    }

    try {
        $db = getDbConnection('lecture');
        if (!$db) {
            return errorResponse('Errore di connessione', 503);
        }

        $userId = getCurrentUserId();
        $query = "SELECT 
                    id,
                    DATE_FORMAT(fascia_oraria, '%d/%m/%Y') as data,
                    TIME_FORMAT(fascia_oraria, '%H:%i') as ora,
                    fascia_oraria
                  FROM turni_volontariato
                  WHERE utente_id = ? AND fascia_oraria >= NOW()
                  ORDER BY fascia_oraria ASC";

        $results = executeQuery($db, $query, [$userId], 'i');
        closeDbConnection($db);

        if (!$results) {
            $results = [];
        }

        return successResponse($results);

    } catch (Exception $e) {
        error_log('Get My Shifts Error: ' . $e->getMessage());
        return errorResponse('Errore interno', 500);
    }
}

/**
 * Crea prenotazioni di turni
 */
function createShifts() {
    if (!isUserAuthenticated()) {
        return errorResponse('Non autenticato', 401);
    }

    try {
        $rawInput = file_get_contents('php://input');
        $data = json_decode($rawInput, true);

        if (!$data) {
            return errorResponse('Dati JSON non validi', 400);
        }

        $date = $data['date'] ?? '';
        $timeslots = $data['timeslots'] ?? [];
        $notes = $data['notes'] ?? '';

        // Validazioni
        if (empty($date) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return errorResponse('Data non valida', 400);
        }

        if (!is_array($timeslots) || empty($timeslots)) {
            return errorResponse('Seleziona almeno una fascia oraria', 400);
        }

        // Verifica data nel futuro
        $dateObj = new DateTime($date);
        if ($dateObj <= new DateTime('today')) {
            return errorResponse('La data deve essere nel futuro', 400);
        }

        $db = getDbConnection('modifier');
        if (!$db) {
            return errorResponse('Errore di connessione', 503);
        }

        $userId = getCurrentUserId();
        $errors = [];
        $successCount = 0;

        foreach ($timeslots as $time) {
            // Valida formato orario
            if (!preg_match('/^\d{2}:\d{2}$/', $time)) {
                $errors[] = "Orario non valido: $time";
                continue;
            }

            $datetime = $date . ' ' . $time . ':00';
            
            // Verifica limite volontari per questa fascia
            $query = "SELECT COUNT(*) as count FROM turni_volontariato 
                      WHERE fascia_oraria = ?";
            $check = executeQuery($db, $query, [$datetime], 's');

            if ($check && isset($check[0]['count']) && $check[0]['count'] >= MAX_VOLUNTEERS_PER_SLOT) {
                $errors[] = "Fascia $time già al completo (2/{MAX_VOLUNTEERS_PER_SLOT} volontari)";
                continue;
            }

            // Verifica duplicate booking dello stesso utente
            $query = "SELECT id FROM turni_volontariato 
                      WHERE utente_id = ? AND fascia_oraria = ?";
            $duplicate = executeQuery($db, $query, [$userId, $datetime], 'is');

            if ($duplicate && !empty($duplicate)) {
                $errors[] = "Hai già prenotato la fascia $time per questa data";
                continue;
            }

            // Inserisci turno
            $query = "INSERT INTO turni_volontariato (utente_id, fascia_oraria) 
                      VALUES (?, ?)";
            $result = executeModifyQuery($db, $query, [$userId, $datetime], 'is');

            if ($result > 0) {
                $successCount++;
            } else {
                $errors[] = "Errore nell'inserimento della fascia $time";
            }
        }

        closeDbConnection($db);

        if ($successCount === 0) {
            return errorResponse(implode('; ', $errors), 400);
        }

        $message = "$successCount turno/i prenotato/i con successo";
        if (!empty($errors)) {
            $message .= ". Avvisi: " . implode('; ', $errors);
        }

        return successResponse(['message' => $message], 201);

    } catch (Exception $e) {
        error_log('Create Shifts Error: ' . $e->getMessage());
        return errorResponse('Errore interno', 500);
    }
}

/**
 * Cancella un turno
 */
function deleteShift() {
    if (!isUserAuthenticated()) {
        return errorResponse('Non autenticato', 401);
    }

    try {
        $rawInput = file_get_contents('php://input');
        $data = json_decode($rawInput, true);

        $shiftId = (int)($data['shift_id'] ?? 0);
        if ($shiftId <= 0) {
            return errorResponse('ID turno non valido', 400);
        }

        $db = getDbConnection('modifier');
        if (!$db) {
            return errorResponse('Errore di connessione', 503);
        }

        $userId = getCurrentUserId();

        // Verifica ownership
        $query = "SELECT id FROM turni_volontariato WHERE id = ? AND utente_id = ?";
        $check = executeQuery($db, $query, [$shiftId, $userId], 'ii');

        if (!$check || empty($check)) {
            closeDbConnection($db);
            return errorResponse('Turno non trovato', 404);
        }

        // Cancella turno
        $query = "DELETE FROM turni_volontariato WHERE id = ?";
        $result = executeModifyQuery($db, $query, [$shiftId], 'i');

        closeDbConnection($db);

        if ($result > 0) {
            return successResponse(['message' => 'Turno cancellato']);
        } else {
            return errorResponse('Errore nella cancellazione', 500);
        }

    } catch (Exception $e) {
        error_log('Delete Shift Error: ' . $e->getMessage());
        return errorResponse('Errore interno', 500);
    }
}

/**
 * Sanitizza parametri
 */
function sanitizeParam($param) {
    return htmlspecialchars(strip_tags($param), ENT_QUOTES, 'UTF-8');
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
    echo createShifts();
} elseif ($method === 'DELETE') {
    echo deleteShift();
} elseif ($method === 'GET') {
    $action = sanitizeParam($_GET['action'] ?? 'availability');
    switch ($action) {
        case 'availability':
            echo getAvailability();
            break;
        case 'my':
            echo getMyShifts();
            break;
        default:
            echo errorResponse('Azione non riconosciuta', 400);
    }
} else {
    echo errorResponse('Metodo HTTP non supportato', 405);
}
?>
