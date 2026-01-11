<?php


require_once "dbConnection.php";
use DB\DBAccess;

$connection = new DBAccess();

$paginaLogin = file_get_contents("login.html");

session_start();

if(isset($_SESSION['username'])) {
    header("Location: AreaPersonale.php");
    exit();
}


echo $paginaLogin;
?>