<?
  include_once dirname(__FILE__) . "/config/variables.php";
  include_once dirname(__FILE__) . "/config/authsite.php";
  include_once dirname(__FILE__) . "/config/functions.php";
?>
<html>
  <head>
    <title>Virtual Exim: Manage Sites</title>
    <link rel="stylesheet" href="style.css" type="text/css">
  </head>
  <body onLoad="document.sitepassword.password.focus()">
  <? include dirname(__FILE__) . "/config/header.php"; ?>
  <div id='menu'>
    <a href="siteadd.php?type=local">Add local domain</a><br>
    <a href="siteadd.php?type=relay">Add relay domain</a><br>
    <a href='sitepassword.php' title='Change site password'>Site Password</a><br>
    <br><a href="logout.php">Logout</a><br>
  </div>
    <? if (isset($_GET['deleted'])) {
	print "<div id='status'>Domain '{$_GET['deleted']}' has been successfully deleted</div>\n";
      } else if (isset($_GET['added'])) {
	print "<div id='status'>{$_GET['type']} domain '{$_GET['added']}' has been successfully added</div>\n";
      } else if (isset($_GET['updated'])) {
	print "<div id='status'>Domain '{$_GET['updated']}' has been successfully updated</div>\n";
      } else if (isset($_GET['sitepass'])) {
	print "<div id='status'>Site Admin password has been successfully updated</div>\n";
      } else if (isset($_GET['failadded'])) {
	print "<div id='Status'>Domain '{$_GET['failadded']}' could not be added</div>\n";
      } else if (isset($_GET['failaddedusrerr'])) {
	print "<div id='Status'>Domain '{$_GET['failadded']}' could not be added.<br>\n";
	print "There was a problem adding the domain to the domains table.</div>\n";
      } else if (isset($_GET['failaddedusrerr'])) {
	print "<div id='Status'>Domain '{$_GET['failadded']}' could not be added.<br>\n";
	print "There was a problem adding the admin account.</div>\n";
      } else if (isset($_GET['failaddedpassmismatch'])) {
	print "<div id='Status'>Domain '{$_GET['failaddedpassmismatch']}' could not be added.<br>\n";
	print "The passwords were blank, or did not match.</div>\n";
      } else if (isset($_GET['failupdated'])) {
	print "<div id='Status'>Domain '{$_GET['failupdated']}' could not be updated</div>\n";
      } else if (isset($_GET['faildelete'])) {
	print "<div id='Status'>Domain '{$_GET['failupdate']}' could not be deleted</div>\n";
      } else if (isset($_GET['canceldelete'])) {
	print "<div id='Status'>Domain '{$_GET['canceldelete']}' delete cancelled</div>\n";
      } else if (isset($_GET['badname'])) {
	print "<div id='Status'>Domain '{$_GET['badname']}' contains invalid characters</div>\n";
      } else if (isset($_GET['badpass'])) {
	print "<div id='Status'>{$_GET['badpass']} password could not be set.<br>\n";
	print "Your passwords were blank, do not match, or contain an illegal characters: ' \" ` or ;</div>\n";
      }
    ?>
  <div id='Content'>
	<?
		alpha_menu($alphadomains);
	?>
	<table align="center">
      <tr>
      	<th></th>
	<th>Local domains</th>
	<th>Admin account</th>
      </tr>
      <?
     if ($alphadomains AND $letter != '') 
		$query = "SELECT localpart,domain,domains.domain_id FROM users,domains
			WHERE users.domain_id = domains.domain_id
			AND domain !='admin'
			AND admin=1 AND domain LIKE '$letter%' ORDER BY domain";
     else 
		$query = "SELECT localpart,domain,domains.domain_id FROM users,domains
			WHERE users.domain_id = domains.domain_id
			AND domain !='admin' AND admin=1 ORDER BY domain";
  	$result = $db->query($query);
	while ($row = $result->fetchRow()) {
	  print "<tr>";
	  print "\t<td><a href=\"sitedelete.php?domain_id={$row['domain_id']}\">";
	  print "<img style='border:0;width:10px;height:16px' title='Delete {$row['domain']}' src='images/trashcan.gif' alt='trashcan'></a></td>\n";
	  print "\t<td><a href=\"sitechange.php?domain_id={$row['domain_id']}\">{$row['domain']}</a></td>\n";
	  print "\t<td>{$row['localpart']}@{$row['domain']}</td>\n";
	  print "</tr>\n";
	}
      ?>
      <tr><td></td></tr>
      <tr><td colspan="3"><b>WARNING:</b> Deleting a domain will delete all user accounts in that domain permanently!</td></tr>
      <tr><td></td></tr>
      <tr>
        <th></th>
	<th>Relay domains</th>
      </tr>
      <?
        $query = "SELECT domain,domain_id FROM domains WHERE domain !='admin' AND type='relay' ORDER BY domain";
        $result = $db->query($query);
        while ($row = $result->fetchRow()) {
          print "<tr>";
          print "\t<td><a href=\"sitedelete.php?domain_id={$row['domain_id']}\">";
          print "<img style='border:0;width:10px;height:16px' title='Delete{$row['domain']}' src='images/trashcan.gif' alt='trashcan'></a></td>\n";
          print "\t<td>{$row['domain']}</a></td>\n";
          print "</tr>\n";
        }
      ?>
    </table>
    </div>
  </body>
</html>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
