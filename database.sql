CREATE DATABASE  IF NOT EXISTS `media_vault` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `media_vault`;
-- MySQL dump 10.13  Distrib 8.0.46, for Win64 (x86_64)
--
-- Host: localhost    Database: media_vault
-- ------------------------------------------------------
-- Server version	8.0.46

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
-- Table structure for table `media`
--

DROP TABLE IF EXISTS `media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `media` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `genre` varchar(100) DEFAULT NULL,
  `description` text,
  `cover_image` varchar(500) DEFAULT NULL,
  `rating` int DEFAULT NULL,
  `status` enum('gesehen','nicht gesehen') DEFAULT 'nicht gesehen',
  `type` varchar(20) DEFAULT 'Film',
  PRIMARY KEY (`id`),
  CONSTRAINT `media_chk_1` CHECK ((`rating` between 1 and 5))
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `media`
--

LOCK TABLES `media` WRITE;
/*!40000 ALTER TABLE `media` DISABLE KEYS */;
INSERT INTO `media` VALUES (9,'Hangover','Komödie','Sie planten eine Vegas-Junggesellen-Party, die sie nie vergessen würden. Jetzt müssen sie unbedingt herausfinden, was genau schiefgelaufen ist. Wem gehört das Baby im Schrank der Caesars-Palace-Suite? Wie kommt der Tiger ins Badezimmer? Warum fehlt einem der Jungs ein Zahn? Und vor allem, wo ist der Bräutigam? Was die Jungs beim „Draufmachen“ so erleben, ist nichts im Vergleich zu den Kapriolen, die sie nüchtern veranstalten müssen. Sie sind gezwungen, all die schlimmen Entscheidungen der letzten Nacht zu rekonstruieren – eine nach der anderen.','https://image.tmdb.org/t/p/w500/cwoVDVzJuGAD3OAji9OkHENrUB0.jpg',5,'gesehen','Film'),(10,'The Mentalist','','','https://image.tmdb.org/t/p/w500/xxDPSPdMqOYbcIBujPdR5lMtkRo.jpg',5,'gesehen','Serie'),(11,'Modern Family','','Die Mockumentary begleitet die Familien von Jay Pritchett, seiner Tochter Claire Dunphy und seines Sohns Mitchell Pritchett . Während Claire die Rolle der Hausfrau in einer klassischen Familie innehat, ist Jay mit einer viel jüngeren Frau verheiratet, mit der er seinen Stiefsohn erzieht. Mitchell und sein Lebensgefährte haben ein asiatisches Baby adoptiert.','https://image.tmdb.org/t/p/w500/k5Qg5rgPoKdh3yTJJrLtyoyYGwC.jpg',5,'nicht gesehen','Serie');
/*!40000 ALTER TABLE `media` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-06-23  3:03:01
