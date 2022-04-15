<?php
  include_once dirname(__FILE__) . '/config/variables.php';
  include_once dirname(__FILE__) . '/config/authpostmaster.php';
  include_once dirname(__FILE__) . '/config/functions.php';
  include_once dirname(__FILE__) . '/config/httpheaders.php';
  $query = "SELECT * FROM domains
    WHERE domain_id=:domain_id";
  $sth = $dbh->prepare($query);
  $sth->execute(array(':domain_id'=>$_SESSION['domain_id']));
  if ($sth->rowCount()) { $row = $sth->fetch(); }
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
      <a href="adminalias.php"><?php echo _('Manage Aliases'); ?></a><br>
      <a href="admin.php"><?php echo _('Main Menu'); ?></a><br>
      <br><a href="logout.php"><?php echo _('Logout'); ?></a><br>
    </div>
    <div id="Forms">
      <form name="adminadd" method="post" action="adminaliasaddsubmit.php">
        <table align="center">
          <tr>
            <td><?php echo _('Alias Name'); ?>:</td>
            <td><input name="realname" type="text" class="textfield" autofocus></td>
          </tr>
          <tr>
            <td><?php echo _('Address'); ?>:</td>
            <td>
              <input name="localpart" type="text" class="textfield">@
              <?php print htmlspecialchars($_SESSION['domain']); ?>
            </td>
          </tr>
          <tr>
            <td colspan="2" class="padafter">
              <?php echo _('Multiple addresses should be comma separated,
                with no spaces'); ?>
            </td>
          </tr>
          <tr>
            <td><?php echo _('Forward To'); ?>:</td>
            <td><input name="smtp" type="text" size="30" class="textfield"></td>
          </tr>
          <tr>
            <td><?php echo _('Password'); ?>:</td>
            <td>
              <input name="clear" type="password" size="30" class="textfield">
            </td>
          </tr>
          <tr>
            <td colspan="2" class="padafter">
              (<?php echo _('Password only needed if you want the user to be able
              to log in, or if the Alias is the admin account'); ?>)
            </td>
          </tr>
          <tr>
            <td><?php echo _('Verify Password'); ?>:</td>
            <td>
              <input name="vclear" type="password" size="30" class="textfield">
            </td>
          </tr>
          <tr>
            <td><?php echo _('Admin'); ?>:</td>
            <td><input name="admin" type="checkbox" class="textfield"></td>
          </tr>
          <?php
            if ($row['avscan'] == "1") {
          ?>
          <tr>
            <td><?php echo _('Anti-Virus'); ?>:</td>
            <td colspan="2"><input name="on_avscan" type="checkbox"></td>
          </tr>
          <?php }
            if ($row['spamassassin'] == "1") {
          ?>
          <tr>
            <td><?php echo _('Spamassassin'); ?>:</td>
            <td colspan="2"><input name="on_spamassassin" type="checkbox"></td>
          </tr>
          <tr>
            <td><?php echo _('Spamassassin tag score'); ?>:</td>
            <td colspan="2">
              <input name="sa_tag" size="5" type="text" class="textfield"
                value="<?php echo $row['sa_tag']; ?>"></td>
          </tr>
          <tr>
            <td><?php echo _('Spamassassin refuse score'); ?>:</td>
            <td colspan="2">
              <input name="sa_refuse" size="5" type="text" class="textfield"
                value="<?php echo $row['sa_refuse']; ?>">
            </td>
          </tr>
          <tr>
            <td><?php echo _('How to handle mail above the SA refuse score'); ?>:</td>
            <td>
             <input type="radio" id="off" name="spam_drop" value="0" checked>
             <label for="off"> <?PHP echo _('forward Spam mails'); ?></label><br>
             <input type="radio" id="on" name="spam_drop" value="1">
             <label for="on"><?PHP echo _('delete Spam mails'); ?></label><br>
            </td>
          </tr>
          <?php } ?>
          <tr>
            <td><?php echo _('Enabled'); ?>:</td>
            <td>
              <input name="enabled" type="checkbox" class="textfield" checked>
            </td>
          </tr>
          <tr>
            <td colspan="2" class="button">
              <input name="submit" type="submit" value="<?php echo _('Submit'); ?>">
            </td>
          </tr>
        </table>
      </form>
    </div>
  </body>
</html>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
