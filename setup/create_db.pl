#!/usr/local/bin/perl

use strict;
use DBI();
use Getopt::Long;

my ($dbh, $mydbh, $pgdbh);
my ($act, $dbtype, $uid, $gid, $mailstore);
my ($superuser, $superpass);
my ($veximpass, $veximpassconfirm);

&GetOptions(	"act=s" =>\$act,
		"dbtype=s" =>\$dbtype,
		"uid=i" =>\$uid,
		"gid=i" =>\$gid,
		"mailstore" =>\$mailstore);
usage() unless defined $act;
usage() unless defined $dbtype;
my $uid = "90" unless defined $uid;
my $gid = "90" unless defined $gid;
$mailstore = "/usr/local/mail" unless defined $mailstore;


#####################################
# If something goes a way we don't  #
# expect it to, bomb with the usage #
#####################################

sub usage {
	print "Usage:\tcreate_db.pl --act=<action> --dbtype=<dbtype> --uid=<uid> --gid=<gid> --mailstore=<dir>\n";
	print "\tPossible actions are: newdb, migratemysql, migratepostgresql\n";
	print "\tAvailable dbtypes: mysql, pgsql\n";
	print "\tuid and gid are the default UID's and GID's for domains\n";
	print "\t    (defaults to uid 90, gid 90)\n";
	print "\tmailstore is the directory under which the maildirs for domains are created\n";
	print "\t    (defaults to /usr/local/mail)\n\n";
	print "Examples: create_db.pl --act=newdb --dbtype=mysql --uid=90 --gid=90 --mailstore=/usr/local/mail\n";
	print "\t    (will create a new 'vexim' database for a new install with mysql)\n";
	print "\tcreate_db.pl --act=migratemysql --dbtype=mysql --uid=90 --gid=90 --mailstore=/usr/local/mail\n";
	print "\t    (will migrate database from a vexim 1.x mysql database, to a vexim 2.x mysql database)\n";
	print "\tcreate_db.pl --act=migratepostgresql --dbtype=mysql --uid=90 --gid=90 --mailstore=/usr/local/mail\n";
	print "\t    (will migrate database from a vexim 1.x mysql database, to a vexim 2.x postgres database)\n";
	print "\n\n";
	exit 1;
}


#####################################
# Collect the username and password #
# for the database root user        @
#####################################

print "Using dbtype $dbtype\n";
print "Please enter the username of the $dbtype superuser: "; chomp($superuser = <STDIN>);
`stty -echo`;
print "Please enter the password of the $dbtype superuser: "; chomp($superpass = <STDIN>);
`stty echo`;


#####################################
# This sub collects and verifies    #
# the username for the 'vexim' user #
# in the database                   @
#####################################

sub veximpw {
  `stty -echo`;
  print "\nPlease enter a password for the 'vexim' database user: ";
  chomp($veximpass = <STDIN>);
  print "\nConfirm password: ";
  chomp($veximpassconfirm = <STDIN>);
  while ($veximpass ne $veximpassconfirm) {
    print "\nPassword mismatch. Please enter a password for the 'vexim' $dbtype database user: ";
    chomp($veximpass = <STDIN>);
    print "\nConfirm password: ";
    chomp($veximpassconfirm = <STDIN>);
  }
  `stty echo`;
}


#####################################
# This sub creates the MySQL db     #
#####################################

sub create_mysqldb {
  $mydbh->do("DROP DATABASE IF EXISTS vexim");
  $mydbh->do("CREATE DATABASE vexim")
    or die "Could not create the database 'vexim' in MySQL!";
}


#####################################
# Here we can create MySQK tables   @
#####################################

