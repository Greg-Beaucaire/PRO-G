
if ( $(window).width() < 768) {     
    //Desktop
    let btnMenuPouceEl = document.getElementById("btnMenuPouce");

    function menuPouceFunc(){
        let menuPouceEl = document.getElementById("menuPouce");
        menuPouceEl.style.display = "flex";
        btnMenuPouceEl.style.display = "none";
    }

    btnMenuPouceEl.addEventListener('click', menuPouceFunc);

    let recherchePouceEl = document.getElementById("recherchePouce");

    function recherchePouceFunc(){
        let menuPouceEl = document.getElementById("menuPouce");
        let rechercheMobileEl = document.getElementById("rechercheMobile");
        menuPouceEl.style.display = "none";
        rechercheMobileEl.style.display = "flex";
    }

    recherchePouceEl.addEventListener("click", recherchePouceFunc);



    let mainEl = document.querySelector('main');
    console.log(mainEl)
    let divHautDroiteEl = document.querySelector('#divHautDroite');
    console.log(divHautDroiteEl);

    
    function hideMenuPouce(){
        let menuPouceEl = document.getElementById("menuPouce");
        let rechercheMobileEl = document.getElementById("rechercheMobile");
        menuPouceEl.style.display = "none";
        rechercheMobileEl.style.display = "none";
        btnMenuPouceEl.style.display = "flex";
    }

    mainEl.addEventListener("click", hideMenuPouce);
    divHautDroiteEl.addEventListener("click", hideMenuPouce);
}


