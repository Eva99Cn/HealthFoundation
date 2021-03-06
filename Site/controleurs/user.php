<?php

/**
 * Le contrôleur :
 * - définit le contenu des variables à afficher
 * - identifie et appelle la vue
 */

/**
 * Contrôleur de l'utilisateur
 */

// on inclut le fichier modèle contenant les appels à la database

include('./modele/requests.user.php');
include('./modele/requests.resetpass.php');
include('./modele/requests.test.php');

// si la fonction n'est pas définie, on choisit d'afficher l'accueil
if (!isset($_GET['function']) || empty($_GET['function'])) {
    $function = "accueil";
} else {
    $function = $_GET['function'];
}
$database = connectdb();
switch ($function) {

    case 'accueil':
        //affichage de l'accueil
        $vue = "accueil";
        $title = "Accueil";
        break;

    case 'connexion':
        // inscription d'un nouvel utilisateur
        $vue = "connexion";
        $error = false;

        //Cette partie du code est appelée si le formulaire a été posté
        if (isset($_POST['Connexion'])) {
            if (!empty($_POST['mail']) || !empty($_POST['mdp'])) {
                $email = $_POST['mail'];
                /*TODO : Securiser le mail*/
                $password = hashPassword($_POST['mdp']);

                if (findUserConnexion($database, $email, $password) != array(null)) {

                    // Tout est ok, on peut connecter l'utilisateur
                    $user = findUserConnexion($database, $email, $password);
                    if (verifyAccount($database, $user)) {
                        $_SESSION['isConnected'] = true;
                        $_SESSION['userID'] = $user['id'];
                        $_SESSION['userType'] = $user['type'];
                        if ($_SESSION['userType'] == "Pilot") {
                            $_SESSION['pilotId'] = $user['id'];
                        }
                        header("Location:index.php?redirect=user");
                        exit();
                    } else {
                        $error = "Compte non validé";
                    }
                } else {
                    $error = "Le mot de passe ou le mail n'est pas valide";
                }
            } else {
                $error = "Vous devez remplir tout les champs";
            }
        }

        break;

    case 'inscription':
        $vue = "inscription";
        $error = false;
        if (isset($_POST['inscriptionP1'])) {
            if (isString($_POST['mail']) and isString($_POST['codeFormateur'])){
            if(isPassword($_POST['mdp']) and isPassword($_POST['mdp2'])) {
                $email = $_POST['mail'];
                $password = hashPassword($_POST['mdp']);
                $password2 = hashPassword($_POST['mdp2']);
                $codeformateur = $_POST['codeFormateur'];
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    if (!testEmailExist($database, $email)) {
                        if (passwordMatch($password, $password2)) {
                            //Inscription d'un formateur avec un code structure
                            if (testCodeStructure($database, $codeformateur) != array(null)) {

                                $_SESSION['signupCodeType'] = "structure";
                                $_SESSION['signupMail'] = $email;
                                $_SESSION['signupPassword'] = $password;
                                $structureId = getStructureByCode($database, $codeformateur);
                                $_SESSION['structureId'] = $structureId['id'];
                                $_SESSION['instructorId'] = $structureId['referent'];
                                
                                header('Location:index.php?redirect=user&function=inscriptionSuite');
                            }

                            // Inscription d'un pilote avec code formateur
                            else if (testCodeInstructor($database, $codeformateur) != array(null)) {
                                $_SESSION['signupCodeType'] = "instructor";
                                $_SESSION['signupMail'] = $email;
                                $_SESSION['signupPassword'] = $password;
                                $structureId =  getStructureByCode($database, $codeformateur);
                                $_SESSION['structureId'] = $structureId['id'];
                                $instructorId = getInstructorByCode($database, $codeformateur);
                                $_SESSION['instructorId'] = $instructorId['id'];
                                header('Location:index.php?redirect=user&function=inscriptionSuite');
                            } else {
                                $error = "Le code formateur est invalide";
                            }
                        } else {
                            $error = "Vos mots de passes ne correspondent pas!";
                        }
                    } else {
                        $error = "Adresse mail déjà utilisée !";
                    }
                } else {
                    $error = "Votre adresse mail n'est pas valide !";
                }
            }
            else{
                $error = "Votre mot de passe doit contenir plus de 8 caractères, une lettre minuscule, une lettre masjuscule et un chiffre";
            }
        }else {
                $error = "Tous les champs doivent être complétés !";
            }
        }

        break;

    case 'inscriptionSuite':
        $vue = "inscriptionSuite";
        $error = false;


        //Test d'arrivée sur le site du visiteur
        if (!isset($_SESSION['signupCodeType'])) {
            header("Location:index.php?redirect=user&function=inscription");
        }
        if (isset($_POST['inscriptionSuite'])) {
            if (isString($_POST['nom']) && isString($_POST['prenom']) && isString($_POST['date']) && isString($_POST['codePostal'])) {
                $sex = $_POST['sex'];
                $firstName = $_POST['prenom'];
                $lastName = $_POST['nom'];
                $birthdate = $_POST['date'];
                if (!empty($_POST['adressePostale'])) {
                    $address = $_POST['adressePostale']; // Champ optionnel
                } else {
                    $address = "";
                }
                $postCode = $_POST['codePostal'];
                if (!empty($_POST['ville'])) {
                    $city = $_POST['ville']; // Champ optionnel
                } else {
                    $city = "";
                }
                $country = $_POST['pays'];
                if (!empty($_POST['numeroDeTelephone'])) {
                    $phoneNumber = $_POST['numeroDeTelephone']; //Champ optionnel
                } else {
                    $phoneNumber = 0;
                }

                if ($_SESSION['signupCodeType'] == "instructor") {
                    $userType = "Pilot";
                } else if ($_SESSION['signupCodeType'] == "structure") {
                    $userType = "Instructor";
                } else {
                    echo ('erreur');
                }

                $email = $_SESSION['signupMail'];
                $password = $_SESSION['signupPassword'];
                $structure =  $_SESSION['structureId'];
                $instructor = $_SESSION['instructorId'];
                $code = uniqid(true);
                $data = array($sex, $firstName, $lastName, $userType, $email, $password, $birthdate, $instructor, $structure, $address, $city, $postCode, $country, $phoneNumber, $code);
                if (addUser($database, $data)) {

                    //CERTAINES VALEURS SONT A VERIF : Instructor, structure, les champs obligatoires et optionnels + cpafini

                    $url = "http://" . $_SERVER["HTTP_HOST"] . dirname($_SERVER["PHP_SELF"]) . "/index.php?redirect=user&function=inscriptionCompteValide&code=$code";
                    $message = "Inscription 
        Cliquez sur ce <a href='$url'>lien  </a>pour valider votre mail
        Ou copiez ce lien http://" . $_SERVER["HTTP_HOST"] . dirname($_SERVER["PHP_SELF"]) . "/index.php?redirect=user&function=inscriptionCompteValide&code=$code dans votre navigateur
            ";
                    if (sendMail($email, "Inscription validation", $message)) {

                        isFirstReferent($database,$structure,$email);

                        header("Location:index.php?redirect=user&function=inscriptionMailEnvoye");
                    } else {
                        $error = "Le mail n'a pas pu être envoyé";
                    }

                } else {
                    $error = "Erreur lors de l'ajout!";
                }
            } else {
                echo "Le mail n'existe pas";
            }
        }

        break;
    case 'inscriptionMailEnvoye':
        $vue = "inscriptionMailEnvoye";
        $error = false;


        break;

    case 'inscriptionCompteValide':
        $vue = "inscriptionCompteValide";
        $error = false;


        if (!isset($_GET["code"])) {
            exit(header("Location:index.php?redirect=user"));
        }
        $code = $_GET["code"];
        mailValidated($database, $code);

        break;

    case 'motDePasseOublie':
        $vue = "motDePasseOublie";
        $error = false;
        if (isset($_POST["motDePasseOublie"])) {

            $mailTo = $_POST["mail"];
            $code = uniqid(true);
            if (testEmailExist($database, $mailTo)) {

                insertInformationResetPass($database, $mailTo, $code);
                $url = "http://" . $_SERVER["HTTP_HOST"] . dirname($_SERVER["PHP_SELF"]) . "/index.php?redirect=user&function=motDePasseOublieNouveauMotDePasse&code=$code";
                $message = "Vous avez demandé un changement de mot de passe ?
                Cliquez sur ce <a href='$url'>lien  </a>pour changer votre mot de passe
                Ou copiez ce lien http://" . $_SERVER["HTTP_HOST"] . dirname($_SERVER["PHP_SELF"]) . "/index.php?redirect=user&function=motDePasseOublieNouveauMotDePasse&code=$code dans votre navigateur
                 ";
                sendMail($mailTo, "Réinitialisation ot de passe", $message);
                header("Location:index.php?redirect=user&function=motDePasseOublieMailEnvoye");
            } else {
                $error = "Le mail entré n'est pas valide!";
            }
        }
        break;
    case 'motDePasseOublieMailEnvoye':
        $vue = "motDePasseOublieMailEnvoye";
        $error = false;
        break;

    case 'motDePasseOublieNouveauMotDePasse':
        $vue = "motDePasseOublieNouveauMotDePasse";
        $error = false;
        if (!isset($_GET["code"])) {
            exit(header("Location:index.php?user&function=404"));
        } else if (!codeResetPassExist($database, $_GET["code"])) {
            exit(header("Location:index.php?redirect=user&function=404"));
        } else {
            $code = $_GET["code"];
            $data = searchMailResetPass($database, $code);
            $email = $data['email'];
            if (isset($_POST['nouveauMdp'])) {
                if (!empty($_POST['Nmdp']) and !empty($_POST['Cmdp'])) {

                    $newPassword = hashPassword($_POST['Nmdp']);
                    $confirmPassword = hashPassword($_POST['Cmdp']);
                    if (isPassword($_POST['Nmdp'])) {
                        if ($newPassword == $confirmPassword) {
                            if (resetPassword($database, $email, $newPassword)) {
                                if (deleteInformationResetPass($database, $code)) {
                                    header("Location: index.php?redirect=user&function=motDePasseOublieConfirmation");
                                } else {
                                    $error = "Une erreur lors de la suppression dans la bdd";
                                }
                            } else {
                                $error = "erreur";
                            }
                        } else {
                            $error = 'Les mots de passe ne correspondent pas!';
                        }
                    } else {
                        $error = "Le mot de passe doit contenir 8 caracteres minimum";
                    }
                } else {
                    $error = 'Veuillez remplir tous les champs';
                }
            }
        }

        break;

    case 'motDePasseOublieConfirmation':
        $vue = "motDePasseOublieConfirmation";
        $error = false;
        break;

