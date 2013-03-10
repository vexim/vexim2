<?php
  include_once dirname(__FILE__) . '/config/variables.php';
  include_once dirname(__FILE__) . '/config/authpostmaster.php';
  include_once dirname(__FILE__) . '/config/functions.php';
  include_once dirname(__FILE__) . '/config/httpheaders.php';

  $query = "UPDATE users SET localpart='{$_POST['localpart']}',
    username='{$_POST['localpart']}@{$_SESSION['domain']}'
    WHERE user_id='{$_POST['user_id']}' AND domain_id='{$_SESSION['domain_id']}' AND type='fail'";
  $result = $db->query($query);
  if (!DB::isError($result)) {
    header ("Location: adminfail.php?updated={$_POST['localpart']}");	
  } else {
    header ("Location: adminfail.php?failupdated={$_POST['localpart']}");
	die;
  }
?>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
