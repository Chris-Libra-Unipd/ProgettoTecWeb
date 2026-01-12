<?php
    function setta_link_area_personale(&$pagina) {
        $linkAreaPersonaleDesktop = 
        '<a href="AreaPersonale.php" id="area-personale-link">
            <div>
                <img src="assets/img/astronauta.svg" alt="">
            </div>
        </a>';
        $linkAreaPersonaleMobile = 
        '<a href="AreaPersonale.php" id="area-personale-link-mobile">
            <div>
                <img src="assets/img/astronauta.svg" alt="">
            </div>
        </a>';
        $pagina = str_replace("[ACCESSO_LOGIN_DESKTOP]", $linkAreaPersonaleDesktop, $pagina);
        $pagina = str_replace("[ACCESSO_LOGIN_MOBILE]", $linkAreaPersonaleMobile, $pagina);
        return $pagina;
    }

    function setta_link_login(&$pagina) {
        $linkLoginDesktop = '<a href="login.php" id="login-link">ACCEDI</a>';
        $linkLoginMobile = '<a href="login.php" id="login-link-mobile">ACCEDI</a>';
        $pagina = str_replace("[ACCESSO_LOGIN_DESKTOP]", $linkLoginDesktop, $pagina);
        $pagina = str_replace("[ACCESSO_LOGIN_MOBILE]", $linkLoginMobile, $pagina);
        return $pagina;
    }
?>