<?
	session_start();
	header("Cache-control: private");	
?>
<html>
<head>
	<title>Virtual Exim II</title>
	<link rel="stylesheet" href="style.css" type="text/css">
</head>
<body onLoad="document.login.sqlserver.focus()">
	<div id="Header"><a href='http://silverwraith.com/vexim/' target=_blank>VExim II - Setup</a></div>
	<div id="Centered">
	<form style="margin-top:3em;" name="login" method="post" action="setup2.php">
	<table valign="center" align="center">
		<tr><td>SQL Server:<td><input name="sqlserver" type="text" class="textfield" value=<? print $_SESSION[sqlserver]; ?>>
		<tr><td>Database Name:<td><input name="database" type="text" class="textfield" value=<? print $_SESSION[database]; ?>>
		<tr><td>SQL User:<td><input name="sqluser" type="text" class="textfield" value=<? print $_SESSION[sqluser]; ?>>
		<tr><td>SQL Password:<td><input name="sqlpasswd" type="password" class="textfield" value=<? print $_SESSION[sqlpasswd] ?>>
		<tr><td>Verify Password:<td><input name="vsqlpasswd" type="password" class="textfield" value=<? print $_SESSION[vsqlpasswd] ?>>
		<tr><td>Database Type:<td><select name='sqltype' class="textfield">
<?
			$mysqlselected = "";
			$pgselected = "";
			$ldapselected = "";
			switch ($_SESSION[sqltype]) {
				case "mysql":
					$mysqlselected = "selected";
					break;
				case "pgsql":
					$pgselected = "selected";
					break;
				case "ldap":
					$ldapselected = "selected";
					break;
				default:
					$mysqlselected = "selected";
					break;
			}
			print "\t\t\t<option $mysqlselected value='mysql'>MySQL</option>\n";
			print "\t\t\t<option $pgselected value='pgsql'>PostgreSQL</option>\n";
			print "\t\t\t<option $ldapselected value='ldap'>LDAP</option>\n";
?>
		</select>
		<tr><td colspan="3" style="text-align:center;padding-top:1em"><input name="submit" type="submit" value="Next" class="longbutton"></td>
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
		case "sql":
			print "\t<div id='status'>" . $_SESSION[sqltype] . " is not currently supported.</div>\n";
			break;
		case "unknown":	// this should never happen
			print "\t<div id='status'>Unknown error.</div>\n";
			break;
	}
?>
</body>
</html>

<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
