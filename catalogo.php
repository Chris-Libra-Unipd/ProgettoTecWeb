<?php

session_start();

require_once "php/dbConnection.php";
require_once "php/utils.php";
use DB\DBAccess;

$paginaCatalogo = file_get_contents("catalogo.html");

if(isset($_SESSION['username'])) {
    $paginaCatalogo = setta_link_area_personale($paginaCatalogo);
} else {
    $paginaCatalogo = setta_link_login($paginaCatalogo);
}

$listaCatalogo = "";
$testoRicerca = "";
$nome = "";


function creaCardViaggio($row) {
    $nome = htmlspecialchars($row['tipo_nome']);
    $immagine = htmlspecialchars($row['url_immagine']);
    $durata = htmlspecialchars($row['durata_giorni']);
    $prezzo = number_format($row['prezzo_min'], 2, ',', '.');

    if(!is_null($row['prezzo_scontato_min']) && $row['prezzo_scontato_min'] < $row['prezzo_min']) {
        $prezzo = number_format($row['prezzo_scontato_min'], 2, ',', '.');
        $scontoHtml = "<span class=\"sconto-viaggio\">SCONTATO!</span>";
    } else {
        $scontoHtml = "";
    }

    return "
    <li class=\"card-viaggio\">
        <img src=\"$immagine\" alt=\"\">
        <div class=\"card-viaggio-info\">
            <h3>$nome</h3>
            <p><span>durata:</span> $durata giorni</p>
            <p><span>a partire da: </span>$prezzo € $scontoHtml</p>
        </div>
        <div class=\"link-dettagli-container\">
            <a href=\"dettagli.php?viaggio=".urlencode($nome)."\" class=\"dettagli-viaggio-link\">SCOPRI<span class=\"sr-only\">$nome</span></a>
        </div>
    </li>
    ";
}


try {
    $db = new DBAccess();
    if(isset($_GET['premuto']) && $_GET['premuto'] === 'cerca') {
            $nome = trim($_GET['nome']);
        }
    if($db->openDBConnection()) {
        $result = $db->getListaViaggi($nome);
        $db->closeConnection();
        if($result && mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_assoc($result)) {
                $listaCatalogo .= creaCardViaggio($row);
            }
        } else {
            $listaCatalogo = "<li><p class='big-message' role='alert'>Nessun viaggio trovato.</p></li>";
        }
    } else {
        $listaCatalogo = "<li><p class='big-error' role='alert'>I nostri sistemi stanno avendo problemi, riprova più tardi.</p></li>";
    }
} catch(Exception $e) {
    $listaCatalogo = "<li><p class='big-error' role='alert'>I nostri sistemi stanno avendo problemi, riprova più tardi.</p></li>";
}

$paginaCatalogo = str_replace("[TESTO_RICERCA]", htmlspecialchars($nome), $paginaCatalogo);
$paginaCatalogo = str_replace("[LISTA_CATALOGO]", $listaCatalogo, $paginaCatalogo);

$footerLink="";
if(isset($_SESSION['username']))
    $footerLink = "<li><a href='AreaPersonale.php' class='footer-link'>Area Personale</a></li>";
else
    $footerLink = "<li><a href='login.php' class='footer-link'>Accedi</a></li>";
$paginaCatalogo = str_replace("[FOOTER_LINK]", $footerLink, $paginaCatalogo);

echo $paginaCatalogo;

?>

