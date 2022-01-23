<?php
  include_once dirname(__FILE__) . '/config/variables.php';
  include_once dirname(__FILE__) . '/config/authpostmaster.php';
  include_once dirname(__FILE__) . '/config/functions.php';
  include_once dirname(__FILE__) . '/config/httpheaders.php';

  $query = "SELECT * FROM users WHERE user_id=:user_id
		AND domain_id=:domain_id
		AND (type='local' OR type='piped')";
  $sth = $dbh->prepare($query);
  $sth->execute(array(':user_id'=>$_GET['user_id'], ':domain_id'=>$_SESSION['domain_id']));
  if ($sth->rowCount()) { $row = $sth->fetch(); }
  
  $username = $row['username'];
  $domquery = "SELECT avscan,spamassassin,quotas,pipe FROM domains
    WHERE domain_id=:domain_id";
  $domsth = $dbh->prepare($domquery);
  $domsth->execute(array(':domain_id'=>$_SESSION['domain_id']));
  if ($domsth->rowCount()) {
    $domrow = $domsth->fetch();
  }
  $blockquery = "SELECT blockhdr,blockval,block_id FROM blocklists
    WHERE blocklists.user_id=:user_id";
  $blocksth = $dbh->prepare($blockquery);
  $blocksth->execute(array(':user_id'=>$_GET['user_id']));
