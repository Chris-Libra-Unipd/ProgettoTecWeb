const field = document.getElementById("password");
const showHideButton = document.getElementById("showHidePsw");

function showHidePsw(){
    if(field.type == "password"){
        document.getElementById("showHideIcon").src="assets/icons/hidePsw.png";
        field.type = "text";
    }
    else if(field.type == "text"){
        document.getElementById("showHideIcon").src="assets/icons/showPsw.png";
        field.type = "password";
    }
}

showHideButton.addEventListener('click',showHidePsw);