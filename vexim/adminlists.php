<?
  include_once dirname(__FILE__) . "/config/variables.php";
  if (isset($_POST['listname'])) {
    header ("Location: $mailmanroot/admin/{$_POST['listname']}");
  }
?>
<html>
  <head>
    <title>Virtual Exim: Mailing List Administration</title>
    <link rel="stylesheet" href="style.css" type="text/css">
  </head>
  <body>
    <? include dirname(__FILE__) . "/config/header.php"; ?>
    <div id="menu">
      <a href="<? print "$mailmanroot/create"; ?>">Add a list</a><br>
      <a href="admin.php">Main Menu</a><br>
      <br><a href="logout.php">Logout</a><br>
    </div>
    <div id="Forms">
      <form name="adminlists" method="post" action="adminlists.php">
	<table align="center">
	  <tr><td>Please enter the name of the list to admin:</td></tr>
	  <tr><td><input name="listname" type="text" class="textfield"></td></tr>
	  <tr><td class="button"><input name="submit" type="submit" value="submit"></td></tr>
	</table>
      </form>
    </div>
  </body>
</html>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
