CREATE TABLE site (
	site_id		TINYINT UNSIGNED NOT NULL DEFAULT 0,
	clear		VARCHAR(255) NOT NULL, 
	crypt		VARCHAR(48) NOT NULL, 
	PRIMARY KEY (site_id)
);
	
CREATE TABLE domains (
	domain_id mediumint(8) unsigned NOT NULL auto_increment,
	domain varchar(64) NOT NULL default '',
	spamassassin bool NOT NULL default '0',
	avscan bool NOT NULL default '0',
	mailinglists bool NOT NULL default '0',
	quotas int(10) unsigned NOT NULL default '0',
	blocklists bool NOT NULL default '0',
	uid smallint(5) unsigned NOT NULL default '65534',
	gid smallint(5) unsigned NOT NULL default '65534',
	complexpass bool NOT NULL default '0',
	tagline varchar(255) default NULL,
	PRIMARY KEY  (domain_id),
	UNIQUE KEY domain (domain),
	KEY domain_id (domain_id),
	KEY domains (domain)
);
	
CREATE TABLE users (
	user_id int(10) unsigned NOT NULL auto_increment,
	domain_id mediumint(8) unsigned NOT NULL,
	localpart varchar(192) NOT NULL default '',
	clear varchar(255) default NULL,
	crypt varchar(48) default NULL,
	uid smallint(5) unsigned NOT NULL default '0',
	gid smallint(5) unsigned NOT NULL default '0',
	smtp varchar(255) NOT NULL default '',
	pop varchar(255) NOT NULL default '',
	realname varchar(255) NOT NULL default '',
	type enum('local','alias','catch') NOT NULL default 'local',
	admin bool NOT NULL default '0',
	avscan bool NOT NULL default '0',
	blocklist bool NOT NULL default '0',
	complexpass bool NOT NULL default '0',
	quota int(10) unsigned NOT NULL default '0',
	sa_tag smallint(5) unsigned NOT NULL default '5',
	sa_refuse smallint(5) unsigned NOT NULL default '10',
	on_vacation bool NOT NULL default '0',
	vacation varchar(255) default NULL,
	flags varchar(16) default NULL,
	PRIMARY KEY  (user_id),
	UNIQUE KEY username (localpart,domain_id),
	KEY local (localpart)
);

CREATE TABLE dns_list (
	domain_id MEDIUMINT UNSIGNED NOT NULL,
	dns VARCHAR(64) NOT NULL,
	message VARCHAR(255),
	UNIQUE KEY username (domain_id,dns),
);
