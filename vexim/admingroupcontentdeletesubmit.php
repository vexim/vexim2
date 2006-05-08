<?php
  include_once dirname(__FILE__) . '/config/variables.php';
  include_once dirname(__FILE__) . '/config/authpostmaster.php';
  include_once dirname(__FILE__) . '/config/functions.php';

  # validate user_id and group_id
  if (!isset($_REQUEST['member_id']) or !isset($_REQUEST['group_id'])) {
    header("Location: admingroup.php?badname={$_REQUEST_['member_id']}");
    die;
  }
  $query = "DELETE FROM group_contents 
    WHERE group_id={$_REQUEST['group_id']}
    AND member_id={$_REQUEST['member_id']}";
  $result = $db->query($query);
  if (!DB::isError($result)) { 
    header ("Location: admingroupchange.php?group_id={$_REQUEST['group_id']}
      &group_updated={$_REQUEST['localpart']}"); 
  } else { 
    header ("Location: adminalias.php?
      group_failupdated={$_REQUEST['localpart']}"); 
  }
?>
