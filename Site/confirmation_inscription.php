<?php
session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
   
  
  
  /*!!!!!!! ajouter ici la référence du header et bas de page connecté*/
  
  
  
  
  
} else {
     /*!!!!!!! ajouter ici la référence du header et bas de page déconnecté*/
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Health Foundation</title>
    <link rel="stylesheet" media="screen" href="design.css" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body>

<header class="headerNonConnecte" >
    <div class = logoPrincipal >
        <img src="Images/HF4.png" class="logo" alt="Logo de Health Foundation">
        <h1 id="Titre"><a href="index.php">Health Foundation</a></h1>
    </div>
            <div class="partieDroite">
            <nav id="menu">
                <ul>
                    <li><a href="index.php"> Accueil</a></li>
                    <li><a href="apropos.php">À propos </a></li>
                    <li><a href="connexion.php">Connexion</a></li>
                    <li><a href="inscription.php">Inscription</a></li>

                </ul>
            </nav>
            <div class=" logoLangue">
                <a href="index.php"><img src="Images/logoAnglais.jpg" class="logo" alt="Drapeau Anglais"></a>
                <a href="index.php"><img src="Images/logoFrance.jpg" class="logo" alt="Drapeau francais"></a>
            </div>
            </div>

</header>
<div class="centrer_bloc">
<div class="confirmationInscription">
        <h2>Confirmation de création de compte </h2>
    <p>Un mail a été envoyé à l’adresse <?php $_POST['mail']?>. Pour confirmer votre inscription, veuillez vérifier votre boite de réception</p>
</div>
</div>

<div class="footer">
    <footer class="footerNonConnecte">
        <div class="menuBas">
            <a href="cgu.php" target="_blank"> CGU</a>
            <a href="faq.php"> FAQ/Aide</a>
            <a href="contact.php"> Contact</a>
            <div id="connexion"><a href="connexion.php" >Connexion</a></div>
            <p>©Copyright Health Foundation, tout droits réservés</p>
        </div>
    </footer>
</div>

</body>
</html>
