<?php
  include_once dirname(__FILE__) . "/config/variables.php";
  include_once dirname(__FILE__) . "/config/authsite.php";
  include_once dirname(__FILE__) . "/config/functions.php";
  include_once dirname(__FILE__) . "/config/httpheaders.php";

  if (isset($_GET['LETTER']))
  {
      $letter = strtolower($_GET['LETTER']);
  }
  else
  {
      $letter = '';
  }
  if (!isset($_POST['searchfor'])) 
  {
      $_POST['searchfor'] = '';
  }
?>
<html>
  <head>
    <title><?php echo _("Virtual Exim") . ": " . _("Manage Sites"); ?></title>
    <link rel="stylesheet" href="style.css" type="text/css">
  </head>
  <?php include dirname(__FILE__) . "/config/header.php"; ?>
  <div id='menu'>
    <a href="siteadd.php?type=alias"><?php echo _("Add alias domain"); ?></a><br>
    <a href="siteadd.php?type=local"><?php echo _("Add local domain"); ?></a><br>
    <a href="siteadd.php?type=relay"><?php echo _("Add relay domain"); ?></a><br>
    <a href='sitepassword.php'><?php echo _("Site Password"); ?></a><br>
    <br><a href="logout.php"><?php echo _("Logout"); ?></a><br>
  </div>
  <div id='Content'>
	<?php
		alpha_menu($alphadomains);
	?>
    <form name="search" method="post" action="site.php">
    <?php echo _("Search"); ?>:
    <input type="text" size="20" name="searchfor" value="<?php echo $_POST['searchfor']; ?>" class="textfield" />
    <input type="submit" name="search" value="<?php echo _("search"); ?>" />
    </form>
	<table>
      <tr>
      	<th></th>
	<th><?php echo _("Local domains"); ?></th>
	<th><?php echo _("Admin account"); ?></th>
	<th><?php echo _("Total admins"); ?></th>
      </tr>
      <?php
        $query = "SELECT localpart, domain, domains.domain_id, 
                         count(*) AS count    
                  FROM   users, domains
                  WHERE  users.domain_id = domains.domain_id
                  AND    domain !='admin' AND admin=1";
        if ($alphadomains AND $letter != '') 
            $query .= " AND lower(domain) LIKE lower('$letter%')";
        elseif ($_POST['searchfor'] != '')
            $query .= " AND domain LIKE '%" . $_POST['searchfor'] . "%'";
        $query .= " GROUP BY domain ORDER BY domain";
  	$result = $db->query($query);
	if ($result->numRows()) {
	  while ($row = $result->fetchRow()) {
	    print "<tr>";
	    print "\t<td><a href=\"sitedelete.php?domain_id={$row['domain_id']}&domain={$row['domain']}\">";
	    print "<img style='border:0;width:10px;height:16px' title='Delete {$row['domain']}' src='images/trashcan.gif' alt='trashcan'></a></td>\n";
	    print "\t<td><a href=\"sitechange.php?domain_id={$row['domain_id']}&domain={$row['domain']}\">{$row['domain']}</a></td>\n";
	    print "\t<td>{$row['localpart']}@{$row['domain']}</td>\n";
	    print "\t<td>{$row['count']}</td>\n";
	    print "</tr>\n";
	  }
	}
      ?>
      <tr><td></td></tr>
      <tr><td colspan="3"><b><?php echo _("WARNING") ?>:</b> <?php echo _("Deleting a domain will delete all user accounts in that domain permanently!"); ?></td></tr>
      <tr><td></td></tr>
      <tr>
	<th></th>
	<th><?php echo _("Relay domains"); ?></th>
      </tr>
      <?php
	$query = "SELECT domain,domain_id FROM domains WHERE domain !='admin' AND type='relay' ORDER BY domain";
	$result = $db->query($query);
	if ($result->numRows()) {
	  while ($row = $result->fetchRow()) {
	    print "<tr>";
	    print "\t<td><a href=\"sitedelete.php?domain_id={$row['domain_id']}&domain={$row['domain']}&type=relay\">";
	    print "<img style=\"border:0;width:10px;height:16px\" title=\"" . _("Delete %s", $row['domain']) . "\" src=\"images/trashcan.gif\" alt=\"trashcan\"></a></td>\n";
	    print "\t<td>{$row['domain']}</td>\n";
	    print "</tr>\n";
	  }
	}
      ?>
      <tr>
        <th></th>
	<th><?php echo _("Aliased domains"); ?></th>
      </tr>
      <?php
        $query = "SELECT alias,domain FROM domainalias,domains WHERE domainalias.domain_id = domains.domain_id";
        $result = $db->query($query);
	if ($result->numRows()) {
	  while ($row = $result->fetchRow()) {
	    print "<tr>";
	    print "\t<td><a href=\"sitedelete.php?domain_id={$row['domain_id']}&domain={$row['alias']}&type=alias\">";
	    print "<img style=\"border:0;width:10px;height:16px\" title=\"" . _("Delete %s", $row['alias']) . "\" src=\"images/trashcan.gif\" alt=\"trashcan\"></a></td>\n";
	    print "\t<td colspan=\"3\">{$row['alias']} -> {$row['domain']}</td>\n";
	    print "</tr>\n";
	  }
	}
      ?>
    </table>
    </div>
  </body>
</html>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
