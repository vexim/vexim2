<?
  include_once dirname(__FILE__) . "/config/variables.php";
  include_once dirname(__FILE__) . "/config/authsite.php";
  include_once dirname(__FILE__) . "/config/httpheaders.php";

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
          header ("Location: site.php?deleted={$_POST['domain']}");
          die;
        }
      }
    } else header ("Location: site.php?faildeleted={$_POST['domain']}");
      die;
  } else if ($_POST['confirm'] == "cancel") {
    header ("Location: site.php?canceldelete={$_POST['domain']}");
    die;
  }

  $query = "SELECT COUNT(*) AS count, domain, domains.type FROM users,domains
  		WHERE (domains.domain_id={$_GET['domain_id']}
		AND users.domain_id=domains.domain_id)
		GROUP BY domain,domains.type";
  $result = $db->query($query);
  if ($result->numRows()) { $row = $result->fetchRow(); }
  
?>
<html>
  <head>
    <title>Virtual Exim: <? echo _("Confirm Delete"); ?></title>
    <link rel="stylesheet" href="style.css" type="text/css">
  </head>
    <body>
    <? include dirname(__FILE__) . "/config/header.php"; ?>
    <div id='menu'>
      <a href='site.php'><? echo _("Manage Domains"); ?></a><br>
      <a href='sitepassword.php'><? echo _("Site Password"); ?></a><br>
      <br><a href='logout.php'><? echo _("Logout"); ?></a><br>
    </div>
    <div id='Content'>
      <form name='domaindelete' method='post' action='sitedelete.php'>
	<table align="center">
	  <tr><td colspan='2'><? echo _("Please confirm deleting domain ") . $_GET['domain']; ?>:</td></tr>
	  <? if ($_GET['type'] != "relay") {
		print "<tr><td colspan='2'>" . _("There are currently <b>{$row['count']}</b> accounts in domain {$_GET['domain']}") . " </td></tr>";
	     }
	  ?>
	  <tr><td><input name='confirm' type='radio' value='cancel' checked><b> <? echo _("Do Not Delete"); ?> <? print $_GET['domain']; ?></b></td></tr>
	  <tr><td><input name='confirm' type='radio' value='1'><b> <? echo _("Delete"); ?> <? print $_GET['domain']; ?></b></td></tr>
	  <tr><td><input name='domain_id' type='hidden' value='<? print $_GET['domain_id']; ?>'>
	  	<input name='domain' type='hidden' value='<? print $_GET['domain']; ?>'>
	      <input name='submit' type='submit' value='<? echo _("Continue"); ?>'></td></tr>
	</table>
      </form>
    </div>
  </body>
</html>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
