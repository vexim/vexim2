<?php
  include_once dirname(__FILE__) . '/config/variables.php';
  include_once dirname(__FILE__) . '/config/authpostmaster.php';
  include_once dirname(__FILE__) . '/config/functions.php';

  # confirm that the user is updating a group they are permitted to change before going further
  $query = "SELECT * FROM groups WHERE id=:group_id AND domain_id=:domain_id";
  $sth = $dbh->prepare($query);
  $sth->execute(array(':group_id'=>$_REQUEST['group_id'], ':domain_id'=>$_SESSION['domain_id']));
  if (!$sth->rowCount()) {
	  header ("Location: admingroupchange.php?group_id={$_REQUEST['group_id']}&group_failupdated={$_REQUEST['localpart']}");
	  die();
  }

  # validate user_id and group_id
  if (!isset($_REQUEST['member_id']) or !isset($_REQUEST['group_id'])) {
    header("Location: admingroup.php?badname={$_REQUEST_['member_id']}");
    die;
  }
  $query = "DELETE FROM group_contents
    WHERE group_id=:group_id
    AND member_id=:member_id";
  $sth = $dbh->prepare($query);
  $success = $sth->execute(array(':group_id'=>$_REQUEST['group_id'], ':member_id'=>$_REQUEST['member_id']));
  if ($success) {
    header ("Location: admingroupchange.php?group_id={$_REQUEST['group_id']}&group_updated={$_REQUEST['localpart']}");
  } else {
    header ("Location: admingroupchange.php?group_failupdated={$_REQUEST['localpart']}");
  }
?>
