<?php
/**
 * Registration Page
 * WCAG 2.1 Level AA - Form accessibile con validazione rigorosa
 * Validazione lato client in Vanilla JS e lato server
 */

'use strict';

session_start();

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';

// Se già loggato, redirigidi a home
if (isUserAuthenticated()) {
    header('Location: index.php');
    exit;
}

$page_title = 'Registrazione - Gattile';
$registration_error = '';
$registration_success = false;

// Gestisci POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $cognome = $_POST['cognome'] ?? '';
    $indirizzo = $_POST['indirizzo'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    // Validazione server-side
    $validation_errors = [];

    // Nome
    if (empty($nome) || !preg_match('/^[a-zA-ZàèéìòùÀÈÉÌÒÙ\s]{2,50}$/', $nome)) {
        $validation_errors[] = 'Nome non valido';
    }

    // Cognome
    if (empty($cognome) || !preg_match('/^[a-zA-ZàèéìòùÀÈÉÌÒÙ\s]{2,50}$/', $cognome)) {
        $validation_errors[] = 'Cognome non valido';
    }

    // Indirizzo
    if (empty($indirizzo) || strlen($indirizzo) < 5 || strlen($indirizzo) > 100) {
        $validation_errors[] = 'Indirizzo non valido (5-100 caratteri)';
    }

    // Username
    if (empty($username) || !preg_match('/^[a-zA-Z][a-zA-Z0-9_-]{3,49}$/', $username)) {
        $validation_errors[] = 'Username non valido';
    }

    // Password
    if (empty($password) || !preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,16}$/', $password)) {
        $validation_errors[] = 'Password non valida';
    }

    // Conferma password
    if ($password !== $password_confirm) {
        $validation_errors[] = 'Le password non corrispondono';
    }

    if (empty($validation_errors)) {
        // Prova a registrare l'utente
        try {
            $db = getDbConnection('registrator');
            if (!$db) {
                $validation_errors[] = 'Errore di connessione al database';
            } else {
                // Hash della password
                $password_hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

                // Controlla se username già esiste
                $query = "SELECT id FROM utenti WHERE username = ?";
                $existing = executeQuery($db, $query, [$username], 's');

                if ($existing && !empty($existing)) {
                    $validation_errors[] = 'Username già in uso';
                } else {
                    // Inserisci nuovo utente
                    $query = "INSERT INTO utenti (nome, cognome, indirizzo, username, password, is_admin) 
                              VALUES (?, ?, ?, ?, ?, 0)";
                    
                    $result = executeModifyQuery($db, $query, 
                        [$nome, $cognome, $indirizzo, $username, $password_hash], 
                        'sssss');

                    if ($result > 0) {
                        $registration_success = true;
                        // Redirigidi a login
                        closeDbConnection($db);
                        header('Location: login.php?registered=success');
                        exit;
                    } else {
                        $validation_errors[] = 'Errore durante la registrazione. Riprova più tardi.';
                    }
                }
                closeDbConnection($db);
            }
        } catch (Exception $e) {
            error_log('Registration Error: ' . $e->getMessage());
            $validation_errors[] = 'Errore durante la registrazione';
        }
    }

    if (!empty($validation_errors)) {
        $registration_error = implode('; ', $validation_errors);
    }
}
?>
<?php require_once __DIR__ . '/includes/header.php'; ?>

