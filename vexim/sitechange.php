<?
  include_once dirname(__FILE__) . "/config/variables.php";
  include_once dirname(__FILE__) . "/config/authsite.php";
?>
<html>
  <head>
    <title>Virtual Exim: Manage Domains</title>
    <link rel="stylesheet" href="style.css" type="text/css">
  </head>
  <body onLoad="document.domainchange.password.focus()">
    <? include dirname(__FILE__) . "/config/header.php"; ?>
    <div id="menu">
      <a href="site.php">Manage Domains</a><br>
      <a href="sitepassword.php" title="Change site password">Site Password</a><br>
      <br><a href="logout.php">Logout</a><br>
    </div>
    <div id="Forms">
      <table align="center">
	<tr><td colspan="2"><h4>Modify Domain Admin:</h4></td></tr>
	<form name="passwordchange" method="post" action="sitechangesubmit.php">
	  <tr><td>Admin:</td><td><select name="localpart" class="textfield">
	    <?
	      $query = "SELECT localpart,domain FROM users,domains
		WHERE domains.domain_id=" . $_GET['domain_id'] . "
		AND admin=1 AND users.domain_id=domains.domain_id";
	      $result = $db->query($query);
	      if ($result->numRows()) {
		while ($row = $result->fetchRow()) {
		  print '<option value="' . $row['localpart'] . '">' . $row['localpart'] . '</option>' . "\n\t";
		}
	      }
	    ?>
	    </select>@<? 
	    $query = "SELECT * FROM domains WHERE domain_id={$_GET['domain_id']}";
	    $result = $db->query($query);
	    if ($result->numRows()) { $row = $result->fetchRow(); }
	    print $row['domain']; ?></td>
	  <td><input name="domain_id" type="hidden" value="<? print $_GET['domain_id']; ?>"></td></tr>
	  <tr><td>Password:</td><td><input name="clear" size="25" type="password" class="textfield"></td></tr>
	  <tr><td>Verify Password:</td><td><input name="vclear" size="25" type="password" class="textfield"></td></tr>
	  <tr><td></td><td><input name="submit" size="25" type="submit" value="Submit Password"></td></tr>
	</form>
	<tr></tr><tr></tr>
	<tr><td colspan="2"><h4>Modify Domain Properties:</h4></td></tr>
	<form name="domainchange" method="post" action="sitechangesubmit.php">
	  <tr><td>System UID:</td><td><input type="text" size="5" name="uid" value="<? print $row['uid']; ?>" class="textfield"></td></tr>
	  <tr><td>System GID:</td><td><input type="text" size="5" name="gid" value="<? print $row['gid']; ?>" class="textfield"></td></tr>
	  <tr><td>Maximum accounts<br>(0 for unlimited):</td><td<input type="text" size="5" name="max_accounts" value="<? print $row['max_accounts']; ?>" class="textfield"></td></tr>
	  <tr><td>Max mailbox quota in Mb<br>(0 for disabled):</td>
	      <td><input type="text" size="5" name="quotas" value="<? print $row['quotas']; ?>" class="textfield"></td></tr>
	  <tr><td>Maximum message size:</td>
	      <td><input name="maxmsgsize" size="5" type="text" class="textfield" value="0">Kb</td></tr>
	  <tr><td>Spamassassin tag score:</td>
	      <td><input type="text" size="5" name="sa_tag" value="<? print $row['sa_tag']; ?>" class="textfield"></td></tr>
	  <tr><td>Spamassassin refuse score:</td>
	      <td><input type="text" size="5" name="sa_refuse" value="<? print $row['sa_refuse']; ?>" class="textfield"></td></tr>
	  <tr><td>Spamassassin:</td>
	      <td><input type="checkbox" name="spamassassin" <? if ($row['spamassassin'] == 1) {print "checked";} ?>></td></tr>
	  <tr><td>Anti-virus:</td><td><input type="checkbox" name="avscan" <? if ($row['avscan'] == 1) {print "checked";} ?>></td></tr>
	  <tr><td>Piping to command:</td>
	      <td><input type="checkbox" name="pipe" <? if ($row['pipe'] == 1) {print "checked";} ?>></td></tr>
	  <tr><td>Enabled:</td><td><input type="checkbox" name="enabled" <? if ($row['enabled'] == 1) {print "checked";} ?>></td>
	  <td><input name="domain_id" type="hidden" value="<? print $_GET['domain_id']; ?>"></td></tr>
	  <tr><td></td><td><input name="submit" size="25" type="submit" value="Submit Changes"></td></tr>
	</form>
	<form name="sadisable" method="post" action="sitechangesubmit.php">
	  <tr><td>Disable SpamAssassin for all domain users<br><b>(Warning: cannot be reversed!)</b>:</td>
	      <td><input name="sadisable" type="hidden" value="sadisable">
		  <input name="domain_id" type="hidden" value="<? print $_GET['domain_id']; ?>">
		  <input name="submit" type="submit" value="Disable"></td></tr>
	</form>
	<form name="avdisable" method="post" action="sitechangesubmit.php">
	  <tr><td>Disable Anti-Virus for all domain users<br><b>(Warning: cannot be reversed!)</b>:</td>
	      <td><input name="avdisable" type="hidden" value="avdisable">
		  <input name="domain_id" type="hidden" value="<? print $_GET['domain_id']; ?>">
		  <input name="submit" type="submit" value="Disable"></td></tr>
	</form>
      </table>
    </div>
  </body>
</html>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
