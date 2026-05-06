-- MySQL dump 10.13  Distrib 8.4.3, for Win64 (x86_64)
--
-- Host: localhost    Database: laravel-invetory
-- ------------------------------------------------------
-- Server version	8.4.3

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `activity_logs`
--

DROP TABLE IF EXISTS `activity_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `activity_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `action` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `item_id` bigint unsigned DEFAULT NULL,
  `user_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `activity_logs_item_id_foreign` (`item_id`),
  KEY `activity_logs_user_id_foreign` (`user_id`),
  CONSTRAINT `activity_logs_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `activity_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=169 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_logs`
--

LOCK TABLES `activity_logs` WRITE;
/*!40000 ALTER TABLE `activity_logs` DISABLE KEYS */;
INSERT INTO `activity_logs` VALUES (1,'dispatched','Dispatched to event: GALA AT STATE HOUSE. Condition: Good.','2026-04-08 23:10:37','2026-04-08 23:10:37',131,1),(2,'dispatched','Dispatched to event: GALA AT STATE HOUSE. Condition: Good.','2026-04-08 23:10:37','2026-04-08 23:10:37',133,1),(3,'dispatched','Dispatched to event: GALA AT STATE HOUSE. Condition: Excellent.','2026-04-08 23:10:37','2026-04-08 23:10:37',139,1),(4,'dispatched','Dispatched to event: GALA AT STATE HOUSE. Condition: Fair.','2026-04-08 23:10:37','2026-04-08 23:10:37',140,1),(5,'dispatched','Dispatched to event: GALA AT STATE HOUSE. Condition: Fair.','2026-04-08 23:10:38','2026-04-08 23:10:38',141,1),(6,'dispatched','Dispatched to event: GALA AT STATE HOUSE. Condition: Good.','2026-04-08 23:10:38','2026-04-08 23:10:38',152,1),(7,'dispatched','Dispatched to event: WRC SAFARI RALLY NAIVASHA. Condition: Good.','2026-04-08 23:55:06','2026-04-08 23:55:06',126,1),(8,'dispatched','Dispatched to event: WRC SAFARI RALLY NAIVASHA. Condition: Excellent.','2026-04-08 23:55:06','2026-04-08 23:55:06',135,1),(9,'dispatched','Dispatched to event: WRC SAFARI RALLY NAIVASHA. Condition: Fair.','2026-04-08 23:55:06','2026-04-08 23:55:06',144,1),(10,'dispatched','Dispatched to event: WRC SAFARI RALLY NAIVASHA. Condition: Fair.','2026-04-08 23:55:06','2026-04-08 23:55:06',145,1),(11,'image_updated','Primary image changed.','2026-04-11 02:56:27','2026-04-11 02:56:27',135,1),(12,'image_updated','Primary image changed.','2026-04-11 02:56:29','2026-04-11 02:56:29',135,1),(13,'dispatched','Dispatched to event: OKTOBER FEST. Condition: Good.','2026-04-11 08:46:58','2026-04-11 08:46:58',139,1),(14,'dispatched','Dispatched to event: OKTOBER FEST. Condition: Average.','2026-04-11 08:46:58','2026-04-11 08:46:58',141,1),(15,'dispatched','Dispatched to event: OKTOBER FEST. Condition: Good.','2026-04-11 08:46:58','2026-04-11 08:46:58',149,1),(16,'dispatched','Dispatched to event: OKTOBER FEST. Condition: Good.','2026-04-11 08:46:59','2026-04-11 08:46:59',134,1),(17,'dispatched','Dispatched to event: OKTOBER FEST. Condition: Good.','2026-04-11 08:46:59','2026-04-11 08:46:59',143,1),(18,'dispatched','Dispatched to event: OKTOBER FEST. Condition: Average.','2026-04-11 08:46:59','2026-04-11 08:46:59',142,1),(19,'dispatched','Dispatched to event: OKTOBER FEST. Condition: Fair.','2026-04-11 08:46:59','2026-04-11 08:46:59',146,1),(20,'dispatched','Dispatched to event: OKTOBER FEST. Condition: Good.','2026-04-11 08:46:59','2026-04-11 08:46:59',148,1),(21,'dispatched','Dispatched to event: OKTOBER FEST. Condition: Good.','2026-04-11 08:46:59','2026-04-11 08:46:59',132,1),(22,'dispatched','Dispatched to event: OKTOBER FEST. Condition: Excellent.','2026-04-11 08:46:59','2026-04-11 08:46:59',147,1),(23,'dispatched','Dispatched to event: OKTOBER FEST. Condition: Excellent.','2026-04-11 08:47:00','2026-04-11 08:47:00',136,1),(24,'dispatched','Dispatched to event: OKTOBER FEST. Condition: Good.','2026-04-11 08:47:00','2026-04-11 08:47:00',151,1),(25,'dispatched','Dispatched to event: OKTOBER FEST. Condition: Good.','2026-04-11 08:47:00','2026-04-11 08:47:00',150,1),(26,'dispatched','Dispatched to event: OKTOBER FEST. Condition: Excellent.','2026-04-11 08:47:00','2026-04-11 08:47:00',137,1),(27,'dispatched','Dispatched to event: OKTOBER FEST. Condition: Good.','2026-04-11 08:47:00','2026-04-11 08:47:00',127,1),(28,'dispatched','Dispatched to event: OKTOBER FEST. Condition: Good.','2026-04-11 08:47:00','2026-04-11 08:47:00',129,1),(29,'dispatched','Dispatched to event: OKTOBER FEST. Condition: Excellent.','2026-04-11 08:47:00','2026-04-11 08:47:00',133,1),(30,'dispatched','Dispatched to event: OKTOBER FEST. Condition: Good.','2026-04-11 08:47:00','2026-04-11 08:47:00',131,1),(31,'dispatched','Dispatched to event: OKTOBER FEST. Condition: Good.','2026-04-11 08:47:01','2026-04-11 08:47:01',140,1),(32,'dispatched','Dispatched to event: OKTOBER FEST. Condition: Good.','2026-04-11 08:47:01','2026-04-11 08:47:01',125,1),(33,'dispatched','Dispatched to event: OKTOBER FEST. Condition: Excellent.','2026-04-11 08:47:01','2026-04-11 08:47:01',138,1),(34,'dispatched','Dispatched to event: OKTOBER FEST. Condition: Good.','2026-04-11 08:47:01','2026-04-11 08:47:01',130,1),(35,'dispatched','Dispatched to event: OKTOBER FEST. Condition: Good.','2026-04-11 08:47:01','2026-04-11 08:47:01',128,1),(36,'dispatched','Dispatched to event: OKTOBER FEST. Condition: Good.','2026-04-11 08:47:01','2026-04-11 08:47:01',154,1),(37,'dispatched','Dispatched to event: OKTOBER FEST. Condition: Good.','2026-04-11 08:47:01','2026-04-11 08:47:01',155,1),(38,'returned','Returned from event: OKTOBER FEST. Destination: Available. Condition: 4/5.','2026-04-11 09:48:21','2026-04-11 09:48:21',139,1),(39,'returned','Returned from event: OKTOBER FEST. Destination: Available. Condition: 5/5.','2026-04-11 09:48:22','2026-04-11 09:48:22',141,1),(40,'returned','Returned from event: OKTOBER FEST. Destination: Cleaning. Condition: 4/5.','2026-04-11 09:48:22','2026-04-11 09:48:22',149,1),(41,'returned','Returned from event: OKTOBER FEST. Destination: Available. Condition: 4/5.','2026-04-11 09:48:22','2026-04-11 09:48:22',134,1),(42,'returned','Returned from event: OKTOBER FEST. Destination: Available. Condition: 4/5.','2026-04-11 09:48:22','2026-04-11 09:48:22',143,1),(43,'returned','Returned from event: OKTOBER FEST. Destination: Available. Condition: 4/5.','2026-04-11 09:48:22','2026-04-11 09:48:22',142,1),(44,'returned','Returned from event: OKTOBER FEST. Destination: Available. Condition: 4/5.','2026-04-11 09:48:22','2026-04-11 09:48:22',146,1),(45,'returned','Returned from event: OKTOBER FEST. Destination: Available. Condition: 4/5.','2026-04-11 09:48:22','2026-04-11 09:48:22',148,1),(46,'returned','Returned from event: OKTOBER FEST. Destination: Available. Condition: 4/5.','2026-04-11 09:48:23','2026-04-11 09:48:23',132,1),(47,'returned','Returned from event: OKTOBER FEST. Destination: Available. Condition: 4/5.','2026-04-11 09:48:23','2026-04-11 09:48:23',147,1),(48,'returned','Returned from event: OKTOBER FEST. Destination: Available. Condition: 4/5.','2026-04-11 09:48:23','2026-04-11 09:48:23',136,1),(49,'returned','Returned from event: OKTOBER FEST. Destination: Available. Condition: 4/5.','2026-04-11 09:48:23','2026-04-11 09:48:23',151,1),(50,'returned','Returned from event: OKTOBER FEST. Destination: Available. Condition: 4/5.','2026-04-11 09:48:23','2026-04-11 09:48:23',150,1),(51,'returned','Returned from event: OKTOBER FEST. Destination: Available. Condition: 4/5.','2026-04-11 09:48:23','2026-04-11 09:48:23',137,1),(52,'returned','Returned from event: OKTOBER FEST. Destination: Available. Condition: 4/5.','2026-04-11 09:48:23','2026-04-11 09:48:23',127,1),(53,'returned','Returned from event: OKTOBER FEST. Destination: Available. Condition: 4/5.','2026-04-11 09:48:23','2026-04-11 09:48:23',129,1),(54,'returned','Returned from event: OKTOBER FEST. Destination: Available. Condition: 4/5.','2026-04-11 09:48:24','2026-04-11 09:48:24',133,1),(55,'returned','Returned from event: OKTOBER FEST. Destination: Available. Condition: 4/5.','2026-04-11 09:48:24','2026-04-11 09:48:24',131,1),(56,'returned','Returned from event: OKTOBER FEST. Destination: Available. Condition: 4/5.','2026-04-11 09:48:24','2026-04-11 09:48:24',140,1),(57,'returned','Returned from event: OKTOBER FEST. Destination: Available. Condition: 4/5.','2026-04-11 09:48:24','2026-04-11 09:48:24',125,1),(58,'returned','Returned from event: OKTOBER FEST. Destination: Available. Condition: 4/5.','2026-04-11 09:48:24','2026-04-11 09:48:24',138,1),(59,'returned','Returned from event: OKTOBER FEST. Destination: Available. Condition: 4/5.','2026-04-11 09:48:24','2026-04-11 09:48:24',130,1),(60,'returned','Returned from event: OKTOBER FEST. Destination: Available. Condition: 4/5.','2026-04-11 09:48:24','2026-04-11 09:48:24',128,1),(61,'returned','Returned from event: OKTOBER FEST. Destination: Cleaning. Condition: 3/5.','2026-04-11 09:48:25','2026-04-11 09:48:25',154,1),(62,'returned','Returned from event: OKTOBER FEST. Destination: Cleaning. Condition: 3/5.','2026-04-11 09:48:25','2026-04-11 09:48:25',155,1),(63,'dispatched','Dispatched to event: WEDDING GALA. Condition: Good.','2026-04-11 10:07:18','2026-04-11 10:07:18',139,1),(64,'dispatched','Dispatched to event: WEDDING GALA. Condition: Good.','2026-04-11 10:07:18','2026-04-11 10:07:18',141,1),(65,'dispatched','Dispatched to event: WEDDING GALA. Condition: Good.','2026-04-11 10:07:18','2026-04-11 10:07:18',161,1),(66,'dispatched','Dispatched to event: WEDDING GALA. Condition: Good.','2026-04-11 10:07:18','2026-04-11 10:07:18',163,1),(67,'dispatched','Dispatched to event: Showman Residency. Condition: Good.','2026-04-11 10:21:53','2026-04-11 10:21:53',125,1),(68,'dispatched','Dispatched to event: Showman Residency. Condition: Good.','2026-04-11 10:21:53','2026-04-11 10:21:53',134,1),(69,'dispatched','Dispatched to event: Showman Residency. Condition: Good.','2026-04-11 10:21:53','2026-04-11 10:21:53',142,1),(70,'dispatched','Dispatched to event: Showman Residency. Condition: Good.','2026-04-11 10:21:53','2026-04-11 10:21:53',159,1),(71,'dispatched','Dispatched to event: Showman Residency. Condition: Good.','2026-04-11 10:21:53','2026-04-11 10:21:53',162,1),(72,'returned','Returned from event: Showman Residency. Destination: Available. Condition: 4/5.','2026-04-11 23:08:15','2026-04-11 23:08:15',125,1),(73,'returned','Returned from event: Showman Residency. Destination: Cleaning. Condition: 5/5.','2026-04-11 23:08:15','2026-04-11 23:08:15',134,1),(74,'returned','Returned from event: Showman Residency. Destination: Available. Condition: 4/5.','2026-04-11 23:08:15','2026-04-11 23:08:15',142,1),(75,'returned','Returned from event: Showman Residency. Destination: Cleaning. Condition: 4/5.','2026-04-11 23:08:15','2026-04-11 23:08:15',159,1),(76,'returned','Returned from event: Showman Residency. Destination: Cleaning. Condition: 5/5.','2026-04-11 23:08:15','2026-04-11 23:08:15',162,1),(77,'cleaned','Item marked as cleaned and moved back to Warehouse.','2026-04-11 23:23:45','2026-04-11 23:23:45',134,1),(78,'cleaned','Item marked as cleaned and moved back to Warehouse.','2026-04-11 23:23:49','2026-04-11 23:23:49',159,1),(79,'cleaned','Item marked as cleaned and moved back to Warehouse.','2026-04-11 23:23:51','2026-04-11 23:23:51',162,1),(80,'cleaned','Item marked as cleaned and moved back to Warehouse.','2026-04-11 23:23:53','2026-04-11 23:23:53',155,1),(81,'cleaned','Item marked as cleaned and moved back to Warehouse.','2026-04-11 23:23:55','2026-04-11 23:23:55',154,1),(82,'cleaned','Item marked as cleaned and moved back to Warehouse.','2026-04-11 23:23:58','2026-04-11 23:23:58',149,1),(83,'created','Item created by Admin User','2026-04-11 23:39:11','2026-04-11 23:39:11',177,1),(84,'returned','Returned from event: WEDDING GALA. Destination: Available. Condition: 5/5.','2026-04-11 23:45:03','2026-04-11 23:45:03',139,1),(85,'returned','Returned from event: WEDDING GALA. Destination: Available. Condition: 4/5.','2026-04-11 23:45:03','2026-04-11 23:45:03',141,1),(86,'returned','Returned from event: WEDDING GALA. Destination: Available. Condition: 4/5.','2026-04-11 23:45:03','2026-04-11 23:45:03',161,1),(87,'returned','Returned from event: WEDDING GALA. Destination: Cleaning. Condition: 5/5.','2026-04-11 23:45:04','2026-04-11 23:45:04',163,1),(88,'returned','Returned from event: Showman Residency. Destination: Available. Condition: 4/5.','2026-04-11 23:57:53','2026-04-11 23:57:53',125,1),(89,'returned','Returned from event: Showman Residency. Destination: Available. Condition: 4/5.','2026-04-11 23:57:53','2026-04-11 23:57:53',134,1),(90,'returned','Returned from event: Showman Residency. Destination: Cleaning. Condition: 4/5.','2026-04-11 23:57:53','2026-04-11 23:57:53',142,1),(91,'returned','Returned from event: Showman Residency. Destination: Cleaning. Condition: 4/5.','2026-04-11 23:57:53','2026-04-11 23:57:53',159,1),(92,'returned','Returned from event: Showman Residency. Destination: Cleaning. Condition: 4/5.','2026-04-11 23:57:53','2026-04-11 23:57:53',162,1),(93,'returned','Returned from event: Showman Residency. Destination: Available. Condition: 4/5.','2026-04-11 23:58:48','2026-04-11 23:58:48',125,1),(94,'returned','Returned from event: Showman Residency. Destination: Cleaning. Condition: 4/5.','2026-04-11 23:58:48','2026-04-11 23:58:48',134,1),(95,'returned','Returned from event: Showman Residency. Destination: Available. Condition: 4/5.','2026-04-11 23:58:48','2026-04-11 23:58:48',142,1),(96,'returned','Returned from event: Showman Residency. Destination: Cleaning. Condition: 4/5.','2026-04-11 23:58:48','2026-04-11 23:58:48',159,1),(97,'returned','Returned from event: Showman Residency. Destination: Cleaning. Condition: 4/5.','2026-04-11 23:58:48','2026-04-11 23:58:48',162,1),(98,'returned','Returned from event: Showman Residency. Destination: Available. Condition: 4/5.','2026-04-12 00:00:16','2026-04-12 00:00:16',125,1),(99,'returned','Returned from event: Showman Residency. Destination: Available. Condition: 4/5.','2026-04-12 00:00:16','2026-04-12 00:00:16',134,1),(100,'returned','Returned from event: Showman Residency. Destination: Cleaning. Condition: 4/5.','2026-04-12 00:00:16','2026-04-12 00:00:16',142,1),(101,'returned','Returned from event: Showman Residency. Destination: Cleaning. Condition: 4/5.','2026-04-12 00:00:16','2026-04-12 00:00:16',159,1),(102,'returned','Returned from event: Showman Residency. Destination: Cleaning. Condition: 4/5.','2026-04-12 00:00:17','2026-04-12 00:00:17',162,1),(103,'dispatched','Dispatched to event: LABOUR DAY. Condition: Good.','2026-04-12 00:02:10','2026-04-12 00:02:10',59,1),(104,'dispatched','Dispatched to event: LABOUR DAY. Condition: Fair.','2026-04-12 00:02:10','2026-04-12 00:02:10',127,1),(105,'dispatched','Dispatched to event: LABOUR DAY. Condition: Good.','2026-04-12 00:02:11','2026-04-12 00:02:11',139,1),(106,'returned','Returned from event: LABOUR DAY. Destination: Available. Condition: 4/5.','2026-04-12 00:02:44','2026-04-12 00:02:44',59,1),(107,'returned','Returned from event: LABOUR DAY. Destination: Cleaning. Condition: 4/5.','2026-04-12 00:02:44','2026-04-12 00:02:44',127,1),(108,'returned','Returned from event: LABOUR DAY. Destination: Cleaning. Condition: 4/5.','2026-04-12 00:02:44','2026-04-12 00:02:44',139,1),(109,'returned','Returned from event: LABOUR DAY. Destination: Available. Condition: 4/5.','2026-04-12 00:07:28','2026-04-12 00:07:28',59,1),(110,'returned','Returned from event: LABOUR DAY. Destination: Available. Condition: 4/5.','2026-04-12 00:07:28','2026-04-12 00:07:28',127,1),(111,'returned','Returned from event: LABOUR DAY. Destination: Cleaning. Condition: 5/5.','2026-04-12 00:07:29','2026-04-12 00:07:29',139,1),(112,'returned','Returned from event: Showman Residency. Destination: Available. Condition: 4/5.','2026-04-12 00:18:44','2026-04-12 00:18:44',125,1),(113,'returned','Returned from event: Showman Residency. Destination: Available. Condition: 4/5.','2026-04-12 00:18:44','2026-04-12 00:18:44',134,1),(114,'returned','Returned from event: Showman Residency. Destination: Available. Condition: 4/5.','2026-04-12 00:18:44','2026-04-12 00:18:44',142,1),(115,'returned','Returned from event: Showman Residency. Destination: Cleaning. Condition: 4/5.','2026-04-12 00:18:44','2026-04-12 00:18:44',159,1),(116,'returned','Returned from event: Showman Residency. Destination: Cleaning. Condition: 4/5.','2026-04-12 00:18:44','2026-04-12 00:18:44',162,1),(117,'dispatched','Dispatched to event: SOUL FEST. Condition: Good.','2026-04-12 01:23:12','2026-04-12 01:23:12',51,1),(118,'dispatched','Dispatched to event: SOUL FEST. Condition: Good.','2026-04-12 01:23:13','2026-04-12 01:23:13',60,1),(119,'dispatched','Dispatched to event: SOUL FEST. Condition: Good.','2026-04-12 01:23:13','2026-04-12 01:23:13',67,1),(120,'dispatched','Dispatched to event: SOUL FEST. Condition: Good.','2026-04-12 01:23:13','2026-04-12 01:23:13',68,1),(121,'dispatched','Dispatched to event: SOUL FEST. Condition: Good.','2026-04-12 01:23:13','2026-04-12 01:23:13',71,1),(122,'dispatched','Dispatched to event: SOUL FEST. Condition: Good.','2026-04-12 01:23:13','2026-04-12 01:23:13',134,1),(123,'dispatched','Dispatched to event: SOUL FEST. Condition: Good.','2026-04-12 01:23:13','2026-04-12 01:23:13',141,1),(124,'dispatched','Dispatched to event: SOUL FEST. Condition: Good.','2026-04-12 01:23:14','2026-04-12 01:23:14',149,1),(125,'dispatched','Dispatched to event: SOUL FEST. Condition: Good.','2026-04-12 01:23:14','2026-04-12 01:23:14',152,1),(126,'dispatched','Dispatched to event: SOUL FEST. Condition: Good.','2026-04-12 01:23:14','2026-04-12 01:23:14',153,1),(127,'returned','Returned from event: SOUL FEST. Destination: Available. Condition: 4/5.','2026-04-12 01:24:20','2026-04-12 01:24:20',51,1),(128,'returned','Returned from event: SOUL FEST. Destination: Available. Condition: 4/5.','2026-04-12 01:24:20','2026-04-12 01:24:20',60,1),(129,'returned','Returned from event: SOUL FEST. Destination: Available. Condition: 4/5.','2026-04-12 01:24:20','2026-04-12 01:24:20',67,1),(130,'returned','Returned from event: SOUL FEST. Destination: Available. Condition: 4/5.','2026-04-12 01:24:20','2026-04-12 01:24:20',68,1),(131,'returned','Returned from event: SOUL FEST. Destination: Available. Condition: 4/5.','2026-04-12 01:24:20','2026-04-12 01:24:20',71,1),(132,'returned','Returned from event: SOUL FEST. Destination: Available. Condition: 4/5.','2026-04-12 01:24:20','2026-04-12 01:24:20',134,1),(133,'returned','Returned from event: SOUL FEST. Destination: Cleaning. Condition: 4/5.','2026-04-12 01:24:20','2026-04-12 01:24:20',141,1),(134,'returned','Returned from event: SOUL FEST. Destination: Under Repair. Condition: 1/5.','2026-04-12 01:24:21','2026-04-12 01:24:21',149,1),(135,'returned','Returned from event: SOUL FEST. Destination: Under Repair. Condition: 1/5.','2026-04-12 01:24:21','2026-04-12 01:24:21',152,1),(136,'returned','Returned from event: SOUL FEST. Destination: Cleaning. Condition: 4/5.','2026-04-12 01:24:21','2026-04-12 01:24:21',153,1),(137,'dispatched','Dispatched to event: NAIROBI FEST. Condition: Good.','2026-04-12 01:28:03','2026-04-12 01:28:03',78,1),(138,'dispatched','Dispatched to event: NAIROBI FEST. Condition: Good.','2026-04-12 01:28:03','2026-04-12 01:28:03',79,1),(139,'dispatched','Dispatched to event: NAIROBI FEST. Condition: Good.','2026-04-12 01:28:04','2026-04-12 01:28:04',111,1),(140,'dispatched','Dispatched to event: NAIROBI FEST. Condition: Good.','2026-04-12 01:28:04','2026-04-12 01:28:04',112,1),(141,'dispatched','Dispatched to event: NAIROBI FEST. Condition: Good.','2026-04-12 01:28:04','2026-04-12 01:28:04',134,1),(142,'dispatched','Dispatched to event: NAIROBI FEST. Condition: Good.','2026-04-12 01:28:04','2026-04-12 01:28:04',138,1),(143,'dispatched','Dispatched to event: NAIROBI FEST. Condition: Good.','2026-04-12 01:28:04','2026-04-12 01:28:04',143,1),(144,'dispatched','Dispatched to event: NAIROBI FEST. Condition: Good.','2026-04-12 01:28:04','2026-04-12 01:28:04',160,1),(145,'dispatched','Dispatched to event: NAIROBI FEST. Condition: Good.','2026-04-12 01:28:04','2026-04-12 01:28:04',171,1),(146,'cleaned','Item marked as cleaned and moved back to Warehouse.','2026-04-12 01:51:41','2026-04-12 01:51:41',141,1),(147,'cleaned','Item marked as cleaned and moved back to Warehouse.','2026-04-12 01:51:41','2026-04-12 01:51:41',153,1),(148,'dispatched','Dispatched to event: WRC NAIVASHA. Condition: Good.','2026-04-12 10:09:27','2026-04-12 10:09:27',117,1),(149,'dispatched','Dispatched to event: WRC NAIVASHA. Condition: Good.','2026-04-12 10:09:27','2026-04-12 10:09:27',120,1),(150,'dispatched','Dispatched to event: WRC NAIVASHA. Condition: Good.','2026-04-12 10:09:27','2026-04-12 10:09:27',142,1),(151,'dispatched','Dispatched to event: WRC NAIVASHA. Condition: Good.','2026-04-12 10:09:27','2026-04-12 10:09:27',146,1),(152,'dispatched','Dispatched to event: WRC NAIVASHA. Condition: Good.','2026-04-12 10:09:28','2026-04-12 10:09:28',154,1),(153,'returned','Returned from event: WRC NAIVASHA. Destination: Cleaning. Condition: 3/5.','2026-04-12 10:11:21','2026-04-12 10:11:21',117,1),(154,'returned','Returned from event: WRC NAIVASHA. Destination: Available. Condition: 4/5.','2026-04-12 10:11:21','2026-04-12 10:11:21',120,1),(155,'returned','Returned from event: WRC NAIVASHA. Destination: Under Repair. Condition: 1/5.','2026-04-12 10:11:21','2026-04-12 10:11:21',142,1),(156,'returned','Returned from event: WRC NAIVASHA. Destination: Available. Condition: 4/5.','2026-04-12 10:11:21','2026-04-12 10:11:21',146,1),(157,'returned','Returned from event: WRC NAIVASHA. Destination: Cleaning. Condition: 4/5.','2026-04-12 10:11:21','2026-04-12 10:11:21',154,1),(158,'cleaned','Item marked as cleaned and moved back to Warehouse.','2026-04-12 10:11:56','2026-04-12 10:11:56',117,1),(159,'cleaned','Item marked as cleaned and moved back to Warehouse.','2026-04-12 10:11:56','2026-04-12 10:11:56',154,1),(160,'cleaned','Item marked as cleaned and moved back to Warehouse.','2026-04-12 10:11:56','2026-04-12 10:11:56',159,1),(161,'dispatched','Dispatched to event: OKTOBA FEST. Condition: Good.','2026-04-12 10:40:42','2026-04-12 10:40:42',132,1),(162,'dispatched','Dispatched to event: OKTOBA FEST. Condition: Good.','2026-04-12 10:40:42','2026-04-12 10:40:42',146,1),(163,'dispatched','Dispatched to event: OKTOBA FEST. Condition: Good.','2026-04-12 10:40:42','2026-04-12 10:40:42',148,1),(164,'returned','Returned from event: OKTOBA FEST. Destination: Available. Condition: 4/5.','2026-04-12 10:41:52','2026-04-12 10:41:52',132,1),(165,'returned','Returned from event: OKTOBA FEST. Destination: Under Repair. Condition: 3/5.','2026-04-12 10:41:52','2026-04-12 10:41:52',146,1),(166,'returned','Returned from event: OKTOBA FEST. Destination: Cleaning. Condition: 4/5.','2026-04-12 10:41:52','2026-04-12 10:41:52',148,1),(167,'cleaned','Item marked as cleaned and moved back to Warehouse.','2026-04-12 10:42:14','2026-04-12 10:42:14',148,1),(168,'cleaned','Item marked as cleaned and moved back to Warehouse.','2026-04-12 10:42:14','2026-04-12 10:42:14',162,1);
/*!40000 ALTER TABLE `activity_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `assignments`
--

DROP TABLE IF EXISTS `assignments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `assignments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `item_id` bigint unsigned NOT NULL,
  `assigned_to` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `assigned_by` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `due_date` date DEFAULT NULL,
  `returned_at` date DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Active',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `assignments_item_id_foreign` (`item_id`),
  CONSTRAINT `assignments_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `assignments`
--

LOCK TABLES `assignments` WRITE;
/*!40000 ALTER TABLE `assignments` DISABLE KEYS */;
/*!40000 ALTER TABLE `assignments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
INSERT INTO `cache` VALUES ('laravel-cache-spatie.permission.cache','a:3:{s:5:\"alias\";a:4:{s:1:\"a\";s:2:\"id\";s:1:\"b\";s:4:\"name\";s:1:\"c\";s:10:\"guard_name\";s:1:\"r\";s:5:\"roles\";}s:11:\"permissions\";a:9:{i:0;a:4:{s:1:\"a\";i:1;s:1:\"b\";s:14:\"view inventory\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:1;a:4:{s:1:\"a\";i:2;s:1:\"b\";s:16:\"create inventory\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:2;a:4:{s:1:\"a\";i:3;s:1:\"b\";s:14:\"edit inventory\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:3;a:4:{s:1:\"a\";i:4;s:1:\"b\";s:16:\"delete inventory\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:4;a:4:{s:1:\"a\";i:5;s:1:\"b\";s:12:\"view reports\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:5;a:4:{s:1:\"a\";i:6;s:1:\"b\";s:16:\"generate reports\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:6;a:4:{s:1:\"a\";i:7;s:1:\"b\";s:12:\"assign items\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:7;a:4:{s:1:\"a\";i:8;s:1:\"b\";s:12:\"return items\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:8;a:4:{s:1:\"a\";i:9;s:1:\"b\";s:12:\"manage users\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}}s:5:\"roles\";a:2:{i:0;a:3:{s:1:\"a\";i:1;s:1:\"b\";s:5:\"Admin\";s:1:\"c\";s:3:\"web\";}i:1;a:3:{s:1:\"a\";i:2;s:1:\"b\";s:7:\"Manager\";s:1:\"c\";s:3:\"web\";}}}',1776044823);
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
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
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'seats','2026-04-11 22:47:03','2026-04-11 22:47:03'),(2,'Tents - 30 Span','2026-04-11 23:26:58','2026-04-11 23:26:58'),(3,'Tents - 20 Span','2026-04-11 23:26:58','2026-04-11 23:26:58'),(4,'Tents - 15 Span','2026-04-11 23:26:58','2026-04-11 23:26:58'),(5,'Tents - 10 Span','2026-04-11 23:26:58','2026-04-11 23:26:58'),(6,'Tent - G25','2026-04-11 23:26:58','2026-04-11 23:26:58'),(7,'Tent - 6x6','2026-04-11 23:26:58','2026-04-11 23:26:58'),(8,'Furniture','2026-04-11 23:26:58','2026-04-11 23:26:58'),(9,'Flooring','2026-04-11 23:26:58','2026-04-11 23:26:58'),(10,'AV Equipment','2026-04-11 23:26:59','2026-04-11 23:26:59'),(11,'Fabric - Table Cloths','2026-04-11 23:26:59','2026-04-11 23:26:59');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `checklists`
--

DROP TABLE IF EXISTS `checklists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `checklists` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `checklists`
--

LOCK TABLES `checklists` WRITE;
/*!40000 ALTER TABLE `checklists` DISABLE KEYS */;
/*!40000 ALTER TABLE `checklists` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `event_item_images`
--

DROP TABLE IF EXISTS `event_item_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_item_images` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `event_item_id` bigint unsigned NOT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('dispatch','return') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'dispatch',
  `uploaded_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `event_item_images_event_item_id_foreign` (`event_item_id`),
  KEY `event_item_images_uploaded_by_foreign` (`uploaded_by`),
  CONSTRAINT `event_item_images_event_item_id_foreign` FOREIGN KEY (`event_item_id`) REFERENCES `event_items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `event_item_images_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `event_item_images`
--

LOCK TABLES `event_item_images` WRITE;
/*!40000 ALTER TABLE `event_item_images` DISABLE KEYS */;
INSERT INTO `event_item_images` VALUES (2,11,'events/3/returns/h5ryisCSunoC2zxclTewZGltIp5z4IkwoEMFuwAv.png','return',1,'2026-04-11 09:45:06','2026-04-11 09:45:06'),(3,40,'events/5/returns/6QACb7GcM2L4refGAKhvTm2BG6SwdEv7K9ME6dVK.png','return',1,'2026-04-11 23:07:40','2026-04-11 23:07:40'),(4,41,'events/5/returns/okYenvgx9Hu8lXkUQXyj3zvS9VTsScUVP8442eqK.png','return',1,'2026-04-11 23:07:45','2026-04-11 23:07:45'),(5,44,'events/5/returns/lOOBpnok5169TONm2o5UOEajRrQNEFkJQMxfpXkc.png','return',1,'2026-04-11 23:07:50','2026-04-11 23:07:50'),(6,74,'events/10/items/npLgW9VsyeSfCd2XS0CwC5tEAJnsjz8HxP10HWV6.png','dispatch',1,'2026-04-12 10:40:31','2026-04-12 10:40:31');
/*!40000 ALTER TABLE `event_item_images` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `event_items`
--

DROP TABLE IF EXISTS `event_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `event_id` bigint unsigned NOT NULL,
  `item_id` bigint unsigned NOT NULL,
  `condition_on_dispatch` tinyint DEFAULT NULL COMMENT '1=Poor 2=Average 3=Fair 4=Good 5=Excellent',
  `condition_on_return` tinyint DEFAULT NULL,
  `dispatch_notes` text COLLATE utf8mb4_unicode_ci,
  `return_notes` text COLLATE utf8mb4_unicode_ci,
  `return_processed` tinyint(1) NOT NULL DEFAULT '0',
  `dispatched_at` timestamp NULL DEFAULT NULL,
  `returned_at` timestamp NULL DEFAULT NULL,
  `dispatched_by` bigint unsigned DEFAULT NULL,
  `returned_by` bigint unsigned DEFAULT NULL,
  `return_destination` enum('warehouse','cleaning','repair') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `event_items_event_id_item_id_unique` (`event_id`,`item_id`),
  KEY `event_items_item_id_foreign` (`item_id`),
  KEY `event_items_dispatched_by_foreign` (`dispatched_by`),
  KEY `event_items_returned_by_foreign` (`returned_by`),
  CONSTRAINT `event_items_dispatched_by_foreign` FOREIGN KEY (`dispatched_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `event_items_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  CONSTRAINT `event_items_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `event_items_returned_by_foreign` FOREIGN KEY (`returned_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=75 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `event_items`
--

LOCK TABLES `event_items` WRITE;
/*!40000 ALTER TABLE `event_items` DISABLE KEYS */;
INSERT INTO `event_items` VALUES (7,2,135,5,NULL,NULL,NULL,0,'2026-04-08 23:55:06',NULL,1,NULL,NULL,'2026-04-08 23:51:13','2026-04-08 23:55:06'),(8,2,126,4,NULL,NULL,NULL,0,'2026-04-08 23:55:06',NULL,1,NULL,NULL,'2026-04-08 23:51:13','2026-04-08 23:55:06'),(9,2,145,3,NULL,NULL,NULL,0,'2026-04-08 23:55:06',NULL,1,NULL,NULL,'2026-04-08 23:51:13','2026-04-08 23:55:06'),(10,2,144,3,NULL,NULL,NULL,0,'2026-04-08 23:55:06',NULL,1,NULL,NULL,'2026-04-08 23:51:13','2026-04-08 23:55:06'),(11,3,139,4,4,NULL,NULL,0,'2026-04-11 08:46:58','2026-04-11 09:48:21',1,1,NULL,'2026-04-11 08:45:09','2026-04-11 09:48:21'),(12,3,141,2,5,NULL,NULL,0,'2026-04-11 08:46:58','2026-04-11 09:48:22',1,1,NULL,'2026-04-11 08:45:10','2026-04-11 09:48:22'),(13,3,149,4,4,NULL,NULL,0,'2026-04-11 08:46:58','2026-04-11 09:48:22',1,1,NULL,'2026-04-11 08:45:10','2026-04-11 09:48:22'),(14,3,134,4,4,NULL,NULL,0,'2026-04-11 08:46:59','2026-04-11 09:48:22',1,1,NULL,'2026-04-11 08:45:10','2026-04-11 09:48:22'),(15,3,143,4,4,NULL,NULL,0,'2026-04-11 08:46:59','2026-04-11 09:48:22',1,1,NULL,'2026-04-11 08:45:10','2026-04-11 09:48:22'),(16,3,142,2,4,NULL,NULL,0,'2026-04-11 08:46:59','2026-04-11 09:48:22',1,1,NULL,'2026-04-11 08:45:10','2026-04-11 09:48:22'),(17,3,146,3,4,NULL,NULL,0,'2026-04-11 08:46:59','2026-04-11 09:48:22',1,1,NULL,'2026-04-11 08:45:10','2026-04-11 09:48:22'),(18,3,148,4,4,NULL,NULL,0,'2026-04-11 08:46:59','2026-04-11 09:48:22',1,1,NULL,'2026-04-11 08:45:10','2026-04-11 09:48:22'),(19,3,132,4,4,NULL,NULL,0,'2026-04-11 08:46:59','2026-04-11 09:48:22',1,1,NULL,'2026-04-11 08:45:10','2026-04-11 09:48:22'),(20,3,147,5,4,NULL,NULL,0,'2026-04-11 08:46:59','2026-04-11 09:48:23',1,1,NULL,'2026-04-11 08:45:10','2026-04-11 09:48:23'),(21,3,136,5,4,NULL,NULL,0,'2026-04-11 08:46:59','2026-04-11 09:48:23',1,1,NULL,'2026-04-11 08:45:10','2026-04-11 09:48:23'),(22,3,151,4,4,NULL,NULL,0,'2026-04-11 08:47:00','2026-04-11 09:48:23',1,1,NULL,'2026-04-11 08:45:10','2026-04-11 09:48:23'),(23,3,150,4,4,NULL,NULL,0,'2026-04-11 08:47:00','2026-04-11 09:48:23',1,1,NULL,'2026-04-11 08:45:10','2026-04-11 09:48:23'),(24,3,137,5,4,NULL,NULL,0,'2026-04-11 08:47:00','2026-04-11 09:48:23',1,1,NULL,'2026-04-11 08:45:10','2026-04-11 09:48:23'),(25,3,127,4,4,NULL,NULL,0,'2026-04-11 08:47:00','2026-04-11 09:48:23',1,1,NULL,'2026-04-11 08:45:10','2026-04-11 09:48:23'),(26,3,129,4,4,NULL,NULL,0,'2026-04-11 08:47:00','2026-04-11 09:48:23',1,1,NULL,'2026-04-11 08:45:10','2026-04-11 09:48:23'),(27,3,133,5,4,NULL,NULL,0,'2026-04-11 08:47:00','2026-04-11 09:48:24',1,1,NULL,'2026-04-11 08:45:10','2026-04-11 09:48:24'),(28,3,131,4,4,NULL,NULL,0,'2026-04-11 08:47:00','2026-04-11 09:48:24',1,1,NULL,'2026-04-11 08:45:10','2026-04-11 09:48:24'),(29,3,140,4,4,NULL,NULL,0,'2026-04-11 08:47:00','2026-04-11 09:48:24',1,1,NULL,'2026-04-11 08:45:10','2026-04-11 09:48:24'),(30,3,125,4,4,NULL,NULL,0,'2026-04-11 08:47:01','2026-04-11 09:48:24',1,1,NULL,'2026-04-11 08:45:10','2026-04-11 09:48:24'),(31,3,138,5,4,NULL,NULL,0,'2026-04-11 08:47:01','2026-04-11 09:48:24',1,1,NULL,'2026-04-11 08:45:10','2026-04-11 09:48:24'),(32,3,130,4,4,NULL,NULL,0,'2026-04-11 08:47:01','2026-04-11 09:48:24',1,1,NULL,'2026-04-11 08:45:10','2026-04-11 09:48:24'),(33,3,128,4,4,NULL,NULL,0,'2026-04-11 08:47:01','2026-04-11 09:48:24',1,1,NULL,'2026-04-11 08:45:10','2026-04-11 09:48:24'),(34,3,154,4,3,NULL,NULL,0,'2026-04-11 08:47:01','2026-04-11 09:48:24',1,1,NULL,'2026-04-11 08:45:10','2026-04-11 09:48:24'),(35,3,155,4,3,NULL,NULL,0,'2026-04-11 08:47:01','2026-04-11 09:48:25',1,1,NULL,'2026-04-11 08:45:10','2026-04-11 09:48:25'),(36,4,139,4,5,NULL,NULL,0,'2026-04-11 10:07:18','2026-04-11 23:45:03',1,1,NULL,'2026-04-11 10:06:53','2026-04-11 23:45:03'),(37,4,141,4,4,NULL,NULL,0,'2026-04-11 10:07:18','2026-04-11 23:45:03',1,1,NULL,'2026-04-11 10:06:53','2026-04-11 23:45:03'),(38,4,161,4,4,NULL,NULL,0,'2026-04-11 10:07:18','2026-04-11 23:45:03',1,1,NULL,'2026-04-11 10:06:53','2026-04-11 23:45:03'),(39,4,163,4,5,NULL,NULL,0,'2026-04-11 10:07:18','2026-04-11 23:45:04',1,1,NULL,'2026-04-11 10:06:53','2026-04-11 23:45:04'),(40,5,134,4,4,NULL,NULL,1,'2026-04-11 10:21:53','2026-04-12 00:18:44',1,1,'warehouse','2026-04-11 10:19:54','2026-04-12 00:18:44'),(41,5,142,4,4,NULL,NULL,1,'2026-04-11 10:21:53','2026-04-12 00:18:44',1,1,'warehouse','2026-04-11 10:19:54','2026-04-12 00:18:44'),(42,5,125,4,4,NULL,NULL,1,'2026-04-11 10:21:53','2026-04-12 00:18:43',1,1,'warehouse','2026-04-11 10:19:54','2026-04-12 00:18:43'),(43,5,159,4,4,NULL,NULL,1,'2026-04-11 10:21:53','2026-04-12 00:18:44',1,1,'cleaning','2026-04-11 10:19:54','2026-04-12 00:18:44'),(44,5,162,4,4,NULL,NULL,1,'2026-04-11 10:21:53','2026-04-12 00:18:44',1,1,'cleaning','2026-04-11 10:19:54','2026-04-12 00:18:44'),(45,6,139,4,5,NULL,NULL,1,'2026-04-12 00:02:10','2026-04-12 00:07:28',1,1,'cleaning','2026-04-12 00:01:41','2026-04-12 00:07:28'),(46,6,127,3,4,NULL,NULL,1,'2026-04-12 00:02:10','2026-04-12 00:07:28',1,1,'warehouse','2026-04-12 00:01:41','2026-04-12 00:07:28'),(47,6,59,4,4,NULL,NULL,1,'2026-04-12 00:02:10','2026-04-12 00:07:28',1,1,'warehouse','2026-04-12 00:01:41','2026-04-12 00:07:28'),(48,7,141,4,4,NULL,NULL,1,'2026-04-12 01:23:13','2026-04-12 01:24:20',1,1,'cleaning','2026-04-12 01:22:20','2026-04-12 01:24:20'),(49,7,149,4,1,NULL,NULL,1,'2026-04-12 01:23:13','2026-04-12 01:24:21',1,1,'repair','2026-04-12 01:22:20','2026-04-12 01:24:21'),(50,7,134,4,4,NULL,NULL,1,'2026-04-12 01:23:13','2026-04-12 01:24:20',1,1,'warehouse','2026-04-12 01:22:20','2026-04-12 01:24:20'),(51,7,152,4,1,NULL,NULL,1,'2026-04-12 01:23:14','2026-04-12 01:24:21',1,1,'repair','2026-04-12 01:22:20','2026-04-12 01:24:21'),(52,7,153,4,4,NULL,NULL,1,'2026-04-12 01:23:14','2026-04-12 01:24:21',1,1,'cleaning','2026-04-12 01:22:20','2026-04-12 01:24:21'),(53,7,67,4,4,NULL,NULL,1,'2026-04-12 01:23:13','2026-04-12 01:24:20',1,1,'warehouse','2026-04-12 01:22:20','2026-04-12 01:24:20'),(54,7,68,4,4,NULL,NULL,1,'2026-04-12 01:23:13','2026-04-12 01:24:20',1,1,'warehouse','2026-04-12 01:22:20','2026-04-12 01:24:20'),(55,7,71,4,4,NULL,NULL,1,'2026-04-12 01:23:13','2026-04-12 01:24:20',1,1,'warehouse','2026-04-12 01:22:20','2026-04-12 01:24:20'),(56,7,60,4,4,NULL,NULL,1,'2026-04-12 01:23:13','2026-04-12 01:24:20',1,1,'warehouse','2026-04-12 01:22:20','2026-04-12 01:24:20'),(57,7,51,4,4,NULL,NULL,1,'2026-04-12 01:23:12','2026-04-12 01:24:20',1,1,'warehouse','2026-04-12 01:22:20','2026-04-12 01:24:20'),(58,8,134,4,NULL,NULL,NULL,0,'2026-04-12 01:28:04',NULL,1,NULL,NULL,'2026-04-12 01:27:02','2026-04-12 01:28:04'),(59,8,143,4,NULL,NULL,NULL,0,'2026-04-12 01:28:04',NULL,1,NULL,NULL,'2026-04-12 01:27:03','2026-04-12 01:28:04'),(60,8,138,4,NULL,NULL,NULL,0,'2026-04-12 01:28:04',NULL,1,NULL,NULL,'2026-04-12 01:27:03','2026-04-12 01:28:04'),(61,8,160,4,NULL,NULL,NULL,0,'2026-04-12 01:28:04',NULL,1,NULL,NULL,'2026-04-12 01:27:03','2026-04-12 01:28:04'),(62,8,171,4,NULL,NULL,NULL,0,'2026-04-12 01:28:04',NULL,1,NULL,NULL,'2026-04-12 01:27:03','2026-04-12 01:28:04'),(63,8,111,4,NULL,NULL,NULL,0,'2026-04-12 01:28:04',NULL,1,NULL,NULL,'2026-04-12 01:27:03','2026-04-12 01:28:04'),(64,8,112,4,NULL,NULL,NULL,0,'2026-04-12 01:28:04',NULL,1,NULL,NULL,'2026-04-12 01:27:03','2026-04-12 01:28:04'),(65,8,78,4,NULL,NULL,NULL,0,'2026-04-12 01:28:03',NULL,1,NULL,NULL,'2026-04-12 01:27:03','2026-04-12 01:28:03'),(66,8,79,4,NULL,NULL,NULL,0,'2026-04-12 01:28:03',NULL,1,NULL,NULL,'2026-04-12 01:27:03','2026-04-12 01:28:03'),(67,9,142,4,1,NULL,NULL,1,'2026-04-12 10:09:27','2026-04-12 10:11:21',1,1,'repair','2026-04-12 10:08:25','2026-04-12 10:11:21'),(68,9,146,4,4,NULL,NULL,1,'2026-04-12 10:09:27','2026-04-12 10:11:21',1,1,'warehouse','2026-04-12 10:08:25','2026-04-12 10:11:21'),(69,9,154,4,4,NULL,NULL,1,'2026-04-12 10:09:27','2026-04-12 10:11:21',1,1,'cleaning','2026-04-12 10:08:25','2026-04-12 10:11:21'),(70,9,120,4,4,NULL,NULL,1,'2026-04-12 10:09:27','2026-04-12 10:11:21',1,1,'warehouse','2026-04-12 10:08:25','2026-04-12 10:11:21'),(71,9,117,4,3,NULL,NULL,1,'2026-04-12 10:09:27','2026-04-12 10:11:21',1,1,'cleaning','2026-04-12 10:08:25','2026-04-12 10:11:21'),(72,10,146,4,3,NULL,NULL,1,'2026-04-12 10:40:42','2026-04-12 10:41:52',1,1,'repair','2026-04-12 10:39:41','2026-04-12 10:41:52'),(73,10,148,4,4,NULL,NULL,1,'2026-04-12 10:40:42','2026-04-12 10:41:52',1,1,'cleaning','2026-04-12 10:39:41','2026-04-12 10:41:52'),(74,10,132,4,4,NULL,NULL,1,'2026-04-12 10:40:42','2026-04-12 10:41:51',1,1,'warehouse','2026-04-12 10:39:41','2026-04-12 10:41:51');
/*!40000 ALTER TABLE `event_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `event_staff`
--

DROP TABLE IF EXISTS `event_staff`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_staff` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `event_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `role` enum('member','leader') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'member',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `event_staff_event_id_user_id_unique` (`event_id`,`user_id`),
  KEY `event_staff_user_id_foreign` (`user_id`),
  CONSTRAINT `event_staff_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  CONSTRAINT `event_staff_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `event_staff`
--

LOCK TABLES `event_staff` WRITE;
/*!40000 ALTER TABLE `event_staff` DISABLE KEYS */;
INSERT INTO `event_staff` VALUES (1,5,3,'leader','2026-04-11 10:20:54','2026-04-11 10:20:54'),(2,5,4,'member','2026-04-11 10:20:54','2026-04-11 10:20:54'),(3,6,4,'member','2026-04-12 00:01:59','2026-04-12 00:01:59'),(4,6,3,'leader','2026-04-12 00:01:59','2026-04-12 00:01:59'),(5,7,11,'member','2026-04-12 01:22:37','2026-04-12 01:22:37'),(6,7,4,'member','2026-04-12 01:22:37','2026-04-12 01:22:37'),(7,8,11,'member','2026-04-12 01:27:22','2026-04-12 01:27:22'),(8,8,4,'member','2026-04-12 01:27:22','2026-04-12 01:27:22'),(9,9,4,'leader','2026-04-12 10:08:52','2026-04-12 10:08:52'),(10,9,11,'member','2026-04-12 10:08:52','2026-04-12 10:08:52'),(11,10,4,'leader','2026-04-12 10:40:15','2026-04-12 10:40:15'),(12,10,11,'member','2026-04-12 10:40:15','2026-04-12 10:40:15');
/*!40000 ALTER TABLE `event_staff` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `events`
--

DROP TABLE IF EXISTS `events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `events` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `client_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `venue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `location_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `loading_date` date NOT NULL,
  `setup_date` date NOT NULL,
  `event_date` date NOT NULL,
  `setdown_date` date NOT NULL,
  `status` enum('Draft','Scheduled','Active','Set Down','Completed','Cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Draft',
  `cost` decimal(12,2) DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `events_created_by_foreign` (`created_by`),
  CONSTRAINT `events_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `events`
--

LOCK TABLES `events` WRITE;
/*!40000 ALTER TABLE `events` DISABLE KEYS */;
INSERT INTO `events` VALUES (2,'WRC SAFARI RALLY NAIVASHA','SAFARI RALLY TEAM','NAIVASHA',NULL,NULL,NULL,'2026-04-10','2026-04-11','2026-04-11','2026-04-12','Completed',NULL,NULL,1,'2026-04-08 23:50:02','2026-04-11 08:13:58'),(3,'OKTOBER FEST','Tusker','Ngong Racecourse','Ngong race course',NULL,NULL,'2026-04-12','2026-04-12','2026-04-13','2026-04-14','Set Down',NULL,NULL,1,'2026-04-11 08:44:39','2026-04-11 08:47:19'),(4,'WEDDING GALA','Beysix','Safari Park','Thika road Nairobi',NULL,NULL,'2026-04-11','2026-04-12','2026-04-12','2026-04-13','Set Down',NULL,NULL,1,'2026-04-11 10:06:22','2026-04-11 23:44:31'),(5,'Showman Residency','NYASHINSKI','Carnivore','Nairobi',NULL,NULL,'2026-04-12','2026-04-13','2026-04-14','2026-04-15','Completed',NULL,NULL,1,'2026-04-11 10:16:41','2026-04-12 00:18:44'),(6,'LABOUR DAY','GOVT','Nyayo stadium','Nairobi',NULL,NULL,'2026-04-13','2026-04-14','2026-04-14','2026-04-14','Completed',3000000.00,NULL,1,'2026-04-12 00:01:26','2026-04-12 00:07:29'),(7,'SOUL FEST','SOUL GENERATION','Carnivore grounds','Langata',NULL,NULL,'2026-04-12','2026-04-13','2026-04-14','2026-04-15','Completed',350000.00,NULL,1,'2026-04-12 01:21:58','2026-04-12 01:24:21'),(8,'NAIROBI FEST','Hustle sasa','KICC','NAIROBI',NULL,NULL,'2026-04-11','2026-04-12','2026-04-13','2026-04-14','Set Down',NULL,NULL,1,'2026-04-12 01:25:58','2026-04-12 01:28:38'),(9,'WRC NAIVASHA','WRC','WRC NAIVASHA','NAIVASHA',NULL,NULL,'2026-04-13','2026-04-15','2026-04-15','2026-04-16','Completed',3000000.00,NULL,1,'2026-04-12 10:07:51','2026-04-12 10:11:21'),(10,'OKTOBA FEST','TUSKER','Ngong hills','Ngong',NULL,NULL,'2026-04-13','2026-04-14','2026-04-15','2026-04-16','Completed',500000.00,NULL,1,'2026-04-12 10:39:04','2026-04-12 10:41:52');
/*!40000 ALTER TABLE `events` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
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
-- Table structure for table `item_images`
--

DROP TABLE IF EXISTS `item_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `item_images` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `item_id` bigint unsigned NOT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT '0',
  `caption` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `uploaded_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `item_images_item_id_foreign` (`item_id`),
  KEY `item_images_uploaded_by_foreign` (`uploaded_by`),
  CONSTRAINT `item_images_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `item_images_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `item_images`
--

LOCK TABLES `item_images` WRITE;
/*!40000 ALTER TABLE `item_images` DISABLE KEYS */;
/*!40000 ALTER TABLE `item_images` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `items`
--

DROP TABLE IF EXISTS `items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `brand` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `model_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `serial_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `purchase_cost` decimal(12,2) DEFAULT NULL,
  `specifications` text COLLATE utf8mb4_unicode_ci,
  `dimensions` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `weight` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `assigned_to` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `assigned_by` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_updated_by` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_updated_at` timestamp NULL DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=178 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `items`
--

LOCK TABLES `items` WRITE;
/*!40000 ALTER TABLE `items` DISABLE KEYS */;
INSERT INTO `items` VALUES (1,'Main Beam / Chase','Tents - 30 Span',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:26','2026-04-06 05:29:26'),(2,'Rafters','Tents - 30 Span',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:26','2026-04-06 05:29:26'),(3,'Gable Ceiling','Tents - 30 Span',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:26','2026-04-06 05:29:26'),(4,'Stands','Tents - 30 Span',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:26','2026-04-06 05:29:26'),(5,'Zips','Tents - 30 Span',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:26','2026-04-06 05:29:26'),(6,'Ridge Beam','Tents - 30 Span',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:26','2026-04-06 05:29:26'),(7,'Roof Purlins','Tents - 30 Span',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:26','2026-04-06 05:29:26'),(8,'Side Purlins','Tents - 30 Span',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:26','2026-04-06 05:29:26'),(9,'Ceiling Rope','Tents - 30 Span',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:26','2026-04-06 05:29:26'),(10,'Connectors','Tents - 30 Span',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:26','2026-04-06 05:29:26'),(11,'Roof Cover (Top)','Tents - 30 Span',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:27','2026-04-06 05:29:27'),(12,'Gable Stand A','Tents - 30 Span',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:27','2026-04-06 05:29:27'),(13,'Gable Stand B','Tents - 30 Span',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:27','2026-04-06 05:29:27'),(14,'Gable Stand C','Tents - 30 Span',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:27','2026-04-06 05:29:27'),(15,'Gable Stand AA','Tents - 30 Span',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:27','2026-04-06 05:29:27'),(16,'Gable Stand BB','Tents - 30 Span',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:27','2026-04-06 05:29:27'),(17,'Side Walls (Flaps)','Tents - 30 Span',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:27','2026-04-06 05:29:27'),(18,'Base Plates','Tents - 30 Span',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:27','2026-04-06 05:29:27'),(19,'Bags','Tents - 30 Span',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:27','2026-04-06 05:29:27'),(20,'Pocketers','Tents - 30 Span',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:27','2026-04-06 05:29:27'),(21,'Angles','Tents - 30 Span',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:27','2026-04-06 05:29:27'),(22,'Gable Bars','Tents - 30 Span',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:27','2026-04-06 05:29:27'),(23,'Ratchet Straps','Tents - 30 Span',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:27','2026-04-06 05:29:27'),(24,'Purlin Connectors','Tents - 30 Span',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:27','2026-04-06 05:29:27'),(25,'Clamps','Tents - 30 Span',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:27','2026-04-06 05:29:27'),(26,'Bolts and Nuts','Tents - 30 Span',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:27','2026-04-06 05:29:27'),(27,'Ceiling Rods','Tents - 30 Span',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:27','2026-04-06 05:29:27'),(28,'X-Bar (Makasi)','Tents - 30 Span',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:27','2026-04-06 05:29:27'),(29,'Screws','Tents - 30 Span',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:27','2026-04-06 05:29:27'),(30,'Gable Canvas','Tents - 30 Span',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:27','2026-04-06 05:29:27'),(31,'Curtains','Tents - 30 Span',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:27','2026-04-06 05:29:27'),(32,'Ceilings','Tents - 30 Span',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:27','2026-04-06 05:29:27'),(33,'USB 30 Span','Tents - 30 Span',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:27','2026-04-06 05:29:27'),(34,'USB 20 Span','Tents - 30 Span',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:27','2026-04-06 05:29:27'),(35,'Main Beam / Chase','Tents - 20 Span',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:27','2026-04-06 05:29:27'),(36,'Rafters','Tents - 20 Span',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:28','2026-04-06 05:29:28'),(37,'Gable Ceiling','Tents - 20 Span',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:28','2026-04-06 05:29:28'),(38,'Stands','Tents - 20 Span',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:28','2026-04-06 05:29:28'),(39,'Ridge Beam','Tents - 20 Span',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:28','2026-04-06 05:29:28'),(40,'Roof Purlins','Tents - 20 Span',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:28','2026-04-06 05:29:28'),(41,'Side Purlins','Tents - 20 Span',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:28','2026-04-06 05:29:28'),(42,'Roof Cover (Top)','Tents - 20 Span',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:28','2026-04-06 05:29:28'),(43,'Side Walls (Flaps)','Tents - 20 Span',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:28','2026-04-06 05:29:28'),(44,'Base Plates','Tents - 20 Span',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:28','2026-04-06 05:29:28'),(45,'Angles','Tents - 20 Span',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:28','2026-04-06 05:29:28'),(46,'Gable Canvas','Tents - 20 Span',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:28','2026-04-06 05:29:28'),(47,'Curtains','Tents - 20 Span',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:28','2026-04-06 05:29:28'),(48,'Ceilings','Tents - 20 Span',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:28','2026-04-06 05:29:28'),(49,'Main Beam / Chase','Tents - 15 Span',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:28','2026-04-06 05:29:28'),(50,'Rafters','Tents - 15 Span',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:28','2026-04-06 05:29:28'),(51,'Gable Ceiling','Tents - 15 Span',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'Admin User','2026-04-12 01:24:20',NULL,NULL,'2026-04-06 05:29:28','2026-04-12 01:24:20'),(52,'Stands','Tents - 15 Span',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:28','2026-04-06 05:29:28'),(53,'Ridge Beam','Tents - 15 Span',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:28','2026-04-06 05:29:28'),(54,'Roof Purlins','Tents - 15 Span',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:28','2026-04-06 05:29:28'),(55,'Side Purlins','Tents - 15 Span',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:28','2026-04-06 05:29:28'),(56,'Roof Cover (Top)','Tents - 15 Span',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:28','2026-04-06 05:29:28'),(57,'Side Walls (Flaps)','Tents - 15 Span',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:28','2026-04-06 05:29:28'),(58,'Base Plates','Tents - 15 Span',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:28','2026-04-06 05:29:28'),(59,'Angles','Tents - 15 Span',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'Admin User','2026-04-12 00:07:28',NULL,NULL,'2026-04-06 05:29:28','2026-04-12 00:07:28'),(60,'Gable Canvas','Tents - 15 Span',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'Admin User','2026-04-12 01:24:20',NULL,NULL,'2026-04-06 05:29:28','2026-04-12 01:24:20'),(61,'Curtains','Tents - 15 Span',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:28','2026-04-06 05:29:28'),(62,'Ceilings','Tents - 15 Span',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:28','2026-04-06 05:29:28'),(63,'Main Beam / Chase','Tents - 10 Span',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:29','2026-04-06 05:29:29'),(64,'Rafters','Tents - 10 Span',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:29','2026-04-06 05:29:29'),(65,'Gable Ceiling','Tents - 10 Span',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:29','2026-04-06 05:29:29'),(66,'Stands','Tents - 10 Span',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:29','2026-04-06 05:29:29'),(67,'Ridge Beam','Tents - 10 Span',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'Admin User','2026-04-12 01:24:20',NULL,NULL,'2026-04-06 05:29:29','2026-04-12 01:24:20'),(68,'Roof Purlins','Tents - 10 Span',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'Admin User','2026-04-12 01:24:20',NULL,NULL,'2026-04-06 05:29:29','2026-04-12 01:24:20'),(69,'Side Purlins','Tents - 10 Span',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:29','2026-04-06 05:29:29'),(70,'Roof Cover (Top)','Tents - 10 Span',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:29','2026-04-06 05:29:29'),(71,'Side Walls (Flaps)','Tents - 10 Span',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'Admin User','2026-04-12 01:24:20',NULL,NULL,'2026-04-06 05:29:29','2026-04-12 01:24:20'),(72,'Base Plates','Tents - 10 Span',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:29','2026-04-06 05:29:29'),(73,'Angles','Tents - 10 Span',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:29','2026-04-06 05:29:29'),(74,'Gable Canvas','Tents - 10 Span',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:29','2026-04-06 05:29:29'),(75,'Curtains','Tents - 10 Span',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:29','2026-04-06 05:29:29'),(76,'Ceilings','Tents - 10 Span',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:29','2026-04-06 05:29:29'),(77,'Canvas AA','Tent - G25',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:29','2026-04-06 05:29:29'),(78,'Canvas BB','Tent - G25',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Assigned','KICC',NULL,NULL,'Admin User','2026-04-12 01:28:03',NULL,NULL,'2026-04-06 05:29:29','2026-04-12 01:28:03'),(79,'Canvas CC','Tent - G25',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Assigned','KICC',NULL,NULL,'Admin User','2026-04-12 01:28:03',NULL,NULL,'2026-04-06 05:29:29','2026-04-12 01:28:03'),(80,'Canvas DD','Tent - G25',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:29','2026-04-06 05:29:29'),(81,'Sidewalls','Tent - G25',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:29','2026-04-06 05:29:29'),(82,'Gable (Sambu)','Tent - G25',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:29','2026-04-06 05:29:29'),(83,'Shade Net','Tent - G25',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:29','2026-04-06 05:29:29'),(84,'Long Chase','Tent - 6x6',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:29','2026-04-06 05:29:29'),(85,'Stands','Tent - 6x6',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:29','2026-04-06 05:29:29'),(86,'Purline','Tent - 6x6',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:29','2026-04-06 05:29:29'),(87,'Head','Tent - 6x6',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:29','2026-04-06 05:29:29'),(88,'Short Chase','Tent - 6x6',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:29','2026-04-06 05:29:29'),(89,'Shado Awning','Tent - 6x6',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:29','2026-04-06 05:29:29'),(90,'Base Plates','Tent - 6x6',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:30','2026-04-06 05:29:30'),(91,'Angles','Tent - 6x6',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:30','2026-04-06 05:29:30'),(92,'Peak','Tent - 6x6',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:30','2026-04-06 05:29:30'),(93,'Lock','Tent - 6x6',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:30','2026-04-06 05:29:30'),(94,'Winch','Tent - 6x6',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:30','2026-04-06 05:29:30'),(95,'Tops','Tent - 6x6',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:30','2026-04-06 05:29:30'),(96,'Flaps','Tent - 6x6',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:30','2026-04-06 05:29:30'),(97,'Curtains','Tent - 6x6',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:30','2026-04-06 05:29:30'),(98,'Ceilings','Tent - 6x6',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:30','2026-04-06 05:29:30'),(99,'Ceiling Ropes','Tent - 6x6',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:30','2026-04-06 05:29:30'),(100,'Banquet Seats','Furniture',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:30','2026-04-06 05:29:30'),(101,'Rectangular Tables','Furniture',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:30','2026-04-06 05:29:30'),(102,'Round Tables','Furniture',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:30','2026-04-06 05:29:30'),(103,'Round Tables Without Stands','Furniture',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:30','2026-04-06 05:29:30'),(104,'Executive Round Tables','Furniture',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:30','2026-04-06 05:29:30'),(105,'Staircases','Furniture',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:30','2026-04-06 05:29:30'),(106,'Kids Chairs Blue','Furniture',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:30','2026-04-06 05:29:30'),(107,'Kids Chairs Pink','Furniture',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:30','2026-04-06 05:29:30'),(108,'Kids Chairs White','Furniture',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:30','2026-04-06 05:29:30'),(109,'Executive Seats Red','Furniture',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:30','2026-04-06 05:29:30'),(110,'Cocktail Tables','Furniture',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:30','2026-04-06 05:29:30'),(111,'Cocktail Seats','Furniture',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Assigned','KICC',NULL,NULL,'Admin User','2026-04-12 01:28:04',NULL,NULL,'2026-04-06 05:29:30','2026-04-12 01:28:04'),(112,'Cocktail Table Tops','Furniture',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Assigned','KICC',NULL,NULL,'Admin User','2026-04-12 01:28:04',NULL,NULL,'2026-04-06 05:29:30','2026-04-12 01:28:04'),(113,'Brass Stanchions','Furniture',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:30','2026-04-06 05:29:30'),(114,'Plastic Armless Seats','Furniture',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:30','2026-04-06 05:29:30'),(115,'Carpet 4x25','Flooring',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:30','2026-04-06 05:29:30'),(116,'Carpet 4x20','Flooring',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:30','2026-04-06 05:29:30'),(117,'Carpet 4x15','Flooring',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'Admin User','2026-04-12 10:11:56',NULL,NULL,'2026-04-06 05:29:30','2026-04-12 10:11:56'),(118,'Carpet 6x6','Flooring',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:31','2026-04-06 05:29:31'),(119,'Carpet 4x6','Flooring',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:31','2026-04-06 05:29:31'),(120,'Carpet 4x10','Flooring',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'Admin User','2026-04-12 10:11:21',NULL,NULL,'2026-04-06 05:29:31','2026-04-12 10:11:21'),(121,'Pro Floor','Flooring',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:31','2026-04-06 05:29:31'),(122,'Turf Grass','Flooring',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:31','2026-04-06 05:29:31'),(123,'Walkways','Flooring',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:31','2026-04-06 05:29:31'),(124,'Carpet Machine','Flooring',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:31','2026-04-06 05:29:31'),(125,'Screens','AV Equipment',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'Admin User','2026-04-12 00:18:44',NULL,NULL,'2026-04-06 05:29:31','2026-04-12 00:18:44'),(126,'Stage Boards','AV Equipment',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Assigned','Warehouse',NULL,NULL,'Admin User','2026-04-08 23:55:06',NULL,NULL,'2026-04-06 05:29:31','2026-04-08 23:55:06'),(127,'Moving Heads','AV Equipment',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'Admin User','2026-04-12 00:07:28',NULL,NULL,'2026-04-06 05:29:31','2026-04-12 00:07:28'),(128,'Wide Camera 512dm','AV Equipment',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'Admin User','2026-04-11 09:48:24',NULL,NULL,'2026-04-06 05:29:31','2026-04-11 09:48:24'),(129,'Pacans','AV Equipment',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'Admin User','2026-04-11 09:48:23',NULL,NULL,'2026-04-06 05:29:31','2026-04-11 09:48:23'),(130,'Strobe Lights','AV Equipment',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'Admin User','2026-04-11 09:48:24',NULL,NULL,'2026-04-06 05:29:31','2026-04-11 09:48:24'),(131,'Processor','AV Equipment',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'Admin User','2026-04-11 09:48:24',NULL,NULL,'2026-04-06 05:29:31','2026-04-11 09:48:24'),(132,'DJ Equipment','AV Equipment',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'Admin User','2026-04-12 10:41:52',NULL,NULL,'2026-04-06 05:29:31','2026-04-12 10:41:52'),(133,'Podium','AV Equipment',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'Admin User','2026-04-11 09:48:24',NULL,NULL,'2026-04-06 05:29:31','2026-04-11 09:48:24'),(134,'Big Bee Eye','AV Equipment',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Assigned','KICC',NULL,NULL,'Admin User','2026-04-12 01:28:04',NULL,NULL,'2026-04-06 05:29:31','2026-04-12 01:28:04'),(135,'Small Bee Eye','AV Equipment',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Assigned','Warehouse',NULL,NULL,'Admin User','2026-04-08 23:55:06',NULL,NULL,'2026-04-06 05:29:31','2026-04-11 08:10:24'),(136,'Fog Machine','AV Equipment',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'Admin User','2026-04-11 09:48:23',NULL,NULL,'2026-04-06 05:29:31','2026-04-11 09:48:23'),(137,'Low Fog','AV Equipment',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'Admin User','2026-04-11 09:48:23',NULL,NULL,'2026-04-06 05:29:31','2026-04-11 09:48:23'),(138,'Strip Lights','AV Equipment',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Assigned','KICC',NULL,NULL,'Admin User','2026-04-12 01:28:04',NULL,NULL,'2026-04-06 05:29:31','2026-04-12 01:28:04'),(139,'200m 3-Face Cable','AV Equipment',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Cleaning','Cleaning Bay',NULL,NULL,'Admin User','2026-04-12 00:07:29',NULL,NULL,'2026-04-06 05:29:31','2026-04-12 00:07:29'),(140,'Scanners','AV Equipment',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'Admin User','2026-04-11 09:48:24',NULL,NULL,'2026-04-06 05:29:31','2026-04-11 09:48:24'),(141,'AC Unit','AV Equipment',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'Admin User','2026-04-12 01:51:41',NULL,NULL,'2026-04-06 05:29:31','2026-04-12 01:51:41'),(142,'Braces 0.5m','AV Equipment',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Under Repair','Repair Workshop',NULL,NULL,'Admin User','2026-04-12 10:11:21',NULL,NULL,'2026-04-06 05:29:31','2026-04-12 10:11:21'),(143,'Braces 0.3m','AV Equipment',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Assigned','KICC',NULL,NULL,'Admin User','2026-04-12 01:28:04',NULL,NULL,'2026-04-06 05:29:31','2026-04-12 01:28:04'),(144,'Stand 0.5m','AV Equipment',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Assigned','Warehouse',NULL,NULL,'Admin User','2026-04-08 23:55:06',NULL,NULL,'2026-04-06 05:29:32','2026-04-08 23:55:06'),(145,'Stand 0.3m','AV Equipment',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Assigned','Warehouse',NULL,NULL,'Admin User','2026-04-08 23:55:06',NULL,NULL,'2026-04-06 05:29:32','2026-04-08 23:55:06'),(146,'Chain Block','AV Equipment',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Under Repair','Repair Workshop',NULL,NULL,'Admin User','2026-04-12 10:41:52',NULL,NULL,'2026-04-06 05:29:32','2026-04-12 10:41:52'),(147,'Floodlights','AV Equipment',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'Admin User','2026-04-11 09:48:23',NULL,NULL,'2026-04-06 05:29:32','2026-04-11 09:48:23'),(148,'Chandelier','AV Equipment',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'Admin User','2026-04-12 10:42:14',NULL,NULL,'2026-04-06 05:29:32','2026-04-12 10:42:14'),(149,'AV Matrix','AV Equipment',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Under Repair','Repair Workshop',NULL,NULL,'Admin User','2026-04-12 01:24:21',NULL,NULL,'2026-04-06 05:29:32','2026-04-12 01:24:21'),(150,'Laptop','AV Equipment',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'Admin User','2026-04-11 09:48:23',NULL,NULL,'2026-04-06 05:29:32','2026-04-11 09:48:23'),(151,'GenSet 110KVA','AV Equipment',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'Admin User','2026-04-11 09:48:23',NULL,NULL,'2026-04-06 05:29:32','2026-04-11 09:48:23'),(152,'Rectangular Table Cloth Black','Fabric - Table Cloths',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Under Repair','Repair Workshop',NULL,NULL,'Admin User','2026-04-12 01:24:21',NULL,NULL,'2026-04-06 05:29:32','2026-04-12 01:24:21'),(153,'Rectangular Table Cloth Green','Fabric - Table Cloths',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'Admin User','2026-04-12 01:51:41',NULL,NULL,'2026-04-06 05:29:32','2026-04-12 01:51:41'),(154,'Rectangular Table Cloth Red','Fabric - Table Cloths',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'Admin User','2026-04-12 10:11:56',NULL,NULL,'2026-04-06 05:29:32','2026-04-12 10:11:56'),(155,'Rectangular Table Cloth White','Fabric - Table Cloths',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'Admin User','2026-04-11 23:23:53',NULL,NULL,'2026-04-06 05:29:32','2026-04-11 23:23:53'),(156,'Velvet Round Table Cloth Red','Fabric - Table Cloths',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:32','2026-04-06 05:29:32'),(157,'Velvet Round Table Cloth Green','Fabric - Table Cloths',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:32','2026-04-06 05:29:32'),(158,'Velvet Round Table Cloth Black','Fabric - Table Cloths',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:32','2026-04-06 05:29:32'),(159,'Round Table Cloth Red','Fabric - Table Cloths',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'Admin User','2026-04-12 10:11:56',NULL,NULL,'2026-04-06 05:29:32','2026-04-12 10:11:56'),(160,'Round Table Cloth Green','Fabric - Table Cloths',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Assigned','KICC',NULL,NULL,'Admin User','2026-04-12 01:28:04',NULL,NULL,'2026-04-06 05:29:32','2026-04-12 01:28:04'),(161,'Round Table Cloth Black','Fabric - Table Cloths',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'Admin User','2026-04-11 23:45:03',NULL,NULL,'2026-04-06 05:29:32','2026-04-11 23:45:03'),(162,'Round Table Cloth White','Fabric - Table Cloths',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'Admin User','2026-04-12 10:42:14',NULL,NULL,'2026-04-06 05:29:32','2026-04-12 10:42:14'),(163,'Round Table Cloth Gold','Fabric - Table Cloths',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Cleaning','Cleaning Bay',NULL,NULL,'Admin User','2026-04-11 23:45:04',NULL,NULL,'2026-04-06 05:29:32','2026-04-11 23:45:04'),(164,'Skirting Red','Fabric - Table Cloths',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:32','2026-04-06 05:29:32'),(165,'Skirting White','Fabric - Table Cloths',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:32','2026-04-06 05:29:32'),(166,'Skirting Green','Fabric - Table Cloths',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:32','2026-04-06 05:29:32'),(167,'Spandex White','Fabric - Table Cloths',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:32','2026-04-06 05:29:32'),(168,'Spandex Black','Fabric - Table Cloths',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:32','2026-04-06 05:29:32'),(169,'Spandex Red','Fabric - Table Cloths',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:32','2026-04-06 05:29:32'),(170,'Spandex Green','Fabric - Table Cloths',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Available','Warehouse',NULL,NULL,'System','2026-04-06 05:29:26',NULL,NULL,'2026-04-06 05:29:33','2026-04-06 05:29:33'),(171,'Underlay / Molton','Fabric - Table Cloths',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Assigned','KICC',NULL,NULL,'Admin User','2026-04-12 01:28:04',NULL,NULL,'2026-04-06 05:29:33','2026-04-12 01:28:04'),(172,'Beam','Flooring','Huawei',NULL,NULL,'2026-04-13',60000000.00,'power','300cm','45kg','Available','Warehouse',NULL,NULL,'Admin User','2026-04-11 23:36:45',NULL,NULL,'2026-04-11 23:36:45','2026-04-11 23:36:45'),(173,'Beam','Flooring','Huawei',NULL,NULL,'2026-04-13',60000000.00,'power','300cm','45kg','Available','Warehouse',NULL,NULL,'Admin User','2026-04-11 23:36:55',NULL,NULL,'2026-04-11 23:36:55','2026-04-11 23:36:55'),(174,'Beam','Flooring','Huawei',NULL,NULL,'2026-04-13',60000000.00,'power','300cm','45kg','Available','Warehouse',NULL,NULL,'Admin User','2026-04-11 23:37:09',NULL,NULL,'2026-04-11 23:37:09','2026-04-11 23:37:09'),(175,'Beam','Flooring','Huawei',NULL,NULL,'2026-04-13',60000000.00,'power','300cm','45kg','Available','Warehouse',NULL,NULL,'Admin User','2026-04-11 23:37:33',NULL,NULL,'2026-04-11 23:37:33','2026-04-11 23:37:33'),(176,'Beam','Flooring','Huawei',NULL,NULL,'2026-04-13',60000000.00,'power','300cm','45kg','Available','Warehouse',NULL,NULL,'Admin User','2026-04-11 23:37:46',NULL,NULL,'2026-04-11 23:37:46','2026-04-11 23:37:46'),(177,'Beam','Flooring','Huawei',NULL,NULL,'2026-04-13',60000000.00,'power','300cm','45kg','Available','Warehouse',NULL,NULL,'Admin User','2026-04-11 23:39:11',NULL,NULL,'2026-04-11 23:39:11','2026-04-11 23:39:11');
/*!40000 ALTER TABLE `items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_03_31_031910_create_items_table',1),(5,'2026_03_31_035402_create_activity_logs_table',1),(6,'2026_03_31_044752_create_permission_tables',1),(7,'2026_03_31_183215_create_assignments_table',1),(8,'2026_03_31_183219_create_checklists_table',1),(9,'2026_03_31_183219_create_repairs_table',1),(10,'2026_04_02_201715_fix_activity_logs_foreign_keys',1),(11,'2026_04_06_085507_add_image_to_items_table',2),(12,'2026_04_07_022806_create_events_table',3),(13,'2026_04_07_022808_create_event_items_table',4),(14,'2026_04_07_022809_create_event_item_images_table',4),(15,'2026_04_11_055102_create_item_images_table',5),(16,'2026_04_11_070000_add_return_fields_to_event_items_table',6),(17,'2026_04_11_080000_create_event_staff_table',7),(18,'2026_04_11_100000_create_categories_table',8),(19,'2026_04_12_000000_add_advanced_fields_to_items_table',9);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_permissions`
--

DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
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
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_roles` (
  `role_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
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
INSERT INTO `model_has_roles` VALUES (1,'App\\Models\\User',1),(2,'App\\Models\\User',2),(2,'App\\Models\\User',3),(2,'App\\Models\\User',4),(1,'App\\Models\\User',5);
/*!40000 ALTER TABLE `model_has_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
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
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (1,'view inventory','web','2026-04-03 03:49:09','2026-04-03 03:49:09'),(2,'create inventory','web','2026-04-03 03:49:09','2026-04-03 03:49:09'),(3,'edit inventory','web','2026-04-03 03:49:09','2026-04-03 03:49:09'),(4,'delete inventory','web','2026-04-03 03:49:09','2026-04-03 03:49:09'),(5,'view reports','web','2026-04-03 03:49:09','2026-04-03 03:49:09'),(6,'generate reports','web','2026-04-03 03:49:09','2026-04-03 03:49:09'),(7,'assign items','web','2026-04-03 03:49:09','2026-04-03 03:49:09'),(8,'return items','web','2026-04-03 03:49:09','2026-04-03 03:49:09'),(9,'manage users','web','2026-04-03 03:49:09','2026-04-03 03:49:09');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `repairs`
--

DROP TABLE IF EXISTS `repairs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `repairs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `item_id` bigint unsigned NOT NULL,
  `repair_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estimated_cost` decimal(10,2) DEFAULT NULL,
  `actual_cost` decimal(10,2) DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pending',
  `started_at` date DEFAULT NULL,
  `completed_at` date DEFAULT NULL,
  `technician_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `repairs_item_id_foreign` (`item_id`),
  CONSTRAINT `repairs_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `repairs`
--

LOCK TABLES `repairs` WRITE;
/*!40000 ALTER TABLE `repairs` DISABLE KEYS */;
INSERT INTO `repairs` VALUES (1,149,'Post-Event Damage','Damage found on return from event: SOUL FEST',0.00,NULL,'Pending','2026-04-12',NULL,NULL,NULL,'2026-04-12 01:24:21','2026-04-12 01:24:21'),(2,152,'Post-Event Damage','Damage found on return from event: SOUL FEST',0.00,NULL,'Pending','2026-04-12',NULL,NULL,NULL,'2026-04-12 01:24:21','2026-04-12 01:24:21'),(3,142,'Post-Event Damage','Damage found on return from event: WRC NAIVASHA',0.00,NULL,'Pending','2026-04-12',NULL,NULL,NULL,'2026-04-12 10:11:21','2026-04-12 10:11:21'),(4,146,'Post-Event Damage','Damage found on return from event: OKTOBA FEST',0.00,NULL,'Pending','2026-04-12',NULL,NULL,NULL,'2026-04-12 10:41:52','2026-04-12 10:41:52');
/*!40000 ALTER TABLE `repairs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_has_permissions`
--

DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
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
INSERT INTO `role_has_permissions` VALUES (1,1),(2,1),(3,1),(4,1),(5,1),(6,1),(7,1),(8,1),(9,1),(1,2),(2,2),(3,2),(5,2),(6,2),(7,2),(8,2);
/*!40000 ALTER TABLE `role_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Admin','web','2026-04-03 03:49:09','2026-04-03 03:49:09'),(2,'Manager','web','2026-04-03 03:49:09','2026-04-03 03:49:09');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
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
INSERT INTO `sessions` VALUES ('gUI7MwGaJvXmqWhgoqiHSQbzLbBMHMs7fVSTopw0',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiWUxpRWtBaTIzaVIxTkRwQmVKaVlYa2ZXVkhjNlBtOTNRMlpjTjNUNyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fX0=',1775466216),('Mnwp4v7z0bF5SV2S7huQeYPZJ86RwqNczQX36176',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiQW5zSERkS3RHVnBESjZkdldPeDNWdnJyYWhlQnVCV2MzQXlKeWp0diI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czoyMToiaHR0cDovL2xvY2FsaG9zdDo4MDAwIjt9czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1775462746);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Admin User','admin@example.com','2026-04-03 03:49:10','$2y$12$uUY.HcubHmVWHiPjVdOmTOeWL31j6qJHK.7NUtBb7ptiXYYtRkf.K','GFdiysT8QAOUkLAco1E7LnlrfJzowRPtILqlFkc6470UrObbW5TLiWCKVyHs','2026-04-03 03:49:10','2026-04-03 03:49:10'),(2,'Manager User','manager@example.com','2026-04-03 03:49:10','$2y$12$rDI0gIjfje9hFssZNe7VCeBu7EkeWHvG3clg8XrSqiO2vMS1NCFjq',NULL,'2026-04-03 03:49:10','2026-04-03 03:49:10'),(3,'Alice Johnson','alice@example.com','2026-04-03 03:49:11','$2y$12$pY3cHZohyNMgQZtqVifMm.u5rIHCDGJv4c8rr5Ehjn41BQDQs6mtm',NULL,'2026-04-03 03:49:11','2026-04-03 03:49:11'),(4,'Bob Smith','bob@example.com','2026-04-03 03:49:11','$2y$12$WuQiV0q22sJ8jnVDDIcEbud5YVX82cUC1l02LzO/TRqPOcP/3ABtC',NULL,'2026-04-03 03:49:11','2026-04-03 03:49:11'),(5,'Carol White','carol@example.com','2026-04-03 03:49:11','$2y$12$vj5kMK4LjRmqK0wMDYJAVuU.Jgnt.DRN1WaFbgMlq43qAULQtYsiC',NULL,'2026-04-03 03:49:11','2026-04-03 03:49:11'),(6,'James Kamau','j.kamau@greyapple.co.ke',NULL,'$2y$12$e1gzGE5Ea7J.FWV2seJHU.LFnug9Obi6iljJEJbC3wXL0RaMfKLXq',NULL,'2026-04-11 10:14:08','2026-04-11 10:14:08'),(7,'Mary Wanjiku','m.wanjiku@greyapple.co.ke',NULL,'$2y$12$nexjqMX3dLLoMQA2iRUkJ.ow.kCcI5MIW9L2MqcCb35udD.8O1YXu',NULL,'2026-04-11 10:14:08','2026-04-11 10:14:08'),(8,'Peter Ochieng','p.ochieng@greyapple.co.ke',NULL,'$2y$12$8gMa92.9KAOhRlpcCaO1HOzlYkGWdEVYCZjxQXZMSllQzYLxMildO',NULL,'2026-04-11 10:14:08','2026-04-11 10:14:08'),(9,'Grace Akinyi','g.akinyi@greyapple.co.ke',NULL,'$2y$12$6WX1i.ZIh6iu8Dft4ACpkuDokydG4MLTshHqJzTkXdfXdFSnstalK',NULL,'2026-04-11 10:14:09','2026-04-11 10:14:09'),(10,'David Mwangi','d.mwangi@greyapple.co.ke',NULL,'$2y$12$PAovfzPYrLXztCgDZPDzBOjhJb9nyTMs09ZroyPftXik5wozwj5Bu',NULL,'2026-04-11 10:14:09','2026-04-11 10:14:09'),(11,'Sarah Njeri','s.njeri@greyapple.co.ke',NULL,'$2y$12$LXnYB2NHsJlx/bqnkTU9r.m85E39/i3NcEIbjFEYWW//heBMvjgXG',NULL,'2026-04-11 10:14:10','2026-04-11 10:14:10'),(12,'John Otieno','j.otieno@greyapple.co.ke',NULL,'$2y$12$qY3UMksRM2zs.jlOma34.umSU5QF5BgamRGUahg/ieR4hBHg7mcgS',NULL,'2026-04-11 10:14:10','2026-04-11 10:14:10'),(13,'Lucy Wambui','l.wambui@greyapple.co.ke',NULL,'$2y$12$GcKYDMdsUQWmFdnnqhmxTOpCeutSDbi14U/j1jlD9Hlyk9pxxu7NW',NULL,'2026-04-11 10:14:11','2026-04-11 10:14:11'),(14,'Michael Kipchoge','m.kipchoge@greyapple.co.ke',NULL,'$2y$12$xChhQr1WcIGWxdNtv8xWNOmKo/ERGDlbUJxJ.Pu7Fw7zNLGtT6ejq',NULL,'2026-04-11 10:14:11','2026-04-11 10:14:11'),(15,'Anne Chebet','a.chebet@greyapple.co.ke',NULL,'$2y$12$pXcc3zyCx3czZWH9J7oICOTx3GjPwqqWLDpXRqVkkodzn.eM6Uwhu',NULL,'2026-04-11 10:14:12','2026-04-11 10:14:12'),(16,'Daniel Mutua','d.mutua@greyapple.co.ke',NULL,'$2y$12$zoGpe9DQEvWoat/h6j6QAOtDrzLYfYieCbdOv1DHPDF1YyplVCpba',NULL,'2026-04-11 10:14:12','2026-04-11 10:14:12'),(17,'Elizabeth Nyambura','e.nyambura@greyapple.co.ke',NULL,'$2y$12$8jpWTN88z2kYyIDGX3Iz5Oi1WtwTdRa.uKNdE7jiQ4yGbHw9kqJqq',NULL,'2026-04-11 10:14:12','2026-04-11 10:14:12'),(18,'Patrick Korir','p.korir@greyapple.co.ke',NULL,'$2y$12$G.1dOF4J213Loo3fXPlpl.UqEV5SR7LPrUZOjU2BEM8k2dqh3VuqW',NULL,'2026-04-11 10:14:13','2026-04-11 10:14:13'),(19,'Jane Wangari','j.wangari@greyapple.co.ke',NULL,'$2y$12$4hguaBZXkOchy.EPI.ZjGeOTHaM1EPGd.fvSD.epUWQzSIp1tuW0u',NULL,'2026-04-11 10:14:13','2026-04-11 10:14:13'),(20,'Francis Kibet','f.kibet@greyapple.co.ke',NULL,'$2y$12$hGwuiguIWMU4N1cLw6YKA.pVIe2pvShPZFX0FE91OrBkA5/wo.tWe',NULL,'2026-04-11 10:14:14','2026-04-11 10:14:14'),(21,'Rose Wanjiru','r.wanjiru@greyapple.co.ke',NULL,'$2y$12$TqAlY3sjkx.LM19f/HgIYOnWFcZO10ZhqangDbyMAs0c1r8fijlla',NULL,'2026-04-11 10:14:14','2026-04-11 10:14:14'),(22,'Samuel Omondi','s.omondi@greyapple.co.ke',NULL,'$2y$12$eh4t0KJuBXdMFAQN/r1x4u8MuPOKsBbw/u3Uwo8si3Otr1ZdK.aVm',NULL,'2026-04-11 10:14:14','2026-04-11 10:14:14'),(23,'Catherine Muthoni','c.muthoni@greyapple.co.ke',NULL,'$2y$12$eVjR.R0veSvIzblKe4scC.Fsyu/AJOxrZCz7pJYUfTrSpMwiCnS/u',NULL,'2026-04-11 10:14:15','2026-04-11 10:14:15'),(24,'Joseph Karanja','j.karanja@greyapple.co.ke',NULL,'$2y$12$NU/EFlb6Dy2ze.bHbyXLYOi0DAcaeNG5MOHRnVkk72srBjO9dUvRK',NULL,'2026-04-11 10:14:15','2026-04-11 10:14:15'),(25,'Rebecca Njoki','r.njoki@greyapple.co.ke',NULL,'$2y$12$vrSzEQkQYVzJyoGi9KoizOGMOs2Bjm.BPYxah9IncRVtDRNu0WF9S',NULL,'2026-04-11 10:14:16','2026-04-11 10:14:16');
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

-- Dump completed on 2026-04-13  5:07:49
