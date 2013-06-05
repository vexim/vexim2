<?php
  include_once dirname(__FILE__) . '/config/variables.php';
  include_once dirname(__FILE__) . '/config/authpostmaster.php';
  include_once dirname(__FILE__) . '/config/functions.php';

  # confirm that the user is updating a group they are permitted to change before going further  
  $query = "SELECT * FROM groups WHERE id='{$_REQUEST['group_id']}' AND domain_id='{$_SESSION['domain_id']}'";
  $result = $db->query($query);
  if ($result->numRows()<1) {
	  header ("Location: admingroupchange.php?group_id={$_REQUEST['group_id']}&group_failupdated={$_REQUEST['localpart']}"); 
	  die();  
  }
  
  # validate user_id and group_id
  if (!isset($_REQUEST['member_id']) or !isset($_REQUEST['group_id'])) {
    header("Location: admingroup.php?badname={$_REQUEST_['member_id']}");
    die;
  }
  $query = "DELETE FROM group_contents 
    WHERE group_id='{$_REQUEST['group_id']}'
    AND member_id='{$_REQUEST['member_id']}'";
  $result = $db->query($query);
  if (!DB::isError($result)) { 
    header ("Location: admingroupchange.php?group_id={$_REQUEST['group_id']}&group_updated={$_REQUEST['localpart']}"); 
  } else { 
    header ("Location: admingroupchange.php?group_failupdated={$_REQUEST['localpart']}"); 
  }
?>
