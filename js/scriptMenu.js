const headerCollected = document.getElementsByTagName("header");
const header = headerCollected[0];
const headerContainer = document.getElementById("header-container");


const menuMobile = document.getElementById("menu-mobile");
const buttonMenu = document.getElementById("button-menu-mobile");
const imageButtonMenu = document.getElementById("image-button-menu-mobile")


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
    if(!isMenuOpen) {
        isMenuOpen = true;
        menuMobile.style.display = "flex";
        imageButtonMenu.style.transform = "rotate(180deg)";
    } else {
        isMenuOpen = false;
        menuMobile.style.display = "none";
        imageButtonMenu.style.transform = "rotate(0deg)"
    }
}



window.addEventListener("scroll", scrollMenuEffect);
buttonMenu.addEventListener("click", openCloseMenuMobile);
