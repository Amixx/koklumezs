-- phpMyAdmin SQL Dump
-- version 4.4.15.10
-- https://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 05, 2019 at 12:08 PM
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
-- Table structure for table `evaluations`
--

CREATE TABLE IF NOT EXISTS `evaluations` (
  `id` int(11) NOT NULL,
  `title` mediumtext COLLATE utf8_unicode_ci NOT NULL COMMENT 'Virsraksts',
  `type` enum('zvaigznes','teksts') COLLATE utf8_unicode_ci NOT NULL COMMENT 'Tips'
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `evaluations`
--

INSERT INTO `evaluations` (`id`, `title`, `type`) VALUES
(1, 'Uzraksti komentāru, kā patika lekcija ', 'teksts'),
(2, 'Novērtē cik labi saprati akustiku', 'zvaigznes'),
(3, 'Novērtē cik labi izprati ritmu', 'zvaigznes');

-- --------------------------------------------------------

--
-- Table structure for table `handdifficulties`
--

CREATE TABLE IF NOT EXISTS `handdifficulties` (
  `id` int(11) NOT NULL,
  `hand` enum('left','right') COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Roka',
  `category` mediumtext COLLATE utf8_unicode_ci NOT NULL COMMENT 'Kategorija'
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `handdifficulties`
--

INSERT INTO `handdifficulties` (`id`, `hand`, `category`) VALUES
(1, 'left', 'Kreisās rokas kategorija iespējams'),
(2, 'left', 'Kreisā roka baigi laba'),
(3, 'right', 'Labā roka laba'),
(4, 'right', 'Labā roka slikta'),
(5, 'right', 'Labā roka roka');

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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `lectures`
--

INSERT INTO `lectures` (`id`, `title`, `description`, `created`, `updated`, `author`, `complexity`) VALUES
(1, 'Lekcija 1', '<h2><strong>Lorem ipsum dolor sit amet,</strong></h2>\r\n\r\n<p>consectetur adipiscing elit. Sed vel mi pretium, elementum nisi nec, consequat eros. Donec nunc lorem, viverra ut vulputate non, ultrices eu eros. Integer vestibulum ex ut risus bibendum iaculis. Aliquam varius, nisi ut commodo commodo, purus tortor semper urna, non pharetra est ligula a est. Quisque sed consequat nunc, ac tincidunt dui. Sed auctor facilisis ultrices. Suspendisse eu nulla rhoncus, rhoncus neque consectetur, <span style="background-color:#FF0000">faucibus justo</span>. In tincidunt molestie convallis. Suspendisse elementum rutrum nisl lobortis feugiat. Quisque viverra felis tellus, eget consequat velit tempus in. Quisque finibus mauris ac pulvinar lobortis.</p>\r\n', '2019-10-31 07:49:29', '2019-11-04 09:51:17', 1, '1'),
(3, 'Lekcija 2', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed vel mi pretium, elementum nisi nec, consequat eros. Donec nunc lorem, viverra ut vulputate non, ultrices eu eros. Integer vestibulum ex ut risus bibendum iaculis. Aliquam varius, nisi ut commodo commodo, purus tortor semper urna, non pharetra est ligula a est. Quisque sed consequat nunc, ac tincidunt dui. Sed auctor facilisis ultrices. Suspendisse eu nulla rhoncus, rhoncus neque consectetur, faucibus justo. In tincidunt molestie convallis. Suspendisse elementum rutrum nisl lobortis feugiat. Quisque viverra felis tellus, eget consequat velit tempus in. Quisque finibus mauris ac pulvinar lobortis.\r\nLorem ipsum dolor sit amet, consectetur adipiscing elit. Sed vel mi pretium, elementum nisi nec, consequat eros. Donec nunc lorem, viverra ut vulputate non, ultrices eu eros. Integer vestibulum ex ut risus bibendum iaculis. Aliquam varius, nisi ut commodo commodo, purus tortor semper urna, non pharetra est ligula a est. Quisque sed consequat nunc, ac tincidunt dui. Sed auctor facilisis ultrices. Suspendisse eu nulla rhoncus, rhoncus neque consectetur, faucibus justo. In tincidunt molestie convallis. Suspendisse elementum rutrum nisl lobortis feugiat. Quisque viverra felis tellus, eget consequat velit tempus in. Quisque finibus mauris ac pulvinar lobortis.', '2019-10-31 07:49:29', '2019-11-01 12:39:02', 2, '6'),
(4, 'Lekcija 3', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed vel mi pretium, elementum nisi nec, consequat eros. Donec nunc lorem, viverra ut vulputate non, ultrices eu eros. Integer vestibulum ex ut risus bibendum iaculis. Aliquam varius, nisi ut commodo commodo, purus tortor semper urna, non pharetra est ligula a est. Quisque sed consequat nunc, ac tincidunt dui. Sed auctor facilisis ultrices. Suspendisse eu nulla rhoncus, rhoncus neque consectetur, faucibus justo. In tincidunt molestie convallis. Suspendisse elementum rutrum nisl lobortis feugiat. Quisque viverra felis tellus, eget consequat velit tempus in. Quisque finibus mauris ac pulvinar lobortis.', '2019-10-31 07:49:29', '2019-11-01 08:16:01', 1, '7'),
(5, 'Lekcija atkal', 'apraksts', '2019-11-01 07:16:22', '2019-11-01 08:09:57', 1, '7');

-- --------------------------------------------------------

--
-- Table structure for table `lecturesdifficulties`
--

CREATE TABLE IF NOT EXISTS `lecturesdifficulties` (
  `id` int(11) NOT NULL,
  `diff_id` int(11) NOT NULL COMMENT 'Parametrs',
  `lecture_id` int(11) NOT NULL COMMENT 'Lekcija',
  `value` varchar(50) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Vērtība'
) ENGINE=InnoDB AUTO_INCREMENT=67 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `lecturesdifficulties`
--

INSERT INTO `lecturesdifficulties` (`id`, `diff_id`, `lecture_id`, `value`) VALUES
(26, 1, 5, '1'),
(27, 2, 5, '3'),
(28, 3, 5, '4'),
(63, 2, 1, '2'),
(64, 3, 1, '3'),
(65, 4, 1, '6'),
(66, 5, 1, '7');

-- --------------------------------------------------------

--
-- Table structure for table `lecturesevaluations`
--

CREATE TABLE IF NOT EXISTS `lecturesevaluations` (
  `id` int(11) NOT NULL,
  `lecture_id` int(11) NOT NULL COMMENT 'Lekcija',
  `evaluation_id` int(11) NOT NULL COMMENT 'Novērtējums'
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `lecturesevaluations`
--

INSERT INTO `lecturesevaluations` (`id`, `lecture_id`, `evaluation_id`) VALUES
(9, 3, 3),
(10, 4, 2),
(19, 1, 1),
(20, 1, 2),
(21, 1, 3);

-- --------------------------------------------------------

--
-- Table structure for table `lecturesfiles`
--

CREATE TABLE IF NOT EXISTS `lecturesfiles` (
  `id` int(11) NOT NULL,
  `title` mediumtext COLLATE utf8_unicode_ci NOT NULL COMMENT 'Virsraksts',
  `file` mediumtext COLLATE utf8_unicode_ci NOT NULL COMMENT 'Fails',
  `lecture_id` int(11) NOT NULL COMMENT 'Lekcija'
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `lecturesfiles`
--

INSERT INTO `lecturesfiles` (`id`, `title`, `file`, `lecture_id`) VALUES
(1, 'Video lekcija 1', '/sys/files/global/video/BEWARE%20of%20Boris.mp4', 5),
(2, 'Video lekcija 2', '/sys/files/global/video/y2mate.com%20-%20this_is_getting_out_of_hand_fan_mail_unboxing_c5byF_jaMEw_1080p.mp4', 3),
(3, 'Video lekcija 3', '/sys/files/global/video/This%20is%20getting%20out%20of%20hand..%20(fan%20mail%20unboxing).mp4', 4),
(4, 'Video lekcija cita', '/sys/files/global/video/Top%2019%20reasons%20you%20should%20get%20a%20Lada.mp4', 1),
(5, 'PDF fails ar notīm', '/sys/files/global/pdf/sample.pdf', 1),
(6, 'Audio test', '/sys/files/global/audio/file_example_MP3_5MG.mp3', 1);

-- --------------------------------------------------------

--
-- Table structure for table `lectureshanddifficulties`
--

CREATE TABLE IF NOT EXISTS `lectureshanddifficulties` (
  `id` int(11) NOT NULL,
  `lecture_id` int(11) NOT NULL COMMENT 'Lekcija',
  `category_id` int(11) NOT NULL COMMENT 'Kategorija'
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `lectureshanddifficulties`
--

INSERT INTO `lectureshanddifficulties` (`id`, `lecture_id`, `category_id`) VALUES
(6, 5, 2),
(7, 5, 4),
(22, 1, 1),
(23, 1, 3);

-- --------------------------------------------------------

--
-- Table structure for table `studentgoals`
--

CREATE TABLE IF NOT EXISTS `studentgoals` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL COMMENT 'Lietotājs',
  `type` enum('Šobrīd','Vēlamais') COLLATE utf8_unicode_ci NOT NULL COMMENT 'Veids',
  `diff_id` int(11) NOT NULL COMMENT 'Parametrs',
  `value` int(11) NOT NULL COMMENT 'Vērtība'
) ENGINE=InnoDB AUTO_INCREMENT=86 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `studentgoals`
--

INSERT INTO `studentgoals` (`id`, `user_id`, `type`, `diff_id`, `value`) VALUES
(81, 2, 'Šobrīd', 1, 1),
(82, 2, 'Šobrīd', 2, 1),
(83, 2, 'Šobrīd', 3, 4),
(84, 2, 'Vēlamais', 2, 5),
(85, 2, 'Vēlamais', 4, 8);

-- --------------------------------------------------------

--
-- Table structure for table `studenthandgoals`
--

CREATE TABLE IF NOT EXISTS `studenthandgoals` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL COMMENT 'Students',
  `category_id` int(11) NOT NULL COMMENT 'Kategorija'
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `studenthandgoals`
--

INSERT INTO `studenthandgoals` (`id`, `user_id`, `category_id`) VALUES
(31, 2, 1),
(32, 2, 4);

-- --------------------------------------------------------

--
-- Table structure for table `userlectureevaluations`
--

CREATE TABLE IF NOT EXISTS `userlectureevaluations` (
  `id` int(11) NOT NULL,
  `lecture_id` int(11) NOT NULL COMMENT 'Lekcija',
  `evaluation_id` int(11) NOT NULL COMMENT 'Novērtējums',
  `user_id` int(11) NOT NULL COMMENT 'Students',
  `evaluation` mediumtext COLLATE utf8_unicode_ci NOT NULL COMMENT 'Vērtējums',
  `created` datetime NOT NULL COMMENT 'Izveidots'
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `userlectureevaluations`
--

INSERT INTO `userlectureevaluations` (`id`, `lecture_id`, `evaluation_id`, `user_id`, `evaluation`, `created`) VALUES
(10, 1, 1, 2, 'Baigi labs bija', '2019-11-04 12:54:51'),
(11, 1, 2, 2, '4', '2019-11-04 12:54:51'),
(12, 1, 3, 2, '5', '2019-11-04 12:54:51'),
(13, 4, 2, 2, '3', '2019-11-04 13:23:30');

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
  `opentime` timestamp NULL DEFAULT NULL COMMENT 'Atvēršanas laiks',
  `sent` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Nosūtīts e-pasts'
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `userlectures`
--

INSERT INTO `userlectures` (`id`, `lecture_id`, `user_id`, `assigned`, `created`, `opened`, `opentime`, `sent`) VALUES
(1, 3, 2, 1, '2019-11-05 09:33:13', 1, '2019-11-04 08:17:05', 1),
(2, 1, 2, 1, '2019-11-05 09:35:37', 1, '2019-11-04 08:01:43', 1),
(3, 4, 2, 1, '2019-11-05 09:21:35', 1, '2019-11-04 08:01:50', 1),
(4, 4, 4, 1, '2019-11-04 14:56:48', 0, NULL, 0),
(5, 5, 2, 1, '2019-11-05 09:06:33', 1, '2019-11-05 07:06:33', 0);

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
  `last_lecture` int(11) DEFAULT NULL COMMENT 'Pēdējā atvērtā lekcija',
  `dont_bother` datetime DEFAULT NULL COMMENT 'līdz kuram datumam viņu netraucēt',
  `goal` mediumtext NOT NULL COMMENT 'Mērķis'
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `phone_number`, `email`, `password`, `user_level`, `password_hash`, `password_reset_token`, `verification_token`, `auth_key`, `status`, `updated_at`, `passwordResetTokenExpire`, `created_at`, `last_login`, `last_lecture`, `dont_bother`, `goal`) VALUES
(1, 'test', 'tester', '112', 'test@test.lv', '$2y$13$Q6qwbz72XUw4acnoTQsl7eHA5SugtEmxynv08ScuyVmeV0SGuf45C', 'Admin', '', '', '', '', 10, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2019-11-05 09:51:31', 0, '2019-11-14 23:59:59', ''),
(2, 'Students', 'Studentiņš', '112', 'student@test.lv', '$2y$13$Q6qwbz72XUw4acnoTQsl7eHA5SugtEmxynv08ScuyVmeV0SGuf45C', 'Student', '', '', '', 'tjI7VodU51a8pA-Qng971MFVzehC9dBp', 10, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2019-11-05 09:51:19', 1, NULL, 'tetsts'),
(3, 'Cits', 'Students', '112', 'student@student.lv', '$2y$13$NHRuxo0M.5KuK58JwVNOCew6WcZxKEoO1ev5oE00c1V.aEAN7zY4K', 'Student', '', '', '', 'eQeIfL_xqWz44ILGMEHp6JOMasD0OWPJ', 9, '2019-10-31 12:40:31', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, NULL, ''),
(4, 'Jauns', 'Students', '1242', 'students@jauns.lv', '$2y$13$SqB.4oPTqEKKiJiN50JZ7.wr4iJoApYkZM.wljUjcj3z8p2akpjaW', 'Student', '', '', '', 'QJzeNchYlrafPiZ_YZ9u51NlLd9w5IIC', 10, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, NULL, '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `difficulties`
--
ALTER TABLE `difficulties`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `evaluations`
--
ALTER TABLE `evaluations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `handdifficulties`
--
ALTER TABLE `handdifficulties`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lectures`
--
ALTER TABLE `lectures`
  ADD PRIMARY KEY (`id`),
  ADD KEY `author` (`author`) USING BTREE;

--
-- Indexes for table `lecturesdifficulties`
--
ALTER TABLE `lecturesdifficulties`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lecture_id` (`lecture_id`),
  ADD KEY `diff_id` (`diff_id`);

--
-- Indexes for table `lecturesevaluations`
--
ALTER TABLE `lecturesevaluations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lecture` (`lecture_id`),
  ADD KEY `evaluation` (`evaluation_id`);

--
-- Indexes for table `lecturesfiles`
--
ALTER TABLE `lecturesfiles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lecture` (`lecture_id`);

--
-- Indexes for table `lectureshanddifficulties`
--
ALTER TABLE `lectureshanddifficulties`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lecture` (`lecture_id`),
  ADD KEY `category` (`category_id`) USING BTREE;

--
-- Indexes for table `studentgoals`
--
ALTER TABLE `studentgoals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `diff` (`diff_id`),
  ADD KEY `user` (`user_id`);

--
-- Indexes for table `studenthandgoals`
--
ALTER TABLE `studenthandgoals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user` (`user_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `userlectureevaluations`
--
ALTER TABLE `userlectureevaluations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lecture` (`lecture_id`),
  ADD KEY `evaluation` (`evaluation_id`),
  ADD KEY `student` (`user_id`);

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
-- AUTO_INCREMENT for table `evaluations`
--
ALTER TABLE `evaluations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `handdifficulties`
--
ALTER TABLE `handdifficulties`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `lectures`
--
ALTER TABLE `lectures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `lecturesdifficulties`
--
ALTER TABLE `lecturesdifficulties`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=67;
--
-- AUTO_INCREMENT for table `lecturesevaluations`
--
ALTER TABLE `lecturesevaluations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=22;
--
-- AUTO_INCREMENT for table `lecturesfiles`
--
ALTER TABLE `lecturesfiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `lectureshanddifficulties`
--
ALTER TABLE `lectureshanddifficulties`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=24;
--
-- AUTO_INCREMENT for table `studentgoals`
--
ALTER TABLE `studentgoals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=86;
--
-- AUTO_INCREMENT for table `studenthandgoals`
--
ALTER TABLE `studenthandgoals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=33;
--
-- AUTO_INCREMENT for table `userlectureevaluations`
--
ALTER TABLE `userlectureevaluations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=14;
--
-- AUTO_INCREMENT for table `userlectures`
--
ALTER TABLE `userlectures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
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
-- Constraints for table `lecturesdifficulties`
--
ALTER TABLE `lecturesdifficulties`
  ADD CONSTRAINT `lecturesdifficulties_ibfk_1` FOREIGN KEY (`diff_id`) REFERENCES `difficulties` (`id`),
  ADD CONSTRAINT `lecturesdifficulties_ibfk_2` FOREIGN KEY (`lecture_id`) REFERENCES `lectures` (`id`);

--
-- Constraints for table `lecturesevaluations`
--
ALTER TABLE `lecturesevaluations`
  ADD CONSTRAINT `lecturesevaluations_ibfk_1` FOREIGN KEY (`evaluation_id`) REFERENCES `evaluations` (`id`),
  ADD CONSTRAINT `lecturesevaluations_ibfk_2` FOREIGN KEY (`lecture_id`) REFERENCES `lectures` (`id`);

--
-- Constraints for table `lecturesfiles`
--
ALTER TABLE `lecturesfiles`
  ADD CONSTRAINT `lecturesfiles_ibfk_1` FOREIGN KEY (`lecture_id`) REFERENCES `lectures` (`id`);

--
-- Constraints for table `lectureshanddifficulties`
--
ALTER TABLE `lectureshanddifficulties`
  ADD CONSTRAINT `lectureshanddifficulties_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `handdifficulties` (`id`),
  ADD CONSTRAINT `lectureshanddifficulties_ibfk_2` FOREIGN KEY (`lecture_id`) REFERENCES `lectures` (`id`);

--
-- Constraints for table `studentgoals`
--
ALTER TABLE `studentgoals`
  ADD CONSTRAINT `studentgoals_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `studentgoals_ibfk_2` FOREIGN KEY (`diff_id`) REFERENCES `difficulties` (`id`);

--
-- Constraints for table `studenthandgoals`
--
ALTER TABLE `studenthandgoals`
  ADD CONSTRAINT `studenthandgoals_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `studenthandgoals_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `handdifficulties` (`id`);

--
-- Constraints for table `userlectureevaluations`
--
ALTER TABLE `userlectureevaluations`
  ADD CONSTRAINT `userlectureevaluations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `userlectureevaluations_ibfk_2` FOREIGN KEY (`lecture_id`) REFERENCES `lectures` (`id`),
  ADD CONSTRAINT `userlectureevaluations_ibfk_3` FOREIGN KEY (`evaluation_id`) REFERENCES `evaluations` (`id`);

--
-- Constraints for table `userlectures`
--
ALTER TABLE `userlectures`
  ADD CONSTRAINT `userlectures_ibfk_1` FOREIGN KEY (`lecture_id`) REFERENCES `lectures` (`id`),
  ADD CONSTRAINT `userlectures_ibfk_2` FOREIGN KEY (`assigned`) REFERENCES `users` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
