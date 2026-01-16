<?php

session_start();
require_once "php/utils.php";

$paginaHome = file_get_contents("index.html");


if(isset($_SESSION['username'])) {
    $paginaHome = setta_link_area_personale($paginaHome);
} else {
    $paginaHome = setta_link_login($paginaHome);
}


echo $paginaHome;



?>