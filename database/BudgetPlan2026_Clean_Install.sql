/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19  Distrib 10.6.25-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: skjacth_budgetplan
-- ------------------------------------------------------
-- Server version	10.6.25-MariaDB-ubu2204

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
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `batch` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tb_admin_rloes`
--

DROP TABLE IF EXISTS `tb_admin_rloes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `tb_admin_rloes` (
  `admin_rloes_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `admin_rloes_userid` varchar(50) NOT NULL COMMENT 'α╕¡α╣ëα╕▓α╕çα╕¡α╕┤α╕ç tb_personnel.pers_id',
  `admin_rloes_status` varchar(50) NOT NULL DEFAULT 'Member' COMMENT 'admin, manager, superadmin, Member',
  `admin_rloes_nanetype` varchar(255) DEFAULT NULL COMMENT 'α╕èα╕╖α╣êα╕¡α╣Çα╕úα╕╡α╕óα╕ü role',
  `admin_rloes_level` int(11) unsigned NOT NULL DEFAULT 0 COMMENT 'α╕Ñα╕│α╕öα╕▒α╕Üα╕äα╕ºα╕▓α╕íα╕¬α╕│α╕äα╕▒α╕ì (α╕Öα╣ëα╕¡α╕ó = α╕¬α╕╣α╕çα╕¬α╕╕α╕ö)',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`admin_rloes_id`),
  KEY `idx_userid` (`admin_rloes_userid`),
  KEY `idx_status` (`admin_rloes_status`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tb_admin_system_access`
--

DROP TABLE IF EXISTS `tb_admin_system_access`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `tb_admin_system_access` (
  `admin_access_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `admin_access_userid` varchar(50) NOT NULL,
  `admin_access_system_code` varchar(100) NOT NULL,
  `admin_access_view` tinyint(1) NOT NULL DEFAULT 1,
  `admin_access_create` tinyint(1) NOT NULL DEFAULT 0,
  `admin_access_update` tinyint(1) NOT NULL DEFAULT 0,
  `admin_access_delete` tinyint(1) NOT NULL DEFAULT 0,
  `admin_access_approve` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`admin_access_id`),
  UNIQUE KEY `uk_access_user_system` (`admin_access_userid`,`admin_access_system_code`),
  KEY `idx_access_user` (`admin_access_userid`),
  KEY `idx_access_system` (`admin_access_system_code`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tb_admin_systems`
--

DROP TABLE IF EXISTS `tb_admin_systems`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `tb_admin_systems` (
  `admin_system_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `admin_system_code` varchar(100) NOT NULL,
  `admin_system_name` varchar(255) NOT NULL,
  `admin_system_description` varchar(500) DEFAULT NULL,
  `admin_system_icon` varchar(100) DEFAULT NULL,
  `admin_system_url` varchar(255) DEFAULT NULL,
  `admin_system_level` int(10) unsigned NOT NULL DEFAULT 1,
  `admin_system_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`admin_system_id`),
  UNIQUE KEY `uk_system_code` (`admin_system_code`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tb_budget_ledger`
--

DROP TABLE IF EXISTS `tb_budget_ledger`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `tb_budget_ledger` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` int(10) unsigned NOT NULL,
  `sub_item_id` int(10) unsigned DEFAULT NULL,
  `ref_borrow_id` int(10) unsigned DEFAULT NULL COMMENT 'α╕¡α╣ëα╕▓α╕çα╕¡α╕┤α╕çα╕úα╕▓α╕óα╕üα╕▓α╕úα╕óα╕╖α╕íα╣Çα╕çα╕┤α╕Ö',
  `ref_doc_no` varchar(100) DEFAULT NULL COMMENT 'α╣Çα╕Ñα╕éα╕ùα╕╡α╣êα╣Çα╕¡α╕üα╕¬α╕▓α╕úα╕¡α╣ëα╕▓α╕çα╕¡α╕┤α╕ç',
  `doc_date` date DEFAULT NULL,
  `dika_no` varchar(100) DEFAULT NULL,
  `doc_no` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `amount_budget_adjust` decimal(15,2) NOT NULL DEFAULT 0.00,
  `amount_borrow` decimal(15,2) NOT NULL DEFAULT 0.00,
  `amount_return_cash` decimal(15,2) NOT NULL DEFAULT 0.00,
  `amount_return_receipt` decimal(15,2) NOT NULL DEFAULT 0.00,
  `amount_direct_withdraw` decimal(15,2) NOT NULL DEFAULT 0.00,
  `balance` decimal(15,2) NOT NULL DEFAULT 0.00,
  `notes` varchar(255) DEFAULT NULL,
  `created_by` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_ledger_project` (`project_id`),
  KEY `idx_ledger_date` (`doc_date`),
  KEY `idx_ledger_subitem` (`sub_item_id`),
  KEY `idx_ledger_ref_borrow` (`ref_borrow_id`),
  CONSTRAINT `fk_ledger_project` FOREIGN KEY (`project_id`) REFERENCES `tb_budget_projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_ledger_ref_borrow` FOREIGN KEY (`ref_borrow_id`) REFERENCES `tb_budget_ledger` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_ledger_subitem` FOREIGN KEY (`sub_item_id`) REFERENCES `tb_budget_project_sub_items` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tb_budget_ordinance_oil`
--

DROP TABLE IF EXISTS `tb_budget_ordinance_oil`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `tb_budget_ordinance_oil` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'α╕úα╕½α╕▒α╕¬α╕úα╕▓α╕óα╕üα╕▓α╕ú',
  `budget_year` varchar(10) NOT NULL DEFAULT '2569' COMMENT 'α╕¢α╕╡α╕çα╕Üα╕¢α╕úα╕░α╕íα╕▓α╕ô α╕₧.α╕¿.',
  `doc_date` date DEFAULT NULL COMMENT 'α╕ºα╕▒α╕Öα╕ùα╕╡α╣êα╣Çα╕¡α╕üα╕¬α╕▓α╕ú',
  `dika_no` varchar(100) DEFAULT NULL COMMENT 'α╣Çα╕Ñα╕éα╕ùα╕╡α╣êα╕Äα╕╡α╕üα╕▓',
  `doc_no` varchar(100) DEFAULT NULL COMMENT 'α╣Çα╕Ñα╕éα╕ùα╕╡α╣êα╕½α╕Öα╕▒α╕çα╕¬α╕╖α╕¡/α╣Çα╕¡α╕üα╕¬α╕▓α╕ú',
  `order_no` varchar(100) DEFAULT NULL COMMENT 'α╕ùα╕╡α╣êα╕äα╕│α╕¬α╕▒α╣êα╕ç/α╣Çα╕Ñα╕éα╕ùα╕╡α╣êα╣âα╕Üα╕¬α╕▒α╣êα╕ç',
  `description` text DEFAULT NULL COMMENT 'α╕úα╕▓α╕óα╕üα╕▓α╕ú/α╕úα╕▓α╕óα╕Ñα╕░α╣Çα╕¡α╕╡α╕óα╕öα╕üα╕▓α╕úα╣Çα╕öα╕┤α╕Öα╕ùα╕▓α╕ç',
  `destination` varchar(255) DEFAULT NULL COMMENT 'α╕¬α╕ûα╕▓α╕Öα╕ùα╕╡α╣êα╣äα╕¢α╕¢α╕Åα╕┤α╕Üα╕▒α╕òα╕┤α╕úα╕▓α╕èα╕üα╕▓α╕ú',
  `car_license` varchar(100) DEFAULT NULL COMMENT 'α╕½α╕íα╕▓α╕óα╣Çα╕Ñα╕éα╕ùα╕░α╣Çα╕Üα╕╡α╕óα╕Öα╕úα╕û',
  `car_type` varchar(100) DEFAULT NULL COMMENT 'α╕¢α╕úα╕░α╣Çα╕áα╕ùα╕úα╕ûα╕óα╕Öα╕òα╣î (α╣Çα╕èα╣êα╕Ö α╕òα╕╣α╣ë, α╕üα╕úα╕░α╕Üα╕░)',
  `driver_name` varchar(150) DEFAULT NULL COMMENT 'α╕èα╕╖α╣êα╕¡α╕₧α╕Öα╕▒α╕üα╕çα╕▓α╕Öα╕éα╕▒α╕Üα╕úα╕û',
  `requester_name` varchar(150) DEFAULT NULL COMMENT 'α╕èα╕╖α╣êα╕¡α╕£α╕╣α╣ëα╕éα╕¡α╣Çα╕Üα╕┤α╕ü/α╕£α╕╣α╣ëα╕äα╕ºα╕Üα╕äα╕╕α╕í',
  `fuel_type` varchar(50) DEFAULT NULL COMMENT 'α╕èα╕Öα╕┤α╕öα╕Öα╣ëα╕│α╕íα╕▒α╕Ö (α╕öα╕╡α╣Çα╕ïα╕Ñ, α╣Çα╕Üα╕Öα╕ïα╕┤α╕Ö 95 α╕»α╕Ñα╕»)',
  `fuel_liters` decimal(12,2) NOT NULL DEFAULT 0.00 COMMENT 'α╕êα╕│α╕Öα╕ºα╕Öα╕Ñα╕┤α╕òα╕ú',
  `fuel_price_per_liter` decimal(12,2) NOT NULL DEFAULT 0.00 COMMENT 'α╕úα╕▓α╕äα╕▓α╕òα╣êα╕¡α╕Ñα╕┤α╕òα╕ú',
  `amount_budget_adjust` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'α╕óα╕¡α╕öα╕òα╕▒α╣ëα╕çα╕òα╣ëα╕Ö/α╕¢α╕úα╕▒α╕Üα╣Çα╕₧α╕┤α╣êα╕íα╕çα╕Üα╕¢α╕úα╕░α╕íα╕▓α╕ô',
  `amount_withdraw` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'α╕óα╕¡α╕öα╣Çα╕Üα╕┤α╕üα╕êα╣êα╕▓α╕ó',
  `balance` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'α╕çα╕Üα╕¢α╕úα╕░α╕íα╕▓α╕ôα╕äα╕çα╣Çα╕½α╕Ñα╕╖α╕¡α╕¬α╕░α╕¬α╕í',
  `gas_station` varchar(255) DEFAULT NULL COMMENT 'α╕èα╕╖α╣êα╕¡α╕¬α╕ûα╕▓α╕Öα╕╡α╕Üα╕úα╕┤α╕üα╕▓α╕úα╕Öα╣ëα╕│α╕íα╕▒α╕Ö/α╕¢α╕▒α╣èα╕í',
  `receipt_no` varchar(100) DEFAULT NULL COMMENT 'α╣Çα╕Ñα╕éα╕ùα╕╡α╣êα╣âα╕Üα╣Çα╕¬α╕úα╣çα╕êα╕úα╕▒α╕Üα╣Çα╕çα╕┤α╕Ö',
  `notes` text DEFAULT NULL COMMENT 'α╕½α╕íα╕▓α╕óα╣Çα╕½α╕òα╕╕',
  `entry_type` enum('adjust','withdraw') NOT NULL DEFAULT 'withdraw',
  `created_by` varchar(100) DEFAULT NULL COMMENT 'α╕£α╕╣α╣ëα╕Üα╕▒α╕Öα╕ùα╕╢α╕üα╕úα╕▓α╕óα╕üα╕▓α╕ú',
  `updated_by` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL COMMENT 'α╕ºα╕▒α╕Öα╕ùα╕╡α╣êα╕¬α╕úα╣ëα╕▓α╕çα╕úα╕▓α╕óα╕üα╕▓α╕ú',
  `updated_at` datetime DEFAULT NULL COMMENT 'α╕ºα╕▒α╕Öα╕ùα╕╡α╣êα╣üα╕üα╣ëα╣äα╕éα╕úα╕▓α╕óα╕üα╕▓α╕úα╕Ñα╣êα╕▓α╕¬α╕╕α╕ö',
  PRIMARY KEY (`id`),
  KEY `idx_oil_budget_year` (`budget_year`),
  KEY `idx_oil_doc_date` (`doc_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='α╕ùα╕░α╣Çα╕Üα╕╡α╕óα╕Öα╕äα╕╕α╕íα╕çα╕Üα╕¢α╕úα╕░α╕íα╕▓α╕ôα╣Çα╕çα╕┤α╕Öα╕éα╣ëα╕¡α╕Üα╕▒α╕ìα╕ìα╕▒α╕òα╕┤ - α╣Çα╕Üα╕┤α╕üα╕äα╣êα╕▓α╕Öα╣ëα╕│α╕íα╕▒α╕Öα╣Çα╕èα╕╖α╣ëα╕¡α╣Çα╕₧α╕Ñα╕┤α╕ç';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tb_budget_ordinance_registration`
--

DROP TABLE IF EXISTS `tb_budget_ordinance_registration`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `tb_budget_ordinance_registration` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'α╕úα╕½α╕▒α╕¬α╕úα╕▓α╕óα╕üα╕▓α╕ú',
  `budget_year` varchar(10) NOT NULL DEFAULT '2569' COMMENT 'α╕¢α╕╡α╕çα╕Üα╕¢α╕úα╕░α╕íα╕▓α╕ô α╕₧.α╕¿.',
  `doc_date` date DEFAULT NULL COMMENT 'α╕ºα╕▒α╕Öα╕ùα╕╡α╣êα╣Çα╕¡α╕üα╕¬α╕▓α╕ú/α╕éα╕¡α╣Çα╕Üα╕┤α╕ü',
  `dika_no` varchar(100) DEFAULT NULL COMMENT 'α╣Çα╕Ñα╕éα╕ùα╕╡α╣êα╕Äα╕╡α╕üα╕▓',
  `doc_no` varchar(100) DEFAULT NULL COMMENT 'α╣Çα╕Ñα╕éα╕ùα╕╡α╣êα╕½α╕Öα╕▒α╕çα╕¬α╕╖α╕¡α╕éα╕¡α╕¡α╕Öα╕╕α╕íα╕▒α╕òα╕┤/α╣Çα╕èα╕┤α╕ìα╕¡α╕Üα╕úα╕í',
  `order_no` varchar(100) DEFAULT NULL COMMENT 'α╣Çα╕Ñα╕éα╕ùα╕╡α╣êα╕äα╕│α╕¬α╕▒α╣êα╕ç/α╕¬α╕▒α╣êα╕çα╕üα╕▓α╕ú',
  `description` text DEFAULT NULL COMMENT 'α╕èα╕╖α╣êα╕¡α╕çα╕▓α╕Öα╕¢α╕úα╕░α╕èα╕╕α╕í/α╕¬α╕▒α╕íα╕íα╕Öα╕▓/α╕½α╕Ñα╕▒α╕üα╕¬α╕╣α╕òα╕úα╕¥α╕╢α╕üα╕¡α╕Üα╕úα╕í',
  `organizer` varchar(255) DEFAULT NULL COMMENT 'α╕½α╕Öα╣êα╕ºα╕óα╕çα╕▓α╕Öα╕£α╕╣α╣ëα╕êα╕▒α╕ö',
  `location` varchar(255) DEFAULT NULL COMMENT 'α╕¬α╕ûα╕▓α╕Öα╕ùα╕╡α╣êα╕êα╕▒α╕öα╕¥α╕╢α╕üα╕¡α╕Üα╕úα╕í/α╕èα╣êα╕¡α╕çα╕ùα╕▓α╕ç',
  `training_start_date` date DEFAULT NULL COMMENT 'α╕ºα╕▒α╕Öα╕ùα╕╡α╣êα╣Çα╕úα╕┤α╣êα╕íα╕¡α╕Üα╕úα╕í',
  `training_end_date` date DEFAULT NULL COMMENT 'α╕ºα╕▒α╕Öα╕ùα╕╡α╣êα╕¬α╕┤α╣ëα╕Öα╕¬α╕╕α╕öα╕üα╕▓α╕úα╕¡α╕Üα╕úα╕í',
  `training_days` int(5) NOT NULL DEFAULT 1 COMMENT 'α╕êα╕│α╕Öα╕ºα╕Öα╕ºα╕▒α╕Öα╕¡α╕Üα╕úα╕í',
  `requester_name` varchar(150) DEFAULT NULL COMMENT 'α╕èα╕╖α╣êα╕¡α╕£α╕╣α╣ëα╕éα╕¡α╣Çα╕Üα╕┤α╕üα╣Çα╕çα╕┤α╕Ö',
  `attendee_names` text DEFAULT NULL COMMENT 'α╕úα╕▓α╕óα╕èα╕╖α╣êα╕¡α╕£α╕╣α╣ëα╣Çα╕éα╣ëα╕▓α╕úα╕▒α╕Üα╕üα╕▓α╕úα╕¡α╕Üα╕úα╕í',
  `attendee_count` int(5) NOT NULL DEFAULT 1 COMMENT 'α╕êα╕│α╕Öα╕ºα╕Öα╕£α╕╣α╣ëα╣Çα╕éα╣ëα╕▓α╕úα╣êα╕ºα╕í (α╕äα╕Ö)',
  `fee_per_person` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'α╕äα╣êα╕▓α╕Ñα╕çα╕ùα╕░α╣Çα╕Üα╕╡α╕óα╕Öα╕òα╣êα╕¡α╕äα╕Ö (α╕Üα╕▓α╕ù)',
  `amount_budget_adjust` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'α╕óα╕¡α╕öα╕òα╕▒α╣ëα╕çα╕òα╣ëα╕Ö/α╕¢α╕úα╕▒α╕Üα╣Çα╕₧α╕┤α╣êα╕íα╕çα╕Üα╕¢α╕úα╕░α╕íα╕▓α╕ô',
  `amount_withdraw` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'α╕óα╕¡α╕öα╣Çα╕Üα╕┤α╕üα╕êα╣êα╕▓α╕óα╕úα╕ºα╕í (α╕Üα╕▓α╕ù)',
  `balance` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'α╕çα╕Üα╕¢α╕úα╕░α╕íα╕▓α╕ôα╕äα╕çα╣Çα╕½α╕Ñα╕╖α╕¡α╕¬α╕░α╕¬α╕í (α╕Üα╕▓α╕ù)',
  `receipt_no` varchar(100) DEFAULT NULL COMMENT 'α╣Çα╕Ñα╕éα╕ùα╕╡α╣êα╣âα╕Üα╣Çα╕¬α╕úα╣çα╕êα╕úα╕▒α╕Üα╣Çα╕çα╕┤α╕Ö/α╣âα╕Üα╕¬α╕│α╕äα╕▒α╕ì',
  `notes` text DEFAULT NULL COMMENT 'α╕½α╕íα╕▓α╕óα╣Çα╕½α╕òα╕╕',
  `entry_type` enum('adjust','withdraw') NOT NULL DEFAULT 'withdraw' COMMENT 'α╕¢α╕úα╕░α╣Çα╕áα╕ùα╕úα╕▓α╕óα╕üα╕▓α╕ú (adjust=α╕¢α╕úα╕▒α╕Üα╕çα╕Ü, withdraw=α╣Çα╕Üα╕┤α╕üα╕êα╣êα╕▓α╕ó)',
  `created_by` varchar(100) DEFAULT NULL COMMENT 'α╕£α╕╣α╣ëα╕Üα╕▒α╕Öα╕ùα╕╢α╕üα╕úα╕▓α╕óα╕üα╕▓α╕ú',
  `updated_by` varchar(100) DEFAULT NULL COMMENT 'α╕£α╕╣α╣ëα╣üα╕üα╣ëα╣äα╕éα╕úα╕▓α╕óα╕üα╕▓α╕ú',
  `created_at` datetime DEFAULT NULL COMMENT 'α╕ºα╕▒α╕Öα╕ùα╕╡α╣êα╕¬α╕úα╣ëα╕▓α╕çα╕úα╕▓α╕óα╕üα╕▓α╕ú',
  `updated_at` datetime DEFAULT NULL COMMENT 'α╕ºα╕▒α╕Öα╕ùα╕╡α╣êα╣üα╕üα╣ëα╣äα╕éα╕úα╕▓α╕óα╕üα╕▓α╕úα╕Ñα╣êα╕▓α╕¬α╕╕α╕ö',
  PRIMARY KEY (`id`),
  KEY `idx_reg_budget_year` (`budget_year`),
  KEY `idx_reg_doc_date` (`doc_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='α╕ùα╕░α╣Çα╕Üα╕╡α╕óα╕Öα╕äα╕╕α╕íα╕çα╕Üα╕¢α╕úα╕░α╕íα╕▓α╕ôα╣Çα╕çα╕┤α╕Öα╕éα╣ëα╕¡α╕Üα╕▒α╕ìα╕ìα╕▒α╕òα╕┤ - α╕äα╣êα╕▓α╕Ñα╕çα╕ùα╕░α╣Çα╕Üα╕╡α╕óα╕Öα╕¡α╕Üα╕úα╕í/α╕¬α╕▒α╕íα╕íα╕Öα╕▓';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tb_budget_ordinance_travel`
--

DROP TABLE IF EXISTS `tb_budget_ordinance_travel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `tb_budget_ordinance_travel` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'α╕úα╕½α╕▒α╕¬α╕úα╕▓α╕óα╕üα╕▓α╕ú',
  `budget_year` varchar(10) NOT NULL DEFAULT '2569' COMMENT 'α╕¢α╕╡α╕çα╕Üα╕¢α╕úα╕░α╕íα╕▓α╕ô α╕₧.α╕¿.',
  `doc_date` date DEFAULT NULL COMMENT 'α╕ºα╕▒α╕Öα╕ùα╕╡α╣êα╣Çα╕¡α╕üα╕¬α╕▓α╕ú',
  `dika_no` varchar(100) DEFAULT NULL COMMENT 'α╣Çα╕Ñα╕éα╕ùα╕╡α╣êα╕Äα╕╡α╕üα╕▓',
  `doc_no` varchar(100) DEFAULT NULL COMMENT 'α╣Çα╕Ñα╕éα╕ùα╕╡α╣êα╕½α╕Öα╕▒α╕çα╕¬α╕╖α╕¡α╕éα╕¡α╕¡α╕Öα╕╕α╕íα╕▒α╕òα╕┤α╣Çα╕öα╕┤α╕Öα╕ùα╕▓α╕ç',
  `order_no` varchar(100) DEFAULT NULL COMMENT 'α╣Çα╕Ñα╕éα╕ùα╕╡α╣êα╕äα╕│α╕¬α╕▒α╣êα╕ç/α╕½α╕Öα╕▒α╕çα╕¬α╕╖α╕¡α╕¬α╕▒α╣êα╕çα╕üα╕▓α╕ú',
  `description` text DEFAULT NULL COMMENT 'α╕úα╕▓α╕óα╕üα╕▓α╕ú/α╕ºα╕▒α╕òα╕ûα╕╕α╕¢α╕úα╕░α╕¬α╕çα╕äα╣îα╕üα╕▓α╕úα╣Çα╕öα╕┤α╕Öα╕ùα╕▓α╕çα╣äα╕¢α╕úα╕▓α╕èα╕üα╕▓α╕ú',
  `destination` varchar(255) DEFAULT NULL COMMENT 'α╕¬α╕ûα╕▓α╕Öα╕ùα╕╡α╣êα╣äα╕¢α╕úα╕▓α╕èα╕üα╕▓α╕ú/α╕êα╕▒α╕çα╕½α╕ºα╕▒α╕ö',
  `travel_start_date` date DEFAULT NULL COMMENT 'α╕ºα╕▒α╕Öα╕ùα╕╡α╣êα╣Çα╕úα╕┤α╣êα╕íα╣Çα╕öα╕┤α╕Öα╕ùα╕▓α╕ç',
  `travel_end_date` date DEFAULT NULL COMMENT 'α╕ºα╕▒α╕Öα╕ùα╕╡α╣êα╕¬α╕┤α╣ëα╕Öα╕¬α╕╕α╕öα╕üα╕▓α╕úα╣Çα╕öα╕┤α╕Öα╕ùα╕▓α╕ç',
  `travel_days` int(5) NOT NULL DEFAULT 1 COMMENT 'α╕êα╕│α╕Öα╕ºα╕Öα╕ºα╕▒α╕Öα╣Çα╕öα╕┤α╕Öα╕ùα╕▓α╕ç',
  `requester_name` varchar(150) DEFAULT NULL COMMENT 'α╕èα╕╖α╣êα╕¡α╕£α╕╣α╣ëα╕éα╕¡α╣Çα╕Üα╕┤α╕ü',
  `traveler_names` text DEFAULT NULL COMMENT 'α╕úα╕▓α╕óα╕èα╕╖α╣êα╕¡α╕äα╕ôα╕░α╕£α╕╣α╣ëα╕úα╣êα╕ºα╕íα╣Çα╕öα╕┤α╕Öα╕ùα╕▓α╕ç (JSON α╕½α╕úα╕╖α╕¡α╕äα╕▒α╣êα╕Öα╕öα╣ëα╕ºα╕óα╕êα╕╕α╕Ñα╕áα╕▓α╕ä)',
  `traveler_count` int(5) NOT NULL DEFAULT 1 COMMENT 'α╕êα╕│α╕Öα╕ºα╕Öα╕£α╕╣α╣ëα╕úα╣êα╕ºα╕íα╣Çα╕öα╕┤α╕Öα╕ùα╕▓α╕ç (α╕äα╕Ö)',
  `allowance_amount` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'α╕äα╣êα╕▓α╣Çα╕Üα╕╡α╣ëα╕óα╣Çα╕Ñα╕╡α╣ëα╕óα╕ç (α╕Üα╕▓α╕ù)',
  `accommodation_amount` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'α╕äα╣êα╕▓α╣Çα╕èα╣êα╕▓α╕ùα╕╡α╣êα╕₧α╕▒α╕ü (α╕Üα╕▓α╕ù)',
  `transport_amount` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'α╕äα╣êα╕▓α╕óα╕▓α╕Öα╕₧α╕▓α╕½α╕Öα╕░/α╕₧α╕▓α╕½α╕Öα╕░α╕¢α╕úα╕░α╕êα╕│α╕ùα╕▓α╕ç (α╕Üα╕▓α╕ù)',
  `other_expenses_amount` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'α╕äα╣êα╕▓α╣âα╕èα╣ëα╕êα╣êα╕▓α╕óα╕¡α╕╖α╣êα╕Ö α╣å (α╕Üα╕▓α╕ù)',
  `amount_budget_adjust` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'α╕óα╕¡α╕öα╕òα╕▒α╣ëα╕çα╕òα╣ëα╕Ö/α╕¢α╕úα╕▒α╕Üα╣Çα╕₧α╕┤α╣êα╕íα╕çα╕Üα╕¢α╕úα╕░α╕íα╕▓α╕ô',
  `amount_withdraw` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'α╕óα╕¡α╕öα╣Çα╕Üα╕┤α╕üα╕êα╣êα╕▓α╕óα╕úα╕ºα╕íα╕ùα╕▒α╣ëα╕çα╕½α╕íα╕ö',
  `balance` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'α╕çα╕Üα╕¢α╕úα╕░α╕íα╕▓α╕ôα╕äα╕çα╣Çα╕½α╕Ñα╕╖α╕¡α╕¬α╕░α╕¬α╕í',
  `notes` text DEFAULT NULL COMMENT 'α╕½α╕íα╕▓α╕óα╣Çα╕½α╕òα╕╕',
  `entry_type` enum('adjust','withdraw') NOT NULL DEFAULT 'withdraw' COMMENT 'α╕¢α╕úα╕░α╣Çα╕áα╕ùα╕úα╕▓α╕óα╕üα╕▓α╕ú (adjust=α╕¢α╕úα╕▒α╕Üα╕çα╕Ü, withdraw=α╣Çα╕Üα╕┤α╕üα╕êα╣êα╕▓α╕ó)',
  `created_by` varchar(100) DEFAULT NULL COMMENT 'α╕£α╕╣α╣ëα╕Üα╕▒α╕Öα╕ùα╕╢α╕üα╕úα╕▓α╕óα╕üα╕▓α╕ú',
  `updated_by` varchar(100) DEFAULT NULL COMMENT 'α╕£α╕╣α╣ëα╣üα╕üα╣ëα╣äα╕éα╕úα╕▓α╕óα╕üα╕▓α╕ú',
  `created_at` datetime DEFAULT NULL COMMENT 'α╕ºα╕▒α╕Öα╕ùα╕╡α╣êα╕¬α╕úα╣ëα╕▓α╕çα╕úα╕▓α╕óα╕üα╕▓α╕ú',
  `updated_at` datetime DEFAULT NULL COMMENT 'α╕ºα╕▒α╕Öα╕ùα╕╡α╣êα╣üα╕üα╣ëα╣äα╕éα╕úα╕▓α╕óα╕üα╕▓α╕úα╕Ñα╣êα╕▓α╕¬α╕╕α╕ö',
  PRIMARY KEY (`id`),
  KEY `idx_travel_budget_year` (`budget_year`),
  KEY `idx_travel_doc_date` (`doc_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='α╕ùα╕░α╣Çα╕Üα╕╡α╕óα╕Öα╕äα╕╕α╕íα╕çα╕Üα╕¢α╕úα╕░α╕íα╕▓α╕ôα╣Çα╕çα╕┤α╕Öα╕éα╣ëα╕¡α╕Üα╕▒α╕ìα╕ìα╕▒α╕òα╕┤ - α╕äα╣êα╕▓α╣âα╕èα╣ëα╕êα╣êα╕▓α╕óα╣Çα╕öα╕┤α╕Öα╕ùα╕▓α╕çα╣äα╕¢α╕úα╕▓α╕èα╕üα╕▓α╕ú';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tb_budget_project_sub_items`
--

DROP TABLE IF EXISTS `tb_budget_project_sub_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `tb_budget_project_sub_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` int(10) unsigned NOT NULL,
  `parent_id` int(10) unsigned DEFAULT NULL,
  `item_no` varchar(50) DEFAULT NULL,
  `item_name` varchar(255) NOT NULL,
  `allocated_budget` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_subitem_project` (`project_id`),
  KEY `idx_parent_id` (`parent_id`),
  CONSTRAINT `fk_subitem_project` FOREIGN KEY (`project_id`) REFERENCES `tb_budget_projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=111 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tb_budget_projects`
--

DROP TABLE IF EXISTS `tb_budget_projects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `tb_budget_projects` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `budget_year` varchar(10) NOT NULL,
  `strategy_id` int(10) unsigned DEFAULT NULL,
  `tactic_id` int(10) unsigned DEFAULT NULL,
  `strategy_name` varchar(255) DEFAULT NULL,
  `strategy_sub` varchar(255) DEFAULT NULL,
  `project_code` varchar(50) DEFAULT NULL,
  `project_name` varchar(255) NOT NULL,
  `budget_source` varchar(255) DEFAULT NULL,
  `start_date` date DEFAULT NULL COMMENT 'วันที่เริ่มต้นห้วงเวลาโครงการ',
  `end_date` date DEFAULT NULL COMMENT 'วันที่สิ้นสุดห้วงเวลาโครงการ',
  `allocated_budget` decimal(15,2) NOT NULL DEFAULT 0.00,
  `status` enum('active','closed') NOT NULL DEFAULT 'active',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_project_year` (`budget_year`),
  KEY `idx_project_strategy` (`strategy_id`),
  KEY `idx_project_tactic` (`tactic_id`),
  CONSTRAINT `fk_project_strategy` FOREIGN KEY (`strategy_id`) REFERENCES `tb_budget_strategies` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_project_tactic` FOREIGN KEY (`tactic_id`) REFERENCES `tb_budget_tactics` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tb_budget_strategies`
--

DROP TABLE IF EXISTS `tb_budget_strategies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `tb_budget_strategies` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `budget_year` varchar(10) NOT NULL,
  `strategy_no` varchar(20) NOT NULL,
  `strategy_name` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_strategy_year_no` (`budget_year`,`strategy_no`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tb_budget_tactics`
--

DROP TABLE IF EXISTS `tb_budget_tactics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `tb_budget_tactics` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `strategy_id` int(10) unsigned NOT NULL,
  `tactic_no` varchar(20) NOT NULL,
  `tactic_name` varchar(255) NOT NULL,
  `objective` text DEFAULT NULL,
  `actions` text DEFAULT NULL,
  `responsible_agency` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_tactic_strategy` (`strategy_id`),
  CONSTRAINT `fk_tactic_strategy` FOREIGN KEY (`strategy_id`) REFERENCES `tb_budget_strategies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tb_public_news`
--

DROP TABLE IF EXISTS `tb_public_news`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `tb_public_news` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `content` text DEFAULT NULL,
  `category` varchar(100) NOT NULL DEFAULT 'α╕¢α╕úα╕░α╕èα╕▓α╕¬α╕▒α╕íα╕₧α╕▒α╕Öα╕ÿα╣î',
  `cover_image` varchar(255) DEFAULT NULL,
  `published_at` datetime DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  `status` enum('draft','published','closed') NOT NULL DEFAULT 'draft',
  `view_count` int(10) unsigned NOT NULL DEFAULT 0,
  `created_by` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_news_status_published` (`status`,`published_at`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tb_public_news_files`
--

DROP TABLE IF EXISTS `tb_public_news_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `tb_public_news_files` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `news_id` int(10) unsigned NOT NULL,
  `original_name` varchar(255) NOT NULL,
  `stored_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `mime_type` varchar(150) DEFAULT NULL,
  `file_size` bigint(20) unsigned NOT NULL DEFAULT 0,
  `download_count` int(10) unsigned NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_news_files_news` (`news_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tb_purchase_hire_orders`
--

DROP TABLE IF EXISTS `tb_purchase_hire_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `tb_purchase_hire_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_number` varchar(100) NOT NULL COMMENT 'α╣Çα╕Ñα╕éα╕ùα╕╡α╣ê',
  `order_date` date NOT NULL COMMENT 'α╕ºα╕▒α╕Ö α╣Çα╕öα╕╖α╕¡α╕Ö α╕¢α╕╡',
  `description` text NOT NULL COMMENT 'α╕úα╕▓α╕óα╕üα╕▓α╕ú',
  `amount` decimal(15,2) NOT NULL COMMENT 'α╕êα╕│α╕Öα╕ºα╕Öα╣Çα╕çα╕┤α╕Ö',
  `signatory` varchar(255) NOT NULL COMMENT 'α╕£α╕╣α╣ëα╕Ñα╕çα╕Öα╕▓α╕íα╕¬α╕▒α╣êα╕çα╕êα╣ëα╕▓α╕ç',
  `contractor` varchar(255) NOT NULL COMMENT 'α╕£α╕╣α╣ëα╕úα╕▒α╕Üα╕êα╣ëα╕▓α╕ç',
  `delivery_date` date DEFAULT NULL COMMENT 'α╕üα╕│α╕½α╕Öα╕öα╕¬α╣êα╕çα╕íα╕¡α╕Ü',
  `remarks` text DEFAULT NULL COMMENT 'α╕½α╕íα╕▓α╕óα╣Çα╕½α╕òα╕╕',
  `inspection_status` varchar(255) DEFAULT NULL COMMENT 'α╕òα╕úα╕ºα╕êα╕úα╕▒α╕Ü',
  `system_sent_status` varchar(255) DEFAULT NULL COMMENT 'α╕¬α╣êα╕çα╣âα╕½α╣ëα╕Ñα╕çα╕úα╕░α╕Üα╕Ü/α╕ùα╕│α╕Äα╕╡α╕üα╕▓',
  `images` text DEFAULT NULL COMMENT 'α╕úα╕╣α╕¢α╕áα╕▓α╕₧ α╕íα╕▓α╕üα╕üα╕ºα╣êα╕▓ 1 α╕úα╕╣α╕¢',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `order_type` varchar(50) NOT NULL DEFAULT 'α╕¬α╕▒α╣êα╕çα╕ïα╕╖α╣ëα╕¡' COMMENT 'α╕¢α╕úα╕░α╣Çα╕áα╕ùα╣âα╕Üα╕¬α╕▒α╣êα╕ç (α╕¬α╕▒α╣êα╕çα╕ïα╕╖α╣ëα╕¡/α╕¬α╕▒α╣êα╕çα╕êα╣ëα╕▓α╕ç)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-25 12:06:20

INSERT INTO tb_admin_systems (admin_system_code,admin_system_name,admin_system_description,admin_system_icon,admin_system_url,admin_system_level,admin_system_active,created_at,updated_at) VALUES
('budget_ledger','ทะเบียนคุมเงินงบประมาณ','ทะเบียนคุมเงินงบประมาณ','bx-book-content','Admin/BudgetLedger',1,1,NOW(),NOW()),
('budget_ordinance','ทะเบียนคุมงบประมาณเงินข้อบัญญัติ','ทะเบียนคุมงบประมาณเงินข้อบัญญัติ','bx-book-bookmark','Admin/BudgetOrdinance/Oil',2,1,NOW(),NOW()),
('registry_purchase','ทะเบียนคุมใบสั่งซื้อ','ทะเบียนคุมใบสั่งซื้อ','bx-cart-alt','Admin/Registry/Purchase',3,1,NOW(),NOW()),
('registry_hire','ทะเบียนคุมใบสั่งจ้าง','ทะเบียนคุมใบสั่งจ้าง','bx-spreadsheet','Admin/Registry/Hire',4,1,NOW(),NOW()),
('procurement','ขั้นตอนจัดซื้อ / จัดจ้าง','ขั้นตอนจัดซื้อและจัดจ้าง','bx-cart','User/Procurement/Process',5,1,NOW(),NOW()),
('money_receipt','ใบสำคัญรับเงินตอบแทน','ใบสำคัญรับเงินตอบแทน','bx-file','User/Procurement/MoneyReceipt',6,1,NOW(),NOW()),
('budget_structure','ตั้งค่ายุทธศาสตร์และโครงการ','ตั้งค่ายุทธศาสตร์และโครงการ','bx-sitemap','Admin/BudgetLedger/Structure',7,1,NOW(),NOW()),
('admin_news','ระบบจัดการประชาสัมพันธ์','เพิ่มลบแก้ไขข่าวสารและเอกสารเผยแพร่','bx-news','Admin/News',8,1,NOW(),NOW());


