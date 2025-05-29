-- MySQL dump 10.13  Distrib 8.0.36, for Win64 (x86_64)
--
-- Host: localhost    Database: mydb
-- ------------------------------------------------------
-- Server version	8.0.37

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `cart`
--

DROP TABLE IF EXISTS `cart`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cart` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`,`product_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cart`
--

LOCK TABLES `cart` WRITE;
/*!40000 ALTER TABLE `cart` DISABLE KEYS */;
/*!40000 ALTER TABLE `cart` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_UNIQUE` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (2,'casual'),(4,'formal'),(1,'running'),(3,'sports');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `status` enum('pending','processing','shipped','delivered','cancelled') DEFAULT 'pending',
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_items`
--

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
INSERT INTO `order_items` VALUES (1,1,'Suede Classic',90.00,2,180.00,'pending'),(2,2,'Club C 85',75.00,2,150.00,'pending'),(3,2,'Classic Leather',65.00,1,65.00,'pending'),(4,2,'Stan Smith',80.00,1,80.00,'pending'),(5,3,'Air Max Revolution',130.00,1,130.00,'pending'),(6,3,'React Infinity Run',160.00,1,160.00,'pending'),(7,3,'RS-X Reinvention',110.00,1,110.00,'pending'),(8,3,'Oxford Classic',145.00,1,145.00,'pending'),(9,3,'Business Pro',155.00,1,155.00,'pending'),(10,4,'Air Force 1',90.00,1,90.00,'pending'),(11,5,'Club C 85',75.00,1,75.00,'pending'),(12,6,'Air Force 1',90.00,1,90.00,'pending');
/*!40000 ALTER TABLE `order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `city` varchar(100) NOT NULL,
  `zip_code` varchar(20) NOT NULL,
  `payment_method` enum('credit_card','paypal','bank_transfer','cash_on_delivery') NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `order_status` enum('pending','processing','shipped','delivered','cancelled') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (1,3,'Ricky Castillo','castricky28@gmail.com','09519873322','Tiniguiban, Palawan, Philippines','Puerto Princesa','5300','cash_on_delivery',180.00,'pending','2025-05-29 02:23:39'),(2,3,'Ricky Castillo','castricky28@gmail.com','09519873322','Tiniguiban, Palawan, Philippines','Puerto Princesa','5300','cash_on_delivery',295.00,'pending','2025-05-29 02:44:30'),(3,3,'Ricky Castillo','castricky28@gmail.com','09519873322','Tiniguiban, Palawan, Philippines','Puerto Princesa','5300','cash_on_delivery',700.00,'pending','2025-05-29 02:58:10'),(4,3,'Ricky Castillo','castricky28@gmail.com','09519873322','Tiniguiban, Palawan, Philippines','Puerto Princesa','5300','cash_on_delivery',90.00,'pending','2025-05-29 03:06:51'),(5,3,'Ricky Castillo','castricky28@gmail.com','09519873322','Tiniguiban, Palawan, Philippines','Puerto Princesa','5300','paypal',75.00,'pending','2025-05-29 03:27:30'),(6,3,'Ricky Castillo','castricky28@gmail.com','09519873322','Tiniguiban, Palawan, Philippines','Puerto Princesa','5300','cash_on_delivery',90.00,'pending','2025-05-29 03:29:16');
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_sizes`
--

DROP TABLE IF EXISTS `product_sizes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_sizes` (
  `product_id` int DEFAULT NULL,
  `size` int DEFAULT NULL,
  KEY `product_id` (`product_id`),
  CONSTRAINT `product_sizes_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_sizes`
--

LOCK TABLES `product_sizes` WRITE;
/*!40000 ALTER TABLE `product_sizes` DISABLE KEYS */;
INSERT INTO `product_sizes` VALUES (1,7),(1,8),(1,9),(1,10),(1,11),(2,8),(2,9),(2,10),(2,11),(2,12),(3,7),(3,8),(3,9),(3,10),(4,8),(4,9),(4,10),(4,11),(5,7),(5,8),(5,9),(5,10),(5,11),(5,12),(6,8),(6,9),(6,10),(6,11),(7,7),(7,8),(7,9),(7,10),(7,11),(8,8),(8,9),(8,10),(9,7),(9,8),(9,9),(9,10),(9,11),(9,12),(10,7),(10,8),(10,9),(10,10),(10,11),(11,8),(11,9),(11,10),(11,11),(12,8),(12,9),(12,10),(12,11),(12,12);
/*!40000 ALTER TABLE `product_sizes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `brand` varchar(45) NOT NULL,
  `CATEGORIES_id` int NOT NULL,
  `price` decimal(10,0) NOT NULL,
  `rating` decimal(2,1) NOT NULL,
  `reviews` int NOT NULL,
  `icon` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_PRODUCTS_CATEGORIES1_idx` (`CATEGORIES_id`),
  CONSTRAINT `fk_PRODUCTS_CATEGORIES1` FOREIGN KEY (`CATEGORIES_id`) REFERENCES `categories` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,'Air Max Revolution','Nike',1,130,4.5,248,'..\\assets\\images\\air-mac-revolution.avif'),(2,'Ultra Boost 22','Adidas',1,180,4.7,392,'..\\assets\\images\\ultra-boost-22.png'),(3,'Suede Classic','Puma',2,90,4.3,156,'..\\assets\\images\\suede-classic.png'),(4,'Club C 85','Reebok',2,75,4.2,89,'..\\assets\\images\\club-c-85.png'),(5,'React Infinity Run','Nike',3,160,4.6,204,'..\\assets\\images\\react-infinity-run.png'),(6,'Ultraboost DNA','Adidas',3,190,4.8,445,'..\\assets\\images\\ultraboost-dna.png'),(7,'RS-X Reinvention','Puma',2,110,4.1,127,'..\\assets\\images\\RS-X-Reinvention.png'),(8,'Classic Leather','Reebok',2,65,4.0,78,'..\\assets\\images\\Classic-Leather.png'),(9,'Air Force 1','Nike',2,90,4.4,567,'..\\assets\\images\\air-force-1.png'),(10,'Stan Smith','Adidas',2,80,4.5,234,'..\\assets\\images\\stan-smith.png'),(11,'Oxford Classic','Nike',4,145,4.3,67,'..\\assets\\images\\oxford-classic.png'),(12,'Business Pro','Adidas',4,155,4.2,43,'..\\assets\\images\\business-pro.png');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `email` varchar(45) NOT NULL,
  `userscol` varchar(45) DEFAULT NULL,
  `phone` varchar(45) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `address` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `role` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL,
  `userscol2` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Ricky','ashborn@gmail.com',NULL,NULL,'$2y$10$RaLdQbLCgFulJ70REnV3nuq19eW7SKmuO54hQSrzN4DDibBfQ85kq',NULL,NULL,NULL,'user','2025-05-28 08:30:51',NULL),(2,'Alinah','alinah@gmail.com',NULL,NULL,'$2y$10$fJU6CKl5EEI79bMGUuiLXeIkXpYZPYNNBcDQCE8LQqz8k5P8SN34K',NULL,NULL,NULL,'user','2025-05-28 12:08:51',NULL),(3,'','JC@gmail.com',NULL,'09519873322','$2y$10$jNetiy/4FBuBjLQf2mIlRe/oY30jsTev.B1s7esytYdyXbLnYYcPS','Tiniguiban, Palawan, Philippines','Puerto Princesa','Philippines','user','2025-05-28 19:34:50',NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-05-29 13:16:30
