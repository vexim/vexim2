#!/usr/local/bin/perl

use strict;
use DBI();
my $mydbh = DBI->connect("DBI:mysql:database=vexim;host=localhost",
                       "root", "medicine",
                       {'RaiseError' => 1});
my $pgdbh = DBI->connect("DBI:Pg:dbname=avleen", "avleen", "f00dlew1t",
                       {'RaiseError' => 1});
my $mailstore = "/usr/local/mail";
my $uid = "90";
my $gid = "90";

#print "Dropping already present tables...\n";
$mydbh->do("DROP TABLE IF EXISTS vexim.domains");
$mydbh->do("DROP TABLE IF EXISTS vexim.users");
$pgdbh->do("DROP TABLE domains");
$pgdbh->do("DROP TABLE users");
print "Creating new MySQL tables...\n";
$mydbh->do("CREATE TABLE IF NOT EXISTS domains (domain_id mediumint(8) unsigned NOT NULL auto_increment,
        domain varchar(64) NOT NULL default '',
	maildir varchar(128) NOT NULL default '',
        uid smallint(5) unsigned NOT NULL default '$uid',
        gid smallint(5) unsigned NOT NULL default '$gid',
	type varchar(5) default NULL,
        spamassassin bool NOT NULL default '0',
        avscan bool NOT NULL default '0',
        mailinglists bool NOT NULL default '0',
        quotas int(10) unsigned NOT NULL default '0',
        blocklists bool NOT NULL default '0',
	pipe bool NOT NULL default '0',
        enabled bool NOT NULL default '1',
        complexpass bool NOT NULL default '0',
        PRIMARY KEY (domain_id),
        UNIQUE KEY domain (domain),
        KEY domain_id (domain_id),
        KEY domains (domain))") or die "Could not create table domains";
$mydbh->do("CREATE TABLE IF NOT EXISTS users (user_id int(10) unsigned NOT NULL auto_increment,
        domain_id mediumint(8) unsigned NOT NULL,
        localpart varchar(192) NOT NULL default '',
        clear varchar(255) default NULL,
        crypt varchar(48) default NULL,
        uid smallint(5) unsigned NOT NULL default '65534',
        gid smallint(5) unsigned NOT NULL default '65534',
        smtp varchar(255) default NULL,
        pop varchar(255) default NULL,
        realname varchar(255) default NULL,
        type enum('local','alias','catch', 'fail', 'piped', 'admin', 'site') NOT NULL default 'local',
        admin bool NOT NULL default '0',
        avscan bool NOT NULL default '0',
        spamassassin bool NOT NULL default '0',
        blocklist bool NOT NULL default '0',
        complexpass bool NOT NULL default '0',
        enabled bool NOT NULL default '0',
        quota int(10) unsigned NOT NULL default '0',
        sa_tag smallint(5) unsigned NOT NULL default '5',
        sa_refuse smallint(5) unsigned NOT NULL default '10',
        on_vacation bool NOT NULL default '0',
        vacation varchar(255) default NULL,
        flags varchar(16) default NULL,
        tagline varchar(255) default NULL,
        PRIMARY KEY (user_id),
        UNIQUE KEY username (localpart,domain_id),
        KEY local (localpart))") or die "Could not create table users";
print "Creating new PostgreSQL tables...\n";
$pgdbh->do("CREATE TABLE domains (domain_id SERIAL PRIMARY KEY,
	  domain varchar(64) UNIQUE NOT NULL,
	  maildir varchar(128) NOT NULL default '',
	  uid int NOT NULL default '65534' CHECK(uid BETWEEN 1 AND 65535),
	  gid int NOT NULL default '65534' CHECK(uid BETWEEN 1 AND 65535),
	  type varchar(5) NOT NULL,
	  spamassassin BOOLEAN NOT NULL default '0',
	  avscan BOOLEAN NOT NULL default '0',
	  mailinglists BOOLEAN NOT NULL default '0',
	  quotas int NOT NULL default '0' CHECK(quotas > -1),
	  blocklists BOOLEAN NOT NULL default '0',
	  pipe BOOLEAN NOT NULL default '0',
	  enabled BOOLEAN NOT NULL default '1',
	  complexpass BOOLEAN NOT NULL default '0')") or die "Could not create table domains";
