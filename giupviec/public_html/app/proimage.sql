-- phpMyAdmin SQL Dump
-- version 4.6.5.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Feb 20, 2017 at 05:46 AM
-- Server version: 5.6.34-log
-- PHP Version: 5.6.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `proimage`
--

-- --------------------------------------------------------

--
-- Table structure for table `banner`
--

CREATE TABLE `banner` (
  `id` int(11) NOT NULL,
  `image` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `position` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `banner`
--

INSERT INTO `banner` (`id`, `image`, `position`, `name`, `created_at`, `updated_at`, `status`) VALUES
(3, 'images/banners/33.jpg', 'idol-end', 'Cuối trang trang Hình tượng', NULL, 1483948248, 1),
(5, 'images/banners/511.jpg', 'news-end', 'Cuối trang Tin tức', NULL, 1483948779, 1);

-- --------------------------------------------------------

--
-- Table structure for table `program_items`
--

CREATE TABLE `program_items` (
  `id` int(11) NOT NULL,
  `program_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `name_en` varchar(255) DEFAULT NULL,
  `teacher` varchar(255) DEFAULT NULL,
  `lesson` int(11) DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `program_items`
--

INSERT INTO `program_items` (`id`, `program_id`, `name`, `name_en`, `teacher`, `lesson`, `status`, `start_date`, `end_date`) VALUES
(13, 3, 'Xây dựng thương hiệu cá nhân', NULL, 'T.Hương', 10, NULL, '2016-01-01', '2016-01-05'),
(16, 1, 'NT', NULL, 'Mrs.Nga', 3, NULL, '2016-01-01', '2016-01-04'),
(20, 1, 'Pro Partent', NULL, 'Mrs.Mai', 1, NULL, '2016-01-05', '2016-01-09'),
(21, 3, 'Quản trị thương hiệu cá nhân', NULL, 'T.Hương', 5, NULL, '2016-01-11', '2016-01-15');

-- --------------------------------------------------------

--
-- Table structure for table `registers_courses`
--

CREATE TABLE `registers_courses` (
  `id` int(11) NOT NULL,
  `register_id` int(11) DEFAULT NULL,
  `program_id` int(11) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `complete` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `registers_courses`
--

INSERT INTO `registers_courses` (`id`, `register_id`, `program_id`, `item_id`, `start_date`, `end_date`, `complete`) VALUES
(4, 1, 1, 20, '2016-01-05', '2016-01-09', 1),
(5, 1, 3, 13, '2016-01-01', '2016-01-05', 1),
(6, 1, 1, 16, '2016-01-01', '2016-01-04', 0);

-- --------------------------------------------------------

--
-- Table structure for table `slider`
--

CREATE TABLE `slider` (
  `id` int(11) NOT NULL,
  `image` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `slider`
--

INSERT INTO `slider` (`id`, `image`, `status`) VALUES
(2, 'images/slider/211.jpg', 1),
(3, 'images/slider/hoc-1-kem-1.jpg', 1),
(4, 'images/slider/43.jpg', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `banner`
--
ALTER TABLE `banner`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `program_items`
--
ALTER TABLE `program_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `registers_courses`
--
ALTER TABLE `registers_courses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `slider`
--
ALTER TABLE `slider`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `banner`
--
ALTER TABLE `banner`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `program_items`
--
ALTER TABLE `program_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;
--
-- AUTO_INCREMENT for table `registers_courses`
--
ALTER TABLE `registers_courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `slider`
--
ALTER TABLE `slider`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
