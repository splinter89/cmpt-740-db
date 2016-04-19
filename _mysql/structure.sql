/*
SQLyog Ultimate v12.14 (64 bit)
MySQL - 5.7.9-log : Database - 740_project
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`740_project` /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci */;

USE `740_project`;

/*Table structure for table `accommodation` */

DROP TABLE IF EXISTS `accommodation`;

CREATE TABLE `accommodation` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `host_user_id` int(10) unsigned NOT NULL,
  `city` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `price` int(10) unsigned NOT NULL,
  `type` enum('entire_home','private_room','shared_room') COLLATE utf8_unicode_ci NOT NULL,
  `has_washer` tinyint(1) unsigned NOT NULL,
  `has_wifi` tinyint(1) unsigned NOT NULL,
  `has_tv` tinyint(1) unsigned NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `city` (`city`),
  KEY `fk_host_user_id` (`host_user_id`),
  CONSTRAINT `fk_host_user_id` FOREIGN KEY (`host_user_id`) REFERENCES `user` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3067 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `reservation` */

DROP TABLE IF EXISTS `reservation`;

CREATE TABLE `reservation` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `accommodation_id` int(10) unsigned NOT NULL,
  `guest_user_id` int(10) unsigned NOT NULL,
  `date_from` date NOT NULL,
  `date_to` date NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `fk_accomodation_id` (`accommodation_id`),
  KEY `fk_guest_user_id` (`guest_user_id`),
  CONSTRAINT `fk_accomodation_id` FOREIGN KEY (`accommodation_id`) REFERENCES `accommodation` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_guest_user_id` FOREIGN KEY (`guest_user_id`) REFERENCES `user` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3067 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `user` */

DROP TABLE IF EXISTS `user`;

CREATE TABLE `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(225) COLLATE utf8_unicode_ci NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=2580 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
