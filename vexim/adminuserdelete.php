<?php
  include_once dirname(__FILE__) . '/config/variables.php';
  include_once dirname(__FILE__) . '/config/authpostmaster.php';
  include_once dirname(__FILE__) . '/config/functions.php';
  include_once dirname(__FILE__) . '/config/httpheaders.php';

if ($_GET['confirm'] == '1') {

  $query = "DELETE FROM users
    WHERE user_id={$_GET['user_id']}
    AND domain_id={$_SESSION['domain_id']}";
  $result = $db->query($query);
  if (!DB::isError($result)) {
    $query = "DELETE FROM group_contents WHERE member_id={$_GET['user_id']}";
    $result = $db->query($query);
    header ("Location: adminuser.php?deleted={$_GET['localpart']}");
  } else {
    header ("Location: adminuser.php?faildeleted={$_GET['localpart']}");
  }
} else if ($_GET['confirm'] == "cancel") {                 
    header ("Location: adminuser.php?faildeleted={$_GET['localpart']}");
    die;                                                      
} else {
  $query = "SELECT user_id AS count FROM users 
    WHERE admin=1 AND domain_id={$_SESSION['domain_id']}
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
    <title><?php echo _('Virtual Exim') . ': ' . _('Confirm Delete'); ?></title>
    <link rel="stylesheet" href="style.css" type="text/css">
  </head>
  <body>
    <?php include dirname(__FILE__) . '/config/header.php'; ?>
    <div id="menu">
      <a href="adminuseradd.php"><?php echo _('Add User'); ?></a><br>
      <a href="admin.php"><?php echo _('Main Menu'); ?></a><br>
      <br><a href="logout.php"><?php echo _('Logout'); ?></a><br>
    </div>
    <div id="Content">
      <form name="userdelete" method="get" action="adminuserdelete.php">
        <table align="center">
          <tr>
            <td colspan="2">
              <?php printf (_('Please confirm deleting user %s@%s'),
                $row['localpart'],
                $_SESSION['domain']);
              ?>:
            </td>
          </tr>
          <tr>
            <td>
              <input name="confirm" type="radio" value="cancel" checked>
              <b><?php printf (_('Do Not Delete %s@%s'),
                $row['localpart'],
                $_SESSION['domain']);
              ?></b>
            </td>
          </tr>
          <tr>
            <td>
              <input name="confirm" type="radio" value="1">
              <b><?php printf (_('Delete %s@%s'),
                $row['localpart'],
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
