-- MariaDB dump 10.19  Distrib 10.6.15-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: parampara
-- ------------------------------------------------------
-- Server version	10.6.15-MariaDB

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
-- Table structure for table `activity_log`
--

DROP TABLE IF EXISTS `activity_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `activity_log` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `log_name` varchar(255) DEFAULT NULL,
  `causer_id` bigint(20) unsigned DEFAULT NULL,
  `causer_type` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `properties` text DEFAULT NULL,
  `batch_uuid` char(36) DEFAULT NULL,
  `subject_type` text DEFAULT NULL,
  `event` varchar(255) DEFAULT NULL,
  `subject_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=101 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_log`
--

LOCK TABLES `activity_log` WRITE;
/*!40000 ALTER TABLE `activity_log` DISABLE KEYS */;
INSERT INTO `activity_log` VALUES (1,'Expense',1,'App\\Models\\User','Expense <strong></strong> (₹39) has been created by','{\"attributes\":{\"expense_date\":\"2026-01-01T00:00:00.000000Z\",\"category_id\":10,\"description\":null,\"amount\":\"39.00\",\"payment_method\":\"Online Transfer\",\"notes\":\"Direct deducted from paytm wallet\"}}',NULL,'App\\Models\\Expense','created',1,'2026-01-11 06:03:16','2026-01-11 06:03:16'),(2,'Expense',1,'App\\Models\\User','Expense <strong></strong> (₹170) has been created by','{\"attributes\":{\"expense_date\":\"2026-01-01T00:00:00.000000Z\",\"category_id\":4,\"description\":null,\"amount\":\"170.00\",\"payment_method\":\"Cash\",\"notes\":null}}',NULL,'App\\Models\\Expense','created',2,'2026-01-11 06:03:51','2026-01-11 06:03:51'),(3,'Expense',1,'App\\Models\\User','Expense <strong></strong> (₹100) has been created by','{\"attributes\":{\"expense_date\":\"2026-01-04T00:00:00.000000Z\",\"category_id\":4,\"description\":null,\"amount\":\"100.00\",\"payment_method\":\"Cash\",\"notes\":null}}',NULL,'App\\Models\\Expense','created',3,'2026-03-08 03:39:01','2026-03-08 03:39:01'),(4,'Expense',1,'App\\Models\\User','Expense <strong></strong> (₹1050) has been created by','{\"attributes\":{\"expense_date\":\"2026-01-05T00:00:00.000000Z\",\"category_id\":4,\"description\":null,\"amount\":\"1050.00\",\"payment_method\":\"Cash\",\"notes\":null}}',NULL,'App\\Models\\Expense','created',4,'2026-03-08 03:40:38','2026-03-08 03:40:38'),(5,'Expense',1,'App\\Models\\User','Expense <strong></strong> (₹91) has been created by','{\"attributes\":{\"expense_date\":\"2026-01-08T00:00:00.000000Z\",\"category_id\":1,\"description\":null,\"amount\":\"91.00\",\"payment_method\":\"G-Pay\",\"notes\":null}}',NULL,'App\\Models\\Expense','created',5,'2026-03-08 04:01:07','2026-03-08 04:01:07'),(6,'Expense',1,'App\\Models\\User','Expense <strong></strong> (₹72) has been created by','{\"attributes\":{\"expense_date\":\"2026-01-08T00:00:00.000000Z\",\"category_id\":1,\"description\":null,\"amount\":\"72.00\",\"payment_method\":\"Cash\",\"notes\":null}}',NULL,'App\\Models\\Expense','created',6,'2026-03-08 04:01:40','2026-03-08 04:01:40'),(7,'Expense',1,'App\\Models\\User','Expense <strong></strong> (₹350) has been created by','{\"attributes\":{\"expense_date\":\"2026-01-09T00:00:00.000000Z\",\"category_id\":4,\"description\":null,\"amount\":\"350.00\",\"payment_method\":\"Cash\",\"notes\":null}}',NULL,'App\\Models\\Expense','created',7,'2026-03-08 04:02:53','2026-03-08 04:02:53'),(8,'Expense',1,'App\\Models\\User','Expense <strong></strong> (₹60) has been created by','{\"attributes\":{\"expense_date\":\"2026-01-09T00:00:00.000000Z\",\"category_id\":3,\"description\":null,\"amount\":\"60.00\",\"payment_method\":\"Cash\",\"notes\":null}}',NULL,'App\\Models\\Expense','created',8,'2026-03-08 04:03:50','2026-03-08 04:03:50'),(9,'Expense',1,'App\\Models\\User','Expense <strong></strong> (₹256) has been created by','{\"attributes\":{\"expense_date\":\"2026-01-11T00:00:00.000000Z\",\"category_id\":6,\"description\":null,\"amount\":\"256.00\",\"payment_method\":\"Online Transfer\",\"notes\":\"128+128\"}}',NULL,'App\\Models\\Expense','created',9,'2026-03-08 04:10:24','2026-03-08 04:10:24'),(10,'Expense',1,'App\\Models\\User','Expense <strong></strong> (₹170) has been created by','{\"attributes\":{\"expense_date\":\"2026-01-16T00:00:00.000000Z\",\"category_id\":7,\"description\":null,\"amount\":\"170.00\",\"payment_method\":\"Cash\",\"notes\":null}}',NULL,'App\\Models\\Expense','created',10,'2026-03-08 04:20:53','2026-03-08 04:20:53'),(11,'Expense',1,'App\\Models\\User','Expense <strong></strong> (₹270) has been created by','{\"attributes\":{\"expense_date\":\"2026-01-16T00:00:00.000000Z\",\"category_id\":8,\"description\":null,\"amount\":\"270.00\",\"payment_method\":\"Cash\",\"notes\":null}}',NULL,'App\\Models\\Expense','created',11,'2026-03-08 04:21:12','2026-03-08 04:21:12'),(12,'Expense',1,'App\\Models\\User','Expense <strong></strong> (₹260) has been created by','{\"attributes\":{\"expense_date\":\"2026-01-16T00:00:00.000000Z\",\"category_id\":1,\"description\":null,\"amount\":\"260.00\",\"payment_method\":\"Cash\",\"notes\":null}}',NULL,'App\\Models\\Expense','created',12,'2026-03-08 04:21:37','2026-03-08 04:21:37'),(13,'Expense',1,'App\\Models\\User','Expense <strong></strong> (₹200) has been created by','{\"attributes\":{\"expense_date\":\"2026-01-16T00:00:00.000000Z\",\"category_id\":11,\"description\":null,\"amount\":\"200.00\",\"payment_method\":\"Cash\",\"notes\":null}}',NULL,'App\\Models\\Expense','created',13,'2026-03-08 04:22:08','2026-03-08 04:22:08'),(14,'Expense',1,'App\\Models\\User','Expense <strong></strong> (₹200) has been created by','{\"attributes\":{\"expense_date\":\"2026-01-17T00:00:00.000000Z\",\"category_id\":12,\"description\":null,\"amount\":\"200.00\",\"payment_method\":\"Cash\",\"notes\":null}}',NULL,'App\\Models\\Expense','created',14,'2026-03-08 04:26:12','2026-03-08 04:26:12'),(15,'Expense',1,'App\\Models\\User','Expense <strong></strong> (₹80) has been created by','{\"attributes\":{\"expense_date\":\"2026-01-24T00:00:00.000000Z\",\"category_id\":4,\"description\":null,\"amount\":\"80.00\",\"payment_method\":\"Cash\",\"notes\":null}}',NULL,'App\\Models\\Expense','created',15,'2026-03-08 04:37:14','2026-03-08 04:37:14'),(16,'Expense',1,'App\\Models\\User','Expense <strong></strong> (₹20) has been created by','{\"attributes\":{\"expense_date\":\"2026-01-24T00:00:00.000000Z\",\"category_id\":3,\"description\":null,\"amount\":\"20.00\",\"payment_method\":\"Cash\",\"notes\":null}}',NULL,'App\\Models\\Expense','created',16,'2026-03-08 04:37:29','2026-03-08 04:37:29'),(17,'Expense',1,'App\\Models\\User','Expense <strong></strong> (₹70) has been created by','{\"attributes\":{\"expense_date\":\"2026-01-26T00:00:00.000000Z\",\"category_id\":4,\"description\":null,\"amount\":\"70.00\",\"payment_method\":\"Cash\",\"notes\":null}}',NULL,'App\\Models\\Expense','created',17,'2026-03-08 04:41:41','2026-03-08 04:41:41'),(18,'Expense',1,'App\\Models\\User','Expense <strong></strong> (₹200) has been created by','{\"attributes\":{\"expense_date\":\"2026-01-30T00:00:00.000000Z\",\"category_id\":13,\"description\":null,\"amount\":\"200.00\",\"payment_method\":\"Cash\",\"notes\":null}}',NULL,'App\\Models\\Expense','created',18,'2026-03-08 04:47:34','2026-03-08 04:47:34'),(19,'Payment',1,'App\\Models\\User','Payment of ₹269600.00 for Purchase #1 has been created by','{\"attributes\":{\"purchase_id\":1,\"payment_date\":\"2026-03-08T00:00:00.000000Z\",\"amount\":\"269600.00\",\"payment_method\":\"cash\",\"payment_status\":\"paid\",\"reference_number\":null}}',NULL,'App\\Models\\Payment','created',1,'2026-03-08 05:04:04','2026-03-08 05:04:04'),(20,'Payment',1,'App\\Models\\User','Payment of ₹296500.00 for Purchase #2 has been created by','{\"attributes\":{\"purchase_id\":2,\"payment_date\":\"2026-01-13T00:00:00.000000Z\",\"amount\":\"296500.00\",\"payment_method\":\"credit_card\",\"payment_status\":\"paid\",\"reference_number\":null}}',NULL,'App\\Models\\Payment','created',2,'2026-03-08 05:05:08','2026-03-08 05:05:08'),(21,'Expense',1,'App\\Models\\User','Expense <strong></strong> (₹39) has been created by','{\"attributes\":{\"expense_date\":\"2026-02-01T00:00:00.000000Z\",\"category_id\":5,\"description\":null,\"amount\":\"39.00\",\"payment_method\":\"Online Transfer\",\"notes\":null}}',NULL,'App\\Models\\Expense','created',19,'2026-03-08 05:12:32','2026-03-08 05:12:32'),(22,'Expense',1,'App\\Models\\User','Expense <strong></strong> (₹770) has been created by','{\"attributes\":{\"expense_date\":\"2026-02-01T00:00:00.000000Z\",\"category_id\":14,\"description\":null,\"amount\":\"770.00\",\"payment_method\":\"G-Pay\",\"notes\":null}}',NULL,'App\\Models\\Expense','created',20,'2026-03-08 05:13:16','2026-03-08 05:13:16'),(23,'Expense',1,'App\\Models\\User','Expense <strong></strong> (₹153) has been created by','{\"attributes\":{\"expense_date\":\"2026-02-01T00:00:00.000000Z\",\"category_id\":1,\"description\":null,\"amount\":\"153.00\",\"payment_method\":\"Cash\",\"notes\":\"Sola office Tin-1\"}}',NULL,'App\\Models\\Expense','created',21,'2026-03-08 05:14:05','2026-03-08 05:14:05'),(24,'Expense',1,'App\\Models\\User','Expense <strong></strong> (₹50) has been created by','{\"attributes\":{\"expense_date\":\"2026-02-03T00:00:00.000000Z\",\"category_id\":3,\"description\":null,\"amount\":\"50.00\",\"payment_method\":\"Cash\",\"notes\":null}}',NULL,'App\\Models\\Expense','created',22,'2026-03-08 05:14:38','2026-03-08 05:14:38'),(25,'Expense',1,'App\\Models\\User','Expense <strong></strong> (₹236) has been created by','{\"attributes\":{\"expense_date\":\"2026-02-08T00:00:00.000000Z\",\"category_id\":6,\"description\":null,\"amount\":\"236.00\",\"payment_method\":\"Online Transfer\",\"notes\":\"118+118\"}}',NULL,'App\\Models\\Expense','created',23,'2026-03-08 05:27:12','2026-03-08 05:27:12'),(26,'Expense',1,'App\\Models\\User','Expense <strong></strong> (₹15000) has been created by','{\"attributes\":{\"expense_date\":\"2026-02-08T00:00:00.000000Z\",\"category_id\":15,\"description\":null,\"amount\":\"15000.00\",\"payment_method\":\"G-Pay\",\"notes\":null}}',NULL,'App\\Models\\Expense','created',24,'2026-03-08 05:27:58','2026-03-08 05:27:58'),(27,'Expense',1,'App\\Models\\User','Expense <strong></strong> (₹240) has been created by','{\"attributes\":{\"expense_date\":\"2026-02-12T00:00:00.000000Z\",\"category_id\":8,\"description\":null,\"amount\":\"240.00\",\"payment_method\":\"Cash\",\"notes\":null}}',NULL,'App\\Models\\Expense','created',25,'2026-03-08 05:57:44','2026-03-08 05:57:44'),(28,'Expense',1,'App\\Models\\User','Expense <strong></strong> (₹540) has been created by','{\"attributes\":{\"expense_date\":\"2026-02-14T00:00:00.000000Z\",\"category_id\":16,\"description\":null,\"amount\":\"540.00\",\"payment_method\":\"Cash\",\"notes\":\"100 pics for 1kg mirchi powder\"}}',NULL,'App\\Models\\Expense','created',26,'2026-03-08 06:02:14','2026-03-08 06:02:14'),(29,'Expense',1,'App\\Models\\User','Expense <strong></strong> (₹110) has been created by','{\"attributes\":{\"expense_date\":\"2026-02-16T00:00:00.000000Z\",\"category_id\":1,\"description\":null,\"amount\":\"110.00\",\"payment_method\":\"Cash\",\"notes\":null}}',NULL,'App\\Models\\Expense','created',27,'2026-03-08 06:06:43','2026-03-08 06:06:43'),(30,'Expense',1,'App\\Models\\User','Expense <strong></strong> (₹100) has been created by','{\"attributes\":{\"expense_date\":\"2026-02-16T00:00:00.000000Z\",\"category_id\":4,\"description\":null,\"amount\":\"100.00\",\"payment_method\":\"Cash\",\"notes\":null}}',NULL,'App\\Models\\Expense','created',28,'2026-03-08 06:07:16','2026-03-08 06:07:16'),(31,'Expense',1,'App\\Models\\User','Expense <strong></strong> (₹70) has been created by','{\"attributes\":{\"expense_date\":\"2026-02-19T00:00:00.000000Z\",\"category_id\":4,\"description\":null,\"amount\":\"70.00\",\"payment_method\":\"Cash\",\"notes\":null}}',NULL,'App\\Models\\Expense','created',29,'2026-03-08 06:14:12','2026-03-08 06:14:12'),(32,'Expense',1,'App\\Models\\User','Expense <strong></strong> (₹110) has been created by','{\"attributes\":{\"expense_date\":\"2026-02-20T00:00:00.000000Z\",\"category_id\":9,\"description\":null,\"amount\":\"110.00\",\"payment_method\":\"Cash\",\"notes\":null}}',NULL,'App\\Models\\Expense','created',30,'2026-03-08 06:14:37','2026-03-08 06:14:37'),(33,'Expense',1,'App\\Models\\User','Expense <strong></strong> (₹100) has been created by','{\"attributes\":{\"expense_date\":\"2026-02-20T00:00:00.000000Z\",\"category_id\":17,\"description\":null,\"amount\":\"100.00\",\"payment_method\":\"Cash\",\"notes\":null}}',NULL,'App\\Models\\Expense','created',31,'2026-03-08 06:15:15','2026-03-08 06:15:15'),(34,'Expense',1,'App\\Models\\User','Expense <strong></strong> (₹100.00) has been updated by','{\"attributes\":{\"notes\":\"50 Tin\"},\"old\":{\"notes\":null}}',NULL,'App\\Models\\Expense','updated',31,'2026-03-08 06:15:30','2026-03-08 06:15:30'),(35,'Expense',1,'App\\Models\\User','Expense <strong></strong> (₹39) has been created by','{\"attributes\":{\"expense_date\":\"2026-03-01T00:00:00.000000Z\",\"category_id\":5,\"description\":null,\"amount\":\"39.00\",\"payment_method\":\"Online Transfer\",\"notes\":null}}',NULL,'App\\Models\\Expense','created',32,'2026-03-08 06:31:04','2026-03-08 06:31:04'),(36,'Expense',1,'App\\Models\\User','Expense <strong></strong> (₹250) has been created by','{\"attributes\":{\"expense_date\":\"2026-03-02T00:00:00.000000Z\",\"category_id\":4,\"description\":null,\"amount\":\"250.00\",\"payment_method\":\"Cash\",\"notes\":null}}',NULL,'App\\Models\\Expense','created',33,'2026-03-08 06:31:30','2026-03-08 06:31:30'),(37,'Expense',1,'App\\Models\\User','Expense <strong></strong> (₹170) has been created by','{\"attributes\":{\"expense_date\":\"2026-03-02T00:00:00.000000Z\",\"category_id\":7,\"description\":null,\"amount\":\"170.00\",\"payment_method\":\"Cash\",\"notes\":null}}',NULL,'App\\Models\\Expense','created',34,'2026-03-08 06:32:14','2026-03-08 06:32:14'),(38,'Expense',1,'App\\Models\\User','Expense <strong></strong> (₹308) has been created by','{\"attributes\":{\"expense_date\":\"2026-03-04T00:00:00.000000Z\",\"category_id\":1,\"description\":null,\"amount\":\"308.00\",\"payment_method\":\"G-Pay\",\"notes\":\"3 Tin Gandhinagar\"}}',NULL,'App\\Models\\Expense','created',35,'2026-03-08 06:33:00','2026-03-08 06:33:00'),(39,'Expense',1,'App\\Models\\User','Expense <strong></strong> (₹240) has been created by','{\"attributes\":{\"expense_date\":\"2026-03-06T00:00:00.000000Z\",\"category_id\":8,\"description\":null,\"amount\":\"240.00\",\"payment_method\":\"Cash\",\"notes\":null}}',NULL,'App\\Models\\Expense','created',36,'2026-03-10 09:27:37','2026-03-10 09:27:37'),(40,'Expense',1,'App\\Models\\User','Expense <strong></strong> (₹20) has been created by','{\"attributes\":{\"expense_date\":\"2026-03-06T00:00:00.000000Z\",\"category_id\":18,\"description\":null,\"amount\":\"20.00\",\"payment_method\":\"Cash\",\"notes\":\"Karchra wala ben ne Holi na\"}}',NULL,'App\\Models\\Expense','created',37,'2026-03-10 09:28:14','2026-03-10 09:28:14'),(41,'Expense',1,'App\\Models\\User','Expense <strong></strong> (₹210) has been created by','{\"attributes\":{\"expense_date\":\"2026-03-09T00:00:00.000000Z\",\"category_id\":1,\"description\":null,\"amount\":\"210.00\",\"payment_method\":\"Cash\",\"notes\":\"Tragad Tin-1\"}}',NULL,'App\\Models\\Expense','created',38,'2026-03-10 09:28:45','2026-03-10 09:28:45'),(42,'Expense',1,'App\\Models\\User','Expense <strong></strong> (₹15000) has been created by','{\"attributes\":{\"expense_date\":\"2026-03-09T00:00:00.000000Z\",\"category_id\":15,\"description\":null,\"amount\":\"15000.00\",\"payment_method\":\"G-Pay\",\"notes\":\"Feb-2026 ni salary\"}}',NULL,'App\\Models\\Expense','created',39,'2026-03-10 09:29:14','2026-03-10 09:29:14'),(43,'Expense',1,'App\\Models\\User','Expense <strong></strong> (₹210) has been created by','{\"attributes\":{\"expense_date\":\"2026-03-08T00:00:00.000000Z\",\"category_id\":1,\"description\":null,\"amount\":\"210.00\",\"payment_method\":\"Cash\",\"notes\":null}}',NULL,'App\\Models\\Expense','created',40,'2026-03-14 02:50:23','2026-03-14 02:50:23'),(44,'Expense',1,'App\\Models\\User','Expense <strong></strong> (₹40) has been created by','{\"attributes\":{\"expense_date\":\"2026-03-08T00:00:00.000000Z\",\"category_id\":3,\"description\":null,\"amount\":\"40.00\",\"payment_method\":\"Cash\",\"notes\":null}}',NULL,'App\\Models\\Expense','created',41,'2026-03-14 02:50:46','2026-03-14 02:50:46'),(45,'Expense',1,'App\\Models\\User','Expense <strong></strong> (₹160) has been created by','{\"attributes\":{\"expense_date\":\"2026-03-12T00:00:00.000000Z\",\"category_id\":3,\"description\":null,\"amount\":\"160.00\",\"payment_method\":\"Cash\",\"notes\":null}}',NULL,'App\\Models\\Expense','created',42,'2026-03-14 03:04:27','2026-03-14 03:04:27'),(46,'Expense',1,'App\\Models\\User','Expense <strong></strong> (₹34000) has been created by','{\"attributes\":{\"expense_date\":\"2026-03-11T00:00:00.000000Z\",\"category_id\":19,\"description\":null,\"amount\":\"34000.00\",\"payment_method\":\"Cash\",\"notes\":\"Mathur kaka\"}}',NULL,'App\\Models\\Expense','created',43,'2026-03-14 03:05:37','2026-03-14 03:05:37'),(47,'Expense',1,'App\\Models\\User','Expense <strong>January 2026 salary</strong> (₹15000.00) has been updated by','{\"attributes\":{\"description\":\"January 2026 salary\"},\"old\":{\"description\":null}}',NULL,'App\\Models\\Expense','updated',24,'2026-03-14 03:26:54','2026-03-14 03:26:54'),(48,'Expense',1,'App\\Models\\User','Expense <strong>February 2026 salary</strong> (₹15000.00) has been updated by','{\"attributes\":{\"description\":\"February 2026 salary\"},\"old\":{\"description\":null}}',NULL,'App\\Models\\Expense','updated',39,'2026-03-14 03:27:22','2026-03-14 03:27:22'),(49,'Expense',1,'App\\Models\\User','Expense <strong>January 2026 salary</strong> (₹15000.00) has been updated by','{\"attributes\":{\"expense_date\":\"2026-02-07T00:00:00.000000Z\"},\"old\":{\"expense_date\":\"2026-02-08T00:00:00.000000Z\"}}',NULL,'App\\Models\\Expense','updated',24,'2026-03-14 03:29:38','2026-03-14 03:29:38'),(50,'Expense',1,'App\\Models\\User','Expense <strong>December 2025 salary</strong> (₹15000) has been created by','{\"attributes\":{\"expense_date\":\"2026-01-05T00:00:00.000000Z\",\"category_id\":15,\"description\":\"December 2025 salary\",\"amount\":\"15000.00\",\"payment_method\":\"G-Pay\",\"notes\":null}}',NULL,'App\\Models\\Expense','created',44,'2026-03-14 03:30:21','2026-03-14 03:30:21'),(51,'Expense',1,'App\\Models\\User','Expense <strong></strong> (₹100) has been created by','{\"attributes\":{\"expense_date\":\"2026-03-13T00:00:00.000000Z\",\"category_id\":4,\"description\":null,\"amount\":\"100.00\",\"payment_method\":\"Cash\",\"notes\":null}}',NULL,'App\\Models\\Expense','created',45,'2026-03-16 09:03:01','2026-03-16 09:03:01'),(52,'Expense',1,'App\\Models\\User','Expense <strong></strong> (₹160) has been created by','{\"attributes\":{\"expense_date\":\"2026-03-13T00:00:00.000000Z\",\"category_id\":1,\"description\":null,\"amount\":\"160.00\",\"payment_method\":\"Cash\",\"notes\":null}}',NULL,'App\\Models\\Expense','created',46,'2026-03-16 09:03:20','2026-03-16 09:03:20'),(53,'Expense',1,'App\\Models\\User','Expense <strong></strong> (₹150) has been created by','{\"attributes\":{\"expense_date\":\"2026-03-14T00:00:00.000000Z\",\"category_id\":4,\"description\":null,\"amount\":\"150.00\",\"payment_method\":\"Cash\",\"notes\":null}}',NULL,'App\\Models\\Expense','created',47,'2026-03-16 09:03:37','2026-03-16 09:03:37'),(54,'Expense',1,'App\\Models\\User','Expense <strong></strong> (₹80) has been created by','{\"attributes\":{\"expense_date\":\"2026-03-15T00:00:00.000000Z\",\"category_id\":1,\"description\":null,\"amount\":\"80.00\",\"payment_method\":\"Cash\",\"notes\":null}}',NULL,'App\\Models\\Expense','created',48,'2026-03-18 08:52:25','2026-03-18 08:52:25'),(55,'Expense',1,'App\\Models\\User','Expense <strong></strong> (₹236) has been created by','{\"attributes\":{\"expense_date\":\"2026-03-15T00:00:00.000000Z\",\"category_id\":6,\"description\":null,\"amount\":\"236.00\",\"payment_method\":\"Online Transfer\",\"notes\":\"118+118\"}}',NULL,'App\\Models\\Expense','created',49,'2026-03-18 08:52:52','2026-03-18 08:52:52'),(56,'Expense',1,'App\\Models\\User','Expense <strong></strong> (₹150) has been created by','{\"attributes\":{\"expense_date\":\"2026-03-19T00:00:00.000000Z\",\"category_id\":4,\"description\":null,\"amount\":\"150.00\",\"payment_method\":\"Cash\",\"notes\":\"Ghav-10 via auto rixa\"}}',NULL,'App\\Models\\Expense','created',50,'2026-03-20 09:28:25','2026-03-20 09:28:25'),(57,'Expense',1,'App\\Models\\User','Expense <strong></strong> (₹3000) has been created by','{\"attributes\":{\"expense_date\":\"2026-03-07T00:00:00.000000Z\",\"category_id\":18,\"description\":null,\"amount\":\"3000.00\",\"payment_method\":\"G-Pay\",\"notes\":\"Surat 15 Tin (15*200)\"}}',NULL,'App\\Models\\Expense','created',51,'2026-03-20 09:30:31','2026-03-20 09:30:31'),(58,'Payment',1,'App\\Models\\User','Payment of ₹271160.00 for Purchase #4 has been created by','{\"attributes\":{\"purchase_id\":4,\"payment_date\":\"2026-03-19T00:00:00.000000Z\",\"amount\":\"271160.00\",\"payment_method\":\"bank_transfer\",\"payment_status\":\"paid\",\"reference_number\":\"HDFCR52026031983761271\"}}',NULL,'App\\Models\\Payment','created',3,'2026-03-21 05:06:19','2026-03-21 05:06:19'),(59,'Payment',1,'App\\Models\\User','Payment of ₹120000.00 for Purchase #3 has been created by','{\"attributes\":{\"purchase_id\":3,\"payment_date\":\"2026-03-14T00:00:00.000000Z\",\"amount\":\"120000.00\",\"payment_method\":\"cash\",\"payment_status\":\"paid\",\"reference_number\":null}}',NULL,'App\\Models\\Payment','created',4,'2026-03-21 05:08:02','2026-03-21 05:08:02'),(60,'Payment',1,'App\\Models\\User','Payment of ₹160750.00 for Purchase #5 has been created by','{\"attributes\":{\"purchase_id\":5,\"payment_date\":\"2026-03-14T00:00:00.000000Z\",\"amount\":\"160750.00\",\"payment_method\":\"cash\",\"payment_status\":\"paid\",\"reference_number\":null}}',NULL,'App\\Models\\Payment','created',5,'2026-03-21 05:08:25','2026-03-21 05:08:25'),(61,'Payment',1,'App\\Models\\User','Payment of ₹8750.00 for Purchase #11 has been created by','{\"attributes\":{\"purchase_id\":11,\"payment_date\":\"2026-03-20T00:00:00.000000Z\",\"amount\":\"8750.00\",\"payment_method\":\"cash\",\"payment_status\":\"paid\",\"reference_number\":null}}',NULL,'App\\Models\\Payment','created',6,'2026-03-21 06:05:54','2026-03-21 06:05:54'),(62,'Payment',1,'App\\Models\\User','Payment of ₹6000.00 for Purchase #10 has been created by','{\"attributes\":{\"purchase_id\":10,\"payment_date\":\"2026-03-20T00:00:00.000000Z\",\"amount\":\"6000.00\",\"payment_method\":\"cash\",\"payment_status\":\"paid\",\"reference_number\":null}}',NULL,'App\\Models\\Payment','created',7,'2026-03-21 06:08:09','2026-03-21 06:08:09'),(63,'Payment',1,'App\\Models\\User','Payment of ₹5000.00 for Purchase #12 has been created by','{\"attributes\":{\"purchase_id\":12,\"payment_date\":\"2026-03-20T00:00:00.000000Z\",\"amount\":\"5000.00\",\"payment_method\":\"cash\",\"payment_status\":\"paid\",\"reference_number\":null}}',NULL,'App\\Models\\Payment','created',8,'2026-03-21 06:10:05','2026-03-21 06:10:05'),(64,'Payment',1,'App\\Models\\User','Payment of ₹47250.00 for Purchase #13 has been created by','{\"attributes\":{\"purchase_id\":13,\"payment_date\":\"2026-03-19T00:00:00.000000Z\",\"amount\":\"47250.00\",\"payment_method\":\"bank_transfer\",\"payment_status\":\"paid\",\"reference_number\":\"HDFCH00875620900\"}}',NULL,'App\\Models\\Payment','created',9,'2026-03-21 06:45:22','2026-03-21 06:45:22'),(65,'Expense',1,'App\\Models\\User','Expense <strong></strong> (₹20) has been created by','{\"attributes\":{\"expense_date\":\"2026-03-20T00:00:00.000000Z\",\"category_id\":12,\"description\":null,\"amount\":\"20.00\",\"payment_method\":\"Cash\",\"notes\":\"Machis Box\"}}',NULL,'App\\Models\\Expense','created',52,'2026-03-24 09:37:59','2026-03-24 09:37:59'),(66,'Expense',1,'App\\Models\\User','Expense <strong></strong> (₹150) has been created by','{\"attributes\":{\"expense_date\":\"2026-03-20T00:00:00.000000Z\",\"category_id\":1,\"description\":null,\"amount\":\"150.00\",\"payment_method\":\"Cash\",\"notes\":\"2 Tin New Vadaj\"}}',NULL,'App\\Models\\Expense','created',53,'2026-03-24 09:38:38','2026-03-24 09:38:38'),(67,'Expense',1,'App\\Models\\User','Expense <strong></strong> (₹180) has been created by','{\"attributes\":{\"expense_date\":\"2026-03-21T00:00:00.000000Z\",\"category_id\":1,\"description\":null,\"amount\":\"180.00\",\"payment_method\":\"Cash\",\"notes\":\"Tin-1 Raysan Gandhinagar\"}}',NULL,'App\\Models\\Expense','created',54,'2026-03-24 09:39:12','2026-03-24 09:39:12'),(68,'Expense',1,'App\\Models\\User','Expense <strong></strong> (₹100) has been created by','{\"attributes\":{\"expense_date\":\"2026-03-25T00:00:00.000000Z\",\"category_id\":4,\"description\":null,\"amount\":\"100.00\",\"payment_method\":\"Cash\",\"notes\":null}}',NULL,'App\\Models\\Expense','created',55,'2026-03-26 09:27:31','2026-03-26 09:27:31'),(69,'Expense',1,'App\\Models\\User','Expense <strong></strong> (₹60) has been created by','{\"attributes\":{\"expense_date\":\"2026-03-26T00:00:00.000000Z\",\"category_id\":4,\"description\":null,\"amount\":\"60.00\",\"payment_method\":\"Cash\",\"notes\":null}}',NULL,'App\\Models\\Expense','created',56,'2026-03-29 07:41:09','2026-03-29 07:41:09'),(70,'Expense',1,'App\\Models\\User','Expense <strong></strong> (₹200) has been created by','{\"attributes\":{\"expense_date\":\"2026-03-27T00:00:00.000000Z\",\"category_id\":11,\"description\":null,\"amount\":\"200.00\",\"payment_method\":\"Cash\",\"notes\":null}}',NULL,'App\\Models\\Expense','created',57,'2026-03-29 07:45:03','2026-03-29 07:45:03'),(71,'Expense',1,'App\\Models\\User','Expense <strong></strong> (₹50) has been created by','{\"attributes\":{\"expense_date\":\"2026-03-27T00:00:00.000000Z\",\"category_id\":3,\"description\":null,\"amount\":\"50.00\",\"payment_method\":\"Cash\",\"notes\":null}}',NULL,'App\\Models\\Expense','created',58,'2026-03-29 07:45:19','2026-03-29 07:45:19'),(72,'Expense',1,'App\\Models\\User','Expense <strong></strong> (₹250) has been created by','{\"attributes\":{\"expense_date\":\"2026-03-28T00:00:00.000000Z\",\"category_id\":1,\"description\":null,\"amount\":\"250.00\",\"payment_method\":\"Cash\",\"notes\":\"2 Tin Raysan - Gandhinagar\"}}',NULL,'App\\Models\\Expense','created',59,'2026-03-29 07:46:07','2026-03-29 07:46:07'),(73,'Expense',1,'App\\Models\\User','Expense <strong></strong> (₹109) has been created by','{\"attributes\":{\"expense_date\":\"2026-03-28T00:00:00.000000Z\",\"category_id\":9,\"description\":null,\"amount\":\"109.00\",\"payment_method\":\"Online Transfer\",\"notes\":\"FSSAI certificate renewal fee\"}}',NULL,'App\\Models\\Expense','created',60,'2026-03-29 07:46:56','2026-03-29 07:46:56'),(74,'Payment',1,'App\\Models\\User','Payment of ₹40000.00 for Purchase #4 has been created by','{\"attributes\":{\"purchase_id\":4,\"payment_date\":\"2026-03-29T00:00:00.000000Z\",\"amount\":\"40000.00\",\"payment_method\":\"cash\",\"payment_status\":\"paid\",\"reference_number\":null}}',NULL,'App\\Models\\Payment','created',10,'2026-03-29 08:03:37','2026-03-29 08:03:37'),(75,'Payment',1,'App\\Models\\User','Payment of ₹33600.00 for Purchase #8 has been created by','{\"attributes\":{\"purchase_id\":8,\"payment_date\":\"2026-03-29T00:00:00.000000Z\",\"amount\":\"33600.00\",\"payment_method\":\"cash\",\"payment_status\":\"paid\",\"reference_number\":null}}',NULL,'App\\Models\\Payment','created',11,'2026-03-29 08:38:17','2026-03-29 08:38:17'),(76,'Expense',1,'App\\Models\\User','Expense <strong></strong> (₹15000) has been created by','{\"attributes\":{\"expense_date\":\"2026-03-31T00:00:00.000000Z\",\"category_id\":15,\"description\":null,\"amount\":\"15000.00\",\"payment_method\":\"G-Pay\",\"notes\":\"March-2026\"}}',NULL,'App\\Models\\Expense','created',61,'2026-03-31 09:19:10','2026-03-31 09:19:10'),(77,'Expense',1,'App\\Models\\User','Expense <strong></strong> (₹200) has been created by','{\"attributes\":{\"expense_date\":\"2026-03-31T00:00:00.000000Z\",\"category_id\":1,\"description\":null,\"amount\":\"200.00\",\"payment_method\":\"Cash\",\"notes\":\"Gandhinagar\"}}',NULL,'App\\Models\\Expense','created',62,'2026-03-31 09:19:40','2026-03-31 09:19:40'),(78,'Payment',1,'App\\Models\\User','Payment of ₹138500.00 for Purchase #6 has been created by','{\"attributes\":{\"purchase_id\":6,\"payment_date\":\"2026-03-31T00:00:00.000000Z\",\"amount\":\"138500.00\",\"payment_method\":\"bank_transfer\",\"payment_status\":\"paid\",\"reference_number\":\"HDFCH00899067484\"}}',NULL,'App\\Models\\Payment','created',12,'2026-03-31 09:22:08','2026-03-31 09:22:08'),(79,'Payment',1,'App\\Models\\User','Payment of ₹7100.00 for Purchase #14 has been created by','{\"attributes\":{\"purchase_id\":14,\"payment_date\":\"2026-03-31T00:00:00.000000Z\",\"amount\":\"7100.00\",\"payment_method\":\"cash\",\"payment_status\":\"paid\",\"reference_number\":null}}',NULL,'App\\Models\\Payment','created',13,'2026-03-31 09:24:05','2026-03-31 09:24:05'),(80,'Expense',1,'App\\Models\\User','Expense <strong></strong> (₹39) has been created by','{\"attributes\":{\"expense_date\":\"2026-04-01T00:00:00.000000Z\",\"category_id\":5,\"description\":null,\"amount\":\"39.00\",\"payment_method\":\"Online Transfer\",\"notes\":null}}',NULL,'App\\Models\\Expense','created',63,'2026-04-07 09:00:45','2026-04-07 09:00:45'),(81,'Expense',1,'App\\Models\\User','Expense <strong></strong> (₹190) has been created by','{\"attributes\":{\"expense_date\":\"2026-04-01T00:00:00.000000Z\",\"category_id\":1,\"description\":null,\"amount\":\"190.00\",\"payment_method\":\"Cash\",\"notes\":\"Tragad Tin-1\"}}',NULL,'App\\Models\\Expense','created',64,'2026-04-07 09:01:13','2026-04-07 09:01:13'),(82,'Expense',1,'App\\Models\\User','Expense <strong></strong> (₹400) has been created by','{\"attributes\":{\"expense_date\":\"2026-04-02T00:00:00.000000Z\",\"category_id\":4,\"description\":null,\"amount\":\"400.00\",\"payment_method\":\"Cash\",\"notes\":null}}',NULL,'App\\Models\\Expense','created',65,'2026-04-07 09:01:35','2026-04-07 09:01:35'),(83,'Expense',1,'App\\Models\\User','Expense <strong></strong> (₹150) has been created by','{\"attributes\":{\"expense_date\":\"2026-04-03T00:00:00.000000Z\",\"category_id\":4,\"description\":null,\"amount\":\"150.00\",\"payment_method\":\"Cash\",\"notes\":null}}',NULL,'App\\Models\\Expense','created',66,'2026-04-07 09:02:05','2026-04-07 09:02:05'),(84,'Expense',1,'App\\Models\\User','Expense <strong></strong> (₹50) has been created by','{\"attributes\":{\"expense_date\":\"2026-04-04T00:00:00.000000Z\",\"category_id\":4,\"description\":null,\"amount\":\"50.00\",\"payment_method\":\"Cash\",\"notes\":null}}',NULL,'App\\Models\\Expense','created',67,'2026-04-07 09:02:17','2026-04-07 09:02:17'),(85,'Expense',1,'App\\Models\\User','Expense <strong></strong> (₹10) has been created by','{\"attributes\":{\"expense_date\":\"2026-04-05T00:00:00.000000Z\",\"category_id\":9,\"description\":null,\"amount\":\"10.00\",\"payment_method\":\"Cash\",\"notes\":\"sell for watch\"}}',NULL,'App\\Models\\Expense','created',68,'2026-04-07 09:13:28','2026-04-07 09:13:28'),(86,'Expense',1,'App\\Models\\User','Expense <strong></strong> (₹60) has been created by','{\"attributes\":{\"expense_date\":\"2026-04-06T00:00:00.000000Z\",\"category_id\":3,\"description\":null,\"amount\":\"60.00\",\"payment_method\":\"Cash\",\"notes\":null}}',NULL,'App\\Models\\Expense','created',69,'2026-04-07 09:13:43','2026-04-07 09:13:43'),(87,'Expense',1,'App\\Models\\User','Expense <strong></strong> (₹34000) has been created by','{\"attributes\":{\"expense_date\":\"2026-04-07T00:00:00.000000Z\",\"category_id\":10,\"description\":null,\"amount\":\"34000.00\",\"payment_method\":\"Cash\",\"notes\":\"Mathurkaka\"}}',NULL,'App\\Models\\Expense','created',70,'2026-04-08 08:27:29','2026-04-08 08:27:29'),(88,'Expense',1,'App\\Models\\User','Expense <strong></strong> (₹910) has been created by','{\"attributes\":{\"expense_date\":\"2026-04-07T00:00:00.000000Z\",\"category_id\":14,\"description\":null,\"amount\":\"910.00\",\"payment_method\":\"G-Pay\",\"notes\":null}}',NULL,'App\\Models\\Expense','created',71,'2026-04-11 08:27:42','2026-04-11 08:27:42'),(89,'Expense',1,'App\\Models\\User','Expense <strong></strong> (₹170) has been created by','{\"attributes\":{\"expense_date\":\"2026-04-07T00:00:00.000000Z\",\"category_id\":7,\"description\":null,\"amount\":\"170.00\",\"payment_method\":\"Cash\",\"notes\":null}}',NULL,'App\\Models\\Expense','created',72,'2026-04-11 08:28:04','2026-04-11 08:28:04'),(90,'Expense',1,'App\\Models\\User','Expense <strong></strong> (₹270) has been created by','{\"attributes\":{\"expense_date\":\"2026-04-08T00:00:00.000000Z\",\"category_id\":8,\"description\":null,\"amount\":\"270.00\",\"payment_method\":\"Cash\",\"notes\":null}}',NULL,'App\\Models\\Expense','created',73,'2026-04-11 08:28:28','2026-04-11 08:28:28'),(91,'Expense',1,'App\\Models\\User','Expense <strong></strong> (₹400) has been created by','{\"attributes\":{\"expense_date\":\"2026-04-08T00:00:00.000000Z\",\"category_id\":4,\"description\":null,\"amount\":\"400.00\",\"payment_method\":\"Cash\",\"notes\":\"Dinubhai Tin-9 & Ghav-13\"}}',NULL,'App\\Models\\Expense','created',74,'2026-04-11 08:29:19','2026-04-11 08:29:19'),(92,'Expense',1,'App\\Models\\User','Expense <strong></strong> (₹70) has been created by','{\"attributes\":{\"expense_date\":\"2026-04-14T00:00:00.000000Z\",\"category_id\":3,\"description\":null,\"amount\":\"70.00\",\"payment_method\":\"Cash\",\"notes\":null}}',NULL,'App\\Models\\Expense','created',75,'2026-04-18 06:26:40','2026-04-18 06:26:40'),(93,'Expense',1,'App\\Models\\User','Expense <strong></strong> (₹100) has been created by','{\"attributes\":{\"expense_date\":\"2026-04-15T00:00:00.000000Z\",\"category_id\":4,\"description\":null,\"amount\":\"100.00\",\"payment_method\":\"Cash\",\"notes\":null}}',NULL,'App\\Models\\Expense','created',76,'2026-04-18 06:31:42','2026-04-18 06:31:42'),(94,'Expense',1,'App\\Models\\User','Expense <strong></strong> (₹236) has been created by','{\"attributes\":{\"expense_date\":\"2026-04-20T00:00:00.000000Z\",\"category_id\":6,\"description\":null,\"amount\":\"236.00\",\"payment_method\":\"Online Transfer\",\"notes\":\"118+118\"}}',NULL,'App\\Models\\Expense','created',77,'2026-04-21 09:12:27','2026-04-21 09:12:27'),(95,'Expense',1,'App\\Models\\User','Expense <strong></strong> (₹40) has been created by','{\"attributes\":{\"expense_date\":\"2026-04-20T00:00:00.000000Z\",\"category_id\":3,\"description\":null,\"amount\":\"40.00\",\"payment_method\":\"Cash\",\"notes\":null}}',NULL,'App\\Models\\Expense','created',78,'2026-04-21 09:12:49','2026-04-21 09:12:49'),(96,'Sell Return',1,'App\\Models\\User','Sell Return for <strong>Mirchi</strong> (Qty: 2) has been created by','{\"attributes\":{\"sell_id\":196,\"product_id\":7,\"return_date\":\"2026-04-20T00:00:00.000000Z\",\"quantity\":2,\"return_price\":\"700.00\",\"total_return_amount\":\"1400.00\",\"reason\":\"cash return Rs.700\\/-\"}}',NULL,'App\\Models\\SellReturn','created',1,'2026-04-21 09:17:31','2026-04-21 09:17:31'),(97,'Sell Return',1,'App\\Models\\User','Sell Return for <strong>Mirchi</strong> (Qty: 2) has been updated by','{\"attributes\":{\"return_price\":\"350.00\",\"total_return_amount\":\"700.00\"},\"old\":{\"return_price\":\"700.00\",\"total_return_amount\":\"1400.00\"}}',NULL,'App\\Models\\SellReturn','updated',1,'2026-04-21 09:17:45','2026-04-21 09:17:45'),(98,'Expense',1,'App\\Models\\User','Expense <strong></strong> (₹30) has been created by','{\"attributes\":{\"expense_date\":\"2026-04-26T00:00:00.000000Z\",\"category_id\":3,\"description\":null,\"amount\":\"30.00\",\"payment_method\":\"Cash\",\"notes\":null}}',NULL,'App\\Models\\Expense','created',79,'2026-05-02 00:43:19','2026-05-02 00:43:19'),(99,'Expense',1,'App\\Models\\User','Expense <strong></strong> (₹200) has been created by','{\"attributes\":{\"expense_date\":\"2026-04-30T00:00:00.000000Z\",\"category_id\":11,\"description\":null,\"amount\":\"200.00\",\"payment_method\":\"Cash\",\"notes\":null}}',NULL,'App\\Models\\Expense','created',80,'2026-05-02 00:49:47','2026-05-02 00:49:47'),(100,'Expense',1,'App\\Models\\User','Expense <strong></strong> (₹50) has been created by','{\"attributes\":{\"expense_date\":\"2026-04-30T00:00:00.000000Z\",\"category_id\":3,\"description\":null,\"amount\":\"50.00\",\"payment_method\":\"Cash\",\"notes\":null}}',NULL,'App\\Models\\Expense','created',81,'2026-05-02 00:50:06','2026-05-02 00:50:06');
/*!40000 ALTER TABLE `activity_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `company_profiles`
--

