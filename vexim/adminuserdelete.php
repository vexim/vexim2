<?
  include_once dirname(__FILE__) . "/config/variables.php";
  include_once dirname(__FILE__) . "/config/authpostmaster.php";

if ($_GET['confirm'] == "1") {

  $query = "DELETE FROM users WHERE user_id={$_GET['user_id']}";
  $result = $db->query($query);
  if (!DB::isError($result)) { header ("Location: adminuser.php?deleted={$_GET['localpart']}"); }
  else { header ("Location: adminuser.php?faildeleted={$_GET['localpart']}"); }
} else if ($_GET['confirm'] == "cancel") {		   
    header ("Location: adminuser.php?faildeleted={$_GET['localpart']}");
    die;						      
} else {
  $query = "SELECT user_id AS count FROM users 
	      WHERE admin=1 AND domain_id={$_COOKIE[vexim][2]}
	      AND user_id!={$_GET['user_id']}";
  $result = $db->query($query);
  if ($result->numRows() == 0) {
    header ("Location: adminuser.php?lastadmin={$_GET['localpart']}");
    die;
  }
  $query = "SELECT localpart FROM users WHERE user_id={$_GET['user_id']}";
  $result = $db->query($query);
  if ($result->numRows()) { $row = $result->fetchRow(); }
}
?>
<html>
  <head>
    <title>Virtual Exim: <? echo _("Confirm Delete"); ?></title>
    <link rel="stylesheet" href="style.css" type="text/css">
  </head>
    <body>
    <? include dirname(__FILE__) . "/config/header.php"; ?>
    <div id='menu'>
      <a href='adminuseradd.php'><? echo _("Add User"); ?></a><br>
      <a href='admin.php' title='Change site password'><? echo _("Main Menu"); ?></a><br>
      <br><a href='logout.php'><? echo _("Logout"); ?></a><br>
    </div>
    <div id='Content'>
      <form name='userdelete' method='get' action='adminuserdelete.php'>
	<table align="center">
	  <tr><td colspan='2'><? echo _("Please confirm deleting user"); ?> <?=$row['localpart']?>@<?=$_COOKIE['vexim'][1]?>:</td></tr>
	  <tr><td><input name='confirm' type='radio' value='cancel' checked><b> <? echo _("Do Not Delete"); ?> <? print $row['localpart']; ?>@<?=$_COOKIE['vexim'][1]?></b></td></tr>
	  <tr><td><input name='confirm' type='radio' value='1'><b> <? echo _("Delete"); ?> <? print $row['localpart']; ?>@<?=$_COOKIE['vexim'][1]?></b></td></tr>
	  <tr><td><input name='domain' type='hidden' value='<?=$_COOKIE['vexim'][1]?>'>
	      <input name='user_id' type='hidden' value='<?=$_GET['user_id']?>'>
	      <input name='localpart' type='hidden' value='<?=$_GET['localpart']?>'>
	      <input name='submit' type='submit' value='<? echo _("Continue"); ?>'></td></tr>
	</table>
      </form>
    </div>
  </body>
</html>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
