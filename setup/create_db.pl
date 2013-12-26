#!/usr/bin/perl


# create_db.pl - History

# - Original script by Avleen Vig
# - Updated to reflect recent .sql files and increase data security by Marko Luft (luft@webnova.de)
#   More flexibility on dbname and users, no fixed password anymore - uses crypt for password
# - 30.Nov.2004 - Updated to reflect recent .sql files by Andreas Rust (rust@webnova.de)



# TODO:
# fix the postgres part - commented out currently to avoid problems - better only use for MySQL currently

#
# USE THIS SCRIPT!
# IF you upgrade from a vexim1.x installation, USE THIS SCRIPT to avoid quite some hazzles.
#

use strict;
use DBI();
use Getopt::Long;

my ($dbh, $mydbh, $pgdbh);
my ($act, $dbtype, $uid, $gid, $mailstore);
my ($superuser, $superpass);
my ($veximpass, $veximpassconfirm);

my $databasename ="";

my $databasenameold= "";

my $dropoldtables = "";
my $addcommentfield = "";
my $addsu = '';
my $zeiger_auf_hauptarray;
my $zeiger_auf_spalten_array;
my $zeiger_auf_spalten;
my $commentcheck;
my $unseencheck;
my $DBuserfields;
my $DBdomainfields;
my $veximdatabaseuser;
my $siteadminpassconfirm;
my $siteadminpass;


&GetOptions(	"act=s" =>\$act,
		"dbtype=s" =>\$dbtype,
		"uid=i" =>\$uid,
		"gid=i" =>\$gid,
		"mailstore=s" =>\$mailstore);
usage() unless defined $act;
usage() unless defined $dbtype;
$uid  = "90" unless defined $uid;
$gid = "90" unless defined $gid;
$mailstore = "/usr/local/mail" unless defined $mailstore;



#####################################
# If something goes a way we don't  #
# expect it to, bomb with the usage #
#####################################



sub usage {



	if($dbtype  && $dbtype eq "pgsql")
	{
		print "\nERROR:\t Sorry no postgrsql support, I have comment the whole pgsql stuff.\n\t So if u want to use pgsql FIXME (please)\n\n";
	exit;
	}
	print "\n";
	print "Usage: create_db.pl --act=<action> --dbtype=<dbtype> --uid=<uid> --gid=<gid> --mailstore=<dir>\n";
	print "\n";
	print "--act \t\tnewdb, migratemysql, (migratepostgresql|FIXME!)\n";
	print "--dbtypes\tmysql, (pgsql|FIXME!)\n";
	print "--uid\t\tdefault UID's for domains (default uid is 90)\n";
	print "--gid\t\tdefault GID's for domains (default gid is 90)\n";
	print "--mailstore\tmailstore is the directory under which the\n\t\tmaildirs for domains are created\n\t\t(defaults to /usr/local/mail)\n\n";
	print "Examples: create_db.pl --act=newdb --dbtype=mysql --uid=90 --gid=90 --mailstore=/usr/local/mail\n";
	print "\t  (will create a new  database for a new install with mysql)\n";
	print "\t  create_db.pl --act=migratemysql --dbtype=mysql --uid=90 --gid=90 --mailstore=/usr/local/mail\n";
	print "\t  (will migrate database from a vexim 1.x mysql database, to a vexim 2.x mysql database)\n";
	print "\n\n";  
	exit 1;
}




#####################################
# Collect the username and password #
# for the database root user	@
#####################################

print "Using dbtype $dbtype\n";
print "Please enter the username of the $dbtype superuser: "; chomp($superuser = <STDIN>);
`stty -echo`;
print "Please enter the password of the $dbtype superuser: "; chomp($superpass = <STDIN>);
`stty echo`;

#####################################
# Collect the old and new database  #
# names
#####################################

while($databasename eq $databasenameold)
{
	if(($databasename eq $databasenameold) && length($databasename) > "0"   )
	{ 
		print "\nPLEASE USE DIFFERENT NAME\n";
	}
	print "\nPlease enter the name of your NEW database: "; chomp($databasename = <STDIN>);
	if($act eq "migratemysql" || $act eq "migratepostgresql" ) 
	{
	print "Please enter the name of your OLD database: "; chomp($databasenameold = <STDIN>);
	}
}
print "--------------------------------------------------\n";
print "Database: ". $databasename." will be created\n";
if($act eq "migratemysql" || $act eq "migratepostgresql" ) 
{
print "Database: ". $databasenameold." will be used as data source\n";		
}
print "--------------------------------------------------\n";
print "Is this correct? (Y = continue / anykey = exit ): "; chomp(my $submit = <STDIN>);
$submit = uc($submit);

