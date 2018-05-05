<?php
  include_once dirname(__FILE__) . '/config/variables.php';
  include_once dirname(__FILE__) . '/config/authpostmaster.php';
  include_once dirname(__FILE__) . '/config/functions.php';
  include_once dirname(__FILE__) . '/config/httpheaders.php';

  # confirm that the postmaster is updating a user they are permitted to change before going further  
  $query = "SELECT * FROM users WHERE user_id=:user_id
		AND domain_id=:domain_id AND (type='local' OR type='piped')";
  $sth = $dbh->prepare($query);
  $sth->execute(array(':user_id'=>$_POST['user_id'], ':domain_id'=>$_SESSION['domain_id']));
  if (!$sth->rowCount()) {
	  header ("Location: adminuser.php?failupdated={$_POST['localpart']}");
	  die();  
  }
  $account = $sth->fetch();
 
  # Fix the boolean values
  $query = "SELECT avscan,spamassassin,pipe,uid,gid,quotas,maxmsgsize
    FROM domains
    WHERE domain_id=:domain_id";
  $sth = $dbh->prepare($query);
  $sth->execute(array(':domain_id'=>$_SESSION['domain_id']));
  if ($sth->rowCount()) {
    $row = $sth->fetch();
  }
  if (isset($_POST['admin'])) {
    $_POST['admin'] = 1;
  } else {
    $_POST['admin'] = 0;
  }
  if (isset($_POST['on_forward']) && isset($_POST['forward']) && $_POST['forward']!=='') {
    $_POST['on_forward'] = 1;
    $forwardto=explode(",",$_POST['forward']);
    for($i=0; $i<count($forwardto); $i++){
      $forwardto[$i]=trim($forwardto[$i]);
      if(!filter_var($forwardto[$i], FILTER_VALIDATE_EMAIL)) {
        header ("Location: adminalias.php?invalidforward=".htmlentities($forwardto[$i]));
        die;
      }
    }
    $forwardaddr = implode(",",$forwardto);
  } else {
    $_POST['on_forward'] = 0;
    $forwardaddr=$account['forward'];
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

  if (isset($_POST['maxmsgsize']) && $row['maxmsgsize']!=='0') {
    if ($_POST['maxmsgsize']<=0 || $_POST['maxmsgsize']>$row['maxmsgsize']) {
      $_POST['maxmsgsize']=$row['maxmsgsize'];
    }
  }

  # Prevent de-admining the last admin
  $query = "SELECT COUNT(user_id) AS count FROM users
    WHERE admin=1 AND domain_id=:domain_id
	AND (type='local' OR type='piped')
    AND user_id!=:user_id";
  $sth = $dbh->prepare($query);
  $sth->execute(array(':domain_id'=>$_SESSION['domain_id'], ':user_id'=>$_POST['user_id']));
  $row = $sth->fetch();
  if (($row['count'] == "0") && ($_POST['admin'] == "0")) {
    header ("Location: adminuser.php?lastadmin={$_POST['localpart']}");
    die;
  }

  # Set the appropriate maildirs
  $query = "SELECT maildir FROM domains WHERE domain_id=:domain_id";
  $sth = $dbh->prepare($query);
  $sth->execute(array(':domain_id'=>$_SESSION['domain_id']));
  $row = $sth->fetch();
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
  if (isset($_POST['clear']) && $_POST['clear']!=='') {
    if (validate_password($_POST['clear'], $_POST['vclear'])) {
      if (!password_strengthcheck($_POST['clear'])) {    
        header ("Location: adminuser.php?weakpass={$_POST['localpart']}");
        die;
      }
       $cryptedpassword = crypt_password($_POST['clear']);
      $query = "UPDATE users
        SET crypt=:crypt WHERE localpart=:localpart
        AND domain_id=:domain_id";
      $sth = $dbh->prepare($query);
      $success = $sth->execute(array(':crypt'=>$cryptedpassword,
          ':localpart'=>$_POST['localpart'], ':domain_id'=>$_SESSION['domain_id']));
      if ($success) {
        if ($_POST['user_id'] === $_SESSION['user_id']) { 
          $_SESSION['crypt'] = $cryptedpassword;
        }
      } else { 
        header ("Location: adminuser.php?failupdated={$_POST['localpart']}");
        die;
      }
    } else {
        header ("Location: adminuser.php?badpass={$_POST['localpart']}");
        die;
    }
  }

  if (isset($_POST['vacation']) && is_string($_POST['vacation'])) {
    $vacation = trim($_POST['vacation']);
    $vacation = quoted_printable_encode($vacation);
  } else {
    $vacation = '';
  }

  $query = "UPDATE users SET uid=:uid,
    gid=:gid, smtp=:smtp, pop=:pop,
    realname=:realname,
    admin=:admin,
    on_avscan=:on_avscan,
    on_forward=:on_forward,
    on_piped=:on_piped,
    on_spamassassin=:on_spamassassin,
    on_vacation=:on_vacation,
    enabled=:enabled,
    forward=:forward,
    maxmsgsize=:maxmsgsize,
    quota=:quota,
    sa_tag=:sa_tag,
    sa_refuse=:sa_refuse,
    spam_drop=:spam_drop,
    type=:type,
    vacation=:vacation,
    unseen=:unseen
    WHERE user_id=:user_id";
  $sth = $dbh->prepare($query);
  $success = $sth->execute(array(':uid'=>$_POST['uid'], ':gid'=>$_POST['gid'],
    ':smtp'=>$smtphomepath, ':pop'=>$pophomepath, ':realname'=>$_POST['realname'],
    ':admin'=>$_POST['admin'], ':on_avscan'=>$_POST['on_avscan'],
    ':on_forward'=>$_POST['on_forward'], ':on_piped'=>$_POST['on_piped'],
    ':on_spamassassin'=>$_POST['on_spamassassin'],
    ':on_vacation'=>$_POST['on_vacation'], ':enabled'=>$_POST['enabled'],
    ':forward'=>$forwardaddr, ':maxmsgsize'=>$_POST['maxmsgsize'],
    ':quota'=>$_POST['quota'],
    ':sa_tag'=>(isset($_POST['sa_tag']) ? $_POST['sa_tag'] : $sa_tag),
    ':sa_refuse'=>(isset($_POST['sa_refuse']) ? $_POST['sa_refuse'] : $sa_refuse),
    ':spam_drop'=>(isset($_POST['spam_drop']) ? $_POST['spam_drop'] : 0),
    ':type'=>$_POST['type'],
    ':vacation'=>$vacation,
    ':unseen'=>$_POST['unseen'], ':user_id'=>$_POST['user_id'],
    ));
  if ($success) {
    header ("Location: adminuser.php?updated={$_POST['localpart']}");
  } else {
    header ("Location: adminuser.php?failupdated={$_POST['localpart']}");
  }
?>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
