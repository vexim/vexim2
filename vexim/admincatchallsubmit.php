<?php
  include_once dirname(__FILE__) . '/config/variables.php';
  include_once dirname(__FILE__) . '/config/authpostmaster.php';
  include_once dirname(__FILE__) . '/config/functions.php';
  include_once dirname(__FILE__) . '/config/httpheaders.php';
  if(!filter_var($_POST['smtp'], FILTER_VALIDATE_EMAIL)) {
    header ("Location: adminalias.php?invalidforward=".htmlentities($_POST['smtp']));
    die;
  }
  $query = "DELETE FROM users WHERE domain_id=:domain_id AND type='catch'";
  $sth = $dbh->prepare($query);
  $success = $sth->execute(array(':domain_id'=>$_SESSION['domain_id']));
  if(!$success) {
    header ("Location: adminalias.php?failupdated=Catchall");
  } else {
    $query = "INSERT INTO users (localpart, username, domain_id, smtp,
      pop, uid, gid, realname, type, enabled) SELECT '*',
        :domain, :domain_id, :smtp, :smtp, uid, gid, 'CatchAll', 'catch',
        '1' FROM domains WHERE domains.domain_id=:domain_id";
    $sth = $dbh->prepare($query);
    $success = $sth->execute(array(':domain'=>'*@'.$_SESSION['domain'], ':domain_id'=>$_SESSION['domain_id'], ':smtp'=>$_POST['smtp']));
    
    if ($success) {
      header ("Location: adminalias.php?updated=Catchall");
    } else {
      header ("Location: adminalias.php?failupdated=Catchall");
    }
  }
?>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
