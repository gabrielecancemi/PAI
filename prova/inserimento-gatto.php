<?php
/**
 * Inserimento Gatto Page
 * Solo per amministratori
 * WCAG 2.1 AA - Form accessibile validato lato client
 */

'use strict';

session_start();

require_once __DIR__ . '/includes/auth.php';

// Richiedi autenticazione e privilegi admin
requireAuthentication(true);

$page_title = 'Inserimento Gatto - Gattile';
$insert_success = false;
$insert_error = '';
?>
<?php require_once __DIR__ . '/includes/header.php'; ?>

<div class="container">
    <article class="admin-form-wrapper">
        <header>
            <h2>Inserisci un Nuovo Gatto</h2>
            <p>Compila il modulo per aggiungere un nuovo gatto al rifugio</p>
            <p role="status" aria-live="polite" aria-label="Informazione utente">
                Utente autenticato: <strong><?php echo htmlspecialchars(getCurrentUsername()); ?></strong> (Admin)
            </p>
        </header>

        <!-- Messaggio stato -->
        <div id="form-message" class="alert" role="alert" aria-live="assertive" style="display: none;"></div>

        <form id="cat-form" method="POST" action="/PAI/prova/api/cat-insert-api.php" novalidate>
            <fieldset>
                <legend>Dati Generali</legend>

                <div class="form-group">
                    <label for="nome">Nome del gatto *</label>
                    <input 
                        type="text" 
                        id="nome" 
                        name="nome" 
                        required 
                        aria-required="true"
                        placeholder="Es: Micia, Garfield"
                        maxlength="50"
                    >
                    <small>2-50 caratteri</small>
                    <span class="error-message" role="alert" aria-live="polite"></span>
                </div>

                <div class="form-group">
                    <label for="descrizione">Descrizione carattere e storia *</label>
                    <textarea 
                        id="descrizione" 
                        name="descrizione" 
                        required 
                        aria-required="true"
                        rows="5"
                        maxlength="1000"
                        placeholder="Descrivi il carattere del gatto, la sua storia e le sue particolarità..."
                    ></textarea>
                    <small>Massimo 1000 caratteri</small>
                    <span class="error-message" role="alert" aria-live="polite"></span>
                </div>

                <div class="form-group">
                    <label for="sesso">Sesso *</label>
                    <select id="sesso" name="sesso" required aria-required="true">
                        <option value="">-- Seleziona --</option>
                        <option value="M">Maschio</option>
                        <option value="F">Femmina</option>
                    </select>
                    <span class="error-message" role="alert" aria-live="polite"></span>
                </div>
            </fieldset>

            <fieldset>
                <legend>Caratteristiche Fisiche</legend>

                <div class="form-group">
                    <label for="peso">Peso (kg) *</label>
                    <input 
                        type="number" 
                        id="peso" 
                        name="peso" 
                        required 
                        aria-required="true"
                        min="0.5" 
                        max="10" 
                        step="0.1"
                        placeholder="Es: 3.5"
                    >
                    <small>Tra 0.5 e 10 kg</small>
                    <span class="error-message" role="alert" aria-live="polite"></span>
                </div>

                <div class="form-group">
                    <label for="colore-mantello">Colore del mantello *</label>
                    <input 
                        type="text" 
                        id="colore-mantello" 
                        name="colore_mantello" 
                        required 
                        aria-required="true"
                        placeholder="Es: Tigrato, Bianco, Nero"
                        maxlength="30"
                    >
                    <small>Es: Tigrato, Bianco, Nero, Calico (30 caratteri max)</small>
                    <span class="error-message" role="alert" aria-live="polite"></span>
                </div>

                <div class="form-group">
                    <label for="lunghezza-pelo">Lunghezza del pelo *</label>
                    <select id="lunghezza-pelo" name="lunghezza_pelo" required aria-required="true">
                        <option value="">-- Seleziona --</option>
                        <option value="Corto">Corto</option>
                        <option value="Medio">Medio</option>
                        <option value="Lungo">Lungo</option>
                    </select>
                    <span class="error-message" role="alert" aria-live="polite"></span>
                </div>

                <div class="form-group">
                    <label for="colore-occhi">Colore degli occhi *</label>
                    <input 
                        type="text" 
                        id="colore-occhi" 
                        name="colore_occhi" 
                        required 
                        aria-required="true"
                        placeholder="Es: Verdi, Azzurri, Gialli"
                        maxlength="30"
                    >
                    <small>Es: Verdi, Azzurri, Gialli, Ambra (30 caratteri max)</small>
                    <span class="error-message" role="alert" aria-live="polite"></span>
                </div>

                <div class="form-group">
                    <label for="razza">Razza *</label>
                    <input 
                        type="text" 
                        id="razza" 
                        name="razza" 
                        required 
                        aria-required="true"
                        placeholder="Es: Europeo, Persiano, Siamese"
                        maxlength="50"
                    >
                    <small>Es: Europeo, Persiano, Incrocio (50 caratteri max)</small>
                    <span class="error-message" role="alert" aria-live="polite"></span>
                </div>
            </fieldset>

            <fieldset>
                <legend>Informazioni Età e Arrivo</legend>

                <div class="form-group">
                    <label for="eta">Età (in mesi) *</label>
                    <input 
                        type="number" 
                        id="eta" 
                        name="eta" 
                        required 
                        aria-required="true"
                        min="0" 
                        max="200"
                        placeholder="Es: 6"
                    >
                    <small>Età in mesi (0-200)</small>
                    <span class="error-message" role="alert" aria-live="polite"></span>
                </div>

                <div class="form-group">
                    <label for="data-arrivo">Data di arrivo al rifugio *</label>
                    <input 
                        type="date" 
                        id="data-arrivo" 
                        name="data_arrivo" 
                        required 
                        aria-required="true"
                        max="<?php echo date('Y-m-d'); ?>"
                    >
                    <small>Non può essere una data futura</small>
                    <span class="error-message" role="alert" aria-live="polite"></span>
                </div>
            </fieldset>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary" id="submit-btn">
                    Inserisci Gatto
                </button>
                <a href="gatti.php" class="btn btn-secondary">
                    Cancella
                </a>
            </div>
        </form>

        <aside role="complementary" aria-label="Informazioni inserimento">
            <h3>ℹ️ Informazioni</h3>
            <p>
                Quando inserisci un nuovo gatto, il sistema assegna automaticamente un'immagine 
                placeholder. In future release sarà possibile caricare foto reali.
            </p>
            <p>
                Tutti i campi contrassegnati con * sono obbligatori.
            </p>
        </aside>
    </article>