$pgdbh->do("CREATE TABLE users (user_id SERIAL PRIMARY KEY,
	  domain_id int NOT NULL,
	  localpart varchar(192) NOT NULL,
	  clear varchar(255) default NULL,
	  crypt varchar(48) default NULL,
	  uid int NOT NULL default '65534' CHECK(uid BETWEEN 1 AND 65535),
	  gid int NOT NULL default '65534' CHECK(uid BETWEEN 1 AND 65535),
	  smtp varchar(255) default NULL,
	  pop varchar(255) default NULL,
	  realname varchar(255) default NULL,
	  type varchar(8) CHECK(type in ('local','alias','catch', 'fail', 'piped', 'admin', 'site')) NOT NULL,
	  admin BOOLEAN NOT NULL default '0',
	  avscan BOOLEAN NOT NULL default '0',
	  spamassassin BOOLEAN NOT NULL default '0',
	  blocklist BOOLEAN NOT NULL default '0',
	  complexpass BOOLEAN NOT NULL default '0',
	  enabled BOOLEAN NOT NULL default '1',
	  quota int NOT NULL default '0',
	  sa_tag smallint NOT NULL default '5',
	  sa_refuse smallint NOT NULL default '10',
	  on_vacation BOOLEAN NOT NULL default '0',
	  vacation varchar(255) default NULL,
	  flags varchar(16) default NULL,
	  tagline varchar(255) default NULL,
	  UNIQUE (localpart,domain_id))") or die "Could not create table users";

# Implement fixes to the new databases
print "Fixing admin column in current DB, setting 'NULL' to ''\n";
$mydbh->do("UPDATE exim.users SET admin='' WHERE admin IS NULL;");
print "Fix complete...\n";
print "Tables created. Starting domain migration (MySQL to MySQL)...\n";
$mydbh->do("INSERT INTO vexim.domains (domain) SELECT DISTINCT domain FROM exim.users ORDER BY domain");
print "Domain migration complete.\n\n";
print " Setting all current domains to local...\n";
$mydbh->do("UPDATE vexim.domains SET type='local'");
print "Fix complete.\n\n";

# Set the maildir for each domain
print " Setting maildir for domains...\n";
my $sth = $mydbh->prepare("SELECT DISTINCT domain_id,domain FROM vexim.domains");
$sth->execute();
while (my $ref = $sth->fetchrow_hashref()) {
  my $domain_id = $ref->{'domain_id'};
  my $domain = $ref->{'domain'};
  my $lkp = $mydbh->prepare("UPDATE vexim.domains SET maildir='${mailstore}/$domain' WHERE domain='$domain'")
		or die "Error setting maildir for $domain";
  $lkp->execute();
}

