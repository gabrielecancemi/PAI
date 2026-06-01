<?php
header('Content-Type: application/json');
require_once '../config/db.php';

try {
    $db = getDBConnection('lecture'); // Sola lettura per catalogo pubblico
    $stmt = $db->query("SELECT id, nome, descrizione, peso, colore_mantello, lunghezza_pelo, razza, colore_occhi, eta, sesso, data_arrivo FROM gatti");
    echo json_encode($stmt->fetchAll());
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Incapacità temporanea di recupero dati catalogo.']);
}

