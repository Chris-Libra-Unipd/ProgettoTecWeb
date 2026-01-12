<?php

session_start();
$paginaHTML = file_get_contents("login.html");
require_once "php/dbConnection.php";
use DB\DBAccess;
$connection = new DBAccess();

$errore = "";


if(isset($_SESSION['username'])) {
    header("Location: AreaPersonale.php");
    exit();
}

if(isset($_POST['username']) && isset($_POST['password'])) {
    if(!empty($_POST['username']) && !empty($_POST['password'])) {
        $username = trim($_POST['username']);
        $password = $_POST['password'];
        try {
            if($connection->openDBConnection()) {
                if($connection->login($username, $password)) {
                    $_SESSION['username'] = $username;
                    header("Location: AreaPersonale.php");
                    exit();
                } else {
                    $errore = "<p class=\"error\" role=\"alert\">Credenziali errate</p>";
                }
            } else {
                $errore = "<p class=\"error\" role=\"alert\">I nostri sistemi stanno avendo problemi, riprova più tardi</p>";
            }
        } catch (Exception $e) {
            $errore = "<p class=\"error\" role=\"alert\">I nostri sistemi stanno avendo problemi, riprova più tardi</p>";
        }
    } else {
        $errore = "<p class=\"error\" role=\"alert\">inserire tutti i campi</p>";
    }
}


$paginaHTML = str_replace("[ERRORE_ACCESSO]", $errore, $paginaHTML);

echo $paginaHTML;
?>