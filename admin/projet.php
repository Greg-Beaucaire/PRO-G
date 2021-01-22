<?php
    session_start();
?>


<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <script src="../prefix.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <title>PRO-G - Mes Projets</title>

</head>

<body>
        <script>
                let colorArray = ["#d50000", "#ff8a80", "#c51162", "#ff80ab", "#aa00ff", "#ea80fc", "#6200ea", "#b388ff", "#304ffe", "#8c9eff", "#2962ff", "#82b1ff", "#0091ea", "#80d8ff", "#00b8d4", "#84ffff", "#00bfa5", "#00c853", "#b9f6ca", "#64dd17", "#ccff90", "#aeea00", "#f4ff81", "#ffd600", "#ffff8d", "#ffab00", "#ff6d00", "#ff3d00"];
                let catColorEl;
                let i;
                let randColor;
                let removeColor;
        </script>
    <div id="divTitre">
        <h1><a href="utilisateur.php">PRO-G</a></h1>
    </div>
    
    
    <div id="divHautDroite">
        <h1 class="animate__animated animate__fadeInDown">Mes Projets</h1>
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
    
    
    <main id="mainProjet">
    <?php
        include('../config.php');
        $connexion = new PDO(DB_DRIVER . ":host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_LOGIN, DB_PASS, DB_OPTIONS);

        //FABIEN RECHERCHE
        if (isset($_POST['cherche']) == true){
            $recherche = $_POST['cherche']; //Récupère la recherche si il y en a une
        
            try {
                $user_id = $_SESSION["ut_id"]; //Récupérer l'id user au moment du log
        
                $requete = "SELECT *
                FROM `pg_asso_projets_taches_utilisateurs`
                JOIN pg_utilisateurs ON `pg_utilisateurs`.`ut_id` = `pg_asso_projets_taches_utilisateurs`.`asso_ptu_utilisateur_id`
                JOIN `pg_projets` ON `pg_projets`.`prj_id` = `pg_asso_projets_taches_utilisateurs`.`asso_ptu_projet_id`
                WHERE `pg_projets`.`prj_nom` LIKE '%$recherche%' AND `pg_utilisateurs`.`ut_id` = :user_id "; 
        
                $prepare = $connexion->prepare($requete);
                $prepare->execute(array(
                    ':user_id' => $user_id //La variable de l'user ID
                ));
        
                $resultat = $prepare->fetchAll();
        
                if (isset($resultat[0])){ //Check si résultat est définie ou pas
                    $count = count($resultat);
                    for($i=0; $i<$count ; $i++){
                        $projet = $resultat[$i];
                        echo("<br>");
                        echo('
                        <form action="projet.php" method="POST">
                        <button name="projet" type="submit" value='. $projet["prj_id"] .'>'. $projet["prj_nom"] .'</button>
                        </form> ');
                    } 
                }
                else{
                    echo("Aucun projet ne correspond à votre recherche"); //Message quand le projet n'est pas trouvé
                }
            } catch (PDOException $e) {
                // en cas d'erreur, on récup et on affiche, grâce à notre try/catch
                exit("❌🙀💀 OOPS :\n" . $e->getMessage());
            }  
        
        }
            
        if (isset($_POST['projet'])){
            //récupérer les dates du projet au format string
            $prjID = $_POST['projet'];
            $requete = "SELECT * FROM `pg_projets` WHERE `prj_id`= :prj_id";
            $prepare = $connexion->prepare($requete);
            $prepare->execute(array(
                ':prj_id' => $prjID
                ));
            $prepare = $prepare->fetch();
            $date_debut_projet = $prepare['prj_date_debut'];
            $date_fin_projet = $prepare['prj_date_fin'];
            $projet_id=$prepare['prj_id'];
            $projet_nom = $prepare['prj_nom'];
            echo("<div id='mainProjetTitre'><h1>".$projet_nom."</h1></div>");

            //récupérer les jours, mois et année des dates de début du projet
            $date_debut_projet_format = date_create_from_format('Y-m-j',$date_debut_projet);
            $jour_debut_projet = $date_debut_projet_format-> format('j');
            $mois_debut_projet = $date_debut_projet_format-> format('m');
            $annee_debut_projet = $date_debut_projet_format-> format('Y');
            $jour_debut_projet_format = intval($jour_debut_projet);
            $mois_debut_projet_format = intval($mois_debut_projet);
            $annee_debut_projet_format = intval($annee_debut_projet);   

            //récupérer les jours, mois et année des dates de fin du projet
            $date_fin_projet_format = date_create_from_format('Y-m-j',$date_fin_projet);
            $jour_fin_projet = $date_fin_projet_format-> format('j');
            $mois_fin_projet = $date_fin_projet_format-> format('m');
            $annee_fin_projet = $date_fin_projet_format-> format('Y');
            $jour_fin_projet_format = intval($jour_fin_projet);
            $mois_fin_projet_format = intval($mois_fin_projet);
            $annee_fin_projet_format = intval($annee_fin_projet); 

            //affichage de la partie en-tête de GANTT dans un tableau
            function afficheCalendrier($debut_jour, $debut_mois, $debut_annee, $fin_jour, $fin_mois, $fin_annee){
                $debut_date = mktime(0, 0, 0, $debut_mois, $debut_jour, $debut_annee);
                $fin_date = mktime(0, 0, 0, $fin_mois, $fin_jour, $fin_annee);
                $jourEN = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
                $jourFR   = array("Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi", "Dimanche");
                $moisEN = array("January", "February", "March", "April", "May", "Jule", "July", "August", "September", "October", "November", "December");
                $moisFR = array("Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Decembre");
                echo("<table><tr><th>Nom de tache</th>");
                echo("<th>Acteurs de tache</th>");
                echo("<th>Date début</th>");
                echo("<th>Date fin</th>");
                echo("<th>Delai</th>");
                echo("<th>Avancement</th>");
                echo("<th>Catégorie tache</th>");
                for($i = $debut_date; $i <= $fin_date; $i+=86400){
                    $date = date("l d F",$i);
                    $date = str_replace($jourEN, $jourFR, $date);
                    $date = str_replace($moisEN, $moisFR, $date);
                    echo ("<th>".$date."</th>") ;
                }
                echo("</tr>");
            }
        
            afficheCalendrier($jour_debut_projet,$mois_debut_projet,$annee_debut_projet,$jour_fin_projet,$mois_fin_projet,$annee_fin_projet);

            // recuperer les infos de toutes les taches du projet
            $requete = "SELECT * FROM `pg_taches` 
                        WHERE `tch_id` IN(SELECT `asso_pt_tache_id` FROM `pg_asso_projets_taches`
                        WHERE `asso_pt_projet_id` = :asso_pt_projet_id)";
            $prepare = $connexion->prepare($requete);
            $prepare->execute(array(
                ':asso_pt_projet_id'=> $projet_id
            ));

            while ($entree =$prepare->fetch()){
                $nom_tache=$entree['tch_nom'];
                $date_debut_tache=$entree['tch_date_debut'];
                $date_fin_tache=$entree['tch_date_fin'];
                $delai_tache=$entree['tch_delai'];
                $avancement_tache=$entree['tch_avancement'];
                $duree_tache=$entree['tch_duree'];
                $categorie_tache=$entree['tch_categorie'];
                $tache_id=$entree['tch_id'];
                
                //recuperer les acteurs de la tache en cours
                $requete2 = "SELECT * FROM `pg_utilisateurs`
                            WHERE `ut_id` IN(SELECT `asso_ptu_utilisateur_id` FROM `pg_asso_projets_taches_utilisateurs`
                            WHERE `asso_ptu_tache_id`= :asso_ptu_tache_id AND asso_ptu_projet_id = :asso_ptu_projet_id)";
                $prepare2 = $connexion->prepare($requete2);
                $prepare2->execute(array(
                    ':asso_ptu_tache_id'=> $tache_id,
                    ':asso_ptu_projet_id'=> $projet_id
                ));
                $liste_acteurs='';
                while ($donnee = $prepare2->fetch()){
                    $acteur = $donnee['ut_nom'];
                    $liste_acteurs=$liste_acteurs." ".$acteur;
                }

                //continuer à construire le tableau avec toutes les infos recuperees sauf la partie calendrier
                echo("<tr><td>".$nom_tache."</td><td>".$liste_acteurs."</td><td>".$date_debut_tache.
                "</td><td>".$date_fin_tache."</td><td>".$delai_tache."</td><td>".$avancement_tache."</td><td>".$categorie_tache."<div class='".$categorie_tache."' style='height: 1rem; width: 1rem;'></div></td>");

                //construction du calendrier
                // 1° on calcul le nombre de jour entre le début du projet et le début de la tache en cours
                    $date_debut_projet_init = strtotime($date_debut_projet);
                    $date_debut_tache_init = strtotime($date_debut_tache);
        
                    // On récupère la différence de timestamp entre les 2 précédents
                    $nbJoursTimestamp = $date_debut_tache_init - $date_debut_projet_init;
        
                    // ** Pour convertir le timestamp (exprimé en secondes) en jours **
                    // On sait que 1 heure = 60 secondes * 60 minutes et que 1 jour = 24 heures donc :
                    $nbJours = $nbJoursTimestamp/86400; // 86 400 = 60*60*24 

                //2° on rempli de cases vides jusqu'à ce que la tache commence
                for ($n=0; $n<$nbJours; $n++){
                    echo("<td></td>");
                }
                //3° on rempli de cases pleines sur la durée de la tache
                for ($n=0; $n<$duree_tache; $n++){
                    echo("<td  class=".$categorie_tache."><a style='display:block; width:100%; height:100%;' href='../info_tache.php?nomTache=".$nom_tache."&dateDebut=".$date_debut_tache."&dateFin=".$date_fin_tache."&duree=".$duree_tache."&categorie=".$categorie_tache."&acteurs=".$liste_acteurs."'></a></td>");
                }
                echo("</tr>");
                ?>
                <script>
                    catColorEl = document.getElementsByClassName('<?php echo($categorie_tache) ?>');
                    randColor = colorArray[Math.floor(Math.random() * Math.floor(colorArray.length))];
                    for(i=0; i < catColorEl.length; i++){
                        catColorEl[i].style.backgroundColor = randColor;
                    }
                    removeColor = colorArray.indexOf(randColor);
                    colorArray.splice(removeColor, 1);
                </script>
                <?php
                
            }
            echo("</table>");
            echo("
                <form action='projet.php' method='POST'>
                    <input type='search' id='chercheTache' name='chercheTache' placeholder='Nom ou partie du nom de la tache' style='width: 30rem; text-align: center;'>
                    <input type='hidden' value='".$prjID."' name='projet'>
                    <button>Rechercher une tache</button>
                </form>
                <form action='projet.php' method='POST'>
                    <input type='search' id='chercheCat' name='chercheCat' placeholder='Nom ou partie du nom de la catégorie' style='width: 30rem; text-align: center;'> 
                    <input type='hidden' value='".$prjID."' name='projet'> 
                    <button>Rechercher une catégorie</button>
                </form>
            ");


            //FONCTION RECHERCHE TACHE
            if (isset($_POST['chercheTache']) == true){
                $prjID = $_POST['projet'];
                $recherche = $_POST['chercheTache']; //Récupère la recherche si il y en a une
            
                try {
                    $user_id = $_SESSION["ut_id"]; //Récupérer l'id user au moment du log
                    $projet_actuel_id = $_POST['projet']; //Récupérer l'id user au moment l'arrivée sur le projet
            
                    $requete = "SELECT *
                    FROM `pg_asso_projets_taches_utilisateurs`
                    JOIN pg_utilisateurs ON `pg_utilisateurs`.`ut_id` = `pg_asso_projets_taches_utilisateurs`.`asso_ptu_utilisateur_id`
                    JOIN `pg_taches` ON `pg_taches`.`tch_id` = `pg_asso_projets_taches_utilisateurs`.`asso_ptu_tache_id`
                    JOIN `pg_projets` ON `pg_projets`.`prj_id` = `pg_asso_projets_taches_utilisateurs`.`asso_ptu_projet_id`
                    WHERE `pg_taches`.`tch_nom` LIKE '%$recherche%' AND `pg_utilisateurs`.`ut_id` = :user_id AND `pg_projets`.`prj_id` = :projet_id "; 
            
                    $prepare = $connexion->prepare($requete);
                    $prepare->execute(array(
                        ':user_id' => $user_id, //La variable de l'user ID
                        ':projet_id' => $projet_actuel_id //La variable du projet dans lequel on est
                    ));
            
                    $resultat = $prepare->fetchAll();
                    echo("<div class='divTache'>");
                    if (isset($resultat[0]) == true){ //Check si résultat est définie ou pas
                        $count = count($resultat);
                        for($i=0; $i<$count ; $i++){
                            $tache = $resultat[$i];
                            echo('
                            <div class="tache">
                                <p>Tache : '.$tache["tch_nom"].'<br>Commence le : '.$tache['tch_date_debut'].'<br>Se termine le :'.$tache['tch_date_fin'].'<br> Durée : '.$tache['tch_delai'].' jours<br>Catégorie : '.$tache['tch_categorie'].'</p>
                            </div>
                            ');
                            
                        } 
                    }
                    else{
                        echo("Aucune tache ne correspond à votre recherche"); //Message quand le projet n'est pas trouvé
                    }
                    echo("</div>");
                } catch (PDOException $e) {
                    // en cas d'erreur, on récup et on affiche, grâce à notre try/catch
                    exit("❌🙀💀 OOPS :\n" . $e->getMessage());
                }  
            }

            //FONCTION RECHERCHE CATEGORIE
            


            if (isset($_POST['chercheCat']) == true){
                $recherche = $_POST['chercheCat']; //Récupère la recherche si il y en a une
                $user_id = $_SESSION["ut_id"]; //Récupérer l'id user au moment du log
                $projet_actuel_id = $_POST['projet']; //Récupérer l'id user au moment l'arrivée sur le projet
                
                try {
                    
                    $user_id = $_SESSION["ut_id"]; //Récupérer l'id user au moment du log
                    $projet_actuel_id = $_POST['projet']; //Récupérer l'id user au moment l'arrivée sur le projet
            
                    $requete = "SELECT *
                    FROM `pg_asso_projets_taches_utilisateurs`
                    JOIN pg_utilisateurs ON `pg_utilisateurs`.`ut_id` = `pg_asso_projets_taches_utilisateurs`.`asso_ptu_utilisateur_id`
                    JOIN `pg_taches` ON `pg_taches`.`tch_id` = `pg_asso_projets_taches_utilisateurs`.`asso_ptu_tache_id`
                    JOIN `pg_projets` ON `pg_projets`.`prj_id` = `pg_asso_projets_taches_utilisateurs`.`asso_ptu_projet_id`
                    WHERE `pg_taches`.`tch_categorie` LIKE '%$recherche%' AND `pg_utilisateurs`.`ut_id` = :user_id AND `pg_projets`.`prj_id` = :projet_id "; 
            
                    $prepare = $connexion->prepare($requete);
                    $prepare->execute(array(
                        ':user_id' => $user_id, //La variable de l'user ID
                        ':projet_id' => $projet_actuel_id //La variable du projet dans lequel on est
                    ));
            
                    $resultat = $prepare->fetchAll();
                    echo("<div class='divTache'>");
                    if (isset($resultat[0]) == true){ //Check si résultat est définie ou pas
                        $count = count($resultat);
                        for($i=0; $i<$count ; $i++){
                            $tache = $resultat[$i];
                            echo('
                            <div class="tache">
                                <p>Tache : '.$tache["tch_nom"].'<br>Commence le : '.$tache['tch_date_debut'].'<br>Se termine le :'.$tache['tch_date_fin'].'<br> Durée : '.$tache['tch_delai'].' jours<br>Catégorie : '.$tache['tch_categorie'].'</p>
                            </div>
                            ');
                        } 
                    }
                    else{
                        echo("Aucune tache ne correspond à votre recherche"); //Message quand le projet n'est pas trouvé
                    }
                    echo('</div>');
                } catch (PDOException $e) {
                    // en cas d'erreur, on récup et on affiche, grâce à notre try/catch
                    exit("❌🙀💀 OOPS :\n" . $e->getMessage());
                }  
            }
            
            
        }
        
    ?>        
    </main>

    <!-- CachésCachésCachésCachésCachésCachésCachés -->

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

    
</body>
</html>