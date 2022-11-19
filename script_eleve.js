window.addEventListener("beforeunload", function () {
    document.body.classList.add("animate-out");
});


function auto_grow(element) {
    element.style.height = "1px";
    element.style.height = (element.scrollHeight)+"px";
}


function bloquerSpe1(idSpe1, idSpe2) {
    // recuperer l'option selectionnée spe1
    const spe1 = document.getElementById(idSpe1).value;

    // désélectionner l'option spe1 options dans spe2
    const spe2 = document.getElementById(idSpe2);
    for (const i of spe2.options) {
        if (i.value === spe1) {
            if (i.selected === true) {
                spe2.value = "";
            }
            i.disabled = true;
        } else {
            i.disabled = false;
        }
    }
}


function verifierSpe1(idSpe1, idSpe2) {
    // si l'option selectionnée en spe1 est vide
    // selectionner "" en spe2
    const spe1 = document.getElementById(idSpe1);
    const spe2 = document.getElementById(idSpe2);
    if (spe1.value === "" && spe2.value !== "") {
        for (const i of spe1.options) {
            if (i.value === spe2.value) {
                i.selected = true;
                spe2.options[i.index].disabled = true;
                spe2.value = "";
            }
        }
    }
}


function recupperer_questions() {
    const id = document.getElementById('id').value;
    const mdp = document.getElementById('mdp').value;

    if (id !== "" && mdp !== "") { //si les champs sont remplis

        // creer un objet XMLHttpRequest
        let xmlhttp = new XMLHttpRequest();

        // editer la requete
        xmlhttp.open("GET", "recuperer.php?user_id=" + id + "&user_mdp=" + mdp, true);

        // envoyer la requete
        xmlhttp.send();


        xmlhttp.onreadystatechange = function () {
            // quand la requete change de status
            if (this.readyState == 4 && this.status == 200) {
                // statut attendu

                // recuperer la reponse du fichier php
                let reponse = this.responseText;

                if (reponse == "err_id") {
                    // identifiants incorrects
                    alert("L'identifiant ou le mot de passe est incorrect !");
                }
                // montrer une alerte si "err_connexion" dans la reponse
                else if (reponse.includes("err_connexion")) {
                    alert("Une erreur de communication est survenue !\nVeuillez réessayer ultérieurement.");
                }

                // si un prof se connecte au mauvais endroit
                else if (reponse.includes("err_prof")) {
                    alert("Vous êtes professeur.\nVeuillez donc vous connecter via l'accès professeur.");
                }

                else {

                    //desactiver le questionnaire d'authentification
                    document.getElementById('id').disabled = true;
                    document.getElementById('mdp').disabled = true;
                    document.getElementById('bouton_connection').innerText = "Connecté";
                    document.getElementById('bouton_connection').style.background = '#2BC016C0';
                    document.getElementById('bouton_connection').disabled = true;
                    document.getElementById('bouton_sauvegarder').removeAttribute('disabled');

                    //activer le questionnaire des questions
                    document.getElementById('q1').removeAttribute('disabled');
                    document.getElementById('q1s1').removeAttribute('disabled');
                    document.getElementById('q1s2').removeAttribute('disabled');
                    document.getElementById('q2').removeAttribute('disabled');
                    document.getElementById('q2s1').removeAttribute('disabled');
                    document.getElementById('q2s2').removeAttribute('disabled');

                    // convertir le JSON en liste
                    const myObj = JSON.parse(reponse);

                    const $q1s1 = document.querySelector('#q1s1');
                    const $q1s2 = document.querySelector('#q1s2');
                    const $q2s1 = document.querySelector('#q2s1');
                    const $q2s2 = document.querySelector('#q2s2');

                    // creer les options pour les select
                    for (let i of [$q1s1, $q1s2, $q2s1, $q2s2]) {
                        for (let j = 0; j < 2; j++) {
                            let newOption = new Option(myObj[j], myObj[j]);
                            i.add(newOption, undefined);
                        }
                    }
                    

                    // assigner les valeurs aux bons champs de textes
                    // relatif a la premiere question
                    document.getElementById("q1").value = myObj[2];
                    auto_grow(document.getElementById("q1"));
                    $q1s1.value = myObj[3];
                    $q1s2.value = myObj[4];
                    // desactiver les options de la premiere question sauf si la valeur est ""
                    if ($q1s1.value !== "") {
                        bloquerSpe1("q1s1", "q1s2");
                    }

                    // relatif a la deuxieme question
                    document.getElementById("q2").value = myObj[5];
                    auto_grow(document.getElementById("q2"));
                    $q2s1.value = myObj[6];
                    $q2s2.value = myObj[7];
                    // desactiver les options de la deuxieme question sauf si la valeur est ""
                    if ($q2s1.value !== "") {
                        bloquerSpe1("q2s1", "q2s2");
                    }
                    
                    // mettre nom et prenom dans #nom-eleve
                    document.getElementById("nom-eleve").innerText = "Bonjour, " + myObj["nom_eleve"][1] + " " + myObj["nom_eleve"][0];
                    // enlever attribut "hidden" de #nom-eleve
                    document.getElementById("nom-eleve").removeAttribute("hidden");

                    // mettre le focus sur q1 si q1 est vide
                    if (document.getElementById("q1").value === "") {
                        document.getElementById("q1").focus();
                    }
                    
                    // change margin-top of .premiere-question to 40px
                    document.querySelector(".premiere-question").style.marginTop = "35px";

                    // baisser la page sur le nom de l'élève
                    document.getElementById("nom-eleve").scrollIntoView({ behavior: "smooth" });
                }
            }
        };
    }
}


