document.addEventListener(
    "DOMContentLoaded",
    () => {

        const form =
            document.getElementById(
                "visitForm"
            );

        if (!form) {
            return;
        }

        const selectedCatsContainer =
            document.getElementById(
                "selectedCatsContainer"
            );

        const visitMessage =
            document.getElementById(
                "visitMessage"
            );

        let selectedCats = [];

        document.addEventListener(
            "catsSelected",
            (event) => {

                selectedCats =
                    event.detail;

                renderSelectedCats();

            }
        );

        function renderSelectedCats() {

            if (
                selectedCats.length === 0
            ) {

                selectedCatsContainer.innerHTML =
                    "<p>Nessun gatto selezionato.</p>";

                return;
            }

            selectedCatsContainer.innerHTML =
                `
                <p>
                    Gatti selezionati:
                    ${selectedCats.join(", ")}
                </p>
                `;
        }

        form.addEventListener(
            "submit",
            async (event) => {

                event.preventDefault();

                visitMessage.textContent = "";
                visitMessage.className = "";

                const dateTime =
                    document.getElementById(
                        "visitDateTime"
                    ).value;

                if (
                    dateTime === ""
                ) {

                    visitMessage.textContent =
                        "Selezionare data e ora.";

                    visitMessage.classList.add(
                        "error"
                    );

                    return;
                }

                if (
                    selectedCats.length === 0
                ) {

                    visitMessage.textContent =
                        "Selezionare almeno un gatto.";

                    visitMessage.classList.add(
                        "error"
                    );

                    return;
                }

                try {

                    const response =
                        await fetch(
                            "api/prenotazione-visita-api.php",
                            {
                                method: "POST",
                                headers: {
                                    "Content-Type":
                                        "application/json"
                                },
                                body:
                                    JSON.stringify({
                                        dataOra:
                                            dateTime,
                                        gatti:
                                            selectedCats
                                    })
                            }
                        );

                    const data =
                        await response.json();

                    if (
                        data.success
                    ) {

                        visitMessage.textContent =
                            "Prenotazione effettuata.";

                        visitMessage.classList.add(
                            "success"
                        );

                        form.reset();

                        selectedCats = [];

                        renderSelectedCats();

                        return;
                    }

                    visitMessage.textContent =
                        data.message;

                    visitMessage.classList.add(
                        "error"
                    );

                } catch (error) {

                    visitMessage.textContent =
                        "Errore di comunicazione.";

                    visitMessage.classList.add(
                        "error"
                    );
                }
            }
        );
    }
);