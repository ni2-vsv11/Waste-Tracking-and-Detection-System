-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 26, 2025 at 05:46 AM
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
-- Database: `wms3`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `role` enum('user','admin') DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `phone`, `address`, `created_at`, `role`) VALUES
(1, 'Admin', 'admin@gmail.com', '$2y$10$8KgJE8xwRbzuPKLvD/jNkOYe.9bPWVW6jpqZ7Zt4OCeWUgEqw5k8.', NULL, NULL, '2025-03-25 18:37:16', 'admin'),
(2, 'abc', 'abc@gmail.com', '$2y$10$g/k.hA33swlyocSLS9Rk4uhPvFYRYHWpJWU90fKj8L9nrzekIIFp.', '1234567890', 'Nashik', '2025-03-25 18:39:10', 'user');

-- --------------------------------------------------------

--
-- Table structure for table `waste_reports`
--

CREATE TABLE `waste_reports` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `waste_type` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `location_lat` decimal(10,8) NOT NULL,
  `location_lng` decimal(11,8) NOT NULL,
  `address` text DEFAULT NULL,
  `status` enum('pending','in_progress','completed','rejected') DEFAULT 'pending',
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `waste_reports`
--

INSERT INTO `waste_reports` (`id`, `user_id`, `waste_type`, `description`, `location_lat`, `location_lng`, `address`, `status`, `image_url`, `created_at`, `updated_at`) VALUES
(1, 2, 'Household', 'HouseHold waste', 20.00821372, 73.80681753, 'Om Nagar, Nashik, Nashik Taluka, Nashik District, Maharashtra, 422001, India', 'completed', '../uploads/1742928096_4733064804491a41820be19fde1ce7b7.jpg', '2025-03-25 18:41:36', '2025-03-25 18:56:35'),
(2, 2, 'Medical', 'Medical waste', 20.00433439, 73.80370188, 'MGV\\\'s KBH Dental College &  Hospital, Agra Mumbai Road Flyover, Vijaynagar Colony, Nashik, Nashik Taluka, Nashik District, Maharashtra, 422001, India', 'in_progress', '../uploads/1742930000_Clinical-Waste-Bin-Collection-scaled.jpg', '2025-03-25 19:13:20', '2025-03-25 19:15:12');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `waste_reports`
--
ALTER TABLE `waste_reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `waste_reports`
--
ALTER TABLE `waste_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `waste_reports`
--
ALTER TABLE `waste_reports`
  ADD CONSTRAINT `waste_reports_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
