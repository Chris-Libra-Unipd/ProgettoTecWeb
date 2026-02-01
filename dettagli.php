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

    $messaggio="";
    $email="";

    try {
         // Accesso al database
        $db = new DBAccess();
        if (!$db->openDBConnection()) {
            throw new Exception ("Errore di connessione al database");
        }

        if(isset($_SESSION['username'])){
            $userInfo = $db->getUserInfo($_SESSION['username']);
            $email=$userInfo['email'];
        }

        //*** Gestione evento acquisto viaggio proveniente da pagina dettaglio
        if(isset($_POST['choice'])){
            $IdPartenza = $_POST['choice'];
            //se non autenticati si reindirizza alla login
            if(!isset($_SESSION['username'])){
                header("Location: login.php?");
                exit();
            }

            try {
                if(!$db->checkVoyageExists($IdPartenza)){
                    throw new Exception("Viaggio non esistente");
                }
                $db->buyVoyage($email, $IdPartenza);

                //Redirect per seguire il pattern PostRedirectGet per evitare che con il refresh, l'acquisto venga affettuato più volte
                $queryString=http_build_query(["messaggio"=>"Acquisto effettuato con successo!"]);
                header("Location: AreaPersonale.php?".$queryString);
                exit();
            }
            catch (Exception $e) {
                // qualunque altro errore
                $queryString = http_build_query([
                    "messaggio" => "Errore durante l'acquisto del viaggio"
                ]);
                header("Location: AreaPersonale.php?".$queryString);
                exit();
            }
        }
        //***

        if(isset($_GET['viaggio']))
            $nomeViaggio = urldecode($_GET['viaggio']);
        else{//se viaggio non specificato reindirizza al catalogo
            header("Location: Catalogo.php?");
            exit();
        }

        if ($nomeViaggio === null) {
            throw new Exception ("Viaggio non specificato");
        }
        $paginaDettagli = str_replace("[NOME VIAGGIO]", $nomeViaggio, $paginaDettagli);
        $paginaDettagli = str_replace("[NAME_TITLE]", $nomeViaggio, $paginaDettagli);

        // Descrizione viaggio
        $descr_viaggio = $db->getVoyageDescription($nomeViaggio);
        $paginaDettagli = str_replace("[DESCRIZIONE_VIAGGIO]", $descr_viaggio , $paginaDettagli);

        // Immagini principali
        $mainImages = $db->getMainImages($nomeViaggio);
        $imagesString="";
        foreach ($mainImages as $index => $img) {
            if($index != 0){
                $imagesString .= "<img class='coverImg' src='".$img['url_immagine']."' aria-hidden='true' alt='".$img['alt_text']."'/>";
            } else {
                $imagesString .= "<img class='coverImg' src='".$img['url_immagine']."' alt='".$img['alt_text']."'/>";
            }
        }
        $paginaDettagli = str_replace("[LISTA_IMMAGINI_PRINCIPALI]", $imagesString , $paginaDettagli);

        // PERIODI E IMMAGINI
        $periods = $db->getPeriodsWithImages($nomeViaggio);
        $periodsString="";
        foreach ($periods as $i => $periodo) {
            $idx = $i + 1;
            $periodsString .= "
                <li class='itemItinerario'>
                    <img class='itineraryImg' src='".$periodo['url_immagine']."' alt='".$periodo['alt_text']."'>
                    <div>
                        <h3>Periodo ".$idx."</h3>
                        <p>".$periodo['descrizione']."</p>
                    </div>
                </li>
            ";
        }
        $paginaDettagli = str_replace("[LISTA_PERIODI]", $periodsString, $paginaDettagli);

        // PARTENZE
        //se autenticati mostra solo le partenze non acquistate
        //se non autenticati mostra tutte le partenze
        if(isset($_SESSION['username']))
            $departures = $db->getAvailableDepartures($email, $nomeViaggio);
        else{
            $departures = $db->getAllDepartures($nomeViaggio);
        }
        $departuresString="";
        foreach ($departures as $i => $dep) {
            //per il formato esteso in italiano
            $mesi_it = [
                1 => "gennaio", 2 => "febbraio", 3 => "marzo", 4 => "aprile",
                5 => "maggio", 6 => "giugno", 7 => "luglio", 8 => "agosto",
                9 => "settembre", 10 => "ottobre", 11 => "novembre", 12 => "dicembre"
            ];
                        
            $dataInizio = strtotime($dep['data_inizio']);
            $dataFine =  strtotime($dep['data_fine']);

            $dataInizioITA = date("d",$dataInizio) ." ". $mesi_it[(int)date("n",$dataInizio)] ." ". date("Y",$dataInizio) ;
            $dataFineITA = date("d",$dataFine) ." ". $mesi_it[(int)date("n",$dataFine)] ." ". date("Y",$dataFine) ;

            $idx = $i + 1;
            $departuresString .="
                <label for='"."choice{$idx}"."' class='opzionePartenza' tabindex='0'>";

            $departuresString .="
                <input type='radio' class='selectionIndicator' name='choice' id='"."choice{$idx}"."' value='".$dep['id']."'/>
                <img class='calendar-icon' src='assets/icons/calendar.png' width='35' alt=''/>
                <time datetime='".$dep['data_inizio']."' class='dataPartenza'><span class='sr-only'>Data partenza: </span>".$dataInizioITA."</time>
                <img class='arrow-icon' src='assets/icons/right-arrow.png' width='35' alt=''/>
                <time datetime='".$dep['data_fine']."' class='dataArrivo'><span class='sr-only'>Data ritorno: </span>".$dataFineITA."</time>
            ";
            // PREZZO
            if (!empty($dep['prezzo_scontato']) && $dep['prezzo_scontato'] < $dep['prezzo']) {
                $percent = round((($dep['prezzo'] - $dep['prezzo_scontato']) / $dep['prezzo']) * 100);

                /*$departuresString .="
                    <div>
                        <p>Prezzo: <span class='costoIniziale'>".number_format($dep['prezzo'],0,",",".")."€</span><span class='sconto'>Sconto ".$percent."%</span></p>
                        <p>Prezzo finale: <span class='costoFinale'>".number_format($dep['prezzo_scontato'],0,",",".")."€</span></p>
                    </div>
                ";*/

                $departuresString .="
                    <span class='containerPrezzi'>
                        <span>Prezzo: <span class='costoIniziale'>".number_format($dep['prezzo'],0,",",".")."€</span><span class='sconto'>Sconto ".$percent."%</span></span>
                        <span>Prezzo finale: <span class='costoFinale'>".number_format($dep['prezzo_scontato'],0,",",".")."€</span></span>
                    </span>
                    ";
            } else {
                $departuresString .="
                    <span>Prezzo: <span class='costoFinale'> ".number_format($dep['prezzo'],0,",",".")."€</span></span>
                ";
            }
            $departuresString .= "</label>";
        }
        if($departuresString == ""){
            $departuresString .= "<p class='no-result'>Nessuna partenza disponibile.</p>";
            $acquista = "<button type='submit' id='buyButton' tabindex='0' disabled>ACQUISTA</button>";
        }
        else{
            $acquista = "<button type='submit' id='buyButton' tabindex='0'>ACQUISTA</button>"; 
        }
        $paginaDettagli = str_replace("[LISTA_PARTENZE]", $departuresString, $paginaDettagli);
        $paginaDettagli = str_replace("[AZIONE-ACQUISTA]", $acquista, $paginaDettagli);


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
                <li class=\"no-result-wrapper\">
                    <p class=\"no-result\">Nessuna recensione disponibile per questo viaggio.</p>
                </li>
            ";
        }
        $paginaDettagli = str_replace("[RECENSIONI_VIAGGIO]", $recensioniHTML, $paginaDettagli);



    } catch(Exception $e) {
        $messaggio = "<p class='messaggio' role='alert'>" . $e->getMessage() . "</p>";
    }

    $db->closeConnection();
    $paginaDettagli = str_replace("[MESSAGGIO]", $messaggio, $paginaDettagli);

    $footerLink="";
    if(isset($_SESSION['username']))
        $footerLink = "<li><a href='AreaPersonale.php' class='footer-link'>Area Personale</a></li>";
    else
        $footerLink = "<li><a href='login.php' class='footer-link'>Accedi</a></li>";
    $paginaDettagli = str_replace("[FOOTER_LINK]", $footerLink, $paginaDettagli);

    echo $paginaDettagli;

?>