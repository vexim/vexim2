<?php
  include_once dirname(__FILE__) . '/config/variables.php';
  include_once dirname(__FILE__) . '/config/authsite.php';
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
?>
<!DOCTYPE html>
<html>
  <head>
    <title><?php echo _('Virtual Exim') . ': ' . _('Manage Sites'); ?></title>
    <link rel="stylesheet" href="style.css" type="text/css">
  </head>
  <body>
  <?php include dirname(__FILE__) . '/config/header.php'; ?>
  <div id="Menu">
    <a href="siteadd.php?type=alias"><?php echo _('Add alias domain'); ?></a><br>
    <a href="siteadd.php?type=local"><?php echo _('Add local domain'); ?></a><br>
    <a href="siteadd.php?type=relay"><?php echo _('Add relay domain'); ?></a><br>
    <a href='sitepassword.php'><?php echo _('Site Password'); ?></a><br>
    <br><a href="logout.php"><?php echo _('Logout'); ?></a><br>
  </div>
  <div id="Content">
    <?php
      alpha_menu($alphadomains);
    ?>
    <form name="search" method="post" action="site.php">
      <?php
        echo _('Search');
      ?>:
      <input type="text" size="20" name="searchfor"
        value="<?php echo $_POST['searchfor']; ?>" class="textfield">
      <input type="submit" name="search"
        value="<?php echo _('search'); ?>">
    </form>
    <table class="zebra">
      <tr>
        <th></th>
        <th><?php echo _('Local domains'); ?></th>
        <th><?php echo _('Admin account'); ?></th>
        <th><?php echo _('Total admins'); ?></th>
      </tr>
      <?php
        $query = "SELECT MIN(localpart) AS localpart, domain,
          domains.domain_id, count(*) AS count, domains.enabled AS enabled
          FROM   users, domains
          WHERE  users.domain_id = domains.domain_id
          AND    domain !='admin' AND admin=1";
        $queryParams = array();
        if ($alphadomains AND $letter != '') {
          $query .= " AND lower(domain) LIKE lower(:letter)";
          $queryParams[':letter'] = $letter.'%';
        } elseif ($_POST['searchfor'] != '') {
          $query .= " AND domain LIKE :searchfor";
          $queryParams[':searchfor'] = '%'.$_POST['searchfor'].'%';
        }
        $query .= " GROUP BY domains.domain, domains.domain_id ORDER BY domain";
        $sth = $dbh->prepare($query);
        $sth->execute($queryParams);
        while ($row = $sth->fetch()) {
          if($row['enabled'] === 0) print '<tr class="disabled">'; else print '<tr>';
      ?>
              <td>
                <a href="sitedelete.php?domain_id=<?php
                  echo $row['domain_id']; ?>&amp;domain=<?php
                  echo $row['domain']; ?>">
                  <img class="trash" title="Delete <?php $row['domain']; ?>"
                    src="images/trashcan.gif" alt="trashcan">
                </a>
              </td>
              <td>
                <a href="sitechange.php?domain_id=<?php
                  echo $row['domain_id']; ?>&amp;domain=<?php
                  echo $row['domain']; ?>"><?php echo $row['domain']; ?></a>
              </td>
          <?php
            if ($row['count'] == 1) {
          ?>
              <td><?php echo $row['localpart'] . '@' . $row['domain']; ?></td>
          <?php
            } else {
          ?>
              <td><?php echo _('Multiple admins'); ?></td>
          <?php
            }
          ?>
            <td><?php echo $row['count']; ?></td>
          <?php
            if (isset($siteadminManageDomains) && $siteadminManageDomains) {
          ?>
            <td><a href="admin.php?manage_domain_id=<?php echo $row['domain_id']; ?>">manage</a></td>
          <?php
            }
          ?>
           </tr>
          <?php
        }
      ?>
      <tr><td></td></tr>
      <tr>
        <td colspan="3">
          <b><?php echo _('WARNING') ?>:</b>
          <?php
            echo _('Deleting a domain will delete all user accounts in that
              domain permanently!');
          ?>
        </td>
      </tr>
      <tr><td></td></tr>
      <tr>
        <th></th>
        <th><?php echo _('Relay domains'); ?></th>
      </tr>
      <?php
        $query = "SELECT domain,domain_id FROM domains
        WHERE domain !='admin'
        AND type='relay' ORDER BY domain";
        $sth = $dbh->query($query);
        while ($row = $sth->fetch()) {
      ?>
            <tr>
              <td>
                <a href="sitedelete.php?domain_id=<?php
                  echo $row['domain_id']; ?>&amp;domain=<?php
                  echo $row['domain']; ?>&amp;type=relay">
                  <img class="trash" title="<?php echo _('Delete') .
                    $row['domain']; ?>" src="images/trashcan.gif" alt="trashcan">
                </a>
              </td>
              <td><?php echo $row['domain']; ?></td>
            </tr>
      <?php
        }
      ?>
      <tr>
        <th></th>
        <th><?php echo _('Aliased domains'); ?></th>
      </tr>
      <?php
        $query = "SELECT alias,domain,domains.domain_id AS domain_id FROM domainalias,domains
          WHERE domainalias.domain_id = domains.domain_id";
        $sth = $dbh->query($query);
        while ($row = $sth->fetch()) {
      ?>
            <tr>
              <td>
                <a href="sitedelete.php?domain_id=<?php
                  echo $row['domain_id']; ?>&amp;domain=<?php
                  echo $row['alias']; ?>&amp;type=alias">
                  <img class="trash" title="<?php echo _('Delete')
                    . $row['alias']; ?>" src="images/trashcan.gif" alt="trashcan">
                </a>
              </td>
            <td colspan="3">
              <?php echo $row['alias'] . ' → ' . $row['domain']; ?>
            </td>
          </tr>
      <?php
        }

		# display status of $AllowUserLogin
		echo '<tr><td colspan="3">&nbsp;</td></tr>';
		if($AllowUserLogin){
		    echo '<tr><td colspan="3">'._('Standard user accounts are currently able to login and change their own personal details').'.</td></tr>';
		}else{
			echo '<tr><td colspan="3">'._('The system is currently configured to prevent standard users logging in to change their own personal details.').'</td></tr>';
		}
      ?>
    </table>
    </div>
  </body>
</html>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