if($submit ne "Y")
{

exit;
}


#####################################
# This sub collects and verifies    #
# the username for the 'vexim' user #
# in the database		   @
#####################################
sub veximpw {
	if($act ne "migratemysql" && $act ne "migratepostgresql" )
	{	
	print "\nPlease enter a name for the database user who gets access to $databasename: ";
	chomp($veximdatabaseuser= <STDIN>);
	 `stty -echo`;
  	print "\nPlease enter a password for the '$veximdatabaseuser' database user: ";
  	chomp($veximpass = <STDIN>);
  	print "\nConfirm password: ";
	  chomp($veximpassconfirm = <STDIN>);
	while ($veximpass ne $veximpassconfirm) {
	    print "\nPassword mismatch. Please enter a password for the '$veximdatabaseuser' $dbtype database user: ";
	    chomp($veximpass = <STDIN>);
	    print "\nConfirm password: ";
	    chomp($veximpassconfirm = <STDIN>);
	}
  	`stty echo`;
	}
}


#####################################
# This sub creates the MySQL db     #
#####################################

sub create_mysqldb {
   	 $mydbh->do("CREATE DATABASE $databasename  DEFAULT CHARACTER SET utf8") or die "Could not create the database '$databasename' in MySQL!";
}


#####################################
# Here we can create MySQL tables   @
#####################################

sub create_mysqltables {
   $mydbh->do("CREATE TABLE IF NOT EXISTS $databasename.domains (domain_id mediumint(8) unsigned NOT NULL auto_increment,
	domain varchar(64) NOT NULL default '',
	maildir varchar(128) NOT NULL default '',
	uid smallint(5) unsigned NOT NULL default '$uid',
	gid smallint(5) unsigned NOT NULL default '$gid',
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
	UNIQUE KEY domain (domain))") or die "Could not create table domains in the vexim database!";
  print "\nCreated domains table\n";
   
   
  $mydbh->do("CREATE TABLE IF NOT EXISTS $databasename.users (user_id int(10) unsigned NOT NULL auto_increment,
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
	unseen bool default '0',
	maxmsgsize mediumint(8) unsigned NOT NULL default '0',
	quota int(10) unsigned NOT NULL default '0',
	realname varchar(255) default NULL,
	sa_tag smallint(5) unsigned NOT NULL default '0',
	sa_refuse smallint(5) unsigned NOT NULL default '0',
	tagline varchar(255) default NULL,
	vacation varchar(1024) default NULL,
	PRIMARY KEY (user_id),
	UNIQUE KEY username (localpart,domain_id),
	KEY local (localpart))") or die "Could not create table users in the vexim database!";
  print "Created users table\n";
 
 $mydbh->do("CREATE TABLE IF NOT EXISTS $databasename.blocklists (block_id int(10) unsigned NOT NULL auto_increment,
  	domain_id mediumint(8) unsigned NOT NULL,
	user_id int(10) unsigned default NULL,
	blockhdr varchar(192) NOT NULL default '',
	blockval varchar(192) NOT NULL default '',
	color varchar(8) NOT NULL default '',
	PRIMARY KEY (block_id))") or die "Could not create table blocklists in the vexim database!";
  print "Created blocklists table\n";
   
  $mydbh->do("CREATE TABLE IF NOT EXISTS $databasename.domainalias (domain_id mediumint(8) unsigned NOT NULL,
  	alias varchar(64))") or die "Could not create table domainalias in the vexim database!";
  print "Created domainalias table\n";
  
  $mydbh->do("CREATE TABLE IF NOT EXISTS $databasename.group_contents (group_id int(10) NOT NULL,
  	member_id int(10) NOT NULL,
	PRIMARY KEY (group_id, member_id))") or die "Could not create table group_contents in the vexim database!";
  print "Created group_contents table\n";
  
  $mydbh->do("CREATE TABLE IF NOT EXISTS $databasename.groups (id int(10) auto_increment,
	domain_id mediumint(8) unsigned NOT NULL,
	name varchar(64) NOT NULL,
	is_public char(1) NOT NULL default 'Y',
	enabled bool NOT NULL default '1',
	PRIMARY KEY (id),
	UNIQUE KEY group_name(domain_id, name))") or die "Could not create table groups in the vexim database!";
  print "Created groups table\n";
  
}

#################################################
# This sub SHOULD create the pgsql table...     #
#################################################

