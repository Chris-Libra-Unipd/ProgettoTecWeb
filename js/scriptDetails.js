// ============= INTRO ==============
var images = document.getElementsByClassName("coverImg");
var numImages = images.length;
var currentIndex = 1;
var controller = document.getElementById("swipingController");
var prevButton = document.getElementById("prevImg");
var nextButton = document.getElementById("nextImg");
var imgIndex = document.getElementById("imgIndex");

prevButton.addEventListener("click",showPrevImage);
nextButton.addEventListener("click",showNextImage);

function showNextImage(){
    if(currentIndex < numImages){
        for(i = 0; i < numImages; i++){
            images[i].style.transform = "translateX(-"+(102*currentIndex).toString()+"%)";
        }
        currentIndex++;
        imgIndex.innerHTML = currentIndex+"/"+numImages;
    }
}

function showPrevImage(){
    if(currentIndex > 1){
        for(i = 0; i < numImages; i++){
            images[i].style.transform = "translateX(-"+(102*(currentIndex-2)).toString()+"%)";
        }
        currentIndex--;
        imgIndex.innerHTML = currentIndex+"/"+numImages;
    }
}


// =============== PARTENZE =============

var opzioniPartenza = document.getElementsByClassName("opzionePartenza");
var riepilogo = document.getElementById("riepilogoAcquisto");

for (i = 0; i < opzioniPartenza.length; i++){
    opzioniPartenza[i].addEventListener("click", updateRiepilogo);
}

function updateRiepilogo(){
    var dataPartenza = this.querySelector(".dataPartenza").innerHTML;
    var dataArrivo = this.querySelector(".dataArrivo").innerHTML;
    var costo = this.querySelector(".costoFinale").innerHTML;

    riepilogo.querySelector("#riepilogoDataPartenza").innerHTML = dataPartenza;
    riepilogo.querySelector("#riepilogoDataArrivo").innerHTML = dataArrivo;
    riepilogo.querySelector("#riepilogoCosto").innerHTML = costo;
}



var radioButtons = document.getElementsByClassName("selectionIndicator");
for (i = 0; i < radioButtons.length; i++){
    radioButtons[i].addEventListener("change", updateState);
}

// Necessario controllare tutte le partenze perché il change event non è scatenato quando un radio perde il check
function updateState(){
    for (i = 0; i < radioButtons.length; i++){
        if(radioButtons[i].checked){
            radioButtons[i].setAttribute("aria-label","partenza selezionata");
        }
        else{
            radioButtons[i].setAttribute("aria-label","partenza non selezionata");
        }
    }
}

