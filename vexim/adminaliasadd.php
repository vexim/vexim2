<?
  include_once dirname(__FILE__) . "/config/variables.php";
  include_once dirname(__FILE__) . "/config/authpostmaster.php";

  $query = "SELECT avscan,spamassassin FROM domains WHERE domain_id={$_COOKIE['vexim'][2]}";
  $result = $db->query($query);
  $row = $result->fetchRow();
?>
<html>
  <head>
    <title>Virtual Exim: Manage Users</title>
    <link rel="stylesheet" href="style.css" type="text/css">
  </head>
  <body onLoad="document.adminadd.realname.focus()">
    <? include dirname(__FILE__) . "/config/header.php"; ?>
    <div id="menu">
      <a href="adminalias.php">Manage Aliases</a><br>
      <a href="admin.php">Main Menu</a><br>
      <br><a href="logout.php">Logout</a><br>
    </div>
    <div id="Forms">
    <form name="adminadd" method="post" action="adminaliasaddsubmit.php">
      <table align="center">
        <tr><td>Alias Name:</td><td><input name="realname" type="text" class="textfield"></td></tr>
        <tr><td>Address:</td><td><input name="localpart" type="text" class="textfield">@<? print $_COOKIE['vexim'][1]; ?></td></tr>
	<tr><td colspan="2" style="padding-bottom:1em">Multiple addresses should be comma seperated, with no spaces</td></tr>
        <tr><td>Forward To:</td><td><input name="smtp" type="text" size="30" class="textfield"></td></tr>
        <tr><td>Password:</td><td><input name="clear" type="password" size="30" class="textfield"></td></tr>
	<tr><td colspan="2" style="padding-bottom:1em">(Password only needed if you want the user to
		be able to log in, or if the Alias is the admin account)</td></tr>
        <tr><td>Verify Password:</td><td><input name="vclear" type="password" size="30" class="textfield"></td></tr>
        <tr><td>Admin:</td><td><input name="admin" type="checkbox" class="textfield"></td></tr>
	<? if ($row['on_avscan'] == "1") {
             print "<tr><td>Anti-Virus:</td><td><input name=\"on_avscan\" type=\"checkbox\" class=\"textfield\"></td></tr>";
	   }
           if ($row['on_spamassassin'] == "1") {
	     print "<tr><td>Spamassassin:</td><td><input name=\"on_spamassassin\" type=\"checkbox\" class=\"textfield\"></td></tr>";
           }
	?>
        <tr><td>Enabled:</td><td><input name="enabled" type="checkbox" class="textfield" checked></td></tr>
	<tr><td colspan="2" class="button"><input name="submit" type="submit" value="Submit"></td></tr>
      </table>
    </form>
    </div>
  </body>
</html>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
