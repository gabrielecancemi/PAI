document.addEventListener(
    "DOMContentLoaded",
    () => {

        const form =
            document.getElementById(
                "volunteerForm"
            );

        if (!form) {
            return;
        }

        const dateTimeInput =
            document.getElementById(
                "volunteerDateTime"
            );

        const slotStatus =
            document.getElementById(
                "slotStatus"
            );

        const submitButton =
            form.querySelector(
                'button[type="submit"]'
            );

        const messageBox =
            document.getElementById(
                "volunteerMessage"
            );

        async function checkAvailability() {

            const value =
                dateTimeInput.value;

            if (!value) {

                slotStatus.textContent = "";
                submitButton.disabled = false;

                return;
            }

            const mysqlDateTime =
                value.replace(
                    "T",
                    " "
                ) + ":00";

            try {

                const response =
                    await fetch(
                        `api/check-slot-api.php?datetime=${encodeURIComponent(mysqlDateTime)}`
                    );

                const data =
                    await response.json();

                if (data.available) {

                    slotStatus.textContent =
                        `Disponibile (${data.current}/2 volontari iscritti)`;

                    slotStatus.className =
                        "success";

                    submitButton.disabled =
                        false;

                } else {

                    slotStatus.textContent =
                        "Fascia oraria completa.";

                    slotStatus.className =
                        "error";

                    submitButton.disabled =
                        true;
                }

            } catch (error) {

                slotStatus.textContent =
                    "Errore durante il controllo disponibilità.";

                slotStatus.className =
                    "error";
            }
        }

        dateTimeInput.addEventListener(
            "change",
            checkAvailability
        );

        form.addEventListener(
            "submit",
            async (event) => {

                event.preventDefault();

                messageBox.textContent = "";
                messageBox.className = "";

                const value =
                    dateTimeInput.value;

                if (!value) {

                    messageBox.textContent =
                        "Selezionare data e ora.";

                    messageBox.classList.add(
                        "error"
                    );

                    return;
                }

                const mysqlDateTime =
                    value.replace(
                        "T",
                        " "
                    ) + ":00";

                try {

                    const response =
                        await fetch(
                            "api/volontariato-api.php",
                            {
                                method: "POST",
                                headers: {
                                    "Content-Type":
                                        "application/json"
                                },
                                body:
                                    JSON.stringify({
                                        fascia_oraria:
                                            mysqlDateTime
                                    })
                            }
                        );

                    const data =
                        await response.json();

                    if (data.success) {

                        messageBox.textContent =
                            "Turno prenotato correttamente.";

                        messageBox.classList.add(
                            "success"
                        );

                        form.reset();

                        slotStatus.textContent =
                            "";

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