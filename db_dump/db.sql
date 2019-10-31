-- phpMyAdmin SQL Dump
-- version 4.4.15.10
-- https://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 31, 2019 at 03:17 PM
-- Server version: 5.5.64-MariaDB
-- PHP Version: 5.4.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `demo55izstrade`
--

-- --------------------------------------------------------

--
-- Table structure for table `difficulties`
--

CREATE TABLE IF NOT EXISTS `difficulties` (
  `id` int(11) NOT NULL,
  `name` mediumtext COLLATE utf8_unicode_ci NOT NULL COMMENT 'Parametrs'
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `difficulties`
--

INSERT INTO `difficulties` (`id`, `name`) VALUES
(1, 'Ritms'),
(2, 'Melodija'),
(3, 'Ātrums'),
(4, 'Tehnika'),
(5, 'Akordi');

-- --------------------------------------------------------

--
-- Table structure for table `lectures`
--

CREATE TABLE IF NOT EXISTS `lectures` (
  `id` int(11) NOT NULL,
  `title` mediumtext COLLATE utf8_unicode_ci NOT NULL COMMENT 'Nosaukums',
  `description` mediumtext COLLATE utf8_unicode_ci COMMENT 'Apraksts',
  `created` timestamp NULL DEFAULT NULL COMMENT 'Izveidota',
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Atjaunota',
  `author` int(11) NOT NULL COMMENT 'Autors',
  `complexity` enum('1','2','3','4','5','6','7','8','9','10') COLLATE utf8_unicode_ci NOT NULL COMMENT 'Sarežģītība'
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `lectures`
--

INSERT INTO `lectures` (`id`, `title`, `description`, `created`, `updated`, `author`, `complexity`) VALUES
(1, 'Lekcija 1', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed vel mi pretium, elementum nisi nec, consequat eros. Donec nunc lorem, viverra ut vulputate non, ultrices eu eros. Integer vestibulum ex ut risus bibendum iaculis. Aliquam varius, nisi ut commodo commodo, purus tortor semper urna, non pharetra est ligula a est. Quisque sed consequat nunc, ac tincidunt dui. Sed auctor facilisis ultrices. Suspendisse eu nulla rhoncus, rhoncus neque consectetur, faucibus justo. In tincidunt molestie convallis. Suspendisse elementum rutrum nisl lobortis feugiat. Quisque viverra felis tellus, eget consequat velit tempus in. Quisque finibus mauris ac pulvinar lobortis.', NULL, '2019-10-31 08:53:19', 1, '1'),
(3, 'Lekcija 2', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed vel mi pretium, elementum nisi nec, consequat eros. Donec nunc lorem, viverra ut vulputate non, ultrices eu eros. Integer vestibulum ex ut risus bibendum iaculis. Aliquam varius, nisi ut commodo commodo, purus tortor semper urna, non pharetra est ligula a est. Quisque sed consequat nunc, ac tincidunt dui. Sed auctor facilisis ultrices. Suspendisse eu nulla rhoncus, rhoncus neque consectetur, faucibus justo. In tincidunt molestie convallis. Suspendisse elementum rutrum nisl lobortis feugiat. Quisque viverra felis tellus, eget consequat velit tempus in. Quisque finibus mauris ac pulvinar lobortis.\r\nLorem ipsum dolor sit amet, consectetur adipiscing elit. Sed vel mi pretium, elementum nisi nec, consequat eros. Donec nunc lorem, viverra ut vulputate non, ultrices eu eros. Integer vestibulum ex ut risus bibendum iaculis. Aliquam varius, nisi ut commodo commodo, purus tortor semper urna, non pharetra est ligula a est. Quisque sed consequat nunc, ac tincidunt dui. Sed auctor facilisis ultrices. Suspendisse eu nulla rhoncus, rhoncus neque consectetur, faucibus justo. In tincidunt molestie convallis. Suspendisse elementum rutrum nisl lobortis feugiat. Quisque viverra felis tellus, eget consequat velit tempus in. Quisque finibus mauris ac pulvinar lobortis.', NULL, '2019-10-31 08:53:30', 2, '6'),
(4, 'Lekcija 3', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed vel mi pretium, elementum nisi nec, consequat eros. Donec nunc lorem, viverra ut vulputate non, ultrices eu eros. Integer vestibulum ex ut risus bibendum iaculis. Aliquam varius, nisi ut commodo commodo, purus tortor semper urna, non pharetra est ligula a est. Quisque sed consequat nunc, ac tincidunt dui. Sed auctor facilisis ultrices. Suspendisse eu nulla rhoncus, rhoncus neque consectetur, faucibus justo. In tincidunt molestie convallis. Suspendisse elementum rutrum nisl lobortis feugiat. Quisque viverra felis tellus, eget consequat velit tempus in. Quisque finibus mauris ac pulvinar lobortis.', '2019-10-31 07:49:29', '2019-10-31 08:53:41', 1, '7');

-- --------------------------------------------------------

--
-- Table structure for table `userlectures`
--

CREATE TABLE IF NOT EXISTS `userlectures` (
  `id` int(11) NOT NULL,
  `lecture_id` int(11) NOT NULL COMMENT 'Lekcija',
  `user_id` int(11) NOT NULL COMMENT 'Students',
  `assigned` int(11) NOT NULL COMMENT 'Administrators',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Izveidots',
  `opened` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Atvērta',
  `opentime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Atvēršanas laiks'
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `userlectures`
--

INSERT INTO `userlectures` (`id`, `lecture_id`, `user_id`, `assigned`, `created`, `opened`, `opentime`) VALUES
(1, 3, 2, 1, '2019-10-31 09:41:26', 0, '0000-00-00 00:00:00'),
(2, 1, 2, 1, '2019-10-31 09:41:41', 0, '0000-00-00 00:00:00'),
(3, 4, 2, 1, '2019-10-31 09:41:48', 0, '0000-00-00 00:00:00'),
(4, 4, 4, 1, '2019-10-31 10:09:43', 0, '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(250) DEFAULT NULL,
  `last_name` varchar(250) DEFAULT NULL,
  `phone_number` varchar(30) DEFAULT NULL,
  `email` varchar(500) NOT NULL,
  `password` varchar(250) NOT NULL,
  `user_level` enum('Admin','Student') NOT NULL DEFAULT 'Student',
  `password_hash` mediumtext NOT NULL,
  `password_reset_token` mediumtext NOT NULL,
  `verification_token` mediumtext NOT NULL,
  `auth_key` mediumtext NOT NULL,
  `status` int(2) NOT NULL DEFAULT '10',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `passwordResetTokenExpire` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_login` datetime DEFAULT NULL,
  `last_lecture` int(11) NOT NULL COMMENT 'Pēdējā atvērtā lekcija',
  `dont_bother` datetime DEFAULT NULL COMMENT 'līdz kuram datumam viņu netraucēt'
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `phone_number`, `email`, `password`, `user_level`, `password_hash`, `password_reset_token`, `verification_token`, `auth_key`, `status`, `updated_at`, `passwordResetTokenExpire`, `created_at`, `last_login`, `last_lecture`, `dont_bother`) VALUES
(1, 'test', 'tester', '112', 'test@test.lv', '$2y$13$Q6qwbz72XUw4acnoTQsl7eHA5SugtEmxynv08ScuyVmeV0SGuf45C', 'Admin', '', '', '', '', 10, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2019-10-31 12:28:44', 0, '2019-11-14 23:59:59'),
(2, 'Students', 'Studentiņš', '112', 'student@test.lv', '$2y$13$Q6qwbz72XUw4acnoTQsl7eHA5SugtEmxynv08ScuyVmeV0SGuf45C', 'Student', '', '', '', 'tjI7VodU51a8pA-Qng971MFVzehC9dBp', 10, '2019-10-31 07:15:26', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, NULL),
(3, 'Cits', 'Students', '112', 'student@student.lv', '$2y$13$NHRuxo0M.5KuK58JwVNOCew6WcZxKEoO1ev5oE00c1V.aEAN7zY4K', 'Student', '', '', '', 'eQeIfL_xqWz44ILGMEHp6JOMasD0OWPJ', 9, '2019-10-31 12:40:31', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, NULL),
(4, 'Jauns', 'Students', '1242', 'students@jauns.lv', '$2y$13$SqB.4oPTqEKKiJiN50JZ7.wr4iJoApYkZM.wljUjcj3z8p2akpjaW', 'Student', '', '', '', 'QJzeNchYlrafPiZ_YZ9u51NlLd9w5IIC', 10, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `difficulties`
--
ALTER TABLE `difficulties`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lectures`
--
ALTER TABLE `lectures`
  ADD PRIMARY KEY (`id`),
  ADD KEY `author` (`author`) USING BTREE;

--
-- Indexes for table `userlectures`
--
ALTER TABLE `userlectures`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lecture` (`lecture_id`),
  ADD KEY `user` (`user_id`),
  ADD KEY `admin` (`assigned`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `difficulties`
--
ALTER TABLE `difficulties`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `lectures`
--
ALTER TABLE `lectures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `userlectures`
--
ALTER TABLE `userlectures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `lectures`
--
ALTER TABLE `lectures`
  ADD CONSTRAINT `lectures_ibfk_1` FOREIGN KEY (`author`) REFERENCES `users` (`id`);

--
-- Constraints for table `userlectures`
--
ALTER TABLE `userlectures`
  ADD CONSTRAINT `userlectures_ibfk_1` FOREIGN KEY (`lecture_id`) REFERENCES `lectures` (`id`),
  ADD CONSTRAINT `userlectures_ibfk_2` FOREIGN KEY (`assigned`) REFERENCES `users` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
