-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : db
-- Généré le : mer. 22 mars 2023 à 19:10
-- Version du serveur : 8.0.31
-- Version de PHP : 8.0.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `recherche_information_document`
--

-- --------------------------------------------------------

--
-- Structure de la table `document`
--

CREATE TABLE `document` (
  `id` int NOT NULL,
  `titre` varchar(250) NOT NULL,
  `path` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;


-- --------------------------------------------------------

--
-- Structure de la table `document_mot`
--

CREATE TABLE `document_mot` (
  `idDocument` int NOT NULL,
  `idMot` int NOT NULL,
  `frequence` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Déchargement des données de la table `document_mot`
--


--
-- Structure de la table `mot`
--

CREATE TABLE `mot` (
  `id` int NOT NULL,
  `mot` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;



--
-- Index pour la table `document`
--
ALTER TABLE `document`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `document_mot`
--
ALTER TABLE `document_mot`
  ADD KEY `idMot` (`idMot`),
  ADD KEY `idDocument` (`idDocument`);

--
-- Index pour la table `mot`
--
ALTER TABLE `mot`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `document`
--
ALTER TABLE `document`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;

--
-- AUTO_INCREMENT pour la table `mot`
--
ALTER TABLE `mot`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `document_mot`
--
ALTER TABLE `document_mot`
  ADD CONSTRAINT `document_mot_ibfk_1` FOREIGN KEY (`idDocument`) REFERENCES `document` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `document_mot_ibfk_2` FOREIGN KEY (`idMot`) REFERENCES `mot` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
