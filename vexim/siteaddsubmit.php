<?
  include_once dirname(__FILE__) . "/config/variables.php";
  include_once dirname(__FILE__) . "/config/authsite.php";
  include_once dirname(__FILE__) . "/config/functions.php";

  if (isset($_POST[avscan])) {$_POST[avscan] = 1;} else {$_POST[avscan] = 0;}
  if (isset($_POST[spamassassin])) {$_POST[spamassassin] = 1;} else {$_POST[spamassassin] = 0;}
  if (isset($_POST[enabled])) {$_POST[enabled] = 1;} else {$_POST[enabled] = 0;}
  if (isset($_POST[pipe])) {$_POST[pipe] = 1;} else {$_POST[pipe] = 0;}

  $smtphomepath = $mailroot . $_POST[domain] . "/" . $_POST[localpart] . "/Maildir";
  $pophomepath = $mailroot . $_POST[domain] . "/" . $_POST[localpart];

  if (validate_password($_POST[clear], $_POST[vclear])) {
    $query = "INSERT INTO domains (domain, spamassassin, avscan, quotas, maildir, pipe, enabled, uid, gid, type)
    VALUES ('" . $_POST[domain] . "',
    '" . $_POST[spamassassin] . "',
    '" . $_POST[avscan] . "',
    '" . $_POST[quotas] . "',
    '" . $_POST[maildir] . $_POST[domain] . "',
    '" . $_POST[pipe] . "',
    '" . $_POST[enabled] . "',
    '" . $_POST[uid] . "',
    '" . $_POST[gid] . "',
    '" . $_POST[type] . "')";
    $domresult = $db->query($query);
    if ((!DB::isError($domresult)) && ($_POST[type] == "local")) {
      $query = "INSERT INTO users (domain_id, localpart, username, clear, crypt, uid, gid, smtp, pop, realname, type, admin)
                SELECT domain_id,
		        '" . $_POST[localpart] . "',
			'" . $_POST[localpart] . "@". $_POST[domain] . "',
			'" . $_POST[clear] . "',
			'" . crypt($_POST[clear],$salt) . "',
			'" . $_POST[uid] . "',
			'" . $_POST[gid] . "',
			'" . $smtphomepath . "',
			'" . $pophomepath . "',
			'Domain Admin',
			'local',
			'1' FROM domains WHERE domains.domain = '" . $_POST[domain] . "'";
      $usrresult = $db->query($query);
      if (DB::isError($usrresult)) {
        header ("Location: site.php?failadded=$_POST[domain]");
	die;
      } else {
        header ("Location: site.php?added=$_POST[domain]&type=$_POST[type]");
      }
    } else if ($_POST[type] == "relay") {
      header ("Location: site.php?added=$_POST[domain]&type=$_POST[type]");
      die;
    } else {
      header ("Location: site.php?failadded=$_POST[domain]");
      die;
    }
  } else {
    header ("Location: site.php?failadded=$_POST[domain]");
    die;
  }
?>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