sub create_mysqltables {
  $mydbh->do("DROP TABLE IF EXISTS vexim.domains");
  $mydbh->do("CREATE TABLE IF NOT EXISTS vexim.domains (domain_id mediumint(8) unsigned NOT NULL auto_increment,
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
        KEY domains (domain))") or die "Could not create table domains in the vexim database!";
  print "\nCreated domains table\n";
  $mydbh->do("DROP TABLE IF EXISTS vexim.users");
  $mydbh->do("CREATE TABLE IF NOT EXISTS vexim.users (user_id int(10) unsigned NOT NULL auto_increment,
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
        on_spamassassin bool NOT NULL default '0',
        on_vacation bool NOT NULL default '0',
        enabled bool NOT NULL default '1',
        flags varchar(16) default NULL,
        forward varchar(255) default NULL,
        quota int(10) unsigned NOT NULL default '0',
        realname varchar(255) default NULL,
        sa_tag smallint(5) unsigned NOT NULL default '0',
        sa_refuse smallint(5) unsigned NOT NULL default '5',
        tagline varchar(255) default NULL,
        vacation varchar(255) default NULL,
        PRIMARY KEY (user_id),
        UNIQUE KEY username (localpart,domain_id),
        KEY local (localpart))") or die "Could not create table users in the vexim database!";
  print "Created users table\n";
  $mydbh->do("DROP TABLE IF EXISTS vexim.blocklists");
  $mydbh->do("CREATE TABLE IF NOT EXISTS vexim.blocklists (block_id int(10) unsigned NOT NULL auto_increment,
  	domain_id mediumint(8) unsigned NOT NULL,
	localpart varchar(192) NOT NULL default '',
	blockaddr varchar(192) NOT NULL default '',
	PRIMARY KEY (block_id))") or die "Could not create table blocklists in the vexim database!";
  print "Created blocklists table\n";
}


#####################################
# Thsi is the equivilent code for   #
# generating PostgreSQL tables. The #
# Code is certainly not the same    #
# due to slight difference in the   #
# SQL implementation.               #
#####################################

sub create_postgrestables {
print "\nCreating new PostgreSQL tables...\n";
$pgdbh->do("CREATE TABLE domains (domain_id SERIAL PRIMARY KEY,
          domain varchar(64) UNIQUE NOT NULL,
          maildir varchar(128) NOT NULL default '',
          uid int NOT NULL default '65534' CHECK(uid BETWEEN 1 AND 65535),
          gid int NOT NULL default '65534' CHECK(uid BETWEEN 1 AND 65535),
          type varchar(5) NOT NULL,
          spamassassin smallint NOT NULL default '0',
          avscan smallint NOT NULL default '0',
          mailinglists smallint NOT NULL default '0',
          quotas int NOT NULL default '0' CHECK(quotas > -1),
          blocklists smallint NOT NULL default '0',
          pipe smallint NOT NULL default '0',
          enabled smallint NOT NULL default '1',
          complexpass smallint NOT NULL default '0')") or die "Could not create table domains";
  print "\nCreated domains table\n";
  $pgdbh->do("CREATE TABLE users (user_id SERIAL PRIMARY KEY,
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
          on_spamassassin smallint NOT NULL default '0',
          on_vacation smallint NOT NULL default '0',
          enabled smallint NOT NULL default '1',
          flags varchar(16) default NULL,
	  forward varchar(255) default NULL,
          quota int NOT NULL default '0',
          realname varchar(255) default NULL,
          sa_tag smallint NOT NULL default '0',
          sa_refuse smallint NOT NULL default '5',
          tagline varchar(255) default NULL,
          vacation varchar(255) default NULL,
          UNIQUE (localpart,domain_id))") or die "Could not create table users";
  print "\nCreated users table\n";
  $pgdbh->do("CREATE TABLE blocklists (block_id SERIAL PRIMARY KEY,
  	domain_id int NOT NULL,
	localpart varchar(192) NOT NULL default '',
	blockaddr varchar(192) NOT NULL default '')") or die "Could not create table blocklists in the vexim database!";
  print "\nCreated blocklists table\n";
}


#####################################
# This adds the 'vexim' database    #
# user, using the passwords         #
# collected at the start of the     #
# script.                           #
#####################################

sub add_mysqlveximuser {
  print "Adding vexim database user...\n";
  veximpw();
  $mydbh->do("GRANT SELECT,INSERT,DELETE,UPDATE ON vexim.* to vexim\@localhost IDENTIFIED BY '$veximpass'")
    or die "Could not create the user 'vexim' in the MySQL database!";
  $mydbh->do("FLUSH PRIVILEGES") or die "Could not flush privileges!";
}

