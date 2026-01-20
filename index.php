<?php

session_start();
require_once "php/utils.php";

$paginaHome = file_get_contents("index.html");


if(isset($_SESSION['username'])) {
    $paginaHome = setta_link_area_personale($paginaHome);
} else {
    $paginaHome = setta_link_login($paginaHome);
}

$footerLink="";
if(isset($_SESSION['username']))
    $footerLink = "<li><a href='AreaPersonale.php' class='footer-link'>Area Personale</a></li>";
else
    $footerLink = "<li><a href='login.php' class='footer-link'>Accedi</a></li>";
$paginaHome = str_replace("[FOOTER_LINK]", $footerLink, $paginaHome);

echo $paginaHome;



?>