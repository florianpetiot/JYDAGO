<?php
// recuperer les variables
$user_id = $_REQUEST['user_id'];
$user_mdp = $_REQUEST['user_mdp'];


// hash le mot de passe pour la securite en cas de hacking de la BDD
$hash_mdp = hash("sha1", $user_mdp);

// CONNEXION A LA BDD
// Se connecter a la base de donnees (indiquer ici les 4 paramètres)
include_once('identifiants_bdd.php');
$con = mysqli_connect($id_bdd['host'], $id_bdd['user'], $id_bdd['password'], $id_bdd['database']);

// retourner une erreur si la connexion a echoue
if (mysqli_connect_errno()) {
    echo "err_connexion";
}
else {

    // recuperer le resultat de la requete
    mysqli_query($con, "SET NAMES 'utf8'");
    $nom_eleve = mysqli_query($con,"SELECT nom, prenom FROM liste WHERE (id = '$user_id' AND mdp = '$hash_mdp');");

    // verifier qu'il y a un resultat
    $existe = mysqli_num_rows($nom_eleve);
    if ($existe == 0) {
        // tester si un prof s'est connecté au mauvais endroit
        $test_prof = mysqli_query($con,"SELECT * FROM profs WHERE (id = '$user_id' AND mdp = '$hash_mdp');");
        $existe_prof = mysqli_num_rows($test_prof);
        if ($existe_prof != 0) {
            echo "err_prof";
        }
        else{
            echo "err_id";
        }
        // On ferme la connexion
        mysqli_close($con);
    }
    else {
        $nom_eleve = mysqli_fetch_array($nom_eleve);

        date_default_timezone_set('Europe/Paris');
        // incrementer la valeur "acces" de 1 dans la base de donnees
        mysqli_query($con, "UPDATE liste SET acces = acces + 1, date = '".date("Y-m-d H:i:s")."' WHERE id = '$user_id';");

        mysqli_query($con, "SET NAMES 'utf8'");

        // recuperer le resultat de la requete
        $requete=mysqli_query($con, "SELECT spe1, spe2, question1, q1spe1, q1spe2, question2, q2spe1, q2spe2 FROM liste WHERE id='$user_id';");

        // traiter le resultat
        $row = mysqli_fetch_array($requete);
        $spe1 = $row["spe1"];
        $spe2 = $row["spe2"];
        $question1 = $row["question1"];
        $q1spe1 = $row["q1spe1"];
        $q1spe2 = $row["q1spe2"];
        $question2 = $row["question2"];
        $q2spe1 = $row["q2spe1"];
        $q2spe2 = $row["q2spe2"];

        // convertir en une liste
        $result = array("$spe1", "$spe2","$question1", "$q1spe1", "$q1spe2", "$question2", "$q2spe1", "$q2spe2");

        $result += array("nom_eleve" => $nom_eleve);


        // envoyer le resultat au format JSON
        $myJSON = json_encode($result);

        echo $myJSON;
    }

}

?>