<div class="container">
    <article class="auth-form-wrapper">
        <header>
            <h2>Crea un nuovo account</h2>
            <p>Compila il modulo per registrarti al sito</p>
        </header>

        <!-- Messaggio errore -->
        <?php if (!empty($registration_error)): ?>
            <section class="alert alert-error" role="alert" aria-live="assertive">
                <span class="alert-icon">⚠️</span>
                <div class="alert-content">
                    <h3>Errore nella registrazione</h3>
                    <p><?php echo htmlspecialchars($registration_error); ?></p>
                </div>
            </section>
        <?php endif; ?>

        <form id="registration-form" method="POST" action="" novalidate>
            <fieldset>
                <legend>Dati personali</legend>

                <div class="form-group">
                    <label for="nome">Nome *</label>
                    <input 
                        type="text" 
                        id="nome" 
                        name="nome" 
                        required 
                        aria-required="true"
                        autocomplete="given-name"
                        placeholder="Es: Mario"
                    >
                    <small>Solo lettere (2-50 caratteri)</small>
                    <span class="error-message" role="alert"></span>
                </div>

                <div class="form-group">
                    <label for="cognome">Cognome *</label>
                    <input 
                        type="text" 
                        id="cognome" 
                        name="cognome" 
                        required 
                        aria-required="true"
                        autocomplete="family-name"
                        placeholder="Es: Rossi"
                    >
                    <small>Solo lettere (2-50 caratteri)</small>
                    <span class="error-message" role="alert"></span>
                </div>

                <div class="form-group">
                    <label for="indirizzo">Indirizzo *</label>
                    <input 
                        type="text" 
                        id="indirizzo" 
                        name="indirizzo" 
                        required 
                        aria-required="true"
                        autocomplete="street-address"
                        placeholder="Es: Via Roma 10, Torino"
                    >
                    <small>Indirizzo completo (5-100 caratteri)</small>
                    <span class="error-message" role="alert"></span>
                </div>
            </fieldset>

            <fieldset>
                <legend>Credenziali di accesso</legend>

                <div class="form-group">
                    <label for="username">Username *</label>
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                        required 
                        aria-required="true"
                        autocomplete="username"
                        placeholder="Es: mario_rossi"
                    >
                    <small>Deve iniziare con lettera, lettere/numeri/trattini (4-50 caratteri)</small>
                    <span class="error-message" role="alert"></span>
                </div>

                <div class="form-group">
                    <label for="password">Password *</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        required 
                        aria-required="true"
                        autocomplete="new-password"
                    >
                    <small>
                        8-16 caratteri. Deve contenere:
                        <ul style="margin: 4px 0 0 20px;">
                            <li>Almeno una lettera maiuscola</li>
                            <li>Almeno una lettera minuscola</li>
                            <li>Almeno un numero</li>
                            <li>Almeno un carattere speciale (@$!%*?&)</li>
                        </ul>
                    </small>
                    <span class="error-message" role="alert"></span>
                </div>

                <div class="form-group">
                    <label for="password-confirm">Conferma Password *</label>
                    <input 
                        type="password" 
                        id="password-confirm" 
                        name="password_confirm" 
                        required 
                        aria-required="true"
                        autocomplete="new-password"
                    >
                    <small>Deve corrispondere alla password inserita sopra</small>
                    <span class="error-message" role="alert"></span>
                </div>
            </fieldset>

            <button type="submit" class="btn btn-primary" id="submit-btn">
                Registrati
            </button>
        </form>

        <section class="auth-links">
            <p>
                Hai già un account?
                <a href="login.php">Accedi qui</a>
            </p>
            <p>
                <a href="index.php">Torna alla home</a>
            </p>
        </section>
    </article>
</div>

<style>
    .auth-form-wrapper {
        max-width: 600px;
        margin: var(--spacing-xl) auto;
    }

    .auth-form-wrapper form {
        background-color: var(--color-bg-alt);
        padding: var(--spacing-lg);
        border-radius: var(--border-radius-lg);
        margin-bottom: var(--spacing-lg);
    }

    fieldset {
        margin-bottom: var(--spacing-lg);
    }

    legend {
        font-weight: 600;
        margin-bottom: var(--spacing-md);
        color: var(--color-primary);
    }

    .auth-links {
        text-align: center;
        padding: var(--spacing-lg);
        border: 1px solid var(--color-border);
        border-radius: var(--border-radius-md);
    }

    .auth-links p {
        margin: var(--spacing-sm) 0;
    }

    small ul {
        font-size: 12px;
    }

    small li {
        margin: 2px 0;
    }
</style>

<script src="/PAI/prova/assets/js/form-validator.js"></script>
<script>
'use strict';

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('registration-form');
    
    if (form) {
        // Abilita validazione real-time
        FormValidator.enableRealtimeValidation(form);

        // Gestisci submit
        FormValidator.attachFormSubmit(form, function(formElement) {
            formElement.submit();
        });
    }

    // Focus nel primo campo
    const nomeField = document.getElementById('nome');
    if (nomeField) {
        nomeField.focus();
    }
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
