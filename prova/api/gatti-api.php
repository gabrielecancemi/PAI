<?php
/**
 * API - Gatti
 * Endpoint per ottenere dati dei gatti in JSON
 * Utilizza utente 'lecture' (sola lettura)
 * WCAG e REST API compliant
 */

'use strict';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('X-Content-Type-Options: nosniff');

require_once __DIR__ . '/../includes/db.php';

/**
 * Sanitizza parametri di ricerca
 * 
 * @param string $param Parametro
 * @return string Sanitizzato
 */
function sanitizeParam($param) {
    return htmlspecialchars(strip_tags($param), ENT_QUOTES, 'UTF-8');
}

/**
 * Ottiene tutti i gatti con filtri opzionali
 */
function getAllCats() {
    try {
        $db = getDbConnection('lecture');
        if (!$db) {
            return errorResponse('Errore di connessione al database', 503);
        }

        // Parametri di ricerca e ordinamento
        $search = sanitizeParam($_GET['search'] ?? '');
        $sort = sanitizeParam($_GET['sort'] ?? 'data_arrivo');
        $order = sanitizeParam($_GET['order'] ?? 'DESC');

        // Validazione parametri
        if (!in_array($sort, ['data_arrivo', 'eta', 'nome', 'colore_mantello'])) {
            $sort = 'data_arrivo';
        }
        if (!in_array($order, ['ASC', 'DESC'])) {
            $order = 'DESC';
        }

        // Costruisci query con WHERE clause se search presente
        $query = "SELECT id, nome, descrizione, peso, colore_mantello, lunghezza_pelo, 
                         razza, colore_occhi, eta, sesso, data_arrivo 
                  FROM gatti";

        $params = [];
        $types = '';

        if (!empty($search)) {
            $query .= " WHERE nome LIKE ? OR descrizione LIKE ?";
            $searchTerm = "%$search%";
            $params = [$searchTerm, $searchTerm];
            $types = 'ss';
        }

        $query .= " ORDER BY $sort $order";

        // Esegui query
        $results = executeQuery($db, $query, $params, $types);
        closeDbConnection($db);

        if ($results === null) {
            return errorResponse('Errore durante la ricerca', 500);
        }

        // Aggiungi URL placeholder immagine
        foreach ($results as &$cat) {
            $cat['immagine'] = '/PAI/prova/assets/images/cat-placeholder.svg';
            $cat['data_arrivo'] = date('d/m/Y', strtotime($cat['data_arrivo']));
        }

        return successResponse($results);

    } catch (Exception $e) {
        error_log('Get Cats API Error: ' . $e->getMessage());
        return errorResponse('Errore interno del server', 500);
    }
}

/**
 * Ottiene ultimi N gatti arrivati
 */
function getLatestCats() {
    try {
        $db = getDbConnection('lecture');
        if (!$db) {
            return errorResponse('Errore di connessione al database', 503);
        }

        $limit = (int)($_GET['limit'] ?? 2);
        if ($limit < 1 || $limit > 20) {
            $limit = 2;
        }

        $query = "SELECT id, nome, descrizione, peso, colore_mantello, lunghezza_pelo, 
                         razza, colore_occhi, eta, sesso, data_arrivo 
                  FROM gatti 
                  ORDER BY data_arrivo DESC 
                  LIMIT ?";

        $results = executeQuery($db, $query, [$limit], 'i');
        closeDbConnection($db);

        if ($results === null) {
            return errorResponse('Errore durante la ricerca', 500);
        }

        // Aggiungi URL immagine
        foreach ($results as &$cat) {
            $cat['immagine'] = '/PAI/prova/assets/images/cat-placeholder.svg';
            $cat['data_arrivo'] = date('d/m/Y', strtotime($cat['data_arrivo']));
        }

        return successResponse($results);

    } catch (Exception $e) {
        error_log('Get Latest Cats API Error: ' . $e->getMessage());
        return errorResponse('Errore interno del server', 500);
    }
}

/**
 * Ottiene dettagli di un singolo gatto
 */
function getCatDetails() {
    try {
        $catId = (int)($_GET['id'] ?? 0);
        if ($catId <= 0) {
            return errorResponse('ID gatto non valido', 400);
        }

        $db = getDbConnection('lecture');
        if (!$db) {
            return errorResponse('Errore di connessione al database', 503);
        }

        $query = "SELECT id, nome, descrizione, peso, colore_mantello, lunghezza_pelo, 
                         razza, colore_occhi, eta, sesso, data_arrivo 
                  FROM gatti 
                  WHERE id = ?";

        $results = executeQuery($db, $query, [$catId], 'i');
        closeDbConnection($db);

        if ($results === null || empty($results)) {
            return errorResponse('Gatto non trovato', 404);
        }

        $cat = $results[0];
        $cat['immagine'] = '/PAI/prova/assets/images/cat-placeholder.svg';
        $cat['data_arrivo'] = date('d/m/Y', strtotime($cat['data_arrivo']));

        return successResponse($cat);

    } catch (Exception $e) {
        error_log('Get Cat Details API Error: ' . $e->getMessage());
        return errorResponse('Errore interno del server', 500);
    }
}

/**
 * Conta gatti disponibili
 */
function countCats() {
    try {
        $db = getDbConnection('lecture');
        if (!$db) {
            return errorResponse('Errore di connessione al database', 503);
        }

        $query = "SELECT COUNT(*) as total FROM gatti";
        $results = executeQuery($db, $query);
        closeDbConnection($db);

        if ($results === null || empty($results)) {
            return errorResponse('Errore nel conteggio', 500);
        }

        return successResponse(['total' => (int)$results[0]['total']]);

    } catch (Exception $e) {
        error_log('Count Cats API Error: ' . $e->getMessage());
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
$action = sanitizeParam($_GET['action'] ?? 'all');

switch ($action) {
    case 'all':
        echo getAllCats();
        break;
    case 'latest':
        echo getLatestCats();
        break;
    case 'detail':
        echo getCatDetails();
        break;
    case 'count':
        echo countCats();
        break;
    default:
        echo errorResponse('Azione non riconosciuta', 400);
}
?>
