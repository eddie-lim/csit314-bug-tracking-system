CREATE DATABASE  IF NOT EXISTS `test` /*!40100 DEFAULT CHARACTER SET utf8mb4 */;
USE `test`;


-- MariaDB dump 10.17  Distrib 10.4.13-MariaDB, for Win64 (AMD64)
--
-- Host: 127.0.0.1    Database:test 
-- ------------------------------------------------------
-- Server version	10.4.8-MariaDB

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
-- Table structure for table `bug`
--

DROP TABLE IF EXISTS `bug`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bug` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(128) NOT NULL,
  `description` text NOT NULL,
  `bug_status` enum('new','assigned','fixing','pending_review','completed','rejected','reopen') NOT NULL,
  `priority_level` enum('1','2','3') NOT NULL,
  `developer_user_id` int(11) DEFAULT NULL,
  `notes` varchar(1028) DEFAULT NULL,
  `delete_status` enum('enabled','disabled') NOT NULL DEFAULT 'enabled',
  `created_at` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=501 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `bug_action`
--

DROP TABLE IF EXISTS `bug_action`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bug_action` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bug_id` int(11) DEFAULT NULL,
  `action_type` enum('new','assigned','fixing','pending_review','completed','rejected','reopen') DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `delete_status` enum('enabled','disabled') NOT NULL DEFAULT 'enabled',
  `created_at` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;


DROP TABLE IF EXISTS `bug_comment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bug_comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bug_id` int(11) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `delete_status` enum('enabled','disabled') NOT NULL DEFAULT 'enabled',
  `created_at` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;


DROP TABLE IF EXISTS `bug_document`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bug_document` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bug_id` int(11) NOT NULL,
  `path` varchar(2056) NOT NULL,
  `base_url` varchar(2056) NOT NULL,
  `delete_status` enum('enabled','disabled') NOT NULL DEFAULT 'enabled',
  `created_at` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;


DROP TABLE IF EXISTS `bug_tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bug_tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bug_id` int(11) DEFAULT NULL,
  `name` varchar(128) DEFAULT NULL,
  `delete_status` enum('enabled','disabled') NOT NULL DEFAULT 'enabled',
  `created_at` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

CREATE INDEX bug_tag_name_idx ON bug_tag (name);



DROP TABLE IF EXISTS `file_storage_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `file_storage_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `component` varchar(255) NOT NULL,
  `base_url` varchar(1024) NOT NULL,
  `path` varchar(1024) NOT NULL,
  `type` varchar(255) DEFAULT NULL,
  `size` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `upload_ip` varchar(45) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;



--
-- Table structure for table `key_storage_item`
--

DROP TABLE IF EXISTS `key_storage_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `key_storage_item` (
  `key` varchar(128) NOT NULL,
  `value` text NOT NULL,
  `comment` text DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`key`),
  UNIQUE KEY `idx_key_storage_item_key` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;




