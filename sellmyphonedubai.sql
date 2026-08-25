-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 25, 2026 at 08:43 AM
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
-- Database: `sellmyphonedubai`
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
-- Table structure for table `blogs`
--

CREATE TABLE `blogs` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `excerpt` text NOT NULL,
  `content` longtext NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `author` varchar(100) DEFAULT NULL,
  `status` enum('draft','published') NOT NULL DEFAULT 'published',
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` varchar(500) DEFAULT NULL,
  `meta_keywords` varchar(500) DEFAULT NULL,
  `meta_robots` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `blogs`
--

INSERT INTO `blogs` (`id`, `title`, `slug`, `excerpt`, `content`, `image`, `category`, `author`, `status`, `meta_title`, `meta_description`, `meta_keywords`, `meta_robots`, `created_at`) VALUES
(1, 'How to Sell Your iPhone in Dubai for the Best Price', 'how-to-sell-your-iphone-in-dubai-for-the-best-price', 'Looking to sell your iPhone in Dubai? Follow these simple tips to get the best possible price for your device.', 'Selling your iPhone doesn\'t have to be complicated. Before selling, make sure you back up your important data, sign out of your Apple ID, and remove your personal information. Check the phone\'s physical condition and collect any original accessories you still have.\r\n\r\nAt SellMyPhoneDubai, you can get a quick evaluation and a competitive price for your device. Our convenient pickup service makes the process simple and hassle-free.', 'imgs/blog_6a856071333c5.png', 'Selling Guide', 'Saba Maqbool', 'published', NULL, NULL, NULL, NULL, '2026-08-19 07:51:13'),
(2, '5 Signs It\'s Time to Sell Your Old Phone (Before It Loses More Value)', '5-signs-it-s-time-to-sell-your-old-phone-before-it-loses-more-value', 'Holding onto your old phone longer than you should? Here are 5 clear signs it\'s time to sell — and how to get the best price for it in Dubai before its value drops further.', '<p>Every phone loses value the moment a newer model launches — but most people wait far too long to sell, watching hundreds of dirhams slip away in the process. If you\'re wondering whether now is the right time to sell your phone, here are five signs you shouldn\'t ignore.</p>\r\n\r\n<h2>1. A New Model Just Launched</h2>\r\n<p>Resale prices drop the fastest in the weeks right after a new flagship is announced. If Apple or Samsung just unveiled their latest device, your current phone\'s value is already sliding — the sooner you sell, the more you\'ll get.</p>\r\n\r\n<h2>2. Your Battery Health Is Below 85%</h2>\r\n<p>Buyers and resellers check battery health closely. Once it drops below 85%, offers start dropping too. If your battery is already showing signs of wear, it\'s smarter to sell now rather than wait for it to degrade further.</p>\r\n\r\n<h2>3. You\'re Eyeing an Upgrade</h2>\r\n<p>If you\'re already saving up for the newest iPhone or Galaxy, selling your current phone early means you can put that cash straight toward your upgrade — instead of letting an idle device gather dust in a drawer.</p>\r\n\r\n<h2>4. The Screen or Body Has Visible Damage</h2>\r\n<p>Cracks, scratches, and dents only get worse with time, and so does the price a buyer will offer. A phone in \"good\" condition today could slip into \"fair\" or \"poor\" condition in just a few more months of daily use.</p>\r\n\r\n<h2>5. It\'s Just Sitting Unused</h2>\r\n<p>An old phone sitting in a drawer isn\'t earning you anything — it\'s losing value every single day. Turning it into cash now is almost always better than holding onto it \"just in case.\"</p>\r\n\r\n<h2>Get the Best Price, Hassle-Free</h2>\r\n<p>At SellMyPhoneDubai, we make selling simple: get an instant quote, book a free pickup anywhere in Dubai, and get paid on the spot — no waiting, no hidden fees. Don\'t let your phone lose more value than it already has.</p>', 'imgs/blog_6a8c29bf1c673.png', 'Selling Tips', 'SellMyPhoneDubai Team', 'published', '', '', '', NULL, '2026-08-24 06:24:28');

-- --------------------------------------------------------

--
-- Table structure for table `contact_info`
--

CREATE TABLE `contact_info` (
  `id` int(11) NOT NULL,
  `phone` varchar(50) NOT NULL DEFAULT '',
  `email` varchar(150) NOT NULL DEFAULT '',
  `address` text NOT NULL,
  `hours_weekday` varchar(100) NOT NULL DEFAULT '',
  `hours_weekend` varchar(100) NOT NULL DEFAULT '',
  `facebook` varchar(255) NOT NULL DEFAULT '#',
  `instagram` varchar(255) NOT NULL DEFAULT '#',
  `twitter` varchar(255) NOT NULL DEFAULT '#',
  `linkedin` varchar(255) NOT NULL DEFAULT '#',
  `whatsapp` varchar(255) NOT NULL DEFAULT '#'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_info`
--

INSERT INTO `contact_info` (`id`, `phone`, `email`, `address`, `hours_weekday`, `hours_weekend`, `facebook`, `instagram`, `twitter`, `linkedin`, `whatsapp`) VALUES
(1, '+971502166562‬', 'info@sellmyphonedubai.com', 'Al Quoz 3rd, Showroom No 33, Dubai,\r\nSheikh Zayed Road, Dubai', 'Sun - Thu: 9AM - 10PM', 'Fri - Sat: 10AM - 8PM', 'https://facebook.com/sellmyphonedubai', 'https://instagram.com/sellmyphonedubai', 'https://twitter.com/sellmyphonedubai', 'https://linkedin.com/company/sellmyphonedubai', 'https://wa.me/971505556779');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(30) NOT NULL DEFAULT '',
  `subject` varchar(200) NOT NULL DEFAULT '',
  `message` text NOT NULL,
  `status` enum('unread','read','replied') NOT NULL DEFAULT 'unread',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `home_sections`