DROP TABLE IF EXISTS `company_profiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `company_profiles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_name` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `favicon_16` varchar(255) DEFAULT NULL,
  `favicon_32` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `website_url` varchar(255) DEFAULT NULL,
  `gst_number` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `company_profiles`
--

LOCK TABLES `company_profiles` WRITE;
/*!40000 ALTER TABLE `company_profiles` DISABLE KEYS */;
INSERT INTO `company_profiles` VALUES (1,'Parampara Edible Oil','uploads/img/tt7o7cV9qg3nrv3wmiAdM4xuz5HJ6tEQQkXGwVrC.png','uploads/img/HdvCGVvO1BVoJunYCIHrUPjOcukjjhMr15ZAqO5A.png','uploads/img/iGWIazkb8hsUwdBobAkk8EAh1WDx69TnZ02JoQ69.png',NULL,'paramparaedibleoils@gmail.com','9898035036','Shop No.3, Opp. RamKrishna Society, 80 feet Road Corner, Uttamnagar, Ahmedabad','https://paramparaoils.com','24DXCPP6763B1ZI','2026-01-11 05:19:08','2026-05-02 07:30:13');
/*!40000 ALTER TABLE `company_profiles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `documents`
--

DROP TABLE IF EXISTS `documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `documents` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `entity_id` int(11) NOT NULL,
  `entity_type` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `orignal_name` varchar(255) NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `documents`
--

LOCK TABLES `documents` WRITE;
/*!40000 ALTER TABLE `documents` DISABLE KEYS */;
/*!40000 ALTER TABLE `documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `expense_categories`
--

DROP TABLE IF EXISTS `expense_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `expense_categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `expense_categories_name_unique` (`name`),
  KEY `expense_categories_name_index` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `expense_categories`
--