#####################################
# Same for PostgreSQL               #
#####################################

sub add_postgresveximuser {
  print "Adding vexim database user...\n";
  veximpw() unless $act eq "migratepostgresql";
  $pgdbh->do("CREATE USER vexim WITH PASSWORD '$veximpass' NOCREATEDB NOCREATEUSER")
    or die "Could not create the user 'vexim' in the MySQL database!";
  $pgdbh->do("GRANT SELECT,INSERT,DELETE,UPDATE ON domains,users to vexim")
    or die "Could not create the user 'vexim' in the MySQL database!";
}


#####################################
# A special 'siteadmin' user has to #
# be added seperately from the rest #
#####################################

sub add_siteadminuser {
  print "\nAdding siteadmin user...\n";
  if ($dbtype == "mysql") { $dbh = $mydbh; } elsif ($dbtype == "pgsql") { $dbh = $pgdbh; }
  $dbh->do("INSERT INTO vexim.domains (domain_id, domain) VALUES ('1', 'admin')");
  $dbh->do("INSERT INTO vexim.users (domain_id, localpart, username, clear, crypt, uid, gid, smtp, pop, realname, type, admin)
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
		'1')") or die "Could not create the user 'siteadmin' in the vexim database!";
  print "The user 'siteadmin' has been added with the password 'CHANGE'\n";
  print "Please log in to the web interface and change this!\n";
}


#####################################
# If the user is migrating from     #
# vexim 1.3 to 2.0, this migration  #
# code is called to preserve data   #
#####################################

