<?
  include_once dirname(__FILE__) . "/config/variables.php";
  include_once dirname(__FILE__) . "/config/authsite.php";
  include_once dirname(__FILE__) . "/config/functions.php";
  include_once dirname(__FILE__) . "/config/httpheaders.php";
?>
<html>
  <head>
    <title>Virtual Exim: Manage Sites</title>
    <link rel="stylesheet" href="style.css" type="text/css">
  </head>
  <? include dirname(__FILE__) . "/config/header.php"; ?>
  <div id='menu'>
    <a href="siteadd.php?type=local">Add local domain</a><br>
    <a href="siteadd.php?type=relay">Add relay domain</a><br>
    <a href='sitepassword.php'>Site Password</a><br>
    <br><a href="logout.php">Logout</a><br>
  </div>
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
			AND admin=1 AND lower(domain) LIKE lower('$letter%') ORDER BY domain";
     else 
		$query = "SELECT localpart,domain,domains.domain_id FROM users,domains
			WHERE users.domain_id = domains.domain_id
			AND domain !='admin' AND admin=1 ORDER BY domain";
  	$result = $db->query($query);
	if ($result->numRows()) {
	  while ($row = $result->fetchRow()) {
	    print "<tr>";
	    print "\t<td><a href=\"sitedelete.php?domain_id={$row['domain_id']}&domain={$row['domain']}\">";
	    print "<img style='border:0;width:10px;height:16px' title='Delete {$row['domain']}' src='images/trashcan.gif' alt='trashcan'></a></td>\n";
	    print "\t<td><a href=\"sitechange.php?domain_id={$row['domain_id']}&domain={$row['domain']}\">{$row['domain']}</a></td>\n";
	    print "\t<td>{$row['localpart']}@{$row['domain']}</td>\n";
	    print "</tr>\n";
	  }
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
	if ($result->numRows()) {
	  while ($row = $result->fetchRow()) {
	    print "<tr>";
	    print "\t<td><a href=\"sitedelete.php?domain_id={$row['domain_id']}&domain={$row['domain']}&type=relay\">";
	    print "<img style='border:0;width:10px;height:16px' title='Delete{$row['domain']}' src='images/trashcan.gif' alt='trashcan'></a></td>\n";
	    print "\t<td>{$row['domain']}</a></td>\n";
	    print "</tr>\n";
	  }
	}
      ?>
    </table>
    </div>
  </body>
</html>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
