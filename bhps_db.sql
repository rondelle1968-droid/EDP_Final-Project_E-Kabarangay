-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 21, 2026 at 03:46 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bhps_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_admin` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`id`, `email`, `username`, `password_hash`, `created_at`, `is_admin`) VALUES
(0, 'admin@example.com', 'admin', '$2y$10$7rLSvRl1pU.V.t5E0.9W2uS4zM/J.9Y.V.V.V.V.V.V.V.V.V.V.', '2026-05-07 07:11:39', 1),
(1, 'rondelle1968@gmail.com', 'rondelle', '$2y$10$uus01gw7CauIKXFwI7DHEe7EGOS6ozgXn4hX7y7iT0jayDTRztwI.', '2026-05-20 00:56:48', 0),
(2, 'marianomaryann830@gmail.com', 'maryann', '$2y$10$9JG2dA2QCBnuRt3qiUvnk.x.qEBOus.DNWrT3U3.QhWoJ5HzeSUU.', '2026-05-20 02:13:20', 0),
(3, 'john@gmail.com', 'john', '$2y$10$0mGkxoQfm0yMouE0gibaSuKmZzZMhsygPRj/CS3lSMOvmSAVrLoFm', '2026-05-20 02:22:49', 0),
(4, 'camille@gmail.com', 'camille', '$2y$10$mmgshd29cVjizRl7qT2hV.KpDjC0bANrGZyDcTHqPxy0eBIl27xme', '2026-05-20 02:26:31', 0),
(5, 'paul@gmail.com', 'paul', '$2y$10$yoFRReGAHxDDEn4qN4Yaje4hcseJHzwcaWfWHC9XXtQ.2AvIygoVW', '2026-05-20 02:29:22', 0),
(6, 'james@gmail.com', 'james', '$2y$10$WA9A426Cs25R2ebWc9XqeeBDb1IGj.U0LdQMnMFjVhL6zDzHYPTKG', '2026-05-20 02:31:48', 0),
(7, 'christopher@gmail.com', 'christopher@gmail.com', '$2y$10$HUriT.WpilyRBDdWZQOpvuYfTG9kn3CiZxiPAqn4IQYcPp5ekHdTO', '2026-05-20 02:35:06', 0),
(8, 'michael@gmail.com', 'michael', '$2y$10$g2ZpVnOa3P333D6PHNhVFuO94oGyO1RPWxvKdoK28IKYgL55hQybW', '2026-05-20 02:38:17', 0),
(9, 'jonathan@gmail.com', 'jonathan', '$2y$10$EEni3u.hO2VAgRswxgfdWu82XNI7t4myUbjNIlGejIIa4gmqwZHvy', '2026-05-20 02:40:59', 0),
(10, 'michelle@gmail.com', 'michelle', '$2y$10$jnzSEB8jJYhd9pXQlHzvpOKn5wT4o1t./2G6uf8pdy3qWY5b5SwbO', '2026-05-20 02:43:22', 0),
(11, 'robert@gmail.com', 'robert', '$2y$10$v8KgIPfzezHtQgiJFO1nNeAzkuvFNMNZag1fj8ZmEjqH5777FHnEG', '2026-05-20 02:46:41', 0),
(12, 'catherine@gmail.com', 'catherine', '$2y$10$SYT7mLkpGI0XenLRDB.znOVvqeRlYW9/wzGSCCM5RszW7gkT6Nple', '2026-05-20 02:49:04', 0),
(13, 'johnn@gmail.com', 'johnn', '$2y$10$bA6Cgvovcl7WY86l3MKMK.MEsNg9b0MO.5jbwzZ8KQDSAeGofB6Fm', '2026-05-20 02:52:19', 0),
(14, 'christian@gmail.com', 'christian', '$2y$10$4Sxy88oBDRpKMvUfxAL7rOCWPOgB1lbrYadQ4APExQjCNyLXh12/C', '2026-05-20 02:55:10', 0),
(15, 'jonathann@gmail.com', 'jonathann', '$2y$10$zz0xPNxPU2huMQ7106m0b.r6XlNAKndAPSoFq2YohYqmU23xf4dky', '2026-05-20 02:57:28', 0),
(16, 'david@gmail.com', 'david', '$2y$10$Wt9nrNgKpnJO.IYYsECkdeipdYAPHzhojNQC5yHynzZIZX.GA.UMy', '2026-05-20 03:00:13', 0),
(17, 'daniel@mail.com', 'daniel', '$2y$10$jddEccjqa7dx2yhCL/utF.eQ8Mk9XcnwC/9iEp9H.3emFjalJ5KLu', '2026-05-20 03:02:28', 0),
(18, 'nicole@gmail.com', 'nicole', '$2y$10$OKWMJO9yFCB2156NnPpebODrKGDnHQZonm7ZoWUmM/Pdigt9w6.zu', '2026-05-20 03:06:29', 0),
(19, 'maricel@gmail.com', 'maricel', '$2y$10$7GvIr6yYiYuGWsibmFJtvu8ct3UlFmaHdmdTrer5kBwbTNDgbqOsa', '2026-05-20 03:27:47', 0),
(20, 'alexander@gmail.com', 'alexander', '$2y$10$b/RuCgW90HbUXESinOuSgOg.I9Zm5g4jWxfDRq3M3Ti2CxpX/M0W2', '2026-05-20 03:30:38', 0),
(21, 'patricia@gmail.com', 'patricia', '$2y$10$jkIC0VaDmKtjG4hm3o.ctuaPYYLpX4zhvH1rfguz6lrqoUFu9JYN6', '2026-05-20 03:37:27', 0),
(22, 'joshua@gmail.com', 'joshua', '$2y$10$jB6Zfk81oC/b6NplJKlose7Iq8mw296etS1xnKzSL.9MFg.SWzZVC', '2026-05-20 03:40:09', 0),
(23, 'paull@gmail.com', 'paull', '$2y$10$5tUrF93j/gkNLiTsr/UyZuZw61oBn4Uqq0wWuieNloe/I4x2byhCS', '2026-05-20 03:45:37', 0),
(24, 'jamess@gmail.com', 'jamess', '$2y$10$pSJ.b2kmf8lEe9HVK1D9M.1Rk0bFE9qdN7KOUz2RzX/Z/mgboui7u', '2026-05-20 03:48:09', 0),
(25, 'christiann@gmail.com', 'christiann', '$2y$10$gb.X5rmgRnLgq06Lu3jT3OLYRZeUhy1Eoq7lpXdp9hY2PuZdv8u0G', '2026-05-20 03:50:52', 0),
(26, 'rowena@gmail.com', 'rowena', '$2y$10$QtDC3bjBy7z13uug6LBCpenDxiLd75Vy.Aj1nZvegZks6GdumKABC', '2026-05-20 03:53:46', 0),
(27, 'elena@gmail.com', 'elena', '$2y$10$9niIxOlriG1DNwwlP7el6eKWT9LfI7ABcqvCItzMuD0DqSJULGn4e', '2026-05-20 03:56:25', 0),
(28, 'patriciaa@gmail.com', 'patriciaa', '$2y$10$nuFhy/JIzc81nXtS/jSe1e/w031OZ73brqsOi/FNrPGPbCymvWSTW', '2026-05-20 04:05:12', 0),
(29, 'reynaldo@gmail.com', 'reynaldo', '$2y$10$YArtqIeCEIlLnDTelvRo5.hmPkUW4AHjYtpLvFQjhDj6rgh0WEp.q', '2026-05-20 04:51:39', 0),
(30, 'edgardo@gmail.com', 'edgardo', '$2y$10$MIXjw5t72fc5t4DHVdP4bOp8ZlG.R37ZVhIdb.HaDg0xz.Z.tCBea', '2026-05-20 04:54:42', 0),
(31, 'richard@gmail.com', 'richard', '$2y$10$uRdSSKSRgrgmdt3k8LX7GeCFkuTWDExXF06aEyxkv3F3OP4miP1nq', '2026-05-20 04:57:38', 0),
(32, 'aldrin@gmail.com', 'aldrin', '$2y$10$DZtvH5CntM6xoCRk068Pye3RvO7mX59DlXloi8jslDMTDoRqQ6YK2', '2026-05-20 05:00:35', 0),
(33, 'catherinee@gmail.com', 'catherinee', '$2y$10$XIa0R7iWDu.OYtNf3KrDnOtQ6qqX03u03wErw8b7XIkKODoGs6kD2', '2026-05-20 05:03:41', 0),
(34, 'piaallisa@gmail.com', 'pia', '$2y$10$dwUTKHmTL7vxoe9prnNWee4GljEUzcVc4UVs0Hp.A3nSXSIQJY2um', '2026-05-20 05:12:00', 0),
(35, 'bonifacionoel17@gmail.com', 'bonifacio0711', '$2y$10$Z4rZ/EBPJcizOM2pnNBlnuvFASAoSaeaiYuJ/zkGKsDdUPweeeXjO', '2026-05-20 05:17:57', 0),
(36, 'glenda@gmail.com', 'glenda', '$2y$10$OSVipdx3oGk8u5Qk84nRcejxrPtVbnokb/jpzEFL2ULP5bqgWZdHi', '2026-05-20 05:22:44', 0),
(37, 'elizabeth@gmail.com', 'elizabeth', '$2y$10$i8Ntbwxb1Qx5QlLMrSD9...DL74j391t2dq6sywxwzzHrA1YTmd0a', '2026-05-20 05:25:18', 0),
(38, 'joseph@gmail.com', 'joseph', '$2y$10$Aq7/k.HRNsdkF4FtLc8J1OnUygR5XAUHYbI.oqgTI07c1v3CH9ZW.', '2026-05-20 05:28:25', 0),
(39, 'jessica@gmail.com', 'jessica', '$2y$10$lMQACBuB8FyS6hUOQw.eoeoR7qDDt7jJoC5W2cJGcwQi4xKx8yc7y', '2026-05-20 05:31:29', 0),
(40, 'reynaldoo@gmail.com', 'reynaldoo', '$2y$10$MdxH8pq4lePbzbJa3Oz4VeqtiOuqcywUvFxWjTLpp4NdRCf9KDShC', '2026-05-20 05:35:20', 0),
(41, 'manuel@gmail.com', 'manuel', '$2y$10$lsMg9KCmfcBH9NRWl7N62OatLLdzreOsADsm4/EqB.ePoSYlkKmbK', '2026-05-20 05:38:44', 0),
(42, 'angel@gmail.com', 'angel', '$2y$10$fGlhjbEcjM3HJyDEEqm/cO7UC4.Tuqp2Vi5Khv8D0CBFCFgXkZL5m', '2026-05-20 05:42:18', 0),
(43, 'mary@gmail.com', 'mary', '$2y$10$Ub/hR2QdF3ninnjR.Sw5Uuq4bAC1yR209wKYtVYhcSTA9MN5t3DwO', '2026-05-20 05:45:20', 0),
(44, 'ron@gmail.com', 'ron', '$2y$10$mWsASHGNpDlH4YwIEFcO8.CO/LS5Gthrp9pUqxbL9DI0KHB8.g2Yy', '2026-05-20 05:48:00', 0),
(45, 'ali@gmail.com', 'ali', '$2y$10$18n4sOTM8LF7zA9SVmq0zOBKt6Erf11RQ0y3oZTS5eXaFaGwGNoem', '2026-05-20 05:51:09', 0),
(46, 'cassydecastro348@gmail.com', 'Cassie', '$2y$10$RHU4gPrxfzJay56R3Htbp.5vrhx1S6GhVd65bwPH7G4dfN463TgYW', '2026-05-20 05:57:57', 0),
(47, 'ana@gmail.com', 'ana54', '$2y$10$aujufERUOSoryzadFpypG.34j9nM6IGy56vzoy/u2r7b/GaDpqAPq', '2026-05-20 07:00:33', 0);

-- --------------------------------------------------------

--
-- Table structure for table `address`
--

CREATE TABLE `address` (
  `id` int(11) NOT NULL,
  `resident_id` int(11) NOT NULL,
  `address_type` enum('current','permanent') NOT NULL,
  `street` varchar(255) NOT NULL,
  `barangay` varchar(100) NOT NULL,
  `municipality` varchar(100) NOT NULL,
  `province` varchar(100) NOT NULL,
  `residence_type` enum('Own House','Boarding House') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `address`
--

INSERT INTO `address` (`id`, `resident_id`, `address_type`, `street`, `barangay`, `municipality`, `province`, `residence_type`) VALUES
(1, 1, 'current', 'Rizal st.', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Own House'),
(2, 1, 'permanent', 'Rizal st.', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Own House'),
(3, 2, 'current', 'Rizal st.', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Own House'),
(4, 2, 'permanent', 'Rizal st.', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Own House'),
(5, 3, 'current', 'Mercene st.', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Own House'),
(6, 3, 'permanent', 'Mercene st.', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Own House'),
(7, 4, 'current', 'Rizal st.', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Own House'),
(8, 4, 'permanent', 'Rizal st.', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Own House'),
(9, 5, 'current', 'Del Pilar st.', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Boarding House'),
(10, 5, 'permanent', 'Del Pilar st.', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Own House'),
(11, 6, 'current', 'Rizal st.', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Boarding House'),
(12, 6, 'permanent', 'Rizal st.', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Own House'),
(13, 7, 'current', 'National Road', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Boarding House'),
(14, 7, 'permanent', 'National Road', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Own House'),
(15, 8, 'current', 'San Jose st.', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Own House'),
(16, 8, 'permanent', 'San Jose st.', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Own House'),
(17, 9, 'current', 'San Jose st.', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Own House'),
(18, 9, 'permanent', 'San Jose st.', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Own House'),
(19, 10, 'current', 'National Road', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Own House'),
(20, 10, 'permanent', 'National Road', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Own House'),
(21, 11, 'current', 'Del Pilar st.', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Own House'),
(22, 11, 'permanent', 'Del Pilar st.', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Own House'),
(23, 12, 'current', 'Rizal st.', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Own House'),
(24, 12, 'permanent', 'Rizal st.', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Own House'),
(25, 13, 'current', 'Del Pilar st.', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Boarding House'),
(26, 13, 'permanent', 'Del Pilar st.', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Own House'),
(27, 14, 'current', 'Rizal st.', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Own House'),
(28, 14, 'permanent', 'Rizal st.', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Own House'),
(29, 15, 'current', 'Mercene st.', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Boarding House'),
(30, 15, 'permanent', 'Mercene st.', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Own House'),
(31, 16, 'current', 'Del Pilar st.', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Own House'),
(32, 16, 'permanent', 'Rizal st.', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Own House'),
(33, 17, 'current', 'Mercene st.', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Own House'),
(34, 17, 'permanent', 'Mercene st.', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Own House'),
(35, 18, 'current', 'P. Viana st.', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Own House'),
(36, 18, 'permanent', 'P. Viana st.', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Own House'),
(37, 19, 'current', 'San Jose st.', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Own House'),
(38, 19, 'permanent', 'San Jose st.', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Own House'),
(39, 20, 'current', 'San Jose st.', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Boarding House'),
(40, 20, 'permanent', 'San Jose st.', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Own House'),
(41, 21, 'current', 'National Road', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Own House'),
(42, 21, 'permanent', 'National Road', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Own House'),
(43, 22, 'current', 'Mercene st.', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Own House'),
(44, 22, 'permanent', 'Mercene st.', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Own House'),
(45, 23, 'current', 'Del Pilar st.', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Own House'),
(46, 23, 'permanent', 'Rizal st.', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Own House'),
(47, 24, 'current', 'San Jose st.', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Boarding House'),
(48, 24, 'permanent', 'San Jose st.', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Own House'),
(49, 25, 'current', 'P. Viana st.', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Boarding House'),
(50, 25, 'permanent', 'P. Viana st.', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Own House'),
(51, 26, 'current', 'P. Viana st.', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Boarding House'),
(52, 26, 'permanent', 'P. Viana st.', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Own House'),
(53, 27, 'current', 'San Jose st.', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Own House'),
(54, 27, 'permanent', 'San Jose st.', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Own House'),
(55, 28, 'current', 'P. Viana st.', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Own House'),
(56, 28, 'permanent', 'Rizal st.', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Own House'),
(57, 29, 'current', 'Mercene st.', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Own House'),
(58, 29, 'permanent', 'Del Pilar st.', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Own House'),
(59, 30, 'current', 'P. Viana st.', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Own House'),
(60, 30, 'permanent', 'P. Viana st.', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Own House'),
(61, 31, 'current', 'National Road', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Own House'),
(62, 31, 'permanent', 'National Road', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Own House'),
(63, 32, 'current', 'National Road', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Own House'),
(64, 32, 'permanent', 'P. Viana st.', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Own House'),
(65, 33, 'current', 'Mercene st.', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Own House'),
(66, 33, 'permanent', 'Mercene st.', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Own House'),
(67, 34, 'current', 'National Road', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Own House'),
(68, 34, 'permanent', 'National Road', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Own House'),
(69, 35, 'current', 'National Road', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Boarding House'),
(70, 35, 'permanent', 'San Jose st.', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Own House'),
(71, 36, 'current', 'Rizal st.', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Own House'),
(72, 36, 'permanent', 'Rizal st.', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Own House'),
(73, 37, 'current', 'National Road', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Own House'),
(74, 37, 'permanent', 'National Road', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Own House'),
(75, 38, 'current', 'Del Pilar st.', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Boarding House'),
(76, 38, 'permanent', 'Del Pilar st.', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Own House'),
(77, 39, 'current', 'Rizal st.', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Own House'),
(78, 39, 'permanent', 'Rizal st.', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Own House'),
(79, 40, 'current', 'Rizal st.', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Own House'),
(80, 40, 'permanent', 'Rizal st.', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Own House'),
(81, 41, 'current', 'San Jose st.', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Own House'),
(82, 41, 'permanent', 'Mercene st.', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Own House'),
(83, 42, 'current', 'San Jose st.', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Own House'),
(84, 42, 'permanent', 'Mercene st.', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Own House'),
(85, 43, 'current', 'San Jose st.', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Own House'),
(86, 43, 'permanent', 'Mercene st.', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Own House'),
(87, 44, 'current', 'San Jose st.', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Own House'),
(88, 44, 'permanent', 'Mercene st.', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Own House'),
(89, 45, 'current', 'San Jose st.', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Own House'),
(90, 45, 'permanent', 'Mercene st.', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Own House'),
(91, 46, 'current', 'San Jose st.', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Own House'),
(92, 46, 'permanent', 'San Jose st.', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Own House'),
(93, 47, 'current', 'Rizal st.', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Own House'),
(94, 47, 'permanent', 'Rizal st.', 'Poblacion 3', 'Mamburao', 'Occidental Mindoro', 'Own House');

-- --------------------------------------------------------

--
-- Table structure for table `admin_archived_request`
--

CREATE TABLE `admin_archived_request` (
  `id` int(11) NOT NULL,
  `resident_id` int(11) NOT NULL,
  `document_type` varchar(100) NOT NULL,
  `purpose` text NOT NULL,
  `status` varchar(50) DEFAULT NULL,
  `date_requested` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `archived_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `announcement_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `media_file` varchar(255) DEFAULT NULL,
  `media_path` varchar(255) DEFAULT NULL,
  `status` enum('Active','Archived') DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`announcement_id`, `title`, `description`, `media_file`, `media_path`, `status`, `created_at`, `updated_at`) VALUES
(1, '1ST SEMESTER BARANGAY ASSEMBLY 2026', 'Petsa: Marso 28, 2026\r\nOras: 2:00 P.M\r\nLugar: Barangay Hall, Barangay 3, Mamburao, Occidental Mindoro\r\nMakilahok. Makinig. Makibahagi!!!\r\nAlamin ang mga programa, proyekto, at plano para sa ating barangay.\r\nAng boses mo ay mahalaga!\r\nSAMA-SAMA PARA SA MAS MAUNLAD NA BARANGAY!', 'Upload/announcement/1779246930_Announcement (2).jpg', NULL, 'Active', '2026-05-20 03:15:30', '2026-05-20 03:15:30'),
(2, '𝐁𝐀𝐋𝐈𝐊 𝐄𝐒𝐊𝐖𝐄𝐋𝐀 𝟐𝟎𝟐𝟓', 'Para sa ating mahal na mga kabarangay, ang Sangguniang Kabataan ng Barangay Tres ay magsasagawa ng pamamahagi ng 𝑳𝑰𝑩𝑹𝑬𝑵𝑮 𝑺𝑪𝑯𝑶𝑶𝑳 𝑺𝑼𝑷𝑷𝑳𝑰𝑬𝑺 para sa mga mag-aaral mula Elementarya, Junior High School, Senior High School, at College na naninirahan sa ating barangay.\r\n\r\nSunday, June 15, 2025\r\n9:00 am\r\nBarangay Hall\r\n\r\n𝑭𝑰𝑹𝑺𝑻 𝑪𝑶𝑴𝑬, 𝑭𝑰𝑹𝑺𝑻 𝑺𝑬𝑹𝑽𝑬 𝑩𝑨𝑺𝑰𝑺', 'Upload/announcement/1779261371_Announcement (1).jpg', NULL, 'Active', '2026-05-20 07:16:11', '2026-05-20 07:16:11');

-- --------------------------------------------------------

--
-- Table structure for table `archived_requests`
--

CREATE TABLE `archived_requests` (
  `id` int(11) NOT NULL,
  `resident_id` int(11) NOT NULL,
  `document_type` varchar(100) NOT NULL,
  `purpose` text NOT NULL,
  `status` varchar(50) DEFAULT NULL,
  `date_requested` datetime DEFAULT NULL,
  `archived_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `archived_requests`
