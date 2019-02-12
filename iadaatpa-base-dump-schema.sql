CREATE DATABASE  IF NOT EXISTS `iadaatpa` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `iadaatpa`;
-- MySQL dump 10.13  Distrib 5.7.23, for Win32 (AMD64)
--
-- Host: 127.0.0.1    Database: iadaatpa
-- ------------------------------------------------------
-- Server version	5.7.17

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
-- Table structure for table `accounts`
--

DROP TABLE IF EXISTS `accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `accounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `groupid` int(11) NOT NULL DEFAULT '1',
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `name` varchar(45) NOT NULL,
  `logo` varchar(45) DEFAULT NULL,
  `adminid` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `expired` datetime DEFAULT NULL,
  `deleted` datetime DEFAULT NULL,
  `apitoken` varchar(45) DEFAULT NULL,
  `cache` tinyint(4) DEFAULT '1',
  `activiatm` tinyint(4) DEFAULT '0',
  `activiatmusername` varchar(256) DEFAULT NULL,
  `activiatmpassword` varchar(500) DEFAULT NULL,
  `token` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `accounts`
--

LOCK TABLES `accounts` WRITE;
/*!40000 ALTER TABLE `accounts` DISABLE KEYS */;
INSERT INTO `accounts` VALUES (1,3,1,'Admin',NULL,1,'2018-02-21 08:43:50',NULL,NULL,'4qNabn0xT4Wx40PFu',1,0,NULL,NULL,NULL);
/*!40000 ALTER TABLE `accounts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `asynchronousrequests`
--

DROP TABLE IF EXISTS `asynchronousrequests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `asynchronousrequests` (
  `id` int(126) NOT NULL AUTO_INCREMENT,
  `guid` varchar(124) NOT NULL,
  `methodid` tinyint(3) DEFAULT '1',
  `consumeraccountid` varchar(45) NOT NULL,
  `suppliertoken` varchar(45) DEFAULT NULL,
  `supplieraccountid` tinyint(3) NOT NULL,
  `status` int(3) DEFAULT NULL,
  `enginename` varchar(45) DEFAULT NULL,
  `enginecustomid` varchar(45) DEFAULT NULL,
  `src` varchar(3) DEFAULT NULL,
  `trg` varchar(3) DEFAULT NULL,
  `domain` varchar(45) DEFAULT NULL,
  `text` longtext,
  `translation` longtext,
  `translationtime` varchar(45) DEFAULT NULL,
  `requesttime` varchar(45) DEFAULT NULL,
  `retry` tinyint(1) DEFAULT '0',
  `error` varchar(45) DEFAULT NULL,
  `filetype` varchar(16) DEFAULT NULL,
  `supplierguid` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=769 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `asynchronousrequests`
--

LOCK TABLES `asynchronousrequests` WRITE;
/*!40000 ALTER TABLE `asynchronousrequests` DISABLE KEYS */;
/*!40000 ALTER TABLE `asynchronousrequests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `supplieraccountid` int(6) NOT NULL,
  `engineid` int(6) DEFAULT NULL,
  `segments` longtext,
  `translatedsegments` longtext,
  `time` datetime DEFAULT NULL,
  `domainid` int(4) DEFAULT NULL,
  `src` varchar(16) DEFAULT NULL,
  `trg` varchar(16) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18597 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `domaindata`
--

DROP TABLE IF EXISTS `domaindata`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `domaindata` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `domainid` int(11) NOT NULL,
  `accountid` int(11) DEFAULT NULL,
  `segments` longblob,
  `added` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=179 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `domaindata`
--

LOCK TABLES `domaindata` WRITE;
/*!40000 ALTER TABLE `domaindata` DISABLE KEYS */;
/*!40000 ALTER TABLE `domaindata` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `domainmodels`
--

DROP TABLE IF EXISTS `domainmodels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `domainmodels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `accountid` int(11) DEFAULT NULL,
  `model` longblob,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=372 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `domainmodels`
--

LOCK TABLES `domainmodels` WRITE;
/*!40000 ALTER TABLE `domainmodels` DISABLE KEYS */;
/*!40000 ALTER TABLE `domainmodels` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `domains`
--

DROP TABLE IF EXISTS `domains`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `domains` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `accountid` int(11) DEFAULT '0',
  `name` varchar(45) NOT NULL,
  `src` varchar(12) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=100 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `domains`
--

LOCK TABLES `domains` WRITE;
/*!40000 ALTER TABLE `domains` DISABLE KEYS */;
/*!40000 ALTER TABLE `domains` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `engines`
--

DROP TABLE IF EXISTS `engines`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `engines` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  `accountid` int(11) NOT NULL,
  `customid` varchar(255) DEFAULT NULL,
  `trg` varchar(16) DEFAULT NULL,
  `src` varchar(16) DEFAULT NULL,
  `description` varchar(258) DEFAULT NULL,
  `domainid` tinyint(1) DEFAULT NULL,
  `type` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `deleted` datetime DEFAULT NULL,
  `online` tinyint(1) DEFAULT NULL,
  `ter` varchar(2) DEFAULT NULL,
  `bleu` varchar(2) DEFAULT NULL,
  `fmeasure` varchar(2) DEFAULT NULL,
  `trainingwordcount` varchar(8) DEFAULT NULL,
  `costperword` varchar(8) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=816 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `engines`
--

LOCK TABLES `engines` WRITE;
/*!40000 ALTER TABLE `engines` DISABLE KEYS */;
/*!40000 ALTER TABLE `engines` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `groups`
--

DROP TABLE IF EXISTS `groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `groups`
--

LOCK TABLES `groups` WRITE;
/*!40000 ALTER TABLE `groups` DISABLE KEYS */;
INSERT INTO `groups` VALUES (1,'Supplier'),(2,'Consumer'),(3,'Administrator');
/*!40000 ALTER TABLE `groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `languages`
--

DROP TABLE IF EXISTS `languages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `languages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(16) NOT NULL,
  `name` varchar(45) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  UNIQUE KEY `code_UNIQUE` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=112 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `languages`
--

LOCK TABLES `languages` WRITE;
/*!40000 ALTER TABLE `languages` DISABLE KEYS */;
INSERT INTO `languages` VALUES (1,'zh-TW','Chinese (Taiwan)'),(2,'ab','Albanian'),(3,'ar','Arabic'),(4,'ar-EG','Arabic (Egypt)'),(5,'ar-SA','Arabic (Saudi Arabia)'),(6,'az','Azerbaijani'),(7,'az-AZ','Azerbaijani'),(9,'be-BE','Belarusian'),(10,'be-BY','Belarusian (Belarus)'),(11,'bg','Bulgarian'),(12,'bn','Bangla'),(13,'ca','Catalan'),(14,'ca-ES','Catalan'),(15,'cs','Czech'),(16,'cv','Chuvash'),(17,'da','Danish'),(18,'de','German'),(19,'de-DE','German (Germany)'),(20,'el','Greek'),(21,'en','English'),(22,'en-CA','English (Canada)'),(23,'en-GB','English (UK)'),(24,'en-US','English (USA)'),(25,'es','Spanish'),(26,'es-EM','Spanish (International)'),(27,'es-ES','Spanish (Spain)'),(28,'es-MX','Spanish (Mexican)'),(29,'es-US','Spanish (American)'),(30,'et','Estonian	'),(31,'et-EE','Estonian (Estonia)'),(32,'fa','Persian'),(33,'fi','Finnish'),(34,'fil','Filipino'),(35,'fr','French'),(36,'fr-CA','French (Canadian)'),(37,'fr-FR','French (France)'),(38,'ga-IE','Irish (Ireland)'),(39,'gd-IE','Irish'),(40,'gu','Gujarati'),(41,'he','Hebrew'),(42,'he-IL','Hebrew (Israel)'),(43,'hi','Hindi'),(44,'hr','Croatian'),(45,'hu','Hungarian	'),(46,'hy','Armenian	'),(47,'hy-AM','Armenian (Armenia)'),(48,'id','Indonesian'),(49,'it','Italian'),(50,'it-IT','Italian (Italy)'),(51,'ja','Japanese'),(52,'ja-JA','Japanese'),(53,'ja-JP','Japanese'),(54,'ka','Georgian'),(55,'ka-GE','Georgian (Georgia)'),(56,'kk','Kazakh'),(57,'kk-KZ','Kazakh'),(58,'kn','Kannada'),(59,'ko','Korean'),(60,'ko-KR','Korean (Korea)'),(61,'lt','Lithuanian'),(62,'lt-LT','Lithuanian'),(63,'lv','Latvian'),(64,'lv-LV','Latvian (Latvia)'),(65,'mr','Marathi '),(66,'mt','Maltese'),(67,'my','Burmese'),(68,'ne','Nepali'),(69,'nl','Dutch'),(70,'nl-NL','Dutch (Netherlands)'),(71,'no','Norwegian'),(72,'pa','Punjabi'),(73,'pl','Polish'),(74,'pl-PL','Polish (Poland)'),(75,'pt','Portuguese'),(76,'pt-BR','Portuguese (Brazil)'),(77,'qu','Quichua'),(78,'ro','Romanian'),(79,'ru','Russian'),(80,'ru-RU','Russian (Russia)'),(81,'si','Sinhala'),(82,'sk','Slovak'),(83,'sl','Slovenian'),(84,'sq','Albanian'),(85,'sv','Swedish'),(86,'sw','Swahili'),(87,'ta','Tamil'),(88,'te','Telugu'),(89,'tg','Tajik'),(91,'tg-TG','Tajik'),(92,'th','Thai'),(93,'tk','Turkmen'),(94,'tk-TM','Turkmen'),(95,'tr','Turkish'),(96,'tr-TR','Turkish'),(97,'udm','Udmurt'),(98,'uk','Ukrainian'),(99,'uk-UA','Ukrainian'),(100,'ur','Urdu'),(101,'uz','Uzbek'),(103,'uz-UZ','Uzbek'),(104,'vi','Vietnamese'),(105,'zh-CN','Chinese (Simplified)'),(106,'zh-HANT','Chinese (Traditional)'),(107,'gl-ES','Galician (Spain)'),(108,'eu','Basque'),(109,'ca-valencia','Catalan (Valencian)'),(110,'gl','Galician'),(111,'ga','Irish');
/*!40000 ALTER TABLE `languages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `log`
--

DROP TABLE IF EXISTS `log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `accountid` varchar(16) NOT NULL,
  `userid` varchar(16) NOT NULL,
  `action` int(11) NOT NULL,
  `comment` varchar(128) DEFAULT NULL,
  `differences` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3877 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `log`
--

LOCK TABLES `log` WRITE;
/*!40000 ALTER TABLE `log` DISABLE KEYS */;
INSERT INTO `log` VALUES (3874,'2019-02-12 10:30:55','14=>1','23=>1',25,'1','{\"password\":\"password => new password\"}'),(3875,'2019-02-12 10:30:59','14=>1','23=>1',21,NULL,NULL),(3876,'2019-02-12 10:31:17','1','1',20,NULL,NULL);
/*!40000 ALTER TABLE `log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `metadata`
--

DROP TABLE IF EXISTS `metadata`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `metadata` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `engineid` int(11) NOT NULL,
  `variable` varchar(45) NOT NULL,
  `value` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `metadata`
--

LOCK TABLES `metadata` WRITE;
/*!40000 ALTER TABLE `metadata` DISABLE KEYS */;
/*!40000 ALTER TABLE `metadata` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `relations`
--

DROP TABLE IF EXISTS `relations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `relations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `consumeraccountid` int(11) DEFAULT NULL,
  `supplieraccountid` int(11) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  `supplierapitoken` varchar(45) DEFAULT NULL,
  `apitoken` varchar(500) DEFAULT NULL,
  `username` varchar(500) DEFAULT NULL,
  `password` varchar(500) DEFAULT NULL,
  `token` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `relations`
--

LOCK TABLES `relations` WRITE;
/*!40000 ALTER TABLE `relations` DISABLE KEYS */;
/*!40000 ALTER TABLE `relations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `requestlog`
--

DROP TABLE IF EXISTS `requestlog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `requestlog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `methodid` int(2) NOT NULL,
  `consumeraccountid` int(6) DEFAULT NULL,
  `supplieraccountid` int(6) DEFAULT NULL,
  `engineid` int(6) DEFAULT NULL,
  `timein` datetime DEFAULT NULL,
  `timeout` datetime DEFAULT NULL,
  `timems` varchar(24) DEFAULT NULL,
  `httpcode` int(2) DEFAULT NULL,
  `src` varchar(8) DEFAULT NULL,
  `trg` varchar(8) DEFAULT NULL,
  `request` longtext,
  `response` longtext,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=51565 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `requestlog`
--

LOCK TABLES `requestlog` WRITE;
/*!40000 ALTER TABLE `requestlog` DISABLE KEYS */;
/*!40000 ALTER TABLE `requestlog` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `statisticssummary`
--

DROP TABLE IF EXISTS `statisticssummary`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `statisticssummary` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `consumeraccountid` int(3) NOT NULL,
  `supplieraccountid` int(3) NOT NULL,
  `methodid` int(1) DEFAULT NULL,
  `engineid` int(3) NOT NULL,
  `requestcount` int(16) NOT NULL DEFAULT '1',
  `wordcount` int(16) NOT NULL,
  `time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1911 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `statisticssummary`
--

LOCK TABLES `statisticssummary` WRITE;
/*!40000 ALTER TABLE `statisticssummary` DISABLE KEYS */;
/*!40000 ALTER TABLE `statisticssummary` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `statisticstemporary`
--

DROP TABLE IF EXISTS `statisticstemporary`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `statisticstemporary` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `consumeraccountid` int(3) NOT NULL,
  `supplieraccountid` int(3) NOT NULL,
  `methodid` int(1) DEFAULT NULL,
  `engineid` int(3) NOT NULL,
  `requestcount` int(16) NOT NULL DEFAULT '1',
  `wordcount` int(16) NOT NULL,
  `time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=40592 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `statisticstemporary`
--

LOCK TABLES `statisticstemporary` WRITE;
/*!40000 ALTER TABLE `statisticstemporary` DISABLE KEYS */;
/*!40000 ALTER TABLE `statisticstemporary` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `urlconfig`
--

DROP TABLE IF EXISTS `urlconfig`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `urlconfig` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `methodid` int(11) NOT NULL,
  `accountid` int(11) NOT NULL,
  `type` varchar(12) DEFAULT 'POST',
  `urlendpoint` varchar(500) DEFAULT NULL,
  `parameters` varchar(500) DEFAULT NULL,
  `header` varchar(500) DEFAULT NULL,
  `response` varchar(500) DEFAULT NULL,
  `request` varchar(500) DEFAULT NULL,
  `callback` varchar(500) DEFAULT NULL,
  `authorization` tinyint(2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=66 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `urlconfig`
--

LOCK TABLES `urlconfig` WRITE;
/*!40000 ALTER TABLE `urlconfig` DISABLE KEYS */;
/*!40000 ALTER TABLE `urlconfig` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `accountid` int(11) DEFAULT NULL,
  `name` varchar(128) NOT NULL,
  `email` varchar(45) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `token` varchar(45) DEFAULT NULL,
  `loginattempts` tinyint(1) DEFAULT NULL,
  `lastlogin` datetime DEFAULT NULL,
  `photo` varchar(45) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`,`name`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  UNIQUE KEY `name_UNIQUE` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=74 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,1,'Admin','admin@mt-hub.eu','$2y$10$YGWpU/VFkwDCQT15oLSI1uPrN9lizh3wYh3HkFdhAORDM8uwgTBkC',NULL,0,'2019-02-12 10:31:15',NULL,'2019-02-28 00:00:00');
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

-- Dump completed on 2019-02-12 10:58:20
