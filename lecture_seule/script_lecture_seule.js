window.addEventListener("beforeunload", function () {
    document.body.classList.add("animate-out");
});


function auto_grow(element) {
    element.style.height = "1px";
    element.style.height = (element.scrollHeight)+"px";
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

                    //mettre le questionnaire en readonly
                    document.getElementById('q1s1').removeAttribute('disabled');
                    document.getElementById('q1s2').removeAttribute('disabled');
                    document.getElementById('q2s1').removeAttribute('disabled');
                    document.getElementById('q2s2').removeAttribute('disabled');
                    document.getElementById('q1s1').classList.add("select-readonly");
                    document.getElementById('q1s2').classList.add("select-readonly");
                    document.getElementById('q2').setAttribute('readonly', 'readonly');
                    document.getElementById('q2s1').classList.add("select-readonly");
                    document.getElementById('q2s2').classList.add("select-readonly");


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

                    // relatif a la deuxieme question
                    document.getElementById("q2").value = myObj[5];
                    auto_grow(document.getElementById("q2"));
                    $q2s1.value = myObj[6];
                    $q2s2.value = myObj[7];
                    
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