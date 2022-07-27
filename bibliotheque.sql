-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Apr 09, 2022 at 08:50 AM
-- Server version: 8.0.27
-- PHP Version: 7.4.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bibliotheque`
--

-- --------------------------------------------------------

--
-- Table structure for table `emprunter`
--

DROP TABLE IF EXISTS `emprunter`;
CREATE TABLE IF NOT EXISTS `emprunter` (
  `NumUser` int NOT NULL,
  `NumLivre` int NOT NULL,
  `DateEmprunt` varchar(25) NOT NULL,
  PRIMARY KEY (`NumUser`,`NumLivre`,`DateEmprunt`),
  KEY `FKNumLivre` (`NumLivre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `livres`
--

DROP TABLE IF EXISTS `livres`;
CREATE TABLE IF NOT EXISTS `livres` (
  `id` int NOT NULL AUTO_INCREMENT,
  `Libelle` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `NomAuteur` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `Couverture` varchar(255) NOT NULL,
  `Fichier` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `livres`
--

INSERT INTO `livres` (`id`, `Libelle`, `NomAuteur`, `Couverture`, `Fichier`) VALUES
(1, 'Cantique de Noël', 'Charles Dickens', '..\\Bibliotheque-Sterling\\Books\\Cantique_de_Noel_-_Charles_Dickens_1\\Cover_-_Cantique_de_Noel_-_Charles_Dickens_1_1.png', '..\\Bibliotheque-Sterling\\Books\\Cantique_de_Noel_-_Charles_Dickens_1\\Cantique_de_Noel_-_Charles_Dickens_1_1.pdf'),
(2, 'Le songe d\'une nuit d\'été', 'William Shakespeare', '..\\Bibliotheque-Sterling\\Books\\Le_songe_dune_nuit_dete_William_Shakespeare\\Cover_-_Le_songe_dune_nuit_dete_-_William_Shakespearepng_1.png', '..\\Bibliotheque-Sterling\\Books\\Le_songe_dune_nuit_dete_William_Shakespeare\\Le_songe_dune_nuit_dete_William_Shakespeare_1.pdf'),
(3, 'Germinal', 'Emile Zola', '..\\Bibliotheque-Sterling\\Books\\Germinal_-_Emile_Zola_1\\Cover_-_Germinal_-_Emile_Zola_1_1.png', '..\\Bibliotheque-Sterling\\Books\\Germinal_-_Emile_Zola_1\\Germinal_-_Emile_Zola_1_1.pdf'),
(4, 'Le Portrait de Dorian Gray', 'Oscar Wilde', '..\\Bibliotheque-Sterling\\Books\\Le_Portrait_de_Dorian_Gray_-_Oscar_Wilde_1\\Cover_-_Le_Portrait_de_Dorian_Gray_-_Oscar_Wilde_1_1.png', '..\\Bibliotheque-Sterling\\Books\\Le_Portrait_de_Dorian_Gray_-_Oscar_Wilde_1\\Le_Portrait_de_Dorian_Gray_-_Oscar_Wilde_1_1.pdf');

-- --------------------------------------------------------

--
-- Table structure for table `utilisateurs`
--

DROP TABLE IF EXISTS `utilisateurs`;
CREATE TABLE IF NOT EXISTS `utilisateurs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `NomUser` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
  `MotDePasse` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `Role` varchar(25) NOT NULL DEFAULT 'user',
  `Statut` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'actif',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id`, `NomUser`, `MotDePasse`, `Role`, `Statut`) VALUES
(1, 'admin', '$2y$10$/ZbdcdQ3RJyXJCgqJ1KGhO2dowlnb9cODjaMiF7GSDxgrRxJc.9ay', 'admin', 'actif'),
(2, 'User', '$2y$10$n718r5d.k0IwZZYfcVDDVeXStZgOzL3p1V4GT5CiKs23tsfA8akHe', 'user', 'actif'),
(3, 'VotreNom', '$2y$10$sdC2kVPKSdW20d.TQUvVe.1e1mu6KAw.V1P70KBEF.0DgdkWa3A2e', 'user', 'actif');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `emprunter`
--
ALTER TABLE `emprunter`
  ADD CONSTRAINT `FKNumLivre` FOREIGN KEY (`NumLivre`) REFERENCES `livres` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `FKNumUser` FOREIGN KEY (`NumUser`) REFERENCES `utilisateurs` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