</div>

<style>
    .admin-form-wrapper {
        max-width: 700px;
        margin: var(--spacing-xl) auto;
    }

    .admin-form-wrapper > header {
        margin-bottom: var(--spacing-xl);
    }

    .admin-form-wrapper form {
        background-color: var(--color-bg-alt);
        padding: var(--spacing-lg);
        border-radius: var(--border-radius-lg);
        margin-bottom: var(--spacing-xl);
    }

    fieldset {
        margin-bottom: var(--spacing-xl);
    }

    legend {
        font-weight: 600;
        margin-bottom: var(--spacing-lg);
        color: var(--color-primary);
        font-size: 18px;
    }

    .form-group {
        margin-bottom: var(--spacing-lg);
    }

    .form-group label {
        display: block;
        margin-bottom: var(--spacing-sm);
        font-weight: 600;
    }

    .form-group input,
    .form-group textarea,
    .form-group select {
        width: 100%;
        padding: var(--spacing-md);
        border: 2px solid var(--color-border);
        border-radius: var(--border-radius-md);
        font-family: var(--font-family-base);
        font-size: var(--font-size-base);
    }

    .form-group small {
        display: block;
        margin-top: var(--spacing-xs);
        color: var(--color-text-light);
    }

    .form-actions {
        display: flex;
        gap: var(--spacing-md);
        margin-top: var(--spacing-xl);
    }

    .form-actions button,
    .form-actions a {
        flex: 1;
    }

    aside {
        background-color: #d1ecf1;
        border: 2px solid #bee5eb;
        color: #0c5460;
        padding: var(--spacing-lg);
        border-radius: var(--border-radius-md);
        margin-top: var(--spacing-xl);
    }

    aside h3 {
        margin-top: 0;
    }

    @media (max-width: 768px) {
        .form-actions {
            flex-direction: column;
        }
    }
</style>

<script src="/PAI/prova/assets/js/form-validator.js"></script>
<script>
'use strict';

// Aggiungi regole di validazione specifiche per questo form
FormValidator.addRule('nome', {
    pattern: /^[a-zA-ZàèéìòùÀÈÉÌÒÙ\s]{2,50}$/,
    message: 'Nome deve contenere solo lettere (2-50 caratteri)',
    required: true
});

FormValidator.addRule('descrizione', {
    validator: (value) => value.length >= 10 && value.length <= 1000,
    message: 'Descrizione deve essere 10-1000 caratteri',
    required: true
});

FormValidator.addRule('peso', {
    validator: (value) => {
        const peso = parseFloat(value);
        return peso >= 0.5 && peso <= 10;
    },
    message: 'Peso deve essere tra 0.5 e 10 kg',
    required: true
});

FormValidator.addRule('eta', {
    validator: (value) => {
        const eta = parseInt(value);
        return eta >= 0 && eta <= 200;
    },
    message: 'Età deve essere tra 0 e 200 mesi',
    required: true
});

FormValidator.addRule('data_arrivo', {
    validator: (value) => {
        const selectedDate = new Date(value);
        const today = new Date();
        return selectedDate <= today;
    },
    message: 'Data non può essere nel futuro',
    required: true
});

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('cat-form');
    const messageEl = document.getElementById('form-message');

    if (form) {
        // Abilita validazione real-time
        FormValidator.enableRealtimeValidation(form);

        // Gestisci submit
        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            // Valida
            if (!FormValidator.validateForm(form)) {
                FormValidator.showFormMessage(form, 'Per favore, correggi gli errori nel form', 'error');
                return;
            }

            // Disabilita submit durante invio
            FormValidator.setFormSubmitDisabled(form, true);

            try {
                const formData = FormValidator.getFormData(form);
                
                const response = await fetch('/PAI/prova/api/cat-insert-api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(formData)
                });

                const data = await response.json();

                if (data.success) {
                    FormValidator.showFormMessage(form, '✓ Gatto inserito con successo!', 'success');
                    setTimeout(() => {
                        window.location.href = 'gatti.php';
                    }, 2000);
                } else {
                    FormValidator.showFormMessage(form, data.error || 'Errore nell\'inserimento', 'error');
                }
            } catch (err) {
                console.error('Insert error:', err);
                FormValidator.showFormMessage(form, 'Errore di connessione', 'error');
            } finally {
                FormValidator.setFormSubmitDisabled(form, false);
            }
        });
    }
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
