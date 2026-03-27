-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 27, 2026 at 04:33 PM
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
-- Database: `alkhaleej_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `billing`
--

CREATE TABLE `billing` (
  `bill_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `bill_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `subtotal` decimal(10,2) NOT NULL,
  `tax` decimal(10,2) DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`cart_id`, `customer_id`, `created_at`) VALUES
(1, 4, '2026-02-14 16:17:40'),
(2, 2, '2026-03-04 17:56:36'),
(5, 5, '2026-03-26 15:36:25'),
(6, 6, '2026-03-26 15:38:09');

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `items_id` int(11) NOT NULL,
  `cart_id` int(11) NOT NULL,
  `menu_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart_items`
--

INSERT INTO `cart_items` (`items_id`, `cart_id`, `menu_id`, `quantity`) VALUES
(31, 2, 10, 1),
(32, 2, 12, 1),
(42, 6, 12, 1),
(43, 6, 11, 2),
(44, 6, 19, 1),
(45, 6, 10, 1),
(46, 6, 37, 2);

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `customer_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`customer_id`, `full_name`, `phone`, `email`) VALUES
(1, 'amina', '', NULL),
(2, 'amina', '08050166177', NULL),
(3, 'muskaan', '4354545223', NULL),
(4, 'muskaan', '9536277110', NULL),
(5, 'muskaan', '1234567890', 'staff1@gmail.com'),
(6, 'Guest', '1234567822', 'muskaan@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `feedback_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` between 1 and 5),
  `message` text DEFAULT NULL,
  `feedback_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `menu`
--

CREATE TABLE `menu` (
  `menu_id` int(11) NOT NULL,
  `item_name` varchar(100) NOT NULL,
  `category` varchar(50) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  `availability` enum('available','not_available') DEFAULT 'available',
  `status` varchar(20) NOT NULL DEFAULT 'available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu`
--

INSERT INTO `menu` (`menu_id`, `item_name`, `category`, `price`, `description`, `availability`, `status`) VALUES
(10, 'Chicken wings', 'starters', 240.00, '10 pieces', 'available', 'available'),
(11, 'Chicken Tikka', 'Starters', 270.00, '6 pieces', 'available', 'available'),
(12, 'Chicken seekh kabab', 'Starters', 280.00, '3 pcs,Kuboos, Mayonnaise, Salad.', 'available', 'available'),
(13, 'Mutton Seek Kabab', 'Starters', 400.00, '3 pcs, Kuboos, Mayonnaise, Salad', 'available', 'available'),
(14, 'chick pop', 'Starters', 180.00, '6 pcs', 'available', 'available'),
(15, 'Chicken Nuggets', 'Starters', 140.00, '6 pcs', 'available', 'available'),
(16, 'Hot Wings (6pcs)', 'Starters', 190.00, '', 'available', 'available'),
(17, 'Hot Wings(12 pcs)', 'Starters', 360.00, '', 'available', 'available'),
(18, 'Chicken 65(6 pcs)', 'Starters', 210.00, '', 'available', 'available'),
(19, 'Chicken lollypop', 'Starters', 240.00, '', 'available', 'available'),
(20, 'Chicken Schezwan Lollypop', 'Starters', 280.00, '', 'available', 'available'),
(21, 'Fried Momos', 'Starters', 170.00, '', 'available', 'available'),
(22, 'Peri Peri Momos', 'Starters', 190.00, '', 'available', 'available'),
(23, 'Steemed Momos', 'Starters', 190.00, '', 'available', 'available'),
(24, 'Chicken Tikka Burger(Individual)', 'main course', 130.00, 'Individual', 'available', 'available'),
(25, 'Chicken Tikka Burger(Combo)', 'main course', 220.00, '', 'available', 'available'),
(26, 'Chicken Zinger Burger(Individual)', 'main course', 170.00, '', 'available', 'available'),
(27, 'Chicken Zinger Burger(Combo)', 'main course', 235.00, '', 'available', 'available'),
(28, 'Chicken Wrap(Individual)', 'main course', 170.00, '', 'available', 'available'),
(29, 'Chicken Wrap(Combo)', 'main course', 235.00, '', 'available', 'available'),
(30, 'Veg Wrap(Individual)', 'main course', 160.00, '', 'available', 'available'),
(31, 'Veg Wrap(Combo)', 'main course', 200.00, '', 'available', 'available'),
(32, 'Veg Patty Burger(Individual)', 'main course', 160.00, '', 'available', 'available'),
(33, 'Veg Patty Burger(Combo)', 'main course', 170.00, '', 'available', 'available'),
(34, 'Veg Soup', 'Starters', 120.00, '', 'available', 'available'),
(35, 'Veg Manchow Soup', 'Starters', 130.00, '', 'available', 'available'),
(36, 'Veg Hot n Sour Soup', 'Starters', 130.00, '', 'available', 'available'),
(37, 'Sweet corn soup', 'Starters', 120.00, '', 'available', 'available'),
(38, 'Chicken Soup', 'Starters', 120.00, '', 'available', 'available'),
(39, 'Chicken Manchew soup', 'Starters', 130.00, '', 'available', 'available'),
(40, 'Chicken Hot n Sour Soup', 'Starters', 120.00, '', 'available', 'available'),
(41, 'Chicken Clear Soup', 'Starters', 130.00, '', 'available', 'available'),
(42, 'Chicken Sweet Corn Soup', 'Starters', 130.00, '', 'available', 'available'),
(43, 'Mutton Soup', 'Starters', 150.00, '', 'available', 'available'),
(44, 'Mutton Clear Soup', 'Starters', 150.00, '', 'available', 'available'),
(45, 'Mutton Hot n Sour Soup', 'Starters', 150.00, '', 'available', 'available'),
(46, 'Mutton Manchew Soup', 'Starters', 150.00, '', 'available', 'available'),
(47, 'Mutton Sweet Corn Soup', 'Starters', 150.00, '', 'available', 'available'),
(48, 'Prawns Manchew Soup', 'Starters', 150.00, '', 'available', 'available'),
(49, 'Prawns Hot n Sour Soup', 'Starters', 150.00, '', 'available', 'available'),
(50, 'Prawns Soup', 'Starters', 150.00, '', 'available', 'available'),
(51, 'Mutton Chaps (Per Piece)', ' Mutton Starters', 230.00, '', 'available', 'available'),
(52, 'Mutton Chilly Dry', ' Mutton Starters', 370.00, '', 'available', 'available'),
(53, 'Mutton Manchurian Dry', ' Mutton Starters', 370.00, '', 'available', 'available'),
(54, 'Mutton schezwan Dry', ' Mutton Starters', 370.00, '', 'available', 'available'),
(55, 'Mutton Pepper Dry', ' Mutton Starters', 370.00, '', 'available', 'available'),
(56, 'Mutton Tawa', ' Mutton Starters', 370.00, '', 'available', 'available'),
(57, 'Mutton Ghee Roast', ' Mutton Starters', 400.00, '', 'available', 'available'),
(58, 'Gobi Manchurian Dry', ' Veg Starters', 170.00, '', 'available', 'available'),
(59, 'Gobi Chilly Dry', ' Veg Starters', 170.00, '', 'available', 'available'),
(60, 'Gobi Schezwan Dry', ' Veg Starters', 180.00, '', 'available', 'available'),
(61, 'Gobi Pepper Dry', ' Veg Starters', 180.00, '', 'available', 'available'),
(62, 'Gobi Garlic Dry', ' Veg Starters', 180.00, '', 'available', 'available'),
(63, 'Mushroom Chilly Dry', ' Veg Starters', 220.00, '', 'available', 'available'),
(64, 'Mushroom Schezwan Dry', ' Veg Starters', 230.00, '', 'available', 'available'),
(65, 'Mushroom Manchurian Dry', ' Veg Starters', 230.00, '', 'available', 'available'),
(66, 'Manchurian Pepper Dry', ' Veg Starters', 230.00, '', 'available', 'available'),
(67, 'Manchurian Garlic Dry', ' Veg Starters', 230.00, '', 'available', 'available'),
(68, 'Paneer Garlic Dry', 'Veg Starters', 250.00, '', 'available', 'available'),
(69, 'Paneer Manchurian Dry', 'Veg Starters', 250.00, '', 'available', 'available'),
(70, 'Paneer Chilly Dry', 'Veg Starters', 250.00, '', 'available', 'available'),
(71, 'Paneer Schrezwan Dry', 'Veg Starters', 260.00, '', 'available', 'available'),
(72, 'Paneer Pepper Dry', 'Veg Starters', 260.00, '', 'available', 'available'),
(73, 'Baby Corn Manchurian Dry', 'Veg Starters', 230.00, '', 'available', 'available'),
(74, 'Baby Corn Chilly Dry', 'Veg Starters', 230.00, '', 'available', 'available'),
(75, 'Baby Corn Schezwan Dry', 'Veg Starters', 230.00, '', 'available', 'available'),
(76, 'Baby Corn Pepper Dry', 'Veg Starters', 230.00, '', 'available', 'available'),
(77, 'Chicken Manchurian Dry', 'Chicken Starters', 200.00, '', 'available', 'available'),
(78, 'Chicken Manchurian Dry(Full)', 'Chicken Starters', 350.00, '', 'available', 'available'),
(79, 'Chicken Chilly Dry(Half)', 'Chicken Starters', 200.00, '', 'available', 'available'),
(80, 'Chicken Chilly Dry(Full)', 'Chicken Starters', 350.00, '', 'available', 'available'),
(81, 'Chicken Schezwan Dry(Half)', 'Chicken Starters', 220.00, '', 'available', 'available'),
(82, 'Chicken Schezwan Dry(Full)', 'Chicken Starters', 400.00, '', 'available', 'available'),
(83, 'Chicken Pepper Dry(Half)', 'Chicken Starters', 210.00, '', 'available', 'available'),
(84, 'Chicken Pepper Dry(Full)', 'Chicken Starters', 400.00, '', 'available', 'available'),
(85, 'Chicken Garlic Dry(Half)', 'Chicken Starters', 220.00, '', 'available', 'available'),
(86, 'Chicken Garlic Dry(Full)', 'Chicken Starters', 400.00, '', 'available', 'available'),
(87, 'Chicken Ginger Dry(Half)', 'Chicken Starters', 220.00, '', 'available', 'available'),
(88, 'Chicken Ginger Dry(Full)', 'Chicken Starters', 400.00, '', 'available', 'available'),
(89, 'Chicken Lemon Chilly Dry(Full)', 'Chicken Starters', 420.00, '', 'available', 'available'),
(90, 'Dragon Chilly Dry', 'Chicken Starters', 460.00, '', 'available', 'available'),
(91, 'Chicken Ghee Roast', 'Chicken Starters', 330.00, '', 'available', 'available'),
(92, 'Chicken Masala', 'Indian Chicken Curry special', 200.00, '', 'available', 'available'),
(93, 'Chicken Kolapuri', 'Indian Chicken Curry Special', 210.00, '', 'available', 'available'),
(94, 'chicken Kolapuri', 'Indian Chicken Curry Special', 210.00, '', 'available', 'available'),
(95, 'Chicken Hyderabadi', 'Indian Chicken Curry Special', 220.00, '', 'available', 'available'),
(96, 'Chicken Mughlai', 'Indian Chicken Curry Special', 230.00, '', 'available', 'available'),
(97, 'Chicken Hariyali', 'Indian Chicken Curry Special', 220.00, '', 'available', 'available'),
(98, 'Chicken Kadai', 'Indian Chicken Curry Special', 230.00, '', 'available', 'available'),
(99, 'Chicken Dopaiza(Al-Khaleej Special)', 'Indian Chicken Curry Special', 450.00, '', 'available', 'available'),
(100, 'Butter Chicken', 'Indian Chicken Curry Special', 280.00, '', 'available', 'available'),
(101, 'Chicken Curry', 'Indian Chicken Curry Special', 180.00, '', 'available', 'available'),
(102, 'Chicken Punjabi Masala', 'Indian Chicken Curry Special', 250.00, '', 'available', 'available'),
(103, 'Chicken Methi Masala', 'Indian Chicken Curry Special', 350.00, '', 'available', 'available'),
(104, 'Khaleej Wing Masala', 'Indian Chicken Curry Special', 260.00, '', 'available', 'available'),
(105, 'Chicken Nawabi Masala', 'Indian Chicken Curry Special', 260.00, '', 'available', 'available'),
(106, 'Chicken Chettinad Masala', 'Indian Chicken Curry Special', 400.00, '', 'available', 'available'),
(107, 'Chicken Tikka Masala', 'Indian Chicken Curry Special', 300.00, '', 'available', 'available'),
(108, 'Chicken Palak', 'Indian Chicken Curry Special', 230.00, '', 'available', 'available'),
(109, 'Pepper Chicken Masala', 'Indian Chicken Curry Special', 270.00, '', 'available', 'available'),
(110, 'Chicken Rogan Josh', 'Indian Chicken Curry Special', 270.00, '', 'available', 'available'),
(111, 'Chicken Kurma', 'Indian Chicken Curry Special', 230.00, '', 'available', 'available'),
(112, 'Chicken Cocktail(Al-Khaleej Special)', 'Indian Chicken Curry Special', 820.00, '', 'available', 'available'),
(113, 'Chicken Rana', 'Indian Chicken Curry Special', 330.00, '', 'available', 'available'),
(114, 'Chicken Taj', 'Indian Chicken Curry Special', 450.00, '', 'available', 'available'),
(115, 'Chicken Turkey Masala', 'Indian Chicken Curry Special', 460.00, '', 'available', 'available'),
(116, 'Chicken Angara', 'Indian Chicken Curry Special', 450.00, '', 'available', 'available'),
(117, 'Mutton Masala', 'Mutton Curry Items', 300.00, '', 'available', 'available'),
(118, 'Mutton Hariyali', 'Mutton Curry Items', 300.00, '', 'available', 'available'),
(119, 'Mutton Hyderabadi', 'Mutton Curry Items', 300.00, '', 'available', 'available'),
(120, 'Mutton Kadai', 'Mutton Curry Items', 300.00, '', 'available', 'available'),
(121, 'Mutton Mughlai', 'Mutton Curry Items', 310.00, '', 'available', 'available'),
(122, 'Mutton Pepper Masala', 'Mutton Curry Items', 320.00, '', 'available', 'available'),
(123, 'Mutton Rogan Ghosh', 'Mutton Curry Items', 320.00, '', 'available', 'available'),
(124, 'Mutton Handi', 'Mutton Curry Items', 330.00, '', 'available', 'available'),
(125, 'Mutton Kohlapuri', 'Mutton Curry Items', 300.00, '', 'available', 'available'),
(126, 'Mutton Afghani', 'Mutton Curry Items', 500.00, '', 'available', 'available'),
(127, 'Mutton Rana', 'Mutton Curry Items', 470.00, '', 'available', 'available'),
(128, 'Mutton Maharaja', 'Mutton Curry Items', 470.00, '', 'available', 'available'),
(129, 'Mutton Ashyana', 'Mutton Curry Items', 470.00, '', 'available', 'available'),
(130, 'Mutton Peshawari', 'Mutton Curry Items', 470.00, '', 'available', 'available'),
(131, 'Mutton Do Pyaza', 'Mutton Curry Items', 580.00, '', 'available', 'available'),
(132, 'Mutton Cocktail', 'Mutton Curry Items', 1050.00, '', 'available', 'available'),
(133, 'Mutton Kurma', 'Mutton Curry Items', 320.00, '', 'available', 'available'),
(134, 'Egg Masala', 'Egg Curry Items', 150.00, '', 'available', 'available'),
(135, 'Egg Kohlapuri', 'Egg Curry Items', 150.00, '', 'available', 'available'),
(136, 'Egg Pepper Masala', 'Egg Curry Items', 150.00, '', 'available', 'available'),
(137, 'Egg Chilli', 'Egg Curry Items', 150.00, '', 'available', 'available'),
(138, 'Egg Pepper', 'Egg Curry Items', 150.00, '', 'available', 'available'),
(139, 'Egg Manchurian', 'Egg Curry Items', 150.00, '', 'available', 'available'),
(140, 'Khuboos', 'Bread Items', 12.00, '', 'available', 'available'),
(141, 'Rotti', 'Bread Items', 22.00, '', 'available', 'available'),
(142, 'Nan', 'Bread Items', 30.00, '', 'available', 'available'),
(143, 'Kulcha', 'Bread Items', 30.00, '', 'available', 'available'),
(144, 'Chapathi', 'Bread Items', 20.00, '', 'available', 'available'),
(145, 'Kerala Parotta', 'Bread Items', 15.00, '', 'available', 'available'),
(146, 'Butter Rotti', 'Bread Items', 30.00, '', 'available', 'available'),
(147, 'Butter Nan', 'Bread Items', 40.00, '', 'available', 'available'),
(148, 'Butter Kulcha', 'Bread Items', 40.00, '', 'available', 'available'),
(149, 'Rumali Roti', 'Bread Items', 22.00, '', 'available', 'available'),
(150, 'Garlic Nan', 'Bread Items', 60.00, '', 'available', 'available'),
(151, 'Pepper Nan', 'Bread Items', 60.00, '', 'available', 'available'),
(152, 'Cheese Nan', 'Bread Items', 70.00, '', 'available', 'available'),
(153, 'Wheat Parota', 'Bread Items', 20.00, '', 'available', 'available'),
(154, 'Chicken Tandoori Full', 'Tandoori Items', 510.00, '', 'available', 'available'),
(155, 'Chicken tandoori Half', 'Tandoori Items', 260.00, '', 'available', 'available'),
(156, 'Chicken Tandoori Quarter', 'Tandoori Items', 150.00, '', 'available', 'available'),
(157, 'Chicken Choice Piece', 'Tandoori Items', 160.00, '', 'available', 'available'),
(158, 'Chicken Tikka', 'Tandoori Items', 270.00, '', 'available', 'available'),
(159, 'Punjabi Tikka', 'Tandoori Items', 300.00, '', 'available', 'available'),
(160, 'Hariyali Tikka', 'Tandoori Items', 300.00, '', 'available', 'available'),
(161, 'Garlic Tikka', 'Tandoori Items', 300.00, '', 'available', 'available'),
(162, 'Pahadi Tikka', 'Tandoori Items', 300.00, '', 'available', 'available'),
(163, 'Reshmi Tikka', 'Tandoori Items', 300.00, '', 'available', 'available'),
(164, 'Malai Tikka', 'Tandoori Items ', 300.00, '', 'available', 'available'),
(165, 'Paneer Tikka', 'Tandoori Items', 400.00, '', 'available', 'available'),
(166, 'Chicken Biriyani(Full)', 'Biriyani Items', 195.00, '', 'available', 'available'),
(167, 'Chicken Biriyani(Half)', 'Biriyani Items', 140.00, '', 'available', 'available'),
(168, 'Mutton Biriyani(Full)', 'Biriyani Items', 290.00, '', 'available', 'available'),
(169, 'Mutton Biriyani(Half)', 'Biriyani Items', 190.00, '', 'available', 'available'),
(170, 'Egg Biriyani', 'Biriyani Items', 160.00, '', 'available', 'available'),
(171, 'Veg Biriyani', 'Biriyani Items', 160.00, '', 'available', 'available'),
(172, 'Veg Curry', 'Vegetable Items', 170.00, '', 'available', 'available'),
(173, 'Dopiyaz(Al-Khaleej Special)', 'Vegetable Items', 350.00, '', 'available', 'available'),
(174, 'Veg Kadai', 'Vegetable Items', 220.00, '', 'available', 'available'),
(175, 'Veg Hyderabadi', 'Vegetable Items', 220.00, '', 'available', 'available'),
(176, 'Veg Kolapuri', 'Vegetable Items', 220.00, '', 'available', 'available'),
(177, 'Veg Handi', 'Vegetable Items', 230.00, '', 'available', 'available'),
(178, 'Veg Kurma', 'Vegetable Items', 200.00, '', 'available', 'available'),
(179, 'Veg Lahori', 'Vegetable Items', 260.00, '', 'available', 'available'),
(180, 'Dal Fry', 'Vegetable Items', 130.00, '', 'available', 'available');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `reservation_id` int(11) DEFAULT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `order_type` enum('dine_in') DEFAULT 'dine_in',
  `status` enum('pending','preparing','served','completed','cancelled') DEFAULT 'pending',
  `total_amount` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `orderitems_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `menu_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `payment_method` enum('cash','upi','card') NOT NULL,
  `payment_status` enum('pending','paid') DEFAULT 'paid',
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_process`
--

CREATE TABLE `payment_process` (
  `id` int(11) NOT NULL,
  `bill_id` int(11) NOT NULL,
  `payment_method` enum('cash','upi','card') NOT NULL,
  `payment_status` enum('paid','unpaid') DEFAULT 'paid',
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `report_id` int(11) NOT NULL,
  `report_type` varchar(50) NOT NULL,
  `generated_by` int(11) NOT NULL,
  `generated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reservation`
--

CREATE TABLE `reservation` (
  `reservation_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `reservation_date` date NOT NULL,
  `reservation_time` time NOT NULL,
  `no_of_people` int(11) NOT NULL,
  `table_number` int(11) DEFAULT NULL,
  `status` enum('pending','confirmed','cancelled') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservation`
--

INSERT INTO `reservation` (`reservation_id`, `customer_id`, `reservation_date`, `reservation_time`, `no_of_people`, `table_number`, `status`) VALUES
(10, 5, '2026-03-10', '18:57:00', 4, 2, 'confirmed'),
(11, 5, '2026-03-10', '18:26:00', 3, NULL, 'pending'),
(12, 5, '2026-03-25', '23:50:00', 2, NULL, 'pending'),
(13, 5, '2026-03-03', '21:48:00', 3, NULL, 'pending'),
(14, 5, '2026-03-03', '23:50:00', 3, 2, 'confirmed');

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `staff_id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(10) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `salary` decimal(10,2) DEFAULT NULL,
  `role` varchar(50) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`staff_id`, `name`, `email`, `password`, `phone`, `salary`, `role`, `status`) VALUES
