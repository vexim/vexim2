<?
  include_once dirname(__FILE__) . "/config/variables.php";
  include_once dirname(__FILE__) . "/config/authpostmaster.php";
  include_once dirname(__FILE__) . "/config/httpheaders.php";

  $query = "SELECT localpart FROM users WHERE user_id={$_GET['user_id']}";
  $result = $db->query($query);
  if ($result->numRows()) { $row = $result->fetchRow(); }
?>
<html>
  <head>
    <title>Virtual Exim: <? echo _("Manage Users"); ?></title>
    <link rel="stylesheet" href="style.css" type="text/css">
  </head>
  <body onLoad="document.failchange.localpart.focus()">
    <? include dirname(__FILE__) . "/config/header.php"; ?>
    <div id="menu">
      <a href="adminfail.php"><? echo _("Manage Fails"); ?></a><br>
      <a href="adminfailadd.php"><? echo _("Add Fail"); ?></a><br>
      <a href="admin.php"><? echo _("Main Menu"); ?></a><br>
      <br><a href="logout.php"><? echo _("Logout"); ?></a><br>
    </div>
    <div="Forms">
      <form name="failchange" method="post" action="adminfailchangesubmit.php">
	<table align="center">
	  <tr><td><? echo _("Fail address"); ?>:</td>
	      <td><input name="localpart" type="text" value="<? print $row['localpart']; ?>" class="textfield">@<? print $_SESSION['domain']; ?></td>
	      <td><input name="user_id" type="hidden" value="<? print $_GET['user_id']; ?>" class="textfield"></td></tr>
	  <tr><td></td><td><input name="submit" type="submit" value="<? echo _("Submit"); ?>"></td></tr>
	</table>
      </form>
    </div>
  </body>
</html>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
