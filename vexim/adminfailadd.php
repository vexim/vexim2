<?php
  include_once dirname(__FILE__) . '/config/variables.php';
  include_once dirname(__FILE__) . '/config/authpostmaster.php';
  include_once dirname(__FILE__) . '/config/functions.php';
  include_once dirname(__FILE__) . '/config/httpheaders.php';
?>
<!DOCTYPE html>
<html>
  <head>
    <title><?php echo _('Virtual Exim') . ': ' . _('Manage Users'); ?></title>
    <link rel="stylesheet" href="style.css" type="text/css">
  </head>
  <body>
    <?php include dirname(__FILE__) . '/config/header.php'; ?>
    <div id="Menu">
       <a href="adminfail.php"><?php echo _('Manage Fails'); ?></a><br>
       <a href="admin.php"><?php echo _('Main Menu'); ?></a><br>
       <br><a href="logout.php"><?php echo _('Logout'); ?></a><br>
    </div>
    <div id="Forms">
      <form name="adminadd" method="post" action="adminfailaddsubmit.php">
        <table align="center">
	  <tr>
	    <td><?php echo _('Address to fail'); ?>:</td>
            <td>
              <input name="localpart" type="text" class="textfield" autofocus>@
              <?php print $_SESSION['domain']; ?>
            </td>
	  </tr>
	  <tr>
	    <td colspan="2" class="button">
              <input name="submit" type="submit"
              value="<?php echo _('Submit'); ?>">
            </td>
	  </tr>
        </table>
      </form>
    </div>
  </body>
</html>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
