-- ============================================================================
-- Gattile Database Schema
-- MySQL 5.7+ / MariaDB 10.2+
-- Charset: UTF-8 MB4 (full Unicode support)
-- ============================================================================

-- Create Database
CREATE DATABASE IF NOT EXISTS gattile_db;
USE gattile_db;

-- ============================================================================
-- TABLE: utenti
-- Gestione profili utenti con ruoli (admin/user)
-- ============================================================================

CREATE TABLE IF NOT EXISTS utenti (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(50) NOT NULL COMMENT 'Nome utente',
    cognome VARCHAR(50) NOT NULL COMMENT 'Cognome utente',
    indirizzo VARCHAR(100) NOT NULL COMMENT 'Indirizzo completo',
    username VARCHAR(50) UNIQUE NOT NULL COMMENT 'Username univoco',
    password VARCHAR(255) NOT NULL COMMENT 'Password hash BCrypt',
    is_admin BOOLEAN DEFAULT FALSE COMMENT 'Flag amministratore',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_username (username),
    INDEX idx_is_admin (is_admin),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLE: gatti
-- Catalogo gatti disponibili per adozione
-- ============================================================================

CREATE TABLE IF NOT EXISTS gatti (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(50) NOT NULL COMMENT 'Nome gatto',
    descrizione VARCHAR(1000) NOT NULL COMMENT 'Descrizione carattere',
    peso DECIMAL(3,1) NOT NULL COMMENT 'Peso in kg',
    colore_mantello VARCHAR(30) NOT NULL COMMENT 'Colore mantello',
    lunghezza_pelo ENUM('Corto', 'Medio', 'Lungo') NOT NULL,
    razza VARCHAR(50) NOT NULL COMMENT 'Razza felina',
    colore_occhi VARCHAR(30) NOT NULL COMMENT 'Colore occhi',
    eta INT NOT NULL COMMENT 'Età in mesi',
    sesso ENUM('M', 'F') NOT NULL COMMENT 'Maschio/Femmina',
    data_arrivo DATE NOT NULL COMMENT 'Data arrivo al rifugio',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_data_arrivo (data_arrivo),
    INDEX idx_nome (nome),
    INDEX idx_colore_mantello (colore_mantello),
    FULLTEXT INDEX ft_search (nome, descrizione, razza)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLE: prenotazioni_visite
-- Prenotazioni per visite conoscitive ai gatti
-- ============================================================================

CREATE TABLE IF NOT EXISTS prenotazioni_visite (
    id INT PRIMARY KEY AUTO_INCREMENT,
    utente_id INT NOT NULL COMMENT 'FK utente che prenota',
    data_ora DATETIME NOT NULL COMMENT 'Data e ora visita',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY fk_utente (utente_id) REFERENCES utenti(id) ON DELETE CASCADE,
    INDEX idx_utente_id (utente_id),
    INDEX idx_data_ora (data_ora)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLE: visita_gatti
-- Relazione many-to-many: prenotazione visita <-> gatti
-- ============================================================================

CREATE TABLE IF NOT EXISTS visita_gatti (
    id INT PRIMARY KEY AUTO_INCREMENT,
    prenotazione_id INT NOT NULL COMMENT 'FK prenotazione',
    gatto_id INT NOT NULL COMMENT 'FK gatto',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY fk_prenotazione (prenotazione_id) REFERENCES prenotazioni_visite(id) ON DELETE CASCADE,
    FOREIGN KEY fk_gatto (gatto_id) REFERENCES gatti(id) ON DELETE CASCADE,
    INDEX idx_prenotazione_id (prenotazione_id),
    INDEX idx_gatto_id (gatto_id),
    UNIQUE KEY uk_prenotazione_gatto (prenotazione_id, gatto_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLE: turni_volontariato
-- Prenotazioni turni volontariato con limite 2 per fascia
-- ============================================================================

CREATE TABLE IF NOT EXISTS turni_volontariato (
    id INT PRIMARY KEY AUTO_INCREMENT,
    utente_id INT NOT NULL COMMENT 'FK volontario',
    fascia_oraria DATETIME NOT NULL COMMENT 'Data e ora inizio turno',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY fk_utente_turno (utente_id) REFERENCES utenti(id) ON DELETE CASCADE,
    INDEX idx_utente_id (utente_id),
    INDEX idx_fascia_oraria (fascia_oraria),
    UNIQUE KEY uk_utente_fascia (utente_id, fascia_oraria)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- VIEW: disponibilita_turni_volontariato
-- Mostra disponibilità per ogni fascia oraria
-- ============================================================================

CREATE OR REPLACE VIEW disponibilita_turni_volontariato AS
SELECT 
    DATE(fascia_oraria) as data,
    TIME(fascia_oraria) as ora,
    COUNT(*) as volontari_attuali,
    (2 - COUNT(*)) as posti_disponibili,
    (COUNT(*) >= 2) as slot_pieno
FROM turni_volontariato
WHERE fascia_oraria >= NOW()
GROUP BY DATE(fascia_oraria), TIME(fascia_oraria);

-- ============================================================================
-- DATA: Sample data for testing
-- ============================================================================

-- Insert admin user (password: Admin2026!)
INSERT INTO utenti (nome, cognome, indirizzo, username, password, is_admin) VALUES
('Anna', 'Rossi', 'Via Roma 10, Milano', 'anna_admin', '$2y$12$n3LfVGBTx7yxY/gWbkKyAeJQCfqxN/0rNa/HpvfJhzKQi0g5JQqHS', TRUE),
('Fabio', 'Bianchi', 'Via Milano 20, Roma', 'fabio_admin', '$2y$12$n3LfVGBTx7yxY/gWbkKyAeJQCfqxN/0rNa/HpvfJhzKQi0g5JQqHS', TRUE);

-- Insert regular users (password: Password123!)
INSERT INTO utenti (nome, cognome, indirizzo, username, password, is_admin) VALUES
('Mario', 'Volontario', 'Via Torino 30, Torino', 'mario_volontario', '$2y$12$LhwMfnc3mZxPOmKvXrFw3.OJHyN1LgVG3mFTvKKVvH5Fl8Nn0k4vW', FALSE),
('Elena', 'Bianchi', 'Via Genova 40, Genova', 'elena_b', '$2y$12$LhwMfnc3mZxPOmKvXrFw3.OJHyN1LgVG3mFTvKKVvH5Fl8Nn0k4vW', FALSE),
('Luca', 'Neri', 'Via Napoli 50, Napoli', 'luca_neri', '$2y$12$LhwMfnc3mZxPOmKvXrFw3.OJHyN1LgVG3mFTvKKVvH5Fl8Nn0k4vW', FALSE),
('Giulia', 'Blu', 'Via Venezia 60, Venezia', 'giulia_b', '$2y$12$LhwMfnc3mZxPOmKvXrFw3.OJHyN1LgVG3mFTvKKVvH5Fl8Nn0k4vW', FALSE);

-- Insert sample cats
INSERT INTO gatti (nome, descrizione, peso, colore_mantello, lunghezza_pelo, razza, colore_occhi, eta, sesso, data_arrivo) VALUES
('Micia', 'Gattina affettuosa e giocherellona, adora stare in braccio. Perfetta per famiglie con bambini. Socialissima e sempre pronta a giocare.', 3.5, 'Tigrato', 'Medio', 'Europeo', 'Verdi', 12, 'F', '2025-11-15'),
('Garfield', 'Gatto pigro e dolce che ama dormire al sole. Non è aggressivo, molto tranquillo e ideale per anziani. Apprezza i coccoli ma a suo ritmo.', 5.2, 'Arancione', 'Medio', 'Europeo', 'Gialli', 36, 'M', '2025-10-20'),
('Luna', 'Gattina indipendente ma affettuosa. Adora il gioco interattivo. Curiosa e intelligente, impara rapidamente le abitudini domestiche.', 2.8, 'Nero', 'Corto', 'Europeo', 'Azzurri', 8, 'F', '2025-12-01'),
('Simba', 'Gatto giovane e energico. Perfetto per chi ama i gatti vivaci e giocherelloni. Ha molta energia e adora saltare e arrampicarsi.', 4.1, 'Calico', 'Corto', 'Europeo', 'Gialli', 6, 'M', '2025-12-10'),
('Principessa', 'Gattina aristocratica e elegante. Ama il lusso e i coccoli. Preferisce ambienti tranquilli e una sola famiglia. Bellissima con occhi azzurri.', 3.2, 'Bianco', 'Lungo', 'Persiano', 'Azzurri', 24, 'F', '2025-09-30'),
('Felix', 'Gatto socievole e amichevole. Ama gli altri gatti e i cani. Ideale per un ambiente multispecies. Molto intelligente e curioso.', 4.0, 'Grigio', 'Medio', 'Europeo', 'Verdi', 18, 'M', '2025-11-05'),
('Beatrice', 'Gattina dolcissima e timida. Necessita di una famiglia paziente. Una volta presa confidenza diventa molto affettuosa. Adora i nascondigli.', 2.5, 'Arancione', 'Corto', 'Europeo', 'Ambra', 4, 'F', '2025-12-05');

-- ============================================================================
-- USERS DATABASE - Per segregazione privilegi
-- Creazione utenti con privilegi minimali
-- ============================================================================

-- NOTA: I seguenti comandi vanno eseguiti come root per creare gli utenti
-- Non verranno eseguiti automaticamente ma solo documentati

/*
-- Utente LECTURE (sola lettura)
CREATE USER IF NOT EXISTS 'lecture'@'localhost' IDENTIFIED BY 'P@ssw0rd!';
GRANT SELECT ON gattile_db.* TO 'lecture'@'localhost';

-- Utente MODIFIER (lettura + modifica)
CREATE USER IF NOT EXISTS 'modifier'@'localhost' IDENTIFIED BY 'Str0ng#Admin9';
GRANT SELECT, INSERT, UPDATE ON gattile_db.* TO 'modifier'@'localhost';
GRANT DELETE ON gattile_db.prenotazioni_visite TO 'modifier'@'localhost';
GRANT DELETE ON gattile_db.turni_volontariato TO 'modifier'@'localhost';
GRANT DELETE ON gattile_db.visita_gatti TO 'modifier'@'localhost';

-- Utente REGISTRATOR (solo insert su utenti)
CREATE USER IF NOT EXISTS 'registrator'@'localhost' IDENTIFIED BY 'ToB31nsert?';
GRANT INSERT ON gattile_db.utenti TO 'registrator'@'localhost';

FLUSH PRIVILEGES;
*/

-- ============================================================================
-- END OF SCHEMA
-- ============================================================================
