<?php
  include_once dirname(__FILE__) . "/config/variables.php";
  include_once dirname(__FILE__) . "/config/authsite.php";
  include_once dirname(__FILE__) . "/config/functions.php";
  include_once dirname(__FILE__) . "/config/httpheaders.php";
?>
<html>
  <head>
    <title><?php echo _("Virtual Exim") . ": " . _("Manage Sites"); ?></title>
    <link rel="stylesheet" href="style.css" type="text/css">
  </head>
  <body onLoad="document.sitepassword.password.focus()">
  <?php include dirname(__FILE__) . "/config/header.php"; ?>
   <div id="menu">
      <a href="site.php"><?php echo _("Manage Domains"); ?></a><br>
      <a href="sitepassword.php"><?php echo _("Site Password"); ?></a><br>
      <br><a href="logout.php"><?php echo _("Logout"); ?></a><br>
  </div>
  <div id="forms">
      <form name="sitepassword" method="post" action="sitepasswordsubmit.php">
	<table align="center">
		<tr><td colspan="2" style="padding-bottom:1em"><?php echo _("Change SiteAdmin Password"); ?>:</td></tr>
		<tr><td><?php echo _("Password"); ?>:</td><td><input type="password" size="25" name="clear"></td></tr>
		<tr><td><?php echo _("Verify Password"); ?>:</td><td><input type="password" size="25" name="vclear"></td></tr>
		<tr><td id="button" colspan="2"><input name="submit" type="submit" value="<?php echo _("Submit"); ?>"></td></tr>
	</table>
      </form>
    </div>
  </body>
</html>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
