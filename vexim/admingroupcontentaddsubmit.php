<?php
  include_once dirname(__FILE__) . '/config/variables.php';
  include_once dirname(__FILE__) . '/config/authpostmaster.php';
  include_once dirname(__FILE__) . '/config/functions.php';

  # validate user_id and group_id
  if (!isset($_POST['usertoadd']) or !isset($_POST['group_id'])) {
    header("Location: admingroup.php?badname={$_POST['usertoadd']}");
    die;
  }
  $query = "INSERT INTO group_contents (group_id, member_id)
          VALUES ( {$_POST['group_id']}, {$_POST['usertoadd']} )";
  $result = $db->query($query);
  if (!DB::isError($result)) { 
    header ("Location: admingroupchange.php?group_id={$_POST['group_id']}
      &group_updated={$_POST['localpart']}"); 
  } else { 
    header ("Location: admingroupchange.php?group_id={$_POST['group_id']}
      &group_failupdated={$_POST['localpart']}"); 
  }
?>
