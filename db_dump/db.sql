-- phpMyAdmin SQL Dump
-- version 4.4.15.10
-- https://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 03, 2019 at 04:07 PM
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
(2, 'Labā roka'),
(3, 'Kreisā roka'),
(4, 'Akordu maiņas biežums'),
(5, 'Akordi');

-- --------------------------------------------------------

--
-- Table structure for table `evaluations`
--

CREATE TABLE IF NOT EXISTS `evaluations` (
  `id` int(11) NOT NULL,
  `title` mediumtext COLLATE utf8_unicode_ci NOT NULL COMMENT 'Virsraksts',
  `type` enum('zvaigznes','teksts') COLLATE utf8_unicode_ci NOT NULL COMMENT 'Tips',
  `stars` int(11) NOT NULL DEFAULT '5' COMMENT 'Zvaigžņu skaits',
  `star_text` mediumtext COLLATE utf8_unicode_ci NOT NULL COMMENT 'Zvaigžņu teksti',
  `is_scale` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'Algoritma skala'
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `evaluations`
--

INSERT INTO `evaluations` (`id`, `title`, `type`, `stars`, `star_text`, `is_scale`) VALUES
(1, 'Uzdevuma sarežģītība', 'zvaigznes', 10, 'a:10:{i:0;s:31:"Viss tik viegls, ka garlaicīgi";i:1;s:43:"Ļoti ļoti viegli, noteikti vajag grūtāk";i:2;s:43:"Izspēlēju vienu reizi un jau viss skaidrs";i:3;s:19:"Diezgan vienkārši";i:4;s:59:"Nācās pastrādāt, bet tiku galā bez milzīgas piepūles";i:5;s:10:"Tiku galā";i:6;s:14:"Diezgan grūti";i:7;s:35:"Itkā saprotu, bet pirksti neklausa";i:8;s:38:"Kaut ko mēģinu, bet pārāk nesanāk";i:9;s:22:"Vispār neko nesaprotu";}', 1),
(2, 'Uzdevumu daudzums', 'zvaigznes', 3, 'a:3:{i:0;s:28:"Par daudz, vajadzētu mazāk";i:1;s:24:"Tieši tik daudz ir labi";i:2;s:27:"Par maz, vajadzētu vairāk";}', 0),
(3, 'Video apjoms', 'zvaigznes', 3, 'a:3:{i:0;s:33:"Vajadzētu mazāk, bija par daudz";i:1;s:22:"Ideāli, tā turpināt";i:2;s:45:"Bija par maz, dodiet uz nākamo reizi vairāk";}', 0),
(4, 'Komentāri', 'teksts', 5, '', 0);

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
  `complexity` int(11) NOT NULL DEFAULT '0' COMMENT 'Sarežģītība',
  `season` enum('Visas','Rudens','Ziema','Pavasaris','Vasara') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Visas' COMMENT 'Gadskārta'
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `lectures`
--

INSERT INTO `lectures` (`id`, `title`, `description`, `created`, `updated`, `author`, `complexity`, `season`) VALUES
(1, ' Bēdu manu + kreisā', '<h2><strong>Lorem ipsum dolor sit amet,</strong></h2>\r\n\r\n<p>consectetur adipiscing elit. Sed vel mi pretium, elementum nisi nec, consequat eros. Donec nunc lorem, viverra ut vulputate non, ultrices eu eros. Integer vestibulum ex ut risus bibendum iaculis. Aliquam varius, nisi ut commodo commodo, purus tortor semper urna, non pharetra est ligula a est. Quisque sed consequat nunc, ac tincidunt dui. Sed auctor facilisis ultrices. Suspendisse eu nulla rhoncus, rhoncus neque consectetur, <span style="background-color:#FF0000">faucibus justo</span>. In tincidunt molestie convallis. Suspendisse elementum rutrum nisl lobortis feugiat. Quisque viverra felis tellus, eget consequat velit tempus in. Quisque finibus mauris ac pulvinar lobortis.</p>\r\n', '2019-10-31 07:49:29', '2019-12-02 10:12:42', 1, 27, 'Visas'),
(3, ' Bēdu manu lielu bēdu 2', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed vel mi pretium, elementum nisi nec, consequat eros. Donec nunc lorem, viverra ut vulputate non, ultrices eu eros. Integer vestibulum ex ut risus bibendum iaculis. Aliquam varius, nisi ut commodo commodo, purus tortor semper urna, non pharetra est ligula a est. Quisque sed consequat nunc, ac tincidunt dui. Sed auctor facilisis ultrices. Suspendisse eu nulla rhoncus, rhoncus neque consectetur, faucibus justo. In tincidunt molestie convallis. Suspendisse elementum rutrum nisl lobortis feugiat. Quisque viverra felis tellus, eget consequat velit tempus in. Quisque finibus mauris ac pulvinar lobortis. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed vel mi pretium, elementum nisi nec, consequat eros. Donec nunc lorem, viverra ut vulputate non, ultrices eu eros. Integer vestibulum ex ut risus bibendum iaculis. Aliquam varius, nisi ut commodo commodo, purus tortor semper urna, non pharetra est ligula a est. Quisque sed consequat nunc, ac tincidunt dui. Sed auctor facilisis ultrices. Suspendisse eu nulla rhoncus, rhoncus neque consectetur, faucibus justo. In tincidunt molestie convallis. Suspendisse elementum rutrum nisl lobortis feugiat. Quisque viverra felis tellus, eget consequat velit tempus in. Quisque finibus mauris ac pulvinar lobortis.</p>\r\n', '2019-10-31 07:49:29', '2019-12-02 10:12:49', 2, 18, 'Visas'),
(4, ' Bēdu manu lielu bēdu 3', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed vel mi pretium, elementum nisi nec, consequat eros. Donec nunc lorem, viverra ut vulputate non, ultrices eu eros. Integer vestibulum ex ut risus bibendum iaculis. Aliquam varius, nisi ut commodo commodo, purus tortor semper urna, non pharetra est ligula a est. Quisque sed consequat nunc, ac tincidunt dui. Sed auctor facilisis ultrices. Suspendisse eu nulla rhoncus, rhoncus neque consectetur, faucibus justo. In tincidunt molestie convallis. Suspendisse elementum rutrum nisl lobortis feugiat. Quisque viverra felis tellus, eget consequat velit tempus in. Quisque finibus mauris ac pulvinar lobortis.</p>\r\n', '2019-10-31 07:49:29', '2019-12-02 10:12:52', 1, 20, 'Visas'),
(5, 'Bēdu manu lielu bēdu 4', '<p>apraksts</p>\r\n', '2019-11-01 07:16:22', '2019-12-02 10:12:56', 1, 24, 'Visas'),
(6, 'Mugurdancis1', '', '2019-11-29 04:35:16', '2019-12-02 10:54:10', 1, 22, 'Visas'),
(7, ' Mugurdancis2', '', '2019-11-29 04:36:49', '2019-12-02 10:54:18', 1, 27, 'Visas'),
(8, ' Jau rudens tuvojās', '', '2019-11-29 04:37:14', '2019-12-02 10:54:25', 1, 13, 'Visas'),
(9, 'Lilioma dziesma', '', '2019-12-02 07:23:17', '2019-12-02 10:54:32', 1, 26, 'Visas'),
(10, ' Lilioma dziema starpspele', '', '2019-12-02 07:32:50', '2019-12-02 10:54:39', 1, 14, 'Visas'),
(11, ' Hallelujah', '', '2019-12-02 09:45:50', '2019-12-02 10:54:46', 1, 23, 'Visas'),
(12, ' Garais grīslis', '', '2019-12-02 09:48:58', '2019-12-02 10:54:54', 1, 28, 'Visas'),
(13, 'Nekarieti šūpulīti', '', '2019-12-02 09:51:39', '2019-12-02 10:55:03', 1, 19, 'Visas'),
(14, ' Teku, teku', '', '2019-12-02 09:54:40', '2019-12-02 10:55:11', 1, 8, 'Visas'),
(15, ' Ķēvīt mana svilpastīte', '', '2019-12-02 09:55:35', '2019-12-02 10:55:19', 1, 17, 'Visas'),
(16, ' ķevīte starpspēle', '', '2019-12-02 09:56:05', '2019-12-02 10:55:35', 1, 11, 'Visas'),
(17, ' Re/fa/fa', '', '2019-12-02 09:56:38', '2019-12-02 10:55:42', 1, 7, 'Visas'),
(18, 'Tumsa galu vakarā', '', '2019-12-02 09:58:07', '2019-12-02 10:55:50', 1, 6, 'Visas'),
(19, ' akords+ ikskis', '', '2019-12-02 09:58:54', '2019-12-02 10:55:58', 1, 8, 'Visas'),
(20, ' Mans mīļākais vingrinājums', '', '2019-12-02 09:59:51', '2019-12-02 10:56:14', 1, 11, 'Visas'),
(21, ' Mans milakais vingrinajums', '', '2019-12-02 10:00:29', '2019-12-02 10:56:22', 1, 12, 'Visas'),
(22, ' Do un ej laimiņa', '', '2019-12-02 10:02:06', '2019-12-02 10:56:32', 1, 12, 'Visas'),
(23, ' La/fa/do', '', '2019-12-02 10:02:45', '2019-12-02 10:56:40', 1, 9, 'Visas'),
(24, 'Sol', '', '2019-12-02 10:03:49', '2019-12-02 10:56:49', 1, 8, 'Visas');

-- --------------------------------------------------------

--
-- Table structure for table `lecturesdifficulties`
--

CREATE TABLE IF NOT EXISTS `lecturesdifficulties` (
  `id` int(11) NOT NULL,
  `diff_id` int(11) NOT NULL COMMENT 'Parametrs',
  `lecture_id` int(11) NOT NULL COMMENT 'Lekcija',
  `value` int(50) NOT NULL COMMENT 'Vērtība'
) ENGINE=InnoDB AUTO_INCREMENT=632 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `lecturesdifficulties`
--

INSERT INTO `lecturesdifficulties` (`id`, `diff_id`, `lecture_id`, `value`) VALUES
(488, 1, 1, 3),
(489, 2, 1, 4),
(490, 3, 1, 8),
(491, 4, 1, 6),
(492, 5, 1, 6),
(493, 1, 3, 3),
(494, 2, 3, 2),
(495, 3, 3, 1),
(496, 4, 3, 6),
(497, 5, 3, 6),
(498, 1, 4, 3),
(499, 2, 4, 4),
(500, 3, 4, 1),
(501, 4, 4, 6),
(502, 5, 4, 6),
(503, 1, 5, 4),
(504, 3, 5, 8),
(505, 4, 5, 6),
(506, 5, 5, 6),
(532, 1, 6, 4),
(533, 2, 6, 1),
(534, 3, 6, 4),
(535, 4, 6, 6),
(536, 5, 6, 7),
(537, 1, 7, 4),
(538, 2, 7, 6),
(539, 3, 7, 4),
(540, 4, 7, 6),
(541, 5, 7, 7),
(542, 1, 8, 3),
(543, 2, 8, 2),
(544, 3, 8, 1),
(545, 4, 8, 3),
(546, 5, 8, 4),
(547, 1, 9, 8),
(548, 2, 9, 2),
(549, 3, 9, 1),
(550, 4, 9, 7),
(551, 5, 9, 8),
(552, 1, 10, 7),
(553, 2, 10, 2),
(554, 3, 10, 1),
(555, 4, 10, 3),
(556, 5, 10, 1),
(557, 1, 11, 4),
(558, 2, 11, 2),
(559, 3, 11, 1),
(560, 4, 11, 8),
(561, 5, 11, 8),
(562, 1, 12, 5),
(563, 2, 12, 6),
(564, 3, 12, 4),
(565, 4, 12, 6),
(566, 5, 12, 7),
(567, 1, 13, 3),
(568, 2, 13, 2),
(569, 3, 13, 1),
(570, 4, 13, 4),
(571, 5, 13, 9),
(572, 1, 14, 1),
(573, 2, 14, 2),
(574, 3, 14, 1),
(575, 4, 14, 2),
(576, 5, 14, 2),
(577, 1, 15, 4),
(578, 2, 15, 2),
(579, 3, 15, 1),
(580, 4, 15, 4),
(581, 5, 15, 6),
(587, 1, 16, 3),
(588, 2, 16, 2),
(589, 3, 16, 1),
(590, 4, 16, 3),
(591, 5, 16, 2),
(592, 1, 17, 1),
(593, 2, 17, 1),
(594, 3, 17, 1),
(595, 4, 17, 2),
(596, 5, 17, 2),
(597, 1, 18, 1),
(598, 2, 18, 2),
(599, 3, 18, 1),
(600, 4, 18, 1),
(601, 5, 18, 1),
(602, 1, 19, 1),
(603, 2, 19, 3),
(604, 3, 19, 2),
(605, 4, 19, 1),
(606, 5, 19, 1),
(607, 1, 20, 3),
(608, 2, 20, 3),
(609, 3, 20, 3),
(610, 4, 20, 1),
(611, 5, 20, 1),
(612, 1, 21, 3),
(613, 2, 21, 3),
(614, 3, 21, 4),
(615, 4, 21, 1),
(616, 5, 21, 1),
(617, 1, 22, 2),
(618, 2, 22, 2),
(619, 3, 22, 1),
(620, 4, 22, 3),
(621, 5, 22, 4),
(622, 1, 23, 1),
(623, 2, 23, 1),
(624, 3, 23, 1),
(625, 4, 23, 3),
(626, 5, 23, 3),
(627, 1, 24, 1),
(628, 2, 24, 1),
(629, 3, 24, 1),
(630, 4, 24, 1),
(631, 5, 24, 4);

-- --------------------------------------------------------

--
-- Table structure for table `lecturesevaluations`
--

CREATE TABLE IF NOT EXISTS `lecturesevaluations` (
  `id` int(11) NOT NULL,
  `lecture_id` int(11) NOT NULL COMMENT 'Lekcija',
  `evaluation_id` int(11) NOT NULL COMMENT 'Novērtējums'
) ENGINE=InnoDB AUTO_INCREMENT=306 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `lecturesevaluations`
--

INSERT INTO `lecturesevaluations` (`id`, `lecture_id`, `evaluation_id`) VALUES
(211, 1, 1),
(212, 1, 2),
(213, 1, 3),
(214, 1, 4),
(215, 3, 1),
(216, 3, 2),
(217, 3, 3),
(218, 3, 4),
(219, 4, 1),
(220, 4, 2),
(221, 4, 3),
(222, 4, 4),
(223, 5, 1),
(224, 5, 2),
(225, 5, 3),
(226, 5, 4),
(227, 6, 1),
(228, 6, 2),
(229, 6, 3),
(230, 6, 4),
(231, 7, 1),
(232, 7, 2),
(233, 7, 3),
(234, 7, 4),
(235, 8, 1),
(236, 8, 2),
(237, 8, 3),
(238, 8, 4),
(239, 9, 1),
(240, 9, 2),
(241, 9, 3),
(242, 9, 4),
(243, 10, 1),
(244, 10, 2),
(245, 10, 3),
(246, 10, 4),
(247, 11, 1),
(248, 11, 2),
(249, 11, 3),
(250, 11, 4),
(251, 12, 1),
(252, 12, 2),
(253, 12, 3),
(254, 12, 4),
(255, 13, 1),
(256, 13, 2),
(257, 13, 3),
(258, 13, 4),
(259, 14, 1),
(260, 14, 2),
(261, 14, 3),
(262, 14, 4),
(263, 15, 1),
(264, 15, 2),
(265, 15, 3),
(266, 15, 4),
(270, 16, 1),
(271, 16, 2),
(272, 16, 3),
(273, 16, 4),
(274, 17, 1),
(275, 17, 2),
(276, 17, 3),
(277, 17, 4),
(278, 18, 1),
(279, 18, 2),
(280, 18, 3),
(281, 18, 4),
(282, 19, 1),
(283, 19, 2),
(284, 19, 3),
(285, 19, 4),
(286, 20, 1),
(287, 20, 2),
(288, 20, 3),
(289, 20, 4),
(290, 21, 1),
(291, 21, 2),
(292, 21, 3),
(293, 21, 4),
(294, 22, 1),
(295, 22, 2),
(296, 22, 3),
(297, 22, 4),
(298, 23, 1),
(299, 23, 2),
(300, 23, 3),
(301, 23, 4),
(302, 24, 1),
(303, 24, 2),
(304, 24, 3),
(305, 24, 4);

-- --------------------------------------------------------

--
-- Table structure for table `lecturesfiles`
--

CREATE TABLE IF NOT EXISTS `lecturesfiles` (
  `id` int(11) NOT NULL,
  `title` mediumtext COLLATE utf8_unicode_ci NOT NULL COMMENT 'Virsraksts',
  `file` mediumtext COLLATE utf8_unicode_ci NOT NULL COMMENT 'Fails',
  `thumb` mediumtext COLLATE utf8_unicode_ci NOT NULL COMMENT 'Bilde',
  `lecture_id` int(11) NOT NULL COMMENT 'Lekcija'
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `lecturesfiles`
--

INSERT INTO `lecturesfiles` (`id`, `title`, `file`, `thumb`, `lecture_id`) VALUES
(1, 'Video lekcija 4', '/sys/files/global/video/Bedu%20manu%20int%2Bkreisa2.mp4', '/sys/files/global/bildes/kokle.jpg', 5),
(2, 'Video lekcija 2', '/sys/files/global/video/BEdu%20manu%20akordi.mp4', '', 3),
(3, 'Video lekcija 3', '/sys/files/global/video/bedu%20manu%20int.mp4', '', 4),
(4, 'Video lekcija', '/sys/files/global/video/bedu%20int%2Bkreis%C4%81.mp4', '/sys/files/global/bildes/kokle2.jpg', 1),
(5, 'Mugurdancis', '/sys/files/global/video/mugurdancis%201.mp4', '', 6),
(6, 'Mugurdancis', '/sys/files/global/video/mugurdancis%202.mp4', '', 7),
(7, 'La/fa/do', '/sys/files/global/video/la-do-fa-do.mp4', '', 23),
(8, 'Lilioma dziesma', '/sys/files/global/video/Lilioma%20dziesma.mp4', '', 9),
(9, 'Sol', '/sys/files/global/video/1%20Faila%204.%20sol%20un%20_pie%20dievi%C5%86a_.mp4', '', 24),
(10, 'Do un ej', '/sys/files/global/video/1Do%20Ej%20laimi%C5%86a%20tu%20pa%20priek%C5%A1u.mp4', '', 22),
(11, 'Lekcija', '/sys/files/global/video/%C4%B7%C4%93v%C4%ABte%20akordi.mp4', '', 15),
(12, 'Vingrinājums', '/sys/files/global/video/1%20mans%20milakais%20vingrin%C4%81jums%202.mp4', '', 21),
(13, 'Lekcija', '/sys/files/global/video/1%20mans%20milakais%20vingrin%C4%81jums%202.mp4', '', 21),
(14, 'Lekcija', '/sys/files/global/video/Limiloma%20dziesma%20starpspele.mp4', '', 10),
(15, 'Lekcija', '/sys/files/global/video/Jau%20rudens%20tuvoj%C4%81s.mp4', '', 8),
(16, 'Lekcija', '/sys/files/global/video/%C4%B6%C4%93v%C4%ABte%20staarpsp%C4%93le.mp4', '', 16),
(17, 'Lekcija', '/sys/files/global/video/Garais%20gr%C4%ABslis.mp4', '', 12),
(18, 'Lekcija', '/sys/files/global/video/1.Nodarbiba%201.%20re_Fa_La.mp4', '', 17),
(19, 'Lekcija', '/sys/files/global/video/2.Tumsa%20gaju%20vakara.mp4', '', 18),
(20, 'Lekcija', '/sys/files/global/video/Teku%20teku.mp4', '', 14),
(21, 'Lekcija', '/sys/files/global/video/5.%20leka%C5%A1ana%20akords-ikskis%2C%20plus%20pirksts%20pec%20otra.mp4', '', 19),
(22, 'Lekcija', '/sys/files/global/video/1%20mans%20m%C4%AB%C4%BC%C4%81kais%20vingrin%C4%81jums%201.mp4', '', 20),
(23, 'Lekcija', '/sys/files/global/video/nekarieti%20supuliti.mp4', '', 13),
(24, 'Lekcija', '/sys/files/global/video/Hallelujah%20akordi.mp4', '', 11);

-- --------------------------------------------------------

--
-- Table structure for table `lectureshanddifficulties`
--

CREATE TABLE IF NOT EXISTS `lectureshanddifficulties` (
  `id` int(11) NOT NULL,
  `lecture_id` int(11) NOT NULL COMMENT 'Lekcija',
  `category_id` int(11) NOT NULL COMMENT 'Kategorija'
) ENGINE=InnoDB AUTO_INCREMENT=96 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `lectureshanddifficulties`
--

INSERT INTO `lectureshanddifficulties` (`id`, `lecture_id`, `category_id`) VALUES
(92, 1, 1),
(93, 1, 3),
(94, 5, 2),
(95, 5, 4);

-- --------------------------------------------------------

--
-- Table structure for table `relatedlectures`
--

CREATE TABLE IF NOT EXISTS `relatedlectures` (
  `id` int(11) NOT NULL,
  `lecture_id` int(11) NOT NULL COMMENT 'Lekcija',
  `related_id` int(11) NOT NULL COMMENT 'Saistītā lekcija'
) ENGINE=InnoDB AUTO_INCREMENT=79 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `relatedlectures`
--

INSERT INTO `relatedlectures` (`id`, `lecture_id`, `related_id`) VALUES
(69, 1, 3),
(70, 3, 4),
(71, 4, 5),
(72, 5, 6),
(76, 6, 7),
(77, 7, 8),
(78, 9, 10);

-- --------------------------------------------------------

--
-- Table structure for table `sentlectures`
--

CREATE TABLE IF NOT EXISTS `sentlectures` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL COMMENT 'Lietotājs',
  `lecture_id` int(11) NOT NULL COMMENT 'Pēdējā lekcija',
  `sent` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Nosūtīts e-pasts',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Izveidots'
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `sentlectures`
--

INSERT INTO `sentlectures` (`id`, `user_id`, `lecture_id`, `sent`, `created`) VALUES
(1, 2, 13, 1, '2019-12-03 11:49:26'),
(2, 2, 13, 1, '2019-12-03 11:50:07'),
(3, 2, 13, 1, '2019-12-03 11:50:34'),
(4, 2, 13, 1, '2019-12-03 11:50:38'),
(5, 2, 13, 1, '2019-12-03 12:02:16');

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
) ENGINE=InnoDB AUTO_INCREMENT=330 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `studentgoals`
--

INSERT INTO `studentgoals` (`id`, `user_id`, `type`, `diff_id`, `value`) VALUES
(213, 2, 'Vēlamais', 2, 5),
(214, 2, 'Vēlamais', 4, 8),
(325, 2, 'Šobrīd', 1, 3),
(326, 2, 'Šobrīd', 2, 2),
(327, 2, 'Šobrīd', 3, 1),
(328, 2, 'Šobrīd', 4, 4),
(329, 2, 'Šobrīd', 5, 9);

-- --------------------------------------------------------

--
-- Table structure for table `studenthandgoals`
--

CREATE TABLE IF NOT EXISTS `studenthandgoals` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL COMMENT 'Students',
  `category_id` int(11) NOT NULL COMMENT 'Kategorija'
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `studenthandgoals`
--

INSERT INTO `studenthandgoals` (`id`, `user_id`, `category_id`) VALUES
(45, 2, 1),
(46, 2, 4);

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
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `userlectureevaluations`
--

INSERT INTO `userlectureevaluations` (`id`, `lecture_id`, `evaluation_id`, `user_id`, `evaluation`, `created`) VALUES
(26, 18, 1, 2, '2', '2019-12-02 13:25:02'),
(27, 18, 1, 2, '2', '2019-12-02 13:25:28'),
(28, 18, 1, 2, '4', '2019-12-03 12:38:27'),
(29, 6, 1, 2, '6', '2019-12-03 13:41:10'),
(30, 6, 1, 2, '5', '2019-12-03 13:42:30');

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
  `sent` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Nosūtīts e-pasts',
  `evaluated` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Novērtēta',
  `sent_times` int(11) NOT NULL DEFAULT '1' COMMENT 'Cik reizes nosūtīts e-pasts pirms novērtēšanas'
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `userlectures`
--

INSERT INTO `userlectures` (`id`, `lecture_id`, `user_id`, `assigned`, `created`, `opened`, `opentime`, `sent`, `evaluated`, `sent_times`) VALUES
(7, 18, 2, 1, '2019-12-03 13:43:37', 1, '2019-12-02 10:53:33', 1, 1, 6),
(8, 6, 2, 1, '2019-12-03 14:05:06', 1, '2019-12-03 11:41:03', 1, 1, 17),
(14, 13, 2, 1, '2019-12-03 14:02:16', 0, NULL, 1, 0, 1);

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
(1, 'test', 'tester', '112', 'test@test.lv', '$2y$13$Q6qwbz72XUw4acnoTQsl7eHA5SugtEmxynv08ScuyVmeV0SGuf45C', 'Admin', '', '', '', '', 10, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2019-12-02 08:17:07', 0, '2019-11-14 23:59:59', ''),
(2, 'Students', 'Studentiņš', '112', 'student@test.lv', '$2y$13$Q6qwbz72XUw4acnoTQsl7eHA5SugtEmxynv08ScuyVmeV0SGuf45C', 'Student', '', '', '', 'tjI7VodU51a8pA-Qng971MFVzehC9dBp', 10, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2019-12-03 10:39:23', 6, '2019-12-02 23:59:59', 'tetsts'),
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
  ADD PRIMARY KEY (`id`),
  ADD KEY `is_scale` (`is_scale`);

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
-- Indexes for table `relatedlectures`
--
ALTER TABLE `relatedlectures`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lecture` (`lecture_id`),
  ADD KEY `related` (`related_id`);

--
-- Indexes for table `sentlectures`
--
ALTER TABLE `sentlectures`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `lecture_id` (`lecture_id`);

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
  ADD KEY `admin` (`assigned`),
  ADD KEY `evaluated` (`evaluated`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `handdifficulties`
--
ALTER TABLE `handdifficulties`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `lectures`
--
ALTER TABLE `lectures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=25;
--
-- AUTO_INCREMENT for table `lecturesdifficulties`
--
ALTER TABLE `lecturesdifficulties`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=632;
--
-- AUTO_INCREMENT for table `lecturesevaluations`
--
ALTER TABLE `lecturesevaluations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=306;
--
-- AUTO_INCREMENT for table `lecturesfiles`
--
ALTER TABLE `lecturesfiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=25;
--
-- AUTO_INCREMENT for table `lectureshanddifficulties`
--
ALTER TABLE `lectureshanddifficulties`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=96;
--
-- AUTO_INCREMENT for table `relatedlectures`
--
ALTER TABLE `relatedlectures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=79;
--
-- AUTO_INCREMENT for table `sentlectures`
--
ALTER TABLE `sentlectures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `studentgoals`
--
ALTER TABLE `studentgoals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=330;
--
-- AUTO_INCREMENT for table `studenthandgoals`
--
ALTER TABLE `studenthandgoals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=47;
--
-- AUTO_INCREMENT for table `userlectureevaluations`
--
ALTER TABLE `userlectureevaluations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=31;
--
-- AUTO_INCREMENT for table `userlectures`
--
ALTER TABLE `userlectures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=15;
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
-- Constraints for table `relatedlectures`
--
ALTER TABLE `relatedlectures`
  ADD CONSTRAINT `relatedlectures_ibfk_1` FOREIGN KEY (`lecture_id`) REFERENCES `lectures` (`id`);

--
-- Constraints for table `sentlectures`
--
ALTER TABLE `sentlectures`
  ADD CONSTRAINT `sentlectures_ibfk_1` FOREIGN KEY (`lecture_id`) REFERENCES `lectures` (`id`),
  ADD CONSTRAINT `sentlectures_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

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
