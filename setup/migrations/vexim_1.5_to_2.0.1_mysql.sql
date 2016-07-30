--
-- MySQL script to upgrade Vexim database schema from Vexim 1.5 to Vexim 2.0.1
--

-- Create the `domains` table
CREATE TABLE `domains` (
	`domain_id` mediumint(8) UNSIGNED AUTO_INCREMENT NOT NULL,
	`domain` varchar(64) NOT NULL DEFAULT '',
	`maildir` varchar(128) NOT NULL DEFAULT '',
	`uid` smallint(5) UNSIGNED NOT NULL DEFAULT '65534',
	`gid` smallint(5) UNSIGNED NOT NULL DEFAULT '65534',
	`max_accounts` int(10) UNSIGNED NOT NULL DEFAULT '0',
	`quotas` int(10) UNSIGNED NOT NULL DEFAULT '0',
	`type` varchar(5) DEFAULT NULL,
	`avscan` bool NOT NULL DEFAULT '0',
	`blocklists` bool NOT NULL DEFAULT '0',
	`complexpass` bool NOT NULL DEFAULT '0',
	`enabled` bool NOT NULL DEFAULT '1',
	`mailinglists` bool NOT NULL DEFAULT '0',
	`maxmsgsize` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
	`pipe` bool NOT NULL DEFAULT '0',
	`spamassassin` bool NOT NULL DEFAULT '0',
	`sa_tag` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
	`sa_refuse` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
	PRIMARY KEY (`domain_id`),
	UNIQUE KEY `domain` (`domain`),
	KEY `domain_id` (`domain_id`),
	KEY `domains` (`domain`)

);
-- Insert the siteadmin domain
INSERT INTO `domains` (`domain_id`, `domain`) VALUES ('1', 'admin');
-- Insert other domains
INSERT INTO `domains` (`domain`, `type`) SELECT DISTINCT `domain`, 'local' FROM `users` WHERE `domain`!='admin';
-- Set domain maildirs
UPDATE `domains` SET `maildir`=(
    SELECT SUBSTRING_INDEX(`pophome`, `local_part`, 1) FROM `users`
        WHERE `users`.`domain`=`domains`.`domain`
        AND SUBSTRING(`users`.`pophome`, 1, 1)='/'
        LIMIT 1
    ) WHERE `domain_id` != '1';

