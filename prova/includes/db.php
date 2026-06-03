<?php
/**
 * Database Configuration
 * Gestisce le connessioni al database con privilegi minimali per ogni operazione
 * WCAG 2.1 Level AA compliant - Aria-live regions per messaggi di errore
 */

'use strict';

// Configurazioni credenziali database - 3 utenti con privilegi minimali
const DB_CONFIGS = [
    'lecture' => [
        'host' => 'localhost',
        'user' => 'lecture',
        'password' => 'P@ssw0rd!',
        'database' => 'gattile_db',
        'privileges' => 'SELECT'
    ],
    'modifier' => [
        'host' => 'localhost',
        'user' => 'modifier',
        'password' => 'Str0ng#Admin9',
        'database' => 'gattile_db',
        'privileges' => 'SELECT, INSERT, UPDATE'
    ],
    'registrator' => [
        'host' => 'localhost',
        'user' => 'registrator',
        'password' => 'ToB31nsert?',
        'database' => 'gattile_db',
        'privileges' => 'INSERT (utenti only)'
    ]
];

/**
 * Ottiene la connessione al database con i privilegi appropriati
 * 
 * @param string $user_type Tipo di utente: 'lecture', 'modifier', 'registrator'
 * @return mysqli Connessione al database o null in caso di errore
 */
function getDbConnection($user_type = 'lecture') {
    try {
        if (!isset(DB_CONFIGS[$user_type])) {
            throw new Exception("Tipo di utente database non valido: $user_type");
        }

        $config = DB_CONFIGS[$user_type];
        
        // Crea connessione mysqli con opzioni di sicurezza
        $connection = new mysqli(
            $config['host'],
            $config['user'],
            $config['password'],
            $config['database']
        );

        // Verifica errori di connessione
        if ($connection->connect_error) {
            // Log dell'errore (non esporre i dettagli all'utente)
            error_log('Database Connection Error [' . $user_type . ']: ' . $connection->connect_error);
            return null;
        }

        // Imposta charset UTF-8 per sicurezza e compatibilità
        $connection->set_charset('utf8mb4');

        return $connection;

    } catch (Exception $e) {
        error_log('Database Configuration Error: ' . $e->getMessage());
        return null;
    }
}

/**
 * Chiude una connessione al database
 * 
 * @param mysqli $connection Connessione da chiudere
 */
function closeDbConnection($connection) {
    if ($connection instanceof mysqli && $connection->ping()) {
        $connection->close();
    }
}

/**
 * Esegue una query preparata in modo sicuro contro SQL injection
 * 
 * @param mysqli $connection Connessione al database
 * @param string $query Query SQL con placeholder ?
 * @param array $params Array di parametri da bindare
 * @param string $types Stringa con tipi di dati (s=string, i=int, d=double, b=blob)
 * @return array|null Array con risultati o null in caso di errore
 */
function executeQuery($connection, $query, $params = [], $types = '') {
    try {
        if (!$connection || !$connection->ping()) {
            throw new Exception('Connessione al database persa');
        }

        $stmt = $connection->prepare($query);
        if (!$stmt) {
            error_log('Query Preparation Error: ' . $connection->error);
            return null;
        }

        // Bind parametri se presenti
        if (!empty($params) && !empty($types)) {
            if (!$stmt->bind_param($types, ...$params)) {
                error_log('Parameter Binding Error: ' . $stmt->error);
                return null;
            }
        }

        // Esegui query
        if (!$stmt->execute()) {
            error_log('Query Execution Error: ' . $stmt->error);
            return null;
        }

        // Ottieni risultati
        $result = $stmt->get_result();
        $data = [];
        
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }

        $stmt->close();
        return $data;

    } catch (Exception $e) {
        error_log('Query Execution Exception: ' . $e->getMessage());
        return null;
    }
}

/**
 * Esegue una query di INSERT/UPDATE/DELETE e ritorna il numero di righe modificate
 * 
 * @param mysqli $connection Connessione al database
 * @param string $query Query SQL con placeholder ?
 * @param array $params Array di parametri da bindare
 * @param string $types Stringa con tipi di dati
 * @return int Numero di righe modificate o -1 in caso di errore
 */
function executeModifyQuery($connection, $query, $params = [], $types = '') {
    try {
        if (!$connection || !$connection->ping()) {
            throw new Exception('Connessione al database persa');
        }

        $stmt = $connection->prepare($query);
        if (!$stmt) {
            error_log('Query Preparation Error: ' . $connection->error);
            return -1;
        }

        if (!empty($params) && !empty($types)) {
            if (!$stmt->bind_param($types, ...$params)) {
                error_log('Parameter Binding Error: ' . $stmt->error);
                return -1;
            }
        }

        if (!$stmt->execute()) {
            error_log('Query Execution Error: ' . $stmt->error);
            return -1;
        }

        $affected_rows = $stmt->affected_rows;
        $stmt->close();
        return $affected_rows;

    } catch (Exception $e) {
        error_log('Modify Query Exception: ' . $e->getMessage());
        return -1;
    }
}

/**
 * Ottiene l'ID dell'ultimo inserimento
 * 
 * @param mysqli $connection Connessione al database
 * @return int ID dell'ultimo insert
 */
function getLastInsertId($connection) {
    return $connection->insert_id;
}
?>
