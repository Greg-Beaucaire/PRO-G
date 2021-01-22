<!-- PHP / Check User TYPE -->
<?php
  // Initialiser la session
  session_start();
  // Vérifiez si l'utilisateur est connecté, sinon redirigez-le vers la page de connexion
  if($_SESSION["ut_type"] != "admin"){
    header("Location: ../index.php");
    exit(); 
  }
?>

<!-- CORPS DU SITE -->
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <script src="../prefix.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <title>PRO-G - Espace Perso</title>
</head>

<body>


    <div id="divTitre">
        <h1><a href="utilisateur.php">PRO-G</a></h1>
    </div>
    
    
    <div id="divHautDroite">
        <h1 class="animate__animated animate__fadeInDown">Espace Personnel</h1>
    </div>


    <div id="divGauche">
        <span><a href="utilisateur.php">Mon Espace</a></span>
        <span><a href="projet.php">Mes Projets</a></span>
        <span class="ligne"></span>
        <span>
          <form action="projet.php" method="POST">
              <label for="cherche">Recherche</label>
              <input type="text" id="recherche" name="cherche">
              <input type="submit" value="Rechercher">
          </form>
        </span>
        <span class="ligne"></span>
        <span><a href="../logout.php">Déconnexion</a></span>
    </div>

    
    
    <main id="mainUser">
        <div class="containerForm">
          <h1 class="box-logo box-title">
                  Ajouter un utilisateur
          </h1>
          <form class="box" action="" method="post">
              <input type="text" class="box-input" name="ut_nom" placeholder="Nom d'utilisateur" required />
              <input type="text" class="box-input" name="ut_email" placeholder="Email" required />
              <input type="password" class="box-input" name="ut_mdp" placeholder="Mot de passe" required />
              <div>
                  <select class="box-input" name="ut_type" id="ut_type" >
                      <option value="" disabled selected>Type</option>
                      <option value="admin">Gestionnaire</option>
                      <option value="user">Acteur</option>
                  </select>
              </div>
              <input type="submit" name="submit" value="Ajouter" class="box-button" />
          </form>
          <p>L'utilisateur a été créé avec succés.</p>
          <?php
            require('../config.php');
            if (isset($_REQUEST['ut_nom'], $_REQUEST['ut_email'], $_REQUEST['ut_type'], $_REQUEST['ut_mdp'])){
                // récupérer le nom d'utilisateur 
                $ut_nom = htmlentities($_REQUEST['ut_nom'],ENT_QUOTES);
                // récupérer l'email 
                $ut_email = htmlentities($_REQUEST['ut_email'],ENT_QUOTES);
                // récupérer le mot de passe 
                $ut_mdp = htmlentities($_REQUEST['ut_mdp'],ENT_QUOTES);
                // récupérer le type (user | admin)
                $ut_type = htmlentities($_REQUEST['ut_type'],ENT_QUOTES);
                

                try{
                    $pdo = new PDO(DB_DRIVER . ":host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_LOGIN, DB_PASS, DB_OPTIONS);
                    $requete = "INSERT INTO `pg_utilisateurs` (`ut_nom`, `ut_email`, `ut_type`, `ut_mdp`)
                    VALUES (\"$ut_nom\", \"$ut_email\", \"$ut_type\", '".hash('sha256', $ut_mdp)."');";
                    $prepare = $pdo->prepare($requete);
                    $prepare->execute();
                    $res = $prepare->rowCount();

                    if($res == 1){
                        echo "<p>L'utilisateur a été créé avec succés.</p>";
                    }
                }
                catch (PDOException $e){
                    exit("❌🙀❌ OOPS :\n" . $e->getMessage());
                }
            }
          ?>
        </div>

        <div class="containerForm">
          <h1 class="box-title">Ajouter un projet</h1>
          <form class="box" action="" method="post">
                      
              <input type="text" class="box-input" name="prj_nom" placeholder="Nom du projet" required />
              <input type="date" class="box-input" name="prj_date_debut" required />
              <input type="date" class="box-input" name="prj_date_fin" required />

              <input type="submit" name="submit" value="Ajouter" class="box-button" />
          </form>
          <p>Le projet a été créée avec succés.</p>
          <?php
          if (isset($_REQUEST['prj_nom'], $_REQUEST['prj_date_debut'], $_REQUEST['prj_date_fin'])){
              // récupérer le nom du projet 
              $prj_nom = htmlentities($_REQUEST['prj_nom'],ENT_QUOTES);

              // récupérer la date de début
              $prj_date_debut = htmlentities($_REQUEST['prj_date_debut'],ENT_QUOTES);

              // récupérer la date de fin
              $prj_date_fin = htmlentities($_REQUEST['prj_date_fin'],ENT_QUOTES);

              //récupérer l'id de l'utilisateur
              $userId = $_SESSION["ut_id"];
          
              try{
                  $pdo = new PDO(DB_DRIVER . ":host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_LOGIN, DB_PASS, DB_OPTIONS);
                  $requete = "INSERT INTO `pg_projets` (`prj_nom`, `prj_date_debut`, `prj_date_fin`)
                  VALUES ('$prj_nom', '$prj_date_debut', '$prj_date_fin');";
                  $prepare = $pdo->prepare($requete);
                  $prepare->execute();
                  $res = $prepare->rowCount();
                  $lastInsertedProjectId = $pdo->lastInsertId();

                  if($res == 1){
                      echo "<p>Le projet a été créée avec succés.</p>";
                      $requete = "INSERT INTO `pg_asso_taches_utilisateurs` (`asso_tu_tache_id`, `asso_tu_utilisateur_id`)
                        VALUES ('1', '$userId');";
                        $prepare = $pdo->prepare($requete);
                        $prepare->execute();
                        $lastInsertedTUId = $pdo->lastInsertId();
                      $requete = "INSERT INTO `pg_asso_projets_taches` (`asso_pt_projet_id`, `asso_pt_tache_id`)
                        VALUES ('$lastInsertedProjectId', '1');";
                        $prepare = $pdo->prepare($requete);
                        $prepare->execute();
                        $lastInsertedPTId = $pdo->lastInsertId();
                      $requete = "INSERT INTO `pg_asso_projets_taches_utilisateurs` (`asso_ptu_pt_id`, `asso_ptu_tu_id`)
                        VALUES ('$lastInsertedPTId', '$lastInsertedTUId');";
                        $prepare = $pdo->prepare($requete);
                        $prepare->execute();
                  }
              }
              catch (PDOException $e){
                  exit("❌🙀❌ OOPS :\n" . $e->getMessage());
              }
          }
          ?>
        </div>
        <!-- DIV TEST MISE EN PAGE -->
        <div class="containerForm">
          <h1 class="box-logo box-title">
                  Ajouter un utilisateur
          </h1>
          <form class="box" action="" method="post">
              <input type="text" class="box-input" name="ut_nom" placeholder="Nom d'utilisateur" required />
              <input type="text" class="box-input" name="ut_email" placeholder="Email" required />
              <input type="password" class="box-input" name="ut_mdp" placeholder="Mot de passe" required />
              <div>
                  <select class="box-input" name="ut_type" id="ut_type" >
                      <option value="" disabled selected>Type</option>
                      <option value="admin">Gestionnaire</option>
                      <option value="user">Acteur</option>
                  </select>
              </div>
              <input type="submit" name="submit" value="Ajouter" class="box-button" />
          </form>
          <p>L'utilisateur a été créé avec succés.</p>
        </div>


        <div class="containerForm">
          <h1 class="box-logo box-title">
                  Ajouter un utilisateur
          </h1>
          <form class="box" action="" method="post">
              <input type="text" class="box-input" name="ut_nom" placeholder="Nom d'utilisateur" required />
              <input type="text" class="box-input" name="ut_email" placeholder="Email" required />
              <input type="password" class="box-input" name="ut_mdp" placeholder="Mot de passe" required />
              <div>
                  <select class="box-input" name="ut_type" id="ut_type" >
                      <option value="" disabled selected>Type</option>
                      <option value="admin">Gestionnaire</option>
                      <option value="user">Acteur</option>
                  </select>
              </div>
              <input type="submit" name="submit" value="Ajouter" class="box-button" />
          </form>
          <p>L'utilisateur a été créé avec succés.</p>
        </div>


        <div class="containerForm">
          <h1 class="box-logo box-title">
                  Ajouter un utilisateur
          </h1>
          <form class="box" action="" method="post">
              <input type="text" class="box-input" name="ut_nom" placeholder="Nom d'utilisateur" required />
              <input type="text" class="box-input" name="ut_email" placeholder="Email" required />
              <input type="password" class="box-input" name="ut_mdp" placeholder="Mot de passe" required />
              <div>
                  <select class="box-input" name="ut_type" id="ut_type" >
                      <option value="" disabled selected>Type</option>
                      <option value="admin">Gestionnaire</option>
                      <option value="user">Acteur</option>
                  </select>
              </div>
              <input type="submit" name="submit" value="Ajouter" class="box-button" />
          </form>
          <p>L'utilisateur a été créé avec succés.</p>
        </div>


        <div class="containerForm">
          <h1 class="box-logo box-title">
                  Ajouter un utilisateur
          </h1>
          <form class="box" action="" method="post">
              <input type="text" class="box-input" name="ut_nom" placeholder="Nom d'utilisateur" required />
              <input type="text" class="box-input" name="ut_email" placeholder="Email" required />
              <input type="password" class="box-input" name="ut_mdp" placeholder="Mot de passe" required />
              <div>
                  <select class="box-input" name="ut_type" id="ut_type" >
                      <option value="" disabled selected>Type</option>
                      <option value="admin">Gestionnaire</option>
                      <option value="user">Acteur</option>
                  </select>
              </div>
              <input type="submit" name="submit" value="Ajouter" class="box-button" />
          </form>
          <p>L'utilisateur a été créé avec succés.</p>
        </div>


        <div class="containerForm">
          <h1 class="box-logo box-title">
                  Ajouter un utilisateur
          </h1>
          <form class="box" action="" method="post">
              <input type="text" class="box-input" name="ut_nom" placeholder="Nom d'utilisateur" required />
              <input type="text" class="box-input" name="ut_email" placeholder="Email" required />
              <input type="password" class="box-input" name="ut_mdp" placeholder="Mot de passe" required />
              <div>
                  <select class="box-input" name="ut_type" id="ut_type" >
                      <option value="" disabled selected>Type</option>
                      <option value="admin">Gestionnaire</option>
                      <option value="user">Acteur</option>
                  </select>
              </div>
              <input type="submit" name="submit" value="Ajouter" class="box-button" />
          </form>
          <p>L'utilisateur a été créé avec succés.</p>
        </div>


        <div class="containerForm">
          <h1 class="box-logo box-title">
                  Ajouter un utilisateur
          </h1>
          <form class="box" action="" method="post">
              <input type="text" class="box-input" name="ut_nom" placeholder="Nom d'utilisateur" required />
              <input type="text" class="box-input" name="ut_email" placeholder="Email" required />
              <input type="password" class="box-input" name="ut_mdp" placeholder="Mot de passe" required />
              <div>
                  <select class="box-input" name="ut_type" id="ut_type" >
                      <option value="" disabled selected>Type</option>
                      <option value="admin">Gestionnaire</option>
                      <option value="user">Acteur</option>
                  </select>
              </div>
              <input type="submit" name="submit" value="Ajouter" class="box-button" />
          </form>
          <p>L'utilisateur a été créé avec succés.</p>
        </div>
    </main>

    <!-- CachésCachésCachésCachésCachésCachésCachés -->

    <div id="btnMenuPouce"><span>M</span></div>

    <div id="rechercheMobile">
      <form action="">
        <label for="recherche">Recherche</label>
        <input type="text" id="recherche" name="recherche">
        <label for="recherchepar">par titre ou catégorie de projet</label>
        <select>
          <option value="titre">Titre</option>
          <option value="categorie">Catégorie</option>
        </select>
        <input type="submit" value="Rechercher">
      </form>
    </div>

    
    <div id="menuPouce">
      <span><a href="utilisateur.php">Mon Espace</a></span>
      <span><a href="projet.php">Mes Projets</a></span>
      <span id="recherchePouce">Recherche</span>
      <span class="ligne"></span>
      <span><a href="../logout.php">Déconnexion</a></span>
    </div>
    <!-- CachésCachésCachésCachésCachésCachésCachés -->
    <script src="../menu.js"></script>
</body>
</html>