sub migratemysql {
  $mydbh->do("INSERT INTO vexim.domains (domain) SELECT DISTINCT domain FROM exim.users ORDER BY domain");
  my $sth = $mydbh->prepare("SELECT DISTINCT domain FROM vexim.domains");
  $sth->execute();
  while (my $ref = $sth->fetchrow_hashref()) {
    my $domain_id = $ref->{'domain_id'};
    my $domain = $ref->{'domain'};
    my $lkp = $mydbh->prepare("UPDATE vexim.domains SET maildir='$mailstore/$domain' WHERE domain='$domain'")
      or die "Error updating maildir for $domain";
    $lkp->execute();
  }
  print "Domain migration complete.\n\n";

  print " Starting migration of user accounts...\n";
  my $sth = $mydbh->prepare("SELECT DISTINCT domain_id,domain FROM vexim.domains");
  $sth->execute();
  while (my $ref = $sth->fetchrow_hashref()) {
    my $domain_id = $ref->{'domain_id'};
    my $domain = $ref->{'domain'};
    print "\tMigrating users for Domain: $domain\n";
    my $lkp = $mydbh->prepare("INSERT INTO vexim.users (domain_id, localpart, username, clear, crypt, uid, gid, smtp, pop, realname, type, admin)
			       SELECT '$domain_id' AS domain_id,
			       local_part AS localpart,
			       t1.username,
			       cpassword AS clear,
			       password AS crypt,
			       t1.uid,
			       t1.gid,
			       smtphome AS smtp,
			       pophome AS pop,
			       t1.realname,
			       t1.type,
			       '0' FROM exim.users t1 INNER JOIN vexim.domains t2 ON t1.domain=t2.domain where
			       t2.domain='$domain' and t1.admin=''")
      or die "Error migrating users for $domain";
    $lkp->execute();
  }
  print " Starting migration of admin accounts (MySQL to MySQL)...\n";
  my $sth = $mydbh->prepare("SELECT DISTINCT domain_id,domain FROM vexim.domains");
  $sth->execute();
  while (my $ref = $sth->fetchrow_hashref()) {
    my $domain_id = $ref->{'domain_id'};
    my $domain = $ref->{'domain'};
    print "\tMigrating admins for Domain: $domain\n";
    my $lkp = $mydbh->prepare("INSERT INTO vexim.users (domain_id, localpart, username, clear, crypt, uid, gid, smtp, pop, realname, type, admin, enabled)
			       SELECT '$domain_id' AS domain_id,
			       t1.local_part AS localpart,
			       t1.username,
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
  $mydbh->do("UPDATE vexim.domains SET type='local' WHERE type is NULL");
}


#####################################
# The following sub is merely an    #
# extension of the previous sub, if #
# the migration is to PostgreSQL    #
#####################################

sub migratepostgresql() {
  print "Exporting user data from MySQL...\n";
  $mydbh->do("select * INTO OUTFILE '/tmp/vexim-mysql-migrate-users'
  		FIELDS TERMINATED BY ','
		OPTIONALLY ENCLOSED BY \"'\"
		LINES TERMINATED BY \"\n\" from vexim.users");
  print "Exporting domains data from MySQL...\n";
  $mydbh->do("select * INTO OUTFILE '/tmp/vexim-mysql-migrate-domains'
  		FIELDS TERMINATED BY ','
		OPTIONALLY ENCLOSED BY \"'\"
		LINES TERMINATED BY \"\n\" from vexim.domains");

  print "Importing user data into PostgreSQL...\n";
  open IN, "</tmp/vexim-mysql-migrate-users" or die $!;
  while(<IN>)
  {
    s/\\N/NULL/g;
    s/^\d+,/nextval\('public.users_user_id_seq'::text\),/g;
    $pgdbh->do("INSERT INTO users VALUES ($_)");
  }
  close IN;

  print "Importing domain data into PostgreSQL...\n";
  open IN, "</tmp/vexim-mysql-migrate-domains" or die $!;
  while(<IN>)
  {
    s/\\N/'local'/g;
    s/^\d+,/nextval\('public.domains_domain_id_seq'::text\),/g;
    $pgdbh->do("INSERT INTO domains VALUES ($_)");
  }
  close IN;

  print "Migration complete!\n";
  print "Please delete /tmp/vexim-mysql-migrate-users and /tmp/vexim-mysql-migrate-domains\n\n";
}


###########################################################
# The actual call to the subs go below here. This comment #
# if nothing else, provides a nice buffer between the     #
# rather ugly subs, and the one ring that bind them...    #
###########################################################

if ($dbtype eq "mysql") {
  $mydbh = DBI->connect("DBI:mysql:database=mysql;host=localhost", "$superuser", "$superpass", {'RaiseError' => 1});
  create_mysqldb();
  create_mysqltables();
  sleep 1;
  add_mysqlveximuser();
  add_siteadminuser();
  print "Database created successfully!\n\n";
} elsif ($dbtype eq "pgsql") {
  print "Please create the PostgreSQL database with 'su - pgsql; createdb vexim'\n";
  print "Then press any key to continue..\n\n";
  my $null = <STDIN>;
  $pgdbh = DBI->connect("DBI:Pg:dbname=vexim", "$superuser", "$superpass", {'RaiseError' => 1});
  create_postgrestables();
  sleep 1;
  add_postgresveximuser();
  add_siteadminuser();
  print "Database created successfully!\n";
}


###########################################################
# If the user asks to migrate data from an old database,  #
# carry on!                                               #
###########################################################

if ($act eq "migratemysql") {
  migratemysql();
} elsif ($act eq "migratepostgresql") {
  print "Please create the PostgreSQL database with 'su - pgsql; createdb vexim'\n";
  print "Then press any key to continue..\n";
  my $null = <STDIN>;
  print "Please enter the username of the postgresql superuser: "; chomp(my $pgsuperuser = <STDIN>);
  `stty -echo`;
  print "Please enter the password of the postgresql superuser: "; chomp(my $pgsuperpass = <STDIN>);
  `stty echo`;
  $pgdbh = DBI->connect("DBI:Pg:dbname=vexim", "$pgsuperuser", "$pgsuperpass", {'RaiseError' => 1});
  create_postgrestables();
  sleep 1;
  add_postgresveximuser();
  print "Database created successfully!\n";
  migratemysql();
  migratepostgresql();
}
