<?php
  include_once dirname(__FILE__) . "/config/variables.php";
  include_once dirname(__FILE__) . "/config/authpostmaster.php";
  include_once dirname(__FILE__) . "/config/functions.php";
  include_once dirname(__FILE__) . "/config/httpheaders.php";
?>
<html>
  <head>
    <title><?php echo _("Virtual Exim") . ": " . _("Manage Users"); ?></title>
    <link rel="stylesheet" href="style.css" type="text/css">
  </head>
  <body>
    <?php include dirname(__FILE__) . "/config/header.php"; ?>
    <div id="Menu">
      <a href="adminuseradd.php"><?php echo _("Add User"); ?></a>
      <?php
	$query = "SELECT count(users.user_id)
		  AS used, max_accounts
		  FROM domains,users
		  WHERE users.domain_id={$_SESSION['domain_id']}
		  AND domains.domain_id=users.domain_id
		  AND (users.type='local' OR users.type='piped')
		  GROUP BY max_accounts"; 
	$result = $db->query($query);
	$row = $result->fetchRow();
	if (($result->numRows()) && $row['max_accounts']) {
	  print "({$row['used']} of {$row['max_accounts']})";
	}
      ?>
      <br>
      <a href="admin.php"><?php echo _("Main Menu"); ?></a><br>
      <br><a href="logout.php"><?php echo _("Logout"); ?></a><br>
    </div>

    <div id="Content">
	<?php
		alpha_menu($alphausers)
	?>
    <table align="center">
	<tr><th>&nbsp;</th><th><?php echo _("User"); ?></th><th><?php echo _("Email address"); ?></th><th><?php echo _("Admin"); ?></th></tr>
	<?php
     if ($alphausers AND $letter != '') 
		$query = "SELECT user_id,localpart,realname,admin,enabled FROM users
			WHERE lower(localpart) LIKE lower('{$letter}%') AND
			domain_id={$_SESSION['domain_id']} AND (type='local' OR type='piped')
			ORDER BY realname";
     else 
		$query = "SELECT user_id,localpart,realname,admin,enabled FROM users
			WHERE domain_id={$_SESSION['domain_id']} AND (type='local' OR type='piped')
			ORDER BY realname";
	  $result = $db->query($query);
	  while ($row = $result->fetchRow()) {
	    print "\t<tr>";
	    print "<td class='trash'><a href=\"adminuserdelete.php?user_id={$row['user_id']}&localpart={$row['localpart']}\">";
	    print "<img style='border:0;width:10px;height:16px' title='Delete {$row['realname']}' src='images/trashcan.gif' alt='trashcan'></a></td>\n";
	    print "\t<td><a href=\"adminuserchange.php?user_id={$row['user_id']}&localpart={$row['localpart']}\" title='" . _("Click to modify") . " {$row['realname']}'>{$row['realname']}</a></td>\n";
	    print "\t<td>{$row['localpart']}@{$_SESSION['domain']}</td>\n";
	    print "\t<td class='check'>";
	    if ($row['admin'] == 1)
        {
            print   "<img style=\"border:0;width:13px;height:12px\" src=\"images/check.gif\" title=\"";
            printf (_("%s is an administrator") . "\">", $row['realname']);
        }
	    print "</td></tr>\n";
	  }
	?>
      </table>
    </div>
  </body>
</html>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
