// ============= INTRO ==============
var coverImages = document.getElementsByClassName("coverImg");

var mainImages = document.getElementsByClassName("mainImg");
var mainImage = mainImages[0];

for (i = 0; i < coverImages.length; i++){
    coverImages[i].addEventListener("click", setMain);
}

function setMain(){
    var path = this.src;
    this.src = mainImage.src;
    mainImage.src=path;
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
