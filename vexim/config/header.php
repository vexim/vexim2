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
	// First a few status messages about account maintenance
	if (isset($_GET['added'])) {
	  print " -- '{$_GET['added']}' has been successfully added\n";
	} else if (isset($_GET['deleted'])) {
	  print " -- '{$_GET['deleted']}' has been successfully deleted\n";
	} else if (isset($_GET['lastadmin'])) {
	  print " -- '{$_GET['lastadmin']}' is the last admin account.";
	  print "    Create another admin account before deleting or demoting this one\n";
	} else if (isset($_GET['sitepass'])) {
	  print " -- Site Admin password has been successfully updated</div>\n";
	} else if (isset($_GET['updated'])) {
	  print " -- '{$_GET['updated']}' has been successfully updated\n";
	} else if (isset($_GET['userexists'])) {
	  print " -- The account could not be added as the name {$_GET['userexists']} is already in use\n";
        } else if (isset($_GET['userupdated'])) {
          print " -- Your update was sucessful.</div>\n";
        } else if (isset($_GET['userfailed'])) {
          print " -- Your account could not be updated. Was your password blank?.</div>\n";
        } else if (isset($_GET['usersuccess'])) {
          print " -- Your account has been succesfully updated.</div>\n";
	} // Now some more general errors on account updates
	  else if (isset($_GET['badaliaspass'])) {
	  print " -- Account {$_GET['badaliaspass']} could not be added. Your passwords do not match, or contain illegal characters: ' \" ` or ;\n";
	} else if (isset($_GET['badname'])) {
	  print " -- '{$_GET['badname']}' contains invalid characters\n";
	} else if (isset($_GET['badpass'])) {
	  print " -- Account {$_GET['badpass']} could not be added.<br>\n";
	  print " -- Your passwords were blank, do not match, or contain illegal characters: ' \" ` or ;\n";
	} else if (isset($_GET['baddestdom'])) {
	  print " -- The destination domain you specified does not exist\n";
	} else if (isset($_GET['blankname'])) {
	  print " -- You can not specify a blank realname\n";
	} else if (isset($_GET['failadded'])) {
	  print " -- '{$_GET['failadded']}' could not be added\n";
	} else if (isset($_GET['failaddedpassmismatch'])) {
	  print " -- Domain '{$_GET['failaddedpassmismatch']}' could not be added.\n";
	  print "The passwords were blank, or did not match.</div>\n";
	} else if (isset($_GET['failaddedusrerr'])) {
	  print " -- Domain '{$_GET['failaddedusrerr']}' could not be added.\n";
	  print "There was a problem adding the admin account.</div>\n";
	} else if (isset($_GET['faildeleted'])) {
	  print " -- '{$_GET['faildeleted']}' was not deleted\n";
	} else if (isset($_GET['failupdated'])) {
	  print " -- '{$_GET['failupdated']}' could not be updated\n";
	} // Now some really general status messages
	  else if (isset($_GET['canceldelete'])) {
	  print " -- Deletion of '{$_GET['canceldelete']}' was canceled\n";
	} else if (isset($_GET['domaindisabled'])) {
	  print " -- This domain is currently disabled. Please see your administrator\n";
	} else if (isset($_GET['maxaccounts'])) {
	  print " -- Your Domain Account Limit Has Been Reached.";
	  print "    Please contact your administrator\n";
	} else if (isset($_GET['quotahigh'])) {
	  print " -- The quota you specified was too high.\n";
	  print "    The maximum quota you can specify is: {$_GET['quotahigh']} MB\n";
	}
        if ($_GET['login'] == "failed") { print _("Login failed"); }

  print "</p></div>";
?>
