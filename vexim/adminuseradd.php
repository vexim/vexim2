<?php
  include_once dirname(__FILE__) . '/config/variables.php';
  include_once dirname(__FILE__) . '/config/authpostmaster.php';
  include_once dirname(__FILE__) . '/config/functions.php';
  include_once dirname(__FILE__) . '/config/httpheaders.php';

  # enforce limit on the maximum number of user accounts in the domain
  $query = "SELECT (count(users.user_id) < domains.max_accounts)
    OR (domains.max_accounts = 0) AS allowed FROM
    users,domains WHERE users.domain_id=domains.domain_id
    AND domains.domain_id='{$_SESSION['domain_id']}'
    AND (users.type='local' OR users.type='piped')
    GROUP BY domains.max_accounts";
  $result = $db->query($query);
  if ($result->numRows()) {
    $row = $result->fetchRow();
  }
  if (!$row['allowed']) {
    header ('Location: adminuser.php?maxaccounts=true');
  }

  $query = "SELECT * FROM domains WHERE domain_id='{$_SESSION['domain_id']}'";
  $result = $db->query($query);
  $row = $result->fetchRow();
?>
<html>
  <head>
    <title><?php echo _('Virtual Exim') . ': ' . _('Manage Users'); ?></title>
    <link rel="stylesheet" href="style.css" type="text/css">
    <script src="scripts.js" type="text/javascript"></script>
  </head>
  <body onLoad="document.adminadd.realname.focus()">
    <?php include dirname(__FILE__) . '/config/header.php'; ?>
    <div id="menu">
      <a href="adminuser.php"><?php echo _('Manage Accounts'); ?></a><br>
      <a href="admin.php"><?php echo _('Main Menu'); ?></a><br>
      <br><a href="logout.php"><?php echo _('Logout'); ?></a><br>
    </div>
    <div id="forms">
    <form name="adminadd" method="post" action="adminuseraddsubmit.php">
      <table align="center">
        <tr>
          <td><?php echo _('Name'); ?>:</td>
          <td colspan="2">
            <input type="textfield" size="25" name="realname" class="textfield">
          </td>
        </tr>
        <tr>
          <td><?php echo _('Address'); ?>:</td>
          <td colspan="2">
            <input type="textfield" size="25" name="localpart"
              class="textfield">@<?php print $_SESSION['domain']; ?>
          </td>
        </tr>
        <tr>
          <td><?php echo _('Password'); ?>:</td>
          <td colspan="2">
            <input type="password" size="25" id="clear" name="clear" class="textfield">
          </td>
        </tr>
        <tr>
          <td><?php echo _('Verify Password'); ?>:</td>
          <td colspan="2">
            <input type="password" size="25" id="vclear" name="vclear" class="textfield">
          </td>
        </tr>
        <tr>
          <td></td>
          <td colspan="2">
            <input type="button" value="<?php echo _('Generate password'); ?>" onclick="suggestPassword('suggest')">
            <input type="text" size="15" id="suggest" class="textfield">
            <input type="button" value="<?php echo _('Copy'); ?>" onclick="copyPassword('suggest', 'clear', 'vclear')">
          </td>
        </tr>
      <?php
        if ($postmasteruidgid == "yes") { ?>
        <tr>
          <td><?php echo _('UID'); ?></td>
          <td colspan="2"><input type="textfield" size="5" name="uid" class="textfield" value="<?php echo $row['uid']; ?>"></td>
        </tr>
        <tr>
          <td><?php echo _('GID'); ?></td>
          <td colspan="2"><input type="textfield" size="5" name="gid" class="textfield" value="<?php echo $row['gid']; ?>"></td>
        </tr>
      <?php }
        if ($row['quotas'] > "0") { ?>
        <tr>
          <td><?php printf (_('Mailbox quota (%s Mb max)'), $row['quotas']); ?></td>
          <td colspan="2"><input type="text" size="5" name="quota" class="textfield" value="<?php echo $row['quotas']; ?>"><?php echo _('Mb'); ?></td>
        </tr>
      <?php } ?>
        <tr>
          <td><?php echo _('Has domain admin privileges?'); ?></td>
          <td colspan="2"><input name="admin" type="checkbox"></td>
        </tr>
        <?php if ($row['pipe'] == "1") { ?>
           <tr>
            <td><?php echo _('Pipe to command'); ?>:</td>
            <td colspan="2"><input type="textfield" size="25" name="smtp" class="textfield"></td>
          </tr>
          <tr>
            <td colspan="2" style="padding-bottom:1em">
              <?php echo _('Optional'); ?>:
              <?php echo _('Pipe all mail to a command (e.g. procmail)'); ?>
              <br>
              <?php echo _('Check box below to enable'); ?>
            </td>
          </tr>
          <tr>
            <td><?php echo _('Enable piped command?'); ?></td>
            <td colspan="2"><input type="checkbox" name="on_piped"></td>
          </tr>
          <?php }
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
            <td>
              <input name="sa_tag" size="5" type="text" class="textfield"
                value="<?php echo $row['sa_tag']; ?>"></td>
            <td><?php echo _('The score at which to tag potential spam
              mail but still deliver'); ?>
            </td>
          </tr>
          <tr>
            <td><?php echo _('Spamassassin refuse score'); ?>:</td>
            <td>
              <input name="sa_refuse" size="5" type="text" class="textfield"
                value="<?php echo $row['sa_refuse']; ?>">
            </td>
            <td>
              <?php echo _('The score at which to refuse potential spam mail
                and not deliver'); ?>
            </td>
          </tr>
          <?php } ?>
          <tr>
            <td><?php echo _('Maximum message size'); ?>:</td>
            <td colspan="2">
              <input name="maxmsgsize" size="5" type="text"
                value="<?php echo $row['maxmsgsize']; ?>">Kb
            </td>
          </tr>
        <tr>
          <td><?php echo _('Enabled'); ?>:</td>
          <td colspan="2"><input name="enabled" type="checkbox" checked></td>
        </tr>
        <tr>
          <td colspan="3" class="button">
          <input name="submit" type="submit" value="<?php echo _('Submit'); ?>">
          </td>
        </tr>
      </table>
    </form>
  </body>
</html>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
