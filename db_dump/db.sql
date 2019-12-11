-- phpMyAdmin SQL Dump
-- version 4.4.15.10
-- https://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 11, 2019 at 03:21 PM
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
  `is_scale` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'Algoritma skala',
  `is_video_param` int(11) NOT NULL COMMENT 'Lekciju biežuma parametrs'
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `evaluations`
--

INSERT INTO `evaluations` (`id`, `title`, `type`, `stars`, `star_text`, `is_scale`, `is_video_param`) VALUES
(1, 'Uzdevuma sarežģītība', 'zvaigznes', 10, 'a:10:{i:0;s:31:"Viss tik viegls, ka garlaicīgi";i:1;s:43:"Ļoti ļoti viegli, noteikti vajag grūtāk";i:2;s:43:"Izspēlēju vienu reizi un jau viss skaidrs";i:3;s:19:"Diezgan vienkārši";i:4;s:59:"Nācās pastrādāt, bet tiku galā bez milzīgas piepūles";i:5;s:10:"Tiku galā";i:6;s:14:"Diezgan grūti";i:7;s:35:"Itkā saprotu, bet pirksti neklausa";i:8;s:38:"Kaut ko mēģinu, bet pārāk nesanāk";i:9;s:22:"Vispār neko nesaprotu";}', 1, 0),
(2, 'Uzdevumu daudzums', 'zvaigznes', 3, 'a:3:{i:0;s:28:"Par daudz, vajadzētu mazāk";i:1;s:24:"Tieši tik daudz ir labi";i:2;s:27:"Par maz, vajadzētu vairāk";}', 0, 0),
(3, 'Video apjoms', 'zvaigznes', 3, 'a:3:{i:0;s:33:"Vajadzētu mazāk, bija par daudz";i:1;s:22:"Ideāli, tā turpināt";i:2;s:45:"Bija par maz, dodiet uz nākamo reizi vairāk";}', 0, 1),
(4, 'Komentāri', 'teksts', 5, '', 0, 0);

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
  `season` enum('Visas','Rudens','Ziema','Pavasaris','Vasara') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Visas' COMMENT 'Gadskārta',
  `file` mediumtext COLLATE utf8_unicode_ci COMMENT 'Video fails',
  `thumb` mediumtext COLLATE utf8_unicode_ci COMMENT 'Video bilde'
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `lectures`
--

