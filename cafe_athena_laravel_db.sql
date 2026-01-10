-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: cafe_athena_laravel
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'2025_12_29_025402_create_product_table',1),(4,'2025_12_29_025403_create_orders_table',1),(5,'2025_12_29_025404_create_order_item_table',1),(6,'2026_01_10_032813_add_soft_deletes_to_user_and_product_tables',2);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_item`
--

DROP TABLE IF EXISTS `order_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order_item` (
  `orderitem_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `orderitem_quantity` int(11) NOT NULL DEFAULT 1,
  `orderitem_price` decimal(10,2) NOT NULL,
  `orderitem_subtotal` decimal(10,2) NOT NULL,
  `orderitem_notes` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`orderitem_id`),
  KEY `order_item_order_id_foreign` (`order_id`),
  KEY `order_item_product_id_foreign` (`product_id`),
  CONSTRAINT `order_item_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  CONSTRAINT `order_item_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_item`
--

LOCK TABLES `order_item` WRITE;
/*!40000 ALTER TABLE `order_item` DISABLE KEYS */;
INSERT INTO `order_item` VALUES (1,9,1,1,850.00,850.00,NULL),(2,10,2,1,550.00,550.00,NULL),(3,10,12,1,220.00,220.00,NULL);
/*!40000 ALTER TABLE `order_item` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orders` (
  `order_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `order_status` enum('pending','preparing','ready','completed','cancelled') NOT NULL DEFAULT 'pending',
  `order_type` enum('pickup','delivery') NOT NULL DEFAULT 'pickup',
  `order_total` decimal(10,2) NOT NULL,
  `order_payment_method` enum('cash','card','gcash','paymaya') NOT NULL DEFAULT 'cash',
  `order_payment_status` enum('unpaid','paid') NOT NULL DEFAULT 'unpaid',
  `order_notes` text DEFAULT NULL,
  `order_delivery_address` text DEFAULT NULL,
  `order_createdat` datetime NOT NULL DEFAULT current_timestamp(),
  `order_updatedat` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `order_completedat` datetime DEFAULT NULL,
  `product_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`order_id`),
  KEY `orders_user_id_foreign` (`user_id`),
  KEY `orders_product_id_foreign` (`product_id`),
  CONSTRAINT `orders_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE CASCADE,
  CONSTRAINT `orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (9,7,'pending','pickup',850.00,'cash','unpaid',NULL,'223 Ortega Street, corner A. Mabini Street, Brgy. Addition Hills, San Juan City','2026-01-10 03:01:27','2026-01-10 03:01:27',NULL,NULL),(10,7,'pending','pickup',770.00,'card','paid',NULL,'223 Ortega Street, corner A. Mabini Street, Brgy. Addition Hills, San Juan City','2026-01-10 03:02:00','2026-01-10 03:02:00',NULL,NULL);
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product`
--

DROP TABLE IF EXISTS `product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product` (
  `product_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_name` varchar(200) NOT NULL,
  `product_description` text DEFAULT NULL,
  `product_price` decimal(10,2) NOT NULL,
  `product_image` varchar(255) DEFAULT NULL,
  `product_status` enum('available','unavailable') NOT NULL DEFAULT 'available',
  `product_category` varchar(100) NOT NULL,
  `product_featured` tinyint(4) NOT NULL DEFAULT 0,
  `product_createdat` datetime NOT NULL DEFAULT current_timestamp(),
  `product_updatedat` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `product_deletedat` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product`
--

