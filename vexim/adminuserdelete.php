<?php
  include_once dirname(__FILE__) . '/config/variables.php';
  include_once dirname(__FILE__) . '/config/authpostmaster.php';
  include_once dirname(__FILE__) . '/config/functions.php';
  include_once dirname(__FILE__) . '/config/httpheaders.php';

# confirm that the postmaster is looking to delete a user they are permitted to change before going further  
$query = "SELECT * FROM users WHERE user_id=:user_id
	AND domain_id=:domain_id	
	AND (type='local' OR type='piped')";
$sth = $dbh->prepare($query);
$sth->execute(array(':user_id'=>$_GET['user_id'], ':domain_id'=>$_SESSION['domain_id']));
if (!$sth->rowCount()) {
  header ("Location: adminuser.php?faildeleted={$_GET['localpart']}");
  die();  
}
if(!isset($_GET['confirm'])) { $_GET['confirm'] = null; }

if ($_GET['confirm'] == '1') {
  # prevent deleting the last admin
  $query = "SELECT COUNT(user_id) AS count FROM users 
    WHERE admin=1 AND domain_id=:domain_id
	AND (type='local' OR type='piped')
    AND user_id!=:user_id";
  $sth = $dbh->prepare($query);
  $sth->execute(array(':domain_id'=>$_SESSION['domain_id'], ':user_id'=>$_GET['user_id']));
  $row = $sth->fetch();
  if ($row['count'] == "0") {
    header ("Location: adminuser.php?lastadmin={$_GET['localpart']}");
    die;
  }  

  $query = "DELETE FROM users
    WHERE user_id=:user_id
    AND domain_id=:domain_id
	AND (type='local' OR type='piped')";
  $sth = $dbh->prepare($query);
  $success = $sth->execute(array(':user_id'=>$_GET['user_id'], ':domain_id'=>$_SESSION['domain_id']));
  if ($success) {
    $query = "DELETE FROM group_contents WHERE member_id=:user_id";
    $sth = $dbh->prepare($query);
    $sth->execute(array(':user_id'=>$_GET['user_id']));
    header ("Location: adminuser.php?deleted={$_GET['localpart']}");
  } else {
    header ("Location: adminuser.php?faildeleted={$_GET['localpart']}");
  }
  die;
} else if ($_GET['confirm'] == "cancel") {                 
    header ("Location: adminuser.php?faildeleted={$_GET['localpart']}");
    die;                                                      
} else {
  $query = "SELECT COUNT(user_id) AS count FROM users 
    WHERE admin=1 AND domain_id=:domain_id
	AND (type='local' OR type='piped')
    AND user_id!=:user_id";
  $sth = $dbh->prepare($query);
  $sth->execute(array(':domain_id'=>$_SESSION['domain_id'], ':user_id'=>$_GET['user_id']));
  $row = $sth->fetch();
  if ($row['count'] == "0") {
    header ("Location: adminuser.php?lastadmin={$_GET['localpart']}");
    die;
  }  
  $query = "SELECT localpart FROM users WHERE user_id=:user_id";
  $sth = $dbh->prepare($query);
  $sth->execute(array(':user_id'=>$_GET['user_id']));
  if ($sth->rowCount()) { $row = $sth->fetch(); }
}
?>
<html>
  <head>
    <title><?php echo _('Virtual Exim') . ': ' . _('Confirm Delete'); ?></title>
    <link rel="stylesheet" href="style.css" type="text/css">
  </head>
  <body>
    <?php include dirname(__FILE__) . '/config/header.php'; ?>
    <div id="Menu">
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