sub create_pgsqldb {
	print "Sorry, the postgres part of this script is currently unmaintained.\nTo progress use the pgsql.sql file.\n\n";
	exit 0;
#    $pgdbh->do("CREATE DATABASE $databasename WITH ENCODING 'UTF8'") or die "Could not create the database '$databasename' in PostgreSQL!";

}


#####################################
# This is the equivilent code for   #
# generating PostgreSQL tables. The #
# Code is certainly not the same    #
# due to slight difference in the   #
# SQL implementation.	            #
#####################################

sub create_postgrestables {
#print "\nCreating new PostgreSQL tables...\n";
#$pgdbh->do("CREATE TABLE domains (domain_id SERIAL PRIMARY KEY,
#	  domain varchar(64) UNIQUE NOT NULL,
#	  maildir varchar(128) NOT NULL default '',
#	  uid int NOT NULL default '65534' CHECK(uid BETWEEN 1 AND 65535),
#	  gid int NOT NULL default '65534' CHECK(uid BETWEEN 1 AND 65535),
#	  max_accounts int default NULL CHECK(max_accounts > -1),
#	  type varchar(5) NOT NULL,
#	  avscan smallint NOT NULL default '0',
#	  blocklists smallint NOT NULL default '0',
#	  complexpass smallint NOT NULL default '0',
#	  enabled smallint NOT NULL default '1',
#	  mailinglists smallint NOT NULL default '0',
#	  pipe smallint NOT NULL default '0',
#	  spamassassin smallint NOT NULL default '0',
#	  quotas int NOT NULL default '0' CHECK(quotas > -1),
#	  maxmsgsize int NOT NULL default '0' CHECK(maxmsgsize > -1),
#	  sa_tag int NOT NULL default '0' CHECK(sa_tag > -1),
#	  sa_refuse int NOT NULL default '0' CHECK(sa_refuse > -1))") or die "Could not create table domains";
#print "\nCreated domains table\n";
#$pgdbh->do("CREATE TABLE users (user_id SERIAL PRIMARY KEY,
#	  domain_id int NOT NULL,
#	  localpart varchar(192) NOT NULL,
#	  username varchar(255) NOT NULL,
#	  clear varchar(255) default NULL,
#	  crypt varchar(48) default NULL,
#	  uid int NOT NULL default '65534' CHECK(uid BETWEEN 1 AND 65535),
#	  gid int NOT NULL default '65534' CHECK(uid BETWEEN 1 AND 65535),
#	  smtp varchar(255) default NULL,
#	  pop varchar(255) default NULL,
#	  type varchar(8) CHECK(type in ('local','alias','catch', 'fail', 'piped', 'admin', 'site')) NOT NULL,
#	  admin smallint NOT NULL default '0',
#	  on_avscan smallint NOT NULL default '0',
#	  on_blocklist smallint NOT NULL default '0',
#	  on_complexpass smallint NOT NULL default '0',
#	  on_forward smallint NOT NULL default '0',
#	  on_piped smallint NOT NULL default '0',
#	  on_spamassassin smallint NOT NULL default '0',
#	  on_vacation smallint NOT NULL default '0',
#	  enabled smallint NOT NULL default '1',
#	  flags varchar(16) default NULL,
#	  forward varchar(255) default NULL,
#	  maxmsgsize int NOT NULL default '0' CHECK(maxmsgsize > -1),
#	  quota int NOT NULL default '0',
#	  realname varchar(255) default NULL,
#	  sa_tag smallint NOT NULL default '0',
#	  sa_refuse smallint NOT NULL default '0',
#	  tagline varchar(255) default NULL,
#	  vacation varchar(1024) default NULL,
#	  UNIQUE (localpart,domain_id))") or die "Could not create table users";
# print "\nCreated users table\n";
#  $pgdbh->do("CREATE TABLE blocklists (block_id SERIAL PRIMARY KEY,
#	domain_id int NOT NULL,
#	user_id int NOT NULL,
#	blockhdr varchar(192) NOT NULL default '',
#	blockval varchar(192) NOT NULL default '',
#	color varchar(8))") or die "Could not create table blocklists in the vexim database!";
#  print "\nCreated blocklists table\n";
# $pgdbh->do("CREATE TABLE IF NOT EXISTS domainalias (domain_id int NOT NULL,
#	alias varchar(64))") or die "Could not create table domainalias in the vexim database!";
#	print "Created domainalias table\n";
}


#####################################
# This adds the 'vexim' database    #
# user, using the passwords	 #
# collected at the start of the     #
# script.			   #
#####################################

