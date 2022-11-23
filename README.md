# JYDAGO
Ce projet a été réalisé en mai 2022 sous l'initiative de mon professeur d'NSI (Numérique et Sciences informatiques).

L'idée de départ était de faciliter le recensement des questions du Grand Oral (épreuve du baccalauréat). Cela consiste à enregistrer 2 questions et leur spécialité respective pour chaque élève de terminale. Et ce, en évitant de passer par une grande feuille Excel.

Ce projet est donc actuellement mis à disposition des élèves, des professeurs et de l'administration de mon lycée.

# Somaire
* **[Démo](#démo)**
* **[Fonctionalités](#fonctionalités)**
	* **[Élèves](#élèves)**
	* **[Professeurs](#professeurs)**
	* **[Administration](#administration)**
* **[Notes](#notes)**
* **[Crédit](#Crédits)**

# Démo
Vous pouvez essayer une démo scénarisée *(bientôt disponible)* dans laquelle vous pourrez vous connecter en tant qu'élève, que professionnel ou qu'administrateur.

# Fonctionalités
* ### Élèves
	Les élèves peuvent se connecter via la racine du site grâce à un identifiant et un mot de passe. S'il a déjà enregistré des questions précédemment, elles apparaissent dans leur case respective. Sinon il dispose d'un temps imparti pour les écrire, après lequel la partie élève du site passera en "lecture seule".

	Les cases "spécialité" lui proposent uniquement les 2 spécialités qu'il a choisies pour sa Terminal. En plus d'un message de bienvenue, l'expérience utilisateur est ainsi personnalisée pour tous.

	[img]
	
* ### Professeurs
	 Les professeurs peuvent se connecter sur la partie /session_prof du site grâce à un identifiant et un mot de passe. Ils peuvent obtenir la liste de leurs élèves et ainsi les suivre dans leur parcours, mais également voir ceux qui n'ont pas encore enregistré leurs questions.

	De plus, le service ne fonctionnant que dans le réseau interne au lycée, ils peuvent également télécharger un fichier correctement formaté pour avoir les questions depuis chez eux.

	Un professeur peut être associé à plusieurs matières (ex: prof enseignant math/physique). Il peut être associé à des élèves en particulier (ex: classe) et donc ne pas recevoir toutes les questions portant sur les maths par exemple. Enfin, un élève peut être associé à plusieurs professeurs (ex: classe à 2 profs)

	[img]

* ### Administration
	Les comptes administrateur peuvent se connecter sur la partie /session_prof du site grâce à un identifiant et un mot de passe. Ils ont accès aux fonctionnalités des professeurs, en étant associés à toutes les spécialités en même temps. De plus, ils peuvent télécharger un "rapport de base" contenant plusieurs informations :

	* La liste de toutes les incohérences présente dans la base, suite à de potentielles mauvaises entré d'élèves
    * Quelques statistiques comme le nombre d'élèves ne s'étant jamais connecté ou encore le nombre de questions non enregistrées.

# Notes
J'ai réalisé ce projet en classe de Terminal en totale autonomie, et porte sur des notions, de loin, hors programme. Le code est donc évidemment loin d’être parfait et sécurisé, mais le faire m’aura permis de découvrir beaucoup de choses.

# Crédits
* **[Florian Petiot](https://github.com/florianpetiot)**
