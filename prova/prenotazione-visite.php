<?php
/**
 * Prenotazione Visite Page
 * Gestisci le tue prenotazioni di visite
 */

'use strict';

session_start();

require_once __DIR__ . '/includes/auth.php';

// Richiedi autenticazione
requireAuthentication();

$page_title = 'Le mie Prenotazioni - Gattile';
?>
<?php require_once __DIR__ . '/includes/header.php'; ?>

<div class="container">
    <section aria-label="Gestione prenotazioni visite">
        <h2>Le mie Prenotazioni di Visita</h2>
        <p class="section-subtitle">
            Gestisci le tue prenotazioni per visitare i gatti che ti interessano
        </p>

        <article class="bookings-wrapper">
            <div id="bookings-list" class="bookings-list">
                <p class="loading">Caricamento prenotazioni in corso...</p>
            </div>
        </article>

        <div class="back-link">
            <a href="gatti.php" class="btn btn-secondary">
                ← Torna a visualizzare gatti
            </a>
        </div>
    </section>
</div>

<style>
    .section-subtitle {
        font-size: var(--font-size-large);
        color: var(--color-text-light);
        text-align: center;
        margin-bottom: var(--spacing-xl);
    }

    .bookings-wrapper {
        max-width: 800px;
        margin: 0 auto var(--spacing-xl);
    }

    .bookings-list {
        min-height: 200px;
    }

    .booking-card {
        background-color: white;
        border: 2px solid var(--color-border);
        border-radius: var(--border-radius-lg);
        padding: var(--spacing-lg);
        margin-bottom: var(--spacing-lg);
        box-shadow: var(--shadow-md);
    }

    .booking-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: var(--spacing-lg);
        border-bottom: 2px solid var(--color-border);
        padding-bottom: var(--spacing-md);
    }

    .booking-datetime {
        font-size: 18px;
        font-weight: 600;
        color: var(--color-primary);
    }

    .booking-status {
        background-color: var(--color-success);
        color: white;
        padding: var(--spacing-sm) var(--spacing-md);
        border-radius: var(--border-radius-md);
        font-size: var(--font-size-small);
        font-weight: 600;
    }

    .booking-cats {
        margin-bottom: var(--spacing-lg);
    }

    .booking-cats h4 {
        margin: 0 0 var(--spacing-md) 0;
    }

    .cat-list {
        list-style: none;
        padding: 0;
    }

    .cat-list li {
        background-color: var(--color-bg-alt);
        padding: var(--spacing-md);
        border-radius: var(--border-radius-md);
        margin-bottom: var(--spacing-sm);
        border-left: 4px solid var(--color-secondary);
    }

    .booking-actions {
        display: flex;
        gap: var(--spacing-md);
    }

    .booking-actions button {
        padding: var(--spacing-sm) var(--spacing-lg);
        border: none;
        border-radius: var(--border-radius-md);
        cursor: pointer;
        font-weight: 600;
    }

    .btn-cancel {
        background-color: var(--color-error);
        color: white;
    }

    .btn-cancel:hover {
        background-color: #a93226;
    }

    .loading,
    .no-bookings {
        text-align: center;
        color: var(--color-text-light);
        padding: var(--spacing-xl);
        font-style: italic;
    }

    .back-link {
        text-align: center;
    }
</style>

<script src="/PAI/prova/assets/js/form-validator.js"></script>
<script>
'use strict';

document.addEventListener('DOMContentLoaded', function() {
    loadBookings();

    function loadBookings() {
        fetch('/PAI/prova/api/booking-api.php?action=my')
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    renderBookings(data.data);
                }
            })
            .catch(err => {
                console.error('Load bookings error:', err);
                document.getElementById('bookings-list').innerHTML = 
                    '<p class="error-message">Errore nel caricamento delle prenotazioni</p>';
            });
    }

    function renderBookings(bookings) {
        const container = document.getElementById('bookings-list');

        if (!bookings || bookings.length === 0) {
            container.innerHTML = '<p class="no-bookings">Nessuna prenotazione. <a href="gatti.php">Crea una nuova prenotazione</a></p>';
            return;
        }

        container.innerHTML = bookings.map(booking => `
            <div class="booking-card">
                <div class="booking-header">
                    <div class="booking-datetime">
                        📅 ${booking.data_ora}
                    </div>
                    <div class="booking-status">✓ Confermata</div>
                </div>

                <div class="booking-cats">
                    <h4>Gatti selezionati:</h4>
                    <ul class="cat-list">
                        ${booking.gatti_nomi ? booking.gatti_nomi.split(', ').map(cat => 
                            `<li>🐱 ${cat}</li>`
                        ).join('') : '<li>Nessun gatto associato</li>'}
                    </ul>
                </div>

                <div class="booking-actions">
                    <button class="btn-cancel" onclick="cancelBooking(${booking.id})">
                        Cancella Prenotazione
                    </button>
                </div>
            </div>
        `).join('');
    }

    window.cancelBooking = function(bookingId) {
        if (confirm('Sei sicuro di voler cancellare questa prenotazione?')) {
            fetch('/PAI/prova/api/booking-api.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({action: 'cancel', booking_id: bookingId})
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('Prenotazione cancellata');
                    loadBookings();
                }
            });
        }
    };
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
