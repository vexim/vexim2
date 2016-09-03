<?php
  if (isset($_SESSION['domain_id'])) {
    $headerquery = "SELECT domains.enabled AS domain, users.enabled AS user FROM users,domains
                    WHERE users.username=:username AND domains.domain_id=:domain_id AND users.domain_id=domains.domain_id;";
    $headerresult = $dbh->prepare($headerquery);
    $headersuccess = $headerresult->execute(array(':username'=>$_SESSION['username'], ':domain_id'=>$_SESSION['domain_id']));
    if ($headersuccess && $headerrow = $headerresult->fetch()) {
      if ($headerrow['domain'] === "0") {
        invalidate_session();
        header ("Location: index.php?domaindisabled");
        die();
      }
      if ($headerrow['user'] === "0") {
        invalidate_session();
        header ("Location: index.php?userdisabled");
        die();
      }
    } else {
      invalidate_session();
      header ("Location: index.php?nodbquery");
      die();
    }
  }
  print "<div id=\"Header\"><p><a href=\"https://github.com/vexim/vexim2\" target=\"_blank\">" . _("Virtual Exim") . "</a> ";
  if (isset($_SESSION['domain'])) {
    print     "-- " . $_SESSION['domain'] . " ";
  }
  // First a few status messages about account maintenance
  if (isset($_GET['added'])) {
    printf (_("-- %s has been successfully added."), html_escape($_GET['added']));
  } else if (isset($_GET['deleted'])) {
    printf (_("-- %s has been successfully deleted."), html_escape($_GET['deleted']));
  } else if (isset($_GET['lastadmin'])) {
    printf (_("-- %s is the last admin account. Create another admin account before deleting or demoting this one."), html_escape($_GET['lastadmin']));
  } else if (isset($_GET['sitepass'])) {
    print   _("-- Site Admin password has been successfully updated.") . "\n";
  } else if (isset($_GET['updated'])) {
    printf (_("-- %s has been successfully updated."), html_escape($_GET['updated']));
  } else if (isset($_GET['userexists'])) {
    printf (_("-- The account could not be added as the name %s is already in use."), html_escape($_GET['userexists']));
  } else if (isset($_GET['addresstoolong'])) {
    printf (_("-- The account could not be added as the mail address is too long."), html_escape($_GET['addresstoolong']));
  } else if (isset($_GET['userupdated'])) {
    print   _("-- Your update was sucessful.");
  } else if (isset($_GET['userfailed'])) {
    print   _("-- Your account could not be updated. Was your password blank?");
  } else if (isset($_GET['usersuccess'])) {
    print   _("-- Your account has been succesfully updated.");
  } // Now some more general errors on account updates
  else if (isset($_GET['badaliaspass'])) {
    printf (_("-- Account %s could not be added. Your passwords do not match."), html_escape($_GET['badaliaspass']));
  } else if (isset($_GET['badname'])) {
    printf (_("-- %s contains invalid characters."), html_escape($_GET['badname']));
  } else if (isset($_GET['badpass'])) {
    printf (_("-- Account %s could not be added. Your passwords were blank, do not match, or contain illegal characters: ' \" ` or ;"), html_escape($_GET['badpass']));
  } else if (isset($_GET['weakpass'])) {
    printf (_("-- The passwords are too weak. Use strong passwords with a a minimum length of 8 and a mix of upper/lower case characters, digits and special characters!"), html_escape($_GET['weakpass']));
  } else if (isset($_GET['failadded'])) {
    printf (_("-- %s could not be added."), html_escape($_GET['failadded']));
  } else if (isset($_GET['failaddeddomerr'])) {
    printf (_("-- Domain %s could not be added."), html_escape($_GET['failaddeddomerr']));
  } else if (isset($_GET['failaddedpassmismatch'])) {
    printf (_("-- Domain %s could not be added. The passwords were blank, or did not match."), html_escape($_GET['failaddedpassmismatch']));
  } else if (isset($_GET['failaddedusrerr'])) {
    printf (_("-- Domain %s could not be added. There was a problem adding the admin account."), html_escape($_GET['failaddedusrerr']));
  } else if (isset($_GET['faildeleted'])) {
    printf (_("-- %s was not deleted."), html_escape($_GET['faildeleted']));
  } else if (isset($_GET['failupdated'])) {
    printf (_("-- %s could not be updated."), html_escape($_GET['failupdated']));
  } // Now some really general status messages
    else if (isset($_GET['canceldelete'])) {
    printf (_("-- Deletion of %s was canceled."), html_escape($_GET['canceldelete']));
  } else if (isset($_GET['domaindisabled'])) {
    print   _("-- This domain is currently disabled. Please see your administrator.");
  } else if (isset($_GET['userdisabled'])) {
    print   _("-- This account is currently disabled. Please see your administrator.");
  } else if (isset($_GET['maxaccounts'])) {
    print   _("-- Your Domain Account Limit Has Been Reached. Please contact your administrator.");
  } else if (isset($_GET['quotahigh'])) {
    printf (_("-- The quota you specified was too high. The maximum quota you can specify is: %s MB."), html_escape($_GET['quotahigh']));
  } else if (isset($_GET['group_deleted'])) {
    printf (_("-- Group %s has been successfully deleted."), html_escape($_GET['group_deleted']));
  } else if (isset($_GET['group_added'])) {
    printf (_("-- Group %s has been successfully added."), html_escape($_GET['group_added']));
  } else if (isset($_GET['group_faildeleted'])) {
    printf (_("-- Group %s was not deleted."), html_escape($_GET['group_faildeleted']));
  } else if (isset($_GET['group_failadded'])) {
    printf (_("-- Group %s failed to be added."), html_escape($_GET['group_failadded']));
  } else if (isset($_GET['group_updated'])) {
    printf (_("-- Group %s has been updated."), html_escape($_GET['group_updated']));
  } else if (isset($_GET['group_failupdated'])) {
    printf (_("-- Group %s could not be updated."), html_escape($_GET['group_failupdated']));
  } else if (isset($_GET['failuidguid'])) {
    printf (_("-- Error getting UID/GID for %s"), html_escape($_GET['failuidguid']));
  } else if (isset($_GET['failmaildirnonabsolute'])) {
    printf (_("-- Domain Mail directory must be an absolute path, but “%s” was provided"), html_escape($_GET['failmaildirnonabsolute']));
  } else if (isset($_GET['failmaildirmissing'])) {
    printf (_("-- Domain Mail directory “%s” does not exist, is not a directory or is not accessible."), html_escape($_GET['failmaildirmissing']));
  } else if (isset($_GET['invalidforward'])) {
    printf (_("-- %s is not a valid e-mail address."), html_escape($_GET['invalidforward']));
  } else if (isset($_GET['nodbquery'])) {
    print   _("-- Database query failed, terminating session");
  } else if (isset($_GET['login']) && ($_GET['login'] === "disabled")) {
    print   _("-- Login is disabled. Please contact your administrator.");
  }
  if (isset($_GET['login']) && ($_GET['login'] == "failed")) { print _("Login failed"); }

  print "</p></div>";
