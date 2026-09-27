-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 27, 2026 at 03:22 PM
-- Server version: 8.0.46-0ubuntu0.24.04.4
-- PHP Version: 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `laravel_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `banned_emails`
--

DROP TABLE IF EXISTS `banned_emails`;
CREATE TABLE IF NOT EXISTS `banned_emails` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `banned_by` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `banned_emails_email_unique` (`email`),
  KEY `banned_emails_banned_by_foreign` (`banned_by`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('laravel-cache-spatie.permission.cache', 'a:3:{s:5:\"alias\";a:4:{s:1:\"a\";s:2:\"id\";s:1:\"b\";s:4:\"name\";s:1:\"c\";s:10:\"guard_name\";s:1:\"r\";s:5:\"roles\";}s:11:\"permissions\";a:125:{i:0;a:3:{s:1:\"a\";i:1;s:1:\"b\";s:12:\"manage users\";s:1:\"c\";s:3:\"web\";}i:1;a:3:{s:1:\"a\";i:2;s:1:\"b\";s:12:\"manage roles\";s:1:\"c\";s:3:\"web\";}i:2;a:3:{s:1:\"a\";i:3;s:1:\"b\";s:18:\"manage permissions\";s:1:\"c\";s:3:\"web\";}i:3;a:3:{s:1:\"a\";i:4;s:1:\"b\";s:12:\"manage shops\";s:1:\"c\";s:3:\"web\";}i:4;a:3:{s:1:\"a\";i:5;s:1:\"b\";s:15:\"manage products\";s:1:\"c\";s:3:\"web\";}i:5;a:3:{s:1:\"a\";i:6;s:1:\"b\";s:14:\"view analytics\";s:1:\"c\";s:3:\"web\";}i:6;a:3:{s:1:\"a\";i:7;s:1:\"b\";s:14:\"manage reports\";s:1:\"c\";s:3:\"web\";}i:7;a:4:{s:1:\"a\";i:8;s:1:\"b\";s:13:\"approve shops\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:8;a:4:{s:1:\"a\";i:9;s:1:\"b\";s:11:\"create shop\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:8;}}i:9;a:4:{s:1:\"a\";i:10;s:1:\"b\";s:10:\"view users\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:10;a:3:{s:1:\"a\";i:11;s:1:\"b\";s:14:\"view dashboard\";s:1:\"c\";s:3:\"web\";}i:11;a:3:{s:1:\"a\";i:12;s:1:\"b\";s:18:\"view notifications\";s:1:\"c\";s:3:\"web\";}i:12;a:4:{s:1:\"a\";i:13;s:1:\"b\";s:12:\"create users\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:13;a:4:{s:1:\"a\";i:14;s:1:\"b\";s:12:\"update users\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:14;a:4:{s:1:\"a\";i:15;s:1:\"b\";s:12:\"delete users\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:15;a:4:{s:1:\"a\";i:16;s:1:\"b\";s:12:\"assign roles\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:16;a:4:{s:1:\"a\";i:17;s:1:\"b\";s:18:\"assign permissions\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:17;a:3:{s:1:\"a\";i:18;s:1:\"b\";s:12:\"delete shops\";s:1:\"c\";s:3:\"web\";}i:18;a:3:{s:1:\"a\";i:19;s:1:\"b\";s:10:\"view shops\";s:1:\"c\";s:3:\"web\";}i:19;a:4:{s:1:\"a\";i:20;s:1:\"b\";s:15:\"create products\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:20;a:3:{s:1:\"a\";i:21;s:1:\"b\";s:15:\"update products\";s:1:\"c\";s:3:\"web\";}i:21;a:3:{s:1:\"a\";i:22;s:1:\"b\";s:15:\"delete products\";s:1:\"c\";s:3:\"web\";}i:22;a:3:{s:1:\"a\";i:23;s:1:\"b\";s:11:\"view orders\";s:1:\"c\";s:3:\"web\";}i:23;a:3:{s:1:\"a\";i:24;s:1:\"b\";s:13:\"manage orders\";s:1:\"c\";s:3:\"web\";}i:24;a:4:{s:1:\"a\";i:25;s:1:\"b\";s:19:\"update order status\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:25;a:3:{s:1:\"a\";i:26;s:1:\"b\";s:17:\"manage categories\";s:1:\"c\";s:3:\"web\";}i:26;a:4:{s:1:\"a\";i:27;s:1:\"b\";s:14:\"export reports\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:27;a:4:{s:1:\"a\";i:28;s:1:\"b\";s:18:\"send notifications\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:28;a:4:{s:1:\"a\";i:29;s:1:\"b\";s:15:\"manage settings\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:29;a:3:{s:1:\"a\";i:30;s:1:\"b\";s:14:\"manage profile\";s:1:\"c\";s:3:\"web\";}i:30;a:4:{s:1:\"a\";i:31;s:1:\"b\";s:15:\"search products\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:8;}}i:31;a:4:{s:1:\"a\";i:32;s:1:\"b\";s:12:\"search shops\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:8;}}i:32;a:4:{s:1:\"a\";i:33;s:1:\"b\";s:13:\"search system\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:33;a:4:{s:1:\"a\";i:34;s:1:\"b\";s:14:\"create sellers\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:34;a:4:{s:1:\"a\";i:35;s:1:\"b\";s:12:\"reject shops\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:35;a:3:{s:1:\"a\";i:36;s:1:\"b\";s:13:\"view products\";s:1:\"c\";s:3:\"web\";}i:36;a:3:{s:1:\"a\";i:37;s:1:\"b\";s:18:\"view order details\";s:1:\"c\";s:3:\"web\";}i:37;a:4:{s:1:\"a\";i:38;s:1:\"b\";s:15:\"checkout orders\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:8;}}i:38;a:4:{s:1:\"a\";i:39;s:1:\"b\";s:11:\"add to cart\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:8;}}i:39;a:4:{s:1:\"a\";i:40;s:1:\"b\";s:17:\"chat with sellers\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:8;}}i:40;a:4:{s:1:\"a\";i:41;s:1:\"b\";s:19:\"chat with customers\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:41;a:3:{s:1:\"a\";i:42;s:1:\"b\";s:13:\"view payments\";s:1:\"c\";s:3:\"web\";}i:42;a:4:{s:1:\"a\";i:43;s:1:\"b\";s:15:\"manage payments\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:43;a:4:{s:1:\"a\";i:44;s:1:\"b\";s:12:\"view reports\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:44;a:4:{s:1:\"a\";i:45;s:1:\"b\";s:13:\"view feedback\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:45;a:4:{s:1:\"a\";i:46;s:1:\"b\";s:23:\"view customer dashboard\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:8;}}i:46;a:4:{s:1:\"a\";i:47;s:1:\"b\";s:21:\"view seller dashboard\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:47;a:4:{s:1:\"a\";i:48;s:1:\"b\";s:20:\"view admin dashboard\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:48;a:4:{s:1:\"a\";i:49;s:1:\"b\";s:20:\"view public products\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:8;}}i:49;a:4:{s:1:\"a\";i:50;s:1:\"b\";s:17:\"view own products\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:50;a:4:{s:1:\"a\";i:51;s:1:\"b\";s:17:\"view all products\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:51;a:4:{s:1:\"a\";i:52;s:1:\"b\";s:19:\"update own products\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:52;a:4:{s:1:\"a\";i:53;s:1:\"b\";s:19:\"update any products\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:53;a:4:{s:1:\"a\";i:54;s:1:\"b\";s:19:\"delete own products\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:54;a:4:{s:1:\"a\";i:55;s:1:\"b\";s:19:\"delete any products\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:55;a:4:{s:1:\"a\";i:56;s:1:\"b\";s:17:\"view public shops\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:8;}}i:56;a:4:{s:1:\"a\";i:57;s:1:\"b\";s:13:\"view own shop\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:57;a:4:{s:1:\"a\";i:58;s:1:\"b\";s:14:\"view all shops\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:58;a:4:{s:1:\"a\";i:59;s:1:\"b\";s:15:\"update own shop\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:8;}}i:59;a:4:{s:1:\"a\";i:60;s:1:\"b\";s:15:\"update any shop\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:60;a:4:{s:1:\"a\";i:61;s:1:\"b\";s:15:\"delete own shop\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:61;a:4:{s:1:\"a\";i:62;s:1:\"b\";s:15:\"delete any shop\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:62;a:4:{s:1:\"a\";i:63;s:1:\"b\";s:21:\"create seller request\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:8;}}i:63;a:4:{s:1:\"a\";i:64;s:1:\"b\";s:23:\"view own seller request\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:8;}}i:64;a:4:{s:1:\"a\";i:65;s:1:\"b\";s:9:\"view cart\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:8;}}i:65;a:4:{s:1:\"a\";i:66;s:1:\"b\";s:11:\"update cart\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:8;}}i:66;a:4:{s:1:\"a\";i:67;s:1:\"b\";s:16:\"remove from cart\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:8;}}i:67;a:4:{s:1:\"a\";i:68;s:1:\"b\";s:12:\"create order\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:8;}}i:68;a:4:{s:1:\"a\";i:69;s:1:\"b\";s:15:\"view own orders\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:8;}}i:69;a:4:{s:1:\"a\";i:70;s:1:\"b\";s:16:\"view shop orders\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:70;a:4:{s:1:\"a\";i:71;s:1:\"b\";s:15:\"view all orders\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:71;a:4:{s:1:\"a\";i:72;s:1:\"b\";s:22:\"view own order details\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:8;}}i:72;a:4:{s:1:\"a\";i:73;s:1:\"b\";s:23:\"view shop order details\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:73;a:4:{s:1:\"a\";i:74;s:1:\"b\";s:22:\"view all order details\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:74;a:4:{s:1:\"a\";i:75;s:1:\"b\";s:16:\"track own orders\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:8;}}i:75;a:4:{s:1:\"a\";i:76;s:1:\"b\";s:17:\"cancel own orders\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:8;}}i:76;a:4:{s:1:\"a\";i:77;s:1:\"b\";s:23:\"update any order status\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:77;a:4:{s:1:\"a\";i:78;s:1:\"b\";s:14:\"create payment\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:8;}}i:78;a:4:{s:1:\"a\";i:79;s:1:\"b\";s:17:\"view own payments\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:8;}}i:79;a:4:{s:1:\"a\";i:80;s:1:\"b\";s:18:\"view shop payments\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:80;a:4:{s:1:\"a\";i:81;s:1:\"b\";s:17:\"view all payments\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:81;a:4:{s:1:\"a\";i:82;s:1:\"b\";s:16:\"receive payments\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:82;a:4:{s:1:\"a\";i:83;s:1:\"b\";s:13:\"send messages\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:8;}}i:83;a:4:{s:1:\"a\";i:84;s:1:\"b\";s:17:\"view own messages\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:8;}}i:84;a:4:{s:1:\"a\";i:85;s:1:\"b\";s:18:\"view shop messages\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:85;a:4:{s:1:\"a\";i:86;s:1:\"b\";s:22:\"view own notifications\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:8;}}i:86;a:4:{s:1:\"a\";i:87;s:1:\"b\";s:22:\"view all notifications\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:87;a:4:{s:1:\"a\";i:88;s:1:\"b\";s:27:\"send customer notifications\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:88;a:4:{s:1:\"a\";i:89;s:1:\"b\";s:18:\"update own profile\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:8;}}i:89;a:4:{s:1:\"a\";i:90;s:1:\"b\";s:19:\"update own settings\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:8;}}i:90;a:4:{s:1:\"a\";i:91;s:1:\"b\";s:14:\"create address\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:8;}}i:91;a:4:{s:1:\"a\";i:92;s:1:\"b\";s:16:\"view own address\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:8;}}i:92;a:4:{s:1:\"a\";i:93;s:1:\"b\";s:18:\"update own address\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:8;}}i:93;a:4:{s:1:\"a\";i:94;s:1:\"b\";s:18:\"delete own address\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:8;}}i:94;a:4:{s:1:\"a\";i:95;s:1:\"b\";s:14:\"create reviews\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:8;}}i:95;a:4:{s:1:\"a\";i:96;s:1:\"b\";s:12:\"view reviews\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:8;}}i:96;a:4:{s:1:\"a\";i:97;s:1:\"b\";s:14:\"reply feedback\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:97;a:4:{s:1:\"a\";i:98;s:1:\"b\";s:27:\"view customer delivery info\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:98;a:4:{s:1:\"a\";i:99;s:1:\"b\";s:20:\"manage own inventory\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:99;a:4:{s:1:\"a\";i:100;s:1:\"b\";s:18:\"view own analytics\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:100;a:4:{s:1:\"a\";i:101;s:1:\"b\";s:18:\"view all analytics\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:101;a:4:{s:1:\"a\";i:102;s:1:\"b\";s:12:\"view sellers\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:102;a:4:{s:1:\"a\";i:103;s:1:\"b\";s:14:\"update sellers\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:103;a:4:{s:1:\"a\";i:104;s:1:\"b\";s:17:\"create categories\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:104;a:4:{s:1:\"a\";i:105;s:1:\"b\";s:15:\"view categories\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:105;a:4:{s:1:\"a\";i:106;s:1:\"b\";s:17:\"update categories\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:106;a:4:{s:1:\"a\";i:107;s:1:\"b\";s:17:\"delete categories\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:107;a:4:{s:1:\"a\";i:108;s:1:\"b\";s:12:\"create roles\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:108;a:4:{s:1:\"a\";i:109;s:1:\"b\";s:12:\"update roles\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:109;a:4:{s:1:\"a\";i:110;s:1:\"b\";s:12:\"delete roles\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:110;a:4:{s:1:\"a\";i:111;s:1:\"b\";s:18:\"create permissions\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:111;a:4:{s:1:\"a\";i:112;s:1:\"b\";s:18:\"update permissions\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:112;a:4:{s:1:\"a\";i:113;s:1:\"b\";s:18:\"delete permissions\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:113;a:4:{s:1:\"a\";i:114;s:1:\"b\";s:13:\"manage system\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:114;a:4:{s:1:\"a\";i:115;s:1:\"b\";s:13:\"backup system\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:115;a:4:{s:1:\"a\";i:116;s:1:\"b\";s:14:\"restore system\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:116;a:4:{s:1:\"a\";i:117;s:1:\"b\";s:11:\"full access\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:117;a:4:{s:1:\"a\";i:118;s:1:\"b\";s:14:\"remove sellers\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:118;a:4:{s:1:\"a\";i:119;s:1:\"b\";s:16:\"view own payouts\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:119;a:4:{s:1:\"a\";i:120;s:1:\"b\";s:13:\"manage cities\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:120;a:4:{s:1:\"a\";i:121;s:1:\"b\";s:15:\"file complaints\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:8;}}i:121;a:4:{s:1:\"a\";i:122;s:1:\"b\";s:15:\"view complaints\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:122;a:4:{s:1:\"a\";i:123;s:1:\"b\";s:17:\"manage complaints\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:123;a:4:{s:1:\"a\";i:124;s:1:\"b\";s:10:\"view roles\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:124;a:4:{s:1:\"a\";i:125;s:1:\"b\";s:17:\"manage commission\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}}s:5:\"roles\";a:4:{i:0;a:3:{s:1:\"a\";i:1;s:1:\"b\";s:11:\"super_admin\";s:1:\"c\";s:3:\"web\";}i:1;a:3:{s:1:\"a\";i:2;s:1:\"b\";s:5:\"admin\";s:1:\"c\";s:3:\"web\";}i:2;a:3:{s:1:\"a\";i:8;s:1:\"b\";s:8:\"customer\";s:1:\"c\";s:3:\"web\";}i:3;a:3:{s:1:\"a\";i:3;s:1:\"b\";s:6:\"seller\";s:1:\"c\";s:3:\"web\";}}}', 1789914260);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cities`
--

DROP TABLE IF EXISTS `cities`;
CREATE TABLE IF NOT EXISTS `cities` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cities_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cities`
--

INSERT INTO `cities` (`id`, `name`, `code`, `created_at`, `updated_at`) VALUES
(1, 'Lahore', '2349', '2026-07-07 20:27:47', '2026-07-07 20:28:04'),
(2, 'Gujranwala', '12345', '2026-07-08 22:31:36', '2026-07-08 22:42:23'),
(4, 'Sadhoke', NULL, '2026-07-09 10:45:49', '2026-07-09 10:45:49'),
(6, 'gujrat', NULL, '2026-09-24 04:36:16', '2026-09-24 04:36:16');

-- --------------------------------------------------------

--
-- Table structure for table `commission_settings`
--

DROP TABLE IF EXISTS `commission_settings`;
CREATE TABLE IF NOT EXISTS `commission_settings` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `percentage` decimal(5,2) NOT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `commission_settings_updated_by_foreign` (`updated_by`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `commission_settings`
--

INSERT INTO `commission_settings` (`id`, `percentage`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 5.00, NULL, '2026-09-16 21:14:21', '2026-09-16 21:14:21'),
(2, 3.00, 15, '2026-09-16 22:06:18', '2026-09-16 22:06:18'),
(3, 5.00, 15, '2026-09-16 22:43:21', '2026-09-16 22:43:21');

-- --------------------------------------------------------

--
-- Table structure for table `complaints`
--

DROP TABLE IF EXISTS `complaints`;
CREATE TABLE IF NOT EXISTS `complaints` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `role` enum('customer','seller') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` enum('payment','order','account','other') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'other',
  `subject` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('open','in_progress','resolved') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `complaints_user_id_foreign` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `complaint_messages`
--

