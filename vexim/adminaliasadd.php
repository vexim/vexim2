<?php
  include_once dirname(__FILE__) . '/config/variables.php';
  include_once dirname(__FILE__) . '/config/authpostmaster.php';
  include_once dirname(__FILE__) . '/config/functions.php';
  include_once dirname(__FILE__) . '/config/httpheaders.php';
  $query = "SELECT avscan,spamassassin FROM domains
    WHERE domain_id='{$_SESSION['domain_id']}'";
  $result = $db->query($query);
  if ($result->numRows()) { $row = $result->fetchRow(); }
?>
<html>
  <head>
    <title><?php echo _('Virtual Exim') . ': ' . _('Manage Users'); ?></title>
    <link rel="stylesheet" href="style.css" type="text/css">
  </head>
  <body onLoad="document.adminadd.realname.focus()">
    <?php include dirname(__FILE__) . '/config/header.php'; ?>
    <div id="menu">
      <a href="adminalias.php"><?php echo _('Manage Aliases'); ?></a><br>
      <a href="admin.php"><?php echo _('Main Menu'); ?></a><br>
      <br><a href="logout.php"><?php echo _('Logout'); ?></a><br>
    </div>
    <div id="Forms">
      <form name="adminadd" method="post" action="adminaliasaddsubmit.php">
        <table align="center">
          <tr>
            <td><?php echo _('Alias Name'); ?>:</td>
            <td><input name="realname" type="text" class="textfield"></td>
          </tr>
          <tr>
            <td><?php echo _('Address'); ?>:</td>
            <td>
              <input name="localpart" type="text" class="textfield">@
              <?php print $_SESSION['domain']; ?>
            </td>
          </tr>
          <tr>
            <td colspan="2" style="padding-bottom:1em">
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
            <td colspan="2" style="padding-bottom:1em">
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
            if ($row['on_avscan'] == "1") {
              print '<tr><td>' . _('Anti-Virus')
                . ':</td><td>'
                . '<input name="on_avscan" type="checkbox" class="textfield">'
                . '</td></tr>';
            }
            if ($row['on_spamassassin'] == "1") {
              print '<tr><td>' . _('Spamassassin')
              . ':</td><td>'
              . '<input name="on_spamassassin" type="checkbox" class="textfield">'
              . '</td></tr>';
            }
          ?>
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
