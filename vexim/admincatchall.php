<?
  include_once dirname(__FILE__) . "/config/variables.php";
  include_once dirname(__FILE__) . "/config/authpostmaster.php";
  include_once dirname(__FILE__) . "/config/httpheaders.php";
  $query = "SELECT smtp FROM users WHERE user_id={$_GET['user_id']}";
  $result = $db->query($query);
  if ($result->numRows()) { $row = $result->fetchRow(); }
?>
<html>
  <head>
    <title>Virtual Exim: <? echo _("Manage Users"); ?></title>
    <link rel="stylesheet" href="style.css" type="text/css">
  </head>
  <body>
  <? include dirname(__FILE__) . "/config/header.php"; ?>
  <div id="menu">
    <a href="admincatchallall.php"><? echo _("Manage Aliases"); ?></a><br>
    <a href="admin.php"><? echo _("Main Menu"); ?></a><br>
    <br><a href="logout.php"><? echo _("Logout"); ?></a><br>
  </div>
  </div id="Forms">
    <form name="admincatchall" method="post" action="admincatchallsubmit.php">
      <table align="center">
	<tr>
	  <td><? echo _("Alias Name") . ":</td><td>" . _("Catchall") . "</td>\n"; ?>
	</tr>
	<tr>
	  <td><? echo _("Forward email addressed to") . ":</td><td>*@" . $_SESSION['domain'];?></td>
	</tr>
	<tr>
	  <td><? echo _("Forward the email to") . ':</td><td><input name="smtp" type="text" value="' . $row['smtp']; ?>" class="textfield"></td>
	</tr>
	<tr>
	  <td><input name="user_id" type="hidden" value="<? print $_GET['user_id']; ?>" class="textfield"></td>
	  <td><input name="submit" type="submit" value="<? echo _("Submit"); ?>"></td>
	</tr>
      </table>
    </form>
  </body>
  </div>
</html>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