(1, 'musskaan', 'staff1@gmail.com', '12345678', '8765432109', 4999.00, 'waiter', 'inactive');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `admin_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`admin_id`, `full_name`, `email`, `phone`, `password`) VALUES
(1, 'Admin', 'admin@gmail.com', '9999999999', 'admin123');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `billing`
--
ALTER TABLE `billing`
  ADD PRIMARY KEY (`bill_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`items_id`),
  ADD KEY `cart_id` (`cart_id`),
  ADD KEY `menu_id` (`menu_id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`customer_id`),
  ADD UNIQUE KEY `phone` (`phone`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`feedback_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`menu_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `reservation_id` (`reservation_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`orderitems_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `menu_id` (`menu_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `payment_process`
--
ALTER TABLE `payment_process`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bill_id` (`bill_id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `generated_by` (`generated_by`);

--
-- Indexes for table `reservation`
--
ALTER TABLE `reservation`
  ADD PRIMARY KEY (`reservation_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`staff_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `billing`
--
ALTER TABLE `billing`
  MODIFY `bill_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `items_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `feedback_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `menu`
--
ALTER TABLE `menu`
  MODIFY `menu_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=181;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `orderitems_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `payment_process`
--
ALTER TABLE `payment_process`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reservation`
--
ALTER TABLE `reservation`
  MODIFY `reservation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `staff_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `billing`
--
ALTER TABLE `billing`
  ADD CONSTRAINT `billing_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE;

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`cart_id`) REFERENCES `cart` (`cart_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`menu_id`) REFERENCES `menu` (`menu_id`) ON DELETE CASCADE;

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `users` (`admin_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `feedback_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE SET NULL;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`reservation_id`) REFERENCES `reservation` (`reservation_id`) ON DELETE SET NULL;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`menu_id`) REFERENCES `menu` (`menu_id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `users` (`admin_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE;

--
-- Constraints for table `payment_process`
--
ALTER TABLE `payment_process`
  ADD CONSTRAINT `payment_process_ibfk_1` FOREIGN KEY (`bill_id`) REFERENCES `billing` (`bill_id`) ON DELETE CASCADE;

--
-- Constraints for table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `reports_ibfk_1` FOREIGN KEY (`generated_by`) REFERENCES `users` (`admin_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