LOCK TABLES `expense_categories` WRITE;
/*!40000 ALTER TABLE `expense_categories` DISABLE KEYS */;
INSERT INTO `expense_categories` VALUES (1,'Porter Charge',NULL,'2026-01-11 05:59:18','2026-01-11 05:59:18'),(2,'AMC Bill',NULL,'2026-01-11 05:59:29','2026-01-11 05:59:29'),(3,'Tea/Cold drinks/Ice cream',NULL,'2026-01-11 05:59:35','2026-01-11 05:59:35'),(4,'Auto Rixa Charge',NULL,'2026-01-11 05:59:56','2026-01-11 05:59:56'),(5,'Sound Box Charge',NULL,'2026-01-11 06:01:42','2026-01-11 06:01:42'),(6,'Mobile Bill',NULL,'2026-01-11 06:01:52','2026-01-11 06:01:52'),(7,'News Paper Bill',NULL,'2026-01-11 06:02:01','2026-01-11 06:02:01'),(8,'Water Bill',NULL,'2026-01-11 06:02:08','2026-01-11 06:02:08'),(9,'Stationary Bill',NULL,'2026-01-11 06:02:21','2026-01-11 06:02:21'),(10,'Rent Agreement',NULL,'2026-01-11 06:02:32','2026-01-11 06:02:32'),(11,'Petrol - Mama',NULL,'2026-03-08 04:21:59','2026-03-08 04:21:59'),(12,'Agarbatti',NULL,'2026-03-08 04:26:05','2026-03-08 04:26:05'),(13,'Shutter Repering',NULL,'2026-03-08 04:47:26','2026-03-08 04:47:26'),(14,'Light Bill',NULL,'2026-03-08 05:13:07','2026-03-08 05:13:07'),(15,'Salary - Mama',NULL,'2026-03-08 05:27:44','2026-03-08 05:27:44'),(16,'Packing Bag',NULL,'2026-03-08 06:01:35','2026-03-08 06:01:35'),(17,'Dabba Utarwana',NULL,'2026-03-08 06:15:00','2026-03-08 06:15:00'),(18,'Other',NULL,'2026-03-10 09:27:50','2026-03-10 09:27:50'),(19,'Shop Rent',NULL,'2026-03-14 03:05:12','2026-03-14 03:05:12');
/*!40000 ALTER TABLE `expense_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `expenses`
--

DROP TABLE IF EXISTS `expenses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `expenses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `expense_date` date NOT NULL,
  `category_id` bigint(20) unsigned DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` enum('Cash','G-Pay','Online Transfer') NOT NULL DEFAULT 'Cash',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `expenses_category_id_foreign` (`category_id`),
  CONSTRAINT `expenses_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `expense_categories` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=82 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `expenses`
--

LOCK TABLES `expenses` WRITE;
/*!40000 ALTER TABLE `expenses` DISABLE KEYS */;
INSERT INTO `expenses` VALUES (1,'2026-01-01',10,NULL,NULL,39.00,'Online Transfer','Direct deducted from paytm wallet','2026-01-11 06:03:16','2026-01-11 06:03:16'),(2,'2026-01-01',4,NULL,NULL,170.00,'Cash',NULL,'2026-01-11 06:03:51','2026-01-11 06:03:51'),(3,'2026-01-04',4,NULL,NULL,100.00,'Cash',NULL,'2026-03-08 03:39:01','2026-03-08 03:39:01'),(4,'2026-01-05',4,NULL,NULL,1050.00,'Cash',NULL,'2026-03-08 03:40:38','2026-03-08 03:40:38'),(5,'2026-01-08',1,NULL,NULL,91.00,'G-Pay',NULL,'2026-03-08 04:01:07','2026-03-08 04:01:07'),(6,'2026-01-08',1,NULL,NULL,72.00,'Cash',NULL,'2026-03-08 04:01:40','2026-03-08 04:01:40'),(7,'2026-01-09',4,NULL,NULL,350.00,'Cash',NULL,'2026-03-08 04:02:53','2026-03-08 04:02:53'),(8,'2026-01-09',3,NULL,NULL,60.00,'Cash',NULL,'2026-03-08 04:03:50','2026-03-08 04:03:50'),(9,'2026-01-11',6,NULL,NULL,256.00,'Online Transfer','128+128','2026-03-08 04:10:24','2026-03-08 04:10:24'),(10,'2026-01-16',7,NULL,NULL,170.00,'Cash',NULL,'2026-03-08 04:20:53','2026-03-08 04:20:53'),(11,'2026-01-16',8,NULL,NULL,270.00,'Cash',NULL,'2026-03-08 04:21:12','2026-03-08 04:21:12'),(12,'2026-01-16',1,NULL,NULL,260.00,'Cash',NULL,'2026-03-08 04:21:37','2026-03-08 04:21:37'),(13,'2026-01-16',11,NULL,NULL,200.00,'Cash',NULL,'2026-03-08 04:22:08','2026-03-08 04:22:08'),(14,'2026-01-17',12,NULL,NULL,200.00,'Cash',NULL,'2026-03-08 04:26:12','2026-03-08 04:26:12'),(15,'2026-01-24',4,NULL,NULL,80.00,'Cash',NULL,'2026-03-08 04:37:14','2026-03-08 04:37:14'),(16,'2026-01-24',3,NULL,NULL,20.00,'Cash',NULL,'2026-03-08 04:37:29','2026-03-08 04:37:29'),(17,'2026-01-26',4,NULL,NULL,70.00,'Cash',NULL,'2026-03-08 04:41:41','2026-03-08 04:41:41'),(18,'2026-01-30',13,NULL,NULL,200.00,'Cash',NULL,'2026-03-08 04:47:33','2026-03-08 04:47:33'),(19,'2026-02-01',5,NULL,NULL,39.00,'Online Transfer',NULL,'2026-03-08 05:12:32','2026-03-08 05:12:32'),(20,'2026-02-01',14,NULL,NULL,770.00,'G-Pay',NULL,'2026-03-08 05:13:16','2026-03-08 05:13:16'),(21,'2026-02-01',1,NULL,NULL,153.00,'Cash','Sola office Tin-1','2026-03-08 05:14:05','2026-03-08 05:14:05'),(22,'2026-02-03',3,NULL,NULL,50.00,'Cash',NULL,'2026-03-08 05:14:38','2026-03-08 05:14:38'),(23,'2026-02-08',6,NULL,NULL,236.00,'Online Transfer','118+118','2026-03-08 05:27:12','2026-03-08 05:27:12'),(24,'2026-02-07',15,NULL,'January 2026 salary',15000.00,'G-Pay',NULL,'2026-03-08 05:27:58','2026-03-14 03:29:38'),(25,'2026-02-12',8,NULL,NULL,240.00,'Cash',NULL,'2026-03-08 05:57:44','2026-03-08 05:57:44'),(26,'2026-02-14',16,NULL,NULL,540.00,'Cash','100 pics for 1kg mirchi powder','2026-03-08 06:02:14','2026-03-08 06:02:14'),(27,'2026-02-16',1,NULL,NULL,110.00,'Cash',NULL,'2026-03-08 06:06:43','2026-03-08 06:06:43'),(28,'2026-02-16',4,NULL,NULL,100.00,'Cash',NULL,'2026-03-08 06:07:16','2026-03-08 06:07:16'),(29,'2026-02-19',4,NULL,NULL,70.00,'Cash',NULL,'2026-03-08 06:14:12','2026-03-08 06:14:12'),(30,'2026-02-20',9,NULL,NULL,110.00,'Cash',NULL,'2026-03-08 06:14:37','2026-03-08 06:14:37'),(31,'2026-02-20',17,NULL,NULL,100.00,'Cash','50 Tin','2026-03-08 06:15:15','2026-03-08 06:15:30'),(32,'2026-03-01',5,NULL,NULL,39.00,'Online Transfer',NULL,'2026-03-08 06:31:04','2026-03-08 06:31:04'),(33,'2026-03-02',4,NULL,NULL,250.00,'Cash',NULL,'2026-03-08 06:31:30','2026-03-08 06:31:30'),(34,'2026-03-02',7,NULL,NULL,170.00,'Cash',NULL,'2026-03-08 06:32:14','2026-03-08 06:32:14'),(35,'2026-03-04',1,NULL,NULL,308.00,'G-Pay','3 Tin Gandhinagar','2026-03-08 06:33:00','2026-03-08 06:33:00'),(36,'2026-03-06',8,NULL,NULL,240.00,'Cash',NULL,'2026-03-10 09:27:37','2026-03-10 09:27:37'),(37,'2026-03-06',18,NULL,NULL,20.00,'Cash','Karchra wala ben ne Holi na','2026-03-10 09:28:14','2026-03-10 09:28:14'),(38,'2026-03-09',1,NULL,NULL,210.00,'Cash','Tragad Tin-1','2026-03-10 09:28:45','2026-03-10 09:28:45'),(39,'2026-03-09',15,NULL,'February 2026 salary',15000.00,'G-Pay','Feb-2026 ni salary','2026-03-10 09:29:14','2026-03-14 03:27:22'),(40,'2026-03-08',1,NULL,NULL,210.00,'Cash',NULL,'2026-03-14 02:50:23','2026-03-14 02:50:23'),(41,'2026-03-08',3,NULL,NULL,40.00,'Cash',NULL,'2026-03-14 02:50:46','2026-03-14 02:50:46'),(42,'2026-03-12',3,NULL,NULL,160.00,'Cash',NULL,'2026-03-14 03:04:27','2026-03-14 03:04:27'),(43,'2026-03-11',19,NULL,NULL,34000.00,'Cash','Mathur kaka','2026-03-14 03:05:37','2026-03-14 03:05:37'),(44,'2026-01-05',15,NULL,'December 2025 salary',15000.00,'G-Pay',NULL,'2026-03-14 03:30:21','2026-03-14 03:30:21'),(45,'2026-03-13',4,NULL,NULL,100.00,'Cash',NULL,'2026-03-16 09:03:01','2026-03-16 09:03:01'),(46,'2026-03-13',1,NULL,NULL,160.00,'Cash',NULL,'2026-03-16 09:03:20','2026-03-16 09:03:20'),(47,'2026-03-14',4,NULL,NULL,150.00,'Cash',NULL,'2026-03-16 09:03:37','2026-03-16 09:03:37'),(48,'2026-03-15',1,NULL,NULL,80.00,'Cash',NULL,'2026-03-18 08:52:25','2026-03-18 08:52:25'),(49,'2026-03-15',6,NULL,NULL,236.00,'Online Transfer','118+118','2026-03-18 08:52:52','2026-03-18 08:52:52'),(50,'2026-03-19',4,NULL,NULL,150.00,'Cash','Ghav-10 via auto rixa','2026-03-20 09:28:25','2026-03-20 09:28:25'),(51,'2026-03-07',18,NULL,NULL,3000.00,'G-Pay','Surat 15 Tin (15*200)','2026-03-20 09:30:31','2026-03-20 09:30:31'),(52,'2026-03-20',12,NULL,NULL,20.00,'Cash','Machis Box','2026-03-24 09:37:59','2026-03-24 09:37:59'),(53,'2026-03-20',1,NULL,NULL,150.00,'Cash','2 Tin New Vadaj','2026-03-24 09:38:38','2026-03-24 09:38:38'),(54,'2026-03-21',1,NULL,NULL,180.00,'Cash','Tin-1 Raysan Gandhinagar','2026-03-24 09:39:12','2026-03-24 09:39:12'),(55,'2026-03-25',4,NULL,NULL,100.00,'Cash',NULL,'2026-03-26 09:27:31','2026-03-26 09:27:31'),(56,'2026-03-26',4,NULL,NULL,60.00,'Cash',NULL,'2026-03-29 07:41:09','2026-03-29 07:41:09'),(57,'2026-03-27',11,NULL,NULL,200.00,'Cash',NULL,'2026-03-29 07:45:03','2026-03-29 07:45:03'),(58,'2026-03-27',3,NULL,NULL,50.00,'Cash',NULL,'2026-03-29 07:45:19','2026-03-29 07:45:19'),(59,'2026-03-28',1,NULL,NULL,250.00,'Cash','2 Tin Raysan - Gandhinagar','2026-03-29 07:46:07','2026-03-29 07:46:07'),(60,'2026-03-28',9,NULL,NULL,109.00,'Online Transfer','FSSAI certificate renewal fee','2026-03-29 07:46:56','2026-03-29 07:46:56'),(61,'2026-03-31',15,NULL,NULL,15000.00,'G-Pay','March-2026','2026-03-31 09:19:10','2026-03-31 09:19:10'),(62,'2026-03-31',1,NULL,NULL,200.00,'Cash','Gandhinagar','2026-03-31 09:19:40','2026-03-31 09:19:40'),(63,'2026-04-01',5,NULL,NULL,39.00,'Online Transfer',NULL,'2026-04-07 09:00:45','2026-04-07 09:00:45'),(64,'2026-04-01',1,NULL,NULL,190.00,'Cash','Tragad Tin-1','2026-04-07 09:01:13','2026-04-07 09:01:13'),(65,'2026-04-02',4,NULL,NULL,400.00,'Cash',NULL,'2026-04-07 09:01:35','2026-04-07 09:01:35'),(66,'2026-04-03',4,NULL,NULL,150.00,'Cash',NULL,'2026-04-07 09:02:05','2026-04-07 09:02:05'),(67,'2026-04-04',4,NULL,NULL,50.00,'Cash',NULL,'2026-04-07 09:02:17','2026-04-07 09:02:17'),(68,'2026-04-05',9,NULL,NULL,10.00,'Cash','sell for watch','2026-04-07 09:13:28','2026-04-07 09:13:28'),(69,'2026-04-06',3,NULL,NULL,60.00,'Cash',NULL,'2026-04-07 09:13:43','2026-04-07 09:13:43'),(70,'2026-04-07',10,NULL,NULL,34000.00,'Cash','Mathurkaka','2026-04-08 08:27:29','2026-04-08 08:27:29'),(71,'2026-04-07',14,NULL,NULL,910.00,'G-Pay',NULL,'2026-04-11 08:27:42','2026-04-11 08:27:42'),(72,'2026-04-07',7,NULL,NULL,170.00,'Cash',NULL,'2026-04-11 08:28:04','2026-04-11 08:28:04'),(73,'2026-04-08',8,NULL,NULL,270.00,'Cash',NULL,'2026-04-11 08:28:28','2026-04-11 08:28:28'),(74,'2026-04-08',4,NULL,NULL,400.00,'Cash','Dinubhai Tin-9 & Ghav-13','2026-04-11 08:29:19','2026-04-11 08:29:19'),(75,'2026-04-14',3,NULL,NULL,70.00,'Cash',NULL,'2026-04-18 06:26:40','2026-04-18 06:26:40'),(76,'2026-04-15',4,NULL,NULL,100.00,'Cash',NULL,'2026-04-18 06:31:42','2026-04-18 06:31:42'),(77,'2026-04-20',6,NULL,NULL,236.00,'Online Transfer','118+118','2026-04-21 09:12:27','2026-04-21 09:12:27'),(78,'2026-04-20',3,NULL,NULL,40.00,'Cash',NULL,'2026-04-21 09:12:49','2026-04-21 09:12:49'),(79,'2026-04-26',3,NULL,NULL,30.00,'Cash',NULL,'2026-05-02 00:43:18','2026-05-02 00:43:18'),(80,'2026-04-30',11,NULL,NULL,200.00,'Cash',NULL,'2026-05-02 00:49:47','2026-05-02 00:49:47'),(81,'2026-04-30',3,NULL,NULL,50.00,'Cash',NULL,'2026-05-02 00:50:06','2026-05-02 00:50:06');
/*!40000 ALTER TABLE `expenses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2014_10_12_000000_create_users_table',1),(2,'2014_10_12_100000_create_password_reset_tokens_table',1),(3,'2019_08_19_000000_create_failed_jobs_table',1),(4,'2019_12_14_000001_create_personal_access_tokens_table',1),(5,'2024_01_30_110022_create_roles_table',1),(6,'2024_02_22_141542_create_documents_table',1),(7,'2024_04_06_075506_create_activity_log_table',1),(8,'2024_04_06_095057_add_batch_uuid_to_activity_log_table',1),(9,'2024_05_20_073755_create_notifications_table',1),(10,'2024_06_29_101902_create_permission_tables',1),(11,'2024_06_29_120229_add_slug_to_permissions_table',1),(12,'2024_07_02_060502_add__module_to_permissions_table',1),(13,'2025_12_14_093056_create_stocks_table',1),(14,'2025_12_14_104818_create_products_table',1),(15,'2025_12_14_104901_create_purchases_table',1),(16,'2025_12_14_104909_create_sells_table',1),(17,'2025_12_14_105032_create_purchase_items_table',1),(18,'2025_12_14_111918_create_sell_items_table',1),(19,'2025_12_14_113524_create_company_profiles_table',1),(20,'2025_12_20_000000_add_expense_columns_to_purchases_table',1),(21,'2025_12_28_102327_create_expenses_table',1),(22,'2025_12_28_102328_create_purchase_returns_table',1),(23,'2025_12_28_102330_create_sell_returns_table',1),(24,'2025_12_28_add_website_gst_to_company_profiles',1),(25,'2025_12_28_create_payments_table',1),(26,'2026_01_01_082723_create_expense_categories_table',1),(27,'2026_01_01_082756_add_category_id_to_expenses_table',1),(28,'2026_01_01_084422_make_expense_fields_optional',1),(29,'2026_01_01_085156_add_payment_method_to_expenses_table',1),(30,'2026_01_01_091638_add_seller_fields_to_sells_table',1),(31,'2026_01_01_093940_migrate_category_string_to_id',1),(32,'2026_01_11_000000_add_stock_quantity_to_products_table',1),(33,'2026_02_08_000000_add_mix_to_sells_payment_mode',2),(34,'2026_02_08_000001_add_cash_online_amounts_to_sells_table',2),(35,'2026_02_11_000000_update_qr_to_gpay_in_sells',2),(36,'2026_02_11_000001_add_bill_type_to_purchases_table',2),(37,'2026_03_14_120000_create_sell_invoices_table',3),(38,'2026_03_15_100000_sell_invoices_cash_online_types',4),(39,'2026_03_16_100000_sell_invoices_mix_type_and_soft_deletes',5),(40,'2026_04_12_100000_create_sell_payments_table',6),(41,'2026_05_02_100000_add_is_active_to_products_table',7),(42,'2026_05_02_110000_make_payment_mode_nullable_in_sells_table',8),(43,'2026_05_02_120000_create_stock_closings_table',9),(44,'2026_05_02_130000_add_returns_to_stock_closing_items',10);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_permissions`
--

DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_permissions`
--

LOCK TABLES `model_has_permissions` WRITE;
/*!40000 ALTER TABLE `model_has_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `model_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_roles`
--

DROP TABLE IF EXISTS `model_has_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_roles`
--

LOCK TABLES `model_has_roles` WRITE;
/*!40000 ALTER TABLE `model_has_roles` DISABLE KEYS */;
/*!40000 ALTER TABLE `model_has_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notifications` (
  `id` char(36) NOT NULL,
  `type` varchar(255) NOT NULL,
  `notifiable_type` varchar(255) NOT NULL,
  `notifiable_id` bigint(20) unsigned NOT NULL,
  `data` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
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
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `purchase_id` bigint(20) unsigned NOT NULL,
  `payment_date` date NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` enum('cash','cheque','bank_transfer','credit_card','other') NOT NULL DEFAULT 'cash',
  `payment_status` enum('pending','paid','failed','cancelled') NOT NULL DEFAULT 'pending',
  `reference_number` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payments_purchase_id_index` (`purchase_id`),
  KEY `payments_payment_date_index` (`payment_date`),
  KEY `payments_payment_status_index` (`payment_status`),
  CONSTRAINT `payments_purchase_id_foreign` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
INSERT INTO `payments` VALUES (1,1,'2026-01-25',269600.00,'cash','paid',NULL,'cash on dhasa via papa','2026-03-08 05:04:04','2026-03-08 05:04:04'),(2,2,'2026-01-13',296500.00,'bank_transfer','paid',NULL,'RTGS via net banking','2026-03-08 05:05:08','2026-03-08 05:05:08'),(3,4,'2026-03-19',271160.00,'bank_transfer','paid','HDFCR52026031983761271',NULL,'2026-03-21 05:06:19','2026-03-21 05:06:19'),(4,3,'2026-03-14',120000.00,'cash','paid',NULL,'Cash paid via papa at Dhasa','2026-03-21 05:08:02','2026-03-21 05:08:02'),(5,5,'2026-03-14',160750.00,'cash','paid',NULL,'Cash paid via papa at Dhasa','2026-03-21 05:08:25','2026-03-21 05:08:25'),(6,11,'2026-03-20',8750.00,'cash','paid',NULL,'Cash paid to papa, Dhasa jata pela','2026-03-21 06:05:54','2026-03-21 06:05:54'),(7,10,'2026-03-20',6000.00,'cash','paid',NULL,'Cash paid to papa, Dhasa jata pela','2026-03-21 06:08:09','2026-03-21 06:08:09'),(8,12,'2026-03-20',5000.00,'cash','paid',NULL,'Cash paid to papa, Dhasa jata pela','2026-03-21 06:10:05','2026-03-21 06:10:05'),(9,13,'2026-03-19',47250.00,'bank_transfer','paid','HDFCH00875620900',NULL,'2026-03-21 06:45:22','2026-03-21 06:45:22'),(10,4,'2026-03-29',40000.00,'cash','paid',NULL,'Cash to Jatin  from A\'bad','2026-03-29 08:03:37','2026-03-29 08:03:37'),(11,8,'2026-03-29',33600.00,'cash','paid',NULL,'Cash to Jatin from A\'bad','2026-03-29 08:38:17','2026-03-29 08:38:17'),(12,6,'2026-03-31',138500.00,'bank_transfer','paid','HDFCH00899067484','20 Feb Bill','2026-03-31 09:22:08','2026-03-31 09:22:08'),(13,14,'2026-03-31',7100.00,'cash','paid',NULL,'bapuji ne','2026-03-31 09:24:05','2026-03-31 09:24:05');
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `module` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `products` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_name` varchar(255) NOT NULL,
  `product_code` varchar(255) NOT NULL,
  `description` longtext DEFAULT NULL,
  `base_price_min` decimal(10,2) NOT NULL,
  `base_price_max` decimal(10,2) NOT NULL,
  `sell_price` decimal(10,2) NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `stock_quantity` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `products_product_code_unique` (`product_code`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,'15 KG Tin','TIN','15 KG Tin (16.48 Ltr)',2800.00,3100.00,3300.00,'products/9kcYnsT1C78IuMiWl6B3mQbkhoxUK695P9ASeVIE.png',252,1,'2026-01-11 05:21:04','2026-05-02 07:34:20'),(2,'15 KG Doll','DOLL','15 Kg Doll (16.48 Ltr)',2800.00,3150.00,3300.00,'products/Y87D80UH2vnvReYsuY7n1AL2rI2FMS66edkp23n4.png',6,0,'2026-01-11 05:21:56','2026-05-02 01:37:09'),(3,'15 Ltr Jar','JAR','15 Ltr Jar',2700.00,3000.00,3000.00,'products/Jd6m98qBQTEi5K2NFay1hEHPQuTrcgNs7J8SFa3K.png',7,0,'2026-01-11 05:23:11','2026-05-02 01:37:06'),(4,'05 Ltr Ken','KEN','5 Ltr Ken',850.00,1050.00,1050.00,'products/wPgnGqstC7OBVf3pCtKryZJh64PwxY08rDbCaJmS.png',124,1,'2026-01-11 05:23:59','2026-05-02 07:53:37'),(5,'01 Ltr Bottle','BOTTLE','1 Ltr Bottle',190.00,250.00,210.00,'products/jFYUSmy28JnMyu3hb72r76LYM3DaSiNKRPOy85jY.png',46,1,'2026-01-11 05:24:38','2026-05-02 07:41:20'),(6,'1 Kg Peanut','PEANUT','1 Kg peanut package',70.00,100.00,90.00,'products/0nF5BKGhOFNrWokm3Vp9nrjJ7tkgQPN73GBHpXE1.png',32,0,'2026-01-11 05:25:56','2026-05-02 01:37:13'),(7,'Mirchi','MIRCHI','1 Kg Red Mirchi Powder Package',300.00,400.00,350.00,'products/0lXZapdBaNVcB8mvGl4jQoxSPuDbbMqSuRgHbgCv.jpg',29,1,'2026-01-11 05:26:44','2026-05-02 05:18:51'),(8,'Haldi','HALDI','1 Kg Haldi Powder Package',280.00,350.00,300.00,'products/PBhgDJ9oaF9TOmOVohpdkf2RJBXktLe4VoJ4auva.jpg',13,1,'2026-01-11 05:27:18','2026-05-02 05:14:07'),(9,'Mirchi 500','MIRCHI-500','500 Gram Red Mirchi Powder Package',150.00,200.00,175.00,'products/xHPPFixDKaIYlf1AWDa0fxtH4Gh0sDm2Cr7VwdjJ.jpg',0,1,'2026-02-23 09:29:43','2026-05-02 01:37:01'),(10,'Ghav','GHAV','30KG bag',1100.00,1200.00,1080.00,'products/ZJFW85sY5AJdtqQLX8ys8lrxlzJOShMz7rt3omK8.png',31,1,'2026-02-28 04:44:54','2026-05-02 07:34:20'),(11,'13 KG Tin','TIN-13','13 Kg net oil + 860 gram Tin weight',2400.00,2500.00,2600.00,'products/7MIEjIy4zZcJrIuLoMVwwitBixOoNIinoMcqk6Mh.png',51,0,'2026-03-08 05:01:09','2026-05-02 01:37:11'),(12,'15.5 Ltr Tin','TIN-15.5','15 Kg Tin (Net)',2900.00,3000.00,3100.00,'products/v4JpFKRWJ3l2JsszqLiZIM9pY2SBsxwccbnz389I.png',0,0,'2026-03-08 05:31:40','2026-05-02 01:37:05'),(13,'Haldi 500','HALDI-500','500 Gram Selam Haldi Powder Package',130.00,175.00,150.00,'products/FdG3C1Zqs1JRwVBUOToKw7xORj0EVol1jcQvv0pb.jpg',0,1,'2026-03-21 06:01:14','2026-03-31 09:10:28');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purchase_items`
--

DROP TABLE IF EXISTS `purchase_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `purchase_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `purchase_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `quantity` int(11) NOT NULL,
  `purchase_price` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_items_purchase_id_foreign` (`purchase_id`),
  KEY `purchase_items_product_id_foreign` (`product_id`),
  CONSTRAINT `purchase_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `purchase_items_purchase_id_foreign` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=67 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchase_items`
--

LOCK TABLES `purchase_items` WRITE;
/*!40000 ALTER TABLE `purchase_items` DISABLE KEYS */;
INSERT INTO `purchase_items` VALUES (3,2,1,95,2360.00,224200.00,'2026-01-11 05:35:30','2026-01-11 05:35:30'),(4,2,2,5,2380.00,11900.00,'2026-01-11 05:35:30','2026-01-11 05:35:30'),(5,2,3,5,2180.00,10900.00,'2026-01-11 05:35:30','2026-01-11 05:35:30'),(6,2,4,60,745.00,44700.00,'2026-01-11 05:35:30','2026-01-11 05:35:30'),(9,4,1,51,2500.00,127500.00,'2026-03-08 04:55:53','2026-03-08 04:55:53'),(10,4,1,49,2500.00,122500.00,'2026-03-08 04:55:53','2026-03-08 04:55:53'),(11,4,2,5,2520.00,12600.00,'2026-03-08 04:55:53','2026-03-08 04:55:53'),(12,4,4,40,780.00,31200.00,'2026-03-08 04:55:53','2026-03-08 04:55:53'),(13,4,5,80,157.00,12560.00,'2026-03-08 04:55:53','2026-03-08 04:55:53'),(15,5,1,50,2600.00,130000.00,'2026-03-08 04:58:09','2026-03-08 04:58:09'),(16,5,1,5,2450.00,12250.00,'2026-03-08 04:58:09','2026-03-08 04:58:09'),(17,5,4,20,805.00,16100.00,'2026-03-08 04:58:09','2026-03-08 04:58:09'),(24,1,1,100,2360.00,236000.00,'2026-03-08 05:03:25','2026-03-08 05:03:25'),(25,1,4,40,730.00,29200.00,'2026-03-08 05:03:25','2026-03-08 05:03:25'),(27,3,1,50,2400.00,120000.00,'2026-03-14 05:37:38','2026-03-14 05:37:38'),(40,11,7,20,300.00,6000.00,'2026-03-21 06:03:34','2026-03-21 06:03:34'),(41,11,8,12,220.00,2640.00,'2026-03-21 06:03:34','2026-03-21 06:03:34'),(42,11,13,1,110.00,110.00,'2026-03-21 06:03:34','2026-03-21 06:03:34'),(45,10,7,20,300.00,6000.00,'2026-03-21 06:07:41','2026-03-21 06:07:41'),(46,12,7,20,250.00,5000.00,'2026-03-21 06:09:34','2026-03-21 06:09:34'),(47,8,4,40,840.00,33600.00,'2026-03-21 06:21:28','2026-03-21 06:21:28'),(51,13,10,50,945.00,47250.00,'2026-03-21 06:44:20','2026-03-21 06:44:20'),(56,9,1,90,2650.00,238500.00,'2026-03-30 12:43:04','2026-03-30 12:43:04'),(57,9,4,52,840.00,43680.00,'2026-03-30 12:43:04','2026-03-30 12:43:04'),(58,9,5,32,169.00,5408.00,'2026-03-30 12:43:04','2026-03-30 12:43:04'),(59,6,1,50,2730.00,136500.00,'2026-03-31 09:22:38','2026-03-31 09:22:38'),(60,7,1,50,2650.00,132500.00,'2026-03-31 09:23:02','2026-03-31 09:23:02'),(61,7,11,51,2350.00,119850.00,'2026-03-31 09:23:02','2026-03-31 09:23:02'),(62,7,3,5,2450.00,12250.00,'2026-03-31 09:23:02','2026-03-31 09:23:02'),(63,14,7,20,300.00,6000.00,'2026-03-31 09:23:23','2026-03-31 09:23:23'),(64,14,8,5,220.00,1100.00,'2026-03-31 09:23:23','2026-03-31 09:23:23'),(65,15,1,50,3000.00,150000.00,'2026-04-02 09:41:19','2026-04-02 09:41:19'),(66,15,10,100,950.00,95000.00,'2026-04-02 09:41:19','2026-04-02 09:41:19');
/*!40000 ALTER TABLE `purchase_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purchase_returns`
--

DROP TABLE IF EXISTS `purchase_returns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `purchase_returns` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `purchase_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `return_date` date NOT NULL,
  `quantity` int(11) NOT NULL,
  `return_price` decimal(10,2) NOT NULL,
  `total_return_amount` decimal(10,2) NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_returns_purchase_id_foreign` (`purchase_id`),
  KEY `purchase_returns_product_id_foreign` (`product_id`),
  CONSTRAINT `purchase_returns_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `purchase_returns_purchase_id_foreign` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchase_returns`
--

LOCK TABLES `purchase_returns` WRITE;
/*!40000 ALTER TABLE `purchase_returns` DISABLE KEYS */;
/*!40000 ALTER TABLE `purchase_returns` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purchases`
--

DROP TABLE IF EXISTS `purchases`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `purchases` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `purchase_date` date NOT NULL,
  `supplier_name` varchar(255) NOT NULL,
  `bill_type` enum('gst','without_gst') NOT NULL DEFAULT 'gst',
  `bill_details` longtext DEFAULT NULL,
  `transportation_cost` decimal(10,2) NOT NULL DEFAULT 0.00,
  `expense` decimal(10,2) NOT NULL DEFAULT 0.00,
  `expense_details` longtext DEFAULT NULL,
  `bill_due_date` date DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` enum('pending','completed','cancelled') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchases`
--

LOCK TABLES `purchases` WRITE;
/*!40000 ALTER TABLE `purchases` DISABLE KEYS */;
INSERT INTO `purchases` VALUES (1,'2025-11-24','Gopinath Oil','gst',NULL,4400.00,0.00,NULL,'2026-01-15',269600.00,'pending','2026-01-11 05:33:35','2026-03-08 05:03:25'),(2,'2025-12-15','Gopinath','gst',NULL,4800.00,0.00,NULL,'2026-01-30',296500.00,'pending','2026-01-11 05:35:30','2026-01-11 05:35:30'),(3,'2025-12-29','Gopinath','without_gst','Transportation Cost 2000/- paid directly',0.00,0.00,NULL,'2026-02-13',120000.00,'pending','2026-01-11 05:36:37','2026-03-14 05:37:38'),(4,'2026-01-05','Gopinath Oil','gst','49 Tin without label',4800.00,0.00,NULL,'2026-03-05',311160.00,'pending','2026-03-08 04:55:53','2026-03-08 04:55:53'),(5,'2026-01-30','Gopinath Oil','gst',NULL,2400.00,0.00,NULL,'2026-03-30',160750.00,'pending','2026-03-08 04:58:09','2026-03-08 04:58:09'),(6,'2026-02-20','Gopinath Oil','gst',NULL,2000.00,0.00,NULL,'2026-03-31',138500.00,'pending','2026-03-08 04:59:37','2026-03-31 09:22:38'),(7,'2026-03-08','Gopinath Oil','gst',NULL,4200.00,0.00,NULL,'2026-04-22',268800.00,'pending','2026-03-08 05:02:16','2026-03-31 09:23:02'),(8,'2026-02-28','Gopinath Oil','gst',NULL,0.00,0.00,NULL,'2026-04-30',33600.00,'pending','2026-03-08 06:25:55','2026-03-21 06:21:28'),(9,'2026-03-16','Gopinath Oil','gst','90+13+2 = 115\r\n115*40 = 4600/-\r\n\r\n50*38 = 1900/- Ghav nu bhadu',6500.00,0.00,NULL,'2026-04-30',294088.00,'pending','2026-03-21 05:49:18','2026-03-30 12:43:04'),(10,'2026-03-12','Vinubhai Vaviya','without_gst','Anil Kaka jode magavyu htu (2kg kashmiri marchu ₹1700/-)',0.00,0.00,NULL,'2026-04-12',6000.00,'pending','2026-03-21 05:57:42','2026-03-21 06:07:41'),(11,'2026-02-20','Vinubhai Vaviya','without_gst','Tel ni gadi jode (50 Tin)',0.00,0.00,NULL,'2026-03-20',8750.00,'pending','2026-03-21 06:03:34','2026-03-21 06:03:34'),(12,'2026-02-01','Pareshbhai Rasnal','without_gst',NULL,0.00,0.00,NULL,'2026-03-31',5000.00,'pending','2026-03-21 06:09:34','2026-03-21 06:09:34'),(13,'2026-03-16','Gopinath Oil','without_gst',NULL,0.00,0.00,NULL,'2026-03-20',47250.00,'pending','2026-03-21 06:44:20','2026-03-21 06:44:20'),(14,'2026-03-25','Vinubhai Vaviya','without_gst','Pareshbhai na Tel jode gadi ma',0.00,0.00,NULL,'2026-05-09',7100.00,'pending','2026-03-29 07:43:33','2026-03-31 09:23:23'),(15,'2026-04-02','Gopinath Oil','gst','100 Ghav * 50/- = 5000\r\n50 Tin * 40/- = 2000',7000.00,0.00,NULL,'2026-05-02',252000.00,'pending','2026-04-02 09:41:19','2026-04-02 09:41:19');
/*!40000 ALTER TABLE `purchases` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_has_permissions`
--

DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_has_permissions`
--

LOCK TABLES `role_has_permissions` WRITE;
/*!40000 ALTER TABLE `role_has_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `role_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Admin','web','2026-01-11 05:16:14','2026-01-11 05:16:14');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles_old`
--

DROP TABLE IF EXISTS `roles_old`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles_old` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles_old`
--

LOCK TABLES `roles_old` WRITE;
/*!40000 ALTER TABLE `roles_old` DISABLE KEYS */;
/*!40000 ALTER TABLE `roles_old` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sell_invoices`
--

DROP TABLE IF EXISTS `sell_invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sell_invoices` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `invoice_number` varchar(64) NOT NULL,
  `invoice_date` date NOT NULL,
  `invoice_type` varchar(16) NOT NULL DEFAULT 'cash',
  `total_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `cash_total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `online_total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `sells_count` int(10) unsigned NOT NULL DEFAULT 0,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sell_invoices_invoice_number_unique` (`invoice_number`),
  UNIQUE KEY `sell_invoices_date_type_unique` (`invoice_date`,`invoice_type`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sell_invoices`
--

LOCK TABLES `sell_invoices` WRITE;
/*!40000 ALTER TABLE `sell_invoices` DISABLE KEYS */;
INSERT INTO `sell_invoices` VALUES (5,'SINV-20260301-CASH','2026-03-01','cash',2200.00,2200.00,0.00,1,NULL,'2026-03-21 08:07:08','2026-03-21 08:07:08',NULL),(6,'SINV-20260301-ONLINE','2026-03-01','online',9500.00,0.00,9500.00,1,NULL,'2026-03-21 08:07:36','2026-03-21 08:09:38',NULL),(7,'SINV-20260302-ONLINE','2026-03-02','online',4100.00,0.00,4100.00,1,NULL,'2026-03-21 08:09:18','2026-03-21 08:09:28',NULL),(8,'SINV-20260303-ONLINE','2026-03-03','online',10300.00,0.00,10300.00,1,NULL,'2026-03-21 08:25:49','2026-03-21 08:25:49',NULL),(9,'SINV-20260307-CASH','2026-03-07','cash',136150.00,136150.00,0.00,2,NULL,'2026-03-25 09:35:27','2026-03-25 09:35:27',NULL);
/*!40000 ALTER TABLE `sell_invoices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sell_items`
--

DROP TABLE IF EXISTS `sell_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sell_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sell_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `quantity` int(11) NOT NULL,
  `selling_price` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sell_items_sell_id_foreign` (`sell_id`),
  KEY `sell_items_product_id_foreign` (`product_id`),
  CONSTRAINT `sell_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sell_items_sell_id_foreign` FOREIGN KEY (`sell_id`) REFERENCES `sells` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=564 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sell_items`
--

LOCK TABLES `sell_items` WRITE;
/*!40000 ALTER TABLE `sell_items` DISABLE KEYS */;
INSERT INTO `sell_items` VALUES (1,1,1,2,2900.00,5800.00,'2026-01-11 05:45:06','2026-01-11 05:45:06'),(2,1,1,1,3000.00,3000.00,'2026-01-11 05:45:06','2026-01-11 05:45:06'),(3,1,1,1,2850.00,2850.00,'2026-01-11 05:45:06','2026-01-11 05:45:06'),(6,3,1,2,3000.00,6000.00,'2026-01-11 05:51:20','2026-01-11 05:51:20'),(7,3,1,1,2950.00,2950.00,'2026-01-11 05:51:20','2026-01-11 05:51:20'),(8,3,1,1,2900.00,2900.00,'2026-01-11 05:51:20','2026-01-11 05:51:20'),(9,3,4,4,900.00,3600.00,'2026-01-11 05:51:20','2026-01-11 05:51:20'),(10,3,4,1,950.00,950.00,'2026-01-11 05:51:20','2026-01-11 05:51:20'),(11,4,4,2,950.00,1900.00,'2026-01-11 05:52:05','2026-01-11 05:52:05'),(12,4,5,1,200.00,200.00,'2026-01-11 05:52:05','2026-01-11 05:52:05'),(13,2,1,4,2850.00,11400.00,'2026-01-11 06:26:33','2026-01-11 06:26:33'),(14,2,5,1,200.00,200.00,'2026-01-11 06:26:33','2026-01-11 06:26:33'),(15,5,2,1,2900.00,2900.00,'2026-03-08 03:32:45','2026-03-08 03:32:45'),(16,5,4,1,950.00,950.00,'2026-03-08 03:32:45','2026-03-08 03:32:45'),(17,5,6,2,90.00,180.00,'2026-03-08 03:32:45','2026-03-08 03:32:45'),(18,6,4,3,950.00,2850.00,'2026-03-08 03:35:58','2026-03-08 03:35:58'),(19,6,1,5,2850.00,14250.00,'2026-03-08 03:35:58','2026-03-08 03:35:58'),(20,6,5,3,200.00,600.00,'2026-03-08 03:35:58','2026-03-08 03:35:58'),(21,7,4,3,950.00,2850.00,'2026-03-08 03:37:53','2026-03-08 03:37:53'),(22,7,1,2,2900.00,5800.00,'2026-03-08 03:37:53','2026-03-08 03:37:53'),(23,7,1,1,3000.00,3000.00,'2026-03-08 03:37:53','2026-03-08 03:37:53'),(24,8,6,2,85.00,170.00,'2026-03-08 03:38:30','2026-03-08 03:38:30'),(25,9,4,1,950.00,950.00,'2026-03-08 03:41:57','2026-03-08 03:41:57'),(26,9,1,4,2900.00,11600.00,'2026-03-08 03:41:57','2026-03-08 03:41:57'),(27,10,1,8,2900.00,23200.00,'2026-03-08 03:46:28','2026-03-08 03:46:28'),(28,10,5,1,200.00,200.00,'2026-03-08 03:46:28','2026-03-08 03:46:28'),(29,10,1,2,2850.00,5700.00,'2026-03-08 03:46:28','2026-03-08 03:46:28'),(30,11,4,4,900.00,3600.00,'2026-03-08 03:49:01','2026-03-08 03:49:01'),(31,11,1,5,2900.00,14500.00,'2026-03-08 03:49:01','2026-03-08 03:49:01'),(32,12,1,3,3000.00,9000.00,'2026-03-08 03:50:43','2026-03-08 03:50:43'),(33,12,5,1,200.00,200.00,'2026-03-08 03:50:43','2026-03-08 03:50:43'),(34,12,1,1,2800.00,2800.00,'2026-03-08 03:50:43','2026-03-08 03:50:43'),(39,15,5,1,200.00,200.00,'2026-03-08 03:53:53','2026-03-08 03:53:53'),(40,14,6,1,90.00,90.00,'2026-03-08 03:53:58','2026-03-08 03:53:58'),(42,16,1,3,3000.00,9000.00,'2026-03-08 03:56:03','2026-03-08 03:56:03'),(43,16,4,2,950.00,1900.00,'2026-03-08 03:56:03','2026-03-08 03:56:03'),(44,16,1,1,2650.00,2650.00,'2026-03-08 03:56:03','2026-03-08 03:56:03'),(45,16,1,1,2850.00,2850.00,'2026-03-08 03:56:03','2026-03-08 03:56:03'),(46,17,1,6,2950.00,17700.00,'2026-03-08 03:57:35','2026-03-08 03:57:35'),(47,17,5,1,200.00,200.00,'2026-03-08 03:57:35','2026-03-08 03:57:35'),(48,13,1,3,2800.00,8400.00,'2026-03-08 03:57:54','2026-03-08 03:57:54'),(49,18,1,26,2900.00,75400.00,'2026-03-08 03:59:52','2026-03-08 03:59:52'),(50,18,1,8,2900.00,23200.00,'2026-03-08 03:59:52','2026-03-08 03:59:52'),(51,19,4,2,950.00,1900.00,'2026-03-08 04:00:16','2026-03-08 04:00:16'),(52,20,5,3,200.00,600.00,'2026-03-08 04:08:06','2026-03-08 04:08:06'),(53,21,1,6,2850.00,17100.00,'2026-03-08 04:09:48','2026-03-08 04:09:48'),(54,21,2,2,2850.00,5700.00,'2026-03-08 04:09:48','2026-03-08 04:09:48'),(55,21,1,1,2950.00,2950.00,'2026-03-08 04:09:48','2026-03-08 04:09:48'),(56,21,4,4,900.00,3600.00,'2026-03-08 04:09:48','2026-03-08 04:09:48'),(57,22,1,3,3000.00,9000.00,'2026-03-08 04:11:16','2026-03-08 04:11:16'),(58,22,1,1,2850.00,2850.00,'2026-03-08 04:11:16','2026-03-08 04:11:16'),(59,22,4,2,950.00,1900.00,'2026-03-08 04:11:16','2026-03-08 04:11:16'),(60,23,1,3,3000.00,9000.00,'2026-03-08 04:14:46','2026-03-08 04:14:46'),(61,23,4,4,950.00,3800.00,'2026-03-08 04:14:46','2026-03-08 04:14:46'),(62,24,4,2,950.00,1900.00,'2026-03-08 04:15:37','2026-03-08 04:15:37'),(63,25,1,3,3000.00,9000.00,'2026-03-08 04:17:14','2026-03-08 04:17:14'),(64,25,1,2,2900.00,5800.00,'2026-03-08 04:17:14','2026-03-08 04:17:14'),(65,25,5,1,200.00,200.00,'2026-03-08 04:17:14','2026-03-08 04:17:14'),(66,25,6,6,86.50,519.00,'2026-03-08 04:17:14','2026-03-08 04:17:14'),(67,26,4,4,900.00,3600.00,'2026-03-08 04:17:53','2026-03-08 04:17:53'),(68,27,5,1,200.00,200.00,'2026-03-08 04:18:21','2026-03-08 04:18:21'),(69,28,1,1,2950.00,2950.00,'2026-03-08 04:18:59','2026-03-08 04:18:59'),(70,29,1,1,2900.00,2900.00,'2026-03-08 04:20:28','2026-03-08 04:20:28'),(71,29,6,10,85.00,850.00,'2026-03-08 04:20:28','2026-03-08 04:20:28'),(72,29,5,2,200.00,400.00,'2026-03-08 04:20:28','2026-03-08 04:20:28'),(74,31,1,2,3000.00,6000.00,'2026-03-08 04:24:04','2026-03-08 04:24:04'),(75,32,1,1,3000.00,3000.00,'2026-03-08 04:24:33','2026-03-08 04:24:33'),(78,33,1,3,3050.00,9150.00,'2026-03-08 04:28:12','2026-03-08 04:28:12'),(79,33,1,1,3000.00,3000.00,'2026-03-08 04:28:12','2026-03-08 04:28:12'),(80,33,4,1,950.00,950.00,'2026-03-08 04:28:12','2026-03-08 04:28:12'),(81,30,1,1,3050.00,3050.00,'2026-03-08 04:29:00','2026-03-08 04:29:00'),(82,30,1,3,2850.00,8550.00,'2026-03-08 04:29:00','2026-03-08 04:29:00'),(83,34,1,3,3000.00,9000.00,'2026-03-08 04:30:11','2026-03-08 04:30:11'),(84,34,4,1,950.00,950.00,'2026-03-08 04:30:11','2026-03-08 04:30:11'),(85,34,5,1,200.00,200.00,'2026-03-08 04:30:11','2026-03-08 04:30:11'),(86,35,4,4,900.00,3600.00,'2026-03-08 04:30:57','2026-03-08 04:30:57'),(87,35,5,1,200.00,200.00,'2026-03-08 04:30:57','2026-03-08 04:30:57'),(88,36,2,1,3000.00,3000.00,'2026-03-08 04:31:42','2026-03-08 04:31:42'),(89,36,6,1,90.00,90.00,'2026-03-08 04:31:42','2026-03-08 04:31:42'),(90,37,1,1,3000.00,3000.00,'2026-03-08 04:32:07','2026-03-08 04:32:07'),(91,38,1,1,3000.00,3000.00,'2026-03-08 04:32:55','2026-03-08 04:32:55'),(92,38,1,1,2950.00,2950.00,'2026-03-08 04:32:55','2026-03-08 04:32:55'),(93,39,5,2,200.00,400.00,'2026-03-08 04:33:30','2026-03-08 04:33:30'),(94,40,1,2,3000.00,6000.00,'2026-03-08 04:34:16','2026-03-08 04:34:16'),(95,40,1,1,2850.00,2850.00,'2026-03-08 04:34:16','2026-03-08 04:34:16'),(96,41,4,4,950.00,3800.00,'2026-03-08 04:35:23','2026-03-08 04:35:23'),(97,42,1,1,3000.00,3000.00,'2026-03-08 04:36:48','2026-03-08 04:36:48'),(98,42,5,1,200.00,200.00,'2026-03-08 04:36:48','2026-03-08 04:36:48'),(99,43,1,1,3000.00,3000.00,'2026-03-08 04:38:01','2026-03-08 04:38:01'),(100,44,1,1,3000.00,3000.00,'2026-03-08 04:39:07','2026-03-08 04:39:07'),(101,44,1,1,2950.00,2950.00,'2026-03-08 04:39:07','2026-03-08 04:39:07'),(102,44,5,1,200.00,200.00,'2026-03-08 04:39:07','2026-03-08 04:39:07'),(103,45,1,3,3000.00,9000.00,'2026-03-08 04:39:44','2026-03-08 04:39:44'),(104,45,5,1,200.00,200.00,'2026-03-08 04:39:44','2026-03-08 04:39:44'),(105,46,2,1,3050.00,3050.00,'2026-03-08 04:41:09','2026-03-08 04:41:09'),(106,47,1,1,3000.00,3000.00,'2026-03-08 04:42:20','2026-03-08 04:42:20'),(107,48,1,1,3100.00,3100.00,'2026-03-08 04:43:17','2026-03-08 04:43:17'),(108,48,1,1,3000.00,3000.00,'2026-03-08 04:43:17','2026-03-08 04:43:17'),(109,48,5,2,200.00,400.00,'2026-03-08 04:43:17','2026-03-08 04:43:17'),(110,48,6,1,90.00,90.00,'2026-03-08 04:43:17','2026-03-08 04:43:17'),(111,49,1,1,3000.00,3000.00,'2026-03-08 04:43:58','2026-03-08 04:43:58'),(112,49,1,1,2850.00,2850.00,'2026-03-08 04:43:58','2026-03-08 04:43:58'),(113,50,1,2,3100.00,6200.00,'2026-03-08 04:44:23','2026-03-08 04:44:23'),(114,51,4,2,950.00,1900.00,'2026-03-08 04:45:01','2026-03-08 04:45:01'),(115,52,1,4,2850.00,11400.00,'2026-03-08 04:46:18','2026-03-08 04:46:18'),(116,52,1,1,3100.00,3100.00,'2026-03-08 04:46:18','2026-03-08 04:46:18'),(117,52,4,1,950.00,950.00,'2026-03-08 04:46:18','2026-03-08 04:46:18'),(118,53,4,2,950.00,1900.00,'2026-03-08 04:46:43','2026-03-08 04:46:43'),(119,54,1,1,3200.00,3200.00,'2026-03-08 04:48:07','2026-03-08 04:48:07'),(120,55,5,1,200.00,200.00,'2026-03-08 04:48:35','2026-03-08 04:48:35'),(121,56,1,1,3050.00,3050.00,'2026-03-08 04:49:55','2026-03-08 04:49:55'),(122,57,1,1,3150.00,3150.00,'2026-03-08 05:11:16','2026-03-08 05:11:16'),(123,57,4,1,950.00,950.00,'2026-03-08 05:11:16','2026-03-08 05:11:16'),(124,58,1,1,3100.00,3100.00,'2026-03-08 05:11:53','2026-03-08 05:11:53'),(125,58,4,1,950.00,950.00,'2026-03-08 05:11:53','2026-03-08 05:11:53'),(126,59,4,1,950.00,950.00,'2026-03-08 05:12:09','2026-03-08 05:12:09'),(127,60,1,1,3150.00,3150.00,'2026-03-08 05:15:18','2026-03-08 05:15:18'),(128,60,4,1,950.00,950.00,'2026-03-08 05:15:18','2026-03-08 05:15:18'),(129,61,1,1,3150.00,3150.00,'2026-03-08 05:17:51','2026-03-08 05:17:51'),(130,61,5,2,200.00,400.00,'2026-03-08 05:17:51','2026-03-08 05:17:51'),(131,61,4,1,1000.00,1000.00,'2026-03-08 05:17:51','2026-03-08 05:17:51'),(132,61,6,2,90.00,180.00,'2026-03-08 05:17:51','2026-03-08 05:17:51'),(133,61,4,1,950.00,950.00,'2026-03-08 05:17:51','2026-03-08 05:17:51'),(134,62,1,1,3100.00,3100.00,'2026-03-08 05:19:08','2026-03-08 05:19:08'),(135,62,1,2,3000.00,6000.00,'2026-03-08 05:19:08','2026-03-08 05:19:08'),(136,62,6,3,83.00,249.00,'2026-03-08 05:19:08','2026-03-08 05:19:08'),(137,63,1,1,3100.00,3100.00,'2026-03-08 05:19:39','2026-03-08 05:19:39'),(138,64,4,1,1000.00,1000.00,'2026-03-08 05:20:10','2026-03-08 05:20:10'),(139,64,5,1,200.00,200.00,'2026-03-08 05:20:10','2026-03-08 05:20:10'),(140,65,1,1,3200.00,3200.00,'2026-03-08 05:20:34','2026-03-08 05:20:34'),(143,66,1,4,2850.00,11400.00,'2026-03-08 05:22:39','2026-03-08 05:22:39'),(144,66,1,2,3200.00,6400.00,'2026-03-08 05:22:39','2026-03-08 05:22:39'),(145,67,1,1,3100.00,3100.00,'2026-03-08 05:23:56','2026-03-08 05:23:56'),(146,68,4,1,1000.00,1000.00,'2026-03-08 05:24:25','2026-03-08 05:24:25'),(147,68,5,2,200.00,400.00,'2026-03-08 05:24:25','2026-03-08 05:24:25'),(148,69,1,1,3150.00,3150.00,'2026-03-08 05:26:33','2026-03-08 05:26:33'),(149,69,4,1,1000.00,1000.00,'2026-03-08 05:26:33','2026-03-08 05:26:33'),(150,69,4,1,950.00,950.00,'2026-03-08 05:26:33','2026-03-08 05:26:33'),(151,69,7,2,350.00,700.00,'2026-03-08 05:26:33','2026-03-08 05:26:33'),(152,69,9,1,175.00,175.00,'2026-03-08 05:26:33','2026-03-08 05:26:33'),(153,70,12,1,2900.00,2900.00,'2026-03-08 05:37:26','2026-03-08 05:37:26'),(154,70,1,2,3200.00,6400.00,'2026-03-08 05:37:26','2026-03-08 05:37:26'),(155,71,1,1,3200.00,3200.00,'2026-03-08 05:38:20','2026-03-08 05:38:20'),(156,71,4,1,1000.00,1000.00,'2026-03-08 05:38:20','2026-03-08 05:38:20'),(157,71,7,1,320.00,320.00,'2026-03-08 05:38:20','2026-03-08 05:38:20'),(158,72,5,1,200.00,200.00,'2026-03-08 05:38:52','2026-03-08 05:38:52'),(159,73,1,2,3100.00,6200.00,'2026-03-08 05:39:21','2026-03-08 05:39:21'),(160,74,1,1,3150.00,3150.00,'2026-03-08 05:40:37','2026-03-08 05:40:37'),(161,75,1,1,3200.00,3200.00,'2026-03-08 05:41:12','2026-03-08 05:41:12'),(162,75,5,2,200.00,400.00,'2026-03-08 05:41:12','2026-03-08 05:41:12'),(163,76,1,1,3100.00,3100.00,'2026-03-08 05:59:03','2026-03-08 05:59:03'),(164,77,1,2,3100.00,6200.00,'2026-03-08 05:59:34','2026-03-08 05:59:34'),(165,77,4,1,1000.00,1000.00,'2026-03-08 05:59:34','2026-03-08 05:59:34'),(166,78,1,1,3150.00,3150.00,'2026-03-08 06:00:09','2026-03-08 06:00:09'),(167,78,4,1,1000.00,1000.00,'2026-03-08 06:00:09','2026-03-08 06:00:09'),(168,79,1,2,3150.00,6300.00,'2026-03-08 06:00:50','2026-03-08 06:00:50'),(169,80,1,1,3050.00,3050.00,'2026-03-08 06:04:09','2026-03-08 06:04:09'),(170,80,4,1,1000.00,1000.00,'2026-03-08 06:04:09','2026-03-08 06:04:09'),(171,81,1,1,3100.00,3100.00,'2026-03-08 06:04:38','2026-03-08 06:04:38'),(172,81,5,2,200.00,400.00,'2026-03-08 06:04:38','2026-03-08 06:04:38'),(173,82,1,1,3050.00,3050.00,'2026-03-08 06:05:33','2026-03-08 06:05:33'),(174,82,1,5,3100.00,15500.00,'2026-03-08 06:05:33','2026-03-08 06:05:33'),(175,83,4,2,1000.00,2000.00,'2026-03-08 06:06:17','2026-03-08 06:06:17'),(176,83,4,1,980.00,980.00,'2026-03-08 06:06:17','2026-03-08 06:06:17'),(177,84,1,2,3050.00,6100.00,'2026-03-08 06:09:57','2026-03-08 06:09:57'),(178,84,4,1,950.00,950.00,'2026-03-08 06:09:57','2026-03-08 06:09:57'),(179,85,1,1,3100.00,3100.00,'2026-03-08 06:10:36','2026-03-08 06:10:36'),(180,85,1,1,3050.00,3050.00,'2026-03-08 06:10:36','2026-03-08 06:10:36'),(181,85,7,1,350.00,350.00,'2026-03-08 06:10:36','2026-03-08 06:10:36'),(182,86,4,1,1000.00,1000.00,'2026-03-08 06:11:15','2026-03-08 06:11:15'),(183,87,1,1,3100.00,3100.00,'2026-03-08 06:11:43','2026-03-08 06:11:43'),(184,87,5,1,200.00,200.00,'2026-03-08 06:11:43','2026-03-08 06:11:43'),(185,88,1,1,3050.00,3050.00,'2026-03-08 06:12:27','2026-03-08 06:12:27'),(186,89,1,3,3100.00,9300.00,'2026-03-08 06:13:22','2026-03-08 06:13:22'),(187,89,1,1,3050.00,3050.00,'2026-03-08 06:13:22','2026-03-08 06:13:22'),(188,89,4,2,1000.00,2000.00,'2026-03-08 06:13:22','2026-03-08 06:13:22'),(189,89,5,1,200.00,200.00,'2026-03-08 06:13:22','2026-03-08 06:13:22'),(190,90,1,1,3100.00,3100.00,'2026-03-08 06:13:54','2026-03-08 06:13:54'),(191,90,4,1,1000.00,1000.00,'2026-03-08 06:13:54','2026-03-08 06:13:54'),(192,91,4,1,1000.00,1000.00,'2026-03-08 06:16:26','2026-03-08 06:16:26'),(193,91,5,1,200.00,200.00,'2026-03-08 06:16:26','2026-03-08 06:16:26'),(194,92,1,1,3200.00,3200.00,'2026-03-08 06:17:41','2026-03-08 06:17:41'),(195,92,4,3,1000.00,3000.00,'2026-03-08 06:17:41','2026-03-08 06:17:41'),(196,92,7,1,350.00,350.00,'2026-03-08 06:17:41','2026-03-08 06:17:41'),(197,92,8,1,300.00,300.00,'2026-03-08 06:17:41','2026-03-08 06:17:41'),(198,93,4,1,1000.00,1000.00,'2026-03-08 06:18:06','2026-03-08 06:18:06'),(199,93,5,3,200.00,600.00,'2026-03-08 06:18:06','2026-03-08 06:18:06'),(200,94,1,1,3100.00,3100.00,'2026-03-08 06:18:30','2026-03-08 06:18:30'),(201,95,1,1,3100.00,3100.00,'2026-03-08 06:19:16','2026-03-08 06:19:16'),(202,95,4,1,1000.00,1000.00,'2026-03-08 06:19:16','2026-03-08 06:19:16'),(203,95,5,2,200.00,400.00,'2026-03-08 06:19:16','2026-03-08 06:19:16'),(204,96,4,1,1000.00,1000.00,'2026-03-08 06:20:03','2026-03-08 06:20:03'),(205,96,5,1,200.00,200.00,'2026-03-08 06:20:03','2026-03-08 06:20:03'),(206,97,1,1,3200.00,3200.00,'2026-03-08 06:20:57','2026-03-08 06:20:57'),(207,97,1,1,3100.00,3100.00,'2026-03-08 06:20:57','2026-03-08 06:20:57'),(208,97,4,1,1000.00,1000.00,'2026-03-08 06:20:57','2026-03-08 06:20:57'),(209,97,5,1,200.00,200.00,'2026-03-08 06:20:57','2026-03-08 06:20:57'),(210,98,1,1,3200.00,3200.00,'2026-03-08 06:21:38','2026-03-08 06:21:38'),(211,99,1,1,3000.00,3000.00,'2026-03-08 06:22:35','2026-03-08 06:22:35'),(212,99,1,1,3150.00,3150.00,'2026-03-08 06:22:35','2026-03-08 06:22:35'),(213,99,5,1,200.00,200.00,'2026-03-08 06:22:35','2026-03-08 06:22:35'),(214,100,1,2,3200.00,6400.00,'2026-03-08 06:24:21','2026-03-08 06:24:21'),(215,100,4,3,1000.00,3000.00,'2026-03-08 06:24:21','2026-03-08 06:24:21'),(216,100,5,1,200.00,200.00,'2026-03-08 06:24:21','2026-03-08 06:24:21'),(217,100,7,1,350.00,350.00,'2026-03-08 06:24:21','2026-03-08 06:24:21'),(218,100,8,1,300.00,300.00,'2026-03-08 06:24:21','2026-03-08 06:24:21'),(219,101,4,1,1000.00,1000.00,'2026-03-08 06:24:49','2026-03-08 06:24:49'),(220,102,1,2,3200.00,6400.00,'2026-03-08 06:27:48','2026-03-08 06:27:48'),(221,102,1,1,3100.00,3100.00,'2026-03-08 06:27:48','2026-03-08 06:27:48'),(222,103,4,2,1000.00,2000.00,'2026-03-08 06:28:17','2026-03-08 06:28:17'),(223,103,5,1,200.00,200.00,'2026-03-08 06:28:17','2026-03-08 06:28:17'),(224,104,1,1,3100.00,3100.00,'2026-03-08 06:29:15','2026-03-08 06:29:15'),(225,104,4,1,1000.00,1000.00,'2026-03-08 06:29:15','2026-03-08 06:29:15'),(226,105,1,1,3200.00,3200.00,'2026-03-08 06:30:22','2026-03-08 06:30:22'),(227,105,1,10,3100.00,31000.00,'2026-03-08 06:30:22','2026-03-08 06:30:22'),(228,105,5,2,200.00,400.00,'2026-03-08 06:30:22','2026-03-08 06:30:22'),(229,105,4,1,1000.00,1000.00,'2026-03-08 06:30:22','2026-03-08 06:30:22'),(230,106,1,2,3000.00,6000.00,'2026-03-08 06:39:00','2026-03-08 06:39:00'),(231,106,4,1,1000.00,1000.00,'2026-03-08 06:39:00','2026-03-08 06:39:00'),(232,106,5,1,200.00,200.00,'2026-03-08 06:39:00','2026-03-08 06:39:00'),(233,106,1,1,3100.00,3100.00,'2026-03-08 06:39:00','2026-03-08 06:39:00'),(234,107,1,1,3050.00,3050.00,'2026-03-08 06:39:47','2026-03-08 06:39:47'),(235,107,1,1,3000.00,3000.00,'2026-03-08 06:39:47','2026-03-08 06:39:47'),(236,107,1,1,3100.00,3100.00,'2026-03-08 06:39:47','2026-03-08 06:39:47'),(237,108,1,3,3200.00,9600.00,'2026-03-08 06:41:18','2026-03-08 06:41:18'),(238,108,1,4,3150.00,12600.00,'2026-03-08 06:41:18','2026-03-08 06:41:18'),(239,108,4,1,1000.00,1000.00,'2026-03-08 06:41:18','2026-03-08 06:41:18'),(240,109,4,4,1000.00,4000.00,'2026-03-08 06:42:25','2026-03-08 06:42:25'),(241,109,1,1,3100.00,3100.00,'2026-03-08 06:42:25','2026-03-08 06:42:25'),(242,109,1,1,3200.00,3200.00,'2026-03-08 06:42:25','2026-03-08 06:42:25'),(243,109,1,1,2950.00,2950.00,'2026-03-08 06:42:25','2026-03-08 06:42:25'),(244,110,1,1,3050.00,3050.00,'2026-03-10 09:22:37','2026-03-10 09:22:37'),(245,110,1,2,3200.00,6400.00,'2026-03-10 09:22:37','2026-03-10 09:22:37'),(246,110,1,1,3150.00,3150.00,'2026-03-10 09:22:37','2026-03-10 09:22:37'),(247,111,4,1,1000.00,1000.00,'2026-03-10 09:23:01','2026-03-10 09:23:01'),(248,111,7,1,350.00,350.00,'2026-03-10 09:23:01','2026-03-10 09:23:01'),(249,112,4,1,1000.00,1000.00,'2026-03-10 09:23:27','2026-03-10 09:23:27'),(251,113,1,2,3200.00,6400.00,'2026-03-10 09:27:08','2026-03-10 09:27:08'),(252,114,1,1,3200.00,3200.00,'2026-03-14 02:48:26','2026-03-14 02:48:26'),(253,114,1,2,3100.00,6200.00,'2026-03-14 02:48:26','2026-03-14 02:48:26'),(254,115,1,1,3100.00,3100.00,'2026-03-14 02:49:37','2026-03-14 02:49:37'),(255,115,4,2,1000.00,2000.00,'2026-03-14 02:49:37','2026-03-14 02:49:37'),(256,115,1,2,3200.00,6400.00,'2026-03-14 02:49:37','2026-03-14 02:49:37'),(257,115,5,1,200.00,200.00,'2026-03-14 02:49:37','2026-03-14 02:49:37'),(258,116,1,2,3200.00,6400.00,'2026-03-14 02:53:37','2026-03-14 02:53:37'),(259,116,5,1,200.00,200.00,'2026-03-14 02:53:37','2026-03-14 02:53:37'),(260,117,1,1,3100.00,3100.00,'2026-03-14 02:55:10','2026-03-14 02:55:10'),(261,117,4,1,1000.00,1000.00,'2026-03-14 02:55:10','2026-03-14 02:55:10'),(262,117,5,1,200.00,200.00,'2026-03-14 02:55:10','2026-03-14 02:55:10'),(263,117,7,1,350.00,350.00,'2026-03-14 02:55:10','2026-03-14 02:55:10'),(264,117,8,1,300.00,300.00,'2026-03-14 02:55:10','2026-03-14 02:55:10'),(265,118,1,1,3200.00,3200.00,'2026-03-14 02:57:06','2026-03-14 02:57:06'),(266,118,1,1,3150.00,3150.00,'2026-03-14 02:57:06','2026-03-14 02:57:06'),(267,118,1,1,3100.00,3100.00,'2026-03-14 02:57:06','2026-03-14 02:57:06'),(268,118,4,1,950.00,950.00,'2026-03-14 02:57:06','2026-03-14 02:57:06'),(269,118,5,1,200.00,200.00,'2026-03-14 02:57:06','2026-03-14 02:57:06'),(270,119,1,4,3200.00,12800.00,'2026-03-14 02:58:11','2026-03-14 02:58:11'),(271,120,4,1,1000.00,1000.00,'2026-03-14 02:58:58','2026-03-14 02:58:58'),(274,121,12,1,2900.00,2900.00,'2026-03-14 03:01:15','2026-03-14 03:01:15'),(275,121,4,2,1000.00,2000.00,'2026-03-14 03:01:15','2026-03-14 03:01:15'),(276,122,1,2,3200.00,6400.00,'2026-03-16 08:59:47','2026-03-16 08:59:47'),(277,122,1,2,3100.00,6200.00,'2026-03-16 08:59:47','2026-03-16 08:59:47'),(278,122,1,1,3000.00,3000.00,'2026-03-16 08:59:47','2026-03-16 08:59:47'),(279,123,1,3,3100.00,9300.00,'2026-03-16 09:00:21','2026-03-16 09:00:21'),(280,123,4,1,1000.00,1000.00,'2026-03-16 09:00:21','2026-03-16 09:00:21'),(281,124,1,1,3200.00,3200.00,'2026-03-16 09:00:46','2026-03-16 09:00:46'),(282,125,1,1,3200.00,3200.00,'2026-03-16 09:01:14','2026-03-16 09:01:14'),(283,125,4,1,1000.00,1000.00,'2026-03-16 09:01:14','2026-03-16 09:01:14'),(284,126,1,1,3200.00,3200.00,'2026-03-18 08:49:53','2026-03-18 08:49:53'),(285,126,7,3,350.00,1050.00,'2026-03-18 08:49:53','2026-03-18 08:49:53'),(286,126,4,1,1000.00,1000.00,'2026-03-18 08:49:53','2026-03-18 08:49:53'),(287,127,3,1,2950.00,2950.00,'2026-03-18 08:51:09','2026-03-18 08:51:09'),(288,127,1,1,3200.00,3200.00,'2026-03-18 08:51:09','2026-03-18 08:51:09'),(289,127,1,3,3050.00,9150.00,'2026-03-18 08:51:09','2026-03-18 08:51:09'),(290,127,1,1,3150.00,3150.00,'2026-03-18 08:51:09','2026-03-18 08:51:09'),(291,127,1,1,3100.00,3100.00,'2026-03-18 08:51:09','2026-03-18 08:51:09'),(292,128,1,2,3200.00,6400.00,'2026-03-18 08:51:32','2026-03-18 08:51:32'),(293,129,4,1,1000.00,1000.00,'2026-03-18 08:51:57','2026-03-18 08:51:57'),(294,129,5,1,200.00,200.00,'2026-03-18 08:51:57','2026-03-18 08:51:57'),(295,130,1,2,3200.00,6400.00,'2026-03-18 08:54:25','2026-03-18 08:54:25'),(296,130,1,3,3000.00,9000.00,'2026-03-18 08:54:25','2026-03-18 08:54:25'),(297,130,1,1,3050.00,3050.00,'2026-03-18 08:54:25','2026-03-18 08:54:25'),(298,131,1,1,3200.00,3200.00,'2026-03-18 08:56:51','2026-03-18 08:56:51'),(299,131,5,3,200.00,600.00,'2026-03-18 08:56:51','2026-03-18 08:56:51'),(300,131,4,1,1000.00,1000.00,'2026-03-18 08:56:51','2026-03-18 08:56:51'),(301,131,1,1,3150.00,3150.00,'2026-03-18 08:56:51','2026-03-18 08:56:51'),(302,132,1,1,3200.00,3200.00,'2026-03-19 09:18:07','2026-03-19 09:18:07'),(303,133,5,1,200.00,200.00,'2026-03-19 09:30:03','2026-03-19 09:30:03'),(304,133,7,3,300.00,900.00,'2026-03-19 09:30:03','2026-03-19 09:30:03'),(305,133,8,1,310.00,310.00,'2026-03-19 09:30:03','2026-03-19 09:30:03'),(306,134,4,1,1000.00,1000.00,'2026-03-19 09:30:26','2026-03-19 09:30:26'),(307,135,1,1,3200.00,3200.00,'2026-03-19 09:34:08','2026-03-19 09:34:08'),(308,135,1,2,3100.00,6200.00,'2026-03-19 09:34:08','2026-03-19 09:34:08'),(309,135,1,1,3250.00,3250.00,'2026-03-19 09:34:08','2026-03-19 09:34:08'),(310,135,5,1,210.00,210.00,'2026-03-19 09:34:08','2026-03-19 09:34:08'),(311,135,10,4,1050.00,4200.00,'2026-03-19 09:34:08','2026-03-19 09:34:08'),(312,136,1,1,3250.00,3250.00,'2026-03-20 09:25:07','2026-03-20 09:25:07'),(313,136,1,1,3200.00,3200.00,'2026-03-20 09:25:07','2026-03-20 09:25:07'),(314,136,1,4,3100.00,12400.00,'2026-03-20 09:25:07','2026-03-20 09:25:07'),(315,136,1,1,3050.00,3050.00,'2026-03-20 09:25:07','2026-03-20 09:25:07'),(316,137,1,2,3200.00,6400.00,'2026-03-20 09:27:48','2026-03-20 09:27:48'),(317,137,3,1,3000.00,3000.00,'2026-03-20 09:27:48','2026-03-20 09:27:48'),(318,137,5,1,200.00,200.00,'2026-03-20 09:27:48','2026-03-20 09:27:48'),(319,137,10,10,1065.00,10650.00,'2026-03-20 09:27:48','2026-03-20 09:27:48'),(320,138,1,2,3100.00,6200.00,'2026-03-24 09:35:04','2026-03-24 09:35:04'),(321,138,4,1,1050.00,1050.00,'2026-03-24 09:35:04','2026-03-24 09:35:04'),(322,139,5,1,210.00,210.00,'2026-03-24 09:35:34','2026-03-24 09:35:34'),(323,139,5,1,200.00,200.00,'2026-03-24 09:35:34','2026-03-24 09:35:34'),(324,140,1,1,3250.00,3250.00,'2026-03-24 09:36:22','2026-03-24 09:36:22'),(325,140,1,2,3100.00,6200.00,'2026-03-24 09:36:22','2026-03-24 09:36:22'),(326,141,1,1,3250.00,3250.00,'2026-03-24 09:37:16','2026-03-24 09:37:16'),(327,141,1,1,3240.00,3240.00,'2026-03-24 09:37:16','2026-03-24 09:37:16'),(328,141,4,1,1050.00,1050.00,'2026-03-24 09:37:16','2026-03-24 09:37:16'),(329,142,1,2,3150.00,6300.00,'2026-03-24 09:41:40','2026-03-24 09:41:40'),(330,142,1,2,3250.00,6500.00,'2026-03-24 09:41:40','2026-03-24 09:41:40'),(331,142,3,1,3050.00,3050.00,'2026-03-24 09:41:40','2026-03-24 09:41:40'),(332,142,7,3,333.00,999.00,'2026-03-24 09:41:40','2026-03-24 09:41:40'),(333,143,1,1,3250.00,3250.00,'2026-03-24 09:42:21','2026-03-24 09:42:21'),(334,144,1,1,3100.00,3100.00,'2026-03-24 09:43:34','2026-03-24 09:43:34'),(335,144,4,1,1050.00,1050.00,'2026-03-24 09:43:34','2026-03-24 09:43:34'),(336,144,1,1,3200.00,3200.00,'2026-03-24 09:43:34','2026-03-24 09:43:34'),(337,145,1,1,3100.00,3100.00,'2026-03-24 09:45:03','2026-03-24 09:45:03'),(338,145,10,4,1050.00,4200.00,'2026-03-24 09:45:03','2026-03-24 09:45:03'),(339,145,1,2,3200.00,6400.00,'2026-03-24 09:45:03','2026-03-24 09:45:03'),(340,146,1,1,3250.00,3250.00,'2026-03-24 09:45:53','2026-03-24 09:45:53'),(341,146,5,1,200.00,200.00,'2026-03-24 09:45:53','2026-03-24 09:45:53'),(342,146,1,2,3200.00,6400.00,'2026-03-24 09:45:53','2026-03-24 09:45:53'),(343,147,10,10,1050.00,10500.00,'2026-03-24 09:48:05','2026-03-24 09:48:05'),(344,147,1,1,3200.00,3200.00,'2026-03-24 09:48:05','2026-03-24 09:48:05'),(345,148,11,51,2650.00,135150.00,'2026-03-25 09:34:11','2026-03-25 09:34:11'),(346,149,1,2,3100.00,6200.00,'2026-03-26 09:21:46','2026-03-26 09:21:46'),(347,149,4,1,1050.00,1050.00,'2026-03-26 09:21:46','2026-03-26 09:21:46'),(348,150,1,1,3250.00,3250.00,'2026-03-26 09:22:57','2026-03-26 09:22:57'),(349,150,1,1,3050.00,3050.00,'2026-03-26 09:22:57','2026-03-26 09:22:57'),(350,150,1,1,3100.00,3100.00,'2026-03-26 09:22:57','2026-03-26 09:22:57'),(351,150,7,1,320.00,320.00,'2026-03-26 09:22:57','2026-03-26 09:22:57'),(352,150,13,1,130.00,130.00,'2026-03-26 09:22:57','2026-03-26 09:22:57'),(353,151,1,2,3250.00,6500.00,'2026-03-26 09:24:02','2026-03-26 09:24:02'),(354,151,5,1,200.00,200.00,'2026-03-26 09:24:02','2026-03-26 09:24:02'),(355,152,1,3,3200.00,9600.00,'2026-03-26 09:27:08','2026-03-26 09:27:08'),(356,152,10,1,1050.00,1050.00,'2026-03-26 09:27:08','2026-03-26 09:27:08'),(357,152,7,4,350.00,1400.00,'2026-03-26 09:27:08','2026-03-26 09:27:08'),(358,152,8,1,300.00,300.00,'2026-03-26 09:27:08','2026-03-26 09:27:08'),(359,152,10,6,1050.00,6300.00,'2026-03-26 09:27:08','2026-03-26 09:27:08'),(360,152,4,2,1000.00,2000.00,'2026-03-26 09:27:08','2026-03-26 09:27:08'),(361,152,4,1,1050.00,1050.00,'2026-03-26 09:27:08','2026-03-26 09:27:08'),(362,152,1,2,3100.00,6200.00,'2026-03-26 09:27:08','2026-03-26 09:27:08'),(363,153,1,7,3200.00,22400.00,'2026-03-29 07:28:44','2026-03-29 07:28:44'),(364,153,1,2,3100.00,6200.00,'2026-03-29 07:28:44','2026-03-29 07:28:44'),(365,154,1,1,3200.00,3200.00,'2026-03-29 07:29:22','2026-03-29 07:29:22'),(366,155,10,7,1042.85,7299.95,'2026-03-29 07:33:24','2026-03-29 07:33:24'),(367,155,1,1,3150.00,3150.00,'2026-03-29 07:33:24','2026-03-29 07:33:24'),(368,155,10,6,1050.00,6300.00,'2026-03-29 07:33:24','2026-03-29 07:33:24'),(369,155,5,1,200.00,200.00,'2026-03-29 07:33:24','2026-03-29 07:33:24'),(370,156,1,2,3250.00,6500.00,'2026-03-29 07:38:44','2026-03-29 07:38:44'),(371,156,1,1,3200.00,3200.00,'2026-03-29 07:38:44','2026-03-29 07:38:44'),(372,157,1,5,3100.00,15500.00,'2026-03-29 07:39:58','2026-03-29 07:39:58'),(373,157,1,1,3250.00,3250.00,'2026-03-29 07:39:58','2026-03-29 07:39:58'),(374,157,4,1,1000.00,1000.00,'2026-03-29 07:39:58','2026-03-29 07:39:58'),(375,157,7,1,350.00,350.00,'2026-03-29 07:39:58','2026-03-29 07:39:58'),(376,158,1,2,3250.00,6500.00,'2026-03-29 07:47:53','2026-03-29 07:47:53'),(387,159,1,2,3100.00,6200.00,'2026-03-29 07:53:42','2026-03-29 07:53:42'),(388,159,7,4,340.00,1360.00,'2026-03-29 07:53:42','2026-03-29 07:53:42'),(389,159,8,2,280.00,560.00,'2026-03-29 07:53:42','2026-03-29 07:53:42'),(390,159,1,1,3150.00,3150.00,'2026-03-29 07:53:42','2026-03-29 07:53:42'),(391,159,4,2,1050.00,2100.00,'2026-03-29 07:53:42','2026-03-29 07:53:42'),(392,160,5,1,210.00,210.00,'2026-03-29 07:54:03','2026-03-29 07:54:03'),(393,161,1,1,3150.00,3150.00,'2026-03-31 09:08:15','2026-03-31 09:08:15'),(394,161,4,4,875.00,3500.00,'2026-03-31 09:08:15','2026-03-31 09:08:15'),(395,161,1,1,3200.00,3200.00,'2026-03-31 09:08:15','2026-03-31 09:08:15'),(396,161,4,1,1050.00,1050.00,'2026-03-31 09:08:15','2026-03-31 09:08:15'),(397,161,10,1,1050.00,1050.00,'2026-03-31 09:08:15','2026-03-31 09:08:15'),(398,162,1,1,3250.00,3250.00,'2026-03-31 09:08:52','2026-03-31 09:08:52'),(399,162,4,1,1000.00,1000.00,'2026-03-31 09:08:52','2026-03-31 09:08:52'),(400,163,1,3,3150.00,9450.00,'2026-03-31 09:13:46','2026-03-31 09:13:46'),(401,163,13,1,150.00,150.00,'2026-03-31 09:13:46','2026-03-31 09:13:46'),(402,163,3,1,3040.00,3040.00,'2026-03-31 09:13:46','2026-03-31 09:13:46'),(403,163,1,1,3200.00,3200.00,'2026-03-31 09:13:46','2026-03-31 09:13:46'),(404,164,1,1,3200.00,3200.00,'2026-03-31 09:14:11','2026-03-31 09:14:11'),(405,165,1,2,3100.00,6200.00,'2026-03-31 09:18:18','2026-03-31 09:18:18'),(406,165,1,1,3150.00,3150.00,'2026-03-31 09:18:18','2026-03-31 09:18:18'),(407,165,4,1,1000.00,1000.00,'2026-03-31 09:18:18','2026-03-31 09:18:18'),(408,165,5,1,210.00,210.00,'2026-03-31 09:18:18','2026-03-31 09:18:18'),(409,166,1,2,3250.00,6500.00,'2026-04-02 09:31:56','2026-04-02 09:31:56'),(410,166,1,1,3200.00,3200.00,'2026-04-02 09:31:56','2026-04-02 09:31:56'),(411,167,1,2,3150.00,6300.00,'2026-04-02 09:32:48','2026-04-02 09:32:48'),(412,167,7,2,350.00,700.00,'2026-04-02 09:32:48','2026-04-02 09:32:48'),(413,168,1,1,3150.00,3150.00,'2026-04-07 08:47:51','2026-04-07 08:47:51'),(414,168,1,1,3100.00,3100.00,'2026-04-07 08:47:51','2026-04-07 08:47:51'),(417,169,1,3,3123.33,9369.99,'2026-04-07 08:49:15','2026-04-07 08:49:15'),(418,169,4,1,1050.00,1050.00,'2026-04-07 08:49:15','2026-04-07 08:49:15'),(419,170,1,2,3125.00,6250.00,'2026-04-07 08:59:47','2026-04-07 08:59:47'),(421,172,1,5,3250.00,16250.00,'2026-04-07 09:04:28','2026-04-07 09:04:28'),(422,172,7,6,350.00,2100.00,'2026-04-07 09:04:28','2026-04-07 09:04:28'),(423,172,1,1,3300.00,3300.00,'2026-04-07 09:04:28','2026-04-07 09:04:28'),(424,172,4,1,1050.00,1050.00,'2026-04-07 09:04:28','2026-04-07 09:04:28'),(425,173,10,1,1080.00,1080.00,'2026-04-07 09:04:49','2026-04-07 09:04:49'),(426,174,10,5,1080.00,5400.00,'2026-04-07 09:06:55','2026-04-07 09:06:55'),(427,174,1,3,3250.00,9750.00,'2026-04-07 09:06:55','2026-04-07 09:06:55'),(428,174,10,5,1050.00,5250.00,'2026-04-07 09:06:55','2026-04-07 09:06:55'),(430,176,1,1,3300.00,3300.00,'2026-04-07 09:09:17','2026-04-07 09:09:17'),(431,176,10,8,1075.00,8600.00,'2026-04-07 09:09:17','2026-04-07 09:09:17'),(432,176,4,1,1050.00,1050.00,'2026-04-07 09:09:17','2026-04-07 09:09:17'),(433,176,9,1,180.00,180.00,'2026-04-07 09:09:17','2026-04-07 09:09:17'),(434,176,5,1,210.00,210.00,'2026-04-07 09:09:17','2026-04-07 09:09:17'),(435,177,1,1,3300.00,3300.00,'2026-04-07 09:10:04','2026-04-07 09:10:04'),(436,177,4,1,1050.00,1050.00,'2026-04-07 09:10:04','2026-04-07 09:10:04'),(437,178,1,1,3300.00,3300.00,'2026-04-07 09:10:20','2026-04-07 09:10:20'),(438,179,1,2,3300.00,6600.00,'2026-04-07 09:10:44','2026-04-07 09:10:44'),(439,180,4,1,1000.00,1000.00,'2026-04-07 09:12:10','2026-04-07 09:12:10'),(440,180,1,1,3100.00,3100.00,'2026-04-07 09:12:10','2026-04-07 09:12:10'),(441,181,10,10,1050.00,10500.00,'2026-04-07 09:13:00','2026-04-07 09:13:00'),(443,182,1,1,3300.00,3300.00,'2026-04-11 08:24:39','2026-04-11 08:24:39'),(444,182,4,1,1050.00,1050.00,'2026-04-11 08:24:39','2026-04-11 08:24:39'),(445,183,1,1,3300.00,3300.00,'2026-04-11 08:25:13','2026-04-11 08:25:13'),(446,184,1,1,3300.00,3300.00,'2026-04-11 08:26:36','2026-04-11 08:26:36'),(447,184,5,2,210.00,420.00,'2026-04-11 08:26:36','2026-04-11 08:26:36'),(448,185,10,7,1080.00,7560.00,'2026-04-11 08:30:32','2026-04-11 08:30:32'),(449,186,1,1,3300.00,3300.00,'2026-04-11 08:33:43','2026-04-11 08:33:43'),(450,186,13,1,150.00,150.00,'2026-04-11 08:33:43','2026-04-11 08:33:43'),(451,186,5,2,210.00,420.00,'2026-04-11 08:33:43','2026-04-11 08:33:43'),(452,187,1,1,3200.00,3200.00,'2026-04-11 08:35:10','2026-04-11 08:35:10'),(453,187,1,1,3150.00,3150.00,'2026-04-11 08:35:10','2026-04-11 08:35:10'),(454,187,1,2,3100.00,6200.00,'2026-04-11 08:35:10','2026-04-11 08:35:10'),(456,171,1,3,3150.00,9450.00,'2026-04-11 08:42:34','2026-04-11 08:42:34'),(457,188,10,10,1075.00,10750.00,'2026-04-18 06:13:11','2026-04-18 06:13:11'),(458,189,1,1,3300.00,3300.00,'2026-04-18 06:16:14','2026-04-18 06:16:14'),(459,189,4,1,1000.00,1000.00,'2026-04-18 06:16:14','2026-04-18 06:16:14'),(460,189,4,1,1050.00,1050.00,'2026-04-18 06:16:14','2026-04-18 06:16:14'),(461,190,5,3,210.00,630.00,'2026-04-18 06:18:03','2026-04-18 06:18:03'),(462,190,1,1,3150.00,3150.00,'2026-04-18 06:18:03','2026-04-18 06:18:03'),(463,190,10,3,1080.00,3240.00,'2026-04-18 06:18:03','2026-04-18 06:18:03'),(464,191,1,4,3300.00,13200.00,'2026-04-18 06:20:21','2026-04-18 06:20:21'),(465,191,4,1,1050.00,1050.00,'2026-04-18 06:20:21','2026-04-18 06:20:21'),(466,192,1,1,3200.00,3200.00,'2026-04-18 06:21:27','2026-04-18 06:21:27'),(467,192,5,1,210.00,210.00,'2026-04-18 06:21:27','2026-04-18 06:21:27'),(468,192,5,1,200.00,200.00,'2026-04-18 06:21:27','2026-04-18 06:21:27'),(469,193,5,1,210.00,210.00,'2026-04-18 06:24:05','2026-04-18 06:24:05'),(470,194,1,1,3250.00,3250.00,'2026-04-18 06:26:14','2026-04-18 06:26:14'),(471,194,4,2,1050.00,2100.00,'2026-04-18 06:26:14','2026-04-18 06:26:14'),(472,194,5,1,210.00,210.00,'2026-04-18 06:26:14','2026-04-18 06:26:14'),(473,195,4,2,1050.00,2100.00,'2026-04-18 06:27:55','2026-04-18 06:27:55'),(474,196,4,1,1050.00,1050.00,'2026-04-18 06:30:02','2026-04-18 06:30:02'),(475,196,5,1,210.00,210.00,'2026-04-18 06:30:02','2026-04-18 06:30:02'),(476,196,1,2,3200.00,6400.00,'2026-04-18 06:30:02','2026-04-18 06:30:02'),(477,196,7,6,320.00,1920.00,'2026-04-18 06:30:02','2026-04-18 06:30:02'),(478,196,9,1,160.00,160.00,'2026-04-18 06:30:02','2026-04-18 06:30:02'),(479,197,7,3,350.00,1050.00,'2026-04-18 06:30:45','2026-04-18 06:30:45'),(480,197,4,2,1050.00,2100.00,'2026-04-18 06:30:45','2026-04-18 06:30:45'),(481,198,4,1,1050.00,1050.00,'2026-04-21 09:06:44','2026-04-21 09:06:44'),(482,198,10,1,1050.00,1050.00,'2026-04-21 09:06:44','2026-04-21 09:06:44'),(483,198,7,1,350.00,350.00,'2026-04-21 09:06:44','2026-04-21 09:06:44'),(485,200,1,1,3300.00,3300.00,'2026-04-21 09:07:22','2026-04-21 09:07:22'),(486,201,1,1,3300.00,3300.00,'2026-04-21 09:08:07','2026-04-21 09:08:07'),(487,201,4,1,1050.00,1050.00,'2026-04-21 09:08:07','2026-04-21 09:08:07'),(488,201,5,2,210.00,420.00,'2026-04-21 09:08:07','2026-04-21 09:08:07'),(490,203,7,1,350.00,350.00,'2026-04-21 09:10:04','2026-04-21 09:10:04'),(491,203,5,1,210.00,210.00,'2026-04-21 09:10:04','2026-04-21 09:10:04'),(492,204,10,4,1050.00,4200.00,'2026-04-21 09:11:49','2026-04-21 09:11:49'),(493,204,1,2,3150.00,6300.00,'2026-04-21 09:11:49','2026-04-21 09:11:49'),(494,204,10,1,1060.00,1060.00,'2026-04-21 09:11:49','2026-04-21 09:11:49'),(495,204,5,1,210.00,210.00,'2026-04-21 09:11:49','2026-04-21 09:11:49'),(496,205,3,1,3050.00,3050.00,'2026-04-23 08:53:23','2026-04-23 08:53:23'),(497,205,1,1,3200.00,3200.00,'2026-04-23 08:53:23','2026-04-23 08:53:23'),(503,175,1,1,3200.00,3200.00,'2026-04-23 08:57:42','2026-04-23 08:57:42'),(504,206,5,1,200.00,200.00,'2026-04-23 08:58:28','2026-04-23 08:58:28'),(505,206,4,1,1050.00,1050.00,'2026-04-23 08:58:28','2026-04-23 08:58:28'),(506,206,1,1,3300.00,3300.00,'2026-04-23 08:58:28','2026-04-23 08:58:28'),(507,206,1,1,3200.00,3200.00,'2026-04-23 08:58:28','2026-04-23 08:58:28'),(508,206,5,1,210.00,210.00,'2026-04-23 08:58:28','2026-04-23 08:58:28'),(509,207,1,1,3250.00,3250.00,'2026-04-23 08:59:02','2026-04-23 08:59:02'),(510,208,1,1,3200.00,3200.00,'2026-04-23 08:59:36','2026-04-23 08:59:36'),(511,208,4,1,1050.00,1050.00,'2026-04-23 08:59:36','2026-04-23 08:59:36'),(512,209,1,1,3300.00,3300.00,'2026-05-02 00:38:16','2026-05-02 00:38:16'),(513,209,1,1,3200.00,3200.00,'2026-05-02 00:38:16','2026-05-02 00:38:16'),(514,210,1,1,3300.00,3300.00,'2026-05-02 00:38:57','2026-05-02 00:38:57'),(515,211,10,1,1050.00,1050.00,'2026-05-02 00:39:23','2026-05-02 00:39:23'),(518,212,1,1,3300.00,3300.00,'2026-05-02 00:40:25','2026-05-02 00:40:25'),(519,212,5,1,210.00,210.00,'2026-05-02 00:40:25','2026-05-02 00:40:25'),(520,213,10,2,1050.00,2100.00,'2026-05-02 00:41:45','2026-05-02 00:41:45'),(521,213,1,1,3150.00,3150.00,'2026-05-02 00:41:45','2026-05-02 00:41:45'),(522,213,1,1,3300.00,3300.00,'2026-05-02 00:41:45','2026-05-02 00:41:45'),(523,214,7,3,350.00,1050.00,'2026-05-02 00:42:19','2026-05-02 00:42:19'),(524,215,1,1,3300.00,3300.00,'2026-05-02 00:44:07','2026-05-02 00:44:07'),(525,215,4,1,1050.00,1050.00,'2026-05-02 00:44:07','2026-05-02 00:44:07'),(526,216,4,1,1050.00,1050.00,'2026-05-02 00:45:20','2026-05-02 00:45:20'),(527,217,1,2,3200.00,6400.00,'2026-05-02 00:46:17','2026-05-02 00:46:17'),(528,217,1,1,3250.00,3250.00,'2026-05-02 00:46:17','2026-05-02 00:46:17'),(529,217,5,1,210.00,210.00,'2026-05-02 00:46:17','2026-05-02 00:46:17'),(530,218,1,1,3300.00,3300.00,'2026-05-02 00:46:40','2026-05-02 00:46:40'),(531,219,4,1,1050.00,1050.00,'2026-05-02 00:47:59','2026-05-02 00:47:59'),(532,219,5,1,210.00,210.00,'2026-05-02 00:47:59','2026-05-02 00:47:59'),(536,221,10,1,1050.00,1050.00,'2026-05-02 00:49:31','2026-05-02 00:49:31'),(537,221,4,1,1050.00,1050.00,'2026-05-02 00:49:31','2026-05-02 00:49:31'),(542,222,1,2,3100.00,6200.00,'2026-05-02 02:39:05','2026-05-02 02:39:05'),(544,223,1,2,3100.00,6200.00,'2026-05-02 02:47:10','2026-05-02 02:47:10'),(545,224,1,1,3200.00,3200.00,'2026-05-02 05:10:57','2026-05-02 05:10:57'),(546,225,7,11,340.00,3740.00,'2026-05-02 05:14:07','2026-05-02 05:14:07'),(547,225,8,1,280.00,280.00,'2026-05-02 05:14:07','2026-05-02 05:14:07'),(548,226,1,1,3200.00,3200.00,'2026-05-02 05:14:50','2026-05-02 05:14:50'),(549,227,1,1,3200.00,3200.00,'2026-05-02 05:15:22','2026-05-02 05:15:22'),(552,229,10,7,1080.00,7560.00,'2026-05-02 05:18:26','2026-05-02 05:18:26'),(553,229,7,2,350.00,700.00,'2026-05-02 05:18:26','2026-05-02 05:18:26'),(554,228,10,10,1080.00,10800.00,'2026-05-02 05:18:51','2026-05-02 05:18:51'),(555,228,7,5,350.00,1750.00,'2026-05-02 05:18:51','2026-05-02 05:18:51'),(557,202,4,2,1050.00,2100.00,'2026-05-02 05:19:52','2026-05-02 05:19:52'),(558,199,4,1,1000.00,1000.00,'2026-05-02 05:20:04','2026-05-02 05:20:04'),(559,220,1,1,3200.00,3200.00,'2026-05-02 07:34:20','2026-05-02 07:34:20'),(560,220,4,2,1050.00,2100.00,'2026-05-02 07:34:20','2026-05-02 07:34:20'),(561,220,10,8,1080.00,8640.00,'2026-05-02 07:34:20','2026-05-02 07:34:20');
/*!40000 ALTER TABLE `sell_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sell_payments`
--

DROP TABLE IF EXISTS `sell_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sell_payments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sell_id` bigint(20) unsigned NOT NULL,
  `payment_date` date NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` enum('cash','upi','gpay','bank_transfer','cheque','other') NOT NULL DEFAULT 'cash',
  `reference_number` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sell_payments_sell_id_index` (`sell_id`),
  KEY `sell_payments_payment_date_index` (`payment_date`),
  CONSTRAINT `sell_payments_sell_id_foreign` FOREIGN KEY (`sell_id`) REFERENCES `sells` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sell_payments`
--

LOCK TABLES `sell_payments` WRITE;
/*!40000 ALTER TABLE `sell_payments` DISABLE KEYS */;
INSERT INTO `sell_payments` VALUES (1,175,'2026-04-21',900.00,'upi',NULL,'Bill No-35','2026-04-23 08:56:24','2026-04-23 08:56:24'),(10,222,'2026-04-26',6200.00,'cash',NULL,'Bill No-45','2026-05-02 03:11:25','2026-05-02 03:11:25'),(11,223,'2026-04-26',6200.00,'cash',NULL,'Bill No-46','2026-05-02 03:11:48','2026-05-02 03:11:48');
/*!40000 ALTER TABLE `sell_payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sell_returns`
--

DROP TABLE IF EXISTS `sell_returns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sell_returns` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sell_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `return_date` date NOT NULL,
  `quantity` int(11) NOT NULL,
  `return_price` decimal(10,2) NOT NULL,
  `total_return_amount` decimal(10,2) NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sell_returns_sell_id_foreign` (`sell_id`),
  KEY `sell_returns_product_id_foreign` (`product_id`),
  CONSTRAINT `sell_returns_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sell_returns_sell_id_foreign` FOREIGN KEY (`sell_id`) REFERENCES `sells` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sell_returns`
--

LOCK TABLES `sell_returns` WRITE;
/*!40000 ALTER TABLE `sell_returns` DISABLE KEYS */;
INSERT INTO `sell_returns` VALUES (1,196,7,'2026-04-20',2,350.00,700.00,'cash return Rs.700/-',NULL,'2026-04-21 09:17:31','2026-04-21 09:17:45');
/*!40000 ALTER TABLE `sell_returns` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sells`
--

DROP TABLE IF EXISTS `sells`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sells` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sell_date` date NOT NULL,
  `seller_name` varchar(255) DEFAULT NULL,
  `seller_contact_number` varchar(255) DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_mode` enum('cash','upi','gpay','mix') DEFAULT NULL,
  `payment_status` enum('paid','pending','partial') NOT NULL DEFAULT 'paid',
  `amount_paid` decimal(10,2) NOT NULL DEFAULT 0.00,
  `cash_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `online_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `pending_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `notes` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `cash_sell_invoice_id` bigint(20) unsigned DEFAULT NULL,
  `online_sell_invoice_id` bigint(20) unsigned DEFAULT NULL,
  `mix_sell_invoice_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sells_cash_sell_invoice_id_foreign` (`cash_sell_invoice_id`),
  KEY `sells_online_sell_invoice_id_foreign` (`online_sell_invoice_id`),
  KEY `sells_mix_sell_invoice_id_foreign` (`mix_sell_invoice_id`),
  CONSTRAINT `sells_cash_sell_invoice_id_foreign` FOREIGN KEY (`cash_sell_invoice_id`) REFERENCES `sell_invoices` (`id`) ON DELETE SET NULL,
  CONSTRAINT `sells_mix_sell_invoice_id_foreign` FOREIGN KEY (`mix_sell_invoice_id`) REFERENCES `sell_invoices` (`id`) ON DELETE SET NULL,
  CONSTRAINT `sells_online_sell_invoice_id_foreign` FOREIGN KEY (`online_sell_invoice_id`) REFERENCES `sell_invoices` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=231 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sells`
--

LOCK TABLES `sells` WRITE;
/*!40000 ALTER TABLE `sells` DISABLE KEYS */;
INSERT INTO `sells` VALUES (1,'2026-01-01',NULL,NULL,11650.00,'upi','paid',11650.00,0.00,0.00,0.00,'2850/- Mahendrabhai Bill','2026-01-11 05:45:06','2026-01-11 05:45:06',NULL,NULL,NULL),(2,'2026-01-01',NULL,NULL,11600.00,'cash','paid',11600.00,0.00,0.00,0.00,NULL,'2026-01-11 05:46:08','2026-01-11 06:26:33',NULL,NULL,NULL),(3,'2026-01-02',NULL,NULL,16400.00,'upi','paid',16400.00,0.00,0.00,0.00,NULL,'2026-01-11 05:51:20','2026-01-11 05:51:20',NULL,NULL,NULL),(4,'2026-01-02',NULL,NULL,2100.00,'cash','paid',2100.00,0.00,0.00,0.00,NULL,'2026-01-11 05:52:05','2026-01-11 05:52:05',NULL,NULL,NULL),(5,'2026-01-03',NULL,NULL,4030.00,'upi','paid',4030.00,0.00,0.00,0.00,NULL,'2026-03-08 03:32:45','2026-03-08 03:32:45',NULL,NULL,NULL),(6,'2026-01-03',NULL,NULL,17700.00,'cash','paid',17700.00,0.00,0.00,0.00,NULL,'2026-03-08 03:35:58','2026-03-08 03:35:58',NULL,NULL,NULL),(7,'2026-01-04',NULL,NULL,11650.00,'upi','paid',11650.00,0.00,0.00,0.00,NULL,'2026-03-08 03:37:53','2026-03-08 03:37:53',NULL,NULL,NULL),(8,'2026-01-04',NULL,NULL,170.00,'cash','paid',170.00,0.00,0.00,0.00,NULL,'2026-03-08 03:38:30','2026-03-08 03:38:30',NULL,NULL,NULL),(9,'2026-01-05',NULL,NULL,12550.00,'upi','paid',12550.00,0.00,0.00,0.00,NULL,'2026-03-08 03:41:57','2026-03-08 03:41:57',NULL,NULL,NULL),(10,'2026-01-05',NULL,NULL,29100.00,'cash','paid',29100.00,0.00,0.00,0.00,NULL,'2026-03-08 03:46:28','2026-03-08 03:46:28',NULL,NULL,NULL),(11,'2026-01-06',NULL,NULL,18100.00,'upi','paid',18100.00,0.00,0.00,0.00,NULL,'2026-03-08 03:49:01','2026-03-08 03:49:01',NULL,NULL,NULL),(12,'2026-01-06',NULL,NULL,12000.00,'cash','paid',12000.00,0.00,0.00,0.00,'2800/- Tin-1 (16/12/26)','2026-03-08 03:50:43','2026-03-08 03:50:43',NULL,NULL,NULL),(13,'2026-03-07',NULL,NULL,8400.00,'upi','paid',8400.00,0.00,0.00,0.00,'Tin-3 (29-12-25)','2026-03-08 03:51:48','2026-03-08 03:57:53',NULL,NULL,NULL),(14,'2026-01-07',NULL,NULL,90.00,'cash','paid',90.00,0.00,0.00,0.00,NULL,'2026-03-08 03:52:09','2026-03-08 03:53:58',NULL,NULL,NULL),(15,'2026-01-07',NULL,NULL,200.00,'mix','paid',200.00,80.00,120.00,0.00,NULL,'2026-03-08 03:52:54','2026-03-08 03:53:53',NULL,NULL,NULL),(16,'2026-01-08',NULL,NULL,16400.00,'upi','paid',16400.00,0.00,0.00,0.00,NULL,'2026-03-08 03:56:03','2026-03-08 03:56:03',NULL,NULL,NULL),(17,'2026-01-08',NULL,NULL,17900.00,'cash','paid',17900.00,0.00,0.00,0.00,NULL,'2026-03-08 03:57:35','2026-03-08 03:57:35',NULL,NULL,NULL),(18,'2026-01-09',NULL,NULL,98600.00,'upi','paid',98600.00,0.00,0.00,0.00,'Ashokbhai Sutariya','2026-03-08 03:59:52','2026-03-08 03:59:52',NULL,NULL,NULL),(19,'2026-01-09',NULL,NULL,1900.00,'cash','paid',1900.00,0.00,0.00,0.00,NULL,'2026-03-08 04:00:16','2026-03-08 04:00:16',NULL,NULL,NULL),(20,'2026-01-10',NULL,NULL,600.00,'upi','paid',600.00,0.00,0.00,0.00,NULL,'2026-03-08 04:08:06','2026-03-08 04:08:06',NULL,NULL,NULL),(21,'2026-01-10',NULL,NULL,29350.00,'cash','paid',29350.00,0.00,0.00,0.00,NULL,'2026-03-08 04:09:48','2026-03-08 04:09:48',NULL,NULL,NULL),(22,'2026-01-11',NULL,NULL,13750.00,'upi','paid',13750.00,0.00,0.00,0.00,NULL,'2026-03-08 04:11:16','2026-03-08 04:11:16',NULL,NULL,NULL),(23,'2026-01-11',NULL,NULL,12800.00,'cash','paid',12800.00,0.00,0.00,0.00,NULL,'2026-03-08 04:14:46','2026-03-08 04:14:46',NULL,NULL,NULL),(24,'2026-01-12',NULL,NULL,1900.00,'upi','paid',1900.00,0.00,0.00,0.00,NULL,'2026-03-08 04:15:37','2026-03-08 04:15:37',NULL,NULL,NULL),(25,'2026-01-12',NULL,NULL,15519.00,'cash','paid',15520.00,0.00,0.00,0.00,NULL,'2026-03-08 04:17:14','2026-03-08 04:17:14',NULL,NULL,NULL),(26,'2026-01-15',NULL,NULL,3600.00,'upi','paid',3600.00,0.00,0.00,0.00,NULL,'2026-03-08 04:17:53','2026-03-08 04:17:53',NULL,NULL,NULL),(27,'2026-01-16',NULL,NULL,200.00,'upi','paid',200.00,0.00,0.00,0.00,NULL,'2026-03-08 04:18:21','2026-03-08 04:18:21',NULL,NULL,NULL),(28,'2026-01-16',NULL,NULL,2950.00,'mix','paid',2950.00,950.00,2000.00,0.00,NULL,'2026-03-08 04:18:59','2026-03-08 04:18:59',NULL,NULL,NULL),(29,'2026-01-16',NULL,NULL,4150.00,'cash','paid',4150.00,0.00,0.00,0.00,NULL,'2026-03-08 04:20:28','2026-03-08 04:20:28',NULL,NULL,NULL),(30,'2026-01-17',NULL,NULL,11600.00,'upi','paid',11600.00,0.00,0.00,0.00,NULL,'2026-03-08 04:23:33','2026-03-08 04:29:00',NULL,NULL,NULL),(31,'2026-01-17',NULL,NULL,6000.00,'cash','paid',6000.00,0.00,0.00,0.00,NULL,'2026-03-08 04:24:04','2026-03-08 04:24:04',NULL,NULL,NULL),(32,'2026-01-17',NULL,NULL,3000.00,'mix','paid',3000.00,1000.00,2000.00,0.00,NULL,'2026-03-08 04:24:33','2026-03-08 04:24:33',NULL,NULL,NULL),(33,'2026-01-18',NULL,NULL,13100.00,'upi','paid',13100.00,0.00,0.00,0.00,NULL,'2026-03-08 04:28:12','2026-03-08 04:28:12',NULL,NULL,NULL),(34,'2026-01-18',NULL,NULL,10150.00,'cash','paid',10150.00,0.00,0.00,0.00,NULL,'2026-03-08 04:30:11','2026-03-08 04:30:11',NULL,NULL,NULL),(35,'2026-01-19',NULL,NULL,3800.00,'upi','paid',3800.00,0.00,0.00,0.00,NULL,'2026-03-08 04:30:57','2026-03-08 04:30:57',NULL,NULL,NULL),(36,'2026-01-19',NULL,NULL,3090.00,'cash','paid',3090.00,0.00,0.00,0.00,NULL,'2026-03-08 04:31:42','2026-03-08 04:31:42',NULL,NULL,NULL),(37,'2026-01-20',NULL,NULL,3000.00,'upi','paid',3000.00,0.00,0.00,0.00,NULL,'2026-03-08 04:32:07','2026-03-08 04:32:07',NULL,NULL,NULL),(38,'2026-01-21',NULL,NULL,5950.00,'upi','paid',5950.00,0.00,0.00,0.00,NULL,'2026-03-08 04:32:55','2026-03-08 04:32:55',NULL,NULL,NULL),(39,'2026-01-21',NULL,NULL,400.00,'cash','paid',400.00,0.00,0.00,0.00,NULL,'2026-03-08 04:33:30','2026-03-08 04:33:30',NULL,NULL,NULL),(40,'2026-01-22',NULL,NULL,8850.00,'cash','paid',8850.00,0.00,0.00,0.00,NULL,'2026-03-08 04:34:16','2026-03-08 04:34:16',NULL,NULL,NULL),(41,'2026-01-24',NULL,NULL,3800.00,'cash','paid',3800.00,0.00,0.00,0.00,NULL,'2026-03-08 04:35:23','2026-03-08 04:35:23',NULL,NULL,NULL),(42,'2026-01-24',NULL,NULL,3200.00,'cash','paid',7800.00,0.00,0.00,0.00,'4600/- Vipul pujari bill-10','2026-03-08 04:36:48','2026-03-08 04:36:48',NULL,NULL,NULL),(43,'2026-01-25',NULL,NULL,3000.00,'cash','paid',3000.00,0.00,0.00,0.00,NULL,'2026-03-08 04:38:01','2026-03-08 04:38:01',NULL,NULL,NULL),(44,'2026-01-26',NULL,NULL,6150.00,'upi','paid',6150.00,0.00,0.00,0.00,NULL,'2026-03-08 04:39:07','2026-03-08 04:39:07',NULL,NULL,NULL),(45,'2026-01-26',NULL,NULL,9200.00,'cash','paid',9200.00,0.00,0.00,0.00,NULL,'2026-03-08 04:39:44','2026-03-08 04:39:44',NULL,NULL,NULL),(46,'2026-01-26',NULL,NULL,3050.00,'mix','paid',3050.00,3000.00,50.00,0.00,NULL,'2026-03-08 04:41:09','2026-03-08 04:41:09',NULL,NULL,NULL),(47,'2026-01-27',NULL,NULL,3000.00,'upi','paid',3000.00,0.00,0.00,0.00,NULL,'2026-03-08 04:42:20','2026-03-08 04:42:20',NULL,NULL,NULL),(48,'2026-01-27',NULL,NULL,6590.00,'cash','paid',6590.00,0.00,0.00,0.00,NULL,'2026-03-08 04:43:17','2026-03-08 04:43:17',NULL,NULL,NULL),(49,'2026-01-28',NULL,NULL,5850.00,'upi','paid',5950.00,0.00,0.00,0.00,NULL,'2026-03-08 04:43:58','2026-03-08 04:43:58',NULL,NULL,NULL),(50,'2026-01-28',NULL,NULL,6200.00,'cash','paid',6200.00,0.00,0.00,0.00,NULL,'2026-03-08 04:44:23','2026-03-08 04:44:23',NULL,NULL,NULL),(51,'2026-01-29',NULL,NULL,1900.00,'upi','paid',1900.00,0.00,0.00,0.00,NULL,'2026-03-08 04:45:01','2026-03-08 04:45:01',NULL,NULL,NULL),(52,'2026-01-29',NULL,NULL,15450.00,'cash','paid',15450.00,0.00,0.00,0.00,NULL,'2026-03-08 04:46:18','2026-03-08 04:46:18',NULL,NULL,NULL),(53,'2026-01-30',NULL,NULL,1900.00,'cash','paid',1900.00,0.00,0.00,0.00,NULL,'2026-03-08 04:46:43','2026-03-08 04:46:43',NULL,NULL,NULL),(54,'2026-01-31',NULL,NULL,3200.00,'upi','paid',3200.00,0.00,0.00,0.00,NULL,'2026-03-08 04:48:06','2026-03-08 04:48:06',NULL,NULL,NULL),(55,'2026-01-31',NULL,NULL,200.00,'cash','paid',200.00,0.00,0.00,0.00,NULL,'2026-03-08 04:48:35','2026-03-08 04:48:35',NULL,NULL,NULL),(56,'2026-01-31',NULL,NULL,3050.00,'mix','paid',3050.00,250.00,2800.00,0.00,NULL,'2026-03-08 04:49:55','2026-03-08 04:49:55',NULL,NULL,NULL),(57,'2026-02-01',NULL,NULL,4100.00,'upi','paid',4100.00,0.00,0.00,0.00,NULL,'2026-03-08 05:11:16','2026-03-08 05:11:16',NULL,NULL,NULL),(58,'2026-02-02',NULL,NULL,4050.00,'upi','paid',4050.00,0.00,0.00,0.00,NULL,'2026-03-08 05:11:53','2026-03-08 05:11:53',NULL,NULL,NULL),(59,'2026-02-02',NULL,NULL,950.00,'cash','paid',950.00,0.00,0.00,0.00,NULL,'2026-03-08 05:12:09','2026-03-08 05:12:09',NULL,NULL,NULL),(60,'2026-02-03',NULL,NULL,4100.00,'upi','paid',4100.00,0.00,0.00,0.00,NULL,'2026-03-08 05:15:18','2026-03-08 05:15:18',NULL,NULL,NULL),(61,'2026-02-03',NULL,NULL,5680.00,'cash','paid',5680.00,0.00,0.00,0.00,NULL,'2026-03-08 05:17:51','2026-03-08 05:17:51',NULL,NULL,NULL),(62,'2026-02-04',NULL,NULL,9349.00,'cash','paid',9350.00,0.00,0.00,0.00,NULL,'2026-03-08 05:19:08','2026-03-08 05:19:08',NULL,NULL,NULL),(63,'2026-02-05',NULL,NULL,3100.00,'upi','paid',3100.00,0.00,0.00,0.00,NULL,'2026-03-08 05:19:39','2026-03-08 05:19:39',NULL,NULL,NULL),(64,'2026-02-05',NULL,NULL,1200.00,'cash','paid',1200.00,0.00,0.00,0.00,NULL,'2026-03-08 05:20:10','2026-03-08 05:20:10',NULL,NULL,NULL),(65,'2026-02-06',NULL,NULL,3200.00,'upi','paid',3200.00,0.00,0.00,0.00,NULL,'2026-03-08 05:20:34','2026-03-08 05:20:34',NULL,NULL,NULL),(66,'2026-02-06',NULL,NULL,17800.00,'cash','paid',17800.00,0.00,0.00,0.00,'11400/- bill no 80/81 at (29/31-Jan-26)','2026-03-08 05:22:28','2026-03-08 05:22:39',NULL,NULL,NULL),(67,'2026-02-07',NULL,NULL,3100.00,'upi','paid',3100.00,0.00,0.00,0.00,NULL,'2026-03-08 05:23:56','2026-03-08 05:23:56',NULL,NULL,NULL),(68,'2026-02-07',NULL,NULL,1400.00,'cash','paid',1400.00,0.00,0.00,0.00,NULL,'2026-03-08 05:24:25','2026-03-08 05:24:25',NULL,NULL,NULL),(69,'2026-02-08',NULL,NULL,5975.00,'cash','paid',5975.00,0.00,0.00,0.00,NULL,'2026-03-08 05:26:33','2026-03-08 05:26:33',NULL,NULL,NULL),(70,'2026-02-09',NULL,NULL,9300.00,'cash','paid',11800.00,0.00,0.00,0.00,'2500/- bill-85 (Dineshbhai Vekariya)','2026-03-08 05:37:26','2026-03-08 05:37:26',NULL,NULL,NULL),(71,'2026-02-10',NULL,NULL,4520.00,'cash','paid',4520.00,0.00,0.00,0.00,NULL,'2026-03-08 05:38:19','2026-03-08 05:38:19',NULL,NULL,NULL),(72,'2026-02-11',NULL,NULL,200.00,'upi','paid',200.00,0.00,0.00,0.00,NULL,'2026-03-08 05:38:52','2026-03-08 05:38:52',NULL,NULL,NULL),(73,'2026-02-11',NULL,NULL,6200.00,'cash','paid',6200.00,0.00,0.00,0.00,NULL,'2026-03-08 05:39:21','2026-03-08 05:39:21',NULL,NULL,NULL),(74,'2026-02-12',NULL,NULL,3150.00,'upi','paid',3150.00,0.00,0.00,0.00,NULL,'2026-03-08 05:40:37','2026-03-08 05:40:37',NULL,NULL,NULL),(75,'2026-02-12',NULL,NULL,3600.00,'cash','paid',3600.00,0.00,0.00,0.00,NULL,'2026-03-08 05:41:12','2026-03-08 05:41:12',NULL,NULL,NULL),(76,'2026-02-13',NULL,NULL,3100.00,'upi','paid',3100.00,0.00,0.00,0.00,NULL,'2026-03-08 05:59:03','2026-03-08 05:59:03',NULL,NULL,NULL),(77,'2026-02-13',NULL,NULL,7200.00,'cash','paid',7200.00,0.00,0.00,0.00,NULL,'2026-03-08 05:59:34','2026-03-08 05:59:34',NULL,NULL,NULL),(78,'2026-02-14',NULL,NULL,4150.00,'upi','paid',4150.00,0.00,0.00,0.00,NULL,'2026-03-08 06:00:09','2026-03-08 06:00:09',NULL,NULL,NULL),(79,'2026-02-14',NULL,NULL,6300.00,'cash','paid',6300.00,0.00,0.00,0.00,NULL,'2026-03-08 06:00:50','2026-03-08 06:00:50',NULL,NULL,NULL),(80,'2026-02-15',NULL,NULL,4050.00,'upi','paid',4050.00,0.00,0.00,0.00,NULL,'2026-03-08 06:04:09','2026-03-08 06:04:09',NULL,NULL,NULL),(81,'2026-02-15',NULL,NULL,3500.00,'cash','paid',3500.00,0.00,0.00,0.00,NULL,'2026-03-08 06:04:38','2026-03-08 06:04:38',NULL,NULL,NULL),(82,'2026-02-16',NULL,NULL,18550.00,'upi','paid',18550.00,0.00,0.00,0.00,NULL,'2026-03-08 06:05:33','2026-03-08 06:05:33',NULL,NULL,NULL),(83,'2026-02-16',NULL,NULL,2980.00,'cash','paid',2980.00,0.00,0.00,0.00,NULL,'2026-03-08 06:06:17','2026-03-08 06:06:17',NULL,NULL,NULL),(84,'2026-02-17',NULL,NULL,7050.00,'upi','paid',7050.00,0.00,0.00,0.00,NULL,'2026-03-08 06:09:57','2026-03-08 06:09:57',NULL,NULL,NULL),(85,'2026-02-17',NULL,NULL,6500.00,'cash','paid',6500.00,0.00,0.00,0.00,NULL,'2026-03-08 06:10:36','2026-03-08 06:10:36',NULL,NULL,NULL),(86,'2026-02-18',NULL,NULL,1000.00,'upi','paid',1000.00,0.00,0.00,0.00,NULL,'2026-03-08 06:11:15','2026-03-08 06:11:15',NULL,NULL,NULL),(87,'2026-02-18',NULL,NULL,3300.00,'cash','paid',3300.00,0.00,0.00,0.00,NULL,'2026-03-08 06:11:43','2026-03-08 06:11:43',NULL,NULL,NULL),(88,'2026-02-19',NULL,NULL,3050.00,'upi','paid',3050.00,0.00,0.00,0.00,NULL,'2026-03-08 06:12:27','2026-03-08 06:12:27',NULL,NULL,NULL),(89,'2026-02-19',NULL,NULL,14550.00,'cash','paid',14550.00,0.00,0.00,0.00,NULL,'2026-03-08 06:13:22','2026-03-08 06:13:22',NULL,NULL,NULL),(90,'2026-02-20',NULL,NULL,4100.00,'upi','paid',4100.00,0.00,0.00,0.00,NULL,'2026-03-08 06:13:54','2026-03-08 06:13:54',NULL,NULL,NULL),(91,'2026-02-21',NULL,NULL,1200.00,'cash','paid',1200.00,0.00,0.00,0.00,NULL,'2026-03-08 06:16:26','2026-03-08 06:16:26',NULL,NULL,NULL),(92,'2026-02-22',NULL,NULL,6850.00,'upi','paid',6850.00,0.00,0.00,0.00,NULL,'2026-03-08 06:17:41','2026-03-08 06:17:41',NULL,NULL,NULL),(93,'2026-02-22',NULL,NULL,1600.00,'cash','paid',1600.00,0.00,0.00,0.00,NULL,'2026-03-08 06:18:06','2026-03-08 06:18:06',NULL,NULL,NULL),(94,'2026-02-23',NULL,NULL,3100.00,'upi','paid',3100.00,0.00,0.00,0.00,NULL,'2026-03-08 06:18:30','2026-03-08 06:18:30',NULL,NULL,NULL),(95,'2026-02-24',NULL,NULL,4500.00,'cash','paid',4500.00,0.00,0.00,0.00,NULL,'2026-03-08 06:19:16','2026-03-08 06:19:16',NULL,NULL,NULL),(96,'2026-02-25',NULL,NULL,1200.00,'upi','paid',1200.00,0.00,0.00,0.00,NULL,'2026-03-08 06:20:03','2026-03-08 06:20:03',NULL,NULL,NULL),(97,'2026-02-25',NULL,NULL,7500.00,'cash','paid',7500.00,0.00,0.00,0.00,NULL,'2026-03-08 06:20:57','2026-03-08 06:20:57',NULL,NULL,NULL),(98,'2026-02-26',NULL,NULL,3200.00,'upi','paid',3200.00,0.00,0.00,0.00,NULL,'2026-03-08 06:21:38','2026-03-08 06:21:38',NULL,NULL,NULL),(99,'2026-02-27',NULL,NULL,6350.00,'upi','paid',6350.00,0.00,0.00,0.00,NULL,'2026-03-08 06:22:35','2026-03-08 06:22:35',NULL,NULL,NULL),(100,'2026-02-27',NULL,NULL,10250.00,'cash','paid',10250.00,0.00,0.00,0.00,NULL,'2026-03-08 06:24:21','2026-03-08 06:24:21',NULL,NULL,NULL),(101,'2026-02-28',NULL,NULL,1000.00,'cash','paid',1000.00,0.00,0.00,0.00,NULL,'2026-03-08 06:24:49','2026-03-08 06:24:49',NULL,NULL,NULL),(102,'2026-03-01',NULL,NULL,9500.00,'upi','paid',9500.00,0.00,0.00,0.00,NULL,'2026-03-08 06:27:47','2026-03-21 08:09:38',NULL,NULL,NULL),(103,'2026-03-01',NULL,NULL,2200.00,'cash','paid',2200.00,0.00,0.00,0.00,NULL,'2026-03-08 06:28:17','2026-03-21 08:07:08',5,NULL,NULL),(104,'2026-03-02',NULL,NULL,4100.00,'upi','paid',4100.00,0.00,0.00,0.00,NULL,'2026-03-08 06:29:15','2026-03-21 08:09:28',NULL,NULL,NULL),(105,'2026-03-02',NULL,NULL,35600.00,'cash','paid',35600.00,0.00,0.00,0.00,NULL,'2026-03-08 06:30:22','2026-03-08 06:30:22',NULL,NULL,NULL),(106,'2026-03-03',NULL,NULL,10300.00,'upi','partial',9300.00,0.00,0.00,1000.00,NULL,'2026-03-08 06:39:00','2026-03-21 08:25:49',NULL,8,NULL),(107,'2026-03-03',NULL,NULL,9150.00,'cash','paid',9150.00,0.00,0.00,0.00,NULL,'2026-03-08 06:39:47','2026-03-08 06:39:47',NULL,NULL,NULL),(108,'2026-03-05',NULL,NULL,23200.00,'upi','paid',23200.00,0.00,0.00,0.00,NULL,'2026-03-08 06:41:18','2026-03-08 06:41:18',NULL,NULL,NULL),(109,'2026-03-05',NULL,NULL,13250.00,'cash','paid',13250.00,0.00,0.00,0.00,NULL,'2026-03-08 06:42:25','2026-03-08 06:42:25',NULL,NULL,NULL),(110,'2026-03-06',NULL,NULL,12600.00,'upi','paid',12600.00,0.00,0.00,0.00,NULL,'2026-03-10 09:22:37','2026-03-10 09:22:37',NULL,NULL,NULL),(111,'2026-03-06',NULL,NULL,1350.00,'cash','paid',1350.00,0.00,0.00,0.00,NULL,'2026-03-10 09:23:01','2026-03-10 09:23:01',NULL,NULL,NULL),(112,'2026-03-07',NULL,NULL,1000.00,'cash','paid',1000.00,0.00,0.00,0.00,NULL,'2026-03-10 09:23:27','2026-03-25 09:35:27',9,NULL,NULL),(113,'2026-03-07',NULL,NULL,6400.00,'upi','partial',3900.00,0.00,0.00,2500.00,'2500/- paid cash at 9 Feb 2026 for Bill No-85','2026-03-10 09:26:00','2026-03-10 09:27:08',NULL,NULL,NULL),(114,'2026-03-08',NULL,NULL,9400.00,'upi','paid',9400.00,0.00,0.00,0.00,NULL,'2026-03-14 02:48:26','2026-03-14 02:48:26',NULL,NULL,NULL),(115,'2026-03-08',NULL,NULL,11700.00,'cash','paid',11700.00,0.00,0.00,0.00,NULL,'2026-03-14 02:49:37','2026-03-14 02:49:37',NULL,NULL,NULL),(116,'2026-03-09',NULL,NULL,6600.00,'upi','paid',7600.00,0.00,0.00,0.00,'1000/- for boo no.4 at 5/3/2026','2026-03-14 02:53:37','2026-03-14 02:53:37',NULL,NULL,NULL),(117,'2026-03-09',NULL,NULL,4950.00,'cash','paid',4950.00,0.00,0.00,0.00,NULL,'2026-03-14 02:55:10','2026-03-14 02:55:10',NULL,NULL,NULL),(118,'2026-03-10',NULL,NULL,10600.00,'upi','paid',10600.00,0.00,0.00,0.00,NULL,'2026-03-14 02:57:06','2026-03-14 02:57:06',NULL,NULL,NULL),(119,'2026-03-10',NULL,NULL,12800.00,'cash','paid',12800.00,0.00,0.00,0.00,NULL,'2026-03-14 02:58:11','2026-03-14 02:58:11',NULL,NULL,NULL),(120,'2026-03-11',NULL,NULL,1000.00,'upi','paid',1000.00,0.00,0.00,0.00,NULL,'2026-03-14 02:58:58','2026-03-14 02:58:58',NULL,NULL,NULL),(121,'2026-03-11',NULL,NULL,4900.00,'cash','paid',4900.00,0.00,0.00,0.00,NULL,'2026-03-14 03:00:13','2026-03-14 03:01:15',NULL,NULL,NULL),(122,'2026-03-13',NULL,NULL,15600.00,'upi','paid',15600.00,0.00,0.00,0.00,NULL,'2026-03-16 08:59:47','2026-03-16 08:59:47',NULL,NULL,NULL),(123,'2026-03-13',NULL,NULL,10300.00,'cash','paid',10300.00,0.00,0.00,0.00,NULL,'2026-03-16 09:00:21','2026-03-16 09:00:21',NULL,NULL,NULL),(124,'2026-03-14',NULL,NULL,3200.00,'upi','paid',3200.00,0.00,0.00,0.00,NULL,'2026-03-16 09:00:46','2026-03-16 09:00:46',NULL,NULL,NULL),(125,'2026-03-14',NULL,NULL,4200.00,'cash','paid',4200.00,0.00,0.00,0.00,NULL,'2026-03-16 09:01:14','2026-03-16 09:01:14',NULL,NULL,NULL),(126,'2026-03-15',NULL,NULL,5250.00,'upi','paid',5250.00,0.00,0.00,0.00,NULL,'2026-03-18 08:49:53','2026-03-18 08:49:53',NULL,NULL,NULL),(127,'2026-03-15',NULL,NULL,21550.00,'cash','paid',21550.00,0.00,0.00,0.00,NULL,'2026-03-18 08:51:09','2026-03-18 08:51:09',NULL,NULL,NULL),(128,'2026-03-16',NULL,NULL,6400.00,'upi','paid',6400.00,0.00,0.00,0.00,NULL,'2026-03-18 08:51:32','2026-03-18 08:51:32',NULL,NULL,NULL),(129,'2026-03-16',NULL,NULL,1200.00,'cash','paid',1200.00,0.00,0.00,0.00,NULL,'2026-03-18 08:51:57','2026-03-18 08:51:57',NULL,NULL,NULL),(130,'2026-03-12',NULL,NULL,18450.00,'upi','paid',18450.00,0.00,0.00,0.00,NULL,'2026-03-18 08:54:25','2026-03-18 08:54:25',NULL,NULL,NULL),(131,'2026-03-12',NULL,NULL,7950.00,'cash','paid',7950.00,0.00,0.00,0.00,NULL,'2026-03-18 08:56:51','2026-03-18 08:56:51',NULL,NULL,NULL),(132,'2026-03-17',NULL,NULL,3200.00,'upi','paid',3200.00,0.00,0.00,0.00,NULL,'2026-03-19 09:18:07','2026-03-19 09:18:07',NULL,NULL,NULL),(133,'2026-03-17',NULL,NULL,1410.00,'cash','paid',1410.00,0.00,0.00,0.00,NULL,'2026-03-19 09:30:03','2026-03-19 09:30:03',NULL,NULL,NULL),(134,'2026-03-18',NULL,NULL,1000.00,'cash','paid',1000.00,0.00,0.00,0.00,NULL,'2026-03-19 09:30:26','2026-03-19 09:30:26',NULL,NULL,NULL),(135,'2026-03-18',NULL,NULL,17060.00,'upi','paid',17060.00,0.00,0.00,0.00,'1 Tin replace \r\nWeight 13.9 with Tin\r\n2 kg oil for Rs. 410/-','2026-03-19 09:34:08','2026-03-19 09:34:08',NULL,NULL,NULL),(136,'2026-03-19',NULL,NULL,21900.00,'upi','paid',21900.00,0.00,0.00,0.00,NULL,'2026-03-20 09:25:07','2026-03-20 09:25:07',NULL,NULL,NULL),(137,'2026-03-19',NULL,NULL,20250.00,'cash','paid',22250.00,0.00,0.00,0.00,'Ghav has include 150/- for auto charge','2026-03-20 09:27:48','2026-03-20 09:27:48',NULL,NULL,NULL),(138,'2026-03-20',NULL,NULL,7250.00,'upi','paid',7250.00,0.00,0.00,0.00,NULL,'2026-03-24 09:35:03','2026-03-24 09:35:03',NULL,NULL,NULL),(139,'2026-03-20',NULL,NULL,410.00,'cash','paid',410.00,0.00,0.00,0.00,NULL,'2026-03-24 09:35:34','2026-03-24 09:35:34',NULL,NULL,NULL),(140,'2026-03-21',NULL,NULL,9450.00,'upi','paid',9450.00,0.00,0.00,0.00,NULL,'2026-03-24 09:36:22','2026-03-24 09:36:22',NULL,NULL,NULL),(141,'2026-03-21',NULL,NULL,7540.00,'cash','paid',7540.00,0.00,0.00,0.00,NULL,'2026-03-24 09:37:16','2026-03-24 09:37:16',NULL,NULL,NULL),(142,'2026-03-22',NULL,NULL,16849.00,'upi','paid',16850.00,0.00,0.00,0.00,NULL,'2026-03-24 09:41:40','2026-03-24 09:41:40',NULL,NULL,NULL),(143,'2026-03-22',NULL,NULL,3250.00,'mix','paid',3250.00,2500.00,750.00,0.00,NULL,'2026-03-24 09:42:21','2026-03-24 09:42:21',NULL,NULL,NULL),(144,'2026-03-22',NULL,NULL,7350.00,'cash','paid',7350.00,0.00,0.00,0.00,NULL,'2026-03-24 09:43:34','2026-03-24 09:43:34',NULL,NULL,NULL),(145,'2026-03-23',NULL,NULL,13700.00,'upi','paid',13700.00,0.00,0.00,0.00,NULL,'2026-03-24 09:45:03','2026-03-24 09:45:03',NULL,NULL,NULL),(146,'2026-03-23',NULL,NULL,9850.00,'cash','paid',9850.00,0.00,0.00,0.00,NULL,'2026-03-24 09:45:53','2026-03-24 09:45:53',NULL,NULL,NULL),(147,'2026-03-24',NULL,NULL,13700.00,'mix','paid',13700.00,7000.00,6700.00,0.00,'5000 cash & 5500 online for 10 bag ghav\r\n2000 cash & 1200 online for 1 Tin 15kg','2026-03-24 09:48:05','2026-03-24 09:48:05',NULL,NULL,NULL),(148,'2026-03-07','Vrundavan Dham - Parimal','9974528029',135150.00,'cash','partial',124950.00,0.00,0.00,10200.00,'2450*51 = 1,24,950/- (21 Mar 2026)\r\n200*51 = 10,200/- (Ronak jode thi lewana baki)\r\nPrice 2600+50 transport na','2026-03-25 09:34:11','2026-03-25 09:35:27',9,NULL,NULL),(149,'2026-03-24',NULL,NULL,7250.00,'upi','paid',7250.00,0.00,0.00,0.00,NULL,'2026-03-26 09:21:46','2026-03-26 09:21:46',NULL,NULL,NULL),(150,'2026-03-24',NULL,NULL,9850.00,'cash','paid',9850.00,0.00,0.00,0.00,NULL,'2026-03-26 09:22:57','2026-03-26 09:22:57',NULL,NULL,NULL),(151,'2026-03-25',NULL,NULL,6700.00,'upi','paid',6700.00,0.00,0.00,0.00,'6500/- Gpay','2026-03-26 09:24:02','2026-03-26 09:24:02',NULL,NULL,NULL),(152,'2026-03-25',NULL,NULL,27900.00,'cash','paid',27900.00,0.00,0.00,0.00,NULL,'2026-03-26 09:27:08','2026-03-26 09:27:08',NULL,NULL,NULL),(153,'2026-03-26',NULL,NULL,28600.00,'upi','paid',28600.00,0.00,0.00,0.00,NULL,'2026-03-29 07:28:44','2026-03-29 07:28:44',NULL,NULL,NULL),(154,'2026-03-26',NULL,NULL,3200.00,'mix','paid',3200.00,700.00,2500.00,0.00,NULL,'2026-03-29 07:29:22','2026-03-29 07:29:22',NULL,NULL,NULL),(155,'2026-03-26',NULL,NULL,16949.95,'cash','paid',16950.00,0.00,0.00,0.00,NULL,'2026-03-29 07:33:24','2026-03-29 07:33:24',NULL,NULL,NULL),(156,'2026-03-27',NULL,NULL,9700.00,'upi','paid',9700.00,0.00,0.00,0.00,NULL,'2026-03-29 07:38:44','2026-03-29 07:38:44',NULL,NULL,NULL),(157,'2026-03-27',NULL,NULL,20100.00,'cash','paid',21100.00,0.00,0.00,0.00,NULL,'2026-03-29 07:39:58','2026-03-29 07:39:58',NULL,NULL,NULL),(158,'2026-03-28',NULL,NULL,6500.00,'upi','paid',6500.00,0.00,0.00,0.00,NULL,'2026-03-29 07:47:53','2026-03-29 07:47:53',NULL,NULL,NULL),(159,'2026-03-28',NULL,NULL,13370.00,'cash','paid',13370.00,0.00,0.00,0.00,NULL,'2026-03-29 07:49:45','2026-03-29 07:53:42',NULL,NULL,NULL),(160,'2026-03-28',NULL,NULL,210.00,'mix','paid',210.00,100.00,110.00,0.00,NULL,'2026-03-29 07:51:51','2026-03-29 07:54:03',NULL,NULL,NULL),(161,'2026-03-29',NULL,NULL,11950.00,'upi','paid',11950.00,0.00,0.00,0.00,'Prem Singh ₹3500/- for bill No.78 at 17 Jan 2026','2026-03-31 09:08:15','2026-03-31 09:08:15',NULL,NULL,NULL),(162,'2026-03-29',NULL,NULL,4250.00,'cash','paid',4250.00,0.00,0.00,0.00,NULL,'2026-03-31 09:08:52','2026-03-31 09:08:52',NULL,NULL,NULL),(163,'2026-03-30',NULL,NULL,15840.00,'cash','paid',15840.00,0.00,0.00,0.00,'250 marchu + 500 haldi +3 tin = ₹9600/-','2026-03-31 09:13:46','2026-03-31 09:13:46',NULL,NULL,NULL),(164,'2026-03-30',NULL,NULL,3200.00,'mix','paid',3200.00,3000.00,200.00,0.00,NULL,'2026-03-31 09:14:11','2026-03-31 09:14:11',NULL,NULL,NULL),(165,'2026-03-30',NULL,NULL,10560.00,'upi','paid',10560.00,0.00,0.00,0.00,NULL,'2026-03-31 09:18:18','2026-03-31 09:18:18',NULL,NULL,NULL),(166,'2026-03-31',NULL,NULL,9700.00,'upi','paid',9700.00,0.00,0.00,0.00,NULL,'2026-04-02 09:31:56','2026-04-02 09:31:56',NULL,NULL,NULL),(167,'2026-03-31',NULL,NULL,7000.00,'cash','paid',7000.00,0.00,0.00,0.00,NULL,'2026-04-02 09:32:48','2026-04-02 09:32:48',NULL,NULL,NULL),(168,'2026-04-01',NULL,NULL,6250.00,'upi','paid',6250.00,0.00,0.00,0.00,NULL,'2026-04-07 08:47:51','2026-04-07 08:47:51',NULL,NULL,NULL),(169,'2026-04-01',NULL,NULL,10419.99,'cash','paid',10420.00,0.00,0.00,0.00,NULL,'2026-04-07 08:49:07','2026-04-07 08:49:15',NULL,NULL,NULL),(170,'2026-04-02',NULL,NULL,6250.00,'upi','paid',6250.00,0.00,0.00,0.00,NULL,'2026-04-07 08:59:47','2026-04-07 08:59:47',NULL,NULL,NULL),(171,'2026-04-02',NULL,NULL,9450.00,'cash','paid',9450.00,0.00,0.00,0.00,'6300/- vadaj wala nu bill No-20 (21/3/2026)','2026-04-07 09:00:12','2026-04-11 08:42:34',NULL,NULL,NULL),(172,'2026-04-03',NULL,NULL,22700.00,'upi','paid',22700.00,0.00,0.00,0.00,NULL,'2026-04-07 09:04:28','2026-04-07 09:04:28',NULL,NULL,NULL),(173,'2026-04-03',NULL,NULL,1080.00,'cash','paid',1080.00,0.00,0.00,0.00,NULL,'2026-04-07 09:04:49','2026-04-07 09:04:49',NULL,NULL,NULL),(174,'2026-04-04',NULL,NULL,20400.00,'upi','paid',20400.00,0.00,0.00,0.00,NULL,'2026-04-07 09:06:55','2026-04-07 09:06:55',NULL,NULL,NULL),(175,'2026-04-04','Nitinbhai',NULL,3200.00,'upi','partial',1600.00,0.00,0.00,1600.00,'bill no-35\r\n1600/- 4/4/25\r\n500/- 10/4/25\r\n900/- 21/4/25','2026-04-07 09:07:35','2026-04-23 08:57:42',NULL,NULL,NULL),(176,'2026-04-04',NULL,NULL,13340.00,'cash','paid',13340.00,0.00,0.00,0.00,NULL,'2026-04-07 09:09:17','2026-04-07 09:09:17',NULL,NULL,NULL),(177,'2026-04-05',NULL,NULL,4350.00,'upi','paid',4350.00,0.00,0.00,0.00,NULL,'2026-04-07 09:10:04','2026-04-07 09:10:04',NULL,NULL,NULL),(178,'2026-04-05',NULL,NULL,3300.00,'cash','paid',3300.00,0.00,0.00,0.00,NULL,'2026-04-07 09:10:20','2026-04-07 09:10:20',NULL,NULL,NULL),(179,'2026-04-06',NULL,NULL,6600.00,'upi','paid',6600.00,0.00,0.00,0.00,NULL,'2026-04-07 09:10:44','2026-04-07 09:10:44',NULL,NULL,NULL),(180,'2026-04-06',NULL,NULL,4100.00,'cash','paid',4100.00,0.00,0.00,0.00,'3100/- bill No-8 Ranjanben Patil  (11/3/2026)','2026-04-07 09:12:10','2026-04-07 09:12:10',NULL,NULL,NULL),(181,'2026-04-06','Ranjanben Patil',NULL,10500.00,'cash','partial',6000.00,0.00,0.00,4500.00,'Delivery Pending','2026-04-07 09:13:00','2026-04-07 09:13:00',NULL,NULL,NULL),(182,'2026-04-07',NULL,NULL,4350.00,'cash','paid',4350.00,0.00,0.00,0.00,NULL,'2026-04-11 08:24:39','2026-04-11 08:24:39',NULL,NULL,NULL),(183,'2026-04-08',NULL,NULL,3300.00,'upi','paid',3300.00,0.00,0.00,0.00,NULL,'2026-04-11 08:25:13','2026-04-11 08:25:13',NULL,NULL,NULL),(184,'2026-04-08',NULL,NULL,3720.00,'cash','paid',3720.00,0.00,0.00,0.00,NULL,'2026-04-11 08:26:36','2026-04-11 08:26:36',NULL,NULL,NULL),(185,'2026-04-08','Kishorbhai',NULL,7560.00,'cash','partial',3500.00,0.00,0.00,4060.00,'Bill No: 39','2026-04-11 08:30:32','2026-04-11 08:30:32',NULL,NULL,NULL),(186,'2026-04-09',NULL,NULL,3870.00,'cash','paid',3870.00,0.00,0.00,0.00,NULL,'2026-04-11 08:33:43','2026-04-11 08:33:43',NULL,NULL,NULL),(187,'2026-04-10',NULL,NULL,12550.00,'cash','paid',12550.00,0.00,0.00,0.00,NULL,'2026-04-11 08:35:10','2026-04-11 08:35:10',NULL,NULL,NULL),(188,'2026-04-11',NULL,NULL,10750.00,'cash','paid',10750.00,0.00,0.00,0.00,'11000/- cash with included 250 auto rixa charge','2026-04-18 06:13:11','2026-04-18 06:13:11',NULL,NULL,NULL),(189,'2026-04-12',NULL,NULL,5350.00,'upi','paid',5350.00,0.00,0.00,0.00,NULL,'2026-04-18 06:16:14','2026-04-18 06:16:14',NULL,NULL,NULL),(190,'2026-04-12',NULL,NULL,7020.00,'cash','paid',7020.00,0.00,0.00,0.00,NULL,'2026-04-18 06:18:03','2026-04-18 06:18:03',NULL,NULL,NULL),(191,'2026-04-13',NULL,NULL,14250.00,'cash','paid',14250.00,0.00,0.00,0.00,NULL,'2026-04-18 06:20:21','2026-04-18 06:20:21',NULL,NULL,NULL),(192,'2026-04-13',NULL,NULL,3610.00,'upi','paid',3610.00,0.00,0.00,0.00,NULL,'2026-04-18 06:21:27','2026-04-18 06:21:27',NULL,NULL,NULL),(193,'2026-04-14',NULL,NULL,210.00,'upi','paid',210.00,0.00,0.00,0.00,NULL,'2026-04-18 06:24:05','2026-04-18 06:24:05',NULL,NULL,NULL),(194,'2026-04-14',NULL,NULL,5560.00,'cash','paid',5560.00,0.00,0.00,0.00,'210/- aagal na diwse baki hta','2026-04-18 06:26:14','2026-04-18 06:26:14',NULL,NULL,NULL),(195,'2026-04-15',NULL,NULL,2100.00,'upi','paid',2100.00,0.00,0.00,0.00,NULL,'2026-04-18 06:27:55','2026-04-18 06:27:55',NULL,NULL,NULL),(196,'2026-04-15',NULL,NULL,9740.00,'cash','paid',9740.00,0.00,0.00,0.00,NULL,'2026-04-18 06:30:02','2026-04-18 06:30:02',NULL,NULL,NULL),(197,'2026-04-16',NULL,NULL,3150.00,'cash','paid',3150.00,0.00,0.00,0.00,NULL,'2026-04-18 06:30:45','2026-04-18 06:30:45',NULL,NULL,NULL),(198,'2026-04-17',NULL,NULL,2450.00,'upi','paid',2450.00,0.00,0.00,0.00,NULL,'2026-04-21 09:06:44','2026-04-21 09:06:44',NULL,NULL,NULL),(199,'2026-04-17',NULL,NULL,1000.00,NULL,'pending',0.00,0.00,0.00,1000.00,NULL,'2026-04-21 09:06:57','2026-05-02 05:20:04',NULL,NULL,NULL),(200,'2026-04-18',NULL,NULL,3300.00,'upi','paid',3300.00,0.00,0.00,0.00,NULL,'2026-04-21 09:07:22','2026-04-21 09:07:22',NULL,NULL,NULL),(201,'2026-04-18',NULL,NULL,4770.00,'cash','paid',4770.00,0.00,0.00,0.00,NULL,'2026-04-21 09:08:07','2026-04-21 09:08:07',NULL,NULL,NULL),(202,'2026-04-18','Ronak',NULL,2100.00,NULL,'pending',0.00,0.00,0.00,2100.00,'Bill No-43\r\n1 ken Ghare delivery karelu 6e\r\n1 ken Dukan thi lai gya 6e','2026-04-21 09:09:15','2026-05-02 05:19:52',NULL,NULL,NULL),(203,'2026-04-19',NULL,NULL,560.00,'upi','paid',560.00,0.00,0.00,0.00,NULL,'2026-04-21 09:10:04','2026-04-21 09:10:04',NULL,NULL,NULL),(204,'2026-04-20',NULL,NULL,11770.00,'upi','paid',11770.00,0.00,0.00,0.00,NULL,'2026-04-21 09:11:49','2026-04-21 09:11:49',NULL,NULL,NULL),(205,'2026-04-21',NULL,NULL,6250.00,'upi','paid',6250.00,0.00,0.00,0.00,NULL,'2026-04-23 08:53:23','2026-04-23 08:53:23',NULL,NULL,NULL),(206,'2026-04-21',NULL,NULL,7960.00,'cash','paid',7960.00,0.00,0.00,0.00,NULL,'2026-04-23 08:55:27','2026-04-23 08:58:28',NULL,NULL,NULL),(207,'2026-04-22',NULL,NULL,3250.00,'upi','paid',3250.00,0.00,0.00,0.00,NULL,'2026-04-23 08:59:02','2026-04-23 08:59:02',NULL,NULL,NULL),(208,'2026-04-22',NULL,NULL,4250.00,'cash','paid',4250.00,0.00,0.00,0.00,NULL,'2026-04-23 08:59:36','2026-04-23 08:59:36',NULL,NULL,NULL),(209,'2026-04-23',NULL,NULL,6500.00,'upi','paid',6500.00,0.00,0.00,0.00,NULL,'2026-05-02 00:38:16','2026-05-02 00:38:16',NULL,NULL,NULL),(210,'2026-04-23',NULL,NULL,3300.00,'cash','paid',3300.00,0.00,0.00,0.00,NULL,'2026-05-02 00:38:57','2026-05-02 00:38:57',NULL,NULL,NULL),(211,'2026-04-24',NULL,NULL,1050.00,'cash','paid',1050.00,0.00,0.00,0.00,NULL,'2026-05-02 00:39:23','2026-05-02 00:39:23',NULL,NULL,NULL),(212,'2026-04-24',NULL,NULL,3510.00,'upi','paid',3510.00,0.00,0.00,0.00,NULL,'2026-05-02 00:39:55','2026-05-02 00:40:25',NULL,NULL,NULL),(213,'2026-04-26',NULL,NULL,8550.00,'cash','paid',8550.00,0.00,0.00,0.00,NULL,'2026-05-02 00:41:45','2026-05-02 00:41:45',NULL,NULL,NULL),(214,'2026-04-26',NULL,NULL,1050.00,'upi','paid',1050.00,0.00,0.00,0.00,NULL,'2026-05-02 00:42:19','2026-05-02 00:42:19',NULL,NULL,NULL),(215,'2026-04-27',NULL,NULL,4350.00,'upi','paid',4350.00,0.00,0.00,0.00,NULL,'2026-05-02 00:44:07','2026-05-02 00:44:07',NULL,NULL,NULL),(216,'2026-04-27',NULL,NULL,1050.00,'cash','paid',1050.00,0.00,0.00,0.00,'+100/- marchu 250 gram','2026-05-02 00:45:20','2026-05-02 00:45:20',NULL,NULL,NULL),(217,'2026-04-28',NULL,NULL,9860.00,'upi','paid',9860.00,0.00,0.00,0.00,NULL,'2026-05-02 00:46:17','2026-05-02 00:46:17',NULL,NULL,NULL),(218,'2026-04-28',NULL,NULL,3300.00,'cash','paid',3300.00,0.00,0.00,0.00,NULL,'2026-05-02 00:46:40','2026-05-02 00:46:40',NULL,NULL,NULL),(219,'2026-04-29',NULL,NULL,1260.00,'cash','paid',1260.00,0.00,0.00,0.00,NULL,'2026-05-02 00:47:59','2026-05-02 00:47:59',NULL,NULL,NULL),(220,'2026-04-30',NULL,NULL,13940.00,'upi','paid',13940.00,0.00,0.00,0.00,NULL,'2026-05-02 00:48:54','2026-05-02 00:48:54',NULL,NULL,NULL),(221,'2026-04-30',NULL,NULL,2100.00,'cash','paid',2100.00,0.00,0.00,0.00,NULL,'2026-05-02 00:49:31','2026-05-02 00:49:31',NULL,NULL,NULL),(222,'2026-04-20','BAPS bapunagar',NULL,6200.00,'cash','paid',0.00,0.00,0.00,0.00,NULL,'2026-05-02 01:07:32','2026-05-02 03:11:25',NULL,NULL,NULL),(223,'2026-04-24','BAPS bapunagar',NULL,6200.00,'cash','paid',0.00,0.00,0.00,0.00,NULL,'2026-05-02 01:09:26','2026-05-02 03:11:48',NULL,NULL,NULL),(224,'2026-04-24','Akshit Bhano',NULL,3200.00,NULL,'pending',0.00,0.00,0.00,3200.00,'Bill No-48','2026-05-02 05:10:57','2026-05-02 05:10:57',NULL,NULL,NULL),(225,'2026-03-13','Rohit Bhano',NULL,4020.00,NULL,'pending',0.00,0.00,0.00,4020.00,'Bill No-14','2026-05-02 05:14:07','2026-05-02 05:14:07',NULL,NULL,NULL),(226,'2026-03-30','Naresh Ramani',NULL,3200.00,NULL,'pending',0.00,0.00,0.00,3200.00,'Bill No-28','2026-05-02 05:14:50','2026-05-02 05:14:50',NULL,NULL,NULL),(227,'2026-04-02','Abhishek Desai',NULL,3200.00,NULL,'pending',0.00,0.00,0.00,3200.00,'Bill No-32','2026-05-02 05:15:22','2026-05-02 05:15:22',NULL,NULL,NULL),(228,'2026-04-06','Ranjanben Patil',NULL,12550.00,NULL,'pending',0.00,0.00,0.00,12550.00,'Bill No-38','2026-05-02 05:17:40','2026-05-02 05:18:51',NULL,NULL,NULL),(229,'2026-04-08','Kishorbhai',NULL,8260.00,NULL,'pending',0.00,0.00,0.00,8260.00,'Bill No-39','2026-05-02 05:18:26','2026-05-02 05:18:26',NULL,NULL,NULL);
/*!40000 ALTER TABLE `sells` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stock_closing_items`
--

DROP TABLE IF EXISTS `stock_closing_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stock_closing_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `stock_closing_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `opening_stock` decimal(12,2) NOT NULL DEFAULT 0.00,
  `purchased_qty` decimal(12,2) NOT NULL DEFAULT 0.00,
  `purchase_returns_qty` decimal(12,2) NOT NULL DEFAULT 0.00,
  `sold_qty` decimal(12,2) NOT NULL DEFAULT 0.00,
  `sell_returns_qty` decimal(12,2) NOT NULL DEFAULT 0.00,
  `expected_stock` decimal(12,2) NOT NULL DEFAULT 0.00,
  `actual_stock` decimal(12,2) NOT NULL DEFAULT 0.00,
  `difference` decimal(12,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `stock_closing_items_stock_closing_id_foreign` (`stock_closing_id`),
  KEY `stock_closing_items_product_id_foreign` (`product_id`),
  CONSTRAINT `stock_closing_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `stock_closing_items_stock_closing_id_foreign` FOREIGN KEY (`stock_closing_id`) REFERENCES `stock_closings` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock_closing_items`
--

LOCK TABLES `stock_closing_items` WRITE;
/*!40000 ALTER TABLE `stock_closing_items` DISABLE KEYS */;
INSERT INTO `stock_closing_items` VALUES (1,1,5,0.00,0.00,0.00,0.00,0.00,0.00,21.00,0.00,'2026-05-02 06:01:08','2026-05-02 06:01:08'),(2,1,4,0.00,0.00,0.00,0.00,0.00,0.00,28.00,0.00,'2026-05-02 06:01:08','2026-05-02 06:01:08'),(3,1,1,0.00,0.00,0.00,0.00,0.00,0.00,34.00,0.00,'2026-05-02 06:01:08','2026-05-02 06:01:08'),(4,1,10,0.00,0.00,0.00,0.00,0.00,0.00,17.00,0.00,'2026-05-02 06:01:08','2026-05-02 06:01:08'),(5,1,8,0.00,0.00,0.00,0.00,0.00,0.00,4.00,0.00,'2026-05-02 06:01:08','2026-05-02 06:01:08'),(6,1,13,0.00,0.00,0.00,0.00,0.00,0.00,1.00,0.00,'2026-05-02 06:01:08','2026-05-02 06:01:08'),(7,1,7,0.00,0.00,0.00,0.00,0.00,0.00,5.00,0.00,'2026-05-02 06:01:08','2026-05-02 06:01:08'),(8,1,9,0.00,0.00,0.00,0.00,0.00,0.00,1.00,0.00,'2026-05-02 06:01:08','2026-05-02 06:01:08'),(9,1,2,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,'2026-05-02 06:01:09','2026-05-02 06:01:09'),(10,1,3,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,'2026-05-02 06:01:09','2026-05-02 06:01:09'),(11,1,6,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,'2026-05-02 06:01:09','2026-05-02 06:01:09'),(12,1,11,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,'2026-05-02 06:01:09','2026-05-02 06:01:09'),(13,1,12,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,'2026-05-02 06:01:09','2026-05-02 06:01:09');
/*!40000 ALTER TABLE `stock_closing_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stock_closings`
--

DROP TABLE IF EXISTS `stock_closings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stock_closings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `closing_date` date NOT NULL,
  `period_label` varchar(20) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock_closings`
--

LOCK TABLES `stock_closings` WRITE;
/*!40000 ALTER TABLE `stock_closings` DISABLE KEYS */;
INSERT INTO `stock_closings` VALUES (1,'2026-04-30','2026-04',NULL,'2026-05-02 06:01:08','2026-05-02 06:01:08');
/*!40000 ALTER TABLE `stock_closings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stocks`
--

DROP TABLE IF EXISTS `stocks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stocks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_name` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `unit_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` enum('in_stock','low_stock','out_of_stock') NOT NULL DEFAULT 'in_stock',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stocks`
--

LOCK TABLES `stocks` WRITE;
/*!40000 ALTER TABLE `stocks` DISABLE KEYS */;
/*!40000 ALTER TABLE `stocks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `contact_no` bigint(20) DEFAULT NULL,
  `address` longtext DEFAULT NULL,
  `role_id` int(11) NOT NULL,
  `dob` date DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Admin','User','admin@parampara.com',NULL,NULL,1,NULL,'2026-01-11 05:16:14',1,'$2y$12$D8naByq268tzcwmLOmdo3uhACElcuqM9saItiqYOUcGA6FEcqDP3y','RuzJW2MCI6FN4GLzhqS1TAC5mQMRcGU8C4BOdkqlcTmroosw8QJaQU4UClzY','2026-01-11 05:16:14','2026-01-11 05:16:14',NULL),(2,'Test','User','test@parampara.com',NULL,NULL,1,NULL,'2026-01-11 05:16:14',1,'$2y$10$pjv0jEjqRGs4RLY1rVZ3y.qLDilUO5EphdUw9dxCj5IgzF6CGfspm',NULL,'2026-01-11 05:16:14','2026-01-11 05:16:14',NULL);
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

-- Dump completed on 2026-05-02 19:51:01
