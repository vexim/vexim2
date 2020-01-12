<?php
  include_once dirname(__FILE__) . '/config/variables.php';
  include_once dirname(__FILE__) . '/config/authpostmaster.php';
  include_once dirname(__FILE__) . '/config/functions.php';
  include_once dirname(__FILE__) . '/config/httpheaders.php';

  check_mail_address(
    $_POST['localpart'],$_SESSION['domain_id'],'adminfail.php'
  );

  check_user_exists(
    $dbh,$_POST['localpart'],$_SESSION['domain_id'],'adminfail.php'
  );

  if (preg_match("/['@%!\/\| ']/",$_POST['localpart'])) {
    header("Location: adminfail.php?badname={$_POST['localpart']}");
    die;
  }

  if (in_array($_POST['smtp'], array(null, '', ':fail:'))) {
    $_POST['smtp'] = ':fail:';
  } else {
    if (!filter_var($_POST['smtp'], FILTER_VALIDATE_EMAIL)) {
      header("Location: adminfail.php?badname=" . htmlentities($_POST['smtp']));
      die;
    }
  }

  if (in_array($_POST['realname'], array(null, ''))) {
    $_POST['realname'] = 'Fail';
  }

  $query = "INSERT INTO users (localpart, username, domain_id, smtp, pop,
    uid, gid, type, realname) SELECT :localpart,
    :username, :domain_id, :smtp, :smtp, uid, gid, 'fail',
    :realname FROM domains WHERE domain_id=:domain_id";
  $sth = $dbh->prepare($query);
  $success = $sth->execute(array(':localpart'=>$_POST['localpart'],
      ':username'=>$_POST['localpart'].'@'.$_SESSION['domain'],
      ':domain_id'=>$_SESSION['domain_id'],
      ':smtp' => $_POST['smtp'],
      ':realname' => $_POST['realname']
  ));

  if ($success) {
    header ("Location: adminfail.php?added={$_POST['localpart']}");
  } else {
    header ("Location: adminfail.php?failadded={$_POST['localpart']}");
  }
?>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
