<?
  include_once dirname(__FILE__) . "/config/variables.php";
  include_once dirname(__FILE__) . "/config/authpostmaster.php";
  include_once dirname(__FILE__) . "/config/functions.php";
?>
<html>
  <head>
    <title>Virtual Exim: <? echo _("List groups"); ?></title>
    <link rel="stylesheet" href="style.css" type="text/css">
  </head>
  <body>
    <? include dirname(__FILE__) . "/config/header.php"; ?>
    <div id="Menu">
      <a href="admingroupadd.php"><? echo _("Add Group"); ?></a>
      <br>
      <a href="admin.php"><? echo _("Main Menu"); ?></a><br>
      <br><a href="logout.php"><? echo _("Logout"); ?></a><br>
    </div>
    <?
        if (isset($_GET['group_deleted'])) {
            ?><div id="status">Group <?=$_GET['group_deleted']?> has been successfully deleted</div><?
        } else if (isset($_GET['group_added'])) {
            ?><div id="status">Group <?=$_GET['group_added']?> has been successfully added</div><?
        } else if (isset($_GET['group_faildeleted'])) {
            ?><div id="status">Group <?=$_GET['group_faildeleted']?> was not deleted</div><?
        } else if (isset($_GET['group_failadded'])) {
            ?><div id="status">Group <?=$_GET['group_failadded']?> failed to be added</div><?
        }
    ?>
    <div id="Content">
    <table align="center">
      <tr><th>&nbsp;</th><th><? echo _("Email address"); ?></th><th>Is public</th><th>Enabled</th></tr>
    <?
        $query = "select id, name, is_public, enabled from groups ";
        $query .= " where domain_id = {$_SESSION['domain_id']} order by name asc";
        $result = $db->query($query);
        while ($row = $result->fetchRow()) {
            ?>
            <tr>
                <td class="trash">
                    <a href="admingroupdelete.php?group_id=<?=$row['id']?>&localpart=<?=$row['name']?>">
                    <img style="border:0;width:10px;height:16px" title="Delete group <?=$row['name']?>" 
                        src="images/trashcan.gif" alt="trashcan"></a>
                </td>
                <td><a href="admingroupchange.php?group_id=<?=$row['id']?>" 
                    title="<?=_("Click to modify")." ".$row['name']?>">
                    <?=$row['name'].'@'.$_SESSION['domain']?></a>
                </td>
                <td>
                    <? if ($row['is_public']='Y') { ?>
                        <img style="border:0;width:13px;height:12px" src="images/check.gif" title="Anyone can write to <?=$row['name']?>"> 
                    <? } ?>
                </td>
                <td>
                    <? if ($row['enabled']='1') { ?>
                        <img style="border:0;width:13px;height:12px" src="images/check.gif" title="<?=$row['name']?> is enabled"> 
                    <? } ?>
                </td>
            </tr>
            <?
        }
    ?>
    </table>
    </div>
  </body>
</html>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
