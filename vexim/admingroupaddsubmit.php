<?php
  include_once dirname(__FILE__) . '/config/variables.php';
  include_once dirname(__FILE__) . '/config/authpostmaster.php';
  include_once dirname(__FILE__) . '/config/functions.php';

  if (preg_match("/['@%!\/\|\" ']/",$_POST['localpart'])) {
    header("Location: admingroup.php?badname={$_POST['localpart']}");
    die;
  }

  check_mail_address(
    $_POST['localpart'],$_SESSION['domain_id'],'admingroup.php'
  );

  check_user_exists(
    $dbh,$_POST['localpart'],$_SESSION['domain_id'],'admingroup.php'
  );

  $query = "INSERT INTO groups (name, domain_id)
    VALUES (:localpart, :domain_id)";
  $sth = $dbh->prepare($query);
  $success = $sth->execute(array(':localpart'=>$_POST['localpart'], ':domain_id'=>$_SESSION['domain_id']));

  if ($success) { 
    header ("Location: admingroup.php?group_added={$_POST['localpart']}"); 
  } else { 
    header ("Location: admingroup.php?group_failadded={$_POST['localpart']}"); 
  } 
?>

<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
