<?
  include_once dirname(__FILE__) . "/config/variables.php";
  include_once dirname(__FILE__) . "/config/authsite.php";
?>
<html>
  <head>
    <title>Virtual Exim: <? echo _("Manage Sites"); ?></title>
    <link rel="stylesheet" href="style.css" type="text/css">
  </head>
  <body onLoad="document.sitepassword.password.focus()">
  <? include dirname(__FILE__) . "/config/header.php"; ?>
   <div id="menu">
      <a href="site.php"><? echo _("Manage Domains"); ?></a><br>
      <a href="sitepassword.php" title="Change site password"><? echo _("Site Password"); ?></a><br>
      <br><a href="logout.php"><? echo _("Logout"); ?></a><br>
  </div>
  <div id="forms">
      <form name="sitepassword" method="post" action="sitepasswordsubmit.php">
	<table align="center">
		<tr><td colspan="2" style="padding-bottom:1em"><? echo _("Change SiteAdmin Password"); ?>:</td></tr>
		<tr><td><? echo _("Password"); ?>:</td><td><input type="password" size="25" name="clear"></td></tr>
		<tr><td><? echo _("Verify Password"); ?>:</td><td><input type="password" size="25" name="vclear"></td></tr>
		<tr><td id="button" colspan="2"><input name="submit" type="submit" value="<? echo _("Submit"); ?>"></td></tr>
	</table>
      </form>
	<b><? echo _("WARNING") . ":</b> " . _("Changing your password will require you to re-login."); ?>
    </div>
  </body>
</html>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
