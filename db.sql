-- MySQL dump 10.13  Distrib 5.5.62, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: Telegram
-- ------------------------------------------------------
-- Server version	5.5.62-0+deb8u1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `info`
--

DROP TABLE IF EXISTS `info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `content` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `info`
--

LOCK TABLES `info` WRITE;
/*!40000 ALTER TABLE `info` DISABLE KEYS */;
INSERT INTO `info` VALUES (1,'button_again','Again'),(2,'info_correct','ÐšÐ°ÐºÐ¾Ð¹ Ð¸Ð½Ð´ÐµÐºÑ Ð¼Ð°ÑÑÑ‹ Ñ‚ÐµÐ»Ð° ÑÑ‡Ð¸Ñ‚Ð°ÐµÑ‚ÑÑ Ð½Ð°Ð¸Ð±Ð¾Ð»ÐµÐµ Ð¿Ñ€Ð¸ÐµÐ¼Ð»ÐµÐ¼Ñ‹Ð¼?<br>ÐžÑ‚Ð²ÐµÑ‚ Ð²ÐµÑ€Ð½Ñ‹Ð¹! 18,5'),(3,'info_inCorrect','ÐšÐ°ÐºÐ¾Ð¹ Ð¸Ð½Ð´ÐµÐºÑ Ð¼Ð°ÑÑÑ‹ Ñ‚ÐµÐ»Ð° ÑÑ‡Ð¸Ñ‚Ð°ÐµÑ‚ÑÑ Ð½Ð°Ð¸Ð±Ð¾Ð»ÐµÐµ Ð¿Ñ€Ð¸ÐµÐ¼Ð»ÐµÐ¼Ñ‹Ð¼?<br>ÐžÑ‚Ð²ÐµÑ‚ Ð½Ðµ Ð²ÐµÑ€Ð½Ñ‹Ð¹! ÐŸÐ¾Ð¿Ñ€Ð¾Ð±ÑƒÐ¹Ñ‚Ðµ ÐµÑ‰Ñ‘ Ñ€Ð°Ð·.'),(4,'start_before','Ð—Ð´Ñ€Ð°Ð²ÑÑ‚Ð²ÑƒÐ¹Ñ‚Ðµ, Ð¿Ñ€ÐµÐ´ÑÑ‚Ð°Ð²ÑŒÑ‚ÐµÑÑŒ, Ð¿Ð¾Ð¶Ð°Ð»ÑƒÐ¹ÑÑ‚Ð°:'),(5,'start_after','ÐŸÑ€Ð¸Ð²ÐµÑ‚ÑÑ‚Ð²ÑƒÑŽ Ð²Ð°Ñ, %name%! ÐŸÐ¾Ð¸Ð³Ñ€Ð°ÐµÐ¼?\nÐ¡Ð¼Ð¾Ñ‚Ñ€Ð¸Ñ‚Ðµ Ð¸Ð½Ñ‚ÐµÑ€ÐµÑÐ½Ñ‹Ðµ Ð²Ð¸Ð´ÐµÐ¾ Ð¾Ñ‚ ÐÐ½Ð´Ñ€ÐµÑ Ð˜ÑÐºÐ¾Ñ€Ð½ÐµÐ²Ð°,\nÐ¾Ñ‚Ð²ÐµÑ‡Ð°Ð¹Ñ‚Ðµ Ð½Ð° Ð²Ð¾Ð¿Ñ€Ð¾ÑÑ‹ Ð½Ð¸Ð¶Ðµ Ð¸ Ð²Ñ‹Ð¸Ð³Ñ€Ñ‹Ð²Ð°Ð¹Ñ‚Ðµ Ð¿Ñ€Ð¸Ð·Ñ‹!'),(6,'',''),(7,'winner_message','ÐŸÐ¾Ð±ÐµÐ´Ð¸Ð» %name%');
/*!40000 ALTER TABLE `info` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `questions`
--

DROP TABLE IF EXISTS `questions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `questions` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `qtitle` varchar(255) DEFAULT NULL,
  `qtext` varchar(1000) DEFAULT NULL,
  `answers` varchar(400) DEFAULT NULL,
  `extra` varchar(255) DEFAULT NULL,
  `picture` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `questions`
--

LOCK TABLES `questions` WRITE;
/*!40000 ALTER TABLE `questions` DISABLE KEYS */;
INSERT INTO `questions` VALUES (1,' ',' ',NULL,NULL,''),(2,' ',' ',NULL,NULL,''),(3,'Ð’Ð¾Ð¿Ñ€Ð¾Ñ â„–1','ÐšÐ°ÐºÐ¾Ð¹ Ð¸Ð½Ð´ÐµÐºÑ Ð¼Ð°ÑÑÑ‹ Ñ‚ÐµÐ»Ð°\nÑÑ‡Ð¸Ñ‚Ð°ÐµÑ‚ÑÑ Ð½Ð°Ð¸Ð±Ð¾Ð»ÐµÐµ Ð¿Ñ€Ð¸ÐµÐ¼Ð»ÐµÐ¼Ñ‹Ð¼?','[]',NULL,'');
/*!40000 ALTER TABLE `questions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` varchar(60) NOT NULL,
  `phone` varchar(35) DEFAULT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `extra` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2019-11-07  0:23:07
