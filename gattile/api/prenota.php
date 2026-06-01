<?php
header('Content-Type: application/json');
require_once '../config/db.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Utente non autenticato.']);
        exit;
    }

    $dati = json_decode(file_get_contents('php://input'), true);
    $gatti_selezionati = $dati['gatti_ids'] ?? [];
    $data_ora = $dati['data_ora'] ?? '';

    if (empty($gatti_selezionati) || empty($data_ora)) {
        http_response_code(400);
        echo json_encode(['error' => 'Dati incompleti o selezione gatti mancante.']);
        exit;
    }

    $db = getDBConnection('modifier');
    $db->beginTransaction();

    try {
        $stmt = $db->prepare("INSERT INTO prenotazioni_visite (utente_id, data_ora) VALUES (?, ?)");
        $stmt->execute([$_SESSION['user_id'], $data_ora]);
        $id_prenotazione = $db->lastInsertId();

        // Popolamento tabella pivot di relazione molti-a-molti
        $stmtM2M = $db->prepare("INSERT INTO visita_gatti (prenotazione_id, gatto_id) VALUES (?, ?)");
        foreach ($gatti_selezionati as $id_gatto) {
            $stmtM2M->execute([$id_prenotazione, $id_gatto]);
        }

        $db->commit();
        echo json_encode(['success' => 'Appuntamento inserito a sistema con successo.']);
    } catch (Exception $e) {
        $db->rollBack();
        http_response_code(500);
        echo json_encode(['error' => 'Errore nel processamento interno della prenotazione.']);
    }
}

