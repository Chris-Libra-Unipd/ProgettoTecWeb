<?php

session_start();

require_once "php/dbConnection.php";
use DB\DBAccess;

if(isset($_SESSION["username"])){
    header("Location: AreaPersonale.php");
    exit;
}

$maxData = date('Y-m-d'); //data massima per il campo data di nascita

$topError = "";                 //errore generale in cima al form
$errorStatus = false;           //indica se c'è un errore nel form
$serverError = false;           //indica se c'è stato un errore di connessione al db

$nomeValue = "";                //valore del campo
$nomeStatus = "hiddenError";    //classe per mostrare/nascondere l'errore
$nomeError = "";                //messaggio di errore
$nomeValid = "";                //attriburo aria-invalid

$cognomeValue = "";
$cognomeStatus = "hiddenError";
$cognomeError = "";
$cognomeValid = "";

$dataValue = "";
$dataStatus = "hiddenError";
$dataError = "";
$dataValid = "";

$emailValue = "";
$emailStatus = "hiddenError";
$emailError = "";
$emailValid = "";

$usernameValue = "";
$usernameStatus = "hiddenError";
$usernameError = "";
$usernameValid = "";

$passwordValue = "";
$passwordStatus = "hiddenError";
$passwordError = "";
$passwordValid = "";

$paginaLogin = file_get_contents("registrazione.html");

function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}


if(isset($_POST["register"])) {
    if(isset($_POST["nome"]) && preg_match("/^[A-Za-zÀ-ÿ' -]{2,50}$/", trim($_POST["nome"]))) {
        $nomeValue = trim($_POST["nome"]);
    } else {
        $nomeValue = htmlspecialchars(trim($_POST["nome"]));
        $nomeStatus = "shownError";
        $nomeError = "Il nome deve avere tra 2 e 50 caratteri e contenere solo lettere, spazi, apostrofi o trattini";
        $nomeValid = "aria-invalid='true'";
        $errorStatus = true;
    }

    if(isset($_POST["cognome"]) && preg_match("/^[A-Za-zÀ-ÿ' -]{2,50}$/", trim($_POST["cognome"]))) {
        $cognomeValue = trim($_POST["cognome"]);
    } else {
        $cognomeValue = htmlspecialchars(trim($_POST["cognome"]));
        $cognomeStatus = "shownError";
        $cognomeError = "Il cognome deve avere tra 2 e 50 caratteri e contenere solo lettere, spazi, apostrofi o trattini";
        $cognomeValid = "aria-invalid='true'";
        $errorStatus = true;
    }

    if(isset($_POST["dataNascita"]) && preg_match("/^\d{4}-\d{2}-\d{2}$/", $_POST["dataNascita"]) && ($_POST["dataNascita"] >= '1900-01-01') && ($_POST["dataNascita"] <= date('Y-m-d'))) {
        $dataValue = trim($_POST["dataNascita"]);
    } else {
        $dataValue = htmlspecialchars(trim($_POST["dataNascita"]));
        $dataStatus = "shownError";
        $dataError = "Inserire una data valida non precedente a 01/01/1900 e non successiva alla data odierna";
        $dataValid = "aria-invalid='true'";
        $errorStatus = true;
    }

    if(isset($_POST["email"]) && preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{1,}$/", trim($_POST["email"]))) {
        $emailValue = trim($_POST["email"]);
    } else {
        $emailValue = htmlspecialchars(trim($_POST["email"]));
        $emailStatus = "shownError";
        $emailError = "Inserire un'email valida";
        $emailValid = "aria-invalid='true'";
        $errorStatus = true;
    }

    if(isset($_POST["username"]) && preg_match("/^[a-zA-Z0-9]{5,50}$/", trim($_POST["username"]))) {
        $usernameValue = trim($_POST["username"]);
    } else {
        $usernameValue = htmlspecialchars(trim($_POST["username"]));
        $usernameStatus = "shownError";
        $usernameError = "L'<span lang='en'>username</span> deve avere tra 5 e 50 caratteri e contenere solo caratteri alfanumerici";
        $usernameValid = "aria-invalid='true'";
        $errorStatus = true;
    }
    
    if(isset($_POST["password"]) && strlen($_POST["password"]) >= 8) {
        $passwordValue = $_POST["password"];
    } else {
        $passwordStatus = "shownError";
        $passwordError = "La <span lang='en'>password</span> deve contenere almeno 8 caratteri";
        $passwordValid = "aria-invalid='true'";
        $errorStatus = true;
    }

    try {
        $connection = new DBAccess();
        if($connection->openDBconnection()) {
            if($usernameStatus != "shownError") {
                $usernameExists = $connection->checkUsernameExists($usernameValue);
                if($usernameExists) {
                    $usernameStatus = "shownError";
                    $usernameError = "L'<span lang='en'>username</span> inserito è già in uso";
                    $usernameValid = "aria-invalid='true'";
                    $errorStatus = true;
                }
            }
            if($emailStatus != "shownError") {
                $emailExists = $connection->checkEmailExists($emailValue);
                if($emailExists) {
                    $emailStatus = "shownError";
                    $emailError = "Esiste già un account associato a questa email";
                    $emailValid = "aria-invalid='true'";
                    $errorStatus = true;
                }
            }
            $connection->closeConnection();
        } else {
            $serverError = true;
            $errorStatus = true;
        }
    } catch (Exception $e) {
        $serverError = true;
        $errorStatus = true;
    }
    

    

    if($serverError) {
        $topError = '<p class="error" role="alert">I nostri sistemi stanno avendo problemi, riprova più tardi</p>';
    } else if ($errorStatus) {
        $topError = '<p class="error" role="alert">Errori nei campi inseriti, si prega di ricontrollare</p>';
    } else {
        $passwordHash = password_hash($passwordValue, PASSWORD_DEFAULT);
        try {
            $connection = new DBAccess();
            if($connection->openDBconnection()) {
                $registerStatus = $connection->registerUser($nomeValue, $cognomeValue, $dataValue, $emailValue, $usernameValue, $passwordHash);
                $connection->closeConnection();
                if($registerStatus) {
                    $_SESSION["username"] = $usernameValue;
                    header("Location: AreaPersonale.php");
                    exit;
                }
            } else {
                $topError = '<p class="error" role="alert">I nostri sistemi stanno avendo problemi, riprova più tardi</p>';
            }
        } catch (Exception $e) {
            $topError = '<p class="error" role="alert">I nostri sistemi stanno avendo problemi, riprova più tardi</p>';
        }
    }
}


