const messaggio = document.getElementsByClassName('messaggio')[0];


//siccome screen reader non legge subito messaggio che ha già role="alert", lo aggiungo dopo un millisecondo
//(i messaggi di conferma inserimento/modifica/eliminazione recensione)
function alertMessage() {
    if (messaggio) {
        setTimeout(() => {
            messaggio.setAttribute("role", "alert");
        }, 1);
    }
}

window.addEventListener("DOMContentLoaded",alertMessage);