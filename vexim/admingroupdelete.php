<?php
  include_once dirname(__FILE__) . '/config/variables.php';
  include_once dirname(__FILE__) . '/config/authpostmaster.php';
  include_once dirname(__FILE__) . "/config/functions.php";

  if(array_key_exists('confirm', $_GET)) {
    if ($_GET['confirm'] == '1') {
      # confirm that the user is deleting a group they are permitted to change before going further
	  $query = "SELECT * FROM groups WHERE id=:group_id AND domain_id=:domain_id";
      $sth = $dbh->prepare($query);
      $sth->execute(array(':group_id'=>$_GET['group_id'], ':domain_id'=>$_SESSION['domain_id']));
	  if (!$sth->rowCount()) {
  	    header ("Location: admingroup.php?group_faildeleted={$_GET['localpart']}");
	    die();
	  }

      # delete group member first
      $query = "DELETE FROM group_contents WHERE group_id=:group_id";
      $sth = $dbh->prepare($query);
      $success = $sth->execute(array(':group_id'=>$_GET['group_id']));
      if ($success) {
        # delete group
        $query = "DELETE FROM groups WHERE id=:group_id AND domain_id=:domain_id";
        $sth = $dbh->prepare($query);
        $success = $sth->execute(array(':group_id'=>$_GET['group_id'], ':domain_id'=>$_SESSION['domain_id']));
        if ($success) {
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
    } else if ($_GET['confirm'] == 'cancel') {
      header ("Location: admingroup.php?group_faildeleted={$_GET['localpart']}");
      die;
    }
  }
?>
<!DOCTYPE html>
<html>
  <head>
    <title><?php echo _('Virtual Exim') . ': ' . _('Confirm Delete'); ?></title>
    <link rel="stylesheet" href="style.css" type="text/css">
  </head>
    <body>
    <?php include dirname(__FILE__) . '/config/header.php'; ?>
    <div id="Menu">
      <a href="admingroupadd.php"><?php echo _('Add Group'); ?></a><br>
      <a href="admin.php"><?php echo _('Main Menu'); ?></a><br>
      <br><a href="logout.php"><?php echo _('Logout'); ?></a><br>
    </div>
    <div id="Content">
      <form name="groupdelete" method="get" action="admingroupdelete.php">
        <table align="center">
          <tr>
            <td colspan="2">
              <?php printf (_('Please confirm deleting group %s@%s'),
                htmlspecialchars($_GET['localpart']),
                htmlspecialchars($_SESSION['domain']));
              ?>:
            </td>
          </tr>
          <tr>
            <td>
              <input name='confirm' type='radio' value='cancel' checked>
              <b><?php printf (_('Do Not Delete %s@%s'),
                htmlspecialchars($_GET['localpart']),
                htmlspecialchars($_SESSION['domain']));
              ?></b>
            </td>
          </tr>
          <tr>
            <td>
              <input name='confirm' type='radio' value='1'><b>
              <?php printf (_('Delete %s@%s'),
                htmlspecialchars($_GET['localpart']),
                htmlspecialchars($_SESSION['domain']));
              ?></b>
            </td>
          </tr>
          <tr>
            <td>
              <input name='domain' type='hidden'
                value='<?php echo htmlspecialchars($_SESSION['domain']); ?>'>
              <input name='group_id' type='hidden'
                value='<?php echo htmlspecialchars($_GET['group_id']); ?>'>
              <input name='localpart' type='hidden'
                value='<?php echo htmlspecialchars($_GET['localpart']); ?>'>
              <input name='submit' type='submit'
                value='<?php echo _('Continue'); ?>'>
            </td>
          </tr>
        </table>
      </form>
    </div>
  </body>
</html>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
