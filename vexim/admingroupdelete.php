<?
  include_once dirname(__FILE__) . "/config/variables.php";
  include_once dirname(__FILE__) . "/config/authpostmaster.php";

  if ($_GET['confirm'] == "1") {
    # delete group member first
    $query = "delete from group_contents where group_id={$_GET['group_id']}";
    $result = $db->query($query);
    if (!DB::isError($result)) {
      # delete group
      $query = "DELETE FROM groups WHERE id={$_GET['group_id']}";
      $result = $db->query($query);
      if (!DB::isError($result)) {
        header ("Location: admingroup.php?group_deleted={$_GET['localpart']}");
        die;
      } else {
        header ("Location: admingroup.php?group_faildeleted={$_GET['localpart']}");
        die;
      }
    } else {
      header ("Location: admingroup.php?group_faildeleted={$_GET['localpart']}");
      die;
    }
  } else if ($_GET['confirm'] == "cancel") {
    header ("Location: admingroup.php?group_faildeleted={$_GET['localpart']}");
    die;
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
      <a href='admingroupadd.php'><? echo _("Add Group"); ?></a><br>
      <a href='admin.php'><? echo _("Main Menu"); ?></a><br>
      <br><a href='logout.php'><? echo _("Logout"); ?></a><br>
    </div>
    <div id='Content'>
      <form name='groupdelete' method='get' action='admingroupdelete.php'>
        <table align="center">
          <tr><td colspan='2'><? echo _("Please confirm deleting group"); ?> <?=$_GET['localpart']?>@<?=$_SESSION['domain']?>:</td></tr>
          <tr><td><input name='confirm' type='radio' value='cancel' checked><b> <? echo _("Do Not Delete"); ?>
 <? print $_GET['localpart']; ?>@<?=$_SESSION['domain']?></b></td></tr>
          <tr><td><input name='confirm' type='radio' value='1'><b> <? echo _("Delete"); ?> <? print $_GET['localpart']; ?>@<?=$_SESSION['domain']?></b></td></tr>
          <tr><td><input name='domain' type='hidden' value='<?=$_SESSION['domain']?>'>
              <input name='group_id' type='hidden' value='<?=$_GET['group_id']?>'>
              <input name='localpart' type='hidden' value='<?=$_GET['localpart']?>'>
              <input name='submit' type='submit' value='<? echo _("Continue"); ?>'></td></tr>
        </table>
      </form>
    </div>
  </body>
</html>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
