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
      <a href="adminalias.php">Manage Aliases</a><br>
      <a href="adminaliasadd.php">Add Alias</a></br>
      <a href="admin.php">Main Menu</a><br>
      <br><a href="logout.php">Logout</a><br>
    </div>
    <div id="Forms">
    <form name="admincatchall" method="post" action="admincatchallsubmit.php">
      <table align="center">
	<tr>
	  <td>Alias Name:</td><td>Catchall</td>
	</tr>
	<tr>
	  <td>Forward email addressed to:</td><td>*@<? print $_COOKIE['vexim'][1];?></td>
	</tr>
	<tr>
	  <td>Forward the email to:</td><td><input name="smtp" type="text" class="textfield"></td>
	</tr>
	<tr>
	  <td colspan="2" class="button"><input name="submit" type="submit" value="Submit"></td>
	</tr>
      </table>
    </form>
    </div>
  </body>
</html>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
