window.addEventListener("beforeunload", function () {
    document.body.classList.add("animate-out");
});


function recupperer_questions() {
    const id = document.getElementById('id').value;
    const mdp = document.getElementById('mdp').value;

    if (id !== "" && mdp !== "") { //si les champs sont remplis

        // creer un objet XMLHttpRequest
        let xmlhttp = new XMLHttpRequest();

        // editer la requete
        xmlhttp.open("GET", "recuperer_prof.php?user_id=" + id + "&user_mdp=" + mdp, true);

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
                // si un eleve s'est connecté au mauvais endroit
                else if (reponse.includes("err_eleve")) {
                    alert("Vous êtes élève.\nVeuillez donc vous connecter via l'accès élève.");
                }
                else {
                    //desactiver le questionnaire d'authentification
                    document.getElementById('id').disabled = true;
                    document.getElementById('mdp').disabled = true;
                    document.getElementById('bouton_connection').innerText = "Connecté";
                    document.getElementById('bouton_connection').style.background = '#2BC016C0';
                    document.getElementById('bouton_connection').disabled = true;

                    // convertir le JSON en liste
                    const myObj = JSON.parse(reponse);

                    // ajout du bloc HTML dans la div "cadre-ext"
                    let cadre_ext = document.getElementById('cadre-ext');
                    let div = document.createElement('div');
                    div.setAttribute('class', 'liste-questions large');
                    div.setAttribute('id', 'liste-questions');

                    // creation du bloc html à ajouter
                    let html = "";

                    html += "<h3 id='nom-prof'>Bonjour, " + myObj["nom_prof"].prenom + " " + myObj["nom_prof"].nom + "</h3>";
                    html += "<button id='bouton-exporter' class='large' onclick='telecharger_questions()'>Exporter les questions</button>";
                    if (id == '999'){
                        html += "<button id='bouton-exporter' class='large' onclick='telecharger_rapport()'>Télécharger un rapport</button>";
                    }
                    html += "<hr>";

                    for (let item in myObj) {

                        if (item != "specialite" && item != "nom_prof") {
                            html += `<h4 class='prenom'>${myObj[item].nom} ${myObj[item].prenom} - ${myObj[item].classe}</h4>`;

                            if (myObj[item].question1 != "" && myObj[item].question1 != null) {   
                                if (myObj[item].q1spe2 === "") {
                                    html += `<p class='question'>${myObj[item].q1spe1} - ${myObj[item].question1}</p>`;
                                }
                                else {
                                    html += `<p class='question'>${myObj[item].q1spe1}, ${myObj[item].q1spe2} - ${myObj[item].question1}</p>`;
                                }
                            }

                            if (myObj[item].question2 != "" && myObj[item].question2 != null) { 
                                if (myObj[item].q2spe2 === "") {
                                    html += `<p class='question'>${myObj[item].q2spe1} - ${myObj[item].question2}</p>`;
                                }
                                else {
                                    html += `<p class='question'>${myObj[item].q2spe1}, ${myObj[item].q2spe2} - ${myObj[item].question2}</p>`;
                                }
                            }
                            html += "<hr>";
                        }
                    }
                
                    div.innerHTML = html;
                    cadre_ext.appendChild(div);

                    document.getElementById('cadre-ext').style.paddingBottom = '10px';

                    // baisser la page sur la premiere question
                    document.getElementById("liste-questions").scrollIntoView({ behavior: "smooth" });

                }
            }
        };
    }
}


function telecharger_questions() {
    const id = document.getElementById('id').value;
    const mdp = document.getElementById('mdp').value;

    if (id !== "" && mdp !== "") { //si les champs sont remplis

        // creer un objet XMLHttpRequest
        let xmlhttp = new XMLHttpRequest();

        // editer la requete
        xmlhttp.open("GET", "exporter_questions.php?user_id=" + id + "&user_mdp=" + mdp, true);

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
                else {
                    // make user's browser download the file
                    let link = document.createElement('a');
                    link.href = reponse;
                    link.download = reponse;
                    link.click();

                    // detect if the file is downloaded
                    let interval = setInterval(function () {
                        if (link.href.includes(reponse)) {
                            clearInterval(interval);
                            // remove the file from the server
                            let xmlhttp2 = new XMLHttpRequest();
                            xmlhttp2.open("GET", "supprimer_fichier.php?fichier=" + reponse, true);
                            xmlhttp2.send();
                        }
                    }, 1000);
                }
            }
        };
    }
}


function telecharger_rapport() {
    const id = document.getElementById('id').value;
    const mdp = document.getElementById('mdp').value;

    if (id !== "" && mdp !== "") { //si les champs sont remplis

        // creer un objet XMLHttpRequest
        let xmlhttp = new XMLHttpRequest();

        // editer la requete
        xmlhttp.open("GET", "telecharger_rapport.php?user_id=" + id + "&user_mdp=" + mdp, true);

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
                    alert("L'optention du rapport n'est disponible que pour l'administrateur !\nVeuillez vous connecter en tant qu'administrateur.");
                }
                // montrer une alerte si "err_connexion" dans la reponse
                else if (reponse.includes("err_connexion")) {
                    alert("Une erreur de communication est survenue !\nVeuillez réessayer ultérieurement.");
                }
                else {
                    // make user's browser download the file
                    let link = document.createElement('a');
                    link.href = reponse;
                    link.download = reponse;
                    link.click();
                    
                    // detect if the file is downloaded
                    let interval = setInterval(function () {
                        if (link.href.includes(reponse)) {
                            clearInterval(interval);
                            // remove the file from the server
                            let xmlhttp2 = new XMLHttpRequest();
                            xmlhttp2.open("GET", "supprimer_fichier.php?fichier=" + reponse, true);
                            xmlhttp2.send();
                        }
                    }, 1000);
                }
            }
        };
    }
}