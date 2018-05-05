--
-- MySQL script to upgrade Vexim database schema from Vexim 2.2RC1, 2.2 and 2.2.1 to Vexim 2.3
--
-- Uncomment the section at the bottom of this file if you used Arne Schirmacher's patch for Vexim 2.2RC1
-- from https://www.schirmacher.de/display/INFO/improved+Vexim+frontend+and+bug+fixes
--
-- The following defines new salt prefix for password hashes.
-- Before dropping the `clear` field, we will re-hash passwords for all users whose cleartext and encrypted
-- passwords match using a secure hashing scheme (SHA-512 by default, supported by Linux, FreeBSD and Solaris).
-- Below you may change this prefix if you prefer a different hashing scheme, e.g. '$2a$10$' for bcrypt
-- (*BSD-only Blowfish hashing scheme):
SET @NEW_PW_PREFIX='$6$';
-- Comment out this definition if you do not want your password hashes updated. Be advised that doing so would
-- leave these passwords less secure, and you will not be able to re-hash them later.

--
-- Table: `domains`
--
ALTER TABLE `domains` ENGINE=InnoDB;
ALTER TABLE `domains` CONVERT TO CHARACTER SET utf8;
ALTER TABLE `domains` MODIFY COLUMN `domain_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
ALTER TABLE `domains` MODIFY COLUMN `domain` varchar(255) NOT NULL DEFAULT '';
ALTER TABLE `domains` MODIFY COLUMN `maildir` varchar(4096) NOT NULL DEFAULT '';
ALTER TABLE `domains` MODIFY COLUMN `uid` smallint(5) unsigned NOT NULL DEFAULT '65534';
ALTER TABLE `domains` MODIFY COLUMN `gid` smallint(5) unsigned NOT NULL DEFAULT '65534';
ALTER TABLE `domains` DROP COLUMN `complexpass`;
ALTER TABLE `domains` DROP KEY `domain_id`;
ALTER TABLE `domains` DROP KEY `domains`;

--
-- Table: `users`
--
ALTER TABLE `users` ENGINE=InnoDB;
ALTER TABLE `users` CONVERT TO CHARACTER SET utf8;
ALTER TABLE `users` MODIFY COLUMN `domain_id` int(10) unsigned NOT NULL;
ALTER TABLE `users` MODIFY COLUMN `localpart` varchar(64) NOT NULL DEFAULT '';
ALTER TABLE `users` MODIFY COLUMN `crypt` varchar(255) DEFAULT NULL;
-- Re-hash passwords using the more secure scheme.
UPDATE `users`
    SET `crypt` = COALESCE(ENCRYPT(`clear`, CONCAT(@NEW_PW_PREFIX, MD5(RAND()))), `crypt`)
    WHERE `crypt` = ENCRYPT(`clear`, `crypt`) AND @NEW_PW_PREFIX IS NOT NULL;
-- Passwords are now re-hashed.
SET @NEW_PW_PREFIX=NULL;
ALTER TABLE `users` DROP COLUMN `clear`;
ALTER TABLE `users` MODIFY COLUMN `uid` smallint(5) unsigned NOT NULL DEFAULT '65534';
ALTER TABLE `users` MODIFY COLUMN `gid` smallint(5) unsigned NOT NULL DEFAULT '65534';
ALTER TABLE `users` MODIFY COLUMN `smtp` varchar(4096) DEFAULT NULL;
ALTER TABLE `users` MODIFY COLUMN `pop` varchar(4096) DEFAULT NULL;
ALTER TABLE `users` DROP COLUMN `on_complexpass`;
ALTER TABLE `users` ADD COLUMN `spam_drop` tinyint(1) NOT NULL DEFAULT '0' AFTER `on_vacation`;
ALTER TABLE `users` MODIFY COLUMN `forward` varchar(4096) DEFAULT NULL;
ALTER TABLE `users` MODIFY COLUMN `unseen` tinyint(1) NOT NULL DEFAULT '0';
ALTER TABLE `users` MODIFY COLUMN `vacation` text DEFAULT NULL;
ALTER TABLE `users` ADD KEY `fk_users_domain_id_idx` (`domain_id`);
ALTER TABLE `users` ADD CONSTRAINT `fk_users_domain_id`
                          FOREIGN KEY (`domain_id`)
                          REFERENCES `domains` (`domain_id`)
                          ON DELETE CASCADE
                          ON UPDATE CASCADE;

--
-- Table: `blocklists`
--
ALTER TABLE `blocklists` ENGINE=InnoDB;
ALTER TABLE `blocklists` CONVERT TO CHARACTER SET utf8;
ALTER TABLE `blocklists` MODIFY COLUMN `domain_id` int(10) unsigned NOT NULL;
ALTER TABLE `blocklists` MODIFY COLUMN `blockval` varchar(255) NOT NULL DEFAULT '';
ALTER TABLE `blocklists` ADD KEY `fk_blocklists_domain_id_idx` (`domain_id`);
ALTER TABLE `blocklists` ADD KEY `fk_blocklists_user_id_idx` (`user_id`);
ALTER TABLE `blocklists` ADD CONSTRAINT `fk_blocklists_domain_id`
                               FOREIGN KEY (`domain_id`)
                               REFERENCES `domains` (`domain_id`)
                               ON DELETE CASCADE
                               ON UPDATE CASCADE;
ALTER TABLE `blocklists` ADD CONSTRAINT `fk_blocklists_user_id`
                               FOREIGN KEY (`user_id`)
                               REFERENCES `users` (`user_id`)
                               ON DELETE CASCADE
                               ON UPDATE CASCADE;

--
-- Table: `domainalias`
--
ALTER TABLE `domainalias` ENGINE=InnoDB;
ALTER TABLE `domainalias` CONVERT TO CHARACTER SET utf8;
ALTER TABLE `domainalias` MODIFY COLUMN `domain_id` int(10) unsigned NOT NULL;
ALTER TABLE `domainalias` MODIFY COLUMN `alias` varchar(255) NOT NULL;
ALTER TABLE `domainalias` ADD PRIMARY KEY (`alias`);
ALTER TABLE `domainalias` ADD KEY `fk_domainalias_domain_id_idx` (`domain_id`);
ALTER TABLE `domainalias` ADD CONSTRAINT `fk_domainalias_domain_id`
                                FOREIGN KEY (`domain_id`)
                                REFERENCES `domains` (`domain_id`)
                                ON DELETE CASCADE
                                ON UPDATE CASCADE;

--
-- Table: `groups`
--
ALTER TABLE `groups` ENGINE=InnoDB;
ALTER TABLE `groups` CONVERT TO CHARACTER SET utf8;
ALTER TABLE `groups` MODIFY COLUMN `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
ALTER TABLE `groups` MODIFY COLUMN `domain_id` int(10) unsigned NOT NULL;
ALTER TABLE `groups` ADD KEY `fk_groups_domain_id_idx` (`domain_id`);
ALTER TABLE `groups` ADD CONSTRAINT `fk_groups_domain_id`
                           FOREIGN KEY (`domain_id`)
                           REFERENCES `domains` (`domain_id`)
                           ON DELETE CASCADE
                           ON UPDATE CASCADE;

