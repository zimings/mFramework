/*
SQLyog Ultimate v12.08 (64 bit)
MySQL - 5.5.60-MariaDB : Database - Mdb
*********************************************************************
*/


/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`Mdb` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `Mdb`;

/*Table structure for table `admin` */

DROP TABLE IF EXISTS `admin`;

CREATE TABLE `admin` (
  `uid` int(6) NOT NULL AUTO_INCREMENT,
  `name` varchar(15) NOT NULL,
  `phone` varchar(11) DEFAULT NULL,
  `pwd` varchar(32) NOT NULL,
  `identity` int(1) NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `article` */

DROP TABLE IF EXISTS `article`;

CREATE TABLE `article` (
  `articleId` int(12) NOT NULL AUTO_INCREMENT,
  `uid` int(6) NOT NULL,
  `time` datetime DEFAULT NULL,
  `title` varchar(65) DEFAULT NULL,
  `content` mediumtext,
  `personal` int(1) DEFAULT NULL,
  `otherInfo` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`articleId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `private` */

DROP TABLE IF EXISTS `private`;

CREATE TABLE `private` (
  `code` varchar(16) NOT NULL,
  `state` varchar(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `user` */

DROP TABLE IF EXISTS `user`;

CREATE TABLE `user` (
  `uid` int(6) NOT NULL AUTO_INCREMENT,
  `user` varchar(8) NOT NULL,
  `name` varchar(16) DEFAULT '请设置昵称',
  `headPortrait` varchar(225) DEFAULT NULL,
  `pwd` varchar(255) NOT NULL,
  `phone` varchar(11) DEFAULT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `userMore` */

DROP TABLE IF EXISTS `userMore`;

CREATE TABLE `userMore` (
  `uid` int(6) NOT NULL,
  `signature` varchar(65) DEFAULT '此人很懒，什么也没留下~~',
  `setting` varchar(300) DEFAULT '{"onePageNum":"6"}',
  PRIMARY KEY (`uid`),
  CONSTRAINT `userMore_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `user` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
