<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'header.php';
?>

<main class="contenuto-principale">
    <section class="box-formulario broad">
        <h2>Pianificazione Turni di Volontariato</h2>
        <p>Seleziona i turni desiderati. Al fine di garantire la sicurezza operativa, ogni fascia supporta un massimo di 2 partecipanti contemporanei.</p>

        <?php if (!isset($_SESSION['user_id'])): ?>
            <aside class="avviso-autenticazione">
                <p>⚠️ Accesso riservato: autenticati per inserire la tua disponibilità oraria.</p>
            </aside>
        <?php else: ?>
            <form id="formVolontariato">
                <fieldset class="gruppo-checkbox-turni">
                    <legend>Fasce Orarie Disponibili (Venerdì 5 Giugno 2026)</legend>
                    
                    <p class="opzione-checkbox-turno">
                        <input type="checkbox" name="turni[]" id="turno_09" value="2026-06-05 09:00:00">
                        <label for="turno_09">Fascia Mattutina (09:00 - 11:00)</label>
                        <span id="slot_09" class="etichetta-stato">Controllo disponibilità...</span>
                    </p>

                    <p class="opzione-checkbox-turno">
                        <input type="checkbox" name="turni[]" id="turno_11" value="2026-06-05 11:00:00">
                        <label for="turno_11">Fascia Centrale (11:00 - 13:00)</label>
                        <span id="slot_11" class="etichetta-stato">Controllo disponibilità...</span>
                    </p>

                    <p class="opzione-checkbox-turno">
                        <input type="checkbox" name="turni[]" id="turno_15" value="2026-06-05 15:00:00">
                        <label for="turno_15">Fascia Pomeridiana (15:00 - 17:00)</label>
                        <span id="slot_15" class="etichetta-stato">Controllo disponibilità...</span>
                    </p>
                </fieldset>

                <p class="pulsanti-form">
                    <button type="submit" id="btnSalvaVolontariato">Registra i miei Turni</button>
                </p>
                <p id="volontariatoFeedback" class="notifica" style="display:none;"></p>
            </form>
        <?php endif; ?>
    </section>
</main>

<script src="js/volontariato.js"></script>
<?php include 'footer.php'; ?>

