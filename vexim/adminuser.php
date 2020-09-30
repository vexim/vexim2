<?php
  include_once dirname(__FILE__) . '/config/variables.php';
  include_once dirname(__FILE__) . '/config/authpostmaster.php';
  include_once dirname(__FILE__) . '/config/functions.php';
  include_once dirname(__FILE__) . '/config/httpheaders.php';

  if (isset($_GET['LETTER'])) {
    $letter = strtolower($_GET['LETTER']);
  } else {
    $letter = '';
  }
  if (!isset($_POST['searchfor'])) {
    $_POST['searchfor'] = '';
  }
  if (!isset($_POST['field']) || ($_POST['field'] != 'localpart')) {
    $_POST['field'] = 'realname';
  }
?>
<!DOCTYPE html>
<html>
  <head>
    <title><?php echo _('Virtual Exim') . ': ' . _('Manage Users'); ?></title>
    <link rel="stylesheet" href="style.css" type="text/css">
  </head>
  <body>
    <?php include dirname(__FILE__) . '/config/header.php'; ?>
    <div id="Menu">
      <a href="adminuseradd.php"><?php echo _('Add User'); ?></a>
      <?php
        $query = "SELECT count(users.user_id)
          AS used, max_accounts
          FROM domains,users
          WHERE users.domain_id=:domain_id
          AND domains.domain_id=users.domain_id
          AND (users.type='local' OR users.type='piped')
          GROUP BY max_accounts";
        $sth = $dbh->prepare($query);
        $sth->execute(array(':domain_id'=>$_SESSION['domain_id']));
        $row = $sth->fetch();
        if (($sth->rowCount()) && $row['max_accounts']) {
          printf(_("(%d of %d)"), $row['used'], $row['max_accounts']);
        }
      ?>
      <br>
      <a href="admin.php"><?php echo _('Main Menu'); ?></a><br>
      <br><a href="logout.php"><?php echo _('Logout'); ?></a><br>
    </div>
    <div id="Content">
      <?php
        alpha_menu($alphausers)
      ?>
      <form name="search" method="post" action="adminuser.php">
        <?php echo _('Search'); ?>:
        <input type="text" size="20" name="searchfor"
          value="<?php echo $_POST['searchfor']; ?>" class="textfield">
        <?php echo _('in'); ?>
        <select name="field" class="textfield">
          <option value="realname" <?php if ($_POST['field'] == 'realname') {
            echo 'selected="selected"';
          } ?>><?php echo _('User'); ?></option>
          <option value="localpart" <?php if ($_POST['field'] == 'localpart') {
            echo "selected=\"selected\"";
          } ?>><?php echo _('Email address'); ?></option>
        </select>
        <input type="submit" name="search" value="<?php echo _('search'); ?>">
      </form>
      <table>
        <tr>
          <th>&nbsp;</th>
          <th><?php echo _('User'); ?></th>
          <th><?php echo _('Email address'); ?></th>
          <th><?php echo _('Admin'); ?></th>
        </tr>
        <?php
        $query = "SELECT user_id, localpart, realname, admin, enabled
          FROM users
          WHERE domain_id=:domain_id
          AND  (type = 'local' OR type= 'piped')";
        $queryParams=array(':domain_id'=>$_SESSION['domain_id']);
        if ($alphausers AND $letter != '') {
          $query .= " AND lower(localpart) LIKE lower(:letter)";
          $queryParams[':letter'] = $letter.'%';
        } elseif ($_POST['searchfor'] != '') {
          $query .= ' AND ' . $dbh->quote($_POST['field']) .  ' LIKE :searchfor';
          $queryParams[':searchfor'] = '%'.$_POST['searchfor'].'%';
        }
        $query .= ' ORDER BY realname, localpart';
        $sth = $dbh->prepare($query);
        $sth->execute($queryParams);
        while ($row = $sth->fetch()) {
          if($row['enabled']==="0") print '<tr class="disabled">'; else print '<tr>';
          print '<td class="trash"><a href="adminuserdelete.php?user_id='
            . $row['user_id']
            . '&amp;localpart='
            . $row['localpart']
            . '">';
          print '<img class="trash" title="Delete '
            . $row['realname']
            . '" src="images/trashcan.gif" alt="trashcan"></a></td>';
          print '<td><a href="adminuserchange.php?user_id=' . $row['user_id']
            . '&amp;localpart=' . $row['localpart']
            . '" title="' . _('Click to modify')
            . ' '
            . $row['realname']
            . '">'
            . $row['realname']
            . '</a></td>';
          print '<td><a href="adminuserchange.php?user_id=' . $row['user_id']
            . '&amp;localpart=' . $row['localpart']
            . '" title="' . _('Click to modify')
            . ' '
            . $row['realname']
            . '">'
            . $row['localpart'] .'@'. $_SESSION['domain']
            . '</a></td>';
          print '<td class="check">';
          if ($row['admin'] == 1) {
            print  '<img class="check" src="images/check.gif" title="'
            . $row['realname']
            . _(' is an administrator')
            . '">';
          }
          print "</td></tr>\n";
        }
        ?>
      </table>
    </div>
  </body>
</html>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