?>
<!DOCTYPE html>
<html>
  <head>
    <title><?php echo _('Virtual Exim') . ': ' . _('Manage Users'); ?></title>
    <link rel="stylesheet" href="style.css" type="text/css">
    <script src="scripts.js" type="text/javascript"></script>
  </head>
  <body>
  <?php include dirname(__FILE__) . '/config/header.php'; ?>
    <div id="Menu">
      <a href="adminuser.php"><?php echo _('Manage Accounts'); ?></a><br>
      <a href="admin.php"><?php echo _('Main Menu'); ?></a><br>
      <br><a href="logout.php"><?php echo _('Logout'); ?></a><br>
    </div>
    <div id="forms">
	<?php 
		# ensure this page can only be used to view/edit user accounts that already exist for the domain of the admin account
		if (!$sth->rowCount()) {			
			echo '<table align="center"><tr><td>';
			echo "Invalid userid '" . htmlentities($_GET['user_id']) . "' for domain '" . htmlentities($_SESSION['domain']). "'";			
			echo '</td></tr></table>';
		}else{
	?>
	
    <form name="userchange" method="post" action="adminuserchangesubmit.php">
      <table align="center">
        <tr>
          <td><?php echo _('Name'); ?>:</td>
          <td>
            <input type="text" size="25" name="realname"
              value="<?php print $row['realname']; ?>" class="textfield" autofocus>
            <input name="user_id" type="hidden"
              value="<?php print htmlspecialchars($_GET['user_id']); ?>">
          </td>
        </tr>
        <tr>
          <td><?php echo _('Email Address'); ?>:</td>
          <td><?php print $row['username']; ?></td>
        </tr>
        <tr>
          <td><?php echo _('Password'); ?>:</td>
          <td>
            <input type="password" size="25" id="clear" name="clear" class="textfield">
          </td>
        </tr>
        <tr>
          <td><?php echo _('Verify Password'); ?>:</td>
          <td>
            <input type="password" size="25" id="vclear" name="vclear" class="textfield">
          </td>
        </tr>
        <tr>
          <td></td>
          <td>
            <input type="button" id="pwgenerate" value="<?php echo _('Generate password'); ?>">
            <input type="text" size="15" id="suggest" class="textfield">
            <input type="button" id="pwcopy" value="<?php echo _('Copy'); ?>">
          </td>
        </tr>
        <?php
          if ($postmasteruidgid == "yes") { ?>
          <tr>
            <td><?php echo _('UID'); ?>:</td>
            <td>
              <input type="text" size="25" name="uid" class="textfield"
                value="<?php echo $row['uid']; ?>">
            </td>
          </tr>
          <tr>
            <td><?php echo _('GID'); ?>:</td>
            <td>
              <input type="text" size="25" name="gid" class="textfield"
                value="<?php echo $row['gid']; ?>">
            </td>
          </tr> 
          <tr>
            <td colspan="2" class="padafter">
              <?php echo _('When you update the UID or GID, please make sure
                your MTA still has permission to create the required user
                directories!'); ?>
            </td>
          </tr>
        <?php
          }
          //if ($domrow['quotas'] > "0") {
        ?>
            <tr>
               <td>
                 <?php printf (_('Mailbox quota (%s MB max)'),
                   $domrow['quotas']); ?>:</td>
                <td>
                  <input type="text" size="5" name="quota" class="textfield"
                    value="<?php echo ($domrow['quotas'] == 0 ? $row['quota'] : ($row['quota'] == 0 ? $domrow['quotas'] : min($domrow['quotas'], $row['quota']))); ?>">
                    <?php echo _('MB'); ?>
                </td>
              </tr>
          <?php
            //}
            if ((function_exists('imap_get_quotaroot'))
              && ($imap_to_check_quota == "yes")) {
              $mbox = imap_open(
                $imapquotaserver, $row['username'], $row['clear'], OP_HALFOPEN
              );
              $quota = imap_get_quotaroot($mbox, "INBOX");
              if (is_array($quota) && !empty($quota)) {
              printf ("<tr><td>"
                . _('Space used:')
                . "</td><td>"
                . _('%.2f MB')
                . "</td></tr>",
                $quota['STORAGE']['usage'] / 1024);
              }
              imap_close($mbox);
            }
          if ($domrow['pipe'] == "1") {
          ?>
          <tr>
            <td><?php echo _('Pipe to command or alternative Maildir'); ?>:</td>
            <td>
              <input type="textfield" size="25" name="smtp" class="textfield"
                value="<?php echo $row['smtp']; ?>">
            </td>
          </tr>
          <tr>
            <td colspan="2" class="padafter">
              <?php echo _('Optional'); ?>:
              <?php echo _('Pipe all mail to a command (e.g. procmail).'); ?>
              <br>
              <?php echo _('Check box below to enable'); ?>:
            </td>
          </tr>
          <tr>
            <td><?php _('Enable piped command or alternative Maildir?'); ?></td>
            <td>
              <input type="checkbox" name="on_piped"
              <?php
                if ($row['on_piped'] == "1") {
                  print " checked ";
                } ?>>
            </td>
          </tr>
        <?php
          }
        ?>
        <tr>
          <td>
            <?php echo _('Admin'); ?>:</td>
            <td>
              <input name="admin" type="checkbox"<?php if ($row['admin'] == 1) { 
                print " checked";
              } ?> class="textfield">
            </td>
          </tr>
        <?php
          if ($domrow['avscan'] == "1") {
        ?>
          <tr>
            <td><?php echo _('Anti-Virus'); ?>:</td>
            <td><input name="on_avscan" type="checkbox"
              <?php if ($row['on_avscan'] == "1") {
                print " checked";
              } ?>>
            </td>
          </tr>
        <?php
           }
           if ($domrow['spamassassin'] == "1") {
        ?>
            <tr>
              <td><?php echo _('Spamassassin'); ?>:</td>
              <td><input name="on_spamassassin" type="checkbox"
                <?php if ($row['on_spamassassin'] == "1") {
                  print " checked";
                }?>>
              </td>
            </tr>
            <tr>
              <td><?php echo _('Spamassassin tag score'); ?>:</td>
              <td>
                <input type="text" size="5" name="sa_tag"
                  value="<?php echo $row['sa_tag']; ?>" class="textfield">
              </td>
            </tr>
            <tr>
              <td><?php echo _('Spamassassin refuse score'); ?>:</td>
              <td>
                <input type="text" size="5" name="sa_refuse"
                  value="<?php echo $row['sa_refuse']; ?>" class="textfield">
              </td>
            </tr>
            <tr>
              <td><?php echo _('How to handle mail above the SA refuse score'); ?>:</td>
              <td>
               <input type="radio" id="off" name="spam_drop" value="0"<?php if ($row['spam_drop'] == "0") {
                  print " checked"; }?>>
               <label for="off"> <?PHP echo _('move to Spam-folder'); ?></label><br>
               <input type="radio" id="on" name="spam_drop" value="1"<?php if ($row['spam_drop'] == "1") {
                  print " checked"; }?>>
               <label for="on"><?PHP echo _('delete - you cannot restore these mails'); ?></label><br>
            </td>
            </tr>
          <?php
            }
          ?>
        <tr>
          <td><?php echo _('Maximum message size'); ?>:</td>
          <td>
            <input type="text" size="5" name="maxmsgsize"
              value="<?php echo $row['maxmsgsize']; ?>" class="textfield">Kb
          </td>
        </tr>
        <tr>
          <td><?php echo _('Enabled'); ?>:</td>
          <td><input name="enabled" type="checkbox" <?php
            if ($row['enabled'] == 1) {
              print "checked";
            } ?> class="textfield">
          </td>
        </tr>
        <tr>
          <td><?php echo _('Vacation on'); ?>:</td>
          <td><input name="on_vacation" type="checkbox" <?php
            if ($row['on_vacation'] == "1") {
              print " checked ";
            } ?>>
          </td>
        </tr>
        <tr>
          <td><?php echo _('Vacation message'); ?>:</td>
          <td>
            <textarea name="vacation" cols="40" rows="5" class="textfield"><?php print quoted_printable_decode($row['vacation']); ?></textarea>
          </td>
        </tr>
        <tr>
          <td><?php echo _('Forwarding on'); ?>:</td>
          <td><input name="on_forward" id="on_forward" type="checkbox" <?php
            if ($row['on_forward'] == "1") {
              print " checked";
            } ?>>
          </td>
        </tr>
        <tr>
          <td valign="top"><?php echo _('Forward mail to'); ?>:</td>
          <td>
            <input type="text" size="25" name="forward" id="forward"
            value="<?php print $row['forward']; ?>" class="textfield"><br>
            <?php echo _('Enter full e-mail addresses, use commas to separate them'); ?>!<br>
            <?php echo _('or select from this list') .":<br>\n"; ?>
            <select name="forwardmenu" id="forwardmenu">
              <option selected value=""></option>
              <?php
                $queryuserlist = "SELECT realname, username, user_id, unseen
                FROM users
                WHERE enabled='1' AND domain_id=:domain_id AND type != 'fail'
                ORDER BY realname, username, type desc";
                $sthuserlist = $dbh->prepare($queryuserlist);
                $sthuserlist->execute(array(':domain_id'=>$_SESSION['domain_id']));
                while ($rowuserlist = $sthuserlist->fetch()) {
              ?>
                <option value="<?php echo $rowuserlist['username']; ?>">
                  <?php echo $rowuserlist['realname']; ?>
                  (<?php echo $rowuserlist['username']; ?>)
                </option>
              <?php
                }
              ?>
            </select>
          </td>
        </tr>
        <tr>
          <td><?php echo _('Store Forwarded Mail Locally'); ?>:</td>
          <td><input name="unseen" type="checkbox" <?php
            if ($row['unseen'] == "1") {
              print " checked ";
            } ?>>
            <input name="user_id" type="hidden"
              value="<?php print htmlspecialchars($_GET['user_id']); ?>">
            <input name="localpart" type="hidden"
              value="<?php print $row['localpart']; ?>">
          </td>
        </tr>
        <tr>
          <td colspan="2" class="button">
            <input name="submit" type="submit" value="<?php echo _('Submit'); ?>">
          </td>
        </tr>
        <tr>
          <td colspan="2" class="padafter">
          <?php
            # Print the aliases associated with this account
            $query = "SELECT user_id,localpart,domain,realname FROM users,domains
              WHERE smtp=:smtp AND users.domain_id=domains.domain_id ORDER BY realname";
            $sth = $dbh->prepare($query);
            $sth->execute(array(':smtp'=>$row['localpart'].'@'.$_SESSION['domain']));
            if ($sth->rowCount()) {
              echo "<h4>"._('Aliases to this account').":</h4>";
              while ($row = $sth->fetch()) {
                if (($row['domain'] == $_SESSION['domain'])
                  && ($row['localpart'] != "*")) {
                  print '<a href="adminaliaschange.php?user_id='
                    . $row['user_id']
                    . '">'
                    . $row['localpart']. '@' . $row['domain']
                    . '</a>';
                } else if (($row['domain'] == $_SESSION['domain'])
                  && ($row['localpart'] == "*")) {
                  print '<a href="admincatchall.php?user_id='
                    . $row['user_id']
                    . '">'
                    . $row['localpart'] . '@' . $row['domain']
                    . '</a>';
              } else {
                print $row['localpart'] . '@' . $row['domain'];
              }
              if ($row['realname'] == "Catchall") {
                print $row['realname'];
              }
              print '<br>';
            }
          }
        ?>
        </td></tr>
      </table>
    </form>
    <br>
    <form name="blocklist" method="post" action="adminuserblocksubmit.php">
      <table align="center">
        <tr>
          <td colspan="2">
            <h4>
              <?php
                echo _('Add a new header blocking filter for this user');
              ?>:
            </h4>
          </td>
        </tr>
        <tr>
          <td><?php echo _('Header'); ?>:</td>
          <td>
            <select name="blockhdr" class="textfield">
              <option value="From"><?php echo _('From'); ?>:</option>
              <option value="To"><?php echo _('To'); ?>:</option>
              <option value="Subject"><?php echo _('Subject'); ?>:</option>
              <option value="X-Mailer"><?php echo _('X-Mailer'); ?>:</option>
            </select>
          </td>
          <td>
            <input name="blockval" type="text" size="25" class="textfield">
            <input name="user_id" type="hidden"
              value="<?php print htmlspecialchars($_GET['user_id']); ?>">
            <input name="localpart" type="hidden"
              value="<?php print htmlspecialchars($_GET['localpart']); ?>">
            <input name="color" type="hidden" value="black">
          </td>
        </tr>
        <tr>
          <td colspan="3" class="button">
            <input name="submit" type="submit"
              value="<?php echo _('Submit'); ?>">
          </td>
        </tr>
      </table>
    </form>
    <table align="center">
      <tr>
        <th><?php echo _('Delete'); ?></th>
        <th><?php echo _('Blocked Header'); ?></th>
        <th><?php echo _('Content'); ?></th>
      </tr>
      <?php
        if ($blocksth->rowCount()) {
          while ($blockrow = $blocksth->fetch()) {
      ?>
            <tr>
              <td>
                <a href="adminuserblocksubmit.php?action=delete&user_id=<?php
					print htmlspecialchars($_GET['user_id'])
					. '&block_id='
					. $blockrow['block_id']
					.'&localpart='
					. htmlspecialchars($_GET['localpart']);?>">
                  <img class="trash" title="Delete" src="images/trashcan.gif"
                    alt="trashcan">
                </a>
              </td>
              <td><?php echo $blockrow['blockhdr']; ?></td>
              <td><?php echo $blockrow['blockval']; ?></td>
            </tr>
        <?php
          }
        }
      ?>
    </table>
	<?php
		# end of the block editing an alias within the domain
	}
	?>
    </div>
  </body>
</html>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
