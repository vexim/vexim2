CREATE DATABASE IF NOT EXISTS vexim;
DROP TABLE IF EXISTS vexim.domains;
CREATE TABLE IF NOT EXISTS vexim.domains (domain_id mediumint(8) unsigned NOT NULL auto_increment,
	domain varchar(64) NOT NULL default '',
	maildir varchar(128) NOT NULL default '',
	uid smallint(5) unsigned NOT NULL default 'CHANGE',
	gid smallint(5) unsigned NOT NULL default 'CHANGE',
	max_accounts int(10) unsigned NOT NULL default '0', 
	quotas int(10) unsigned NOT NULL default '0',
	type varchar(5) default NULL,
	avscan bool NOT NULL default '0',
	blocklists bool NOT NULL default '0',
	complexpass bool NOT NULL default '0',
	enabled bool NOT NULL default '1',
	mailinglists bool NOT NULL default '0',
	maxmsgsize mediumint(8) unsigned NOT NULL default '0',
	pipe bool NOT NULL default '0',
	spamassassin bool NOT NULL default '0',
	sa_tag smallint(5) unsigned NOT NULL default '0',
	sa_refuse smallint(5) unsigned NOT NULL default '0',
	PRIMARY KEY (domain_id),
	UNIQUE KEY domain (domain),
	KEY domain_id (domain_id),
	KEY domains (domain));
DROP TABLE IF EXISTS vexim.users;
CREATE TABLE IF NOT EXISTS vexim.users (user_id int(10) unsigned NOT NULL auto_increment,
	domain_id mediumint(8) unsigned NOT NULL,
	localpart varchar(192) NOT NULL default '',
	username varchar(255) NOT NULL default '',
	clear varchar(255) default NULL,
	crypt varchar(48) default NULL,
	uid smallint(5) unsigned NOT NULL default '65534',
	gid smallint(5) unsigned NOT NULL default '65534',
	smtp varchar(255) default NULL,
	pop varchar(255) default NULL,
	type enum('local','alias','catch', 'fail', 'piped', 'admin', 'site') NOT NULL default 'local',
	admin bool NOT NULL default '0',
	on_avscan bool NOT NULL default '0',
	on_blocklist bool NOT NULL default '0',
	on_complexpass bool NOT NULL default '0',
	on_forward bool NOT NULL default '0',
	on_piped bool NOT NULL default '0',
	on_spamassassin bool NOT NULL default '0',
	on_vacation bool NOT NULL default '0',
	enabled bool NOT NULL default '1',
	flags varchar(16) default NULL,
	forward varchar(255) default NULL,
	maxmsgsize mediumint(8) unsigned NOT NULL default '0',
	quota int(10) unsigned NOT NULL default '0',
	realname varchar(255) default NULL,
	sa_tag smallint(5) unsigned NOT NULL default '0',
	sa_refuse smallint(5) unsigned NOT NULL default '0',
	tagline varchar(255) default NULL,
	vacation varchar(255) default NULL,
	PRIMARY KEY (user_id),
	UNIQUE KEY username (localpart,domain_id),
	KEY local (localpart));
DROP TABLE IF EXISTS vexim.blocklists;
CREATE TABLE IF NOT EXISTS vexim.blocklists (block_id int(10) unsigned NOT NULL auto_increment,
  	domain_id mediumint(8) unsigned NOT NULL,
	user_id int(10) unsigned default NULL,
	blockhdr varchar(192) NOT NULL default '',
	blockval varchar(192) NOT NULL default '',
	color varchar(8) NOT NULL default '',
	PRIMARY KEY (block_id));
CREATE TABLE IF NOT EXISTS domainalias (domain_id mediumint(8) unsigned NOT NULL,
	alias varchar(64));

GRANT SELECT,INSERT,DELETE,UPDATE ON vexim.* to vexim@localhost IDENTIFIED BY 'CHANGE';
FLUSH PRIVILEGES;

INSERT INTO vexim.domains (domain_id, domain) VALUES ('1', 'admin');

-- Uncomment the following lines by removing the initial '--', if your system uses MD5 passwords:
-- INSERT INTO vexim.users (domain_id, localpart, username, clear, crypt, uid, gid, smtp, pop, realname, type, admin)
-- VALUES ('1', 'siteadmin', 'siteadmin', 'CHANGE', '$1$12345678$2lQK5REWxaFyGz.p/dos3/', '65535', '65535', '', '', 'SiteAdmin', 'site', '1');

-- Uncomment the following lines by removing the initial '--', if your system uses DES passwords:
-- INSERT INTO vexim.users (domain_id, localpart, username, clear, crypt, uid, gid, smtp, pop, realname, type, admin)
-- VALUES ('1', 'siteadmin', 'siteadmin', 'CHANGE', '0Apup3ZbF9RPg', '65535', '65535', '', '', 'SiteAdmin', 'site', '1');
