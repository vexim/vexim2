<?
  include_once dirname(__FILE__) . "/config/variables.php";
  include_once dirname(__FILE__) . "/config/authpostmaster.php";

if ($_GET[confirm] == "1") {

  $admincheck_query = "SELECT COUNT(localpart) AS count FROM users
  			WHERE admin='1'
			AND localpart='$_GET[localpart]'
			AND domain_id='" . $_COOKIE[vexim][2] . "'";
  $admincheck_result = $db->query($admincheck_query);
  $admincheckrow = $admincheck_result->fetchRow();

  $count_query = "SELECT COUNT(localpart) AS count FROM users
  			WHERE admin='1'
			AND domain_id='" . $_COOKIE[vexim][2] . "'";
  $count_result = $db->query($count_query);
  $countrow = $count_result->fetchRow();

  if ($admincheckrow[count] == "1") {
    if ($countrow[count] == "1") {
      header ("Location: adminuser.php?nodel=$_GET[localpart]");
    } else {
      $query = "DELETE FROM users WHERE localpart='$_GET[localpart]' AND domain_id='" . $_COOKIE[vexim][2] . "'";
      $result = $db->query($query);
      if (!DB::isError($result)) { header ("Location: adminuser.php?deleted=$_GET[localpart]"); }
      else { header ("Location: adminuser.php?faildeleted=$_GET[localpart]"); }
    }
  } else {
    $query = "DELETE FROM users WHERE localpart='$_GET[localpart]' AND domain_id='" . $_COOKIE[vexim][2] . "'";
    $result = $db->query($query);
    if (!DB::isError($result)) { header ("Location: adminuser.php?deleted=$_GET[localpart]"); }
    else { header ("Location: adminuser.php?faildeleted=$_GET[localpart]"); }
  }
  
} else if ($_GET[confirm] == "cancel") {                   
    header ("Location: adminuser.php?faildeleted=$_GET[localpart]");
    die;                                                      
}                                                           

?>
<html>
  <head>
    <title>Domain Plus!: Confirm Delete</title>
    <link rel="stylesheet" href="style.css" type="text/css">
  </head>
    <body>
    <? include dirname(__FILE__) . "/config/header.php"; ?>
    <div id='menu'>
      <a href='adminuseradd.php'>Add User</a><br>
      <a href='admin.php' title='Change site password'>Main Menu</a><br>
      <br><a href='logout.php'>Logout</a><br>
    </div>
    <div id='Content'>
      <form name='userdelete' method='get' action='adminuserdelete.php'>
        <table align="center">
	  <tr><td colspan='2'>Please confirm deleting user <?=$_GET[localpart]?>@<?=$_COOKIE[vexim][1]?>:</td></tr>
	  <tr><td><input name='confirm' type='radio' value='cancel' checked><b> Do Not Delete <? print $_GET[localpart]; ?>@<?=$_COOKIE[vexim][1]?></b></td></tr>
	  <tr><td><input name='confirm' type='radio' value='1'><b> Delete <? print $_GET[localpart]; ?>@<?=$_COOKIE[vexim][1]?></b></td></tr>
	  <tr><td><input name='domain' type='hidden' value='<?=$_COOKIE[vexim][1]?>'>
	      <input name='domain_id' type='hidden' value='<?=$_COOKIE[vexim][2]?>'>
	      <input name='localpart' type='hidden' value='<?=$_GET[localpart]?>'>
	      <input name='submit' type='submit' value='Continue'></td></tr>
	</table>
      </form>
    </div>
  </body>
</html>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