//testato per xxs con "><script>alert("ciao")</script><"
//testato per sql injection con Pippo') DROP TABLE Utente;-- 

$paginaLogin = str_replace("[MAX_DATA]", $maxData, $paginaLogin);

$paginaLogin = str_replace("[TOP_ERROR]", $topError, $paginaLogin);

$paginaLogin = str_replace("[NOME_STATUS]", $nomeStatus, $paginaLogin);
$paginaLogin = str_replace("[NOME_ERROR]", $nomeError, $paginaLogin);
$paginaLogin = str_replace("[NOME_VALUE]", $nomeValue, $paginaLogin);
$paginaLogin = str_replace("[NOME_VALID]", $nomeValid, $paginaLogin);

$paginaLogin = str_replace("[COGNOME_STATUS]", $cognomeStatus, $paginaLogin);
$paginaLogin = str_replace("[COGNOME_ERROR]", $cognomeError, $paginaLogin);
$paginaLogin = str_replace("[COGNOME_VALUE]", $cognomeValue, $paginaLogin);
$paginaLogin = str_replace("[COGNOME_VALID]", $cognomeValid, $paginaLogin);

$paginaLogin = str_replace("[EMAIL_STATUS]", $emailStatus, $paginaLogin);
$paginaLogin = str_replace("[EMAIL_ERROR]", $emailError, $paginaLogin);
$paginaLogin = str_replace("[EMAIL_VALUE]", $emailValue, $paginaLogin);
$paginaLogin = str_replace("[EMAIL_VALID]", $emailValid, $paginaLogin);

$paginaLogin = str_replace("[DATA_STATUS]", $dataStatus, $paginaLogin);
$paginaLogin = str_replace("[DATA_ERROR]", $dataError, $paginaLogin);
$paginaLogin = str_replace("[DATA_VALUE]", $dataValue, $paginaLogin);
$paginaLogin = str_replace("[DATA_VALID]", $dataValid, $paginaLogin);

$paginaLogin = str_replace("[USERNAME_STATUS]", $usernameStatus, $paginaLogin);
$paginaLogin = str_replace("[USERNAME_ERROR]", $usernameError, $paginaLogin);
$paginaLogin = str_replace("[USERNAME_VALUE]", $usernameValue, $paginaLogin);
$paginaLogin = str_replace("[USERNAME_VALID]", $usernameValid, $paginaLogin);

$paginaLogin = str_replace("[PASSWORD_STATUS]", $passwordStatus, $paginaLogin);
$paginaLogin = str_replace("[PASSWORD_ERROR]", $passwordError, $paginaLogin);
//valore password non viene reinserito per motivi di sicurezza
$paginaLogin = str_replace("[PASSWORD_VALID]", $passwordValid, $paginaLogin);



echo $paginaLogin;



?>