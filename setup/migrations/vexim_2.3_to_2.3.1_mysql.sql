--
-- MySQL script to upgrade Vexim database schema from Vexim 2.3 to Vexim 2.3.1.
-- The only change so far is that of character set from utf8mb3 to utf8mb4.
--

--
-- Table: `domains`
--
ALTER TABLE `domains` ROW_FORMAT=DYNAMIC;
ALTER TABLE `domains` CONVERT TO CHARACTER SET utf8mb4;

--
-- Table: `users`
--
ALTER TABLE `users` ROW_FORMAT=DYNAMIC;
ALTER TABLE `users` CONVERT TO CHARACTER SET utf8mb4;

--
-- Table: `blocklists`
--
ALTER TABLE `blocklists` ROW_FORMAT=DYNAMIC;
ALTER TABLE `blocklists` CONVERT TO CHARACTER SET utf8mb4;

--
-- Table: `domainalias`
--
ALTER TABLE `domainalias` ROW_FORMAT=DYNAMIC;
ALTER TABLE `domainalias` CONVERT TO CHARACTER SET utf8mb4;

--
-- Table: `groups`
--
ALTER TABLE `groups` ROW_FORMAT=DYNAMIC;
ALTER TABLE `groups` CONVERT TO CHARACTER SET utf8mb4;

--
-- Table: `group_contents`
--
ALTER TABLE `group_contents` ROW_FORMAT=DYNAMIC;
ALTER TABLE `group_contents` CONVERT TO CHARACTER SET utf8mb4;
