<?
  include_once dirname(__FILE__) . "/config/variables.php";
  include_once dirname(__FILE__) . "/config/authpostmaster.php";
?>
<html>
  <head>
    <title>Virtual Exim: Manage Users</title>
    <link rel="stylesheet" href="style.css" type="text/css">
  </head>
  <body onLoad="document.adminadd.username.focus()">
    <? include dirname(__FILE__) . "/config/header.php"; ?>
    <div id="menu">
       <a href="adminfail.php">Manage Fails</a><br>
       <a href="admin.php">Main Menu</a><br>
       <br><a href="logout.php">Logout</a><br>
    </div>
    <div id="Forms">
    <form name="adminadd" method="post" action="adminfailaddsubmit.php">
      <table align="center">
        <tr>
	  <td>Address to fail:</td><td><input name="localpart" type="text" class="textfield">@<? print $_COOKIE['vexim'][1]; ?></td>
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
