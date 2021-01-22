<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <script src="prefix.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <title>PRO-G - Mes Projets</title>
</head>

<body>


    <div id="divTitre">
        <h1><a href="utilisateur.html">PRO-G</a></h1>
    </div>
    
    
    <div id="divHautDroite">
        <h1 class="animate__animated animate__fadeInDown">Mes Projets</h1>
    </div>


    <div id="divGauche">
        <span><a href="utilisateur.html">Mon Espace</a></span>
        <span><a href="projet.html">Mes Projets</a></span>
        <span class="ligne"></span>
        <span>
          <form action="">
            <label for="recherche">Recherche</label>
            <input type="text" id="recherche" name="recherche">
            <input type="submit" value="Rechercher">
          </form>
        </span>
        <span class="ligne"></span>
        <span><a href="logout.php">Déconnexion</a></span>
      </div>
    
    
    <main>
        
    </main>

    <!-- CachésCachésCachésCachésCachésCachésCachés -->

    <div id="btnMenuPouce"><span>M</span></div>

    <div id="rechercheMobile">
      <form action="">
        <label for="recherche">Recherche</label>
        <input type="text" id="recherche" name="recherche">
        <input type="submit" value="Rechercher">
      </form>
    </div>

    
    <div id="menuPouce">
      <span><a href="utilisateur.html">Mon Espace</a></span>
      <span><a href="projet.html">Mes Projets</a></span>
      <span id="recherchePouce">Recherche</span>
      <span class="ligne"></span>
      <span><a href="logout.php">Déconnexion</a></span>
    </div>
    <!-- CachésCachésCachésCachésCachésCachésCachés -->
    <script src="menu.js"></script>
</body>
</html>