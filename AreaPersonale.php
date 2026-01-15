<?php
session_start();

if(!isset($_SESSION["username"])){
    header("Location: login.php");
    exit();
}

require_once "php/dbConnection.php";
use DB\DBAccess;

$paginaAreapersonale = file_get_contents("AreaPersonale.html");
$errore = "";

$connessione = new DBAccess();

$infoUtente="";
$username = $_SESSION["username"];
$nome="";
$cognome="";
$email="";
$dataNascita="";

$listaViaggiAcquistati="";
$stringaViaggi="";

try{
    if(!$connessione->openDBConnection()){
        throw new Exception("Errore connessione DB");
    }

    //compilazione info utente
    $infoUtente = $connessione->getUserInfo($username);
    $nome=$infoUtente["nome"];
    $cognome=$infoUtente["cognome"];
    $email=$infoUtente["email"];
    $dataNascita=date("d/m/Y",strtotime($infoUtente["data_nascita"]));//conversione formato data

    $paginaAreapersonale = str_replace("[USERNAME]", $username, $paginaAreapersonale);
    $paginaAreapersonale = str_replace("[NOME]", $nome, $paginaAreapersonale);
    $paginaAreapersonale = str_replace("[COGNOME]", $cognome, $paginaAreapersonale);
    $paginaAreapersonale = str_replace("[EMAIL]", $email, $paginaAreapersonale);
    $paginaAreapersonale = str_replace("[DATA_NASCITA]", $dataNascita, $paginaAreapersonale);

    //compilazione viaggi
    $listaViaggiAcquistati = $connessione->getHistory($username);

    /*
    TEMPLATE
    <li class="card-viaggio">
        <img src="assets/img/Gemini_Generated_Image_spirale.jpg" alt="">
        <div class="card-viaggio-info">
            <h3>Giardino indimenticabile</h3>
            <p>Partenza: <time datetime="2030-05-15"> 15 Maggio 2030 </time></p>
            <p>Ritorno: <time datetime="2034-12-30">30 Dicembre 2034</time></p>
        </div>
        <form class="azione-recensione" action="recensione.php" method="post">
            <input type="hidden" name="azione" value="modifica">
            <input type="hidden" name="nomeViaggio" value="Giardino indimenticabile">                        
            <button type="submit" class='modifica-leggi-recensione'>MODIFICA RECENSIONE</button>
        </form>
    </li>*/

    if($listaViaggiAcquistati){
    foreach($listaViaggiAcquistati as $viaggio){
        $stringaViaggi .= "
        <li class='card-viaggio'>
            <img src='" . $viaggio['url_immagine'] . "' alt=''>
            <div class='card-viaggio-info'>
                <h3>" . $viaggio['tipo_viaggio_nome'] ."</h3>
                <p>Partenza: <time datetime='" . $viaggio['data_inizio'] . "'>" . date('d/m/Y',strtotime($viaggio['data_inizio'])) . "</time></p>
                <p>Ritorno: <time datetime='" . $viaggio['data_fine'] . "'>" . date('d/m/Y',strtotime($viaggio['data_fine'])) . "</time></p>
            </div>
            <form class='azione-recensione' action='recensione.php' method='post'>
                <input type='hidden' name='nomeViaggio' value='" . $viaggio['tipo_viaggio_nome'] . "'>";
            if($connessione->checkIfReviewed($username,$viaggio['tipo_viaggio_nome'])){
                $stringaViaggi .= "
                <button type='submit' name='modifica_recensione' class='modifica-leggi-recensione'>MODIFICA RECENSIONE</button>
                ";
            }
            else{
                $stringaViaggi .= "
                <button type='submit' name='scrivi_recensione' class='modifica-leggi-recensione'>SCRIVI RECENSIONE</button>
                ";
            }
        $stringaViaggi .= "</form>
        </li>
        ";
    }
    }
    else{
        $stringaViaggi = "<p id='nessun_viaggio_acquistato'> Non hai ancora viaggiato con noi</p>";
    }

    $paginaAreapersonale = str_replace("[LISTA-STORICO-VIAGGI]", $stringaViaggi, $paginaAreapersonale);
}
catch(Exception $e){
    $errore = "<p class='error' role='alert'>" . $e->getMessage() . "</p>";
}
finally{
    $connessione->closeConnection();
}

echo $paginaAreapersonale;

?>