sub add_mysqlveximuser {
	
  	if($act eq "newdb")
	{	#xxx
	   print "Adding vexim database user...\n";
	 	#display the entry dialog
	  veximpw();
	  $mydbh->do("GRANT SELECT,INSERT,DELETE,UPDATE ON $databasename.* to $veximdatabaseuser\@localhost IDENTIFIED BY '$veximpass'")
	    or die "Could not create the user '$veximdatabaseuser' in the MySQL database!";
	  $mydbh->do("FLUSH PRIVILEGES") or die "Could not flush privileges!";
	}
}

#####################################
# Same for PostgreSQL	            #
#####################################

sub add_postgresveximuser {
 #print "Adding vexim database user...\n";
 # veximpw() unless $act eq "migratepostgresql";
 # $pgdbh->do("CREATE USER vexim WITH PASSWORD '$veximpass' NOCREATEDB NOCREATEUSER")
 #   or die "Could not create the user 'vexim' in the MySQL database!";
 # $pgdbh->do("GRANT SELECT,INSERT,DELETE,UPDATE ON domains,users to vexim")
 #   or die "Could not create the user 'vexim' in the MySQL database!";
}


#####################################
# A special 'siteadmin' user has to #
# be added seperately from the rest #
#####################################

sub add_siteadminuser {
 
 	if($act eq  "newdb")
 	{
	  if ($dbtype eq "mysql") { $dbh = $mydbh; } 
	#elsif ($dbtype == "pgsql") { $dbh = $pgdbh; }
	
	print "\nPlease enter a password for the 'siteadmin' user: ";
	`stty -echo`;
	chomp($siteadminpass = <STDIN>);
  	print "\nConfirm password: ";
	  chomp($siteadminpassconfirm = <STDIN>);
	while ($siteadminpassconfirm ne $siteadminpass) {
	print "\nPassword mismatch. Please enter a password for the 'siteadmin' user: ";
	chomp($siteadminpass = <STDIN>);
	print "\nConfirm password: ";
	chomp($siteadminpassconfirm = <STDIN>);
	
	}
	`stty echo`;


	  my $crypted = crypt("$siteadminpassconfirm", "\$1\$xx") ;
	  $dbh->do("INSERT INTO $databasename.domains (domain_id, domain) VALUES ('1', 'admin')");
	  $dbh->do("INSERT INTO $databasename.users (domain_id, localpart, username, clear, crypt, uid, gid, smtp, pop, realname, type, admin)
	  		VALUES ('1',
			'siteadmin',
			'siteadmin',
			'$siteadminpassconfirm',
			'$crypted',
			'65535',
			'65535',
			'',
			'',
			'SiteAdmin',
			'site',
			'1')") or die "Could not create the user 'siteadmin' in the vexim database!";
	  print "\nThe user 'siteadmin' has been added with the password \n";
	} 
	  
}
 



#####################################
# If the user is migrating from     #
# vexim 1.3 to 2.0, this migration  #
# code is called to preserve data   #
#####################################

sub migratemysql {
	my $sql = "show columns from $databasenameold.users";
	my $sth = $mydbh->prepare( $sql );
	$sth->execute();
	$zeiger_auf_hauptarray=$sth->fetchall_arrayref;
	$sth->finish;
	foreach $zeiger_auf_spalten_array(@$zeiger_auf_hauptarray)
	{
		foreach   $zeiger_auf_spalten(@$zeiger_auf_spalten_array)
		{
		        if($zeiger_auf_spalten && $zeiger_auf_spalten eq "comment") 
	        	{ 
	        	$commentcheck = "yes";
			}
			
			if($zeiger_auf_spalten && $zeiger_auf_spalten eq "unseen") 
	        	{ 
	        	$unseencheck = "yes";
			}
	        }
	}
	if($commentcheck  && $commentcheck ne "yes")
	{ 
		$commentcheck = "nope";
	}
	if($unseencheck  && $unseencheck ne "yes")
	{ 
		$unseencheck = "nope";
	}

$DBuserfields = "user_id,domain_id,localpart,username,clear,crypt,uid,gid,smtp,pop,type,admin,on_avscan,on_blocklist,on_complexpass,on_forward,on_piped,on_spamassassin,on_vacation,enabled,flags,forward,maxmsgsize,quota,realname,sa_tag,sa_refuse,tagline,vacation";

	if(($commentcheck  && $commentcheck eq "yes"))
	{
	$DBuserfields .= ",comment";
	}
	
	if(($unseencheck  && $unseencheck ne "yes"))	
	{
	$DBuserfields .= ",unseen";
	}
print "Starting migration ... \n";	
my $qryx = "INSERT INTO $databasename.users ($DBuserfields)  SELECT $DBuserfields FROM $databasenameold.users";
$mydbh->do($qryx); 
my $qry = "INSERT INTO $databasename.domains SELECT * FROM $databasenameold.domains";
$mydbh->do($qry); 
print "migration complete!\n";


}

#####################################
# The following sub is merely an    #
# extension of the previous sub, if #
# the migration is to PostgreSQL    #
#####################################

sub migratepostgresql() {
  #print "Exporting user data from MySQL...\n";
  #$mydbh->do("select * INTO OUTFILE '/tmp/vexim-mysql-migrate-users'
  #		FIELDS TERMINATED BY ','
#		OPTIONALLY ENCLOSED BY \"'\"
#		LINES TERMINATED BY \"\n\" from $databasename.users");
#  print "Exporting domains data from MySQL...\n";
#  $mydbh->do("select * INTO OUTFILE '/tmp/vexim-mysql-migrate-domains'
#  		FIELDS TERMINATED BY ','
#		OPTIONALLY ENCLOSED BY \"'\"
#		LINES TERMINATED BY \"\n\" from $databasename.domains");
#
#  print "Importing user data into PostgreSQL...\n";
#  open IN, "</tmp/vexim-mysql-migrate-users" or die $!;
#  while(<IN>)
#  {
#    s/\\N/NULL/g;
#    s/^\d+,/nextval\('public.users_user_id_seq'::text\),/g;
#    $pgdbh->do("INSERT INTO users VALUES ($_)");
#  }
#  close IN;
#
#  print "Importing domain data into PostgreSQL...\n";
#  open IN, "</tmp/vexim-mysql-migrate-domains" or die $!;
#  while(<IN>)
#  {
#    s/\\N/'local'/g;
#    s/^\d+,/nextval\('public.domains_domain_id_seq'::text\),/g;
#    $pgdbh->do("INSERT INTO domains VALUES ($_)");
#  }
#  close IN;

#  print "Migration complete!\n";
#  print "Please delete /tmp/vexim-mysql-migrate-users and /tmp/vexim-mysql-migrate-domains\n\n";
}


###########################################################
# The actual call to the subs go below here. This comment #
# if nothing else, provides a nice buffer between the     #
# rather ugly subs, and the one ring that bind them...    #
###########################################################

if ($dbtype eq "mysql") {
  
  $mydbh = DBI->connect("DBI:mysql:database=mysql;host=localhost", "$superuser", "$superpass", {'RaiseError' => 1});
  $mydbh->do("set character set utf8");
  $mydbh->do("set names utf8");
  create_mysqldb();
  create_mysqltables();
  sleep 1; 
  add_mysqlveximuser();
  add_siteadminuser();
  print "Database created successfully!\n\n";
} 
elsif ($dbtype eq "pgsql") 
{
  ##print "Please create the PostgreSQL database with 'su - pgsql; createdb vexim'\n";
  ##print "Then press any key to continue..\n\n";
  ##my $null = <STDIN>;
  #$pgdbh = DBI->connect("DBI:Pg:dbname=template0", "$superuser", "$superpass", {'RaiseError' => 1}) or die $DBI::errstr;
  #XXX
  #create_postgrestables();
  #sleep 1;
  #add_postgresveximuser();
  #add_siteadminuser();
  #print "Database created successfully!\n";
}

###########################################################
# If the user asks to migrate data from an old database,  #
# carry on!			                          #
###########################################################

if ($act eq "migratemysql") {
  migratemysql();
} 
elsif ($act eq "migratepostgresql") 
{
  #print "Please create the PostgreSQL database with 'su - pgsql; createdb $databasename'\n";
  #print "Then press any key to continue..\n";
  #my $null = <STDIN>;
  #print "Please enter the username of the postgresql superuser: "; chomp(my $pgsuperuser = <STDIN>);
  #`stty -echo`;
  #print "Please enter the password of the postgresql superuser: "; chomp(my $pgsuperpass = <STDIN>);
  #`stty echo`;
  #$pgdbh = DBI->connect("DBI:Pg:dbname=$databasename", "$pgsuperuser", "$pgsuperpass", {'RaiseError' => 1});
  #create_postgrestables();
  #sleep 1;
  #add_postgresveximuser();
  #print "Database created successfully!\n";
  #migratemysql();
  #migratepostgresql();
}

