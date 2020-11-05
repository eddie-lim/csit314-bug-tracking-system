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