--

INSERT INTO `archived_requests` (`id`, `resident_id`, `document_type`, `purpose`, `status`, `date_requested`, `archived_at`) VALUES
(2, 47, 'Certificate of Residency', 'Work', 'Pending', '2026-05-20 15:09:26', '2026-05-20 07:09:31');

-- --------------------------------------------------------

--
-- Table structure for table `archived_residents`
--

CREATE TABLE `archived_residents` (
  `archive_id` int(11) NOT NULL,
  `resident_id` int(11) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `archived_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `archived_residents`
--

INSERT INTO `archived_residents` (`archive_id`, `resident_id`, `first_name`, `last_name`, `archived_at`) VALUES
(1, 5, 'Paul', 'Abeleda', '2026-05-20 07:13:51'),
(2, 11, 'Robert', 'Abeleda', '2026-05-20 07:13:53'),
(3, 12, 'Catherine', 'Abeleda', '2026-05-20 07:28:11');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `announcement_id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `sender_type` enum('Admin','Resident') NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `comment_text` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `announcement_id`, `account_id`, `sender_type`, `parent_id`, `comment_text`, `created_at`) VALUES
(1, 1, 47, 'Resident', NULL, 'ok', '2026-05-20 07:06:10'),
(2, 1, 47, 'Resident', 1, 'ok din', '2026-05-20 07:06:27'),
(4, 1, 0, 'Admin', 1, 'reply', '2026-05-20 07:15:35');

