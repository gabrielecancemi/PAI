<?php
/**
 * Volontariato Page
 * Prenotazione turni volontariato con validazione slot disponibili
 * Max 2 volontari per fascia oraria
 * WCAG 2.1 AA - Vanilla JS
 */

'use strict';

session_start();

require_once __DIR__ . '/includes/auth.php';

// Richiedi autenticazione
requireAuthentication();

$page_title = 'Volontariato - Gattile';
?>
<?php require_once __DIR__ . '/includes/header.php'; ?>

<div class="container">
    <section aria-label="Prenotazione turni volontariato">
        <h2>Offri il tuo Aiuto - Volontariato</h2>
        <p class="section-subtitle">
            Puoi prenotare un numero illimitato di turni per aiutarci nella gestione del rifugio.
            Massimo 2 volontari per fascia oraria.
        </p>

        <article class="volunteer-info" role="complementary">
            <h3>ℹ️ Informazioni Importanti</h3>
            <ul>
                <li>Le fasce orarie hanno una capacità massima di <strong>2 volontari</strong></li>
                <li>Puoi prenotare <strong>un numero illimitato di turni</strong></li>
                <li>I turni disabilitati sono già al completo</li>
                <li>Riceverai una conferma della tua prenotazione via email</li>
            </ul>
        </article>

        <div class="volunteer-page-wrapper">
            <!-- Calendario/Form prenotazione -->
            <div class="volunteer-booking">
                <form id="volunteer-form" class="volunteer-form" novalidate>
                    <fieldset>
                        <legend>Seleziona le tue fasce orarie</legend>

                        <div class="form-group">
                            <label for="volunteer-date">Data *</label>
                            <input 
                                type="date" 
                                id="volunteer-date" 
                                name="volunteer_date" 
                                required
                                aria-required="true"
                                min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>"
                            >
                            <small>Seleziona una data a partire da domani</small>
                            <span class="error-message" role="alert" aria-live="polite"></span>
                        </div>

                        <div id="timeslots-container" class="timeslots-container" role="group" aria-label="Fasce orarie disponibili">
                            <p class="loading-timeslots">Seleziona una data per visualizzare le fasce orarie</p>
                        </div>

                        <div class="form-group">
                            <label for="notes">Note facoltative</label>
                            <textarea 
                                id="notes" 
                                name="notes"
                                rows="3"
                                maxlength="500"
                                placeholder="Es: Preferibilmente dopo le 18:00, disponibile per pulizie..."
                            ></textarea>
                            <small>Massimo 500 caratteri</small>
                        </div>

                        <button type="submit" class="btn btn-primary" id="volunteer-submit">
                            Prenota Turni
                        </button>
                    </fieldset>
                </form>
            </div>

            <!-- Sidebar con i miei turni -->
            <aside class="my-shifts-sidebar" role="complementary" aria-label="I miei turni di volontariato">
                <h3>I miei Turni</h3>
                <div id="my-shifts-list" class="shifts-list">
                    <p class="loading">Caricamento...</p>
                </div>
            </aside>
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

    .volunteer-info {
        background-color: #cce5ff;
        border: 2px solid #b3d9ff;
        color: #004085;
        padding: var(--spacing-lg);
        border-radius: var(--border-radius-md);
        margin-bottom: var(--spacing-xl);
    }

    .volunteer-info h3 {
        margin-top: 0;
    }

    .volunteer-info ul {
        margin: var(--spacing-md) 0;
        padding-left: 20px;
    }

    .volunteer-info li {
        margin: var(--spacing-sm) 0;
    }

    .volunteer-page-wrapper {
        display: grid;
        grid-template-columns: 1fr 300px;
        gap: var(--spacing-lg);
        align-items: start;
    }

    .volunteer-booking {
        background-color: var(--color-bg-alt);
        padding: var(--spacing-lg);
        border-radius: var(--border-radius-lg);
    }

    .volunteer-form fieldset {
        border: none;
        padding: 0;
        margin: 0;
    }

    .volunteer-form legend {
        margin-bottom: var(--spacing-lg);
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
    .form-group textarea {
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

    .timeslots-container {
        background-color: white;
        padding: var(--spacing-lg);
        border-radius: var(--border-radius-md);
        margin-bottom: var(--spacing-lg);
        border: 1px solid var(--color-border);
    }

    .timeslots-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: var(--spacing-md);
    }

    .timeslot-item {
        display: flex;
        align-items: center;
        gap: var(--spacing-md);
        padding: var(--spacing-md);
        background-color: var(--color-bg-alt);
        border-radius: var(--border-radius-md);
        border: 2px solid transparent;
    }

    .timeslot-item.available:hover {
        border-color: var(--color-secondary);
        background-color: white;
    }

    .timeslot-item.disabled {
        opacity: 0.5;
        cursor: not-allowed;
        background-color: #f5f5f5;
    }

    .timeslot-item input[type="checkbox"] {
        width: 20px;
        height: 20px;
        cursor: pointer;
    }

    .timeslot-item.disabled input[type="checkbox"] {
        cursor: not-allowed;
    }

    .timeslot-label {
        display: flex;
        flex-direction: column;
        gap: var(--spacing-xs);
    }

    .timeslot-label strong {
        display: block;
    }

    .timeslot-label small {
        color: var(--color-text-light);
        font-size: 12px;
    }

    .loading-timeslots,
    .loading {
        text-align: center;
        color: var(--color-text-light);
        font-style: italic;
        padding: var(--spacing-lg);
    }

    .my-shifts-sidebar {
        background-color: white;
        border: 2px solid var(--color-border);
        border-radius: var(--border-radius-lg);
        padding: var(--spacing-lg);
        height: fit-content;
        position: sticky;
        top: 100px;
    }

    .my-shifts-sidebar h3 {
        margin-top: 0;
        color: var(--color-primary);
        border-bottom: 2px solid var(--color-secondary);
        padding-bottom: var(--spacing-md);
        margin-bottom: var(--spacing-lg);
    }

    .shifts-list {
        max-height: 400px;
        overflow-y: auto;
    }

    .shift-item {
        background-color: var(--color-bg-alt);
        padding: var(--spacing-md);
        border-radius: var(--border-radius-md);
        margin-bottom: var(--spacing-md);
        border-left: 4px solid var(--color-success);
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: var(--spacing-sm);
    }

    .shift-item-date {
        font-weight: 600;
        font-size: 14px;
    }

    .shift-item-time {
        color: var(--color-text-light);
        font-size: 13px;
    }

    .shift-item-remove {
        background-color: var(--color-error);
        color: white;
        border: none;
        padding: 4px 8px;
        border-radius: var(--border-radius-sm);
        cursor: pointer;
        font-size: 12px;
    }

    .shift-item-remove:hover {
        background-color: #a93226;
    }

    .volunteer-form .btn {
        width: 100%;
    }

    @media (max-width: 1024px) {
        .volunteer-page-wrapper {
            grid-template-columns: 1fr;
        }

        .my-shifts-sidebar {
            position: static;
        }
    }

    @media (max-width: 768px) {
        .timeslots-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<script src="/PAI/prova/assets/js/form-validator.js"></script>
<script>
'use strict';

const VolunteerBooking = (() => {
    const timeslotContainer = document.getElementById('timeslots-container');
    const volunteerForm = document.getElementById('volunteer-form');
    const volunteerDateInput = document.getElementById('volunteer-date');
    const myShiftsList = document.getElementById('my-shifts-list');
    
    const timeslots = [
        '09:00', '10:00', '11:00', '12:00',
        '14:00', '15:00', '16:00', '17:00', '18:00', '19:00'
    ];
    
    const maxVolunteersPerSlot = 2;
    let selectedSlots = [];

    // Carica turni dell'utente
    function loadMyShifts() {
        fetch('/PAI/prova/api/volunteer-api.php?action=my')
            .then(res => res.json())
            .then(data => {
                if (data.success && Array.isArray(data.data)) {
                    renderMyShifts(data.data);
                }
            })
            .catch(err => {
                console.error('Load shifts error:', err);
                myShiftsList.innerHTML = '<p class="error-message">Errore nel caricamento</p>';
            });
    }

    function renderMyShifts(shifts) {
        if (shifts.length === 0) {
            myShiftsList.innerHTML = '<p style="color: var(--color-text-light); font-style: italic;">Nessun turno prenotato</p>';
            return;
        }

        myShiftsList.innerHTML = shifts.map(shift => `
            <div class="shift-item">
                <div>
                    <div class="shift-item-date">${shift.data}</div>
                    <div class="shift-item-time">${shift.ora}</div>
                </div>
                <button type="button" class="shift-item-remove" onclick="VolunteerBooking.removeShift(${shift.id})">
                    Rimuovi
                </button>
            </div>
        `).join('');
    }

    function removeShift(shiftId) {
        if (confirm('Sei sicuro di voler cancellare questo turno?')) {
            fetch('/PAI/prova/api/volunteer-api.php', {
                method: 'DELETE',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({shift_id: shiftId})
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    loadMyShifts();
                    loadTimeslots(); // Ricarica disponibilità
                }
            });
        }
    }

    function loadTimeslots() {
        const selectedDate = volunteerDateInput.value;
        if (!selectedDate) return;

        // Chiama API per ottenere disponibilità
        fetch(`/PAI/prova/api/volunteer-api.php?action=availability&date=${selectedDate}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    renderTimeslots(data.data);
                }
            })
            .catch(err => {
                console.error('Load timeslots error:', err);
                timeslotContainer.innerHTML = '<p class="error-message">Errore nel caricamento</p>';
            });
    }

    function renderTimeslots(availability) {
        let html = '<div class="timeslots-grid">';

        timeslots.forEach(time => {
            const available = availability[time] || 0;
            const isFull = available >= maxVolunteersPerSlot;
            const isSelected = selectedSlots.includes(time);

            html += `
                <div class="timeslot-item ${isFull ? 'disabled' : 'available'}">
                    <input 
                        type="checkbox" 
                        id="slot-${time}" 
                        name="timeslots" 
                        value="${time}"
                        ${isFull ? 'disabled' : ''}
                        ${isSelected ? 'checked' : ''}
                        onchange="VolunteerBooking.toggleSlot('${time}', this.checked)"
                        aria-label="Fascia oraria ${time}"
                    >
                    <label for="slot-${time}" class="timeslot-label">
                        <strong>${time}</strong>
                        <small>${available}/${maxVolunteersPerSlot} volontari</small>
                    </label>
                </div>
            `;
        });

        html += '</div>';
        timeslotContainer.innerHTML = html;
    }

    function toggleSlot(time, isSelected) {
        if (isSelected) {
            if (!selectedSlots.includes(time)) {
                selectedSlots.push(time);
            }
        } else {
            selectedSlots = selectedSlots.filter(t => t !== time);
        }
    }

    // Event listeners
    volunteerDateInput.addEventListener('change', function() {
        if (this.value) {
            loadTimeslots();
        } else {
            timeslotContainer.innerHTML = '<p class="loading-timeslots">Seleziona una data</p>';
            selectedSlots = [];
        }
    });

    volunteerForm.addEventListener('submit', async function(e) {
        e.preventDefault();

        if (selectedSlots.length === 0) {
            FormValidator.showFormMessage(this, 'Seleziona almeno una fascia oraria', 'error');
            return;
        }

        const date = volunteerDateInput.value;
        const notes = document.getElementById('notes').value;

        FormValidator.setFormSubmitDisabled(this, true);

        try {
            const response = await fetch('/PAI/prova/api/volunteer-api.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({
                    date: date,
                    timeslots: selectedSlots,
                    notes: notes
                })
            });

            const data = await response.json();

            if (data.success) {
                FormValidator.showFormMessage(this, '✓ Turni prenotati con successo!', 'success');
                setTimeout(() => {
                    volunteerForm.reset();
                    selectedSlots = [];
                    timeslotContainer.innerHTML = '<p class="loading-timeslots">Seleziona una data</p>';
                    loadMyShifts();
                }, 1500);
            } else {
                FormValidator.showFormMessage(this, data.error || 'Errore nella prenotazione', 'error');
                // Ricarica disponibilità in caso di conflitto
                loadTimeslots();
            }
        } catch (err) {
            console.error('Booking error:', err);
            FormValidator.showFormMessage(this, 'Errore di connessione', 'error');
        } finally {
            FormValidator.setFormSubmitDisabled(this, false);
        }
    });

    // Caricamento iniziale
    function init() {
        loadMyShifts();
        timeslotContainer.innerHTML = '<p class="loading-timeslots">Seleziona una data per visualizzare le fasce orarie</p>';
    }

    return {
        init,
        toggleSlot,
        removeShift
    };
})();

document.addEventListener('DOMContentLoaded', function() {
    VolunteerBooking.init();
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
