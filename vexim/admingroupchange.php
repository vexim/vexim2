<?
  include_once dirname(__FILE__) . "/config/variables.php";
  include_once dirname(__FILE__) . "/config/authpostmaster.php";
?>
<?
  $query = "SELECT * FROM groups WHERE id={$_GET['group_id']}";
  $result = $db->query($query);
  $row = $result->fetchRow();
  $localpart = $row['name'];
?>
<html>
  <head>
    <title>Virtual Exim: Edit group</title>
    <link rel="stylesheet" href="style.css" type="text/css">
  </head>
  <body onLoad="document.groupchange.realname.focus()">
    <? include dirname(__FILE__) . "/config/header.php"; ?>
    <div id="menu">
      <a href="admingroup.php"><? echo _("Manage Groups"); ?></a><br>
      <a href="admingroupadd.php"><? echo _("Add Group"); ?></a></br>
      <a href="admin.php"><? echo _("Main Menu"); ?></a><br>
      <br><a href="logout.php"><? echo _("Logout"); ?></a><br>
    </div>
    <div id="Forms">
      <table align="center">
        <form name="groupchange" method="post" action="admingroupchangesubmit.php">
	<tr>
            <td><?=_("Group Address")?>:</td>
            <td>
                <input name="localpart" type="text" value="<?=$row['name']?>"class="textfield">@<?=$_SESSION['domain']?>
                <input name="group_id" type="hidden" value="<?=$_GET['group_id']?>" class="textfield">
            </td>
        </tr>
	<tr>
            <td><?=_("Is public")?></td>
            <td>
                <input name="is_public" type="checkbox" <?= $row['is_public']=='Y' ? 'checked' : '' ?> class="textfield">
            </td>
        </tr>
	<tr>
            <td><?=_("Enabled")?></td>
            <td>
                <input name="enabled" type="checkbox" <?= $row['enabled']=='1' ? 'checked' : '' ?> class="textfield">
            </td>
        </tr>
	<tr>
            <td colspan="2" class="button"><input name="editgroup" type="submit" value="Submit"></td>
        </tr>
        </form>
        <tr><td colspan="2">&nbsp;</td></tr>
        <tr>
            <td colspan="2"> 
                <?
                $query = "select u.realname, u.username, u.enabled, c.member_id ";
                $query .= "from users u, group_contents c ";
                $query .= "where u.user_id = c.member_id and c.group_id = {$_GET['group_id']} ";
                $query .= "order by u.enabled desc, u.realname asc";
                $result = $db->query($query);
                if ($result->numRows()) {
                    ?>
                    <table align="center">
                        <tr>
                            <th>&nbsp;</th>
                            <th><?=_("Real name")?></th>
                            <th><?=_("Email Address")?></th>
                            <th><?=_("Enabled")?></th>
                        </tr>
                        <?
                            while ($row = $result->fetchRow()) {
                                ?>
                                <tr>
                                    <td class="trash">
                                        <a href="admingroupcontentdeletesubmit.php?group_id=<?=$_GET['group_id']?>&member_id=<?=$row['member_id']?>&localpart=<?=$localpart?>">
                                        <img style="border:0;width:10px;height:16px" title="Remove member <?=$row['realname']?> from group <?=$localpart?>" src="images/trashcan.gif" alt="trashcan">
                                        </a>
                                    </td>
                                    <td><?=$row['realname']?></td>
                                    <td><?=$row['username']?></td>
                                    <td><? if($row['enabled']='1') { ?><img style="border:0;width:13px;height:12px" src="images/check.gif"><? } ?></td>
                                </tr>
                                <?
                            }#while
                        ?>
                    </table>
                    <?
                } else {
                    print _("There is no member in this group");
                }
                ?>
            </td>
        </tr>
        <tr><td colspan="2">&nbsp;</td></tr>
        <form method="post" action="admingroupcontentaddsubmit.php" name="groupcontentadd">
	<tr>
            <td><?=_("Add Member")?></td>
            <td>
                <input name="group_id" type="hidden" value="<?=$_GET['group_id']?>" class="textfield">
                <input name="localpart" type="hidden" value="<?=$localpart?>" class="textfield">
                <select name="usertoadd">
                <option selected value=""> </option>
                <?
                    $query = "select realname, username, user_id from users ";
                    $query .= "where enabled = '1' and domain_id = {$_SESSION['domain_id']} and type != 'fail' ";
                    $query .= "order by realname, username, type desc";
                    $result = $db->query($query);
                    while ($row = $result->fetchRow()) {
                        ?>
                        <option value="<?=$row['user_id']?>"><?=$row['realname']?> (<?=$row['username']?>)</option>
                        <? 
                    }
                ?>
                </select>
            </td>
        </tr>
	<tr>
            <td colspan="2" class="button"><input name="addmember" type="submit" value="Submit"></td>
        </tr>
        </form>
      </table>
    </div>
  </body>
</html>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
