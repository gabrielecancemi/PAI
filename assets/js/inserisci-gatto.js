document.addEventListener(
    "DOMContentLoaded",
    () => {

        const form =
            document.getElementById(
                "catForm"
            );

        if (!form) {
            return;
        }

        const messageBox =
            document.getElementById(
                "catMessage"
            );

        form.addEventListener(
            "submit",
            async (event) => {

                event.preventDefault();

                messageBox.textContent = "";
                messageBox.className = "";

                const formData = {

                    nome:
                        document.getElementById(
                            "nome"
                        ).value.trim(),

                    descrizione:
                        document.getElementById(
                            "descrizione"
                        ).value.trim(),

                    peso:
                        document.getElementById(
                            "peso"
                        ).value.trim(),

                    colore_mantello:
                        document.getElementById(
                            "colore_mantello"
                        ).value.trim(),

                    lunghezza_pelo:
                        document.getElementById(
                            "lunghezza_pelo"
                        ).value.trim(),

                    razza:
                        document.getElementById(
                            "razza"
                        ).value.trim(),

                    colore_occhi:
                        document.getElementById(
                            "colore_occhi"
                        ).value.trim(),

                    eta:
                        document.getElementById(
                            "eta"
                        ).value.trim(),

                    sesso:
                        document.getElementById(
                            "sesso"
                        ).value,

                    data_arrivo:
                        document.getElementById(
                            "data_arrivo"
                        ).value
                };

                if (
                    Object.values(formData)
                        .some(
                            value =>
                                value === ""
                        )
                ) {

                    messageBox.textContent =
                        "Compilare tutti i campi.";

                    messageBox.classList.add(
                        "error"
                    );

                    return;
                }

                if (
                    Number(formData.peso) <= 0
                ) {

                    messageBox.textContent =
                        "Peso non valido.";

                    messageBox.classList.add(
                        "error"
                    );

                    return;
                }

                if (
                    Number(formData.eta) < 0
                ) {

                    messageBox.textContent =
                        "Età non valida.";

                    messageBox.classList.add(
                        "error"
                    );

                    return;
                }

                try {

                    const response =
                        await fetch(
                            "api/inserisci-gatto-api.php",
                            {
                                method: "POST",
                                headers: {
                                    "Content-Type":
                                        "application/json"
                                },
                                body:
                                    JSON.stringify(
                                        formData
                                    )
                            }
                        );

                    const data =
                        await response.json();

                    if (
                        data.success
                    ) {

                        messageBox.textContent =
                            "Gatto inserito correttamente.";

                        messageBox.classList.add(
                            "success"
                        );

                        form.reset();

                        return;
                    }

                    messageBox.textContent =
                        data.message;

                    messageBox.classList.add(
                        "error"
                    );

                } catch (error) {

                    messageBox.textContent =
                        "Errore di comunicazione.";

                    messageBox.classList.add(
                        "error"
                    );
                }
            }
        );
    }
);