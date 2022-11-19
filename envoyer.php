<?php
// recuperer les variables
$user_id = $_REQUEST['user_id'];
$user_mdp = $_REQUEST['mdp'];
$q1 = $_REQUEST['q1'];
$q1s1 = $_REQUEST['q1s1'];
$q1s2 = $_REQUEST['q1s2'];
$q2 = $_REQUEST['q2'];
$q2s1 = $_REQUEST['q2s1'];
$q2s2 = $_REQUEST['q2s2'];

// hash le mdp en sha1
$hash_mdp = hash("sha1", $user_mdp);


// CONNEXION A LA BDD
// Se connecter a la base de donnees (indiquer ici les 4 paramètres)
include_once('identifiants_bdd.php');
$con = mysqli_connect($id_bdd['host'], $id_bdd['user'], $id_bdd['password'], $id_bdd['database']);

// retourner une erreur si la connexion a echoue
if (mysqli_connect_errno()) {
    echo "err_connexion";
}
else{
    // recuperer le resultat de la requete
    $requete=mysqli_query($con,"SELECT nom FROM liste WHERE (id = '$user_id' AND mdp = '$hash_mdp');");

    // verifier qu'il y a un resultat
    $existe=mysqli_num_rows($requete);
    if ($existe == 0) {
        echo "err_id";
        // On ferme la connexion
        mysqli_close($con);
    }
    else {
        // verifier si la date limite n'est pas depassee (ex: une machine avait garder le site en cache)
        date_default_timezone_set('Europe/Paris');

        $limite = getenv('LIMITE');
        $annee = substr($limite, 0, 4);
        $mois = substr($limite, 4, 2);
        $jour = substr($limite, 6, 2);
        $heure = substr($limite, 8, 2);
        $minute = substr($limite, 10, 2);
        $seconde = substr($limite, 12, 2);

        $date_limite = mktime($heure, $minute, $seconde, $mois, $jour, $annee);
        if (time() >= $date_limite) {
            echo "err_limite";
        }
        // verifier la conformitee des specialitees des questions, laisse le droit de n'entrer qu'une seule question
        else if ($q1s2 == "" && ($q2s1 == $q1s1 && $q2s2 == "" && $q1s1 != "")) {
            echo "err_spe";

        }
        else {
            mysqli_query($con, "SET NAMES 'utf8'");

            // modifier la question 1
            mysqli_query($con, "UPDATE liste SET question1='$q1', q1spe1='$q1s1', q1spe2='$q1s2' WHERE (id='$user_id' AND mdp='$hash_mdp');");
        
            // modifier la question 2
            mysqli_query($con, "UPDATE liste SET question2='$q2', q2spe1='$q2s1', q2spe2='$q2s2' WHERE (id='$user_id' AND mdp='$hash_mdp');");
        
            // envoyer un message de succes
            echo "success";
        }  
    }
}
mysqli_close($con);
?>