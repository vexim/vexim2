<?php
  include_once dirname(__FILE__) . '/config/variables.php';
  include_once dirname(__FILE__) . '/config/authpostmaster.php';
  include_once dirname(__FILE__) . "/config/functions.php";
  include_once dirname(__FILE__) . '/config/httpheaders.php';
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
      <a href="adminfailadd.php"><?php echo _('Add Fail'); ?></a><br>
      <a href="admin.php"><?php echo _('Main Menu'); ?></a><br>
      <br><a href="logout.php"><?php echo _('Logout'); ?></a><br>
    </div>
    <div id="Content">
    <table align="center">
      <tr>
        <th>&nbsp;</th>
        <th><?php echo _('Failed Address'); ?>..</th>
      </tr>
      <?php
        $query = "SELECT user_id,localpart FROM users
          WHERE domain_id=:domain_id
          AND users.type='fail'
          ORDER BY localpart;";
        $sth = $dbh->prepare($query);
        $sth->execute(array(':domain_id'=>$_SESSION['domain_id']));
        if ($sth->rowCount()) {
          while ($row = $sth->fetch()) {
            print '<tr>'
              . '<td align="center">'
              . '<a href="adminfaildelete.php?user_id='
              . $row['user_id']
              . '&amp;localpart='
              . $row['localpart']
              . '"><img class="trash" src="images/trashcan.gif" title="'
              . _('Delete fail ')
              . $row['localpart']
              . '"></a></td>';
            print '<td>'
              . '<a href="adminfailchange.php?user_id='
              . $row['user_id']
              . '">'
              . $row['localpart']
              . '@'
              . $_SESSION['domain']
              . '</a></td>';
            print '</tr>';
          }
        }
      ?>
    </table>
    </div>
  </body>
</html>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