DROP TABLE IF EXISTS `rbac_auth_assignment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rbac_auth_assignment` (
  `item_name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`item_name`,`user_id`),
  CONSTRAINT `rbac_auth_assignment_ibfk_1` FOREIGN KEY (`item_name`) REFERENCES `rbac_auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `rbac_auth_assignment` WRITE;
/*!40000 ALTER TABLE `rbac_auth_assignment` DISABLE KEYS */;
INSERT INTO `rbac_auth_assignment` VALUES ('administrator','1',1601299386),('developer','10',1601725884),('developer','11',1601725886),('developer','12',1601725887),('developer','13',1601725888),('developer','14',1601725890),('developer','15',1601725891),('developer','16',1601725893),('developer','17',1601725894),('developer','18',1601725895),('developer','4',1601725876),('developer','5',1601725878),('developer','6',1601725879),('developer','7',1601725880),('developer','8',1601725882),('developer','9',1601725883),('reviewer','19',1601725897),('reviewer','20',1601725898),('reviewer','21',1601725900),('reviewer','22',1601725901),('reviewer','23',1601725902),('triager','2',1601299386),('triager','24',1601725904),('triager','25',1601725905),('triager','26',1601725907),('triager','27',1601725908),('triager','28',1601725909),('user','29',1601725911),('user','3',1601299386),('user','30',1601725912),('user','31',1601725914),('user','32',1601725915),('user','33',1601725916),('user','34',1601725918),('user','35',1601725919),('user','36',1601725921),('user','37',1601725922),('user','38',1601725923),('user','39',1601725925),('user','40',1601725926),('user','41',1601725928),('user','42',1601725929),('user','43',1601725931),('user','44',1601725933),('user','45',1601725934),('user','46',1601725936),('user','47',1601725937),('user','48',1601725939),('user','49',1601725940),('user','50',1601725942),('user','51',1601725943),('user','52',1601725944),('user','53',1601725946);
/*!40000 ALTER TABLE `rbac_auth_assignment` ENABLE KEYS */;
UNLOCK TABLES;


DROP TABLE IF EXISTS `rbac_auth_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rbac_auth_item` (
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `type` smallint(6) NOT NULL,
  `description` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `rule_name` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `data` blob DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`name`),
  KEY `idx-auth_item-type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `rbac_auth_item` WRITE;
/*!40000 ALTER TABLE `rbac_auth_item` DISABLE KEYS */;
INSERT INTO `rbac_auth_item` VALUES ('administrator',1,NULL,NULL,NULL,1601299386,1601299386),('developer',1,NULL,NULL,NULL,1601299386,1601299386),('editOwnModel',2,NULL,'ownModelRule',NULL,1601299386,1601299386),('loginToBackend',2,NULL,NULL,NULL,1601299386,1601299386),('reviewer',1,NULL,NULL,NULL,1601299386,1601299386),('triager',1,NULL,NULL,NULL,1601299386,1601299386),('user',1,NULL,NULL,NULL,1601299386,1601299386);
/*!40000 ALTER TABLE `rbac_auth_item` ENABLE KEYS */;
UNLOCK TABLES;


DROP TABLE IF EXISTS `rbac_auth_item_child`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rbac_auth_item_child` (
  `parent` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `child` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`parent`,`child`),
  KEY `child` (`child`),
  CONSTRAINT `rbac_auth_item_child_ibfk_1` FOREIGN KEY (`parent`) REFERENCES `rbac_auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `rbac_auth_item_child_ibfk_2` FOREIGN KEY (`child`) REFERENCES `rbac_auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `rbac_auth_item_child` WRITE;
/*!40000 ALTER TABLE `rbac_auth_item_child` DISABLE KEYS */;
INSERT INTO `rbac_auth_item_child` VALUES ('administrator','loginToBackend'),('administrator','triager'),('administrator','user'),('developer','loginToBackend'),('developer','user'),('reviewer','loginToBackend'),('reviewer','user'),('triager','loginToBackend'),('triager','user'),('user','editOwnModel'),('user','loginToBackend');
/*!40000 ALTER TABLE `rbac_auth_item_child` ENABLE KEYS */;
UNLOCK TABLES;

DROP TABLE IF EXISTS `rbac_auth_rule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rbac_auth_rule` (
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `data` blob DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `rbac_auth_rule` WRITE;
/*!40000 ALTER TABLE `rbac_auth_rule` DISABLE KEYS */;
INSERT INTO `rbac_auth_rule` VALUES ('ownModelRule','O:29:\"common\\rbac\\rule\\OwnModelRule\":3:{s:4:\"name\";s:12:\"ownModelRule\";s:9:\"createdAt\";i:1599030378;s:9:\"updatedAt\";i:1599030378;}',1599030378,1599030378);
/*!40000 ALTER TABLE `rbac_auth_rule` ENABLE KEYS */;
UNLOCK TABLES;

DROP TABLE IF EXISTS `system_audit_trail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_audit_trail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `row_id` int(11) NOT NULL,
  `model` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `controller` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `value` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;


DROP TABLE IF EXISTS `system_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_log` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `level` int(11) DEFAULT NULL,
  `category` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `log_time` double DEFAULT NULL,
  `prefix` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `message` text COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_log_level` (`level`),
  KEY `idx_log_category` (`category`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;


DROP TABLE IF EXISTS `system_login_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_login_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `application` varchar(45) NOT NULL,
  `login_at` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;


DROP TABLE IF EXISTS `timeline_event`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `timeline_event` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `application` varchar(64) NOT NULL,
  `category` varchar(64) NOT NULL,
  `event` varchar(64) NOT NULL,
  `data` text DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;


DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(32) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `auth_key` varchar(32) NOT NULL,
  `access_token` varchar(40) NOT NULL,
  `oauth_client` varchar(255) DEFAULT NULL,
  `oauth_client_user_id` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `account_status` varchar(255) DEFAULT NULL,
  `status` smallint(6) NOT NULL DEFAULT 2,
  `logged_at` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,'webmaster','$2y$13$ZmTDeY4OhOvRnyNlEDlRhuQy3jjeZkfTiorPlomjSWkZxyGqc5lHi','iT3_D9YxRLPbxc12WpjCjdZnBxcfK4Wf','7Cj4d1OTC2eLHUyKcuPioqg07HALPE_9Iti1YXfy',NULL,NULL,'webmaster@example.com',NULL,2,1601553526,1601299366,1601299366),(2,'manager','$2y$13$gR.aI1oqqxhtiDYcDFDiiugTR5dOATKLQ0xW3PzRfSHNiK7J/fUAG','lYrVEstrva498MqRUYqotwSa75WEyWaf','puZVx8bKqB6PgnhY6rdZz7b1521wJrZvO2OQPfpO',NULL,NULL,'manager@example.com',NULL,2,NULL,1601299366,1601299366),(3,'user','$2y$13$C93kpySma.mvMa5OG4Fdk.jNW3TWYde4Vuup9ryB9j97JO9B6Uph.','70TgvlgGbK3BRFC0MVlxc3bMymuIV6vl','9l-MFC_Z8yh71gxI0PDODLhjDOTRkwK9MMzw2f91',NULL,NULL,'user@example.com',NULL,2,NULL,1601299367,1601299367),(4,'Flor Holford','$2y$13$tuxbwMlvKPzZpIQby/1E/uLI7IC7HaFjeECpHeqR7MeLq9lOM50tW','z6_eSQ20SQxojUGyXZIlNj-RYQE58n2M','vUOYcL1pfIgPCc3REiR9MMPM6Ch_j6HCSF2PTcDk',NULL,NULL,'FlorHolford@nomail.com',NULL,2,NULL,1601725876,1601725876),(5,'Derek Searson','$2y$13$irOGHJ5rxs3x6D/TeUwm8O4AiCFitcm4nTQWbsqEzRiI5W2TD0jvO','cSztqWfz6uhNWTTaeKkgxOpO-sXVqxAl','G75zlWoqrgiNd6luGfVo6t_9WHhDyXHqz7szHULb',NULL,NULL,'DerekSearson@nomail.com',NULL,2,NULL,1601725878,1601725878),(6,'Helen Herrera','$2y$13$.B.uRbX6mgCvcRk461/hEunHJ1Cx6JBWgU8KUB9W5ZPr2HxVC4tku','8EDylWs13mmYAxOeYROS9iqTaWLhls4t','r_rhQOEhu8l6sWEcYF4CAzDyGTWVP5jwb6JI61R9',NULL,NULL,'HelenHerrera@nomail.com',NULL,2,NULL,1601725879,1601725879),(7,'James Martinez','$2y$13$nHZmi9cMOUb4ys2huAsHEunbTsKAKwPMAe.4pZg7AZ0vvUWFyM0R.','5LdYOezpUXvS4sv6YKemlQ8qkBXKDGzQ','a3KwCTcnBdA07X-YYv8m7U8AphbZmftBZJOIAVCA',NULL,NULL,'JamesMartinez@nomail.com',NULL,2,NULL,1601725880,1601725880),(8,'Frank Rogers','$2y$13$r938OEvrzgwZicilsLjIh.yfmGoWubKsGMuei4nCM0tecF9WYAU9C','SxP8w4Le0w0zuBHh1tt1Gu2xzFPBFQ4M','7Wb8pyYflEcujc8Em5fAvMOIO7078qBbvXE83cms',NULL,NULL,'FrankRogers@nomail.com',NULL,2,NULL,1601725882,1601725882),(9,'Katie Morrison','$2y$13$zx/eE27wx.Ffi64CCp4H4OoLYx9W1.EAhjMvImBC6c77/xDjDGO/a','pcWq8a1jW5D5zjbz1cdQLP0OpZZC-Fxs','ewVC7V1lxzPqC-qhQ_8FBwGny6ILyFsoIAZHbnv5',NULL,NULL,'KatieMorrison@nomail.com',NULL,2,NULL,1601725883,1601725883),(10,'Richard Burke','$2y$13$Zq/4FbxQef4MMfyDMsCCzueZr6.dfYxLMyORj281H77AQwcEN4U72','OdZsdKE6HDzLobj5mgWiGMgAa47D3lIH','v7grDBt0PohpqNOGONglFjBMthc8dKuh2LSYTxba',NULL,NULL,'RichardBurke@nomail.com',NULL,2,NULL,1601725884,1601725884),(11,'Charles Mata','$2y$13$qRYMHo8FCQYzUgAp1d439.i8N6thl5gN/38KFvAoI03sMG7CfzHTO','9qWhMRomghoDL8mzU38Vtm99S9yd3Slj','Q2DmBPs0vThP12LKVKl_2HYrghLH-1Ro7eFJiDss',NULL,NULL,'CharlesMata@nomail.com',NULL,2,NULL,1601725886,1601725886),(12,'Glenn Sublett','$2y$13$WwOX3k1vCehWjQ4UmmknCu6KEylIhOBetSpNmBHXJT1AdQUMsw3XW','ta6zXuToHBpj5KNf_s54rxKQxzW6_E5i','Tc6poTPcZckypOYdlP3IMA5bKMZ_Z6p7vn0ir7Js',NULL,NULL,'GlennSublett@nomail.com',NULL,2,NULL,1601725887,1601725887),(13,'Vernon Taylor','$2y$13$ClFc.mVrVP2RtowYRch9AuTHpUrIPTau.5m6ypvF0JPlwFJlDPAv2','SQlXAl7wlx51nomL5pS51iVwv-pL54sB','xL5Ro2-86POxHE7AHIrXEQR6l-30MVo_33GwVzx0',NULL,NULL,'VernonTaylor@nomail.com',NULL,2,NULL,1601725888,1601725888),(14,'Timothy Walker','$2y$13$AROk6eBd1SnB43HNCW0Lf.JLYjN8ZCJvJQ2XedM.zgOzEq5XosVnS','t_IXQEupu35yaOyD2CeA3yesS6GKNqCP','JrxSKnBmkQnG6OxMihyWFXz004rcwxxgJ5KlV6sM',NULL,NULL,'TimothyWalker@nomail.com',NULL,2,NULL,1601725890,1601725890),(15,'Jason Howard','$2y$13$7dMdZXIZsZ2SdW51RN3LpeXbRbs4LAxNGklr9q3fOaEqRfw36YNXC','zDcgMkC-Va7a1KkmSS2KQT9C5rRFugW7','i1GZEMDh_WCOHFpynsGJqr9LWacP3l86t0zXiGeX',NULL,NULL,'JasonHoward@nomail.com',NULL,2,NULL,1601725891,1601725891),(16,'Christopher Alexander','$2y$13$s5g1DuE4q3PxRgveLE9MEeneiXNqe8a86tEFjGHZDyKYfzrQdToNK','KIVEAak9IC6yvVSb97XtP_DOGYEkSEN7','LC9ukrcwgfdSkmqNhp_Gduqz7uwNMZ8KYq54Rd-u',NULL,NULL,'ChristopherAlexander@nomail.com',NULL,2,NULL,1601725893,1601725893),(17,'Kenneth Stringer','$2y$13$1e20ZxNa/Myt4kuf6xyKX.qfAIj3FTg90hlFJsoGn8bXKUO69a.hG','_1_Hbe3vskJ04n4YMPpGe_179oVeAp17','sEClrWsGrbTZarU8ITolrCwuEiIGxnXtQUSehbgj',NULL,NULL,'KennethStringer@nomail.com',NULL,2,NULL,1601725894,1601725894),(18,'Sandra Kuehn','$2y$13$y.uJnxRBBP.2OlK9eabFcOkfWshF9wYmGy0s75ZOYXhFavlVhtSOq','Vprp8yIA9sXLSFsejMkKDb0QD39DPoTL','Er-xTTvCY_o0E5HYE_eoJQ_tYZkZRVXbHQLs0i6I',NULL,NULL,'SandraKuehn@nomail.com',NULL,2,NULL,1601725895,1601725895),(19,'Edward Bird','$2y$13$bE1yhcxD1cMRUAzHJS1abuRbnwOOoeGfIIwQBd0iMoE0TjfeFobAW','flNcnatSCZ43VORru1ly2IESPaKybxbm','s7pdTyIbgGcaqJxVsFmPLOqVmzqwCtjyibKUd_dB',NULL,NULL,'EdwardBird@nomail.com',NULL,2,NULL,1601725897,1601725897),(20,'Linwood Colucci','$2y$13$kkEx/HF/rOehTArXH77mLOX23lOfMdWBhMWgbtC.AFi.KamTESQKq','JGf7BprK0OrKc5yZk7ur8NeHtlK5setD','Qxm54MHpPz0_Kt0_tHQFXefkbTtoxeQKXNnbKV2v',NULL,NULL,'LinwoodColucci@nomail.com',NULL,2,NULL,1601725898,1601725898),(21,'Matthew Burkett','$2y$13$gj4HLsWrJ2S6WtsBYxht5u8qIEP5xbuB92pTtf2k.ItyigFFZ2bR.','dSvgYk4h3waiMhIuNnrj-SE_Z25pUfOQ','ami7d24jdpKIUHsdSO0PU386VOvccnQx4fJejzkU',NULL,NULL,'MatthewBurkett@nomail.com',NULL,2,NULL,1601725900,1601725900),(22,'Alvin Bruce','$2y$13$TDdsQKflFkEBjdDqSHr/uut6N8EAbkTQRlBbkJljv4IvXwEfL2e9y','_DbwpPj6mVx6HooLzk9jLQDTmgSV_UkZ','0jdULRX32z8cMu7UXiJxRu0wX9J9fCxYMhEb5_Dn',NULL,NULL,'AlvinBruce@nomail.com',NULL,2,NULL,1601725901,1601725901),(23,'Clifford Johnson','$2y$13$I1CKGE7KPGm/ggfUcpZbt.6YtbKd43tPOZkKQYSR2LxU7Ywc1Fcx.','g8i6J2PLKsXaM6JtX9GWmahOZqQOfn9y','0J8TlJcVxYztWC7l7Ttndn_MVWbVuvuhUhhEc_jl',NULL,NULL,'CliffordJohnson@nomail.com',NULL,2,NULL,1601725902,1601725902),(24,'John Hendrix','$2y$13$soT7BnC0StIpBWj6mvfF/eraNBCZEITvZ2XMRQeJLjxgJzSgcpAPG','V07PXrvp8HiSZeM06KzcRuQSoF3OgDM6','KI0cXiyxkscNT5_M-3NSb7jT2Wh8INfkuajWzcAs',NULL,NULL,'JohnHendrix@nomail.com',NULL,2,NULL,1601725904,1601725904),(25,'Gene Williams','$2y$13$uYvXk37CKwrqMxYIxxPVZuuVFlyJ6LUaqQlMssVbvGPTdTOCf4ge2','pEMQ9qMsD1rmcTsIQZX9nFNEhIMTJ1q3','bpxkoTthT2Hz0ffEHRAW2dUkF6ywuC3Adld2G8ao',NULL,NULL,'GeneWilliams@nomail.com',NULL,2,NULL,1601725905,1601725905),(26,'Jessica Shelton','$2y$13$Bg/AqL/6vvPYh7f1f/t4Dez6zp2EnBdgSHD25IDq40hsJUtxKFN4G','j1QYnmiU_d72__EX-WjAJUWajU3q9Gya','LTIjrNT75VvMoV1mIPwnw4PxH4lMePaMV1yLoOM6',NULL,NULL,'JessicaShelton@nomail.com',NULL,2,NULL,1601725907,1601725907),(27,'Dorothy Hubbard','$2y$13$BD1LjGrSzkrWoUtT0/Y9ue1e83bZ4t.cWISEoJKRxchwpp7Ic2LW6','qNNq-Pnb60mj6DgIb6vp_Mnize575IfM','qZaZ0SJvp_guc5L7DQQVbxlSHl9e4zoAs-uBvTT6',NULL,NULL,'DorothyHubbard@nomail.com',NULL,2,NULL,1601725908,1601725908),(28,'Hildegarde Britt','$2y$13$os58H/bOxDJKJ/JACbRqSecB81GkZeQQ8tZxlvhfF6QT9Kave8jSe','L3z9FTP0exHjU6SY6cq1IWu4p7zscEl6','3d9nxHShA5lclYbrqXCfF02PFqKxOz-af-MfalcA',NULL,NULL,'HildegardeBritt@nomail.com',NULL,2,NULL,1601725909,1601725909),(29,'Robert Kinney','$2y$13$aTSHUBqHXWS1/HNlGaEbAOc0TezQWuC9EFo43Bbe1fOz.wm5zYmDu','qdkLT3AlEGrWu07MzLNSJvs0v2THsXe9','1KLuBYbxkluhyfNdO5EPKCIaeHQhXdVdxe7_NJda',NULL,NULL,'RobertKinney@nomail.com',NULL,2,NULL,1601725911,1601725911),(30,'Barry Neese','$2y$13$NA9tlwf4I3Qc6ChkjmAsau0jKfmr6TIUvv4XmFKYeF2NwnUv.eq.K','n-6zRVuPVZ1yUmiqCTaRXac0jbUbZwHr','OIz1JEhyr4_nA1g-QQiOk4_Hm02m1yrYtZGrKFFV',NULL,NULL,'BarryNeese@nomail.com',NULL,2,NULL,1601725912,1601725912),(31,'Lamar Gullixson','$2y$13$XoKDpBJvkwtaOg2sZJpOw.HhONcQxPFk6WiQq0zkOqXK2yQ8df8/W','YVsYwU8wZTWFJZ09mTTsZSyrGaHi3Dlq','JTLC5p0QDJ60dOuwNfZQsVHp7mp83SFcg8CyveGt',NULL,NULL,'LamarGullixson@nomail.com',NULL,2,NULL,1601725914,1601725914),(32,'Richard Carter','$2y$13$pzAh5fpDUgqbtX7Iiu03I.hY4YfclO2OWzLfaUcIK5HHj0z5S5lmC','dkb4LIdfQw9gnBWZftEX96bOclVJFdiu','dPbxLQt3TStFBBcHm5o596bOotdOgvZxPsYFqT3u',NULL,NULL,'RichardCarter@nomail.com',NULL,2,NULL,1601725915,1601725915),(33,'David Fant','$2y$13$e3Esbr76wwKwlQQ1PRW4qunVJfKKFj292d5mXA0nCJh3XxsQ48lE6','dj3Eo-lyWyUqXnm9Iu7K97I5XR8cC5cJ','K-AR8EtvGhkW2KFfq6e2BJ498TOFZrA_A1erfcKY',NULL,NULL,'DavidFant@nomail.com',NULL,2,NULL,1601725916,1601725916),(34,'Brett Boyd','$2y$13$.TNJ.WNQ3IduHOjO2ZTDb.W6PNPqjPCMrCOLlK5nPougJMdsm3ioi','wW92KlPlEXdsRYzHP9uSpjFywcOu8h1N','EM462nzQijqW0yJnYDD8OIRV6pq8Y9n9K-zHseko',NULL,NULL,'BrettBoyd@nomail.com',NULL,2,NULL,1601725918,1601725918),(35,'Jennifer Guerra','$2y$13$g88.iycM1i4qOuRzw71emeFHSgiWQcicBaIuM8m9akswRin3ogkPi','NI_1Ung07tVwRwi4LRrS05jaZom3-Wy_','3iFN9FM-bOizEujuT6EzwDqpAztp-wyZwM2rpNZb',NULL,NULL,'JenniferGuerra@nomail.com',NULL,2,NULL,1601725919,1601725919),(36,'Gail Dougherty','$2y$13$DKKjeT0khCTaf3HOTgpGQe41F6/.UbaTNZz9AnnWUY9AyiUoQwhCG','gMo8dLPYzn3wcG3cg6udaVyXs7zK3i-W','5JGq5d-GsniD9uRJHO5_SOd3WeEx4gGtq1jC4Jqx',NULL,NULL,'GailDougherty@nomail.com',NULL,2,NULL,1601725921,1601725921),(37,'James Shell','$2y$13$jG/ZDQWjWrkmMC0CkLzWH.SBtJj0kpcK3JKLfBMECwLLGpxpePETq','mfA3uVEOazUZHhYTGbYoQLG2v21kEx7J','-icgFbgIBGi4o8fvfh3zXOfVq_Ym_BJGDqEb2laS',NULL,NULL,'JamesShell@nomail.com',NULL,2,NULL,1601725922,1601725922),(38,'Judy Waverly','$2y$13$7u9BtUFDn3tS59EjZJNpuujVQ//YOLNeCoavokNlgBp6Sgwmwm08q','qc-NBRsYry9cmNGxix0oXg2cPeEFcriy','iv9C_n9Ja0l32dRIqqBBBS5WRTVdfs2m3W6up2La',NULL,NULL,'JudyWaverly@nomail.com',NULL,2,NULL,1601725923,1601725923),(39,'Henry Lutz','$2y$13$VHlvgP8m44c5cUc6pAMOPOWn2WW2sR3gl6aZCabHknjyHEbap1nPC','1twwEtGQvC58yy8g-Fbx9gxwXg3B3tHN','zM6XkGIhqI-lKUasjY2mvf-GwYf2t0b6024g0T-l',NULL,NULL,'HenryLutz@nomail.com',NULL,2,NULL,1601725925,1601725925),(40,'Rebecca Muckle','$2y$13$WqgCuUKV7Ax20GRxrkqIheIWdiu8m1sD/jOsgtnYnds1NvAK8sCsm','aPIYbCLZEgef21fA18pcDCaRL6NL9N9Q','vzNIJKenEICNN_nGDebCUPnkBNoa0KG3U5npoB48',NULL,NULL,'RebeccaMuckle@nomail.com',NULL,2,NULL,1601725926,1601725926),(41,'Lillie Shipley','$2y$13$SZ4nBndUs3Qj0NTQsElUqeweZ9KhxqBoB6FHZimM4PdeEs0F1fMyC','gcy_BufXQwc8DYCKjZD1g7R-I4mRod1E','9pAMr9x1jUlKo-GcUjCUePEUFizQb7dz1GkPD-WS',NULL,NULL,'LillieShipley@nomail.com',NULL,2,NULL,1601725928,1601725928),(42,'Dawn Stegmann','$2y$13$fRvqypWx4YCHBg75eymWJu9YiKsWAtN4ZPf/qT6bb2eeg5biT0HFS','USMaGdwZewZzTgiGN_7kNaafr8kJgbn8','FUuH0EBa1U5lfeU7hbvInidgywq-XhDv8bUSJOWX',NULL,NULL,'DawnStegmann@nomail.com',NULL,2,NULL,1601725929,1601725929),(43,'Juana Garcia','$2y$13$PqdJ0mJK7g7Ie9Rz1gP.nOSf8x7hcYXdI1Uw6soCE50kWLqnrakTm','Og3bV1gEsuQ6huq4gJGPX0DWLjIfSgeE','ndJzz_b6Yi5IKuEjqdbBycQwsF2AWnx3P9SCch-r',NULL,NULL,'JuanaGarcia@nomail.com',NULL,2,NULL,1601725931,1601725931),(44,'Jae Payton','$2y$13$Yw3rXHNi1oZccknkjFFfbeZ0NMccBmf/6hqMPIzBDlH5l.6KCnCUS','53YXVYyd-jn3lL7ma4KW1bOTcehQfOOa','yCZIrCLRmZfxT-uWgpWeXjRlQmKEduDqsm38Px3A',NULL,NULL,'JaePayton@nomail.com',NULL,2,NULL,1601725933,1601725933),(45,'Andrea Gill','$2y$13$pwLK1kK/LkbqyZr5JUaIXunfh.qHCSn7VQesGdG4ynQ3S/oq3J5Ny','ssDpgkZ1j4y6cDTYE9AAd1FIg6xxmaIK','6KSFn9uC2xSAKpYfMW4P3cf5r6bYP2LDctKQSDZR',NULL,NULL,'AndreaGill@nomail.com',NULL,2,NULL,1601725934,1601725934),(46,'August Delvalle','$2y$13$B6bCkEQttbYm7LW/9SyNa.skHbOGMeOQcpirvrz1ACqT.CKaADOQG','a52SbT5JO4vVo2SXK0dkF8XsU_rG-7Ao','Xhwh6vK9mqgmDgn5_f7jxbsJaxMMBaIpxQJi47P_',NULL,NULL,'AugustDelvalle@nomail.com',NULL,2,NULL,1601725936,1601725936),(47,'Matt Kirkpatrick','$2y$13$.sHipw/SaMbQjOSYMd44gupPamsb46b803Yj2.g86S5jcE4D.4.Zq','7H0pBHEUQxdflWhxVzPKvJc5wfoVzybA','rtH7HJJFgNMWBld1L9wA4XjCcK-4j9xDXC_4dqrk',NULL,NULL,'MattKirkpatrick@nomail.com',NULL,2,NULL,1601725937,1601725937),(48,'Alan Hardt','$2y$13$gcZY.Qz/bKKcIzJMf8Zb3.rLiaBaViidOh2ru34K2RTHQJXZGrFE.','4vLCECQpRfvJS5wnEzW_k-W158mMzyBW','6l-tqK3L0y1D_RsZABaFHIkhw6sNuZ1fUkV8ItG6',NULL,NULL,'AlanHardt@nomail.com',NULL,2,NULL,1601725939,1601725939),(49,'Ethel Bernier','$2y$13$ibSLT/6ZuLDWtebknBK95.twBjvf5dRhBaDwhABwLL.L7pMdXJv86','JDDrq8SjtvVcLV4qDa1aJ_qdN_HFzDIM','Bmd9lvijah5w8lGYRQAjhuOy37riuBBdB6TfT-yc',NULL,NULL,'EthelBernier@nomail.com',NULL,2,NULL,1601725940,1601725940),(50,'Gloria Bethke','$2y$13$4HuV6x.qPtYupJSNr0A2n.y2cL7X7uYUQ3Kcmbo81cDPdTqgu3kdG','gnmkgaW2RFT6tS0mCIOHrWRAARbk0aQg','GnnUUgH50WJq3ST8Pd_-Kl7fIs_smpNgGExEd1ew',NULL,NULL,'GloriaBethke@nomail.com',NULL,2,NULL,1601725942,1601725942),(51,'Velma Davis','$2y$13$s7l9r2n49qwaAMpgS5TOqOKNtWE3kPeIF8nsoomXad.JVEXY4lZnu','XPa-3rVcapSZUlbBUb-cxp1KC2HUvU3e','72vjLEZLfuEJCio6af1qMWzY9l7du35lyBITGOmt',NULL,NULL,'VelmaDavis@nomail.com',NULL,2,NULL,1601725943,1601725943),(52,'Louise Adam','$2y$13$AEVS9MLoA9BgQLI5nJg8lOkrwB/fvfU/tlp0h5yR4gkLLVeQHrZCK','rKKu7cM9jif0J4O9I8k7AKFDeqQIztgl','MbfS5jV9i72yfg3V3mUtwZ32ZEpgVmNATSJks9I6',NULL,NULL,'LouiseAdam@nomail.com',NULL,2,NULL,1601725944,1601725944),(53,'Elizabeth Pinera','$2y$13$s4rA7qnBa/gem195KvaC..eoXInRZhyxBx2m5Gxx/urhPl1l7KVL6','fkWIAfDKQCo0gCT6x8-dmDyeZVQMonFP','B-vLFL_ElGfCHIjzsir62FKc26ftVCgjrSaou6fh',NULL,NULL,'ElizabethPinera@nomail.com',NULL,2,NULL,1601725946,1601725946);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

DROP TABLE IF EXISTS `user_profile`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_profile` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(255) DEFAULT NULL,
  `middlename` varchar(255) DEFAULT NULL,
  `lastname` varchar(255) DEFAULT NULL,
  `avatar_path` varchar(255) DEFAULT NULL,
  `avatar_base_url` varchar(255) DEFAULT NULL,
  `locale` varchar(32) NOT NULL,
  `gender` smallint(1) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  CONSTRAINT `fk_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;


DROP TABLE IF EXISTS `user_token`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_token` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `type` varchar(255) NOT NULL,
  `token` varchar(40) NOT NULL,
  `expire_at` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;
