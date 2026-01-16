<?php
session_start();
require_once "php/utils.php";

$paginaChiSiamo = file_get_contents("chi_siamo.html");

if(isset($_SESSION['username'])) {
    $paginaChiSiamo = setta_link_area_personale($paginaChiSiamo);
} else {
    $paginaChiSiamo = setta_link_login($paginaChiSiamo);
}

echo $paginaChiSiamo;

?>