LOCK TABLES `product` WRITE;
/*!40000 ALTER TABLE `product` DISABLE KEYS */;
INSERT INTO `product` VALUES (1,'Philosopher\'s Reserve','Single Origin (Exclusive Lot)',850.00,'assets/uploads/coffee-beans/philosopher-s-reserve-single-origin.webp','available','Coffee Beans',1,'2025-11-07 14:15:51','2025-11-08 11:12:45',NULL),(2,'The Parthenon Blend','House Blend (Balanced Arabica & Robusta)',550.00,'assets/uploads/coffee-beans/the-parthenon-blend-house-blend.webp','available','Coffee Beans',1,'2025-11-07 14:15:51','2025-11-08 11:12:54',NULL),(3,'The Catalyst Shot','Pure Double Shot Espresso (Single-Origin Base)',160.00,'assets/uploads/hot-brew/the-catalyst-shot-espresso.webp','available','Hot Brew',1,'2025-11-07 14:15:51','2025-11-08 11:13:06',NULL),(4,'The Strategist','Espresso with Steamed Milk and Fine Foam',210.00,'assets/uploads/hot-brew/the-strategist-latte.webp','available','Hot Brew',1,'2025-11-07 14:15:51','2025-11-08 11:13:16',NULL),(9,'Attic Ice','Classic Cold Brew Coffee (Low Acidity, High Caffeine)',190.00,'assets/uploads/iced-&-cold/attic-ice-cold-brew.webp','available','Iced & Cold',1,'2025-11-07 14:15:51','2025-11-08 11:17:18',NULL),(10,'The Oracle','Mocha (Iced Chocolate & Espresso Blend)',230.00,'assets/uploads/iced-&-cold/the-oracle-mocha.webp','available','Iced & Cold',1,'2025-11-07 14:15:51','2025-11-08 11:17:34',NULL),(11,'Tempest Freeze','Signature Blended Coffee Drink (Frappuccino)',250.00,'assets/uploads/iced-&-cold/tempest-freeze-frap.webp','available','Iced & Cold',1,'2025-11-07 14:15:51','2025-11-08 11:17:48',NULL),(12,'The Aegis','Espresso with Equal Parts Steamed Milk and Thick Foam',220.00,'assets/uploads/hot-brew/the-aegis-cappucino.webp','available','Hot Brew',1,'2025-11-08 11:16:35','2025-11-08 11:16:35',NULL),(13,'The Weaver\'s Scroll','Savory Spinach & Feta Phyllo Pie (Spanakopita)',280.00,'assets/uploads/pastry/spanakopita.webp','available','Pastry',1,'2025-11-08 11:18:41','2025-11-08 11:18:41',NULL),(14,'Golden Honeycomb','Layered Nut & Honey Phyllo Dessert (Baklava)',190.00,'assets/uploads/pastry/baklava.webp','available','Pastry',1,'2025-11-08 11:19:02','2025-11-08 11:19:02',NULL),(15,'Custodian\'s Custard','Creamy Semolina Custard Phyllo Pie (Galaktoboureko)',210.00,'assets/uploads/pastry/galkatoboureko.webp','available','Pastry',1,'2025-11-08 11:19:36','2025-11-08 11:19:36',NULL);
/*!40000 ALTER TABLE `product` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('cAUH2hovZ3NpbX3Vc7N9VT6yl10DIGj6G9CO5FvM',1,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','YTo1OntzOjY6Il90b2tlbiI7czo0MDoiWk5mUlU5Vm1MVVlka3Y0bjJkeEJDd0o3S0NockQwS3F3NTBxd2xMRCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzY6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9wcm9kdWN0cyI7czo1OiJyb3V0ZSI7czoxNDoiYWRtaW4ucHJvZHVjdHMiO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO3M6MjA6ImNvZmZlZV9vZl90aGVfZGF5X2lkIjtpOjE0O30=',1768017032);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `user_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_firstname` varchar(100) NOT NULL,
  `user_lastname` varchar(100) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `user_birthday` date DEFAULT NULL,
  `user_password` varchar(255) NOT NULL,
  `user_role` enum('admin','barista','customer') NOT NULL DEFAULT 'customer',
  `user_phone` varchar(20) DEFAULT NULL,
  `user_address` text DEFAULT NULL,
  `user_createdat` datetime NOT NULL DEFAULT current_timestamp(),
  `user_updatedat` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_deletedat` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_user_email_unique` (`user_email`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,'Admin','Athena Cafe','admincafeathena@gmail.com','2026-01-08','$2y$12$CV4PnDI.qDxIXNy.YlifzOo6dAdAgiyFuByost4g4UsyiVamJc5ri','admin','09602518414',NULL,'2026-01-07 14:14:11','2026-01-07 16:18:55',NULL),(3,'Cedrick Joseph','Mariano','marianocedrick3@gmail.com','2004-08-03','$2y$12$1kJHJCmhSrcV7yTmNcSmZ.bajjhBZbdV4AXOHg76Z8ULcIoPmoBOW','customer','09602518414','Amaia Skies Shaw, Samat St., Brgy. Highway Hills, Mandaluyong City','2025-11-04 09:08:07','2026-01-07 16:19:18',NULL),(5,'Rj Jack','Florida','floridarj@barista.com',NULL,'$2y$12$GNLg9oddHrU8BMMnaDfCOeJH12gPeJe6dpo5Wnpl2/z35rQxcnRpa','barista',NULL,NULL,'2025-11-06 14:03:04','2025-11-06 14:03:04',NULL),(6,'Barista','Cafe Athena','baristacafeathena@gmail.com',NULL,'$2y$12$YLI9S8g7sloW2csgpwNiBe10jmiUTVX29qi8WVk27SbxgmdhENKDK','barista','09498327384',NULL,'2026-01-08 06:33:06','2026-01-08 07:08:00',NULL),(7,'Customer','Cafe Athena','customercafeathena@gmail.com','2026-01-10','$2y$12$CCbbc5llsj2bVittv5CVS.DVI2pbPQS.PP4SO1O9CW9GrSdjQOnCm','customer',NULL,'223 Ortega Street, corner A. Mabini Street, Brgy. Addition Hills, San Juan City','2026-01-08 07:36:35','2026-01-10 03:25:00',NULL);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-01-10 11:52:04
