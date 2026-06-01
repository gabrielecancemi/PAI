document.addEventListener(
    "DOMContentLoaded",
    () => {

        const loginForm =
            document.getElementById(
                "loginForm"
            );

        const messageContainer =
            document.getElementById(
                "loginMessage"
            );

        loginForm.addEventListener(
            "submit",
            async (event) => {

                event.preventDefault();

                messageContainer.textContent = "";
                messageContainer.className = "";

                const username =
                    document
                        .getElementById("username")
                        .value
                        .trim();

                const password =
                    document
                        .getElementById("password")
                        .value
                        .trim();

                if (username === "") {

                    messageContainer.textContent =
                        "Inserire lo username.";

                    messageContainer.classList.add(
                        "error"
                    );

                    return;
                }

                if (password === "") {

                    messageContainer.textContent =
                        "Inserire la password.";

                    messageContainer.classList.add(
                        "error"
                    );

                    return;
                }

                try {

                    const formData =
                        new FormData(loginForm);

                    const response =
                        await fetch(
                            "api/login-api.php",
                            {
                                method: "POST",
                                body: formData
                            }
                        );

                    const data =
                        await response.json();

                    if (data.success) {

                        messageContainer.textContent =
                            "Accesso effettuato.";

                        messageContainer.classList.add(
                            "success"
                        );

                        setTimeout(
                            () => {
                                window.location.href =
                                    "index.php";
                            },
                            800
                        );

                        return;
                    }

                    messageContainer.textContent =
                        data.message;

                    messageContainer.classList.add(
                        "error"
                    );

                } catch (error) {

                    messageContainer.textContent =
                        "Errore di comunicazione con il server.";

                    messageContainer.classList.add(
                        "error"
                    );
                }
            }
        );
    }
);