<?php
/**
 * Login Page
 * WCAG 2.1 Level AA - Form accessibile con validazione client-side
 * Cookie remember con token opaco
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

// Variabili per template
$page_title = 'Login - Gattile';
$remembered_username = getRememberedUsername();
$login_error = '';
$login_success = false;

// Gestisci POST (form submission da server, non da JS)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $remember_me = isset($_POST['remember_me']);

    if (!empty($username) && !empty($password)) {
        $result = loginUser($username, $password, $remember_me);
        
        if ($result['success']) {
            // Redirigidi a home o pagina precedente
            $redirect = $_GET['redirect'] ?? 'index.php';
            header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
            exit;
        } else {
            $login_error = $result['message'];
        }
    } else {
        $login_error = 'Username e password sono obbligatori';
    }
}
?>
<?php $page_title = 'Login - Gattile'; ?>
<?php require_once __DIR__ . '/includes/header.php'; ?>

<div class="container">
    <article class="auth-form-wrapper">
        <header>
            <h2>Accedi al tuo account</h2>
            <p>Inserisci le tue credenziali per accedere al sito</p>
        </header>

        <!-- Messaggio errore server -->
        <?php if (!empty($login_error)): ?>
            <section class="alert alert-error" role="alert" aria-live="assertive">
                <span class="alert-icon">⚠️</span>
                <div class="alert-content">
                    <h3>Errore di login</h3>
                    <p><?php echo htmlspecialchars($login_error); ?></p>
                </div>
            </section>
        <?php endif; ?>

        <form id="login-form" method="POST" action="" novalidate>
            <div class="form-group">
                <label for="username">Username</label>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    required 
                    aria-required="true"
                    value="<?php echo htmlspecialchars($remembered_username ?? ''); ?>"
                    autocomplete="username"
                    aria-describedby="username-help"
                >
                <small id="username-help">Deve iniziare con una lettera (4-50 caratteri)</small>
                <span class="error-message" role="alert" aria-live="polite"></span>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    required 
                    aria-required="true"
                    autocomplete="current-password"
                    aria-describedby="password-help"
                >
                <small id="password-help">8-16 caratteri con maiuscola, minuscola, numero e simbolo</small>
                <span class="error-message" role="alert" aria-live="polite"></span>
            </div>

            <div class="form-group">
                <label for="remember-me">
                    <input 
                        type="checkbox" 
                        id="remember-me" 
                        name="remember_me"
                    >
                    <span>Ricordami per 72 ore</span>
                </label>
                <small>
                    Utilizziamo un cookie sicuro (token opaco) per ricordare il tuo accesso.
                    La password non viene salvata nel cookie.
                </small>
            </div>

            <button type="submit" class="btn btn-primary" id="submit-btn">
                Accedi
            </button>
        </form>

        <section class="auth-links">
            <p>
                Non hai un account?
                <a href="registrazione.php">Registrati qui</a>
            </p>
            <p>
                <a href="index.php">Torna alla home</a>
            </p>
        </section>

        <aside role="complementary" aria-label="Informazioni login">
            <h3>Account demo disponibili:</h3>
            <dl>
                <dt>Admin:</dt>
                <dd>
                    Username: <code>anna_admin</code><br>
                    Password: <code>Admin2026!</code>
                </dd>
                <dt>Utente:</dt>
                <dd>
                    Username: <code>mario_volontario</code><br>
                    Password: <code>Password123!</code>
                </dd>
            </dl>
        </aside>
    </article>
</div>

<style>
    .auth-form-wrapper {
        max-width: 500px;
        margin: var(--spacing-xl) auto;
    }

    .auth-form-wrapper form {
        background-color: var(--color-bg-alt);
        padding: var(--spacing-lg);
        border-radius: var(--border-radius-lg);
        margin-bottom: var(--spacing-lg);
    }

    .auth-links {
        text-align: center;
        padding: var(--spacing-lg);
        border: 1px solid var(--color-border);
        border-radius: var(--border-radius-md);
        margin-bottom: var(--spacing-lg);
    }

    .auth-links p {
        margin: var(--spacing-sm) 0;
    }

    .auth-links a {
        font-weight: 600;
    }

    aside {
        margin-top: var(--spacing-xl);
    }

    code {
        background-color: white;
        padding: 4px 8px;
    }
</style>

<script src="/PAI/prova/assets/js/form-validator.js"></script>
<script>
'use strict';

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('login-form');
    
    if (form) {
        // Abilita validazione real-time
        FormValidator.enableRealtimeValidation(form);

        // Gestisci submit via JavaScript (con fallback server-side)
        FormValidator.attachFormSubmit(form, function(formElement) {
            // Dati validi - il form naturalmente submitta
            // Potremmo fare AJAX qui se volessimo
            formElement.submit();
        });
    }

    // Focus management per accessibilità
    const usernameField = document.getElementById('username');
    if (usernameField && !usernameField.value) {
        usernameField.focus();
    }
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