-- Update the structure and contents of the `users` table
ALTER TABLE `users` DROP INDEX `username`;
ALTER TABLE `users` ADD COLUMN `user_id` int(10) UNSIGNED AUTO_INCREMENT NOT NULL PRIMARY KEY FIRST;
ALTER TABLE `users` ADD COLUMN `domain_id` mediumint(8) UNSIGNED NOT NULL AFTER `user_id`;
UPDATE `users` SET `domain_id`=(SELECT `domain_id` FROM `domains` WHERE `domains`.`domain`=`users`.`domain`);
ALTER TABLE `users` CHANGE COLUMN `local_part` `localpart` varchar(192) NOT NULL DEFAULT '' AFTER `domain_id`;
ALTER TABLE `users` MODIFY COLUMN `username` varchar(255) NOT NULL DEFAULT '' AFTER `localpart`;
ALTER TABLE `users` CHANGE COLUMN `cpassword` `clear` varchar(255) DEFAULT NULL AFTER `username`;
ALTER TABLE `users` CHANGE COLUMN `password` `crypt` varchar(48) DEFAULT NULL AFTER `clear`;
-- Set default uid and gid values where these are not numeric
UPDATE `users` SET `uid`='65535' WHERE `type`='admin' AND `admin`='site' AND `uid` NOT REGEXP  '^[0-9]+$';
UPDATE `users` SET `uid`='65534' WHERE `uid` NOT REGEXP '^[0-9]+$';
ALTER TABLE `users` MODIFY COLUMN `uid` smallint(5) UNSIGNED NOT NULL DEFAULT '65534' AFTER `crypt`;
UPDATE `users` SET `gid`='65535' WHERE `type`='admin' AND `admin`='site' AND `gid` NOT REGEXP  '^[0-9]+$';
UPDATE `users` SET `gid`='65534' WHERE `gid` NOT REGEXP '^[0-9]+$';
ALTER TABLE `users` MODIFY COLUMN `gid` smallint(5) UNSIGNED NOT NULL DEFAULT '65534' AFTER `uid`;
ALTER TABLE `users` CHANGE COLUMN `smtphome` `smtp` varchar(255) DEFAULT NULL AFTER `gid`;
ALTER TABLE `users` CHANGE COLUMN `pophome` `pop` varchar(255) DEFAULT NULL AFTER `smtp`;
-- Reset invalid type values to 'local'
UPDATE `users` SET `type`='local' WHERE `type` NOT IN ('local','alias','catch','fail','piped','admin','site');
ALTER TABLE `users` MODIFY COLUMN `type` enum('local','alias','catch','fail','piped','admin','site') NOT NULL DEFAULT 'local' AFTER `pop`;
UPDATE `users` SET `type`='site',`localpart`=`username` WHERE `type`='admin' AND `admin`='site';
UPDATE `users` SET `admin`='1' WHERE `admin` IS NOT NULL AND `admin`!='';
UPDATE `users` SET `admin`='0' WHERE `admin`!='1' OR `admin` IS NULL;
ALTER TABLE `users` MODIFY COLUMN `admin` bool NOT NULL DEFAULT '0' AFTER `type`;
ALTER TABLE `users` ADD COLUMN `on_avscan` bool NOT NULL DEFAULT '0' AFTER `admin`;
ALTER TABLE `users` ADD COLUMN `on_blocklist` bool NOT NULL DEFAULT '0' AFTER `on_avscan`;
ALTER TABLE `users` ADD COLUMN `on_complexpass` bool NOT NULL DEFAULT '0' AFTER `on_blocklist`;
ALTER TABLE `users` ADD COLUMN `on_forward` bool NOT NULL DEFAULT '0' AFTER `on_complexpass`;
ALTER TABLE `users` ADD COLUMN `on_piped` bool NOT NULL DEFAULT '0' AFTER `on_forward`;
UPDATE `users` SET `on_piped`='1' WHERE `smtp` LIKE '"|%';
ALTER TABLE `users` ADD COLUMN `on_spamassassin` bool NOT NULL DEFAULT '0' AFTER `on_piped`;
ALTER TABLE `users` ADD COLUMN `on_vacation` bool NOT NULL DEFAULT '0' AFTER `on_spamassassin`;
ALTER TABLE `users` CHANGE COLUMN `status` `enabled` bool NOT NULL DEFAULT '1' AFTER `on_vacation`;
ALTER TABLE `users` ADD COLUMN `flags` varchar(16) DEFAULT NULL AFTER `enabled`;
ALTER TABLE `users` ADD COLUMN `forward` varchar(255) DEFAULT NULL AFTER `flags`;
ALTER TABLE `users` ADD COLUMN `maxmsgsize` mediumint(8) UNSIGNED NOT NULL DEFAULT '0' AFTER `forward`;
ALTER TABLE `users` ADD COLUMN `quota` int(10) UNSIGNED NOT NULL DEFAULT '0' AFTER `maxmsgsize`;
ALTER TABLE `users` MODIFY COLUMN `realname` varchar(255) DEFAULT NULL AFTER `quota`;
ALTER TABLE `users` ADD COLUMN `sa_tag` smallint(5) UNSIGNED NOT NULL DEFAULT '0' AFTER `realname`;
ALTER TABLE `users` ADD COLUMN `sa_refuse` smallint(5) UNSIGNED NOT NULL DEFAULT '0' AFTER `sa_tag`;
ALTER TABLE `users` ADD COLUMN `tagline` varchar(255) DEFAULT NULL AFTER `sa_refuse`;
ALTER TABLE `users` ADD COLUMN `vacation` varchar(255) DEFAULT NULL AFTER `tagline`;
ALTER TABLE `users` DROP COLUMN `domain`;
ALTER TABLE `users` ADD UNIQUE INDEX `username` (`localpart`, `domain_id`);
ALTER TABLE `users` ADD INDEX `local` (`localpart`);

-- Create the `blocklists` table
CREATE TABLE `blocklists` (
	`block_id` int(10) UNSIGNED AUTO_INCREMENT NOT NULL,
	`domain_id` mediumint(8) UNSIGNED NOT NULL,
	`user_id` int(10) UNSIGNED DEFAULT NULL,
	`blockhdr` varchar(192) NOT NULL DEFAULT '',
	`blockval` varchar(192) NOT NULL DEFAULT '',
	`color` varchar(8) NOT NULL DEFAULT '',
	PRIMARY KEY (`block_id`)
);

-- Create the `domainalias` table
CREATE TABLE `domainalias` (
	`domain_id` mediumint(8) UNSIGNED NOT NULL,
	`alias` varchar(64) DEFAULT NULL
);
