<?
	session_start();
	header("Cache-control: private");

	function ValidatePassword($password,$vpassword)
	{
		return ($password == $vpassword) 
		  && ("$password" != "") 
		  && ($password == preg_replace("/[\'\"\`\;]/","",$password));
	}

	if ($_POST[sitepasswd] != '') {
		$_SESSION[sitepasswd] = $_POST[sitepasswd];
		$_SESSION[dupsitepasswd] = $_POST[dupsitepasswd];
	}

	if (!ValidatePassword($_SESSION[sitepasswd],$_SESSION[dupsitepasswd])) {
		header("Location: setup2.php?error=password");
		die;
	}


	if ($_SESSION[sqltype] == "mysql") {
	    if (!@mysql_connect ($_SESSION[sqlserver], $_SESSION[sqluser], $_SESSION[sqlpasswd])) {
	    	header ("Location: setup2.php?error=Failed to log into the SQL server");
	    	die;
	    }
	    if (!mysql_select_db ($_SESSION[database])) {
	    	header ("Location: mkdb.php?error=Failed to select the db");
	    	die;
	    }
    } else { // this shouldn't happen
    	header("Location: setup1.php?error=sql");
    	die;
    }


	$query = sprintf("REPLACE INTO site (site_id,clear,crypt) VALUES(0, '%s','%s')", $_SESSION[sitepasswd], MD5($_SESSION[sitepasswd]));
	mysql_query($query);
?>
<html>
<head>
	<title>Virtual Exim</title>
	<link rel="stylesheet" href="style.css" type="text/css">
</head>
<body onLoad="document.login.sqlserver.focus()">
	<div id="Header"><a href='http://silverwraith.com/vexim/' target=_blank>VExim - Defaults</a></div>
	<div id="Centered">
	<form style="margin-top:3em;" name="login" method="post" action="final.php">
	<table valign="center" align="center">
		<tr><td>Catchall Name:<td><input name="catchall" type="text" class="textfield" value="Catchall">
		<tr><td>Crypt Scheme:<td><select name='crypt' class="textfield">
			<option value="md5">MD5</option>
			<option value="des">DES</option>
		</select>
		<tr><td>Empty Password:<td><input name="emptypasswd" type="text" class="textfield" value="NOPASSWORD">
		<tr><td>Mail Root:<td><input name="mailroot" type="text" class="textfield" value="/var/mail/">
		<tr><td>Mailman Root:<td><input name="mailmanroot" type="text" class="textfield" value="">
        <tr><td colspan="3" style="text-align:center;padding-top:1em"><input name="Next" type="submit" value="Next" class="longbutton"></td>
	</table>
	</form>
	</div>
</body>
</html>

<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
