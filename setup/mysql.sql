---
--- Database: `vexim`
---
CREATE DATABASE IF NOT EXISTS `vexim` DEFAULT CHARACTER SET utf8;

---
--- Table: `domains`
---
DROP TABLE IF EXISTS `vexim`.`domains`;
CREATE TABLE IF NOT EXISTS `vexim`.`domains`
(
    domain_id      mediumint(8)  unsigned  NOT NULL  auto_increment,
	domain           varchar(64)             NOT NULL  default '',
	maildir          varchar(128)            NOT NULL  default '',
	uid              smallint(5)   unsigned  NOT NULL  default 'CHANGE',
	gid              smallint(5)   unsigned  NOT NULL  default 'CHANGE',
	max_accounts     int(10)       unsigned  NOT NULL  default '0', 
	quotas           int(10)       unsigned  NOT NULL  default '0',
	type             varchar(5)                        default NULL,
	avscan           bool                    NOT NULL  default '0',
	blocklists       bool                    NOT NULL  default '0',
	complexpass      bool                    NOT NULL  default '0',
	enabled          bool                    NOT NULL  default '1',
	mailinglists     bool                    NOT NULL  default '0',
	maxmsgsize       mediumint(8)  unsigned  NOT NULL  default '0',
	pipe             bool                    NOT NULL  default '0',
	spamassassin     bool                    NOT NULL  default '0',
	sa_tag           smallint(5)   unsigned  NOT NULL  default '0',
	sa_refuse        smallint(5)   unsigned  NOT NULL  default '0',
	PRIMARY KEY (domain_id),
	UNIQUE KEY domain (domain),
	KEY domain_id (domain_id),
	KEY domains (domain)
);

---
--- Table: `users`
---
DROP TABLE IF EXISTS `vexim`.`users`;
CREATE TABLE IF NOT EXISTS `vexim`.`users` 
(
    user_id          int(10)       unsigned  NOT NULL  auto_increment,
	domain_id        mediumint(8)  unsigned  NOT NULL,
	localpart        varchar(192)            NOT NULL  default '',
	username         varchar(255)            NOT NULL  default '',
	clear            varchar(255)                      default NULL,
	crypt            varchar(48)                       default NULL,
	uid              smallint(5)   unsigned  NOT NULL  default '65534',
	gid              smallint(5)   unsigned  NOT NULL  default '65534',
	smtp             varchar(255)                      default NULL,
	pop              varchar(255)                      default NULL,
	type             enum('local', 'alias', 
                          'catch', 'fail', 
                          'piped', 'admin', 
                          'site')            NOT NULL  default 'local',
	admin            bool                    NOT NULL  default '0',
	on_avscan        bool                    NOT NULL  default '0',
	on_blocklist     bool                    NOT NULL  default '0',
	on_complexpass   bool                    NOT NULL  default '0',
	on_forward       bool                    NOT NULL  default '0',
	on_piped         bool                    NOT NULL  default '0',
	on_spamassassin  bool                    NOT NULL  default '0',
	on_vacation      bool                    NOT NULL  default '0',
	enabled          bool                    NOT NULL  default '1',
	flags            varchar(16)                       default NULL,
	forward          varchar(255)                      default NULL,
    unseen           bool                              default '0',
	maxmsgsize       mediumint(8)  unsigned  NOT NULL  default '0',
	quota            int(10)       unsigned  NOT NULL  default '0',
	realname         varchar(255)                      default NULL,
	sa_tag           smallint(5)   unsigned  NOT NULL  default '0',
	sa_refuse        smallint(5)   unsigned  NOT NULL  default '0',
	tagline          varchar(255)                      default NULL,
	vacation         varchar(1024)                      default NULL,
	PRIMARY KEY (user_id),
	UNIQUE KEY username (localpart, domain_id),
	KEY local (localpart)
);

---
--- Table: `blocklists`
---
DROP TABLE IF EXISTS `vexim`.`blocklists`;
CREATE TABLE IF NOT EXISTS `vexim`.`blocklists`
(
    block_id         int(10)       unsigned  NOT NULL  auto_increment,
  	domain_id        mediumint(8)  unsigned  NOT NULL,
	user_id          int(10)       unsigned            default NULL,
	blockhdr         varchar(192)            NOT NULL  default '',
	blockval         varchar(192)            NOT NULL  default '',
	color            varchar(8)              NOT NULL  default '',
	PRIMARY KEY (block_id)
);


---
--- Table: `domainalias`
---
CREATE TABLE IF NOT EXISTS `vexim`.`domainalias` 
(
    domain_id        mediumint(8)  unsigned  NOT NULL,
	alias varchar(64)
);

---
--- Table: `groups`
---
DROP TABLE IF EXISTS `vexim`.`groups`;
CREATE TABLE IF NOT EXISTS `vexim`.`groups`
(
    id               int(10)                           auto_increment,
    domain_id        mediumint(8)  unsigned  NOT NULL,
    name             varchar(64)             NOT NULL,
    is_public        char(1)                 NOT NULL  default 'Y',
    enabled          bool                    NOT NULL  default '1',
    PRIMARY KEY (id),
    UNIQUE KEY group_name(domain_id, name)
);

---
--- Table: `group_contents`
---
DROP TABLE IF EXISTS `vexim`.`group_contents`;
CREATE TABLE IF NOT EXISTS `vexim`.`group_contents` 
(
    group_id         int(10)                 NOT NULL,
    member_id        int(10)                 NOT NULL,
    PRIMARY KEY (group_id, member_id)
);

---
--- Priviledges:
---
GRANT SELECT,INSERT,DELETE,UPDATE ON `vexim`.* to "vexim"@"localhost" 
    IDENTIFIED BY 'CHANGE';
FLUSH PRIVILEGES;

---
--- add initial domain: admin
---
INSERT INTO `vexim`.`domains` (domain_id, domain) VALUES ('1', 'admin');

---
--- add initial user; postmaster
---
INSERT INTO `vexim`.`users`
(
    domain_id, localpart, username, clear, crypt, uid, gid, 
    smtp, pop, realname, type, admin
)
VALUES 
(   '1', 'siteadmin', 'siteadmin', 'CHANGE', 
    '$1$12345678$2lQK5REWxaFyGz.p/dos3/', '65535', '65535', '', '', 
    'SiteAdmin', 'site', '1'
);

--- fix password when using DES encrypted password:
-- UPDATE `vexim`.`users` SET `crypt` = '0Apup3ZbF9RPg'
--   WHERE `user_id` = '1' LIMIT 1 ;

