<?php
/**
 * Header Template
 * Componente semantico HTML5 per testata pagina
 * WCAG 2.1 Level AA - Struttura logica con nav, logo, user info
 */

'use strict';

require_once __DIR__ . '/auth.php';

// Verifica timeout sessione
if (isUserAuthenticated()) {
    checkSessionTimeout();
}
?>
<!DOCTYPE html>
<html lang="it" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Gattile - Adozioni e volontariato per gatti in difficoltà">
    <meta name="keywords" content="gatti, adozione, volontariato, rifugio, felini">
    <meta name="author" content="Gattile PAI">
    <meta name="theme-color" content="#2c3e50">
    <meta name="color-scheme" content="light">
    
    <!-- Sicurezza e CSP -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Security-Policy" content="default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self' data:">
    
    <title><?php echo htmlspecialchars($page_title ?? 'Gattile - Adozioni e Volontariato'); ?></title>
    <link rel="stylesheet" href="/PAI/prova/assets/css/style.css">
    <link rel="stylesheet" href="/PAI/prova/assets/css/accessibility.css">
    
    <!-- Skip to content link per accessibilità -->
    <style>
        .skip-to-content {
            position: absolute;
            top: -40px;
            left: 0;
            background: #2c3e50;
            color: white;
            padding: 8px;
            text-decoration: none;
            z-index: 100;
        }
        .skip-to-content:focus {
            top: 0;
        }
    </style>
</head>
<body>
    <a href="#main-content" class="skip-to-content">Salta al contenuto principale</a>

    <header role="banner" class="site-header">
        <div class="container">
            <div class="header-inner">
                <!-- Logo/Titolo -->
                <div class="site-branding">
                    <h1>
                        <a href="/PAI/prova/index.php" class="logo">
                            <span class="logo-icon">🐱</span>
                            <span class="logo-text">Gattile</span>
                        </a>
                    </h1>
                </div>

                <!-- Navigazione principale -->
                <nav role="navigation" aria-label="Navigazione principale" class="main-nav">
                    <button class="nav-toggle" aria-expanded="false" aria-controls="nav-menu">
                        <span></span>
                        <span></span>
                        <span></span>
                    </button>
                    
                    <ul id="nav-menu" class="nav-menu">
                        <li>
                            <a href="/PAI/prova/index.php" class="nav-link">
                                <span>Home</span>
                            </a>
                        </li>
                        <li>
                            <a href="/PAI/prova/gatti.php" class="nav-link">
                                <span>Gatti</span>
                            </a>
                        </li>
                        <?php if (isUserAuthenticated()): ?>
                            <li>
                                <a href="/PAI/prova/prenotazione-visite.php" class="nav-link">
                                    <span>Prenotazione Visita</span>
                                </a>
                            </li>
                            <li>
                                <a href="/PAI/prova/volontariato.php" class="nav-link">
                                    <span>Volontariato</span>
                                </a>
                            </li>
                            <?php if (isUserAdmin()): ?>
                                <li>
                                    <a href="/PAI/prova/inserimento-gatto.php" class="nav-link" aria-label="Area amministrativa - Inserimento gatti">
                                        <span>Gestione Gatti</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                        <?php else: ?>
                            <li>
                                <a href="/PAI/prova/registrazione.php" class="nav-link">
                                    <span>Registrati</span>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>

                <!-- Informazioni utente -->
                <div class="user-info" role="region" aria-label="Informazioni utente">
                    <?php if (isUserAuthenticated()): ?>
                        <span class="username" aria-label="Utente autenticato">
                            <span class="user-icon">👤</span>
                            <span class="user-label"><?php echo htmlspecialchars(getCurrentUsername()); ?></span>
                        </span>
                        <a href="/PAI/prova/logout.php" class="logout-btn" role="button">
                            <span>Logout</span>
                        </a>
                    <?php else: ?>
                        <a href="/PAI/prova/login.php" class="login-btn" role="button">
                            <span>Login</span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <!-- Main content wrapper -->
    <main id="main-content" role="main" class="main-content">
