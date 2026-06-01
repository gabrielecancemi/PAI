document.addEventListener(
    "DOMContentLoaded",
    () => {

        const form =
            document.getElementById(
                "registrationForm"
            );

        const message =
            document.getElementById(
                "registrationMessage"
            );

        const usernameRegex =
            /^[A-Za-z][A-Za-z0-9_]*$/;

        const passwordRegex =
            /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z\d]).{8,16}$/;

        form.addEventListener(
            "submit",
            async (event) => {

                event.preventDefault();

                message.textContent = "";
                message.className = "";

                const nome =
                    document
                        .getElementById("nome")
                        .value
                        .trim();

                const cognome =
                    document
                        .getElementById("cognome")
                        .value
                        .trim();

                const indirizzo =
                    document
                        .getElementById("indirizzo")
                        .value
                        .trim();

                const username =
                    document
                        .getElementById("username")
                        .value
                        .trim();

                const password =
                    document
                        .getElementById("password")
                        .value;

                const confirmPassword =
                    document
                        .getElementById(
                            "confirmPassword"
                        )
                        .value;

                if (
                    nome === "" ||
                    cognome === "" ||
                    indirizzo === "" ||
                    username === "" ||
                    password === "" ||
                    confirmPassword === ""
                ) {

                    message.textContent =
                        "Tutti i campi sono obbligatori.";

                    message.classList.add(
                        "error"
                    );

                    return;
                }

                if (
                    !usernameRegex.test(
                        username
                    )
                ) {

                    message.textContent =
                        "Lo username deve iniziare con una lettera.";

                    message.classList.add(
                        "error"
                    );

                    return;
                }

                if (
                    !passwordRegex.test(
                        password
                    )
                ) {

                    message.textContent =
                        "La password deve essere lunga da 8 a 16 caratteri e contenere almeno una lettera maiuscola, una minuscola, un numero e un carattere speciale.";

                    message.classList.add(
                        "error"
                    );

                    return;
                }

                if (
                    password !==
                    confirmPassword
                ) {

                    message.textContent =
                        "Le password non coincidono.";

                    message.classList.add(
                        "error"
                    );

                    return;
                }

                try {

                    const formData =
                        new FormData(form);

                    const response =
                        await fetch(
                            "api/registrazione-api.php",
                            {
                                method: "POST",
                                body: formData
                            }
                        );

                    const data =
                        await response.json();

                    if (
                        data.success
                    ) {

                        message.textContent =
                            "Registrazione completata.";

                        message.classList.add(
                            "success"
                        );

                        form.reset();

                        setTimeout(
                            () => {

                                window.location.href =
                                    "login.php";

                            },
                            1200
                        );

                        return;
                    }

                    message.textContent =
                        data.message;

                    message.classList.add(
                        "error"
                    );

                } catch (error) {

                    message.textContent =
                        "Errore di comunicazione con il server.";

                    message.classList.add(
                        "error"
                    );
                }
            }
        );
    }
);