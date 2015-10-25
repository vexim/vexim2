<?php
  include_once dirname(__FILE__) . "/config/variables.php";
  include_once dirname(__FILE__) . "/config/authuser.php";
  include_once dirname(__FILE__) . "/config/functions.php";
  include_once dirname(__FILE__) . "/config/httpheaders.php";
  if (isset($_POST['on_vacation'])) {$_POST['on_vacation'] = 1;} else {$_POST['on_vacation'] = 0;}
  if (isset($_POST['on_forward'])) {
    $_POST['on_forward'] = 1;
    if(!filter_var($_POST['forward'], FILTER_VALIDATE_EMAIL)) {
      header ("Location: userchange.php?invalidforward=".htmlentities($_POST['forward']));
      die;
    }
  } else {
    $_POST['on_forward'] = 0;
    $_POST['forward']='';
  }
  if (isset($_POST['unseen'])) {$_POST['unseen'] = 1;} else {$_POST['unseen'] = 0;}
  # Do some checking, to make sure the user is ALLOWED to make these changes
  $query = "SELECT avscan,spamassassin,maxmsgsize from domains WHERE domain_id=:domain_id";
  $sth = $dbh->prepare($query);
  $sth->execute(array(':domain_id'=>$_SESSION['domain_id']));
  $row = $sth->fetch();
  if ((isset($_POST['on_avscan'])) && ($row['avscan'] === '1')) {$_POST['on_avscan'] = 1;} else {$_POST['on_avscan'] = 0;}
  if ((isset($_POST['on_spamassassin'])) && ($row['spamassassin'] === '1')) {$_POST['on_spamassassin'] = 1;} else {$_POST['on_spamassassin'] = 0;}
  if ((isset($_POST['maxmsgsize'])) && ($_POST['maxmsgsize'] > $row['maxmsgsize'])) {$_POST['maxmsgsize'] = $row['maxmsgsize'];}

  if ($_POST['realname'] !== "") {
    $query = "UPDATE users SET realname=:realname
		WHERE user_id=:user_id";
    $sth = $dbh->prepare($query);
    $sth->execute(array(':realname'=>$_POST['realname'], ':user_id'=>$_SESSION['user_id']));
  }

# Update the password, if the password was given
  if (isset($_POST['clear']) && $_POST['clear']!=='') {
    if (validate_password($_POST['clear'], $_POST['vclear'])) {
      $cryptedpassword = crypt_password($_POST['clear']);
      $query = "UPDATE users SET crypt=:crypt WHERE user_id=:user_id";
      $sth = $dbh->prepare($query);
      $success = $sth->execute(array(':crypt'=>$cryptedpassword, ':user_id'=>$_SESSION['user_id']));
      if ($success) {
        $_SESSION['crypt'] = $cryptedpassword;
        header ("Location: userchange.php?userupdated");
        die;
      } else {
        header ("Location: userchange.php?badpass");
        die;
      }
      header ("Location: userchange.php?badpass");
      die;
    }
  }

  if (isset($_POST['vacation']) && is_string($_POST['vacation'])) {
    $vacation = trim($_POST['vacation']);
    if (function_exists('imap_8bit')) {
      $vacation = imap_8bit($vacation);
    }
  } else {
    $vacation = '';
  }

    # Finally 'the rest' which is handled by the profile form
    $query = "UPDATE users SET on_avscan=:on_avscan,
      on_spamassassin=:on_spamassassin, sa_tag=:sa_tag,
      sa_refuse=:sa_refuse, on_vacation=:on_vacation,
      vacation=:vacation, on_forward=:on_forward,
      forward=:forward, maxmsgsize=:maxmsgsize,
      unseen=:unseen, spam_drop=:spam_drop
      WHERE user_id=:user_id";
    $sth = $dbh->prepare($query);
    $success = $sth->execute(array(':on_avscan'=>$_POST['on_avscan'],
      ':on_spamassassin'=>$_POST['on_spamassassin'],
      ':sa_tag'=>(isset($_POST['sa_tag']) ? $_POST['sa_tag'] : 0),
      ':sa_refuse'=>(isset($_POST['sa_refuse']) ? $_POST['sa_refuse'] : 0),
      ':on_vacation'=>$_POST['on_vacation'],
      ':vacation'=>$vacation,
      ':on_forward'=>$_POST['on_forward'], ':forward'=>$_POST['forward'],
      ':maxmsgsize'=>$_POST['maxmsgsize'], ':unseen'=>$_POST['unseen'],
      ':spam_drop'=>(isset($_POST['spam_drop']) ? $_POST['spam_drop'] : 0),
      ':user_id'=>$_SESSION['user_id']
      ));
    if ($success) {
      if (strlen($_POST['vacation']) > $max_vacation_length)
      {
        header ("Location: userchange.php?uservacationtolong=" . strlen($_POST['vacation']));
      }
      else
      {
        header ("Location: userchange.php?userupdated");
      }
      die;
    } else {
      header ("Location: userchange.php?userfailed");
      die;
    }
