document.addEventListener("DOMContentLoaded", function () {
    const moduloVolontariato = document.getElementById("formVolontariato");

    function sincronizzaFasceOrarie() {
        fetch("api/turni.php")
            .then(res => res.json())
            .then(listaTurni => {
                // Inizializza lo stato predefinito delle checkbox sul client
                document.querySelectorAll(".gruppo-checkbox-turni input").forEach(elementoInput => {
                    elementoInput.disabled = false;
                    const spanStato = document.getElementById("slot_" + elementoInput.id.split("_")[1]);
                    if (spanStato) {
                        spanStato.innerText = "(Posti Disponibili)";
                        spanStato.className = "etichetta-stato disponibile";
                    }
                });

                // Applica le disabilitazioni per le fasce che contano già 2 o più iscritti
                listaTurni.forEach(turnoDb => {
                    if (parseInt(turnoDb.totale, 10) >= 2) {
                        const inputPieno = document.querySelector(`input[value="${turnoDb.fascia_oraria}"]`);
                        if (inputPieno) {
                            inputPieno.disabled = true;
                            const idFascia = inputPieno.id.split("_")[1];
                            const spanStatoPieno = document.getElementById("slot_" + idFascia);
                            if (spanStatoPieno) {
                                spanStatoPieno.innerText = "(Turno Pieno - Limite 2 Volontari Raggiunto)";
                                spanStatoPieno.className = "etichetta-stato esaurito";
                            }
                        }
                    }
                });
            });
    }

    if (moduloVolontariato) {
        sincronizzaFasceOrarie();

        moduloVolontariato.addEventListener("submit", function (e) {
            e.preventDefault();
            const checkedBoxes = Array.from(document.querySelectorAll('input[name="turni[]"]:checked')).map(cb => cb.value);
            const feedbackBox = document.getElementById("volontariatoFeedback");

            if (checkedBoxes.length === 0) {
                alert("Selezionare almeno un turno orario prima di inviare.");
                return;
            }

            fetch("api/turni.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ turni: checkedBoxes })
            })
            .then(res => res.json())
            .then(data => {
                feedbackBox.style.display = "block";
                if (data.success) {
                    feedbackBox.className = "notifica successo";
                    feedbackBox.innerText = data.success;
                    sincronizzaFasceOrarie();
                } else {
                    feedbackBox.className = "notifica errore";
                    feedbackBox.innerText = data.error; // Mostra l'errore se la corsa critica si è verificata
                }
            });
        });
    }
});

