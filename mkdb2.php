<?
	session_start();
	header("Cache-control: private");	

	$_SESSION[sqlgoduser] = $_POST[sqlgoduser];
	$_SESSION[sqlgodpasswd] = $_POST[sqlgodpasswd];
	// this is here in case the person hits the BACK button
	if (@mysql_connect ($_SESSION[sqlserver], $_SESSION[sqluser], $_SESSION[sqlpasswd]) AND mysql_select_db ($_SESSION[database])) {
		header ("Location: setup2.php");
		die;
	}
	if (!@mysql_connect ($_SESSION[sqlserver], $_SESSION[sqlgoduser], $_SESSION[sqlgodpasswd])) {
		header ("Location: mkdb.php?error=connect");
		die;
	}
    if (!mysql_create_db($_SESSION[database])) {
		header ("Location: mkdb.php?error=create");
		die;
	}
 	mysql_select_db ($_SESSION[database]) or header ("Location: mkdb.php?error=connect");

	$handle = fopen ("mysql.sql", "r");
	$query = '';

	// I need to add some decent error handling in here
	while (!feof ($handle)) {
    	$line = trim(fgets($handle, 4096));
		if ($line[strlen($line)-1] == ';') {
			$line[strlen($line)-1] = ' ';
			$query .= ' ' . $line;
			$query = trim($query);
			if ("$query" != "") {
				mysql_query($query) or die("Failure while creating tables: " . mysql_error());
			}
			$query = '';
		} else {
			$query .= ' ' . $line;
		}
	}
	fclose ($handle);

	$query = sprintf("GRANT SELECT,INSERT,DELETE,UPDATE ON %s.* TO %s IDENTIFIED BY '%s'",$_SESSION[database],$_SESSION[sqluser],$_SESSION[sqlpasswd]);
	mysql_query($query) or die("Failed while GRANTing: " . mysql_error());
	$query = sprintf("GRANT SELECT,INSERT,DELETE,UPDATE ON %s.* TO %s@localhost IDENTIFIED BY '%s'",$_SESSION[database],$_SESSION[sqluser],$_SESSION[sqlpasswd]);
	mysql_query($query) or die("Failed while GRANTing: " . mysql_error());
	mysql_query("flush privileges");
	header ("Location: setup2.php");
?>
