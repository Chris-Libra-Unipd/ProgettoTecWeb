<?php

session_start();
require_once "php/utils.php";

if(!isset($_SESSION["username"])){
    header("Location: login.php");
    exit();
}

$paginaRecensione = file_get_contents("recensione.html");

if(isset($_SESSION['username'])) {
    $paginaRecensione = setta_link_area_personale($paginaRecensione);
} else {
    $paginaRecensione = setta_link_login($paginaRecensione);
}


function checkReviewData($testo, $valutazione){
    if(strlen($testo) == 0){
        throw new Exception("Recensione vuota");
    }

    $valutazione = trim($valutazione);
    if(strlen($valutazione) == 0){
        throw new Exception("Valutazione vuota");
    }else if(!preg_match("/^[12345]$/",$valutazione)){
        throw new Exception("Valutazione out-of-range (1-5)".$valutazione);
    }
}


require_once "php/dbConnection.php";
use DB\DBAccess;

$connessione = new DBAccess();

$username = $_SESSION['username'];
$nomeViaggio = "";
$testo = "";
$valutazione = "";
$errore = "";
$esito = "";
$preview = "";
$azioni="";

try{

    if(!$connessione->openDBConnection()){
        throw new Exception("Errore connessione DB");
    }

    if(isset($_POST['nomeViaggio'])){
        $nomeViaggio = $_POST['nomeViaggio'];
        if(strlen($nomeViaggio) == 0){
            throw new Exception("Valutazione vuota");
        }
    }


    $path=$connessione->getMainImg($nomeViaggio);
    $preview = "<img id='imgPreview' src=". $path['url_immagine'] . " alt=''>";

    $nomeViaggioInput = "<input type='hidden' name='nomeViaggio' value='".$nomeViaggio."'>";

    //CRUD
    if(isset($_POST['applica_modifiche'])){
        $testoNuovo=$_POST['testo-recensione'];
        $valutazioneNuova=$_POST['valutazione'];
        $IDRecensioneModificata = $_POST['IDRecensione'];

        checkReviewData($testoNuovo, $valutazioneNuova);

        $connessione->editReview($IDRecensioneModificata, $testoNuovo, $valutazioneNuova);

        $esito = "<p class='esito' role='alert'>Recensione modificata!</p>";

        $queryString=http_build_query(["messaggio"=>"Recensione modificatata con successo!"]);
        header("Location: AreaPersonale.php?".$queryString);
        exit();
    }
    if(isset($_POST['invia_recensione'])){
        $testoNuovo=$_POST['testo-recensione'];
        $valutazioneNuova=$_POST['valutazione'];

        checkReviewData($testoNuovo, $valutazioneNuova);

        $email = $connessione->getUserInfo($username)['email'];

        $connessione->insertNewReview($email,$nomeViaggio,$testoNuovo,$valutazioneNuova);
        $esito = "<p class='esito' role='alert'>Recensione aggiunta!</p>";

        $queryString=http_build_query(["messaggio"=>"Recensione inviata!"]);
        header("Location: AreaPersonale.php?".$queryString);
        exit();
    }
    if(isset($_POST['elimina_recensione'])){
        $IDRecensione = $_POST['IDRecensione'];
        $connessione->deleteReview($IDRecensione);
        $esito = "<p class='esito' role='alert'>Recensione eliminata!</p>";

        $queryString=http_build_query(["messaggio"=>"Recensione eliminata con successo!"]);
        header("Location: AreaPersonale.php?".$queryString);
        exit();
    }
    
    // Mostra la pagine di recensione compilata a seguito di richiesta modifica recensione da AreaPersonale
    if(isset($_POST['modifica_recensione'])){

        //compilazione pagina recensione
        $selezioneOpzione = array("","","","","");

        $infoRecensione = $connessione->getReview($username, $nomeViaggio);
      

        $testo = $infoRecensione["testo"];
        $selezioneOpzione[intval($infoRecensione["punteggio"])-1] = "selected"; // se la seconda opzione è selezionata si ha ["","selected","","",""]
        $IDRecensioneModificata = $infoRecensione["id"];
        
        $opzioneValutazione = "
        <option value='' disabled>Dacci un voto!</option>
        <option value='1'".$selezioneOpzione[0].">1 Mai più</option>
        <option value='2'".$selezioneOpzione[1].">2 Sopportabile</option>
        <option value='3'".$selezioneOpzione[2].">3 Carino</option>
        <option value='4'".$selezioneOpzione[3].">4 Bello</option>
        <option value='5'".$selezioneOpzione[4].">5 Da rifare</option>
        ";

        $azione = "
        <input type='hidden' name='IDRecensione' value='".$IDRecensioneModificata."'>
        <button type='submit' name='elimina_recensione' class='button-recensione' aria-label='elimina recensione'>ELIMINA</a>
        <button type='submit' name='applica_modifiche' class='button-recensione' aria-label='applica modifiche recensione'>APPLICA MODIFICHE</button>
        ";
        
    }
    //mostra la pagina recensione non compilata a seguito di richiesta scrittura nuova recensione da area personale
    else if(isset($_POST['scrivi_recensione'])){

        $opzioneValutazione = "
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

    }
}
catch(Exception $e){
    echo $e->getMessage();
    $errore = "<p class='error' role='alert'>" . $e->getMessage() . "</p>";
}
finally{
    $connessione -> closeConnection();
    
    $paginaRecensione = str_replace("[PREVIEW]",$preview, $paginaRecensione);
    $paginaRecensione = str_replace("[NOME_VIAGGIO]",$nomeViaggio, $paginaRecensione);
    $paginaRecensione = str_replace("[NOME_VIAGGIO_INPUT]",$nomeViaggioInput, $paginaRecensione);
    $paginaRecensione = str_replace("[TESTO-RECENSIONE]",$testo, $paginaRecensione);
    $paginaRecensione = str_replace("[VALUTAZIONE]",$opzioneValutazione, $paginaRecensione);
    $paginaRecensione = str_replace("[AZIONI]",$azione, $paginaRecensione);
    $paginaRecensione = str_replace("[ERRORE]",$errore, $paginaRecensione);
    $paginaRecensione = str_replace("[ESITO]",$esito, $paginaRecensione);
    
    echo $paginaRecensione;
}


?>