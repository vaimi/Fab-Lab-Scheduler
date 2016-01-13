-- phpMyAdmin SQL Dump
-- version 4.4.14
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 13, 2016 at 06:26 PM
-- Server version: 5.6.26
-- PHP Version: 5.5.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `fablab_scheduler`
--
CREATE DATABASE IF NOT EXISTS `fablab_scheduler` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `fablab_scheduler`;

-- --------------------------------------------------------

--
-- Table structure for table `aauth_groups`
--

DROP TABLE IF EXISTS `aauth_groups`;
CREATE TABLE IF NOT EXISTS `aauth_groups` (
  `id` int(11) unsigned NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `definition` text,
  `email_prefixes` varchar(100) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `aauth_groups`
--

INSERT INTO `aauth_groups` (`id`, `name`, `definition`, `email_prefixes`) VALUES
(1, 'Admin', 'Administrator group', ''),
(2, 'Public', 'Public group', ''),
(3, 'University of Oulu', 'Students and employees of University of Oulu', '*.oulu.fi|oulu.fi');

-- --------------------------------------------------------

--
-- Table structure for table `aauth_perms`
--

DROP TABLE IF EXISTS `aauth_perms`;
CREATE TABLE IF NOT EXISTS `aauth_perms` (
  `id` int(11) unsigned NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `definition` text
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `aauth_perms`
--

INSERT INTO `aauth_perms` (`id`, `name`, `definition`) VALUES
(1, 'user_management', NULL),
(2, 'machine_management', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `aauth_perm_to_group`
--

DROP TABLE IF EXISTS `aauth_perm_to_group`;
CREATE TABLE IF NOT EXISTS `aauth_perm_to_group` (
  `perm_id` int(11) unsigned NOT NULL DEFAULT '0',
  `group_id` int(11) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `aauth_perm_to_group`
--

INSERT INTO `aauth_perm_to_group` (`perm_id`, `group_id`) VALUES
(1, 1),
(2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `aauth_perm_to_user`
--

DROP TABLE IF EXISTS `aauth_perm_to_user`;
CREATE TABLE IF NOT EXISTS `aauth_perm_to_user` (
  `perm_id` int(11) unsigned NOT NULL DEFAULT '0',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `aauth_pms`
--

DROP TABLE IF EXISTS `aauth_pms`;
CREATE TABLE IF NOT EXISTS `aauth_pms` (
  `id` int(11) unsigned NOT NULL,
  `sender_id` int(11) unsigned NOT NULL,
  `receiver_id` int(11) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text,
  `date_sent` datetime DEFAULT NULL,
  `date_read` datetime DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

--
-- Table structure for table `aauth_system_variables`
--

DROP TABLE IF EXISTS `aauth_system_variables`;
CREATE TABLE IF NOT EXISTS `aauth_system_variables` (
  `id` int(11) unsigned NOT NULL,
  `data_key` varchar(100) NOT NULL,
  `value` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `aauth_users`
--

DROP TABLE IF EXISTS `aauth_users`;
CREATE TABLE IF NOT EXISTS `aauth_users` (
  `id` int(11) unsigned NOT NULL,
  `email` varchar(100) NOT NULL,
  `pass` varchar(64) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `banned` tinyint(1) DEFAULT '0',
  `last_login` datetime DEFAULT NULL,
  `last_activity` datetime DEFAULT NULL,
  `last_login_attempt` datetime DEFAULT NULL,
  `forgot_exp` text,
  `remember_time` datetime DEFAULT NULL,
  `remember_exp` text,
  `verification_code` text,
  `totp_secret` varchar(16) DEFAULT NULL,
  `ip_address` text,
  `login_attempts` int(11) DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=70 DEFAULT CHARSET=utf8;

--
-- Table structure for table `aauth_user_to_group`
--

DROP TABLE IF EXISTS `aauth_user_to_group`;
CREATE TABLE IF NOT EXISTS `aauth_user_to_group` (
  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `group_id` int(11) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `aauth_user_variables`
--

DROP TABLE IF EXISTS `aauth_user_variables`;
CREATE TABLE IF NOT EXISTS `aauth_user_variables` (
  `id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `data_key` varchar(100) NOT NULL,
  `value` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `conversations`
--

DROP TABLE IF EXISTS `conversations`;
CREATE TABLE IF NOT EXISTS `conversations` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` varchar(100) NOT NULL,
  `last_message_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `conversation_participants`
--

DROP TABLE IF EXISTS `conversation_participants`;
CREATE TABLE IF NOT EXISTS `conversation_participants` (
  `id` int(11) NOT NULL,
  `conversation_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `read_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `extended_users_information`
--

DROP TABLE IF EXISTS `extended_users_information`;
CREATE TABLE IF NOT EXISTS `extended_users_information` (
  `id` int(11) unsigned NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `surname` varchar(512) DEFAULT NULL,
  `company` varchar(50) NOT NULL,
  `address_street` varchar(512) DEFAULT NULL,
  `address_postal_code` varchar(512) DEFAULT NULL,
  `phone_number` varchar(30) DEFAULT NULL,
  `student_number` varchar(20) DEFAULT NULL,
  `quota` int(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `Machine`
--

DROP TABLE IF EXISTS `Machine`;
CREATE TABLE IF NOT EXISTS `Machine` (
  `MachineID` int(11) NOT NULL,
  `MachineGroupID` int(11) NOT NULL,
  `MachineName` varchar(100) NOT NULL,
  `Manufacturer` varchar(50) NOT NULL,
  `Model` varchar(50) NOT NULL,
  `NeedSupervision` tinyint(1) NOT NULL,
  `Description` mediumtext,
  `active` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=latin1;

--
-- Table structure for table `MachineGroup`
--

DROP TABLE IF EXISTS `MachineGroup`;
CREATE TABLE IF NOT EXISTS `MachineGroup` (
  `MachineGroupID` int(11) NOT NULL,
  `Name` varchar(50) NOT NULL,
  `Description` text,
  `NeedSupervision` tinyint(4) NOT NULL,
  `active` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=latin1;

--
-- Table structure for table `Reservation`
--

DROP TABLE IF EXISTS `Reservation`;
CREATE TABLE IF NOT EXISTS `Reservation` (
  `ReservationID` int(11) NOT NULL,
  `MachineID` int(11) NOT NULL,
  `aauth_usersID` int(11) unsigned NOT NULL,
  `StartTime` datetime NOT NULL,
  `EndTime` datetime NOT NULL,
  `QRCode` varchar(256) NOT NULL,
  `PassCode` varchar(10) NOT NULL,
  `State` tinyint(4) NOT NULL DEFAULT '1'
) ENGINE=InnoDB AUTO_INCREMENT=175 DEFAULT CHARSET=latin1;

--
-- Table structure for table `Setting`
--

DROP TABLE IF EXISTS `Setting`;
CREATE TABLE IF NOT EXISTS `Setting` (
  `SettingKey` varchar(20) NOT NULL,
  `SettingValue` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `Setting`
--

INSERT INTO `Setting` (`SettingKey`, `SettingValue`) VALUES
('default_tokens', '5'),
('interval', 'Months'),
('nightslot_pre_time', '30'),
('nightslot_threshold', '120'),
('reservation_deadline', '23:00'),
('reservation_timespan', '4');

-- --------------------------------------------------------

--
-- Table structure for table `Supervision`
--

DROP TABLE IF EXISTS `Supervision`;
CREATE TABLE IF NOT EXISTS `Supervision` (
  `SupervisionID` int(11) NOT NULL,
  `aauth_usersID` int(11) unsigned NOT NULL,
  `aauth_groupsID` int(11) NOT NULL,
  `StartTime` datetime NOT NULL,
  `EndTime` datetime NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=252 DEFAULT CHARSET=latin1;

--
-- Table structure for table `UserLevel`
--

DROP TABLE IF EXISTS `UserLevel`;
CREATE TABLE IF NOT EXISTS `UserLevel` (
  `MachineID` int(11) NOT NULL,
  `aauth_usersID` int(11) unsigned NOT NULL,
  `Level` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `aauth_groups`
--
ALTER TABLE `aauth_groups`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `aauth_perms`
--
ALTER TABLE `aauth_perms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `aauth_perm_to_group`
--
ALTER TABLE `aauth_perm_to_group`
  ADD PRIMARY KEY (`perm_id`,`group_id`);

--
-- Indexes for table `aauth_perm_to_user`
--
ALTER TABLE `aauth_perm_to_user`
  ADD PRIMARY KEY (`perm_id`,`user_id`);

--
-- Indexes for table `aauth_pms`
--
ALTER TABLE `aauth_pms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `full_index` (`id`,`sender_id`,`receiver_id`,`date_read`);

--
-- Indexes for table `aauth_system_variables`
--
ALTER TABLE `aauth_system_variables`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `aauth_users`
--
ALTER TABLE `aauth_users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `aauth_user_to_group`
--
ALTER TABLE `aauth_user_to_group`
  ADD PRIMARY KEY (`user_id`,`group_id`);

--
-- Indexes for table `aauth_user_variables`
--
ALTER TABLE `aauth_user_variables`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id_index` (`user_id`);

--
-- Indexes for table `conversations`
--
ALTER TABLE `conversations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `conversation_participants`
--
ALTER TABLE `conversation_participants`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `extended_users_information`
--
ALTER TABLE `extended_users_information`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `Machine`
--
ALTER TABLE `Machine`
  ADD PRIMARY KEY (`MachineID`,`MachineGroupID`),
  ADD KEY `fk_Machines_Machinegroups_idx` (`MachineGroupID`);

--
-- Indexes for table `MachineGroup`
--
ALTER TABLE `MachineGroup`
  ADD PRIMARY KEY (`MachineGroupID`);

--
-- Indexes for table `Reservation`
--
ALTER TABLE `Reservation`
  ADD PRIMARY KEY (`ReservationID`,`MachineID`,`aauth_usersID`),
  ADD KEY `fk_Reservations_Machines1_idx` (`MachineID`),
  ADD KEY `fk_Reservations_aauth_users1_idx` (`aauth_usersID`);

--
-- Indexes for table `Setting`
--
ALTER TABLE `Setting`
  ADD PRIMARY KEY (`SettingKey`);

--
-- Indexes for table `Supervision`
--
ALTER TABLE `Supervision`
  ADD PRIMARY KEY (`SupervisionID`),
  ADD KEY `fk_Supervisions_aauth_users1_idx` (`aauth_usersID`),
  ADD KEY `aauth_groups` (`aauth_groupsID`);

--
-- Indexes for table `UserLevel`
--
ALTER TABLE `UserLevel`
  ADD PRIMARY KEY (`MachineID`,`aauth_usersID`),
  ADD KEY `fk_Userlevels_Machines1_idx` (`MachineID`),
  ADD KEY `fk_Userlevels_aauth_users1_idx` (`aauth_usersID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `aauth_groups`
--
ALTER TABLE `aauth_groups`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `aauth_perms`
--
ALTER TABLE `aauth_perms`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `aauth_pms`
--
ALTER TABLE `aauth_pms`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `aauth_system_variables`
--
ALTER TABLE `aauth_system_variables`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `aauth_users`
--
ALTER TABLE `aauth_users`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `aauth_user_variables`
--
ALTER TABLE `aauth_user_variables`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `conversations`
--
ALTER TABLE `conversations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `conversation_participants`
--
ALTER TABLE `conversation_participants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `Machine`
--
ALTER TABLE `Machine`
  MODIFY `MachineID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `MachineGroup`
--
ALTER TABLE `MachineGroup`
  MODIFY `MachineGroupID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `Reservation`
--
ALTER TABLE `Reservation`
  MODIFY `ReservationID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `Supervision`
--
ALTER TABLE `Supervision`
  MODIFY `SupervisionID` int(11) NOT NULL AUTO_INCREMENT;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `Machine`
--
ALTER TABLE `Machine`
  ADD CONSTRAINT `fk_Machines_Machinegroups` FOREIGN KEY (`MachineGroupID`) REFERENCES `MachineGroup` (`MachineGroupID`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `Reservation`
--
ALTER TABLE `Reservation`
  ADD CONSTRAINT `fk_Reservations_Machines1` FOREIGN KEY (`MachineID`) REFERENCES `Machine` (`MachineID`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_Reservations_aauth_users1` FOREIGN KEY (`aauth_usersID`) REFERENCES `aauth_users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `Supervision`
--
ALTER TABLE `Supervision`
  ADD CONSTRAINT `fk_Supervisions_aauth_users1` FOREIGN KEY (`aauth_usersID`) REFERENCES `aauth_users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `UserLevel`
--
ALTER TABLE `UserLevel`
  ADD CONSTRAINT `fk_Userlevels_Machines1` FOREIGN KEY (`MachineID`) REFERENCES `Machine` (`MachineID`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_Userlevels_aauth_users1` FOREIGN KEY (`aauth_usersID`) REFERENCES `aauth_users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
