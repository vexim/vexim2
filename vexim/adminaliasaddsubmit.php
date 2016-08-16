<?php
  include_once dirname(__FILE__) . '/config/variables.php';
  include_once dirname(__FILE__) . '/config/authpostmaster.php';
  include_once dirname(__FILE__) . '/config/functions.php';
  include_once dirname(__FILE__) . '/config/httpheaders.php';

  # Fix the boolean values
  if (isset($_POST['admin'])) {
    $_POST['admin'] = 1;
  } else {
    $_POST['admin'] = 0;
  }
  if (isset($_POST['enabled'])) {
    $_POST['enabled'] = 1;
  } else {
    $_POST['enabled'] = 0;
  }
  $query = "SELECT avscan,spamassassin from domains
    WHERE domain_id=:domain_id";
  $sth = $dbh->prepare($query);
  $sth->execute(array(':domain_id'=>$_SESSION['domain_id']));
  $row = $sth->fetch();
  if ((isset($_POST['on_avscan'])) && ($row['avscan'] == 1)) {
    $_POST['on_avscan'] = 1;
  } else {
    $_POST['on_avscan'] = 0;
  }
  if ((isset($_POST['on_spamassassin'])) && ($row['spamassassin'] == 1)) {
    $_POST['on_spamassassin'] = 1;
  } else {
    $_POST['on_spamassassin'] = 0;
  }
  # If a password wasn't specified, create a randomised 128bit password
  if (($_POST['clear'] === "") && ($_POST['vclear'] === "")) {
    $junk = md5(rand().time().rand());
    $_POST['clear'] = $junk;
    $_POST['vclear'] = $junk;
  }

  # aliases must have a localpart defined
  if ($_POST['localpart']==''){
    header("Location: adminalias.php?badname={$_POST['localpart']}");
    die;
  }

  check_mail_address(
    $_POST['localpart'],$_SESSION['domain_id'],'adminalias.php'
  );

  # check_user_exists() will die if a user account already exists with the same localpart and domain id
  check_user_exists(
    $dbh,$_POST['localpart'],$_SESSION['domain_id'],'adminalias.php'
  );

  if(!isset($_POST['realname']) || $_POST['realname']==='') {
    $_POST['realname']=$_POST['localpart'];
  }

  if ((preg_match("/['@%!\/\|\" ']/",$_POST['localpart']))
    || preg_match("/^\s*$/",$_POST['realname'])) {
    header("Location: adminalias.php?badname={$_POST['localpart']}");
    die;
  }
  $forwardto=explode(",",$_POST['smtp']);
  for($i=0; $i<count($forwardto); $i++){
    $forwardto[$i]=trim($forwardto[$i]);
    if(!filter_var($forwardto[$i], FILTER_VALIDATE_EMAIL)) {
      header ("Location: adminalias.php?invalidforward=".htmlentities($forwardto[$i]));
      die;
    }
  }
  $aliasto = implode(",",$forwardto);
  if (validate_password($_POST['clear'], $_POST['vclear'])) {
    if (!password_strengthcheck($_POST['clear'])) {
      header ("Location: adminalias.php?weakpass={$_POST['localpart']}");
      die;
    }
    $query = "INSERT INTO users
      (localpart, username, domain_id, crypt, smtp, pop, uid, gid, realname, type, admin, on_avscan, 
       on_spamassassin, sa_tag, sa_refuse, spam_drop, enabled)
      SELECT :localpart, :username, :domain_id, :crypt, :smtp, :pop, uid, gid, :realname, 'alias', :admin,
      :on_avscan, :on_spamassassin, :sa_tag, :sa_refuse, :spam_drop, :enabled
      FROM domains
      WHERE domains.domain_id=:domain_id";
    $sth = $dbh->prepare($query);
       $success = $sth->execute(array(
       ':localpart' => $_POST['localpart'],
       ':username' => $_POST['localpart'] . '@' . $_SESSION['domain'],
       ':domain_id' => $_SESSION['domain_id'],
       ':crypt' => crypt_password($_POST['clear']),
       ':smtp' => $aliasto,
       ':pop' => $aliasto,
       ':realname' => $_POST['realname'],
       ':admin' => $_POST['admin'],
       ':on_avscan' => $_POST['on_avscan'],
       ':on_spamassassin' => $_POST['on_spamassassin'],
       ':sa_tag'=>(isset($_POST['sa_tag']) ? $_POST['sa_tag'] : $sa_tag),
       ':sa_refuse'=>(isset($_POST['sa_refuse']) ? $_POST['sa_refuse'] : $sa_refuse),
       ':spam_drop'=>(isset($_POST['spam_drop']) ? $_POST['spam_drop'] : 0),
       ':enabled' => $_POST['enabled']
       ));
       

    if ($success) {
      header ("Location: adminalias.php?added={$_POST['localpart']}");
    } else {
      header ("Location: adminalias.php?failadded={$_POST['localpart']}");
    }
  } else {
    header ("Location: adminalias.php?badaliaspass={$_POST['localpart']}");
  } 
?>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
