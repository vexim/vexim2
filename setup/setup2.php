<?
	session_start();
	header("Cache-control: private");

	function ValidatePassword($password,$vpassword)
	{
		return ($password == $vpassword) 
		  && ("$password" != "") 
		  && ($password == preg_replace("/[\'\"\`\;]/","",$password));
	}

	if ($_POST[sqlserver] != '') {
		$_SESSION[sqlserver] = $_POST[sqlserver];
		$_SESSION[sqluser] = $_POST[sqluser];
		$_SESSION[sqlpasswd] = $_POST[sqlpasswd];
		$_SESSION[vsqlpasswd] = $_POST[vsqlpasswd];
		$_SESSION[database] = $_POST[database];
		$_SESSION[sqltype] = $_POST[sqltype];
	}

	if (!ValidatePassword($_SESSION[sqlpasswd],$_SESSION[vsqlpasswd])) {
		header("Location: setup1.php?error=password");
		die;
	}

	switch ($_SESSION[sqltype]) {
		case 'mysql':
		    if (@mysql_connect ($_SESSION[sqlserver], $_SESSION[sqluser], $_SESSION[sqlpasswd])) {
		    	if (!mysql_select_db ($_SESSION[database]))  {
		    		header ("Location: mkdb.php");
		    		die;
		    	}
		    } else {
		    	header ("Location: mkdb.php");
		    	die;
		    }
			break;
		case 'pgsql':
	    	header ("Location: setup1.php?error=sql");
	    	die;
	    	break;
		case 'ldap':
    		header ("Location: setup1.php?error=sql");
    		die;
			break;
		default:	// this should never happen!
    		header ("Location: index.php?error=unknown");
    		die;
			break;
	}
?>
<html>
<head>
	<title>Virtual Exim</title>
	<link rel="stylesheet" href="style.css" type="text/css">
</head>
<body onLoad="document.login.sqlserver.focus()">
	<div id="Header"><a href='http://silverwraith.com/vexim/' target=_blank>VExim II - Site Admin Password</a></div>
	<div id="Centered">
	<form style="margin-top:3em;" name="login" method="post" action="setup3.php">
	<table valign="center" align="center">
		<tr><td>Site Admin Password:<td><input name="sitepasswd" type="password" class="textfield">
		<tr><td>Verify Site Admin Password:<td><input name="dupsitepasswd" type="password" class="textfield">
        <tr><td colspan="3" style="text-align:center;padding-top:1em"><input name="Next" type="submit" value="Next" class="longbutton"></td>
	</table>
	</form>
	</div>
<?
	switch ($_GET[error]) {
		case "password":
			print "\t<div id='status'>Passwords did not match or were blank.</div>\n";
			break;
		case "connect":
			print "\t<div id='status'>Failed to connect to the SQL server.<br>Check your settings for accuracy.</div>\n";
			break;
		case "unknown":	// this should never happen
			print "\t<div id='status'>Unknown error.</div>\n";
			break;
	}
?>

</body>
</html>

<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
