-- phpMyAdmin SQL Dump
-- version 4.9.5deb2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 15, 2020 at 06:41 PM
-- Server version: 10.3.25-MariaDB-0ubuntu0.20.04.1
-- PHP Version: 7.4.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bookstore_db_order_rep_server_2`
--

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `order_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `book_id`, `order_date`) VALUES
(1, 7, '2020-12-12 15:19:45'),
(2, 7, '2020-12-12 15:19:52'),
(3, 7, '2020-12-12 15:19:53'),
(4, 7, '2020-12-12 15:19:54'),
(5, 7, '2020-12-12 15:19:54'),
(6, 7, '2020-12-12 15:19:55'),
(7, 7, '2020-12-12 15:19:55'),
(8, 7, '2020-12-12 15:19:55'),
(9, 7, '2020-12-12 15:19:56'),
(10, 7, '2020-12-12 15:19:56'),
(11, 7, '2020-12-12 15:19:56'),
(12, 7, '2020-12-12 15:19:57'),
(13, 7, '2020-12-12 15:19:57'),
(14, 7, '2020-12-12 15:19:57'),
(15, 7, '2020-12-12 15:19:57'),
(16, 7, '2020-12-12 15:19:58'),
(17, 7, '2020-12-12 15:19:58'),
(18, 7, '2020-12-12 15:19:58'),
(19, 7, '2020-12-12 15:19:59'),
(20, 7, '2020-12-12 15:19:59'),
(21, 7, '2020-12-12 15:19:59'),
(22, 7, '2020-12-12 15:20:00'),
(23, 7, '2020-12-12 15:20:00'),
(24, 7, '2020-12-12 15:20:00'),
(25, 7, '2020-12-12 15:20:01'),
(26, 7, '2020-12-12 15:20:01'),
(27, 7, '2020-12-12 15:20:01'),
(28, 7, '2020-12-12 15:20:02'),
(29, 7, '2020-12-12 15:20:02'),
(30, 7, '2020-12-12 15:20:02'),
(31, 7, '2020-12-12 20:08:38'),
(32, 4, '2020-12-15 10:02:38'),
(33, 5, '2020-12-15 13:15:13');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
