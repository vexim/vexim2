<html>
<head>
	<title>Virtual Exim</title>
	<link rel="stylesheet" href="style.css" type="text/css">
</head>
<body onLoad="document.login.sqlgoduser.focus()">
	<div id="Header"><a href='http://silverwraith.com/vexim/' target=_blank>VExim II - Create Database</a></div>
	<div id="Centered">
	<form style="margin-top:3em;" name="login" method="post" action="mkdb2.php">
	<table valign="center" align="center">
		<tr><td>SQL Superuser Name:<td><input name="sqlgoduser" type="text" class="textfield" value=<? print $_SESSION[sqlgoduser]; ?>>
		<tr><td>SQL Superuser Password:<td><input name="sqlgodpasswd" type="password" class="textfield" value=<? print $_SESSION[sqlgodpasswd]; ?>>
        <tr><td colspan="3" style="text-align:center;padding-top:1em"><input name="submit" type="submit" value="Next" class="longbutton"></td>
	</table>
	</form>
	</div>
<?
	switch ($_GET[error]) {
		case "connect":
			print "<div id='status'>Failed to connect to the database as superuser.</div>";
			break;
		case "create":
			print "<div id='status'>Failed to create to the database.  Bad user or password?</div>";
			break;
		default:
			print "<div id='status'>Unable to select the specified database ("  . $_SESSION[database] . "). To create it, enter the name and password of an account with CREATE and GRANT privileges.</div>";
			break;
	}
?>
</body>
</html>

<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
