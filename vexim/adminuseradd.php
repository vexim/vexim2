<?
  include_once dirname(__FILE__) . "/config/variables.php";
  include_once dirname(__FILE__) . "/config/authpostmaster.php";
  include_once dirname(__FILE__) . "/config/httpheaders.php";

  $query = "SELECT (count(users.user_id) < domains.max_accounts)
  		OR (domains.max_accounts = 0)	AS allowed FROM
		users,domains WHERE users.domain_id=domains.domain_id
		AND domains.domain_id={$_SESSION['domain_id']}
		AND (users.type='local' OR users.type='piped') GROUP BY domains.max_accounts";
  $result = $db->query($query);
  if ($result->numRows()) { $row = $result->fetchRow(); }
  if (!$row[allowed]) {
	header ("Location: adminuser.php?maxaccounts=true");
  }

  $query = "SELECT * FROM domains WHERE domain_id={$_SESSION['domain_id']}";
  $result = $db->query($query);
  $row = $result->fetchRow();
?>
<html>
  <head>
    <title>Virtual Exim: <? echo _("Manage Users"); ?></title>
    <link rel="stylesheet" href="style.css" type="text/css">
  </head>
  <body onLoad="document.adminadd.realname.focus()">
    <? include dirname(__FILE__) . "/config/header.php"; ?>
    <div id="menu">
      <a href="adminuser.php"><? echo _("Manage Accounts"); ?></a><br>
      <a href="admin.php"><? echo _("Main Menu"); ?></a><br>
      <br><a href="logout.php"><? echo _("Logout"); ?></a><br>
    </div>
    <div id="forms">
    <form name="adminadd" method="post" action="adminuseraddsubmit.php">
      <table align="center">
	<tr><td><? echo _("Name"); ?>:</td><td><input type="textfield" size="25" name="realname" class="textfield"></td></tr>
	<tr><td><? echo _("Address"); ?>:</td><td><input type="textfield" size="25" name="localpart" class="textfield">@<? print $_SESSION['domain']; ?></td></tr>
	<tr><td><? echo _("Password"); ?>:</td><td><input type="password" size="25" name="clear" class="textfield"></td></tr>
	<tr><td><? echo _("Verify Password"); ?>:</td><td><input type="password" size="25" name="vclear" class="textfield"></td></tr>
	<? if ($postmasteruidgid == "yes") {
	  print "<tr><td>UID:</td><td><input type=\"textfield\" size=\"5\" name=\"uid\" class=\"textfield\" value=\"{$row['uid']}\"></td></tr>\n";
	  print "<tr><td>GID:</td><td><input type=\"textfield\" size=\"5\" name=\"gid\" class=\"textfield\" value=\"{$row['gid']}\"></td></tr>\n"; 
	}
	if ($row['quotas'] > "0") {
	  print "<tr><td>Mailbox quota ({$row['quotas']} Mb max):</td>";
	  print "<td><input type=\"text\" size=\"5\" name=\"quota\" value=\"{$row['quotas']}\" class=\"textfield\">Mb</td></tr>\n";
	} ?>
	<tr><td><? echo _("Has domain admin privileges?"); ?></td><td><input name="admin" type="checkbox"></td></tr>
	<? if ($row['pipe'] == "1") {
	     print "<tr><td>" . _("Pipe to command") . ":</td><td><input type=\"textfield\" size=\"25\" name=\"smtp\" class=\"textfield\"></td></tr>\n";
	     print "<tr><td colspan=\"2\" style=\"padding-bottom:1em\">" . _("Optional") . ": " . _("Pipe all mail to a command (e.g. procmail)") . ".<br>\n";
	     print _("Check box below to enable") . ":</td></tr>\n";
	     print "<tr><td>" . _("Enable piped command") . "?</td><td><input type=\"checkbox\" name=\"on_piped\"></td></tr>\n";
	   }
 	   if ($row['avscan'] == "1") {
	     print "<tr><td>" . _("Anti-Virus") . ":</td><td><input name=\"on_avscan\" type=\"checkbox\"></td></tr>\n";
	   }
	   if ($row['spamassassin'] == "1") {
	     print "<tr><td>" . _("Spamassassin") . ":</td><td><input name=\"on_spamassassin\" type=\"checkbox\"></td></tr>\n";
	     print "<tr><td>" . _("Spamassassin tag score") . ":</td>";
	     print "<td><input name=\"sa_tag\" size=\"5\" type=\"text\" class=\"textfield\" value=\"{$row['sa_tag']}\"></td>";
	     print "<td>" . _("The score at which to tag potential spam mail but still deliver") . "</td></tr>\n";
	     print "<tr><td>" . _("Spamassassin refuse score") . ":</td>";
	     print "<td><input name=\"sa_refuse\" size=\"5\" type=\"text\" class=\"textfield\" value=\"{$row['sa_refuse']}\"></td>";
	     print "<td>" . _("The score at which to refuse potential spam mail and not deliver") . "</td></tr>\n";
	   }
	     print "<tr><td>" . _("Maximum message size") . ":</td>";
	     print "<td><input name=\"maxmsgsize\" size=\"5\" type=\"text\" value=\"{$row['maxmsgsize']}\">Kb</td></tr>\n";
	?>
	<tr><td><? echo _("Enabled"); ?>:</td><td><input name="enabled" type="checkbox" checked></td></tr>
	<tr><td colspan="2" class="button"><input name="submit" type="submit" value="<? echo _("Submit"); ?>"></td>
      </table>
    </form>
  </body>
</html>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
