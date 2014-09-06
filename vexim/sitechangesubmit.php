<?php
  include_once dirname(__FILE__) . "/config/variables.php";
  include_once dirname(__FILE__) . "/config/authsite.php";
  include_once dirname(__FILE__) . "/config/functions.php";
  include_once dirname(__FILE__) . "/config/httpheaders.php";

  if (isset($_POST['avscan'])) {$_POST['avscan'] = 1;} else {$_POST['avscan'] = 0;}
  if (isset($_POST['spamassassin'])) {$_POST['spamassassin'] = 1;} else {$_POST['spamassassin'] = 0;}
  if (isset($_POST['enabled'])) {$_POST['enabled'] = 1;} else {$_POST['enabled'] = 0;}
  if (isset($_POST['pipe'])) {$_POST['pipe'] = 1;} else {$_POST['pipe'] = 0;}
  if ($_POST['max_accounts'] == '') {$_POST['max_accounts'] = '0';}
  if (isset($_POST['clear'])) {
    if (validate_password($_POST['clear'], $_POST['vclear'])) {
        $query = "UPDATE users SET crypt=:crypt";
        if($saveclearpw==1) $query .= ", clear=:clear";
        $query .= " WHERE localpart=:localpart AND domain_id=:domain_id";
      $sth = $dbh->prepare($query);
        if($saveclearpw==1) {
            $success = $sth->execute(array(':crypt'=>crypt_password($_POST['clear']),
                                           ':clear'=>$_POST['clear'], ':localpart'=>$_POST['localpart'],
                                           ':domain_id'=>$_POST['domain_id']));
        } else {
            $success = $sth->execute(array(':crypt'=>crypt_password($_POST['clear']),
                                           ':localpart'=>$_POST['localpart'],
                                           ':domain_id'=>$_POST['domain_id']));
        }
     
      if ($success) {
	header ("Location: site.php?updated={$_POST['domain']}");
	die;
      } else {
	header ("Location: site.php?failupdated={$_POST['domain']}");
	die;
      }
    } else {
      header ("Location: site.php?badpass={$_POST['domain']}");
      die;
    }
  } 

// User can specify either UID, or username, the former being preferred.
// Using posix_getpwuid/posix_getgrgid even when we have an UID is so we
// are sure the UID exists.
  if (isset ($_POST['uid'])) {
    $uid = $_POST['uid'];
  }
  if (isset ($_POST['gid'])) {
    $gid = $_POST['gid'];
  }
  
  if ($userinfo = @posix_getpwuid ($uid)) {
    $uid = $userinfo['uid'];
  } elseif ($userinfo = @posix_getpwnam ($uid)) {
    $uid = $userinfo['uid'];
  } else {
    header ("Location: site.php?failuidguid={$_POST['domain']}");
    die;
  }
  
  if ($groupinfo = @posix_getgrgid ($gid)) {
    $gid = $groupinfo['gid'];
  } elseif ($groupinfo = @posix_getgrnam ($gid)) {
    $gid = $groupinfo['gid'];
  } else {
    header ("Location: site.php?failuidguid={$_POST['domain']}");
    die;
  }

  $query = "UPDATE domains SET uid=:uid, gid=:gid, avscan=:avscan,
		maxmsgsize=:maxmsgsize, pipe=:pipe, max_accounts=:max_accounts,
		quotas=:quotas, sa_tag=:sa_tag, sa_refuse=:sa_refuse,
		spamassassin=:spamassassin, enabled=:enabled
        WHERE domain_id=:domain_id";
  $sth = $dbh->prepare($query);
  $success = $sth->execute(array(':uid'=>$uid, ':gid'=>$gid,
      ':avscan'=>$_POST['avscan'], ':maxmsgsize'=>$_POST['maxmsgsize'],
      ':pipe'=>$_POST['pipe'], ':max_accounts'=>$_POST['max_accounts'],
      ':quotas'=>$_POST['quotas'],
      ':sa_tag'=>((isset($_POST['sa_tag'])) ? $_POST['sa_tag'] : 0),
      ':sa_refuse'=>((isset($_POST['sa_refuse'])) ? $_POST['sa_refuse'] : 0),
      ':spamassassin'=>$_POST['spamassassin'], ':enabled'=>$_POST['enabled'],
      ':domain_id'=>$_POST['domain_id'],
      ));
  if ($success) {
    header ("Location: site.php?updated={$_POST['domain']}");
    die; 
  } else {
    header ("Location: site.php?failupdated={$_POST['domain']}");
    die;
  }
  

  if (isset($_POST['sadisable'])) {
    $query = "UPDATE users SET on_spamassassin='0' WHERE domain_id=:domain_id";
    $sth = $dbh->prepare($query);
    $success = $sth->execute(array(':domain_id'=>$_POST['domain_id']));
    if ($success) {
      header ("Location: site.php?updated={$_POST['domain']}");
      die;
    }
  }

  if (isset($_POST['avdisable'])) {
    $query = "UPDATE users SET on_avscan='0' WHERE domain_id=:domain_id";
    $sth = $dbh->prepare($query);
    $success = $sth->excecute(array(':domain_id'=>$_POST['domain_id']));
    if ($success) {
      header ("Location: site.php?updated={$_POST['domain']}");
      die;
    }
  }

# Just-in-case catchall
header ("Location: site.php?failupdated={$_POST['domain']}");
?>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
