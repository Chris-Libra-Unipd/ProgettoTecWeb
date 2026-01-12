<?php
session_start();
$paginaAreapersonale = file_get_contents("AreaPersonale.html");
require_once "php/utils.php";

if(!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
} else {
    $paginaAreapersonale = setta_link_area_personale($paginaAreapersonale);
}

echo $paginaAreapersonale;

?>