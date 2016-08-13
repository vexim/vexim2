--
-- MySQL script to upgrade Vexim database schema from Vexim 1.1, 1.2, 1.2.1 and 1.3 to Vexim 1.5
--

ALTER TABLE `users` ADD COLUMN `status` tinyint(1) NOT NULL DEFAULT '1' AFTER `admin`;
