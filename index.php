<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styleLP.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <script src="prefix.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <title>PRO-G</title>
</head>
<body>
    <!-- CHECK WINDOW SIZE -->
    <script>
        $(function(){
            if ( $(window).width() > 768) {     
            //Desktop
            console.log('Grand écran');
            $("#haut").addClass('animate__fadeInLeft');
            console.log('fadeInLeft Added');
            $("#bas").addClass('animate__fadeInRight');
            console.log('fadeInRight Added');
            }
            else {
            //Mobile
            console.log('Petit écran');
            $("#haut").addClass('animate__fadeInDown');
            console.log('fadeInDown Added');
            $("#bas").addClass('animate__fadeInUp');
            console.log('fadeInUpAdded');
            }
        });
    </script>


    <!-- PHP coDB / User Check-->
    <?php
    require('config.php');
    session_start();

    if (isset($_POST['ut_nom'])){

        $ut_nom = htmlentities($_REQUEST['ut_nom'],ENT_QUOTES);
        $_SESSION['ut_nom'] = $ut_nom;
        $ut_mdp = htmlentities($_REQUEST['ut_mdp'],ENT_QUOTES);

        try{
            $pdo = new PDO(DB_DRIVER . ":host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_LOGIN, DB_PASS, DB_OPTIONS);
            $requete = "SELECT * FROM `pg_utilisateurs` WHERE `ut_nom` = '$ut_nom' AND `ut_mdp` ='".hash('sha256', $ut_mdp)."'";
            $prepare = $pdo->prepare($requete);
            $prepare->execute();
            $res = $prepare->rowCount();
            if (($res) == 1) {
                $user = $prepare->fetchAll();
                $_SESSION["ut_type"] = $user[0]['ut_type'];
                $_SESSION["ut_id"] = $user[0]['ut_id'];
                // vérifier si l'utilisateur est un administrateur ou un utilisateur
                if ($_SESSION["ut_type"] == 'admin') {
                    header('location: admin/utilisateur.php');
                }
                else{
                    header('location: index.php');
                }

            }
            else{
                $message = "Le nom d'utilisateur ou le mot de passe est incorrect.";
            }
        }
        catch (PDOException $e){
            exit("❌🙀❌ OOPS :\n" . $e->getMessage());
        }
    }
    ?>

    <!-- CORPS SITE -->
    <div id="haut" class="animate__animated gauche" >
        <h1>PRO-<span>G</span></h1>
        <div class="regle">
            <h2>Un outil de gestion de projet qui se sert de la puissance des diagrammes GANTT</h2>
        </div>
    </div>
    <div id="bas" class="animate__animated droite">
        
        <div class="login">
            <form action="" method="POST">
                <label for="idLogin">Login</label>
                <input type="text" name="ut_nom" id="idLogin" class="animate__animated animate__shakeX">
                <label for="mdpLogin">Mot de passe</label>
                <input type="password" name="ut_mdp" id="mdpLogin">
                <button id="btn">CONNEXION</button>
            </form>
        </div>
    </div>
</body>
</html>