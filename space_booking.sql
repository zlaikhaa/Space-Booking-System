-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 25, 2025 at 05:31 PM
-- Server version: 10.1.25-MariaDB
-- PHP Version: 5.6.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `space_booking`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `user_id_number` varchar(50) DEFAULT NULL,
  `room` varchar(100) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `time` time DEFAULT NULL,
  `status` enum('Pending','Approved','Rejected') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_id_number`, `room`, `date`, `time`, `status`) VALUES
(2, 'L1001', 'Lab 1', '2025-06-28', '10:00:00', 'Rejected'),
(3, 'L1002', 'Lab 2', '2025-06-28', '14:00:00', 'Approved'),
(4, 'L1001', 'Bilik Mesyuarat', '2025-06-29', '09:00:00', 'Rejected'),
(5, 'L1002', 'Studio A', '2025-06-30', '16:00:00', 'Approved');

-- --------------------------------------------------------

--
-- Table structure for table `spaces`
--

CREATE TABLE `spaces` (
  `id` int(11) NOT NULL,
  `building` varchar(100) NOT NULL,
  `room_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `spaces`
--

INSERT INTO `spaces` (`id`, `building`, `room_name`) VALUES
(1, 'L50', 'Dewan Kuliah 1'),
(2, 'L50', 'Dewan Kuliah 2'),
(3, 'L50', 'Dewan Kuliah 3'),
(4, 'L50', 'Dewan Kuliah 4'),
(5, 'L50', 'Ruang Legar L50'),
(6, 'N24', 'Bilik Kuliah 1'),
(7, 'N24', 'Bilik Kuliah 2'),
(8, 'N24', 'Bilik Kuliah 3'),
(9, 'N24', 'Bilik Kuliah 4'),
(10, 'N24', 'Bilik Kuliah 5'),
(11, 'N24', 'Bilik Kuliah 6'),
(12, 'N24', 'Bilik Kuliah 7'),
(13, 'N24', 'Bilik Kuliah 8'),
(14, 'N24', 'Dewan Kuliah 1'),
(15, 'N24', 'Dewan Kuliah 2'),
(16, 'N24', 'Dewan Kuliah 3'),
(17, 'N24', 'Dewan Kuliah 4'),
(18, 'N24', 'Dewan Kuliah 5'),
(19, 'N24', 'Dewan Kuliah 6'),
(20, 'N24', 'Dewan Kuliah 7'),
(21, 'N24', 'Dewan Kuliah 8'),
(22, 'N24', 'Ruang Legar N24'),
(23, 'P19', 'Bilik Kuliah 1'),
(24, 'P19', 'Bilik Kuliah 2'),
(25, 'P19', 'Bilik Kuliah 3'),
(26, 'P19', 'Bilik Kuliah 4'),
(27, 'P19', 'Bilik Kuliah 5'),
(28, 'P19', 'Bilik Kuliah 6'),
(29, 'P19', 'Bilik Kuliah 7'),
(30, 'P19', 'Bilik Kuliah 8'),
(31, 'P19', 'Ruang Legar P19'),
(32, 'DSI', 'Bilik Canselor'),
(33, 'DSI', 'Bilik VVIP'),
(34, 'DSI', 'Bilik Jamuan'),
(35, 'DSI', 'Bilik Majlis'),
(36, 'DSI', 'Ruang Legar DSI'),
(37, 'DSI', 'Dataran Agora'),
(40, 'N28a', 'Lab 1'),
(41, 'N28a', 'Lab 2'),
(42, 'FAB', 'Bilik Mesyuarat'),
(43, 'P19', 'Studio A'),
(44, 'P19', 'Studio C'),
(86, 'P19', 'Lab C'),
(88, 'P19', 'Studio D');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_number` varchar(50) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','lecturer','manager') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_number`, `username`, `password`, `role`) VALUES
('A3001', 'Admin', 'admin123', 'admin'),
('L1001', 'Jas', '123456', 'lecturer'),
('L1002', 'Nik', '654321', 'lecturer'),
('M2001', 'Abdul', 'Abdul123', 'manager'),
('M2002', 'Ady', 'Ady321', 'manager');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id_number` (`user_id_number`);

--
-- Indexes for table `spaces`
--
ALTER TABLE `spaces`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_number`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `spaces`
--
ALTER TABLE `spaces`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id_number`) REFERENCES `users` (`id_number`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