--
-- Table: `group_contents`
--
ALTER TABLE `group_contents` ENGINE=InnoDB;
ALTER TABLE `group_contents` CONVERT TO CHARACTER SET utf8;
ALTER TABLE `group_contents` MODIFY COLUMN `group_id` int(10) unsigned NOT NULL;
ALTER TABLE `group_contents` MODIFY COLUMN `member_id` int(10) unsigned NOT NULL;
ALTER TABLE `group_contents` ADD KEY `fk_group_contents_group_id_idx` (`group_id`);
ALTER TABLE `group_contents` ADD KEY `fk_group_contents_member_id_idx` (`member_id`);
ALTER TABLE `group_contents` ADD CONSTRAINT `fk_group_contents_group_id`
                                   FOREIGN KEY (`group_id`)
                                   REFERENCES `groups` (`id`)
                                   ON DELETE CASCADE
                                   ON UPDATE CASCADE;
ALTER TABLE `group_contents` ADD CONSTRAINT `fk_group_contents_member_id`
                                   FOREIGN KEY (`member_id`)
                                   REFERENCES `users` (`user_id`)
                                   ON DELETE CASCADE
                                   ON UPDATE CASCADE;

--
-- Uncomment the following section if you used Arne Schirmacher's patch for Vexim 2.2RC1
--

-- UPDATE `users` SET spam_drop=1 WHERE movedelete=2;
-- ALTER TABLE `users` DROP COLUMN `movedelete`;
-- ALTER TABLE `users` DROP COLUMN `on_rewritesubject`;
