<?
  include_once dirname(__FILE__) . "/config/variables.php";
  include_once dirname(__FILE__) . "/config/authpostmaster.php";
?>
<html>
  <head>
    <title>Virtual Exim: Manage Users</title>
    <link rel="stylesheet" href="style.css" type="text/css">
  </head>
  <body>
    <? include dirname(__FILE__) . "/config/header.php"; ?>
    <div id="menu">
      <a href="adminfailadd.php">Add Fail</a><br>
      <a href="admin.php">Main Menu</a><br>
      <br><a href="logout.php">Logout</a><br>
    </div>
    <div id="Content">
    <table align="center">
      <tr><th>&nbsp;</th><th>Failed Address..</td></tr>
      <?
	$query = "SELECT localpart FROM users WHERE domain_id='$_COOKIE[vexim][2]' AND users.type='fail' ORDER BY localpart;";
	$result = $db->query($query);
	while ($row = $result->fetchRow()) {
	  print "<tr>";
	  print "<td align=\"center\"><a href=\"adminfaildelete.php?localpart=$row[localpart]\"><img style='border:0;width:10px;height:16px' src=\"images/trashcan.gif\" title=\"Delete fail $row[localpart]\"></a></td>\n";
	  print "<td><a href=\"adminfailchange.php?localpart=$row[localpart]\">$row[localpart]</a></td>\n";
	  print "</tr>\n";
	}
      ?>
    </table>
    </div>
    <? 
      include "status.php";
    ?>
  </body>
</html>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
