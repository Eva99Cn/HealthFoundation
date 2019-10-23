<?php

try{
	//connexion à la database
	$bdd = new PDO('mysql:host=localhost;dbname=health_foundation;charset=utf8','root','');
}
catch(Exception $error)
{
	die('Erreur lors du chargement de la base de donnée : '.$error->getMessage());
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
        <h1 id="Titre"><a href="accueil.html">Health Foundation</a></h1>
        </div>
            <div class="partieDroite">
            <nav id="menu">
                <ul>
                    <li><a href="accueil.html"> Accueil</a></li>
                    <li><a href="apropos.html">À propos </a></li>
                    <li><a href="connexion.html">Connexion</a></li>
                    <li><a href="inscription.html">Inscription</a></li>

                </ul>
            </nav>
            <div class=" logoLangue">
                <a href="accueil.html"><img src="Images/logoAnglais.jpg" class="logo" alt="Drapeau Anglais"></a>
                <a href="accueil.html"><img src="Images/logoFrance.jpg" class="logo" alt="Drapeau francais"></a>
            </div>
            </div>
      </header>
    <div class="centrer_bloc">
     <div class="Connexion">
        <span>
          <a class="enteteInscription" href="connexion.html"> Connexion </a>
          <a class="enteteInscription" href="inscription.html"> Inscription </a>
        </span>
         <h4>Email ou mot de passe incorrect</h4>
          <fieldset>
          <form action="verification_connexion.php" method="post"> 
          
          <label for="mail" id="email">Email</label>
          <input type="email" name="mail" id="mail" >
          <br>
          <label for="mdp">Mot de passe</label>
          <input type="password" name="mdp" id="mdp">
          <br>
          <input type="submit" Value="Suivant" name="Connexion">
        </form>
        </fieldset>
        <a id= mdp href="mot-de-passe-oublié.html">Mot de passe oublié</a>
      </div>
    </div>
<footer class="footerNonConnecte">
            <div class="menuBas">
                <a href="cgu.html" target="_blank"> CGU</a>
                <a href="faq.html"> FAQ/Aide</a>
                <a href="contact.html"> Contact</a>
                <div id="connexion"><a href="connexion.html" >Connexion</a></div>
                <p>©Copyright Health Foundation, tout droits réservés</p>
            </div>
        </footer>

  </body>
</html>
