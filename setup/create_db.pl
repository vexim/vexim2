#!/usr/local/bin/perl

use strict;
use DBI();
use Getopt::Long;

my $dbh;
my $null;
my ($dbtype, $uid, $gid, $mailstore);
my ($dbrootuser, $dbrootpass);
my ($veximpass, $veximpassconfirm);

&GetOptions(	"dbtype=s" =>\$dbtype,
		"uid=i" =>\$uid,
		"gid=i" =>\$gid,
		"mailstore" =>\$mailstore);
usage() unless defined $dbtype;
my $uid = "90" unless defined $uid;
my $gid = "90" unless defined $gid;
$mailstore = "/usr/local/mail" unless defined $mailstore;

sub usage {
	print "Usage:\tcreate_db.pl --dbtype=<dbtype> --uid=<uid> --gid=<gid> --mailstore=<dir>\n";
	print "\tAvailable dbtypes: mysql, pgsql\n";
	print "\tuid and gid are the default UID's and GID's for domains\n";
	print "\t  ->(defaults to uid 90, gid 90)\n";
	print "\tmailstore is the directory under which the maildirs for domains are created\n";
	print "\t  ->(defaults to /usr/local/mail)\n";
	exit 1;
}

print "Please enter the name of the database superuser: ";
chomp ($dbrootuser = <STDIN>);
print "Please enter the password for the database superuser: ";
`stty -echo`;
chomp ($dbrootpass = <STDIN>);
`stty echo`;

print "Using dbtype $dbtype\n";

if ($dbtype == "mysql") {
  $dbh = DBI->connect("DBI:mysql:database=mysql;host=localhost", "$dbrootuser", "$dbrootpass", {'RaiseError' => 1});
  $dbh->do("DROP DATABASE IF EXISTS vexim");
  $dbh->do("CREATE DATABASE vexim")
    or die "Could not create the database 'vexim' in MySQL!";
  $dbh->do("DROP TABLE IF EXISTS vexim.domains");
  $dbh->do("CREATE TABLE IF NOT EXISTS vexim.domains (domain_id mediumint(8) unsigned NOT NULL auto_increment,
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
  print "Created domains table\n";
  $dbh->do("DROP TABLE IF EXISTS vexim.users");
  $dbh->do("CREATE TABLE IF NOT EXISTS vexim.users (user_id int(10) unsigned NOT NULL auto_increment,
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
        KEY local (localpart))") or die "Could not create table users in the vexim database!";
  print "Created users table\n";
  sleep 1;
  `stty -echo`;
  print "\nPlease enter a password for the 'vexim' database user: ";
  chomp($veximpass = <STDIN>);
  print "\nConfirm password: ";
  chomp($veximpassconfirm = <STDIN>);
  while ($veximpass ne $veximpassconfirm) {
    print "\nPassword mismatch. Please enter a password for the 'vexim' database user: ";
    chomp($veximpass = <STDIN>);
    print "\nConfirm password: ";
    chomp($veximpassconfirm = <STDIN>);
  }
  `stty echo`;
  print "Adding vexim database user...\n";
  $dbh->do("GRANT SELECT,INSERT,DELETE,UPDATE ON vexim.* to vexim\@localhost IDENTIFIED BY '$veximpass'")
    or die "Could not create the user 'vexim' in the MySQL database!";
  $dbh->do("FLUSH PRIVILEGES") or die "Could not flush privileges!";
  print "Adding siteadmin user...\n";
  $dbh->do("INSERT INTO vexim.domains (domain_id, domain) VALUES ('1', 'admin')");
  $dbh->do("INSERT INTO vexim.users (domain_id, localpart, clear, crypt, uid, gid, smtp, pop, realname, type, admin) VALUES ('1', 'siteadmin', 'CHANGE', '\$1\$12345678\$2lQK5REWxaFyGz.p/dos3/', '65535', '65535', '', '', 'SiteAdmin', 'site', '1')") or die "Could not create the user 'siteadmin' in the vexim database!";
  print "The user 'siteadmin' has been added with the password 'CHANGE'\n";
  print "Please log in to the web interface and change this!\n";
} elsif ($dbtype == "pgsql") {
  print "Please create the PostgreSQL database with 'su - pgsql; cd ~pgsql; createdb -U <superusername> vexim'\n";
  print "Then press any key to continue..\n";
  $null = <STDIN>;
  $dbh = DBI->connect("DBI:Pg:dbname=vexim", "$dbrootuser", "$dbrootpass", {'RaiseError' => 1});
}
