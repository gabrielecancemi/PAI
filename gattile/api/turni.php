<?php
header('Content-Type: application/json');
require_once '../config/db.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

$db = getDBConnection('modifier');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Ritorna le aggregazioni di conteggio per disabilitare dinamicamente sul client i turni pieni
    $stmt = $db->query("SELECT fascia_oraria, COUNT(*) as totale FROM turni_volontariato GROUP BY fascia_oraria");
    echo json_encode($stmt->fetchAll());
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Sessione utente non valida. Accedi nuovamente.']);
        exit;
    }

    $dati = json_decode(file_get_contents('php://input'), true);
    $turni_scelti = $dati['turni'] ?? [];
    $id_utente = $_SESSION['user_id'];

    // Avvio della transazione atomica per prevenire corse critiche (Race Condition)
    $db->beginTransaction();
    try {
        foreach ($turni_scelti as $fascia) {
            // Verifica di sicurezza bloccante lato server
            $stmtCheck = $db->prepare("SELECT COUNT(*) FROM turni_volontariato WHERE fascia_oraria = ?");
            $stmtCheck->execute([$fascia]);
            if ($stmtCheck->fetchColumn() >= 2) {
                $db->rollBack(); // Annulla tutte le operazioni in blocco
                http_response_code(400);
                echo json_encode(['error' => "Spiacenti, il turno richiesto delle ore $fascia è stato appena saturato da un altro utente."]);
                exit;
            }

            // Inserimento idempotente se non presente
            $stmtInsert = $db->prepare("INSERT INTO turni_volontariato (utente_id, fascia_oraria) VALUES (?, ?) ON DUPLICATE KEY UPDATE fascia_oraria=fascia_oraria");
            $stmtInsert->execute([$id_utente, $fascia]);
        }
        $db->commit(); // Conferma definitiva sul database
        echo json_encode(['success' => 'La tua prenotazione oraria è stata salvata con successo.']);
    } catch (Exception $e) {
        $db->rollBack();
        http_response_code(500);
        echo json_encode(['error' => 'Eccezione server durate il salvataggio dei turni.']);
    }
}

