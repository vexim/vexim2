<?
  include_once dirname(__FILE__) . "/config/variables.php";
  include_once dirname(__FILE__) . "/config/authuser.php";

  $domquery = "SELECT avscan,spamassassin FROM domains WHERE domain_id={$_COOKIE['vexim'][2]}";
  $domresult = $db->query($domquery);
  $domrow = $domresult->fetchRow();
  $query = "SELECT * FROM users WHERE user_id={$_COOKIE['vexim'][4]}";
  $result = $db->query($query);
  $row = $result->fetchRow();
  $blockquery = "SELECT block_id,blockhdr,blockval FROM blocklists,users
  		WHERE blocklists.user_id={$_COOKIE['vexim'][4]}
		AND users.user_id=blocklists.user_id";
  $blockresult = $db->query($blockquery);
?>
<html>
  <head>
    <title>Virtual Exim: Manage Users</title>
    <link rel="stylesheet" href="style.css" type="text/css">
  </head>
  <body onLoad="document.userchange.realname.focus()">
    <? include dirname(__FILE__) . "/config/header.php"; ?>
    <div id="menu">
      <a href="logout.php">logout</a><br>
    </div>
      <?
	if (isset($_GET['updated'])) {
	  print "<div id='status'>Your update was sucessful.</div>\n";
	} else if (isset($_GET['failed'])) {
	  print "<div id='status'>Your account could not be updated. Please see your administrator.</div>\n";
	} else if (isset($_GET['success'])) {
	  print "<div id='status'>Your account has been succesfully updated.</div>\n";
	} else if (isset($_GET['failrealname'])) {
	  print "<div id='status'>Your account could not be updated. Your Real Name was blank!</div>\n";
	} else if (isset($_GET['badpass'])) {
	  print "<div id='status'>Your account password was not updated.<br>\n";
	  print "Your passwords were blank, did not match, or contained illegal characters: ' \" ` or ;<br>";
	  print "All other settings were updated.</div>\n";
	}
      ?>
    <div id="forms">
      <form name="userchange" method="post" action="userchangesubmit.php">
        <table align="center">
          <tr><td>Name:</td><td><input name="realname" type="text" value="<? print $row['realname']; ?>" class="textfield"></td></tr>
          <tr><td>Email Address:</td><td><? print $row['localpart']."@".$_COOKIE['vexim'][1]; ?></td>
          <tr><td>Password:</td><td><input name="clear" type="password" class="textfield"></td></tr>
          <tr><td>Verify Password:</td><td><input name="vclear" type="password" class="textfield"></td></tr>
          <tr><td></td><td class="button"><input name="submit" type="submit" value="Submit Password"></td></tr>
      </form>
      <form name="userchange" method="post" action="userchangesubmit.php">
	</table>
	<table align="center">
	  <tr><td colspan="2">Your mailbox quota is currently: <? if ($row['quota'] != "0") {
	  		  $row['quota'] = $row['quota'] . "Mb";
			} else {
			  $row['quota'] = "Unlimited";} print $row['quota']; ?></td></tr>
	  <? if ($domrow['avscan'] == "1") {
	       print "<tr><td>Anti-Virus:</td><td><input name=\"on_avscan\" type=\"checkbox\"";
	       if ($row['on_avscan'] == "1") { print " checked "; } print "></td></tr>\n";
	     }
	     if ($domrow['spamassassin'] == "1") {
	       print "<tr><td>Spamassassin:</td><td><input name=\"on_spamassassin\" type=\"checkbox\"";
	       if ($row['on_spamassassin'] == "1") { print " checked "; } print "></td></tr>\n";
	       print "<tr><td>SpamAssassin tag score:</td>";
	       print "<td><input type=\"text\" size=\"5\" name=\"sa_tag\" value=\"{$row['sa_tag']}\" class=\"textfield\"></td></tr>\n";
	       print "<tr><td>SpamAssassin refuse score:</td>";
	       print "<td><input type=\"text\" size=\"5\" name=\"sa_refuse\" value=\"{$row['sa_refuse']}\" class=\"textfield\"></td></tr>\n";
	     }
	     print "<tr><td>Maximum message size:</td>";
	     print "<td><input type=\"text\" size=\"5\" name=\"maxmsgsize\" value=\"{$row['maxmsgsize']}\" class=\"textfield\">Kb</td></tr>\n";
	     print "<tr><td>Vacation on:</td><td><input name=\"on_vacation\" type=\"checkbox\"";
	       if ($row['on_vacation'] == "1") { print " checked "; } print "></td></tr>\n";
 	     print "<tr><td>Vacation message:</td>";
	     print "<td><textarea name=\"vacation\" cols=\"40\" rows=\"5\" class=\"textfield\">{$row['vacation']}</textarea>";

	     print "<tr><td>Forwarding on:</td><td><input name=\"on_forward\" type=\"checkbox\"";
	       if ($row['on_forward'] == "1") { print " checked "; } print "></td></tr>\n";
 	     print "<tr><td>Forward mail to:</td>";
	     print "<td><input type=\"text\" name=\"forward\" value=\"{$row['forward']}\" class=\"textfield\"></td></tr>\n";
          ?>
          <tr><td></td><td class="button"><input name="submit" type="submit" value="Submit Profile"></td></tr>
          <tr><td colspan="2" style="padding-top:1em;"><b>Note:</b> Attempting to set blank passwords does not work!<td></tr>
        </table>
      </form>
      <form name="blocklist" method="post" action="userblocksubmit.php">
        <table align="center">
	  <tr><td>Add a new header blocking filter:</td></tr>
	  <tr><td><select name="blockhdr" class="textfield">
		  <option value="From">From:</option>
	  	  <option value="X-Mailer">X-Mailer:</option>
		  </select></td>
	      <td><input name="blockval" type="text" size="25" class="textfield">
	          <input name="color" type="hidden" value="black"></td></tr>
	  <tr><td><input name="submit" type="submit" value="Submit"></td></tr>
	</table>
      </form>
      <table align="center">
	<tr><th>Delete</th><th>Blocked Header</th><th>Content</th></tr>
	<? if (!DB::isError($blockresult)) { while ($blockrow = $blockresult->fetchRow()) {
		print "<tr><td><a href=\"userblocksubmit.php?action=delete&block_id={$blockrow['block_id']}\"><img style=\"border:0;width:10px;height:16px\" title=\"Delete\" src=\"images/trashcan.gif\" alt=\"trashcan\"></a></td>";
		print "<td>{$blockrow['blockhdr']}</td><td>{$blockrow['blockval']}</td></tr>\n";
	     }
	   }
	?>
      </table>
    </div>
  </body>
</html>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
