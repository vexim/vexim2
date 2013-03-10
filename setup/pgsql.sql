CREATE DATABASE vexim WITH ENCODING 'UTF8';
CREATE TABLE domains (domain_id SERIAL PRIMARY KEY,
	domain varchar(64) UNIQUE NOT NULL,
	maildir varchar(128) NOT NULL default '',
	uid int NOT NULL default '65534' CHECK(uid BETWEEN 1 AND 65535),
	gid int NOT NULL default '65534' CHECK(uid BETWEEN 1 AND 65535),
	max_accounts int NOT NULL default '0',
	type varchar(5) default NULL,
	avscan smallint NOT NULL default '0',
	blocklists smallint NOT NULL default '0',
	complexpass smallint NOT NULL default '0',
	enabled smallint NOT NULL default '1',
	mailinglists smallint NOT NULL default '0',
	pipe smallint NOT NULL default '0',
	spamassassin smallint NOT NULL default '0',
	quotas int NOT NULL default '0' CHECK(quotas > -1),
	maxmsgsize int NOT NULL default '0' CHECK(maxmsgsize > -1),
	sa_tag int NOT NULL default '0' CHECK(sa_tag > -1),
	sa_refuse int NOT NULL default '0' CHECK(sa_refuse > -1));
CREATE TABLE users (user_id SERIAL PRIMARY KEY,
	domain_id int NOT NULL,
	localpart varchar(192) NOT NULL,
	username varchar(255) NOT NULL,
	clear varchar(255) default NULL,
	crypt varchar(48) default NULL,
	uid int NOT NULL default '65534' CHECK(uid BETWEEN 1 AND 65535),
	gid int NOT NULL default '65534' CHECK(uid BETWEEN 1 AND 65535),
	smtp varchar(255) default NULL,
	pop varchar(255) default NULL,
	type varchar(8) CHECK(type in ('local','alias','catch', 'fail', 'piped', 'admin', 'site')) NOT NULL,
	admin smallint NOT NULL default '0',
	on_avscan smallint NOT NULL default '0',
	on_blocklist smallint NOT NULL default '0',
	on_complexpass smallint NOT NULL default '0',
	on_forward smallint NOT NULL default '0',
	on_piped smallint NOT NULL default '0',
	on_spamassassin smallint NOT NULL default '0',
	on_vacation smallint NOT NULL default '0',
	enabled smallint NOT NULL default '1',
	flags varchar(16) default NULL,
	forward varchar(255) default NULL,
	maxmsgsize int NOT NULL default '0' CHECK(maxmsgsize > -1),
	quota int NOT NULL default '0',
	realname varchar(255) default NULL,
	sa_tag smallint NOT NULL default '0',
	sa_refuse smallint NOT NULL default '0',
	tagline varchar(255) default NULL,
	vacation varchar(1024) default NULL,
	UNIQUE (localpart,domain_id));
CREATE TABLE blocklists (block_id SERIAL PRIMARY KEY,
	domain_id int NOT NULL,
	localpart varchar(192) NOT NULL,
	user_id int NOT NULL,
	blockhdr varchar(192) NOT NULL default '',
	blockval varchar(192) NOT NULL default '',
	color varchar(8) NOT NULL default '');
CREATE INDEX blocklists_user_id_key ON blocklists (user_id);
CREATE TABLE domainalias (domain_id int NOT NULL,
        alias varchar(64));
CREATE TABLE groups (
        id                  SERIAL PRIMARY KEY,
        domain_id           int CHECK(domain_id > -1),
        name                varchar(64) NOT NULL,
        is_public           char(1) NOT NULL DEFAULT 'Y',
        enabled             smallint NOT NULL DEFAULT '1'
);
CREATE INDEX groups_name ON groups(domain_id, name);
CREATE TABLE group_contents (
        group_id            int NOT NULL,
        member_id           int NOT NULL,
        UNIQUE (group_id,  member_id));

CREATE USER vexim WITH PASSWORD 'mypass' NOCREATEDB NOCREATEUSER;
GRANT SELECT,INSERT,DELETE,UPDATE ON domains,users,blocklists,blocklists_block_id_seq,domains_domain_id_seq,users_user_id_seq,domainalias to vexim;

INSERT INTO domains (domain_id, domain) VALUES ('1', 'admin');
INSERT INTO users (domain_id, localpart, username, clear, crypt, uid, gid, smtp, pop, realname, type, admin)
  		VALUES ('1',
		'siteadmin',
		'siteadmin',
		'CHANGE',
		'\$1\$12345678\$2lQK5REWxaFyGz.p/dos3/',
		'65535',
		'65535',
		'',
		'',
		'SiteAdmin',
		'site',
		'1');
