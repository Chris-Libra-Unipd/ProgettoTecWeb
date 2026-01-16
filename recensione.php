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
    $paginaRecensione = str_replace("[PREVIEW]",$preview, $paginaRecensione);

    $nomeViaggioInput = "<input type='hidden' name='nomeViaggio' value='".$nomeViaggio."'>";
    $paginaRecensione = str_replace("[NOME_VIAGGIO]",$nomeViaggio, $paginaRecensione);
    $paginaRecensione = str_replace("[NOME_VIAGGIO_INPUT]",$nomeViaggioInput, $paginaRecensione);

    // Mostra la pagine di recensione compilata a seguito di
    // * richiesta modifica recensione da AreaPersonale
    // * applicazione modifiche recensione
    // * aggiunta nuova recensione
    if(isset($_POST['modifica_recensione']) || 
        isset($_POST['applica_modifiche']) ||
        isset($_POST['invia_recensione'])){

        if(isset($_POST['applica_modifiche'])){
            $testo=$_POST['contenuto-recensione'];
            $valutazione=$_POST['valutazione'];
            $IDRecensioneModificata = $_POST['IDRecensione'];

            checkReviewData($testo, $valutazione);

            $connessione->editReview($IDRecensioneModificata, $testo, $valutazione);

            $esito = "<p class='esito' role='alert'>Recensione modificata!</p>";
        }
        if(isset($_POST['invia_recensione'])){
            $testo=$_POST['contenuto-recensione'];
            $valutazione=$_POST['valutazione'];

            checkReviewData($testo, $valutazione);

            $email = $connessione->getUserInfo($username)['email'];

            $connessione->insertNewReview($email,$nomeViaggio,$testo,$valutazione);
            $esito = "<p class='esito' role='alert'>Recensione aggiunta!</p>";
        }

        //compilazione pagina recensione
        $selezioneOpzione = array("","","","","");

        $infoRecensione = $connessione->getReview($username, $nomeViaggio);
        // nel caso di visualizzazione mostra il risultato della query
        // nel caso di aggiunta e modifica il risultato della query coincide con la recensione appena scritta (poiché max 1 recensione per viaggio)

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

        $paginaRecensione = str_replace("[TESTO-RECENSIONE]",$testo, $paginaRecensione);
        $paginaRecensione = str_replace("[VALUTAZIONE]",$opzioneValutazione, $paginaRecensione);
        $paginaRecensione = str_replace("[AZIONI]",$azione, $paginaRecensione);
        
    }
    //mostra la pagina recensione non compilata a seguito di
    // * richiesta scrittura nuova recensione da area personale
    // * eliminazione recensione
    else if(isset($_POST['scrivi_recensione']) ||
        isset($_POST['elimina_recensione'])){

        if(isset($_POST['elimina_recensione'])){
            $IDRecensione = $_POST['IDRecensione'];
            $connessione->deleteReview($IDRecensione);
            $esito = "<p class='esito' role='alert'>Recensione eliminata!</p>";
        }

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

        $paginaRecensione = str_replace("[TESTO-RECENSIONE]","", $paginaRecensione);
        $paginaRecensione = str_replace("[VALUTAZIONE]",$opzioneValutazione, $paginaRecensione);
        $paginaRecensione = str_replace("[AZIONI]",$azione, $paginaRecensione);
        
    }
}
catch(Exception $e){
    echo $e->getMessage();
    $errore = "<p class='error' role='alert'>" . $e->getMessage() . "</p>";
}
finally{
    $connessione -> closeConnection();
    
    $paginaRecensione = str_replace("[ERRORE]",$errore, $paginaRecensione);
    $paginaRecensione = str_replace("[ESITO]",$esito, $paginaRecensione);
    
    echo $paginaRecensione;
}


?>