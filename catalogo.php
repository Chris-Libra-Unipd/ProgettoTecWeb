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
        $scontoPercentuale = $row['prezzo_min'] - $row['prezzo_scontato_min'];
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
            <a href=\"dettagli.php?viaggio=".urlencode($nome)."\" class=\"dettagli-viaggio-link\">scopri<span class=\"sr-only\">$nome</span></a>
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
            $listaCatalogo = "<li><p class='big-message'>Nessun viaggio trovato.</p></li>";
        }
    } else {
        $listaCatalogo = "<li><p class='big-error'>I nostri sistemi stanno avendo problemi, riprova più tardi.</p></li>";
    }
} catch(Exception $e) {
    $listaCatalogo = "<li><p class='big-error'>I nostri sistemi stanno avendo problemi, riprova più tardi.</p></li>";
}

$paginaCatalogo = str_replace("[TESTO_RICERCA]", htmlspecialchars($nome), $paginaCatalogo);
$paginaCatalogo = str_replace("[LISTA_CATALOGO]", $listaCatalogo, $paginaCatalogo);

echo $paginaCatalogo;


/*
$query = "
SELECT tv.nome AS tipo_nome, tv.descrizione AS tipo_descrizione,
       MIN(v.prezzo) AS prezzo_min,
       MIN(v.prezzo_scontato) AS prezzo_scontato_min,
       i.url_immagine, i.alt_text
FROM Tipo_Viaggio tv
JOIN Viaggio v ON v.tipo_viaggio_nome = tv.nome
LEFT JOIN Immagini i ON i.tipo_viaggio_nome = tv.nome AND i.periodo_itinerario_id IS NULL
GROUP BY tv.nome
ORDER BY tv.nome ASC
";

$result = $db->genericQuery($query);


$cardsHtml = "";
if($result && mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
        $nome = htmlspecialchars($row['tipo_nome']);
        $descrizione = htmlspecialchars($row['tipo_descrizione']);
        $immagine = htmlspecialchars($row['url_immagine'] ?? 'assets/img/t0.jpg');
        $alt = htmlspecialchars($row['alt_text'] ?? $nome);

        //sta roba serve per mostrare il prezzo scontato
        $prezzo = $row['prezzo_min'];
        if(!is_null($row['prezzo_scontato_min']) && $row['prezzo_scontato_min'] < $row['prezzo_min']) {
            $prezzo = $row['prezzo_scontato_min'];
            $scontoHtml = "<span class=\"sconto-viaggio\"><span class=\"sr-only\">sconto</span>-30%</span>";
        } else {
            $scontoHtml = "";
        }

        $cardsHtml .= "
        <li class=\"card-viaggio\">
            <img src=\"$immagine\" alt=\"$alt\">
            <div class=\"card-viaggio-info\">
                <h3>$nome</h3>
                <p><span>durata:</span> 7 giorni</p>
                <p><span>a partire da:</span>$prezzo € $scontoHtml</p>
            </div>
            <div class=\"link-dettagli-container\">
                <a href=\"dettagli.php?viaggio=".urlencode($nome)."\" class=\"dettagli-viaggio-link\">scopri<span class=\"sr-only\">$nome</span></a>
            </div>
        </li>
        ";
    }
} else {
    $cardsHtml = "<li>Nessun viaggio disponibile al momento.</li>";
}

$db->closeConnection();

 //sostituisco la parte di html con le card che ho creato
$paginaCatalogo = preg_replace(
    '/<ul id="container-lista-viaggi">.*?<\/ul>/s',
    "<ul id=\"container-lista-viaggi\">\n$cardsHtml\n</ul>",
    $paginaCatalogo
);

echo $paginaCatalogo;
*/
?>

