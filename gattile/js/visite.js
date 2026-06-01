document.addEventListener("DOMContentLoaded", function () {
    const moduloVisita = document.getElementById("formPrenotazioneVisita");
    const riquadroNomiGatti = document.getElementById("listaGattiSelezionati");
    let elencoIdGattiScelti = [];

    // Ascolto del CustomEvent cross-tecnologico generato dal catalogo React
    document.addEventListener("gattiSelezionatiCambiati", function (eventoDati) {
        const arrayGatti = eventoDati.detail.gatti;
        elencoIdGattiScelti = arrayGatti.map(felino => felino.id);

        if (arrayGatti.length === 0) {
            riquadroNomiGatti.innerHTML = "<em>Nessun gatto selezionato nella griglia a sinistra.</em>";
        } else {
            // Rendering pulito dei badge dei gatti pronti per la prenotazione
            riquadroNomiGatti.innerHTML = arrayGatti.map(f => `<span class="badge-gatto">${f.nome}</span>`).join(" ");
        }
    });

    if (moduloVisita) {
        moduloVisita.addEventListener("submit", function (e) {
            e.preventDefault();
            const dataOraScelta = document.getElementById("data_ora_visita").value;
            const contenitoreFeedback = document.getElementById("visitaFeedbackMessage");

            if (elencoIdGattiScelti.length === 0) {
                alert("Scegliere almeno un gatto dal catalogo interattivo prima di inoltrare la richiesta.");
                return;
            }
            if (!dataOraScelta) {
                alert("Specificare un giorno ed un orario valido per la visita conoscitiva.");
                return;
            }

            fetch("api/prenota.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ gatti_ids: elencoIdGattiScelti, data_ora: dataOraScelta })
            })
            .then(risposta => risposta.json())
            .then(datiRisposta => {
                contenitoreFeedback.style.display = "block";
                if (datiRisposta.success) {
                    contenitoreFeedback.className = "notifica successo";
                    contenitoreFeedback.innerText = datiRisposta.success;
                    moduloVisita.reset();
                    riquadroNomiGatti.innerHTML = "<em>Nessun gatto selezionato nella griglia a sinistra.</em>";
                } else {
                    contenitoreFeedback.className = "notifica errore";
                    contenitoreFeedback.innerText = datiRisposta.error;
                }
            });
        });
    }
});

