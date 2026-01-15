<?php

session_start();
require_once "php/utils.php";

if(!isset($_SESSION["username"])){
    header("Location: login.php");
    exit();
}

require_once "php/dbConnection.php";
use DB\DBAccess;

$connessione = new DBAccess();

$paginaRecensione = file_get_contents("recensione.html");

if(isset($_SESSION['username'])) {
    $paginaRecensione = setta_link_area_personale($paginaRecensione);
} else {
    $paginaRecensione = setta_link_login($paginaRecensione);
}

/* 
    TEMPLATE
    <option value="" disabled selected>Dacci un voto!</option>
    <option value="1">1 "Mai più"</option>
    <option value="2">2 "Sopportabile"</option>
    <option value="3">3 "Carino"</option>
    <option value="4">4 "Bello"</option>
    <option value="5">5 "Da rifare"</option>
    */

try{
if(!$connessione->openDBConnection()){
    throw new Exception("Errore connessione DB");
}

// costruzione pagina in base alla richiesta di Area Personale
if(isset($_POST['modifica_recensione'])){

    $selectedAttrOptions = array("","","","","");
    $testo="";

    $infoRecensione = $connessione->getReview($_SESSION['username'], $_POST['nomeViaggio']);

    $testo = $infoRecensione["testo"];
    $selectedAttrOptions[intval($infoRecensione["punteggio"])-1] = "selected";
    
    $defaultOption = "
    <option value='' disabled>Dacci un voto!</option>
    <option value='1'".$selectedAttrOptions[0].">1 Mai più</option>
    <option value='2'".$selectedAttrOptions[1].">2 Sopportabile</option>
    <option value='3'".$selectedAttrOptions[2].">3 Carino</option>
    <option value='4'".$selectedAttrOptions[3].">4 Bello</option>
    <option value='5'".$selectedAttrOptions[4].">5 Da rifare</option>
    ";

    $azione = "
    <button type='submit' name='applica_modifiche' class='button-recensione' aria-label='applica modifiche recensione'>APPLICA MODIFICHE</button>
    ";

    $paginaRecensione = str_replace("[TESTO-RECENSIONE]",$testo, $paginaRecensione);
    $paginaRecensione = str_replace("[VALUTAZIONE]",$defaultOption, $paginaRecensione);
    $paginaRecensione = str_replace("[AZIONE]",$azione, $paginaRecensione);

    echo $paginaRecensione;
}
else if(isset($_POST['scrivi_recensione'])){
    $defaultOption = "
    <option value='' disabled selected>Dacci un voto!</option>
    <option value='1'>1 Mai più</option>
    <option value='2'>2 Sopportabile</option>
    <option value='3'>3 Carino</option>
    <option value='4'>4 Bello</option>
    <option value='5'>5 Da rifare</option>
    ";

    $azione = "
    <button type='submit' name='invia_recensione' class='button-recensione' aria-label='invia recensione'>INVIA</button>
    ";

    $paginaRecensione = str_replace("[TESTO-RECENSIONE]","", $paginaRecensione);
    $paginaRecensione = str_replace("[VALUTAZIONE]",$defaultOption, $paginaRecensione);
    $paginaRecensione = str_replace("[AZIONE]",$azione, $paginaRecensione);

    echo $paginaRecensione;
}

// azioni sul db in base alle azioni del form pagina Recensione
else if(isset($_POST['invia_recensione'])){

}
else if(isset($_POST['applica_modifiche'])){

}
else{
    header("Location: AreaPersonale.php");
    exit();
}
}
catch(Exception $e){

}


?>