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
        echo "err_id";
        // On ferme la connexion
        mysqli_close($con);
    }
    else {

        // stocker la specialite
        $row = mysqli_fetch_array($specialite_prof);
        $specialite = $row["specialite"];
        if ($specialite != "TOUTES") {
            // separer les specialites en une liste
            $liste_specialites_demandee = explode("/", $specialite);

            // recuperer les questions si l'une d'entre elles est concernée par la specialite
            mysqli_query($con, "SET NAMES 'utf8'");
            $requete_eleve = mysqli_query($con, "SELECT nom, prenom, classe, spe1, spe2, idprof1, idprof2, question1, q1spe1, q1spe2, question2, q2spe1, q2spe2 FROM liste  
                                            WHERE idprof1 LIKE '%".$user_id."%' OR idprof2 LIKE '%".$user_id."%';");

        }
        else {
            // recuperer toutes les questions
            mysqli_query($con, "SET NAMES 'utf8'");
            $requete_eleve = mysqli_query($con, "SELECT nom, prenom, classe, spe1, spe2, idprof1, idprof2, question1, q1spe1, q1spe2, question2, q2spe1, q2spe2 FROM liste;");
            $requete_prof = mysqli_query($con, "SELECT id, nom, prenom FROM profs;");
            $requete_specialites = mysqli_query($con, "SELECT * FROM specialites;");

            $liste_profs = array();
            while ($row = mysqli_fetch_array($requete_prof)) {
                $liste_profs[] = $row;
            }
            $liste_specialites = array();
            while ($row = mysqli_fetch_array($requete_specialites)) {
                $liste_specialites[] = $row;
            }
            $liste_specialites_demandee = array();
            foreach ($liste_specialites as $spe) {
                $liste_specialites_demandee[] = $spe["id_spe"];
            }
        }

        // trier tous les resultats dans une liste de listes
        $liste_eleves = array();
        while ($row = mysqli_fetch_array($requete_eleve)) {
            $liste_eleves[] = $row;
        }

        // fermer la connexion
        mysqli_close($con);


        // EXCEL ===========================================================================================================================

        // Dépendence pour creer des fichier excel
        include_once("xlsxwriter.class.php");

        $writer = new XLSXWriter();
        $writer->setTitle('Questions élèves');


        // fichier excel special administrateur ---------------------------------------------------
        if ($specialite == "TOUTES") {
            // Feuille 1
            $sheet1 = 'Toutes spécialités';

            $writer->writeSheetHeader($sheet1, array('nom'=>'string', 'prenom'=>'string', 'classe'=>'string', 'spe1'=>'string', 'spe2'=>'string', 'prof1_nom'=>'string' ,'prof1_prenom'=>'string', 'prof2_nom'=>'string', 'prof2_prenom'=>'string', 'question1'=>'string', 'q1spe1'=>'string', 'q1spe2'=>'string', 'question2'=>'string', 'q2spe1'=>'string', 'q2spe2'=>'string'),
                                ['auto_filter'=>true, 'freeze_row'=>1, 'widths'=>[20,20,10,20,20,20,20,20,20,50,20,20,50,20,20]]);

            for ($i=0; $i<count($liste_eleves); $i++) {
                
                // construction de la ligne
                // nom, prenom, classe
                $ligne = array($liste_eleves[$i]["nom"],
                                $liste_eleves[$i]["prenom"],
                                $liste_eleves[$i]["classe"],);
                // spe1, spe2
                for ($j=0; $j<2; $j++) {
                    $compteur = 0;
                    foreach ($liste_specialites as $spe) {
                        if ($liste_eleves[$i]["spe".($j+1)] == $spe["id_spe"]) {
                            array_push($ligne, $spe["libelle"]);
                            $compteur++;
                        }
                    }
                    if ($compteur == 0) {
                        array_push($ligne, "");
                    }
                }
                // prof1_nom, prof1_prenom, prof2_nom, prof2_prenom
                for ($j=0; $j<2; $j++) {
                    $compteur = 0;
                    foreach ($liste_profs as $prof) {
                        if (strpos($liste_eleves[$i]["idprof".($j+1)], $prof["id"]) !== false) {
                            array_push($ligne, $prof["nom"], $prof["prenom"]);
                            $compteur++;
                            break;
                        }
                    }
                    if ($compteur == 0) {
                        array_push($ligne, "", "");
                    }
                }
                // question1, q1spe1, q1spe2, question2, q2spe1, q2spe2
                for ($k=0; $k<2; $k++) {
                    array_push($ligne, $liste_eleves[$i]["question".($k+1)]);
                    for ($j=0; $j<2; $j++) {
                        $compteur = 0;
                        foreach ($liste_specialites as $spe) {
                            if ($liste_eleves[$i]["q".($k+1)."spe".($j+1)] == $spe["id_spe"]) {
                                array_push($ligne, $spe["libelle"]);
                                $compteur++;
                            }
                        }
                        if ($compteur == 0) {
                            array_push($ligne, "");
                        }
                    }
                }

                $writer->writeSheetRow($sheet1, $ligne, $row_options = array('height'=>40,'wrap_text'=>true, 'border'=>'left,right,top,bottom'));
            }

            // FEUILLE PAR SPECIALITE COMMUNE A EXCEL POUR PROF
        }


        // fichier excel special prof ---------------------------------------------------
        else {
            
            // trier la liste des question
            foreach ($liste_eleves as &$eleve) {
                // on a recupperer plus haut toute les question si l'élève à le prof dans une de ses deux spé
                // mais si un prof a deux specialité A et B, et que l'eleve l'a en A mais pas en B, alors le prof ne vera pas la question de B
                // pareil si un prof a qu'une specialité, il ne verra pas les question de l'autre spécialité

                // si le prof n'est pas en spe A de l'eleve
                if (strpos($eleve["idprof1"], $user_id) === false) {
                    // si la question 1 concerne uniquement la specialité A
                    if (($eleve["q1spe1"] == $eleve["spe1"] || $eleve["q1spe1"] == "") && ($eleve["q1spe2"] == $eleve["spe1"] || $eleve["q1spe2"] == "")) {
                        // on supprime la question de la liste
                        $eleve["question1"] = "";
                        $eleve["q1spe1"] = "";
                        $eleve["q1spe2"] = "";
                    }
                    // pareil pour la question 2
                    if (($eleve["q2spe1"] == $eleve["spe1"] || $eleve["q2spe1"] == "") && ($eleve["q2spe2"] == $eleve["spe1"] || $eleve["q2spe2"] == "")) {
                        $eleve["question2"] = "";
                        $eleve["q2spe1"] = "";
                        $eleve["q2spe2"] = "";
                    }
                }
                // si le prof n'est pas en spe B de l'eleve
                if (strpos($eleve["idprof2"], $user_id) === false) {
                    // si la question 1 concerne uniquement la specialité B
                    if (($eleve["q1spe1"] == $eleve["spe2"] || $eleve["q1spe1"] == "") && ($eleve["q1spe2"] == $eleve["spe2"] || $eleve["q1spe2"] == "")) {
                        $eleve["question1"] = "";
                        $eleve["q1spe1"] = "";
                        $eleve["q1spe2"] = "";
                    }
                    // pareil pour la question 2
                    if (($eleve["q2spe1"] == $eleve["spe2"] || $eleve["q2spe1"] == "") && ($eleve["q2spe2"] == $eleve["spe2"] || $eleve["q2spe2"] == "")) {
                        $eleve["question2"] = "";
                        $eleve["q2spe1"] = "";
                        $eleve["q2spe2"] = "";
                    }
                }
                unset($eleve);
            }

            // feuille toutes specialité si prof multispé
            if (count($liste_specialites_demandee) > 1) {
                $sheet1 = 'Toutes spécialités';

                $writer->writeSheetHeader($sheet1, array('nom'=>'string', 'prenom'=>'string', 'classe'=>'string', 'question1'=>'string', 'q1spe1'=>'string', 'q1spe2'=>'string', 'question2'=>'string', 'q2spe1'=>'string', 'q2spe2'=>'string'),
                                    ['auto_filter'=>true, 'freeze_rows'=>1, 'widths'=>[20,20,10,50,20,20,50,20,20]]);

                foreach ($liste_eleves as $eleve) {
                    $ligne = array($eleve["nom"], $eleve["prenom"], $eleve["classe"], $eleve["question1"], $eleve["q1spe1"], $eleve["q1spe2"], $eleve["question2"], $eleve["q2spe1"], $eleve["q2spe2"]);

                    $writer->writeSheetRow($sheet1, $ligne, $row_options = array('height'=>40,'wrap_text'=>true, 'border'=>'left,right,top,bottom'));
                }
            }
        }


        // Feuilles supplementaires -------------------------------------------------------
        foreach ($liste_specialites_demandee as $spe) {
            if ($spe == "TOUTES") {
                continue;
            }
            $sheet = $spe;
            $writer->writeSheetHeader($sheet, array('nom'=>'string', 'prenom'=>'string', 'classe'=>'string', 'question1'=>'string', 'q1spe1'=>'string', 'q1spe2'=>'string', 'question2'=>'string', 'q2spe1'=>'string', 'q2spe2'=>'string'),
                                ['auto_filter'=>true, 'freeze_rows'=>1, 'widths'=>[15,15,10,50,20,20,50,20,20]]);

            foreach ($liste_eleves as &$eleve) {

                // construction de la ligne
                // savoir si la ligne sera vide
                $vide = false;
                
                $ligne = array($eleve["nom"],
                            $eleve["prenom"],
                            $eleve["classe"]);
            
                if ($eleve["q1spe1"] == $spe || $eleve["q1spe2"] == $spe) {
                    array_push($ligne, $eleve["question1"], $eleve["q1spe1"], $eleve["q1spe2"]);
                }
                else {
                    array_push($ligne, "", "", "");

                    // quand la question 1 est vide on commence a verifier si, dans le cas ou la question 2 serait vide, la ligne serait inutile, et donc passée
                    // expilcation de la ligne 244 :
                    // OBJECTIF : Amorcer la suppression de la ligne si elle sera vide MAIS la garder (meme vide) si elle concerne le prof
                    // RAPPEL : cette boucle (ligne 212) tri les questions une spe par une spe, les eleves selectionnés ont forcement le prof en classe
                    // si la spe de la feuille n'est pas concernee par l'élève (utile pour les prof multi spe ou le compte admin, ex : prof A/B, q1B q2C, feuille A)
                    // OU || si le prof de l'élève, dans cette spe, n'est pas le prof qui reclamme le fichier excel (cas typique : prof1A/B, prof2A, q1A avec p2, q2B) ET QUE && le prof qui reclamme n'est pas l'administrateur, car transcendant de tous les profs
                    // ALORS on initie la supression de la ligne, SAUF si la question 2 sera utile (ligne 253)
                    
                    if (in_array($spe, array($eleve["spe1"], $eleve["spe2"])) === false || (in_array($user_id, explode("/", array($eleve["idprof1"], $eleve["idprof2"])[array_search($spe, array($eleve["spe1"], $eleve["spe2"]))])) === false && $specialite != "TOUTES")) {
                        $vide = true;
                    }
                }

                if ($eleve["q2spe1"] == $spe || $eleve["q2spe2"] == $spe) {
                    array_push($ligne, $eleve["question2"], $eleve["q2spe1"], $eleve["q2spe2"]);
                }
                else {
                    if (!$vide) {
                        array_push($ligne, "", "", "");
                    } else {
                        continue;
                    }
                }
                
                $writer->writeSheetRow($sheet, $ligne, $row_options = array('height'=>40,'wrap_text'=>true, 'border'=>'left,right,top,bottom'));
            }
        }
        
        $nom_fichier = "questions_eleves_".$user_id.".xlsx";
        $writer->writeToFile($nom_fichier);
        echo $nom_fichier;
    }
}

?>