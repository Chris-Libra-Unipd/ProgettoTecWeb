<?php
session_start();

if(!isset($_SESSION["username"])){
    header("Location: login.php");
    exit();
}

require_once "php/dbConnection.php";
use DB\DBAccess;

$paginaAreapersonale = file_get_contents("AreaPersonale.html");

$connessione = new DBAccess();

$infoUtente="";
$username = $_SESSION["username"];
$nome="";
$cognome="";
$email="";
$dataNascita="";

$messaggio="";
$errore="";

$listaViaggiAcquistati="";
$stringaViaggi="";

try{
    if(!$connessione->openDBConnection()){
        throw new Exception("Errore connessione DB");
    }

    //compilazione info utente
    $infoUtente = $connessione->getUserInfo($username);
    $nome=htmlspecialchars($infoUtente["nome"]);
    $cognome=htmlspecialchars($infoUtente["cognome"]);
    $email=htmlspecialchars($infoUtente["email"]);
    $dataNascita=htmlspecialchars(date("d/m/Y",strtotime($infoUtente["data_nascita"])));//conversione formato data

    if(isset($_GET['messaggio']))
            $messaggio="<p class='messaggio'>".htmlspecialchars($_GET['messaggio'])."</p>";
    if(isset($_GET['errore']))
            $errore="<p class='messaggio errore'>".htmlspecialchars($_GET['errore'])."</p>";

    //***Gestione logout
    if (isset($_POST['logout'])) {
        session_unset();
        session_destroy();
        header('Location: AreaPersonale.php');
        exit;
    }
    //***
    //***Gestione cancella account
    if (isset($_POST['deleteAccount'])) {
        $connessione->deleteAccount($email);
        session_unset();
        session_destroy();
        header('Location: index.php');
        exit;
    }
    //***
    
    $paginaAreapersonale = str_replace("[USERNAME]", $username, $paginaAreapersonale);
    $paginaAreapersonale = str_replace("[NOME]", $nome, $paginaAreapersonale);
    $paginaAreapersonale = str_replace("[COGNOME]", $cognome, $paginaAreapersonale);
    $paginaAreapersonale = str_replace("[EMAIL]", $email, $paginaAreapersonale);
    $paginaAreapersonale = str_replace("[DATA_NASCITA]", $dataNascita, $paginaAreapersonale);

    //compilazione viaggi
    $listaViaggiAcquistati = $connessione->getHistory($username);


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
                <fieldset>
                    <button type='submit' name='modifica_recensione' class='modifica-leggi-recensione'>MODIFICA RECENSIONE</button>
                </fieldset>
                ";
            }
            else{
                $stringaViaggi .= "
                <fieldset>
                    <button type='submit' name='scrivi_recensione' class='modifica-leggi-recensione'>SCRIVI RECENSIONE</button>
                </fieldset>
                ";
            }
        $stringaViaggi .= "</form>
        </li>
        ";
    }
    }
    else{
        $stringaViaggi = "<p id='nessun_viaggio_acquistato'> Non hai ancora viaggiato con noi!</p>";
    }

    $paginaAreapersonale = str_replace("[LISTA-STORICO-VIAGGI]", $stringaViaggi, $paginaAreapersonale);
}
catch(Exception $e){
    $errore = "<p class=messaggio errore'>" . $e->getMessage() . "</p>";
}

$paginaAreapersonale = str_replace("[MESSAGGIO]", $messaggio, $paginaAreapersonale);
$paginaAreapersonale = str_replace("[ERRORE]", $errore, $paginaAreapersonale);
$connessione->closeConnection();

$footerLink="";
if(isset($_SESSION['username']))
    $footerLink = "<li><span class='current-footer-link'>Area Personale</span></li>";
else
    $footerLink = "<li><a href='login.php' class='footer-link'>Accedi</a></li>";
$paginaAreapersonale = str_replace("[FOOTER_LINK]", $footerLink, $paginaAreapersonale);


echo $paginaAreapersonale;

?>