function sauvegarder_questions() {
    // recuperer les valeurs des champs de textes
    let q1 = document.getElementById("q1").value;
    q1 = q1.replace(/'/g, "\\\'");
    q1 = q1.replace(/[\r\n]+/g,' ');
    q1 = q1.replace(/\s\s+/g, ' ');
    const q1s1 = document.getElementById("q1s1").value;
    const q1s2 = document.getElementById("q1s2").value;
    let q2 = document.getElementById("q2").value;
    q2 = q2.replace(/'/g, "\\\'");
    q2 = q2.replace(/[\r\n]+/g,' ');
    q2 = q2.replace(/\s\s+/g, ' ');
    const q2s1 = document.getElementById("q2s1").value;
    const q2s2 = document.getElementById("q2s2").value;

    // recuperer l'identifiant de l'utilisateur
    const id = document.getElementById('id').value;
    // recuperer le mot de passe de l'utilisateur
    const mdp = document.getElementById('mdp').value;

    // tester s'il y a "&" or "#" dans q1 ou q2
    if (q1.includes("&") || q1.includes("#") || q2.includes("&") || q1.includes("#")) {
        alert("Les caractères '&' et '#' ne sont pas autorisés dans les questions !");
    }
    else {
        // creer un objet XMLHttpRequest
        let xmlhttp = new XMLHttpRequest();

        // editer la requete
        xmlhttp.open("GET", "envoyer.php?user_id=" + id + "&mdp="+ mdp + "&q1=" + q1 + "&q1s1=" + q1s1 + "&q1s2=" + q1s2 + "&q2=" + q2 + "&q2s1=" + q2s1 + "&q2s2=" + q2s2, true);

        // envoyer la requete
        xmlhttp.send();

        xmlhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                // statut attendu
                let reponse = this.responseText;

                if (reponse.includes("err_connexion")) {
                    alert("Une erreur de communication est survenue !\nVeuillez réessayer ultérieurement.");
                }
                else if (reponse.includes("err_id")) {
                    alert("L'identifiant ou le mot de passe est incorrect !");
                }
                else if (reponse.includes("err_limite")) {
                    alert("Les enregistrements sont terminés. Cependant, vous pouvez toujours consulter vos questions.");
                    window.location.reload();
                }
                else if (reponse == "err_spe") {
                    alert("Vous devez obligatoirement solliciter vos deux spécialités !");
                }
                else if (reponse === "success") {
                    alert("Vos questions ont bien été sauvegardées.");
                }
            }
        };
    }
}