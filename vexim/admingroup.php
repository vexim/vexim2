<?php
  include_once dirname(__FILE__) . "/config/variables.php";
  include_once dirname(__FILE__) . "/config/authpostmaster.php";
  include_once dirname(__FILE__) . "/config/functions.php";
?>
<html>
  <head>
    <title><?php echo _("Virtual Exim") . ": " . _("List groups"); ?></title>
    <link rel="stylesheet" href="style.css" type="text/css">
  </head>
  <body>
    <?php include dirname(__FILE__) . "/config/header.php"; ?>
    <div id="Menu">
      <a href="admingroupadd.php"><?php echo _("Add Group"); ?></a>
      <br>
      <a href="admin.php"><?php echo _("Main Menu"); ?></a><br>
      <br><a href="logout.php"><?php echo _("Logout"); ?></a><br>
    </div>
    <?php
      if (isset($_GET['group_deleted'])) {
        printf ("<div id=\"status\">" . _("Group %s has been successfully deleted") . "</div>", $_GET['group_deleted']);
      } else if (isset($_GET['group_added'])) {
        printf ("<div id=\"status\">" . _("Group %s has been successfully added") . "</div>", $_GET['group_added']);
      } else if (isset($_GET['group_faildeleted'])) {
        printf ("<div id=\"status\">" . _("Group %s was not deleted") . "</div>", $_GET['group_faildeleted']);
      } else if (isset($_GET['group_failadded'])) {
        printf ("<div id=\"status\">" . _("Group %s failed to be added") . "</div>",  $_GET['group_failadded']);
      }
    ?>
    <div id="Content">
    <table align="center">
      <tr><th>&nbsp;</th><th><?php echo _("Email address"); ?></th><th><?php echo _("Is public"); ?></th><th><?php echo _("Enabled"); ?></th></tr>
    <?php
      $query = "SELECT id, name, is_public, enabled FROM groups ";
      $query .= " WHERE domain_id = {$_SESSION['domain_id']} ORDER BY NAME ASC";
      $result = $db->query($query);
      while ($row = $result->fetchRow()) {
    ?>
    <tr>
      <td class="trash">
        <a href="admingroupdelete.php?group_id=<?php echo $row['id']; ?>&localpart=<?php echo $row['name']; ?>">
        <img style="border:0;width:10px;height:16px" title="<?php printf (_("Delete group %s"), $row['name']); ?>" src="images/trashcan.gif" alt="trashcan"></a>
      </td>
      <td><a href="admingroupchange.php?group_id=<?php echo $row['id']; ?>" title="<?php printf (_("Click to modify %s"), $row['name']); ?>">
        <?php echo $row['name'].'@'.$_SESSION['domain']; ?></a>
      </td>
      <td>
        <?php if ($row['is_public']='Y') { ?>
          <img style="border:0;width:13px;height:12px" src="images/check.gif" title="<?php printf (_("Anyone can write to %s"), $row['name']); ?>"> 
        <?php } ?>
      </td>
      <td>
        <?php if ($row['enabled']='1') { ?>
          <img style="border:0;width:13px;height:12px" src="images/check.gif" title="<?php printf (_("%s is enabled"), $row['name']); ?>"> 
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
