<?php
  include_once dirname(__FILE__) . '/config/variables.php';
  include_once dirname(__FILE__) . '/config/authpostmaster.php';
  include_once dirname(__FILE__) . '/config/functions.php';
  include_once dirname(__FILE__) . '/config/httpheaders.php';

  # confirm that the postmaster is updating a user they are permitted to change before going further  
  $query = "SELECT * FROM users WHERE user_id='{$_POST['user_id']}'
		AND domain_id='{$_SESSION['domain_id']}'
		AND (type='local' OR type='piped')";
  $result = $db->query($query);
  if ($result->numRows()<1) {
	  header ("Location: adminuser.php?failupdated={$_POST['localpart']}");
	  die();  
  }
 
  # Fix the boolean values
  $query = "SELECT avscan,spamassassin,pipe,uid,gid,quotas
    FROM domains
    WHERE domain_id='{$_SESSION['domain_id']}'";
  $result = $db->query($query);
  if (!DB::isError($result)) {
    $row = $result->fetchRow();
  }
  if (isset($_POST['admin'])) {
    $_POST['admin'] = 1;
  } else {
    $_POST['admin'] = 0;
  }
  if (isset($_POST['on_forward'])) {
    $_POST['on_forward'] = 1;
  } else {
    $_POST['on_forward'] = 0;
  }
  if (isset($_POST['unseen'])) {
    $_POST['unseen'] = 1;
  } else {
    $_POST['unseen'] = 0;
  }
  if (isset($_POST['on_vacation'])) {
    $_POST['on_vacation'] = 1;
  } else {
    $_POST['on_vacation'] = 0;
  }
  if (isset($_POST['enabled'])) {
    $_POST['enabled'] = 1;
  } else {
    $_POST['enabled'] = 0;
  }
  if ($postmasteruidgid == "yes"){
	  if (!isset($_POST['gid'])) {
		$_POST['gid'] = $row['gid'];
	  }
	  if (!isset($_POST['uid'])) {
		$_POST['uid'] = $row['uid'];
	  }
  }else{
	# customisation of the uid and gid is not permitted for postmasters, use the domain defaults
	$_POST['uid'] = $row['uid'];
	$_POST['gid'] = $row['gid'];  
  }  
  if (!isset($_POST['quota'])) {
    $_POST['quota'] = $row['quotas'];
  }
  if (!isset($_POST['sa_refuse'])) {
    $_POST['sa_refuse'] = "0";
  }
  if ($row['quotas'] != "0") {
    if (($_POST['quota'] > $row['quotas']) || ($_POST['quota'] == "0")) {
      header ("Location: adminuser.php?quotahigh={$row['quotas']}");
      die;
    }
  }
  # Do some checking, to make sure the user is ALLOWED to make these changes
  if ((isset($_POST['on_piped'])) && ($row['pipe'] = 1)) {
    $_POST['on_piped'] = 1;
  } else {
    $_POST['on_piped'] = 0;
  }
  if ((isset($_POST['on_avscan'])) && ($row['avscan'] = 1)) {
    $_POST['on_avscan'] = 1;
  } else {
    $_POST['on_avscan'] = 0;
  }

  if ((isset($_POST['on_spamassassin'])) && ($row['spamassassin'] = 1)) {
    $_POST['on_spamassassin'] = 1;
  } else {
    $_POST['on_spamassassin'] = 0;
  }

  if (preg_match("/@/",$_POST['forwardmenu'])) {
    $forwardaddr = $_POST['forwardmenu'];
  } else {
    $forwardaddr = $_POST['forward'];
  }

  # Big code block, to make sure we're not de-admining the last admin
  $query = "SELECT COUNT(admin) AS count FROM users
    WHERE admin=1 AND domain_id='{$_SESSION['domain_id']}' 
	AND (type='local' OR type='piped')";
  $result = $db->query($query);
  if ($result->numRows()) {
    $row = $result->fetchRow();
  }
  if ($row['count'] == "1") {
    $nxtquery = "SELECT admin FROM users WHERE localpart='{$_POST['localpart']}'
      AND domain_id='{$_SESSION['domain_id']}' AND (type='local' OR type='piped')";
    $nxtresult = $db->query($nxtquery);
    if ($nxtresult->numRows()) {
      $nxtrow = $nxtresult->fetchRow();
    }
    if (($nxtrow['admin'] == "1") && ($_POST['admin'] == "0")) {
      header ("Location: adminuser.php?lastadmin={$_POST['localpart']}");
      die;
    }
  }

  # Set the appropriate maildirs
  $query = "SELECT maildir FROM domains WHERE domain_id='{$_SESSION['domain_id']}'";
  $result = $db->query($query);
  $row = $result->fetchRow();
  if (($_POST['on_piped'] == 1) && ($_POST['smtp'] != "")) {
    $smtphomepath = $_POST['smtp'];
    $pophomepath = "{$row['maildir']}/{$_POST['localpart']}";
    $_POST['type'] = "piped";
  } else {
    $smtphomepath = "{$row['maildir']}/{$_POST['localpart']}/Maildir";
    $pophomepath = "{$row['maildir']}/{$_POST['localpart']}";
    $_POST['type'] = "local";
  }

  # Update the password, if the password was given
  if (validate_password($_POST['clear'], $_POST['vclear'])) {
    $cryptedpassword = crypt_password($_POST['clear']);
    $query = "UPDATE users
      SET crypt='$cryptedpassword', clear='{$_POST['clear']}'
      WHERE localpart='{$_POST['localpart']}'
      AND domain_id='{$_SESSION['domain_id']}'";
    $result = $db->query($query);
    if (!DB::isError($result)) {
      if ($_POST['localpart'] == $_SESSION['localpart']) { 
        $_SESSION['crypt'] = $cryptedpassword;
      }
    } else { 
      header ("Location: adminuser.php?failupdated={$_POST['localpart']}");
      die;
    }
  } else if ($_POST['clear'] != $_POST['vclear']) {
      header ("Location: adminuser.php?badpass={$_POST['localpart']}");
      die;
  }

  $query = "UPDATE users SET uid='{$_POST['uid']}',
    gid='{$_POST['gid']}', smtp='$smtphomepath', pop='$pophomepath',
    realname='{$_POST['realname']}',
    admin='{$_POST['admin']}',
    on_avscan='{$_POST['on_avscan']}',
    on_forward='{$_POST['on_forward']}',
    on_piped='{$_POST['on_piped']}',
    on_spamassassin='{$_POST['on_spamassassin']}',
    on_vacation='{$_POST['on_vacation']}',
    enabled='{$_POST['enabled']}',
    forward='{$forwardaddr}',
    maxmsgsize='{$_POST['maxmsgsize']}',
    quota='{$_POST['quota']}',
    sa_tag='" . ((isset($_POST['sa_tag'])) ? $_POST['sa_tag'] : 0) . "',
    sa_refuse='" . ((isset($_POST['sa_refuse'])) ? $_POST['sa_refuse'] : 0) . "',
    type='{$_POST['type']}',
    vacation='" . (trim($_POST['vacation']) ? imap_8bit(trim($_POST['vacation'])) : '') . "',
    unseen='{$_POST['unseen']}'
    WHERE user_id='{$_POST['user_id']}'";
  $result = $db->query($query);
  if (!DB::isError($result)) {
    header ("Location: adminuser.php?updated={$_POST['localpart']}");
  } else {
    header ("Location: adminuser.php?failupdated={$_POST['localpart']}");
  }
?>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
