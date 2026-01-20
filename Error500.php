<?php
    session_start();

    $paginaHTML = file_get_contents("Error500.html");

    $footerLink="";
    if(isset($_SESSION['username']))
    $footerLink = "<li><a href='AreaPersonale.php' class='footer-link'>Area Personale</a></li>";
    else
    $footerLink = "<li><a href='login.php' class='footer-link'>Accedi</a></li>";
    $paginaHTML = str_replace("[FOOTER_LINK]", $footerLink, $paginaHTML);

    echo $paginaHTML;
?>