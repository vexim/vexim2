<?php
  include_once dirname(__FILE__) . '/config/variables.php';
  include_once dirname(__FILE__) . '/config/authpostmaster.php';
  include_once dirname(__FILE__) . "/config/functions.php";
?>
<?php
  $query = "SELECT * FROM groups WHERE id=:group_id AND domain_id=:domain_id";
  $sth = $dbh->prepare($query);
  $sth->execute(array(':group_id'=>$_GET['group_id'], ':domain_id'=>$_SESSION['domain_id']));
  if($sth->rowCount()) {
    $row = $sth->fetch();
    $grouplocalpart = $row['name'];
  }
?>
<!DOCTYPE html>
<html>
  <head>
    <title><?php echo _('Virtual Exim') . ': ' . _('Edit group'); ?></title>
    <link rel="stylesheet" href="style.css" type="text/css">
  </head>
  <body>
    <?php include dirname(__FILE__) . '/config/header.php'; ?>
    <div id="Menu">
      <a href="admingroup.php"><?php echo _('Manage Groups'); ?></a><br>
      <a href="admingroupadd.php"><?php echo _('Add Group'); ?></a></br>
      <a href="admin.php"><?php echo _('Main Menu'); ?></a><br>
      <br><a href="logout.php"><?php echo _('Logout'); ?></a><br>
    </div>
    <div id="Forms">
	<?php
		# ensure this page can only be used to view/edit aliases that already exist for the domain of the admin account
		if (!$sth->rowCount()) {
			echo '<table align="center"><tr><td>';
			echo "Invalid groupid '" . htmlentities($_GET['group_id']) . "' for domain '" . htmlentities($_SESSION['domain']). "'";
			echo '</td></tr></table>';
		}else{
	?>
      <table align="center">
        <form name="groupchange" method="post"
          action="admingroupchangesubmit.php">
        <tr>
          <td><?php echo _('Group Address'); ?>:</td>
          <td>
            <input name="localpart" type="text"
              value="<?php echo $row['name']; ?>"class="textfield" autofocus>@
              <?php echo $_SESSION['domain']; ?>
            <input name="group_id" type="hidden"
              value="<?php echo $_GET['group_id']; ?>" class="textfield">
          </td>
        </tr>
        <tr>
          <td><?php echo _('Is public'); ?></td>
          <td>
            <input name="is_public" type="checkbox"
              <?php echo $row['is_public'] == 'Y' ? 'checked' : ''; ?>
              class="textfield">
          </td>
        </tr>
        <tr>
          <td><?php echo _('Enabled'); ?></td>
          <td>
            <input name="enabled" type="checkbox"
              <?php echo $row['enabled']=='1' ? 'checked' : ''; ?>
              class="textfield">
          </td>
        </tr>
        <tr>
          <td colspan="2" class="button">
            <input name="editgroup" type="submit" value="Submit">
          </td>
        </tr>
        </form>
        <tr>
          <td colspan="2">&nbsp;</td>
        </tr>
        <tr>
          <td colspan="2">
            <?php
              $query = "SELECT u.realname, u.localpart, u.enabled, c.member_id
                FROM users u, group_contents c
                WHERE u.user_id=c.member_id and c.group_id=:group_id
                ORDER BY u.enabled desc, u.realname asc";
              $sth = $dbh->prepare($query);
              $sth->execute(array(':group_id'=>$_GET['group_id']));
              if ($sth->rowCount()) {
            ?>
            <table align="center">
              <tr>
                <th>&nbsp;</th>
                <th><?php echo _('Real name'); ?></th>
                <th><?php echo _('Email Address'); ?></th>
                <th><?php echo _('Enabled'); ?></th>
              </tr>
              <?php
                while ($row = $sth->fetch()) {
              ?>
              <tr>
                <td class="trash">
                  <a href="admingroupcontentdeletesubmit.php?group_id=<?php echo $_GET['group_id'];
					?>&member_id=<?php echo $row['member_id'];
					?>&localpart=<?php echo $grouplocalpart;
					?>">
                    <img class="trash"
                      title="Remove member <?php echo $row['realname']
                      . ' from group ' . $grouplocalpart; ?>"
                      src="images/trashcan.gif" alt="trashcan">
                  </a>
                </td>
                <td><?php echo $row['realname']; ?></td>
                <td><?php echo $row['localpart'].'@'.$_SESSION['domain']; ?></td>
                <td>
                  <?php
                    if($row['enabled']='1') {
                  ?>
                  <img class="check" src="images/check.gif">
                  <?php
                    }
                  ?>
                </td>
              </tr>
              <?php
                }#while
              ?>
            </table>
            <?php
              } else {
                print _('There are no members in this group');
              }
            ?>
          </td>
          </tr>
          <tr>
            <td colspan="2">&nbsp;</td>
          </tr>
          <form method="post" action="admingroupcontentaddsubmit.php"
            name="groupcontentadd">
          <tr>
            <td><?php echo _('Add Member'); ?></td>
            <td>
              <input name="group_id" type="hidden"
                value="<?php echo $_GET['group_id']; ?>" class="textfield">
              <input name="localpart" type="hidden"
                value="<?php echo $grouplocalpart; ?>" class="textfield">
              <select name="usertoadd">
                <option selected value=""></option>
                <?php
                  $query = "SELECT realname, localpart, user_id FROM users
                    WHERE enabled='1' AND domain_id=:domain_id AND type!='fail'
                    ORDER BY realname, username, type desc";
                  $sth = $dbh->prepare($query);
                  $sth->execute(array(':domain_id'=>$_SESSION['domain_id']));
                  while ($row = $sth->fetch()) {
                ?>
                  <option value="<?php echo $row['user_id'];
					?>"><?php echo $row['realname'];
					?> (<?php echo $row['localpart'].'@'.$_SESSION['domain']; ?>)</option>
                <?php
                  }
                ?>
              </select>
            </td>
          </tr>
          <tr>
            <td colspan="2" class="button">
              <input name="addmember" type="submit"
                value="<?php echo _('Submit'); ?>">
            </td>
          </tr>
        </form>
      </table>
		<?php
			# end of the block editing a group within the domain
		}
		?>
    </div>
  </body>
</html>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
