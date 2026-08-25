-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 13, 2026 at 08:45 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sellmyphone`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_user`
--

CREATE TABLE `admin_user` (
  `id` int(100) NOT NULL,
  `username` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `password` varchar(255) NOT NULL,
  `admin_email` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_user`
--

INSERT INTO `admin_user` (`id`, `username`, `password`, `admin_email`) VALUES
(1, 'admin', '$2y$10$PgrLHpPziuNwjw//Ev.ef.nf5aISqyEuAK8wi6t0JYnwdE6tQxTgm', 'admin@gmail.com'),
(6, 'saba maqbool', '$2y$10$3lWYgiyFv7E2s3zTlX/YZO.H1J/lufAxUwvLFoL5pLqeTefesWyzG', 'sabamaqbool@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `leads`
--

CREATE TABLE `leads` (
  `id` int(200) NOT NULL,
  `order_number` text NOT NULL,
  `name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `address` text NOT NULL,
  `brand` varchar(50) NOT NULL,
  `model` varchar(150) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `storage` varchar(20) NOT NULL,
  `price` varchar(11) NOT NULL DEFAULT '0',
  `status` enum('pending','contacted','completed') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `leads`
--

INSERT INTO `leads` (`id`, `order_number`, `name`, `phone`, `email`, `address`, `brand`, `model`, `image`, `storage`, `price`, `status`, `created_at`) VALUES
(1, 'ORD-2026-00001', 'Sara Ali', '+91 28390 23563', 'saraali@gmail.com', '21st floor, dubai', 'apple', 'iphone 15', 'imgs/model_6a7311d56548a.web', '256', 'AED 6789', 'pending', '2026-08-12 04:24:15');

--
-- Triggers `leads`
--
DELIMITER $$
CREATE TRIGGER `generate_order_number` BEFORE INSERT ON `leads` FOR EACH ROW BEGIN
    SET NEW.order_number = CONCAT(
        'ORD-',
        YEAR(CURDATE()),
        '-',
        LPAD(NEW.id, 5, '0')
    );
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `lead_images`
--

CREATE TABLE `lead_images` (
  `id` int(11) NOT NULL,
  `lead_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `models`
--

CREATE TABLE `models` (
  `id` int(200) NOT NULL,
  `brand` varchar(50) NOT NULL,
  `model_name` varchar(100) NOT NULL,
  `image` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `models`
--

INSERT INTO `models` (`id`, `brand`, `model_name`, `image`, `created_at`) VALUES
(3, 'Apple', 'iphone 13', 'imgs/model_6a731051807eb.webp', '2026-08-05 10:28:33'),
(7, 'Apple', 'iphone 13 Pro', 'imgs/model_6a731140b02db.jpg', '2026-08-05 10:32:32'),
(8, 'Apple', 'iphone 13 Pro Max', 'imgs/model_6a73114db381f.webp', '2026-08-05 10:32:45'),
(9, 'Apple', 'iphone 14', 'imgs/model_6a7311673bd95.webp', '2026-08-05 10:33:11'),
(10, 'Apple', 'iphone 14 Plus', 'imgs/model_6a73117e75698.webp', '2026-08-05 10:33:34'),
(11, 'Apple', 'iphone 14 Pro', 'imgs/model_6a7311908801b.webp', '2026-08-05 10:33:52'),
(12, 'Apple', 'iphone 14 Pro Max', 'imgs/model_6a73119e6e695.webp', '2026-08-05 10:34:06'),
(13, 'Apple', 'iphone 15', 'imgs/model_6a7311d56548a.webp', '2026-08-05 10:35:01'),
(14, 'Apple', 'iphone 15 Plus', 'imgs/model_6a7311ddc24c4.webp', '2026-08-05 10:35:09'),
(15, 'Apple', 'iphone 15 Pro', 'imgs/model_6a731215ed03c.webp', '2026-08-05 10:36:05'),
(16, 'Apple', 'iphone 15 Pro Max', 'imgs/model_6a73121d2d48e.webp', '2026-08-05 10:36:13'),
(17, 'Apple', 'iphone 16', 'imgs/model_6a73122889ef3.webp', '2026-08-05 10:36:24'),
(18, 'Apple', 'iphone 16 Plus', 'imgs/model_6a7312356fb8d.webp', '2026-08-05 10:36:37'),
(19, 'Apple', 'iphone 16 Pro', 'imgs/model_6a731258734e3.webp', '2026-08-05 10:37:12'),
(20, 'Apple', 'iphone 16 Pro Max', 'imgs/model_6a73126893f51.webp', '2026-08-05 10:37:28'),
(21, 'Samsung', 'Samsung S26', 'imgs/model_6a7312d30eb90.jpg', '2026-08-05 10:39:15'),
(23, 'Samsung', 'Samsung S26 Plus', 'imgs/model_6a7312f85fe05.jpg', '2026-08-05 10:39:52'),
(24, 'Samsung', 'Samsung S25 Ultra', 'imgs/model_6a73132a23911.webp', '2026-08-05 10:40:42'),
(25, 'Samsung', 'Samsung S25 Plus', 'imgs/model_6a73133e2cf44.jpg', '2026-08-05 10:41:02'),
(26, 'Samsung', 'Samsung S25 FE 5G', 'imgs/model_6a73135502bb4.jpg', '2026-08-05 10:41:25'),
(27, 'Samsung', 'Samsung S25 Edge', 'imgs/model_6a731371e8d5c.jpg', '2026-08-05 10:41:53'),
(28, 'Samsung', 'Samsung S24', 'imgs/model_6a73138051779.jpg', '2026-08-05 10:42:08'),
(29, 'Samsung', 'Samsung S24 Ultra', 'imgs/model_6a73138d714a7.jpg', '2026-08-05 10:42:21'),
(30, 'Samsung', 'Samsung S24 Plus', 'imgs/model_6a7313a6e552b.jpg', '2026-08-05 10:42:46'),
(31, 'Samsung', 'Samsung S23', 'imgs/model_6a7313b1c875f.jpg', '2026-08-05 10:42:57'),
(32, 'Samsung', 'Samsung S23 Plus', 'imgs/model_6a7313bec9d95.jpg', '2026-08-05 10:43:10'),
(33, 'Samsung', 'Samsung S23 Ultra', 'imgs/model_6a7313ccec402.jpg', '2026-08-05 10:43:24'),
(34, 'Samsung', 'Samsung S22 Ultra', 'imgs/model_6a7313e6f2344.webp', '2026-08-05 10:43:50'),
(35, 'Samsung', 'Samsung Galaxy A37', 'imgs/model_6a7313f9a722a.jpg', '2026-08-05 10:44:09'),
(39, 'Samsung', 'Samsung S26 Ultra', 'imgs/model_6a7c24eb63519.jpg', '2026-08-12 07:46:51'),
(40, 'Samsung', 'Samsung A57', 'imgs/model_6a7c2534ed765.jpg', '2026-08-12 07:48:04'),
(41, 'Apple', 'iphone 13 mini', 'imgs/model_6a7c25c70e85b.webp', '2026-08-12 07:50:31');

-- --------------------------------------------------------

--
-- Table structure for table `model_pricing`
--

CREATE TABLE `model_pricing` (
  `id` int(100) NOT NULL,
  `model_id` int(150) NOT NULL,
  `base` int(200) NOT NULL DEFAULT 0,
  `storage_128` int(50) NOT NULL DEFAULT 0,
  `storage_256` int(50) NOT NULL DEFAULT 0,
  `storage_512` int(50) NOT NULL DEFAULT 0,
  `condition_flawless` int(50) NOT NULL DEFAULT 0,
  `condition_good` int(50) NOT NULL DEFAULT 0,
  `condition_fair` int(100) NOT NULL DEFAULT 0,
  `acc_charger` int(100) NOT NULL DEFAULT 0,
  `acc_earbuds` int(50) NOT NULL DEFAULT 0,
  `acc_box` int(50) NOT NULL DEFAULT 0,
  `acc_warranty` int(50) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `model_pricing`
--

INSERT INTO `model_pricing` (`id`, `model_id`, `base`, `storage_128`, `storage_256`, `storage_512`, `condition_flawless`, `condition_good`, `condition_fair`, `acc_charger`, `acc_earbuds`, `acc_box`, `acc_warranty`) VALUES
(2, 34, 1800, 0, 150, 300, 0, -120, -250, 20, 10, 120, 10),
(3, 8, 2800, 0, 150, 300, 0, -100, -250, 30, 10, 150, 10),
(7, 28, 2400, 0, 180, 320, 0, -140, -290, 10, 10, 100, 10),
(10, 3, 1300, 0, 80, 150, 0, -150, -300, 10, 10, 80, 10),
(11, 41, 1200, 0, 60, 130, 0, -50, -100, 10, 10, 50, 10),
(13, 7, 2000, 0, 150, 250, 0, -100, -200, 10, 10, 100, 10),
(15, 9, 1800, 0, 150, 300, 0, -100, -200, 20, 10, 100, 10),
(17, 10, 2000, 0, 150, 250, 0, -150, -250, 10, 10, 100, 10),
(18, 11, 2500, 0, 200, 350, 0, -150, -300, 10, 10, 100, 10),
(19, 12, 3000, 0, 200, 350, 0, -150, -300, 30, 10, 150, 10),
(21, 13, 2800, 0, 200, 350, 0, -150, -300, 10, 10, 100, 10),
(22, 14, 3000, 0, 200, 350, 0, -150, -300, 10, 10, 100, 10),
(23, 15, 3400, 0, 200, 350, 0, -150, -300, 15, 10, 120, 10),
(24, 16, 3800, 0, 200, 350, 0, -150, -300, 30, 10, 150, 10),
(25, 17, 3200, 0, 200, 350, 0, -150, -300, 10, 10, 100, 10),
(26, 18, 3400, 0, 200, 350, 0, -150, -300, 10, 10, 100, 10),
(27, 19, 3800, 0, 200, 350, 0, -150, -300, 15, 10, 120, 10),
(28, 20, 4200, 0, 200, 350, 0, -150, -300, 30, 10, 150, 10),
(36, 35, 700, 0, 100, 200, 0, -80, -160, 5, 5, 40, 5),
(37, 40, 900, 0, 100, 200, 0, -100, -200, 5, 5, 50, 5),
(38, 31, 2000, 0, 150, 300, 0, -130, -270, 10, 10, 100, 10),
(39, 32, 2300, 0, 150, 300, 0, -130, -270, 15, 10, 120, 10),
(40, 33, 2700, 0, 150, 300, 0, -130, -270, 25, 10, 150, 10),
(41, 30, 2700, 0, 180, 320, 0, -140, -290, 15, 10, 120, 10),
(42, 29, 3200, 0, 180, 320, 0, -140, -290, 30, 10, 150, 10),
(43, 26, 2200, 0, 180, 320, 0, -140, -290, 10, 10, 100, 10),
(44, 27, 2900, 0, 200, 350, 0, -150, -300, 15, 10, 120, 10),
(45, 25, 3000, 0, 200, 350, 0, -150, -300, 15, 10, 120, 10),
(46, 24, 3600, 0, 200, 350, 0, -150, -300, 30, 10, 150, 10),
(47, 21, 2800, 0, 200, 350, 0, -150, -300, 10, 10, 100, 10),
(48, 23, 3300, 0, 200, 350, 0, -150, -300, 15, 10, 120, 10),
(49, 39, 4000, 0, 200, 350, 0, -150, -300, 30, 10, 150, 10);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_user`
--
ALTER TABLE `admin_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `leads`
--
ALTER TABLE `leads`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_number` (`order_number`) USING HASH;

--
-- Indexes for table `lead_images`
--
ALTER TABLE `lead_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lead_id` (`lead_id`);

--
-- Indexes for table `models`
--
ALTER TABLE `models`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `model_pricing`
--
ALTER TABLE `model_pricing`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `model_id` (`model_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_user`
--
ALTER TABLE `admin_user`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `leads`
--
ALTER TABLE `leads`
  MODIFY `id` int(200) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `lead_images`
--
ALTER TABLE `lead_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `models`
--
ALTER TABLE `models`
  MODIFY `id` int(200) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `model_pricing`
--
ALTER TABLE `model_pricing`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `lead_images`
--
ALTER TABLE `lead_images`
  ADD CONSTRAINT `lead_images_ibfk_1` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_pricing`
--
ALTER TABLE `model_pricing`
  ADD CONSTRAINT `model_pricing_ibfk_1` FOREIGN KEY (`model_id`) REFERENCES `models` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
