// var menu_btn = document.querySelector("#menu-btn");
// var sidebar = document.querySelector("#sidebar");
// var container = document.querySelector(".my-container");
// menu_btn.addEventListener("click", () => {
//     sidebar.classList.toggle("active-nav");
//     container.classList.toggle("active-cont");
// });

document.addEventListener("DOMContentLoaded", function() {


    var menu_btn = document.querySelector("#menu-btn");


    if (menu_btn) {
        var sidebar = document.querySelector("#sidebar");
        var container = document.querySelector(".my-container");

        menu_btn.addEventListener("click", function() {
            sidebar.classList.toggle("active-nav");
            container.classList.toggle("active-cont");
        });
    } else {
        console.error("Le bouton de menu n'a pas été trouvé dans le DOM.");
    }
});
