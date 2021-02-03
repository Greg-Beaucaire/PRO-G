window.onload = changeEchelleTempsGeneral;

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


function changeEchelleTempsGeneral(){
    let btnEchelleTempsMoisElement = document.getElementById('btnEchelleTempsMois');
    let btnEchelleTempsSemainesElement = document.getElementById('btnEchelleTempsSemaines');

    let divTableMois = document.getElementById('tableMois');
    let divTableSemaines = document.getElementById('tableSemaines');

    function changeEchelleTempsMois() {

        divTableSemaines.style.display = 'none';
        divTableMois.style.display = 'block';       
    }

    function changeEchelleTempsSemaines(){
        divTableMois.style.display = 'none';
        divTableSemaines.style.display = 'block';
    }

    btnEchelleTempsMoisElement.addEventListener("click", changeEchelleTempsMois);
    btnEchelleTempsSemainesElement.addEventListener("click", changeEchelleTempsSemaines);
}

let mainDiv = document.getElementsByTagName('main');

$(mainDiv).scroll(function() {
    sessionStorage.scrollTop = $(this).scrollTop();
});
  
$(document).ready(function() {
if (sessionStorage.scrollTop != "undefined") {
    $(mainDiv).scrollTop(sessionStorage.scrollTop);
}
});
