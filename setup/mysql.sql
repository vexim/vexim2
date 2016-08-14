--
-- The password for siteadmin is CHANGE.
--

SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT;
SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS;
SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION;
SET NAMES utf8;
SET @OLD_TIME_ZONE=@@TIME_ZONE;
SET TIME_ZONE='+00:00';
SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';
SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0;

--
-- Uncomment the following block if you want this script to create 
-- the database for you and set up its access.
-- Don't forget to change the password (currently: CHANGE).
-- You may also change the database and user names if you want.
--

-- CREATE DATABASE IF NOT EXISTS `vexim` DEFAULT CHARACTER SET utf8;
-- USE `vexim`;
-- GRANT SELECT,INSERT,DELETE,UPDATE ON `vexim`.* to "vexim"@"localhost" 
--    IDENTIFIED BY 'CHANGE';
-- FLUSH PRIVILEGES;

--
-- Table structure for table `domains`
--

DROP TABLE IF EXISTS `domains`;
CREATE TABLE `domains` (
  `domain_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `domain` varchar(255) NOT NULL DEFAULT '',
  `maildir` varchar(4096) NOT NULL DEFAULT '',
  `uid` smallint(5) unsigned NOT NULL DEFAULT '65534',
  `gid` smallint(5) unsigned NOT NULL DEFAULT '65534',
  `max_accounts` int(10) unsigned NOT NULL DEFAULT '0',
  `quotas` int(10) unsigned NOT NULL DEFAULT '0',
  `type` varchar(5) DEFAULT NULL,
  `avscan` tinyint(1) NOT NULL DEFAULT '0',
  `blocklists` tinyint(1) NOT NULL DEFAULT '0',
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  `mailinglists` tinyint(1) NOT NULL DEFAULT '0',
  `maxmsgsize` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `pipe` tinyint(1) NOT NULL DEFAULT '0',
  `spamassassin` tinyint(1) NOT NULL DEFAULT '0',
  `sa_tag` smallint(5) unsigned NOT NULL DEFAULT '0',
  `sa_refuse` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`domain_id`),
  UNIQUE KEY `domain` (`domain`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `domain_id` int(10) unsigned NOT NULL,
  `localpart` varchar(64) NOT NULL DEFAULT '',
  `username` varchar(255) NOT NULL DEFAULT '',
  `crypt` varchar(255) DEFAULT NULL,
  `uid` smallint(5) unsigned NOT NULL DEFAULT '65534',
  `gid` smallint(5) unsigned NOT NULL DEFAULT '65534',
  `smtp` varchar(4096) DEFAULT NULL,
  `pop` varchar(4096) DEFAULT NULL,
  `type` enum('local','alias','catch','fail','piped','admin','site') NOT NULL DEFAULT 'local',
  `admin` tinyint(1) NOT NULL DEFAULT '0',
  `on_avscan` tinyint(1) NOT NULL DEFAULT '0',
  `on_blocklist` tinyint(1) NOT NULL DEFAULT '0',
  `on_forward` tinyint(1) NOT NULL DEFAULT '0',
  `on_piped` tinyint(1) NOT NULL DEFAULT '0',
  `on_spamassassin` tinyint(1) NOT NULL DEFAULT '0',
  `on_vacation` tinyint(1) NOT NULL DEFAULT '0',
  `spam_drop` tinyint(1) NOT NULL DEFAULT '0',
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  `flags` varchar(16) DEFAULT NULL,
  `forward` varchar(255) DEFAULT NULL,
  `unseen` tinyint(1) DEFAULT '0',
  `maxmsgsize` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `quota` int(10) unsigned NOT NULL DEFAULT '0',
  `realname` varchar(255) DEFAULT NULL,
  `sa_tag` smallint(5) unsigned NOT NULL DEFAULT '0',
  `sa_refuse` smallint(5) unsigned NOT NULL DEFAULT '0',
  `tagline` varchar(255) DEFAULT NULL,
  `vacation` varchar(1024) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE INDEX `username` (`localpart`, `domain_id`),
  INDEX `local` (`localpart`),
  INDEX `fk_users_domain_id_idx` (`domain_id`),
  CONSTRAINT `fk_users_domain_id`
    FOREIGN KEY (`domain_id`)
    REFERENCES `domains` (`domain_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `blocklists`
--

DROP TABLE IF EXISTS `blocklists`;
CREATE TABLE `blocklists` (
  `block_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `domain_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `blockhdr` varchar(192) NOT NULL DEFAULT '',
  `blockval` varchar(255) NOT NULL DEFAULT '',
  `color` varchar(8) NOT NULL DEFAULT '',
  PRIMARY KEY (`block_id`),
  INDEX `fk_blocklists_domain_id_idx` (`domain_id`),
  INDEX `fk_blocklists_user_id_idx` (`user_id`),
  CONSTRAINT `fk_blocklists_domain_id`
    FOREIGN KEY (`domain_id`)
    REFERENCES `domains` (`domain_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_blocklists_user_id`
    FOREIGN KEY (`user_id`)
    REFERENCES `users` (`user_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `domainalias`
--

DROP TABLE IF EXISTS `domainalias`;
CREATE TABLE `domainalias` (
  `domain_id` int(10) unsigned NOT NULL,
  `alias` varchar(255) NOT NULL,
  PRIMARY KEY (`alias`),
  INDEX `fk_domainalias_domain_id_idx` (`domain_id`),
  CONSTRAINT `fk_domainalias_domain_id`
    FOREIGN KEY (`domain_id`)
    REFERENCES `domains` (`domain_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `groups`
--

DROP TABLE IF EXISTS `groups`;
CREATE TABLE `groups` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `domain_id` int(10) unsigned NOT NULL,
  `name` varchar(64) NOT NULL,
  `is_public` char(1) NOT NULL DEFAULT 'Y',
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE INDEX `group_name` (`domain_id`, `name`),
  INDEX `fk_groups_domain_id_idx` (`domain_id`),
  CONSTRAINT `fk_groups_domain_id`
    FOREIGN KEY (`domain_id`)
    REFERENCES `domains` (`domain_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `group_contents`
--

DROP TABLE IF EXISTS `group_contents`;
CREATE TABLE `group_contents` (
  `group_id` int(10) unsigned NOT NULL,
  `member_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`group_id`, `member_id`),
  INDEX `fk_group_contents_group_id_idx` (`group_id`),
  INDEX `fk_group_contents_member_id_idx` (`member_id`),
  CONSTRAINT `fk_group_contents_group_id`
    FOREIGN KEY (`group_id`)
    REFERENCES `groups` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_group_contents_member_id`
    FOREIGN KEY (`member_id`)
    REFERENCES `users` (`user_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Seed the `domains` table with the hidden siteadmin domain
--

LOCK TABLES `domains` WRITE;
ALTER TABLE `domains` DISABLE KEYS;
INSERT INTO `domains` VALUES (1,'admin','',65534,65534,0,0,NULL,0,0,1,0,0,0,0,0,0);
ALTER TABLE `domains` ENABLE KEYS;
UNLOCK TABLES;

--
-- Seed the `users` table with the siteadmin user
--

LOCK TABLES `users` WRITE;
ALTER TABLE `users` DISABLE KEYS;
INSERT INTO `users` VALUES (1,1,'siteadmin','siteadmin','$1$qZc7ANMc$h07fKA10jQQmJ33fzlJ3Z0',65535,65535,'','','site',1,0,0,0,0,0,0,0,1,NULL,NULL,0,0,0,'SiteAdmin',0,0,NULL,NULL);
ALTER TABLE `users` ENABLE KEYS;
UNLOCK TABLES;


SET TIME_ZONE=@OLD_TIME_ZONE;
SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT;
SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS;
SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION;
SET SQL_NOTES=@OLD_SQL_NOTES;
