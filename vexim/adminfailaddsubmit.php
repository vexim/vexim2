<?php
  include_once dirname(__FILE__) . '/config/variables.php';
  include_once dirname(__FILE__) . '/config/authpostmaster.php';
  include_once dirname(__FILE__) . '/config/functions.php';
  include_once dirname(__FILE__) . '/config/httpheaders.php';

  check_user_exists(
    $dbh,$_POST['localpart'],$_SESSION['domain_id'],'adminfail.php'
  );

  if (preg_match("/['@%!\/\| ']/",$_POST['localpart'])) {
    header("Location: adminfail.php?badname={$_POST['localpart']}");
    die;
  }

  $query = "INSERT INTO users (localpart, username, domain_id, smtp, pop,
    uid, gid, type, realname) SELECT :localpart,
    :username, :domain_id, ':fail:', ':fail:', uid, gid, 'fail',
    'Fail' FROM domains WHERE domain_id=:domain_id";
  $sth = $dbh->prepare($query);
  $success = $sth->execute(array(':localpart'=>$_POST['localpart'],
      ':username'=>$_POST['localpart'].'@'.$_SESSION['domain'],
      ':domain_id'=>$_SESSION['domain_id']));
  if ($success) {
    header ("Location: adminfail.php?added={$_POST['localpart']}");
  } else {
    header ("Location: adminfail.php?failadded={$_POST['localpart']}");
  }
?>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
