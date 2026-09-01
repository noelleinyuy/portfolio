/*
===============================================================================
MOBILE NAVIGATION
===============================================================================
*/

const menu = document.querySelector("#menu-icon");
const navbar = document.querySelector(".navbar");

function setMenuIcon(isOpen) {
    if (!menu) return;
    menu.className = isOpen ? "bx bx-x" : "bx bx-menu";
}

if (menu && navbar) {

    menu.addEventListener("click", () => {
        const isOpen = navbar.classList.toggle("active");
        setMenuIcon(isOpen);
    });

    window.addEventListener("scroll", () => {
        navbar.classList.remove("active");
        setMenuIcon(false);
    });

}


/*
===============================================================================
TYPED TEXT ANIMATION
===============================================================================
*/

const typedElement = document.querySelector(".multiple-text");

if (typedElement && typeof Typed !== "undefined") {

    new Typed(".multiple-text", {
        strings: [
            "Frontend Developer",
            "Backend Developer",
            "Blockchain Developer",
            "Web Designer",
            "Youtuber"
        ],
        typeSpeed: 80,
        backSpeed: 80,
        backDelay: 1200,
        loop: true
    });

}


/*
===============================================================================
THEME SWITCHER
===============================================================================
The selected theme is saved in localStorage so it remains active after
refreshing or reopening the website.
===============================================================================
*/

const themeToggle = document.querySelector("#theme-toggle");
const root = document.documentElement;

function updateThemeButton() {

    if (!themeToggle) return;

    const icon = themeToggle.querySelector("i");

    if (!icon) return;

    icon.className = document.body.classList.contains("theme-light")
        ? "bx bx-sun"
        : "bx bx-moon";
}

const urlTheme = new URLSearchParams(window.location.search).get("theme");
const savedTheme = urlTheme || localStorage.getItem("portfolioTheme");
const shouldUseLightTheme = savedTheme === "light";

if (urlTheme) {
    localStorage.setItem("portfolioTheme", urlTheme);
}

if (shouldUseLightTheme) {
    document.body.classList.add("theme-light");
} else {
    document.body.classList.remove("theme-light");
}

root.setAttribute("data-theme", shouldUseLightTheme ? "light" : "dark");

// The inline head script added this class to <html> to avoid a flash of the
// wrong theme before the CSS/JS load. Once the real theme class is applied
// to <body>, this loading class is no longer needed.
document.documentElement.classList.remove("theme-light-loading");

updateThemeButton();

if (themeToggle) {

    themeToggle.addEventListener("click", () => {

        const isLightTheme = document.body.classList.toggle("theme-light");
        const nextTheme = isLightTheme ? "light" : "dark";

        localStorage.setItem("portfolioTheme", nextTheme);
        root.setAttribute("data-theme", nextTheme);

        updateThemeButton();

    });

}
