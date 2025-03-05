-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 04, 2025 at 03:23 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `login_register`
--

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sit_in_sessions`
--

CREATE TABLE `sit_in_sessions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `hours` int(11) NOT NULL,
  `session_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `idno` varchar(20) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `midname` varchar(50) NOT NULL,
  `course` varchar(100) NOT NULL,
  `yearlvl` int(11) NOT NULL,
  `emailadd` varchar(50) NOT NULL,
  `username` int(11) NOT NULL,
  `password` varchar(250) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `idno`, `lastname`, `firstname`, `midname`, `course`, `yearlvl`, `emailadd`, `username`, `password`, `created_at`) VALUES
(10, '22653414', 'Muñasque', 'Stanlee', 'Riveral', 'BSIT', 3, 'munasquestanlee@gmail.com', 22653414, '$2y$10$ZW26cliLfepLZSHs4y2h3OQoxzNr9gRVVVhoQq2QiTzEIYlx9caQ.', '2025-02-26 07:52:51'),
(11, '12345', 'Garcia', 'Vaugn', 'Xhander', 'BSIT', 2, 'vaughn@gmail.com', 12345, '$2y$10$fw2HqfR2dtj.jVsllio8a.QmG/y0N4ODaDpvKKMZwyOCqx7AQD1su', '2025-02-26 08:51:57'),
(12, '123', 'Rotaqiuo', 'Kester', 'Jude', 'ACT', 1, 'kester@gmail.com', 123, '$2y$10$ZzLBXmjEixgeXYEJJcY27OjkZOIYvswIBAMwnSfqBkeiH5gAQ65Ji', '2025-02-26 10:51:36'),
(16, '226534', 'Muñasque', 'Stanlee', 'Riveral', 'BSIT', 3, 'munasstanlee@gmail.com', 226534, '$2y$10$zT6N3QjciCKdk4fS.IkXneCbR4cJ9S88/G95OWnBRw7owFFFAmIVq', '2025-02-26 10:55:04'),
(17, '22653412', 'Muñasque', 'Stanlee', 'Riveral', 'BSIT', 3, 'munasquestan@gmail.com', 22653412, '$2y$10$pEnPj7umm/nD5T4cJPdYDOygAYI4jRgftQxgoO.coZem8Sd7X/rNC', '2025-02-26 11:07:01');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sit_in_sessions`
--
ALTER TABLE `sit_in_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idno` (`idno`,`username`),
  ADD UNIQUE KEY `emailadd` (`emailadd`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sit_in_sessions`
--
ALTER TABLE `sit_in_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `sit_in_sessions`
--
ALTER TABLE `sit_in_sessions`
  ADD CONSTRAINT `sit_in_sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