DROP TABLE IF EXISTS `complaint_messages`;
CREATE TABLE IF NOT EXISTS `complaint_messages` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `complaint_id` bigint UNSIGNED NOT NULL,
  `sender_id` bigint UNSIGNED NOT NULL,
  `sender_role` enum('complainant','super_admin') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attachment_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `complaint_messages_complaint_id_foreign` (`complaint_id`),
  KEY `complaint_messages_sender_id_foreign` (`sender_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `conversations`
--

DROP TABLE IF EXISTS `conversations`;
CREATE TABLE IF NOT EXISTS `conversations` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `buyer_id` bigint UNSIGNED NOT NULL,
  `shop_id` bigint UNSIGNED NOT NULL,
  `last_message_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `conversations_buyer_id_shop_id_unique` (`buyer_id`,`shop_id`),
  KEY `conversations_shop_id_foreign` (`shop_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `conversations`
--

INSERT INTO `conversations` (`id`, `buyer_id`, `shop_id`, `last_message_at`, `created_at`, `updated_at`) VALUES
(1, 99, 4, '2026-09-22 15:15:27', '2026-09-22 15:15:27', '2026-09-22 15:15:27'),
(2, 99, 5, '2026-09-25 13:47:29', '2026-09-25 13:47:29', '2026-09-25 13:47:29'),
(3, 97, 8, '2026-09-27 15:11:13', '2026-09-27 15:08:33', '2026-09-27 15:11:13');

-- --------------------------------------------------------

--
-- Table structure for table `courier_charges`
--

DROP TABLE IF EXISTS `courier_charges`;
CREATE TABLE IF NOT EXISTS `courier_charges` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `city_id` bigint UNSIGNED NOT NULL,
  `charge` decimal(10,2) NOT NULL,
  `extra_percent` decimal(5,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `courier_charges_city_id_foreign` (`city_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `courier_charges`
--

INSERT INTO `courier_charges` (`id`, `city_id`, `charge`, `extra_percent`, `created_at`, `updated_at`) VALUES
(1, 1, 400.00, 0.00, '2026-07-07 20:36:36', '2026-07-07 20:36:36'),
(2, 4, 200.00, 0.00, '2026-07-09 10:57:03', '2026-07-09 10:57:03'),
(3, 2, 500.00, 0.00, '2026-09-05 03:34:28', '2026-09-05 03:34:28'),
(4, 6, 1000.00, 0.00, '2026-09-24 04:36:44', '2026-09-24 04:36:44');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE IF NOT EXISTS `job_batches` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
CREATE TABLE IF NOT EXISTS `messages` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `conversation_id` bigint UNSIGNED NOT NULL,
  `sender_id` bigint UNSIGNED NOT NULL,
  `body` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `messages_conversation_id_foreign` (`conversation_id`),
  KEY `messages_sender_id_foreign` (`sender_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `conversation_id`, `sender_id`, `body`, `is_read`, `created_at`, `updated_at`) VALUES
(1, 3, 97, 'hiii', 1, '2026-09-27 15:08:39', '2026-09-27 15:09:08'),
(2, 3, 103, 'hnjiiiii', 1, '2026-09-27 15:09:12', '2026-09-27 15:10:13'),
(3, 3, 97, 'can you tell me about your products..', 1, '2026-09-27 15:10:54', '2026-09-27 15:11:08'),
(4, 3, 103, 'yes sure', 1, '2026-09-27 15:11:13', '2026-09-27 15:11:35');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_04_22_041843_create_personal_access_tokens_table', 1),
(5, '2026_04_22_060002_create_shops_table', 1),
(6, '2026_04_24_151305_modify_cnic_image_nullable_in_shops_table', 1),
(7, '2026_04_29_044018_create_rice_categories_table', 1),
(8, '2026_05_07_172157_create_rice_types_table', 1),
(9, '2026_05_12_011936_create_products_table', 1),
(10, '2026_05_12_012524_update_rice_categories_table', 1),
(11, '2026_05_12_013408_clean_rice_categories_table', 1),
(12, '2026_05_12_030839_update_products_table', 1),
(13, '2026_05_14_154048_add_status_to_shops_table', 2),
(14, '2026_05_15_033858_create_orders_table', 3),
(15, '2026_05_15_034552_create_order_items_table', 4),
(16, '2026_05_15_163909_add_status_to_order_items_table', 5),
(17, '2026_05_17_045453_create_permission_tables', 6),
(18, '2026_05_17_145945_create_notifications_table', 7),
(19, '2026_05_19_075634_remove_role_column_from_users_table', 8),
(20, '2026_06_05_162436_add_checkout_fields_to_orders_table', 9),
(21, '2026_06_07_094203_create_payments_table', 10),
(22, '2026_06_07_094808_add_order_number_to_orders_table', 11),
(24, '2026_06_07_121006_remove_columns_from_orders_table', 12),
(25, '2026_06_12_073025_create_shop_reviews_table', 13),
(28, '2026_06_18_060722_create_conversations_table', 14),
(29, '2026_06_18_060740_create_messages_table', 14),
(30, '2026_06_20_063759_add_otp_fields_to_users_table', 15),
(31, '2026_07_07_004721_create_cities_table', 16),
(32, '2026_07_07_004749_create_courier_charges_table', 16),
(33, '2026_07_10_034739_create_payment_settings_table', 17),
(34, '2026_07_11_043059_add_correction_fields_to_shops_table', 18),
(35, '2026_07_11_043232_add_city_and_cnic_back_image_to_shops_table', 18),
(36, '2026_08_10_064333_add_city_id_and_delivery_charge_to_orders_table', 19),
(37, '2026_08_19_060930_create_seller_payouts_table', 20),
(38, '2026_08_19_060424_add_commission_fields_to_order_items_table', 21),
(40, '2026_08_19_061023_add_payout_fields_to_shops_table', 22),
(41, '2026_08_24_044033_create_complaints_table', 23),
(42, '2026_08_24_044525_create_complaint_messages_table', 24),
(43, '2026_08_24_044712_create_settings_table', 25),
(44, '2026_08_26_060709_add_account_status_to_users_table', 26),
(45, '2026_08_26_060916_add_is_active_to_products_table', 27),
(46, '2026_08_26_061101_create_seller_removals_table', 28),
(47, '2026_08_26_061256_create_banned_emails_table', 29),
(48, '2026_08_26_133633_add_rejection_reason_to_shops_table', 30),
(49, '2026_08_11_151917_create_notifications_table', 31),
(50, '2026_09_06_121127_add_delivery_charge_to_seller_payouts_table', 32),
(51, '2026_09_14_062930_make_image_nullable_on_rice_categories_table', 33),
(52, '2026_09_17_020949_create_commission_settings_table', 34),
(53, '2026_09_20_070829_add_cleared_at_to_notifications_app_table', 35);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

DROP TABLE IF EXISTS `model_has_permissions`;
CREATE TABLE IF NOT EXISTS `model_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_permissions`
--

INSERT INTO `model_has_permissions` (`permission_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 15),
(2, 'App\\Models\\User', 15),
(3, 'App\\Models\\User', 15),
(4, 'App\\Models\\User', 15),
(5, 'App\\Models\\User', 15),
(6, 'App\\Models\\User', 15),
(7, 'App\\Models\\User', 15),
(8, 'App\\Models\\User', 15),
(9, 'App\\Models\\User', 15),
(10, 'App\\Models\\User', 15),
(11, 'App\\Models\\User', 15),
(12, 'App\\Models\\User', 15),
(13, 'App\\Models\\User', 15),
(14, 'App\\Models\\User', 15),
(15, 'App\\Models\\User', 15),
(16, 'App\\Models\\User', 15),
(17, 'App\\Models\\User', 15),
(18, 'App\\Models\\User', 15),
(19, 'App\\Models\\User', 15),
(20, 'App\\Models\\User', 15),
(21, 'App\\Models\\User', 15),
(22, 'App\\Models\\User', 15),
(23, 'App\\Models\\User', 15),
(24, 'App\\Models\\User', 15),
(25, 'App\\Models\\User', 15),
(26, 'App\\Models\\User', 15),
(27, 'App\\Models\\User', 15),
(28, 'App\\Models\\User', 15),
(29, 'App\\Models\\User', 15),
(30, 'App\\Models\\User', 15),
(31, 'App\\Models\\User', 15),
(32, 'App\\Models\\User', 15),
(33, 'App\\Models\\User', 15),
(34, 'App\\Models\\User', 15),
(35, 'App\\Models\\User', 15),
(36, 'App\\Models\\User', 15),
(37, 'App\\Models\\User', 15),
(38, 'App\\Models\\User', 15),
(39, 'App\\Models\\User', 15),
(40, 'App\\Models\\User', 15),
(41, 'App\\Models\\User', 15),
(42, 'App\\Models\\User', 15),
(43, 'App\\Models\\User', 15),
(44, 'App\\Models\\User', 15),
(45, 'App\\Models\\User', 15),
(46, 'App\\Models\\User', 15),
(47, 'App\\Models\\User', 15),
(48, 'App\\Models\\User', 15),
(49, 'App\\Models\\User', 15),
(50, 'App\\Models\\User', 15),
(51, 'App\\Models\\User', 15),
(52, 'App\\Models\\User', 15),
(53, 'App\\Models\\User', 15),
(54, 'App\\Models\\User', 15),
(55, 'App\\Models\\User', 15),
(56, 'App\\Models\\User', 15),
(57, 'App\\Models\\User', 15),
(58, 'App\\Models\\User', 15),
(59, 'App\\Models\\User', 15),
(60, 'App\\Models\\User', 15),
(61, 'App\\Models\\User', 15),
(62, 'App\\Models\\User', 15),
(63, 'App\\Models\\User', 15),
(64, 'App\\Models\\User', 15),
(65, 'App\\Models\\User', 15),
(66, 'App\\Models\\User', 15),
(67, 'App\\Models\\User', 15),
(68, 'App\\Models\\User', 15),
(69, 'App\\Models\\User', 15),
(70, 'App\\Models\\User', 15),
(71, 'App\\Models\\User', 15),
(72, 'App\\Models\\User', 15),
(73, 'App\\Models\\User', 15),
(74, 'App\\Models\\User', 15),
(75, 'App\\Models\\User', 15),
(76, 'App\\Models\\User', 15),
(77, 'App\\Models\\User', 15),
(78, 'App\\Models\\User', 15),
(79, 'App\\Models\\User', 15),
(80, 'App\\Models\\User', 15),
(81, 'App\\Models\\User', 15),
(82, 'App\\Models\\User', 15),
(83, 'App\\Models\\User', 15),
(84, 'App\\Models\\User', 15),
(85, 'App\\Models\\User', 15),
(86, 'App\\Models\\User', 15),
(87, 'App\\Models\\User', 15),
(88, 'App\\Models\\User', 15),
(89, 'App\\Models\\User', 15),
(90, 'App\\Models\\User', 15),
(91, 'App\\Models\\User', 15),
(92, 'App\\Models\\User', 15),
(93, 'App\\Models\\User', 15),
(94, 'App\\Models\\User', 15),
(95, 'App\\Models\\User', 15),
(96, 'App\\Models\\User', 15),
(97, 'App\\Models\\User', 15),
(98, 'App\\Models\\User', 15),
(99, 'App\\Models\\User', 15),
(100, 'App\\Models\\User', 15),
(101, 'App\\Models\\User', 15),
(102, 'App\\Models\\User', 15),
(103, 'App\\Models\\User', 15),
(104, 'App\\Models\\User', 15),
(105, 'App\\Models\\User', 15),
(106, 'App\\Models\\User', 15),
(107, 'App\\Models\\User', 15),
(108, 'App\\Models\\User', 15),
(109, 'App\\Models\\User', 15),
(110, 'App\\Models\\User', 15),
(111, 'App\\Models\\User', 15),
(112, 'App\\Models\\User', 15),
(113, 'App\\Models\\User', 15),
(114, 'App\\Models\\User', 15),
(115, 'App\\Models\\User', 15),
(116, 'App\\Models\\User', 15),
(117, 'App\\Models\\User', 15),
(118, 'App\\Models\\User', 15),
(119, 'App\\Models\\User', 15),
(120, 'App\\Models\\User', 15),
(121, 'App\\Models\\User', 15),
(122, 'App\\Models\\User', 15),
(123, 'App\\Models\\User', 15),
(124, 'App\\Models\\User', 15);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

DROP TABLE IF EXISTS `model_has_roles`;
CREATE TABLE IF NOT EXISTS `model_has_roles` (
  `role_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1),
(2, 'App\\Models\\User', 2),
(3, 'App\\Models\\User', 4),
(8, 'App\\Models\\User', 5),
(1, 'App\\Models\\User', 7),
(3, 'App\\Models\\User', 8),
(3, 'App\\Models\\User', 9),
(3, 'App\\Models\\User', 10),
(8, 'App\\Models\\User', 13),
(8, 'App\\Models\\User', 14),
(1, 'App\\Models\\User', 15),
(2, 'App\\Models\\User', 16),
(3, 'App\\Models\\User', 17),
(3, 'App\\Models\\User', 18),
(3, 'App\\Models\\User', 19),
(3, 'App\\Models\\User', 20),
(8, 'App\\Models\\User', 21),
(8, 'App\\Models\\User', 22),
(8, 'App\\Models\\User', 23),
(8, 'App\\Models\\User', 24),
(8, 'App\\Models\\User', 25),
(8, 'App\\Models\\User', 26),
(8, 'App\\Models\\User', 28),
(8, 'App\\Models\\User', 30),
(3, 'App\\Models\\User', 31),
(3, 'App\\Models\\User', 32),
(8, 'App\\Models\\User', 33),
(1, 'App\\Models\\User', 34),
(8, 'App\\Models\\User', 36),
(8, 'App\\Models\\User', 39),
(8, 'App\\Models\\User', 40),
(8, 'App\\Models\\User', 41),
(8, 'App\\Models\\User', 42),
(8, 'App\\Models\\User', 43),
(8, 'App\\Models\\User', 44),
(8, 'App\\Models\\User', 45),
(8, 'App\\Models\\User', 46),
(8, 'App\\Models\\User', 47),
(8, 'App\\Models\\User', 48),
(8, 'App\\Models\\User', 49),
(8, 'App\\Models\\User', 50),
(8, 'App\\Models\\User', 51),
(8, 'App\\Models\\User', 52),
(8, 'App\\Models\\User', 53),
(8, 'App\\Models\\User', 54),
(8, 'App\\Models\\User', 55),
(8, 'App\\Models\\User', 56),
(8, 'App\\Models\\User', 57),
(8, 'App\\Models\\User', 58),
(8, 'App\\Models\\User', 59),
(8, 'App\\Models\\User', 60),
(8, 'App\\Models\\User', 61),
(8, 'App\\Models\\User', 62),
(8, 'App\\Models\\User', 63),
(8, 'App\\Models\\User', 64),
(8, 'App\\Models\\User', 65),
(8, 'App\\Models\\User', 66),
(8, 'App\\Models\\User', 67),
(8, 'App\\Models\\User', 68),
(8, 'App\\Models\\User', 69),
(8, 'App\\Models\\User', 70),
(8, 'App\\Models\\User', 71),
(8, 'App\\Models\\User', 72),
(8, 'App\\Models\\User', 73),
(8, 'App\\Models\\User', 74),
(8, 'App\\Models\\User', 75),
(8, 'App\\Models\\User', 76),
(8, 'App\\Models\\User', 77),
(8, 'App\\Models\\User', 78),
(8, 'App\\Models\\User', 79),
(8, 'App\\Models\\User', 80),
(3, 'App\\Models\\User', 81),
(2, 'App\\Models\\User', 82),
(8, 'App\\Models\\User', 83),
(8, 'App\\Models\\User', 84),
(8, 'App\\Models\\User', 85),
(3, 'App\\Models\\User', 86),
(8, 'App\\Models\\User', 87),
(8, 'App\\Models\\User', 88),
(8, 'App\\Models\\User', 89),
(3, 'App\\Models\\User', 90),
(8, 'App\\Models\\User', 91),
(1, 'App\\Models\\User', 92),
(8, 'App\\Models\\User', 93),
(8, 'App\\Models\\User', 94),
(8, 'App\\Models\\User', 95),
(8, 'App\\Models\\User', 96),
(8, 'App\\Models\\User', 97),
(8, 'App\\Models\\User', 98),
(8, 'App\\Models\\User', 99),
(8, 'App\\Models\\User', 100),
(8, 'App\\Models\\User', 101),
(2, 'App\\Models\\User', 102),
(3, 'App\\Models\\User', 103);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_user_id_foreign` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications_app`
--

DROP TABLE IF EXISTS `notifications_app`;
CREATE TABLE IF NOT EXISTS `notifications_app` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `body` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `data` json DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `read_at` timestamp NULL DEFAULT NULL,
  `cleared_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_app_user_id_is_read_index` (`user_id`,`is_read`)
) ENGINE=InnoDB AUTO_INCREMENT=109 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications_app`
--

INSERT INTO `notifications_app` (`id`, `user_id`, `type`, `title`, `body`, `data`, `is_read`, `read_at`, `cleared_at`, `created_at`, `updated_at`) VALUES
(1, 15, 'shop_pending', 'New shop pending approval', 'Anwar e Sidra has submitted a shop for approval.', '{\"shop_id\": 1}', 1, '2026-09-20 16:19:25', NULL, '2026-09-20 15:35:02', '2026-09-20 16:19:25'),
(2, 34, 'shop_pending', 'New shop pending approval', 'Anwar e Sidra has submitted a shop for approval.', '{\"shop_id\": 1}', 1, '2026-09-20 15:36:30', NULL, '2026-09-20 15:35:02', '2026-09-20 15:36:30'),
(3, 82, 'shop_pending', 'New shop pending approval', 'Anwar e Sidra has submitted a shop for approval.', '{\"shop_id\": 1}', 0, NULL, NULL, '2026-09-20 15:35:02', '2026-09-20 15:35:02'),
(4, 81, 'shop_status', 'Shop approved', 'Your shop \"Anwar e Sidra\" has been approved.', '{\"shop_id\": 1}', 1, '2026-09-21 16:04:22', NULL, '2026-09-20 15:36:36', '2026-09-21 16:04:22'),
(5, 15, 'shop_pending', 'New shop pending approval', 'Samia has submitted a shop for approval.', '{\"shop_id\": 2}', 1, '2026-09-20 16:18:15', NULL, '2026-09-20 16:13:50', '2026-09-20 16:18:15'),
(6, 34, 'shop_pending', 'New shop pending approval', 'Samia has submitted a shop for approval.', '{\"shop_id\": 2}', 1, '2026-09-20 16:38:09', NULL, '2026-09-20 16:13:50', '2026-09-20 16:38:09'),
(7, 82, 'shop_pending', 'New shop pending approval', 'Samia has submitted a shop for approval.', '{\"shop_id\": 2}', 0, NULL, NULL, '2026-09-20 16:13:50', '2026-09-20 16:13:50'),
(8, 83, 'shop_status', 'Shop approved', 'Your shop \"Samia\" has been approved.', '{\"shop_id\": 2}', 1, '2026-09-20 16:21:46', '2026-09-20 16:24:24', '2026-09-20 16:18:22', '2026-09-20 16:24:24'),
(9, 15, 'shop_pending', 'New shop pending approval', 'yousaf traders has submitted a shop for approval.', '{\"shop_id\": 3}', 1, '2026-09-25 13:38:01', NULL, '2026-09-21 15:24:35', '2026-09-25 13:38:01'),
(10, 34, 'shop_pending', 'New shop pending approval', 'yousaf traders has submitted a shop for approval.', '{\"shop_id\": 3}', 1, '2026-09-21 15:27:06', NULL, '2026-09-21 15:24:35', '2026-09-21 15:27:06'),
(11, 82, 'shop_pending', 'New shop pending approval', 'yousaf traders has submitted a shop for approval.', '{\"shop_id\": 3}', 1, '2026-09-25 16:08:24', NULL, '2026-09-21 15:24:35', '2026-09-25 16:08:24'),
(12, 15, 'shop_pending', 'New shop pending approval', 'Al hadi Enterprises has submitted a shop for approval.', '{\"shop_id\": 4}', 1, '2026-09-25 13:38:06', NULL, '2026-09-21 15:41:29', '2026-09-25 13:38:06'),
(13, 34, 'shop_pending', 'New shop pending approval', 'Al hadi Enterprises has submitted a shop for approval.', '{\"shop_id\": 4}', 1, '2026-09-21 15:53:29', NULL, '2026-09-21 15:41:29', '2026-09-21 15:53:29'),
(14, 82, 'shop_pending', 'New shop pending approval', 'Al hadi Enterprises has submitted a shop for approval.', '{\"shop_id\": 4}', 0, NULL, NULL, '2026-09-21 15:41:29', '2026-09-21 15:41:29'),
(15, 15, 'shop_pending', 'New shop pending approval', 'Ayub rice traders has submitted a shop for approval.', '{\"shop_id\": 5}', 1, '2026-09-25 13:38:34', NULL, '2026-09-21 15:47:50', '2026-09-25 13:38:34'),
(16, 34, 'shop_pending', 'New shop pending approval', 'Ayub rice traders has submitted a shop for approval.', '{\"shop_id\": 5}', 1, '2026-09-21 15:57:44', NULL, '2026-09-21 15:47:50', '2026-09-21 15:57:44'),
(17, 82, 'shop_pending', 'New shop pending approval', 'Ayub rice traders has submitted a shop for approval.', '{\"shop_id\": 5}', 0, NULL, NULL, '2026-09-21 15:47:50', '2026-09-21 15:47:50'),
(18, 15, 'shop_pending', 'New shop pending approval', 'Ayub Rice traders has submitted a shop for approval.', '{\"shop_id\": 6}', 1, '2026-09-25 13:38:39', NULL, '2026-09-21 15:52:01', '2026-09-25 13:38:39'),
(19, 34, 'shop_pending', 'New shop pending approval', 'Ayub Rice traders has submitted a shop for approval.', '{\"shop_id\": 6}', 1, '2026-09-21 15:57:42', NULL, '2026-09-21 15:52:01', '2026-09-21 15:57:42'),
(20, 82, 'shop_pending', 'New shop pending approval', 'Ayub Rice traders has submitted a shop for approval.', '{\"shop_id\": 6}', 0, NULL, NULL, '2026-09-21 15:52:01', '2026-09-21 15:52:01'),
(21, 90, 'shop_status', 'Shop approved', 'Your shop \"Al hadi Enterprises\" has been approved.', '{\"shop_id\": 4}', 1, '2026-09-24 02:21:50', NULL, '2026-09-21 15:53:22', '2026-09-24 02:21:50'),
(22, 86, 'shop_status', 'Shop approved', 'Your shop \"Ayub rice traders\" has been approved.', '{\"shop_id\": 5}', 1, '2026-09-24 04:45:39', NULL, '2026-09-21 15:53:42', '2026-09-24 04:45:39'),
(23, 88, 'shop_status', 'Shop rejected', 'Your shop \"Ayub Rice traders\" was rejected. Reason: Already has the shop Shafi Rice traders and also the owner name is shafi .Your cnic also blur....Plz enter clear picture of CNIC', '{\"shop_id\": 6, \"shop_status\": \"rejected\"}', 1, '2026-09-23 15:37:00', NULL, '2026-09-21 15:55:59', '2026-09-23 15:37:00'),
(24, 15, 'payment_pending', 'New payment submitted', 'Order ORD-20260923-F4F39D has a payment awaiting approval.', '{\"order_id\": 53}', 1, '2026-09-25 13:38:14', NULL, '2026-09-23 05:37:51', '2026-09-25 13:38:14'),
(25, 34, 'payment_pending', 'New payment submitted', 'Order ORD-20260923-F4F39D has a payment awaiting approval.', '{\"order_id\": 53}', 1, '2026-09-23 05:48:47', NULL, '2026-09-23 05:37:51', '2026-09-23 05:48:47'),
(26, 92, 'payment_pending', 'New payment submitted', 'Order ORD-20260923-F4F39D has a payment awaiting approval.', '{\"order_id\": 53}', 0, NULL, NULL, '2026-09-23 05:37:51', '2026-09-23 05:37:51'),
(27, 82, 'payment_pending', 'New payment submitted', 'Order ORD-20260923-F4F39D has a payment awaiting approval.', '{\"order_id\": 53}', 0, NULL, NULL, '2026-09-23 05:37:51', '2026-09-23 05:37:51'),
(28, 15, 'payment_pending', 'New payment submitted', 'Order ORD-20260923-30A59D has a payment awaiting approval.', '{\"order_id\": 54}', 1, '2026-09-25 13:38:09', NULL, '2026-09-23 05:48:19', '2026-09-25 13:38:09'),
(29, 34, 'payment_pending', 'New payment submitted', 'Order ORD-20260923-30A59D has a payment awaiting approval.', '{\"order_id\": 54}', 1, '2026-09-23 05:50:23', NULL, '2026-09-23 05:48:19', '2026-09-23 05:50:23'),
(30, 92, 'payment_pending', 'New payment submitted', 'Order ORD-20260923-30A59D has a payment awaiting approval.', '{\"order_id\": 54}', 0, NULL, NULL, '2026-09-23 05:48:19', '2026-09-23 05:48:19'),
(31, 82, 'payment_pending', 'New payment submitted', 'Order ORD-20260923-30A59D has a payment awaiting approval.', '{\"order_id\": 54}', 0, NULL, NULL, '2026-09-23 05:48:19', '2026-09-23 05:48:19'),
(32, 15, 'payment_pending', 'New payment submitted', 'Order ORD-20260923-1C475D has a payment awaiting approval.', '{\"order_id\": 55}', 1, '2026-09-25 13:38:17', NULL, '2026-09-23 14:36:17', '2026-09-25 13:38:17'),
(33, 34, 'payment_pending', 'New payment submitted', 'Order ORD-20260923-1C475D has a payment awaiting approval.', '{\"order_id\": 55}', 1, '2026-09-23 15:14:13', NULL, '2026-09-23 14:36:17', '2026-09-23 15:14:13'),
(34, 92, 'payment_pending', 'New payment submitted', 'Order ORD-20260923-1C475D has a payment awaiting approval.', '{\"order_id\": 55}', 0, NULL, NULL, '2026-09-23 14:36:17', '2026-09-23 14:36:17'),
(35, 82, 'payment_pending', 'New payment submitted', 'Order ORD-20260923-1C475D has a payment awaiting approval.', '{\"order_id\": 55}', 0, NULL, NULL, '2026-09-23 14:36:17', '2026-09-23 14:36:17'),
(36, 15, 'payment_pending', 'New payment submitted', 'Order ORD-20260923-574501 has a payment awaiting approval.', '{\"order_id\": 56}', 1, '2026-09-25 13:38:21', NULL, '2026-09-23 14:37:41', '2026-09-25 13:38:21'),
(37, 34, 'payment_pending', 'New payment submitted', 'Order ORD-20260923-574501 has a payment awaiting approval.', '{\"order_id\": 56}', 1, '2026-09-23 15:13:54', NULL, '2026-09-23 14:37:41', '2026-09-23 15:13:54'),
(38, 92, 'payment_pending', 'New payment submitted', 'Order ORD-20260923-574501 has a payment awaiting approval.', '{\"order_id\": 56}', 0, NULL, NULL, '2026-09-23 14:37:41', '2026-09-23 14:37:41'),
(39, 82, 'payment_pending', 'New payment submitted', 'Order ORD-20260923-574501 has a payment awaiting approval.', '{\"order_id\": 56}', 0, NULL, NULL, '2026-09-23 14:37:41', '2026-09-23 14:37:41'),
(40, 15, 'shop_pending', 'New shop pending approval', 'gsushs has submitted a shop for approval.', '{\"shop_id\": 7}', 1, '2026-09-25 13:38:25', NULL, '2026-09-23 15:39:53', '2026-09-25 13:38:25'),
(41, 34, 'shop_pending', 'New shop pending approval', 'gsushs has submitted a shop for approval.', '{\"shop_id\": 7}', 1, '2026-09-23 15:40:32', NULL, '2026-09-23 15:39:53', '2026-09-23 15:40:32'),
(42, 92, 'shop_pending', 'New shop pending approval', 'gsushs has submitted a shop for approval.', '{\"shop_id\": 7}', 0, NULL, NULL, '2026-09-23 15:39:53', '2026-09-23 15:39:53'),
(43, 82, 'shop_pending', 'New shop pending approval', 'gsushs has submitted a shop for approval.', '{\"shop_id\": 7}', 0, NULL, NULL, '2026-09-23 15:39:53', '2026-09-23 15:39:53'),
(44, 62, 'shop_status', 'Shop approved', 'Your shop \"gsushs\" has been approved.', '{\"shop_id\": 7}', 1, '2026-09-23 15:45:00', NULL, '2026-09-23 15:40:39', '2026-09-23 15:45:00'),
(45, 15, 'shop_pending', 'Shop resubmitted', 'yousaf traders was updated by the seller and needs re-review.', '{\"shop_id\": 3}', 1, '2026-09-25 13:38:28', NULL, '2026-09-24 04:47:31', '2026-09-25 13:38:28'),
(46, 34, 'shop_pending', 'Shop resubmitted', 'yousaf traders was updated by the seller and needs re-review.', '{\"shop_id\": 3}', 1, '2026-09-24 04:47:48', NULL, '2026-09-24 04:47:31', '2026-09-24 04:47:48'),
(47, 92, 'shop_pending', 'Shop resubmitted', 'yousaf traders was updated by the seller and needs re-review.', '{\"shop_id\": 3}', 0, NULL, NULL, '2026-09-24 04:47:31', '2026-09-24 04:47:31'),
(48, 82, 'shop_pending', 'Shop resubmitted', 'yousaf traders was updated by the seller and needs re-review.', '{\"shop_id\": 3}', 0, NULL, NULL, '2026-09-24 04:47:31', '2026-09-24 04:47:31'),
(49, 15, 'payment_pending', 'New payment submitted', 'Order ORD-20260926-7DF945 has a payment awaiting approval.', '{\"order_id\": 57}', 0, NULL, NULL, '2026-09-26 07:49:27', '2026-09-26 07:49:27'),
(50, 34, 'payment_pending', 'New payment submitted', 'Order ORD-20260926-7DF945 has a payment awaiting approval.', '{\"order_id\": 57}', 1, '2026-09-26 07:57:38', NULL, '2026-09-26 07:49:27', '2026-09-26 07:57:38'),
(51, 92, 'payment_pending', 'New payment submitted', 'Order ORD-20260926-7DF945 has a payment awaiting approval.', '{\"order_id\": 57}', 0, NULL, NULL, '2026-09-26 07:49:27', '2026-09-26 07:49:27'),
(52, 82, 'payment_pending', 'New payment submitted', 'Order ORD-20260926-7DF945 has a payment awaiting approval.', '{\"order_id\": 57}', 0, NULL, NULL, '2026-09-26 07:49:27', '2026-09-26 07:49:27'),
(53, 102, 'payment_pending', 'New payment submitted', 'Order ORD-20260926-7DF945 has a payment awaiting approval.', '{\"order_id\": 57}', 0, NULL, NULL, '2026-09-26 07:49:27', '2026-09-26 07:49:27'),
(54, 15, 'payment_pending', 'New payment submitted', 'Order ORD-20260926-1B9789 has a payment awaiting approval.', '{\"order_id\": 58}', 0, NULL, NULL, '2026-09-26 07:51:45', '2026-09-26 07:51:45'),
(55, 34, 'payment_pending', 'New payment submitted', 'Order ORD-20260926-1B9789 has a payment awaiting approval.', '{\"order_id\": 58}', 1, '2026-09-26 07:57:32', NULL, '2026-09-26 07:51:45', '2026-09-26 07:57:32'),
(56, 92, 'payment_pending', 'New payment submitted', 'Order ORD-20260926-1B9789 has a payment awaiting approval.', '{\"order_id\": 58}', 0, NULL, NULL, '2026-09-26 07:51:45', '2026-09-26 07:51:45'),
(57, 82, 'payment_pending', 'New payment submitted', 'Order ORD-20260926-1B9789 has a payment awaiting approval.', '{\"order_id\": 58}', 0, NULL, NULL, '2026-09-26 07:51:45', '2026-09-26 07:51:45'),
(58, 102, 'payment_pending', 'New payment submitted', 'Order ORD-20260926-1B9789 has a payment awaiting approval.', '{\"order_id\": 58}', 0, NULL, NULL, '2026-09-26 07:51:45', '2026-09-26 07:51:45'),
(59, 15, 'payment_pending', 'New payment submitted', 'Order ORD-20260926-9E52B2 has a payment awaiting approval.', '{\"order_id\": 59}', 0, NULL, NULL, '2026-09-26 07:54:33', '2026-09-26 07:54:33'),
(60, 34, 'payment_pending', 'New payment submitted', 'Order ORD-20260926-9E52B2 has a payment awaiting approval.', '{\"order_id\": 59}', 1, '2026-09-26 07:57:28', NULL, '2026-09-26 07:54:33', '2026-09-26 07:57:28'),
(61, 92, 'payment_pending', 'New payment submitted', 'Order ORD-20260926-9E52B2 has a payment awaiting approval.', '{\"order_id\": 59}', 0, NULL, NULL, '2026-09-26 07:54:33', '2026-09-26 07:54:33'),
(62, 82, 'payment_pending', 'New payment submitted', 'Order ORD-20260926-9E52B2 has a payment awaiting approval.', '{\"order_id\": 59}', 0, NULL, NULL, '2026-09-26 07:54:33', '2026-09-26 07:54:33'),
(63, 102, 'payment_pending', 'New payment submitted', 'Order ORD-20260926-9E52B2 has a payment awaiting approval.', '{\"order_id\": 59}', 0, NULL, NULL, '2026-09-26 07:54:33', '2026-09-26 07:54:33'),
(64, 15, 'payment_pending', 'New payment submitted', 'Order ORD-20260926-3ABB78 has a payment awaiting approval.', '{\"order_id\": 60}', 0, NULL, NULL, '2026-09-26 07:55:15', '2026-09-26 07:55:15'),
(65, 34, 'payment_pending', 'New payment submitted', 'Order ORD-20260926-3ABB78 has a payment awaiting approval.', '{\"order_id\": 60}', 1, '2026-09-26 07:57:19', NULL, '2026-09-26 07:55:15', '2026-09-26 07:57:19'),
(66, 92, 'payment_pending', 'New payment submitted', 'Order ORD-20260926-3ABB78 has a payment awaiting approval.', '{\"order_id\": 60}', 0, NULL, NULL, '2026-09-26 07:55:15', '2026-09-26 07:55:15'),
(67, 82, 'payment_pending', 'New payment submitted', 'Order ORD-20260926-3ABB78 has a payment awaiting approval.', '{\"order_id\": 60}', 0, NULL, NULL, '2026-09-26 07:55:15', '2026-09-26 07:55:15'),
(68, 102, 'payment_pending', 'New payment submitted', 'Order ORD-20260926-3ABB78 has a payment awaiting approval.', '{\"order_id\": 60}', 0, NULL, NULL, '2026-09-26 07:55:15', '2026-09-26 07:55:15'),
(69, 15, 'payment_pending', 'New payment submitted', 'Order ORD-20260926-515A07 has a payment awaiting approval.', '{\"order_id\": 61}', 0, NULL, NULL, '2026-09-26 15:03:49', '2026-09-26 15:03:49'),
(70, 34, 'payment_pending', 'New payment submitted', 'Order ORD-20260926-515A07 has a payment awaiting approval.', '{\"order_id\": 61}', 1, '2026-09-26 15:25:03', NULL, '2026-09-26 15:03:49', '2026-09-26 15:25:03'),
(71, 92, 'payment_pending', 'New payment submitted', 'Order ORD-20260926-515A07 has a payment awaiting approval.', '{\"order_id\": 61}', 0, NULL, NULL, '2026-09-26 15:03:49', '2026-09-26 15:03:49'),
(72, 82, 'payment_pending', 'New payment submitted', 'Order ORD-20260926-515A07 has a payment awaiting approval.', '{\"order_id\": 61}', 0, NULL, NULL, '2026-09-26 15:03:49', '2026-09-26 15:03:49'),
(73, 102, 'payment_pending', 'New payment submitted', 'Order ORD-20260926-515A07 has a payment awaiting approval.', '{\"order_id\": 61}', 0, NULL, NULL, '2026-09-26 15:03:49', '2026-09-26 15:03:49'),
(74, 81, 'order_placed', 'New order received', 'You have a new order: ORD-20260926-515A07', '{\"order_id\": 61}', 1, '2026-09-26 15:13:15', NULL, '2026-09-26 15:04:37', '2026-09-26 15:13:15'),
(75, 81, 'payment_status', 'Payment verified', 'Payment for order ORD-20260926-515A07 has been verified. You can start preparing the order.', '{\"order_id\": 61}', 1, '2026-09-26 15:13:09', NULL, '2026-09-26 15:04:37', '2026-09-26 15:13:09'),
(76, 99, 'payment_status', 'Payment confirmed', 'Your payment for order ORD-20260926-515A07 has been confirmed.', '{\"order_id\": 61}', 1, '2026-09-26 15:04:48', NULL, '2026-09-26 15:04:37', '2026-09-26 15:04:48'),
(77, 15, 'payment_pending', 'New payment submitted', 'Order ORD-20260926-9C3425 has a payment awaiting approval.', '{\"order_id\": 62}', 0, NULL, NULL, '2026-09-26 15:07:21', '2026-09-26 15:07:21'),
(78, 34, 'payment_pending', 'New payment submitted', 'Order ORD-20260926-9C3425 has a payment awaiting approval.', '{\"order_id\": 62}', 1, '2026-09-26 15:25:03', NULL, '2026-09-26 15:07:21', '2026-09-26 15:25:03'),
(79, 92, 'payment_pending', 'New payment submitted', 'Order ORD-20260926-9C3425 has a payment awaiting approval.', '{\"order_id\": 62}', 0, NULL, NULL, '2026-09-26 15:07:21', '2026-09-26 15:07:21'),
(80, 82, 'payment_pending', 'New payment submitted', 'Order ORD-20260926-9C3425 has a payment awaiting approval.', '{\"order_id\": 62}', 0, NULL, NULL, '2026-09-26 15:07:21', '2026-09-26 15:07:21'),
(81, 102, 'payment_pending', 'New payment submitted', 'Order ORD-20260926-9C3425 has a payment awaiting approval.', '{\"order_id\": 62}', 0, NULL, NULL, '2026-09-26 15:07:21', '2026-09-26 15:07:21'),
(82, 15, 'payment_pending', 'New payment submitted', 'Order ORD-20260926-7E833B has a payment awaiting approval.', '{\"order_id\": 63}', 0, NULL, NULL, '2026-09-26 15:08:23', '2026-09-26 15:08:23'),
(83, 34, 'payment_pending', 'New payment submitted', 'Order ORD-20260926-7E833B has a payment awaiting approval.', '{\"order_id\": 63}', 1, '2026-09-26 15:10:09', NULL, '2026-09-26 15:08:23', '2026-09-26 15:10:09'),
(84, 92, 'payment_pending', 'New payment submitted', 'Order ORD-20260926-7E833B has a payment awaiting approval.', '{\"order_id\": 63}', 0, NULL, NULL, '2026-09-26 15:08:23', '2026-09-26 15:08:23'),
(85, 82, 'payment_pending', 'New payment submitted', 'Order ORD-20260926-7E833B has a payment awaiting approval.', '{\"order_id\": 63}', 0, NULL, NULL, '2026-09-26 15:08:23', '2026-09-26 15:08:23'),
(86, 102, 'payment_pending', 'New payment submitted', 'Order ORD-20260926-7E833B has a payment awaiting approval.', '{\"order_id\": 63}', 0, NULL, NULL, '2026-09-26 15:08:23', '2026-09-26 15:08:23'),
(87, 81, 'order_placed', 'New order received', 'You have a new order: ORD-20260926-7E833B', '{\"order_id\": 63}', 1, '2026-09-26 15:13:29', NULL, '2026-09-26 15:09:02', '2026-09-26 15:13:29'),
(88, 81, 'payment_status', 'Payment verified', 'Payment for order ORD-20260926-7E833B has been verified. You can start preparing the order.', '{\"order_id\": 63}', 1, '2026-09-26 15:13:18', NULL, '2026-09-26 15:09:02', '2026-09-26 15:13:18'),
(89, 99, 'payment_status', 'Payment confirmed', 'Your payment for order ORD-20260926-7E833B has been confirmed.', '{\"order_id\": 63}', 1, '2026-09-26 15:11:05', NULL, '2026-09-26 15:09:02', '2026-09-26 15:11:05'),
(90, 15, 'payment_pending', 'New payment submitted', 'Order ORD-20260926-53A349 has a payment awaiting approval.', '{\"order_id\": 64}', 0, NULL, NULL, '2026-09-26 15:23:17', '2026-09-26 15:23:17'),
(91, 34, 'payment_pending', 'New payment submitted', 'Order ORD-20260926-53A349 has a payment awaiting approval.', '{\"order_id\": 64}', 1, '2026-09-26 15:25:03', NULL, '2026-09-26 15:23:17', '2026-09-26 15:25:03'),
(92, 92, 'payment_pending', 'New payment submitted', 'Order ORD-20260926-53A349 has a payment awaiting approval.', '{\"order_id\": 64}', 0, NULL, NULL, '2026-09-26 15:23:17', '2026-09-26 15:23:17'),
(93, 82, 'payment_pending', 'New payment submitted', 'Order ORD-20260926-53A349 has a payment awaiting approval.', '{\"order_id\": 64}', 0, NULL, NULL, '2026-09-26 15:23:17', '2026-09-26 15:23:17'),
(94, 102, 'payment_pending', 'New payment submitted', 'Order ORD-20260926-53A349 has a payment awaiting approval.', '{\"order_id\": 64}', 0, NULL, NULL, '2026-09-26 15:23:17', '2026-09-26 15:23:17'),
(95, 81, 'order_placed', 'New order received', 'You have a new order: ORD-20260926-53A349', '{\"order_id\": 64}', 1, '2026-09-26 15:24:17', NULL, '2026-09-26 15:23:35', '2026-09-26 15:24:17'),
(96, 81, 'payment_status', 'Payment verified', 'Payment for order ORD-20260926-53A349 has been verified. You can start preparing the order.', '{\"order_id\": 64}', 1, '2026-09-26 15:24:14', NULL, '2026-09-26 15:23:35', '2026-09-26 15:24:14'),
(97, 99, 'payment_status', 'Payment confirmed', 'Your payment for order ORD-20260926-53A349 has been confirmed.', '{\"order_id\": 64}', 1, '2026-09-26 15:23:44', NULL, '2026-09-26 15:23:35', '2026-09-26 15:23:44'),
(98, 103, 'shop_status', 'Shop created', 'Your shop \"test shop\" has been created and approved by admin.', '{\"shop_id\": 8}', 1, '2026-09-27 15:09:05', NULL, '2026-09-27 13:06:46', '2026-09-27 15:09:05'),
(99, 103, 'chat_message', 'New message', 'Sidra sent you a message', '{\"conversation_id\": 3}', 1, '2026-09-27 15:09:07', NULL, '2026-09-27 15:08:39', '2026-09-27 15:09:07'),
(100, 97, 'chat_message', 'New message', 'test shop sent you a message', '{\"conversation_id\": 3}', 1, '2026-09-27 15:10:13', NULL, '2026-09-27 15:09:12', '2026-09-27 15:10:13'),
(101, 103, 'chat_message', 'New message', 'Sidra sent you a message', '{\"conversation_id\": 3}', 1, '2026-09-27 15:11:07', NULL, '2026-09-27 15:10:54', '2026-09-27 15:11:07'),
(102, 97, 'chat_message', 'New message', 'test shop sent you a message', '{\"conversation_id\": 3}', 1, '2026-09-27 15:11:34', NULL, '2026-09-27 15:11:13', '2026-09-27 15:11:34'),
(103, 15, 'shop_pending', 'Shop resubmitted', 'test shop was updated by the seller and needs re-review.', '{\"shop_id\": 8}', 0, NULL, NULL, '2026-09-27 15:13:09', '2026-09-27 15:13:09'),
(104, 34, 'shop_pending', 'Shop resubmitted', 'test shop was updated by the seller and needs re-review.', '{\"shop_id\": 8}', 1, '2026-09-27 15:16:52', NULL, '2026-09-27 15:13:09', '2026-09-27 15:16:52'),
(105, 92, 'shop_pending', 'Shop resubmitted', 'test shop was updated by the seller and needs re-review.', '{\"shop_id\": 8}', 0, NULL, NULL, '2026-09-27 15:13:09', '2026-09-27 15:13:09'),
(106, 82, 'shop_pending', 'Shop resubmitted', 'test shop was updated by the seller and needs re-review.', '{\"shop_id\": 8}', 0, NULL, NULL, '2026-09-27 15:13:09', '2026-09-27 15:13:09'),
(107, 102, 'shop_pending', 'Shop resubmitted', 'test shop was updated by the seller and needs re-review.', '{\"shop_id\": 8}', 0, NULL, NULL, '2026-09-27 15:13:09', '2026-09-27 15:13:09'),
(108, 103, 'shop_status', 'Shop approved', 'Your shop \"test shop\" has been approved.', '{\"shop_id\": 8}', 1, '2026-09-27 15:16:30', NULL, '2026-09-27 15:16:14', '2026-09-27 15:16:30');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
CREATE TABLE IF NOT EXISTS `orders` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `order_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `city_id` bigint UNSIGNED DEFAULT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `total_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `delivery_charge` decimal(10,2) NOT NULL DEFAULT '0.00',
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `payment_status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `orders_order_number_unique` (`order_number`),
  KEY `orders_user_id_foreign` (`user_id`),
  KEY `orders_city_id_foreign` (`city_id`)
) ENGINE=InnoDB AUTO_INCREMENT=65 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `order_number`, `customer_name`, `phone`, `city`, `city_id`, `address`, `notes`, `total_price`, `delivery_charge`, `status`, `payment_status`, `created_at`, `updated_at`) VALUES
(54, 62, 'ORD-20260923-30A59D', 'hamna', '0304803483', 'Sadhoke', 4, 'shaddhra', NULL, 10280.00, 200.00, 'pending', 'pending', '2026-09-23 05:48:19', '2026-09-23 05:48:19'),
(56, 62, 'ORD-20260923-574501', 'svuaga', '9764845400', 'Lahore', 1, 'jshshsb', NULL, 580.00, 400.00, 'pending', 'pending', '2026-09-23 14:37:41', '2026-09-23 14:37:41'),
(61, 99, 'ORD-20260926-515A07', 'bisma', '0304249848', 'gujrat', 6, '6whsvsv', NULL, 1180.00, 1000.00, 'processing', 'paid', '2026-09-26 15:03:49', '2026-09-26 15:04:37'),
(62, 99, 'ORD-20260926-9C3425', 'bisma', '03054348484', 'gujrat', 6, 'kameokw', NULL, 5500.00, 1000.00, 'pending', 'pending', '2026-09-26 15:07:21', '2026-09-26 15:07:21'),
(63, 99, 'ORD-20260926-7E833B', 'bidms', '52666', 'Lahore', 1, 'iggucg', NULL, 743.00, 400.00, 'processing', 'paid', '2026-09-26 15:08:23', '2026-09-26 15:09:02'),
(64, 99, 'ORD-20260926-53A349', 'biwma', '646494649', 'Sadhoke', 4, 'gujranwala', NULL, 380.00, 200.00, 'processing', 'paid', '2026-09-26 15:23:17', '2026-09-26 15:23:35');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
CREATE TABLE IF NOT EXISTS `order_items` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id` bigint UNSIGNED NOT NULL,
  `shop_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `quantity` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `commission_amount` decimal(10,2) DEFAULT NULL,
  `net_amount` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `customer_confirmed_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_items_shop_id_foreign` (`shop_id`),
  KEY `order_items_product_id_foreign` (`product_id`),
  KEY `order_items_order_id_shop_id_index` (`order_id`,`shop_id`)
) ENGINE=InnoDB AUTO_INCREMENT=114 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `shop_id`, `product_id`, `quantity`, `price`, `commission_amount`, `net_amount`, `created_at`, `updated_at`, `status`, `customer_confirmed_at`) VALUES
(103, 54, 1, 41, 56, 180.00, NULL, NULL, '2026-09-23 05:48:19', '2026-09-23 05:48:19', 'pending', NULL),
(105, 56, 1, 41, 1, 180.00, NULL, NULL, '2026-09-23 14:37:41', '2026-09-23 14:37:41', 'pending', NULL),
(110, 61, 1, 41, 1, 180.00, 9.00, 171.00, '2026-09-26 15:03:49', '2026-09-26 15:04:37', 'pending', NULL),
(111, 62, 1, 41, 25, 180.00, NULL, NULL, '2026-09-26 15:07:21', '2026-09-26 15:07:21', 'pending', NULL),
(112, 63, 1, 40, 1, 343.00, 17.15, 325.85, '2026-09-26 15:08:23', '2026-09-26 15:09:02', 'pending', NULL),
(113, 64, 1, 41, 1, 180.00, 9.00, 171.00, '2026-09-26 15:23:17', '2026-09-26 15:23:34', 'pending', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_shop_charges`
--

DROP TABLE IF EXISTS `order_shop_charges`;
CREATE TABLE IF NOT EXISTS `order_shop_charges` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id` bigint UNSIGNED NOT NULL,
  `shop_id` bigint UNSIGNED NOT NULL,
  `weight_kg` decimal(10,2) NOT NULL,
  `delivery_charge` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_shop_charges_order_id_foreign` (`order_id`),
  KEY `order_shop_charges_shop_id_foreign` (`shop_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
CREATE TABLE IF NOT EXISTS `payments` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id` bigint UNSIGNED NOT NULL,
  `payment_method` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `transaction_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `screenshot_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `verified_by` bigint UNSIGNED DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `rejection_reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `gateway_response` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payments_order_id_unique` (`order_id`),
  KEY `payments_verified_by_foreign` (`verified_by`),
  KEY `payments_status_index` (`status`),
  KEY `payments_payment_method_index` (`payment_method`)
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `order_id`, `payment_method`, `payment_type`, `amount`, `transaction_id`, `screenshot_path`, `status`, `verified_by`, `verified_at`, `rejection_reason`, `gateway_response`, `created_at`, `updated_at`) VALUES
(37, 54, 'easypaisa', 'manual', 10280.00, '723289174216', 'payments/nV8FsizsmA40CVraq0b2vIZamzaAt9Qe679JJURZ.jpg', 'pending', NULL, NULL, NULL, NULL, '2026-09-23 05:48:19', '2026-09-23 05:48:19'),
(39, 56, 'card', 'stripe', 580.00, 'pi_3UIrIqQ8RiNpzmf210Vx0HJX', NULL, 'rejected', NULL, NULL, 'Card payment failed', '{\"id\": \"pi_3UIrIqQ8RiNpzmf210Vx0HJX\", \"amount\": 58000, \"object\": \"payment_intent\", \"review\": null, \"source\": null, \"status\": \"requires_payment_method\", \"created\": 1790174264, \"currency\": \"pkr\", \"customer\": null, \"livemode\": false, \"metadata\": {\"order_id\": \"56\"}, \"shipping\": null, \"processing\": null, \"application\": null, \"canceled_at\": null, \"description\": null, \"next_action\": null, \"on_behalf_of\": null, \"client_secret\": \"pi_3UIrIqQ8RiNpzmf210Vx0HJX_secret_KnYLyffLyVTmF99a2mG4ByyV1\", \"latest_charge\": \"ch_3UIrIqQ8RiNpzmf21YyFlx3g\", \"receipt_email\": null, \"transfer_data\": null, \"amount_details\": {\"tip\": []}, \"capture_method\": \"automatic_async\", \"payment_method\": null, \"payment_record\": null, \"transfer_group\": null, \"amount_received\": 0, \"customer_account\": null, \"managed_payments\": {\"enabled\": false}, \"amount_capturable\": 0, \"last_payment_error\": {\"code\": \"card_declined\", \"type\": \"card_error\", \"charge\": \"ch_3UIrIqQ8RiNpzmf21YyFlx3g\", \"doc_url\": \"https://stripe.com/docs/error-codes/card-declined\", \"message\": \"Your card was declined. Your request used a real card while testing. For a list of valid test cards, visit: https://stripe.com/docs/testing.\", \"decline_code\": \"test_mode_live_card\", \"payment_method\": {\"id\": \"pm_1UIrL0Q8RiNpzmf2Pk7sgMTB\", \"card\": {\"brand\": \"visa\", \"last4\": \"4200\", \"checks\": {\"cvc_check\": \"unchecked\", \"address_line1_check\": null, \"address_postal_code_check\": \"unchecked\"}, \"wallet\": null, \"country\": \"US\", \"funding\": \"credit\", \"exp_year\": 2055, \"networks\": {\"available\": [\"visa\"], \"preferred\": null}, \"exp_month\": 5, \"fingerprint\": \"QysQrLw67GEUDMhc\", \"display_brand\": \"visa\", \"generated_from\": null, \"regulated_status\": \"unregulated\", \"three_d_secure_usage\": {\"supported\": true}}, \"type\": \"card\", \"object\": \"payment_method\", \"created\": 1790174398, \"customer\": null, \"livemode\": false, \"metadata\": [], \"allow_redisplay\": \"unspecified\", \"billing_details\": {\"name\": null, \"email\": null, \"phone\": null, \"tax_id\": null, \"address\": {\"city\": null, \"line1\": null, \"line2\": null, \"state\": null, \"country\": \"GB\", \"postal_code\": \"YWYHWYW\"}}, \"customer_account\": null, \"shared_payment_granted_token\": null}, \"payment_method_type\": \"card\"}, \"setup_future_usage\": null, \"cancellation_reason\": null, \"confirmation_method\": \"automatic\", \"payment_method_types\": [\"card\", \"link\"], \"statement_descriptor\": null, \"application_fee_amount\": null, \"payment_method_options\": {\"card\": {\"network\": null, \"installments\": null, \"mandate_options\": null, \"request_three_d_secure\": \"automatic\"}, \"link\": {\"persistent_token\": null}}, \"automatic_payment_methods\": {\"enabled\": true, \"allow_redirects\": \"always\"}, \"statement_descriptor_suffix\": null, \"allowed_payment_method_types\": null, \"shared_payment_granted_token\": null, \"excluded_payment_method_types\": null, \"payment_method_configuration_details\": {\"id\": \"pmc_1U7HrnQ8RiNpzmf23C3NUKUR\", \"parent\": null}}', '2026-09-23 14:37:41', '2026-09-23 14:39:59'),
(44, 61, 'card', 'stripe', 1180.00, 'pi_3UJx8jQ8RiNpzmf20ioxfASt', NULL, 'paid', NULL, '2026-09-26 15:04:37', NULL, '{\"id\": \"pi_3UJx8jQ8RiNpzmf20ioxfASt\", \"amount\": 118000, \"object\": \"payment_intent\", \"review\": null, \"source\": null, \"status\": \"succeeded\", \"created\": 1790435029, \"currency\": \"pkr\", \"customer\": null, \"livemode\": false, \"metadata\": {\"order_id\": \"61\"}, \"shipping\": null, \"processing\": null, \"application\": null, \"canceled_at\": null, \"description\": null, \"next_action\": null, \"on_behalf_of\": null, \"client_secret\": \"pi_3UJx8jQ8RiNpzmf20ioxfASt_secret_sWkbXJ4tXnvcS75wdrOaNhwkv\", \"latest_charge\": \"ch_3UJx8jQ8RiNpzmf20IlpYoV5\", \"receipt_email\": null, \"transfer_data\": null, \"amount_details\": {\"tip\": []}, \"capture_method\": \"automatic_async\", \"payment_method\": \"pm_1UJx9TQ8RiNpzmf2K7REDoHc\", \"payment_record\": null, \"transfer_group\": null, \"amount_received\": 118000, \"customer_account\": null, \"managed_payments\": {\"enabled\": false}, \"amount_capturable\": 0, \"last_payment_error\": null, \"setup_future_usage\": null, \"cancellation_reason\": null, \"confirmation_method\": \"automatic\", \"payment_method_types\": [\"card\", \"link\"], \"statement_descriptor\": null, \"application_fee_amount\": null, \"payment_method_options\": {\"card\": {\"network\": null, \"installments\": null, \"mandate_options\": null, \"request_three_d_secure\": \"automatic\"}, \"link\": {\"persistent_token\": null}}, \"automatic_payment_methods\": {\"enabled\": true, \"allow_redirects\": \"always\"}, \"statement_descriptor_suffix\": null, \"allowed_payment_method_types\": null, \"shared_payment_granted_token\": null, \"excluded_payment_method_types\": null, \"payment_method_configuration_details\": {\"id\": \"pmc_1U7HrnQ8RiNpzmf23C3NUKUR\", \"parent\": null}}', '2026-09-26 15:03:49', '2026-09-26 15:04:37'),
(45, 62, 'easypaisa', 'manual', 5500.00, '7jhwuw72hw', 'payments/fBrDFU6lj5mwvBmu9TzSuJ1Crgl4eKud05iGDWse.jpg', 'pending', NULL, NULL, NULL, NULL, '2026-09-26 15:07:21', '2026-09-26 15:07:21'),
(46, 63, 'card', 'stripe', 743.00, 'pi_3UJxDCQ8RiNpzmf20vIXFM21', NULL, 'paid', NULL, '2026-09-26 15:09:02', NULL, '{\"id\": \"pi_3UJxDCQ8RiNpzmf20vIXFM21\", \"amount\": 74300, \"object\": \"payment_intent\", \"review\": null, \"source\": null, \"status\": \"succeeded\", \"created\": 1790435306, \"currency\": \"pkr\", \"customer\": null, \"livemode\": false, \"metadata\": {\"order_id\": \"63\"}, \"shipping\": null, \"processing\": null, \"application\": null, \"canceled_at\": null, \"description\": null, \"next_action\": null, \"on_behalf_of\": null, \"client_secret\": \"pi_3UJxDCQ8RiNpzmf20vIXFM21_secret_DOGrSUCqeLIIMwSc9nxfP6FQ0\", \"latest_charge\": \"ch_3UJxDCQ8RiNpzmf208LC3hl2\", \"receipt_email\": null, \"transfer_data\": null, \"amount_details\": {\"tip\": []}, \"capture_method\": \"automatic_async\", \"payment_method\": \"pm_1UJxDlQ8RiNpzmf2R8wWSYAC\", \"payment_record\": null, \"transfer_group\": null, \"amount_received\": 74300, \"customer_account\": null, \"managed_payments\": {\"enabled\": false}, \"amount_capturable\": 0, \"last_payment_error\": null, \"setup_future_usage\": null, \"cancellation_reason\": null, \"confirmation_method\": \"automatic\", \"payment_method_types\": [\"card\", \"link\"], \"statement_descriptor\": null, \"application_fee_amount\": null, \"payment_method_options\": {\"card\": {\"network\": null, \"installments\": null, \"mandate_options\": null, \"request_three_d_secure\": \"automatic\"}, \"link\": {\"persistent_token\": null}}, \"automatic_payment_methods\": {\"enabled\": true, \"allow_redirects\": \"always\"}, \"statement_descriptor_suffix\": null, \"allowed_payment_method_types\": null, \"shared_payment_granted_token\": null, \"excluded_payment_method_types\": null, \"payment_method_configuration_details\": {\"id\": \"pmc_1U7HrnQ8RiNpzmf23C3NUKUR\", \"parent\": null}}', '2026-09-26 15:08:23', '2026-09-26 15:09:02'),
(47, 64, 'card', 'stripe', 380.00, 'pi_3UJxRZQ8RiNpzmf20FNItJLS', NULL, 'paid', NULL, '2026-09-26 15:23:34', NULL, '{\"id\": \"pi_3UJxRZQ8RiNpzmf20FNItJLS\", \"amount\": 38000, \"object\": \"payment_intent\", \"review\": null, \"source\": null, \"status\": \"succeeded\", \"created\": 1790436197, \"currency\": \"pkr\", \"customer\": null, \"livemode\": false, \"metadata\": {\"order_id\": \"64\"}, \"shipping\": null, \"processing\": null, \"application\": null, \"canceled_at\": null, \"description\": null, \"next_action\": null, \"on_behalf_of\": null, \"client_secret\": \"pi_3UJxRZQ8RiNpzmf20FNItJLS_secret_6W0H5DvHrIGOfY6Ru1b4UMDHn\", \"latest_charge\": \"ch_3UJxRZQ8RiNpzmf20xdRUG4w\", \"receipt_email\": null, \"transfer_data\": null, \"amount_details\": {\"tip\": []}, \"capture_method\": \"automatic_async\", \"payment_method\": \"pm_1UJxRpQ8RiNpzmf2BYCtdmV3\", \"payment_record\": null, \"transfer_group\": null, \"amount_received\": 38000, \"customer_account\": null, \"managed_payments\": {\"enabled\": false}, \"amount_capturable\": 0, \"last_payment_error\": null, \"setup_future_usage\": null, \"cancellation_reason\": null, \"confirmation_method\": \"automatic\", \"payment_method_types\": [\"card\", \"link\"], \"statement_descriptor\": null, \"application_fee_amount\": null, \"payment_method_options\": {\"card\": {\"network\": null, \"installments\": null, \"mandate_options\": null, \"request_three_d_secure\": \"automatic\"}, \"link\": {\"persistent_token\": null}}, \"automatic_payment_methods\": {\"enabled\": true, \"allow_redirects\": \"always\"}, \"statement_descriptor_suffix\": null, \"allowed_payment_method_types\": null, \"shared_payment_granted_token\": null, \"excluded_payment_method_types\": null, \"payment_method_configuration_details\": {\"id\": \"pmc_1U7HrnQ8RiNpzmf23C3NUKUR\", \"parent\": null}}', '2026-09-26 15:23:17', '2026-09-26 15:23:34');

-- --------------------------------------------------------

--
-- Table structure for table `payment_settings`
--

DROP TABLE IF EXISTS `payment_settings`;
CREATE TABLE IF NOT EXISTS `payment_settings` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `easypaisa_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `easypaisa_account_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jazzcash_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jazzcash_account_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payment_settings`
--

INSERT INTO `payment_settings` (`id`, `easypaisa_number`, `easypaisa_account_name`, `jazzcash_number`, `jazzcash_account_name`, `created_at`, `updated_at`) VALUES
(1, '03004803483', 'Basma jabeen', '03024539786', 'Basma Jabeen', '2026-07-09 23:51:29', '2026-09-20 15:10:01');

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=126 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'manage users', 'web', '2026-05-21 20:53:21', '2026-05-21 20:53:21'),
(2, 'manage roles', 'web', '2026-05-21 20:53:46', '2026-05-21 20:53:46'),
(3, 'manage permissions', 'web', '2026-05-21 20:54:00', '2026-05-21 20:54:00'),
(4, 'manage shops', 'web', '2026-05-21 20:54:12', '2026-05-21 20:54:12'),
(5, 'manage products', 'web', '2026-05-21 20:54:21', '2026-05-21 20:54:21'),
(6, 'view analytics', 'web', '2026-05-21 20:54:30', '2026-05-21 20:54:30'),
(7, 'manage reports', 'web', '2026-05-21 20:54:40', '2026-05-21 20:54:40'),
(8, 'approve shops', 'web', '2026-05-22 10:27:15', '2026-05-22 10:27:15'),
(9, 'create shop', 'web', '2026-05-25 01:53:13', '2026-05-25 01:53:13'),
(10, 'view users', 'web', '2026-05-28 13:33:26', '2026-05-28 13:33:26'),
(11, 'view dashboard', 'web', '2026-05-29 03:48:40', '2026-05-29 03:48:40'),
(12, 'view notifications', 'web', '2026-05-29 03:48:40', '2026-05-29 03:48:40'),
(13, 'create users', 'web', '2026-05-29 03:48:40', '2026-05-29 03:48:40'),
(14, 'update users', 'web', '2026-05-29 03:48:40', '2026-05-29 03:48:40'),
(15, 'delete users', 'web', '2026-05-29 03:48:40', '2026-05-29 03:48:40'),
(16, 'assign roles', 'web', '2026-05-29 03:48:40', '2026-05-29 03:48:40'),
(17, 'assign permissions', 'web', '2026-05-29 03:48:41', '2026-05-29 03:48:41'),
(18, 'delete shops', 'web', '2026-05-29 03:48:41', '2026-05-29 03:48:41'),
(19, 'view shops', 'web', '2026-05-29 03:48:41', '2026-05-29 03:48:41'),
(20, 'create products', 'web', '2026-05-29 03:48:41', '2026-05-29 03:48:41'),
(21, 'update products', 'web', '2026-05-29 03:48:41', '2026-05-29 03:48:41'),
(22, 'delete products', 'web', '2026-05-29 03:48:41', '2026-05-29 03:48:41'),
(23, 'view orders', 'web', '2026-05-29 03:48:42', '2026-05-29 03:48:42'),
(24, 'manage orders', 'web', '2026-05-29 03:48:42', '2026-05-29 03:48:42'),
(25, 'update order status', 'web', '2026-05-29 03:48:42', '2026-05-29 03:48:42'),
(26, 'manage categories', 'web', '2026-05-29 03:48:42', '2026-05-29 03:48:42'),
(27, 'export reports', 'web', '2026-05-29 03:48:42', '2026-05-29 03:48:42'),
(28, 'send notifications', 'web', '2026-06-01 02:09:44', '2026-06-01 02:09:44'),
(29, 'manage settings', 'web', '2026-06-01 02:09:44', '2026-06-01 02:09:44'),
(30, 'manage profile', 'web', '2026-06-01 02:09:44', '2026-06-01 02:09:44'),
(31, 'search products', 'web', '2026-06-01 02:09:44', '2026-06-01 02:09:44'),
(32, 'search shops', 'web', '2026-06-01 02:09:44', '2026-06-01 02:09:44'),
(33, 'search system', 'web', '2026-06-01 02:09:45', '2026-06-01 02:09:45'),
(34, 'create sellers', 'web', '2026-06-01 02:09:45', '2026-06-01 02:09:45'),
(35, 'reject shops', 'web', '2026-06-01 02:09:45', '2026-06-01 02:09:45'),
(36, 'view products', 'web', '2026-06-01 02:09:45', '2026-06-01 02:09:45'),
(37, 'view order details', 'web', '2026-06-01 02:09:45', '2026-06-01 02:09:45'),
(38, 'checkout orders', 'web', '2026-06-01 02:09:45', '2026-06-01 02:09:45'),
(39, 'add to cart', 'web', '2026-06-01 02:09:45', '2026-06-01 02:09:45'),
(40, 'chat with sellers', 'web', '2026-06-01 02:09:45', '2026-06-01 02:09:45'),
(41, 'chat with customers', 'web', '2026-06-01 02:09:45', '2026-06-01 02:09:45'),
(42, 'view payments', 'web', '2026-06-01 02:09:45', '2026-06-01 02:09:45'),
(43, 'manage payments', 'web', '2026-06-01 02:09:46', '2026-06-01 02:09:46'),
(44, 'view reports', 'web', '2026-06-01 02:09:46', '2026-06-01 02:09:46'),
(45, 'view feedback', 'web', '2026-06-01 02:09:46', '2026-06-01 02:09:46'),
(46, 'view customer dashboard', 'web', '2026-06-02 21:53:56', '2026-06-02 21:53:56'),
(47, 'view seller dashboard', 'web', '2026-06-02 21:53:56', '2026-06-02 21:53:56'),
(48, 'view admin dashboard', 'web', '2026-06-02 21:53:56', '2026-06-02 21:53:56'),
(49, 'view public products', 'web', '2026-06-02 21:53:56', '2026-06-02 21:53:56'),
(50, 'view own products', 'web', '2026-06-02 21:53:56', '2026-06-02 21:53:56'),
(51, 'view all products', 'web', '2026-06-02 21:53:56', '2026-06-02 21:53:56'),
(52, 'update own products', 'web', '2026-06-02 21:53:57', '2026-06-02 21:53:57'),
(53, 'update any products', 'web', '2026-06-02 21:53:57', '2026-06-02 21:53:57'),
(54, 'delete own products', 'web', '2026-06-02 21:53:57', '2026-06-02 21:53:57'),
(55, 'delete any products', 'web', '2026-06-02 21:53:57', '2026-06-02 21:53:57'),
(56, 'view public shops', 'web', '2026-06-02 21:53:57', '2026-06-02 21:53:57'),
(57, 'view own shop', 'web', '2026-06-02 21:53:57', '2026-06-02 21:53:57'),
(58, 'view all shops', 'web', '2026-06-02 21:53:57', '2026-06-02 21:53:57'),
(59, 'update own shop', 'web', '2026-06-02 21:53:57', '2026-06-02 21:53:57'),
(60, 'update any shop', 'web', '2026-06-02 21:53:57', '2026-06-02 21:53:57'),
(61, 'delete own shop', 'web', '2026-06-02 21:53:57', '2026-06-02 21:53:57'),
(62, 'delete any shop', 'web', '2026-06-02 21:53:58', '2026-06-02 21:53:58'),
(63, 'create seller request', 'web', '2026-06-02 21:53:58', '2026-06-02 21:53:58'),
(64, 'view own seller request', 'web', '2026-06-02 21:53:58', '2026-06-02 21:53:58'),
(65, 'view cart', 'web', '2026-06-02 21:53:58', '2026-06-02 21:53:58'),
(66, 'update cart', 'web', '2026-06-02 21:53:58', '2026-06-02 21:53:58'),
(67, 'remove from cart', 'web', '2026-06-02 21:53:58', '2026-06-02 21:53:58'),
(68, 'create order', 'web', '2026-06-02 21:53:58', '2026-06-02 21:53:58'),
(69, 'view own orders', 'web', '2026-06-02 21:53:58', '2026-06-02 21:53:58'),
(70, 'view shop orders', 'web', '2026-06-02 21:53:58', '2026-06-02 21:53:58'),
(71, 'view all orders', 'web', '2026-06-02 21:53:58', '2026-06-02 21:53:58'),
(72, 'view own order details', 'web', '2026-06-02 21:53:59', '2026-06-02 21:53:59'),
(73, 'view shop order details', 'web', '2026-06-02 21:53:59', '2026-06-02 21:53:59'),
(74, 'view all order details', 'web', '2026-06-02 21:53:59', '2026-06-02 21:53:59'),
(75, 'track own orders', 'web', '2026-06-02 21:53:59', '2026-06-02 21:53:59'),
(76, 'cancel own orders', 'web', '2026-06-02 21:53:59', '2026-06-02 21:53:59'),
(77, 'update any order status', 'web', '2026-06-02 21:53:59', '2026-06-02 21:53:59'),
(78, 'create payment', 'web', '2026-06-02 21:53:59', '2026-06-02 21:53:59'),
(79, 'view own payments', 'web', '2026-06-02 21:53:59', '2026-06-02 21:53:59'),
(80, 'view shop payments', 'web', '2026-06-02 21:53:59', '2026-06-02 21:53:59'),
(81, 'view all payments', 'web', '2026-06-02 21:53:59', '2026-06-02 21:53:59'),
(82, 'receive payments', 'web', '2026-06-02 21:53:59', '2026-06-02 21:53:59'),
(83, 'send messages', 'web', '2026-06-02 21:53:59', '2026-06-02 21:53:59'),
(84, 'view own messages', 'web', '2026-06-02 21:54:00', '2026-06-02 21:54:00'),
(85, 'view shop messages', 'web', '2026-06-02 21:54:00', '2026-06-02 21:54:00'),
(86, 'view own notifications', 'web', '2026-06-02 21:54:00', '2026-06-02 21:54:00'),
(87, 'view all notifications', 'web', '2026-06-02 21:54:00', '2026-06-02 21:54:00'),
(88, 'send customer notifications', 'web', '2026-06-02 21:54:00', '2026-06-02 21:54:00'),
(89, 'update own profile', 'web', '2026-06-02 21:54:00', '2026-06-02 21:54:00'),
(90, 'update own settings', 'web', '2026-06-02 21:54:00', '2026-06-02 21:54:00'),
(91, 'create address', 'web', '2026-06-02 21:54:00', '2026-06-02 21:54:00'),
(92, 'view own address', 'web', '2026-06-02 21:54:00', '2026-06-02 21:54:00'),
(93, 'update own address', 'web', '2026-06-02 21:54:01', '2026-06-02 21:54:01'),
(94, 'delete own address', 'web', '2026-06-02 21:54:01', '2026-06-02 21:54:01'),
(95, 'create reviews', 'web', '2026-06-02 21:54:01', '2026-06-02 21:54:01'),
(96, 'view reviews', 'web', '2026-06-02 21:54:01', '2026-06-02 21:54:01'),
(97, 'reply feedback', 'web', '2026-06-02 21:54:01', '2026-06-02 21:54:01'),
(98, 'view customer delivery info', 'web', '2026-06-02 21:54:01', '2026-06-02 21:54:01'),
(99, 'manage own inventory', 'web', '2026-06-02 21:54:01', '2026-06-02 21:54:01'),
(100, 'view own analytics', 'web', '2026-06-02 21:54:01', '2026-06-02 21:54:01'),
(101, 'view all analytics', 'web', '2026-06-02 21:54:01', '2026-06-02 21:54:01'),
(102, 'view sellers', 'web', '2026-06-02 21:54:01', '2026-06-02 21:54:01'),
(103, 'update sellers', 'web', '2026-06-02 21:54:01', '2026-06-02 21:54:01'),
(104, 'create categories', 'web', '2026-06-02 21:54:02', '2026-06-02 21:54:02'),
(105, 'view categories', 'web', '2026-06-02 21:54:02', '2026-06-02 21:54:02'),
(106, 'update categories', 'web', '2026-06-02 21:54:02', '2026-06-02 21:54:02'),
(107, 'delete categories', 'web', '2026-06-02 21:54:02', '2026-06-02 21:54:02'),
(108, 'create roles', 'web', '2026-06-02 21:54:02', '2026-06-02 21:54:02'),
(109, 'update roles', 'web', '2026-06-02 21:54:02', '2026-06-02 21:54:02'),
(110, 'delete roles', 'web', '2026-06-02 21:54:02', '2026-06-02 21:54:02'),
(111, 'create permissions', 'web', '2026-06-02 21:54:02', '2026-06-02 21:54:02'),
(112, 'update permissions', 'web', '2026-06-02 21:54:02', '2026-06-02 21:54:02'),
(113, 'delete permissions', 'web', '2026-06-02 21:54:02', '2026-06-02 21:54:02'),
(114, 'manage system', 'web', '2026-06-02 21:54:02', '2026-06-02 21:54:02'),
(115, 'backup system', 'web', '2026-06-02 21:54:02', '2026-06-02 21:54:02'),
(116, 'restore system', 'web', '2026-06-02 21:54:02', '2026-06-02 21:54:02'),
(117, 'full access', 'web', '2026-06-02 21:54:03', '2026-06-02 21:54:03'),
(118, 'remove sellers', 'web', '2026-08-26 01:39:26', '2026-08-26 01:39:26'),
(119, 'view own payouts', 'web', '2026-08-29 23:31:19', '2026-08-29 23:31:19'),
(120, 'manage cities', 'web', '2026-08-29 23:31:19', '2026-08-29 23:31:19'),
(121, 'file complaints', 'web', '2026-08-29 23:31:20', '2026-08-29 23:31:20'),
(122, 'view complaints', 'web', '2026-08-29 23:31:20', '2026-08-29 23:31:20'),
(123, 'manage complaints', 'web', '2026-08-29 23:31:20', '2026-08-29 23:31:20'),
(124, 'view roles', 'web', '2026-08-29 23:31:20', '2026-08-29 23:31:20'),
(125, 'manage commission', 'web', '2026-09-16 21:18:16', '2026-09-16 21:18:16');

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  KEY `personal_access_tokens_expires_at_index` (`expires_at`)
) ENGINE=InnoDB AUTO_INCREMENT=1092 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(89, 'App\\Models\\User', 6, 'auth_token', '490b9ba4e66a4d011ad471db3f6167db6d66020f5e333728114d032e4e9812bd', '[\"*\"]', '2026-05-17 06:04:32', NULL, '2026-05-17 06:02:42', '2026-05-17 06:04:32'),
(91, 'App\\Models\\User', 6, 'auth_token', 'f8a1abd86e50a17fc54952eb5e99ed6dd79aa5e82a75378417d4efefe8353faf', '[\"*\"]', NULL, NULL, '2026-05-17 06:13:16', '2026-05-17 06:13:16'),
(92, 'App\\Models\\User', 6, 'auth_token', '5510585accbbe0d0fa10b23f0f150de426a5aa56ca3030dc3a97e330e35d8400', '[\"*\"]', NULL, NULL, '2026-05-17 06:15:28', '2026-05-17 06:15:28'),
(93, 'App\\Models\\User', 6, 'auth_token', '4f273b39f17d0c8477bba8b75905172e83fdab038f97ff6b8198b556cca3f7aa', '[\"*\"]', '2026-05-17 06:34:37', NULL, '2026-05-17 06:26:36', '2026-05-17 06:34:37'),
(95, 'App\\Models\\User', 6, 'auth_token', 'c40a36be85ae34c40241c85658f2662f5af4ad1cc75dae52b49475cccb166f83', '[\"*\"]', '2026-05-17 07:02:16', NULL, '2026-05-17 07:01:13', '2026-05-17 07:02:16'),
(130, 'App\\Models\\User', 6, 'auth_token', '89798d5d0df32ad3591f3f7d7e368ff349ab6ed4532b5a6135a0a8347d54d434', '[\"*\"]', '2026-05-19 03:08:38', NULL, '2026-05-19 03:08:00', '2026-05-19 03:08:38'),
(132, 'App\\Models\\User', 6, 'auth_token', 'a63ccf6a91e34f587ffb2e3c792e2a0315f485ac37f3d2288392f5cfe943043a', '[\"*\"]', NULL, NULL, '2026-05-19 03:10:28', '2026-05-19 03:10:28'),
(135, 'App\\Models\\User', 6, 'auth_token', '0e2a52d6f0bf227bb30e6176f28002da022fa39e1367b79f6fdecd456899cfd4', '[\"*\"]', '2026-05-19 03:35:19', NULL, '2026-05-19 03:34:06', '2026-05-19 03:35:19'),
(138, 'App\\Models\\User', 6, 'auth_token', 'a21b4e27b8f0f90a53be474893e5ff72bb6306ae7026bb5899813d5d4af3b485', '[\"*\"]', '2026-05-19 10:02:20', NULL, '2026-05-19 10:01:01', '2026-05-19 10:02:20'),
(139, 'App\\Models\\User', 6, 'auth_token', '1c3c99331718661ccf427ca35959ad8a00b76ae666c01319209c10594bb1d391', '[\"*\"]', '2026-05-19 10:18:18', NULL, '2026-05-19 10:16:44', '2026-05-19 10:18:18'),
(140, 'App\\Models\\User', 6, 'auth_token', '372fa6c0f2ff41ab9ca5add37affbdfe6244f027f53e008cd09856988adb6f0f', '[\"*\"]', NULL, NULL, '2026-05-19 10:22:36', '2026-05-19 10:22:36'),
(141, 'App\\Models\\User', 6, 'auth_token', 'd50c5b55e8b28dadd7ecb592976854c7d992a5e55ce3c251d9df53bee333f9eb', '[\"*\"]', '2026-05-19 10:37:51', NULL, '2026-05-19 10:36:35', '2026-05-19 10:37:51'),
(144, 'App\\Models\\User', 6, 'auth_token', 'c0b471081f5f3d960ac0dac0c28cb9dd526d764e64a4a2f0027c38548a13f59d', '[\"*\"]', '2026-05-19 10:55:30', NULL, '2026-05-19 10:55:27', '2026-05-19 10:55:30'),
(145, 'App\\Models\\User', 6, 'auth_token', '1a90530f6d6435c3a49630241258ae48260f9299ddf86367a40fea62d32ec8c3', '[\"*\"]', NULL, NULL, '2026-05-19 10:55:51', '2026-05-19 10:55:51'),
(147, 'App\\Models\\User', 6, 'auth_token', '318f9c1cd06c14249f07c2441854107042452abea6987f583ab234f7f08bb30a', '[\"*\"]', '2026-05-19 10:58:13', NULL, '2026-05-19 10:57:20', '2026-05-19 10:58:13'),
(157, 'App\\Models\\User', 8, 'auth_token', 'b621ea73885d06e198c9dc554ebd6481d6673d309e7a18fef40a44adb65bcf42', '[\"*\"]', NULL, NULL, '2026-05-21 10:51:29', '2026-05-21 10:51:29'),
(168, 'App\\Models\\User', 6, 'auth_token', '86ad464874de7e16b0d4c5e71bbdcd4381fb8319da384e533a77abfdf4259275', '[\"*\"]', '2026-05-21 23:53:11', NULL, '2026-05-21 23:53:06', '2026-05-21 23:53:11'),
(426, 'App\\Models\\User', 14, 'auth_token', 'aa4584df9a755a24c6805387efd96a9e4ba529765e9e1e1f23a08fc928ee554d', '[\"*\"]', NULL, NULL, '2026-06-20 02:22:17', '2026-06-20 02:22:17'),
(502, 'App\\Models\\User', 2, 'auth_token', '7be10bb386d69a493595bf354b5bc53a5dded168b75ef1dc8f3b5a1d198f5319', '[\"*\"]', '2026-08-18 02:49:33', NULL, '2026-08-18 02:49:14', '2026-08-18 02:49:33'),
(605, 'App\\Models\\User', 28, 'auth_token', 'd3e6155ab515374a41cfbe141967b380088e8759b228fb0de4212d4277669879', '[\"*\"]', '2026-08-26 05:08:42', NULL, '2026-08-26 05:08:33', '2026-08-26 05:08:42'),
(664, 'App\\Models\\User', 25, 'auth_token', 'd6588e0da48e8e05ea58e79890569070de762acd7776ab896f3992aa9e32cd68', '[\"*\"]', '2026-08-29 02:08:55', NULL, '2026-08-29 02:08:41', '2026-08-29 02:08:55'),
(681, 'App\\Models\\User', 16, 'auth_token', 'a1508edbe1e0e6762deb287c47e546825ec08d27f8702752316735cef9cfc409', '[\"*\"]', '2026-09-01 01:42:08', NULL, '2026-09-01 01:41:35', '2026-09-01 01:42:08'),
(718, 'App\\Models\\User', 4, 'auth_token', '2b0a3feec1b0b8c645355886d482c47662edf3620ce5d28c69eafd9b11425296', '[\"*\"]', NULL, NULL, '2026-09-03 03:28:31', '2026-09-03 03:28:31'),
(730, 'App\\Models\\User', 30, 'auth_token', 'daba923d9a7795d1efeed6b673ba17f8074d254a18cb77085d23a287ca2ce14d', '[\"*\"]', '2026-09-03 04:01:37', NULL, '2026-09-03 04:01:05', '2026-09-03 04:01:37'),
(759, 'App\\Models\\User', 10, 'auth_token', '7ca9732c56791dd0dcb7a4b87b2d3a23031f101c99c78caadfd0f6c699e3f21f', '[\"*\"]', '2026-09-05 11:55:33', NULL, '2026-09-05 11:54:51', '2026-09-05 11:55:33'),
(884, 'App\\Models\\User', 20, 'auth_token', 'a9eeb7325963547250140d835347b6bb7620be7a76712087ab18083e47ca0b98', '[\"*\"]', '2026-09-11 01:24:35', NULL, '2026-09-11 01:13:27', '2026-09-11 01:24:35'),
(888, 'App\\Models\\User', 31, 'auth_token', 'fb83fb011652283267ec1a8a89c05e53d4b13a95fc3785a5bdb84b42c45cfeb7', '[\"*\"]', '2026-09-11 11:03:05', NULL, '2026-09-11 10:58:52', '2026-09-11 11:03:05'),
(898, 'App\\Models\\User', 32, 'auth_token', 'cdcdd1e67fd35624d588462719be879535db2db351a4faae0de1d882935c9e95', '[\"*\"]', '2026-09-11 20:44:11', NULL, '2026-09-11 20:43:53', '2026-09-11 20:44:11'),
(918, 'App\\Models\\User', 19, 'auth_token', '715c3b318b7439371eb25ee40a119cbb67204c2c69dfebe0830a0f107341533a', '[\"*\"]', '2026-09-14 09:55:05', NULL, '2026-09-14 09:53:46', '2026-09-14 09:55:05'),
(939, 'App\\Models\\User', 17, 'auth_token', '61fe3f8edb194091e637ef83a89ea03be186146c5a4506ed7f133919555bbb08', '[\"*\"]', '2026-09-16 22:33:59', NULL, '2026-09-16 22:32:02', '2026-09-16 22:33:59'),
(946, 'App\\Models\\User', 21, 'auth_token', '8becd2f539ffa08b9cae503cab045a5c72ff2f1bd5f85e378f3c201730315630', '[\"*\"]', '2026-09-19 09:39:32', NULL, '2026-09-19 09:23:23', '2026-09-19 09:39:32'),
(947, 'App\\Models\\User', 22, 'auth_token', '755cb1e968b320a39ec6aaf6f39c8b553243133f1ebf361b24597b543c45d24c', '[\"*\"]', '2026-09-19 09:43:31', NULL, '2026-09-19 09:40:55', '2026-09-19 09:43:31'),
(948, 'App\\Models\\User', 18, 'auth_token', 'df5637f48374e836d0d4b2af39b870bf42b93daf5433cb4abbf62eed04855bdc', '[\"*\"]', '2026-09-19 09:44:01', NULL, '2026-09-19 09:43:59', '2026-09-19 09:44:01'),
(951, 'App\\Models\\User', 24, 'auth_token', 'a680005c11339a60516be3cdc3c4091bd9223b6cfb59dff45182de6119ba0b5e', '[\"*\"]', '2026-09-20 02:21:13', NULL, '2026-09-19 22:02:14', '2026-09-20 02:21:13'),
(952, 'App\\Models\\User', 23, 'auth_token', '18f012704f87c99c79117d6f2ab5165bdf7dd89dede91d61a176c9df4fc0f8a5', '[\"*\"]', '2026-09-20 02:46:40', NULL, '2026-09-20 02:46:08', '2026-09-20 02:46:40'),
(955, 'App\\Models\\User', 3, 'auth_token', '3fd5ecd3c7cbafcf16c7327bb75a9cb9823135d32ebce8a06a5e1b0b8d5e7263', '[\"*\"]', '2026-09-20 11:59:10', NULL, '2026-09-20 11:57:09', '2026-09-20 11:59:10'),
(956, 'App\\Models\\User', 5, 'auth_token', '8e904c0dbf50c11f8254bab06af68220ded30d48c7d8b33f757bdc2832b8d2a4', '[\"*\"]', '2026-09-20 12:02:56', NULL, '2026-09-20 11:59:44', '2026-09-20 12:02:56'),
(959, 'App\\Models\\User', 7, 'auth_token', 'bf9cdb6ef0dfb0b10e2e7c6ea92a577a4724faa8311d67e5b9d5c41e5dc80382', '[\"*\"]', '2026-09-20 12:04:49', NULL, '2026-09-20 12:04:12', '2026-09-20 12:04:49'),
(964, 'App\\Models\\User', 39, 'auth_token', 'fd9fe138d8eac1e4156437aebefaae055fe8acc42339b587015df2aa4c5198f5', '[\"*\"]', '2026-09-20 14:37:59', NULL, '2026-09-20 14:34:27', '2026-09-20 14:37:59'),
(1000, 'App\\Models\\User', 101, 'auth_token', '7544799c21c72e9fe4d8ebbeb1603173ff823c4e5c449aed26258d4032c1aee9', '[\"*\"]', '2026-09-22 19:10:56', NULL, '2026-09-22 17:47:44', '2026-09-22 19:10:56'),
(1010, 'App\\Models\\User', 89, 'auth_token', 'e24cd5c11d295562d386b8daaebc639083a8500cac2877083d2149f07786e23a', '[\"*\"]', '2026-09-23 15:33:19', NULL, '2026-09-23 15:33:04', '2026-09-23 15:33:19'),
(1029, 'App\\Models\\User', 85, 'auth_token', '5ad2a9ad5243e3bc9cd56236a29df42f6fc32846cda91deca93b22a3abbedb2c', '[\"*\"]', '2026-09-24 04:49:05', NULL, '2026-09-24 04:46:11', '2026-09-24 04:49:05'),
(1036, 'App\\Models\\User', 90, 'auth_token', '8eff5b0e5e55bed846f7ef32685ded782126dc1add7d1878b6d9a44312b86c33', '[\"*\"]', '2026-09-24 15:57:18', NULL, '2026-09-24 15:57:13', '2026-09-24 15:57:18'),
(1037, 'App\\Models\\User', 88, 'auth_token', '9bd4392897675365b24c7da1a92ca343bf2bd391155690ff46a5c52f986c70ce', '[\"*\"]', '2026-09-25 07:22:55', NULL, '2026-09-24 15:57:35', '2026-09-25 07:22:55'),
(1039, 'App\\Models\\User', 92, 'auth_token', '340ea619e7d46b04a598996e8fb3b4629cd575be28ce1a4e32e2339a2f70187e', '[\"*\"]', '2026-09-27 12:58:11', NULL, '2026-09-25 13:24:08', '2026-09-27 12:58:11'),
(1040, 'App\\Models\\User', 83, 'auth_token', 'cd4da2e729038a75d627791ac313fcb7bc11a347521d11fb2166510e76527162', '[\"*\"]', '2026-09-25 13:37:29', NULL, '2026-09-25 13:37:06', '2026-09-25 13:37:29'),
(1052, 'App\\Models\\User', 102, 'auth_token', 'cd55fd709120f48fd7bb5592c74846fad98eb3a37c66c6ba754a550c3d1af978', '[\"*\"]', '2026-09-26 00:45:35', NULL, '2026-09-25 15:26:25', '2026-09-26 00:45:35'),
(1066, 'App\\Models\\User', 82, 'auth_token', 'bd2f0f89bea739f020f83ba7726558f43161ab11f277eca0233af845c47a4cdd', '[\"*\"]', '2026-09-26 05:39:15', NULL, '2026-09-26 05:38:15', '2026-09-26 05:39:15'),
(1071, 'App\\Models\\User', 15, 'auth_token', 'a4fc1348efd99825fd393cac49842afab43545aee792b9b35044eeb3ec680c80', '[\"*\"]', '2026-09-26 09:12:11', NULL, '2026-09-26 09:10:31', '2026-09-26 09:12:11'),
(1074, 'App\\Models\\User', 99, 'auth_token', '36c806ec1b1360650d75e1c1bdc89b8425f4244048fae593899431b0dc4b9871', '[\"*\"]', '2026-09-26 15:23:45', NULL, '2026-09-26 15:22:46', '2026-09-26 15:23:45'),
(1075, 'App\\Models\\User', 81, 'auth_token', '984e557744109a9b4359aca896dc57a3cea24a8efcbc0076eb437191cd2628c9', '[\"*\"]', '2026-09-26 15:25:17', NULL, '2026-09-26 15:24:10', '2026-09-26 15:25:17'),
(1077, 'App\\Models\\User', 86, 'auth_token', 'fb1a45959f69bc027e326277f35e012dda540a4472631b08521f0eb9086f9027', '[\"*\"]', '2026-09-27 09:05:23', NULL, '2026-09-27 08:15:46', '2026-09-27 09:05:23'),
(1089, 'App\\Models\\User', 97, 'auth_token', '3f0f7195df881401c6230e33dc53852d472f28978ea867d945171e974491688d', '[\"*\"]', '2026-09-27 15:12:28', NULL, '2026-09-27 15:11:29', '2026-09-27 15:12:28'),
(1090, 'App\\Models\\User', 103, 'auth_token', '41b741a23623fa97eeba70c5b60138f77595b882a6a20a557a9f909f775ca022', '[\"*\"]', '2026-09-27 15:17:31', NULL, '2026-09-27 15:12:37', '2026-09-27 15:17:31');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
CREATE TABLE IF NOT EXISTS `products` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `shop_id` bigint UNSIGNED DEFAULT NULL,
  `rice_category_id` bigint UNSIGNED DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `stock` int DEFAULT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `products_user_id_foreign` (`user_id`),
  KEY `products_shop_id_foreign` (`shop_id`),
  KEY `products_rice_category_id_foreign` (`rice_category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `created_at`, `updated_at`, `user_id`, `shop_id`, `rice_category_id`, `name`, `price`, `stock`, `image`, `is_active`) VALUES
(38, '2026-09-21 16:06:31', '2026-09-21 16:06:31', 81, 1, 11, 'kainat', 400.00, 16000, 'products/LzOvP5r8IKtnfnwuyESalBUkt8l3ADUzHUOasCwV.jpg', 1),
(39, '2026-09-21 16:18:03', '2026-09-21 16:18:03', 81, 1, 14, 'tota', 300.00, 500, 'products/UEddBXvgKotKln2fNi9TMDBrgUOrqqT2oiKsAFti.jpg', 1),
(40, '2026-09-21 16:19:35', '2026-09-26 15:09:02', 81, 1, 6, 'Steam', 343.00, 19999, 'products/G62HKBuZWA5SyUccE4qKo9016sA9u6T1BlY8tDdO.jpg', 1),
(41, '2026-09-22 15:26:13', '2026-09-26 15:23:34', 81, 1, 4, 'Rozana B1', 180.00, 19998, 'products/UR70RiIa0CyMTiL2u7Ong5xUz6f9wRMlmldWbJJd.jpg', 1),
(42, '2026-09-27 08:20:32', '2026-09-27 08:20:32', 86, 5, 12, 'Steam basmati', 400.00, 1000000, 'products/GOZMZhX6rskOScOG26HR9LBAjErdaaM132OLSbwz.jpg', 1),
(43, '2026-09-27 15:09:50', '2026-09-27 15:09:50', 103, 8, 8, 'tota', 300.00, 1000, 'products/Ef3FSN2lTCroXpT2Bla0uYkPlCNOTnBEjW3DwbiK.jpg', 1);

-- --------------------------------------------------------

--
-- Table structure for table `rice_categories`
--

DROP TABLE IF EXISTS `rice_categories`;
CREATE TABLE IF NOT EXISTS `rice_categories` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rice_categories`
--

INSERT INTO `rice_categories` (`id`, `name`, `image`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Basmati', NULL, 1, '2026-09-14 01:55:03', '2026-09-14 01:55:03'),
(2, 'Extra Long Grain / Premium', NULL, 1, '2026-09-14 01:55:03', '2026-09-14 09:52:59'),
(3, 'Long Grain Non-Basmati', 'categories/ePKHRJEWIkCaobOcjMx02fw1F8eF2e7qonQecxo4.jpg', 1, '2026-09-14 01:55:03', '2026-09-14 21:34:36'),
(4, 'Medium Grain', NULL, 1, '2026-09-14 01:55:03', '2026-09-14 21:49:48'),
(5, 'Sella Rice', NULL, 1, '2026-09-14 01:55:03', '2026-09-14 01:55:03'),
(6, 'Steam Rice', NULL, 1, '2026-09-14 01:55:03', '2026-09-14 01:55:03'),
(7, 'Brown Rice', NULL, 1, '2026-09-14 01:55:04', '2026-09-14 09:53:04'),
(8, 'Broken Rice', 'categories/pMUXzZ7zEJJL7IPIzcxmXDMHaAVsLDnGtwiL6GA6.jpg', 1, '2026-09-14 01:55:04', '2026-09-14 09:53:19'),
(9, 'Hybrid Rice', NULL, 1, '2026-09-14 01:55:04', '2026-09-14 01:55:04'),
(10, 'Super Gold', NULL, 1, '2026-09-14 01:55:04', '2026-09-27 11:48:06'),
(11, 'Kainat Rice', NULL, 1, '2026-09-14 01:55:04', '2026-09-27 11:49:34'),
(12, 'Long Grain White Basmati', NULL, 1, '2026-09-14 01:55:04', '2026-09-14 01:55:04'),
(13, 'Jasmine Rice', 'categories/BLTZAlWfMgZst2J1kZmWdbBuu1WPxfcdRx298ik7.png', 1, '2026-09-14 21:50:35', '2026-09-21 16:09:26'),
(14, 'Short Grain', NULL, 1, '2026-09-21 16:09:34', '2026-09-21 16:09:34');

-- --------------------------------------------------------

--
-- Table structure for table `rice_types`
--

DROP TABLE IF EXISTS `rice_types`;
CREATE TABLE IF NOT EXISTS `rice_types` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'super_admin', 'web', '2026-05-17 00:12:56', '2026-05-23 01:27:44'),
(2, 'admin', 'web', '2026-05-17 00:12:56', '2026-05-23 01:27:44'),
(3, 'seller', 'web', '2026-05-17 00:12:56', '2026-05-23 01:27:44'),
(8, 'customer', 'web', '2026-05-22 03:30:21', '2026-05-23 01:27:44');

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

DROP TABLE IF EXISTS `role_has_permissions`;
CREATE TABLE IF NOT EXISTS `role_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `role_id` bigint UNSIGNED NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(8, 1),
(9, 1),
(10, 1),
(13, 1),
(14, 1),
(15, 1),
(16, 1),
(17, 1),
(20, 1),
(25, 1),
(27, 1),
(28, 1),
(29, 1),
(31, 1),
(32, 1),
(33, 1),
(34, 1),
(35, 1),
(38, 1),
(39, 1),
(40, 1),
(41, 1),
(43, 1),
(44, 1),
(45, 1),
(46, 1),
(47, 1),
(48, 1),
(49, 1),
(50, 1),
(51, 1),
(52, 1),
(53, 1),
(54, 1),
(55, 1),
(56, 1),
(57, 1),
(58, 1),
(59, 1),
(60, 1),
(61, 1),
(62, 1),
(63, 1),
(64, 1),
(65, 1),
(66, 1),
(67, 1),
(68, 1),
(69, 1),
(70, 1),
(71, 1),
(72, 1),
(73, 1),
(74, 1),
(75, 1),
(76, 1),
(77, 1),
(78, 1),
(79, 1),
(80, 1),
(81, 1),
(82, 1),
(83, 1),
(84, 1),
(85, 1),
(86, 1),
(87, 1),
(88, 1),
(89, 1),
(90, 1),
(91, 1),
(92, 1),
(93, 1),
(94, 1),
(95, 1),
(96, 1),
(97, 1),
(98, 1),
(99, 1),
(100, 1),
(101, 1),
(102, 1),
(103, 1),
(104, 1),
(105, 1),
(106, 1),
(107, 1),
(108, 1),
(109, 1),
(110, 1),
(111, 1),
(112, 1),
(113, 1),
(114, 1),
(115, 1),
(116, 1),
(117, 1),
(118, 1),
(119, 1),
(120, 1),
(121, 1),
(122, 1),
(123, 1),
(124, 1),
(125, 1),
(4, 2),
(8, 2),
(18, 2),
(19, 2),
(32, 2),
(35, 2),
(48, 2),
(58, 2),
(20, 3),
(25, 3),
(31, 3),
(32, 3),
(41, 3),
(47, 3),
(49, 3),
(50, 3),
(52, 3),
(54, 3),
(56, 3),
(57, 3),
(59, 3),
(61, 3),
(70, 3),
(73, 3),
(80, 3),
(83, 3),
(85, 3),
(86, 3),
(88, 3),
(89, 3),
(90, 3),
(98, 3),
(99, 3),
(100, 3),
(119, 3),
(121, 3),
(9, 8),
(31, 8),
(32, 8),
(38, 8),
(39, 8),
(40, 8),
(46, 8),
(49, 8),
(56, 8),
(59, 8),
(63, 8),
(64, 8),
(65, 8),
(66, 8),
(67, 8),
(68, 8),
(69, 8),
(72, 8),
(75, 8),
(76, 8),
(78, 8),
(79, 8),
(83, 8),
(84, 8),
(86, 8),
(89, 8),
(90, 8),
(91, 8),
(92, 8),
(93, 8),
(94, 8),
(95, 8),
(96, 8),
(121, 8);

-- --------------------------------------------------------

--
-- Table structure for table `seller_payouts`
--

DROP TABLE IF EXISTS `seller_payouts`;
CREATE TABLE IF NOT EXISTS `seller_payouts` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id` bigint UNSIGNED NOT NULL,
  `shop_id` bigint UNSIGNED NOT NULL,
  `gross_amount` decimal(10,2) NOT NULL,
  `commission_amount` decimal(10,2) NOT NULL,
  `net_amount` decimal(10,2) NOT NULL,
  `delivery_charge` decimal(10,2) NOT NULL DEFAULT '0.00',
  `status` enum('pending','ready','paid') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `payout_method` enum('easypaisa','jazzcash') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transaction_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `proof_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `paid_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `seller_payouts_order_id_foreign` (`order_id`),
  KEY `seller_payouts_shop_id_foreign` (`shop_id`),
  KEY `seller_payouts_paid_by_foreign` (`paid_by`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `seller_payouts`
--

INSERT INTO `seller_payouts` (`id`, `order_id`, `shop_id`, `gross_amount`, `commission_amount`, `net_amount`, `delivery_charge`, `status`, `payout_method`, `transaction_id`, `proof_path`, `paid_at`, `paid_by`, `created_at`, `updated_at`) VALUES
(25, 61, 1, 180.00, 9.00, 171.00, 1000.00, 'pending', NULL, NULL, NULL, NULL, NULL, '2026-09-26 15:04:37', '2026-09-26 15:04:37'),
(26, 63, 1, 343.00, 17.15, 325.85, 400.00, 'pending', NULL, NULL, NULL, NULL, NULL, '2026-09-26 15:09:02', '2026-09-26 15:09:02'),
(27, 64, 1, 180.00, 9.00, 171.00, 200.00, 'pending', NULL, NULL, NULL, NULL, NULL, '2026-09-26 15:23:34', '2026-09-26 15:23:34');

-- --------------------------------------------------------

--
-- Table structure for table `seller_removals`
--

DROP TABLE IF EXISTS `seller_removals`;
CREATE TABLE IF NOT EXISTS `seller_removals` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `shop_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `removed_by` bigint UNSIGNED NOT NULL,
  `reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `permanently_banned` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `seller_removals_shop_id_foreign` (`shop_id`),
  KEY `seller_removals_user_id_foreign` (`user_id`),
  KEY `seller_removals_removed_by_foreign` (`removed_by`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `seller_removals`
--

INSERT INTO `seller_removals` (`id`, `shop_id`, `user_id`, `removed_by`, `reason`, `permanently_banned`, `created_at`, `updated_at`) VALUES
(4, 2, 83, 15, 'Reason', 0, '2026-09-20 16:19:14', '2026-09-20 16:19:14');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
CREATE TABLE IF NOT EXISTS `settings` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_key_unique` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `key`, `value`, `created_at`, `updated_at`) VALUES
(1, 'super_admin_email', 'samiairshad090@gmail.com', '2026-08-24 00:03:38', '2026-08-24 00:03:38'),
(2, 'super_admin_phone', '+92302 4539786', '2026-08-24 00:03:38', '2026-08-24 00:03:38');

-- --------------------------------------------------------

--
-- Table structure for table `shops`
--

DROP TABLE IF EXISTS `shops`;
CREATE TABLE IF NOT EXISTS `shops` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `cnic` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `cnic_image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cnic_back_image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shop_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payout_easypaisa_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payout_easypaisa_account_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payout_jazzcash_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payout_jazzcash_account_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `is_approved` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `correction_reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `correction_requested_at` timestamp NULL DEFAULT NULL,
  `rejection_reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `shops_user_id_foreign` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `shops`
--

INSERT INTO `shops` (`id`, `user_id`, `cnic`, `cnic_image`, `cnic_back_image`, `shop_name`, `owner_name`, `phone`, `address`, `payout_easypaisa_number`, `payout_easypaisa_account_name`, `payout_jazzcash_number`, `payout_jazzcash_account_name`, `city`, `description`, `is_approved`, `created_at`, `updated_at`, `status`, `correction_reason`, `correction_requested_at`, `rejection_reason`) VALUES
(1, 81, '3410263117841', 'shops/cnic/G2rRQQMZ87iGSRbzMUmtPMCtikv7euXLuwaTFsow.jpg', 'shops/cnic/ae0OmjpbvB1JcOesYnL3WzZYtvIh2KH8sPT1yxcb.jpg', 'Anwar e Sidra', 'Asif Shahid', '03007488915', 'Galla mandi kamoke', NULL, NULL, '03003784786', 'Shahid ahmed khaskheli', 'kamoke', 'We have Premier quality rice with best prices Alhamdulillah 30+ years of serving the best quality rice', 1, '2026-09-20 15:35:02', '2026-09-23 16:48:24', 'approved', NULL, NULL, NULL),
(2, 83, '12345-123456-9', 'shops/cnic/e74OtMQxj0UWqfxKH3ZJxnm0fWajngIhYtkyCBj5.jpg', 'shops/cnic/1pZwRyPSWCZoNZhj85tX4ZU8oq7fSwzequCjB6H2.jpg', 'Samia', 'samia', '06465634', 'ghjk', NULL, NULL, NULL, NULL, 'cncndkkd', 'do', 1, '2026-09-20 16:13:49', '2026-09-20 16:19:14', 'removed', NULL, NULL, NULL),
(3, 85, '3410204405775', 'shops/cnic/cn3ujMz14QRVlcZKOPZaJ7XFJrcVDPr9VEwCFsEm.jpg', 'shops/cnic/L0gUr0zNk0n1GqCWoIBgSiiEFGPObFm1CiTA45YS.jpg', 'yousaf traders', 'Yousaf', '03004803483', 'salamat pura', NULL, NULL, NULL, NULL, 'kamoke', 'Quality rice', 0, '2026-09-21 15:24:35', '2026-09-24 04:48:46', 'pending', 'Your Cnic is expired  plz re-issue your cnic', '2026-09-24 04:48:46', NULL),
(4, 90, '3410204541001', 'shops/cnic/pq7U98fHyKjm7b4S2eIyLixJfZtIWidRIi8g9eR9.jpg', 'shops/cnic/CqsFZqe8gG4etmaTPwiF1AvUieREFqEzKQ7YO5UQ.jpg', 'Al hadi Enterprises', 'Qadeer Abbas', '03127548691', 'head office:Gallah mandi kamoke sub office:gallah mandi faisalabad', NULL, NULL, '03213436518', 'Qadeer Abbas', 'kamoke', NULL, 1, '2026-09-21 15:41:29', '2026-09-24 02:22:37', 'approved', NULL, NULL, NULL),
(5, 86, '3410204477589', 'shops/cnic/L6pMg2hHJlJubbqEh1H7jUCcIFn8iPC7Lwon2k72.jpg', 'shops/cnic/JlQRumYBNgcapuswQoxVrlBvvUiQRI3A23Vgfxm0.jpg', 'Ayub rice traders', 'Shafi ayub', '03078611791', 'gallah mandi kamoke', NULL, NULL, '03076426772', 'Muhammad shafeh', 'kamoke', NULL, 1, '2026-09-21 15:47:50', '2026-09-23 16:42:34', 'approved', NULL, NULL, NULL),
(6, 88, '3410268201993', 'shops/cnic/dpcA1X34ACp1Ijm7ZbqTF0uUIJN1lJbaEQsdfYmd.jpg', 'shops/cnic/sJOOHdzVuKUCbXM0xCYJfWmYJUgM6wOK1irUdPSJ.jpg', 'Ayub Rice traders', 'Shafi ayub', '03078611791', 'kamoke ghalla mandi', NULL, NULL, NULL, NULL, 'kamoke', NULL, 0, '2026-09-21 15:52:01', '2026-09-21 15:55:59', 'rejected', NULL, NULL, 'Already has the shop Shafi Rice traders and also the owner name is shafi .Your cnic also blur....Plz enter clear picture of CNIC'),
(8, 103, '3410259546290', 'shops/cnic/Q6aPLnlIFHAEZjmxmjEeieTUfgzWDWarvHXTs5IX.jpg', 'shops/cnic/Nbl4vMVArz70o7mYXI00a44l1BE0TyxpI0wC5zwQ.jpg', 'test shop', 'test', '03024539786', 'kamoke', NULL, NULL, NULL, NULL, 'kamoke', 'Don\'t order from that shop ....we created this for testing purpose', 1, '2026-09-27 13:06:46', '2026-09-27 15:16:14', 'approved', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `shop_reviews`
--

DROP TABLE IF EXISTS `shop_reviews`;
CREATE TABLE IF NOT EXISTS `shop_reviews` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `customer_id` bigint UNSIGNED NOT NULL,
  `order_item_id` bigint UNSIGNED NOT NULL,
  `shop_id` bigint UNSIGNED NOT NULL,
  `rating` int NOT NULL,
  `review` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `shop_reviews_customer_id_order_item_id_unique` (`customer_id`,`order_item_id`),
  KEY `shop_reviews_order_item_id_foreign` (`order_item_id`),
  KEY `shop_reviews_shop_id_foreign` (`shop_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `account_status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `removed_reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `removed_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `otp` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `otp_expires_at` timestamp NULL DEFAULT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT '0',
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=104 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `account_status`, `removed_reason`, `removed_at`, `password`, `otp`, `otp_expires_at`, `is_verified`, `remember_token`, `created_at`, `updated_at`) VALUES
(15, 'Super Admin', 'ricemartaisystem@gmail.com', '2026-08-21 01:54:54', 'active', NULL, NULL, '$2y$12$DaE3JE20kzAXkdGpI5XpfOiOmVAJprQlRJQt7k0g3j1GE3JEO4eWC', NULL, NULL, 1, NULL, '2026-08-21 01:54:54', '2026-08-21 01:54:54'),
(34, 'Bisma', 'bisma272727@gmail.com', NULL, 'active', NULL, NULL, '$2y$12$DaE3JE20kzAXkdGpI5XpfOiOmVAJprQlRJQt7k0g3j1GE3JEO4eWC', NULL, NULL, 1, NULL, '2026-09-20 12:03:11', '2026-09-20 12:03:40'),
(36, 'Riasat Ali', 'aleeshabsit@gmail.com', NULL, 'active', NULL, NULL, '$2y$12$DcN5O8LsmMagL1W4oB6/ieghitIH5Bu.1xeisFs2V8Od0R3lL/vz2', NULL, NULL, 1, NULL, '2026-09-20 12:46:29', '2026-09-20 12:46:29'),
(39, 'Sam', 'samnoor673@gmail.com', NULL, 'active', NULL, NULL, '$2y$12$GB1FFIgJVwwVKX0GGaQiouLOBxWCQgxQePwl/ENO5qngGajKoCrVe', NULL, NULL, 1, NULL, '2026-09-20 14:32:06', '2026-09-20 14:33:13'),
(40, 'Saher', 'saher79914@gmail.com', NULL, 'active', NULL, NULL, '$2y$12$cxxtgydbYE/lmg/vIl832eyPh2cnzXMjj8WZIr7GLFSo3gDSOSZtm', NULL, NULL, 1, NULL, '2026-09-20 15:01:19', '2026-09-20 15:01:19'),
(41, 'ayesha', 'ayesha079881@gmail.com', NULL, 'active', NULL, NULL, '$2y$12$dE3ZuAdHa8tMKoH3rikLc.moBcj6b.eU0iCYQTD3JMA3Xj1hrV/Lu', NULL, NULL, 1, NULL, '2026-09-20 15:01:38', '2026-09-20 15:01:38'),
(42, 'ayesha farooq', '5834389ayesha@gmail.com', NULL, 'active', NULL, NULL, '$2y$12$0Qph62MpSD5QJeAtTU8Bju4Z7flxWib5WimnUarrItAP07NuSLIdS', NULL, NULL, 1, NULL, '2026-09-20 15:02:08', '2026-09-20 15:02:08'),
(43, 'javeria', 'ccevriya@gmail.com', NULL, 'active', NULL, NULL, '$2y$12$sj2FQ9jjMLOuQkKUeAVzXuNzqyLAYD8YUMmOlTXKBwDXz2glE7BTq', NULL, NULL, 1, NULL, '2026-09-20 15:02:19', '2026-09-20 15:02:19'),
(44, 'kashaf', 'kashaf9948@gmail.com', NULL, 'active', NULL, NULL, '$2y$12$.BtqSE53E1l8/ToHv7McdeBomOocF9p7s3ftXM9eRk214yyi/Czo2', NULL, NULL, 1, NULL, '2026-09-20 15:02:34', '2026-09-20 15:02:34'),
(45, 'Atiya', 'atiyatariqali@gmail.com', NULL, 'active', NULL, NULL, '$2y$12$dKoFp2h8iLlFwcPTCZw2w.eZob.p2nvhIEzpeqg3xi42zTksvIbVy', NULL, NULL, 1, NULL, '2026-09-20 15:03:00', '2026-09-20 15:03:00'),
(46, 'zoha', 'zoha51013@gmail.com', NULL, 'active', NULL, NULL, '$2y$12$HjPVEzFDldYwPuLosUs8BOdIdUF6JQoLQ1NmrjR/BIR1d6j830j26', NULL, NULL, 1, NULL, '2026-09-20 15:03:19', '2026-09-20 15:03:19'),
(47, 'Arshad', 'arshadghuman555@gmail.com', NULL, 'active', NULL, NULL, '$2y$12$smbexmjHdfpzNPMMgHkT3ORNolABLKPf.Jar5Kpis00jpUcz626ze', NULL, NULL, 1, NULL, '2026-09-20 15:03:47', '2026-09-20 15:03:47'),
(48, 'eman', 'feman9436@gmail.com', NULL, 'active', NULL, NULL, '$2y$12$.n6SvlKZsIpIYMVUcmBteelRK8udHLBjxQQhhcNNDnrCgM36Xz11u', NULL, NULL, 1, NULL, '2026-09-20 15:04:03', '2026-09-20 15:04:03'),
(49, 'Gul', 'agul6411@gmail.com', NULL, 'active', NULL, NULL, '$2y$12$H5XzwPmTqPsF0lSxXNXrAu3RFvEiqYGKZRYTS4CyM00uxgjuonN4W', NULL, NULL, 1, NULL, '2026-09-20 15:04:17', '2026-09-20 15:04:17'),
(50, 'Nimara', 'nimrashamrez@gmail.com', NULL, 'active', NULL, NULL, '$2y$12$xNEgM2AGGwSZhbhKYag9tuVQjLf0.uRccMGT6.e4Ov4sNEoN.RB/i', NULL, NULL, 1, NULL, '2026-09-20 15:04:40', '2026-09-20 15:04:40'),
(51, 'Benish', 'benishbatoolkmk@gmail.com', NULL, 'active', NULL, NULL, '$2y$12$yIxTbdBllKueaY9AhgZe7utRJH3D70pnFA4CQcKmLTzyYBfLOrlti', NULL, NULL, 1, NULL, '2026-09-20 15:05:05', '2026-09-20 15:05:05'),
(52, 'zobia', 'batoolzobia14@gmail.com', NULL, 'active', NULL, NULL, '$2y$12$22h.KTq9pdWbCz1hM8FkV.kW/jVpiDLxv.l2B0WeJFJPGJmRfDdhq', NULL, NULL, 1, NULL, '2026-09-20 15:05:24', '2026-09-20 15:05:24'),
(53, 'Eman', 'manobilieman11@gmail.com', NULL, 'active', NULL, NULL, '$2y$12$4xDIC0PtwpU9b1svjXbiV.3TL/Diu6Hi.GkbVnThnngTtF4.Z9uwe', NULL, NULL, 1, NULL, '2026-09-20 15:06:03', '2026-09-20 15:06:03'),
(54, 'pakiza', 'pakizahranarana@gamil.com', NULL, 'active', NULL, NULL, '$2y$12$8X0CPJ13LLFVjI3Raf2rMO/HY8n.JQFb6COaYF/KK/281LCIPZu6W', NULL, NULL, 1, NULL, '2026-09-20 15:06:35', '2026-09-20 15:06:35'),
(55, 'irha', 'irhashahid7890@gmail.con', NULL, 'active', NULL, NULL, '$2y$12$x6hBxGPOHJLa2B/sUgQ0q.mOSQaX6.C90lEeL9nwhCi84W9RnER4K', NULL, NULL, 1, NULL, '2026-09-20 15:06:49', '2026-09-20 15:06:49'),
(56, 'ayesha liaquat', 'ayeshaliaquatali37@gmail.com', NULL, 'active', NULL, NULL, '$2y$12$MDWN/PkKXc.9k1MX0AhD2.fA2z1QJ9IZEaNWFA/z8oRYW.Zljbybm', NULL, NULL, 1, NULL, '2026-09-20 15:07:18', '2026-09-20 15:07:18'),
(57, 'sundas', 'sundassafder046@gmail.com', NULL, 'active', NULL, NULL, '$2y$12$vRDsbHl3LEPaqyLu.z0L2.bGvPt.CpWxDg.T.aQN7/zYhzOJth3xW', NULL, NULL, 1, NULL, '2026-09-20 15:07:36', '2026-09-20 15:07:36'),
(58, 'Sidra bibi', 'sidrabibi4675@gmail.com', NULL, 'active', NULL, NULL, '$2y$12$vJI7qeaz/6baEFsHhWGkmuC.Z1s1mmGYptkSIb7JjMoe0o5nllidu', NULL, NULL, 1, NULL, '2026-09-20 15:07:52', '2026-09-20 15:07:52'),
(59, 'Maheen', 'maheenarif038@gmail.com', NULL, 'active', NULL, NULL, '$2y$12$fxLRs4pBsTV9nxNGRIO0m.GF9B/o1rbFNAMW9brpRRoSwUfl94llC', NULL, NULL, 1, NULL, '2026-09-20 15:08:02', '2026-09-20 15:08:02'),
(60, 'Insa', 'seemabinsa@gmail.com', NULL, 'active', NULL, NULL, '$2y$12$h8S03qRYbSsG18QAuxpLaOGQhXjHgRTf6OEw7JrLuHDhDcjPFdD7O', NULL, NULL, 1, NULL, '2026-09-20 15:08:18', '2026-09-20 15:08:18'),
(61, 'Adiya', 'adiyasohail5@gmail.com', NULL, 'active', NULL, NULL, '$2y$12$gsxKz6A81lin8wyUtkaGR.OEHG5kKDfnhsq9FuUVGy6Rbm9BQyeQq', NULL, NULL, 1, NULL, '2026-09-20 15:08:35', '2026-09-20 15:08:35'),
(62, 'hamna', 'hamnamughal259@gmail.com', NULL, 'removed', 'test', '2026-09-23 15:45:28', '$2y$12$gXWW6N7jawkywRHNT7pNC.BUXCYldgRFSY7jDqUPqfSIyluikUqV2', NULL, NULL, 1, NULL, '2026-09-20 15:10:56', '2026-09-23 15:45:28'),
(63, 'Aslam', 'kambohbrand49@gmail.com', NULL, 'active', NULL, NULL, '$2y$12$S.MCxwk5Kfq.653d7FCdSO/ni91j1BAXGflpt8EFtcJu9N8YoeC0O', NULL, NULL, 1, NULL, '2026-09-20 15:11:19', '2026-09-20 15:11:19'),
(64, 'Rida', 'kambohb098@gmail.com', NULL, 'active', NULL, NULL, '$2y$12$LARfeSyGfVNjvoBH5Z4mEewlBaUwAPpKu7txJ57VdsCaFDEgEAxjm', NULL, NULL, 1, NULL, '2026-09-20 15:11:38', '2026-09-20 15:11:38'),
(65, 'Arham', 'arhammuhamedijaz@gmail.com', NULL, 'active', NULL, NULL, '$2y$12$2ZiY9X6g5T.F3noEGtIz3OF4eS8E.9AlNDgKmQ2.m1tM3vdzhdMY6', NULL, NULL, 1, NULL, '2026-09-20 15:12:16', '2026-09-20 15:12:16'),
(66, 'Arham mughal', 'arhammuhamad008@gmail.com', NULL, 'active', NULL, NULL, '$2y$12$XY1q9C5AxbX2cGaGcgEnNOGk9XGuLwOqdOApdPHfGqtIM8EQa5IIS', NULL, NULL, 1, NULL, '2026-09-20 15:12:49', '2026-09-20 15:12:49'),
(67, 'Alishba', 'aneey377@gmail.com', NULL, 'active', NULL, NULL, '$2y$12$vdyn8x1FEgssfCZD/FrMGOzKV2VxlPe9haTBTlFZlafcV3igO5h/O', NULL, NULL, 1, NULL, '2026-09-20 15:13:29', '2026-09-20 15:13:29'),
(68, 'Ayesha malik', 'mmalikprince207@gmail.com', NULL, 'active', NULL, NULL, '$2y$12$bZLnWA8XEyJCkT7CHaTR7eSkL5iA2T4SXvt6WfahOev58wUCVQ9pS', NULL, NULL, 1, NULL, '2026-09-20 15:13:55', '2026-09-20 15:13:55'),
(69, 'Minahil', 'minamehar09@gmail.com', NULL, 'active', NULL, NULL, '$2y$12$ZjIAGRBPuuc4eI0hQ6OWeuzHrqSsd82cbuiQP2SCwdJhZEANn1yQm', NULL, NULL, 1, NULL, '2026-09-20 15:14:57', '2026-09-20 15:14:57'),
(70, 'Feeza', 'strana981@gmail.com', NULL, 'active', NULL, NULL, '$2y$12$WiVmfgm48.2iCXIVZrmHa..nLUY3noMxhvBxAd8EtQpJhr/gEeZ.q', NULL, NULL, 1, NULL, '2026-09-20 15:15:26', '2026-09-20 15:15:26'),
(71, 'Fareed', 'gulamfareed1212az@gmail.com', NULL, 'active', NULL, NULL, '$2y$12$OQjS9d2GMKeqqAAwX7sQmu6QL2eHg/ILhdqdZH4b3V70WTDGBYKO.', NULL, NULL, 1, NULL, '2026-09-20 15:15:47', '2026-09-20 15:15:47'),
(72, 'sawera', 'swairahmuneem@Email.com', NULL, 'active', NULL, NULL, '$2y$12$U0LJhHb6neFwFF7qQ429u.xnHotVOfwFqKFuJfze2AdMTkXJnML5i', NULL, NULL, 1, NULL, '2026-09-20 15:16:22', '2026-09-20 15:16:22'),
(73, 'Alishba shabbir', 'rajashabeer903@gmail.com', NULL, 'active', NULL, NULL, '$2y$12$DmuoBEsuDhCm1CEAjQMX4eNZFSF9.YPmNiiCWbLi.FLtpBrj8WziC', NULL, NULL, 1, NULL, '2026-09-20 15:16:51', '2026-09-20 15:16:51'),
(74, 'Rimza sabir', 'gondalsahiba99@gmail.com', NULL, 'active', NULL, NULL, '$2y$12$OtHstRYbiERkJplRZ0nQ3.i6PaVzdI68pweDjU07gJHEAfiNuv2Y2', NULL, NULL, 1, NULL, '2026-09-20 15:17:19', '2026-09-20 15:17:19'),
(75, 'parvaiz', 'kallisparvez8610@gmail.com', NULL, 'active', NULL, NULL, '$2y$12$dxeCqYryRSrtadoLa47DYuVKdzhjPiwf.HMtORn8EcfoSo1.w44s6', NULL, NULL, 1, NULL, '2026-09-20 15:18:10', '2026-09-20 15:18:10'),
(76, 'Zaibun Nisa', 'zaibulnisashabbir@gmail.com', NULL, 'active', NULL, NULL, '$2y$12$VUQ1lK9E61KlxKJKt7eDmuCPrwMhSZSaT7qD8qoEQHEDkLSJX/hfm', NULL, NULL, 1, NULL, '2026-09-20 15:18:42', '2026-09-20 15:18:42'),
(77, 'hameed', 'hameedamjad8877@gmail.com', NULL, 'active', NULL, NULL, '$2y$12$/V66IgnN8nKMeognqdAEteckBgwY54DuzNRwZ2xduDvTQOR/u4162', NULL, NULL, 1, NULL, '2026-09-20 15:19:30', '2026-09-20 15:19:30'),
(78, 'Zayan', 'zayanahmadrana99@gmail.com', NULL, 'active', NULL, NULL, '$2y$12$yuVHmH9vI/nTVXOSsNVW..Mcsj1nJMviQhr3amiosHP5NsWvR0r0q', NULL, NULL, 1, NULL, '2026-09-20 15:20:04', '2026-09-20 15:20:04'),
(79, 'Eman shahzadi', 'mughalgii9988@gmail.com', NULL, 'active', NULL, NULL, '$2y$12$czjKFMlKEU/3YqUis0YqgeFi1xVqF9F7yOU/LGAxfUku2cVXBDYhW', NULL, NULL, 1, NULL, '2026-09-20 15:20:38', '2026-09-20 15:20:38'),
(80, 'Qasim', 'muhammad.qasim.dev07@gmail.com', NULL, 'active', NULL, NULL, '$2y$12$2erjXPE2pX4CIQvOFMKIC.wT.Brmywq8wpObGBcU5Jxz.2j8MUkl6', NULL, NULL, 1, NULL, '2026-09-20 15:23:09', '2026-09-20 15:23:09'),
(81, 'Asif Shahid', 'shabir7488915@gmail.com', NULL, 'active', NULL, NULL, '$2y$12$Q3z9qDD/yiMi5IEqR451xesjv4s2TghR2GWUhkMHehA4IsaqHvhpq', NULL, NULL, 1, NULL, '2026-09-20 15:27:09', '2026-09-22 15:30:46'),
(82, 'Shoaib', 'Shoaibshafi069@gmail.com', NULL, 'active', NULL, NULL, '$2y$12$MVhSXleaXOquyrSwpxJ5Y.flYJqAaftOeAHKOCCYjgS0ic8KU1MUS', NULL, NULL, 1, NULL, '2026-09-20 15:28:20', '2026-09-20 15:28:20'),
(83, 'samia', 'samiairshad498@gmail.com', NULL, 'active', NULL, NULL, '$2y$12$CcSdZZwQIxj/Nha1PI8x2uk5hgOSTZT0NuNnsLvqt5ZbaLsMF3f6K', NULL, NULL, 1, NULL, '2026-09-20 16:12:09', '2026-09-20 16:21:32'),
(84, 'swera', 'sawerawaheed778@gmail.com', NULL, 'active', NULL, NULL, '$2y$12$zZi/9jsuCo5ZSSCF6fnlLeHvAiwndht3wVYpP/mcO4INyApTOBAy2', NULL, NULL, 1, NULL, '2026-09-21 08:50:22', '2026-09-21 08:50:22'),
(85, 'Muhammad yousaf', 'sbiiirajpot@gmail.com', NULL, 'active', NULL, NULL, '$2y$12$Hfku5c7P88TmShwa9WYzfev5Zv2PCFZb4l.hTOx77K0MBpFvV3K2S', NULL, NULL, 1, NULL, '2026-09-21 15:01:32', '2026-09-21 15:01:32'),
(86, 'Muhammad Shafi', 'manopapi431@gmail.com', NULL, 'active', NULL, NULL, '$2y$12$bsHyleRKZp.BeG4U9jz67.yfVku9DxYwyEzrncfNeiZVqAPOsfCrO', NULL, NULL, 1, NULL, '2026-09-21 15:29:20', '2026-09-23 16:41:51'),
(87, 'Sidra sufyan', 'Sdrasuffi821@gmail.com', NULL, 'active', NULL, NULL, '$2y$12$8OMjZVps7hCJGqaEY1hWGulAp8sHsNiduYpaeEbuoGsfbsydPNwU2', NULL, NULL, 1, NULL, '2026-09-21 15:29:41', '2026-09-21 15:29:53'),
(88, 'SHOAIB', 'happybrand046@gmail.com', NULL, 'active', NULL, NULL, '$2y$12$KWqiF7EwgbCOnjPbhY2YKOZmhuzDXsDj2v1N.EyVtEjgOsjsbk8YS', NULL, NULL, 1, NULL, '2026-09-21 15:30:19', '2026-09-21 15:49:33'),
(89, 'waleed', 'waleedahmad7766@gmail.com', NULL, 'active', NULL, NULL, '$2y$12$w7IHJtZRU2OFUo0UDdEppujcukPHuMGGAirh3eL5sV5r/TVtKIbQ.', NULL, NULL, 1, NULL, '2026-09-21 15:31:17', '2026-09-21 15:31:17'),
(90, 'Hadi', 'alhadienterprises51@gmail.com', NULL, 'active', NULL, NULL, '$2y$12$WFbb41cP2B5TXblbtfVS0uUV3f5QDOYOQqjYG8Re8H/lLtNXIhddq', NULL, NULL, 1, NULL, '2026-09-21 15:33:22', '2026-09-21 15:33:22'),
(91, 'Umer', 'asky55601@gmail.com', NULL, 'active', NULL, NULL, '$2y$12$.fKwD.dxlMGgAKfyzamLLuz4oO69okmEhVHtytuKD2XVEaHAFai5m', NULL, NULL, 1, NULL, '2026-09-21 15:34:23', '2026-09-21 15:34:23'),
(92, 'Sheeza', 'sheezasheeza938@gmail.com', NULL, 'active', NULL, NULL, '$2y$12$3mhMptkeo1ZdWfaeBt6.0.JQX145dL0yq9KR//fO5amJFr6wxPDz2', NULL, NULL, 1, NULL, '2026-09-21 16:21:51', '2026-09-21 16:21:51'),
(93, 'Amir', 'amirshahraza.312@gmail.com', NULL, 'active', NULL, NULL, '$2y$12$ZaNjAGEnswqi9UrdbcDryeauPCRRUnfYZyjdMCvADeQpJ7SEyA64W', NULL, NULL, 1, NULL, '2026-09-21 16:26:28', '2026-09-21 16:26:28'),
(94, 'Yousaf', 'muqadasyousaf7777@gmail.com', NULL, 'active', NULL, NULL, '$2y$12$gQ21B9Vrky5ULtKSBnYHSOYSuIIbJ4Ju7XkIkNDjAbMQsiD.a7Fe2', NULL, NULL, 1, NULL, '2026-09-21 16:27:26', '2026-09-21 16:27:26'),
(95, 'Tanveer', 'tm8031443@gmail.com', NULL, 'active', NULL, NULL, '$2y$12$xYLs1x6muxYDT1AzfODiIeUKH5dun0I/fz6eWWXdDkd/D2AZcbvIu', NULL, NULL, 1, NULL, '2026-09-21 16:27:48', '2026-09-21 16:27:48'),
(96, 'mahnoor', 'mahnoortanveerm@gmail.com', NULL, 'active', NULL, NULL, '$2y$12$ceLGEKz9w4PeFqxodW5l/ePzHtoZB7VzWkt409bCfn/QJcYakAyK.', NULL, NULL, 1, NULL, '2026-09-21 16:27:59', '2026-09-21 16:27:59'),
(97, 'Sidra', 'sidrasyed564@gmail.com', NULL, 'active', NULL, NULL, '$2y$12$H/q8ZK/r7R7oaRVQu41HOOW/6pzl5IExVC/68/AeA9AvUOE2R/FcW', NULL, NULL, 1, NULL, '2026-09-21 16:28:11', '2026-09-21 16:28:11'),
(98, 'Ali', 'mywork100107@gmail.com', NULL, 'active', NULL, NULL, '$2y$12$12Qyb8YcilDSTryuE0yEyuweOLaWHgBPBvBptzI4DiGebmm7nmMMi', NULL, NULL, 1, NULL, '2026-09-21 16:28:23', '2026-09-21 16:28:23'),
(99, 'Jamshed', 'jamshaidnusrat0@gmail.com', NULL, 'active', NULL, NULL, '$2y$12$/RzhtqnHU1y9aNTRg7Pa6.ItDzVO4D4pHcFCKb.3Ju5j0iowOBMSO', NULL, NULL, 1, NULL, '2026-09-21 16:28:39', '2026-09-21 16:28:39'),
(100, 'shawal', 'shawaltayyab@gmail.com', NULL, 'active', NULL, NULL, '$2y$12$QKjWGF0W.T83xtZtiD6m/u8D61XaN1.HbRhbIQSZNlWo9tbsP6g4C', '307979', '2026-09-22 17:46:08', 0, NULL, '2026-09-22 17:33:30', '2026-09-22 17:36:08'),
(101, 'shawal', 'shawaltayyab635@gmail.com', NULL, 'active', NULL, NULL, '$2y$12$KZ2/ztv6VsWTT5owcr1iouR117tqeRbYBM8CWejFr7REwR30ZiOki', NULL, NULL, 1, NULL, '2026-09-22 17:37:12', '2026-09-22 17:47:12'),
(102, 'Waji', 'waji@gmail.com', NULL, 'active', NULL, NULL, '$2y$12$jy71l5G7kBXpqAEMLNmG6.sCduxoOxW7VoZdo8oQAEAvsuapFs6iC', NULL, NULL, 1, NULL, '2026-09-25 13:40:34', '2026-09-25 13:40:34'),
(103, 'test shop', 'adibajabeen02@gmail.com', NULL, 'active', NULL, NULL, '$2y$12$RlWjYeobcoMvJ89.IYxQUe3vrrxMEOeZhGPi0811GsL7NjamotPnm', NULL, NULL, 1, NULL, '2026-09-27 13:06:46', '2026-09-27 13:06:46');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `banned_emails`
--
ALTER TABLE `banned_emails`
  ADD CONSTRAINT `banned_emails_banned_by_foreign` FOREIGN KEY (`banned_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `commission_settings`
--
ALTER TABLE `commission_settings`
  ADD CONSTRAINT `commission_settings_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `complaints`
--
ALTER TABLE `complaints`
  ADD CONSTRAINT `complaints_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `complaint_messages`
--
ALTER TABLE `complaint_messages`
  ADD CONSTRAINT `complaint_messages_complaint_id_foreign` FOREIGN KEY (`complaint_id`) REFERENCES `complaints` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `complaint_messages_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `conversations`
--
ALTER TABLE `conversations`
  ADD CONSTRAINT `conversations_buyer_id_foreign` FOREIGN KEY (`buyer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `conversations_shop_id_foreign` FOREIGN KEY (`shop_id`) REFERENCES `shops` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `courier_charges`
--
ALTER TABLE `courier_charges`
  ADD CONSTRAINT `courier_charges_city_id_foreign` FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_conversation_id_foreign` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications_app`
--
ALTER TABLE `notifications_app`
  ADD CONSTRAINT `notifications_app_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_city_id_foreign` FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_shop_id_foreign` FOREIGN KEY (`shop_id`) REFERENCES `shops` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_shop_charges`
--
ALTER TABLE `order_shop_charges`
  ADD CONSTRAINT `order_shop_charges_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_shop_charges_shop_id_foreign` FOREIGN KEY (`shop_id`) REFERENCES `shops` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_verified_by_foreign` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_rice_category_id_foreign` FOREIGN KEY (`rice_category_id`) REFERENCES `rice_categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `products_shop_id_foreign` FOREIGN KEY (`shop_id`) REFERENCES `shops` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `products_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `seller_payouts`
--
ALTER TABLE `seller_payouts`
  ADD CONSTRAINT `seller_payouts_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `seller_payouts_paid_by_foreign` FOREIGN KEY (`paid_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `seller_payouts_shop_id_foreign` FOREIGN KEY (`shop_id`) REFERENCES `shops` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `seller_removals`
--
ALTER TABLE `seller_removals`
  ADD CONSTRAINT `seller_removals_removed_by_foreign` FOREIGN KEY (`removed_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `seller_removals_shop_id_foreign` FOREIGN KEY (`shop_id`) REFERENCES `shops` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `seller_removals_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `shops`
--
ALTER TABLE `shops`
  ADD CONSTRAINT `shops_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `shop_reviews`
--
ALTER TABLE `shop_reviews`
  ADD CONSTRAINT `shop_reviews_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `shop_reviews_order_item_id_foreign` FOREIGN KEY (`order_item_id`) REFERENCES `order_items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `shop_reviews_shop_id_foreign` FOREIGN KEY (`shop_id`) REFERENCES `shops` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