INSERT INTO `lectures` (`id`, `title`, `description`, `created`, `updated`, `author`, `complexity`, `season`, `file`, `thumb`) VALUES
(1, ' Bēdu manu + kreisā', '<h2><strong>Lorem ipsum dolor sit amet,</strong></h2>\r\n\r\n<p>consectetur adipiscing elit. Sed vel mi pretium, elementum nisi nec, consequat eros. Donec nunc lorem, viverra ut vulputate non, ultrices eu eros. Integer vestibulum ex ut risus bibendum iaculis. Aliquam varius, nisi ut commodo commodo, purus tortor semper urna, non pharetra est ligula a est. Quisque sed consequat nunc, ac tincidunt dui. Sed auctor facilisis ultrices. Suspendisse eu nulla rhoncus, rhoncus neque consectetur, <span style="background-color:#FF0000">faucibus justo</span>. In tincidunt molestie convallis. Suspendisse elementum rutrum nisl lobortis feugiat. Quisque viverra felis tellus, eget consequat velit tempus in. Quisque finibus mauris ac pulvinar lobortis.</p>\r\n', '2019-10-31 07:49:29', '2019-12-10 07:32:32', 1, 27, 'Visas', '/sys/files/global/video/Aij%C4%81%20Anc%C4%ABt%206.mp4', '/sys/files/global/bildes/kokle2.jpg'),
(3, ' Bēdu manu lielu bēdu 2', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed vel mi pretium, elementum nisi nec, consequat eros. Donec nunc lorem, viverra ut vulputate non, ultrices eu eros. Integer vestibulum ex ut risus bibendum iaculis. Aliquam varius, nisi ut commodo commodo, purus tortor semper urna, non pharetra est ligula a est. Quisque sed consequat nunc, ac tincidunt dui. Sed auctor facilisis ultrices. Suspendisse eu nulla rhoncus, rhoncus neque consectetur, faucibus justo. In tincidunt molestie convallis. Suspendisse elementum rutrum nisl lobortis feugiat. Quisque viverra felis tellus, eget consequat velit tempus in. Quisque finibus mauris ac pulvinar lobortis. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed vel mi pretium, elementum nisi nec, consequat eros. Donec nunc lorem, viverra ut vulputate non, ultrices eu eros. Integer vestibulum ex ut risus bibendum iaculis. Aliquam varius, nisi ut commodo commodo, purus tortor semper urna, non pharetra est ligula a est. Quisque sed consequat nunc, ac tincidunt dui. Sed auctor facilisis ultrices. Suspendisse eu nulla rhoncus, rhoncus neque consectetur, faucibus justo. In tincidunt molestie convallis. Suspendisse elementum rutrum nisl lobortis feugiat. Quisque viverra felis tellus, eget consequat velit tempus in. Quisque finibus mauris ac pulvinar lobortis.</p>\r\n', '2019-10-31 07:49:29', '2019-12-02 10:12:49', 2, 18, 'Visas', NULL, NULL),
(4, ' Bēdu manu lielu bēdu 3', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed vel mi pretium, elementum nisi nec, consequat eros. Donec nunc lorem, viverra ut vulputate non, ultrices eu eros. Integer vestibulum ex ut risus bibendum iaculis. Aliquam varius, nisi ut commodo commodo, purus tortor semper urna, non pharetra est ligula a est. Quisque sed consequat nunc, ac tincidunt dui. Sed auctor facilisis ultrices. Suspendisse eu nulla rhoncus, rhoncus neque consectetur, faucibus justo. In tincidunt molestie convallis. Suspendisse elementum rutrum nisl lobortis feugiat. Quisque viverra felis tellus, eget consequat velit tempus in. Quisque finibus mauris ac pulvinar lobortis.</p>\r\n', '2019-10-31 07:49:29', '2019-12-04 05:06:53', 1, 20, 'Visas', NULL, NULL),
(5, 'Bēdu manu lielu bēdu 4', '<p>apraksts</p>\r\n', '2019-11-01 07:16:22', '2019-12-04 04:40:42', 1, 24, 'Visas', NULL, NULL),
(6, 'Mugurdancis1', '', '2019-11-29 04:35:16', '2019-12-02 10:54:10', 1, 22, 'Visas', NULL, NULL),
(7, ' Mugurdancis2', '', '2019-11-29 04:36:49', '2019-12-02 10:54:18', 1, 27, 'Visas', NULL, NULL),
(8, ' Jau rudens tuvojās', '', '2019-11-29 04:37:14', '2019-12-02 10:54:25', 1, 13, 'Visas', NULL, NULL),
(9, 'Lilioma dziesma', '', '2019-12-02 07:23:17', '2019-12-02 10:54:32', 1, 26, 'Visas', NULL, NULL),
(10, ' Lilioma dziema starpspele', '', '2019-12-02 07:32:50', '2019-12-11 05:26:07', 1, 14, 'Visas', '', '/sys/files/global/bildes/kokle2.jpg'),
(11, ' Hallelujah', '', '2019-12-02 09:45:50', '2019-12-02 10:54:46', 1, 23, 'Visas', NULL, NULL),
(12, ' Garais grīslis', '', '2019-12-02 09:48:58', '2019-12-02 10:54:54', 1, 28, 'Visas', NULL, NULL),
(13, 'Nekarieti šūpulīti', '', '2019-12-02 09:51:39', '2019-12-04 06:19:43', 1, 19, 'Visas', NULL, NULL),
(14, ' Teku, teku', '', '2019-12-02 09:54:40', '2019-12-04 05:17:58', 1, 8, 'Visas', NULL, NULL),
(15, ' Ķēvīt mana svilpastīte', '', '2019-12-02 09:55:35', '2019-12-04 05:16:06', 1, 17, 'Visas', NULL, NULL),
(16, ' ķevīte starpspēle', '', '2019-12-02 09:56:05', '2019-12-04 05:18:49', 1, 11, 'Visas', NULL, NULL),
(17, ' Re/fa/fa', '', '2019-12-02 09:56:38', '2019-12-04 06:33:57', 1, 7, 'Visas', NULL, NULL),
(18, 'Tumsa galu vakarā', '', '2019-12-02 09:58:07', '2019-12-04 05:17:19', 1, 6, 'Visas', NULL, NULL),
(19, ' akords+ ikskis', '', '2019-12-02 09:58:54', '2019-12-02 10:55:58', 1, 8, 'Visas', NULL, NULL),
(20, ' Mans mīļākais vingrinājums', '', '2019-12-02 09:59:51', '2019-12-02 10:56:14', 1, 11, 'Visas', NULL, NULL),
(21, ' Mans milakais vingrinajums', '', '2019-12-02 10:00:29', '2019-12-02 10:56:22', 1, 12, 'Visas', NULL, NULL),
(22, ' Do un ej laimiņa', '', '2019-12-02 10:02:06', '2019-12-02 10:56:32', 1, 12, 'Visas', NULL, NULL),
(23, ' La/fa/do', '', '2019-12-02 10:02:45', '2019-12-04 05:18:38', 1, 9, 'Visas', NULL, NULL),
(24, 'Sol', '', '2019-12-02 10:03:49', '2019-12-10 11:05:23', 1, 8, 'Visas', '', ''),
(25, 'Ej laimiņa', '<p><span style="color:rgb(0, 0, 0); font-family:arial">Ej laimiņa</span></p>\r\n', '2019-12-06 18:08:42', '2019-12-10 11:14:15', 1, 9, 'Visas', '', ''),
(26, 'tests2133w', '<p>gtd</p>\r\n', '2019-12-09 06:59:21', '2019-12-10 11:14:23', 1, 0, 'Visas', '', ''),
(27, 'Divi sirmi 1', '<p>Aprakstins</p>\r\n', '2019-12-10 07:37:09', '2019-12-10 13:14:43', 1, 17, 'Visas', '/sys/files/global/video/Divi%20sirmi%20kumeli%201.mp4', ''),
(28, 'Divi divi', '<p>osdfbjsbnbjk</p>\r\n', '2019-12-10 07:40:52', '2019-12-10 13:14:50', 1, 8, 'Visas', '/sys/files/global/video/Divi%2C%20divi%202.mp4', '');

-- --------------------------------------------------------

--
-- Table structure for table `lecturesdifficulties`
--

CREATE TABLE IF NOT EXISTS `lecturesdifficulties` (
  `id` int(11) NOT NULL,
  `diff_id` int(11) NOT NULL COMMENT 'Parametrs',
  `lecture_id` int(11) NOT NULL COMMENT 'Lekcija',
  `value` int(50) NOT NULL COMMENT 'Vērtība'
) ENGINE=InnoDB AUTO_INCREMENT=781 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `lecturesdifficulties`
--

INSERT INTO `lecturesdifficulties` (`id`, `diff_id`, `lecture_id`, `value`) VALUES
(493, 1, 3, 3),
(494, 2, 3, 2),
(495, 3, 3, 1),
(496, 4, 3, 6),
(497, 5, 3, 6),
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
(632, 1, 5, 4),
(633, 3, 5, 8),
(634, 4, 5, 6),
(635, 5, 5, 6),
(641, 1, 4, 3),
(642, 2, 4, 4),
(643, 3, 4, 1),
(644, 4, 4, 6),
(645, 5, 4, 6),
(656, 1, 15, 4),
(657, 2, 15, 2),
(658, 3, 15, 1),
(659, 4, 15, 4),
(660, 5, 15, 6),
(661, 1, 18, 1),
(662, 2, 18, 2),
(663, 3, 18, 1),
(664, 4, 18, 1),
(665, 5, 18, 1),
(671, 1, 14, 1),
(672, 2, 14, 2),
(673, 3, 14, 1),
(674, 4, 14, 2),
(675, 5, 14, 2),
(676, 1, 23, 1),
(677, 2, 23, 1),
(678, 3, 23, 1),
(679, 4, 23, 3),
(680, 5, 23, 3),
(681, 1, 16, 3),
(682, 2, 16, 2),
(683, 3, 16, 1),
(684, 4, 16, 3),
(685, 5, 16, 2),
(691, 1, 13, 3),
(692, 2, 13, 2),
(693, 3, 13, 1),
(694, 4, 13, 4),
(695, 5, 13, 9),
(701, 1, 17, 1),
(702, 2, 17, 1),
(703, 3, 17, 1),
(704, 4, 17, 2),
(705, 5, 17, 2),
(721, 1, 1, 3),
(722, 2, 1, 4),
(723, 3, 1, 8),
(724, 4, 1, 6),
(725, 5, 1, 6),
(746, 1, 24, 1),
(747, 2, 24, 1),
(748, 3, 24, 1),
(749, 4, 24, 1),
(750, 5, 24, 4),
(761, 1, 25, 2),
(762, 2, 25, 2),
(763, 3, 25, 1),
(764, 4, 25, 2),
(765, 5, 25, 2),
(766, 1, 27, 5),
(767, 2, 27, 2),
(768, 3, 27, 1),
(769, 4, 27, 5),
(770, 5, 27, 4),
(771, 1, 28, 1),
(772, 2, 28, 2),
(773, 3, 28, 1),
(774, 4, 28, 1),
(775, 5, 28, 3),
(776, 1, 10, 7),
(777, 2, 10, 2),
(778, 3, 10, 1),
(779, 4, 10, 3),
(780, 5, 10, 1);

-- --------------------------------------------------------

--
-- Table structure for table `lecturesevaluations`
--

CREATE TABLE IF NOT EXISTS `lecturesevaluations` (
  `id` int(11) NOT NULL,
  `lecture_id` int(11) NOT NULL COMMENT 'Lekcija',
  `evaluation_id` int(11) NOT NULL COMMENT 'Novērtējums'
) ENGINE=InnoDB AUTO_INCREMENT=397 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `lecturesevaluations`
--

INSERT INTO `lecturesevaluations` (`id`, `lecture_id`, `evaluation_id`) VALUES
(215, 3, 1),
(216, 3, 2),
(217, 3, 3),
(218, 3, 4),
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
(247, 11, 1),
(248, 11, 2),
(249, 11, 3),
(250, 11, 4),
(251, 12, 1),
(252, 12, 2),
(253, 12, 3),
(254, 12, 4),
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
(306, 5, 1),
(307, 5, 2),
(308, 5, 3),
(309, 5, 4),
(314, 4, 1),
(315, 4, 2),
(316, 4, 3),
(317, 4, 4),
(326, 15, 1),
(327, 15, 2),
(328, 15, 3),
(329, 15, 4),
(330, 18, 1),
(331, 18, 2),
(332, 18, 3),
(333, 18, 4),
(338, 14, 1),
(339, 14, 2),
(340, 14, 3),
(341, 14, 4),
(342, 23, 1),
(343, 23, 2),
(344, 23, 3),
(345, 23, 4),
(346, 16, 1),
(347, 16, 2),
(348, 16, 3),
(349, 16, 4),
(354, 13, 1),
(355, 13, 2),
(356, 13, 3),
(357, 13, 4),
(362, 17, 1),
(363, 17, 2),
(364, 17, 3),
(365, 17, 4),
(370, 1, 1),
(371, 1, 2),
(372, 1, 3),
(373, 1, 4),
(383, 24, 1),
(384, 24, 2),
(385, 24, 3),
(386, 24, 4),
(387, 27, 1),
(388, 27, 3),
(389, 27, 4),
(390, 28, 1),
(391, 28, 2),
(392, 28, 4),
(393, 10, 1),
(394, 10, 2),
(395, 10, 3),
(396, 10, 4);

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
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `lecturesfiles`
--

INSERT INTO `lecturesfiles` (`id`, `title`, `file`, `thumb`, `lecture_id`) VALUES
(25, '', '/sys/files/global/video/B%C4%93du%20manu%20%2B%20kreis%C4%81.mp4', '', 1),
(26, '', '/sys/files/global/video/B%C4%93du%20manu%20%2B%20kreis%C4%81.mp4', '', 1);

-- --------------------------------------------------------

--
-- Table structure for table `lectureshanddifficulties`
--

CREATE TABLE IF NOT EXISTS `lectureshanddifficulties` (
  `id` int(11) NOT NULL,
  `lecture_id` int(11) NOT NULL COMMENT 'Lekcija',
  `category_id` int(11) NOT NULL COMMENT 'Kategorija'
) ENGINE=InnoDB AUTO_INCREMENT=98 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `lectureshanddifficulties`
--

INSERT INTO `lectureshanddifficulties` (`id`, `lecture_id`, `category_id`) VALUES
(92, 1, 1),
(93, 1, 3),
(96, 5, 2),
(97, 5, 4);

-- --------------------------------------------------------

--
-- Table structure for table `relatedlectures`
--

CREATE TABLE IF NOT EXISTS `relatedlectures` (
  `id` int(11) NOT NULL,
  `lecture_id` int(11) NOT NULL COMMENT 'Lekcija',
  `related_id` int(11) NOT NULL COMMENT 'Saistītā lekcija'
) ENGINE=InnoDB AUTO_INCREMENT=97 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `relatedlectures`
--

INSERT INTO `relatedlectures` (`id`, `lecture_id`, `related_id`) VALUES
(70, 3, 4),
(76, 6, 7),
(77, 7, 8),
(78, 9, 10),
(79, 5, 1),
(81, 4, 5),
(84, 18, 17),
(86, 14, 23),
(87, 23, 16),
(88, 16, 21),
(89, 15, 13),
(90, 13, 16),
(92, 17, 14),
(94, 1, 3),
(96, 28, 27);

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
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `sentlectures`
--

INSERT INTO `sentlectures` (`id`, `user_id`, `lecture_id`, `sent`, `created`) VALUES
(5, 2, 13, 1, '2019-12-03 12:02:16'),
(6, 2, 25, 1, '2019-12-04 06:47:39'),
(7, 2, 15, 1, '2019-12-04 06:53:55'),
(8, 2, 9, 1, '2019-12-04 07:02:22'),
(9, 2, 10, 1, '2019-12-04 07:02:22'),
(10, 2, 15, 1, '2019-12-11 07:36:59'),
(11, 2, 15, 1, '2019-12-11 07:48:46');

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
) ENGINE=InnoDB AUTO_INCREMENT=439 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `studentgoals`
--

INSERT INTO `studentgoals` (`id`, `user_id`, `type`, `diff_id`, `value`) VALUES
(432, 2, 'Šobrīd', 1, 4),
(433, 2, 'Šobrīd', 2, 2),
(434, 2, 'Šobrīd', 3, 1),
(435, 2, 'Šobrīd', 4, 4),
(436, 2, 'Šobrīd', 5, 4),
(437, 2, 'Vēlamais', 2, 5),
(438, 2, 'Vēlamais', 4, 8);

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
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `userlectureevaluations`
--

INSERT INTO `userlectureevaluations` (`id`, `lecture_id`, `evaluation_id`, `user_id`, `evaluation`, `created`) VALUES
(28, 18, 1, 2, '4', '2019-12-03 12:38:27'),
(30, 13, 4, 2, 'Tests', '2019-12-03 13:42:30'),
(31, 13, 3, 2, '2', '2019-12-10 08:53:29'),
(32, 13, 1, 2, '2', '2019-12-04 08:55:22');

-- --------------------------------------------------------

--
-- Table structure for table `userlectures`
--

CREATE TABLE IF NOT EXISTS `userlectures` (
  `id` int(11) NOT NULL,
  `lecture_id` int(11) NOT NULL COMMENT 'Lekcija',
  `user_id` int(11) NOT NULL COMMENT 'Students',
  `assigned` int(11) NOT NULL COMMENT 'Administrators',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Izveidots',
  `opened` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Atvērta',
  `opentime` timestamp NULL DEFAULT NULL COMMENT 'Atvēršanas laiks',
  `sent` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Nosūtīts e-pasts',
  `evaluated` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Novērtēta',
  `sent_times` int(11) NOT NULL DEFAULT '1' COMMENT 'Cik reizes nosūtīts e-pasts pirms novērtēšanas',
  `open_times` int(11) NOT NULL COMMENT 'Spēles reizes',
  `user_difficulty` int(11) NOT NULL COMMENT 'Maksimālās spējas uz doto brīdi'
) ENGINE=InnoDB AUTO_INCREMENT=91 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `userlectures`
--

INSERT INTO `userlectures` (`id`, `lecture_id`, `user_id`, `assigned`, `created`, `opened`, `opentime`, `sent`, `evaluated`, `sent_times`, `open_times`, `user_difficulty`) VALUES
(7, 18, 2, 1, '2019-12-02 08:50:58', 1, '2019-12-02 10:53:33', 1, 1, 6, 1, 12),
(8, 6, 2, 1, '2019-12-03 08:51:01', 1, '2019-12-03 11:41:03', 1, 1, 22, 2, 16),
(14, 13, 2, 1, '2019-12-04 08:51:05', 1, '2019-12-04 04:57:08', 1, 1, 14, 5, 14),
(15, 12, 2, 1, '2019-12-11 07:29:55', 1, '2019-12-11 05:29:52', 1, 0, 1, 2, 17),
(19, 16, 2, 1, '2019-12-11 07:25:31', 1, '2019-12-11 05:16:04', 1, 0, 1, 13, 8),
(26, 1, 2, 1, '2019-12-11 10:51:30', 0, NULL, 1, 0, 1, 0, 15),
(27, 7, 2, 1, '2019-12-11 10:54:26', 0, NULL, 1, 0, 1, 0, 15),
(90, 4, 2, 1, '2019-12-11 10:57:38', 0, NULL, 1, 0, 1, 0, 15);

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
(1, 'test', 'tester', '112', 'test@test.lv', '$2y$13$Q6qwbz72XUw4acnoTQsl7eHA5SugtEmxynv08ScuyVmeV0SGuf45C', 'Admin', '', '', '', '', 10, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2019-12-10 09:29:09', 0, '2019-11-14 23:59:59', ''),
(2, 'Students', 'Studentiņš', '112', 'student@test.lv', '$2y$13$Q6qwbz72XUw4acnoTQsl7eHA5SugtEmxynv08ScuyVmeV0SGuf45C', 'Student', '', '', '', 'tjI7VodU51a8pA-Qng971MFVzehC9dBp', 10, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2019-12-11 12:07:13', 10, '2019-12-02 23:59:59', 'tetsts'),
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
  ADD KEY `is_scale` (`is_scale`),
  ADD KEY `is_video_param` (`is_video_param`);

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
  ADD KEY `evaluated` (`evaluated`),
  ADD KEY `opentimes` (`open_times`),
  ADD KEY `diff` (`user_difficulty`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=29;
--
-- AUTO_INCREMENT for table `lecturesdifficulties`
--
ALTER TABLE `lecturesdifficulties`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=781;
--
-- AUTO_INCREMENT for table `lecturesevaluations`
--
ALTER TABLE `lecturesevaluations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=397;
--
-- AUTO_INCREMENT for table `lecturesfiles`
--
ALTER TABLE `lecturesfiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=27;
--
-- AUTO_INCREMENT for table `lectureshanddifficulties`
--
ALTER TABLE `lectureshanddifficulties`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=98;
--
-- AUTO_INCREMENT for table `relatedlectures`
--
ALTER TABLE `relatedlectures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=97;
--
-- AUTO_INCREMENT for table `sentlectures`
--
ALTER TABLE `sentlectures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT for table `studentgoals`
--
ALTER TABLE `studentgoals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=439;
--
-- AUTO_INCREMENT for table `studenthandgoals`
--
ALTER TABLE `studenthandgoals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=47;
--
-- AUTO_INCREMENT for table `userlectureevaluations`
--
ALTER TABLE `userlectureevaluations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=33;
--
-- AUTO_INCREMENT for table `userlectures`
--
ALTER TABLE `userlectures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=91;
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
