<?php
  include_once dirname(__FILE__) . '/config/variables.php';
  include_once dirname(__FILE__) . '/config/authpostmaster.php';
  include_once dirname(__FILE__) . '/config/functions.php';
  include_once dirname(__FILE__) . '/config/httpheaders.php';

  if(array_key_exists('confirm', $_GET)) {
    if ($_GET['confirm'] == '1') {
      $query = "DELETE FROM users
        WHERE user_id=:user_id
        AND domain_id=:domain_id
	    AND (type='alias' OR type='catch')";
      $sth = $dbh->prepare($query);
      $success = $sth->execute(array(':user_id'=>$_GET['user_id'], ':domain_id'=>$_SESSION['domain_id']));
      if ($success) {
        header ("Location: adminalias.php?deleted={$_GET['localpart']}");
        die;
      } else {
        header ("Location: adminalias.php?faildeleted={$_GET['localpart']}");
        die;
      }
    } else if ($_GET['confirm'] == 'cancel') {
      header ("Location: adminalias.php?faildeleted={$_GET['localpart']}");
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
      <a href="adminaliasadd.php"><?php echo _('Add Alias'); ?></a><br>
      <a href="admin.php"><?php echo _('Main Menu'); ?></a><br>
      <br><a href="logout.php"><?php echo _('Logout'); ?></a><br>
    </div>
    <div id="Content">
      <form name="aliasdelete" method="get" action="adminaliasdelete.php">
        <table align="center">
          <tr>
            <td colspan="2">
              <?php printf (_('Please confirm deleting alias %s@%s'),
                $_GET['localpart'] ,
                $_SESSION['domain']);
              ?>:
            </td>
          </tr>
          <tr>
            <td>
              <input name='confirm' type='radio' value='cancel' checked>
              <b><?php printf (_('Do Not Delete %s@%s'),
                $_GET['localpart'],
                $_SESSION['domain']);
              ?></b>
            </td>
          </tr>
          <tr>
            <td>
              <input name='confirm' type='radio' value='1'>
              <b><?php printf (_('Delete %s@%s'),
                $_GET['localpart'],
                $_SESSION['domain']);
              ?></b>
            </td>
          </tr>
          <tr>
            <td>
              <input name='domain' type='hidden'
                value='<?php echo $_SESSION['domain']; ?>'>
              <input name='user_id' type='hidden'
                value='<?php echo $_GET['user_id']; ?>'>
              <input name='localpart' type='hidden'
                value='<?php echo $_GET['localpart']; ?>'>
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
