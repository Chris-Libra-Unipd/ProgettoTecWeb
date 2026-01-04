const email = document.getElementById("email");
const username = document.getElementById("username");
const password = document.getElementById("password");

function checkEmail(){ 
    const emailErr = document.getElementById("emailError");
    if(email.value.length == 0){
        emailErr.classList.remove("hiddenError");
        emailErr.classList.add("shownError");
        emailErr.innerHTML = "Inserisci un indirizzo Email";
        return 0;
    }else if(email.value.search(/^[\.\w]+\@[\w+\.]+$/) != 0){
        emailErr.classList.remove("hiddenError");
        emailErr.classList.add("shownError");
        emailErr.innerHTML = "Indirizzo Email non valido";
        return 0;
    }
    emailErr.classList.remove("shownError");
    emailErr.classList.add("hiddenError");
    return 1;
}

function checkUsername(){ 
    var usrMinLength = 5;
    const usernameErr = document.getElementById("usernameError");
    if(username.value.length < usrMinLength){
        usernameErr.classList.remove("hiddenError");
        usernameErr.classList.add("shownError");
        usernameErr.innerHTML = "L'username deve contenere almeno " + usrMinLength.toString() + " caratteri";
        return 0;
    }else if(username.value.search(/^[\w\!\#\$\%\&\'\.\*\/\=\?\_]+$/) != 0){
        usernameErr.classList.remove("hiddenError");
        usernameErr.classList.add("shownError");
        usernameErr.innerHTML = "L'username deve contenere caratteri alfanumerici o !#$%&'.*/=?_";
        return 0;
    }
    usernameErr.classList.remove("shownError");
    usernameErr.classList.add("hiddenError");
    usernameErr.focus();
    return 1;
}

function checkPassword(){ 
    var pswMinLength = 8;
    const passwordErr = document.getElementById("passwordError");
    if(password.value.length < pswMinLength){
        passwordErr.classList.remove("hiddenError");
        passwordErr.classList.add("shownError");
        passwordErr.innerHTML = "La password deve contenere almeno " + pswMinLength.toString() + " caratteri";
        return 0;
    }else if(password.value.search(/^[\w\!\#\$\%\&\'\.\*\/\=\?\_]+$/) != 0){
        passwordErr.classList.remove("hiddenError");
        passwordErr.classList.add("shownError");
        passwordErr.innerHTML = "La password deve contenere caratteri alfanumerici o !#$%&'.*/=?_";
        return 0;
    }
    passwordErr.classList.remove("shownError");
    passwordErr.classList.add("hiddenError");
    return 1;
}

email.addEventListener('focusout', checkEmail);
username.addEventListener('focusout', checkUsername);
password.addEventListener('focusout', checkPassword);