case 'psychotestEnLigne':
        $vue = "psychotestEnLigne";
        $error = false;
        break;
		
    case 'monCompte':
        $vue = "monCompte";
        $error = false;
        if (getOneUser($database, $_SESSION['userID']) != array(null)) {
            $data = getOneUser($database, $_SESSION['userID']);
        }
        if (getUserNotValidated($database, $_SESSION['userID']) != array(null)) {
            $userToValidate = getUserNotValidated($database, $_SESSION['userID']);
            if (isset($_POST['activate'])) {
                validateUser($database, $_POST['id']);
                header("Location:index.php?redirect=user&function=monCompte");
            } else  if (isset($_POST['delete'])) {
                //DELETE USER
                header("Location:index.php?redirect=user&function=monCompte");
            }
        }
        //Générer un code unique ?

        if (isset($_POST["changeCodeInstructor"])) {
            $uniqInstructorCode = uniqid("inst");
            changeCode($database, $_SESSION['userID'], $uniqInstructorCode);
            header("Location:index.php?redirect=user&function=monCompte");
        }

        break;

    case 'resultatsPilote':
        $vue = "resultatsPilote";
        $error = false;
        if (getOneUser($database, $_SESSION['userID']) != array(null)) {
            $data = getOneUser($database, $_SESSION['userID']);
        }
        if (isset($_POST['rechercher']) && !empty($_POST['searchUser'])) {
            $search = $_POST['searchUser'];
            if (getUserByNameByInstructor($database, $_SESSION['userID'], $search) != array(null)) {
                $userByInstructor = getUserByNameByInstructor($database, $_SESSION['userID'], $search);
            } else {
                $error = "Aucun utilisateur de ce nom";
                if (getUserByInstructor($database, $_SESSION['userID']) != array(null)) {
                    $userByInstructor = getUserByInstructor($database, $_SESSION['userID']);
                }
            }
        } else {
            if (getUserByInstructor($database, $_SESSION['userID']) != array(null)) {
                $userByInstructor = getUserByInstructor($database, $_SESSION['userID']);
            }
        }
        if (isset($_POST['results'])) {
            $id = $_POST['id'];
            $_SESSION['pilotId'] = $id;
            header("Location:index.php?redirect=test&function=resultatsParPilote");
        }
        //getTestAVGPerGender($database,'Female',1,$_SESSION['userID']);
        
        break;

    case 'modificationMotDePasse':
        $vue = "modificationMotdePasse";
        $error = false;
        if (isset($_POST['piloteModifMdp'])) {
            if (!empty($_POST['Nmdp']) and isPassword($_POST['Cmdp']) and isPassword($_POST['Amdp'])) {
                $ancienMdp = hashPassword($_POST['Amdp']);
                $nouveauMdp = hashPassword($_POST['Nmdp']);
                $confirmationMdp = hashPassword($_POST['Cmdp']);
                $user = getUserInfo($database, $_SESSION['userID']);

                $password = $user['password'];


                if (passwordMatch($password, $ancienMdp)) {
                    if (passwordMatch($nouveauMdp, $confirmationMdp)) {
                        if (changePassword($database, $_SESSION['userID'], $nouveauMdp)) {
                            header("Location:index.php?redirect=user&function=modificationValide");
                        } else {
                            $error = "Echec";
                        }
                    } else {
                        $error = 'Les mots de passe ne correspondent pas!';
                    }
                } else {
                    $error = "L'ancien mot de passe n'est pas valide!";
                }
            } else {
                $error = 'Veuillez remplir tous les champs';
            }
        }
        break;

    case 'modificationProfil':

        $vue = "modificationProfil";
        $user = getUserInfo($database, $_SESSION['userID']);
        $error = false;
        if (isset($_POST['modifnom'])) {
            editProfil($_POST['modifnom'], changeLastName($database, $_SESSION['userID'], $_POST['nom']));
        }
        if (isset($_POST['modifprenom'])) {

            editProfil($_POST['modifprenom'], changeFirstName($database, $_SESSION['userID'], $_POST['prenom']));
        }
        if (isset($_POST['modifdate'])) {
            editProfil($_POST['modifdate'], changeBirthDate($database, $_SESSION['userID'], $_POST['date']));
        }
        if (isset($_POST['modifemail'])) {

            editProfil($_POST['modifemail'], changeEmail($database, $_SESSION['userID'], $_POST['email']));
        }
        if (isset($_POST['modifadresse'])) {
            editProfil($_POST['modifadresse'], changeAddress($database, $_SESSION['userID'], $_POST['adressePostale']));
        }
        if (isset($_POST['modifcodepostal'])) {

            editProfil($_POST['modifcodepostal'], changePostCode($database, $_SESSION['userID'], $_POST['codePostale']));
        }
        if (isset($_POST['modifville'])) {
            editProfil($_POST['modifville'], changeCity($database, $_SESSION['userID'], $_POST['ville']));
        }
        if (isset($_POST['modifpays'])) {

            editProfil($_POST['modifpays'], changeCountry($database, $_SESSION['userID'], $_POST['pays']));
        }

        if (isset($_POST['modifnumero'])) {

            editProfil($_POST['modifnumero'], changePhoneNumber($database, $_SESSION['userID'], $_POST['numeroDeTelephone']));
        }


        break;

    case 'modificationValide':

        $vue = "modificationValide";
        $error = false;
        break;


    case 'gestionDesUtilisateurs':
        $vue = "gestionDesUtilisateurs";
        $error = false;
			
		if(!isset($_SESSION["nbCriteria"]))
		{
			$_SESSION["nbCriteria"] = 1;
		}
		$_SESSION["firstLoad"] = "true";

		if(isset($_POST['Research']))
		{
			$_SESSION["firstLoad"] = "false";
			
			for($i = 0; $i<$_SESSION['nbCriteria'];$i++)
			{
				$_SESSION['criteriaText'][$i] = $_POST['Text'.$i];
				if(isset($_POST[$i]))
				{
					$_SESSION['criteriaType'][$i] = $_POST[$i];
				}
				
			}
			
			$users = multiCriteriaResearch($database);

		}
		else{

				$users = multiCriteriaResearch($database);

		}
					
			
            
            
            //$id = $_POST["id"]; 
            if(isset($_POST['delete'])){
               $id = $_POST['id'];
           if(deleteUser($database,$id)){
               header("Location:index.php?redirect=user&function=gestionDesUtilisateurs");
           }
           else{
               $error = "L'utilisateur n'a pas pu être supprimé!";
           }
            
         }
         
         if(isset($_POST['block'])){
            $id = $_POST['id'];
            if(banUser($database,$id)){
                header("Location:index.php?redirect=user&function=gestionDesUtilisateurs");
            }
            else{
                $error = "L'utilisateur n'a pas pu être banni!";
            }
        }

        if(isset($_POST['referent'])){    
            $id = $_POST['id'];
            passUserToReferent($database,$id);
            header("Location:index.php?redirect=user&function=gestionDesUtilisateurs");

        }
            
        
    break;

    case 'cgu':
        // Liste des user déjà enregistrés
        $vue = "cgu";
        $error = false;
        break;


    case 'aPropos':

        $vue = "aPropos";
        $error = false;
        break;

        case 'chartBar':
            $vue = "chart";
            $error = false;
            //TODO : Ajouter les resultats des test moyens des femmes et des hommes
           
            drawBarGraphics("Moyenne des résultats de la structure des différents tests entre les femmes et les hommes",
            array(getTestAVGPerGender($database,'Female',1,$_SESSION['userID'])[0],getTestAVGPerGender($database,'Male',1,$_SESSION['userID'])[0]),
            array(getTestAVGPerGender($database,'Female',2,$_SESSION['userID'])[0],getTestAVGPerGender($database,'Male',2,$_SESSION['userID'])[0]),
            array(getTestAVGPerGender($database,'Female',3,$_SESSION['userID'])[0],getTestAVGPerGender($database,'Male',3,$_SESSION['userID'])[0]),
            array(getTestAVGPerGender($database,'Female',4,$_SESSION['userID'])[0],getTestAVGPerGender($database,'Male',4,$_SESSION['userID'])[0]),
            array(getTestAVGPerGender($database,'Female',5,$_SESSION['userID'])[0],getTestAVGPerGender($database,'Male',5,$_SESSION['userID'])[0]),
            array(getTestAVGPerGender($database,'Female',6,$_SESSION['userID'])[0],getTestAVGPerGender($database,'Male',6,$_SESSION['userID'])[0]),
            array(getTestAVGPerGender($database,'Female',7,$_SESSION['userID'])[0],getTestAVGPerGender($database,'Male',7,$_SESSION['userID'])[0]));
            break;
   /* case 'chartStress':
        $vue = "chart";
        $error = false;

        if(getTestTimeline($database,$_SESSION['pilotId'],1)!=array(null)&&getTestValue($database,$_SESSION['pilotId'],1)!=array(null)){
        drawPointGraphics("Resultats du stress en fonction du temps",(getTestTimeline($database,$_SESSION['pilotId'],1)),(getTestValue($database,$_SESSION['pilotId'],1)));}
        else{
            drawPointGraphics("",array(0),array(0));}
        

        break;

    case 'chartTonality':
        $vue = "chart";
        $error = false;
        if(getTestTimeline($database,$_SESSION['pilotId'],4)!=array(null)&&getTestValue($database,$_SESSION['pilotId'],4)!=array(null)){
        drawPointGraphics("Resultats de la reconnaissance de tonalité en fonction du temps",(getTestTimeline($database,$_SESSION['pilotId'],4)),(getTestValue($database,$_SESSION['pilotId'],4)));
        }
        else{
            drawPointGraphics("",array(0),array(0));}
        break;

    case 'chartReactionTime':
        $vue = "chart";
        $error = false;
        if(getTestTimeline($database,$_SESSION['pilotId'],2)!=array(null)&&getTestValue($database,$_SESSION['pilotId'],2)!=array(null)){
        drawPointGraphics("Resultats du temps de réaction en fonction du temps",(getTestTimeline($database,$_SESSION['pilotId'],2)),(getTestValue($database,$_SESSION['pilotId'],2)));
        }
        else{
            drawPointGraphics("",array(0),array(0));}
      

        break;
            
    case 'chartPerception':
        $vue = "chart";
        $error = false;
        if(getTestTimeline($database,$_SESSION['pilotId'],3)!=array(null)&&getTestValue($database,$_SESSION['pilotId'],3)!=array(null)){
        drawPointGraphics("Resultats du seuil de perception en fonction du temps",(getTestTimeline($database,$_SESSION['pilotId'],3)),(getTestValue($database,$_SESSION['pilotId'],3)));
        }
        else{
            drawPointGraphics("",array(0),array(0));}

        break;*/

    default:
        // si aucune fonction ne correspond au paramètre function passé en GET
        $vue = "404";
        $error = false;
        $message = "Erreur 404 : la page recherchée n'existe pas.";
}

include('vues/header.php');
include('vues/' . $vue . '.php');
include('vues/footer.php');
?>
