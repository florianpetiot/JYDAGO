![Static Badge](https://img.shields.io/badge/Scolaire-1B4965?style=for-the-badge) - ![Static Badge](https://img.shields.io/badge/Mise_%C3%A0_jour-04%2F12%2F2022-E4DFDA?style=flat) - ![Static Badge](https://img.shields.io/badge/HTML-dd4b25?style=flat) ![Static Badge](https://img.shields.io/badge/CSS-016cb4?style=flat) ![Static Badge](https://img.shields.io/badge/JS-e8d44d?style=flat) ![Static Badge](https://img.shields.io/badge/PHP-7177af?style=flat) ![Static Badge](https://img.shields.io/badge/.HTACCESS-3696b8?style=flat)



# JYDAGO
Ce projet a été réalisé en mai 2022 sous l'initiative de mon professeur d'NSI (Numérique et Sciences informatiques).

L'idée de départ était de faciliter le recensement des questions du Grand Oral (épreuve du baccalauréat). Cela consiste à enregistrer deux questions et leur(s) spécialité(s) respective(s) pour chaque élève de terminale. Et ce, en évitant de passer par une grande feuille Excel.

Ce projet est donc actuellement mis à disposition des élèves, des professeurs et de l'administration de mon lycée.

# Sommaire
* **[Démo](#démo)**
* **[Installation](#installation)**
* **[Fonctionalités](#fonctionalités)**
	* **[Élèves](#élèves)**
	* **[Professeurs](#professeurs)**
	* **[Administration](#administration)**
* **[Notes](#notes)**
* **[Crédits](#Crédits)**

# Démo
Vous pouvez essayer une **[démo scénarisée](https://github.com/florianpetiot/JYDAGO-Demo)** dans laquelle vous pourrez vous "connecter" en tant qu'élève, que professeur ou qu'administrateur, sans avoir besoin de mettre en place la base de données.

# Installation
Vous pouvez lier le site à une base de données stockée sur votre machine (ex: Laragon).
Pour cela, téléchargez puis hébergez la sauvegarde `/ressources/jydago.sql` puis modifiez le ficher `/identifiants_bdd.php` à la racine du projet en conséquence.

# Fonctionalités
* ### Élèves
	Les élèves peuvent se connecter via **[la racine du site](https://florianpetiot.github.io/JYDAGO-Demo/)** grâce à un identifiant et un mot de passe. S'ils ont déjà enregistré des questions précédemment, elles apparaissent dans leur case respective. Sinon ils disposent d'un temps imparti pour les écrire, après lequel la partie élève du site passera en "lecture seule".

	Les cases "spécialité" leur proposent uniquement les deux spécialités qu'ils ont choisies pour leur Terminale. En plus d'un message de bienvenue, l'expérience utilisateur est ainsi personnalisée pour tous.

	<p align="center">
	<img src="https://i.imgur.com/A6OI9ID.png" width="500" style="border-radius: 20px">
	</p>
	
* ### Professeurs
	 Les professeurs peuvent se connecter sur la partie **[/session_prof](https://florianpetiot.github.io/JYDAGO-Demo/session_prof/)** du site grâce à un identifiant et un mot de passe. Ils peuvent obtenir la liste de leurs élèves et ainsi les suivre dans leur parcours, mais également voir ceux qui n'ont pas encore enregistré leurs questions.

	De plus, le service ne fonctionnant que dans le réseau interne de mon lycée, ils peuvent également télécharger un fichier Excel correctement formaté pour avoir les questions depuis chez eux.

	Un professeur peut être associé à plusieurs matières *(ex: prof enseignant maths/physique)*. Il peut être associé à des élèves en particulier *(ex: classe)* et donc ne pas recevoir l'ensemble des questions portant sur les maths par exemple. Enfin, un élève peut être associé à plusieurs professeurs *(ex: classe à 2 profs)*.

	<p align="center">
	<img src="https://i.imgur.com/K1Z9a7R.png" width="45%" style="border-radius: 20px">
	<span>&nbsp;&nbsp;&nbsp;</span>
	<img src="https://i.imgur.com/tbIieW8.png" width="45%" style="border-radius: 20px">
	</p>

* ### Administration
	Les administrateurs peuvent se connecter sur la partie **[/session_prof](https://florianpetiot.github.io/JYDAGO-Demo/session_prof/)** du site grâce à un identifiant et un mot de passe. Ils ont accès aux fonctionnalités des professeurs, en étant associés à toutes les spécialités en même temps. De plus, ils peuvent télécharger un "rapport de base" contenant plusieurs informations :

	* La liste de toutes les incohérences présentes dans la base, suite à de potentielles mauvaises entrées d'élèves, ou bien une mauvaise mise en place de la base de données
    * Quelques statistiques comme le nombre d'élèves ne s'étant jamais connecté ou encore le nombre de questions non enregistrées.

	<p align="center">
	<img src="https://i.imgur.com/AgdEnK9.png" width="500" style="border-radius: 20px">
	</p>

# Notes
J'ai réalisé ce projet en classe de Terminal en totale autonomie, et il porte sur des notions, de loin, hors programme. Le code est donc évidemment loin d’être parfait et sécurisé, mais le faire m’aura permis de découvrir beaucoup de choses.

# Crédits
* **[Florian Petiot](https://github.com/florianpetiot)**
