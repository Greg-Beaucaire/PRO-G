<!-- PHP / Check User TYPE -->
<?php
// Initialiser la session
session_start();
// Vérifiez si l'utilisateur est connecté, sinon redirigez-le vers la page de connexion
if ($_SESSION["ut_type"] != "admin") {
  header("Location: ../index.php");
  exit();
}
require('../config.php');
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
    <h1>Espace Personnel</h1>
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
    <span>TÂCHE</span>
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
            $resultat = unique_multidim_array($resultat, 'prj_id');
            foreach ($resultat as $key => $value) {
                echo "<option value=" . $value['prj_id'] . ">" . $value['prj_nom'] . "</option>";
            }
            ?>
          </select>
        </div>
        <input type="submit" value="Valider">

      </form>
      <?php
      if (isset($_REQUEST["util"], $_REQUEST["proj"], $_REQUEST["tch_nom"], $_REQUEST["tch_date_debut"], $_REQUEST["tch_date_fin"], $_REQUEST["tch_delai"], $_REQUEST["tch_categorie"], $_REQUEST["tch_duree"])) {

        $util = htmlentities($_REQUEST['util'], ENT_QUOTES);
        $proj = htmlentities($_REQUEST['proj'], ENT_QUOTES);
        $tch_nom = htmlentities($_REQUEST['tch_nom'], ENT_QUOTES);
        $tch_date_debut = htmlentities($_REQUEST['tch_date_debut'], ENT_QUOTES);
        $tch_date_fin = htmlentities($_REQUEST['tch_date_fin'], ENT_QUOTES);
        $tch_delai = htmlentities($_REQUEST['tch_delai'], ENT_QUOTES);
        $tch_avancement = 0;
        $tch_duree = htmlentities($_REQUEST['tch_duree'], ENT_QUOTES);
        $tch_categorie = htmlentities($_REQUEST['tch_categorie'], ENT_QUOTES);

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
    <div class="containerForm grandForm">
      <h1 class="box-logo box-title">
        Modifier une tâche
      </h1>
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
        $resultatProjet = unique_multidim_array($resultatProjet, 'prj_id');
      } catch (PDOException $e) {
        exit("❌🙀❌ OOPS :\n" . $e->getMessage());
      }

      if (!isset($_REQUEST['tchSelect']) && !isset($_REQUEST['projSelect'])) {
      ?>

        <form action="utilisateur.php" method="POST">
          <label for="projSelect">A quel projet cette tâche appartient</label>
          <select name="projSelect">
            <?php
            $resultatProjetlength = count($resultatProjet);
            foreach ($resultatProjet as $key => $value) {
              echo "<option value=" . $value['prj_id'] . ">" . $value['prj_nom'] . "</option>";
            }
            ?>
          </select>
          <input type="submit" value="Valider">
        </form>

      <?php
      }

      if (isset($_REQUEST['projSelect'])) {

        $proj = $_REQUEST['projSelect'];

        try {
          $requete = "SELECT * FROM pg_taches
                            JOIN pg_asso_projets_taches ON pg_asso_projets_taches.asso_pt_tache_id = pg_taches.tch_id
                            JOIN pg_projets ON pg_projets.prj_id = pg_asso_projets_taches.asso_pt_projet_id
                            WHERE pg_projets.prj_id = $proj;";
          $prepare = $pdo->prepare($requete);
          $prepare->execute();
          $resultatTache = $prepare->fetchAll();
        } catch (PDOException $e) {
          exit("❌🙀❌lol OOPS :\n" . $e->getMessage());
        }
      ?>
        <form action="utilisateur.php" method="POST">
          <label for="tchSelect">Choisi la tâche à modifier</label>
          <select name="tchSelect">
            <?php
            $resultatTacheLength = count($resultatTache);
            for ($i = 0; $i < $resultatTacheLength; $i++) {
              if ($resultatTache[$i]['tch_id'] != 1)
                echo "<option value=" . $resultatTache[$i]['tch_id'] . ">" . $resultatTache[$i]['tch_nom'] . "</option>";
            }
            ?>
          </select>
          <input type="submit" value="Valider">
        </form>
      <?php
      }

      if (isset($_REQUEST['tchSelect'])) {
        $tch = $_REQUEST['tchSelect'];

        $requete = "SELECT * FROM `pg_taches`
                        WHERE `tch_id` = $tch;";
        $prepare = $pdo->prepare($requete);
        $prepare->execute();
        $resultatNouvelleTache = $prepare->fetchAll();
      ?>
        <form action="utilisateur.php" method="POST">
          <div>
            <input type="text" name="tch_nomMod" value="<?php echo $resultatNouvelleTache[0]['tch_nom']; ?>" required>
            <label for="tch_date_debutMod">Date de début</label>
            <input type="date" name="tch_date_debutMod" value="<?php echo $resultatNouvelleTache[0]['tch_date_debut']; ?>" required>
            <label for="tch_date_finMod">Date de fin</label>
            <input type="date" name="tch_date_finMod" value="<?php echo $resultatNouvelleTache[0]['tch_date_fin']; ?>" required>
            <label for="tch_dureeMod">Durée de la tâche</label>
            <input type="text" name="tch_dureeMod" value="<?php echo $resultatNouvelleTache[0]['tch_duree']; ?>" required>
            <label for="tch_delaiMod">Délai potentiel</label>
            <input type="date" name="tch_delaiMod" value="<?php echo $resultatNouvelleTache[0]['tch_delai']; ?>" required>
            <label for="tch_categorieMod">Catégorie</label>
            <input type="text" name="tch_categorieMod" value="<?php echo $resultatNouvelleTache[0]['tch_categorie']; ?>" required>
            <input type="text" name="tch_id" value="<?php echo $resultatNouvelleTache[0]['tch_id']; ?>" hidden>
            <input type="text" name="tch_avancementMod" value="<?php echo $resultatNouvelleTache[0]['tch_avancement']; ?>" hidden>
            <input type="submit" value="Valider">
          </div>
        </form>

      <?php
      }

      if (isset($_REQUEST["tch_nomMod"], $_REQUEST["tch_date_debutMod"], $_REQUEST["tch_date_finMod"], $_REQUEST["tch_delaiMod"], $_REQUEST["tch_categorieMod"], $_REQUEST["tch_dureeMod"])) {

        $tch_id = $_REQUEST["tch_id"];
        $tch_avancement = $_REQUEST["tch_avancementMod"];
        $tch_nom = htmlentities($_REQUEST['tch_nomMod'], ENT_QUOTES);
        $tch_date_debut = htmlentities($_REQUEST['tch_date_debutMod'], ENT_QUOTES);
        $tch_date_fin = htmlentities($_REQUEST['tch_date_finMod'], ENT_QUOTES);
        $tch_delai = htmlentities($_REQUEST['tch_delaiMod'], ENT_QUOTES);
        $tch_duree = htmlentities($_REQUEST['tch_dureeMod'], ENT_QUOTES);
        $tch_categorie = htmlentities($_REQUEST['tch_categorieMod'], ENT_QUOTES);

        try {
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

          if ($res == 1) {
            echo "<p>Tâche modifiée avec succés</p>";
          }
        } catch (PDOException $e) {
          exit("❌🙀❌ OOPS :\n" . $e->getMessage());
        }
      }
      ?>
    </div>

    <!-- SUPPRESSION TACHE SUPPRESSION TACHE SUPPRESSION TACHE SUPPRESSION TACHE SUPPRESSION TACHE SUPPRESSION TACHE SUPPRESSION TACHE SUPPRESSION TACHE SUPPRESSION TACHE SUPPRESSION TACHE -->
    <div class="containerForm">
      <h1 class="box-logo box-title">
        Supprimer une tâche
      </h1>
      <?php
      if (!isset($_REQUEST['projetchoix']) && !isset($_REQUEST['tachesupp'])) {
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
          $resultatProjetShort = unique_multidim_array($resultatProjet, 'prj_nom');
        } catch (PDOException $e) {
          exit("❌🙀❌ OOPS :\n" . $e->getMessage());
        }
      ?>

        <form action="" methode="POST">
          <label for="projetchoix">A quel projet appartient la tâche que vous souhaitez supprimer ?</label>
          <select name="projetchoix">
            <?php
            foreach ($resultatProjetShort as $key => $value) {
              echo "<option value=" . $value['prj_id'] . ">" . $value['prj_nom'] . "</option>";
            }
            ?>
          </select>
          <input type="submit" value="Valider">
        </form>
      <?php
      }
      if (isset($_REQUEST['projetchoix'])) {
        $prj_id = $_REQUEST['projetchoix'];

        try {
          $requete = "SELECT * FROM `pg_taches`
                    INNER JOIN `pg_asso_projets_taches` ON `tch_id` = `asso_pt_tache_id`
                    WHERE `asso_pt_projet_id` = :asso_pt_projet_id;";
          $prepare = $pdo->prepare($requete);
          $prepare->execute(array(
            ":asso_pt_projet_id" => $prj_id
          ));
          $resultatTache = $prepare->fetchAll();
        } catch (PDOException $e) {
          exit("❌🙀❌ OOPS :\n" . $e->getMessage());
        }
      ?>
        <form action="" methode="POST">
          <label for="tachesupp">Quelle tâche veux-tu supprimer ?</label>
          <select name="tachesupp">
            <?php
            foreach ($resultatTache as $key => $value) {
              if ($value['tch_id'] != 1) {
                echo "<option value=" . $value['tch_id'] . ">" . $value['tch_nom'] . "</option>";
              }
            }
            ?>
          </select>
          <input type="submit" value="Valider">
        </form>
      <?php
      }

      if (isset($_REQUEST['tachesupp'])) {
        $tch_id = $_REQUEST['tachesupp'];

        $requete = "SELECT * FROM `pg_taches`
                    WHERE `tch_id` = :tch_id;";
        $prepare = $pdo->prepare($requete);
        $prepare->execute(array(
          ":tch_id" => $tch_id
        ));
        $resultatTache = $prepare->fetch();
      ?>
        <form action="" methode="POST">
          <label>Confirmation de la suppression de la tache : <b><?php echo ($resultatTache['tch_nom']); ?></b></label>
          <input name="Valider" type="submit" value="Oui">
          <input name="Annuler" type="submit" value="Non">
          <input name="tch_id" type="text" value=" <?php echo $tch_id; ?> " hidden>
        </form>
      <?php
      }

      if (isset($_REQUEST['Valider'])) {

        $tch_id = $_REQUEST['tch_id'];

        try {
          $requete = "SELECT * 
                    FROM pg_asso_projets_taches_utilisateurs
                    JOIN pg_asso_projets_taches ON pg_asso_projets_taches.asso_pt_id = pg_asso_projets_taches_utilisateurs.asso_ptu_pt_id
                    JOIN pg_projets ON pg_projets.prj_id = pg_asso_projets_taches.asso_pt_projet_id
                    JOIN pg_asso_taches_utilisateurs ON pg_asso_taches_utilisateurs.asso_tu_id = pg_asso_projets_taches_utilisateurs.asso_ptu_tu_id
                    JOIN pg_utilisateurs ON pg_utilisateurs.ut_id = pg_asso_taches_utilisateurs.asso_tu_utilisateur_id
                    WHERE pg_asso_taches_utilisateurs.asso_tu_tache_id = $tch_id;";
          $prepare = $pdo->prepare($requete);
          $prepare->execute();
          $resultatTacheSupp = $prepare->fetchAll();

          foreach ($resultatTacheSupp as $key => $value) {

            $asso_ptu_id = $value['asso_ptu_id'];

            $requete = "DELETE FROM `pg_asso_projets_taches_utilisateurs`
                      WHERE `asso_ptu_id` = '$asso_ptu_id';";
            $prepare = $pdo->prepare($requete);
            $prepare->execute();
          }

          foreach ($resultatTacheSupp as $key => $value) {

            $asso_pt_id = $value['asso_pt_id'];
            $asso_tu_id = $value['asso_tu_id'];

            $requete = "DELETE FROM `pg_asso_projets_taches`
                        WHERE `asso_pt_id` = '$asso_pt_id';";
            $prepare = $pdo->prepare($requete);
            $prepare->execute();

            $requete = "DELETE FROM `pg_asso_taches_utilisateurs`
                        WHERE `asso_tu_id` = '$asso_tu_id';";
            $prepare = $pdo->prepare($requete);
            $prepare->execute();
          }

          foreach ($resultatTacheSupp as $key => $value) {
            if ($tch_id != 1) {
              $requete = "DELETE FROM `pg_taches`
                            WHERE `tch_id` = '$tch_id';";
              $prepare = $pdo->prepare($requete);
              $prepare->execute();
            }
          }
        } catch (PDOException $e) {
          exit("❌🙀❌ OOPS :\n" . $e->getMessage());
        }
      }

      if (isset($_REQUEST['Annuler'])) {
        echo "<p>Veuillez recharger la page.</p>";
      }
      ?>
    </div>

    <span>PROJET</span>
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
            echo "<p>Le projet a été créé avec succés.</p>";
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
    
    <!-- MODIFICATION PROJET  MODIFICATION PROJET MODIFICATION PROJET MODIFICATION PROJET MODIFICATION PROJET MODIFICATION PROJET MODIFICATION PROJET MODIFICATION PROJET MODIFICATION PROJET-->
    <div class="containerForm">
      <h1 class="box-logo box-title">
        Modifier un projet
      </h1>
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
        $resultatProjet = unique_multidim_array($resultatProjet, 'prj_id');
      } catch (PDOException $e) {
        exit("❌🙀❌ OOPS :\n" . $e->getMessage());
      }

      if (!isset($_REQUEST['projMod'])) {
      ?>
        <form action="utilisateur.php" method="POST">
          <label for="projMod">A quel projet cette tâche appartient</label>
          <select name="projMod">
            <?php
            $resultatProjetlength = count($resultatProjet);
            foreach ($resultatProjet as $key => $value) {
              echo "<option value=" . $value['prj_id'] . ">" . $value['prj_nom'] . "</option>";
            }
            ?>
          </select>
          <input type="submit" value="Valider">
        </form>

      <?php
      }

      if (isset($_REQUEST['projMod'])) {

        $prjID = htmlentities($_REQUEST['projMod'], ENT_QUOTES);
        // ICICICICICICICICICICICICICICICICICICICICICICICICICICCIi
        //Récupère les infos pour préremplir les Forms
        try {
          $pdo = new PDO(DB_DRIVER . ":host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_LOGIN, DB_PASS, DB_OPTIONS);
          $requete = "SELECT * FROM `pg_projets`
              WHERE `prj_id` = :prj_id;";
          $prepare = $pdo->prepare($requete);
          $prepare->execute(array(
            ":prj_id"   => $prjID,
          ));
          $resultat = $prepare->fetchAll();
          $resultat = $resultat[0]; ////Récupérer l'id du projet au moment l'arrivée sur le projet

          echo ('
                  <form action="utilisateur.php" method="POST">
                      <label for="modifier_nom_projet">Nom du projet:</label>
                      <input type="search" id="bouton_cherche" name="modifier_nom_projet" value="' . $resultat["prj_nom"] . '" required>  
                      <label for="modifier_date_debut_projet">Date de début:</label>
                      <input type="date" name="modifier_date_debut_projet" value="' . $resultat["prj_date_debut"] . '" required>
                      <label for="modifier_date_fin_projet">Date de fin:</label>
                      <input type="date" name="modifier_date_fin_projet" value="' . $resultat["prj_date_fin"] . '" required>
                      <input type="hidden" name="projMod2" value="' . $prjID . '"> 
                      <input type="submit" value="Valider">
                  </form>');
        } catch (PDOException $e) {
          // en cas d'erreur, on récup et on affiche, grâce à notre try/catch
          exit("❌🙀💀 OOPS :\n" . $e->getMessage());
        }
      }
      if (isset($_POST['modifier_nom_projet']) == true || isset($_POST['modifier_date_debut_projet']) == true || isset($_POST['modifier_date_fin_projet']) == true) {
        try {
          $prjID = htmlentities($_POST['projMod2'], ENT_QUOTES);
          $renommer_prj_nom = htmlentities($_POST['modifier_nom_projet'], ENT_QUOTES); //Récupère la recherche si il y en a une
          $renommer_prj_date_debut = htmlentities($_POST['modifier_date_debut_projet'], ENT_QUOTES); //Récupère la recherche si il y en a une
          $renommer_prj_date_fin = htmlentities($_POST['modifier_date_fin_projet'], ENT_QUOTES); //Récupère la recherche si il y en a une
          $pdo = new PDO(DB_DRIVER . ":host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_LOGIN, DB_PASS, DB_OPTIONS);
          $requete = "UPDATE `pg_projets`
              SET `prj_nom` = :prj_nom, 
              `prj_date_debut` = :prj_date_debut,
              `prj_date_fin` = :prj_date_fin 
              WHERE `prj_id` = :prj_id;";
          $prepare = $pdo->prepare($requete);
          $prepare->execute(array(
            ":prj_nom"   => $renommer_prj_nom,
            ":prj_date_debut" => $renommer_prj_date_debut,
            ":prj_date_fin" => $renommer_prj_date_fin,
            ":prj_id" => $prjID,
          ));
          $resultat = $prepare->fetchAll();
          $resultatCount = $prepare->rowCount();
          if ($resultatCount == 1) {
            echo "<p>Projet modifié avec succés.</p>";
          }
        } catch (PDOException $e) {
          // en cas d'erreur, on récup et on affiche, grâce à notre try/catch
          exit("❌🙀💀 OOPS :\n" . $e->getMessage());
        }
      }
      ?>
    </div>

    <!-- SUPPRESSION PROJET SUPPRESSION PROJET SUPPRESSION PROJET SUPPRESSION PROJET SUPPRESSION PROJET SUPPRESSION PROJET SUPPRESSION PROJET SUPPRESSION PROJET SUPPRESSION PROJET SUPPRESSION PROJET -->
    <div class="containerForm">
      <h1 class="box-logo box-title">
        Supprimer un projet
      </h1>
      <?php
      if (!isset($_REQUEST['projetsupp'])) {
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
          $resultatProjetShort = unique_multidim_array($resultatProjet, 'prj_nom');
        } catch (PDOException $e) {
          exit("❌🙀❌ OOPS :\n" . $e->getMessage());
        }
      ?>

        <form action="" methode="POST">
          <label for="projetsupp">Quel projet voulez-vous supprimer ?</label>
          <select name="projetsupp">
            <?php
            foreach ($resultatProjetShort as $key => $value) {
              echo "<option value=" . $value['prj_id'] . ">" . $value['prj_nom'] . "</option>";
            }
            ?>
          </select>
          <input type="submit" value="Valider">
        </form>

      <?php
      }
      if (isset($_REQUEST['projetsupp'])) {
        $prj_id = $_REQUEST['projetsupp'];
      ?>
        <form action="" methode="POST">
          <label>Souhaitez-vous vraiment supprimer ce projet ?</label>
          <input name="ValiderPrj" type="submit" value="Oui">
          <input name="AnnulerPrj" type="submit" value="Non">
          <input name="prj_id" type="text" value=" <?php echo $prj_id; ?> " hidden>
        </form>
      <?php
      }

      if (isset($_REQUEST['ValiderPrj'])) {

        $prj_id = $_REQUEST['prj_id'];

        try {
          $requete = "SELECT * 
                    FROM pg_asso_projets_taches_utilisateurs
                    JOIN pg_asso_projets_taches ON pg_asso_projets_taches.asso_pt_id = pg_asso_projets_taches_utilisateurs.asso_ptu_pt_id
                    JOIN pg_projets ON pg_projets.prj_id = pg_asso_projets_taches.asso_pt_projet_id
                    JOIN pg_asso_taches_utilisateurs ON pg_asso_taches_utilisateurs.asso_tu_id = pg_asso_projets_taches_utilisateurs.asso_ptu_tu_id
                    JOIN pg_utilisateurs ON pg_utilisateurs.ut_id = pg_asso_taches_utilisateurs.asso_tu_utilisateur_id
                    WHERE pg_projets.prj_id = $prj_id;";
          $prepare = $pdo->prepare($requete);
          $prepare->execute();
          $resultatProjetSupp = $prepare->fetchAll();

          foreach ($resultatProjetSupp as $key => $value) {

            $asso_ptu_id = $value['asso_ptu_id'];

            $requete = "DELETE FROM `pg_asso_projets_taches_utilisateurs`
                        WHERE ((`asso_ptu_id` = '$asso_ptu_id'));";
            $prepare = $pdo->prepare($requete);
            $prepare->execute();
          }

          foreach ($resultatProjetSupp as $key => $value) {

            $asso_pt_id = $value['asso_pt_id'];
            $asso_tu_id = $value['asso_tu_id'];

            $requete = "DELETE FROM `pg_asso_projets_taches`
                        WHERE ((`asso_pt_id` = '$asso_pt_id'));";
            $prepare = $pdo->prepare($requete);
            $prepare->execute();

            $requete = "DELETE FROM `pg_asso_taches_utilisateurs`
                        WHERE ((`asso_tu_id` = '$asso_tu_id'));";
            $prepare = $pdo->prepare($requete);
            $prepare->execute();
          }

          foreach ($resultatProjetSupp as $key => $value) {

            $tch_id = $value['asso_pt_tache_id'];

            if ($tch_id != 1) {
              $requete = "DELETE FROM `pg_taches`
                            WHERE ((`tch_id` = '$tch_id'));";
              $prepare = $pdo->prepare($requete);
              $prepare->execute();
            }

            $requete = "DELETE FROM `pg_projets`
                        WHERE ((`prj_id` = '$prj_id'));";
            $prepare = $pdo->prepare($requete);
            $prepare->execute();
          }
        } catch (PDOException $e) {
          exit("❌🙀❌ OOPS :\n" . $e->getMessage());
        }
      }

      if (isset($_REQUEST['AnnulerPrj'])) {
        echo "Suppression annulée.";
      }
      ?>
    </div>


    <span>UTILISATEUR</span>
    <div class="containerForm">
      <h1 class="box-logo box-title">
        Ajouter un utilisateur
      </h1>
      <form class="box" action="" method="post">
        <input type="text" class="box-input" name="ut_nom" placeholder="Nom d'utilisateur" required />
        <input type="email" class="box-input" name="ut_email" placeholder="Email" required />
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

  
    <!-- LIAISON TACHE USER  LIAISON TACHE USER LIAISON TACHE USER LIAISON TACHE USER LIAISON TACHE USER LIAISON TACHE USER LIAISON TACHE USER LIAISON TACHE USER-->
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

      if (isset($_REQUEST["projLiaison"])) {

        $proj = $_REQUEST["projLiaison"];

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
            echo ("
                <h1>Lier un utilisateur à une tâche</h1>
                <form action='utilisateur.php' method='POST'>
                <label for='proj'>À quel projet cette tâche appartient</label>
                <select name='proj'>
                ");
            $resultatProjet = unique_multidim_array($resultatProjet, 'prj_id');
            foreach ($resultatProjet as $key => $value) {
              echo "<option value=" . $value['prj_id'] . ">" . $value['prj_nom'] . "</option>";
            }
            echo ("
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
          <label for="projLiaison">À quel projet cette tâche appartient</label>
          <select name="projLiaison">
            <?php
            $resultatProjet = unique_multidim_array($resultatProjet, 'prj_id');
            foreach ($resultatProjet as $key => $value) {
              echo "<option value=" . $value['prj_id'] . ">" . $value['prj_nom'] . "</option>";
            }
            ?>
          </select>
          <input type="submit" value="Choisir">
        </form>
      <?php
      }
      ?>
    </div>


    <!-- MOD USER/TASK MOD USER/TASK MOD USER/TASK MOD USER/TASK MOD USER/TASK MOD USER/TASK MOD USER/TASK MOD USER/TASK MOD USER/TASK MOD USER/TASK MOD USER/TASK MOD USER/TASK -->
    <div class="containerForm">
      <h1 class="box-logo box-title">
        Retirer un acteur d'une tâche
      </h1>
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
        $resultatProjetAdmin = $prepare->fetchAll();
        $tab = [];

        foreach ($resultatProjetAdmin as $key => $value) {
          $prj_id = $value['prj_id'];

          $requete = "SELECT * 
                FROM pg_asso_projets_taches_utilisateurs
                JOIN pg_asso_projets_taches ON pg_asso_projets_taches.asso_pt_id = pg_asso_projets_taches_utilisateurs.asso_ptu_pt_id
                JOIN pg_projets ON pg_projets.prj_id = pg_asso_projets_taches.asso_pt_projet_id
                JOIN pg_asso_taches_utilisateurs ON pg_asso_taches_utilisateurs.asso_tu_id = pg_asso_projets_taches_utilisateurs.asso_ptu_tu_id
                JOIN pg_utilisateurs ON pg_utilisateurs.ut_id = pg_asso_taches_utilisateurs.asso_tu_utilisateur_id
                WHERE pg_projets.prj_id = $prj_id;";
          $prepare = $pdo->prepare($requete);
          $prepare->execute();
          $resultatUtilisateur = $prepare->fetchAll();

          foreach ($resultatUtilisateur as $clef => $valeur) {
            array_push($tab, $valeur);
          }
        }
        $resultatUtilisateurShort = unique_multidim_array($tab, 'ut_id');
      } catch (PDOException $e) {
        exit("❌🙀❌ OOPS :\n" . $e->getMessage());
      }

      if (!isset($_REQUEST['UserInProjects']) && !isset($_REQUEST['TaskToDelete'])) {
      ?>

        <form action="" methode="POST">
          <label for="UserInProjects">Quel utilisateur souhaitez-vous retirer d'une tâche ?</label>
          <select name="UserInProjects">
            <?php
            foreach ($resultatUtilisateurShort as $key => $value) {
              echo "<option value=" . $value['ut_id'] . ">" . $value['ut_nom'] . "</option>";
            }
            ?>
          </select>
          <input type="submit" value="Valider">
        </form>

      <?php
      }

      if (isset($_REQUEST['UserInProjects'])) {

        $ut_id = $_REQUEST['UserInProjects'];

        $requete = "SELECT * FROM pg_asso_taches_utilisateurs
                    JOIN pg_taches ON pg_taches.tch_id = pg_asso_taches_utilisateurs.asso_tu_tache_id
                    JOIN pg_utilisateurs ON pg_utilisateurs.ut_id = pg_asso_taches_utilisateurs.asso_tu_utilisateur_id
                    WHERE pg_utilisateurs.ut_id = $ut_id;";
        $prepare = $pdo->prepare($requete);
        $prepare->execute();
        $resultatTache = $prepare->fetchAll();
        $resultatTacheShort = unique_multidim_array($resultatTache, 'tch_id');
      }

      if (!isset($_REQUEST['TaskToDelete']) && isset($_REQUEST['UserInProjects'])) {
      ?>

        <form action="" methode="POST">
          <label for="TaskToDelete">De quelle tâche doit-il se faire retirer ?</label>
          <select name="TaskToDelete">
            <?php
            foreach ($resultatTacheShort as $key => $value) {
              if ($value['tch_id'] != 1) {
                echo "<option value=" . $value['asso_tu_id'] . ">" . $value['tch_nom'] . "</option>";
              }
            }
            ?>
          </select>
          <input type="submit" value="Valider">
        </form>
      <?php
      }

      if (isset($_REQUEST['TaskToDelete'])) {
        $asso_tu_id = $_REQUEST['TaskToDelete'];
      ?>
        <form action="" methode="POST">
          <label>Souhaitez-vous réellement retirer cet utilisateur de cette tâche ?</label>
          <input name="Validersupplink" type="submit" value="Oui">
          <input name="Annulersupplink" type="submit" value="Non">
          <input name="asso_tu_id" type="text" value=" <?php echo $asso_tu_id; ?> " hidden>
        </form>
      <?php
      }

      if (isset($_REQUEST['Validersupplink'])) {

        $asso_tu_id = $_REQUEST['asso_tu_id'];

        try {

          $requete = "DELETE FROM `pg_asso_projets_taches_utilisateurs`
                        WHERE `asso_ptu_tu_id` = '$asso_tu_id';";
          $prepare = $pdo->prepare($requete);
          $prepare->execute();

          $requete = "DELETE FROM `pg_asso_taches_utilisateurs`
                        WHERE `asso_tu_id` = '$asso_tu_id';";
          $prepare = $pdo->prepare($requete);
          $prepare->execute();
        } catch (PDOException $e) {
          exit("❌🙀❌ OOPS :\n" . $e->getMessage());
        }
      }

      if (isset($_REQUEST['Annulersupplink'])) {
        echo "<p>Requête annulée.</p>";
      }

      ?>
    </div>
  </main>

  <!-- CachésCachésCachésCachésCachésCachésCachés MOBILE-->

  <div id="btnMenuPouce"><span>M</span></div>

  <div id="rechercheMobile">
    <form action="projet.php" method="POST">
      <label for="cherche">Recherche</label>
      <input type="text" id="recherche" name="cherche">
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

  <?php
  function unique_multidim_array($array, $key)
  {
    $temp_array = array();
    $i = 0;
    $key_array = array();

    foreach ($array as $val) {
      if (!in_array($val[$key], $key_array)) {
        $key_array[$i] = $val[$key];
        $temp_array[$i] = $val;
      }
      $i++;
    }
    return $temp_array;
  }
  ?>
</body>

</html>