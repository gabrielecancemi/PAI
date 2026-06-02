document.addEventListener("DOMContentLoaded", () => {

    const button = document.querySelector(".menu-toggle");
    const nav = document.querySelector(".header nav");
    const account = document.querySelector(".stato-autenticazione");

    button.addEventListener("click", () => {

        // toggle: se presente la classe "aperto" la elimina, altrimenti la aggiunge
        nav.classList.toggle("aperto");        
        account.classList.toggle("aperto");

        const aperto =
            nav.classList.contains("aperto");

        button.setAttribute(
            "aria-expanded",
            aperto
        );

        button.textContent =
            aperto ? "✕" : "☰";
    });

});