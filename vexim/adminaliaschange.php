<?
  include_once dirname(__FILE__) . "/config/variables.php";
  include_once dirname(__FILE__) . "/config/authpostmaster.php";
?>
<?
  $query = "SELECT localpart,realname,smtp,on_avscan,on_spamassassin,admin,enabled FROM users WHERE user_id={$_GET['user_id']}";
  $result = $db->query($query);
  if ($result->numRows()) { $row = $result->fetchRow(); }
?>
<html>
  <head>
    <title>Virtual Exim: Manage Users</title>
    <link rel="stylesheet" href="style.css" type="text/css">
  </head>
  <body onLoad="document.aliaschange.realname.focus()">
    <? include dirname(__FILE__) . "/config/header.php"; ?>
    <div id="menu">
      <a href="adminalias.php"><? echo _("Manage Aliases"); ?></a><br>
      <a href="adminaliasadd.php"><? echo _("Add Alias"); ?></a></br>
      <a href="admin.php"><? echo _("Main Menu"); ?></a><br>
      <br><a href="logout.php"><? echo _("Logout"); ?></a><br>
    </div>
    <div id="Forms">
    <form name="aliaschange" method="post" action="adminaliaschangesubmit.php">
      <table align="center">
	<tr><td><? echo _("Alias Name"); ?>:</td><td><input name="realname" type="text" value="<? print $row['realname']; ?>"class="textfield"></td></tr>
	<tr><td><? echo _("Address"); ?>:</td><td><input name="localpart" type="text" value="<? print $row['localpart']; ?>"class="textfield">@<? print $_COOKIE['vexim'][1]; ?></td></tr>
	<tr><td><input name="user_id" type="hidden" value="<? print $_GET['user_id']; ?>" class="textfield"></td></tr>
	<tr><td colspan="2" style="padding-bottom:1em"><? echo _("Multiple addresses should be comma separated, with no spaces"); ?></td></tr>
	<tr><td><? echo _("Forward To"); ?>:</td><td><input name="target" type="text" size="30"value="<? print $row['smtp']; ?>" class="textfield"></td></tr>
	<tr><td><? echo _("Password"); ?>:</td><td><input name="clear" type="password" size="30" class="textfield"></td></tr>
	<tr><td colspan="2" style="padding-bottom:1em">
		(<? echo _("Password only needed if you want the user to be able to log in, or if the Alias is the admin account"); ?>)</td></tr>
	<tr><td><? echo _("Verify Password"); ?>:</td><td><input name="clear" type="password" size="30" class="textfield"></td></tr>
	<tr><td><? echo _("Admin"); ?>:</td><td><input name="admin" type="checkbox" <? if ($row['admin'] == 1)
		{ print "checked"; } ?> class="textfield"></td></tr>
	<tr><td><? echo _("Anti-Virus"); ?>:</td><td><input name="on_avscan" type="checkbox" <? if ($row['on_avscan'] == 1)
		{ print "checked"; } ?> class="textfield"></td></tr>
	<tr><td><? echo _("Spamassassin"); ?>:</td><td><input name="on_spamassassin" type="checkbox" <? if ($row['on_spamassassin'] == 1)
		{ print "checked"; } ?> class="textfield"></td></tr>
	<tr><td><? echo _("Enabled") ?>:</td><td><input name="enabled" type="checkbox" <? if ($row['enabled'] == 1)
		{ print "checked"; } ?> class="textfield"></td></tr>
	<tr><td colspan="2" class="button"><input name="submit" type="submit" value="Submit"></td></tr>
      </table>
    </form>
    </div>
  </body>
</html>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
