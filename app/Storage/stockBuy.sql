-- MySQL dump 10.13  Distrib 8.0.23, for Linux (x86_64)
--
-- Host: 127.0.0.1    Database: stockBuy
-- ------------------------------------------------------
-- Server version	8.0.23-0ubuntu0.20.04.1

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
-- Table structure for table `MyBalance`
--

DROP TABLE IF EXISTS `MyBalance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `MyBalance` (
  `id` int NOT NULL,
  `budget` float DEFAULT NULL,
  UNIQUE KEY `MyBalance_id_uindex` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `MyBalance`
--

LOCK TABLES `MyBalance` WRITE;
/*!40000 ALTER TABLE `MyBalance` DISABLE KEYS */;
INSERT INTO `MyBalance` VALUES (1,1.06);
/*!40000 ALTER TABLE `MyBalance` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `MyStocks`
--

DROP TABLE IF EXISTS `MyStocks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `MyStocks` (
  `name` varchar(255) DEFAULT NULL,
  `symbol` varchar(255) DEFAULT NULL,
  `price_at_buy` varchar(255) DEFAULT NULL,
  `amount` int DEFAULT NULL,
  `total_price` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `current_price` float DEFAULT NULL,
  `earnings` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `MyStocks`
--

LOCK TABLES `MyStocks` WRITE;
/*!40000 ALTER TABLE `MyStocks` DISABLE KEYS */;
INSERT INTO `MyStocks` VALUES ('Bayport International Holdings Inc','BAYP','2.25',220,'495','https://finnhub.io/api/logo?symbol=BAYP',2.25,0),('GameStop Corp','GME','166.53',1,'166.53','',166.53,0),('Apple Inc','AAPL','132.03',1,'132.03','https://finnhub.io/api/logo?symbol=AAPL',132.03,0),('C3Ai Inc','AI','68.46',3,'205.38','https://finnhub.io/api/logo?symbol=AI',68.46,0);
/*!40000 ALTER TABLE `MyStocks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stocks`
--

DROP TABLE IF EXISTS `stocks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stocks` (
  `name` varchar(255) DEFAULT NULL,
  `symbol` varchar(255) DEFAULT NULL,
  `current_price` float DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stocks`
--

LOCK TABLES `stocks` WRITE;
/*!40000 ALTER TABLE `stocks` DISABLE KEYS */;
INSERT INTO `stocks` VALUES ('Bayport International Holdings Inc','BAYP',2.25,'https://finnhub.io/api/logo?symbol=BAYP'),('GameStop Corp','GME',166.53,''),('Apple Inc','AAPL',132.03,'https://finnhub.io/api/logo?symbol=AAPL'),('C3Ai Inc','AI',68.46,'https://finnhub.io/api/logo?symbol=AI');
/*!40000 ALTER TABLE `stocks` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2021-04-15 10:14:30
