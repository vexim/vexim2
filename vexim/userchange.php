<?
  include_once dirname(__FILE__) . "/config/variables.php";
  include_once dirname(__FILE__) . "/config/authuser.php";

  $domquery = "SELECT avscan,spamassassin FROM domains WHERE domain_id='" .$_COOKIE[vexim][2]. "'";
  $domresult = $db->query($domquery);
  $domrow = $domresult->fetchRow();
  $query = "SELECT * FROM users WHERE localpart='" .$_COOKIE[vexim][0]. "' AND domain_id='" .$_COOKIE[vexim][2]. "'";
  $result = $db->query($query);
  $row = $result->fetchRow();
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
	if (isset($_GET[updated])) {
	  print "<div id='status'>Your update was sucessful.</div>\n";
	} else if (isset($_GET[failed])) {
	  print "<div id='status'>Your account could not be updated. Please see your administrator.</div>\n";
	} else if (isset($_GET[success])) {
	  print "<div id='status'>Your account has been succesfully updated.</div>\n";
	} else if (isset($_GET[failrealname])) {
	  print "<div id='status'>Your account could not be updated. Your Real Name was blank!</div>\n";
	} else if (isset($_GET[badpass])) {
	  print "<div id='status'>Your account password was not updated.<br>\n";
	  print "Your passwords were blank, did not match, or contained illegal characters: ' \" ` or ;</div>\n";
	}
      ?>
    <div id="forms">
      <form name="userchange" method="post" action="userchangesubmit.php">
        <table align="center">
          <tr><td>Name:</td><td><input name="realname" type="text" value="<? print $row[realname]; ?>" class="textfield"></td></tr>
          <tr><td>Email Address:</td><td><? print $row[localpart]."@".$_COOKIE[vexim][1]; ?></td>
          <tr><td>Password:</td><td><input name="clear" type="password" class="textfield"></td></tr>
          <tr><td>Verify Password:</td><td><input name="vclear" type="password" class="textfield"></td></tr>
	  <tr><td colspan="2">Your mailbox quota is currently: <? if ($row[quota] != "0") {
	  		  $row[quota] = $row[quota] . "Mb";
			} else {
			  $row[quota] = "Unlimited";} print $row[quota]; ?></td></tr>
	  <? if ($domrow[avscan] == "1") {
	       print "<tr><td>Anti-Virus:</td><td><input name=\"avscan\" type=\"checkbox\"";
	       if ($row[avscan] == "1") { print " checked "; } print "></td></tr>\n";
	     }
	     if ($domrow[spamassassin] == "1") {
	       print "<tr><td>Spamassassin:</td><td><input name=\"spamassassin\" type=\"checkbox\"";
	     if ($row[spamassassin] == "1") { print " checked "; } print "></td></tr>\n";
	       print "<tr><td>SA warn score:</td>";
	       print "<td><input type=\"text\" size=\"25\" name=\"sa_tag\" value=\"$row[sa_tag]\" class=\"textfield\"></td></tr>\n";
	       print "<tr><td>SA reject score:</td>";
	       print "<td><input type=\"text\" size=\"25\" name=\"sa_refuse\" value=\"$row[sa_refuse]\" class=\"textfield\"></td></tr>\n";
	     }
          ?>
          <tr><td class="button" colspan="2"><input name="submit" type="submit" value="Submit"></td></tr>
          <tr><td colspan="2" style="padding-top:1em;"><b>Note:</b> Attempting to set blank passwords does not work!<td></tr>
        </table>
      </form>
    </div>
  </body>
</html>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
