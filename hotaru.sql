-- MySQL dump 10.13  Distrib 5.5.41, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: hotaru
-- ------------------------------------------------------
-- Server version	5.5.41-0ubuntu0.14.04.1

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
-- Table structure for table `hotaru_blocked`
--

DROP TABLE IF EXISTS `hotaru_blocked`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hotaru_blocked` (
  `blocked_id` int(20) NOT NULL AUTO_INCREMENT,
  `blocked_type` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `blocked_value` text COLLATE utf8_unicode_ci,
  `blocked_updatedts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `blocked_updateby` int(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`blocked_id`),
  KEY `blocked_type` (`blocked_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Blocked IPs, users, emails, etc';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hotaru_blocked`
--

LOCK TABLES `hotaru_blocked` WRITE;
/*!40000 ALTER TABLE `hotaru_blocked` DISABLE KEYS */;
/*!40000 ALTER TABLE `hotaru_blocked` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hotaru_categories`
--

DROP TABLE IF EXISTS `hotaru_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hotaru_categories` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `category_parent` int(11) NOT NULL DEFAULT '1',
  `category_name` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `category_safe_name` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `rgt` int(11) NOT NULL DEFAULT '0',
  `lft` int(11) NOT NULL DEFAULT '0',
  `category_order` int(11) NOT NULL DEFAULT '0',
  `category_desc` text COLLATE utf8_unicode_ci,
  `category_keywords` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `category_updatedts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `category_updateby` int(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`category_id`),
  UNIQUE KEY `key` (`category_name`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Categories';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hotaru_categories`
--

LOCK TABLES `hotaru_categories` WRITE;
/*!40000 ALTER TABLE `hotaru_categories` DISABLE KEYS */;
INSERT INTO `hotaru_categories` VALUES (1,1,'All','all',3,0,0,NULL,NULL,'2017-02-08 15:29:13',1),(2,1,'Everything','everything',2,1,1,NULL,NULL,'2017-02-08 15:29:12',1);
/*!40000 ALTER TABLE `hotaru_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hotaru_comments`
--

DROP TABLE IF EXISTS `hotaru_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hotaru_comments` (
  `comment_id` int(20) NOT NULL AUTO_INCREMENT,
  `comment_archived` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  `comment_updatedts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `comment_post_id` int(20) NOT NULL DEFAULT '0',
  `comment_user_id` int(20) NOT NULL DEFAULT '0',
  `comment_parent` int(20) DEFAULT '0',
  `comment_date` timestamp NULL DEFAULT NULL,
  `comment_status` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'approved',
  `comment_content` text COLLATE utf8_unicode_ci,
  `comment_votes_up` smallint(11) NOT NULL DEFAULT '0',
  `comment_votes_down` smallint(11) NOT NULL DEFAULT '0',
  `comment_subscribe` tinyint(1) NOT NULL DEFAULT '0',
  `comment_updateby` int(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`comment_id`),
  KEY `comment_archived` (`comment_archived`),
  KEY `comment_post_id` (`comment_post_id`),
  KEY `comment_status` (`comment_status`),
  KEY `comment_user_id` (`comment_user_id`),
  KEY `comment_parent` (`comment_parent`),
  FULLTEXT KEY `comment_content` (`comment_content`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Post Comments';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hotaru_comments`
--

LOCK TABLES `hotaru_comments` WRITE;
/*!40000 ALTER TABLE `hotaru_comments` DISABLE KEYS */;
/*!40000 ALTER TABLE `hotaru_comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hotaru_commentvotes`
--

DROP TABLE IF EXISTS `hotaru_commentvotes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hotaru_commentvotes` (
  `cvote_archived` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  `cvote_updatedts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `cvote_post_id` int(11) NOT NULL DEFAULT '0',
  `cvote_comment_id` int(11) NOT NULL DEFAULT '0',
  `cvote_user_id` int(11) NOT NULL DEFAULT '0',
  `cvote_user_ip` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `cvote_date` timestamp NULL DEFAULT NULL,
  `cvote_rating` smallint(11) NOT NULL DEFAULT '0',
  `cvote_reason` tinyint(3) NOT NULL DEFAULT '0',
  `cvote_updateby` int(20) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Comment Votes';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hotaru_commentvotes`
--

LOCK TABLES `hotaru_commentvotes` WRITE;
/*!40000 ALTER TABLE `hotaru_commentvotes` DISABLE KEYS */;
/*!40000 ALTER TABLE `hotaru_commentvotes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hotaru_friends`
--

DROP TABLE IF EXISTS `hotaru_friends`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hotaru_friends` (
  `follower_user_id` int(20) NOT NULL DEFAULT '0',
  `following_user_id` int(20) NOT NULL DEFAULT '0',
  `friends_updatedts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`follower_user_id`,`following_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Friends';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hotaru_friends`
--

LOCK TABLES `hotaru_friends` WRITE;
/*!40000 ALTER TABLE `hotaru_friends` DISABLE KEYS */;
/*!40000 ALTER TABLE `hotaru_friends` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hotaru_messaging`
--

DROP TABLE IF EXISTS `hotaru_messaging`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hotaru_messaging` (
  `message_id` int(20) NOT NULL AUTO_INCREMENT,
  `message_updatedts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `message_archived` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  `message_from` int(20) NOT NULL DEFAULT '0',
  `message_to` int(20) NOT NULL DEFAULT '0',
  `message_date` timestamp NULL DEFAULT NULL,
  `message_subject` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `message_content` text COLLATE utf8_unicode_ci,
  `message_read` tinyint(1) NOT NULL DEFAULT '0',
  `message_inbox` tinyint(1) NOT NULL DEFAULT '1',
  `message_outbox` tinyint(1) NOT NULL DEFAULT '1',
  `message_updateby` int(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`message_id`),
  KEY `message_archived` (`message_archived`),
  KEY `message_to` (`message_to`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Messaging';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hotaru_messaging`
--

LOCK TABLES `hotaru_messaging` WRITE;
/*!40000 ALTER TABLE `hotaru_messaging` DISABLE KEYS */;
/*!40000 ALTER TABLE `hotaru_messaging` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hotaru_miscdata`
--

DROP TABLE IF EXISTS `hotaru_miscdata`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hotaru_miscdata` (
  `miscdata_id` int(20) NOT NULL AUTO_INCREMENT,
  `miscdata_key` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `miscdata_value` text COLLATE utf8_unicode_ci,
  `miscdata_default` text COLLATE utf8_unicode_ci,
  `miscdata_updatedts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `miscdata_updateby` int(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`miscdata_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Miscellaneous Data';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hotaru_miscdata`
--

LOCK TABLES `hotaru_miscdata` WRITE;
/*!40000 ALTER TABLE `hotaru_miscdata` DISABLE KEYS */;
INSERT INTO `hotaru_miscdata` VALUES (1,'hotaru_version','1.7.3','1.7.3','2017-02-08 15:10:54',0),(2,'permissions','a:13:{s:7:\"options\";a:12:{s:16:\"can_access_admin\";a:2:{i:0;s:3:\"yes\";i:1;s:2:\"no\";}s:9:\"can_login\";a:2:{i:0;s:3:\"yes\";i:1;s:2:\"no\";}s:10:\"can_submit\";a:3:{i:0;s:3:\"yes\";i:1;s:2:\"no\";i:2;s:3:\"mod\";}s:14:\"can_edit_posts\";a:3:{i:0;s:3:\"yes\";i:1;s:2:\"no\";i:2;s:3:\"own\";}s:16:\"can_delete_posts\";a:2:{i:0;s:3:\"yes\";i:1;s:2:\"no\";}s:21:\"can_post_without_link\";a:2:{i:0;s:3:\"yes\";i:1;s:2:\"no\";}s:11:\"can_comment\";a:3:{i:0;s:3:\"yes\";i:1;s:2:\"no\";i:2;s:3:\"mod\";}s:17:\"can_edit_comments\";a:3:{i:0;s:3:\"yes\";i:1;s:2:\"no\";i:2;s:3:\"own\";}s:24:\"can_set_comments_pending\";a:2:{i:0;s:3:\"yes\";i:1;s:2:\"no\";}s:19:\"can_delete_comments\";a:2:{i:0;s:3:\"yes\";i:1;s:2:\"no\";}s:28:\"can_comment_manager_settings\";a:2:{i:0;s:3:\"yes\";i:1;s:2:\"no\";}s:10:\"can_search\";a:2:{i:0;s:3:\"yes\";i:1;s:2:\"no\";}}s:16:\"can_access_admin\";a:3:{s:5:\"admin\";s:3:\"yes\";s:8:\"supermod\";s:3:\"yes\";s:7:\"default\";s:2:\"no\";}s:9:\"can_login\";a:6:{s:5:\"admin\";s:3:\"yes\";s:8:\"supermod\";s:3:\"yes\";s:9:\"moderator\";s:3:\"yes\";s:6:\"member\";s:3:\"yes\";s:8:\"undermod\";s:3:\"yes\";s:7:\"default\";s:2:\"no\";}s:10:\"can_submit\";a:6:{s:5:\"admin\";s:3:\"yes\";s:8:\"supermod\";s:3:\"yes\";s:9:\"moderator\";s:3:\"yes\";s:6:\"member\";s:3:\"yes\";s:8:\"undermod\";s:3:\"mod\";s:7:\"default\";s:2:\"no\";}s:14:\"can_edit_posts\";a:6:{s:5:\"admin\";s:3:\"yes\";s:8:\"supermod\";s:3:\"yes\";s:9:\"moderator\";s:3:\"yes\";s:6:\"member\";s:3:\"own\";s:8:\"undermod\";s:3:\"own\";s:7:\"default\";s:2:\"no\";}s:16:\"can_delete_posts\";a:3:{s:5:\"admin\";s:3:\"yes\";s:8:\"supermod\";s:3:\"yes\";s:7:\"default\";s:2:\"no\";}s:21:\"can_post_without_link\";a:3:{s:5:\"admin\";s:3:\"yes\";s:8:\"supermod\";s:3:\"yes\";s:7:\"default\";s:2:\"no\";}s:11:\"can_comment\";a:6:{s:5:\"admin\";s:3:\"yes\";s:8:\"supermod\";s:3:\"yes\";s:9:\"moderator\";s:3:\"yes\";s:6:\"member\";s:3:\"yes\";s:8:\"undermod\";s:3:\"mod\";s:7:\"default\";s:2:\"no\";}s:17:\"can_edit_comments\";a:6:{s:5:\"admin\";s:3:\"yes\";s:8:\"supermod\";s:3:\"yes\";s:9:\"moderator\";s:3:\"yes\";s:6:\"member\";s:3:\"own\";s:8:\"undermod\";s:3:\"own\";s:7:\"default\";s:2:\"no\";}s:24:\"can_set_comments_pending\";a:4:{s:5:\"admin\";s:3:\"yes\";s:8:\"supermod\";s:3:\"yes\";s:9:\"moderator\";s:3:\"yes\";s:7:\"default\";s:2:\"no\";}s:19:\"can_delete_comments\";a:3:{s:5:\"admin\";s:3:\"yes\";s:8:\"supermod\";s:3:\"yes\";s:7:\"default\";s:2:\"no\";}s:28:\"can_comment_manager_settings\";a:4:{s:5:\"admin\";s:3:\"yes\";s:8:\"supermod\";s:3:\"yes\";s:9:\"moderator\";s:3:\"yes\";s:7:\"default\";s:2:\"no\";}s:10:\"can_search\";a:1:{s:7:\"default\";s:3:\"yes\";}}','a:13:{s:7:\"options\";a:12:{s:16:\"can_access_admin\";a:2:{i:0;s:3:\"yes\";i:1;s:2:\"no\";}s:9:\"can_login\";a:2:{i:0;s:3:\"yes\";i:1;s:2:\"no\";}s:10:\"can_submit\";a:3:{i:0;s:3:\"yes\";i:1;s:2:\"no\";i:2;s:3:\"mod\";}s:14:\"can_edit_posts\";a:3:{i:0;s:3:\"yes\";i:1;s:2:\"no\";i:2;s:3:\"own\";}s:16:\"can_delete_posts\";a:2:{i:0;s:3:\"yes\";i:1;s:2:\"no\";}s:21:\"can_post_without_link\";a:2:{i:0;s:3:\"yes\";i:1;s:2:\"no\";}s:11:\"can_comment\";a:3:{i:0;s:3:\"yes\";i:1;s:2:\"no\";i:2;s:3:\"mod\";}s:17:\"can_edit_comments\";a:3:{i:0;s:3:\"yes\";i:1;s:2:\"no\";i:2;s:3:\"own\";}s:24:\"can_set_comments_pending\";a:2:{i:0;s:3:\"yes\";i:1;s:2:\"no\";}s:19:\"can_delete_comments\";a:2:{i:0;s:3:\"yes\";i:1;s:2:\"no\";}s:28:\"can_comment_manager_settings\";a:2:{i:0;s:3:\"yes\";i:1;s:2:\"no\";}s:10:\"can_search\";a:2:{i:0;s:3:\"yes\";i:1;s:2:\"no\";}}s:16:\"can_access_admin\";a:3:{s:5:\"admin\";s:3:\"yes\";s:8:\"supermod\";s:3:\"yes\";s:7:\"default\";s:2:\"no\";}s:9:\"can_login\";a:6:{s:5:\"admin\";s:3:\"yes\";s:8:\"supermod\";s:3:\"yes\";s:9:\"moderator\";s:3:\"yes\";s:6:\"member\";s:3:\"yes\";s:8:\"undermod\";s:3:\"yes\";s:7:\"default\";s:2:\"no\";}s:10:\"can_submit\";a:6:{s:5:\"admin\";s:3:\"yes\";s:8:\"supermod\";s:3:\"yes\";s:9:\"moderator\";s:3:\"yes\";s:6:\"member\";s:3:\"yes\";s:8:\"undermod\";s:3:\"mod\";s:7:\"default\";s:2:\"no\";}s:14:\"can_edit_posts\";a:6:{s:5:\"admin\";s:3:\"yes\";s:8:\"supermod\";s:3:\"yes\";s:9:\"moderator\";s:3:\"yes\";s:6:\"member\";s:3:\"own\";s:8:\"undermod\";s:3:\"own\";s:7:\"default\";s:2:\"no\";}s:16:\"can_delete_posts\";a:3:{s:5:\"admin\";s:3:\"yes\";s:8:\"supermod\";s:3:\"yes\";s:7:\"default\";s:2:\"no\";}s:21:\"can_post_without_link\";a:3:{s:5:\"admin\";s:3:\"yes\";s:8:\"supermod\";s:3:\"yes\";s:7:\"default\";s:2:\"no\";}s:11:\"can_comment\";a:6:{s:5:\"admin\";s:3:\"yes\";s:8:\"supermod\";s:3:\"yes\";s:9:\"moderator\";s:3:\"yes\";s:6:\"member\";s:3:\"yes\";s:8:\"undermod\";s:3:\"mod\";s:7:\"default\";s:2:\"no\";}s:17:\"can_edit_comments\";a:6:{s:5:\"admin\";s:3:\"yes\";s:8:\"supermod\";s:3:\"yes\";s:9:\"moderator\";s:3:\"yes\";s:6:\"member\";s:3:\"own\";s:8:\"undermod\";s:3:\"own\";s:7:\"default\";s:2:\"no\";}s:24:\"can_set_comments_pending\";a:4:{s:5:\"admin\";s:3:\"yes\";s:8:\"supermod\";s:3:\"yes\";s:9:\"moderator\";s:3:\"yes\";s:7:\"default\";s:2:\"no\";}s:19:\"can_delete_comments\";a:3:{s:5:\"admin\";s:3:\"yes\";s:8:\"supermod\";s:3:\"yes\";s:7:\"default\";s:2:\"no\";}s:28:\"can_comment_manager_settings\";a:4:{s:5:\"admin\";s:3:\"yes\";s:8:\"supermod\";s:3:\"yes\";s:9:\"moderator\";s:3:\"yes\";s:7:\"default\";s:2:\"no\";}s:10:\"can_search\";a:1:{s:7:\"default\";s:3:\"yes\";}}','2017-02-08 15:13:00',0),(3,'user_settings','a:2:{s:7:\"new_tab\";s:0:\"\";s:11:\"link_action\";s:0:\"\";}','a:2:{s:7:\"new_tab\";s:0:\"\";s:11:\"link_action\";s:0:\"\";}','2017-02-08 15:12:49',1),(4,'site_announcement','','','2017-02-08 15:10:54',0);
/*!40000 ALTER TABLE `hotaru_miscdata` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hotaru_pluginhooks`
--

DROP TABLE IF EXISTS `hotaru_pluginhooks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hotaru_pluginhooks` (
  `phook_id` int(20) NOT NULL AUTO_INCREMENT,
  `plugin_folder` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `plugin_hook` varchar(128) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `plugin_hook_updatedts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `plugin_updateby` int(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`phook_id`),
  KEY `plugin_folder` (`plugin_folder`)
) ENGINE=InnoDB AUTO_INCREMENT=216 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Plugins Hooks';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hotaru_pluginhooks`
--

LOCK TABLES `hotaru_pluginhooks` WRITE;
/*!40000 ALTER TABLE `hotaru_pluginhooks` DISABLE KEYS */;
INSERT INTO `hotaru_pluginhooks` VALUES (1,'bookmarking','install_plugin','2017-02-08 16:53:06',1),(2,'bookmarking','theme_index_top','2017-02-08 16:53:06',1),(3,'bookmarking','header_meta','2017-02-08 16:53:06',1),(4,'bookmarking','header_include','2017-02-08 16:53:06',1),(5,'bookmarking','navigation','2017-02-08 16:53:06',1),(6,'bookmarking','breadcrumbs','2017-02-08 16:53:06',1),(7,'bookmarking','theme_index_main','2017-02-08 16:53:06',1),(8,'bookmarking','admin_plugin_settings','2017-02-08 16:53:06',1),(9,'bookmarking','admin_sidebar_plugin_settings','2017-02-08 16:53:06',1),(10,'bookmarking','user_settings_pre_save','2017-02-08 16:53:06',1),(11,'bookmarking','user_settings_fill_form','2017-02-08 16:53:06',1),(12,'bookmarking','user_settings_extra_settings','2017-02-08 16:53:06',1),(13,'bookmarking','pre_show_post','2017-02-08 16:53:06',1),(14,'bookmarking','show_post_extra_fields','2017-02-08 16:53:06',1),(15,'bookmarking','show_post_title','2017-02-08 16:53:06',1),(16,'bookmarking','theme_index_pre_main','2017-02-08 16:53:06',1),(17,'bookmarking','profile_navigation','2017-02-08 16:53:06',1),(18,'bookmarking','api_call','2017-02-08 16:53:06',1),(19,'bookmarking','post_rss_feed_items','2017-02-08 16:53:06',1),(20,'bookmarking','usermenu_top','2017-02-08 16:53:06',1),(21,'widgets','theme_index_top','2017-02-08 16:53:06',1),(22,'widgets','header_include','2017-02-08 16:53:06',1),(23,'widgets','admin_header_include','2017-02-08 16:53:06',1),(24,'widgets','admin_plugin_settings','2017-02-08 16:53:06',1),(25,'widgets','admin_sidebar_plugin_settings','2017-02-08 16:53:06',1),(26,'widgets','widget_block','2017-02-08 16:53:06',1),(27,'users','install_plugin','2017-02-08 16:53:06',1),(28,'users','pagehandling_getpagename','2017-02-08 16:53:06',1),(29,'users','theme_index_top','2017-02-08 16:53:06',1),(30,'users','navigation','2017-02-08 16:53:06',1),(31,'users','header_include','2017-02-08 16:53:06',1),(32,'users','bookmarking_functions_preparelist','2017-02-08 16:53:06',1),(33,'users','breadcrumbs','2017-02-08 16:53:06',1),(34,'users','theme_index_main','2017-02-08 16:53:06',1),(35,'users','users_edit_profile_save','2017-02-08 16:53:06',1),(36,'users','user_settings_save','2017-02-08 16:53:06',1),(37,'users','admin_theme_main_stats','2017-02-08 16:53:06',1),(38,'users','header_meta','2017-02-08 16:53:06',1),(39,'users','post_rss_feed','2017-02-08 16:53:06',1),(40,'user_signin','install_plugin','2017-02-08 16:53:06',1),(41,'user_signin','theme_index_top','2017-02-08 16:53:06',1),(42,'user_signin','navigation_users','2017-02-08 16:53:06',1),(43,'user_signin','theme_index_main','2017-02-08 16:53:06',1),(44,'user_signin','admin_sidebar_plugin_settings','2017-02-08 16:53:06',1),(45,'user_signin','admin_plugin_settings','2017-02-08 16:53:06',1),(46,'user_signin','admin_footer','2017-02-08 16:53:06',1),(47,'submit','install_plugin','2017-02-08 16:53:06',1),(48,'submit','admin_theme_index_top','2017-02-08 16:53:06',1),(49,'submit','theme_index_top','2017-02-08 16:53:06',1),(50,'submit','header_include','2017-02-08 16:53:06',1),(51,'submit','header_include_raw','2017-02-08 16:53:06',1),(52,'submit','navigation','2017-02-08 16:53:06',1),(53,'submit','admin_header_include_raw','2017-02-08 16:53:06',1),(54,'submit','breadcrumbs','2017-02-08 16:53:06',1),(55,'submit','theme_index_main','2017-02-08 16:53:06',1),(56,'submit','admin_plugin_settings','2017-02-08 16:53:06',1),(57,'submit','admin_sidebar_plugin_settings','2017-02-08 16:53:06',1),(58,'categories','theme_index_top','2017-02-08 16:53:06',1),(59,'categories','install_plugin','2017-02-08 16:53:06',1),(60,'categories','header_include','2017-02-08 16:53:06',1),(61,'categories','pagehandling_getpagename','2017-02-08 16:53:06',1),(62,'categories','bookmarking_functions_preparelist','2017-02-08 16:53:06',1),(63,'categories','show_post_author_date','2017-02-08 16:53:06',1),(64,'categories','categories_post_show','2017-02-08 16:53:06',1),(65,'categories','header_end','2017-02-08 16:53:06',1),(66,'categories','breadcrumbs','2017-02-08 16:53:06',1),(67,'categories','header_meta','2017-02-08 16:53:06',1),(68,'categories','post_rss_feed','2017-02-08 16:53:06',1),(69,'categories','admin_plugin_settings','2017-02-08 16:53:06',1),(70,'categories','admin_sidebar_plugin_settings','2017-02-08 16:53:06',1),(71,'stop_spam','install_plugin','2017-02-08 16:53:06',1),(72,'stop_spam','users_signin_register_check_blocked','2017-02-08 16:53:06',1),(73,'stop_spam','users_register_check_blocked','2017-02-08 16:53:06',1),(74,'stop_spam','users_register_pre_add_user','2017-02-08 16:53:06',1),(75,'stop_spam','users_signin_register_pre_add_user','2017-02-08 16:53:06',1),(76,'stop_spam','users_register_post_add_user','2017-02-08 16:53:06',1),(77,'stop_spam','users_signin_register_post_add_user','2017-02-08 16:53:06',1),(78,'stop_spam','users_email_conf_post_role','2017-02-08 16:53:06',1),(79,'stop_spam','users_signin_email_conf_post_role','2017-02-08 16:53:06',1),(80,'stop_spam','user_manager_role','2017-02-08 16:53:06',1),(81,'stop_spam','user_manager_details','2017-02-08 16:53:06',1),(82,'stop_spam','user_manager_pre_submit_button','2017-02-08 16:53:06',1),(83,'stop_spam','user_man_killspam_delete','2017-02-08 16:53:06',1),(84,'stop_spam','admin_sidebar_plugin_settings','2017-02-08 16:53:06',1),(85,'stop_spam','admin_plugin_settings','2017-02-08 16:53:06',1),(86,'stop_spam','','2017-02-08 16:53:06',1),(87,'comments','install_plugin','2017-02-08 16:53:06',1),(88,'comments','theme_index_top','2017-02-08 16:53:06',1),(89,'comments','header_include','2017-02-08 16:53:06',1),(90,'comments','admin_header_include_raw','2017-02-08 16:53:06',1),(91,'comments','theme_index_main','2017-02-08 16:53:06',1),(92,'comments','show_post_extra_fields','2017-02-08 16:53:06',1),(93,'comments','post_show_post','2017-02-08 16:53:06',1),(94,'comments','admin_plugin_settings','2017-02-08 16:53:06',1),(95,'comments','admin_sidebar_plugin_settings','2017-02-08 16:53:06',1),(96,'comments','submit_2_fields','2017-02-08 16:53:06',1),(97,'comments','submit_edit_admin_fields','2017-02-08 16:53:06',1),(98,'comments','post_delete_post','2017-02-08 16:53:06',1),(99,'comments','profile_navigation','2017-02-08 16:53:06',1),(100,'comments','admin_theme_main_stats','2017-02-08 16:53:06',1),(101,'comments','breadcrumbs','2017-02-08 16:53:06',1),(102,'comments','submit_functions_process_submitted','2017-02-08 16:53:06',1),(103,'comments','submit_2_process_submission','2017-02-08 16:53:06',1),(104,'comments','profile_content','2017-02-08 16:53:06',1),(105,'comments','api_call','2017-02-08 16:53:06',1),(106,'recaptcha','install_plugin','2017-02-08 16:53:06',1),(107,'recaptcha','admin_sidebar_plugin_settings','2017-02-08 16:53:06',1),(108,'recaptcha','admin_plugin_settings','2017-02-08 16:53:06',1),(109,'recaptcha','show_recaptcha','2017-02-08 16:53:06',1),(110,'recaptcha','check_recaptcha','2017-02-08 16:53:06',1),(111,'gravatar','install_plugin','2017-02-08 16:53:06',1),(112,'gravatar','theme_index_top','2017-02-08 16:53:06',1),(113,'gravatar','admin_theme_index_top','2017-02-08 16:53:06',1),(114,'gravatar','avatar_set_avatar','2017-02-08 16:53:06',1),(115,'gravatar','avatar_get_avatar','2017-02-08 16:53:06',1),(116,'gravatar','avatar_show_avatar','2017-02-08 16:53:06',1),(117,'gravatar','avatar_test_avatar','2017-02-08 16:53:06',1),(118,'gravatar','admin_plugin_settings','2017-02-08 16:53:06',1),(119,'category_manager','install_plugin','2017-02-08 16:53:06',1),(120,'category_manager','admin_header_include','2017-02-08 16:53:06',1),(121,'category_manager','admin_plugin_settings','2017-02-08 16:53:06',1),(122,'category_manager','admin_sidebar_plugin_settings','2017-02-08 16:53:06',1),(123,'category_manager','admin_sidebar_categories','2017-02-08 16:53:06',1),(124,'search','install_plugin','2017-02-08 16:53:06',1),(125,'search','theme_index_top','2017-02-08 16:53:06',1),(126,'search','header_include','2017-02-08 16:53:06',1),(127,'search','bookmarking_functions_preparelist','2017-02-08 16:53:06',1),(128,'search','search_box','2017-02-08 16:53:06',1),(129,'search','search_box_nav','2017-02-08 16:53:06',1),(130,'search','breadcrumbs','2017-02-08 16:53:06',1),(131,'search','post_rss_feed','2017-02-08 16:53:06',1),(132,'post_manager','hotaru_header','2017-02-08 16:53:06',1),(133,'post_manager','install_plugin','2017-02-08 16:53:06',1),(134,'post_manager','admin_header_include','2017-02-08 16:53:06',1),(135,'post_manager','admin_plugin_settings','2017-02-08 16:53:06',1),(136,'post_manager','admin_sidebar_plugin_settings','2017-02-08 16:53:06',1),(137,'post_manager','user_manager_role','2017-02-08 16:53:06',1),(138,'post_manager','user_manager_details','2017-02-08 16:53:06',1),(139,'post_manager','admin_theme_main_stats','2017-02-08 16:53:06',1),(140,'post_manager','admin_sidebar_posts','2017-02-08 16:53:06',1),(141,'akismet','admin_plugin_settings','2017-02-08 16:53:06',1),(142,'akismet','admin_sidebar_plugin_settings','2017-02-08 16:53:06',1),(143,'akismet','install_plugin','2017-02-08 16:53:06',1),(144,'akismet','comment_pre_add_comment','2017-02-08 16:53:06',1),(145,'akismet','submit_step_3_pre_trackback','2017-02-08 16:53:06',1),(146,'akismet','com_man_approve_comment','2017-02-08 16:53:06',1),(147,'akismet','com_man_delete_comment','2017-02-08 16:53:06',1),(148,'akismet','comments_delete_comment','2017-02-08 16:53:06',1),(149,'akismet','post_man_status_new','2017-02-08 16:53:06',1),(150,'akismet','post_man_status_top','2017-02-08 16:53:06',1),(151,'akismet','post_man_status_buried','2017-02-08 16:53:06',1),(152,'akismet','post_man_delete','2017-02-08 16:53:06',1),(153,'akismet','submit_edit_post_change_status','2017-02-08 16:53:06',1),(154,'akismet','submit_edit_post_delete','2017-02-08 16:53:06',1),(155,'akismet','vote_post_status_buried','2017-02-08 16:53:06',1),(156,'akismet','bookmarking_post_status_buried','2017-02-08 16:53:06',1),(157,'tags','theme_index_top','2017-02-08 16:53:06',1),(158,'tags','header_include','2017-02-08 16:53:06',1),(159,'tags','header_include_raw','2017-02-08 16:53:06',1),(160,'tags','header_meta','2017-02-08 16:53:06',1),(161,'tags','show_post_extra_fields','2017-02-08 16:53:06',1),(162,'tags','bookmarking_functions_preparelist','2017-02-08 16:53:06',1),(163,'tags','breadcrumbs','2017-02-08 16:53:06',1),(164,'tags','post_rss_feed','2017-02-08 16:53:06',1),(165,'tags','admin_plugin_settings','2017-02-08 16:53:06',1),(166,'related_posts','install_plugin','2017-02-08 16:53:06',1),(167,'related_posts','theme_index_top','2017-02-08 16:53:06',1),(168,'related_posts','header_include','2017-02-08 16:53:06',1),(169,'related_posts','submit_settings_get_values','2017-02-08 16:53:06',1),(170,'related_posts','submit_settings_form2','2017-02-08 16:53:06',1),(171,'related_posts','submit_save_settings','2017-02-08 16:53:06',1),(172,'related_posts','submit_step3_pre_buttons','2017-02-08 16:53:06',1),(173,'related_posts','submit_step3_post_buttons','2017-02-08 16:53:06',1),(174,'related_posts','show_post_middle','2017-02-08 16:53:06',1),(175,'related_posts','admin_plugin_settings','2017-02-08 16:53:06',1),(176,'comment_manager','install_plugin','2017-02-08 16:53:06',1),(177,'comment_manager','admin_header_include','2017-02-08 16:53:06',1),(178,'comment_manager','admin_plugin_settings','2017-02-08 16:53:06',1),(179,'comment_manager','admin_sidebar_comments','2017-02-08 16:53:06',1),(180,'comment_manager','user_manager_role','2017-02-08 16:53:06',1),(181,'comment_manager','user_manager_details','2017-02-08 16:53:06',1),(182,'user_manager','hotaru_header','2017-02-08 16:53:06',1),(183,'user_manager','install_plugin','2017-02-08 16:53:06',1),(184,'user_manager','admin_header_include','2017-02-08 16:53:06',1),(185,'user_manager','admin_plugin_settings','2017-02-08 16:53:06',1),(186,'user_manager','admin_sidebar_plugin_settings','2017-02-08 16:53:06',1),(187,'user_manager','post_manager_user_name','2017-02-08 16:53:06',1),(188,'user_manager','comment_manager_user_name','2017-02-08 16:53:06',1),(189,'user_manager','submit_edit_end','2017-02-08 16:53:06',1),(190,'user_manager','admin_sidebar_users','2017-02-08 16:53:06',1),(191,'post_images','install_plugin','2017-02-08 16:53:06',1),(192,'post_images','admin_sidebar_plugin_settings','2017-02-08 16:53:06',1),(193,'post_images','admin_plugin_settings','2017-02-08 16:53:06',1),(194,'post_images','submit_2_fields','2017-02-08 16:53:06',1),(195,'post_images','header_include_raw','2017-02-08 16:53:06',1),(196,'post_images','post_read_post','2017-02-08 16:53:06',1),(197,'post_images','submit_functions_process_submitted','2017-02-08 16:53:06',1),(198,'post_images','post_add_post','2017-02-08 16:53:06',1),(199,'post_images','post_update_post','2017-02-08 16:53:06',1),(200,'post_images','header_include','2017-02-08 16:53:06',1),(201,'post_images','theme_index_top','2017-02-08 16:53:06',1),(202,'post_images','footer','2017-02-08 16:53:06',1),(203,'post_images','show_post_pre_title','2017-02-08 16:53:06',1),(204,'submit_no_links','theme_index_top','2017-02-08 16:53:06',1),(205,'vote','install_plugin','2017-02-08 16:53:06',1),(206,'vote','theme_index_top','2017-02-08 16:53:06',1),(207,'vote','post_read_post','2017-02-08 16:53:06',1),(208,'vote','header_include','2017-02-08 16:53:06',1),(209,'vote','pre_show_post','2017-02-08 16:53:06',1),(210,'vote','admin_plugin_settings','2017-02-08 16:53:06',1),(211,'vote','admin_sidebar_plugin_settings','2017-02-08 16:53:06',1),(212,'vote','post_add_post','2017-02-08 16:53:06',1),(213,'vote','submit_confirm_pre_trackback','2017-02-08 16:53:06',1),(214,'vote','post_delete_post','2017-02-08 16:53:06',1),(215,'vote','header_include_raw','2017-02-08 16:53:06',1);
/*!40000 ALTER TABLE `hotaru_pluginhooks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hotaru_plugins`
--

DROP TABLE IF EXISTS `hotaru_plugins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hotaru_plugins` (
  `plugin_id` int(11) NOT NULL AUTO_INCREMENT,
  `plugin_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `plugin_name` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `plugin_folder` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `plugin_class` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `plugin_extends` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `plugin_type` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `plugin_desc` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `plugin_requires` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `plugin_version` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0.0',
  `plugin_order` int(11) NOT NULL DEFAULT '0',
  `plugin_author` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `plugin_authorurl` varchar(128) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `plugin_updatedts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `plugin_updateby` int(20) NOT NULL DEFAULT '0',
  `plugin_latestversion` varchar(8) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0.0',
  `plugin_resourceId` int(11) NOT NULL DEFAULT '0',
  `plugin_resourceVersionId` int(11) NOT NULL DEFAULT '0',
  `plugin_rating` varchar(8) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0.0',
  PRIMARY KEY (`plugin_id`),
  UNIQUE KEY `key` (`plugin_folder`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Application Plugins';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hotaru_plugins`
--

LOCK TABLES `hotaru_plugins` WRITE;
/*!40000 ALTER TABLE `hotaru_plugins` DISABLE KEYS */;
INSERT INTO `hotaru_plugins` VALUES (1,1,'Bookmarking','bookmarking','Bookmarking','','base','Social Bookmarking base - provides \"list\" and \"post\" templates.','','0.8',1,'Nick Ramsay','http%3A%2F%2Fhotarucms.org%2Fmember.php%3F1-Nick','2017-02-08 15:12:48',1,'0.0',0,0,'0.0'),(2,1,'Widgets','widgets','Widgets','','','Manages the contents of the widget blocks','','1.0',2,'Nick Ramsay','http%3A%2F%2Fhotarucms.org%2Fmember.php%3F1-Nick','2017-02-08 15:12:49',1,'0.0',0,0,'0.0'),(3,1,'Users','users','Users','','users','Provides profile, settings and permission pages','','2.3',3,'Nick Ramsay','http%3A%2F%2Fhotarucms.org%2Fmember.php%3F1-Nick','2017-02-08 15:12:50',1,'0.0',0,0,'0.0'),(4,1,'User Signin','user_signin','UserSignin','','signin','Provides user registration and login','','1.1',4,'Nick Ramsay','http%3A%2F%2Fhotarucms.org%2Fmember.php%3F1-Nick','2017-02-08 15:12:50',1,'0.0',0,0,'0.0'),(5,1,'Submit','submit','Submit','','post','Social Bookmarking submit - Enables post submission','','3.6',5,'Nick Ramsay','http%3A%2F%2Fhotarucms.org%2Fmember.php%3F1-Nick','2017-02-08 15:12:51',1,'0.0',0,0,'0.0'),(6,1,'Categories','categories','Categories','','categories','Enables categories for posts','','2.2',6,'Nick Ramsay','http%3A%2F%2Fhotarucms.org%2Fmember.php%3F1-Nick','2017-02-08 15:12:51',1,'0.0',0,0,'0.0'),(7,1,'Stop Spam','stop_spam','StopSpam','','antispam','Checks new users against the StopForumSpam.com blacklist','users 1.1, user_signin','0.9',7,'Nick Ramsay','http%3A%2F%2Fhotarucms.org%2Fmember.php%3F1-Nick','2017-02-08 15:12:52',1,'0.0',0,0,'0.0'),(8,1,'Comments','comments','Comments','','comments','Enables logged-in users to comment on posts','users 1.1','2.8',8,'','','2017-02-08 15:12:53',1,'0.0',0,0,'0.0'),(9,1,'reCaptcha','recaptcha','ReCaptcha','','captcha','Anti-spam captcha system','','0.1',9,'Nick Ramsay','http%3A%2F%2Fhotarucms.org%2Fmember.php%3F1-Nick','2017-02-08 15:12:53',1,'0.0',0,0,'0.0'),(10,1,'Gravatar','gravatar','Gravatar','','avatar','Enables Gravatar avatars for users','','1.3.1',10,'Nick Ramsay','http%3A%2F%2Fhotarucms.org%2Fmember.php%3F1-Nick','2017-02-08 15:12:59',1,'0.0',0,0,'0.0'),(11,1,'Category Manager','category_manager','CategoryManager','','Admin','Manager categories.','','1.1',11,'Nick Ramsay','http%3A%2F%2Fhotarucms.org%2Fmember.php%3F1-Nick','2017-02-08 15:13:00',1,'0.0',0,0,'0.0'),(12,1,'Search','search','Search','','search','Displays search box','','1.5',12,'Nick Ramsay','http%3A%2F%2Fnickramsay.com','2017-02-08 15:13:00',1,'0.0',0,0,'0.0'),(13,1,'Post Manager','post_manager','PostManager','','Admin','Manage posts.','','1.0',13,'Nick Ramsay','http%3A%2F%2Fhotarucms.org%2Fmember.php%3F1-Nick','2017-02-08 15:13:01',1,'0.0',0,0,'0.0'),(14,1,'Akismet','akismet','HotaruAkismet','','antispam','Anti-spam service','submit 1.9, comments 1.2','0.6',14,'Nick Ramsay','http%3A%2F%2Fhotarucms.org%2Fmember.php%3F1-Nick','2017-02-08 15:13:01',1,'0.0',0,0,'0.0'),(15,1,'Tags','tags','Tags','','tags','Show tags, filter tags and RSS for tags','','2.0',15,'Nick Ramsay','http%3A%2F%2Fhotarucms.org%2Fmember.php%3F1-Nick','2017-02-08 15:13:02',1,'0.0',0,0,'0.0'),(16,1,'Related Posts','related_posts','relatedPosts','','','Show a list of related posts','','1.4',16,'Nick Ramsay','http%3A%2F%2Fhotarucms.org%2Fmember.php%3F1-Nick','2017-02-08 15:13:02',1,'0.0',0,0,'0.0'),(17,1,'Comment Manager','comment_manager','CommentManager','','','Manage comments.','comments 1.2','0.6',17,'Nick Ramsay','http%3A%2F%2Fhotarucms.org%2Fmember.php%3F1-Nick','2017-02-08 15:13:03',1,'0.0',0,0,'0.0'),(18,1,'User Manager','user_manager','UserManager','','Admin','Manage users.','users 1.1, user_signin 0.5','1.5',18,'Nick Ramsay','http%3A%2F%2Fhotarucms.org%2Fmember.php%3F1-Nick','2017-02-08 15:13:03',1,'0.0',0,0,'0.0'),(19,1,'Post Images','post_images','PostImages','','post_images','Add images to your posts','','1.7',19,'Matthis de Wit','http%3A%2F%2Ffourtydegrees.nl%2Fties','2017-02-08 15:18:16',1,'0.0',0,0,'0.0'),(21,1,'Submit No Links','submit_no_links','SubmitNoLinks','Submit','','Removes requirement to submit a link','submit 2.4','0.2',20,'Nick Ramsay','http%3A%2F%2Fhotarucms.org%2Fmember.php%3F1-Nick','2017-02-08 15:39:53',1,'0.0',0,0,'0.0'),(24,1,'Vote','vote','Vote','','vote','Adds voting ability to posted stories.','submit 1.9, users 1.1','2.5',21,'Nick Ramsay','http%3A%2F%2Fhotarucms.org%2Fmember.php%3F1-Nick','2017-02-08 16:53:05',1,'0.0',0,0,'0.0');
/*!40000 ALTER TABLE `hotaru_plugins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hotaru_pluginsettings`
--

DROP TABLE IF EXISTS `hotaru_pluginsettings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hotaru_pluginsettings` (
  `psetting_id` int(20) NOT NULL AUTO_INCREMENT,
  `plugin_folder` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `plugin_setting` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `plugin_value` text COLLATE utf8_unicode_ci,
  `plugin_updatedts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `plugin_updateby` int(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`psetting_id`),
  KEY `plugin_folder` (`plugin_folder`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Plugins Settings';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hotaru_pluginsettings`
--

LOCK TABLES `hotaru_pluginsettings` WRITE;
/*!40000 ALTER TABLE `hotaru_pluginsettings` DISABLE KEYS */;
INSERT INTO `hotaru_pluginsettings` VALUES (1,'bookmarking','bookmarking_settings','a:9:{s:14:\"posts_per_page\";i:10;s:12:\"rss_redirect\";s:0:\"\";s:12:\"default_type\";s:4:\"news\";s:12:\"default_page\";s:7:\"popular\";s:7:\"archive\";s:10:\"no_archive\";s:17:\"sort_bar_dropdown\";s:7:\"checked\";s:10:\"use_alerts\";s:7:\"checked\";s:14:\"alerts_to_bury\";i:5;s:15:\"physical_delete\";s:0:\"\";}','2017-02-08 15:12:48',1),(2,'user_signin','user_signin_settings','a:5:{s:17:\"recaptcha_enabled\";s:0:\"\";s:17:\"emailconf_enabled\";s:0:\"\";s:19:\"registration_status\";s:6:\"member\";s:12:\"email_notify\";s:0:\"\";s:17:\"email_notify_mods\";a:0:{}}','2017-02-08 15:12:50',1),(3,'submit','submit_settings','a:18:{s:7:\"enabled\";s:7:\"checked\";s:7:\"content\";s:7:\"checked\";s:14:\"content_length\";i:50;s:7:\"summary\";s:7:\"checked\";s:14:\"summary_length\";i:200;s:14:\"allowable_tags\";s:39:\"<b><i><u><a><span><br><blockquote><del>\";s:9:\"url_limit\";i:0;s:11:\"daily_limit\";i:0;s:12:\"period_limit\";i:0;s:10:\"freq_limit\";i:0;s:11:\"set_pending\";s:0:\"\";s:7:\"x_posts\";i:1;s:12:\"email_notify\";s:0:\"\";s:17:\"email_notify_mods\";a:0:{}s:10:\"categories\";s:7:\"checked\";s:4:\"tags\";s:7:\"checked\";s:8:\"max_tags\";i:100;s:13:\"required_tags\";s:7:\"checked\";}','2017-02-08 15:12:51',1),(4,'categories','categories_settings','a:2:{s:20:\"categories_nav_style\";s:6:\"style1\";s:19:\"categories_nav_show\";s:7:\"checked\";}','2017-02-08 15:12:51',1),(5,'stop_spam','stop_spam_key','','2017-02-08 15:12:52',1),(6,'stop_spam','stop_spam_type','go_pending','2017-02-08 15:12:52',1),(7,'comments','comments_settings','a:18:{s:17:\"comment_all_forms\";s:7:\"checked\";s:14:\"comment_voting\";s:0:\"\";s:14:\"comment_levels\";i:5;s:13:\"comment_email\";s:14:\"beez@nc.rr.com\";s:22:\"comment_allowable_tags\";s:29:\"<b><i><u><a><blockquote><del>\";s:19:\"comment_set_pending\";s:0:\"\";s:13:\"comment_order\";s:3:\"asc\";s:18:\"comment_pagination\";s:0:\"\";s:22:\"comment_items_per_page\";i:20;s:18:\"comment_x_comments\";i:1;s:20:\"comment_email_notify\";s:0:\"\";s:25:\"comment_email_notify_mods\";a:0:{}s:17:\"comment_url_limit\";i:0;s:19:\"comment_daily_limit\";i:0;s:19:\"comment_avatar_size\";i:16;s:12:\"comment_hide\";i:3;s:12:\"comment_bury\";i:10;s:15:\"comment_avatars\";s:0:\"\";}','2017-02-08 15:12:53',1),(8,'recaptcha','recaptcha_settings','a:2:{s:6:\"pubkey\";s:0:\"\";s:7:\"privkey\";s:0:\"\";}','2017-02-08 15:12:54',1),(9,'gravatar','gravatar_settings','a:1:{s:14:\"default_avatar\";s:9:\"identicon\";}','2017-02-08 15:12:59',1),(10,'akismet','akismet_settings','a:3:{s:17:\"akismet_use_posts\";s:0:\"\";s:20:\"akismet_use_comments\";s:0:\"\";s:11:\"akismet_key\";s:0:\"\";}','2017-02-08 15:13:01',1),(11,'related_posts','submit_related_posts_submit','10','2017-02-08 15:13:02',1),(12,'related_posts','submit_related_posts_post','5','2017-02-08 15:13:02',1),(13,'widgets','widgets_settings','a:1:{s:7:\"widgets\";a:1:{s:6:\"search\";a:7:{s:5:\"order\";i:1;s:5:\"block\";i:1;s:7:\"enabled\";b:1;s:6:\"plugin\";s:6:\"search\";s:5:\"class\";s:6:\"Search\";s:8:\"function\";s:6:\"search\";s:4:\"args\";s:0:\"\";}}}','2017-02-08 15:16:20',1),(14,'post_images','post_images_settings','a:11:{s:1:\"w\";i:75;s:1:\"h\";i:75;s:7:\"quality\";i:90;s:6:\"memory\";s:3:\"16M\";s:7:\"default\";s:2:\"no\";s:11:\"default_url\";s:62:\"http://localhost/hotaru/content/images/post_images/default.jpg\";s:17:\"sitethumbshot_key\";s:0:\"\";s:18:\"sitethumbshot_size\";s:1:\"T\";s:21:\"show_in_related_posts\";s:9:\"unchecked\";s:20:\"show_in_posts_widget\";s:9:\"unchecked\";s:21:\"post_images_pullRight\";s:9:\"unchecked\";}','2017-02-08 15:18:16',1),(15,'vote','vote_settings','a:9:{s:11:\"submit_vote\";s:7:\"checked\";s:17:\"submit_vote_value\";s:1:\"1\";s:16:\"votes_to_promote\";s:1:\"5\";s:10:\"use_demote\";s:0:\"\";s:17:\"upcoming_duration\";s:1:\"5\";s:13:\"no_front_page\";s:1:\"5\";s:12:\"posts_widget\";s:7:\"checked\";s:17:\"vote_on_url_click\";s:0:\"\";s:14:\"vote_anon_vote\";s:7:\"checked\";}','2017-02-08 16:54:53',1),(16,'updown_voting','updown_voting_settings','a:10:{s:11:\"submit_vote\";s:7:\"checked\";s:17:\"submit_vote_value\";i:1;s:16:\"votes_to_promote\";i:5;s:10:\"use_demote\";s:0:\"\";s:10:\"use_alerts\";s:7:\"checked\";s:14:\"alerts_to_bury\";i:5;s:15:\"physical_delete\";s:0:\"\";s:17:\"upcoming_duration\";i:5;s:13:\"no_front_page\";i:5;s:12:\"posts_widget\";s:7:\"checked\";}','2017-02-08 16:20:35',1);
/*!40000 ALTER TABLE `hotaru_pluginsettings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hotaru_postmeta`
--

DROP TABLE IF EXISTS `hotaru_postmeta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hotaru_postmeta` (
  `postmeta_id` int(20) NOT NULL AUTO_INCREMENT,
  `postmeta_archived` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  `postmeta_updatedts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `postmeta_postid` int(20) NOT NULL DEFAULT '0',
  `postmeta_key` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `postmeta_value` text COLLATE utf8_unicode_ci,
  `postmeta_updateby` int(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`postmeta_id`),
  KEY `postmeta_postid` (`postmeta_postid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Post Meta';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hotaru_postmeta`
--

LOCK TABLES `hotaru_postmeta` WRITE;
/*!40000 ALTER TABLE `hotaru_postmeta` DISABLE KEYS */;
/*!40000 ALTER TABLE `hotaru_postmeta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hotaru_posts`
--

DROP TABLE IF EXISTS `hotaru_posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hotaru_posts` (
  `post_id` int(20) NOT NULL AUTO_INCREMENT,
  `post_archived` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  `post_updatedts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `post_author` int(20) NOT NULL DEFAULT '0',
  `post_date` timestamp NULL DEFAULT NULL,
  `post_pub_date` timestamp NULL DEFAULT NULL,
  `post_status` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'processing',
  `post_type` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `post_category` int(20) NOT NULL DEFAULT '1',
  `post_tags` text COLLATE utf8_unicode_ci,
  `post_title` text COLLATE utf8_unicode_ci,
  `post_orig_url` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `post_domain` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `post_url` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `post_content` text COLLATE utf8_unicode_ci,
  `post_votes_up` smallint(11) NOT NULL DEFAULT '0',
  `post_votes_down` smallint(11) NOT NULL DEFAULT '0',
  `post_comments_count` smallint(11) NOT NULL DEFAULT '0',
  `post_comments` enum('open','closed') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'open',
  `post_img` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `post_subscribe` tinyint(1) NOT NULL DEFAULT '0',
  `post_updateby` int(20) NOT NULL DEFAULT '0',
  `post_lat` float(8,6) DEFAULT NULL,
  `post_lng` float(8,6) DEFAULT NULL,
  PRIMARY KEY (`post_id`),
  KEY `post_archived` (`post_archived`),
  KEY `post_status` (`post_status`),
  KEY `post_type` (`post_type`),
  KEY `post_category` (`post_category`),
  KEY `post_author` (`post_author`),
  FULLTEXT KEY `post_title` (`post_title`,`post_domain`,`post_url`,`post_content`,`post_tags`)
) ENGINE=MyISAM AUTO_INCREMENT=26 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Story Posts';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hotaru_posts`
--

LOCK TABLES `hotaru_posts` WRITE;
/*!40000 ALTER TABLE `hotaru_posts` DISABLE KEYS */;
INSERT INTO `hotaru_posts` VALUES (1,'N','2017-02-08 21:04:29',1,'2017-02-08 15:30:12',NULL,'new','news',2,'tag','Google','http%3A%2F%2Fgoogle.com','http%3A%2F%2Fgoogle.com','google','Search+the+world%27s+information%2C+including+webpages%2C+images%2C+videos+and+more.+Google+has+many+special+features+to+help+you+find+exactly+what+you%27re+looking+for.',1,0,0,'open','',0,1,35.000000,-78.000000),(2,'N','2017-02-08 17:35:24',1,'2017-02-08 15:36:03',NULL,'new','news',2,'tag2','Hello+World','http%3A%2F%2Flocalhost%2Fhotaru%2Findex.php%3Fpage%3D2','http%3A%2F%2Flocalhost','hello-world','This+is+a+test+and+it+requires+a+certain+amount+of+text.%3Cbr+%2F%3E',1,0,0,'open','',0,1,35.798176,-78.845039),(3,'N','2017-02-08 17:35:26',1,'2017-02-08 16:34:58',NULL,'top','news',2,'tag','stff','http%3A%2F%2Flocalhost%2Fhotaru%2Findex.php%3Fpage%3D3','http%3A%2F%2Flocalhost','stff','asdf+adsf+adsf+asd+fad+daf+sad+f+df+asd+f+adf+adf+adf+ad+fad+fad+fdaf+%3Cbr+%2F%3E',1,0,0,'open','',0,1,35.798176,-78.843033),(25,'N','2017-02-08 22:24:54',1,'2017-02-08 21:52:53',NULL,'new','news',1,'tag','This+target+store+really+rocks%21','http%3A%2F%2Flocalhost%2Fhotaru%2Findex.php%3Fpage%3D25','http%3A%2F%2Flocalhost','this-target-store-really-rocks','https%3A%2F%2Fdevelopers.google.com%2Fmaps%2Fdocumentation%2Fjavascript%2Fgeolocation%3Cbr+%2F%3E%0D%0AThis+is+old+but+I+was+able+to+hack+it+together+using+a+library+found+from+searching+on+error%3A%3Cbr+%2F%3E%0D%0Ahttps%3A%2F%2Fdevelopers.google.com%2Fmaps%2Fdocumentation%2Fjavascript%2Fmysql-to-maps%23putting-it-all-together%3Cbr+%2F%3E%0D%0A%3Cbr+%2F%3E',1,0,0,'open','',0,1,35.805347,-78.815346),(23,'N','2017-02-08 22:24:54',1,'2017-02-08 21:41:59',NULL,'new','news',1,'tag','ljk%3Basdflj+adlsjkldj%3Bfadsfjldsfljk+lkjdsf','http%3A%2F%2Flocalhost%2Fhotaru%2Findex.php%3Fpage%3D23','http%3A%2F%2Flocalhost','ljkasdflj-adlsjkldjfadsfjldsfljk-lkjdsf','https%3A%2F%2Fdevelopers.google.com%2Fmaps%2Fdocumentation%2Fjavascript%2Fgeolocation%3Cbr+%2F%3E%0D%0AThis+is+old+but+I+was+able+to+hack+it+together+using+a+library+found+from+searching+on+error%3A%3Cbr+%2F%3E%0D%0Ahttps%3A%2F%2Fdevelopers.google.com%2Fmaps%2Fdocumentation%2Fjavascript%2Fmysql-to-maps%23putting-it-all-together%3Cbr+%2F%3E%0D%0A%3Cbr+%2F%3E',1,0,0,'open','',0,1,35.000000,-78.000000),(22,'N','2017-02-08 22:24:54',1,'2017-02-08 21:24:29',NULL,'new','news',1,'tag','ljdafl+dfjlladsfj+ldjf','http%3A%2F%2Flocalhost%2Fhotaru%2Findex.php%3Fpage%3D22','http%3A%2F%2Flocalhost','ljdafl-dfjlladsfj-ldjf','https%3A%2F%2Fdevelopers.google.com%2Fmaps%2Fdocumentation%2Fjavascript%2Fgeolocation%3Cbr+%2F%3E%0D%0AThis+is+old+but+I+was+able+to+hack+it+together+using+a+library+found+from+searching+on+error%3A%3Cbr+%2F%3E%0D%0Ahttps%3A%2F%2Fdevelopers.google.com%2Fmaps%2Fdocumentation%2Fjavascript%2Fmysql-to-maps%23putting-it-all-together%3Cbr+%2F%3E%0D%0A%3Cbr+%2F%3E',1,0,0,'open','',0,1,35.000000,-78.000000),(24,'N','2017-02-08 22:24:54',1,'2017-02-08 21:47:14',NULL,'new','news',1,'tag','lj+dalfjldajfldajfljd+f+ldljladsfjladjsf','http%3A%2F%2Flocalhost%2Fhotaru%2Findex.php%3Fpage%3D24','http%3A%2F%2Flocalhost','lj-dalfjldajfldajfljd-f-ldljladsfjladjsf','https%3A%2F%2Fdevelopers.google.com%2Fmaps%2Fdocumentation%2Fjavascript%2Fgeolocation%3Cbr+%2F%3E%0D%0AThis+is+old+but+I+was+able+to+hack+it+together+using+a+library+found+from+searching+on+error%3A%3Cbr+%2F%3E%0D%0Ahttps%3A%2F%2Fdevelopers.google.com%2Fmaps%2Fdocumentation%2Fjavascript%2Fmysql-to-maps%23putting-it-all-together%3Cbr+%2F%3E%0D%0A%3Cbr+%2F%3E',1,0,0,'open','',0,1,35.743198,-78.855003);
/*!40000 ALTER TABLE `hotaru_posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hotaru_postvotes`
--

DROP TABLE IF EXISTS `hotaru_postvotes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hotaru_postvotes` (
  `vote_updatedts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `vote_archived` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  `vote_post_id` int(11) NOT NULL DEFAULT '0',
  `vote_user_id` int(11) NOT NULL DEFAULT '0',
  `vote_user_ip` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `vote_date` timestamp NULL DEFAULT NULL,
  `vote_type` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `vote_rating` smallint(11) NOT NULL DEFAULT '0',
  `vote_reason` tinyint(3) NOT NULL DEFAULT '0',
  `vote_updateby` int(20) NOT NULL DEFAULT '0',
  KEY `vote_post_id` (`vote_post_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Post Votes';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hotaru_postvotes`
--

LOCK TABLES `hotaru_postvotes` WRITE;
/*!40000 ALTER TABLE `hotaru_postvotes` DISABLE KEYS */;
INSERT INTO `hotaru_postvotes` VALUES ('2017-02-08 15:30:12','N',1,1,'127.0.0.1','2017-02-08 15:30:12','vote',10,0,1),('2017-02-08 16:54:12','N',2,1,'127.0.0.1','2017-02-08 16:54:12','vote',10,0,1),('2017-02-08 16:56:46','N',3,0,'127.0.0.1','2017-02-08 16:56:46','vote',10,0,0),('2017-02-08 18:14:34','N',4,1,'127.0.0.1','2017-02-08 18:14:34','vote',10,0,1),('2017-02-08 18:57:14','N',5,1,'127.0.0.1','2017-02-08 18:57:14','vote',10,0,1),('2017-02-08 19:04:23','N',6,1,'127.0.0.1','2017-02-08 19:04:23','vote',10,0,1),('2017-02-08 19:21:47','N',7,1,'127.0.0.1','2017-02-08 19:21:47','vote',10,0,1),('2017-02-08 19:25:11','N',8,1,'127.0.0.1','2017-02-08 19:25:11','vote',10,0,1),('2017-02-08 19:28:12','N',9,1,'127.0.0.1','2017-02-08 19:28:12','vote',10,0,1),('2017-02-08 19:34:00','N',10,1,'127.0.0.1','2017-02-08 19:34:00','vote',10,0,1),('2017-02-08 19:43:53','N',11,1,'127.0.0.1','2017-02-08 19:43:53','vote',10,0,1),('2017-02-08 19:53:43','N',12,1,'127.0.0.1','2017-02-08 19:53:43','vote',10,0,1),('2017-02-08 19:59:08','N',13,1,'127.0.0.1','2017-02-08 19:59:08','vote',10,0,1),('2017-02-08 20:20:58','N',14,1,'127.0.0.1','2017-02-08 20:20:58','vote',10,0,1),('2017-02-08 20:24:27','N',36,1,'127.0.0.1','2017-02-08 20:24:27','vote',10,0,1),('2017-02-08 20:27:27','N',15,1,'127.0.0.1','2017-02-08 20:27:27','vote',10,0,1),('2017-02-08 20:42:50','N',16,1,'127.0.0.1','2017-02-08 20:42:50','vote',10,0,1),('2017-02-08 20:47:36','N',17,1,'127.0.0.1','2017-02-08 20:47:36','vote',10,0,1),('2017-02-08 20:57:09','N',18,1,'127.0.0.1','2017-02-08 20:57:09','vote',10,0,1),('2017-02-08 21:00:25','N',19,1,'127.0.0.1','2017-02-08 21:00:25','vote',10,0,1),('2017-02-08 21:11:46','N',20,1,'127.0.0.1','2017-02-08 21:11:46','vote',10,0,1),('2017-02-08 21:21:48','N',21,1,'127.0.0.1','2017-02-08 21:21:48','vote',10,0,1),('2017-02-08 21:24:30','N',22,1,'127.0.0.1','2017-02-08 21:24:30','vote',10,0,1),('2017-02-08 21:41:59','N',23,1,'127.0.0.1','2017-02-08 21:41:59','vote',10,0,1),('2017-02-08 21:47:14','N',24,1,'127.0.0.1','2017-02-08 21:47:14','vote',10,0,1),('2017-02-08 21:52:53','N',25,1,'127.0.0.1','2017-02-08 21:52:53','vote',10,0,1);
/*!40000 ALTER TABLE `hotaru_postvotes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hotaru_settings`
--

DROP TABLE IF EXISTS `hotaru_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hotaru_settings` (
  `settings_id` int(20) NOT NULL AUTO_INCREMENT,
  `settings_name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `settings_type` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `settings_subType` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `settings_value` text COLLATE utf8_unicode_ci,
  `settings_default` text COLLATE utf8_unicode_ci,
  `settings_note` text COLLATE utf8_unicode_ci,
  `settings_show` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Y',
  `settings_updatedts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `settings_updateby` int(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`settings_id`),
  UNIQUE KEY `key` (`settings_name`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Application Settings';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hotaru_settings`
--

LOCK TABLES `hotaru_settings` WRITE;
/*!40000 ALTER TABLE `hotaru_settings` DISABLE KEYS */;
INSERT INTO `hotaru_settings` VALUES (1,'SITE_OPEN','','','true','true','','Y','2017-02-08 15:10:55',0),(2,'SITE_NAME','','','Hotaru CMS','Hotaru CMS','','Y','2017-02-08 15:10:55',0),(3,'THEME','','','default/','default/','You need the \"/\"','Y','2017-02-08 15:10:55',0),(4,'ADMIN_THEME','','','admin_default/','admin_default/','You need the \"/\"','Y','2017-02-08 15:10:55',0),(5,'DEBUG','','','false','false','','Y','2017-02-08 15:10:55',0),(6,'FRIENDLY_URLS','','','false','false','','Y','2017-02-08 15:10:55',0),(7,'DB_CACHE','Perf','Cache','false','false','','Y','2017-02-08 15:10:55',0),(8,'DB_CACHE_DURATION','Perf','Cache','12','12','Hours','Y','2017-02-08 15:10:55',0),(9,'CSS_JS_CACHE','Perf','Cache','true','true','','Y','2017-02-08 15:10:55',0),(10,'HTML_CACHE','Perf','Cache','true','true','','Y','2017-02-08 15:10:55',0),(11,'LANG_CACHE','Perf','Cache','true','true','','Y','2017-02-08 15:10:55',0),(12,'RSS_CACHE','Perf','Cache','true','true','','Y','2017-02-08 15:10:55',0),(13,'RSS_CACHE_DURATION','Perf','Cache','60','60','Minutes','Y','2017-02-08 15:10:55',0),(14,'SITE_EMAIL','','','beez@nc.rr.com','email@example.com','Must be changed','Y','2017-02-08 15:11:44',1),(15,'SMTP','Mail','','false','false','Email auth','Y','2017-02-08 15:10:55',0),(16,'SMTP_HOST','Mail','','mail.example.com','mail.example.com','','Y','2017-02-08 15:10:56',0),(17,'SMTP_PORT','Mail','','25','25','','Y','2017-02-08 15:10:56',0),(18,'SMTP_USERNAME','Mail','','','','','Y','2017-02-08 15:10:56',0),(19,'SMTP_PASSWORD','Mail','','','','','Y','2017-02-08 15:10:56',0),(20,'FTP_SITE','Security','',' ',' ','Optional','Y','2017-02-08 15:10:56',0),(21,'FTP_USERNAME','Security','','','','Optional','Y','2017-02-08 15:10:56',0),(22,'FTP_PASSWORD','Security','','','','Optional','Y','2017-02-08 15:10:56',0),(23,'REST_API','Security','','false','false','','Y','2017-02-08 15:10:56',0),(24,'FORUM_USERNAME','Security','','','','Need for auto updates','Y','2017-02-08 15:10:56',0),(25,'FORUM_PASSWORD','Security','','','','Need for auto updates','Y','2017-02-08 15:10:56',0),(26,'JQUERY_PATH','Perf','Files','','','','Y','2017-02-08 15:10:56',0),(27,'BOOTSTRAP_PATH','Perf','Files','','','','Y','2017-02-08 15:10:56',0),(28,'MINIFY_JS','Perf','Scripts','false','false','','Y','2017-02-08 15:10:56',0),(29,'MINIFY_CSS','Perf','Scripts','false','false','','Y','2017-02-08 15:10:56',0),(30,'HOTARU_API_KEY','Security','','','','','Y','2017-02-08 15:10:57',0),(31,'HOTARUCMS_COM_CONNECTED','Security','','false','false','','Y','2017-02-08 15:10:57',0);
/*!40000 ALTER TABLE `hotaru_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hotaru_spamlog`
--

DROP TABLE IF EXISTS `hotaru_spamlog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hotaru_spamlog` (
  `spamlog_id` int(20) NOT NULL AUTO_INCREMENT,
  `spamlog_email` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `spamlog_pluginfolder` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `spamlog_type` tinyint(1) NOT NULL DEFAULT '0',
  `spamlog_updatedts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`spamlog_id`),
  KEY `spamlog_pluginfolder` (`spamlog_pluginfolder`),
  KEY `spamlog_type` (`spamlog_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='SpamLog';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hotaru_spamlog`
--

LOCK TABLES `hotaru_spamlog` WRITE;
/*!40000 ALTER TABLE `hotaru_spamlog` DISABLE KEYS */;
/*!40000 ALTER TABLE `hotaru_spamlog` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hotaru_tags`
--

DROP TABLE IF EXISTS `hotaru_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hotaru_tags` (
  `tags_post_id` int(11) NOT NULL DEFAULT '0',
  `tags_archived` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  `tags_updatedts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `tags_date` timestamp NULL DEFAULT NULL,
  `tags_word` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `tags_updateby` int(20) NOT NULL DEFAULT '0',
  UNIQUE KEY `tags_post_id` (`tags_post_id`,`tags_word`),
  KEY `tags_archived` (`tags_archived`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Post Tags';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hotaru_tags`
--

LOCK TABLES `hotaru_tags` WRITE;
/*!40000 ALTER TABLE `hotaru_tags` DISABLE KEYS */;
INSERT INTO `hotaru_tags` VALUES (1,'N','2017-02-08 21:04:29','2017-02-08 21:04:29','tag',1),(2,'N','2017-02-08 15:36:03','2017-02-08 15:36:03','tag2',1),(3,'N','2017-02-08 16:34:58','2017-02-08 16:34:58','tag',1),(4,'N','2017-02-08 18:14:34','2017-02-08 18:14:34','tag',1),(5,'N','2017-02-08 18:57:14','2017-02-08 18:57:14','tag',1),(6,'N','2017-02-08 19:04:23','2017-02-08 19:04:23','tag',1),(7,'N','2017-02-08 19:21:47','2017-02-08 19:21:47','tag2',1),(8,'N','2017-02-08 19:25:11','2017-02-08 19:25:11','tag2',1),(9,'N','2017-02-08 19:28:12','2017-02-08 19:28:12','tag',1),(10,'N','2017-02-08 19:34:00','2017-02-08 19:34:00','tag',1),(11,'N','2017-02-08 19:43:53','2017-02-08 19:43:53','tag',1),(12,'N','2017-02-08 19:53:43','2017-02-08 19:53:43','tag2',1),(13,'N','2017-02-08 19:59:08','2017-02-08 19:59:08','tag',1),(14,'N','2017-02-08 20:20:58','2017-02-08 20:20:58','tag',1),(15,'N','2017-02-08 20:27:27','2017-02-08 20:27:27','tag',1),(16,'N','2017-02-08 20:42:50','2017-02-08 20:42:50','tag',1),(17,'N','2017-02-08 20:47:36','2017-02-08 20:47:36','tag',1),(18,'N','2017-02-08 20:57:09','2017-02-08 20:57:09','tag',1),(19,'N','2017-02-08 21:00:25','2017-02-08 21:00:25','tag2',1),(20,'N','2017-02-08 21:11:45','2017-02-08 21:11:45','tag',1),(21,'N','2017-02-08 21:21:48','2017-02-08 21:21:48','tag',1),(22,'N','2017-02-08 21:24:29','2017-02-08 21:24:29','tag',1),(23,'N','2017-02-08 21:41:59','2017-02-08 21:41:59','tag',1),(24,'N','2017-02-08 21:47:14','2017-02-08 21:47:14','tag',1),(25,'N','2017-02-08 21:52:53','2017-02-08 21:52:53','tag',1),(36,'N','2017-02-08 20:24:27','2017-02-08 20:24:27','tag',1);
/*!40000 ALTER TABLE `hotaru_tags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hotaru_tempdata`
--

DROP TABLE IF EXISTS `hotaru_tempdata`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hotaru_tempdata` (
  `tempdata_id` int(20) NOT NULL AUTO_INCREMENT,
  `tempdata_key` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tempdata_value` text COLLATE utf8_unicode_ci,
  `tempdata_updatedts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `tempdata_updateby` int(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`tempdata_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Temporary Data';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hotaru_tempdata`
--

LOCK TABLES `hotaru_tempdata` WRITE;
/*!40000 ALTER TABLE `hotaru_tempdata` DISABLE KEYS */;
INSERT INTO `hotaru_tempdata` VALUES (2,'24d6167aa80b67aeb090afd24f940320','a:12:{s:15:\"submit_orig_url\";s:0:\"\";s:16:\"submit_editorial\";b:1;s:12:\"submit_title\";s:41:\"ljk;asdflj adlsjkldj;fadsfjldsfljk lkjdsf\";s:14:\"submit_content\";s:293:\"https://developers.google.com/maps/documentation/javascript/geolocation<br />\r\nThis is old but I was able to hack it together using a library found from searching on error:<br />\r\nhttps://developers.google.com/maps/documentation/javascript/mysql-to-maps#putting-it-all-together<br />\r\n<br />\r\n\";s:10:\"submit_lng\";d:-78.832539999999995;s:10:\"submit_lat\";d:35.767499999999998;s:11:\"submit_tags\";s:3:\"tag\";s:9:\"submit_id\";b:0;s:16:\"submit_subscribe\";i:0;s:15:\"submit_comments\";s:4:\"open\";s:10:\"submit_img\";s:0:\"\";s:17:\"submit_img_coords\";s:0:\"\";}','2017-02-08 21:41:58',1),(3,'1c4c70aae4dfbfa5bde5b72696564acc','a:2:{s:15:\"submit_orig_url\";s:0:\"\";s:16:\"submit_editorial\";b:1;}','2017-02-08 21:46:51',1),(4,'0a63864f8ecb31e383bbe4b3c3168cb5','a:12:{s:15:\"submit_orig_url\";s:0:\"\";s:16:\"submit_editorial\";b:1;s:12:\"submit_title\";s:40:\"lj dalfjldajfldajfljd f ldljladsfjladjsf\";s:14:\"submit_content\";s:293:\"https://developers.google.com/maps/documentation/javascript/geolocation<br />\r\nThis is old but I was able to hack it together using a library found from searching on error:<br />\r\nhttps://developers.google.com/maps/documentation/javascript/mysql-to-maps#putting-it-all-together<br />\r\n<br />\r\n\";s:10:\"submit_lng\";d:-78.855000000000004;s:10:\"submit_lat\";d:35.743200000000002;s:11:\"submit_tags\";s:3:\"tag\";s:9:\"submit_id\";b:0;s:16:\"submit_subscribe\";i:0;s:15:\"submit_comments\";s:4:\"open\";s:10:\"submit_img\";s:0:\"\";s:17:\"submit_img_coords\";s:0:\"\";}','2017-02-08 21:47:13',1),(5,'a5cf4b44c2529866bd49ef4f4d61c52e','a:2:{s:15:\"submit_orig_url\";s:0:\"\";s:16:\"submit_editorial\";b:1;}','2017-02-08 21:51:46',1),(6,'e5c2c471fb1913971792b10fc96d9541','a:12:{s:15:\"submit_orig_url\";s:0:\"\";s:16:\"submit_editorial\";b:1;s:12:\"submit_title\";s:31:\"This target store really rocks!\";s:14:\"submit_content\";s:293:\"https://developers.google.com/maps/documentation/javascript/geolocation<br />\r\nThis is old but I was able to hack it together using a library found from searching on error:<br />\r\nhttps://developers.google.com/maps/documentation/javascript/mysql-to-maps#putting-it-all-together<br />\r\n<br />\r\n\";s:10:\"submit_lng\";d:-78.815344999999994;s:10:\"submit_lat\";d:35.805349;s:11:\"submit_tags\";s:3:\"tag\";s:9:\"submit_id\";b:0;s:16:\"submit_subscribe\";i:0;s:15:\"submit_comments\";s:4:\"open\";s:10:\"submit_img\";s:0:\"\";s:17:\"submit_img_coords\";s:0:\"\";}','2017-02-08 21:52:51',1),(7,'70090a2625f271491a115be2af1bda4d','a:2:{s:15:\"submit_orig_url\";s:0:\"\";s:16:\"submit_editorial\";b:1;}','2017-02-08 22:01:09',1);
/*!40000 ALTER TABLE `hotaru_tempdata` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hotaru_useractivity`
--

DROP TABLE IF EXISTS `hotaru_useractivity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hotaru_useractivity` (
  `useract_id` int(20) NOT NULL AUTO_INCREMENT,
  `useract_archived` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  `useract_updatedts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `useract_userid` int(20) NOT NULL DEFAULT '0',
  `useract_status` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'show',
  `useract_key` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `useract_value` text COLLATE utf8_unicode_ci,
  `useract_key2` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `useract_value2` text COLLATE utf8_unicode_ci,
  `useract_date` timestamp NULL DEFAULT NULL,
  `useract_updateby` int(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`useract_id`),
  KEY `useract_userid` (`useract_userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='User Activity';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hotaru_useractivity`
--

LOCK TABLES `hotaru_useractivity` WRITE;
/*!40000 ALTER TABLE `hotaru_useractivity` DISABLE KEYS */;
/*!40000 ALTER TABLE `hotaru_useractivity` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hotaru_userclaim`
--

DROP TABLE IF EXISTS `hotaru_userclaim`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hotaru_userclaim` (
  `claim_id` int(20) NOT NULL AUTO_INCREMENT,
  `user_id` int(20) NOT NULL,
  `claim_type` text COLLATE utf8_unicode_ci,
  `claim_value` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`claim_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='UserClaim for login';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hotaru_userclaim`
--

LOCK TABLES `hotaru_userclaim` WRITE;
/*!40000 ALTER TABLE `hotaru_userclaim` DISABLE KEYS */;
/*!40000 ALTER TABLE `hotaru_userclaim` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hotaru_userlogin`
--

DROP TABLE IF EXISTS `hotaru_userlogin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hotaru_userlogin` (
  `user_id` int(20) NOT NULL,
  `login_provider` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `provider_key` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='3rd Party UserLogin Providers';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hotaru_userlogin`
--

LOCK TABLES `hotaru_userlogin` WRITE;
/*!40000 ALTER TABLE `hotaru_userlogin` DISABLE KEYS */;
/*!40000 ALTER TABLE `hotaru_userlogin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hotaru_usermeta`
--

DROP TABLE IF EXISTS `hotaru_usermeta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hotaru_usermeta` (
  `usermeta_id` int(20) NOT NULL AUTO_INCREMENT,
  `usermeta_userid` int(20) NOT NULL DEFAULT '0',
  `usermeta_key` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `usermeta_value` text COLLATE utf8_unicode_ci,
  `usermeta_updatedts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `usermeta_updateby` int(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`usermeta_id`),
  KEY `usermeta_userid` (`usermeta_userid`),
  KEY `usermeta_key` (`usermeta_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='User Meta';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hotaru_usermeta`
--

LOCK TABLES `hotaru_usermeta` WRITE;
/*!40000 ALTER TABLE `hotaru_usermeta` DISABLE KEYS */;
/*!40000 ALTER TABLE `hotaru_usermeta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hotaru_users`
--

DROP TABLE IF EXISTS `hotaru_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hotaru_users` (
  `user_id` int(20) NOT NULL AUTO_INCREMENT,
  `user_username` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_role` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'member',
  `user_date` timestamp NULL DEFAULT NULL,
  `user_password` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `user_password_conf` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password_version` tinyint(1) NOT NULL DEFAULT '2',
  `user_email` varchar(128) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `user_email_valid` tinyint(3) NOT NULL DEFAULT '0',
  `user_email_conf` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_is_locked_out` tinyint(1) NOT NULL DEFAULT '0',
  `user_permissions` text COLLATE utf8_unicode_ci,
  `user_ip` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `user_access_failed_count` tinyint(1) NOT NULL DEFAULT '0',
  `user_lastlogin` timestamp NULL DEFAULT NULL,
  `user_lastvisit` timestamp NULL DEFAULT NULL,
  `user_last_password_changed_date` timestamp NULL DEFAULT NULL,
  `user_lockout_date` timestamp NULL DEFAULT NULL,
  `user_updatedts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `user_updateby` int(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `key` (`user_username`),
  KEY `user_email` (`user_email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Users and Roles';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hotaru_users`
--

LOCK TABLES `hotaru_users` WRITE;
/*!40000 ALTER TABLE `hotaru_users` DISABLE KEYS */;
INSERT INTO `hotaru_users` VALUES (1,'admin','admin','2017-02-08 15:11:07','$2y$10$2OF8zVHPOYsrA0Zozxfdl.Nf3CpU62E.Cd9UcQAm/JORJxwa2ixhy',NULL,2,'beez@nc.rr.com',1,NULL,0,'a:1:{s:16:\"can_access_admin\";s:3:\"yes\";}','127.0.0.1',0,'2017-02-08 19:03:20','2017-02-08 19:03:20',NULL,NULL,'2017-02-08 19:03:20',1);
/*!40000 ALTER TABLE `hotaru_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hotaru_widgets`
--

DROP TABLE IF EXISTS `hotaru_widgets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hotaru_widgets` (
  `widget_id` int(20) NOT NULL AUTO_INCREMENT,
  `widget_updatedts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `widget_plugin` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `widget_function` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `widget_args` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `widget_updateby` int(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`widget_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Widgets';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hotaru_widgets`
--

LOCK TABLES `hotaru_widgets` WRITE;
/*!40000 ALTER TABLE `hotaru_widgets` DISABLE KEYS */;
INSERT INTO `hotaru_widgets` VALUES (1,'2017-02-08 15:13:00','search','search','',1);
/*!40000 ALTER TABLE `hotaru_widgets` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-02-08 17:47:26
