<?php
// recuperer les variables
$user_id = $_REQUEST['user_id'];
$user_mdp = $_REQUEST['user_mdp'];

// hash le mot de passe pour la securite en cas de hacking de la BDD
$hash_mdp = hash("sha1", $user_mdp);

// CONNEXION A LA BDD
// Se connecter a la base de donnees (indiquer ici les 4 paramètres)
include_once("../identifiants_bdd.php");
$con = mysqli_connect($id_bdd['host'], $id_bdd['user'], $id_bdd['password'], $id_bdd['database']);

// retourner une erreur si la connexion a echoue
if (mysqli_connect_errno()) {
    echo "err_connexion";
}
else {
    // recuperer la specialite demandee
    mysqli_query($con, "SET NAMES 'utf8'");
    $specialite_prof = mysqli_query($con, "SELECT specialite FROM profs WHERE (id = '$user_id' AND mdp = '$hash_mdp');");

    // verifier qu'il y a un resultat
    $existe=mysqli_num_rows($specialite_prof);
    if ($existe == 0) {
        // tester si un eleve s'est connecté au mauvais endroit
        $test_eleve = mysqli_query($con, "SELECT * FROM liste WHERE (id = '$user_id' AND mdp = '$hash_mdp');");
        $existe_eleve = mysqli_num_rows($test_eleve);
        if ($existe_eleve != 0) {
            echo "err_eleve";
        }
        else{
            echo "err_id";
        }
        // On ferme la connexion
        mysqli_close($con);
    }
    else {
        // recuperer nom prenom du prof dans une autre variable
        mysqli_query($con, "SET NAMES 'utf8'");
        $nom_prof = mysqli_query($con, "SELECT nom, prenom, specialite FROM profs WHERE (id = '$user_id' AND mdp = '$hash_mdp');");
        // stocker le nom et prenom du prof dans une liste
        $nom_prof = mysqli_fetch_array($nom_prof);
        
    
        // stocker la specialite
        $row = mysqli_fetch_array($specialite_prof);
        $specialite = $row["specialite"];

        if ($specialite != "TOUTES") {
            // recuperer les questions si l'élève à le prof dans une de ses deux spé
            mysqli_query($con, "SET NAMES 'utf8'");
            $requete_eleve = mysqli_query($con, "SELECT nom, prenom, classe, spe1, spe2, idprof1, idprof2, question1, q1spe1, q1spe2, question2, q2spe1, q2spe2 FROM liste
                                        WHERE idprof1 LIKE '%".$user_id."%' OR idprof2 LIKE '%".$user_id."%';"); 

        }
        else {
            // recuperer toutes les questions
            mysqli_query($con, "SET NAMES 'utf8'");
            $requete_eleve = mysqli_query($con, "SELECT nom, prenom, classe, question1, q1spe1, q1spe2, question2, q2spe1, q2spe2 FROM liste;");
        }
        

        // trier tous les resultats dans une liste de listes
        $liste_eleves = array();
        while ($row = mysqli_fetch_array($requete_eleve)) {
            $liste_eleves[] = $row;
        }

        if ($specialite != "TOUTES") {
            // trier les question
            foreach ($liste_eleves as &$eleve) {
                // on a recupperer plus haut toute les question si l'élève à le prof dans une de ses deux spé
                // mais si un prof a deux spécialités A et B, et que l’élève l’a en A mais pas en B, alors le prof ne verra pas la question de B
                // pareil si un prof a qu'une specialité, il ne verra pas les question de l'autre spécialité

                // si le prof n'est pas en spe A de l'eleve
                if (strpos($eleve["idprof1"], $user_id) === false) {
                    // si la question 1 concerne uniquement la specialité A
                    if (($eleve["q1spe1"] == $eleve["spe1"] || $eleve["q1spe1"] == "") && ($eleve["q1spe2"] == $eleve["spe1"] || $eleve["q1spe2"] == "")) {
                        // on supprime la question de la liste
                        $eleve["question1"] = "";
                        $eleve["q1spe1"] = "";
                        $eleve["q1spe2"] = "";
                        $eleve[7] = "";
                        $eleve[8] = "";
                        $eleve[9] = "";
                    }
                    // pareil pour la question 2
                    if (($eleve["q2spe1"] == $eleve["spe1"] || $eleve["q2spe1"] == "") && ($eleve["q2spe2"] == $eleve["spe1"] || $eleve["q2spe2"] == "")) {
                        $eleve["question2"] = "";
                        $eleve["q2spe1"] = "";
                        $eleve["q2spe2"] = "";
                        $eleve[10] = "";
                        $eleve[11] = "";
                        $eleve[12] = "";
                    }
                }
                // si le prof n'est pas en spe B de l'eleve
                if (strpos($eleve["idprof2"], $user_id) === false) {
                    // si la question 1 concerne uniquement la specialité B
                    if (($eleve["q1spe1"] == $eleve["spe2"] || $eleve["q1spe1"] == "") && ($eleve["q1spe2"] == $eleve["spe2"] || $eleve["q1spe2"] == "")) {
                        $eleve["question1"] = "";
                        $eleve["q1spe1"] = "";
                        $eleve["q1spe2"] = "";
                        $eleve[7] = "";
                        $eleve[8] = "";
                        $eleve[9] = "";
                    }
                    // pareil pour la question 2
                    if (($eleve["q2spe1"] == $eleve["spe2"] || $eleve["q2spe1"] == "") && ($eleve["q2spe2"] == $eleve["spe2"] || $eleve["q2spe2"] == "")) {
                        $eleve["question2"] = "";
                        $eleve["q2spe1"] = "";
                        $eleve["q2spe2"] = "";
                        $eleve[10] = "";
                        $eleve[11] = "";
                        $eleve[12] = "";
                    }
                }
            }
        }

        // ajout de LA specialite et du nom/prenom dans la liste
        $liste_eleves += array("nom_prof" => $nom_prof);

        // fermer la connexion
        mysqli_close($con);

        // retourner le resultat au format JSON
        $myJSON = json_encode($liste_eleves);
        echo $myJSON;
    }
}
?>