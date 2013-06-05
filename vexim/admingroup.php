<?php
  include_once dirname(__FILE__) . '/config/variables.php';
  include_once dirname(__FILE__) . '/config/authpostmaster.php';
  include_once dirname(__FILE__) . '/config/functions.php';
?>
<html>
  <head>
    <title><?php echo _('Virtual Exim') . ': ' . _('List groups'); ?></title>
    <link rel="stylesheet" href="style.css" type="text/css">
  </head>
  <body>
    <?php include dirname(__FILE__) . '/config/header.php'; ?>
    <div id="Menu">
      <a href="admingroupadd.php"><?php echo _('Add Group'); ?></a>
      <br>
      <a href="admin.php"><?php echo _('Main Menu'); ?></a><br>
      <br><a href="logout.php"><?php echo _('Logout'); ?></a><br>
    </div>
    <div id="Content">
      <table align="center">
        <tr>
          <th>&nbsp;</th>
          <th><?php echo _('Email address'); ?></th>
          <th><?php echo _('Is public'); ?></th>
          <th><?php echo _('Enabled'); ?></th>
        </tr>
        <?php
          $query = "SELECT id, name, is_public, enabled FROM groups
            WHERE domain_id = '{$_SESSION['domain_id']}'
            ORDER BY NAME ASC";
          $result = $db->query($query);
          while ($row = $result->fetchRow()) {
        ?>
        <tr>
          <td class="trash">
            <a href="admingroupdelete.php?group_id=<?php echo $row['id']; ?>&localpart=<?php echo $row['name']; ?>">
            <img class='trash' title="<?php print _('Delete group') . $row['name']; ?>"
              src="images/trashcan.gif" alt="trashcan">
            </a>
          </td>
          <td>
            <a href="admingroupchange.php?group_id=<?php echo $row['id']; ?>"
              title="<?php print _('Click to modify ') . $row['name']; ?>">
            <?php echo $row['name'].'@'.$_SESSION['domain']; ?></a>
          </td>
          <td>
            <?php if ('Y' == $row['is_public']) { ?>
              <img class="check" src="images/check.gif"
                title="<?php print _('Anyone can write to') . ' '. $row['name']; ?>">
            <?php } ?>
          </td>
          <td>
            <?php if ('1' == $row['enabled']) { ?>
              <img class="check" src="images/check.gif"
                title="<?php print $row['name'] . _(' is enabled'); ?>">
            <?php } ?>
          </td>
        </tr>
        <?php
          }
        ?>
      </table>
    </div>
  </body>
</html>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
