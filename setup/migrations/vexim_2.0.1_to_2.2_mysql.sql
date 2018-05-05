--
-- MySQL script to upgrade Vexim database schema from Vexim 2.0.1 to Vexim 2.2
--

ALTER TABLE `users` ADD COLUMN `unseen` bool DEFAULT '0' AFTER `forward`;

CREATE TABLE `groups` (
	`id` int(10) AUTO_INCREMENT,
	`domain_id` mediumint(8) UNSIGNED NOT NULL,
	`name` varchar(64) NOT NULL,
	`is_public` char(1) NOT NULL DEFAULT 'Y',
	`enabled` bool NOT NULL DEFAULT '1',
	PRIMARY KEY (`id`),
	UNIQUE KEY `group_name`(`domain_id`, `name`)
);
CREATE TABLE `group_contents` (
	`group_id` int(10) NOT NULL,
	`member_id` int(10) NOT NULL,
	PRIMARY KEY (`group_id`, `member_id`)
);
