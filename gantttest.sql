-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 28, 2021 at 02:45 PM
-- Server version: 5.7.24
-- PHP Version: 7.2.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gantttest`
--

-- --------------------------------------------------------

--
-- Table structure for table `pg_asso_projets_taches`
--

CREATE TABLE `pg_asso_projets_taches` (
  `asso_pt_id` int(11) NOT NULL,
  `asso_pt_projet_id` int(11) NOT NULL,
  `asso_pt_tache_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Dumping data for table `pg_asso_projets_taches`
--

INSERT INTO `pg_asso_projets_taches` (`asso_pt_id`, `asso_pt_projet_id`, `asso_pt_tache_id`) VALUES
(20, 10, 1),
(22, 10, 16),
(23, 10, 17),
(24, 10, 18);

-- --------------------------------------------------------

--
-- Table structure for table `pg_asso_projets_taches_utilisateurs`
--

CREATE TABLE `pg_asso_projets_taches_utilisateurs` (
  `asso_ptu_id` int(11) NOT NULL,
  `asso_ptu_pt_id` int(11) NOT NULL,
  `asso_ptu_tu_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Dumping data for table `pg_asso_projets_taches_utilisateurs`
--

INSERT INTO `pg_asso_projets_taches_utilisateurs` (`asso_ptu_id`, `asso_ptu_pt_id`, `asso_ptu_tu_id`) VALUES
(28, 20, 30),
(30, 22, 32),
(31, 23, 33),
(32, 24, 34);

-- --------------------------------------------------------

--
-- Table structure for table `pg_asso_taches_utilisateurs`
--

CREATE TABLE `pg_asso_taches_utilisateurs` (
  `asso_tu_id` int(11) NOT NULL,
  `asso_tu_tache_id` int(11) NOT NULL,
  `asso_tu_utilisateur_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Dumping data for table `pg_asso_taches_utilisateurs`
--

INSERT INTO `pg_asso_taches_utilisateurs` (`asso_tu_id`, `asso_tu_tache_id`, `asso_tu_utilisateur_id`) VALUES
(30, 1, 1),
(32, 16, 1),
(33, 17, 1),
(34, 18, 1);

-- --------------------------------------------------------

--
-- Table structure for table `pg_projets`
--

CREATE TABLE `pg_projets` (
  `prj_id` int(11) NOT NULL,
  `prj_nom` varchar(255) COLLATE utf8mb4_bin NOT NULL,
  `prj_date_debut` date NOT NULL,
  `prj_date_fin` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Dumping data for table `pg_projets`
--

INSERT INTO `pg_projets` (`prj_id`, `prj_nom`, `prj_date_debut`, `prj_date_fin`) VALUES
(10, 'test', '2021-01-28', '2021-02-06');

-- --------------------------------------------------------

--
-- Table structure for table `pg_taches`
--

CREATE TABLE `pg_taches` (
  `tch_id` int(11) NOT NULL,
  `tch_nom` varchar(255) COLLATE utf8mb4_bin NOT NULL,
  `tch_date_debut` date NOT NULL,
  `tch_date_fin` date NOT NULL,
  `tch_delai` date NOT NULL,
  `tch_avancement` int(11) NOT NULL,
  `tch_duree` int(11) NOT NULL,
  `tch_categorie` varchar(255) COLLATE utf8mb4_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Dumping data for table `pg_taches`
--

INSERT INTO `pg_taches` (`tch_id`, `tch_nom`, `tch_date_debut`, `tch_date_fin`, `tch_delai`, `tch_avancement`, `tch_duree`, `tch_categorie`) VALUES
(1, 'admin', '2021-01-22', '2021-01-22', '2021-01-22', 0, 0, 'admin'),
(16, 'test', '2021-01-28', '2021-01-30', '2021-01-31', 0, 3, 'R&eacute;daction (Vn+1)'),
(17, '1 t&acirc;che du deuxi&egrave;me projet bis', '2021-01-29', '2021-01-30', '2021-01-29', 0, 5, 'aze'),
(18, 'azearararz', '2021-01-29', '2021-02-05', '2021-01-30', 0, 2, 'jppd&eacute;cons');

-- --------------------------------------------------------

--
-- Table structure for table `pg_utilisateurs`
--

CREATE TABLE `pg_utilisateurs` (
  `ut_id` int(11) NOT NULL,
  `ut_nom` varchar(255) COLLATE utf8mb4_bin NOT NULL,
  `ut_email` varchar(255) COLLATE utf8mb4_bin NOT NULL,
  `ut_type` varchar(255) COLLATE utf8mb4_bin NOT NULL,
  `ut_mdp` varchar(255) COLLATE utf8mb4_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Dumping data for table `pg_utilisateurs`
--

INSERT INTO `pg_utilisateurs` (`ut_id`, `ut_nom`, `ut_email`, `ut_type`, `ut_mdp`) VALUES
(1, 'dada', 'greg.beaucaire@gmail.com', 'admin', '47c7ef39cfa6b7bd1286d9c83424f322741549e849ad1af19a8416e861434da5');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `pg_asso_projets_taches`
--
ALTER TABLE `pg_asso_projets_taches`
  ADD PRIMARY KEY (`asso_pt_id`),
  ADD KEY `asso_pt_projet_id` (`asso_pt_projet_id`),
  ADD KEY `asso_pt_tache_id` (`asso_pt_tache_id`);

--
-- Indexes for table `pg_asso_projets_taches_utilisateurs`
--
ALTER TABLE `pg_asso_projets_taches_utilisateurs`
  ADD PRIMARY KEY (`asso_ptu_id`),
  ADD KEY `asso_ptu_pt_id` (`asso_ptu_pt_id`),
  ADD KEY `asso_ptu_tu_id` (`asso_ptu_tu_id`);

--
-- Indexes for table `pg_asso_taches_utilisateurs`
--
ALTER TABLE `pg_asso_taches_utilisateurs`
  ADD PRIMARY KEY (`asso_tu_id`),
  ADD KEY `asso_tu_tache_id` (`asso_tu_tache_id`),
  ADD KEY `asso_tu_utilisateur_id` (`asso_tu_utilisateur_id`);

--
-- Indexes for table `pg_projets`
--
ALTER TABLE `pg_projets`
  ADD PRIMARY KEY (`prj_id`);

--
-- Indexes for table `pg_taches`
--
ALTER TABLE `pg_taches`
  ADD PRIMARY KEY (`tch_id`);

--
-- Indexes for table `pg_utilisateurs`
--
ALTER TABLE `pg_utilisateurs`
  ADD PRIMARY KEY (`ut_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `pg_asso_projets_taches`
--
ALTER TABLE `pg_asso_projets_taches`
  MODIFY `asso_pt_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `pg_asso_projets_taches_utilisateurs`
--
ALTER TABLE `pg_asso_projets_taches_utilisateurs`
  MODIFY `asso_ptu_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `pg_asso_taches_utilisateurs`
--
ALTER TABLE `pg_asso_taches_utilisateurs`
  MODIFY `asso_tu_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `pg_projets`
--
ALTER TABLE `pg_projets`
  MODIFY `prj_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `pg_taches`
--
ALTER TABLE `pg_taches`
  MODIFY `tch_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `pg_utilisateurs`
--
ALTER TABLE `pg_utilisateurs`
  MODIFY `ut_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `pg_asso_projets_taches`
--
ALTER TABLE `pg_asso_projets_taches`
  ADD CONSTRAINT `pg_asso_projets_taches_ibfk_1` FOREIGN KEY (`asso_pt_projet_id`) REFERENCES `pg_projets` (`prj_id`),
  ADD CONSTRAINT `pg_asso_projets_taches_ibfk_2` FOREIGN KEY (`asso_pt_tache_id`) REFERENCES `pg_taches` (`tch_id`);

--
-- Constraints for table `pg_asso_projets_taches_utilisateurs`
--
ALTER TABLE `pg_asso_projets_taches_utilisateurs`
  ADD CONSTRAINT `pg_asso_projets_taches_utilisateurs_ibfk_1` FOREIGN KEY (`asso_ptu_pt_id`) REFERENCES `pg_asso_projets_taches` (`asso_pt_id`),
  ADD CONSTRAINT `pg_asso_projets_taches_utilisateurs_ibfk_2` FOREIGN KEY (`asso_ptu_tu_id`) REFERENCES `pg_asso_taches_utilisateurs` (`asso_tu_id`);

--
-- Constraints for table `pg_asso_taches_utilisateurs`
--
ALTER TABLE `pg_asso_taches_utilisateurs`
  ADD CONSTRAINT `pg_asso_taches_utilisateurs_ibfk_1` FOREIGN KEY (`asso_tu_tache_id`) REFERENCES `pg_taches` (`tch_id`),
  ADD CONSTRAINT `pg_asso_taches_utilisateurs_ibfk_2` FOREIGN KEY (`asso_tu_utilisateur_id`) REFERENCES `pg_utilisateurs` (`ut_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
