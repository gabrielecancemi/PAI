<?php
/**
 * API - Inserimento Gatti
 * Endpoint per aggiungere nuovi gatti (solo admin)
 * Utilizza utente 'modifier'
 */

'use strict';

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

session_start();

/**
 * Inserisce un nuovo gatto
 */
function insertCat() {
    // Verifica autenticazione e privilegi admin
    if (!isUserAuthenticated() || !isUserAdmin()) {
        return errorResponse('Non autorizzato', 403);
    }

    try {
        // Leggi JSON
        $rawInput = file_get_contents('php://input');
        $data = json_decode($rawInput, true);

        if (!$data) {
            return errorResponse('Dati JSON non validi', 400);
        }

        // Validazioni
        $validation_errors = [];

        // Nome
        $nome = $data['nome'] ?? '';
        if (empty($nome) || !preg_match('/^[a-zA-ZàèéìòùÀÈÉÌÒÙ\s]{2,50}$/', $nome)) {
            $validation_errors[] = 'Nome non valido';
        }

        // Descrizione
        $descrizione = $data['descrizione'] ?? '';
        if (empty($descrizione) || strlen($descrizione) < 10 || strlen($descrizione) > 1000) {
            $validation_errors[] = 'Descrizione deve essere 10-1000 caratteri';
        }

        // Peso
        $peso = (float)($data['peso'] ?? 0);
        if ($peso < 0.5 || $peso > 10) {
            $validation_errors[] = 'Peso deve essere tra 0.5 e 10 kg';
        }

        // Colore mantello
        $colore_mantello = $data['colore_mantello'] ?? '';
        if (empty($colore_mantello) || strlen($colore_mantello) > 30) {
            $validation_errors[] = 'Colore mantello non valido';
        }

        // Lunghezza pelo
        $lunghezza_pelo = $data['lunghezza_pelo'] ?? '';
        if (!in_array($lunghezza_pelo, ['Corto', 'Medio', 'Lungo'])) {
            $validation_errors[] = 'Lunghezza pelo non valida';
        }

        // Razza
        $razza = $data['razza'] ?? '';
        if (empty($razza) || strlen($razza) > 50) {
            $validation_errors[] = 'Razza non valida';
        }

        // Colore occhi
        $colore_occhi = $data['colore_occhi'] ?? '';
        if (empty($colore_occhi) || strlen($colore_occhi) > 30) {
            $validation_errors[] = 'Colore occhi non valido';
        }

        // Età
        $eta = (int)($data['eta'] ?? 0);
        if ($eta < 0 || $eta > 200) {
            $validation_errors[] = 'Età non valida (0-200 mesi)';
        }

        // Sesso
        $sesso = $data['sesso'] ?? '';
        if (!in_array($sesso, ['M', 'F'])) {
            $validation_errors[] = 'Sesso non valido';
        }

        // Data arrivo
        $data_arrivo = $data['data_arrivo'] ?? '';
        if (empty($data_arrivo) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $data_arrivo)) {
            $validation_errors[] = 'Data arrivo non valida';
        }

        // Verifica data non nel futuro
        if (new DateTime($data_arrivo) > new DateTime()) {
            $validation_errors[] = 'Data non può essere nel futuro';
        }

        if (!empty($validation_errors)) {
            return errorResponse(implode('; ', $validation_errors), 400);
        }

        // Connessione al database
        $db = getDbConnection('modifier');
        if (!$db) {
            return errorResponse('Errore di connessione al database', 503);
        }

        // Inserisci gatto
        $query = "INSERT INTO gatti (nome, descrizione, peso, colore_mantello, lunghezza_pelo, 
                                     razza, colore_occhi, eta, sesso, data_arrivo) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $result = executeModifyQuery($db, $query,
            [$nome, $descrizione, $peso, $colore_mantello, $lunghezza_pelo, 
             $razza, $colore_occhi, $eta, $sesso, $data_arrivo],
            'ssdssssiss'
        );

        closeDbConnection($db);

        if ($result <= 0) {
            return errorResponse('Errore durante l\'inserimento', 500);
        }

        return successResponse([
            'message' => 'Gatto inserito con successo',
            'cat_id' => getLastInsertId($db)
        ], 201);

    } catch (Exception $e) {
        error_log('Insert Cat API Error: ' . $e->getMessage());
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
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo insertCat();
} else {
    echo errorResponse('Metodo HTTP non supportato', 405);
}
?>
