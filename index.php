<?php

session_start();
require_once "php/utils.php";

$paginaLogin = file_get_contents("index.html");


if(isset($_SESSION['username'])) {
    $paginaLogin = setta_link_area_personale($paginaLogin);
} else {
    $paginaLogin = setta_link_login($paginaLogin);
}


echo $paginaLogin;



?>