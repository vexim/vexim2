<?php
  include_once dirname(__FILE__) . '/config/variables.php';
  include_once dirname(__FILE__) . '/config/authpostmaster.php';
  include_once dirname(__FILE__) . '/config/functions.php';
  include_once dirname(__FILE__) . '/config/httpheaders.php';


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

  $query = "UPDATE users SET localpart=:localpart, username=:username, smtp=:smtp, realname=:realname
    WHERE user_id=:user_id AND domain_id=:domain_id AND type='fail'";
  $sth = $dbh->prepare($query);
  $success = $sth->execute(array(':localpart'=>$_POST['localpart'],
    ':username'=>$_POST['localpart'].'@'.$_SESSION['domain'],
    ':user_id'=>$_POST['user_id'], ':domain_id'=>$_SESSION['domain_id'],
    ':smtp'=>$_POST['smtp'], ':realname'=>$_POST['realname']
  ));
  if ($success) {
    header ("Location: adminfail.php?updated={$_POST['localpart']}");
  } else {
    header ("Location: adminfail.php?failupdated={$_POST['localpart']}");
	die;
  }
?>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
