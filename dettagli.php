<?php

    session_start();

    require_once "php/dbConnection.php";
    require_once "php/utils.php";
    use DB\DBAccess;

    $paginaDettagli = file_get_contents("dettagli.html");

    if(isset($_SESSION['username'])) {
        $paginaDettagli = setta_link_area_personale($paginaDettagli);
    } else {
        $paginaDettagli = setta_link_login($paginaDettagli);
    }

    $nomeViaggio = urldecode($_GET['viaggio'] ?? null);

    if ($nomeViaggio === null) {
        die("Viaggio non specificato");
    }
    $paginaDettagli = str_replace("[NOME VIAGGIO]", $nomeViaggio, $paginaDettagli);

    try {
        // Accesso al database
        $db = new DBAccess();
        if (!$db->openDBConnection()) {
            die("Errore di connessione al database");
        }

        // Descrizione viaggio
        $descr_viaggio = $db->getVoyageDescription($nomeViaggio);
        $paginaDettagli = str_replace("[DESCRIZIONE_VIAGGIO]", $descr_viaggio , $paginaDettagli);

        // Immagini principali
        $mainImages = $db->getMainImages($nomeViaggio);
        foreach ($mainImages as $i => $img) {
            $paginaDettagli = str_replace("[IMG_VIAGGIO_".($i+1)."_URL]", $img['url_immagine'], $paginaDettagli);
            $paginaDettagli = str_replace("[IMG_VIAGGIO_".($i+1)."_ALT]", $img['alt_text'], $paginaDettagli);
        }

        // PERIODI E IMMAGINI
        $periods = $db->getPeriodsWithImages($nomeViaggio);
        foreach ($periods as $i => $periodo) {
            $idx = $i + 1;

            // Descrizione periodo
            $paginaDettagli = str_replace(
                "[DESCRIZIONE_PERIODO_VIAGGIO_{$idx}]",
                $periodo['descrizione'],
                $paginaDettagli
            );

            // Immagini periodo
            $paginaDettagli = str_replace("[IMG_PERIODO_{$idx}_URL]", $periodo['immagine']['url'], $paginaDettagli);
            $paginaDettagli = str_replace("[IMG_PERIODO_{$idx}_ALT]", $periodo['immagine']['alt'], $paginaDettagli);
        }

        // PARTENZE
        $departures = $db->getDepartures($nomeViaggio);
        foreach ($departures as $i => $dep) {
            $idx = $i + 1;
            // DATE
            $paginaDettagli = str_replace("[PARTENZA_{$idx}_DATA_INIZIO]", date("Y-m-d", strtotime($dep['data_inizio'])), $paginaDettagli);
            $paginaDettagli = str_replace("[PARTENZA_{$idx}_DATA_INIZIO_FORMAT]", date("d F Y", strtotime($dep['data_inizio'])), $paginaDettagli);
            $paginaDettagli = str_replace("[PARTENZA_{$idx}_DATA_FINE]", date("Y-m-d", strtotime($dep['data_fine'])), $paginaDettagli);
            $paginaDettagli = str_replace("[PARTENZA_{$idx}_DATA_FINE_FORMAT]", date("d F Y", strtotime($dep['data_fine'])), $paginaDettagli);

            // PREZZO
            $prezzo = "";
            if (!empty($dep['prezzo_scontato']) && $dep['prezzo_scontato'] < $dep['prezzo']) {
                $percent = round(
                    (($dep['prezzo'] - $dep['prezzo_scontato']) / $dep['prezzo']) * 100
                );

                $prezzo = '
                    <p class="costoIniziale"> Prezzo :'
                        . number_format($dep['prezzo'], 0, ',', '.') . '€
                    </span>
                    <span class="sconto">-' . $percent . '%</p>
                    <p class="costoFinale"> Prezzo scontato :' 
                        . number_format($dep['prezzo_scontato'], 0, ',', '.') . '€
                    </p>';

            } else {

                $prezzo = '<p class="costoFinale"> Prezzo :'
                    . number_format($dep['prezzo'], 0, ',', '.') . '€</p>';
            }

            $paginaDettagli = str_replace("[PARTENZA_{$idx}_PREZZO]", $prezzo, $paginaDettagli);


        }

        // RECENSIONI
        $recensioniHTML = "";
        $recensioni = $db->getReviewsByVoyage($nomeViaggio);
        if (count($recensioni) > 0) {
            foreach ($recensioni as $rec) {
                $username = htmlspecialchars($rec['username']);
                $testo = htmlspecialchars($rec['testo']);
                $punteggio = (int)$rec['punteggio'];
                $dataISO = $rec['data_recensione'];
                $dataFormattata = date("d/m/Y", strtotime($dataISO));

                $recensioniHTML .= "
                    <li class=\"recensione\">
                        <img src=\"assets/icons/user.png\" class=\"userIcon\" alt=\"\" />
                        <h3>{$username}</h3>
                        <p class=\"valutazione\">{$punteggio}/5</p>
                        <p>{$testo}</p>
                        <p class=\"dataRecensione\">
                            <time datetime=\"{$dataISO}\">{$dataFormattata}</time>
                        </p>
                    </li>
                ";
            }
        } else {
            $recensioniHTML = "
                <li class=\"no-recensioni-wrapper\">
                    <p class=\"no-recensioni\">Nessuna recensione disponibile per questo viaggio.</p>
                </li>
            ";
        }
        $paginaDettagli = str_replace("[RECENSIONI_VIAGGIO]", $recensioniHTML, $paginaDettagli);



    } catch(Exception $e) {
        $descr_viaggio = "<li><p class='big-error' role='alert'>Viaggio non trovato in database.</p></li>";
    }

    echo $paginaDettagli;

?>