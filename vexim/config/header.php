<?
  if (isset($_SESSION['domain_id'])) {
    $domheaderquery = "SELECT enabled FROM domains WHERE domains.domain_id='" . $_SESSION['domain_id'] . "'";
    $domheaderresult = $db->query($domheaderquery);
    $domheaderrow = $domheaderresult->fetchRow();
    $usrheaderquery = "SELECT enabled FROM users WHERE localpart='" . $_SESSION['localpart'] . "' AND domain_id='" . $_SESSION['domain_id'] . "'";
    $usrheaderresult = $db->query($usrheaderquery);
    $usrheaderrow = $usrheaderresult->fetchRow();
  }

  print "<div id=\"Header\"><p><a href=\"http://silverwraith.com/vexim/\" target=\"_blank\">Virtual Exim</a>";
  if (isset($_SESSION['domain'])) {
    print " -- " . $_SESSION['domain'];
  }
  if (($domheaderrow['enabled'] == "0") || ($domheaderrow['enabled'] == "f")) {
    print "-- domain disabled (please see your administrator)";
  } else if (($usrheaderrow['enabled'] == "0") ||($usrheaderrow['enabled'] == "f")) {
    print "-- account disabled (please see your administrator)";
  }

	if (isset($_GET['deleted'])) {
	  print " -- User '{$_GET['deleted']}' has been successfully deleted\n";
	} else if (isset($_GET['lastadmin'])) {
	  print " -- User '{$_GET['lastadmin']}' is the last admin account.";
	  print "    Create another admin account before deleting or demoting this one\n";
	 } else if (isset($_GET['added'])) {
	  print " -- User '{$_GET['added']}' has been successfully added\n";
	} else if (isset($_GET['updated'])) {
	  print " -- User '{$_GET['updated']}' has been successfully updated\n";
	} else if (isset($_GET['faildeleted'])) {
	  print " -- User '{$_GET['faildeleted']}' was not deleted\n";
	} else if (isset($_GET['failadded'])) {
	  print " -- User '{$_GET['failadded']}' could not be added\n";
	} else if (isset($_GET['failupdated'])) {
	  print " -- User '{$_GET['failupdated']}' could not be updated\n";
	} else if (isset($_GET['canceldelete'])) {
	  print " -- Deletion of user '{$_GET['canceldelete']}' was canceled\n";
	} else if (isset($_GET['badname'])) {
	  print " -- User '{$_GET['badname']}' contains invalid characters\n";
	} else if (isset($_GET['userexists'])) {
	  print " -- The account could not be added as the name {$_GET['userexists']} is already in use\n";
	} else if (isset($_GET['blankname'])) {
	  print " -- You can not specify a blank realname\n";
	} else if (isset($_GET['badpass'])) {
	  print " -- Account {$_GET['badpass']} could not be added.<br>\n";
	  print " -- Your passwords were blank, do not match, or contain illegal characters: ' \" ` or ;\n";
	} else if (isset($_GET['badaliaspass'])) {
	  print " -- Account {$_GET['badaliaspass']} could not be added. Your passwords do not match, or contain illegal characters: ' \" ` or ;\n";
	} else if (isset($_GET['maxaccounts'])) {
	  print " -- Your Domain Account Limit Has Been Reached.<br>\n";
	  print "    Please contact your administrator\n";
	} else if (isset($_GET['quotahigh'])) {
	  print " -- The quota you specified was too high.<br>\n";
	  print "    The maximum quota you can specify is: {$_GET['quotahigh']} MB\n";
	} else if (isset($_GET['domaindisabled'])) {
	  print " -- This domain is currently disabled. Please see your administrator\n";
	}
        if ($_GET['login'] == "failed") { print _("Login failed"); }

  print "</p></div>";
?>