# Do the migration to MySQL's new database.
print " Starting migration of user accounts (MySQL to MySQL)...\n";
my $sth = $mydbh->prepare("SELECT DISTINCT domain_id,domain FROM vexim.domains");
$sth->execute();
while (my $ref = $sth->fetchrow_hashref()) {
  my $domain_id = $ref->{'domain_id'};
  my $domain = $ref->{'domain'};
  print "\tMigrating users for Domain: $domain\n";
  my $lkp = $mydbh->prepare("INSERT INTO vexim.users (domain_id, localpart, clear, crypt, uid, gid, smtp, pop, realname, type, admin, enabled)
		SELECT '$domain_id' AS domain_id,
		local_part AS localpart,
		cpassword AS clear,
		password AS crypt,
		t1.uid,
		t1.gid,
		smtphome AS smtp,
		pophome AS pop,
		realname,
		t1.type,
		'0',
		'1' FROM exim.users t1 INNER JOIN vexim.domains t2 ON t1.domain=t2.domain where
		t2.domain='$domain' and t1.admin=''") or die "Error migrating users for $domain";
  $lkp->execute();
}

print " Starting migration of admin accounts (MySQL to MySQL)...\n";
my $sth = $mydbh->prepare("SELECT DISTINCT domain_id,domain FROM vexim.domains");
$sth->execute();
while (my $ref = $sth->fetchrow_hashref()) {
  my $domain_id = $ref->{'domain_id'};
  my $domain = $ref->{'domain'};
  print "\tMigrating admins for Domain: $domain\n";
  my $lkp = $mydbh->prepare("INSERT INTO vexim.users (domain_id, localpart, clear, crypt, uid, gid, smtp, pop, realname, type, admin, enabled)
		SELECT '$domain_id' AS domain_id,
		t1.local_part AS localpart,
		cpassword AS clear,
		password AS crypt,
		t1.uid,
		t1.gid,
		smtphome AS smtp,
		pophome AS pop,
		realname,
		t1.type,
		'1',
		'1' as admin FROM exim.users t1 INNER JOIN vexim.domains t2 ON t1.domain=t2.domain where
		t2.domain='$domain' and t1.admin!='' and username !='siteadmin'")
		or die "Error migrating users for $domain";
  $lkp->execute();
}
$mydbh->do("UPDATE vexim.domains SET type='admin' WHERE domains.domain='admin'");
$mydbh->do("INSERT into vexim.users (domain_id, localpart, clear, crypt, uid, gid, smtp, pop, realname, type, admin) VALUES ('1',
		'siteadmin',
		'CHANGE',
		'\$1\$12345678\$2lQK5REWxaFyGz.p/dos3/',
		'65535',
		'65535',
		'',
		'',
		'SiteAdmin',
		'site',
		'1')");

print "Exporting user data from MySQL...\n";
$mydbh->do("select * INTO OUTFILE '/tmp/vexim-mysql-migrate-users' FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY \"'\" LINES TERMINATED BY \"\n\" from vexim.users");
print "Exporting domains data from MySQL...\n";
$mydbh->do("select * INTO OUTFILE '/tmp/vexim-mysql-migrate-domains' FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY \"'\" LINES TERMINATED BY \"\n\" from vexim.domains");

print "Importing user data into PostgreSQL...\n";
open IN, "</tmp/vexim-mysql-migrate-users" or die $!;
while(<IN>)
{
  s/\\N/NULL/g;
  s/,(\d),(\d),(\d),(\d),(\d),(\d),(\d+?,\d+?,\d+?),(\d),/,\'$1\',\'$2\',\'$3\',\'$4\',\'$5\',\'$6\',$7,\'$8\',/g;
  s/^\d+,/nextval\('public.users_user_id_seq'::text\),/g;
  #print OUT;
  #print;
  $pgdbh->do("INSERT INTO users VALUES ($_)");
}
close IN;

print "Importing domain data into PostgreSQL...\n";
open IN, "</tmp/vexim-mysql-migrate-domains" or die $!;
while(<IN>)
{
  s/(\d),(\d),(\d),(\d+?),(\d),(\d),(\d),(\d)$/\'$1\',\'$2\',\'$3\',$4,\'$5\',\'$6\',\'$7\',\'$8\'/g;
  s/^\d+,/nextval\('public.domains_domain_id_seq'::text\),/g;
  #print STDOUT;
  #print;
  $pgdbh->do("INSERT INTO domains VALUES ($_)");
}
close IN;


print "Migration complete!\n";
print "Please delete /tmp/vexim-mysql-migrate-users and /tmp/vexim-mysql-migrate-domains\n\n";

$sth->finish();
$pgdbh->disconnect();
