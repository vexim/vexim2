<?
  include_once dirname(__FILE__) . "/config/variables.php";
  include_once dirname(__FILE__) . "/config/authpostmaster.php";
  include_once dirname(__FILE__) . "/config/httpheaders.php";
?>
<html>
  <head>
    <title>Virtual Exim: <? echo _("Manage Users"); ?></title>
    <link rel="stylesheet" href="style.css" type="text/css">
  </head>
  <body>
    <? include dirname(__FILE__) . "/config/header.php"; ?>
    <div id="menu">
      <a href="adminfailadd.php"><? echo _("Add Fail"); ?></a><br>
      <a href="admin.php"><? echo _("Main Menu"); ?></a><br>
      <br><a href="logout.php"><? echo _("Logout"); ?></a><br>
    </div>
    <div id="Content">
    <table align="center">
      <tr><th>&nbsp;</th><th><? echo _("Failed Address"); ?>..</td></tr>
      <?
	$query = "SELECT user_id,localpart FROM users WHERE domain_id='{$_SESSION['domain_id']}' AND users.type='fail' ORDER BY localpart;";
	$result = $db->query($query);
	if ($result->numRows()) {
	  while ($row = $result->fetchRow()) {
	    print "<tr>";
	    print "<td align=\"center\"><a href=\"adminfaildelete.php?user_id={$row['user_id']}\">";
	    print "<img style='border:0;width:10px;height:16px' src=\"images/trashcan.gif\" title=\"" . _("Delete fail") . " {$row['localpart']}\"></a></td>\n";
	    print "<td><a href=\"adminfailchange.php?user_id={$row['user_id']}\">{$row['localpart']}@{$_SESSION['domain']}</a></td>\n";
	    print "</tr>\n";
	  }
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
