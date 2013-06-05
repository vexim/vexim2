<?php
  include_once dirname(__FILE__) . '/config/variables.php';
  include_once dirname(__FILE__) . '/config/authpostmaster.php';
  include_once dirname(__FILE__) . "/config/functions.php";
?>
<?php
  $query = "SELECT * FROM groups WHERE id='{$_GET['group_id']}' AND domain_id='{$_SESSION['domain_id']}'";
  $result = $db->query($query);
  $row = $result->fetchRow();
  $grouplocalpart = $row['name'];
?>
<html>
  <head>
    <title><?php echo _('Virtual Exim') . ': ' . _('Edit group'); ?></title>
    <link rel="stylesheet" href="style.css" type="text/css">
  </head>
  <body onLoad="document.groupchange.realname.focus()">
    <?php include dirname(__FILE__) . '/config/header.php'; ?>
    <div id="menu">
      <a href="admingroup.php"><?php echo _('Manage Groups'); ?></a><br>
      <a href="admingroupadd.php"><?php echo _('Add Group'); ?></a></br>
      <a href="admin.php"><?php echo _('Main Menu'); ?></a><br>
      <br><a href="logout.php"><?php echo _('Logout'); ?></a><br>
    </div>
    <div id="Forms">
	<?php 
		# ensure this page can only be used to view/edit aliases that already exist for the domain of the admin account
		if (!$result->numRows()) {			
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
              value="<?php echo $row['name']; ?>"class="textfield">@
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
              $query = "select u.realname, u.username, u.enabled, c.member_id
                from users u, group_contents c
                where u.user_id = c.member_id and c.group_id = '{$_GET['group_id']}'
                order by u.enabled desc, u.realname asc";
              $result = $db->query($query);
              if ($result->numRows()) {
            ?>
            <table align="center">
              <tr>
                <th>&nbsp;</th>
                <th><?php echo _('Real name'); ?></th>
                <th><?php echo _('Email Address'); ?></th>
                <th><?php echo _('Enabled'); ?></th>
              </tr>
              <?php
                while ($row = $result->fetchRow()) {
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
                <td><?php echo $row['username']; ?></td>
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
                  $query = "select realname, username, user_id from users
                    where enabled = '1' and domain_id = '{$_SESSION['domain_id']}' and type != 'fail'
                    order by realname, username, type desc";
                  $result = $db->query($query);
                  while ($row = $result->fetchRow()) {
                ?>
                  <option value="<?php echo $row['user_id']; 
					?>"><?php echo $row['realname']; 
					?>(<?php echo $row['username']; ?>)</option>
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