-- --------------------------------------------------------

--
-- Table structure for table `document_requests`
--

CREATE TABLE `document_requests` (
  `id` int(11) NOT NULL,
  `resident_id` int(11) NOT NULL,
  `document_type` varchar(100) NOT NULL,
  `purpose` text NOT NULL,
  `status` enum('Pending','Approved','Declined','Rejected') DEFAULT 'Pending',
  `date_requested` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `document_requests`
--

INSERT INTO `document_requests` (`id`, `resident_id`, `document_type`, `purpose`, `status`, `date_requested`) VALUES
(1, 1, 'Certificate of Indigency', 'Scholarship', 'Pending', '2026-05-20 03:10:55'),
(2, 47, 'Certificate of Indigency', 'TUPAD', 'Approved', '2026-05-20 07:01:46');

-- --------------------------------------------------------

--
-- Table structure for table `family_groups`
--

CREATE TABLE `family_groups` (
  `id` int(11) NOT NULL,
  `household_id` int(11) NOT NULL,
  `family_name` varchar(100) NOT NULL,
  `family_head_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `family_groups`
--

INSERT INTO `family_groups` (`id`, `household_id`, `family_name`, `family_head_id`, `created_at`) VALUES
(1, 1, '', 11, '2026-05-20 03:14:05'),
(2, 2, '', 41, '2026-05-20 06:05:21'),
(3, 3, '', 15, '2026-05-20 07:12:37');

-- --------------------------------------------------------

--
-- Table structure for table `households`
--

CREATE TABLE `households` (
  `id` int(11) NOT NULL,
  `household_head_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `households`
--

INSERT INTO `households` (`id`, `household_head_id`, `created_at`) VALUES
(1, 11, '2026-05-20 03:14:00'),
(2, 41, '2026-05-20 06:04:26'),
(3, 15, '2026-05-20 07:12:29');

-- --------------------------------------------------------

--
-- Table structure for table `household_info`
--

CREATE TABLE `household_info` (
  `id` int(11) NOT NULL,
  `resident_id` int(11) NOT NULL,
  `toilet_type` enum('Water Sealed','Open pit') NOT NULL,
  `water_source` enum('Community Piped','Well','Spring') NOT NULL,
  `iodized_salt` enum('Yes','No') NOT NULL,
  `iron_fortified_rice` enum('Yes','No') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `household_info`
--

INSERT INTO `household_info` (`id`, `resident_id`, `toilet_type`, `water_source`, `iodized_salt`, `iron_fortified_rice`) VALUES
(1, 1, 'Water Sealed', 'Community Piped', 'Yes', 'No'),
(2, 2, 'Water Sealed', 'Community Piped', 'Yes', 'Yes'),
(3, 3, 'Water Sealed', 'Well', 'No', 'Yes'),
(4, 4, 'Water Sealed', 'Well', 'No', 'Yes'),
(5, 5, 'Water Sealed', 'Well', 'Yes', 'No'),
(6, 6, 'Water Sealed', 'Community Piped', 'Yes', 'Yes'),
(7, 7, 'Water Sealed', 'Community Piped', 'Yes', 'No'),
(8, 8, 'Water Sealed', 'Community Piped', 'No', 'Yes'),
(9, 9, 'Water Sealed', 'Community Piped', 'Yes', 'Yes'),
(10, 10, 'Water Sealed', 'Community Piped', 'No', 'Yes'),
(11, 11, 'Water Sealed', 'Community Piped', 'No', 'Yes'),
(12, 12, 'Water Sealed', 'Community Piped', 'No', 'No'),
(13, 13, 'Water Sealed', 'Community Piped', 'Yes', 'Yes'),
(14, 14, 'Water Sealed', 'Community Piped', 'No', 'Yes'),
(15, 15, 'Water Sealed', 'Community Piped', 'Yes', 'Yes'),
(16, 16, 'Water Sealed', 'Community Piped', 'No', 'No'),
(17, 17, 'Water Sealed', 'Community Piped', 'No', 'No'),
(18, 18, 'Water Sealed', 'Community Piped', 'No', 'No'),
(19, 19, 'Water Sealed', 'Community Piped', 'No', 'No'),
(20, 20, 'Water Sealed', 'Community Piped', 'No', 'No'),
(21, 21, 'Water Sealed', 'Community Piped', 'No', 'No'),
(22, 22, 'Water Sealed', 'Community Piped', 'No', 'Yes'),
(23, 23, 'Water Sealed', 'Well', 'Yes', 'Yes'),
(24, 24, 'Water Sealed', 'Community Piped', 'No', 'Yes'),
(25, 25, 'Water Sealed', 'Community Piped', 'No', 'No'),
(26, 26, 'Water Sealed', 'Well', 'Yes', 'Yes'),
(27, 27, 'Water Sealed', 'Well', 'Yes', 'No'),
(28, 28, 'Water Sealed', 'Community Piped', 'No', 'Yes'),
(29, 29, 'Water Sealed', 'Community Piped', 'No', 'Yes'),
(30, 30, 'Water Sealed', 'Community Piped', 'Yes', 'Yes'),
(31, 31, 'Water Sealed', 'Community Piped', 'No', 'No'),
(32, 32, 'Water Sealed', 'Community Piped', 'No', 'Yes'),
(33, 33, 'Water Sealed', 'Community Piped', 'No', 'Yes'),
(34, 34, 'Water Sealed', 'Community Piped', 'Yes', 'Yes'),
(35, 35, 'Water Sealed', 'Community Piped', 'Yes', 'Yes'),
(36, 36, 'Water Sealed', 'Community Piped', 'No', 'No'),
(37, 37, 'Water Sealed', 'Well', 'No', 'Yes'),
(38, 38, 'Water Sealed', 'Community Piped', 'No', 'No'),
(39, 39, 'Water Sealed', 'Community Piped', 'Yes', 'Yes'),
(40, 40, 'Water Sealed', 'Community Piped', 'No', 'Yes'),
(41, 41, 'Water Sealed', 'Well', 'Yes', 'Yes'),
(42, 42, 'Water Sealed', 'Well', 'Yes', 'Yes'),
(43, 43, 'Water Sealed', 'Well', 'Yes', 'Yes'),
(44, 44, 'Water Sealed', 'Well', 'Yes', 'Yes'),
(45, 45, 'Water Sealed', 'Well', 'Yes', 'Yes'),
(46, 46, 'Water Sealed', 'Community Piped', 'Yes', 'No'),
(47, 47, 'Water Sealed', 'Community Piped', 'No', 'Yes');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `resident_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `resident_id`, `message`, `is_read`, `created_at`) VALUES
(1, 1, 'Your registration has been approved. Welcome to E-Kabarangay!', 1, '2026-05-20 01:00:17'),
(2, 18, 'Your registration has been approved. Welcome to E-Kabarangay!', 0, '2026-05-20 03:12:10'),
(3, 17, 'Your registration has been approved. Welcome to E-Kabarangay!', 0, '2026-05-20 03:12:12'),
(4, 16, 'Your registration has been approved. Welcome to E-Kabarangay!', 0, '2026-05-20 03:12:13'),
(5, 15, 'Your registration has been approved. Welcome to E-Kabarangay!', 0, '2026-05-20 03:12:14'),
(6, 14, 'Your registration has been approved. Welcome to E-Kabarangay!', 0, '2026-05-20 03:12:15'),
(7, 13, 'Your registration has been approved. Welcome to E-Kabarangay!', 0, '2026-05-20 03:12:15'),
(8, 12, 'Your registration has been approved. Welcome to E-Kabarangay!', 0, '2026-05-20 03:12:16'),
(9, 11, 'Your registration has been approved. Welcome to E-Kabarangay!', 0, '2026-05-20 03:12:18'),
(10, 10, 'Your registration has been approved. Welcome to E-Kabarangay!', 0, '2026-05-20 03:12:19'),
(11, 9, 'Your registration has been approved. Welcome to E-Kabarangay!', 0, '2026-05-20 03:12:19'),
(12, 8, 'Your registration has been approved. Welcome to E-Kabarangay!', 0, '2026-05-20 03:12:20'),
(13, 7, 'Your registration has been approved. Welcome to E-Kabarangay!', 0, '2026-05-20 03:12:21'),
(14, 6, 'Your registration has been approved. Welcome to E-Kabarangay!', 0, '2026-05-20 03:12:22'),
(15, 5, 'Your registration has been approved. Welcome to E-Kabarangay!', 0, '2026-05-20 03:12:23'),
(16, 4, 'Your registration has been approved. Welcome to E-Kabarangay!', 0, '2026-05-20 03:12:24'),
(17, 3, 'Your registration has been approved. Welcome to E-Kabarangay!', 0, '2026-05-20 03:12:25'),
(18, 2, 'Your registration has been approved. Welcome to E-Kabarangay!', 0, '2026-05-20 03:12:25'),
(19, 45, 'Your registration has been approved. Welcome to E-Kabarangay!', 0, '2026-05-20 05:54:05'),
(20, 44, 'Your registration has been approved. Welcome to E-Kabarangay!', 0, '2026-05-20 05:54:08'),
(21, 43, 'Your registration has been approved. Welcome to E-Kabarangay!', 0, '2026-05-20 05:54:10'),
(22, 42, 'Your registration has been approved. Welcome to E-Kabarangay!', 0, '2026-05-20 05:54:12'),
(23, 41, 'Your registration has been approved. Welcome to E-Kabarangay!', 0, '2026-05-20 05:54:14'),
(24, 46, 'Your registration has been approved. Welcome to E-Kabarangay!', 0, '2026-05-20 06:03:59'),
(25, 40, 'Your registration has been approved. Welcome to E-Kabarangay!', 0, '2026-05-20 06:06:43'),
(26, 39, 'Your registration has been approved. Welcome to E-Kabarangay!', 0, '2026-05-20 06:06:44'),
(27, 38, 'Your registration has been approved. Welcome to E-Kabarangay!', 0, '2026-05-20 06:06:45'),
(28, 37, 'Your registration has been approved. Welcome to E-Kabarangay!', 0, '2026-05-20 06:06:45'),
(29, 36, 'Your registration has been approved. Welcome to E-Kabarangay!', 0, '2026-05-20 06:06:46'),
(30, 35, 'Your registration has been approved. Welcome to E-Kabarangay!', 0, '2026-05-20 06:06:47'),
(31, 34, 'Your registration has been approved. Welcome to E-Kabarangay!', 0, '2026-05-20 06:06:47'),
(32, 33, 'Your registration has been approved. Welcome to E-Kabarangay!', 0, '2026-05-20 06:06:48'),
(33, 32, 'Your registration has been approved. Welcome to E-Kabarangay!', 0, '2026-05-20 06:06:49'),
(34, 31, 'Your registration has been approved. Welcome to E-Kabarangay!', 0, '2026-05-20 06:06:49'),
(35, 30, 'Your registration has been approved. Welcome to E-Kabarangay!', 0, '2026-05-20 06:06:50'),
(36, 29, 'Your registration has been approved. Welcome to E-Kabarangay!', 0, '2026-05-20 06:06:51'),
(37, 47, 'Your request for Certificate of Indigency has been approved. You may now proceed to the Barangay Hall to pick up your requested documents.', 0, '2026-05-20 07:13:08'),
(38, 47, 'Your registration has been approved. Welcome to E-Kabarangay!', 0, '2026-05-20 07:13:36'),
(39, 28, 'Your registration was rejected. Please contact the barangay office for details.', 0, '2026-05-20 07:13:38'),
(40, 27, 'Your registration was rejected. Please contact the barangay office for details.', 0, '2026-05-20 07:13:39'),
(41, 26, 'Your registration has been approved. Welcome to E-Kabarangay!', 0, '2026-05-20 07:13:42'),
(42, 25, 'Your registration has been approved. Welcome to E-Kabarangay!', 0, '2026-05-20 07:13:43'),
(43, 24, 'Your registration has been approved. Welcome to E-Kabarangay!', 0, '2026-05-20 07:13:45');

-- --------------------------------------------------------

--
-- Table structure for table `residents`
--

CREATE TABLE `residents` (
  `id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `household_id` int(11) DEFAULT NULL,
  `family_group_id` int(11) DEFAULT NULL,
  `first_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) NOT NULL,
  `civil_status` enum('Single','Married','Separated','Divorced','Widowed','Live-in') NOT NULL,
  `sex` enum('Male','Female') NOT NULL,
  `dob` date NOT NULL,
  `place_of_birth` varchar(255) NOT NULL,
  `contact_no` varchar(11) NOT NULL,
  `religion` varchar(100) NOT NULL,
  `employment_status` enum('Employed','Unemployed','Self-employed','Student','Retired','Homemaker','Part-time') NOT NULL,
  `educational_attainment` enum('Elementary','High school','Senior high school','College / Undergraduate','College Graduate','No formal education','Vocational / Technical') NOT NULL,
  `family_planning` enum('Contraceptive Method','None') DEFAULT NULL,
  `pregnancy_status` enum('Pregnant','Delivered and Breastfeeding','None') DEFAULT NULL,
  `breastfeeding_type` enum('Exclusive breastfeeding','Formula feeding','Mixed feeding') DEFAULT NULL,
  `id_picture` varchar(255) NOT NULL,
  `status` enum('Pending','Approved','Rejected') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `residents`
--

INSERT INTO `residents` (`id`, `account_id`, `household_id`, `family_group_id`, `first_name`, `middle_name`, `last_name`, `civil_status`, `sex`, `dob`, `place_of_birth`, `contact_no`, `religion`, `employment_status`, `educational_attainment`, `family_planning`, `pregnancy_status`, `breastfeeding_type`, `id_picture`, `status`) VALUES
(1, 1, NULL, NULL, 'Rondelle', 'Lacanaria', 'Magundayao', 'Single', 'Male', '2002-03-18', 'Calapan City', '09456334371', 'Catholic', 'Student', 'College / Undergraduate', 'None', 'None', NULL, 'user_1_1779238608.png', 'Approved'),
(2, 2, NULL, NULL, 'Mary Ann ', 'Franco', 'Mariano', 'Single', 'Female', '2005-04-10', 'Mamburao', '09534117888', 'Born Again', 'Student', 'College / Undergraduate', 'None', 'None', NULL, 'user_2_1779243200.png', 'Approved'),
(3, 3, NULL, NULL, 'John ', 'Garcia', 'Bautista', 'Live-in', 'Male', '1997-12-04', 'Cavite City', '09196001338', 'Jehovah Witnesses', 'Employed', 'High school', 'None', 'None', NULL, 'user_3_1779243769.png', 'Approved'),
(4, 4, NULL, NULL, 'Camille', 'Bautista', 'Dimaculangan', 'Single', 'Female', '2001-02-13', 'Mamburao', '09594078161', 'Catholic', 'Employed', 'No formal education', 'Contraceptive Method', 'Delivered and Breastfeeding', 'Mixed feeding', 'user_4_1779243991.png', 'Approved'),
(5, 5, 1, 1, 'Paul', 'Ramos', 'Abeleda', 'Live-in', 'Male', '1967-06-07', 'Mamburao', '09192832764', 'Inc', 'Employed', 'Senior high school', 'None', 'None', NULL, 'user_5_1779244162.png', 'Approved'),
(6, 6, NULL, NULL, 'James', 'Castillo', 'Dimaculangan', 'Divorced', 'Male', '1987-11-16', 'Tagaytay', '09242388496', 'Jehovas Withnesses', 'Employed', 'High school', 'None', 'None', NULL, 'user_6_1779244308.png', 'Approved'),
(7, 7, NULL, NULL, 'Christopher', 'Reyes', 'Malabanan', 'Widowed', 'Male', '1966-10-15', 'Mamburao', '09801845146', 'Inc', 'Unemployed', 'No formal education', 'None', 'None', NULL, 'user_7_1779244506.png', 'Approved'),
(8, 8, NULL, NULL, 'Michael', 'Gonzales', 'Quinto', 'Widowed', 'Male', '1947-01-20', 'Mamburao', '09543039117', 'Catholic', 'Retired', 'High school', 'None', 'None', NULL, 'user_8_1779244697.png', 'Approved'),
(9, 9, NULL, NULL, 'Jonathan', 'Ramos', 'Madrigal', 'Married', 'Male', '1948-08-04', 'Mamburao', '09983930103', 'Catholic', 'Retired', 'Vocational / Technical', 'None', 'None', NULL, 'user_9_1779244859.png', 'Approved'),
(10, 10, NULL, NULL, 'Michelle', 'Dela Cruz', 'Panganiban', 'Divorced', 'Female', '2002-02-22', 'Mamburao', '09670106513', 'inc', 'Employed', 'College / Undergraduate', 'None', 'Pregnant', NULL, 'user_10_1779245002.png', 'Approved'),
(11, 11, 1, 1, 'Robert', 'Garcia', 'Abeleda', 'Married', 'Male', '1962-08-16', 'STC', '09260647468', 'SDA', 'Employed', 'Senior high school', 'None', 'None', NULL, 'user_11_1779245201.png', 'Approved'),
(12, 12, 1, 1, 'Catherine', 'Santiago', 'Abeleda', 'Separated', 'Female', '2007-09-03', 'Mmaburao', '09136193990', 'Catholic', 'Self-employed', 'College Graduate', 'Contraceptive Method', 'Delivered and Breastfeeding', 'Formula feeding', 'user_12_1779245344.png', 'Approved'),
(13, 13, NULL, NULL, 'John', 'Castillo', 'Tria', 'Widowed', 'Male', '2005-09-07', 'Mamburao', '09513542784', 'Jehovahs Withnesses', 'Self-employed', 'Vocational / Technical', 'None', 'None', NULL, 'user_13_1779245539.png', 'Approved'),
(14, 14, NULL, NULL, 'Christian', 'Diaz', 'Mercado', 'Live-in', 'Male', '1978-10-07', 'Mamburao', '09487401640', 'Catholic', 'Employed', 'High school', 'None', 'None', NULL, 'user_14_1779245710.png', 'Approved'),
(15, 15, 3, 3, 'Jonathann', 'Alvarez', 'Agustin', 'Married', 'Male', '1967-10-18', 'Mmaburao', '09045053315', 'Jehovahs Withnesse', 'Homemaker', 'College / Undergraduate', 'None', 'None', NULL, 'user_15_1779245848.png', 'Approved'),
(16, 16, NULL, NULL, 'David', 'Aquino', 'Inigo', 'Live-in', 'Male', '1983-05-06', 'Mmaburao', '09073375433', 'Catholic', 'Self-employed', 'College / Undergraduate', 'None', 'None', NULL, 'user_16_1779246013.png', 'Approved'),
(17, 17, NULL, NULL, 'Daniel', 'Mendoza', 'Agustin', 'Separated', 'Male', '2001-10-14', 'Manila', '09698169340', 'SDA', 'Employed', 'Vocational / Technical', 'None', 'None', NULL, 'user_17_1779246148.png', 'Approved'),
(18, 18, NULL, NULL, 'Nicole', 'Cruz', 'Gonzales', 'Divorced', 'Female', '1950-05-22', 'Cavite', '09236629946', 'Jehovahs Witnesses', 'Retired', 'Senior high school', 'None', 'None', NULL, 'user_18_1779246389.png', 'Approved'),
(19, 19, NULL, NULL, 'Maricel', 'Fernandez', 'Abeleda', 'Live-in', 'Female', '2004-05-17', 'Manila', '09134332003', 'SDA', 'Student', 'Vocational / Technical', 'None', 'Pregnant', NULL, 'user_19_1779247667.png', 'Pending'),
(20, 20, NULL, NULL, 'Alexander', 'Cruz', 'Inigo', 'Live-in', 'Male', '1986-03-26', 'Mamburao', '09083172788', 'Jehovahs Witnesses', 'Employed', 'Vocational / Technical', 'None', 'None', NULL, 'user_20_1779247838.png', 'Pending'),
(21, 21, NULL, NULL, 'Patricia', 'Garcia', 'Mercado', 'Married', 'Female', '1948-08-21', 'Mamburao', '09434558123', 'INC', 'Homemaker', 'No formal education', 'None', 'Pregnant', NULL, 'user_21_1779248247.png', 'Pending'),
(22, 22, NULL, NULL, 'Joshua', 'Castillo', 'Magundayao', 'Separated', 'Male', '1966-08-01', 'Mamburao', '09668893734', 'SDA', 'Unemployed', 'College / Undergraduate', 'None', 'None', NULL, 'user_22_1779248409.png', 'Pending'),
(23, 23, NULL, NULL, 'Paull', 'Lopez', 'Villarosa', 'Divorced', 'Male', '1998-03-28', 'Mamburao', '09537556464', 'Cathilic', 'Homemaker', 'Elementary', 'None', 'None', NULL, 'user_23_1779248737.png', 'Pending'),
(24, 24, NULL, NULL, 'Jamess', 'Diaz', 'Solis', 'Single', 'Male', '1998-08-22', 'Mamburao', '09374529912', 'BAC', 'Employed', 'Elementary', 'None', 'None', NULL, 'user_24_1779248889.png', 'Approved'),
(25, 25, NULL, NULL, 'Christiann', 'Torres', 'Tolentino', 'Single', 'Male', '1999-10-26', 'Mamburao', '09651850671', 'SDA', 'Part-time', 'No formal education', 'None', 'None', NULL, 'user_25_1779249052.png', 'Approved'),
(26, 26, NULL, NULL, 'Rowena', 'Aquino', 'Solis', 'Married', 'Female', '2003-05-15', 'Lipa City', '09965075273', 'BAC', 'Student', 'No formal education', 'None', 'Pregnant', NULL, 'user_26_1779249226.png', 'Approved'),
(27, 27, NULL, NULL, 'Elena', 'Garcia', 'Limos', 'Single', 'Female', '1952-08-26', 'Mamburao', '09349578856', 'Jehovahs Witnesses', 'Homemaker', 'Senior high school', 'None', 'None', NULL, 'user_27_1779249385.png', 'Rejected'),
(28, 28, NULL, NULL, 'Patriaciaa', 'Santos', 'Mercado', 'Widowed', 'Female', '1953-02-27', 'Mamburao', '09022941318', 'SDA', 'Retired', 'Vocational / Technical', 'None', 'None', NULL, 'user_28_1779249912.png', 'Rejected'),
(29, 29, NULL, NULL, 'Reynaldo ', 'Cruz', 'Panganiban', 'Single', 'Male', '1987-05-22', 'Mamburao, Occidental Mindoro', '09281206797', 'BAC', 'Employed', 'Senior high school', 'None', 'None', NULL, 'user_29_1779252699.png', 'Approved'),
(30, 30, NULL, NULL, 'Edgardo', 'Diaz', 'Mercado', 'Married', 'Male', '1996-02-02', 'Lipa City', '09947174648', 'Jehovah\'s Witnesses', 'Unemployed', 'Elementary', 'None', 'None', NULL, 'user_30_1779252882.png', 'Approved'),
(31, 31, NULL, NULL, 'Richard', 'Mendoza', 'Valencia', 'Single', 'Male', '2009-03-16', 'Calapan', '09296717565', 'BAC', 'Student', 'Senior high school', 'None', 'None', NULL, 'user_31_1779253058.png', 'Approved'),
(32, 32, NULL, NULL, 'Aldrin', '', 'Mercado', 'Divorced', 'Male', '1973-02-25', 'STC', '09876038597', 'SDA', 'Student', 'High school', 'None', 'None', NULL, 'user_32_1779253235.png', 'Approved'),
(33, 33, NULL, NULL, 'Catherinee', 'Lopez', 'Rosales', 'Live-in', 'Female', '1976-10-19', 'Mamburao', '09278755886', 'SDA', 'Homemaker', 'Vocational / Technical', 'None', 'None', NULL, 'user_33_1779253421.png', 'Approved'),
(34, 34, NULL, NULL, 'Pia Allisa', 'Viernes', 'Pauste', 'Single', 'Female', '2006-08-23', 'Mansalay', '09532246920', 'Catholic', 'Student', 'College / Undergraduate', 'None', 'None', NULL, 'user_34_1779253920.png', 'Approved'),
(35, 35, NULL, NULL, 'Noel', 'Gabito', 'Bonifacio', 'Single', 'Male', '2006-07-11', 'Mamburao', '09703833785', 'Roman Catholic', 'Student', 'College / Undergraduate', 'None', 'None', NULL, 'user_35_1779254277.png', 'Approved'),
(36, 36, NULL, NULL, 'Glenda', 'De Castro', 'Malabanan', 'Married', 'Female', '1995-08-02', 'Mamburao', '09951718702', 'seventh day Adventist', 'Homemaker', 'High school', 'Contraceptive Method', 'Delivered and Breastfeeding', 'Formula feeding', 'user_36_1779254564.png', 'Approved'),
(37, 37, NULL, NULL, 'Elizabeth', '', 'Villarosa', 'Separated', 'Female', '1994-11-22', 'Sablayan', '09611724005', 'Catholic', 'Employed', 'Senior high school', 'Contraceptive Method', 'Delivered and Breastfeeding', 'Exclusive breastfeeding', 'user_37_1779254718.png', 'Approved'),
(38, 38, NULL, NULL, 'Joseph', 'Mendoza', 'Madrigal', 'Divorced', 'Male', '1982-11-01', 'Cavite City', '09217594647', 'Born Again', 'Employed', 'College / Undergraduate', 'None', 'None', NULL, 'user_38_1779254905.png', 'Approved'),
(39, 39, 3, 3, 'Jessica', '', 'Agustin', 'Widowed', 'Female', '1951-05-25', 'Mamburao', '09394210470', 'Jehovahs Witnesses', 'Homemaker', 'No formal education', 'None', 'None', NULL, 'user_39_1779255089.png', 'Approved'),
(40, 40, NULL, NULL, 'Reynaldo', 'Alvarez', 'Abeleda', 'Live-in', 'Male', '1986-11-03', 'Calapan', '09612004711', 'Inc', 'Homemaker', 'High school', 'None', 'None', NULL, 'user_40_1779255321.png', 'Approved'),
(41, 41, 2, 2, 'Manuel', 'Fernandez', 'Ramos', 'Married', 'Male', '1982-07-26', 'Manila', '09555246920', 'Born Again', 'Employed', 'College Graduate', 'None', 'None', NULL, 'user_41_1779255524.png', 'Approved'),
(42, 42, 2, 2, 'Angel', 'Castillo', 'Ramos', 'Married', 'Female', '1982-03-19', 'Manila', '09366547908', 'Born Again', 'Homemaker', 'College Graduate', 'None', 'None', NULL, 'user_42_1779255738.png', 'Approved'),
(43, 43, 2, 2, 'Mary', 'Fernandez', 'Ramos', 'Single', 'Female', '2009-02-04', 'Manila', '09534117888', 'Born Again', 'Student', 'Senior high school', 'None', 'None', NULL, 'user_43_1779255920.png', 'Approved'),
(44, 44, 2, 2, 'Ron', 'Fernandez', 'Ramos', 'Single', 'Male', '2003-02-09', 'Manila', '09856673207', 'Born Again', 'Student', 'College / Undergraduate', 'None', 'None', NULL, 'user_44_1779256080.png', 'Approved'),
(45, 45, 2, 2, 'Ali', 'Fernandez', 'Ramos', 'Single', 'Female', '2006-05-23', 'Manila', '09975223068', 'Born Again', 'Student', 'College / Undergraduate', 'None', 'None', NULL, 'user_45_1779256269.png', 'Approved'),
(46, 46, NULL, NULL, 'Maria Cassandra', 'Gan', 'Cayco', 'Single', 'Female', '2005-12-16', 'Calapan Hospital', '09532789627', 'Christian', 'Student', 'College / Undergraduate', 'None', 'None', NULL, 'user_46_1779256677.png', 'Approved'),
(47, 47, NULL, NULL, 'Ana', '', 'Villarosa', 'Married', 'Female', '2003-07-04', 'San Jose', '09809885165', 'Catholic', 'Homemaker', 'No formal education', 'Contraceptive Method', 'None', NULL, 'ID_47_1779260711.jpg', 'Approved');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `address`
--
ALTER TABLE `address`
  ADD PRIMARY KEY (`id`),
  ADD KEY `resident_id` (`resident_id`);

--
-- Indexes for table `admin_archived_request`
--
ALTER TABLE `admin_archived_request`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`announcement_id`);

--
-- Indexes for table `archived_requests`
--
ALTER TABLE `archived_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `resident_id` (`resident_id`);

--
-- Indexes for table `archived_residents`
--
ALTER TABLE `archived_residents`
  ADD PRIMARY KEY (`archive_id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `announcement_id` (`announcement_id`),
  ADD KEY `account_id` (`account_id`),
  ADD KEY `fk_parent_comment` (`parent_id`);

--
-- Indexes for table `document_requests`
--
ALTER TABLE `document_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_resident_id` (`resident_id`);

--
-- Indexes for table `family_groups`
--
ALTER TABLE `family_groups`
  ADD PRIMARY KEY (`id`),
  ADD KEY `household_id` (`household_id`),
  ADD KEY `family_groups_ibfk_2` (`family_head_id`);

--
-- Indexes for table `households`
--
ALTER TABLE `households`
  ADD PRIMARY KEY (`id`),
  ADD KEY `households_ibfk_1` (`household_head_id`);

--
-- Indexes for table `household_info`
--
ALTER TABLE `household_info`
  ADD PRIMARY KEY (`id`),
  ADD KEY `resident_id` (`resident_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `resident_id` (`resident_id`),
  ADD KEY `idx_notif_resident_id` (`resident_id`);

--
-- Indexes for table `residents`
--
ALTER TABLE `residents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `account_id` (`account_id`),
  ADD KEY `household_id` (`household_id`),
  ADD KEY `family_group_id` (`family_group_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `address`
--
ALTER TABLE `address`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=95;

--
-- AUTO_INCREMENT for table `admin_archived_request`
--
ALTER TABLE `admin_archived_request`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `announcement_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `archived_requests`
--
ALTER TABLE `archived_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `archived_residents`
--
ALTER TABLE `archived_residents`
  MODIFY `archive_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `document_requests`
--
ALTER TABLE `document_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `family_groups`
--
ALTER TABLE `family_groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `households`
--
ALTER TABLE `households`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `household_info`
--
ALTER TABLE `household_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `residents`
--
ALTER TABLE `residents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `address`
--
ALTER TABLE `address`
  ADD CONSTRAINT `address_ibfk_1` FOREIGN KEY (`resident_id`) REFERENCES `residents` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `archived_requests`
--
ALTER TABLE `archived_requests`
  ADD CONSTRAINT `archived_requests_ibfk_1` FOREIGN KEY (`resident_id`) REFERENCES `residents` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`announcement_id`) REFERENCES `announcements` (`announcement_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_parent_comment` FOREIGN KEY (`parent_id`) REFERENCES `comments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `document_requests`
--
ALTER TABLE `document_requests`
  ADD CONSTRAINT `document_requests_ibfk_1` FOREIGN KEY (`resident_id`) REFERENCES `residents` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_document_requests_residents` FOREIGN KEY (`resident_id`) REFERENCES `residents` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `family_groups`
--
ALTER TABLE `family_groups`
  ADD CONSTRAINT `family_groups_ibfk_1` FOREIGN KEY (`household_id`) REFERENCES `households` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `family_groups_ibfk_2` FOREIGN KEY (`family_head_id`) REFERENCES `residents` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `households`
--
ALTER TABLE `households`
  ADD CONSTRAINT `households_ibfk_1` FOREIGN KEY (`household_head_id`) REFERENCES `residents` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `household_info`
--
ALTER TABLE `household_info`
  ADD CONSTRAINT `household_info_ibfk_1` FOREIGN KEY (`resident_id`) REFERENCES `residents` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`resident_id`) REFERENCES `residents` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `residents`
--
ALTER TABLE `residents`
  ADD CONSTRAINT `residents_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `residents_ibfk_2` FOREIGN KEY (`household_id`) REFERENCES `households` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `residents_ibfk_3` FOREIGN KEY (`family_group_id`) REFERENCES `family_groups` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
