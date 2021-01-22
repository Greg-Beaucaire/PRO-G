<?php
$nom_tache = $_GET['nomTache'];
$date_debut_tache = $_GET['dateDebut'];
$date_fin_tache = $_GET['dateFin'];
$duree_tache = $_GET['duree'];
$categorie_tache = $_GET['categorie'];
$liste_acteurs = $_GET['acteurs'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Info tache <?php echo($nom_tache)?></title>
<?php

echo("Tache : ".$nom_tache."<br>Commence le : ".$date_debut_tache."<br>Se termine le :".$date_fin_tache."<br> Durée : ".$duree_tache." jours<br>Catégorie : ".$categorie_tache."<br>Acteurs : ".$liste_acteurs);

?>

<body>

