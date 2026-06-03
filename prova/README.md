# GATTILE - Sistema di Adozioni e Volontariato

## Setup Iniziale

### Prerequisiti
- PHP 7.4+ con MySQLi estensione
- MySQL 5.7+ o MariaDB
- XAMPP/LAMP/LEMP stack (o simile)

### Installazione

1. **Database Setup**
   ```
   Apri phpMyAdmin: http://localhost/phpmyadmin
   Crea nuovo database: gattile_db
   Importa il file SQL con la struttura (fornito separatamente)
   ```

2. **File Upload**
   ```
   Copia la cartella 'prova' in:
   - XAMPP: C:\xampp\htdocs\PAI\prova
   - LAMP: /var/www/html/PAI/prova
   ```

3. **Permessi Directory**
   ```
   Linux/Mac:
   chmod 755 prova/
   chmod 755 prova/data/
   chmod 644 prova/**/*.php
   chmod 644 prova/**/*.css
   chmod 644 prova/**/*.js
   ```

4. **Primo Accesso**
   ```
   URL: http://localhost/PAI/prova/
   Login demo:
     - Username: anna_admin (admin)
     - Password: Admin2026!
   ```

### Configurazione

Verificare connessione DB in `/includes/db.php`:
- Host: localhost
- Database: gattile_db
- Utenti predefiniti:
  - lecture / P@ssw0rd! (read-only)
  - modifier / Str0ng#Admin9 (read-write)
  - registrator / ToB31nsert? (insert-only)

### Struttura File

```
prova/
├── index.php              (Homepage)
├── login.php              (Autenticazione)
├── registrazione.php      (Registrazione utenti)
├── gatti.php              (Galleria gatti + prenotazione)
├── inserimento-gatto.php  (Gestione gatti - admin)
├── volontariato.php       (Prenotazione turni)
├── prenotazione-visite.php (Gestione prenotazioni)
├── logout.php             (Logout)
├── includes/
│   ├── db.php             (Funzioni database)
│   ├── auth.php           (Autenticazione/sessioni)
│   ├── header.php         (Template header)
│   └── footer.php         (Template footer)
├── api/
│   ├── gatti-api.php      (API gatti - GET)
│   ├── booking-api.php    (API prenotazioni)
│   ├── cat-insert-api.php (API inserimento gatti)
│   └── volunteer-api.php  (API volontariato)
├── assets/
│   ├── css/
│   │   ├── style.css      (CSS principale)
│   │   └── accessibility.css
│   ├── js/
│   │   └── form-validator.js
│   └── images/
│       └── cat-placeholder.svg
├── data/
│   └── remember_tokens.json (Token remember-me)
├── progettoSito.txt       (Documentazione)
└── s123456_struttura.txt  (Struttura progetto)
```

### Account Demo

**Amministratore:**
- Username: anna_admin / fabio_admin
- Password: Admin2026!
- Accesso: Gestione gatti, inserimento nuovi

**Utente Normale:**
- Username: mario_volontario / elena_b / luca_neri / giulia_b
- Password: Password123!
- Accesso: Prenotazione visite, volontariato

### Funzionalità Principale

1. **Homepage (index.php)**
   - Presentazione gattile
   - Ultimi 2 gatti arrivati
   - Link a tutte le sezioni

2. **Galleria Gatti (gatti.php)**
   - Componente React interattivo
   - Ricerca e filtri
   - Prenotazione visite per utenti loggati
   - Selezione multipla gatti

3. **Inserimento Gatti (inserimento-gatto.php)**
   - Solo admin
   - Form completo con validazione
   - Placeholder automatico

4. **Volontariato (volontariato.php)**
   - Selezione turni con limite 2 volontari
   - Validazione lato client e server
   - Gestione prenotazioni

5. **Autenticazione**
   - Login con remember-me (72 ore)
   - Registrazione con validazione ristretta
   - Logout sicuro

### Validazione Client-Side

Tutti i form includono:
- Validazione in tempo reale
- Messaggi di errore inline
- Supporto screen reader
- WCAG 2.1 Level AA

### Messaggi di Errore

L'applicazione fornisce feedback esplicito su:
- Credenziali non valide
- Campi obbligatori mancanti
- Formati non corretti
- Errori di connessione
- Conflitti nei dati (es: slot pieni)

### Cookie e Privacy

- Banner informativo cookie
- Tasto cancellazione cookie semplice
- Token opaco per remember-me
- No tracking/analytics

### Performance

- Lazy loading immagini
- CSS/JS ottimizzati
- Database query indicizzate
- AJAX per operazioni asincrone

### Sicurezza

- Prepared statements su tutte le query SQL
- Password hashing BCrypt
- CSRF protection (SameSite cookies)
- Session timeout 30 minuti
- Input sanitization
- Privilege segregation database

### Debugging

Per abilitare debug:
```php
// In includes/db.php aggiungere:
ini_set('display_errors', 1);
error_reporting(E_ALL);
```

Verificare log:
```
XAMPP: C:\xampp\apache\logs\error.log
PHP: /var/log/php-fpm.log (Linux)
MySQL: /var/log/mysql/error.log
```

### Supporto Browser

- Chrome/Edge 90+
- Firefox 88+
- Safari 14+
- Mobile browsers (iOS Safari 14+, Chrome Android)

### Licenza

Progetto scolastico PAI - Tutti i diritti riservati

---

**Per supporto o bug report, consultare la documentazione in progettoSito.txt**
