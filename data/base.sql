-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 20, 2020 at 02:04 PM
-- Server version: 10.4.13-MariaDB
-- PHP Version: 7.4.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sos_base`
--

-- --------------------------------------------------------

--
-- Table structure for table `setting`
--

DROP TABLE IF EXISTS `setting`;
CREATE TABLE `setting` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user` char(56) NOT NULL,
  `skey` char(26) NOT NULL,
  `sval` char(128) NOT NULL,
  `_updated` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `setting_option`
--

DROP TABLE IF EXISTS `setting_option`;
CREATE TABLE `setting_option` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` char(28) NOT NULL,
  `domain` char(32) NOT NULL,
  `multi_val` tinyint(1) DEFAULT 0,
  `type` char(28) NOT NULL,
  `type_variant` varchar(512) DEFAULT NULL,
  `title` char(52) NOT NULL,
  `description` varchar(256) NOT NULL,
  `_updated` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `status`
--

DROP TABLE IF EXISTS `status`;
CREATE TABLE `status` (
  `name` char(35) NOT NULL,
  `info` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `status`
--

INSERT INTO `status` (`name`, `info`) VALUES
('ACTIVE', ''),
('BANNED', ''),
('CLOSED', ''),
('COMPLETED', ''),
('DISABLED', ''),
('ENDED', ''),
('OPEN', ''),
('PENDING', ''),
('STARTED', ''),
('SUSPENDED', '');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `_id` char(12) NOT NULL,
  `status` char(25) NOT NULL DEFAULT 'PENDING',
  `email` char(55) NOT NULL,
  `phone` char(16) DEFAULT NULL,
  `password` varchar(128) NOT NULL,
  `_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `user_alias`
--

DROP TABLE IF EXISTS `user_alias`;
CREATE TABLE `user_alias` (
  `user` char(12) NOT NULL,
  `alias` char(16) NOT NULL,
  `_updated` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `user_dashlist`
--

DROP TABLE IF EXISTS `user_dashlist`;
CREATE TABLE `user_dashlist` (
  `id` int(10) UNSIGNED NOT NULL,
  `path` varchar(256) NOT NULL,
  `onclick` char(32) DEFAULT NULL,
  `classname` char(56) DEFAULT NULL,
  `title` char(56) NOT NULL,
  `subtitle` char(72) DEFAULT NULL,
  `icon` char(72) DEFAULT NULL,
  `sort` tinyint(3) UNSIGNED DEFAULT 0,
  `description` varchar(256) NOT NULL,
  `_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `user_follower`
--

DROP TABLE IF EXISTS `user_follower`;
CREATE TABLE `user_follower` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user` char(12) NOT NULL,
  `follower` char(12) NOT NULL,
  `_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `user_profile`
--

DROP TABLE IF EXISTS `user_profile`;
CREATE TABLE `user_profile` (
  `user` char(32) NOT NULL,
  `name` char(28) NOT NULL,
  `surname` char(28) NOT NULL,
  `middle_name` char(16) DEFAULT NULL,
  `sex` char(8) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `country_code` char(2) NOT NULL,
  `state_code` char(5) DEFAULT NULL,
  `city_code` char(8) DEFAULT NULL,
  `zip_code` char(32) DEFAULT NULL,
  `address` char(128) DEFAULT NULL,
  `_updated` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `user_referer`
--

DROP TABLE IF EXISTS `user_referer`;
CREATE TABLE `user_referer` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user` char(12) NOT NULL,
  `parent` char(12) NOT NULL,
  `level` tinyint(4) NOT NULL,
  `_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `setting`
--
ALTER TABLE `setting`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `setting_option`
--
ALTER TABLE `setting_option`
  ADD PRIMARY KEY (`id`),
  ADD KEY `name` (`name`);

--
-- Indexes for table `status`
--
ALTER TABLE `status`
  ADD PRIMARY KEY (`name`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_alias`
--
ALTER TABLE `user_alias`
  ADD PRIMARY KEY (`user`) USING BTREE,
  ADD UNIQUE KEY `alias` (`alias`);

--
-- Indexes for table `user_dashlist`
--
ALTER TABLE `user_dashlist`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_follower`
--
ALTER TABLE `user_follower`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_profile`
--
ALTER TABLE `user_profile`
  ADD PRIMARY KEY (`user`);

--
-- Indexes for table `user_referer`
--
ALTER TABLE `user_referer`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `setting`
--
ALTER TABLE `setting`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `setting_option`
--
ALTER TABLE `setting_option`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_dashlist`
--
ALTER TABLE `user_dashlist`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_follower`
--
ALTER TABLE `user_follower`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_referer`
--
ALTER TABLE `user_referer`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
