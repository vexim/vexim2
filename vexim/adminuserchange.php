<?
  include_once dirname(__FILE__) . "/config/variables.php";
  include_once dirname(__FILE__) . "/config/authpostmaster.php";

  $query = "SELECT * FROM users WHERE localpart='" .$_GET[localpart]. "' AND domain_id='" .$_COOKIE[vexim][2]. "'";
  $result = $db->query($query);
  $row = $result->fetchRow();
  $domquery = "SELECT avscan,spamassassin,quotas FROM domains WHERE domain_id='" .$_COOKIE[vexim][2]. "'";
  $domresult = $db->query($domquery);
  $domrow = $domresult->fetchRow();
?>
<html>
  <head>
    <title>Virtual Exim: Manage Users</title>
    <link rel="stylesheet" href="style.css" type="text/css">
  </head>
  <body onLoad="document.userchange.realname.focus()">
  <? include dirname(__FILE__) . "/config/header.php"; ?>
    <div id="menu">
      <a href="adminuser.php">Manage Accounts</a><br>
      <a href="admin.php">Main Menu</a><br>
      <br><a href="logout.php">Logout</a><br>
    </div>
    <div id="forms">
    <table align="center">
      <form name="userchange" method="post" action="adminuserchangesubmit.php">
	<tr><td>Name:</td><td><input type="text" size="25" name="realname" value="<? print $row[realname]; ?>" class="textfield"></td></tr>
	<tr><td>Email Address:</td><td><? print $row[localpart]."@".$_COOKIE[vexim][1]; ?></td></tr>
	<input name="localpart" type="hidden" value="<? print $row[localpart]; ?>" class="textfield">
	<tr><td>Password:</td><td><input type="password" size="25" name="clear" class="textfield"></td></tr>
	<tr><td>Verify Password:</td><td><input type="password" size="25" name="vclear" class="textfield"></td></tr>
	<? if ($postmasteruidgid == "yes") {
	  print "<tr><td>UID:</td><td><input type=\"textfield\" size=\"25\" name=\"uid\" class=\"textfield\" value=\"$row[uid]\"></td></tr>\n";
	  print "<tr><td>GID:</td><td><input type=\"textfield\" size=\"25\" name=\"gid\" class=\"textfield\" value=\"$row[gid]\"></td></tr>\n"; 
	  print "<tr><td colspan=\"2\" style=\"padding-bottom:1em\">When you update the UID or GID, please make sure your
		 MTA still has permission to create the required user directories!</td></tr>\n";
	  }
	  if ($domrow[quotas] > "0") {
	    print "<tr><td>Mailbox quota ($domrow[quotas] Mb max):</td>";
	    print "<td><input type=\"text\" size=\"5\" name=\"quota\" value=\"$row[quota]\" class=\"textfield\">Mb</td></tr>\n";
	} ?>
	<tr><td>Admin:</td><td><input name="admin" type="checkbox" <?
	   if ($row[admin] == 1) { print "checked"; } ?> class="textfield"></td></tr>
	<? if ($domrow[avscan] == "1") {
	     print "<tr><td>Anti-Virus:</td><td><input name=\"on_avscan\" type=\"checkbox\"";
	     if ($row[on_avscan] == "1") { print " checked "; } print "></td></tr>\n";
	   }
	   if ($domrow[spamassassin] == "1") {
	     print "<tr><td>Spamassassin:</td><td><input name=\"on_spamassassin\" type=\"checkbox\"";
	     if ($row[on_spamassassin] == "1") { print " checked "; } print "></td></tr>\n";
	     print "<tr><td>SA refuse score:</td>";
	     print "<td><input type=\"text\" size=\"25\" name=\"sa_refuse\" value=\"$row[sa_refuse]\" class=\"textfield\"></td></tr>\n";
	   }
	?>
	<tr><td>Enabled:</td><td><input name="enabled" type="checkbox" <?
		if ($row[enabled] == 1) { print "checked"; } ?> class="textfield"></td></tr>
	<tr><td>Vacation on:</td><td><input name="on_vacation" type="checkbox" <?
		if ($row[on_vacation] == "1") { print " checked "; } ?> ></td></tr>
	<tr><td>Vacation message:</td>
	<td><textarea name="vacation" cols="40" rows="5" class="textfield"><? print $row[vacation]; ?></textarea>
	<tr><td>Forwarding on:</td><td><input name="on_forward" type="checkbox" <?
		if ($row[on_forward] == "1") { print " checked "; } ?> ></td></tr>
	<tr><td>Forward mail to:</td>
	<td><input type="text" name="forward" value="<? print $row[forward]; ?>" class="textfield"></td></tr>
	<tr><td colspan="2" class="button"><input name="submit" type="submit" value="Submit"></td></tr>
	<tr><td colspan="2" style="padding-top:1em">Aliases to this account:<br>
	<?
	  # Print the aliases associated with this account
	  $query = "SELECT localpart,domain,realname FROM users,domains
			WHERE smtp='".$_GET[localpart]."@".$_COOKIE[vexim][1]."'
			AND users.domain_id=domains.domain_id ORDER BY realname";
	  $result = $db->query($query);
	  while ($row = $result->fetchRow()) {
	    if ($row[domain] == $_COOKIE[vexim][1]) {
	      print " <a href='adminaliaschange.php?localpart=" . $row[localpart] . "'>" . $row[localpart]."@".$row[domain]. "</a> ";
	    } else {
	      print $row[localpart]."@".$row[domain];
	    }
	    if ($row[realname] == "Catchall") {
	      print " - " . $row[realname];
	    }
	    print "<br>\n";
	  }
	?>
	</td></tr>
      </form>
    </table>
    </div>
  </body>
</html>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
