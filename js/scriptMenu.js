const headerCollected = document.getElementsByTagName("header");
const header = headerCollected[0];
const headerContainer = document.getElementById("header-container");


const menuMobile = document.getElementById("menu-mobile");
const buttonMenu = document.getElementById("button-menu-mobile");
const imageButtonMenu = document.getElementById("image-button-menu-mobile")

const buttunTornaSu = document.getElementById("button-torna-su");

menuMobile.classList.remove("menu-mobile-aperto-alta-spec");
menuMobile.classList.add("menu-mobile-chiuso-alta-spec");
let isMenuOpen = false;


function scrollMenuEffect() {
    if(window.scrollY >= 1) {
        header.classList.remove("header-no-scroll");
        header.classList.add("header-scroll");
        headerContainer.classList.remove("header-container-no-scroll");
        headerContainer.classList.add("header-container-scroll");
    } else {
        header.classList.remove("header-scroll");
        header.classList.add("header-no-scroll");
        headerContainer.classList.remove("header-container-scroll");
        headerContainer.classList.add("header-container-no-scroll");
    }
}

function openCloseMenuMobile() {
    if(isMenuOpen) {
        isMenuOpen = false;
        buttonMenu.setAttribute("aria-label", "apri menu mobile");
        menuMobile.classList.remove("menu-mobile-aperto-alta-spec")
        menuMobile.classList.add("menu-mobile-chiuso-alta-spec")
        imageButtonMenu.classList.remove("image-button-menu-mobile-aperto")
        imageButtonMenu.classList.add("image-button-menu-mobile-chiuso")
    } else {
        isMenuOpen = true;
        buttonMenu.setAttribute("aria-label", "chiudi menu mobile");
        menuMobile.classList.remove("menu-mobile-chiuso-alta-spec");
        menuMobile.classList.add("menu-mobile-aperto-alta-spec");
        imageButtonMenu.classList.remove("image-button-menu-mobile-chiuso");
        imageButtonMenu.classList.add("image-button-menu-mobile-aperto");
    }
}

function scrollToTop() {
    window.scrollTo(0,0);
}

window.addEventListener("scroll", scrollMenuEffect);
buttonMenu.addEventListener("click", openCloseMenuMobile);
buttunTornaSu.addEventListener("click", scrollToTop);