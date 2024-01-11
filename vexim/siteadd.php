<?php
  include_once dirname(__FILE__) . '/config/variables.php';
  include_once dirname(__FILE__) . '/config/authsite.php';
  include_once dirname(__FILE__) . '/config/functions.php';
  include_once dirname(__FILE__) . '/config/httpheaders.php';
?>
<!DOCTYPE html>
<html>
  <head>
    <title><?php echo _('Virtual Exim') . ': ' . _('Manage Domains'); ?></title>
    <link rel="stylesheet" href="style.css" type="text/css">
    <script src="scripts.js" type="text/javascript"></script>
  </head>
  <body>
    <?php include dirname(__FILE__) . '/config/header.php'; ?>
    <div id='Menu'>
      <a href="site.php"><?php echo _('Manage Domains'); ?></a><br>
      <a href="sitepassword.php"><?php echo _('Site Password'); ?></a><br>
      <br><a href="logout.php"><?php echo _('Logout'); ?></a><br>
    </div>
    <div id="forms">
      <form name="siteadd" method="post" action="siteaddsubmit.php">
        <table align="center">
          <tr>
            <td><?php echo _('New Domain'); ?>:</td>
            <td><input name="domain" type="text" class="textfield" autofocus></td>
            <td>
              <?php echo _('The name of the new domain you are adding'); ?>
            </td>
          </tr>
          <?php
            if ($_GET['type'] == "local") {
          ?>
          <tr>
            <td><?php echo _('Domain Admin'); ?>:</td>
            <td>
              <input name="localpart" type="text" value="postmaster"
                class="textfield">
            </td>
            <td>
              <?php
                echo _('The username of the domain\'s administrator account');
              ?>
            </td>
          </tr>
          <tr>
            <td><?php echo _('Password'); ?>:</td>
            <td colspan="2">
              <input name="clear" id="clear" type="password" class="textfield">
            </td>
          </tr>
          <tr>
            <td><?php echo _('Verify Password'); ?>:</td>
            <td colspan="2">
              <input name="vclear" id="vclear" type="password" class="textfield">
            </td>
          </tr>
        <tr>
            <td></td>
            <td colspan="2">
              <input type="button" id="pwgenerate" value="Generate password">
              <input type="text" size="15" name="suggest" id="suggest" class="textfield">
              <input type="button" id="pwcopy" value="Copy">
            </td>
          </tr>
          <tr>
            <td><?php echo _('System UID'); ?>:</td>
            <td colspan="2">
              <input name="uid" type="text" class="textfield"
                value="<?php echo $uid; ?>">
            </td>
          </tr>
          <tr>
            <td><?php echo _('System GID'); ?>:</td>
            <td colspan="2">
              <input name="gid" type="text" class="textfield"
                value="<?php echo $gid; ?>">
            </td>
          </tr>
          <tr>
            <td><?php echo _('Domain Mail directory'); ?>:</td>
            <td>
              <input name="maildir" type="text" class="textfield"
                value="<?php echo $mailroot; ?>">
            </td>
            <td>
              <?php
                echo _('Create the domain directory below this top-level
                  mailstore');
              ?>
            </td>
          </tr>
          <tr>
            <td>
              <?php echo _('Maximum accounts'); ?><br>
              (<?php echo _("0 for unlimited"); ?>):
            </td>
            <td colspan="2">
              <input type="text" size="5" name="max_accounts" value="0"
                class="textfield">
            </td>
          </tr>
          <tr>
            <td>
              <?php echo _('Max mailbox quota'); ?>
              (<?php echo _('0 for disabled'); ?>):
            </td>
            <td colspan="2">
              <input name="quotas" size="5" type="text" class="textfield"
                value="0"><?php echo _('Mb'); ?>
            </td>
          </tr>
          <tr>
            <td><?php echo _('Maximum message size'); ?>:</td>
            <td>
              <input name="maxmsgsize" size="5" type="text" class="textfield"
                value="0"><?php echo _('Kb'); ?>
            </td>
            <td>
              <?php echo _('The maximum size for incoming mail (user
                tunable)'); ?>
            </td>
          </tr>
          <tr>
            <td><?php echo _('Spamassassin tag score'); ?>:</td>
            <td>
              <input name="sa_tag" size="5" type="text" class="textfield"
                value="<?php echo $sa_tag; ?>">
            </td>
            <td>
              <?php echo _('The score at the "X-Spam-Flag: YES" header will
                be added'); ?>
            </td>
          </tr>
          <tr>
            <td><?php echo _('Spamassassin refuse score'); ?>:</td>
            <td>
              <input name="sa_refuse" size="5" type="text" class="textfield"
              value="<?php echo $sa_refuse; ?>">
            </td>
            <td><?php echo _('The score at which to refuse potentially spam
              mail and not deliver'); ?>
            </td>
          </tr>
          <tr>
            <td><?php echo _('Spamassassin enabled?'); ?></td>
            <td colspan="2">
              <input name="spamassassin" type="checkbox" class="textfield">
            </td>
          </tr>
          <tr>
            <td><?php echo _('Anti Virus enabled?'); ?></td>
            <td colspan="2">
              <input name="avscan" type="checkbox" class="textfield">
            </td>
          </tr>
          <tr>
            <td><?php echo _('Enable piping mail to command?'); ?></td>
            <td colspan="2">
              <input name="pipe" type="checkbox" class="textfield">
            </td>
          </tr>
          <tr>
            <td><?php echo _('Domain enabled?'); ?></td>
            <td colspan="2">
              <input name="enabled" type="checkbox" class="textfield" checked>
            </td>
          </tr>
          <tr><td colspan="3"></td></tr>
        <?php
           } else if ($_GET['type'] == "alias") {
        ?>
          <tr>
            <td><?php echo _('Redirect messages to domain'); ?>:</td>
            <td colspan="2">
              <select name="aliasdest" type="text" class="textfield">
                <?php
                  $query = 'SELECT domain_id, domain FROM domains WHERE type="local" ORDER BY domain';
                  $sth = $dbh->prepare($query);
                  $sth->execute();
                  while ($row = $sth->fetch()) {
                    print '<option value="' . $row['domain_id'] . '">' . $row['domain'] . '</option>' . "\n\t";
                  }
                ?>
              </select>
            </td>
          </tr>
        <?php
           }
        ?>
          <tr>
            <td>
            </td>
            <td colspan="2">
              <input name="type" type="hidden"
                value="<?php print htmlspecialchars($_GET['type']); ?>">
              <input name="admin" type="hidden" value="1">
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
