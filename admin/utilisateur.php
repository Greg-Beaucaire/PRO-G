<!-- PHP / Check User TYPE -->
<?php
// Initialiser la session
session_start();
// Vérifiez si l'utilisateur est connecté, sinon redirigez-le vers la page de connexion
if ($_SESSION["ut_type"] != "admin") {
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
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
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
          <select class="box-input" name="ut_type" id="ut_type">
            <option value="" disabled selected>Type</option>
            <option value="admin">Gestionnaire</option>
            <option value="user">Acteur</option>
          </select>
        </div>
        <input type="submit" name="submit" value="Ajouter" class="box-button" />
      </form>
      <?php
      require('../config.php');
      if (isset($_REQUEST['ut_nom'], $_REQUEST['ut_email'], $_REQUEST['ut_type'], $_REQUEST['ut_mdp'])) {
        // récupérer le nom d'utilisateur 
        $ut_nom = htmlentities($_REQUEST['ut_nom'], ENT_QUOTES);
        // récupérer l'email 
        $ut_email = htmlentities($_REQUEST['ut_email'], ENT_QUOTES);
        // récupérer le mot de passe 
        $ut_mdp = htmlentities($_REQUEST['ut_mdp'], ENT_QUOTES);
        // récupérer le type (user | admin)
        $ut_type = htmlentities($_REQUEST['ut_type'], ENT_QUOTES);


        try {
          $pdo = new PDO(DB_DRIVER . ":host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_LOGIN, DB_PASS, DB_OPTIONS);
          $requete = "INSERT INTO `pg_utilisateurs` (`ut_nom`, `ut_email`, `ut_type`, `ut_mdp`)
                    VALUES (\"$ut_nom\", \"$ut_email\", \"$ut_type\", '" . hash('sha256', $ut_mdp) . "');";
          $prepare = $pdo->prepare($requete);
          $prepare->execute();
          $res = $prepare->rowCount();

          if ($res == 1) {
            echo "<p>L'utilisateur a été créé avec succés.</p>";
          }
        } catch (PDOException $e) {
          exit("❌🙀❌ OOPS :\n" . $e->getMessage());
        }
      }
      ?>
    </div>
  
    <div class="containerForm">
      <h1 class="box-title">Ajouter un projet</h1>
      <form class="box" action="" method="post">

        <input type="text" class="box-input" name="prj_nom" placeholder="Nom du projet" required />
        <label for="prj_date_debut">Début du projet</label>
        <input type="date" class="box-input" name="prj_date_debut" required />
        <label for="prj_date_fin">Fin du projet</label>
        <input type="date" class="box-input" name="prj_date_fin" required />

        <input type="submit" name="submit" value="Ajouter" class="box-button" />
      </form>
      <?php
      if (isset($_REQUEST['prj_nom'], $_REQUEST['prj_date_debut'], $_REQUEST['prj_date_fin'])) {
        // récupérer le nom du projet 
        $prj_nom = htmlentities($_REQUEST['prj_nom'], ENT_QUOTES);

        // récupérer la date de début
        $prj_date_debut = htmlentities($_REQUEST['prj_date_debut'], ENT_QUOTES);

        // récupérer la date de fin
        $prj_date_fin = htmlentities($_REQUEST['prj_date_fin'], ENT_QUOTES);

        //récupérer l'id de l'utilisateur
        $userId = $_SESSION["ut_id"];

        try {
          $pdo = new PDO(DB_DRIVER . ":host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_LOGIN, DB_PASS, DB_OPTIONS);
          $requete = "INSERT INTO `pg_projets` (`prj_nom`, `prj_date_debut`, `prj_date_fin`)
                  VALUES ('$prj_nom', '$prj_date_debut', '$prj_date_fin');";
          $prepare = $pdo->prepare($requete);
          $prepare->execute();
          $res = $prepare->rowCount();
          $lastInsertedProjectId = $pdo->lastInsertId();

          if ($res == 1) {
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
        } catch (PDOException $e) {
          exit("❌🙀❌ OOPS :\n" . $e->getMessage());
        }
      }
      ?>
    </div>

    <!-- LIAISON TACHE USER -->
    <div class="containerForm">
      <?php
      try {
        $pdo = new PDO(DB_DRIVER . ":host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_LOGIN, DB_PASS, DB_OPTIONS);
        $userID = $_SESSION['ut_id'];
        $requete = "SELECT * 
            FROM pg_asso_projets_taches_utilisateurs
            JOIN pg_asso_projets_taches ON pg_asso_projets_taches.asso_pt_id = pg_asso_projets_taches_utilisateurs.asso_ptu_pt_id
            JOIN pg_projets ON pg_projets.prj_id = pg_asso_projets_taches.asso_pt_projet_id
            JOIN pg_asso_taches_utilisateurs ON pg_asso_taches_utilisateurs.asso_tu_id = pg_asso_projets_taches_utilisateurs.asso_ptu_tu_id
            JOIN pg_utilisateurs ON pg_utilisateurs.ut_id = pg_asso_taches_utilisateurs.asso_tu_utilisateur_id
            WHERE pg_utilisateurs.ut_id = $userID;";
        $prepare = $pdo->prepare($requete);
        $prepare->execute();
        $resultatProjet = $prepare->fetchAll();
      } catch (PDOException $e) {
        exit("❌🙀❌ OOPS :\n" . $e->getMessage());
      }

      if (isset($_REQUEST["proj"])) {

        $proj = $_REQUEST["proj"];

        try {
          $pdo = new PDO(DB_DRIVER . ":host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_LOGIN, DB_PASS, DB_OPTIONS);
          $requete = "SELECT * FROM `pg_taches`
                INNER JOIN `pg_asso_projets_taches` ON `tch_id` = `asso_pt_tache_id`
                WHERE `asso_pt_projet_id` = :asso_pt_projet_id;";
          $prepare = $pdo->prepare($requete);
          $prepare->execute(array(
            ":asso_pt_projet_id" => $proj
          ));
          $resultatTache = $prepare->fetchAll();

          $requete = "SELECT * FROM `pg_utilisateurs`";
          $prepare = $pdo->prepare($requete);
          $prepare->execute();
          $resultatUtilisateur = $prepare->fetchAll();
        } catch (PDOException $e) {
          exit("❌🙀❌ OOPS :\n" . $e->getMessage());
        }
      ?>
        <h1>Lier un utilisateur à une tâche</h1>
        <form action="utilisateur.php" method="POST">
          <label for="tch">Choisi le nom de la tache</label>
          <select name="tch">
            <?php
            $resultatTacheLength = count($resultatTache);
            for ($i = 0; $i < $resultatTacheLength; $i++) {
              if ($resultatTache[$i]['tch_id'] != 1)
                echo "<option value=" . $resultatTache[$i]['tch_id'] . ">" . $resultatTache[$i]['tch_nom'] . "</option>";
            }
            ?>
          </select>
          <label for="util">Choisi un utilisateur pour cette tache</label>
          <select name="util">
            <?php
            foreach ($resultatUtilisateur as $key => $value) {
              echo "<option value=" . $value['ut_id'] . ">" . $value['ut_nom'] . "</option>";
            }
            ?>
          </select>
          <input type="submit" value="Lier">
        </form>


      <?php
      } elseif (isset($_REQUEST["tch"], $_REQUEST["util"])) {

        $tch = $_REQUEST["tch"];
        $util = $_REQUEST["util"];
        try {
          $requete = "SELECT `asso_pt_id` FROM `pg_asso_projets_taches`
                        INNER JOIN `pg_taches` ON `asso_pt_tache_id` = `tch_id`
                        WHERE `tch_id` = $tch;";
          $prepare = $pdo->prepare($requete);
          $prepare->execute();
          $resultat = $prepare->fetchAll();
          $PTId = $resultat[0]["asso_pt_id"];


          $requete = "INSERT INTO `pg_asso_taches_utilisateurs` (`asso_tu_tache_id`, `asso_tu_utilisateur_id`)
            VALUES ('$tch', '$util');";
          $prepare = $pdo->prepare($requete);
          $prepare->execute();
          $lastInsertedTUId = $pdo->lastInsertId();

          $requete = "INSERT INTO `pg_asso_projets_taches_utilisateurs` (`asso_ptu_pt_id`, `asso_ptu_tu_id`)
            VALUES ('$PTId', '$lastInsertedTUId');";
          $prepare = $pdo->prepare($requete);
          $prepare->execute();
          $res = $prepare->rowCount();

          if ($res == 1) {
            echo("
                <h1>Lier un utilisateur à une tâche</h1>
                <form action='utilisateur.php' method='POST'>
                <label for='proj'>À quel projet cette tâche appartient</label>
                <select name='proj'>
                ");
                $resultatProjetlength = count($resultatProjet);
                for ($i = 0; $i < $resultatProjetlength; $i++) {
                  if ($resultatProjet[$i]['prj_id'] != $resultatProjet[$i + 1]['prj_id'])
                    echo "<option value=" . $resultatProjet[$i]['prj_id'] . ">" . $resultatProjet[$i]['prj_nom'] . "</option>";
                }
                echo("
                </select>
                <input type='submit' value='Choisir'>
                </form>
                <p>La liaison a été réalisée avec succés.</p>
                ");
          }
        } catch (PDOException $e) {
          exit("❌🙀❌ OOPS :\n" . $e->getMessage());
        }
      } else {
      ?>
        <h1>Lier un utilisateur à une tâche</h1>
        <form action="utilisateur.php" method="POST">
          <label for="proj">À quel projet cette tâche appartient</label>
          <select name="proj">
            <?php
            $resultatProjetlength = count($resultatProjet);
            for ($i = 0; $i < $resultatProjetlength; $i++) {
              if ($resultatProjet[$i]['prj_id'] != $resultatProjet[$i + 1]['prj_id'])
                echo "<option value=" . $resultatProjet[$i]['prj_id'] . ">" . $resultatProjet[$i]['prj_nom'] . "</option>";
            }
            ?>
          </select>
          <input type="submit" value="Choisir">
        </form>
      <?php
      }
      ?>
    </div>


    <!-- FLEX BREAK -->
    <div class="flexBreak"></div>



    <!-- AJOUT TACHE -->
    <div class="containerForm grandForm">
      <?php
      try {
        $userID = $_SESSION["ut_id"];
        $pdo = new PDO(DB_DRIVER . ":host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_LOGIN, DB_PASS, DB_OPTIONS);

        $requete = "SELECT * 
                FROM pg_asso_projets_taches_utilisateurs
                JOIN pg_asso_projets_taches ON pg_asso_projets_taches.asso_pt_id = pg_asso_projets_taches_utilisateurs.asso_ptu_pt_id
                JOIN pg_projets ON pg_projets.prj_id = pg_asso_projets_taches.asso_pt_projet_id
                JOIN pg_asso_taches_utilisateurs ON pg_asso_taches_utilisateurs.asso_tu_id = pg_asso_projets_taches_utilisateurs.asso_ptu_tu_id
                JOIN pg_utilisateurs ON pg_utilisateurs.ut_id = pg_asso_taches_utilisateurs.asso_tu_utilisateur_id
                WHERE pg_utilisateurs.ut_id = $userID;";
        $prepare = $pdo->prepare($requete);
        $prepare->execute();
        $resultat = $prepare->fetchAll();

        $requete = "SELECT * FROM `pg_utilisateurs`";
        $prepare = $pdo->prepare($requete);
        $prepare->execute();
        $res = $prepare->fetchAll();
      } catch (PDOException $e) {
        exit("❌🙀❌ OOPS :\n" . $e->getMessage());
      }
      ?>
      <h1 class="box-title">Ajouter une tâche</h1>
      <form action="utilisateur.php" method="POST">
        <div>
          <label for="tch_nom">Nom de la tâche</label>
          <input type="text" name="tch_nom" required>

          <label for="tch_date_debut">Début de la tâche</label>
          <input type="date" name="tch_date_debut" required>

          <label for="tch_date_fin">Fin de la tâche</label>
          <input type="date" name="tch_date_fin" required>

          <label for="tch_durée">Durée (en jour)</label>
          <input type="text" name="tch_duree" required>

          <div class="flexBreakForm"></div>
          <label for="tch_delai">Délai potentiel</label>
          <input type="date" name="tch_delai" required>

          <label for="tch_categorie">Catégorie</label>
          <input type="text" name="tch_categorie" required>

          <label for="util">Utilisateur</label>
          <select name="util">
            <?php
            foreach ($res as $key => $value) {
              echo "<option value=" . $value['ut_id'] . ">" . $value['ut_nom'] . "</option>";
            }
            ?>
          </select>

          <label for="proj">Projet de la tâche</label>
          <select name="proj">
            <?php
            $resultatLength = count($resultat);
            for ($i = 0; $i < $resultatLength; $i++) {
              if ($resultat[$i]['prj_id'] != $resultat[$i + 1]['prj_id'])
                echo "<option value=" . $resultat[$i]['prj_id'] . ">" . $resultat[$i]['prj_nom'] . "</option>";
            }
            ?>
          </select>
        </div>
        <input type="submit" value="Valider">

      </form>
      <?php
      if (isset($_REQUEST["util"], $_REQUEST["proj"], $_REQUEST["tch_nom"], $_REQUEST["tch_date_debut"], $_REQUEST["tch_date_fin"], $_REQUEST["tch_delai"], $_REQUEST["tch_categorie"], $_REQUEST["tch_duree"])) {

        $util = htmlentities($_REQUEST['util'],ENT_QUOTES);
        $proj = htmlentities($_REQUEST['proj'],ENT_QUOTES);
        $tch_nom = htmlentities($_REQUEST['tch_nom'],ENT_QUOTES);
        $tch_date_debut = htmlentities($_REQUEST['tch_date_debut'],ENT_QUOTES);
        $tch_date_fin = htmlentities($_REQUEST['tch_date_fin'],ENT_QUOTES);
        $tch_delai = htmlentities($_REQUEST['tch_delai'],ENT_QUOTES);
        $tch_avancement = 0;
        $tch_duree = htmlentities($_REQUEST['tch_duree'],ENT_QUOTES);
        $tch_categorie = htmlentities($_REQUEST['tch_categorie'],ENT_QUOTES);

        try {
          $pdo = new PDO(DB_DRIVER . ":host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_LOGIN, DB_PASS, DB_OPTIONS);
          $requete = "INSERT INTO `pg_taches` (`tch_nom`, `tch_date_debut`,`tch_date_fin`, `tch_delai`, `tch_avancement`, `tch_duree`, `tch_categorie`)
                            VALUES ('$tch_nom', '$tch_date_debut', '$tch_date_fin', '$tch_delai', '$tch_avancement', '$tch_duree',  '$tch_categorie');";
          $prepare = $pdo->prepare($requete);
          $prepare->execute();
          $lastInsertedTaskId = $pdo->lastInsertId();

          $requete = "INSERT INTO `pg_asso_taches_utilisateurs` (`asso_tu_tache_id`, `asso_tu_utilisateur_id`)
                            VALUES ('$lastInsertedTaskId', '$util');";
          $prepare = $pdo->prepare($requete);
          $prepare->execute();
          $lastInsertedTUId = $pdo->lastInsertId();

          $requete = "INSERT INTO `pg_asso_projets_taches` (`asso_pt_projet_id`, `asso_pt_tache_id`)
                            VALUES ('$proj', '$lastInsertedTaskId');";
          $prepare = $pdo->prepare($requete);
          $prepare->execute();
          $lastInsertedPTId = $pdo->lastInsertId();

          $requete = "INSERT INTO `pg_asso_projets_taches_utilisateurs` (`asso_ptu_pt_id`, `asso_ptu_tu_id`)
                            VALUES ('$lastInsertedPTId', '$lastInsertedTUId');";
          $prepare = $pdo->prepare($requete);
          $prepare->execute();
          $res = $prepare->rowCount();

          if ($res == 1) {
            echo "
                                    <p>La tâche a été créée avec succés.</p>
                                    ";
          }
        } catch (PDOException $e) {
          exit("❌🙀❌ OOPS :\n" . $e->getMessage());
        }
      }
      ?>
    </div>


    <!-- MODIFICATION TACHE MODIFICATION TACHE MODIFICATION TACHE MODIFICATION TACHE MODIFICATION TACHE MODIFICATION TACHE MODIFICATION TACHE MODIFICATION TACHE -->
    <div class="containerForm">
      <h1 class="box-logo box-title">
        Modifier une tâche
      </h1>
      <?php
        try{
            $pdo = new PDO(DB_DRIVER . ":host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_LOGIN, DB_PASS, DB_OPTIONS);
            $userID = $_SESSION['ut_id'];
            $requete = "SELECT * 
                FROM pg_asso_projets_taches_utilisateurs
                JOIN pg_asso_projets_taches ON pg_asso_projets_taches.asso_pt_id = pg_asso_projets_taches_utilisateurs.asso_ptu_pt_id
                JOIN pg_projets ON pg_projets.prj_id = pg_asso_projets_taches.asso_pt_projet_id
                JOIN pg_asso_taches_utilisateurs ON pg_asso_taches_utilisateurs.asso_tu_id = pg_asso_projets_taches_utilisateurs.asso_ptu_tu_id
                JOIN pg_utilisateurs ON pg_utilisateurs.ut_id = pg_asso_taches_utilisateurs.asso_tu_utilisateur_id
                WHERE pg_utilisateurs.ut_id = $userID;"; 
            $prepare = $pdo->prepare($requete);
            $prepare->execute();
            $resultatProjet = $prepare->fetchAll();
        }
        catch (PDOException $e){
            exit("❌🙀❌ OOPS :\n" . $e->getMessage());
        }
    
        if(!isset($_REQUEST['tch']) && !isset($_REQUEST['proj']) && !isset($_REQUEST["tch_nom"]) && !isset($_REQUEST["tch_date_debut"]) && !isset($_REQUEST["tch_date_fin"]) && !isset($_REQUEST["tch_delai"]) && !isset($_REQUEST["tch_categorie"]) && !isset($_REQUEST["tch_duree"])){
      ?>
  
          <form action="utilisateur.php" method="POST">
          <label for="proj">A quel projet cette tâche appartient</label>
          <select name="proj">
            <?php
            $resultatProjetlength = count($resultatProjet);
            for($i = 0; $i < $resultatProjetlength; $i++){
                if($resultatProjet[$i]['prj_id'] != $resultatProjet[$i + 1]['prj_id'])
                echo "<option value=". $resultatProjet[$i]['prj_id'] .">" . $resultatProjet[$i]['prj_nom'] . "</option>";
            }
            ?>
          </select>
          <input type="submit" value="Valider">
          </form>

    <?php
        }

        if(isset($_REQUEST['proj'])){

            $proj = $_REQUEST['proj'];

            try{
                $requete = "SELECT * FROM pg_taches
                            JOIN pg_asso_projets_taches ON pg_asso_projets_taches.asso_pt_tache_id = pg_taches.tch_id
                            JOIN pg_projets ON pg_projets.prj_id = pg_asso_projets_taches.asso_pt_projet_id
                            WHERE pg_projets.prj_id = $proj;";
                $prepare = $pdo->prepare($requete);
                $prepare->execute();
                $resultatTache = $prepare->fetchAll();
            }
            catch (PDOException $e){
                exit("❌🙀❌lol OOPS :\n" . $e->getMessage());
            }
            ?>
            <form action="utilisateur.php" method="POST">
            <label for="tch">Choisi la tâche à modifier</label>
            <select name="tch">
                <?php
                    $resultatTacheLength = count($resultatTache);
                    for($i = 0; $i < $resultatTacheLength; $i++){
                        if($resultatTache[$i]['tch_id'] != 1)
                        echo "<option value=". $resultatTache[$i]['tch_id'] .">" . $resultatTache[$i]['tch_nom'] . "</option>";
                    }
    ?>
            </select>
            <input type="submit" value="Valider">
        </form>
    <?php
        }

        if(isset($_REQUEST['tch'])){
            $tch = $_REQUEST['tch'];
            
            $requete = "SELECT * FROM `pg_taches`
                        WHERE `tch_id` = $tch;";
            $prepare = $pdo->prepare($requete);
            $prepare->execute();
            $resultatNouvelleTache = $prepare->fetchAll();
    ?>
    <form action="utilisateur.php" method="POST">
        
        <label>Update ta tache</label>
        </br>
        <input type="text" name="tch_nom" value="<?php echo $resultatNouvelleTache[0]['tch_nom']; ?>" required>
        </br>
        <input type="date" name="tch_date_debut" value="<?php echo $resultatNouvelleTache[0]['tch_date_debut']; ?>" required>
        </br>
        <input type="date" name="tch_date_fin" value="<?php echo $resultatNouvelleTache[0]['tch_date_fin']; ?>" required>
        </br>
        <input type="text" name="tch_duree" value="<?php echo $resultatNouvelleTache[0]['tch_duree']; ?>" required>
        </br>
        <input type="date" name="tch_delai" value="<?php echo $resultatNouvelleTache[0]['tch_delai']; ?>" required>
        </br>
        <input type="text" name="tch_categorie" value="<?php echo $resultatNouvelleTache[0]['tch_categorie']; ?>" required>
        </br>
        <input type="text" name="tch_id" value="<?php echo $resultatNouvelleTache[0]['tch_id']; ?>" hidden>
        <input type="text" name="tch_avancement" value="<?php echo $resultatNouvelleTache[0]['tch_avancement']; ?>" hidden>
        <input type="submit" value="Valider">
      </form>

    <?php    
        }

        if(isset($_REQUEST["tch_nom"], $_REQUEST["tch_date_debut"], $_REQUEST["tch_date_fin"], $_REQUEST["tch_delai"], $_REQUEST["tch_categorie"], $_REQUEST["tch_duree"])){
         
            $tch_id = $_REQUEST["tch_id"];
            $tch_avancement = $_REQUEST["tch_avancement"];
            $tch_nom = htmlentities($_REQUEST['tch_nom'],ENT_QUOTES);
            $tch_date_debut = htmlentities($_REQUEST['tch_date_debut'],ENT_QUOTES);
            $tch_date_fin = htmlentities($_REQUEST['tch_date_fin'],ENT_QUOTES);
            $tch_delai = htmlentities($_REQUEST['tch_delai'],ENT_QUOTES);
            $tch_duree = htmlentities($_REQUEST['tch_duree'],ENT_QUOTES);
            $tch_categorie = htmlentities($_REQUEST['tch_categorie'],ENT_QUOTES);

            try{
                $requete = "UPDATE `pg_taches` SET
                `tch_id` = '$tch_id',
                `tch_nom` = '$tch_nom',
                `tch_date_debut` = '$tch_date_debut',
                `tch_date_fin` = '$tch_date_fin',
                `tch_delai` = '$tch_delai',
                `tch_avancement` = '$tch_avancement',
                `tch_duree` = '$tch_duree',
                `tch_categorie` = '$tch_categorie'
                WHERE `tch_id` = '$tch_id';"; 
                $prepare = $pdo->prepare($requete);
                $prepare->execute();
                $res = $prepare->rowCount();

                if($res == 1){
                    echo "<p>Tâche modifiée avec succés</p>";
                }
            }
            catch (PDOException $e){
                exit("❌🙀❌ OOPS :\n" . $e->getMessage());
            }

        }
    ?>
    </div>
  </main>

  <!-- CachésCachésCachésCachésCachésCachésCachés -->

  <div id="btnMenuPouce"><span>M</span></div>

  <div id="rechercheMobile">
    <form action="projet.php" method="POST">
      <label for="recherche">Recherche</label>
      <input type="text" id="recherche" name="recherche">
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