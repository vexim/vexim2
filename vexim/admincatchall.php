<?
  include_once dirname(__FILE__) . "/config/variables.php";
  include_once dirname(__FILE__) . "/config/authpostmaster.php";
?>
<?
  $query = "SELECT smtp FROM users WHERE user_id={$_GET['user_id']}";
  $result = $db->query($query);
  $row = $result->fetchRow();
?>
<html>
  <head>
    <title>Virtual Exim: Manage Users</title>
    <link rel="stylesheet" href="style.css" type="text/css">
  </head>
  <body>
  <? include dirname(__FILE__) . "/config/header.php"; ?>
  <div id="menu">
    <a href="admincatchallall.php">Manage Aliases</a><br>
    <a href="admin.php">Main Menu</a><br>
    <br><a href="logout.php">Logout</a><br>
  </div>
  </div id="Forms">
    <form name="admincatchall" method="post" action="admincatchallsubmit.php">
      <table align="center">
        <tr>
	  <td>Alias Name:</td><td>Catchall</td>
	</tr>
        <tr>
	  <td>Forward email addressed to:</td><td>*@<? print $_COOKIE['vexim'][1];?></td>
	</tr>
        <tr>
	  <td>Forward the email to:</td><td><input name="smtp" type="text" value="<? print $row['smtp']; ?>" class="textfield"></td>
	</tr>
	<tr>
	  <td><input name="user_id" type="hidden" value="<? print $_GET['user_id']; ?>" class="textfield"></td>
	  <td><input name="submit" type="submit" value="Submit"></td>
	</tr>
      </table>
    </form>
  </body>
  </div>
</html>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
