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
    $admin = mysqli_query($con, "SELECT specialite FROM profs WHERE (id = '$user_id' AND mdp = '$hash_mdp');");

    // verifier qu'il y a un resultat ou que la specialite du prof est 'TOUTES'
    $existe=mysqli_num_rows($admin);
    if ($existe == 0 || ($existe != 0 && mysqli_fetch_row($admin)[0] != "TOUTES")) {
        echo "err_id";
        // On ferme la connexion
        mysqli_close($con);
    }
    else {

        // recuperer les trois tables de la base de donnees
        mysqli_query($con, "SET NAMES 'utf8'");
        $requete_eleve = mysqli_query($con, "SELECT * FROM liste;");
        $requete_prof = mysqli_query($con, "SELECT * FROM profs;");
        $requete_specialite = mysqli_query($con, "SELECT * FROM specialites;");

        // trier tout les resultats dans des tableaux
        $liste_eleve = array();
        $liste_prof = array();
        $liste_specialite = array();

        while ($row = mysqli_fetch_array($requete_eleve)) {
            $liste_eleve[] = $row;
        }
        while ($row = mysqli_fetch_array($requete_prof)) {
            $liste_prof[] = $row;
        }
        while ($row = mysqli_fetch_array($requete_specialite)) {
            $liste_specialite[] = $row;
        }

        // VARIABLES UTILISEES
        $liste_erreurs = array();
        $liste_id_prof = array_map(function ($prof) {
            return strval($prof[0]);
        }, $liste_prof);
        $liste_spe_court = array_map(function ($spe) {
            return $spe[0];
        }, $liste_specialite);
        $liste_classe = array();
        $liste_professeur = array();
        $liste_statistique = array("Comptes incomplets : ", 0, "Élèves non connectés : ", 0, "Questions incomplètes : ", 0, "", "", "Connexion moyenne : ", 0);



        // PROFESSEURS ###############################################################################################
        foreach ($liste_prof as $prof) {
            // compter le nombre d'élèves par professeur
            if ($prof[0] != "") {
                array_push($liste_professeur, $prof[0]);
                array_push($liste_professeur, 0);
            }
            foreach ($liste_eleve as $eleve) {
                if (strpos($eleve[7], $prof[0]) !== false || strpos($eleve[8], $prof[0]) !== false) {
                    $liste_professeur[array_search($prof[0], $liste_professeur) + 1]++;
                }
            }


            $fatal = false;
            // vérifier que les caractéristiques du professeur sont toutes complétées
            for ($i = 0; $i < 5; $i++) {
                if ($prof[$i] == "") {
                    array_push($liste_erreurs, array("Erreur critique", "Le professeur ".$prof["id"]." n'a pas toutes ses caractéristiques"));
                    $liste_statistique[1]++;
                    $fatal = true;
                    break;
                }
            }
            if ($fatal) {
                continue;
            }


            // vérifier que la spécialité du professeur est bien dans la liste des spécialités
            $liste_spe_prof = explode("/", $prof[4]);
            foreach ($liste_spe_prof as $spe) {
                if (!in_array($spe, $liste_spe_court)) {
                    array_push($liste_erreurs, array("Erreur critique", "M./Mme ".$prof["nom"]." a une spécialité non existante dans la table 'specialites' (spé : ".$spe.")"));
                }
            }
        }



        // ELEVES ###############################################################################################
        foreach ($liste_eleve as $eleve) {
        
            // compter le nombre d'eleves par classe
            if (!in_array($eleve['classe'], $liste_classe) && $eleve['classe'] != "") {
                array_push($liste_classe, $eleve['classe']);
                array_push($liste_classe, 1);
            }
            else {
                // ajouter 1 (++) a l'index juste apres la classe rencontree
                $liste_classe[array_search($eleve['classe'], $liste_classe) + 1]++;
            }


            $fatal = false;
            // vérifier que les caractéristiques de l'élève sont toutes complétées
            for ($i = 0; $i < 9; $i++) {
                if ($eleve[$i] == "") {
                    array_push($liste_erreurs, array("Erreur critique", "L'élève ".$eleve["id"]." n'a pas toutes ses caractéristiques"));
                    $liste_statistique[1]++;
                    $fatal = true;
                    break;
                }
            }
            if ($fatal) {
                continue;
            }


            // vérifier que les professeurs associés à l'élève sont bien dans la liste des professeurs
            for ($i = 0; $i < 2; $i++) {
                $liste_prof_associe = explode("/", $eleve[7+$i]);
                foreach ($liste_prof_associe as $prof) {
                    if (!in_array($prof, $liste_id_prof)) {
                        array_push($liste_erreurs, array("Erreur critique", $eleve["prenom"]." ".$eleve["nom"]." a un professeur non existant (idprof".strval($i+1).")"));
                        $fatal = true;
                    }
                }
            }
            if ($fatal) {
                continue;
            }


            // vérifier que spé1 est cohérente avec idprof1 et spé2 avec idprof2
            for ($i = 0; $i < 2; $i++) {
                $liste_prof_associe = explode("/", $eleve[7+$i]);
                foreach ($liste_prof_associe as $prof) {
                    foreach ($liste_prof as $prof_) {
                        if(($prof_["id"] == $prof) && (in_array($eleve[7+$i-2], explode("/", $prof_["specialite"])) === false)){
                            array_push($liste_erreurs, array("Erreur critique", $eleve["prenom"]." ".$eleve["nom"]." a une spé".strval($i+1)." incohérente avec son idprof".strval($i+1)." (idprof : ".$prof_[0].", spé : ".$eleve[7+$i-2].")"));
                            $fatal = true;
                        }
                    }
                }
            }
            if ($fatal) {
                continue;
            }


            // vérifier si l'élève s'est déjà connecté
            if ($eleve['acces'] == 0) {
                array_push($liste_erreurs, array("Information", $eleve["prenom"]." ".$eleve["nom"]." ne s'est jamais connecté"));
                $liste_statistique[3]++;
                $fatal = true;
            }
            if ($fatal) {
                continue;
            }
            $liste_statistique[9] = $liste_statistique[9] + $eleve['acces'];


            // vérifier la bonne complétion des questions
            for ($i = 0; $i <= 3; $i=$i+3) {
                if ($eleve[9+$i] == "") {
                    array_push($liste_erreurs, array("Information", $eleve["prenom"]." ".$eleve["nom"]." n'a pas complété sa question ".strval(array_search($i, [0,3])+1)));
                    $liste_statistique[5]++;
                    $fatal = true;
                }
                elseif ($eleve[10+$i] == ""){
                    array_push($liste_erreurs, array("Erreur critique", $eleve["prenom"]." ".$eleve["nom"]." n'a pas de spécialité associée à sa question ".strval(array_search($i, [0,3])+1)));
                    $liste_statistique[5]++;
                    $fatal = true;
                }
            }
            if ($fatal) {
                continue;
            }


            // vérifier que la spécialités des questions sont bien dans la liste des spécialités
            for ($i = 0; $i <= 3; $i=$i+3) {
                $liste_specialite_associe = array();
                for ($j = 0; $j < 2; $j++) {
                    array_push($liste_specialite_associe, $eleve[10+$i+$j]);
                }
                foreach ($liste_specialite_associe as $spe) {
                    if ($spe != "" && !in_array($spe, $liste_spe_court)) {
                        array_push($liste_erreurs, array("Erreur critique", $eleve["prenom"]." ".$eleve["nom"]." a choisi une spécialité non existante dans la table 'specialites' pour sa question ".strval(array_search($i, [0,3])+1)." (spé : ".$spe.")"));
                    }
                    elseif ($spe != "" && !in_array($spe, array($eleve[5], $eleve[6]))) {
                        array_push($liste_erreurs, array("Erreur critique", $eleve["prenom"]." ".$eleve["nom"]." a choisi une spécialité qui ne lui est pas associée pour sa question ".strval(array_search($i, [0,3])+1)." (spé : ".$spe.")"));
                    }
                }
            }


            // verifier que l'élève utilise bien ses deux specialité au moins une fois
            $liste_specialite_questions = array();
            for ($i = 0; $i <= 3; $i=$i+3) {
                for ($j = 0; $j < 2; $j++) {
                    array_push($liste_specialite_questions, $eleve[10+$i+$j]);
                }
            }
            for ($i = 0; $i < 2; $i++){
                if (in_array($eleve[5+$i], $liste_specialite_questions) === false) {
                    array_push($liste_erreurs, array("Erreur critique", $eleve["prenom"]." ".$eleve["nom"]." n'a pas utilisé sa spécialité ".$eleve[5+$i]." dans ses questions"));
                }
            }
        }

        // fin du calcule du nombre moyen de connexions
        $liste_statistique[9] = round($liste_statistique[9] / (count($liste_eleve)-$liste_statistique[1]-$liste_statistique[3]), 2);



        // Fichier excel ###############################################################################################
        
        $err_cri = 0;
        $inf = 0;
        foreach ($liste_erreurs as $erreur) {
            if ($erreur[0] == "Erreur critique") {
                $err_cri++;
            }
            else {
                $inf++;
            }
        }

        // Dépendence pour creer des fichier excel
        include_once("xlsxwriter.class.php");

        $writer = new XLSXWriter();
        $sheet1 = "Rapport JYDAGO";

        $writer->writeSheetHeader($sheet1, array('string', 'string', 'string', 'string', 'string', 'string', 'string', 'string', 'string', 'string', 'string'),
                                ['suppress_row'=>true, 'widths'=>[15,50,5,21,21,5,15,15,5,15,15]]);
        
        $writer->writeSheetRow($sheet1, array(count($liste_erreurs).' erreur(s) trouvée(s) - '.$err_cri.' erreur(s) critique(s) et '.$inf.' information(s)',
                                 '', '', "Statistiques", '', '', "Nombre d'élèves par classe", '', '', "Nombre d'élèves par professeur", ''), ['halign'=>'center']);

        $writer->markMergedCell($sheet1, $start_row=0, $start_col=0, $end_row=0, $end_col=1);
        $writer->markMergedCell($sheet1, $start_row=0, $start_col=3, $end_row=0, $end_col=4);
        $writer->markMergedCell($sheet1, $start_row=0, $start_col=6, $end_row=0, $end_col=7);
        $writer->markMergedCell($sheet1, $start_row=0, $start_col=9, $end_row=0, $end_col=10);

        $writer->writeSheetRow($sheet1, array('', '', '', '', '', '', '', '', '', '', ''));
        $writer->writeSheetRow($sheet1, array('Type', 'Détails', '', '', '', '', 'Classe', "Nombre d'élèves", '', 'ID professeur', "Nombre d'élèves"), ['halign'=>'center']);

        $max = max(count($liste_erreurs), count($liste_classe)/2, count($liste_professeur)/2);

        for ($i = 0; $i < $max; $i++) {
            $ligne = array();
            // erreurs
            if ($i < count($liste_erreurs)) {
                array_push($ligne, $liste_erreurs[$i][0]." : ", $liste_erreurs[$i][1]);
            }
            else {
                array_push($ligne, '', '');
            }
            array_push($ligne, "");
            // statistiques
            if ($i < count($liste_statistique)/2) {
                array_push($ligne, $liste_statistique[$i*2], $liste_statistique[$i*2+1]);
            }
            else {
                array_push($ligne, '', '');
            }
            array_push($ligne, "");
            // nombre d'élèves par classe
            if ($i < count($liste_classe)/2) {
                array_push($ligne, $liste_classe[$i*2]." : ", $liste_classe[$i*2+1]);
            }
            else {
                array_push($ligne, '', '');
            }
            array_push($ligne, "");
            // nombre d'élèves par professeur
            if ($i < count($liste_professeur)/2) {
                array_push($ligne, $liste_professeur[$i*2]." : ", $liste_professeur[$i*2+1]);
            }
            else {
                array_push($ligne, '', '');
            }

            // couleur de la premiere case
            $style = array('height'=>30);
            if ($ligne[0] === "Erreur critique : ") {
                array_push($style, ['halign'=>'right','valign'=>'center','color'=>'#ff0000']);
            }
            else {
                array_push($style, ['halign'=>'right','valign'=>'center','color'=>'#0000ff']);
            }
            array_push($style, ['halign'=>'left','valign'=>'center','wrap_text'=>true],['halign'=>'right'],['halign'=>'right','valign'=>'center'],['halign'=>'left','valign'=>'center'],['halign'=>'right'],['halign'=>'right','valign'=>'center'],['halign'=>'left','valign'=>'center'],['halign'=>'right'],['halign'=>'right','valign'=>'center'],['halign'=>'left','valign'=>'center']);

            $writer->writeSheetRow($sheet1, $ligne, $style);
        }

        $nom_fichier = "Rapport_JYDAGO.xlsx";

        $writer->writeToFile('Rapport_JYDAGO.xlsx');

        echo $nom_fichier;

    }
}
?>