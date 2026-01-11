<?php


require_once "dbConnection.php";
use DB\DBAccess;

$connection = new DBAccess();

$paginaLogin = file_get_contents("login.html");

session_start();

if(isset($_SESSION['username'])) {
    header("Location: AreaPersonale.html");
    exit();
}


function pulisciInput($value){
    // elimina gli spazi
    $value = trim($value);
    // rimuove tag html
    $value = strip_tags($value);
    // converte i caratteri speciali in entità html
    $value = htmlentities($value);
    return $value;
}


if(isset($_POST['submit'])) {
}

echo $paginaLogin;
?>