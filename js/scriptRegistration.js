const nome = document.getElementById("nome");
const cognome = document.getElementById("cognome");
const dataNascita = document.getElementById("dataNascita");
const email = document.getElementById("email");
const username = document.getElementById("username");
const password = document.getElementById("password");
const formRegistrazione = document.getElementsByTagName("form")[0];

let isTopErrorPresent = false;

function showError(element, message) {
    element.innerHTML = message;
    element.classList.remove("hiddenError");
    element.classList.add("shownError");
}

function hideError(element) {
    element.innerHTML = "";
    element.classList.remove("shownError");
    element.classList.add("hiddenError");
}


function checkNome(){ 
    const nomeErr = document.getElementById("nomeError");
    const nomeValue = nome.value.trim();
    const regex = /^[A-Za-zÀ-ÿ' -]{2,50}$/;
    if(!regex.test(nomeValue)){
        showError(nomeErr, "Il nome deve avere tra 2 e 50 caratteri e contenere solo lettere, spazi, apostrofi o trattini");
        nome.setAttribute("aria-invalid", "true");
        return 0;
    }
    hideError(nomeErr);
    nome.removeAttribute("aria-invalid");
    return 1;
}

function checkCognome(){ 
    const cognomeErr = document.getElementById("cognomeError");
    const cognomeValue = cognome.value.trim();
    const regex = /^[A-Za-zÀ-ÿ' -]{2,50}$/;
    if(!regex.test(cognomeValue)){
        showError(cognomeErr, "Il cognome deve avere tra 2 e 50 caratteri e contenere solo lettere, spazi, apostrofi o trattini");
        cognome.setAttribute("aria-invalid", "true");
        return 0;
    }   
    hideError(cognomeErr);
    cognome.removeAttribute("aria-invalid");
    return 1;
}

function checkData(){ 
    const dataErr = document.getElementById("dataError");
    const dataValue = dataNascita.value;
    regex = /^\d{4}-\d{2}-\d{2}$/;

    if(!regex.test(dataValue) || new Date(dataValue) < new Date('1900-01-01') || new Date(dataValue) > new Date()){
        showError(dataErr, "Inserire una data valida non precedente a 01/01/1900 e non successiva alla data odierna");
        return 0;
    }
    hideError(dataErr);
    return 1;
}

function checkEmail(){ 
    const emailErr = document.getElementById("emailError");
    const emailValue = email.value.trim();
    const regex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{1,}$/;
    
    if(!regex.test(emailValue)){
        showError(emailErr, "Indirizzo Email non valido");
        return 0;
    }
    hideError(emailErr);
    return 1;
}

function checkUsername(){ 
    const usernameErr = document.getElementById("usernameError");
    const usernameValue = username.value.trim();
    console.log(usernameValue);
    const regex = /^[a-zA-Z0-9]{5,50}$/;

    if(!regex.test(usernameValue)){
        showError(usernameErr, "L'<span lang='en'>username</span> deve avere tra 5 e 50 caratteri e contenere solo caratteri alfanumerici");
        username.setAttribute("aria-invalid", "true");
        return 0;
    }
    hideError(usernameErr);
    username.removeAttribute("aria-invalid");
    return 1;
}

function checkPassword(){ 
    const passwordErr = document.getElementById("passwordError");
    const passwordValue = password.value;

    if(passwordValue.length < 8){
        showError(passwordErr, "La <span lang='en'>password</span> deve contenere almeno 8 caratteri");
        password.setAttribute("aria-invalid", "true");
        return 0;
    }
    hideError(passwordErr);
    password.removeAttribute("aria-invalid");
    return 1;
}

function preventSubmit(event) {
    const errori = document.getElementsByClassName("shownError");
    if(errori.length > 0) {
        event.preventDefault();
        if(!isTopErrorPresent){
            const errorP = document.createElement("p");
            errorP.classList.add("error");
            errorP.setAttribute("role", "alert");
            errorP.innerText = "Errori nei campi inseriti, si prega di ricontrollare";
            formRegistrazione.insertBefore(errorP, formRegistrazione.firstChild);
            isTopErrorPresent = true;
        }
        return;
    }
}

nome.addEventListener('focusout', checkNome);
cognome.addEventListener('focusout', checkCognome);
dataNascita.addEventListener('focusout', checkData);
email.addEventListener('focusout', checkEmail);
username.addEventListener('focusout', checkUsername);
password.addEventListener('focusout', checkPassword);
formRegistrazione.addEventListener('submit', preventSubmit);
