-- --------------------------------------------------------
-- Hôte:                         127.0.0.1
-- Version du serveur:           5.7.33 - MySQL Community Server (GPL)
-- SE du serveur:                Win64
-- HeidiSQL Version:             11.2.0.6213
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Listage de la structure de la base pour jydago
CREATE DATABASE IF NOT EXISTS `jydago` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `jydago`;

-- Listage de la structure de la table jydago. liste
CREATE TABLE IF NOT EXISTS `liste` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `nom` char(50) DEFAULT NULL,
  `prenom` char(50) DEFAULT NULL,
  `classe` char(10) DEFAULT NULL,
  `mdp` char(50) DEFAULT NULL,
  `spe1` char(10) DEFAULT NULL,
  `spe2` char(10) DEFAULT NULL,
  `idprof1` char(50) DEFAULT NULL,
  `idprof2` char(50) DEFAULT NULL,
  `question1` varchar(300) DEFAULT NULL,
  `q1spe1` char(10) DEFAULT NULL,
  `q1spe2` char(10) DEFAULT NULL,
  `question2` varchar(300) DEFAULT NULL,
  `q2spe1` char(10) DEFAULT NULL,
  `q2spe2` char(10) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `acces` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_liste_specialites` (`spe1`),
  KEY `FK_liste_specialites_2` (`spe2`),
  CONSTRAINT `FK_liste_specialites` FOREIGN KEY (`spe1`) REFERENCES `specialites` (`id_spe`),
  CONSTRAINT `FK_liste_specialites_2` FOREIGN KEY (`spe2`) REFERENCES `specialites` (`id_spe`)
) ENGINE=InnoDB AUTO_INCREMENT=189 DEFAULT CHARSET=utf8;

-- Listage des données de la table jydago.liste : ~88 rows (environ)
/*!40000 ALTER TABLE `liste` DISABLE KEYS */;
INSERT INTO `liste` (`id`, `nom`, `prenom`, `classe`, `mdp`, `spe1`, `spe2`, `idprof1`, `idprof2`, `question1`, `q1spe1`, `q1spe2`, `question2`, `q2spe1`, `q2spe2`, `date`, `acces`) VALUES
	(101, 'PETIOT', 'Florian', 'TA', 'dbc0f004854457f59fb16ab863a3a1722cef553f', 'MATHS', 'NSI', '903', '901/903', 'Le hasard est il vraiment aléatoire en informatique ?', 'NSI', 'MATHS', '', '', '', '2022-11-24 17:59:47', 321),
	(102, 'XXXX', 'Xxxx', 'TA', 'c8306ae139ac98f432932286151dc0ec55580eca', 'HGGSP', 'LLCE', '907', '906', 'En quoi le succès de Wikipédia est-il révélateur des nouveaux enjeux liés à la connaissance ?', 'HGGSP', '', '', '', '', '2022-11-24 17:59:53', 41),
	(103, 'XXXX', 'Xxxx', 'TA', '934385f53d1bd0c1b8493e44d0dfd4c8e88a04bb', 'PC', 'SVT', '904', '905', 'Est-il possible d’éliminer les déchets résultant de l’industrie nucléaire ?', 'PC', '', 'Comment la chimie dite « verte » est-elle au service de la protection de l\'environnement ?', 'PC', 'SVT', '2022-11-24 17:59:58', 23),
	(104, 'XXXX', 'Xxxx', 'TA', '78a8efcbaaa1a9a30f9f327aa89d0b6acaaffb03', 'HGGSP', 'SES', '907', '902', 'Peut-on parler de classes sociales au niveau européen ?', 'SES', '', 'Les migrants climatiques, quels visages du réchauffement climatique ?', 'HGGSP', '', '2022-11-24 18:00:11', 2),
	(105, 'XXXX', 'Xxxx', 'TA', 'e114c448f4ab8554ad14eff3d66dfeb3965ce8fc', 'MATHS', 'NSI', '908', '901/903', 'Quels sont les enjeux de l’intelligence artificielle ?', 'NSI', '', 'Comment peut-on calculer l’aire sous la courbe d’une fonction ?', 'MATHS', '', '2022-11-24 18:00:23', 22),
	(106, 'XXXX', 'Xxxx', 'TB', '7224f997fc148baa0b7f81c1eda6fcc3fd003db0', 'HGGSP', 'NSI', '907', '901/903', 'Cyberguerre : la 3ème guerre mondiale ?', 'HGGSP', 'NSI', '', '', '', '2022-11-24 18:00:31', 2),
	(107, 'XXXX', 'Xxxx', 'TB', '524e05dc77239f3a15dab766aaa59a9e432efde7', 'MATHS', 'SVT', '908', '902', '', '', '', '', '', NULL, '2022-11-24 18:00:37', 1),
	(108, 'XXXX', 'Xxxx', 'TB', '17503a6b2326f09fbc4e3a7c03874c7333002038', 'MATHS', 'NSI', '908', '901/903', '', '', '', '', '', NULL, '2022-11-24 18:00:42', 1),
	(109, 'XXXX', 'Xxxx', 'TC', 'a1422e6a168630cdd214ac5e31ca01ae1bee8d92', 'SES', 'PC', '902', '904', '', '', '', '', '', '', '2022-11-24 18:00:47', 4),
	(110, 'XXXX', 'Xxxx', 'TC', '5e796e48332af4142b10ca0f86e65d9bfdb05884', 'SVT', 'PC', '905', '904', '', '', '', '', '', '', '2022-11-24 18:00:55', 2),
	(111, 'XXXX', 'Xxxx', 'TC', '6216f8a75fd5bb3d5f22b6f9958cdede3fc086c2', 'SVT', 'PC', '905', '904', NULL, NULL, NULL, NULL, NULL, NULL, '2022-11-24 18:00:59', 1);
/*!40000 ALTER TABLE `liste` ENABLE KEYS */;

-- Listage de la structure de la table jydago. profs
CREATE TABLE IF NOT EXISTS `profs` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `nom` char(50) DEFAULT NULL,
  `prenom` char(50) DEFAULT NULL,
  `mdp` char(50) DEFAULT NULL,
  `specialite` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1000 DEFAULT CHARSET=utf8;

-- Listage des données de la table jydago.profs : ~9 rows (environ)
/*!40000 ALTER TABLE `profs` DISABLE KEYS */;
INSERT INTO `profs` (`id`, `nom`, `prenom`, `mdp`, `specialite`) VALUES
	(901, 'XXXX', 'Xxxx', 'a071f3cf900d868205b8cc4c7e6aa7885cac3643', 'NSI'),
	(902, 'XXXX', 'Xxxx', '0e2a8d2c235e4c425c2afa27ca7a0d089e5116ef', 'SES'),
	(903, 'XXXX', 'Xxxx', '437aa7b54ef6800c19f152c9ddcfebee7dd315e9', 'MATHS/NSI'),
	(904, 'XXXX', 'Xxxx', '6f2c73e47a4a7da5ed35dc3954c0ea3e2fe863a3', 'PC'),
	(905, 'XXXX', 'Xxxx', '71ef3ed0695341b63e469eea3478e82b3aab9a27', 'SVT'),
	(906, 'XXXX', 'Xxxx', '624ec06de69083ea715768cae1166b0f194c7639', 'LLCE'),
	(907, 'XXXX', 'Xxxx', 'bd7c809d7d47026e7390ba3c6b253d24efcbe8cf', 'HGGSP'),
	(908, 'XXXX', 'Xxxx', '2262b29b0cc33c64b49508a2c98aec7ccc60c51d', 'MATHS'),
	(999, 'Admin', 'Admin', 'afc97ea131fd7e2695a98ef34013608f97f34e1d', 'TOUTES');
/*!40000 ALTER TABLE `profs` ENABLE KEYS */;

-- Listage de la structure de la table jydago. specialites
CREATE TABLE IF NOT EXISTS `specialites` (
  `id_spe` char(10) NOT NULL,
  `libelle` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_spe`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Listage des données de la table jydago.specialites : ~14 rows (environ)
/*!40000 ALTER TABLE `specialites` DISABLE KEYS */;
INSERT INTO `specialites` (`id_spe`, `libelle`) VALUES
	('ARTS', 'Arts'),
	('BIOEC', 'Biologie-écologie'),
	('EPS', 'Éducation physique, pratiques et culture sportives'),
	('HGGSP', 'Histoire-géographie, géopolitique et sciences politiques'),
	('HLP', 'Humanités, littérature et philosophie'),
	('LLCA', 'Littératures, langues et cultures de l’Antiquité'),
	('LLCE', 'Langues, littératures et cultures étrangères'),
	('MATHS', 'Mathématiques'),
	('NSI', 'Numérique et sciences informatiques'),
	('PC', 'Physique-Chimie'),
	('SES', 'Sciences économiques et sociales'),
	('SI', 'Sciences de l’ingénieur'),
	('SVT', 'Sciences de la vie et de la terre'),
	('TOUTES', 'Toutes spécialités');
/*!40000 ALTER TABLE `specialites` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
