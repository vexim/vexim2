<?php
  include_once dirname(__FILE__) . '/config/variables.php';
  include_once dirname(__FILE__) . '/config/authpostmaster.php';
  include_once dirname(__FILE__) . '/config/functions.php';

  if (preg_match("/['@%!\/\|\" ']/",$_POST['localpart'])) {
    header("Location: admingroup.php?badname={$_POST['localpart']}");
    die;
  }

  check_user_exists(
    $db,$_POST['localpart'],$_SESSION['domain_id'],'admingroup.php'
  );

  $query = "INSERT INTO groups (name, domain_id)
    VALUES ('{$_POST['localpart']}', '{$_SESSION['domain_id']}' ) ";
  $result = $db->query($query);

  if (!DB::isError($result)) { 
    header ("Location: admingroup.php?group_added={$_POST['localpart']}"); 
  } else { 
    header ("Location: admingroup.php?group_failadded={$_POST['localpart']}"); 
	die;
  } 
?>

<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
