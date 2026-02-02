<?php
session_start();

$paginaHTML = file_get_contents("Error404.html");
require_once "php/utils.php";


if(isset($_SESSION['username'])) {
    $paginaHTML = setta_link_area_personale($paginaHTML);
} else {
    $paginaHTML = setta_link_login($paginaHTML);
}


$footerLink="";
if(isset($_SESSION['username']))
    $footerLink = "<li><a href='AreaPersonale.php' class='footer-link'>Area Personale</a></li>";
else
    $footerLink = "<li><a href='login.php' class='footer-link'>Accedi</a></li>";
$paginaHTML = str_replace("[FOOTER_LINK]", $footerLink, $paginaHTML);

echo $paginaHTML;

?>