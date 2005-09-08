<?php
  include_once dirname(__FILE__) . '/config/variables.php';
  include_once dirname(__FILE__) . '/config/authpostmaster.php';
  include_once dirname(__FILE__) . '/config/functions.php';
  include_once dirname(__FILE__) . '/config/httpheaders.php';
?>
<html>
  <head>
    <title><?php echo _('Virtual Exim') . ': ' . _('Manage Users'); ?></title>
    <link rel="stylesheet" href="style.css" type="text/css">
  </head>
  <body>
    <?php include dirname(__FILE__) . '/config/header.php'; ?>
    <div id="menu">
      <a href="adminalias.php"><?php echo _('Manage Aliases'); ?></a><br>
      <a href="adminaliasadd.php"><?php echo _('Add Alias'); ?></a></br>
      <a href="admin.php"><?php echo _('Main Menu'); ?></a><br>
      <br><a href="logout.php"><?php echo _('Logout'); ?></a><br>
    </div>
    <div id="Forms">
      <form name="admincatchall" method="post" action="admincatchallsubmit.php">
        <table align="center">
          <tr>
            <td><?php echo _('Alias Name'); ?>:</td>
            <td><?php echo _('Catchall'); ?> </td>
          </tr>
          <tr>
            <td><?php echo _('Forward email addressed to'); ?>:</td>
            <td>*@<?php echo $_SESSION['domain']; ?></td>
          </tr>
          <tr>
            <td><?php echo _('Forward the email to'); ?>:</td>
            <td><input name="smtp" type="text" class="textfield"><br>
              <?php echo _('Must be a full e-mail address'); ?>!
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
