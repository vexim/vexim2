<?
  include_once dirname(__FILE__) . "/config/variables.php";
  include_once dirname(__FILE__) . "/config/authsite.php";
?>
<html>
  <head>
    <title>Virtual Exim: <? echo _("Manage Domains"); ?></title>
    <link rel="stylesheet" href="style.css" type="text/css">
  </head>
  <body onLoad="document.siteadd.domain.focus()">
  <? include dirname(__FILE__) . "/config/header.php"; ?>
  <div id='menu'>
      <a href="site.php"><? echo _("Manage Domains"); ?></a><br>
      <a href='sitepassword.php' title='Change site password'><? echo _("Site Password"); ?></a><br>
      <br><a href="logout.php"><? echo _("Logout"); ?></a><br>
  </div>
  <div id='Forms'>
    <form name="siteadd" method="post" action="siteaddsubmit.php">
      <table align="center">
	<tr><td><? echo _("Domain"); ?>:</td><td><input name="domain" type="text" class="textfield"></td>
	    <td><? echo _("The name of the new domain you are adding"); ?></td></tr>
	<? if ($_GET['type'] == "local") {
	   print "
	     <tr><td>" . _("Domain Admin") . ":</td><td><input name=\"localpart\" type=\"text\" value=\"postmaster\" class=\"textfield\"></td>
		 <td>" . _("The username of the domain's administrator account") . "</td></tr>
	     <tr><td>" . _("Password") . ":</td><td><input name=\"clear\" type=\"password\" class=\"textfield\"></td></tr>
	     <tr><td>" . _("Verify Password") . ":</td><td><input name=\"vclear\" type=\"password\" class=\"textfield\"></td></tr>
	     <tr><td>" . _("System UID") . ":</td><td><input name=\"uid\" type=\"text\" class=\"textfield\" value=\"$uid\"></td></tr>
	     <tr><td>" . _("System GID") . ":</td><td><input name=\"gid\" type=\"text\" class=\"textfield\" value=\"$gid\"></td></tr>
	     <tr><td>" . _("Domain Mail directory") . ":</td>
		 <td><input name=\"maildir\" type=\"text\" class=\"textfield\" value=\"$mailroot\"></td>
		 <td>" . _("Create the domain directory below this top-level mailstore") . "</td></tr>
	     <tr><td>" . _("Maximum accounts<br>(0 for unlimited)") . ":</td><td<input type=\"text\" size=\"5\" name=\"max_accounts\" value=\"0\" class=\"textfield\"></td></tr>
	     <tr><td>" . _("Max mailbox quota (0 for disabled)") . ":</td>
		 <td><input name=\"quotas\" size=\"5\" type=\"text\" class=\"textfield\" value=\"0\"></td></tr>
	     <tr><td>" . _("Maximum message size") . ":</td>
		 <td><input name=\"maxmsgsize\" size=\"5\" type=\"text\" class=\"textfield\" value=\"0\">Kb</td>
		 <td>" . _("The maximum size for incoming mail (user tunable)") . "</td></tr>
	     <tr><td>" . _("Spamassassin tag score") . ":</td>
		 <td><input name=\"sa_tag\" size=\"5\" type=\"text\" class=\"textfield\" value=\"$sa_tag\"></td>
		 <td>" . _("The score at the 'X-Spam-Flag: YES' header will be added") . "</td></tr>
	     <tr><td>" . _("Spamassassin refuse score") . ":</td>
		 <td><input name=\"sa_refuse\" size=\"5\" type=\"text\" class=\"textfield\" value=\"$sa_refuse\"></td>
		 <td>" . _("The score at which to refuse potentially spam mail and not deliver") . "</td></tr>
	     <tr><td>" . _("Spamassassin enabled") . "?</td><td><input name=\"spamassassin\" type=\"checkbox\" class=\"textfield\"></td></tr>
	     <tr><td>" . _("Anti Virus enabled") . "?</td><td><input name=\"avscan\" type=\"checkbox\" class=\"textfield\"></td></tr>
	     <tr><td>" . _("Enable piping mail to command") . "?</td><td><input name=\"pipe\" type=\"checkbox\" class=\"textfield\"></td></tr>
	     <tr><td>" . _("Domain enabled") . "?</td><td><input name=\"enabled\" type=\"checkbox\" class=\"textfield\" checked></td></tr>
	     <tr><td></td>";
	   }
	?>
	    <td><input name="type" type="hidden" value="<? print $_GET['type']; ?>">
		<input name="admin" type="hidden" value="1">
		<input name="submit" type="submit" value="<? echo _("Submit"); ?>"></td>
	</tr>
      </table>
    </form>
    </div>
  </body>
</html>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
