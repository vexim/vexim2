#!/usr/local/bin/perl

use strict;
use DBI();
my $dbh = DBI->connect("DBI:mysql:database=vexim;host=localhost",
                       "USER", "PASS",
                       {'RaiseError' => 1});

#print "Dropping already present tables...\n";
$dbh->do("DROP TABLE IF EXISTS domains");
$dbh->do("DROP TABLE IF EXISTS users");
print "Creating new tables...\n";
$dbh->do("CREATE TABLE IF NOT EXISTS domains (domain_id mediumint(8) unsigned NOT NULL auto_increment,
	domain varchar(64) NOT NULL default '',
	spamassassin bool NOT NULL default '0',
	avscan bool NOT NULL default '0',
	mailinglists bool NOT NULL default '0',
	quotas int(10) unsigned NOT NULL default '0',
	blocklists bool NOT NULL default '0',
	uid smallint(5) unsigned NOT NULL default '65534',
	gid smallint(5) unsigned NOT NULL default '65534',
	complexpass bool NOT NULL default '0',
	PRIMARY KEY  (domain_id),
	UNIQUE KEY domain (domain),
	KEY domain_id (domain_id),
	KEY domains (domain))") or die "Could not create table domains";
$dbh->do("CREATE TABLE IF NOT EXISTS users (user_id int(10) unsigned NOT NULL auto_increment,
	domain_id mediumint(8) unsigned NOT NULL,
	localpart varchar(192) NOT NULL default '',
	clear varchar(255) default NULL,
	crypt varchar(48) default NULL,
	uid smallint(5) unsigned NOT NULL default '0',
	gid smallint(5) unsigned NOT NULL default '0',
	smtp varchar(255) NOT NULL default '',
	pop varchar(255) NOT NULL default '',
	realname varchar(255) NOT NULL default '',
	type enum('local','alias','catch', 'piped') NOT NULL default 'local',
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
	tagline varchar(255) default NULL,
	PRIMARY KEY  (user_id),
	UNIQUE KEY username (localpart,domain_id),
	KEY local (localpart))") or die "Could not create table users";

print "Tables created. Starting domain migration...\n";
$dbh->do("INSERT INTO vexim.domains (domain) SELECT DISTINCT domain FROM exim.users ORDER BY domain");
print "Domain migration complete.\n\n";

print " Starting migration of user accounts...\n";
my $sth = $dbh->prepare("SELECT DISTINCT domain_id,domain FROM vexim.domains");
$sth->execute();
while (my $ref = $sth->fetchrow_hashref()) {
  my $domain_id = $ref->{'domain_id'};
  my $domain = $ref->{'domain'};
  print "\tMigrating users for Domain: $domain\n";
  my $lkp = $dbh->prepare("INSERT INTO users (domain_id, localpart, clear, crypt, uid, gid, smtp, pop, realname, type, admin)
		SELECT '$domain_id' AS domain_id,
		local_part AS localpart,
		cpassword AS clear,
		password AS crypt,
		t1.uid,
		t1.gid,
		smtphome AS smtp,
		pophome AS pop,
		realname,
		type,
		'0' FROM exim.users t1 INNER JOIN vexim.domains t2 ON t1.domain=t2.domain where
		t2.domain='$domain' and t1.admin=''") or die "Error migrating users for $domain";
  $lkp->execute();
}

print " Starting migration of admin accounts...\n";
my $sth = $dbh->prepare("SELECT DISTINCT domain_id,domain FROM vexim.domains");
$sth->execute();
while (my $ref = $sth->fetchrow_hashref()) {
  my $domain_id = $ref->{'domain_id'};
  my $domain = $ref->{'domain'};
  print "\tMigrating users for Domain: $domain\n";
  my $lkp = $dbh->prepare("INSERT INTO users (domain_id, localpart, clear, crypt, uid, gid, smtp, pop, realname, type, admin)
		SELECT '$domain_id' AS domain_id,
		local_part AS localpart,
		cpassword AS clear,
		password AS crypt,
		t1.uid,
		t1.gid,
		smtphome AS smtp,
		pophome AS pop,
		realname,
		type,
		'1' FROM exim.users t1 INNER JOIN vexim.domains t2 ON t1.domain=t2.domain where
		t2.domain='$domain' and t1.admin!=''") or die "Error migrating users for $domain";
  $lkp->execute();
}
my $sth = $dbh->prepare("UPDATE users SET localpart='siteadmin' WHERE localpart='site' and realname='SiteAdmin'");
$sth->execute();
$sth->finish();
print "Migration complete!\n\n";