--

CREATE TABLE `home_sections` (
  `id` int(11) NOT NULL,
  `section_key` varchar(50) NOT NULL,
  `section_name` varchar(100) NOT NULL,
  `kicker` varchar(150) DEFAULT NULL,
  `heading` varchar(255) DEFAULT NULL,
  `heading_highlight` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `button_text` varchar(100) DEFAULT NULL,
  `button_link` varchar(255) DEFAULT NULL,
  `extra_1` varchar(255) DEFAULT NULL,
  `extra_2` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `home_sections`
--

INSERT INTO `home_sections` (`id`, `section_key`, `section_name`, `kicker`, `heading`, `heading_highlight`, `description`, `image`, `button_text`, `button_link`, `extra_1`, `extra_2`, `is_active`, `updated_at`) VALUES
(1, 'hero', 'Hero Section', 'The smarter way to', 'Sell your phone', 'in Dubai', 'Get the best price for your used iPhone, Samsung and more. Quick, secure & trusted.', 'imgs/heroo.png', NULL, NULL, '4.9/5', 'Based on 2,500+ reviews', 1, '2026-08-19 06:07:58'),
(2, 'brand', 'Brand / Get Quote Section', 'GET YOUR QUOTE', 'Which <span>brand</span> is your phone', NULL, 'We buy all models in any condition. Get the best price guaranteed!', NULL, NULL, NULL, NULL, NULL, 1, '2026-08-19 06:15:22'),
(3, 'process', 'How It Works (Process)', 'PROCESS', 'Sell Your Phone in <span>3</span> Easy Steps', '', 'Our simple and transparent process makes selling your phone quick, safe, and profitable', NULL, NULL, NULL, '', '', 0, '2026-08-19 06:33:44'),
(4, 'chooseus', 'Why Choose Us', 'BENEFITS', 'Why Choose <span>SellMyPhoneDubai</span>', '', 'Why choose SellMyPhoneDubai for your phone selling needs?', 'imgs/hero_6a85510010600.png', NULL, NULL, '4.9/5', 'Based on 2,500+ reviews', 0, '2026-08-19 06:45:20'),
(5, 'testimonials', 'Testimonials', 'TESTIMONIALS', 'What Our Customers Say', NULL, 'Trusted by Thousands of Satisfied Customers Across Dubai', NULL, NULL, NULL, NULL, NULL, 0, '2026-08-19 07:00:06'),
(6, 'quicklink', 'Quick Links', 'QUICK LINKS', 'Quick Access', NULL, 'Find what you need quickly with our helpful resources', NULL, NULL, NULL, NULL, NULL, 0, '2026-08-19 07:03:23'),
(7, 'about_con', 'About / CTA Section', 'WE COME TO YOU', 'Ready to Sell Your Phone with <br> Dubai\'s', 'Most Trusted Buyer?', 'Your phone loses value every day. Get the best cash price in Dubai now with our fast, safe, and easy service. We offer free pickup and instant payment right at your doorstep.', 'imgs/hero.webp', NULL, NULL, 'Ready to Sell Your Phone?', 'Get an instant quote now and sell your phone in minutes.', 0, '2026-08-19 07:15:03'),
(8, 'about_story', '', 'OUR STORY', 'Making it Easy to Sell Your', 'Phone in Dubai', 'Since 2020, we\'ve been helping you sell your used phones in Dubai without the stress. We\'re here to make sure you get a fair price, quickly and easily.', NULL, NULL, NULL, NULL, NULL, 1, '2026-08-19 10:09:22'),
(9, 'about_mission', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-08-19 10:01:39');

-- --------------------------------------------------------

--
-- Table structure for table `home_section_items`
--

CREATE TABLE `home_section_items` (
  `id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `icon` varchar(100) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `title` varchar(150) DEFAULT NULL,
  `subtitle` varchar(150) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `home_section_items`
--

INSERT INTO `home_section_items` (`id`, `section_id`, `icon`, `image`, `title`, `subtitle`, `content`, `description`, `link`, `sort_order`) VALUES
(1, 1, 'fa-solid fa-hand-holding-dollar', NULL, 'Best Prices', 'Guaranteed', NULL, NULL, NULL, 1),
(2, 1, 'fa-solid fa-truck-fast', NULL, 'Free Pickup', 'Across Dubai', NULL, NULL, NULL, 2),
(3, 1, 'fa-solid fa-shield-halved', NULL, '100% Secure', '& Safe', NULL, NULL, NULL, 3),
(4, 2, 'imgs/apple-icon.png', 'imgs/apple-card.png', 'Apple', 'iPhone', '', NULL, NULL, 1),
(5, 2, 'imgs/samsung.png', 'imgs/samsung-card.png', 'Samsung', 'Galaxy', '', NULL, NULL, 2),
(6, 3, 'fa-solid fa-mobile-screen-button', NULL, 'Get Instant Quote', 'Tell us about your phone brand, model, condition, and specifications. Get an instant estimated price within seconds.', NULL, NULL, NULL, 1),
(7, 3, 'fa-solid fa-truck-fast', NULL, 'Schedule Free Pickup', 'Choose your preferred time slot. Our expert professional buyer visits at your doorstep anywhere in Dubai.', NULL, NULL, NULL, 2),
(8, 3, 'fa-solid fa-hand-holding-dollar', NULL, 'Get Instant Cash', 'After quick inspection, receive instant cash payment right at your doorstep. No delays no hassles.', NULL, NULL, NULL, 3),
(10, 4, 'fas fa-award', NULL, 'Best Price Guarantee', 'Get the best price for your phone. We offer competitive prices and transparent pricing.', NULL, NULL, NULL, 1),
(11, 4, 'fas fa-shield-alt', NULL, '100% Secure & Safe', 'Get easy with our secure and safe transaction process. Your information is protected.', NULL, NULL, NULL, 2),
(12, 4, 'fas fa-clock', NULL, 'Quick & Efficient', 'Get your phone sold quickly and efficiently. We offer fast and reliable service.', NULL, NULL, NULL, 3),
(13, 4, 'fas fa-headset', NULL, '24/7 Support', 'Contact us anytime. Our team is available 24/7 to answer your questions and ready to assist you.', NULL, NULL, NULL, 4),
(14, 5, '5', NULL, 'Fatima Al Zaabi', 'Dubai Marina, Dubai', 'Sold my Samsung S24 Ultra here and got paid on the spot. Free pickup came right to my building in Marina, super easy process.', NULL, NULL, 1),
(15, 5, '5', NULL, 'Rahul Kapoor', 'JLT, Dubai', 'Compared prices with two other buyers in JLT and this was by far the best offer. Quick pickup, instant cash, no hassle at all.', NULL, NULL, 2),
(16, 5, '4.5', NULL, 'Omar Al Farsi', 'Downtown Dubai', 'Sold two old iPhones from my family in one visit. Clear pricing, no haggling, and cash paid right away. Would recommend to anyone in Downtown.', NULL, NULL, 3),
(17, 6, 'fas fa-question-circle', NULL, 'FAQ', 'Find answers to common questions about selling your phone with us.', 'View FAQ', NULL, NULL, 1),
(18, 6, 'fas fa-file-alt', NULL, 'Price Guide', 'Check our comprehensive price guide for all phone models and brands.', 'View Prices', NULL, NULL, 2),
(19, 6, 'fas fa-map-marker-alt', NULL, 'Service Areas', 'Check if we service your area in Dubai. We cover all major locations.', 'View Areas', NULL, NULL, 3),
(20, 6, 'fas fa-phone-alt', NULL, 'Contact Us', 'Get in touch with our customer support team for any assistance.', 'Contact Now', NULL, NULL, 4),
(21, 7, 'fa-solid fa-truck-fast', NULL, 'Same Day Pickup', NULL, NULL, NULL, NULL, 1),
(22, 7, 'fa-solid fa-shield-halved', NULL, 'Safe & Contactless', NULL, NULL, NULL, NULL, 2),
(23, 7, 'fa-solid fa-calendar-days', NULL, 'Available 7 Days a Week', NULL, NULL, NULL, NULL, 3),
(25, 8, NULL, NULL, '25,000+', 'Phones Purchased.', NULL, NULL, NULL, 1),
(26, 8, NULL, NULL, '98%', 'Customer Satisfaction.', NULL, NULL, NULL, 2),
(27, 8, NULL, NULL, '30 min', 'Average Service Time', NULL, NULL, NULL, 3),
(28, 9, 'fa-solid fa-circle-check', NULL, 'Our Mission', NULL, 'To give people in Dubai the fastest and easiest way to sell their phones for the best price. We make upgrading your technology simple and fair for everyone.', NULL, NULL, 1),
(29, 9, 'fa-solid fa-eye', NULL, 'Our Vision', NULL, 'To be the best place in Dubai to sell your phone. We promise to give everyone a great experience and the best price.', NULL, NULL, 2),
(30, 9, 'fa-solid fa-handshake', NULL, 'Our Values', NULL, 'We believe in being honest, doing the right thing, and always putting you first. We work hard to find new ways to help and care for our community.', NULL, NULL, 3);

-- --------------------------------------------------------

--
-- Table structure for table `leads`
--

CREATE TABLE `leads` (
  `id` int(200) NOT NULL,
  `order_number` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `address` text NOT NULL,
  `brand` varchar(50) NOT NULL,
  `model` varchar(150) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `storage` varchar(20) NOT NULL,
  `price` varchar(11) NOT NULL DEFAULT '0',
  `status` enum('pending','contacted','completed','cancelled') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `leads`
--

INSERT INTO `leads` (`id`, `order_number`, `name`, `phone`, `email`, `address`, `brand`, `model`, `image`, `storage`, `price`, `status`, `created_at`) VALUES
(5, 'ORD-2026-00005', 'ayesha fatima', '03938738933', 'ayesha@gmail.com', 'address multan', 'Apple', 'iphone 15', 'imgs/model_6a7311d56548a.webp', 'storage_512', '3260', 'completed', '2026-08-13 08:05:07');

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

--
-- Dumping data for table `lead_images`
--

INSERT INTO `lead_images` (`id`, `lead_id`, `image_path`) VALUES
(1, 5, 'imgs/lead_6a7d7ab30efe0.jpg');

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
(42, 'Apple', 'iphone 17', 'imgs/model_6a87de99d65f8.webp', '2026-08-21 05:14:01'),
(43, 'Apple', 'iphone 17 air', 'imgs/model_6a87debd479f8.jpg', '2026-08-21 05:14:37'),
(44, 'Apple', 'iphone 17 e', 'imgs/model_6a87df12a88e4.webp', '2026-08-21 05:16:02'),
(45, 'Apple', 'iphone 17 pro', 'imgs/model_6a87df343d698.webp', '2026-08-21 05:16:36'),
(46, 'Apple', 'iphone 17 Pro Max', 'imgs/model_6a87df553ec3b.webp', '2026-08-21 05:17:09'),
(47, 'Samsung', 'Samsung A56', 'imgs/model_6a87ff7d54a38.webp', '2026-08-21 07:34:21'),
(48, 'Samsung', 'Samsung A55', 'imgs/model_6a88000d8957a.webp', '2026-08-21 07:36:45'),
(49, 'Samsung', 'Samsung A54', 'imgs/model_6a880081d7761.webp', '2026-08-21 07:38:41'),
(50, 'Samsung', 'Samsung A53', 'imgs/model_6a8800921ad42.webp', '2026-08-21 07:38:58'),
(51, 'Samsung', 'Samsung A36', 'imgs/model_6a88028a2fe46.webp', '2026-08-21 07:47:22'),
(52, 'Samsung', 'Samsung A35', 'imgs/model_6a8802aa10043.webp', '2026-08-21 07:47:54'),
(53, 'Samsung', 'Samsung A34', 'imgs/model_6a8802c2acfac.webp', '2026-08-21 07:48:18'),
(55, 'Samsung', 'Samsung Z fold 4', 'imgs/model_6a8814116eba9.webp', '2026-08-21 09:02:09'),
(56, 'Samsung', 'Samsung Z fold 5', 'imgs/model_6a88142f1359d.webp', '2026-08-21 09:02:39'),
(57, 'Samsung', 'Samsung Z fold 6', 'imgs/model_6a8814419b857.webp', '2026-08-21 09:02:57'),
(58, 'Samsung', 'Samsung Z fold 7', 'imgs/model_6a88148377b64.webp', '2026-08-21 09:04:03'),
(59, 'Samsung', 'Samsung Z Flip 4', 'imgs/model_6a88192484dd6.webp', '2026-08-21 09:23:48'),
(60, 'Samsung', 'Samsung Z Flip 5', 'imgs/model_6a88193ee8ba9.webp', '2026-08-21 09:24:14'),
(61, 'Samsung', 'Samsung Z Flip 6', 'imgs/model_6a881953b4be1.webp', '2026-08-21 09:24:35'),
(62, 'Samsung', 'Samsung Z Flip 7', 'imgs/model_6a8819764ff2b.webp', '2026-08-21 09:25:10'),
(63, 'Samsung', 'Samsung S21', 'imgs/model_6a8819a25e41f.webp', '2026-08-21 09:25:54'),
(64, 'Samsung', 'Samsung S21 Plus', 'imgs/model_6a8819d2a782c.webp', '2026-08-21 09:26:42'),
(65, 'Samsung', 'Samsung S21 Ultra', 'imgs/model_6a8819ee93294.webp', '2026-08-21 09:27:10'),
(66, 'Samsung', 'Samsung S21 FE', 'imgs/model_6a881a02afd16.webp', '2026-08-21 09:27:30'),
(67, 'Samsung', 'Samsung S22', 'imgs/model_6a881a3c0b784.webp', '2026-08-21 09:28:28'),
(68, 'Samsung', 'Samsung S22 Plus', 'imgs/model_6a881a5f1e15a.webp', '2026-08-21 09:29:03'),
(71, 'Samsung', 'Samsung S22 Ultra', 'imgs/model_6a881b01f2c00.webp', '2026-08-21 09:31:45'),
(72, 'Samsung', 'Samsung S22 FE', 'imgs/model_6a881b189f750.webp', '2026-08-21 09:32:08'),
(73, 'Samsung', 'Samsung S23', 'imgs/model_6a881b2d2127d.jpg', '2026-08-21 09:32:29'),
(74, 'Samsung', 'Samsung S23 Plus', 'imgs/model_6a881b3c82ec0.jpg', '2026-08-21 09:32:44'),
(75, 'Samsung', 'Samsung S23 Ultra', 'imgs/model_6a881b48e4b44.jpg', '2026-08-21 09:32:56'),
(76, 'Samsung', 'Samsung S23 FE', 'imgs/model_6a881b661b986.webp', '2026-08-21 09:33:26'),
(77, 'Samsung', 'Samsung S24', 'imgs/model_6a881b7820df0.jpg', '2026-08-21 09:33:44'),
(78, 'Samsung', 'Samsung S24 Plus', 'imgs/model_6a881b86b349f.jpg', '2026-08-21 09:33:58'),
(79, 'Samsung', 'Samsung S24 Ultra', 'imgs/model_6a881b960f3f7.jpg', '2026-08-21 09:34:14'),
(80, 'Samsung', 'Samsung S24 FE', 'imgs/model_6a881bac0c9b6.webp', '2026-08-21 09:34:36'),
(81, 'Samsung', 'Samsung S25', 'imgs/model_6a881bc36e012.webp', '2026-08-21 09:34:59'),
(82, 'Samsung', 'Samsung S25 Plus', 'imgs/model_6a881bdd15b0b.jpg', '2026-08-21 09:35:25'),
(83, 'Samsung', 'Samsung S25 Ultra', 'imgs/model_6a881bf976ebd.webp', '2026-08-21 09:35:53'),
(84, 'Samsung', 'Samsung S25 FE', 'imgs/model_6a881c0f37cdf.jpg', '2026-08-21 09:36:15'),
(85, 'Samsung', 'Samsung S26', 'imgs/model_6a881cb5ce037.jpg', '2026-08-21 09:39:01'),
(86, 'Samsung', 'Samsung S26 Plus', 'imgs/model_6a881cc8d2fce.jpg', '2026-08-21 09:39:20'),
(87, 'Samsung', 'Samsung S26 Ultra', 'imgs/model_6a881cd9ef751.jpg', '2026-08-21 09:39:37');

-- --------------------------------------------------------

--
-- Table structure for table `model_pricing`
--

CREATE TABLE `model_pricing` (
  `id` int(100) NOT NULL,
  `model_id` int(150) NOT NULL,
  `base` int(200) NOT NULL DEFAULT 0,
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

INSERT INTO `model_pricing` (`id`, `model_id`, `base`, `condition_flawless`, `condition_good`, `condition_fair`, `acc_charger`, `acc_earbuds`, `acc_box`, `acc_warranty`) VALUES
(3, 8, 960, 0, -130, -250, 30, 10, 200, 10),
(10, 3, 540, 0, -100, -200, 10, 10, 100, 10),
(13, 7, 830, 0, -120, -250, 10, 10, 150, 10),
(15, 9, 640, 0, -200, -350, 10, 10, 100, 10),
(17, 10, 700, 0, -200, -400, 10, 10, 100, 10),
(18, 11, 1010, 0, -300, -550, 10, 10, 150, 10),
(19, 12, 1160, 0, -320, -700, 10, 10, 200, 10),
(21, 13, 910, 0, -250, -450, 10, 10, 100, 10),
(22, 14, 1030, 0, -300, -550, 10, 10, 100, 10),
(23, 15, 1480, 0, -500, -850, 10, 10, 150, 10),
(24, 16, 1670, 0, -550, -900, 10, 10, 200, 10),
(25, 17, 1300, 0, -400, -750, 10, 10, 100, 10),
(26, 18, 1510, 0, -480, -880, 10, 10, 100, 10),
(27, 19, 1780, 0, -600, -1100, 10, 10, 200, 10),
(28, 20, 2230, 0, -700, -1260, 10, 10, 200, 10),
(79, 42, 1820, 0, -600, -1130, 10, 10, 100, 10),
(80, 43, 1960, 0, -600, -1150, 10, 10, 100, 10),
(83, 44, 1100, 0, -150, -300, 10, 10, 100, 10),
(85, 45, 2750, 0, -800, -1700, 10, 10, 150, 10),
(86, 46, 3020, 0, -1000, -2000, 10, 10, 200, 10),
(87, 53, 190, 0, -57, -114, 10, 10, 100, 10),
(88, 52, 230, 0, -69, -138, 10, 10, 100, 10),
(90, 51, 380, 0, -114, -228, 10, 10, 100, 10),
(91, 50, 260, 0, -78, -156, 10, 10, 100, 10),
(95, 49, 280, 0, -84, -168, 10, 10, 100, 10),
(96, 48, 300, 0, -90, -180, 10, 10, 100, 10),
(97, 47, 410, 0, -123, -246, 10, 10, 100, 10),
(98, 63, 260, 0, -78, -156, 10, 10, 100, 10),
(100, 66, 310, 0, -93, -186, 10, 10, 100, 10),
(101, 64, 320, 0, -96, -192, 10, 10, 100, 10),
(104, 65, 400, 0, -120, -240, 10, 10, 100, 10),
(105, 67, 380, 0, -114, -228, 10, 10, 100, 10),
(106, 72, 350, 0, -70, -150, 10, 10, 100, 10),
(107, 68, 520, 0, -156, -312, 10, 10, 100, 10),
(108, 71, 680, 0, -204, -408, 10, 10, 100, 10),
(109, 73, 540, 0, -162, -324, 10, 10, 100, 10),
(110, 76, 430, 0, -129, -258, 10, 10, 100, 10),
(113, 74, 750, 0, -225, -450, 10, 10, 100, 10),
(115, 75, 1160, 0, -348, -696, 10, 10, 100, 10),
(116, 77, 750, 0, -225, -450, 10, 10, 100, 10),
(117, 80, 590, 0, -177, -354, 10, 10, 100, 10),
(118, 78, 1050, 0, -315, -630, 10, 10, 100, 10),
(119, 79, 1280, 0, -384, -768, 10, 10, 100, 10),
(120, 81, 930, 0, -279, -558, 10, 10, 100, 10),
(122, 84, 740, 0, -222, -444, 10, 10, 100, 10),
(123, 82, 1220, 0, -366, -732, 10, 10, 100, 10),
(124, 83, 1850, 0, -555, -1110, 10, 10, 200, 10),
(125, 85, 1360, 0, -408, -816, 10, 10, 100, 10),
(126, 86, 1820, 0, -546, -1092, 10, 10, 100, 10),
(127, 87, 2180, 0, -654, -1308, 10, 10, 200, 10),
(129, 59, 330, 0, -99, -198, 10, 10, 100, 10),
(131, 55, 640, 0, -192, -384, 10, 10, 100, 10),
(133, 56, 1030, 0, -309, -618, 10, 10, 100, 10),
(134, 57, 1560, 0, -468, -936, 10, 10, 200, 10),
(135, 58, 3040, 0, -912, -1824, 10, 10, 200, 10),
(138, 60, 450, 0, -135, -270, 10, 10, 100, 10),
(139, 61, 810, 0, -243, -486, 10, 10, 100, 10),
(140, 62, 1300, 0, -390, -780, 10, 10, 100, 10);

-- --------------------------------------------------------

--
-- Table structure for table `model_storage_options`
--

CREATE TABLE `model_storage_options` (
  `id` int(11) NOT NULL,
  `model_id` int(11) NOT NULL,
  `label` varchar(50) NOT NULL,
  `price_delta` decimal(10,2) NOT NULL DEFAULT 0.00,
  `sort_order` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `model_storage_options`
--

INSERT INTO `model_storage_options` (`id`, `model_id`, `label`, `price_delta`, `sort_order`) VALUES
(237, 3, '128 GB', 0.00, 0),
(238, 3, '256 GB', 120.00, 1),
(239, 3, '512 GB', 200.00, 2),
(244, 8, '128 GB', 0.00, 0),
(245, 8, '256 GB', 80.00, 1),
(246, 8, '512 GB', 200.00, 2),
(247, 8, '1 TB', 320.00, 3),
(248, 9, '128 GB', 0.00, 0),
(249, 9, '256 GB', 80.00, 1),
(250, 9, '512 GB', 190.00, 2),
(251, 10, '128 GB', 0.00, 0),
(252, 10, '256 GB', 90.00, 1),
(253, 10, '512 GB', 180.00, 2),
(254, 11, '128 GB', 0.00, 0),
(255, 11, '256 GB', 100.00, 1),
(256, 11, '512 GB', 190.00, 2),
(257, 11, '1 TB', 260.00, 3),
(258, 12, '128 GB', 0.00, 0),
(259, 12, '256 GB', 100.00, 1),
(260, 12, '512 GB', 250.00, 2),
(261, 12, '1 TB', 480.00, 3),
(262, 13, '128 GB', 0.00, 0),
(263, 13, '256 GB', 100.00, 1),
(264, 13, '512 GB', 170.00, 2),
(265, 14, '128 GB', 0.00, 0),
(266, 14, '256 GB', 100.00, 1),
(267, 14, '512 GB', 210.00, 2),
(268, 15, '128 GB', 0.00, 0),
(269, 15, '256 GB', 120.00, 1),
(270, 15, '512 GB', 210.00, 2),
(271, 15, '1 TB', 400.00, 3),
(272, 16, '256 GB', 0.00, 0),
(273, 16, '512 GB', 150.00, 1),
(274, 16, '1 TB', 280.00, 2),
(275, 17, '128 GB', 0.00, 0),
(276, 17, '256 GB', 140.00, 1),
(277, 17, '512 GB', 320.00, 2),
(278, 18, '128 GB', 0.00, 0),
(279, 18, '256 GB', 170.00, 1),
(280, 18, '512 GB', 250.00, 2),
(285, 19, '128 GB', 0.00, 0),
(286, 19, '256 GB', 200.00, 1),
(287, 19, '512 GB', 500.00, 2),
(288, 19, '1 TB', 720.00, 3),
(289, 20, '256 GB', 0.00, 0),
(290, 20, '512 GB', 250.00, 1),
(291, 20, '1 TB', 430.00, 2),
(296, 42, '256 GB', 0.00, 0),
(297, 42, '512 GB', 570.00, 1),
(298, 43, '256 GB', 0.00, 0),
(299, 43, '512 GB', 250.00, 1),
(300, 43, '1 TB', 480.00, 2),
(302, 44, '256 GB', 0.00, 0),
(303, 45, '256 GB', 0.00, 0),
(304, 45, '512 GB', 400.00, 1),
(305, 45, '1 TB', 850.00, 2),
(306, 46, '256 GB', 0.00, 0),
(307, 46, '512 GB', 540.00, 1),
(308, 46, '1 TB', 1110.00, 2),
(309, 46, '2 TB', 1410.00, 3),
(318, 50, '128 GB', 0.00, 0),
(319, 50, '256 GB', 50.00, 1),
(320, 53, '128 GB', 0.00, 0),
(321, 53, '256 GB', 50.00, 1),
(322, 52, '128 GB', 0.00, 0),
(323, 52, '256 GB', 40.00, 1),
(324, 51, '128 GB', 0.00, 0),
(325, 51, '256 GB', 50.00, 1),
(326, 49, '128 GB', 0.00, 0),
(327, 49, '256 GB', 40.00, 1),
(328, 48, '128 GB', 0.00, 0),
(329, 48, '256 GB', 70.00, 1),
(330, 47, '128 GB', 0.00, 0),
(331, 47, '256 GB', 90.00, 1),
(333, 63, '128 GB', 0.00, 0),
(334, 63, '256 GB', 30.00, 1),
(338, 66, '128 GB', 0.00, 0),
(339, 66, '256 GB', 30.00, 1),
(340, 64, '128 GB', 0.00, 0),
(341, 64, '256 GB', 70.00, 1),
(342, 65, '128 GB', 0.00, 0),
(343, 65, '256 GB', 40.00, 1),
(344, 65, '512 GB', 100.00, 2),
(345, 67, '128 GB', 0.00, 0),
(346, 67, '256 GB', 50.00, 1),
(349, 68, '128 GB', 0.00, 0),
(350, 68, '256 GB', 40.00, 1),
(351, 71, '128 GB', 0.00, 0),
(352, 71, '256 GB', 90.00, 1),
(353, 71, '512 GB', 220.00, 2),
(354, 71, '1 TB', 300.00, 3),
(355, 73, '128 GB', 0.00, 0),
(356, 73, '256 GB', 70.00, 1),
(359, 76, '128 GB', 0.00, 0),
(360, 76, '256 GB', 100.00, 1),
(361, 72, '128 GB', 0.00, 0),
(362, 72, '256 GB', 40.00, 1),
(363, 74, '128 GB', 0.00, 0),
(364, 75, '256 GB', 0.00, 0),
(365, 75, '1 TB', 120.00, 1),
(366, 75, '2 TB', 210.00, 2),
(367, 77, '128 GB', 0.00, 0),
(368, 77, '256 GB', 60.00, 1),
(369, 80, '128 GB', 0.00, 0),
(370, 80, '256 GB', 60.00, 1),
(371, 78, '256 GB', 0.00, 0),
(372, 78, '1 TB', 80.00, 1),
(373, 79, '256 GB', 0.00, 0),
(374, 79, '1 TB', 100.00, 1),
(375, 79, '2 TB', 250.00, 2),
(378, 81, '128 GB', 0.00, 0),
(379, 81, '256 GB', 100.00, 1),
(380, 81, '512 GB', 260.00, 2),
(381, 84, '128 GB', 0.00, 0),
(382, 84, '256 GB', 70.00, 1),
(383, 82, '128 GB', 0.00, 0),
(384, 82, '256 GB', 170.00, 1),
(385, 82, '512 GB', 370.00, 2),
(389, 85, '256 GB', 0.00, 0),
(390, 85, '512 GB', 250.00, 1),
(391, 86, '256 GB', 0.00, 0),
(392, 86, '512 GB', 100.00, 1),
(408, 55, '256 GB', 0.00, 0),
(409, 55, '512 GB', 70.00, 1),
(410, 55, '1 TB', 120.00, 2),
(411, 56, '256 GB', 0.00, 0),
(412, 56, '512 GB', 100.00, 1),
(422, 59, '128 GB', 0.00, 0),
(423, 59, '256 GB', 30.00, 1),
(424, 59, '512 GB', 50.00, 2),
(425, 60, '256 GB', 0.00, 0),
(426, 60, '512 GB', 80.00, 1),
(427, 61, '256 GB', 0.00, 0),
(428, 61, '512 GB', 80.00, 1),
(429, 62, '256 GB', 0.00, 0),
(430, 62, '512 GB', 330.00, 1),
(431, 58, '256 GB', 0.00, 0),
(432, 58, '512 GB', 350.00, 1),
(433, 58, '1 TB', 760.00, 2),
(434, 57, '256 GB', 0.00, 0),
(435, 57, '512 GB', 200.00, 1),
(436, 57, '1 TB', 420.00, 2),
(437, 87, '256 GB', 0.00, 0),
(438, 87, '512 GB', 480.00, 1),
(439, 87, '1 TB', 700.00, 2),
(440, 83, '256 GB', 0.00, 0),
(441, 83, '512 GB', 180.00, 1),
(442, 83, '1 TB', 350.00, 2),
(443, 7, '128 GB', 0.00, 0),
(444, 7, '256 GB', 80.00, 1),
(445, 7, '512 GB', 190.00, 2),
(446, 7, '1 TB', 320.00, 3);

-- --------------------------------------------------------

--
-- Table structure for table `page_meta`
--

CREATE TABLE `page_meta` (
  `page_key` varchar(50) NOT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` varchar(500) DEFAULT NULL,
  `meta_keywords` varchar(500) DEFAULT NULL,
  `meta_robots` varchar(50) NOT NULL DEFAULT 'index, follow',
  `og_image` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `page_meta`
--

INSERT INTO `page_meta` (`page_key`, `meta_title`, `meta_description`, `meta_keywords`, `meta_robots`, `og_image`, `updated_at`) VALUES
('about', 'About Us | Sell My Phone Dubai – Trusted Phone Buyers', 'Discover why thousands of Dubai residents trust SellMyPhoneDubai to sell their used iPhones and Samsung phones. Fast quotes, fair prices, and secure, hassle-free service.', 'about sell my phone dubai, trusted phone buyer dubai, phone selling company dubai, who buys phones in dubai, best phone resale company dubai', 'index, follow', NULL, '2026-08-24 09:59:36'),
('blogs', 'Blog | Phone Selling Tips & Tech News – Sell My Phone Dubai', 'Stay updated with expert tips on selling your phone, resale value guides, and the latest mobile technology trends from Dubai\'s leading phone buying platform.', 'phone selling tips dubai, sell iphone tips, mobile resale guide, tech news dubai, phone blog dubai, how to sell my phone', 'index, follow', NULL, '2026-08-24 09:59:36'),
('contact', 'Contact Us | Sell My Phone Dubai', 'Have a phone to sell? Contact SellMyPhoneDubai via WhatsApp, phone, or our online form. Book a free pickup anywhere in Dubai and get paid instantly.', 'contact sell my phone dubai, phone pickup dubai, sell phone whatsapp dubai, book phone pickup dubai, phone selling contact number dubai', 'index, follow', NULL, '2026-08-24 09:59:36'),
('home', 'Sell My Phone Dubai | Instant Cash for Used iPhone & Samsung', 'Sell your used iPhone, Samsung or any smartphone in Dubai for the best price. Get an instant quote, free doorstep pickup, and same-day secure payment.', 'sell phone dubai, sell iphone dubai, sell samsung dubai, sell used phone dubai, cash for phones dubai, we buy phones dubai, phone buyer dubai, sell mobile dubai', 'index, follow', NULL, '2026-08-24 09:59:36');

-- --------------------------------------------------------

--
-- Table structure for table `pickup_requests`
--

CREATE TABLE `pickup_requests` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pickup_requests`
--

INSERT INTO `pickup_requests` (`id`, `name`, `phone`, `status`, `created_at`) VALUES
(1, 'Ahmad', '03268671830', 'pending', '2026-08-19 07:11:47'),
(2, 'Roman', '03268671830', 'contacted', '2026-08-19 07:27:41'),
(3, 'sara', '03938738939', 'pending', '2026-08-19 07:37:42'),
(4, 'Saba Maqbool', '03938738939', 'contacted', '2026-08-19 10:07:30');

-- --------------------------------------------------------

--
-- Table structure for table `price_storage_options`
--

CREATE TABLE `price_storage_options` (
  `id` int(11) NOT NULL,
  `model_id` int(11) NOT NULL,
  `label` varchar(50) NOT NULL,
  `price_delta` decimal(10,2) NOT NULL DEFAULT 0.00,
  `sort_order` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `price_storage_options`
--

INSERT INTO `price_storage_options` (`id`, `model_id`, `label`, `price_delta`, `sort_order`) VALUES
(17, 94, '1 TB', 310.00, 0),
(18, 97, '1 TB', 210.00, 0),
(19, 101, '1 TB', 240.00, 0),
(20, 105, '1 TB', 350.00, 0),
(21, 109, '1 TB', 710.00, 0),
(22, 119, '1 TB', 110.00, 0),
(23, 121, '1 TB', 430.00, 0),
(24, 122, '1 TB', 760.00, 0),
(33, 55, '1 TB', 110.00, 0),
(34, 57, '1 TB', 430.00, 0),
(35, 58, '1 TB', 760.00, 0),
(36, 71, '1 TB', 310.00, 0),
(37, 75, '1 TB', 210.00, 0),
(38, 79, '1 TB', 240.00, 0),
(39, 83, '1 TB', 350.00, 0),
(40, 87, '1 TB', 710.00, 0);

-- --------------------------------------------------------

--
-- Table structure for table `site_settings`
--

CREATE TABLE `site_settings` (
  `id` int(11) NOT NULL DEFAULT 1,
  `site_title` varchar(150) NOT NULL DEFAULT 'SellMyPhoneDubai',
  `logo` varchar(255) DEFAULT NULL,
  `favicon` varchar(255) DEFAULT NULL,
  `nav_home_label` varchar(60) NOT NULL DEFAULT 'Home',
  `nav_about_label` varchar(60) NOT NULL DEFAULT 'About',
  `nav_quote_label` varchar(60) NOT NULL DEFAULT 'Get Instant Quote',
  `nav_blogs_label` varchar(60) NOT NULL DEFAULT 'Blogs',
  `nav_testimonials_label` varchar(60) NOT NULL DEFAULT 'Testimonials',
  `nav_contact_label` varchar(60) NOT NULL DEFAULT 'Contact Us',
  `footer_about_text` text DEFAULT NULL,
  `footer_phone` varchar(30) DEFAULT NULL,
  `footer_whatsapp` varchar(30) DEFAULT NULL,
  `footer_email` varchar(150) DEFAULT NULL,
  `footer_address` varchar(255) DEFAULT NULL,
  `facebook_url` varchar(255) DEFAULT NULL,
  `instagram_url` varchar(255) DEFAULT NULL,
  `twitter_url` varchar(255) DEFAULT NULL,
  `linkedin_url` varchar(255) DEFAULT NULL,
  `copyright_text` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `site_settings`
--

INSERT INTO `site_settings` (`id`, `site_title`, `logo`, `favicon`, `nav_home_label`, `nav_about_label`, `nav_quote_label`, `nav_blogs_label`, `nav_testimonials_label`, `nav_contact_label`, `footer_about_text`, `footer_phone`, `footer_whatsapp`, `footer_email`, `footer_address`, `facebook_url`, `instagram_url`, `twitter_url`, `linkedin_url`, `copyright_text`, `updated_at`) VALUES
(1, 'SellMyPhoneDubai', 'imgs/logo_6a8c1cc804a29.webp', 'imgs/favicon_6a8c1cc804d8e.jpg', 'Home', 'About', 'Get Instant Quote', 'Blogs', 'Testimonials', 'Contact Us', 'Dubai\'s trusted platform for selling used phones with instant cash payment and free pickup service across all areas.', '+971 50 216 6562', '+971 50 216 6562', 'info@sellmyphonedubai.com', 'Al Quoz 3rd, Showroom No 33, Sheikh Zayed Road, Dubai', '', '', '', '', '© 2026 SellPhoneDubai. All rights reserved.', '2026-08-24 10:28:24');

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
-- Indexes for table `blogs`
--
ALTER TABLE `blogs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `contact_info`
--
ALTER TABLE `contact_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `home_sections`
--
ALTER TABLE `home_sections`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `section_key` (`section_key`);

--
-- Indexes for table `home_section_items`
--
ALTER TABLE `home_section_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_home_section_items_section` (`section_id`);

--
-- Indexes for table `leads`
--
ALTER TABLE `leads`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_number` (`order_number`);

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
-- Indexes for table `model_storage_options`
--
ALTER TABLE `model_storage_options`
  ADD PRIMARY KEY (`id`),
  ADD KEY `model_storage_options_ibfk_1` (`model_id`);

--
-- Indexes for table `page_meta`
--
ALTER TABLE `page_meta`
  ADD PRIMARY KEY (`page_key`);

--
-- Indexes for table `pickup_requests`
--
ALTER TABLE `pickup_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `price_storage_options`
--
ALTER TABLE `price_storage_options`
  ADD PRIMARY KEY (`id`),
  ADD KEY `model_id` (`model_id`);

--
-- Indexes for table `site_settings`
--
ALTER TABLE `site_settings`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_user`
--
ALTER TABLE `admin_user`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `blogs`
--
ALTER TABLE `blogs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `contact_info`
--
ALTER TABLE `contact_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `home_sections`
--
ALTER TABLE `home_sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `home_section_items`
--
ALTER TABLE `home_section_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `leads`
--
ALTER TABLE `leads`
  MODIFY `id` int(200) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `lead_images`
--
ALTER TABLE `lead_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `models`
--
ALTER TABLE `models`
  MODIFY `id` int(200) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=127;

--
-- AUTO_INCREMENT for table `model_pricing`
--
ALTER TABLE `model_pricing`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=185;

--
-- AUTO_INCREMENT for table `model_storage_options`
--
ALTER TABLE `model_storage_options`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=447;

--
-- AUTO_INCREMENT for table `pickup_requests`
--
ALTER TABLE `pickup_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `price_storage_options`
--
ALTER TABLE `price_storage_options`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `home_section_items`
--
ALTER TABLE `home_section_items`
  ADD CONSTRAINT `fk_home_section_items_section` FOREIGN KEY (`section_id`) REFERENCES `home_sections` (`id`) ON DELETE CASCADE;

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

--
-- Constraints for table `model_storage_options`
--
ALTER TABLE `model_storage_options`
  ADD CONSTRAINT `model_storage_options_ibfk_1` FOREIGN KEY (`model_id`) REFERENCES `models` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
