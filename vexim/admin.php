<?php
  include_once dirname(__FILE__) . '/config/variables.php';
  include_once dirname(__FILE__) . '/config/authpostmaster.php';
  include_once dirname(__FILE__) . '/config/functions.php';
  include_once dirname(__FILE__) . '/config/httpheaders.php';
?>
<html>
  <head>
    <title><?php echo _('Virtual Exim'); ?></title>
    <link rel="stylesheet" href="style.css" type="text/css">
  </head>
  <body>
    <?php include dirname(__FILE__) . '/config/header.php'; ?>
    <div id="Centered">
      <table align="center">
        <tr>
          <td>
            <a href="adminuser.php">
              <?php
                echo _('Add, delete and manage POP/IMAP accounts');
              ?>
            </a>
          </td>
        </tr>
        <tr>
          <td>
            <a href="adminalias.php">
              <?php
                echo _('Add, delete and manage aliases, forwards and a Catchall');
              ?>
            </a>
          </td>
        </tr>
        <tr>
          <td>
            <a href="admingroup.php">
              <?php echo _('Add, delete and manage groups'); ?>
            </a>
          </td>
        </tr>
        <tr>
          <td>
            <a href="adminfail.php">
              <?php echo _('Add, delete and manage :fail:\'s'); ?>
            </a>
          </td>
        </tr>
          <?php
            if ($mailmanroot != "") {
              print '<tr><td><a href="adminlists.php">' . _('Manage mailing lists') . '</a></td></tr>';
            }
          ?>
        <tr>
          <td style="padding-top:1em">
            <a href="logout.php"><?php echo _('Logout'); ?></a>
          </td>
        </tr>
        <tr><td></td></tr>
        <tr><td></td></tr>
        <?php
          $query = "SELECT alias,domain FROM domainalias,domains 
            WHERE domainalias.domain_id = {$_SESSION['domain_id']}
            AND domains.domain_id = domainalias.domain_id";
          $result = $db->query($query);
          if ($result->numRows()) {
            print '<tr><td>Domain data:</td></tr>';
            while ($row = $result->fetchRow()) {
              print '<tr><td>';
              print "{$row['alias']} is an alias of {$_SESSION['domain']}";
              print '</td></tr>';
            }
          }
        ?>
      </table>
    </div>
  </body>
</html>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
