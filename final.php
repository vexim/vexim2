<?
	session_start();
	header("Cache-control: private");

	$handle = fopen('../settings/variables.php','w+');
	fwrite($handle,"<?\n");
	fwrite($handle,"\$SqlServer = '" . $_SESSION[sqlserver] . "';\n");
	fwrite($handle, "\$SqlUser = '" . $_SESSION[sqluser] . "';\n");
	fwrite($handle, "\$SqlPassword = '" . $_SESSION[sqlpasswd] . "';\n");
	fwrite($handle, "\$db = '" . $_SESSION[database] . "';\n");
	fwrite($handle, "\$sqltype = '" . $_SESSION[sqltype] . "';\n");
	fwrite($handle, "\$catchallrealname = '" . $_POST[catchall] . "';\n");
	fwrite($handle, "\$cryptscheme = '" . $_POST[crypt] . "';\n");
	fwrite($handle, "\$emptypass = '" . $_POST[emptypasswd] . "';\n");
	fwrite($handle, "\$mailroot = '" . $_POST[mailroot] . "';\n");
	fwrite($handle, "\$mailmanroot = '" . $_POST[mailmanroot] . "';\n");

  	fwrite($handle, "\$welcome_message = \"Welcome, \$_POST[realname] !\\n\\n\"\n");
  	fwrite($handle, "\t\t. \"Your new E-mail account is all ready for you.\\n\\n\"\n");
  	fwrite($handle, "\t\t. \"Here are some settings you might find useful:\\n\\n\"\n");
  	fwrite($handle, "\t\t. \"Username: \$_POST[username]@\" . \$_COOKIE[vexim][2] .\"\\n\"\n");
  	fwrite($handle, "\t\t. \"POP3 server: mail.\" . \$_COOKIE[vexim][2] . \"\\n\"\n");
  	fwrite($handle, "\t\t. \"SMTP server: mail.\" . \$_COOKIE[vexim][2] . \"\\n\";\n");
  	fwrite($handle,"?>\n");
	fclose($handle);
	
	// Kill the session information
	$_SESSION = array();
	session_destroy();
?>
<html>
<head>
	<title>Virtual Exim</title>
	<link rel="stylesheet" href="style.css" type="text/css">
</head>
<body>
	<div id="Header"><a href='http://silverwraith.com/vexim/' target=_blank>VExim II - Finished</a></div>
	<div id="Centered">
	VExim is now configured.  However, you must configure Exim and your POP/IMAP software before it will be useful.<br><br>
	You can now <a href='../index.php'>login</a> as the Site Administrator and create your virtual domains.<br><br>
	For security reasons you should delete the VExim setup directory (<? print dirname(__FILE__); ?>).
	</div>
</body>
</html>

<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
