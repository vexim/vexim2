<?
  include_once dirname(__FILE__) . "/config/variables.php";
  include_once dirname(__FILE__) . "/config/authsite.php";

  // Delete the domain's users
  if ($_POST['confirm'] == "1") {
    $usrdelquery = "DELETE FROM users WHERE domain_id={$_POST['domain_id']}";
    $usrdelresult = $db->query($usrdelquery);
    // if we were successful, delete the domain's blocklists
    if (!DB::isError($usrdelresult)) {
      $usrdelquery = "DELETE FROM blocklists WHERE domain_id={$_POST['domain_id']}";
      $usrdelresult = $db->query($usrdelquery);
      // if we were successful, delete the domain itself
      if (!DB::isError($usrdelresult)) {
        $domdelquery = "DELETE FROM domains WHERE domain_id={$_POST['domain_id']}";
        $domdelresult = $db->query($domdelquery);
	// If everything went well, redirect to a success page.
        if (!DB::isError($domdelresult)) {
          header ("Location: site.php?deleted={$_POST['domain_id']}");
          die;
        }
      }
    } else header ("Location: site.php?faildeleted={$_POST['domain_id']}");
      die;
  } else if ($_POST['confirm'] == "cancel") {
    header ("Location: site.php?canceldelete={$_POST['domain_id']}");
    die;
  }

  $query = "SELECT COUNT(*) AS count, domain, domains.type FROM users,domains WHERE (domains.domain_id={$_GET['domain_id']}
	      AND users.domain_id=domains.domain_id) OR domains.type = 'relay' GROUP BY domain,domains.type";
  $result = $db->query($query);
  if ($result->numRows()) { $row = $result->fetchRow(); }
  
?>
<html>
  <head>
    <title>Virtual Exim: Confirm Delete</title>
    <link rel="stylesheet" href="style.css" type="text/css">
  </head>
    <body>
    <? include dirname(__FILE__) . "/config/header.php"; ?>
    <div id='menu'>
      <a href='site.php'>Manage Domains</a><br>
      <a href='sitepassword.php' title='Change site password'>Site Password</a><br>
      <br><a href='logout.php'>Logout</a><br>
    </div>
    <div id='Content'>
      <form name='domaindelete' method='post' action='sitedelete.php'>
	<table align="center">
	  <tr><td colspan='2'>Please confirm deleting domain <? print $row['domain']; ?>:</td></tr>
	  <? if ($row['type'] != "relay") {
		print "<tr><td colspan='2'>There are currently <b>{$row['count']}</b> accounts in domain {$row['domain']}</td></tr>";
	     }
	  ?>
	  <tr><td><input name='confirm' type='radio' value='cancel' checked><b> Do Not Delete <? print $row['domain']; ?></b></td></tr>
	  <tr><td><input name='confirm' type='radio' value='1'><b> Delete <? print $row['domain']; ?></b></td></tr>
	  <tr><td><input name='domain_id' type='hidden' value='<? print $_GET['domain_id']; ?>'>
	      <input name='submit' type='submit' value='Continue'></td></tr>
	</table>
      </form>
    </div>
  </body>
</html>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
