<?php
    session_start();
    include('../config.php');
    // Vérifiez si l'utilisateur est connecté, sinon redirigez-le vers la page de connexion
    if ($_SESSION["ut_type"] != "admin") {
    header("Location: ../index.php");
    exit();
    }
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
                let colorArray = ["#ccff90", "#d50000", "#304ffe", "#ffab00", "#80d8ff", "#00c853", "#84ffff", "#00bfa5", "#00c853", "#b9f6ca", "#64dd17", "#ccff90", "#aeea00", "#f4ff81", "#ffd600", "#ffff8d", "#ffab00", "#ff6d00", "#ff3d00", "#d50000", "#ff8a80", "#c51162", "#ff80ab", "#aa00ff", "#ea80fc", "#6200ea", "#b388ff", "#304ffe", "#8c9eff"];
                let catColorEl;
                let i;
                let firstColor;
                let removeColor;
        </script>
    <div id="divTitre">
        <h1><a href="utilisateur.php">PRO-G</a></h1>
    </div>
    
    
    <div id="divHautDroite">
        <h1>Mes Projets</h1>
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
    // CODE RECHERCHE FABIEN CODE RECHERCHE FABIEN CODE RECHERCHE FABIEN CODE RECHERCHE FABIEN CODE RECHERCHE FABIEN CODE RECHERCHE FABIEN CODE RECHERCHE FABIEN
    if (isset($_POST['cherche']) == true){
        $recherche = htmlentities($_POST['cherche'], ENT_QUOTES); //Récupère la recherche si il y en a une
    
        try {
            $pdo = new PDO(DB_DRIVER . ":host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_LOGIN, DB_PASS, DB_OPTIONS);
            
            $user_id = $_SESSION["ut_id"]; //Récupérer l'id user au moment l'arrivée sur le projet
    
            $requete = "SELECT *
            FROM `pg_asso_projets_taches_utilisateurs`
            JOIN pg_asso_taches_utilisateurs ON `pg_asso_taches_utilisateurs`.`asso_tu_id` = `pg_asso_projets_taches_utilisateurs`.`asso_ptu_tu_id`
            JOIN `pg_taches` ON `pg_taches`.`tch_id` = `pg_asso_taches_utilisateurs`.`asso_tu_tache_id`
            JOIN `pg_utilisateurs` ON `pg_utilisateurs`.`ut_id` = `pg_asso_taches_utilisateurs`.`asso_tu_utilisateur_id`
            JOIN pg_asso_projets_taches ON `pg_asso_projets_taches`.`asso_pt_id` = `pg_asso_projets_taches_utilisateurs`.`asso_ptu_pt_id`
            JOIN `pg_projets` ON `pg_projets`.`prj_id` = `pg_asso_projets_taches`.`asso_pt_projet_id`
            WHERE `pg_projets`.`prj_nom` LIKE '%$recherche%' AND `pg_asso_taches_utilisateurs`.`asso_tu_utilisateur_id` = :user_id "; 
            $prepare = $pdo->prepare($requete);
            $prepare->execute(array(
                ':user_id' => $user_id //La variable de l'user ID
            ));
    
            $resultat = $prepare->fetchAll();
            $resultat = unique_multidim_array($resultat, 'prj_nom');//FONCTION DE TRI
    
            if (isset($resultat[0]) == true){ //Check si résultat est définie ou pas     
          
                $count = count($resultat);
                echo('<div class="divBoutonsSelection">
                      <h2>Résultat de votre recherche :</h2>
                      <div>');
                foreach($resultat as $key => $value){
                    //Affiche le résultat une fois trié                    
                        $prjNomRechercheFabien = $value['prj_nom'];
                        $prjIdRechercheFabien = $value['prj_id'];
                        echo('
                        <form action="projet.php" method="POST">
                        <button name="projet" type="submit" value="'.$prjIdRechercheFabien.'">'.$prjNomRechercheFabien.'</button>
                        </form>');
                }//End for du count général
                echo('</div></div>');
            }//End if si on a trouvé des infos dans la DB 
            else{
                echo("<div class='divBoutonsSelection'>
                    <h2>Aucun projet ne correspond à votre recherche</h2>
                    </div>"); //Message quand le projet n'est pas trouvé
            }
        } catch (PDOException $e) {
            // en cas d'erreur, on récup et on affiche, grâce à notre try/catch
            exit("❌🙀💀 OOPS :\n" . $e->getMessage());
        }  
    }

    // CODE CEDRIC GANTT CODE CEDRIC GANTT CODE CEDRIC GANTT CODE CEDRIC GANTT CODE CEDRIC GANTT CODE CEDRIC GANTT CODE CEDRIC GANTT CODE CEDRIC GANTT
    if(isset($_REQUEST['projet'])){
            $pdo = new PDO(DB_DRIVER . ":host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_LOGIN, DB_PASS, DB_OPTIONS);

            $prjID = $_REQUEST['projet']; // A VARIABILISER QUAND LA FONCTION DE RECHERCHER SERA LA 
           //récupérer les dates du projet au format string
           $requete = "SELECT * FROM `pg_projets` WHERE `prj_id`= :prj_id";
           $prepare = $pdo->prepare($requete);
           $prepare->execute(array(
               ':prj_id' => $prjID
               ));
           $prepare = $prepare->fetch();
           $date_debut_projet = $prepare['prj_date_debut'];
           $date_fin_projet = $prepare['prj_date_fin'];
           $projet_id=$prepare['prj_id'];
           echo("<div id='mainProjetTitre'><h1>".$prepare['prj_nom']."</h1></div>");
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
        
           //affichage des semaines sur la première ligne du tableau
           //récupérer le premier jour du projet 
           $nom_jour_debut_projet = $date_debut_projet_format-> format('N');
           $nb_a_remplir_debut = 8-$nom_jour_debut_projet ;
           //recuperer le nb de semaines du projet
           $date_deb = new DateTime($date_debut_projet);
           $date_fin = new DateTime($date_fin_projet);
           $nb_semaine_projet = floor($date_deb->diff($date_fin)->days / 7)+1;
           $jour_fin_projet_sem = $date_fin_projet_format-> format('N');
           $jour_fin_projet_sem_format = intval($jour_fin_projet_sem);
           if ($jour_fin_projet_sem_format<4){$nb_semaine_projet++;}
        
           //on rempli les 7 premiers champs vides pour arriver au calendrier
                echo("<div class='tableMainProjet' id='tableSemaines'><button id='btnEchelleTempsMois'>Changer l'échelle de temps vers l'affichage des mois</button><table><thead><tr>
                <th rowspan=2>Nom de tache</th>
                <th rowspan=2>Acteurs de tache</th>
                <th rowspan=2>Date début</th>
                <th rowspan=2>Date fin</th>
                <th rowspan=2>Délai</th>
                <th rowspan=2>Avancement</th>
                <th rowspan=2>Catégorie</th>");
            //on commence à afficher la semaine1 sur les premiers jours du projet
                echo("<th colspan=".$nb_a_remplir_debut.">semaine 1</th>");
            //puis on affiche le reste des semaines
                for ($n=2; $n<=$nb_semaine_projet; $n++){
                    echo("<th colspan=7>semaine ".$n."</th>");
                }
                echo("</tr><tr>");
                
           //affichage de la partie en-tête de GANTT dans un tableau
           function afficheCalendrier($debut_jour, $debut_mois, $debut_annee, $fin_jour, $fin_mois, $fin_annee){
               $debut_date = mktime(0, 0, 0, $debut_mois, $debut_jour, $debut_annee);
               $fin_date = mktime(0, 0, 0, $fin_mois, $fin_jour, $fin_annee);
               $jourEN = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
               $jourFR   = array("Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi", "Dimanche");
               $moisEN = array("January", "February", "March", "April", "May", "Jule", "July", "August", "September", "October", "November", "December");
               $moisFR = array("Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Decembre");
               //echo("<tr><th>Nom de tache</th>");
               //echo("<th>Acteurs de tache</th>");
               //echo("<th>Date début</th>");
               //echo("<th>Date fin</th>");
               //echo("<th>Delai</th>");
               //echo("<th>Avancement</th>");
               //echo("<th>Catégorie tache</th>");
               for($i = $debut_date; $i <= $fin_date; $i+=86400){
                   $date = date("l d F",$i);
                   $date = str_replace($jourEN, $jourFR, $date);
                   $date = str_replace($moisEN, $moisFR, $date);
                   echo ("<th>".$date."</th>") ;
               }
               echo("</tr></thead>");
           }
        
        afficheCalendrier($jour_debut_projet,$mois_debut_projet,$annee_debut_projet,$jour_fin_projet,$mois_fin_projet,$annee_fin_projet);
        
           // recuperer les infos de toutes les taches du projet
           $requete = "SELECT * FROM `pg_taches` 
                       WHERE `tch_id` IN(SELECT `asso_pt_tache_id` FROM `pg_asso_projets_taches`
                       WHERE `asso_pt_projet_id` = :asso_pt_projet_id)";
           $prepare = $pdo->prepare($requete);
           $prepare->execute(array(
               ':asso_pt_projet_id'=> $projet_id
           ));
           $entree = $prepare->fetchAll();
           $entreeLength = count($entree);

           for ($i = 1; $i< $entreeLength; $i++){
               $nom_tache=$entree[$i]['tch_nom'];
               $date_debut_tache=$entree[$i]['tch_date_debut'];
               $date_fin_tache=$entree[$i]['tch_date_fin'];
               $delai_tache=$entree[$i]['tch_delai'];
               $avancement_tache=$entree[$i]['tch_avancement'];
               $duree_tache=$entree[$i]['tch_duree'];
               $categorie_tache=$entree[$i]['tch_categorie'];
               $tache_id=$entree[$i]['tch_id'];
        
               //recuperer les acteurs de la tache en cours
                $requete2 = "SELECT * FROM `pg_utilisateurs` 
                                WHERE `ut_id` IN (SELECT `asso_tu_utilisateur_id` FROM `pg_asso_taches_utilisateurs`
                                WHERE `asso_tu_tache_id` =:tache_id)";                    
               $prepare2 = $pdo->prepare($requete2);
               $prepare2->execute(array(
                  ':tache_id'=>$tache_id
               ));
               
               $liste_acteurs='';
               while ($donnee = $prepare2->fetch()){
        
                   $acteur = $donnee['ut_nom'];
                   $liste_acteurs=$liste_acteurs." ".$acteur;
               }
        
               //continuer à construire le tableau avec toutes les infos recuperees sauf la partie calendrier
               echo("<tr><td>".$nom_tache."</td><td>".$liste_acteurs."</td><td>".$date_debut_tache.
               "</td><td>".$date_fin_tache."</td><td>".$delai_tache."</td><td>".$avancement_tache."%</td><td>".$categorie_tache."</td>");
        
               //construction du calendrier
               // 1° on calcul le nombre de jour entre le début du projet et le début de la tache en cours
                   $date_debut_projet_init = strtotime($date_debut_projet);
                   $date_debut_tache_init = strtotime($date_debut_tache);
                   $date_fin_projet_init = strtotime($date_fin_projet);
                   $date_fin_tache_init = strtotime($date_fin_tache);
        
                   // On récupère la différence de timestamp entre les 2 précédents
                   $nbJoursTimestamp = $date_debut_tache_init - $date_debut_projet_init;
        
                   // ** Pour convertir le timestamp (exprimé en secondes) en jours **
                   // On sait que 1 heure = 60 secondes * 60 minutes et que 1 jour = 24 heures donc :
                   $nbJours = $nbJoursTimestamp/86400; // 86 400 = 60*60*24 
        
               //2° on rempli de cases vides jusqu'à ce que la tache commence
               for ($n=0; $n<$nbJours; $n++){
                   echo("<td><div style='display:block; width:100%; height:100%;'></div></td>");
               }
               //3° on rempli de cases pleines sur la durée de la tache
               //on verifie que la date de debut de la tache est bien posterieure au debut du projet
                if ($date_debut_tache_init<$date_debut_projet_init ){
                    echo("<td colspan=5>!Incohérence dates- la tâche commence avant le projet</td>");
                //on verifie que la date de fin de la tache est bien anterieure a la fin du projet
                } else if($date_fin_projet_init<$date_fin_tache_init){
                    echo("<td colspan=5>!Incohérence dates- la tâche finit après le projet</td>");
                } else {
                    for ($n=0; $n<$duree_tache; $n++){
                        echo("<td  class='".$categorie_tache."'><a style='display:block; width:100%; height:100%;' href='projet.php?chercheTache=".$nom_tache."&projet=".$prjID."&tch=".$tache_id."'></a></td>");
                    }
                }

                //4° on rempli de cases vides jusqu'a la fin du projet
                $nb_jour_projet = $date_deb->diff($date_fin)->days;
                $reste_jour = $nb_jour_projet-$nbJours-$duree_tache+1;

                for ($n=0; $n<$reste_jour; $n++){
                    echo("<td><div style='display:block; width:100%; height:100%;'></td>");
                }
           } echo("</tr></table></div>");



// GANTT MOIS GANTT MOIS GANTT MOIS GANTT MOIS GANTT MOIS GANTT MOIS GANTT MOIS GANTT MOIS GANTT MOIS GANTT MOIS GANTT MOIS GANTT MOIS GANTT MOIS GANTT MOIS GANTT MOIS GANTT MOIS
           $connexion = new PDO(DB_DRIVER . ":host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_LOGIN, DB_PASS, DB_OPTIONS);
           $prjID = $_REQUEST['projet'];
            //récupérer les dates du projet au format string
            $requete = "SELECT * FROM `pg_projets` WHERE `prj_id`= :prj_id";
            $prepare = $connexion->prepare($requete);
            $prepare->execute(array(
                ':prj_id' => $prjID
                ));
            $prepare = $prepare->fetch();
            $date_debut_projet = $prepare['prj_date_debut'];
            $date_fin_projet = $prepare['prj_date_fin'];
            $projet_id=$prepare['prj_id'];

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
            $jour_fin_projet_sem = $date_fin_projet_format-> format('N');
            $jour_fin_projet_format = intval($jour_fin_projet);
            $mois_fin_projet_format = intval($mois_fin_projet);
            $annee_fin_projet_format = intval($annee_fin_projet); 
            $jour_fin_projet_sem_format = intval($jour_fin_projet_sem);

            //affichage des mois sur la première ligne du tableau
            //récupérer le premier jour du projet 
            $numero_jour_debut_projet = $date_debut_projet_format-> format('j');
            $numero_mois_en_cours = $date_debut_projet_format-> format('n');
            $nb_jour_mois='';

            //determiner le nb de jour du mois de debut
            if($numero_mois_en_cours==4 or $numero_mois_en_cours==6 or $numero_mois_en_cours==9 or $numero_mois_en_cours==11){
                $nb_jour_mois=31;
            } else if ($numero_mois_en_cours==2){
                $nb_jour_mois=29;
            } else {$nb_jour_mois=32;}

            $nb_a_remplir_debut = $nb_jour_mois-$numero_jour_debut_projet ;
            //recuperer le nb de mois du projet
            $date_deb = new DateTime($date_debut_projet);
            $date_fin = new DateTime($date_fin_projet);
            function datediff($a,$b)
            {
            $date1 = intval(substr($a,0,4))*12+intval(substr($a,4,2));
            $date2 = intval(substr($b,0,4))*12+intval(substr($b,4,2));
            return abs($date1-$date2); //abs pour éviter les résultas négatifs suivant l'ordre des arguments de la fonction
            }
            $nb_mois_projet = 1+datediff($annee_debut_projet_format.$mois_debut_projet_format, $annee_fin_projet_format.$mois_fin_projet_format);

            //on rempli les 7 premiers champs vides pour arriver au calendrier
            echo("<div class='tableMainProjet' id='tableMois'><button id='btnEchelleTempsSemaines'>Changer l'échelle de temps vers l'affichage des semaines</button><table><thead><tr>
            <th rowspan=2>Nom de tache</th>
            <th rowspan=2>Acteurs de tache</th>
            <th rowspan=2>Date début</th>
            <th rowspan=2>Date fin</th>
            <th rowspan=2>Délai</th>
            <th rowspan=2>Avancement</th>
            <th rowspan=2>Catégorie</th>");
            //on commence à afficher le mois 1 sur les premiers jours du projet
            echo("<th colspan=".$nb_a_remplir_debut.">mois 1</th>");
            //puis on affiche le reste des mois
            for ($n=2; $n<=$nb_mois_projet; $n++){
                if ($numero_mois_en_cours==12){$numero_mois_en_cours=1;}else{$numero_mois_en_cours++;}
                if($numero_mois_en_cours==4 or $numero_mois_en_cours==6 or $numero_mois_en_cours==9 or $numero_mois_en_cours==11){
                    $nb_jour_mois=31;
                } else if ($numero_mois_en_cours==2){
                    $nb_jour_mois=29;
                } else {$nb_jour_mois=32;}
                $nb=$nb_jour_mois-1;
                echo("<th colspan=".$nb.">mois ".$n."</th>");
            }
            echo("</tr><tr>");

            //affichage des semaines sur la première ligne du tableau
            //récupérer le premier jour du projet 
            $nom_jour_debut_projet = $date_debut_projet_format-> format('N');
            $numero_jour_debut_projet = $date_debut_projet_format-> format('j');
            $nb_a_remplir_debut = 8-$nom_jour_debut_projet ;

            //recuperer le nb de semaines du projet
            $date_deb = new DateTime($date_debut_projet);
            $date_fin = new DateTime($date_fin_projet);

            $nb_semaine_projet = floor($date_deb->diff($date_fin)->days / 7)+1;
            if ($jour_fin_projet_sem_format<4){$nb_semaine_projet++;}

            //on commence à afficher la semaine 1 sur les premiers jours du projet
            echo("<th colspan=".$nb_a_remplir_debut.">semaine 1</th>");

            //puis on affiche le reste des semaines 
            for ($n=2; $n<=$nb_semaine_projet; $n++){
                echo("<th colspan=7>semaine ".$n."</th>");
            }

            echo("</tr></thead>");

            // recuperer les infos de toutes les taches du projet
            $requete = "SELECT * FROM `pg_taches` 
                        WHERE `tch_id` IN(SELECT `asso_pt_tache_id` FROM `pg_asso_projets_taches`
                        WHERE `asso_pt_projet_id` = :asso_pt_projet_id)";
            $prepare = $connexion->prepare($requete);
            $prepare->execute(array(
                ':asso_pt_projet_id'=> $projet_id
            ));

            $tache_id ='';
            
            while ($entree =$prepare->fetch()){
            if($entree['tch_id'] != 1) {
                $nom_tache=$entree['tch_nom'];
                $date_debut_tache=$entree['tch_date_debut'];
                $date_fin_tache=$entree['tch_date_fin'];
                $delai_tache=$entree['tch_delai'];
                $avancement_tache=$entree['tch_avancement'];
                $duree_tache=$entree['tch_duree'];
                $categorie_tache=$entree['tch_categorie'];
                $tache_id=$entree['tch_id'];

                $date_debut_tache_format = date_create_from_format('Y-m-j',$date_debut_tache);
                $jour_debut_tache = $date_debut_tache_format-> format('j');
                $jour_debut_tache_format = intval($jour_debut_tache);

                //recuperer les acteurs de la tache en cours
                $requete2 = "SELECT * FROM `pg_utilisateurs` 
                WHERE `ut_id` IN (SELECT `asso_tu_utilisateur_id` FROM `pg_asso_taches_utilisateurs`
                WHERE `asso_tu_tache_id` =:tache_id)";                    
                $prepare2 = $connexion->prepare($requete2);
                $prepare2->execute(array(
                    ':tache_id'=>$tache_id
                ));
                $liste_acteurs='';
                while ($donnee = $prepare2->fetch()){
                    $acteur = $donnee['ut_nom'];
                    $liste_acteurs=$liste_acteurs." ".$acteur;
                }

                //continuer à construire le tableau avec toutes les infos recuperees sauf la partie calendrier
                echo("<tr><td>".$nom_tache."</td><td>".$liste_acteurs."</td><td>".$date_debut_tache.
                "</td><td>".$date_fin_tache."</td><td>".$delai_tache."</td><td>".$avancement_tache."</td><td>".$categorie_tache."</td>");

                //construction du calendrier
                    // 1° on calcul le nombre de jour entre le début du projet et le début de la tache en cours
                    $date_debut_projet_init = strtotime($date_debut_projet);
                    $date_fin_projet_init = strtotime($date_fin_projet);
                    $date_debut_tache_init = strtotime($date_debut_tache);
                    $date_fin_tache_init = strtotime($date_fin_tache);

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
                //on verifie que la date de debut de la tache est bien posterieure au debut du projet
                if ($date_debut_tache_init<$date_debut_projet_init ){
                    echo("<td colspan=5>!Incohérence dates- la tâche commence avant le projet</td>");
                //on verifie que la date de fin de la tache est bien anterieure a la fin du projet
                } else if($date_fin_projet_init<$date_fin_tache_init){
                    echo("<td colspan=5>!Incohérence dates- la tâche finit après le projet</td>");
                } else {
                    for ($n=0; $n<$duree_tache; $n++){
                        echo("<td  class='".$categorie_tache."'><a style='display:block; width:100%; height:100%;' href='projet.php?chercheTache=".$nom_tache."&projet=".$prjID."&tch=".$tache_id."'></a></td>");
                    }
                }
                ?>
                    <script>
                        catColorEl = document.getElementsByClassName('<?php echo(html_entity_decode($categorie_tache));?>');
                        firstColor = colorArray[0];
                        for(i=0; i < catColorEl.length; i++){
                            catColorEl[i].style.backgroundColor = firstColor;
                        }
                        removeColor = colorArray.indexOf(firstColor);
                        colorArray.splice(removeColor, 1);
                    </script>
                <?php
                //4° on rempli de cases vides jusqu'a la fin du projet
                $nb_jour_projet = $date_deb->diff($date_fin)->days;
                $reste_jour = $nb_jour_projet-$nbJours-$duree_tache+1;

                for ($n=0; $n<$reste_jour; $n++){
                    echo("<td></td>");
                }
                
                
            }
            }
             echo("</tr></table></div>");
           // FIN GANTT MOIS FIN GANTT MOIS FIN GANTT MOIS FIN GANTT MOIS FIN GANTT MOIS FIN GANTT MOIS FIN GANTT MOIS FIN GANTT MOIS FIN GANTT MOIS FIN GANTT MOIS
            echo("
                <form action='projet.php' method='POST' class='cacheMenuProjet'>
                    <input type='search' id='chercheTache' name='chercheTache' placeholder='Nom de la tâche' required>
                    <input type='hidden' value='".$prjID."' name='projet'>
                    <input type='hidden' value='".$tache_id."' name='tch'>
                    </br>
                    <button>Rechercher une tâche</button>
                </form>
                <form action='projet.php' method='POST' class='cacheMenuProjet'>
                    <input type='search' id='chercheCat' name='chercheCat' placeholder='Catégorie' required> 
                    <input type='hidden' value='".$prjID."' name='projet'>
                    <input type='hidden' value='".$tache_id."' name='tch'>
                    </br>
                    <button>Rechercher une catégorie</button>
                </form>
            ");


            //FONCTION RECHERCHE TACHE
            if (isset($_REQUEST['chercheTache']) == true){
                $prjID = $_REQUEST['projet'];
                $recherche = htmlentities($_REQUEST['chercheTache'], ENT_QUOTES); //Récupère la recherche si il y en a une
            
                try {
                    $user_id = $_SESSION["ut_id"]; //Récupérer l'id user au moment du log
                    $prjID = $_REQUEST['projet']; //Récupérer l'id user au moment l'arrivée sur le projet
            
                    $requete = "SELECT *
                    FROM `pg_asso_projets_taches_utilisateurs`
                    JOIN pg_asso_taches_utilisateurs ON `pg_asso_taches_utilisateurs`.`asso_tu_id` = `pg_asso_projets_taches_utilisateurs`.`asso_ptu_tu_id`
                    JOIN `pg_taches` ON `pg_taches`.`tch_id` = `pg_asso_taches_utilisateurs`.`asso_tu_tache_id`
                    JOIN `pg_utilisateurs` ON `pg_utilisateurs`.`ut_id` = `pg_asso_taches_utilisateurs`.`asso_tu_utilisateur_id`
                    JOIN pg_asso_projets_taches ON `pg_asso_projets_taches`.`asso_pt_id` = `pg_asso_projets_taches_utilisateurs`.`asso_ptu_pt_id`
                    JOIN `pg_projets` ON `pg_projets`.`prj_id` = `pg_asso_projets_taches`.`asso_pt_projet_id`
                    WHERE `pg_taches`.`tch_nom` LIKE '%$recherche%' AND `pg_asso_taches_utilisateurs`.`asso_tu_utilisateur_id` = :user_id AND `pg_asso_projets_taches`.`asso_pt_projet_id` = :projet_id "; 
            
                    $prepare = $pdo->prepare($requete);
                    $prepare->execute(array(
                        ':user_id' => $user_id, //La variable de l'user ID
                        ':projet_id' => $prjID //La variable du projet dans lequel on est
                    ));
            
                    $resultat = $prepare->fetchAll();
                    echo("<div class='divTache'>");
                    if (isset($resultat[0]) == true  && $resultat[0]['tch_id'] != 1){ //Check si résultat est définie ou pas
                        $count = count($resultat);
                        for($i=0; $i<$count ; $i++){
                            $tache = $resultat[$i];
                            echo('
                            <div class="tache">
                            <h2>'.$tache['tch_nom'].'</h2>
                            ');
                            
                            if(isset($_REQUEST['tch'])){
                                $tch = $tache['tch_id'];
                                
                                $requete = "SELECT * FROM `pg_taches`
                                            WHERE `tch_id` = $tch;";
                                $prepare = $pdo->prepare($requete);
                                $prepare->execute();
                                $resultatNouvelleTache = $prepare->fetchAll();
                        ?>
                                <form action="projet.php" method="POST">
                                    <input type="text" name="tch_nom" value="<?php echo $resultatNouvelleTache[0]['tch_nom']; ?>" required>
                                    <label for="tch_date_debut">Date de début</label>
                                    <input type="date" name="tch_date_debut" value="<?php echo $resultatNouvelleTache[0]['tch_date_debut']; ?>" required>
                                    <label for="tch_date_fin">Date de fin</label>
                                    <input type="date" name="tch_date_fin" value="<?php echo $resultatNouvelleTache[0]['tch_date_fin']; ?>" required>
                                    <input type="text" name="tch_duree" value="<?php echo $resultatNouvelleTache[0]['tch_duree']; ?>" required>
                                    <label for="tch_delai">Date de délai</label>
                                    <input type="date" name="tch_delai" value="<?php echo $resultatNouvelleTache[0]['tch_delai']; ?>" required>
                                    <input type="text" name="tch_categorie" value="<?php echo $resultatNouvelleTache[0]['tch_categorie']; ?>" required>
                                    <input type="text" name="tch_id" value="<?php echo $resultatNouvelleTache[0]['tch_id']; ?>" hidden>
                                    <label for="tch_avancement">Avancement</label>
                                    <input type="number" name="tch_avancement" value="<?php echo $resultatNouvelleTache[0]['tch_avancement']; ?>" required>
                                    <input type="hidden" name="chercheTache" value="<?php echo $nom_tache?>">
                                    <input type="hidden" name="projet" value="<?php echo $prjID?>">
                                    <input type="hidden" name="tch" value="<?php echo $tache_id?>">
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
                                    ?>
                                        <script type="text/javascript">
                                            location.href = 'projet.php?projet=<?php echo($prjID); ?>';
                                        </script>
                                    <?php
                                    }
                                }
                                catch (PDOException $e){
                                    exit("❌🙀❌ OOPS :\n" . $e->getMessage());
                                }
                    
                            }
                            echo('</div>');
                            
                        }
                    }
                    else{
                        echo("<h3>Aucune tâche ne correspond à votre recherche</h3>"); //Message quand le projet n'est pas trouvé
                    }
                    echo("</div>");
                } catch (PDOException $e) {
                    // en cas d'erreur, on récup et on affiche, grâce à notre try/catch
                    exit("❌🙀💀 OOPS :\n" . $e->getMessage());
                }  
            }

            // //FONCTION RECHERCHE CATEGORIE
            if (isset($_POST['chercheCat']) == true){
                $recherche = $_POST['chercheCat']; //Récupère la recherche si il y en a une
            
                try {
                    $user_id = $_SESSION["ut_id"];
                    $prjID = $_REQUEST['projet']; //Récupérer l'id user au moment l'arrivée sur le projet <------ 🍆💦🍑👩🏻🤦🏻‍♀️A VARIABILISER ABOSLUMENT !🍆💦🍑👩🏻🤦🏻‍♀️
            
            
                    $requete = "SELECT *
                    FROM `pg_asso_projets_taches_utilisateurs`
                    JOIN pg_asso_taches_utilisateurs ON `pg_asso_taches_utilisateurs`.`asso_tu_id` = `pg_asso_projets_taches_utilisateurs`.`asso_ptu_tu_id`
                    JOIN `pg_taches` ON `pg_taches`.`tch_id` = `pg_asso_taches_utilisateurs`.`asso_tu_tache_id`
                    JOIN `pg_utilisateurs` ON `pg_utilisateurs`.`ut_id` = `pg_asso_taches_utilisateurs`.`asso_tu_utilisateur_id`
                    JOIN pg_asso_projets_taches ON `pg_asso_projets_taches`.`asso_pt_id` = `pg_asso_projets_taches_utilisateurs`.`asso_ptu_pt_id`
                    JOIN `pg_projets` ON `pg_projets`.`prj_id` = `pg_asso_projets_taches`.`asso_pt_projet_id`
                    WHERE `pg_taches`.`tch_categorie` LIKE '%$recherche%' AND `pg_asso_taches_utilisateurs`.`asso_tu_utilisateur_id` = :user_id AND `pg_asso_projets_taches`.`asso_pt_projet_id` = :projet_id "; 
            
                    $prepare = $pdo->prepare($requete);
                    $prepare->execute(array(
                        ':projet_id' => $prjID, //La variable du projet dans lequel on est
                        ':user_id' => $user_id //La variable du projet dans lequel on est
                    ));
            
                    $resultat = $prepare->fetchAll();
                    echo("<div class='divTache'>");
                    if (isset($resultat[0]) == true && $resultat[0]['tch_id'] != 1){ //Check si résultat est définie ou pas
                        $count = count($resultat);
                        for($i=0; $i<$count ; $i++){
                            $tache = $resultat[$i];
                            
                                echo('
                                <div class="tache">
                                <h2>'.$tache['tch_nom'].'</h2>
                                ');
                                
                                if(isset($_REQUEST['tch'])){
                                    $tch = $tache['tch_id'];
                                    
                                    $requete = "SELECT * FROM `pg_taches`
                                                WHERE `tch_id` = $tch;";
                                    $prepare = $pdo->prepare($requete);
                                    $prepare->execute();
                                    $resultatNouvelleTache = $prepare->fetchAll();
                            ?>
                                    <form action="projet.php" method="POST">

                                        <input type="text" name="tch_nom" value="<?php echo $resultatNouvelleTache[0]['tch_nom']; ?>" required>
                                        <label for="tch_date_debut">Date de début</label>
                                        <input type="date" name="tch_date_debut" value="<?php echo $resultatNouvelleTache[0]['tch_date_debut']; ?>" required>
                                        <label for="tch_date_fin">Date de fin</label>
                                        <input type="date" name="tch_date_fin" value="<?php echo $resultatNouvelleTache[0]['tch_date_fin']; ?>" required>
                                        <input type="text" name="tch_duree" value="<?php echo $resultatNouvelleTache[0]['tch_duree']; ?>" required>
                                        <label for="tch_delai">Date de délai</label>
                                        <input type="date" name="tch_delai" value="<?php echo $resultatNouvelleTache[0]['tch_delai']; ?>" required>
                                        <input type="text" name="tch_categorie" value="<?php echo $resultatNouvelleTache[0]['tch_categorie']; ?>" required>
                                        <input type="text" name="tch_id" value="<?php echo $resultatNouvelleTache[0]['tch_id']; ?>" hidden>
                                        <label for="tch_avancement">Avancement</label>
                                        <input type="number" name="tch_avancement" value="<?php echo $resultatNouvelleTache[0]['tch_avancement']; ?>" required>
                                        <input type="hidden" name="chercheTache" value="<?php echo $nom_tache?>">
                                        <input type="hidden" name="projet" value="<?php echo $prjID?>">
                                        <input type="hidden" name="tch" value="<?php echo $tache_id?>">
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
                                        ?>
                                            <script type="text/javascript">
                                                location.href = 'projet.php?projet=<?php echo($prjID); ?>';
                                            </script>
                                        <?php
                                        }
                                    }
                                    catch (PDOException $e){
                                        exit("❌🙀❌ OOPS :\n" . $e->getMessage());
                                    }
                        
                                }
                                echo('</div>');
                            }
                        
                    }
                    else{
                        echo("<h3>Aucune tâche ne correspond à votre recherche</h3>"); //Message quand le projet n'est pas trouvé
                    }
                    echo('</div>');
                } catch (PDOException $e) {
                    // en cas d'erreur, on récup et on affiche, grâce à notre try/catch
                    exit("❌🙀💀 OOPS :\n" . $e->getMessage());
                }  
            }
    } 
    
    // DAMIEN DAMIEN DAMIEN

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
            $resultatProjetShort = unique_multidim_array($resultatProjet, 'prj_nom');
        }
        catch (PDOException $e){
            exit("❌🙀❌ OOPS :\n" . $e->getMessage());
        }
        ?>
        <form action="projet.php" methode="POST" class="formDamien cacheMenuProjet">
            <label for="projetDamien">Afficher le projet</label>
            <select name="projet">
                <?php
                $resultatProjetShortLenght = count($resultatProjetShort);
                foreach($resultatProjetShort as $key => $value){
                    echo "<option value=". $value['prj_id'] .">" . $value['prj_nom'] . "</option>";
                }
                ?>
            </select>
            <button value="Afficher">Afficher</button>
        </form>
    <?php    
    
    function unique_multidim_array($array, $key) {
        $temp_array = array();
        $i = 0;
        $key_array = array();
       
        foreach($array as $val) {
            if (!in_array($val[$key], $key_array)) {
                $key_array[$i] = $val[$key];
                $temp_array[$i] = $val;
            }
            $i++;
        }
        return $temp_array;
    } 
    ?>        
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

    
</body>
</html>