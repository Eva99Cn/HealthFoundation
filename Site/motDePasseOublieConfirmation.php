<?php
session_start();
include('osQuery.php');
try{
	//connexion à la database
	//Pour les utilisateurs Mac : entrez cette ligne
	//$bdd = new PDO('mysql:host=localhost;dbname=health_foundation','root','root');
    //Pour windows entrez cette ligne
    
	if (getOS( $_SERVER['HTTP_USER_AGENT'])=='Windows' || getOS( $_SERVER['HTTP_USER_AGENT'])=='Linux')
	{
        $bdd = new PDO('mysql:host=localhost;dbname=health_foundation','root','');
        $bdd-> setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
	}
	elseif (getOS( $_SERVER['HTTP_USER_AGENT'])=='Mac')
	{
        $bdd = new PDO('mysql:host=localhost;dbname=health_foundation','root','root');
        $bdd-> setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
	}
}
catch(Exception $error)
{
	die('Erreur lors du chargement de la base de donnée : '.$error->getMessage().' Vérifiez dans le code source les instructions');
}

//Test d'arrivée sur le site du visiteur
if(!isset($_SESSION['isConnected']))
{
	$_SESSION['isConnected'] = false;
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
  <body class= "allPage">

    <header class="headerNonConnecte" >
            <div class = logoPrincipal >
                <img src="Images/HF4.png" class="logo" alt="Logo Health Foundation">
                <h1 ><a href="index.php" class="bigTitle">Health Foundation</a></h1>
            </div>
            <nav <?php  if(!$_SESSION['isConnected']) : ?> class="notConnectedNAV"<?php endif;?>
                 <?php  if($_SESSION['isConnected']&& !$_SESSION['admin']) : ?> class="connectedNAV"<?php endif;?> 
                 <?php  if($_SESSION['admin']) : ?> class="adminNAV"<?php endif;?>                                                              
                                                                                id="menu">
                <ul <?php  if($_SESSION['isConnected']&& !$_SESSION['admin']) : ?> class="connectedrightUL"<?php endif;?>
                    <?php  if($_SESSION['admin']) : ?> class="adminrightUL"<?php endif;?> >
                    <li><a href="index.php"> Accueil</a></li>
                                            <?php //Si l'utilisateur n'est pas un admin
					if(!$_SESSION['admin']) : ?>
                    <li><a href="aPropos.php">À propos </a></li>
                </ul>
                <ul <?php  if($_SESSION['isConnected']&& !$_SESSION['admin']) : ?> class="connectedLeftUL"<?php endif;?>>
					<?php endif;
                                            //Si l'utilisateur n'est pas connecté
					if(!$_SESSION['isConnected']) : ?>
                
                
                    <li><a href="connexion.php">Connexion</a></li>
                    <li><a href="inscription.php">Inscription</a></li>
					<?php endif;?>
					
					<?php //Si l'utilisateur est connecté
					if($_SESSION['isConnected']&& !$_SESSION['admin']) : ?> 
                    <li><a href="monCompte.php">Mon compte</a></li>
                    <li><a href="index.php?deconnexion=true.php">Se déconnecter</a></li>
                    <?php endif;?>
                    
                    <?php //Si l'utilisateur est connecté
                    if($_SESSION['isConnected']&& $_SESSION['admin']) : ?> 
                    
                    <li><a href="gestionUtilisateur.php">Gestion des utilisateurs</a></li>
                </ul>
                <ul <?php  if($_SESSION['admin']) : ?> class="adminLeftUL"<?php endif;?> >
                    <li><a href="gestionDesStructures.php">Gestion des structures</a></li>
                    <li><a href="index.php?deconnexion=true.php">Se déconnecter</a></li>
					<?php endif;?>
                </ul>
             
                <div class=" logoLangue">
                    <a href="index.php"><img src="Images/logoAnglais.jpg" class="logo" alt="Drapeau Anglais"></a>
                    <a href="index.php"><img src="Images/logoFrance.jpg" class="logo" alt="Drapeau francais"></a>
                </div>
            </nav>
            
    	</header>
    <div class="centrer_bloc">
    <div class="nouveauMdpc">
        <span>
          <a class="enteteNouveauMdpc"> Nouveau mot de passe </a>
          
        </span>
           
            <fieldset>
            <form action="index.php"  method="post"> 
             <h3> Votre mot de passe a bien été enregistré </h3>

            <input class="submitButtons"  type="submit" Value="Retour à l'accueil" name="nouveauMdpc"> 
        </form>
        </fieldset>
      </div>
  </div>
      
<footer id="footer">
            <div class="menuBas">
                <a href="cgu.php" target="_blank"> CGU</a>
                <a href="faq.php"> FAQ/Aide</a>
                <a href="contact.php"> Contact</a>
                <?php //Si l'utilisateur n'est pas connecté
				if(!$_SESSION['isConnected']) : ?> 
				<div id="footerButton"><a href="inscription.php" >S'inscrire</a></div>
				<?php endif;?>
				
				<?php //Si l'utilisateur est connecté
				if($_SESSION['isConnected']) : ?> 
				<div id="footerButton"><a href="index.php?deconnexion=true.php" >Déconnexion</a></div>
				<?php endif;?>
                <p>©Copyright Health Foundation, tout droits réservés</p>
            </div>
        </footer>
	  <script src="script.js"></script>

  </body>
</html>
