<?php
session_start();
require_once "php/utils.php";

$paginaChiSiamo = file_get_contents("chi_siamo.html");

if(isset($_SESSION['username'])) {
    $paginaChiSiamo = setta_link_area_personale($paginaChiSiamo);
} else {
    $paginaChiSiamo = setta_link_login($paginaChiSiamo);
}

$footerLink="";
if(isset($_SESSION['username']))
    $footerLink = "<li><a href='AreaPersonale.php' class='footer-link'>Area Personale</a></li>";
else
    $footerLink = "<li><a href='login.php' class='footer-link'>Accedi</a></li>";
$paginaChiSiamo = str_replace("[FOOTER_LINK]", $footerLink, $paginaChiSiamo);

echo $paginaChiSiamo;

?>