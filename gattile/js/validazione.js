document.addEventListener("DOMContentLoaded", function () {
    const moduloRegistrazione = document.getElementById("formRegistrazione");
    if (moduloRegistrazione) {
        moduloRegistrazione.addEventListener("submit", function (evento) {
            const utente = document.getElementById("username").value;
            const chiave = document.getElementById("password").value;
            const confermaChiave = document.getElementById("conf_password").value;

            const regexUtente = /^[a-zA-Z]/; // Inizio alfabetico obbligatorio
            const regexChiave = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,16}$/; // Politica password

            if (!regexUtente.test(utente)) {
                evento.preventDefault();
                alert("Errore format: Lo username deve obbligatoriamente iniziare con una lettera dell'alfabeto.");
                return;
            }

            if (!regexChiave.test(chiave)) {
                evento.preventDefault();
                alert("Errore format: La password deve avere una lunghezza compresa tra 8 e 16 caratteri e contenere almeno una maiuscola, una minuscola, un numero e un simbolo speciale.");
                return;
            }

            if (chiave !== confermaChiave) {
                evento.preventDefault();
                alert("Errore convalida: Le due password digitate non corrispondono.");
                return;
            }
        });
    }

    const moduloGatto = document.getElementById("formGatto");
    if (moduloGatto) {
        moduloGatto.addEventListener("submit", function (evento) {
            const pesoCatturato = parseFloat(document.getElementById("peso").value);
            const etaCatturata = parseInt(document.getElementById("eta").value, 10);

            if (isNaN(pesoCatturato) || pesoCatturato <= 0 || isNaN(etaCatturata) || etaCatturata < 0) {
                evento.preventDefault();
                alert("Verificare la correttezza dei dati numerici immessi (Peso ed Età devono essere positivi).");
            }
        });
    }
});

